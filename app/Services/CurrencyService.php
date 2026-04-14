<?php

namespace App\Services;

use App\Models\Currency;
use Illuminate\Support\Facades\Cache;

/**
 * CurrencyService
 * ─────────────────────────────────────────────────────────────────────────────
 * Single source of truth for:
 *   - Resolving the active currency (session → JOD default → DB fallback)
 *   - Converting amounts from JOD (base) to the active currency
 *   - Formatting prices with the correct symbol
 *
 * JOD is the BASE currency: exchange_rate = 1.000000
 * All other currencies store their rate RELATIVE TO JOD.
 * Example:  USD exchange_rate = 0.7067   →  1 JOD = 0.7067 USD
 *           SAR exchange_rate = 2.6500   →  1 JOD = 2.6500 SAR
 */
class CurrencyService
{
    /** ISO code of the global default currency */
    public const DEFAULT_CODE = 'JOD';

    /** Symbol used when no currency object is available */
    public const DEFAULT_SYMBOL = 'د.أ';

    // ─── Resolution ──────────────────────────────────────────────────────────

    /**
     * Get the currently active Currency model.
     * Cached per-request in a static property to avoid repeated DB hits.
     */
    public function getActive(): Currency
    {
        static $resolved = null;

        if ($resolved !== null) {
            return $resolved;
        }

        $code     = session('currency_code', self::DEFAULT_CODE);
        $resolved = $this->findByCode($code)               // 1. session / default
                 ?? $this->findByCode(self::DEFAULT_CODE)  // 2. JOD hardcoded
                 ?? $this->dbBase()                        // 3. is_base row
                 ?? $this->dbFirstActive();                // 4. any active row

        // Should never be null after fallbacks, but guarantee a safe object
        if ($resolved === null) {
            $resolved = $this->makePlaceholder();
        }

        return $resolved;
    }

    /**
     * Explicitly set the session currency by code.
     * Returns the resolved Currency or null if code is invalid.
     */
    public function setActive(string $code): ?Currency
    {
        $currency = $this->findByCode(strtoupper($code));
        if ($currency) {
            session(['currency_code' => $currency->code]);
        }
        return $currency;
    }

    // ─── Conversion & Formatting ─────────────────────────────────────────────

    /**
     * Convert an amount stored in JOD to the active currency.
     */
    public function convert(float $amountInJod, ?Currency $currency = null): float
    {
        $currency ??= $this->getActive();
        return round($amountInJod * (float) $currency->exchange_rate, 2);
    }

    /**
     * Format a JOD amount as a localized string with symbol.
     * Example: format(50) → "50.00 د.أ"
     */
    public function format(float $amountInJod, ?Currency $currency = null): string
    {
        $currency ??= $this->getActive();
        $converted = $this->convert($amountInJod, $currency);
        $formatted = number_format($converted, 2);
        return "{$formatted} {$currency->symbol}";
    }

    /**
     * Return converted amount + symbol separately (useful for Blade).
     */
    public function breakdown(float $amountInJod, ?Currency $currency = null): array
    {
        $currency ??= $this->getActive();
        return [
            'amount'   => $this->convert($amountInJod, $currency),
            'symbol'   => $currency->symbol,
            'code'     => $currency->code,
            'currency' => $currency,
        ];
    }

    // ─── Private helpers ─────────────────────────────────────────────────────

    private function findByCode(string $code): ?Currency
    {
        // Cache active currencies list for 5 minutes to avoid N+1 per request
        $all = Cache::remember('currencies_active', 300, fn () =>
            Currency::active()->get()->keyBy('code')
        );

        return $all->get($code);
    }

    private function dbBase(): ?Currency
    {
        return Currency::active()->where('is_base', true)->first();
    }

    private function dbFirstActive(): ?Currency
    {
        return Currency::active()->first();
    }

    /**
     * Emergency placeholder — prevents null errors if DB has no currencies.
     */
    private function makePlaceholder(): Currency
    {
        $c                = new Currency();
        $c->name          = 'Jordanian Dinar';
        $c->code          = self::DEFAULT_CODE;
        $c->symbol        = self::DEFAULT_SYMBOL;
        $c->exchange_rate = 1.0;
        $c->is_base       = true;
        $c->is_active     = true;
        return $c;
    }
}