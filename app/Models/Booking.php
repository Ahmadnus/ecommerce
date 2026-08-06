<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * A rental reservation.
 */
class Booking extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_PENDING   = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_ACTIVE    = 'active';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'booking_reference', 'user_id', 'vehicle_id',
        'pickup_location_id', 'return_location_id',
        'pickup_date_time', 'return_date_time', 'total_days', 'rate_plan',
        'driver_name', 'driver_email', 'driver_phone', 'driver_date_of_birth',
        'driver_license_number', 'driver_license_country', 'driver_license_expiry',
        'driver_national_id',
        'daily_rate', 'subtotal', 'location_fee', 'extras_total', 'discount_amount',
        'tax_amount', 'security_deposit', 'total_amount', 'currency', 'extras',
        'booking_status', 'payment_method', 'payment_status',
        'notes', 'admin_notes',
        'confirmed_at', 'picked_up_at', 'returned_at', 'cancelled_at',
    ];

    protected $casts = [
        'pickup_date_time'     => 'datetime',
        'return_date_time'     => 'datetime',
        'driver_date_of_birth' => 'date',
        'driver_license_expiry' => 'date',
        'total_days'           => 'integer',
        'daily_rate'           => 'decimal:2',
        'subtotal'             => 'decimal:2',
        'location_fee'         => 'decimal:2',
        'extras_total'         => 'decimal:2',
        'discount_amount'      => 'decimal:2',
        'tax_amount'           => 'decimal:2',
        'security_deposit'     => 'decimal:2',
        'total_amount'         => 'decimal:2',
        'extras'               => 'array',
        'confirmed_at'         => 'datetime',
        'picked_up_at'         => 'datetime',
        'returned_at'          => 'datetime',
        'cancelled_at'         => 'datetime',
    ];

    // ── Reference data ───────────────────────────────────────────────────────

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING   => ['label' => 'بانتظار التأكيد', 'label_en' => 'Pending',   'color' => 'yellow'],
            self::STATUS_CONFIRMED => ['label' => 'مؤكد',            'label_en' => 'Confirmed', 'color' => 'blue'],
            self::STATUS_ACTIVE    => ['label' => 'جارٍ الإيجار',     'label_en' => 'Active',    'color' => 'indigo'],
            self::STATUS_COMPLETED => ['label' => 'مكتمل',           'label_en' => 'Completed', 'color' => 'green'],
            self::STATUS_CANCELLED => ['label' => 'ملغي',            'label_en' => 'Cancelled', 'color' => 'red'],
        ];
    }

    /** Statuses that hold a vehicle unit and therefore block availability. */
    public static function blockingStatuses(): array
    {
        return [self::STATUS_PENDING, self::STATUS_CONFIRMED, self::STATUS_ACTIVE];
    }

    protected static function booted(): void
    {
        static::creating(function (self $booking): void {
            $booking->booking_reference ??= self::generateReference();
        });
    }

    public static function generateReference(): string
    {
        do {
            $reference = 'KEY-' . now()->format('ymd') . '-' . strtoupper(Str::random(4));
        } while (self::withTrashed()->where('booking_reference', $reference)->exists());

        return $reference;
    }

    // ── Relationships ────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class)->withTrashed();
    }

    public function pickupLocation(): BelongsTo
    {
        return $this->belongsTo(RentalLocation::class, 'pickup_location_id')->withTrashed();
    }

    public function returnLocation(): BelongsTo
    {
        return $this->belongsTo(RentalLocation::class, 'return_location_id')->withTrashed();
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where('booking_status', $status);
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->whereIn('booking_status', [self::STATUS_PENDING, self::STATUS_CONFIRMED])
                     ->where('pickup_date_time', '>=', now());
    }

    public function scopeOnRent(Builder $query): Builder
    {
        return $query->where('booking_status', self::STATUS_ACTIVE);
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function (Builder $q) use ($term) {
            $q->where('booking_reference', 'like', "%{$term}%")
              ->orWhere('driver_name', 'like', "%{$term}%")
              ->orWhere('driver_phone', 'like', "%{$term}%")
              ->orWhere('driver_email', 'like', "%{$term}%");
        });
    }

    // ── Display helpers ──────────────────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        $status = self::statuses()[$this->booking_status] ?? null;

        if (! $status) {
            return $this->booking_status;
        }

        return app()->getLocale() === 'ar' ? $status['label'] : $status['label_en'];
    }

    public function getStatusColorAttribute(): string
    {
        return self::statuses()[$this->booking_status]['color'] ?? 'gray';
    }

    public function getIsOneWayAttribute(): bool
    {
        return $this->return_location_id !== null
            && $this->return_location_id !== $this->pickup_location_id;
    }

    public function isCancellable(): bool
    {
        return in_array($this->booking_status, [self::STATUS_PENDING, self::STATUS_CONFIRMED], true)
            && $this->pickup_date_time?->isFuture();
    }

    // ── Transitions ──────────────────────────────────────────────────────────

    public function markConfirmed(): void
    {
        $this->update([
            'booking_status' => self::STATUS_CONFIRMED,
            'confirmed_at'   => now(),
        ]);
    }

    public function markPickedUp(): void
    {
        $this->update([
            'booking_status' => self::STATUS_ACTIVE,
            'picked_up_at'   => now(),
        ]);
    }

    public function markCompleted(): void
    {
        $this->update([
            'booking_status' => self::STATUS_COMPLETED,
            'returned_at'    => now(),
        ]);
    }

    public function markCancelled(): void
    {
        $this->update([
            'booking_status' => self::STATUS_CANCELLED,
            'cancelled_at'   => now(),
        ]);
    }
}
