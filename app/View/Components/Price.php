<?php

namespace App\View\Components;

use App\Services\CurrencyService;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Price component
 * ─────────────────────────────────────────────────────────────────────────────
 * Usage in Blade:
 *
 *   <x-price :amount="$product->base_price" />
 *   <x-price :amount="$product->discount_price" class="text-red-500 font-black" />
 *   <x-price :amount="49.99" tag="div" />
 *
 * Props:
 *   amount (float)   — Amount in JOD (base currency). REQUIRED.
 *   class  (string)  — Extra CSS classes forwarded to the wrapper element.
 *   tag    (string)  — HTML tag to render. Default: 'span'.
 *
 * The component:
 *   1. Reads $activeCurrency from the view share (set by ResolveCurrency middleware)
 *   2. Converts: displayed = round(amount × exchange_rate, 2)
 *   3. Formats:  "12.50 د.أ"  or  "$12.50"  depending on symbol type
 *   4. Renders a <span> (or custom tag) with the formatted price + symbol
 *
 * Why a component class instead of a pure Blade file?
 *   → The conversion logic belongs in PHP, not in a template.
 *   → The component is testable independently of the view layer.
 *   → It keeps every usage in Blade to a single clean tag.
 */
class Price extends Component
{
    /** Converted amount (JOD × exchange_rate) */
    public float $converted;

    /** Formatted number string, e.g. "12.50" */
    public string $formatted;

    /** Currency symbol, e.g. "د.أ" or "$" */
    public string $symbol;

    /** Currency code, e.g. "JOD" */
    public string $code;

    /** Full display string with symbol in correct position */
    public string $display;

    /** Whether the symbol is a prefix ($, €, £ …) or suffix (د.أ, ر.س …) */
    public bool $isPrefix;

    public function __construct(
        public float  $amount,
        public string $tag    = 'span',
        // Note: $class is handled by Blade's $attributes->merge(), not a prop
    ) {
        $service  = app(CurrencyService::class);
        $currency = $service->getActive();

        $this->converted  = $service->convert($this->amount);
        $this->formatted  = number_format($this->converted, 2);
        $this->symbol     = $currency->symbol;
        $this->code       = $currency->code;
        $this->isPrefix   = $this->detectPrefix($currency->symbol);

        $this->display    = $this->isPrefix
            ? $this->symbol . $this->formatted
            : $this->formatted . ' ' . $this->symbol;
    }

    public function render(): View|Closure|string
    {
        return view('components.price');
    }

    /**
     * Detect whether the currency symbol should appear before or after the number.
     * Prefix: $, €, £, ¥, ₹, ₩, ฿, R$, kr, CHF, etc.
     * Suffix: د.أ, ر.س, ج.م, ل.ل, د.ب, etc.
     */
    private function detectPrefix(string $symbol): bool
    {
        $prefixPatterns = ['$', '€', '£', '¥', '¢', '₹', '₩', '₪', '₺', '₦', '฿', 'R$', 'kr', 'CHF', 'Fr'];

        foreach ($prefixPatterns as $pattern) {
            if (str_starts_with($symbol, $pattern)) {
                return true;
            }
        }

        return false;
    }
}