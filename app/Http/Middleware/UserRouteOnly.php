<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * UserRouteOnly
 * ─────────────────────────────────────────────────────────────────────────────
 * Attached to the /login and /register routes.
 * • If already authenticated as admin → redirect to /admin.
 * • If already authenticated as user → redirect to /.
 * • Guest → allow through.
 */
class UserRouteOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            return auth()->user()->hasRole('admin')
                ? redirect()->to('/admin')
                : redirect()->to('/');
        }

        return $next($request);
    }
}