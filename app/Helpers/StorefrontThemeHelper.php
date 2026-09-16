<?php

namespace App\Helpers;

use App\Models\Setting;

/**
 * StorefrontThemeHelper — design tokens for the storefront shell.
 *
 * Companion to TypographySettingsHelper: that one owns fonts/sizes/text
 * colours, this one owns the *structural* design language (surfaces,
 * borders, radii, shadows, product-card geometry, layout rhythm).
 *
 * Every value lands in :root as a CSS variable (see layouts/app.blade.php)
 * and is consumed by public/css/storefront.css. Nothing in the storefront
 * markup should carry a literal colour, radius or shadow — the whole visual
 * identity of a tenant's store is reconfigurable from these keys plus the
 * existing primary_color / bg_color / nav_bg_color / card_bg_color ones.
 */
class StorefrontThemeHelper
{
    /** Colour tokens — accept #rgb/#rrggbb, bare hex, or any CSS colour. */
    private static array $colorDefaults = [
        // Sale / discount / destructive. Functional, not brand: it must stay
        // legible against card_bg no matter what primary_color becomes.
        'accent_color'        => '#f55157',
        // Hairlines: card borders, dividers, input outlines.
        'border_color'        => '#f0f0f0',
        // Neutral "quiet" surface: add-to-cart rest state, chips, skeletons.
        'subtle_bg_color'     => '#f6f6f6',
        // Badge background (the "-20%" / "new" pills on product cards).
        'badge_bg_color'      => '#f55157',
        // Footer's lower strip, which the reference inverts against the body.
        'footer_bottom_bg'    => '#ffffff',
    ];

    /** Geometry tokens — accept a bare number (→px) or any CSS length. */
    private static array $sizeDefaults = [
        'radius_card'         => '0px',
        'radius_button'       => '0px',
        'radius_input'        => '4px',
        'radius_badge'        => '999px',
        'card_image_height'   => '16.3rem',  // desktop product-card image box
        'card_image_height_sm'=> '11rem',    // mobile product-card image box
        'card_border_width'   => '1px',
        'container_max_width' => '1280px',
        'section_gap'         => '2.5rem',   // vertical rhythm between sections
        'header_height'       => '72px',
    ];

    /** Free-form tokens validated against a fixed set of options. */
    private static array $enumDefaults = [
        // How product photos fill their box. "contain" matches the reference
        // (packshots on white); "cover" suits lifestyle photography.
        'card_image_fit'      => ['contain', ['contain', 'cover']],

        // What the header shows as the brand:
        //   auto — the uploaded logo when there is one, otherwise the name
        //   logo — always the uploaded logo
        //   text — always the store name as a wordmark
        //   both — the logo with the store name beside it
        'header_brand_mode'   => ['auto', ['auto', 'logo', 'text', 'both']],
    ];

    /** Shadow tokens — free-form CSS box-shadow values. */
    private static array $shadowDefaults = [
        'shadow_card'         => 'none',
        'shadow_card_hover'   => '0 0 5px rgba(0,0,0,.20)',
        'shadow_overlay'      => '0 12px 40px rgba(0,0,0,.18)',
    ];

    /** Every key this helper manages, for the admin settings form. */
    public static function keys(): array
    {
        return array_merge(
            array_keys(self::$colorDefaults),
            array_keys(self::$sizeDefaults),
            array_keys(self::$enumDefaults),
            array_keys(self::$shadowDefaults),
        );
    }

    public static function colorKeys(): array  { return self::$colorDefaults; }
    public static function sizeKeys(): array   { return self::$sizeDefaults; }
    public static function shadowKeys(): array { return self::$shadowDefaults; }
    public static function enumKeys(): array   { return self::$enumDefaults; }

    /** Resolved token values, keyed exactly as above. */
    public static function all(): array
    {
        $result = [];

        foreach (self::$colorDefaults as $key => $default) {
            $result[$key] = self::normalizeColor(Setting::get($key, $default), $default);
        }

        foreach (self::$sizeDefaults as $key => $default) {
            $result[$key] = self::normalizeSize(Setting::get($key, $default), $default);
        }

        foreach (self::$enumDefaults as $key => [$default, $allowed]) {
            $raw = trim((string) Setting::get($key, $default));
            $result[$key] = in_array($raw, $allowed, true) ? $raw : $default;
        }

        foreach (self::$shadowDefaults as $key => $default) {
            $result[$key] = self::normalizeShadow(Setting::get($key, $default), $default);
        }

        return $result;
    }

    // ── Sanitizers ─────────────────────────────────────────────────────────
    // These values are interpolated straight into a <style> block, so each
    // one is validated rather than escaped: a stray "}" or ";" from the
    // settings table must never be able to terminate the rule it sits in.

    private static function normalizeColor(mixed $value, string $default): string
    {
        $value = trim((string) $value);
        if ($value === '') return $default;
        if (preg_match('/^[0-9a-fA-F]{3,8}$/', $value)) return '#' . $value;
        // #hex, rgb()/rgba()/hsl()/hsla(), or a bare CSS colour keyword.
        if (preg_match('/^(#[0-9a-fA-F]{3,8}|(rgb|hsl)a?\([0-9a-zA-Z.,%\/\s]+\)|[a-zA-Z]+)$/', $value)) {
            return $value;
        }
        return $default;
    }

    private static function normalizeSize(mixed $value, string $default): string
    {
        $value = trim((string) $value);
        if ($value === '') return $default;
        if (is_numeric($value)) return $value . 'px';
        if (preg_match('/^-?[0-9.]+(px|rem|em|%|vh|vw|vmin|vmax)$/', $value)) return $value;
        return $default;
    }

    private static function normalizeShadow(mixed $value, string $default): string
    {
        $value = trim((string) $value);
        if ($value === '') return 'none';
        // Shadows are compound values; allow their grammar but nothing that
        // could break out of the declaration.
        if (preg_match('/^[0-9a-zA-Z.,%()#\/\s-]+$/', $value) && !str_contains($value, '}')) {
            return $value;
        }
        return $default;
    }
}
