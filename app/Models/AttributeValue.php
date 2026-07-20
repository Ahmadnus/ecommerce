<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\HasTranslations;

class AttributeValue extends Model
{
    use HasTranslations;

    // Spatie: which fields are translatable
    public array $translatable = ['value', 'label'];

    protected $fillable = [
        'attribute_id',
        'value',
        'label',
        'color_hex',
        'sort_order',
    ];

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }

    public function variants(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductVariant::class,
            'product_variant_attribute_values',
            'attribute_value_id',
            'product_variant_id'
        );
    }

    /**
     * Returns the translated label if set, otherwise falls back to translated value.
     * Always resolves against the current app locale automatically.
     */
    public function getDisplayLabelAttribute(): string
    {
        $label = $this->label; // Spatie returns current-locale string or ''
        return (is_string($label) && $label !== '')
            ? $label
            : (string) $this->value;
    }
}