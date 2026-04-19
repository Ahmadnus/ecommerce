<?php

namespace App\Services;

use App\Models\Currency;
use Illuminate\Support\Facades\Cache;

/**
 * CurrencyService
 * ─────────────────────────────────────────────────────────────────────────────
 * Registered as a singleton in AppServiceProvider.
 *
 * Responsibilities:
 *   • Resolve the active Currency model for the current request
 *   • Convert amounts from JOD (base) → active currency
 *   • Format amounts with the correct symbol and decimal places
 *
 * Resolution priority (getActive):
 *   1. session('currency_code')          — user explicitly switched
 *   2. 'JOD'                             — hard-coded project default
 *   3. DB row where is_base = true       — safety if JOD row is renamed
 *   4. First active row in the table     — last resort
 *   5. Emergency placeholder             — never fails, even with empty DB
 *
 * All prices stored in DB as JOD. Conversion formula:
 *   displayed = round(jod_amount × exchange_rate, decimal_places)
 */
class CurrencyService
{
    /** Per-request cache — avoids multiple DB hits in one page load */
    private ?Currency $resolved = null;

    /**
     * Resolve and return the active Currency model.
     * Cached per-request in $this->resolved.
     */
    public function getActive(): Currency
    {
        if ($this->resolved !== null) {
            return $this->resolved;
        }

        $code = session('currency_code');

        // 1. Session-stored code
        if ($code) {
            $currency = Currency::active()->where('code', $code)->first();
            if ($currency) {
                return $this->resolved = $currency;
            }
            // Invalid code in session — clear it
            session()->forget('currency_code');
        }

        // 2. Hard-coded JOD default (base currency for this project)
        $jod = Currency::active()->where('code', 'JOD')->first();
        if ($jod) {
            return $this->resolved = $jod;
        }

        // 3. DB row flagged is_base = true
        $base = Currency::active()->where('is_base', true)->first();
        if ($base) {
            return $this->resolved = $base;
        }

        // 4. Any active row
        $any = Currency::active()->first();
        if ($any) {
            return $this->resolved = $any;
        }

        // 5. Emergency in-memory placeholder — never throws, even with an empty DB
        return $this->resolved = $this->makePlaceholder();
    }

    /**
     * Switch the active currency by code.
     * Stores in session. Returns false if the currency is not found.
     */
    public function switchTo(string $code): bool
    {
        $code     = strtoupper(trim($code));
        $currency = Currency::active()->where('code', $code)->first();

        if (! $currency) {
            return false;
        }

        session(['currency_code' => $currency->code]);
        $this->resolved = $currency; // update per-request cache

        return true;
    }

    /**
     * Convert a JOD amount to the active currency.
     *
     * @param  float  $jod   Amount in Jordanian Dinar (base)
     * @return float          Converted and rounded amount
     */
    public function convert(float $jod): float
    {
        $rate = (float) $this->getActive()->exchange_rate;
        return round($jod * $rate, 2);
    }

    /**
     * Format a JOD amount as a human-readable string with currency symbol.
     *
     * Examples:
     *   format(10.5)  →  "10.50 د.أ"   (JOD)
     *   format(10.5)  →  "$10.50"       (USD, symbol-prefix currency)
     *
     * Symbol placement is determined by whether the symbol looks like a prefix
     * ($, €, £) or a suffix (د.أ, ر.س).
     *
     * @param  float  $jod    Amount in JOD
     * @return string
     */
    public function format(float $jod): string
    {
        $currency  = $this->getActive();
        $converted = $this->convert($jod);
        $formatted = number_format($converted, 2);
        $symbol    = $currency->symbol;

        // Prefix symbols: $, €, £, ¥, ¢, ₹, ₩, ฿
        $prefixSymbols = ['$', '€', '£', '¥', '¢', '₹', '₩', '฿', 'R$', 'kr'];

        foreach ($prefixSymbols as $prefix) {
            if (str_starts_with($symbol, $prefix)) {
                return $symbol . $formatted;
            }
        }

        // Default: suffix
        return $formatted . ' ' . $symbol;
    }

    /**
     * Return a structured breakdown for use in Blade templates.
     *
     * Example return:
     * [
     *   'amount'   => 12.5,       // converted float
     *   'formatted'=> '12.50',    // formatted string without symbol
     *   'symbol'   => 'د.أ',
     *   'code'     => 'JOD',
     *   'display'  => '12.50 د.أ' // full formatted string
     * ]
     */
    public function breakdown(float $jod): array
    {
        $currency  = $this->getActive();
        $amount    = $this->convert($jod);
        $formatted = number_format($amount, 2);
        $full      = $this->format($jod);

        return [
            'amount'    => $amount,
            'formatted' => $formatted,
            'symbol'    => $currency->symbol,
            'code'      => $currency->code,
            'display'   => $full,
        ];
    }

    /**
     * Return all active currencies for a currency switcher UI.
     * Cached for 10 minutes to avoid repeated DB queries.
     */
    public function allActive(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember('currencies.active', 600, fn() =>
            Currency::active()->orderBy('sort_order')->orderBy('name')->get()
        );
    }

    /**
     * Invalidate the per-request cache.
     * Called after switching currency mid-request.
     */
    public function flush(): void
    {
        $this->resolved = null;
    }

    /**
     * Emergency placeholder — returned only when the currencies table is empty.
     * Prevents null-pointer errors everywhere that uses $activeCurrency.
     */
    private function makePlaceholder(): Currency
    {
        $placeholder = new Currency();
        $placeholder->forceFill([
            'code'          => 'JOD',
            'name'          => 'Jordanian Dinar',
            'symbol'        => 'د.أ',
            'exchange_rate' => '1.000000',
            'is_base'       => true,
            'is_active'     => true,
        ]);

        return $placeholder;
    }
}