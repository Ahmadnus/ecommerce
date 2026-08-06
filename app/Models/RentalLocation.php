<?php

namespace App\Models;

use App\Models\Concerns\GeneratesUniqueSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

/**
 * A pickup / return branch (city office, airport counter, delivery point).
 */
class RentalLocation extends Model
{
    use HasFactory, SoftDeletes, HasTranslations, GeneratesUniqueSlug;

    public array $translatable = ['name', 'city', 'address'];

    public const TYPE_BRANCH   = 'branch';
    public const TYPE_AIRPORT  = 'airport';
    public const TYPE_DELIVERY = 'delivery';

    protected $fillable = [
        'name', 'city', 'address', 'slug', 'code', 'type', 'phone',
        'latitude', 'longitude', 'pickup_fee', 'one_way_fee',
        'opens_at', 'closes_at', 'is_24h', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'pickup_fee'  => 'decimal:2',
        'one_way_fee' => 'decimal:2',
        'latitude'    => 'decimal:7',
        'longitude'   => 'decimal:7',
        'is_24h'      => 'boolean',
        'is_active'   => 'boolean',
        'sort_order'  => 'integer',
    ];

    public static function types(): array
    {
        return [
            self::TYPE_BRANCH   => ['label' => 'فرع', 'label_en' => 'Branch',   'icon' => 'fa-solid fa-building'],
            self::TYPE_AIRPORT  => ['label' => 'مطار', 'label_en' => 'Airport', 'icon' => 'fa-solid fa-plane'],
            self::TYPE_DELIVERY => ['label' => 'توصيل', 'label_en' => 'Delivery', 'icon' => 'fa-solid fa-truck'],
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $location): void {
            $location->slug ??= static::uniqueSlug(
                $location->getTranslation('name', 'en', false)
                    ?: $location->getTranslation('name', 'ar', false),
                'location'
            );
        });
    }

    // ── Relationships ────────────────────────────────────────────────────────

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    public function pickupBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'pickup_location_id');
    }

    public function returnBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'return_location_id');
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive($query): mixed
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query): mixed
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    public function getTypeIconAttribute(): string
    {
        return self::types()[$this->type]['icon'] ?? 'fa-solid fa-location-dot';
    }

    public function getDisplayLabelAttribute(): string
    {
        $city = $this->city;

        return $city && $city !== $this->name
            ? "{$this->name} — {$city}"
            : (string) $this->name;
    }

    public function getHoursLabelAttribute(): string
    {
        if ($this->is_24h) {
            return app()->getLocale() === 'ar' ? 'مفتوح 24 ساعة' : 'Open 24 hours';
        }

        if ($this->opens_at && $this->closes_at) {
            return substr((string) $this->opens_at, 0, 5) . ' – ' . substr((string) $this->closes_at, 0, 5);
        }

        return '—';
    }
}
