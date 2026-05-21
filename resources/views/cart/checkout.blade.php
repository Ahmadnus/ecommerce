{{--
    resources/views/cart/checkout.blade.php
    ─────────────────────────────────────────────────────────────────────────────
    Supports both authenticated users and guests.
    $isGuest    = true when user is not logged in
    $guestEnabled = true when guest_checkout_enabled setting is '1'
    ─────────────────────────────────────────────────────────────────────────────
--}}
@extends('layouts.app')
@section('title', 'إتمام الطلب')

@push('head')
<style>
    .field {
        width: 100%; padding: 14px 16px;
        border: 1.5px solid #e5e3df; border-radius: 14px;
        background: #faf9f7; font-size: 13.5px; color: #1a1917;
        font-family: inherit; outline: none;
        transition: border-color .18s, box-shadow .18s, background .18s;
    }
    .field::placeholder { color: #b5b2ab; }
    .field:focus {
        background: #fff;
        border-color: var(--brand-color, #0ea5e9);
        box-shadow: 0 0 0 3px color-mix(in srgb, var(--brand-color, #0ea5e9) 12%, transparent);
    }
    .field.has-error { border-color: #ef4444; background: #fef9f9; }

    .step-n {
        width: 26px; height: 26px; border-radius: 50%;
        background: #1a1917; color: #fff;
        font-size: 11px; font-weight: 700;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }

    .cart-row { transition: background .15s; }
    .cart-row:hover { background: #faf9f7; }

    .zone-option {
        display: flex; align-items: center; justify-content: space-between;
        padding: 11px 14px; border: 1.5px solid #e5e3df;
        border-radius: 12px; cursor: pointer; transition: all .15s; background: #faf9f7;
    }
    .zone-option:has(input:checked) {
        border-color: var(--brand-color, #0ea5e9);
        background: color-mix(in srgb, var(--brand-color, #0ea5e9) 5%, #fff);
    }
    .zone-option:hover { border-color: var(--brand-color, #0ea5e9); }

    .zones-loading { display: none; }
    .zones-loading.active { display: flex; }

    /* Guest banner */
    .guest-banner {
        background: linear-gradient(135deg,
            color-mix(in srgb, var(--brand-color, #0ea5e9) 8%, #fff),
            color-mix(in srgb, var(--brand-color, #0ea5e9) 3%, #fff));
        border: 1.5px solid color-mix(in srgb, var(--brand-color, #0ea5e9) 25%, transparent);
        border-radius: 16px; padding: 16px 20px;
    }

    @keyframes up { from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)} }
    .u1{animation:up .38s ease .05s both}.u2{animation:up .38s ease .12s both}
    .u3{animation:up .38s ease .19s both}.u4{animation:up .38s ease .26s both}
    .u5{animation:up .38s ease .33s both}
</style>
@endpush
@section('content')

@php
    $cur  = $activeCurrency;
    $rate = (float) $cur->exchange_rate;
    $sym  = $cur->symbol;
    $cv   = fn(float $jod): string => number_format(round($jod * $rate, 2), 2);

    $isRtl = app()->getLocale() === 'ar';
@endphp

<script>
window.CHECKOUT = {
    subtotalJod: {{ (float) $summary['subtotal'] }},
    rate: {{ $rate }},
    symbol: '{{ $sym }}',
};
</script>

<div class="min-h-screen" dir="{{ $isRtl ? 'rtl' : 'ltr' }}" style="background-color: var(--bg-color);">
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">

    {{-- Breadcrumb --}}
    <div class="u1 mb-8">
        <nav class="flex items-center gap-2 text-xs text-[#9a9793] mb-3">
            <a href="{{ route('products.index') }}" class="hover:text-[#1a1917] transition-colors">
                {{ __('app.checkout.breadcrumb_store') }}
            </a>
            <span>/</span>
            <a href="{{ route('cart.index') }}" class="hover:text-[#1a1917] transition-colors">
                {{ __('app.checkout.breadcrumb_cart') }}
            </a>
            <span>/</span>
            <span class="text-[#1a1917] font-medium">
                {{ __('app.checkout.breadcrumb_checkout') }}
            </span>
        </nav>

        <h1 class="font-display text-2xl lg:text-3xl font-bold text-[#1a1917] tracking-tight">
            {{ __('app.checkout.heading') }}
        </h1>

        <p class="text-xs text-[#9a9793] mt-1">
            {{ __('app.checkout.prices_in', ['currency' => $cur->name, 'symbol' => $sym]) }}
        </p>
    </div>

    @if($errors->any())
    <div class="u1 mb-6 flex items-start gap-3 bg-red-50 border border-red-100 rounded-2xl px-5 py-4">
        <svg class="w-4 h-4 text-red-400 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
        </svg>

        <ul class="text-red-600 text-sm space-y-0.5">
            @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if(session('error'))
    <div class="u1 mb-6 flex items-start gap-3 bg-red-50 border border-red-100 rounded-2xl px-5 py-4">
        <span class="text-red-600 text-sm font-semibold">
            {{ session('error') }}
        </span>
    </div>
    @endif

    {{-- Guest banner --}}
    @if($isGuest && $guestEnabled)
    <div class="u1 guest-banner mb-6 flex items-start justify-between gap-4 flex-wrap">
        <div class="flex items-start gap-3">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5"
                 style="background:color-mix(in srgb,var(--brand-color,#0ea5e9) 15%,#fff)">

                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                     style="color:var(--brand-color,#0ea5e9)">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>

            <div>
                <p class="text-sm font-bold text-gray-800 mb-0.5">
                    {{ __('app.checkout.guest_title') }}
                </p>

                <p class="text-xs text-gray-500 leading-relaxed">
                    {{ __('app.checkout.guest_sub') }}
                </p>
            </div>
        </div>

        <a href="{{ route('login') }}"
           class="flex-shrink-0 text-xs font-bold px-4 py-2 rounded-xl border-2 transition-all hover:bg-white"
           style="border-color:var(--brand-color,#0ea5e9);color:var(--brand-color,#0ea5e9)">
            {{ __('app.checkout.login') }}
        </a>
    </div>
    @endif

    <form action="{{ route('checkout.place') }}" method="POST" id="checkout-form">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 lg:gap-8 items-start">

        {{-- LEFT --}}
        <div class="lg:col-span-3 space-y-5">

            {{-- STEP 1 --}}
            <div class="u2 bg-white rounded-2xl border border-[#ece9e4] overflow-hidden">

                <div class="flex items-center justify-between px-5 py-4 border-b border-[#f0ede8]">
                    <div class="flex items-center gap-3">
                        <div class="step-n">١</div>

                        <span class="font-semibold text-[#1a1917] text-sm">
                            {{ __('app.checkout.step1_title') }}
                        </span>
                    </div>

                    <a href="{{ route('cart.index') }}"
                       class="text-xs font-medium text-[#9a9793] hover:text-[#1a1917] transition-colors flex items-center gap-1">

                        <svg class="w-3 h-3 {{ $isRtl ? '' : 'rotate-180' }}"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>

                        {{ __('app.checkout.edit') }}
                    </a>
                </div>

                <div class="divide-y divide-[#f7f6f3]">
                    @foreach($summary['items'] as $item)

                    <div class="cart-row flex items-center gap-4 px-5 py-4">

                        <div class="w-14 h-14 rounded-xl overflow-hidden bg-[#f7f6f3] border border-[#f0ede8] flex-shrink-0">
                            <img src="{{ $item['image'] ?? '' }}"
                                 class="w-full h-full object-cover"
                                 alt="{{ $item['name'] }}">
                        </div>

                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-[#1a1917] line-clamp-1">
                                {{ $item['name'] }}
                            </p>

                            @if(!empty($item['variant_name']))
                            <p class="text-xs font-medium mt-0.5"
                               style="color:var(--brand-color)">
                                {{ $item['variant_name'] }}
                            </p>
                            @endif

                            <p class="text-xs text-[#9a9793] mt-0.5">
                                {{ $item['quantity'] }} × {{ $cv($item['price']) }} {{ $sym }}
                            </p>
                        </div>

                        <p class="text-sm font-bold text-[#1a1917] flex-shrink-0 tabular-nums">
                            {{ $cv($item['subtotal']) }} {{ $sym }}
                        </p>
                    </div>

                    @endforeach
                </div>
            </div>

            {{-- STEP 2 --}}
            <div class="u3 bg-white rounded-2xl border border-[#ece9e4] overflow-hidden">

                <div class="flex items-center gap-3 px-5 py-4 border-b border-[#f0ede8]">
                    <div class="step-n">٢</div>

                    <span class="font-semibold text-[#1a1917] text-sm">
                        {{ __('app.checkout.step2_title') }}
                    </span>
                </div>

                <div class="p-5 space-y-4">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        {{-- NAME --}}
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-[#6b6966] mb-2 uppercase tracking-wide">
                                {{ __('app.checkout.field_name') }}
                            </label>

                            <input type="text"
                                   name="shipping_name"
                                   value="{{ old('shipping_name', $user->name ?? '') }}"
                                   required
                                   placeholder="{{ __('app.checkout.field_name_ph') }}"
                                   class="field @error('shipping_name') has-error @enderror">

                            @error('shipping_name')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- PHONE --}}
                        <div class="{{ $isGuest ? '' : 'sm:col-span-2' }}">

                            <label class="block text-xs font-bold text-[#6b6966] mb-2 uppercase tracking-wide">
                                {{ __('app.checkout.field_phone') }}
                            </label>

                            <div class="flex gap-2 items-stretch">

                                <div id="phone-prefix-badge"
                                     class="flex items-center justify-center px-3 rounded-xl border-[1.5px] border-[#e5e3df]
                                            bg-[#f7f6f3] text-sm font-bold text-[#6b6966] flex-shrink-0 min-w-[64px] text-center"
                                     style="height:50px">
                                    +
                                </div>

                                <input type="hidden"
                                       name="shipping_phone_code"
                                       id="shipping-phone-code"
                                       value="">

                                <input type="tel"
                                       name="shipping_phone"
                                       id="shipping-phone-input"
                                       value="{{ old('shipping_phone') }}"
                                       required
                                       placeholder="{{ __('app.checkout.field_phone_ph') }}"
                                       dir="ltr"
                                       class="field flex-1 @error('shipping_phone') has-error @enderror">
                            </div>

                            @error('shipping_phone')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- EMAIL --}}
                        @if($isGuest)
                        <div>
                            <label class="block text-xs font-bold text-[#6b6966] mb-2 uppercase tracking-wide">
                                {{ __('app.checkout.field_email') }}
                            </label>

                            <input type="email"
                                   name="guest_email"
                                   value="{{ old('guest_email') }}"
                                   placeholder="example@mail.com"
                                   dir="ltr"
                                   class="field @error('guest_email') has-error @enderror">

                            @error('guest_email')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        @endif

                        {{-- ADDRESS --}}
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-[#6b6966] mb-2 uppercase tracking-wide">
                                {{ __('app.checkout.field_address') }}
                            </label>

                            <input type="text"
                                   name="shipping_address"
                                   value="{{ old('shipping_address') }}"
                                   required
                                   placeholder="{{ __('app.checkout.field_address_ph') }}"
                                   class="field @error('shipping_address') has-error @enderror">

                            @error('shipping_address')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- CITY --}}
                        <div>
                            <label class="block text-xs font-bold text-[#6b6966] mb-2 uppercase tracking-wide">
                                {{ __('app.checkout.field_city') }}
                            </label>

                            <input type="text"
                                   name="shipping_city"
                                   value="{{ old('shipping_city') }}"
                                   required
                                   placeholder="{{ __('app.checkout.field_city_ph') }}"
                                   class="field @error('shipping_city') has-error @enderror">

                            @error('shipping_city')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- COUNTRY --}}
                        <div>
                            <label class="block text-xs font-bold text-[#6b6966] mb-2 uppercase tracking-wide">
                                {{ __('app.checkout.field_country') }}
                            </label>

                            <select name="country_id"
                                    id="country-select"
                                    class="field"
                                    onchange="PhoneSync.update(this.value)">

                                <option value="">
                                    {{ __('app.checkout.country_placeholder') }}
                                </option>

                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}">
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>

                    {{-- NOTES --}}
                    <div>
                        <label class="block text-xs font-bold text-[#6b6966] mb-2 uppercase tracking-wide">
                            {{ __('app.checkout.field_notes') }}
                        </label>

                        <textarea name="notes"
                                  rows="2"
                                  class="field resize-none"
                                  placeholder="{{ __('app.checkout.field_notes_ph') }}">{{ old('notes') }}</textarea>
                    </div>

                </div>
            </div>

            {{-- STEP 3 --}}
            <div class="u4 bg-white rounded-2xl border border-[#ece9e4] overflow-hidden">

                <div class="flex items-center gap-3 px-5 py-4 border-b border-[#f0ede8]">
                    <div class="step-n">٣</div>

                    <span class="font-semibold text-[#1a1917] text-sm">
                        {{ __('app.checkout.step3_title') }}
                    </span>
                </div>

                <div class="p-5">

                    <label class="flex items-center gap-4 p-4 border-2 border-[#1a1917] rounded-xl cursor-pointer bg-[#faf9f7]">

                        <input type="radio"
                               name="payment_method"
                               value="cod"
                               checked
                               class="w-4 h-4 accent-[#1a1917] flex-shrink-0">

                        <div class="w-10 h-10 bg-white rounded-xl border border-[#ece9e4] flex items-center justify-center flex-shrink-0 shadow-sm">
                            <svg class="w-5 h-5 text-[#6b6966]"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="1.8"
                                      d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>

                        <div>
                            <p class="text-sm font-bold text-[#1a1917]">
                                {{ __('app.checkout.cod_title') }}
                            </p>

                            <p class="text-xs text-[#9a9793] mt-0.5">
                                {{ __('app.checkout.cod_sub') }}
                            </p>
                        </div>
                    </label>

                </div>
            </div>

        </div>

        {{-- RIGHT --}}
        <div class="lg:col-span-2">

            <div class="u5 bg-white rounded-2xl border border-[#ece9e4] overflow-hidden sticky top-20">

                <div class="px-5 py-4 border-b border-[#f0ede8] flex items-center justify-between">

                    <h2 class="font-semibold text-[#1a1917] text-sm">
                        {{ __('app.checkout.order_summary') }}
                    </h2>

                    <span class="text-[10px] text-[#b5b2ab] bg-[#f7f6f3] px-2 py-1 rounded-lg font-bold">
                        {{ $sym }} {{ $cur->code }}
                    </span>
                </div>

                <div class="p-5">

                    <div class="space-y-3 mb-5">

                        @foreach($summary['items'] as $item)

                        <div class="flex items-start gap-3">

                            <div class="w-9 h-9 rounded-lg overflow-hidden bg-[#f7f6f3] border border-[#f0ede8] flex-shrink-0">
                                <img src="{{ $item['image'] ?? '' }}"
                                     class="w-full h-full object-cover"
                                     alt="">
                            </div>

                            <div class="flex-1 min-w-0">

                                <p class="text-xs font-semibold text-[#1a1917] line-clamp-1">
                                    {{ $item['name'] }}
                                </p>

                                @if(!empty($item['variant_name']))
                                <p class="text-[10px] text-[#9a9793]">
                                    {{ $item['variant_name'] }}
                                </p>
                                @endif

                                <p class="text-[10px] text-[#b5b2ab]">
                                    × {{ $item['quantity'] }}
                                </p>
                            </div>

                            <p class="text-xs font-bold text-[#1a1917] flex-shrink-0 tabular-nums">
                                {{ $cv($item['subtotal']) }} {{ $sym }}
                            </p>
                        </div>

                        @endforeach
                    </div>

                    {{-- TOTAL --}}
                    <div class="space-y-2.5 text-xs border-t border-[#f0ede8] pt-4 mb-4">

                        <div class="flex justify-between text-[#9a9793]">
                            <span>{{ __('app.checkout.grand_total') }}</span>

                            <span class="font-semibold text-[#1a1917] tabular-nums">
                                {{ $cv($summary['subtotal']) }} {{ $sym }}
                            </span>
                        </div>
                    </div>

                    {{-- BUTTON --}}
                    <button type="submit"
                            id="place-btn"
                            class="w-full py-4 rounded-xl bg-[#1a1917] hover:bg-[#2d2c2a]
                                   text-white font-bold text-sm tracking-wide transition-colors
                                   flex items-center justify-center gap-2
                                   shadow-lg shadow-black/15 active:scale-[.98]">

                        <svg class="w-4 h-4"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2.5"
                                  d="M5 13l4 4L19 7"/>
                        </svg>

                        {{ __('app.checkout.place_order') }}
                    </button>

                </div>
            </div>
        </div>

    </div>
    </form>

</div>
</div>
@endsection

@push('scripts')
<script>

window.COUNTRY_CODES = {
    @foreach($countries as $country)
    "{{ $country->id }}": "{{ $country->calling_code }}",
    @endforeach
};

const PhoneSync = {

    update(countryId) {

        const badge      = document.getElementById('phone-prefix-badge');
        const codeInput  = document.getElementById('shipping-phone-code');

        if (!countryId || !window.COUNTRY_CODES[countryId]) {
            badge.textContent = '+';
            codeInput.value = '';
            return;
        }

        const code = window.COUNTRY_CODES[countryId];

        badge.textContent = '+' + code;
        codeInput.value = code;
    },

    prepareSubmit() {

        const code  = document.getElementById('shipping-phone-code').value;
        const input = document.getElementById('shipping-phone-input');

        if (code && input.value.startsWith('+' + code)) {
            input.value = input.value.slice(code.length + 1);
        }

        if (code && input.value.startsWith('0')) {
            input.value = input.value.slice(1);
        }
    }
};

document.getElementById('checkout-form')?.addEventListener('submit', function () {

    PhoneSync.prepareSubmit();

    const btn = document.getElementById('place-btn');

    btn.disabled = true;

    btn.innerHTML = `
        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10"
                    stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75"
                  fill="currentColor"
                  d="M4 12a8 8 0 018-8v8H4z"/>
        </svg>

        {{ __("app.checkout.placing_order_text") }}
    `;
});

</script>
@endpush