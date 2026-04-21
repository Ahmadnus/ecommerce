<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    // تم التعديل هنا لتقرأ من الدالة الجديدة get_otp_setting
   private function url(): string  { return (string) get_otp_setting('sms_url', ''); }
    private function user(): string { return (string) get_otp_setting('sms_user', ''); }
    private function pass(): string { return (string) get_otp_setting('sms_pass', ''); }
    private function sid(): string  { return (string) get_otp_setting('sms_sid', ''); }
    private function type(): int    { return (int) get_otp_setting('sms_type', 4); }
    private function ttl(): int     { return (int) get_otp_setting('otp_ttl_minutes', 5); }
    private function otpLen(): int  { return (int) get_otp_setting('otp_length', 6); }
    // باقي الكود كما هو تماماً دون تغيير...
    public function normalizeMsisdn(string $input): string
    {
        $digits = preg_replace('/\D+/', '', $input);
        if (str_starts_with($digits, '962')) return $digits;
        if (str_starts_with($digits, '0') && strlen($digits) <= 10) return '962' . substr($digits, 1);
        return $digits;
    }

    public function generateOtp(): string
    {
        $len = $this->otpLen();
        $min = (int) str_pad('1', $len, '0');
        $max = (int) str_repeat('9', $len);
        return (string) random_int($min, $max);
    }

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

        try {
            $response = Http::timeout(15)->withoutVerifying()->get($this->url(), $params);
            $body     = trim($response->body());
            $success  = str_starts_with($body, '17');
            if (!$success) {
                Log::warning('SmsService: non-success', ['msisdn' => $msisdn, 'response' => $body]);
            }
            return ['success' => $success, 'response' => $body];
        } catch (\Throwable $e) {
            Log::error('SmsService: HTTP failed', ['msisdn' => $msisdn, 'error' => $e->getMessage()]);
            return ['success' => false, 'response' => $e->getMessage()];
        }
    }

    public function sendOtp(\App\Models\User $user): array
    {
        $otp       = $this->generateOtp();
        $expiresAt = now()->addMinutes($this->ttl());

        $user->forceFill(['otp' => $otp, 'otp_expires_at' => $expiresAt])->saveQuietly();

        $message = "رمز التحقق الخاص بك: {$otp}\nصالح لمدة {$this->ttl()} دقائق.";
        $result  = $this->send($user->phone, $message);

        if (!$result['success']) {
            throw new \RuntimeException('فشل إرسال رمز التحقق. يرجى المحاولة مرة أخرى.');
        }

        return [$otp, $expiresAt];
    }

    public function verifyOtp(\App\Models\User $user, string $submittedOtp): bool
    {
        if (!$user->otp || !$user->otp_expires_at) return false;
        if (now()->isAfter($user->otp_expires_at)) return false;
        if (!hash_equals($user->otp, trim($submittedOtp))) return false;

        $user->forceFill([
            'otp'               => null,
            'otp_expires_at'    => null,
            'phone_verified_at' => now(),
        ])->saveQuietly();

        return true;
    }

    public function testConnection(string $testPhone): array
    {
        return $this->send($testPhone, 'اختبار الاتصال بخدمة الرسائل النصية — JbuyApp');
    }
}