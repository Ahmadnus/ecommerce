<?php

namespace App\Services;

use App\Models\OtpSetting;

/**
 * SmsSettingsService — business logic for the admin SMS/OTP settings page
 * (otpsettings table). SmsService itself handles sending; this class only
 * manages the stored settings. Never returns views/redirects.
 */
class SmsSettingsService
{
    /**
     * Settings rows + effective values (DB or config fallback) for the page.
     */
    public function getSettingsData(): array
    {
        // جلب الإعدادات من جدول otpsettings وتجميعها بالمفتاح
        $settings  = OtpSetting::where('group', 'sms')->get()->keyBy('key');

        // جلب القيم الفعالة (من القاعدة أو من الـ config كـ fallback)
        $effective = [
            'sms_url'         => get_otp_setting('sms_url'),
            'sms_user'        => get_otp_setting('sms_user'),
            'sms_pass'        => get_otp_setting('sms_pass'),
            'sms_sid'         => get_otp_setting('sms_sid'),
            'sms_type'        => get_otp_setting('sms_type', 4),
            'otp_ttl_minutes' => get_otp_setting('otp_ttl_minutes', 5),
            'otp_length'      => get_otp_setting('otp_length', 6),
        ];

        return compact('settings', 'effective');
    }

    /**
     * Persist validated settings (empty values are stored as null).
     */
    public function saveSettings(array $validated): void
    {
        foreach ($validated as $key => $value) {
            // استخدام ميثود set الموجودة في موديل OtpSetting لمسح الكاش تلقائياً
            OtpSetting::set($key, ($value !== '' && $value !== null) ? $value : null);
        }
    }
}
