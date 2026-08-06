<?php

namespace App\Models;

use App\Models\Concerns\GeneratesUniqueSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

/**
 * Vehicle class / type — Economy, Compact, Sedan, SUV, Luxury, Van…
 */
class VehicleCategory extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia, HasTranslations, GeneratesUniqueSlug;

    public array $translatable = ['name', 'description'];

    protected $fillable = [
        'name', 'description', 'slug', 'icon',
        'price_from', 'is_active', 'is_featured', 'sort_order',
    ];

    protected $casts = [
        'price_from'  => 'decimal:2',
        'is_active'   => 'boolean',
        'is_featured' => 'boolean',
        'sort_order'  => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $category): void {
            $category->slug ??= static::uniqueSlug(
                $category->getTranslation('name', 'en', false)
                    ?: $category->getTranslation('name', 'ar', false),
                'category'
            );
        });
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('vehicle_category_image')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/avif']);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->fit(Fit::Contain, 400, 260)
            ->nonQueued()
            ->performOnCollections('vehicle_category_image');
    }

    // ── Relationships ────────────────────────────────────────────────────────

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    public function activeVehicles(): HasMany
    {
        return $this->vehicles()->where('is_active', true);
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive($query): mixed
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query): mixed
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    public function getImageUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('vehicle_category_image', 'thumb') ?: null;
    }

    /**
     * Lowest daily rate actually offered in this class — falls back to the
     * manually entered price_from when the class has no active vehicles yet.
     */
    public function lowestDailyRate(): ?float
    {
        $lowest = $this->vehicles()
            ->where('is_active', true)
            ->min(DB::raw('COALESCE(discount_daily_rate, daily_rate)'));

        return $lowest !== null ? (float) $lowest : ($this->price_from ? (float) $this->price_from : null);
    }
}
