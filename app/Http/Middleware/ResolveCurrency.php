<?php

namespace App\Http\Middleware;

use App\Models\Currency;
use App\Services\CurrencyService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * ResolveCurrency
 * ─────────────────────────────────────────────────────────────────────────────
 * Runs on every web request (registered in bootstrap/app.php).
 *
 * 1. Checks for a ?currency=USD query parameter → validates → stores in session
 * 2. Resolves the active Currency via CurrencyService (session → JOD → DB)
 * 3. Shares $activeCurrency with ALL Blade views via view()->share()
 *
 * This means every Blade view, every component, every partial automatically
 * has access to $activeCurrency without any controller boilerplate.
 */
class ResolveCurrency
{
    public function __construct(private readonly CurrencyService $currencyService) {}

    public function handle(Request $request, Closure $next): Response
    {
        // Allow ?currency=USD in the URL to switch the active currency
        if ($request->query('currency')) {
            $this->currencyService->switchTo(
                (string) $request->query('currency')
            );
            // Flush per-request cache so getActive() re-resolves with the new code
            $this->currencyService->flush();
        }

        // Resolve and share — every Blade template now has $activeCurrency
        $currency = $this->currencyService->getActive();
        view()->share('activeCurrency', $currency);

        return $next($request);
    }
}