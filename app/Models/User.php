<?php

namespace App\Models;
use Filament\Panel;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
    public function wishlistedProducts(): BelongsToMany
{
    return $this->belongsToMany(
        \App\Models\Product::class,
        'wishlists',
        'user_id',
        'product_id'
    )->withTimestamps();
}
 
/**
 * Check if the user has wishlisted a specific product.
 * Use when the wishlist is already eager-loaded to avoid N+1.
 */
public function orders()
{
    return $this->hasMany(\App\Models\Order::class);
}
public function hasWishlisted(int $productId): bool
{
    // Works both with eager-loaded collection and a fresh query
    if ($this->relationLoaded('wishlistedProducts')) {
        return $this->wishlistedProducts->contains('id', $productId);
    }
 
    return $this->wishlistedProducts()->where('product_id', $productId)->exists();
}
public function getAuthIdentifierName()
{
    return 'phone';
}

}