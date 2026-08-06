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
use Spatie\Translatable\HasTranslations; // ← ADD

class Category extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;
    use HasTranslations; // ← ADD

    // ── Declare translatable fields ────────────────────────────────────────
    public array $translatable = ['name', 'description'];

    protected $fillable = [
        'parent_id',
        'name',         // json
        'slug',
        'description',  // json
        'image',
        'depth',
        'path',
        'sort_order',
        'is_active',
        'banner_is_active',
    ];

    protected $casts = [
        'is_active'        => 'boolean',
        'depth'            => 'integer',
        'sort_order'       => 'integer',
        'banner_is_active' => 'boolean',
        // DO NOT cast 'name' or 'description' — Spatie handles their JSON
    ];

    // ─── Spatie Media Library ─────────────────────────────────────────────

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('category_images')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif']);

        $this->addMediaCollection('categories')   // legacy — keep for existing uploads
            ->singleFile();

        $this->addMediaCollection('category_banner')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif']);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->fit(Fit::Crop, 200, 200)
            ->nonQueued()
            ->performOnCollections('category_images', 'categories');

        $this->addMediaConversion('card')
            ->fit(Fit::Crop, 400, 400)
            ->nonQueued()
            ->performOnCollections('category_images', 'categories');

        $this->addMediaConversion('banner')
            ->fit(Fit::Crop, 1400, 400)
            ->nonQueued()
            ->performOnCollections('category_banner');
    }

    // ─── Boot ─────────────────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (self $cat): void {
            // Slug from Arabic name first, fall back to English
            $cat->slug ??= Str::slug(
                $cat->getTranslation('name', 'ar', false)
                ?: $cat->getTranslation('name', 'en', false)
            );
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

    // ─── Relationships ────────────────────────────────────────────────────

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')
            ->orderBy('sort_order')
            ->orderBy('name');  // Spatie sorts by the current locale value automatically
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

    // ─── Image helpers (unchanged — these are media, not translatable) ────

    public function getBannerImageUrl(string $conversion = 'banner'): string
    {
        return $this->getFirstMediaUrl('category_banner', $conversion);
    }

    public function shouldShowBanner(): bool
    {
        return $this->banner_is_active && !empty($this->getBannerImageUrl());
    }

    public function getCategoryImageUrl(string $conversion = 'thumb'): string
    {
        $url = $this->getFirstMediaUrl('category_images', $conversion);
        if ($url) return $url;

        $url = $this->getFirstMediaUrl('categories', $conversion);
        if ($url) return $url;

        return $this->generatePlaceholderUrl();
    }

    public function getImageUrlAttribute(): string
    {
        return $this->getCategoryImageUrl('thumb');
    }

    public function hasImage(): bool
    {
        return $this->getFirstMedia('category_images') !== null
            || $this->getFirstMedia('categories') !== null;
    }

    private function generatePlaceholderUrl(): string
    {
        // Use the current-locale name for the initial letter
        $initial = mb_substr($this->name ?? '?', 0, 1);
        $colours = [
            '#f97316', '#ec4899', '#8b5cf6', '#06b6d4',
            '#10b981', '#f59e0b', '#6366f1', '#ef4444',
        ];
        // Use the raw JSON string for a stable hash regardless of locale
        $raw = $this->getRawOriginal('name') ?? $this->name;
        $bg  = $colours[crc32((string) $raw) % count($colours)];

        $svg = rawurlencode(
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200">'
            . '<circle cx="100" cy="100" r="100" fill="' . $bg . '"/>'
            . '<text x="100" y="115" text-anchor="middle" font-size="90" '
            . 'font-family="sans-serif" fill="white">' . $initial . '</text>'
            . '</svg>'
        );

        return 'data:image/svg+xml,' . $svg;
    }

    // ─── Path & breadcrumb helpers ────────────────────────────────────────

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
            ->map(fn(string $id) => (int) $id)
            ->filter(fn(int $id) => $id !== $this->id)
            ->values();

        return self::whereIn('id', $ids)->orderBy('depth')->get();
    }

    public function getBreadcrumbAttribute(): string
    {
        // $this->name automatically returns current-locale value
        return $this->getAncestors()->push($this)->pluck('name')->implode(' / ');
    }

    public function rebuildPath(): void
    {
        $parentPath  = $this->parent?->path ?? '';
        $this->path  = $parentPath ? "{$parentPath}/{$this->id}" : (string) $this->id;
        $this->depth = $this->parent ? $this->parent->depth + 1 : 0;
        $this->saveQuietly();
    }

    public function isRoot(): bool { return $this->parent_id === null; }
    public function isLeaf(): bool { return !$this->children()->exists(); }

    // ─── Scopes ───────────────────────────────────────────────────────────

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