<?php

namespace App\Services;

use App\Models\Country;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

/**
 * AuthService
 *
 * Business logic for login/registration: rate limiting, credential checks,
 * and user creation. Session handling and redirects stay in the controller.
 */
class AuthService
{
    public function getActiveCountries()
    {
        return Country::active()->ordered()->get();
    }

    public function throttleKey(string $identity, string $ip): string
    {
        return 'login|' . $identity . '|' . $ip;
    }

    /**
     * Seconds until another attempt is allowed, or null if not throttled.
     */
    public function tooManyAttempts(string $throttleKey): ?int
    {
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            return RateLimiter::availableIn($throttleKey);
        }

        return null;
    }

    /**
     * Verify credentials against email or phone. Returns the user on
     * success; null on failure (and records a throttle hit).
     */
    public function attemptLogin(string $identity, bool $isEmail, string $password, string $throttleKey): ?User
    {
        $user = $isEmail
            ? User::where('email', $identity)->first()
            : User::where('phone', $identity)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            RateLimiter::hit($throttleKey, 60);

            return null;
        }

        RateLimiter::clear($throttleKey);

        return $user;
    }

    /**
     * Change a user's password (already validated by the controller).
     */
    public function updatePassword(User $user, string $password): void
    {
        $user->update([
            'password' => Hash::make($password),
        ]);
    }

    /**
     * Create a user from validated registration data.
     */
    public function register(array $validated, bool $hasPhone, bool $hasEmail): User
    {
        return User::create([
            'name'       => $validated['name'],
            'phone'      => $hasPhone ? ($validated['phone_full'] ?? null) : null,
            'email'      => $hasEmail ? ($validated['email'] ?? null) : null,
            'country_id' => $validated['country_id'] ?? null,
            'password'   => Hash::make($validated['password']),
        ]);
    }
}
