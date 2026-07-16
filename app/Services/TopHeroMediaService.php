<?php

namespace App\Services;

use App\Models\TopHeroMedia;
use Illuminate\Support\Facades\DB;

/**
 * TopHeroMediaService — business logic for the admin top-hero-media CRUD,
 * including the hero media file collection. Never returns views/redirects.
 */
class TopHeroMediaService
{
    public function getAll()
    {
        return TopHeroMedia::orderBy('sort_order')->get();
    }

    public function getActive(string $position = 'top'): ?TopHeroMedia
    {
        return TopHeroMedia::where('is_active', true)
            ->where('position', $position)
            ->orderBy('sort_order')
            ->first();
    }

    /**
     * Create a hero media entry with its file.
     *
     * @throws \Throwable on failure (transaction rolled back)
     */
    public function create(array $attributes, $file): TopHeroMedia
    {
        try {
            return DB::transaction(function () use ($attributes, $file) {
                if ($attributes['is_active'] ?? false) {
                    TopHeroMedia::where('is_active', true)
                        ->where('position', $attributes['position'] ?? 'top')
                        ->update(['is_active' => false]);
                }

                $hero = TopHeroMedia::create($attributes);

                if ($file) {
                    $hero->addMedia($file)->toMediaCollection('hero_media');
                }

                return $hero;
            });
        } catch (\Throwable $e) {
            report($e);
            throw $e;
        }
    }

    /**
     * Update a hero media entry; a new file replaces the existing one.
     *
     * @throws \Throwable on failure (transaction rolled back)
     */
    public function update(TopHeroMedia $hero, array $attributes, $file): TopHeroMedia
    {
        try {
            return DB::transaction(function () use ($hero, $attributes, $file) {
                if (($attributes['is_active'] ?? false) && ! $hero->is_active) {
                    TopHeroMedia::where('is_active', true)
                        ->where('position', $attributes['position'] ?? $hero->position)
                        ->update(['is_active' => false]);
                }

                $hero->update($attributes);

                if ($file) {
                    $hero->clearMediaCollection('hero_media');
                    $hero->addMedia($file)->toMediaCollection('hero_media');
                }

                return $hero;
            });
        } catch (\Throwable $e) {
            report($e);
            throw $e;
        }
    }

    public function delete(TopHeroMedia $hero): void
    {
        $hero->delete();
    }
}
