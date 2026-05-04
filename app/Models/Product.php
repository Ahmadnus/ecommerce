<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations; // ← ADD

class Product extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;
    use HasTranslations; // ← ADD

    // ── Declare which fields are translatable ──────────────────────────────
    public array $translatable = ['name', 'description', 'short_description'];

    protected $fillable = [
        'name',             // json
        'slug',
        'description',      // json
        'short_description',// json
        'base_price',
        'discount_price',
        'sku',
        'image',
        'images',
        'status',
        'is_featured',
        'sort_order',
        'meta',
    ];

    protected $casts = [
        'base_price'      => 'decimal:2',
        'discount_price'  => 'decimal:2',
        'is_featured'     => 'boolean',
        'sort_order'      => 'integer',
        'images'          => 'array',
        'meta'            => 'array',
        // Note: DO NOT cast translatable fields here —
        // Spatie handles their JSON encoding/decoding internally.
    ];

    // ─── Boot ─────────────────────────────────────────────────────────────
    protected static function booted(): void
    {
        static::creating(function (self $p): void {
            // Slug from the Arabic name, fallback to English
            $p->slug ??= Str::slug($p->getTranslation('name', 'ar')
                ?: $p->getTranslation('name', 'en'));
        });
    }

    // ─── Relationships ────────────────────────────────────────────────────

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_product')
                    ->withPivot('is_primary')
                    ->withTimestamps();
    }

    public function primaryCategory(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_product')
                    ->withPivot('is_primary')
                    ->wherePivot('is_primary', true);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function activeVariants(): HasMany
    {
        return $this->variants()
                    ->where('is_active', true)
                    ->where('stock_quantity', '>', 0);
    }

    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(AttributeValue::class, 'product_attribute_value');
    }

    public function wishlistedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Models\User::class,
            'wishlists',
            'product_id',
            'user_id'
        )->withTimestamps();
    }

    // ─── Accessors ────────────────────────────────────────────────────────

    protected function effectivePrice(): Attribute
    {
        return Attribute::make(
            get: function (): float {
                if ($this->relationLoaded('variants') && $this->variants->isNotEmpty()) {
                    $lowest = $this->variants
                        ->where('is_active', true)
                        ->where('stock_quantity', '>', 0)
                        ->min(fn(ProductVariant $v) => $v->effective_price);

                    if ($lowest !== null) {
                        return (float) $lowest;
                    }
                }

                return (float) ($this->discount_price ?? $this->base_price);
            }
        );
    }

    protected function isOnSale(): Attribute
    {
        return Attribute::make(
            get: fn(): bool => $this->discount_price !== null
                               && $this->discount_price < $this->base_price
        );
    }

    protected function discountPercentage(): Attribute
    {
        return Attribute::make(
            get: fn(): int => $this->is_on_sale
                ? (int) round(
                    (($this->base_price - $this->discount_price) / $this->base_price) * 100
                  )
                : 0
        );
    }

    protected function totalStock(): Attribute
    {
        return Attribute::make(
            get: fn(): int => $this->relationLoaded('variants')
                ? (int) $this->variants->sum('stock_quantity')
                : (int) $this->variants()->sum('stock_quantity')
        );
    }

    protected function inStock(): Attribute
    {
        return Attribute::make(
            get: fn(): bool => $this->total_stock > 0
        );
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: function (): ?string {
                if ($this->image) {
                    return Storage::url($this->image);
                }
                if ($this->relationLoaded('variants')) {
                    $variantImage = $this->variants->first()?->variant_image;
                    if ($variantImage) {
                        return Storage::url($variantImage);
                    }
                }
                return null;
            }
        );
    }
public function getCategoryAttribute()
{
    // يبحث عن القسم المسمّى "أساسي" أولاً، وإذا لم يوجد يأخذ أول قسم مرتبط بالمنتج
    return $this->categories->where('pivot.is_primary', true)->first() 
           ?? $this->categories->first();
}
    protected function imageUrls(): Attribute
    {
        return Attribute::make(
            get: fn(): array => collect($this->images ?? [])
                ->map(fn(string $path) => Storage::url($path))
                ->toArray()
        );
    }

    // ─── Scopes ───────────────────────────────────────────────────────────

    public function scopeActive($query): mixed
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured($query): mixed
    {
        return $query->where('is_featured', true);
    }

    /**
     * Search across both locale columns stored in JSON.
     * Works on MySQL 5.7+ / MariaDB 10.2+.
     */
    public function scopeSearch($query, string $term): mixed
    {
        return $query->where(function ($q) use ($term) {
            $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.ar')) LIKE ?", ["%{$term}%"])
              ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.en')) LIKE ?", ["%{$term}%"])
              ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(description, '$.ar')) LIKE ?", ["%{$term}%"])
              ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(description, '$.en')) LIKE ?", ["%{$term}%"]);
        });
    }

    public function scopeInCategory($query, int $categoryId): mixed
    {
        return $query->whereHas(
            'categories',
            fn($q) => $q->where('categories.id', $categoryId)
        );
    }

    // ─── Helpers ──────────────────────────────────────────────────────────

    /** Convenience: get name in a specific locale without changing app locale */
    public function nameIn(string $locale): string
    {
        return $this->getTranslation('name', $locale) ?: $this->name;
    }

    // ─── Currency helpers (unchanged) ────────────────────────────────────

    public function getPriceInCurrency(string $field = 'base_price'): float
    {
        $svc  = app(\App\Services\CurrencyService::class);
        $base = (float) ($this->$field ?? 0);
        return $svc->convert($base);
    }

    public function getEffectivePriceConvertedAttribute(): float
    {
        $field = $this->is_on_sale ? 'discount_price' : 'base_price';
        return $this->getPriceInCurrency($field);
    }

    public function getFormattedPriceAttribute(): string
    {
        $svc = app(\App\Services\CurrencyService::class);
        return $svc->format($this->is_on_sale ? $this->discount_price : $this->base_price);
    }
}