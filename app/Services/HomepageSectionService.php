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
    private const DIR = 'homepage-sections';

    /**
     * Resolve the storage disk at call time (not a class const) so it always
     * matches whatever disk the rest of the app's media (Spatie Media
     * Library — Category/Product/HeroBanner/etc.) is actually using. On
     * shared hosting this is commonly a custom disk (e.g. "public_html")
     * whose root is the real web-servable folder, distinct from Laravel's
     * default "public" disk — hardcoding "public" here silently wrote files
     * to a path the webserver never serves, causing 404s on upload.
     */
    private function disk(): string
    {
        return config('media-library.disk_name', 'public');
    }

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
                    $attributes['media_path'] = $file->store(self::DIR, $this->disk());
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
                        Storage::disk($this->disk())->delete($section->media_path);
                    }
                    $attributes['media_path'] = $file->store(self::DIR, $this->disk());
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
            Storage::disk($this->disk())->delete($section->media_path);
        }

        $section->delete();
    }
}
