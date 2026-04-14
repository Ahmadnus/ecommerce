{{--
    resources/views/components/price.blade.php
    ─────────────────────────────────────────────
    Usage:
        <x-price :amount="$product->base_price" />
        <x-price :amount="$product->discount_price" class="text-red-500 font-black" />
        <x-price :amount="50" symbol-only />

    Props:
        $amount      float   — Amount in JOD (base currency)
        $class       string  — Extra Tailwind classes
        $symbolOnly  bool    — Output symbol only, no number
--}}

@props([
    'amount'     => 0,
    'class'      => '',
    'symbolOnly' => false,
])

@php
    /** @var \App\Models\Currency $activeCurrency */
    // $activeCurrency is shared by ResolveCurrency middleware on every request.
    // Fallback in case middleware isn't registered yet.
    $cur = $activeCurrency
        ?? \App\Models\Currency::where('is_base', true)->first()
        ?? (object)['symbol' => 'د.أ', 'exchange_rate' => 1, 'code' => 'JOD'];

    $converted = round((float) $amount * (float) $cur->exchange_rate, 2);
    $formatted = number_format($converted, 2);
@endphp

@if($symbolOnly)
    <span {{ $attributes->merge(['class' => $class]) }}>{{ $cur->symbol }}</span>
@else
    <span {{ $attributes->merge(['class' => 'tabular-nums ' . $class]) }}>{{ $formatted }} {{ $cur->symbol }}</span>
@endif