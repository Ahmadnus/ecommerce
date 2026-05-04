<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

class Country extends Model
{
    /**
     * ISO code of the system-protected primary country.
     * Use this constant everywhere instead of hardcoding 'JO' or an ID.
     *
     * Examples:
     *   Country::JORDAN_CODE                          → 'JO'
     *   Country::jordan()                             → Country model instance
     *   Country::jordan()->id                         → integer ID
     *   $country->code === Country::JORDAN_CODE       → boolean check
     */
    public const JORDAN_CODE = 'JO';

    /**
     * Fields that may NEVER be changed on a system-protected record.
     * Any attempt to update these will throw a ValidationException.
     */
    public const IMMUTABLE_SYSTEM_FIELDS = ['code', 'calling_code', 'is_system'];

    protected $fillable = [
        'name',
        'name_en',
        'code',
        'calling_code',
        'is_active',
        'sort_order',
        'is_system',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'is_system'  => 'boolean',
        'sort_order' => 'integer',
    ];

    // ── Boot — enforce system-record protection ────────────────────────────────

    protected static function boot(): void
    {
        parent::boot();

        // Block deletion of system records at the model level
        static::deleting(function (Country $country): void {
            if ($country->is_system) {
                throw ValidationException::withMessages([
                    'country' => __('admin.countries.cannot_delete_system'),
                ]);
            }
        });

        // Block changes to immutable fields on system records
        static::updating(function (Country $country): void {
            if (!$country->is_system) {
                return;
            }

            $dirty = array_keys($country->getDirty());

            $violations = array_intersect($dirty, self::IMMUTABLE_SYSTEM_FIELDS);

            if (!empty($violations)) {
                throw ValidationException::withMessages([
                    'country' => __('admin.countries.cannot_edit_system_fields', [
                        'fields' => implode(', ', $violations),
                    ]),
                ]);
            }
        });
    }

    // ── Global accessor for Jordan ─────────────────────────────────────────────

    /**
     * Returns the Jordan Country instance (cached for the request lifetime).
     *
     * Usage anywhere in the app:
     *   $jordan   = Country::jordan();
     *   $jordanId = Country::jordan()->id;
     */
    public static function jordan(): self
    {
        return once(fn () => static::where('code', self::JORDAN_CODE)->firstOrFail());
    }

    /**
     * Convenience check — is this the primary/system country?
     */
    public function isJordan(): bool
    {
        return $this->code === self::JORDAN_CODE;
    }

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

    public function callingCodeFormatted(): ?string
    {
        return $this->calling_code ? '+' . ltrim($this->calling_code, '+') : null;
    }

    public function dropdownLabel(): string
    {
        $code = $this->callingCodeFormatted();
        return $code ? "{$this->name} {$code}" : $this->name;
    }
}