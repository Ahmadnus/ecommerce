<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * AdminRouteOnly
 * ─────────────────────────────────────────────────────────────────────────────
 * Attached to the /adlogin route.
 * • If already authenticated as non-admin → redirect to home (can't admin-login).
 * • If already authenticated as admin → redirect to /admin directly.
 * • Guest → allow through.
 */
class AdminRouteOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            if (auth()->user()->hasRole('admin')) {
                return redirect()->to('/admin');
            }
            // Non-admin trying to access the admin login page
            return redirect()->route('login')
                             ->withErrors(['phone_full' => 'لا يمكنك الوصول لبوابة الإدارة.']);
        }

        return $next($request);
    }
}