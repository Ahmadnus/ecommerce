<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

use Illuminate\Database\Eloquent\Collection;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Category extends Model implements HasMedia
{
    use HasFactory, SoftDeletes , InteractsWithMedia;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'description',
        'image',
        'depth',
        'path',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'depth'      => 'integer',
        'sort_order' => 'integer',
    ];

    // ─── Boot: auto-slug + rebuild path after create ──────────────────────────

    protected static function booted(): void
    {
        static::creating(function (self $category): void {
            $category->slug ??= Str::slug($category->name);
        });

        static::created(function (self $category): void {
            $category->rebuildPath();
        });

        static::updating(function (self $category): void {
            // Rebuild if parent changed
            if ($category->isDirty('parent_id')) {
                $category->rebuildPath();
            }
        });
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    /** Immediate parent */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /** Direct children, ordered */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')
                    ->orderBy('sort_order')
                    ->orderBy('name');
    }

    /** Active children only */
    public function activeChildren(): HasMany
    {
        return $this->children()->where('is_active', true);
    }

    /**
     * Recursive eager-loadable relationship.
     * Usage:  Category::with('allChildren')->whereNull('parent_id')->get()
     */
    public function allChildren(): HasMany
    {
        return $this->children()->with('allChildren');
    }

    /**
     * Same as allChildren but restricted to active nodes.
     * Use this in the public-facing sidebar.
     */
    public function allActiveChildren(): HasMany
    {
        return $this->activeChildren()->with('allActiveChildren');
    }

    /** Products linked to this category (many-to-many) */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'category_product')
                    ->withPivot('is_primary')
                    ->withTimestamps();
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    /**
     * All descendants via materialized path — single query, no recursion.
     */
    public function getAllDescendants(): Collection
    {
        return self::where('path', 'like', $this->path . '/%')
                   ->orderBy('depth')
                   ->orderBy('sort_order')
                   ->get();
    }

    /**
     * Ancestor chain (root → parent) using materialized path.
     */
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

    /** "Clothing / Men / Pants" breadcrumb string */
    public function getBreadcrumbAttribute(): string
    {
        return $this->getAncestors()
                    ->push($this)
                    ->pluck('name')
                    ->implode(' / ');
    }

    /** Full public image URL, Railway storage:link compatible */
    public function getImageUrlAttribute(): ?string
    {
        return $this->image
            ? Storage::url($this->image)
            : null;
    }

    public function isRoot(): bool
    {
        return $this->parent_id === null;
    }

    public function isLeaf(): bool
    {
        return !$this->children()->exists();
    }

    // ─── Path management ─────────────────────────────────────────────────────

    public function rebuildPath(): void
    {
        $parentPath  = $this->parent?->path ?? '';
        $this->path  = $parentPath ? "{$parentPath}/{$this->id}" : (string) $this->id;
        $this->depth = $this->parent ? $this->parent->depth + 1 : 0;
        $this->saveQuietly();
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
    public function category()
{
    // هذه سترجع أول قسم مرتبطة به المنتج (كحل سريع للخطأ)
    return $this->belongsToMany(Category::class, 'category_product')
                ->wherePivot('is_primary', true); // القسم الأساسي فقط
}
public function categories(): BelongsToMany
{
    return $this->belongsToMany(Category::class, 'category_product')
                ->withPivot('is_primary')
                ->withTimestamps();
}
}
