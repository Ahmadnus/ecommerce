<?php

namespace App\Support;

use App\Models\Setting;

/**
 * Single source of truth for the storefront palette.
 *
 * The admin picks two colours — an accent and an ink — in Settings. Everything
 * else (hover shades, soft tinted backgrounds, ring colours, and the text
 * colour that sits *on top* of a filled accent button) is derived from those
 * two here, so dropping in a new logo colour re-themes the header, booking
 * widget, vehicle cards, badges and footer in one step.
 */
class Brand
{
    /**
     * Fallbacks used when the settings rows don't exist yet. Sampled from the
     * WIND mark: the satin gold of the wave, and the graphite of the wordmark.
     */
    public const DEFAULT_ACCENT = '#C89B4B';
    public const DEFAULT_INK    = '#34322E';

    public static function accent(): string
    {
        return self::normalize(Setting::get('rental_accent_color', self::DEFAULT_ACCENT), self::DEFAULT_ACCENT);
    }

    public static function ink(): string
    {
        return self::normalize(Setting::get('rental_ink_color', self::DEFAULT_INK), self::DEFAULT_INK);
    }

    /**
     * The brand mark. An admin-uploaded logo (the `logo` media collection on
     * Settings) always wins; otherwise the bundled WIND mark is used, so the
     * storefront is branded on a fresh install with nothing uploaded yet.
     */
    public static function logoUrl(): ?string
    {
        $uploaded = Setting::mediaHolder()->getFirstMediaUrl('logo');

        if ($uploaded) {
            return $uploaded;
        }

        return file_exists(public_path(self::FALLBACK_LOGO))
            ? asset(self::FALLBACK_LOGO)
            : null;
    }

    private const FALLBACK_LOGO = 'images/brand/wind-logo.jpg';

    /**
     * The full derived palette, keyed by the token name used in CSS variables
     * and in the Tailwind config (`--accent-600` / `bg-accent-600`).
     *
     * @return array<string, string>
     */
    public static function palette(): array
    {
        $accent = self::accent();
        $ink    = self::ink();

        return [
            'accent'     => $accent,
            'accent-50'  => self::mix($accent, 'white', 0.92),
            'accent-100' => self::mix($accent, 'white', 0.84),
            'accent-200' => self::mix($accent, 'white', 0.68),
            'accent-300' => self::mix($accent, 'white', 0.48),
            'accent-400' => self::mix($accent, 'white', 0.24),
            'accent-600' => self::mix($accent, 'black', 0.14),
            'accent-700' => self::mix($accent, 'black', 0.30),
            'accent-800' => self::mix($accent, 'black', 0.46),

            /*
             * Text/icon colour for anything sitting on a filled accent surface,
             * picked by whichever of ink/white actually contrasts better. A
             * mid-tone gold like the WIND wave carries white at only ~2.5:1 but
             * the dark ink at ~5:1, so a plain "is it light?" test gets it wrong.
             */
            'accent-fg'  => self::contrast($accent, $ink) >= self::contrast($accent, '#ffffff')
                                ? $ink
                                : '#ffffff',

            'ink'        => $ink,
            'ink-700'    => self::mix($ink, 'white', 0.24),
            'ink-500'    => self::mix($ink, 'white', 0.46),
        ];
    }

    /** Clamp anything that isn't a #rgb/#rrggbb value back to the fallback. */
    private static function normalize(?string $hex, string $fallback): string
    {
        $hex = ltrim((string) $hex, '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        return preg_match('/^[0-9a-fA-F]{6}$/', $hex) ? '#'.strtolower($hex) : $fallback;
    }

    /** Blend a colour towards white or black by $amount (0–1). */
    public static function mix(string $hex, string $towards, float $amount): string
    {
        $hex    = ltrim(self::normalize($hex, self::DEFAULT_ACCENT), '#');
        $target = $towards === 'white' ? 255 : 0;

        $out = '#';
        foreach ([0, 2, 4] as $i) {
            $c = hexdec(substr($hex, $i, 2));
            $out .= str_pad(dechex((int) round($c + ($target - $c) * $amount)), 2, '0', STR_PAD_LEFT);
        }

        return $out;
    }

    /** WCAG contrast ratio between two colours, 1 (identical) to 21. */
    public static function contrast(string $a, string $b): float
    {
        $la = self::luminance($a);
        $lb = self::luminance($b);

        return (max($la, $lb) + 0.05) / (min($la, $lb) + 0.05);
    }

    /** WCAG relative luminance, 0 (black) to 1 (white). */
    public static function luminance(string $hex): float
    {
        $hex = ltrim(self::normalize($hex, self::DEFAULT_ACCENT), '#');

        $channel = function (int $v): float {
            $v /= 255;

            return $v <= 0.03928 ? $v / 12.92 : (($v + 0.055) / 1.055) ** 2.4;
        };

        return 0.2126 * $channel((int) hexdec(substr($hex, 0, 2)))
             + 0.7152 * $channel((int) hexdec(substr($hex, 2, 2)))
             + 0.0722 * $channel((int) hexdec(substr($hex, 4, 2)));
    }
}
