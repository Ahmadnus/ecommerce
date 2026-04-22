<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class OtpSetting extends Model
{
    protected $table    = 'otpsettings';
    protected $fillable = ['key', 'value', 'group', 'label', 'is_secret'];
    protected $casts    = ['is_secret' => 'boolean'];

    // ── Cache ─────────────────────────────────────────────────────────────────

    public static function cacheKey(string $key): string
    {
        return 'otp_setting:' . $key;
    }

    // ── Read ──────────────────────────────────────────────────────────────────

    /**
     * Priority:
     *   1. otpsettings table (DB, cached 60 min)
     *   2. $default argument
     *   3. config/sms.php via the mapping below
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $dbValue = Cache::remember(static::cacheKey($key), 3600, function () use ($key) {
            return static::where('key', $key)->value('value');
        });

        if (!is_null($dbValue) && $dbValue !== '') {
            return $dbValue;
        }

        if (!is_null($default)) {
            return $default;
        }

        // ── Config fallback — keys map to config/sms.php structure ───────────
        $configMapping = [
            'sms_url'         => 'sms.jordan_api.endpoint',
            'sms_user'        => 'sms.jordan_api.user',
            'sms_pass'        => 'sms.jordan_api.pass',
            'sms_sid'         => 'sms.jordan_api.sid',
            'sms_type'        => 'sms.jordan_api.type',
            'otp_ttl_minutes' => 'sms.otp_ttl_minutes',
            'otp_length'      => 'sms.otp_length',
        ];

        $configKey = $configMapping[$key] ?? null;
        return $configKey ? config($configKey) : null;
    }

    // ── Write ─────────────────────────────────────────────────────────────────

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget(static::cacheKey($key));
    }

    public static function flushCache(): void
    {
        static::pluck('key')->each(fn($k) => Cache::forget(static::cacheKey($k)));
    }
}