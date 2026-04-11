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
use Spatie\MediaLibrary\HasMedia; // 1. استيراد الـ Interface
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'short_description',
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
        'base_price'     => 'decimal:2',
        'discount_price' => 'decimal:2',
        'is_featured'    => 'boolean',
        'sort_order'     => 'integer',
        'images'         => 'array',
        'meta'           => 'array',
    ];

    // ─── Boot ─────────────────────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(fn(self $p): mixed => $p->slug ??= Str::slug($p->name));
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    /** All categories this product belongs to */
 
    /** The primary/canonical category */
    public function primaryCategory(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_product')
                    ->withPivot('is_primary')
                    ->wherePivot('is_primary', true);
    }

    /** All variants */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    /** Active, in-stock variants */
    public function activeVariants(): HasMany
    {
        return $this->variants()
                    ->where('is_active', true)
                    ->where('stock_quantity', '>', 0);
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    /**
     * Effective selling price.
     * If the product has active variants, returns the lowest variant price.
     * Otherwise returns discount_price if set, or base_price.
     */
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

    /**
     * Total stock across all active variants.
     * Falls back to checking if any variant has stock, or 1 if no variants.
     */
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

    /**
     * Full image URL — Railway storage:link compatible.
     * Falls back to first variant image if product has no direct image.
     */
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

    protected function imageUrls(): Attribute
    {
        return Attribute::make(
            get: fn(): array => collect($this->images ?? [])
                ->map(fn(string $path) => Storage::url($path))
                ->toArray()
        );
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive($query): mixed
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured($query): mixed
    {
        return $query->where('is_featured', true);
    }

    public function scopeSearch($query, string $term): mixed
    {
        return $query->where(
            fn($q) => $q->where('name', 'like', "%{$term}%")
                        ->orWhere('description', 'like', "%{$term}%")
        );
    }

    public function scopeInCategory($query, int $categoryId): mixed
    {
        return $query->whereHas(
            'categories',
            fn($q) => $q->where('categories.id', $categoryId)
        );
    }
    public function categories(): BelongsToMany
{
    return $this->belongsToMany(Category::class, 'category_product')
                ->withPivot('is_primary')
                ->withTimestamps();
}

/** إضافة علاقة "مساعدة" بالاسم الذي يبحث عنه الكود (بالمفرد) **/
public function category()
{
    // هذه سترجع أول قسم مرتبطة به المنتج (كحل سريع للخطأ)
    return $this->belongsToMany(Category::class, 'category_product')
                ->wherePivot('is_primary', true); // القسم الأساسي فقط
}
public function getCategoryAttribute()
{
    // يبحث عن القسم المسمّى "أساسي" أولاً، وإذا لم يوجد يأخذ أول قسم مرتبط بالمنتج
    return $this->categories->where('pivot.is_primary', true)->first() 
           ?? $this->categories->first();
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
 
}