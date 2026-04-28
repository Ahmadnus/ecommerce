<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Category extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'description',
        'image',       // kept for legacy/fallback — prefer Spatie going forward
        'depth',
        'path',
        'sort_order',
        'is_active',
        'banner_is_active',
    ];

    protected $casts = [
        'is_active'         => 'boolean',
        'depth'             => 'integer',
        'sort_order'        => 'integer',
        'banner_is_active'  => 'boolean',
    ];

    // ─── Spatie Media Library ─────────────────────────────────────────────────

    /**
     * Register media collections and conversions.
     *
     * Collection  : "category_images"
     * Conversions :
     *   "thumb"    — 200×200 crop  (used in category circles on product listing)
     *   "card"     — 400×400 crop  (used in admin edit preview + larger grids)
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('category_images')
            ->singleFile()
            ->acceptsMimeTypes([
                'image/jpeg',
                'image/png',
                'image/webp',
                'image/gif',
            ]);

        // Keep the legacy "categories" collection name working
        // so existing uploads don't break.
        $this->addMediaCollection('categories')
            ->singleFile();

        // Category banner image collection
        $this->addMediaCollection('category_banner')
            ->singleFile()
            ->acceptsMimeTypes([
                'image/jpeg',
                'image/png',
                'image/webp',
                'image/gif',
            ]);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        // 200×200 — circular UI in storefront
        $this->addMediaConversion('thumb')
            ->fit(Fit::Crop, 200, 200)
            ->nonQueued()
            ->performOnCollections('category_images', 'categories');

        // 400×400 — admin previews and larger displays
        $this->addMediaConversion('card')
            ->fit(Fit::Crop, 400, 400)
            ->nonQueued()
            ->performOnCollections('category_images', 'categories');

        // 1400×400 — banner image
        $this->addMediaConversion('banner')
            ->fit(Fit::Crop, 1400, 400)
            ->nonQueued()
            ->performOnCollections('category_banner');
    }

    /**
     * Returns the category banner image URL, or empty string if none uploaded.
     * Uses the 'category_banner' Spatie media collection.
     */
    public function getBannerImageUrl(string $conversion = 'banner'): string
    {
        return $this->getFirstMediaUrl('category_banner', $conversion);
    }

    /**
     * True when an active banner image exists and should be shown.
     */
    public function shouldShowBanner(): bool
    {
        return $this->banner_is_active && !empty($this->getBannerImageUrl());
    }

    // ─── Image URL accessor ───────────────────────────────────────────────────

    /**
     * Returns the best available circular thumbnail URL.
     *
     * Priority:
     *   1. Spatie 'category_images' collection → 'thumb' conversion
     *   2. Spatie 'categories'       collection → 'thumb' conversion  (legacy)
     *   3. Fallback placeholder image
     *
     * @param  string  $conversion  'thumb' | 'card' | ''
     */
    public function getCategoryImageUrl(string $conversion = 'thumb'): string
    {
        // Try the new collection first
        $url = $this->getFirstMediaUrl('category_images', $conversion);
        if ($url) {
            return $url;
        }

        // Fall back to the legacy collection
        $url = $this->getFirstMediaUrl('categories', $conversion);
        if ($url) {
            return $url;
        }

        // Hard-coded SVG placeholder — always works without a DB/file hit
        return $this->generatePlaceholderUrl();
    }

    /**
     * Convenience accessor — always returns the 'thumb' URL.
     * Use in Blade: {{ $category->image_url }}
     */
    public function getImageUrlAttribute(): string
    {
        return $this->getCategoryImageUrl('thumb');
    }

    /**
     * True when this category has any real uploaded image.
     */
    public function hasImage(): bool
    {
        return $this->getFirstMedia('category_images') !== null
            || $this->getFirstMedia('categories') !== null;
    }

    /**
     * Deterministic placeholder using the category's initial letter.
     * Returns a data-URI SVG so no network request is needed.
     */
    private function generatePlaceholderUrl(): string
    {
        $initial = mb_substr($this->name ?? '?', 0, 1);
        $colours = [
            '#f97316', '#ec4899', '#8b5cf6', '#06b6d4',
            '#10b981', '#f59e0b', '#6366f1', '#ef4444',
        ];
        $bg = $colours[crc32($this->name ?? '') % count($colours)];
        $svg = rawurlencode(
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200">'
            . '<circle cx="100" cy="100" r="100" fill="' . $bg . '"/>'
            . '<text x="100" y="115" text-anchor="middle" font-size="90" '
            . 'font-family="sans-serif" fill="white">' . $initial . '</text>'
            . '</svg>'
        );

        return 'data:image/svg+xml,' . $svg;
    }

    // ─── Boot ─────────────────────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (self $cat): void {
            $cat->slug ??= Str::slug($cat->name);
        });

        static::created(function (self $cat): void {
            $cat->rebuildPath();
        });

        static::updating(function (self $cat): void {
            if ($cat->isDirty('parent_id')) {
                $cat->rebuildPath();
            }
        });
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')
            ->orderBy('sort_order')
            ->orderBy('name');
    }

    public function activeChildren(): HasMany
    {
        return $this->children()->where('is_active', true);
    }

    public function allChildren(): HasMany
    {
        return $this->children()->with('allChildren');
    }

    public function allActiveChildren(): HasMany
    {
        return $this->activeChildren()->with('allActiveChildren');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'category_product')
            ->withPivot('is_primary')
            ->withTimestamps();
    }

    // ─── Path & breadcrumb helpers ────────────────────────────────────────────

    public function getAllDescendants(): Collection
    {
        return self::where('path', 'like', $this->path . '/%')
            ->orderBy('depth')
            ->orderBy('sort_order')
            ->get();
    }

    public function getAncestors(): Collection
    {
        if (blank($this->path)) {
            return new Collection();
        }

        $ids = collect(explode('/', $this->path))
            ->map(fn (string $id) => (int) $id)
            ->filter(fn (int $id) => $id !== $this->id)
            ->values();

        return self::whereIn('id', $ids)->orderBy('depth')->get();
    }

    public function getBreadcrumbAttribute(): string
    {
        return $this->getAncestors()->push($this)->pluck('name')->implode(' / ');
    }

    public function rebuildPath(): void
    {
        $parentPath = $this->parent?->path ?? '';
        $this->path = $parentPath ? "{$parentPath}/{$this->id}" : (string) $this->id;
        $this->depth = $this->parent ? $this->parent->depth + 1 : 0;
        $this->saveQuietly();
    }

    public function isRoot(): bool
    {
        return $this->parent_id === null;
    }

    public function isLeaf(): bool
    {
        return !$this->children()->exists();
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive($query): mixed
    {
        return $query->where('is_active', true);
    }

    public function scopeRoots($query): mixed
    {
        return $query->whereNull('parent_id');
    }

    public function scopeAtDepth($query, int $depth): mixed
    {
        return $query->where('depth', $depth);
    }
}