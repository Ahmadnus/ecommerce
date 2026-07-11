<?php

namespace App\Services;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

/**
 * PasswordResetService — wraps the framework password broker with the
 * app's reset behavior. Returns the broker status string.
 */
class PasswordResetService
{
    /**
     * Send a reset link. Returns a Password::* status constant.
     */
    public function sendResetLink(array $credentials): string
    {
        return Password::sendResetLink($credentials);
    }

    /**
     * $credentials: email, password, password_confirmation, token.
     * Returns a Password::* status constant.
     */
    public function reset(array $credentials): string
    {
        return Password::reset(
            $credentials,
            function ($user, $password) {
                $user->forceFill([
                    'password'       => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );
    }
}
