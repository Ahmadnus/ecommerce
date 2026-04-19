<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Currency
 * ─────────────────────────────────────────────────────────────────────────────
 * Stores currency definitions with live exchange rates.
 * Base currency is JOD (exchange_rate = 1.000000).
 *
 * Columns:
 *   id, name, code (ISO 4217), symbol, exchange_rate, is_base, is_active,
 *   sort_order, created_at, updated_at
 *
 * Usage:
 *   $jod     = Currency::where('code', 'JOD')->first();
 *   $usd     = Currency::where('code', 'USD')->first();
 *   $amount  = $usd->convert(10.00); // 10 JOD → USD
 */
class Currency extends Model
{
    protected $fillable = [
        'name',
        'code',
        'symbol',
        'exchange_rate',
        'is_base',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'exchange_rate' => 'decimal:6',
        'is_base'       => 'boolean',
        'is_active'     => 'boolean',
        'sort_order'    => 'integer',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function countries(): BelongsToMany
    {
        return $this->belongsToMany(Country::class, 'country_currency')
                    ->withPivot('is_default');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Convert a JOD amount into this currency.
     * Formula: result = round(amount × exchange_rate, 2)
     */
    public function convert(float $jod): float
    {
        return round($jod * (float) $this->exchange_rate, 2);
    }

    /**
     * Format a JOD amount as a display string with this currency's symbol.
     * Examples:
     *   "12.50 د.أ"   (suffix symbol)
     *   "$12.50"       (prefix symbol)
     */
    public function format(float $jod): string
    {
        $amount    = $this->convert($jod);
        $formatted = number_format($amount, 2);

        // Prefix detection: $, €, £, ¥, ₹, ₩, etc.
        $prefixSymbols = ['$', '€', '£', '¥', '¢', '₹', '₩', '₪', '₺', '₦', '฿', 'R$', 'kr'];
        foreach ($prefixSymbols as $prefix) {
            if (str_starts_with($this->symbol, $prefix)) {
                return $this->symbol . $formatted;
            }
        }

        return $formatted . ' ' . $this->symbol;
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    /** Only active currencies */
    public function scopeActive($query): mixed
    {
        return $query->where('is_active', true);
    }

    /** Active currencies ordered for display */
    public function scopeOrdered($query): mixed
    {
        return $query->where('is_active', true)
                     ->orderBy('sort_order')
                     ->orderBy('name');
    }
}