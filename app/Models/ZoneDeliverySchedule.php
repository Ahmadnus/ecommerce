<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ZoneDeliverySchedule
 *
 * Represents the delivery configuration for one Zone in one calendar month.
 *
 * @property int         $id
 * @property int         $zone_id
 * @property string      $month           — "YYYY-MM"
 * @property int|null    $delivery_days   — lead-time in business days
 * @property array|null  $available_days  — day-of-month numbers, or null = all days
 * @property string|null $notes
 * @property bool        $is_active
 */
class ZoneDeliverySchedule extends Model
{
    protected $fillable = [
        'zone_id',
        'month',
        'delivery_days',
        'available_days',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'delivery_days'  => 'integer',
        'available_days' => 'array',
        'is_active'      => 'boolean',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    /** Only active schedules */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /** Schedules for a specific month string ("YYYY-MM") */
    public function scopeForMonth(Builder $query, string $month): Builder
    {
        return $query->where('month', $month);
    }

    /** Schedules for the current calendar month */
    public function scopeCurrentMonth(Builder $query): Builder
    {
        return $query->where('month', now()->format('Y-m'));
    }

    /** Schedules ordered newest first */
    public function scopeLatestFirst(Builder $query): Builder
    {
        return $query->orderByDesc('month');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Returns today's month key "YYYY-MM".
     */
    public static function currentMonthKey(): string
    {
        return now()->format('Y-m');
    }

    /**
     * Returns "YYYY-MM" for N months from now (default: next month).
     */
    public static function nextMonthKey(int $offset = 1): string
    {
        return now()->addMonths($offset)->format('Y-m');
    }

    /**
     * Human-readable month label, e.g. "يوليو 2025".
     */
    public function monthLabel(): string
    {
        return Carbon::createFromFormat('Y-m', $this->month)->translatedFormat('F Y');
    }

    /**
     * Whether this schedule is for the current calendar month.
     */
    public function isCurrentMonth(): bool
    {
        return $this->month === self::currentMonthKey();
    }

    /**
     * Whether this schedule is for a past month.
     */
    public function isPast(): bool
    {
        return $this->month < self::currentMonthKey();
    }

    /**
     * Whether this schedule is for a future month.
     */
    public function isFuture(): bool
    {
        return $this->month > self::currentMonthKey();
    }

    /**
     * Returns the status badge label + color for admin UI.
     * Returns an array: ['label' => '...', 'color' => 'green|yellow|gray']
     */
    public function statusBadge(): array
    {
        if (! $this->is_active) {
            return ['label' => 'معطّل', 'color' => 'gray'];
        }
        if ($this->isCurrentMonth()) {
            return ['label' => 'الشهر الحالي', 'color' => 'green'];
        }
        if ($this->isFuture()) {
            return ['label' => 'قادم', 'color' => 'blue'];
        }
        return ['label' => 'منتهي', 'color' => 'gray'];
    }

    /**
     * Arabic delivery lead-time label.
     * e.g. 1 → "يوم عمل واحد", 3 → "3 أيام عمل", null → null
     */
    public function deliveryLabel(): ?string
    {
        if (! $this->delivery_days) {
            return null;
        }
        return match ($this->delivery_days) {
            1       => 'يوم عمل واحد',
            2       => 'يومَا عمل',
            default => $this->delivery_days . ' أيام عمل',
        };
    }

    /**
     * Sorted unique array of available day-of-month numbers.
     * Returns null when all days are available.
     */
    public function sortedDays(): ?array
    {
        if (empty($this->available_days)) {
            return null;
        }
        $days = array_unique(array_map('intval', $this->available_days));
        sort($days);
        return $days;
    }

    /**
     * Comma-separated day list for display, e.g. "1، 5، 10، 15".
     */
    public function daysDisplay(): string
    {
        $days = $this->sortedDays();
        return $days ? implode('، ', $days) : 'جميع أيام الشهر';
    }

    /**
     * Count of available days (null = count of days in that month).
     */
    public function availableDayCount(): int
    {
        if ($this->available_days) {
            return count($this->available_days);
        }
        return (int) Carbon::createFromFormat('Y-m', $this->month)->daysInMonth;
    }
}