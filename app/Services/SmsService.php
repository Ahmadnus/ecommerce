<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * SmsService
 * ─────────────────────────────────────────────────────────────────────────────
 * All credentials read via get_otp_setting() → OtpSetting::get()
 * → config/sms.php fallback.
 *
 * CRITICAL FIX: send() now uses cURL with CURLOPT_POST instead of
 * Laravel's Http::get(). The Broadnet API requires an HTTP POST request.
 * The working test route (provided) confirmed this.
 */
class SmsService
{
    // ── Credential accessors ──────────────────────────────────────────────────

  private function url(): string  { return config('sms.jordan_api.endpoint'); }
private function user(): string { return config('sms.jordan_api.user'); }
private function pass(): string { return config('sms.jordan_api.pass'); }
private function sid(): string  { return config('sms.jordan_api.sid'); }
private function type(): int    { return config('sms.jordan_api.type'); }
  
    private function ttl(): int     { return (int)    get_otp_setting('otp_ttl_minutes', 5); }
    private function otpLen(): int  { return (int)    get_otp_setting('otp_length', 6); }

    // ── Phone normalisation ───────────────────────────────────────────────────

    /**
     * Convert any Jordanian/international format to digits-only MSISDN.
     * Examples:
     *   +962799400020  → 962799400020
     *   0799400020     → 962799400020   (Jordanian local with leading 0)
     *   +9639xxxxxxx   → 9639xxxxxxx    (Syrian — strip + only)
     */
    public function normalizeMsisdn(string $input): string
    {
        $digits = preg_replace('/\D+/', '', $input);

        // Already has Jordanian country code
        if (str_starts_with($digits, '962')) {
            return $digits;
        }

        // Jordanian local number (07xxxxxxxx → 10 digits)
        if (str_starts_with($digits, '0') && strlen($digits) <= 10) {
            return '962' . substr($digits, 1);
        }

        // Other international — already stripped of "+" by preg_replace
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

    // ── Core send (cURL POST — matches the working test route) ────────────────

    /**
     * Send a raw SMS via Broadnet websms API.
     *
     * WHY cURL POST:
     *   The working standalone test route uses curl_init + CURLOPT_POST.
     *   Laravel Http::get() was not reaching the API successfully.
     *   We replicate the exact same cURL options here.
     *
     * @return array{success: bool, response: string}
     */
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

        // Guard: if credentials are still empty after all fallbacks, log and fail fast
        if (empty($url) || empty($params['user']) || empty($params['pass'])) {
            Log::error('SmsService: credentials missing', [
                'url'  => $url,
                'user' => $params['user'],
            ]);
            return ['success' => false, 'response' => 'SMS credentials not configured.'];
        }

        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => false,   // Broadnet uses self-signed cert on port 8443
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($params),
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

        $body    = trim((string) $response);
        // Broadnet returns a code starting with "17" on success (e.g. "1701")
        $success = str_starts_with($body, '17');

        if (!$success) {
            Log::warning('SmsService: API returned non-success', [
                'msisdn'    => $msisdn,
                'http_code' => $httpCode,
                'response'  => $body,
            ]);
        } else {
            Log::info('SmsService: SMS sent', [
                'msisdn'   => $msisdn,
                'response' => $body,
            ]);
        }

        return ['success' => $success, 'response' => $body];
    }

    // ── OTP flow ──────────────────────────────────────────────────────────────

    /**
     * Generate + persist + send OTP for the given user.
     * Throws \RuntimeException if the SMS delivery fails.
     */
    public function sendOtp(\App\Models\User $user): array
    { 
  
        $otp       = $this->generateOtp();
        $expiresAt = now()->addMinutes($this->ttl());

        $user->forceFill([
            'otp'            => $otp,
            'otp_expires_at' => $expiresAt,
        ])->saveQuietly();

        $message = "رمز التحقق الخاص بك: {$otp}\nصالح لمدة {$this->ttl()} دقائق.";
        $result  = $this->send($user->phone, $message);

        if (!$result['success']) {
            throw new \RuntimeException('فشل إرسال رمز التحقق. يرجى المحاولة مرة أخرى.');
        }

        return [$otp, $expiresAt];
    }

    // ── OTP verification ──────────────────────────────────────────────────────

    public function verifyOtp(\App\Models\User $user, string $submittedOtp): bool
    {
        if (!$user->otp || !$user->otp_expires_at) {
            return false;
        }
        if (now()->isAfter($user->otp_expires_at)) {
            return false;
        }
        if (!hash_equals($user->otp, trim($submittedOtp))) {
            return false;
        }

        $user->forceFill([
            'otp'               => null,
            'otp_expires_at'    => null,
            'phone_verified_at' => now(),
        ])->saveQuietly();

        return true;
    }

    // ── Admin test ────────────────────────────────────────────────────────────

    public function testConnection(string $testPhone): array
    {
        return $this->send($testPhone, 'اختبار الاتصال بخدمة الرسائل النصية — JbuyApp');
    }
}