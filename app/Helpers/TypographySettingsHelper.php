<?php

namespace App\Helpers;

use App\Models\Setting;

class TypographySettingsHelper
{
    private static array $fontSizeDefaults = [
        'base_font_size'          => '16px',
        'navbar_font_size'        => '14px',
        'card_font_size'          => '13px',
        'heading_font_size'       => '32px',
        'subheading_font_size'    => '20px',
        'footer_font_size'        => '14px',
        'button_font_size'        => '14px',
        'product_title_font_size' => '13px',
        'product_price_font_size' => '15px',
    ];

    private static array $colorDefaults = [
        'body_text_color'                => '#111827',
        'heading_text_color'             => '#0f172a',
        'muted_text_color'               => '#9ca3af',
        'navbar_text_color'              => '#374151',
        'card_text_color'                => '#374151',
        'footer_text_color'              => '#9ca3af',
        'button_text_color'              => '#ffffff',
        'badge_text_color'               => '#ffffff',
        'price_text_color'               => '#dc2626',
        'input_text_color'               => '#111827',
        'product_title_text_color'       => '#111827',
        'product_description_text_color' => '#6b7280',
    ];

    /** Returns all 21 settings as a flat array — used in layouts/app.blade.php */
    public static function all(): array
    {
        $result = [];

        foreach (self::$fontSizeDefaults as $key => $default) {
            $raw = Setting::get($key, $default);
            $result[$key] = self::normalizeFontSize($raw, $default);
        }

        foreach (self::$colorDefaults as $key => $default) {
            $raw = Setting::get($key, $default);
            $result[$key] = self::normalizeColor($raw, $default);
        }

        return $result;
    }

    public static function fontSizeKeys(): array
    {
        return self::$fontSizeDefaults;
    }

    public static function colorKeys(): array
    {
        return self::$colorDefaults;
    }

    // ── Sanitizers ─────────────────────────────────────────────────────────

    private static function normalizeFontSize(string $value, string $default): string
    {
        $value = trim($value);
        if ($value === '') return $default;
        // "16" → "16px"
        if (is_numeric($value)) return $value . 'px';
        return $value;
    }

    private static function normalizeColor(string $value, string $default): string
    {
        $value = trim($value);
        if ($value === '') return $default;
        // "111827" → "#111827"
        if (preg_match('/^[0-9a-fA-F]{3,8}$/', $value)) return '#' . $value;
        return $value;
    }
}