<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class OtpSetting extends Model
{
    // تحديد اسم الجدول يدوياً لضمان الربط مع otpsettings
    protected $table = 'otpsettings';

    protected $fillable = ['key', 'value', 'group', 'label', 'is_secret'];
    
    protected $casts = [
        'is_secret' => 'boolean'
    ];

    /**
     * مفتاح التخزين المؤقت (Cache)
     */
    public static function cacheKey(string $key): string
    {
        return 'otp_setting:' . $key;
    }

    /**
     * جلب الإعداد مع مراعاة الأولويات:
     * 1. القيمة من قاعدة البيانات (otpsettings table).
     * 2. قيمة الـ $default الممررة للدالة.
     * 3. القيمة الافتراضية من config/sms.php.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        // محاولة جلب القيمة من الكاش أو قاعدة البيانات
        $dbValue = Cache::remember(static::cacheKey($key), 3600, function () use ($key) {
            return static::where('key', $key)->value('value');
        });

        // إذا وجدت القيمة في قاعدة البيانات وليست فارغة
        if (!is_null($dbValue) && $dbValue !== '') {
            return $dbValue;
        }

        // إذا تم تمرير قيمة افتراضية يدوياً للدالة
        if (!is_null($default)) {
            return $default;
        }

        /**
         * محاولة الجلب من config/sms.php
         * نقوم بتحويل المفاتيح مثل 'sms_url' إلى 'endpoint' لتتطابق مع ملف الـ config
         */
        $configMapping = [
            'sms_url'  => 'sms.jordan_api.endpoint',
            'sms_user' => 'sms.jordan_api.user',
            'sms_pass' => 'sms.jordan_api.pass',
            'sms_sid'  => 'sms.jordan_api.sid',
            'sms_type' => 'sms.jordan_api.type',
            'otp_ttl_minutes' => 'sms.otp_ttl_minutes',
            'otp_length'      => 'sms.otp_length',
        ];

        $configKey = $configMapping[$key] ?? 'sms.' . str_replace('sms_', '', $key);

        return config($configKey);
    }

    /**
     * تحديث أو إنشاء إعداد جديد مع مسح الكاش
     */
    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget(static::cacheKey($key));
    }

    /**
     * مسح الكاش لجميع الإعدادات
     */
    public static function flushCache(): void
    {
        static::pluck('key')->each(fn($k) => Cache::forget(static::cacheKey($k)));
    }
}