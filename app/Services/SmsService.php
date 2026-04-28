<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class SmsService
{
    // ── Credential accessors ──────────────────────────────────────────────────

    private function url(): string  { return config('sms.jordan_api.endpoint'); }
    private function user(): string { return config('sms.jordan_api.user'); }
    private function pass(): string { return config('sms.jordan_api.pass'); }
    private function sid(): string  { return config('sms.jordan_api.sid'); }
    private function type(): int    { return config('sms.jordan_api.type'); }
    
    private function ttl(): int     { return (int) get_otp_setting('otp_ttl_minutes', 5); }
    private function otpLen(): int  { return (int) get_otp_setting('otp_length', 6); }

    // ── Phone normalisation ───────────────────────────────────────────────────

    public function normalizeMsisdn(string $input): string
    {
        $digits = preg_replace('/\D+/', '', $input);

        if (str_starts_with($digits, '962')) {
            return $digits;
        }

        if (str_starts_with($digits, '0') && strlen($digits) <= 10) {
            return '962' . substr($digits, 1);
        }

        return $digits;
    }

    // ── OTP generation ────────────────────────────────────────────────────────

    public function generateOtp(): string
    {
        $len = $this->otpLen();
        $min = (int) str_pad('1', $len, '0');
        $max = (int) str_repeat('9', $len);
        return (string) random_int($min, $max);
    }

    // ── Core send ─────────────────────────────────────────────────────────────

    public function send(string $phone, string $message): array
    {
        $msisdn = $this->normalizeMsisdn($phone);

        $params = [
            'user' => $this->user(),
            'pass' => $this->pass(),
            'sid'  => $this->sid(),
            'mno'  => $msisdn,
            'type' => $this->type(),
            'text' => $message,
        ];

        $url = $this->url();

        if (empty($url) || empty($params['user']) || empty($params['pass'])) {
            Log::error('SmsService: credentials missing', ['url' => $url]);
            return ['success' => false, 'response' => 'SMS credentials not configured.'];
        }

        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => false, 
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($params),
            CURLOPT_FORBID_REUSE   => true, // إغلاق الاتصال فوراً لتجنب Connection Reset
            CURLOPT_FRESH_CONNECT  => true,
        ]);

        $response = curl_exec($ch);
        $curlErr  = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($curlErr) {
            Log::error('SmsService: cURL error', [
                'msisdn' => $msisdn,
                'error'  => $curlErr,
            ]);
            return ['success' => false, 'response' => $curlErr];
        }

        $body = trim((string) $response);

        /**
         * CRITICAL FIX: Broadnet API Response Logic
         * الاستجابة الناجحة تبدأ بكلمة "Response:" متبوعة برقم العملية
         * أو تبدأ بـ "17" في بعض الإعدادات القديمة.
         */
        $success = str_contains($body, 'Response:') || str_starts_with($body, '17') || str_contains($body, 'OK');

        if (!$success) {
            Log::warning('SmsService: API returned actual failure', [
                'msisdn'    => $msisdn,
                'http_code' => $httpCode,
                'response'  => $body,
            ]);
        } else {
            Log::info('SmsService: SMS sent successfully', [
                'msisdn'   => $msisdn,
                'response' => $body,
            ]);
        }

        return ['success' => $success, 'response' => $body];
    }

    // ── OTP flow ──────────────────────────────────────────────────────────────

    public function sendOtp(\App\Models\User $user): array
    {
        $otp       = $this->generateOtp();
        $expiresAt = now()->addMinutes($this->ttl());

        $user->forceFill([
            'otp'            => $otp,
            'otp_expires_at' => $expiresAt,
        ])->saveQuietly();

        $message = "رمز التحقق الخاص بك هو: {$otp}\nصالح لمدة {$this->ttl()} دقائق.";
        
        // محاولة الإرسال
        $result = $this->send($user->phone, $message);

        if (!$result['success']) {
            throw new \RuntimeException('عذراً، فشل إرسال رمز التحقق (SMS Gateway Error).');
        }

        return [
            'otp'        => $otp,
            'expires_at' => $expiresAt,
            'sms'        => $result,
        ];
    }

    // ── OTP verification ──────────────────────────────────────────────────────

    public function verifyOtp(\App\Models\User $user, string $submittedOtp): bool
    {
        if (!$user->otp || !$user->otp_expires_at) return false;
        if (now()->isAfter($user->otp_expires_at)) return false;
        
        if (!hash_equals((string)$user->otp, trim($submittedOtp))) {
            return false;
        }

        $user->forceFill([
            'otp'               => null,
            'otp_expires_at'    => null,
            'phone_verified_at' => now(),
        ])->saveQuietly();

        return true;
    }

    public function testConnection(string $testPhone): array
    {
        return $this->send($testPhone, 'اختبار الخدمة من لوحة التحكم     — JbuyApp');
    }


}