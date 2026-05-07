<?php
// app/Models/SeoSetting.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class SeoSetting extends Model implements HasMedia
{
    use HasTranslations, InteractsWithMedia;

    protected $table = 'seo_settings';

    protected $fillable = [
        'type',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'og_title',
        'og_description',
        'twitter_title',
        'twitter_description',
        'canonical_url',
        'robots',
        'twitter_card',
        'og_type',
        'is_active',
    ];

    /**
     * Fields managed by Spatie Translatable (stored as JSON with locale keys)
     */
    public array $translatable = [
        'seo_title',
        'seo_description',
        'seo_keywords',
        'og_title',
        'og_description',
        'twitter_title',
        'twitter_description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('og_image')
            ->singleFile();

        $this->addMediaCollection('favicon')
            ->singleFile();
    }

    /**
     * Retrieve SEO settings for a given layout type.
     * Falls back to a new unsaved instance so views never get null.
     */
    public static function forType(string $type): self
    {
        return static::where('type', $type)
            ->where('is_active', true)
            ->firstOrNew(['type' => $type]);
    }
}