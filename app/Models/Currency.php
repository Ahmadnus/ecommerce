<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Currency extends Model
{
    protected $fillable = [
        'name', 'code', 'symbol', 'exchange_rate', 'is_base', 'is_active',
    ];

    protected $casts = [
        'exchange_rate' => 'decimal:6',
        'is_base'       => 'boolean',
        'is_active'     => 'boolean',
    ];

    // ── Relationships ─────────────────────────────────────────────────────

    public function countries(): BelongsToMany
    {
        return $this->belongsToMany(Country::class, 'country_currency')
                    ->withPivot('is_default');
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    /**
     * Convert an amount from base currency to this currency.
     */
    public function convert(float $amount): float
    {
        return round($amount * (float) $this->exchange_rate, 2);
    }

    // ── Scopes ────────────────────────────────────────────────────────────

    public function scopeActive($query): mixed
    {
        return $query->where('is_active', true);
    }
}