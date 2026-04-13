<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Zone extends Model
{
    protected $fillable = [
        'country_id', 'name', 'shipping_price', 'delivery_days', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'shipping_price' => 'decimal:2',
        'delivery_days'  => 'integer',
        'is_active'      => 'boolean',
        'sort_order'     => 'integer',
    ];

    // ── Relationships ─────────────────────────────────────────────────────

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────

    public function scopeActive($query): mixed
    {
        return $query->where('is_active', true);
    }
}