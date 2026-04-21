<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    protected $fillable = [
        'name',
        'name_en',
        'code',
        'calling_code',   // ← NEW: international dialling prefix, digits only (e.g. "963")
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function zones(): HasMany
    {
        return $this->hasMany(Zone::class)->orderBy('sort_order')->orderBy('name');
    }

    public function activeZones(): HasMany
    {
        return $this->zones()->where('is_active', true);
    }

    public function currencies(): BelongsToMany
    {
        return $this->belongsToMany(Currency::class, 'country_currency')
                    ->withPivot('is_default');
    }

    public function defaultCurrency(): BelongsToMany
    {
        return $this->currencies()->wherePivot('is_default', true);
    }

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
     * Returns the formatted calling code with a leading "+" sign.
     * Returns null if calling_code is not set.
     *
     * Example: country->callingCodeFormatted() → "+963"
     */
    public function callingCodeFormatted(): ?string
    {
        return $this->calling_code ? '+' . ltrim($this->calling_code, '+') : null;
    }

    /**
     * Label used in dropdowns throughout the UI.
     * Example: "سوريا +963"
     */
    public function dropdownLabel(): string
    {
        $code = $this->callingCodeFormatted();
        return $code ? "{$this->name} {$code}" : $this->name;
    }
}