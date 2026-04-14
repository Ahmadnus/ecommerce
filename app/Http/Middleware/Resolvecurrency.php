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
 * Runs on every web request. Resolves which currency is active in this session
 * and shares a $activeCurrency view variable globally.
 *
 * Priority:
 *   1. session('currency_code')    — user explicitly switched via ?currency=JOD
 *   2. JOD                         — hard-coded global default
 *   3. DB is_base = true           — safety fallback if JOD row is deleted
 *   4. First active currency row   — last resort
 */
class ResolveCurrency
{
    public function __construct(private readonly CurrencyService $currencyService) {}

    public function handle(Request $request, Closure $next): Response
    {
        // Allow ?currency=USD in URL to switch session currency
        if ($request->query('currency')) {
            $code = strtoupper($request->query('currency'));
            $candidate = Currency::active()->where('code', $code)->first();
            if ($candidate) {
                session(['currency_code' => $candidate->code]);
            }
        }

        // Resolve currency and share with all views
        $currency = $this->currencyService->getActive();
        view()->share('activeCurrency', $currency);

        return $next($request);
    }
}