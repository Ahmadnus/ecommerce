<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

/**
 * An optional booking add-on — collision waiver, extra driver, GPS, child seat.
 *
 * `code` is the stable identifier. Bookings store it inside their `extras` JSON
 * snapshot, so it must not change once a booking references it; the display
 * name is free to be renamed at any time.
 */
class RentalExtra extends Model
{
    use HasTranslations;

    public array $translatable = ['name'];

    protected $fillable = [
        'code', 'name', 'icon', 'price', 'per_day', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'price'      => 'decimal:2',
        'per_day'    => 'boolean',
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    /**
     * The shape RentalPricingService::availableExtras() hands to the booking
     * form and the quote builder. Kept as an explicit method so the array keys
     * live next to the model rather than being assembled ad hoc at each call.
     *
     * @return array{label: string, label_ar: string, price: float, per_day: bool, icon: string}
     */
    public function toCatalogueEntry(): array
    {
        return [
            'label'    => $this->getTranslation('name', 'en', false) ?: $this->code,
            'label_ar' => $this->getTranslation('name', 'ar', false) ?: $this->code,
            'price'    => (float) $this->price,
            'per_day'  => (bool) $this->per_day,
            'icon'     => $this->icon ?: 'fa-solid fa-plus',
        ];
    }
}
