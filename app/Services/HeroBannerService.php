<?php

namespace App\Services;

use App\Models\HeroBanner;
use Illuminate\Support\Facades\DB;

/**
 * HeroBannerService — business logic for the admin hero banners CRUD,
 * including the banner image media collection. Never returns views/redirects.
 */
class HeroBannerService
{
    public function getBanners()
    {
        return HeroBanner::orderBy('sort_order')->get();
    }

    /**
     * Create a banner with optional image.
     *
     * @throws \Throwable on failure (transaction rolled back)
     */
    public function create(array $attributes, $image): HeroBanner
    {
        try {
            return DB::transaction(function () use ($attributes, $image) {
                $banner = HeroBanner::create($attributes);

                if ($image) {
                    $banner->addMedia($image)->toMediaCollection('banner_image');
                }

                return $banner;
            });
        } catch (\Throwable $e) {
            report($e);
            throw $e;
        }
    }

    /**
     * Update a banner; a new image replaces the existing one.
     *
     * @throws \Throwable on failure (transaction rolled back)
     */
    public function update(HeroBanner $banner, array $attributes, $image): HeroBanner
    {
        try {
            return DB::transaction(function () use ($banner, $attributes, $image) {
                $banner->update($attributes);

                if ($image) {
                    $banner->clearMediaCollection('banner_image');
                    $banner->addMedia($image)->toMediaCollection('banner_image');
                }

                return $banner;
            });
        } catch (\Throwable $e) {
            report($e);
            throw $e;
        }
    }

    public function delete(HeroBanner $banner): void
    {
        $banner->delete();
    }
}
