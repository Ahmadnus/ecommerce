<?php

namespace App\View\Components;

use App\Models\Currency;
use Illuminate\View\Component;

class Price extends Component
{
    public float  $converted;
    public string $formatted;
    public string $symbol;
    public string $code;
    public string $display;
    public bool   $isPrefix;
    public string $tag;

    /**
     * @param float         $amount    Amount in base/storage currency (JOD or whatever is_base=1)
     * @param string|null   $currency  Force a specific currency code (e.g. 'JOD' for admin views).
     *                                 If null, reads session('currency_code') → falls back to base.
     * @param string        $tag       HTML wrapper tag
     */
    public function __construct(
        float   $amount,
        ?string $currency = null,
        string  $tag = 'span',
    ) {
        $resolved = $this->resolveCurrency($currency);

        $this->converted = $resolved->convert($amount);
        $this->formatted = number_format($this->converted, 2);
        $this->symbol    = $resolved->symbol;
        $this->code      = $resolved->code;
        $this->isPrefix  = $this->detectPrefix($resolved->symbol);
        $this->display   = $this->isPrefix
            ? $resolved->symbol . $this->formatted
            : $this->formatted . ' ' . $resolved->symbol;
        $this->tag       = $tag;
    }

    public function render()
    {
        return view('components.price');
    }

    // ── Private ───────────────────────────────────────────────────────────────

    private function resolveCurrency(?string $forceCode): Currency
    {
        // 1. Forced code (admin passing base currency explicitly)
        if ($forceCode) {
            $c = Currency::where('code', $forceCode)
                         ->where('is_active', true)->first();
            if ($c) return $c;
        }

        // 2. Session-selected currency (storefront user preference)
        $sessionCode = session('currency_code');
        if ($sessionCode) {
            $c = Currency::where('code', $sessionCode)
                         ->where('is_active', true)->first();
            if ($c) return $c;
        }

        // 3. Fallback: base currency
        return Currency::where('is_base', true)->first()
            ?? Currency::where('is_active', true)->orderBy('sort_order')->firstOrFail();
    }

    private function detectPrefix(string $symbol): bool
    {
        foreach (['$', '€', '£', '¥', '¢', '₹', '₩', '₪', '₺', '₦', '฿', 'R$', 'kr'] as $p) {
            if (str_starts_with($symbol, $p)) return true;
        }
        return false;
    }
}