<?php

namespace App\Services;

use App\Models\Setting;

/**
 * SettingService — business logic for the admin general settings page
 * (key/value settings + logo/favicon media). Never returns views/redirects.
 */
class SettingService
{
    /** Keys the general settings page manages. */
    public const KEYS = [
        'primary_color', 'bg_color', 'nav_bg_color',
        'card_bg_color', 'footer_bg_color', 'footer_text_color',
        'footer_link_color', 'footer_bottom_text_color',
        'footer_text_size', 'site_name',
        'splash_title_main', 'splash_title_sub',
        'splash_color_main', 'splash_color_sub',
        'splash_font_size', 'splash_font_family',
        'font_ar', 'font_en',
    ];

    /** Keys the splash settings page manages. */
    public const SPLASH_KEYS = [
        'splash_title_main', 'splash_title_sub',
        'splash_color_main', 'splash_color_sub',
        'splash_loading_text', 'splash_font_size', 'splash_font_family',
    ];

    /**
     * Splash settings values for the admin splash page.
     */
    public function getSplashSettings()
    {
        return Setting::whereIn('key', self::SPLASH_KEYS)->pluck('value', 'key');
    }

    /**
     * Persist validated splash settings (null values stored as '').
     */
    public function saveSplashSettings(array $data): void
    {
        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value ?? ''] // إذا كانت القيمة نل نضع نص فارغ
            );
        }
    }

    /**
     * All data the settings page needs.
     */
    public function getIndexData(): array
    {
        $siteSettings = Setting::pluck('value', 'key');
        $holder       = Setting::mediaHolder();
        $logoUrl      = $holder->getFirstMediaUrl('logo');
        $faviconUrl   = $holder->getFirstMediaUrl('favicon');

        return compact('siteSettings', 'logoUrl', 'faviconUrl');
    }

    /**
     * Persist submitted settings (only keys present in $input) and the
     * optional logo/favicon uploads.
     */
    public function saveSettings(array $input, $logo, $favicon): void
    {
        foreach (self::KEYS as $key) {
            if (array_key_exists($key, $input)) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $input[$key]]
                );
            }
        }

        $holder = Setting::mediaHolder();

        if ($logo) {
            $holder->addMedia($logo)->toMediaCollection('logo');
        }

        if ($favicon) {
            $holder->addMedia($favicon)->toMediaCollection('favicon');
        }
    }
}
