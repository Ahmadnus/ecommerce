<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class ProductVariant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'sku',
        'price_override',
        'stock_quantity',
        'variant_image',
        'is_active',
    ];

    protected $casts = [
        'price_override' => 'decimal:2',
        'stock_quantity' => 'integer',
        'is_active'      => 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(
            AttributeValue::class,
            'product_variant_attribute_values',
            'product_variant_id',
            'attribute_value_id'
        )->with('attribute');
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    /**
     * Effective price: price_override if set, else product's discount_price,
     * else product's base_price.
     */
    protected function effectivePrice(): Attribute
    {
        return Attribute::make(
            get: function (): float {
                if ($this->price_override !== null) {
                    return (float) $this->price_override;
                }

                $product = $this->product;
                return (float) ($product?->discount_price ?? $product?->base_price ?? 0);
            }
        );
    }

    protected function inStock(): Attribute
    {
        return Attribute::make(
            get: fn(): bool => $this->stock_quantity > 0
        );
    }

    /**
     * Human-readable label from attribute values.
     * e.g. "أزرق / 42"  — requires attributeValues to be eager-loaded.
     */
    protected function label(): Attribute
    {
        return Attribute::make(
            get: fn(): string => $this->relationLoaded('attributeValues')
                ? $this->attributeValues->pluck('value')->implode(' / ')
                : ''
        );
    }

    /**
     * Full image URL — Railway storage:link compatible.
     * Falls back to product image.
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: function (): ?string {
                if ($this->variant_image) {
                    return Storage::url($this->variant_image);
                }

                return $this->product?->image_url;
            }
        );
    }
    
}