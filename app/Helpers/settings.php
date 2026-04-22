<?php

/*
|--------------------------------------------------------------------------
| OTP Settings Helper
|--------------------------------------------------------------------------
| Add to composer.json autoload.files, then run: composer dump-autoload
|
|   "autoload": {
|       "files": ["app/Helpers/otp_settings.php"]
|   }
*/

if (!function_exists('get_otp_setting')) {
    /**
     * Fetch a value from the otpsettings table.
     *
     * Priority:
     *   1. DB (otpsettings table, cached 60 min)
     *   2. $default argument
     *   3. config/sms.php fallback
     */
    function get_otp_setting(string $key, mixed $default = null): mixed
    {
        return \App\Models\OtpSetting::get($key, $default);
    }
}

if (!function_exists('set_otp_setting')) {
    /**
     * Persist a value to otpsettings and bust its cache.
     */
    function set_otp_setting(string $key, mixed $value): void
    {
        \App\Models\OtpSetting::set($key, $value);
    }
}