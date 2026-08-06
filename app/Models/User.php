<?php

namespace App\Models;
use Filament\Panel;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
class User extends Authenticatable  // أضف implements FilamentUser
{
    use HasFactory, Notifiable, HasRoles;

    // ─── Mass-assignable fields ───────────────────────────────────────────────

protected $fillable = [
    'name', 'phone', 'email', 'password',
    'country_id', 'otp', 'otp_expires_at', 'phone_verified_at',
];
    // ─── Hidden from serialization ────────────────────────────────────────────

    protected $hidden = [
        'password',
        'remember_token',
    ];
protected $primaryKey = 'id';
    // ─── Casts ────────────────────────────────────────────────────────────────

  protected function casts(): array
{
    return [
        'phone_verified_at' => 'datetime',
        'otp_expires_at'    => 'datetime',
        'password'          => 'hashed',
    ];
}

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Whether the user's phone number has been verified.
     */
    public function hasVerifiedPhone(): bool
    {
        return $this->phone_verified_at !== null;
    }

    /**
     * Mark the phone as verified right now.
     */
    public function markPhoneAsVerified(): bool
    {
        return $this->forceFill([
            'phone_verified_at' => $this->freshTimestamp(),
        ])->saveQuietly();
    }
    /**
     * Rental reservations placed by this customer.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(\App\Models\Booking::class);
    }
}