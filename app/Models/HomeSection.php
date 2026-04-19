<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class HomeSection extends Model
{
    protected $fillable = [
        'title',
        'type',
        'category_id',
        'limit',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
        'limit'      => 'integer',
    ];

    // ── Type constants ─────────────────────────────────────────────────────────

    const TYPE_FEATURED   = 'featured';
    const TYPE_LATEST     = 'latest';
    const TYPE_PRICE_HIGH = 'price_high';
    const TYPE_PRICE_LOW  = 'price_low';
    const TYPE_CATEGORY   = 'category';

    /** Human-readable labels for the admin dropdown. */
    public static function typeLabels(): array
    {
        return [
            self::TYPE_FEATURED   => 'المنتجات المميزة',
            self::TYPE_LATEST     => 'أحدث المنتجات',
            self::TYPE_PRICE_HIGH => 'السعر: من الأعلى للأقل',
            self::TYPE_PRICE_LOW  => 'السعر: من الأقل للأعلى',
            self::TYPE_CATEGORY   => 'تصنيف محدد',
        ];
    }

    // ── Relationships ──────────────────────────────────────────────────────────

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // ── Scopes ─────────────────────────────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    // ── Core method: resolve products for this section ─────────────────────────

    /**
     * Returns the Product collection for this section.
     * All products are eager-loaded with categories + active variants
     * so Blade templates don't trigger extra queries.
     */
    public function resolveProducts(): EloquentCollection
    {
        $query = Product::active()
            ->with([
                'categories',
                'variants' => fn($q) => $q->where('is_active', true),
            ]);

        match ($this->type) {
            self::TYPE_FEATURED   => $query->where('is_featured', true)
                                           ->orderByDesc('sort_order'),
            self::TYPE_LATEST     => $query->latest(),
            self::TYPE_PRICE_HIGH => $query->orderByDesc('base_price'),
            self::TYPE_PRICE_LOW  => $query->orderBy('base_price'),
            self::TYPE_CATEGORY   => $query->whereHas(
                                        'categories',
                                        fn($q) => $q->where('categories.id', $this->category_id)
                                     )->orderByDesc('sort_order'),
            default               => $query->latest(),
        };

        return $query->limit($this->limit)->get();
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    public function typeLabel(): string
    {
        return self::typeLabels()[$this->type] ?? $this->type;
    }

    public function requiresCategory(): bool
    {
        return $this->type === self::TYPE_CATEGORY;
    }
}