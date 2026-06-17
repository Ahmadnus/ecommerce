<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Image\Enums\Fit;
use Spatie\Image\Image;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;
// الإضافات الجديدة الخاصة بالتخصيص
use App\Models\OrderCustomization;
use App\Models\ProductCustomization; 

class Product extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;
    use HasTranslations;

    public array $translatable = ['name', 'description', 'short_description'];

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
        'is_customizable',      // تمت إضافته
        'customization_config', // تمت إضافته
    ];

    protected $casts = [
        'base_price'           => 'decimal:2',
        'discount_price'       => 'decimal:2',
        'is_featured'          => 'boolean',
        'sort_order'           => 'integer',
        'images'               => 'array',
        'meta'                 => 'array',
        'is_customizable'      => 'boolean', // تمت إضافته
        'customization_config' => 'array',   // تمت إضافته
    ];

    protected static function booted(): void
    {
        static::creating(function (self $product): void {
            $product->slug ??= Str::slug(
                $product->getTranslation('name', 'ar') ?: $product->getTranslation('name', 'en')
            );
        });
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('main')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/avif']);

        $this->addMediaCollection('products')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/avif']);
    }

    public static function optimizeUploadedImageToTempWebp(
        UploadedFile $file,
        int $maxWidth = 1600,
        int $maxHeight = 1600
    ): string {
        $tempBase = tempnam(sys_get_temp_dir(), 'product_media_');
        $tempPath = $tempBase . '.webp';

        if ($tempBase && is_file($tempBase)) {
            @unlink($tempBase);
        }

        Image::load($file->getRealPath())
            ->fit(Fit::Contain, $maxWidth, $maxHeight)
            ->optimize()
            ->format('webp')
            ->save($tempPath);

        return $tempPath;
    }

    public function addCompressedMedia(
        UploadedFile $file,
        string $collection = 'main',
        int $maxWidth = 1600,
        int $maxHeight = 1600
    ): Media {
        $tempPath = self::optimizeUploadedImageToTempWebp($file, $maxWidth, $maxHeight);

        try {
            return $this->addMedia($tempPath)
                ->usingFileName(Str::uuid() . '.webp')
                ->toMediaCollection($collection);
        } finally {
            if (is_file($tempPath)) {
                @unlink($tempPath);
            }
        }
    }

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

    protected function effectivePrice(): Attribute
    {
        return Attribute::make(
            get: function (): float {
                if ($this->relationLoaded('variants') && $this->variants->isNotEmpty()) {
                    $lowest = $this->variants
                        ->where('is_active', true)
                        ->where('stock_quantity', '>', 0)
                        ->min(fn(ProductVariant $variant) => $variant->effective_price);

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

    public function nameIn(string $locale): string
    {
        return $this->getTranslation('name', $locale) ?: $this->name;
    }

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

    // ─────────────────────────────────────────────────────────────────────────────
    // قسم التقييمات والمراجعات (تم تصحيح الأقواس لتصبح داخل الكلاس)
    // ─────────────────────────────────────────────────────────────────────────────
    
    public function reviews(): HasMany
    {
        return $this->hasMany(\App\Models\ProductReview::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->hasMany(\App\Models\ProductReview::class)
                    ->where('status', 'approved')
                    ->orderByDesc('is_pinned')
                    ->orderByDesc('created_at');
    }

    public function averageRating(): float
    {
        return (float) $this->reviews()
            ->where('status', 'approved')
            ->avg('rating') ?? 0.0;
    }

    public function reviewCount(): int
    {
        return $this->reviews()
            ->where('status', 'approved')
            ->count();
    }

    public function ratingStars(): array
    {
        $avg = $this->averageRating();
        return array_map(fn(int $i) => $i <= round($avg), range(1, 5));
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // قسم التخصيصات (Customizations) - الإضافات المطلوبة
    // ─────────────────────────────────────────────────────────────────────────────

    /**
     * All order-level customizations placed for this product.
     */
    public function orderCustomizations(): HasMany
    {
        return $this->hasMany(OrderCustomization::class);
    }

    /**
     * Returns a typed wrapper around the raw customization_config JSON.
     * Returns an empty (but usable) config object if not set.
     */
    public function customizationConfig(): ProductCustomization
    {
        return new ProductCustomization($this->customization_config ?? []);
    }

    /**
     * Scope: only products that are marked customizable.
     */
    public function scopeCustomizable(\Illuminate\Database\Eloquent\Builder $query): void
    {
        $query->where('is_customizable', true);
    }
}