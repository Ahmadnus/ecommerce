<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * GuestCheckout
 * ─────────────────────────────────────────────────────────────────────────────
 * Applied to the checkout routes.
 *
 * If guest_checkout_enabled = '1'  → allow anyone through (guest or auth)
 * If guest_checkout_enabled = '0'  → redirect unauthenticated users to login
 *
 * Register in bootstrap/app.php (Laravel 11):
 *   $middleware->alias(['guest.checkout' => \App\Http\Middleware\GuestCheckout::class]);
 *
 * Or in Kernel.php (Laravel 10):
 *   'guest.checkout' => \App\Http\Middleware\GuestCheckout::class,
 */
class GuestCheckout
{
    public function handle(Request $request, Closure $next): Response
    {
        $guestEnabled = get_otp_setting('guest_checkout_enabled', '0') === '1';

        if (!$guestEnabled && !auth()->check()) {
            // Store intended URL so user lands back on checkout after login
            return redirect()->route('login')
                             ->with('info', 'يرجى تسجيل الدخول لإتمام عملية الشراء.');
        }

        return $next($request);
    }
}