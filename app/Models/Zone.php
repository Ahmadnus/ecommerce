<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Zone extends Model
{
    protected $fillable = [
        'country_id',
        'name',
        'name_en',
        'calling_code',
        'is_active',
        'sort_order',
        'shipping_price',
        'delivery_days',
    ];

    protected $casts = [
        'is_active'      => 'boolean',
        'sort_order'     => 'integer',
        'shipping_price' => 'decimal:2',
        'delivery_days'  => 'integer',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /** All monthly delivery schedules for this zone, newest first */
    public function deliverySchedules(): HasMany
    {
        return $this->hasMany(ZoneDeliverySchedule::class)->orderByDesc('month');
    }

    /**
     * The schedule for the current calendar month, if any.
     *
     * NOTE: Plain ->where() constraints on HasOne are IGNORED by Laravel's
     * eager loader (with()). We use latestOfMany() with a scoped subquery
     * so the month + is_active constraints are respected even in with().
     */
    public function currentSchedule(): HasOne
    {
        return $this->hasOne(ZoneDeliverySchedule::class)
                    ->latestOfMany('id')
                    ->where('month', ZoneDeliverySchedule::currentMonthKey())
                    ->where('is_active', true);
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
     * Returns the schedule for a given month key, loaded from already-fetched
     * deliverySchedules collection (no extra query needed if relation is loaded).
     */
    public function scheduleForMonth(string $month): ?ZoneDeliverySchedule
    {
        if ($this->relationLoaded('deliverySchedules')) {
            return $this->deliverySchedules
                ->where('month', $month)
                ->where('is_active', true)
                ->first();
        }

        return $this->deliverySchedules()
            ->where('month', $month)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Effective lead time for the current month.
     * Uses monthly schedule if present, falls back to zone's legacy delivery_days.
     */
    public function effectiveDeliveryDays(): ?int
    {
        $schedule = $this->scheduleForMonth(ZoneDeliverySchedule::currentMonthKey());
        return $schedule?->delivery_days ?? $this->delivery_days;
    }

    /**
     * Arabic lead-time label for the current month.
     */
    public function deliveryEstimate(): ?string
    {
        $days = $this->effectiveDeliveryDays();
        if (! $days) return null;

        return match ($days) {
            1       => 'يوم عمل واحد',
            2       => 'يومَا عمل',
            default => $days . ' أيام عمل',
        };
    }

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

    public function effectiveCallingCodeFormatted(): ?string
    {
        $code = $this->effectiveCallingCode();
        return $code ? "+{$code}" : null;
    }
}