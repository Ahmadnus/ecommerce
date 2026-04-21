<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Zone extends Model
{
    protected $fillable = [
        'country_id',
        'name',
        'name_en',
        'calling_code',   // ← NEW: zone-level override; falls back to parent country code
        'is_active',
        'sort_order',
            'shipping_price',
    'delivery_days',

    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Cities / sub-zones if your schema has a third level.
     * Remove if you only have Country → Zone (two levels).
     */

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($query): mixed
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query): mixed
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Returns the effective calling code for this zone:
     * uses the zone's own code if set, otherwise falls back to the
     * parent country's code. Requires 'country' relation to be loaded.
     *
     * Example: $zone->effectiveCallingCode() → "963"
     */
    public function effectiveCallingCode(): ?string
    {
        if ($this->calling_code) {
            return ltrim($this->calling_code, '+');
        }

        if ($this->relationLoaded('country') && $this->country?->calling_code) {
            return ltrim($this->country->calling_code, '+');
        }

        return null;
    }

    /**
     * Same as effectiveCallingCode() but with a "+" prefix.
     * Example: "+963"
     */
    public function effectiveCallingCodeFormatted(): ?string
    {
        $code = $this->effectiveCallingCode();
        return $code ? "+{$code}" : null;
    }
}