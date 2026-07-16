<?php

namespace App\Services;

use App\Models\HomepageSection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * HomepageSectionService — business logic for the admin homepage-sections
 * CRUD (dynamic tall media / title / paragraph / CTA blocks rendered on
 * the storefront homepage). Never returns views/redirects.
 */
class HomepageSectionService
{
    private const DISK = 'public';
    private const DIR  = 'homepage-sections';

    public function getAll()
    {
        return HomepageSection::orderBy('sort_order')->get();
    }

    public function getActiveOrdered()
    {
        return HomepageSection::active()->ordered()->get();
    }

    /**
     * @throws \Throwable on failure (transaction rolled back)
     */
    public function create(array $attributes, ?UploadedFile $file): HomepageSection
    {
        try {
            return DB::transaction(function () use ($attributes, $file) {
                if ($file) {
                    $attributes['media_path'] = $file->store(self::DIR, self::DISK);
                }

                return HomepageSection::create($attributes);
            });
        } catch (\Throwable $e) {
            report($e);
            throw $e;
        }
    }

    /**
     * @throws \Throwable on failure (transaction rolled back)
     */
    public function update(HomepageSection $section, array $attributes, ?UploadedFile $file): HomepageSection
    {
        try {
            return DB::transaction(function () use ($section, $attributes, $file) {
                if ($file) {
                    if ($section->media_path) {
                        Storage::disk(self::DISK)->delete($section->media_path);
                    }
                    $attributes['media_path'] = $file->store(self::DIR, self::DISK);
                }

                $section->update($attributes);

                return $section;
            });
        } catch (\Throwable $e) {
            report($e);
            throw $e;
        }
    }

    public function delete(HomepageSection $section): void
    {
        if ($section->media_path) {
            Storage::disk(self::DISK)->delete($section->media_path);
        }

        $section->delete();
    }
}
