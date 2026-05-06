<?php

namespace App\Helpers;

use App\Models\Currency;

class CurrencyHelper
{
    /**
     * Get the active display currency.
     * Reads from session, falls back to base currency.
     */
    public static function activeCurrency(): Currency
    {
        static $cache = null;

        if ($cache) {
            return $cache;
        }

        $code = session('currency_code');

        if ($code) {
            $currency = Currency::where('code', $code)
                                ->where('is_active', true)
                                ->first();
            if ($currency) {
                return $cache = $currency;
            }
        }

        return $cache = Currency::where('is_base', true)->first()
            ?? Currency::where('is_active', true)->orderBy('sort_order')->firstOrFail();
    }

    /**
     * Convert a JOD amount for display in the active currency.
     */
    public static function convert(float $jod): float
    {
        return static::activeCurrency()->convert($jod);
    }

    /**
     * Format a JOD amount as a display string.
     * e.g. "$12.50" or "12.50 د.أ"
     */
    public static function format(float $jod): string
    {
        return static::activeCurrency()->format($jod);
    }
}