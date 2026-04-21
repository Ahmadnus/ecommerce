<?php

/*
|--------------------------------------------------------------------------
| OTP & SMS Settings Helper
|--------------------------------------------------------------------------
| Register this file in composer.json autoload.files
| Then run: composer dump-autoload
*/

if (!function_exists('get_otp_setting')) {
    /**
     * جلب إعدادات الـ OTP من جدول otpsettings حصراً.
     * * الأولويات:
     * 1. قاعدة البيانات (جدول otpsettings)
     * 2. قيمة الـ $default الممررة يدوياً
     * 3. القيم الافتراضية في config/sms.php
     */
    function get_otp_setting(string $key, mixed $default = null): mixed
    {
        // استدعاء الموديل الجديد OtpSetting وليس Setting
        return \App\Models\OtpSetting::get($key, $default);
    }
}

if (!function_exists('set_otp_setting')) {
    /**
     * حفظ الإعداد في جدول otpsettings ومسح الكاش.
     */
    function set_otp_setting(string $key, mixed $value): void
    {
        // استدعاء الموديل الجديد OtpSetting وليس Setting
        \App\Models\OtpSetting::set($key, $value);
    }
}