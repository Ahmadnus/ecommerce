<?php

namespace App\Services;

use App\Models\SeoSetting;
use Illuminate\Support\Facades\DB;

/**
 * SeoSettingService — business logic for the admin SEO settings pages
 * (per-type settings + og_image/favicon media). Never returns views/redirects.
 */
class SeoSettingService
{
    public const TYPES = ['main', 'splash'];

    public function isValidType(string $type): bool
    {
        return in_array($type, self::TYPES);
    }

    /**
     * One SeoSetting (existing or new) per type, keyed by type.
     */
    public function getAllByType()
    {
        return collect(self::TYPES)->mapWithKeys(
            fn($type) => [$type => SeoSetting::firstOrNew(['type' => $type])]
        );
    }

    public function getForType(string $type): SeoSetting
    {
        return SeoSetting::firstOrNew(['type' => $type]);
    }

    /**
     * Save the settings row for a type plus optional media uploads.
     *
     * @throws \Throwable on failure (transaction rolled back)
     */
    public function save(string $type, array $attributes, $ogImage, $favicon): SeoSetting
    {
        try {
            return DB::transaction(function () use ($type, $attributes, $ogImage, $favicon) {
                $seo = SeoSetting::firstOrNew(['type' => $type]);
                $seo->fill($attributes);
                $seo->save();

                if ($ogImage) {
                    $seo->addMedia($ogImage)->toMediaCollection('og_image');
                }

                if ($favicon) {
                    $seo->addMedia($favicon)->toMediaCollection('favicon');
                }

                return $seo;
            });
        } catch (\Throwable $e) {
            report($e);
            throw $e;
        }
    }
}
