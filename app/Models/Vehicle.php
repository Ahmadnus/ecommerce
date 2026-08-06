<?php

namespace App\Models;

use App\Models\Concerns\GeneratesUniqueSlug;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Spatie\Image\Enums\Fit;
use Spatie\Image\Image;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

/**
 * A rentable car in the fleet.
 */
class Vehicle extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia, HasTranslations, GeneratesUniqueSlug;

    public array $translatable = ['description'];

    public const STATUS_AVAILABLE   = 'available';
    public const STATUS_RENTED      = 'rented';
    public const STATUS_MAINTENANCE = 'maintenance';
    public const STATUS_UNAVAILABLE = 'unavailable';

    protected $fillable = [
        'vehicle_category_id', 'rental_location_id',
        'brand', 'model', 'year', 'slug', 'registration_number', 'color', 'description',
        'transmission', 'fuel_type', 'seats', 'doors', 'bags', 'engine_cc',
        'mileage_limit_per_day', 'features',
        'daily_rate', 'weekly_rate', 'monthly_rate', 'discount_daily_rate', 'security_deposit',
        'min_driver_age', 'min_rental_days',
        'main_image', 'gallery_images',
        'availability_status', 'units_available',
        'is_active', 'is_featured', 'sort_order',
    ];

    protected $casts = [
        'year'                  => 'integer',
        'seats'                 => 'integer',
        'doors'                 => 'integer',
        'bags'                  => 'integer',
        'engine_cc'             => 'integer',
        'mileage_limit_per_day' => 'integer',
        'features'              => 'array',
        'gallery_images'        => 'array',
        'daily_rate'            => 'decimal:2',
        'weekly_rate'           => 'decimal:2',
        'monthly_rate'          => 'decimal:2',
        'discount_daily_rate'   => 'decimal:2',
        'security_deposit'      => 'decimal:2',
        'min_driver_age'        => 'integer',
        'min_rental_days'       => 'integer',
        'units_available'       => 'integer',
        'is_active'             => 'boolean',
        'is_featured'           => 'boolean',
        'sort_order'            => 'integer',
    ];

    // ── Reference data ───────────────────────────────────────────────────────

    public static function statuses(): array
    {
        return [
            self::STATUS_AVAILABLE   => ['label' => 'متاحة',        'label_en' => 'Available',   'color' => 'green'],
            self::STATUS_RENTED      => ['label' => 'مؤجرة حالياً',  'label_en' => 'Rented',      'color' => 'blue'],
            self::STATUS_MAINTENANCE => ['label' => 'في الصيانة',    'label_en' => 'Maintenance', 'color' => 'yellow'],
            self::STATUS_UNAVAILABLE => ['label' => 'غير متاحة',     'label_en' => 'Unavailable', 'color' => 'red'],
        ];
    }

    public static function transmissions(): array
    {
        return [
            'automatic' => ['label' => 'أوتوماتيك', 'label_en' => 'Automatic'],
            'manual'    => ['label' => 'عادي',      'label_en' => 'Manual'],
        ];
    }

    public static function fuelTypes(): array
    {
        return [
            'petrol'   => ['label' => 'بنزين',  'label_en' => 'Petrol'],
            'diesel'   => ['label' => 'ديزل',   'label_en' => 'Diesel'],
            'hybrid'   => ['label' => 'هجين',   'label_en' => 'Hybrid'],
            'electric' => ['label' => 'كهربائي', 'label_en' => 'Electric'],
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $vehicle): void {
            $vehicle->slug ??= static::uniqueSlug(
                trim("{$vehicle->brand} {$vehicle->model} {$vehicle->year}"),
                'vehicle'
            );
        });
    }

    // ── Media ────────────────────────────────────────────────────────────────

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('vehicle_main')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/avif']);

        $this->addMediaCollection('vehicle_gallery')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/avif']);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('card')
            ->fit(Fit::Contain, 640, 420)
            ->nonQueued()
            ->performOnCollections('vehicle_main', 'vehicle_gallery');
    }

    /**
     * Mirrors Product::addCompressedMedia — converts to webp before storing so
     * fleet photos don't balloon the media disk.
     */
    public function addCompressedMedia(
        UploadedFile $file,
        string $collection = 'vehicle_main',
        int $maxWidth = 1600,
        int $maxHeight = 1600
    ): Media {
        $tempBase = tempnam(sys_get_temp_dir(), 'vehicle_media_');
        $tempPath = $tempBase . '.webp';

        if ($tempBase && is_file($tempBase)) {
            @unlink($tempBase);
        }

        Image::load($file->getRealPath())
            ->fit(Fit::Contain, $maxWidth, $maxHeight)
            ->optimize()
            ->format('webp')
            ->save($tempPath);

        try {
            return $this->addMedia($tempPath)
                ->usingFileName(Str::uuid() . '.webp')
                ->toMediaCollection($collection);
        } finally {
            if (is_file($tempPath)) {
                @unlink($tempPath);
            }
        }
    }

    // ── Relationships ────────────────────────────────────────────────────────

    public function category(): BelongsTo
    {
        return $this->belongsTo(VehicleCategory::class, 'vehicle_category_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(RentalLocation::class, 'rental_location_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeBookable(Builder $query): Builder
    {
        return $query->where('is_active', true)
                     ->where('availability_status', self::STATUS_AVAILABLE);
    }

    /**
     * Vehicles with at least one unit free across the requested window.
     *
     * A unit is taken when a live booking overlaps the window. Overlap is
     * strict: a booking that returns exactly when the next one picks up does
     * not conflict, which is why the comparisons are '<' / '>' and not '<=' / '>='.
     */
    public function scopeAvailableBetween(Builder $query, $pickup, $return): Builder
    {
        // Normalise first: a bare string like '2030-01-15 10:00' would be
        // compared lexicographically against the stored '2030-01-15 10:00:00'
        // and wrongly register as an overlap.
        $pickup = Carbon::parse($pickup);
        $return = Carbon::parse($return);

        return $query->bookable()->whereRaw(
            'units_available > (
                select count(*) from bookings
                where bookings.vehicle_id = vehicles.id
                  and bookings.deleted_at is null
                  and bookings.booking_status in (?, ?, ?)
                  and bookings.pickup_date_time < ?
                  and bookings.return_date_time > ?
            )',
            [
                Booking::STATUS_PENDING,
                Booking::STATUS_CONFIRMED,
                Booking::STATUS_ACTIVE,
                $return,
                $pickup,
            ]
        );
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function (Builder $q) use ($term) {
            $q->where('brand', 'like', "%{$term}%")
              ->orWhere('model', 'like', "%{$term}%")
              ->orWhere('registration_number', 'like', "%{$term}%");
        });
    }

    // ── Availability ─────────────────────────────────────────────────────────

    /** How many units of this vehicle are already committed in the window. */
    public function unitsBookedBetween($pickup, $return): int
    {
        $pickup = Carbon::parse($pickup);
        $return = Carbon::parse($return);

        return (int) $this->bookings()
            ->whereIn('booking_status', [
                Booking::STATUS_PENDING,
                Booking::STATUS_CONFIRMED,
                Booking::STATUS_ACTIVE,
            ])
            ->where('pickup_date_time', '<', $return)
            ->where('return_date_time', '>', $pickup)
            ->count();
    }

    public function isAvailableBetween($pickup, $return): bool
    {
        if (! $this->is_active || $this->availability_status !== self::STATUS_AVAILABLE) {
            return false;
        }

        return $this->unitsBookedBetween($pickup, $return) < $this->units_available;
    }

    // ── Pricing ──────────────────────────────────────────────────────────────

    /** The rate actually charged per day, honouring any promo rate. */
    public function getEffectiveDailyRateAttribute(): float
    {
        return (float) ($this->discount_daily_rate ?? $this->daily_rate);
    }

    public function getIsOnSaleAttribute(): bool
    {
        return $this->discount_daily_rate !== null
            && (float) $this->discount_daily_rate < (float) $this->daily_rate;
    }

    public function getDiscountPercentageAttribute(): int
    {
        if (! $this->is_on_sale || (float) $this->daily_rate <= 0) {
            return 0;
        }

        return (int) round(
            ((float) $this->daily_rate - (float) $this->discount_daily_rate) / (float) $this->daily_rate * 100
        );
    }

    /**
     * Best per-day rate for a stay of $days, stepping down to the weekly and
     * monthly plans when the customer qualifies for them.
     *
     * @return array{plan:string, per_day:float}
     */
    public function rateForDays(int $days): array
    {
        if ($days >= 30 && $this->monthly_rate) {
            return ['plan' => 'monthly', 'per_day' => round((float) $this->monthly_rate / 30, 2)];
        }

        if ($days >= 7 && $this->weekly_rate) {
            return ['plan' => 'weekly', 'per_day' => round((float) $this->weekly_rate / 7, 2)];
        }

        return ['plan' => 'daily', 'per_day' => $this->effective_daily_rate];
    }

    // ── Display helpers ──────────────────────────────────────────────────────

    public function getTitleAttribute(): string
    {
        return trim("{$this->brand} {$this->model}");
    }

    public function getFullTitleAttribute(): string
    {
        return trim("{$this->brand} {$this->model} {$this->year}");
    }

    public function getMainImageUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('vehicle_main', 'card')
            ?: $this->getFirstMediaUrl('vehicle_main')
            ?: ($this->main_image ? asset('storage/' . $this->main_image) : null);
    }

    public function getGalleryUrlsAttribute(): array
    {
        $urls = $this->getMedia('vehicle_gallery')
            ->map(fn(Media $m) => $m->getUrl('card'))
            ->all();

        if ($urls) {
            return $urls;
        }

        return collect($this->gallery_images ?? [])
            ->map(fn(string $path) => asset('storage/' . $path))
            ->all();
    }

    public function getStatusLabelAttribute(): string
    {
        $status = self::statuses()[$this->availability_status] ?? null;

        if (! $status) {
            return $this->availability_status;
        }

        return app()->getLocale() === 'ar' ? $status['label'] : $status['label_en'];
    }

    public function getStatusColorAttribute(): string
    {
        return self::statuses()[$this->availability_status]['color'] ?? 'gray';
    }

    public function getTransmissionLabelAttribute(): string
    {
        $t = self::transmissions()[$this->transmission] ?? null;

        if (! $t) {
            return $this->transmission;
        }

        return app()->getLocale() === 'ar' ? $t['label'] : $t['label_en'];
    }

    public function getFuelLabelAttribute(): string
    {
        $f = self::fuelTypes()[$this->fuel_type] ?? null;

        if (! $f) {
            return $this->fuel_type;
        }

        return app()->getLocale() === 'ar' ? $f['label'] : $f['label_en'];
    }

    public function getMileageLabelAttribute(): string
    {
        $ar = app()->getLocale() === 'ar';

        if (! $this->mileage_limit_per_day) {
            return $ar ? 'كيلومترات غير محدودة' : 'Unlimited mileage';
        }

        return $ar
            ? "{$this->mileage_limit_per_day} كم / يوم"
            : "{$this->mileage_limit_per_day} km / day";
    }
}
