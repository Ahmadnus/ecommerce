{{--
    resources/views/cart/checkout.blade.php
    ─────────────────────────────────────────────────────────────────────────────
    All $summary values are in JOD. $activeCurrency shared by middleware.

    Step 2 now contains a two-level shipping selector:
      1. Country dropdown (from $countries collection)
      2. Zone dropdown (populated by AJAX when country changes)

    When a zone is selected, vanilla JS recalculates the displayed total
    using the zone's shipping_price (in JOD) × $activeCurrency->exchange_rate.
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
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }

    .cart-row { transition: background .15s; }
    .cart-row:hover { background: #faf9f7; }

    /* Zone option card */
    .zone-option {
        display: flex; align-items: center; justify-content: space-between;
        padding: 11px 14px;
        border: 1.5px solid #e5e3df; border-radius: 12px;
        cursor: pointer; transition: all .15s;
        background: #faf9f7;
    }
    .zone-option:has(input:checked) {
        border-color: var(--brand-color, #0ea5e9);
        background: color-mix(in srgb, var(--brand-color, #0ea5e9) 5%, #fff);
    }
    .zone-option:hover { border-color: var(--brand-color, #0ea5e9); }

    /* Delivery days badge */
    .days-badge {
        font-size: 10px; font-weight: 700;
        padding: 2px 8px; border-radius: 99px;
        background: rgba(0,0,0,.05); color: #6b6966;
    }

    /* Spinner */
    .zones-loading { display: none; }
    .zones-loading.active { display: flex; }

    @keyframes up {
        from { opacity: 0; transform: translateY(14px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .u1 { animation: up .38s ease .05s both; }
    .u2 { animation: up .38s ease .12s both; }
    .u3 { animation: up .38s ease .19s both; }
    .u4 { animation: up .38s ease .26s both; }
    .u5 { animation: up .38s ease .33s both; }
</style>
@endpush

@section('content')

@php
    $cur           = $activeCurrency;
    $rate          = (float) $cur->exchange_rate;
    $sym           = $cur->symbol;
    $cv            = fn(float $jod): string => number_format(round($jod * $rate, 2), 2);
    $freeThreshold = $summary['free_threshold'] ?? 50.0;
@endphp

{{-- Pass data to JS --}}
<script>
    window.CHECKOUT = {
        subtotalJod:  {{ (float) $summary['subtotal'] }},
        rate:         {{ $rate }},
        symbol:       '{{ $sym }}',
        zonesApiBase: '{{ url('/api/shipping/zones') }}',
    };
</script>

<div class="min-h-screen bg-[#f7f6f3]" dir="rtl">
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">

    {{-- Header --}}
    <div class="u1 mb-8">
        <nav class="flex items-center gap-2 text-xs text-[#9a9793] mb-3">
            <a href="{{ route('products.index') }}" class="hover:text-[#1a1917] transition-colors">المتجر</a>
            <span>/</span>
            <a href="{{ route('cart.index') }}" class="hover:text-[#1a1917] transition-colors">السلة</a>
            <span>/</span>
            <span class="text-[#1a1917] font-medium">إتمام الطلب</span>
        </nav>
        <h1 class="font-display text-2xl lg:text-3xl font-bold text-[#1a1917] tracking-tight">
            إتمام الطلب
        </h1>
        <p class="text-xs text-[#9a9793] mt-1">
            الأسعار بـ {{ $cur->name }} ({{ $sym }})
        </p>
    </div>

    @if($errors->any())
    <div class="u1 mb-6 flex items-start gap-3 bg-red-50 border border-red-100 rounded-2xl px-5 py-4">
        <svg class="w-4 h-4 text-red-400 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
        </svg>
        <ul class="text-red-600 text-sm space-y-0.5">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    @if(session('error'))
    <div class="u1 mb-6 flex items-start gap-3 bg-red-50 border border-red-100 rounded-2xl px-5 py-4">
        <span class="text-red-600 text-sm font-semibold">{{ session('error') }}</span>
    </div>
    @endif

    <form action="{{ route('checkout.place') }}" method="POST" id="checkout-form">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 lg:gap-8 items-start">

        {{-- ════ LEFT: Steps ══════════════════════════════════════════ --}}
        <div class="lg:col-span-3 space-y-5">

            {{-- STEP 1: Cart review --}}
            <div class="u2 bg-white rounded-2xl border border-[#ece9e4] overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-[#f0ede8]">
                    <div class="flex items-center gap-3">
                        <div class="step-n">١</div>
                        <span class="font-semibold text-[#1a1917] text-sm">المنتجات المطلوبة</span>
                    </div>
                    <a href="{{ route('cart.index') }}"
                       class="text-xs font-medium text-[#9a9793] hover:text-[#1a1917] transition-colors flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        تعديل
                    </a>
                </div>
                <div class="divide-y divide-[#f7f6f3]">
                    @foreach($summary['items'] as $item)
                    <div class="cart-row flex items-center gap-4 px-5 py-4">
                        <div class="w-14 h-14 rounded-xl overflow-hidden bg-[#f7f6f3] border border-[#f0ede8] flex-shrink-0">
                            <img src="{{ $item['image'] ?? 'https://picsum.photos/seed/'.$loop->index.'/100/100' }}"
                                 class="w-full h-full object-cover" alt="{{ $item['name'] }}">
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-[#1a1917] line-clamp-1 leading-snug">{{ $item['name'] }}</p>
                            @if(!empty($item['variant_name']))
                            <p class="text-xs font-medium mt-0.5" style="color:var(--brand-color)">{{ $item['variant_name'] }}</p>
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

            {{-- STEP 2: Shipping ─────────────────────────────────────── --}}
            <div class="u3 bg-white rounded-2xl border border-[#ece9e4] overflow-hidden">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-[#f0ede8]">
                    <div class="step-n">٢</div>
                    <span class="font-semibold text-[#1a1917] text-sm">بيانات الشحن والتوصيل</span>
                </div>
                <div class="p-5 space-y-4">

                    {{-- Full name --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-[#6b6966] mb-2 uppercase tracking-wide">
                                الاسم الكامل <span class="text-red-400 normal-case">*</span>
                            </label>
                            <input type="text" name="shipping_name"
                                   value="{{ old('shipping_name', $user->name ?? '') }}"
                                   required placeholder="محمد أحمد"
                                   class="field @error('shipping_name') has-error @enderror">
                            @error('shipping_name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-[#6b6966] mb-2 uppercase tracking-wide">
                                رقم الهاتف <span class="text-red-400 normal-case">*</span>
                            </label>
                            <input type="tel" name="shipping_phone"
                                   value="{{ old('shipping_phone', $user->phone ?? '') }}"
                                   required placeholder="07XXXXXXXX" dir="ltr"
                                   class="field @error('shipping_phone') has-error @enderror">
                            @error('shipping_phone')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-[#6b6966] mb-2 uppercase tracking-wide">
                                العنوان التفصيلي <span class="text-red-400 normal-case">*</span>
                            </label>
                            <input type="text" name="shipping_address"
                                   value="{{ old('shipping_address') }}"
                                   required placeholder="الشارع، الحي، رقم البناء..."
                                   class="field @error('shipping_address') has-error @enderror">
                            @error('shipping_address')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-[#6b6966] mb-2 uppercase tracking-wide">
                                المدينة <span class="text-red-400 normal-case">*</span>
                            </label>
                            <input type="text" name="shipping_city"
                                   value="{{ old('shipping_city') }}"
                                   required placeholder="عمّان"
                                   class="field @error('shipping_city') has-error @enderror">
                            @error('shipping_city')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-[#6b6966] mb-2 uppercase tracking-wide">
                                الرمز البريدي
                                <span class="text-[#b5b2ab] font-normal normal-case">اختياري</span>
                            </label>
                            <input type="text" name="shipping_zip"
                                   value="{{ old('shipping_zip') }}"
                                   placeholder="11118" dir="ltr" class="field">
                        </div>
                    </div>

                    {{-- ── Country + Zone selectors ──────────────────── --}}
                    <div class="border-t border-[#f0ede8] pt-4">
                        <p class="text-xs font-bold text-[#6b6966] mb-3 uppercase tracking-wide flex items-center gap-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            منطقة التوصيل <span class="text-red-400 normal-case">*</span>
                        </p>

                        {{-- Country --}}
                        <div class="mb-3">
                            <label class="block text-xs font-semibold text-[#9a9793] mb-1.5">الدولة</label>
                            <select name="country_id" id="country-select"
                                    class="field @error('country_id') has-error @enderror"
                                    onchange="Shipping.loadZones(this.value)">
                                <option value="">اختر الدولة...</option>
                                @foreach($countries as $country)
                                <option value="{{ $country->id }}"
                                        {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                    {{ $country->name }}
                                    @if($country->name_en) ({{ $country->name_en }}) @endif
                                </option>
                                @endforeach
                            </select>
                            @error('country_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>

                        {{-- Zone (populated via AJAX) --}}
                        <div id="zone-wrapper" class="{{ old('country_id') ? '' : 'hidden' }}">
                            <label class="block text-xs font-semibold text-[#9a9793] mb-1.5">المنطقة / المدينة</label>

                            {{-- Loading spinner --}}
                            <div id="zones-loading"
                                 class="zones-loading items-center gap-2 text-sm text-[#9a9793] py-3">
                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                                </svg>
                                جاري تحميل المناطق...
                            </div>

                            {{-- Zone cards --}}
                            <div id="zones-container" class="space-y-2"></div>

                            {{-- Hidden input that holds the selected zone_id --}}
                            <input type="hidden" name="zone_id" id="zone-id-input"
                                   value="{{ old('zone_id') }}">

                            @error('zone_id')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    {{-- Notes --}}
                    <div>
                        <label class="block text-xs font-bold text-[#6b6966] mb-2 uppercase tracking-wide">
                            ملاحظات
                            <span class="text-[#b5b2ab] font-normal normal-case">اختياري</span>
                        </label>
                        <textarea name="notes" rows="2"
                                  class="field resize-none"
                                  placeholder="تعليمات خاصة للتوصيل...">{{ old('notes') }}</textarea>
                    </div>

                </div>
            </div>

            {{-- STEP 3: Payment --}}
            <div class="u4 bg-white rounded-2xl border border-[#ece9e4] overflow-hidden">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-[#f0ede8]">
                    <div class="step-n">٣</div>
                    <span class="font-semibold text-[#1a1917] text-sm">طريقة الدفع</span>
                </div>
                <div class="p-5">
                    <label class="flex items-center gap-4 p-4 border-2 border-[#1a1917] rounded-xl cursor-pointer bg-[#faf9f7]">
                        <input type="radio" name="payment_method" value="cod" checked
                               class="w-4 h-4 accent-[#1a1917] flex-shrink-0">
                        <div class="w-10 h-10 bg-white rounded-xl border border-[#ece9e4] flex items-center justify-center flex-shrink-0 shadow-sm">
                            <svg class="w-5 h-5 text-[#6b6966]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                      d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-[#1a1917]">الدفع عند الاستلام</p>
                            <p class="text-xs text-[#9a9793] mt-0.5">ادفع نقداً عند وصول طلبك</p>
                        </div>
                        <span class="mr-auto text-[10px] font-bold bg-[#1a1917] text-white px-2 py-1 rounded-lg">متاح</span>
                    </label>
                </div>
            </div>

        </div>

        {{-- ════ RIGHT: Sticky summary ════════════════════════════════ --}}
        <div class="lg:col-span-2">
            <div class="u5 bg-white rounded-2xl border border-[#ece9e4] overflow-hidden sticky top-20">

                <div class="px-5 py-4 border-b border-[#f0ede8] flex items-center justify-between">
                    <h2 class="font-semibold text-[#1a1917] text-sm">ملخص الطلب</h2>
                    <span class="text-[10px] text-[#b5b2ab] bg-[#f7f6f3] px-2 py-1 rounded-lg font-bold">
                        {{ $sym }} {{ $cur->code }}
                    </span>
                </div>

                <div class="p-5">

                    {{-- Items mini-list --}}
                    <div class="space-y-3 mb-5">
                        @foreach($summary['items'] as $item)
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-lg overflow-hidden bg-[#f7f6f3] border border-[#f0ede8] flex-shrink-0">
                                <img src="{{ $item['image'] ?? '' }}" class="w-full h-full object-cover" alt="">
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-[#1a1917] line-clamp-1">{{ $item['name'] }}</p>
                                @if(!empty($item['variant_name']))
                                <p class="text-[10px] text-[#9a9793]">{{ $item['variant_name'] }}</p>
                                @endif
                                <p class="text-[10px] text-[#b5b2ab]">× {{ $item['quantity'] }}</p>
                            </div>
                            <p class="text-xs font-bold text-[#1a1917] flex-shrink-0 tabular-nums">
                                {{ $cv($item['subtotal']) }} {{ $sym }}
                            </p>
                        </div>
                        @endforeach
                    </div>

                    {{-- Totals --}}
                    <div class="space-y-2.5 text-xs border-t border-[#f0ede8] pt-4 mb-4">
                        <div class="flex justify-between text-[#9a9793]">
                            <span>المجموع الفرعي</span>
                            <span class="font-semibold text-[#1a1917] tabular-nums">
                                {{ $cv($summary['subtotal']) }} {{ $sym }}
                            </span>
                        </div>

                        {{-- Delivery fee — updates dynamically when zone selected --}}
                        <div class="flex justify-between text-[#9a9793]">
                            <span class="flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                رسوم التوصيل
                                <span id="summary-zone-name" class="text-[#b5b2ab]"></span>
                            </span>
                            <span id="summary-delivery" class="font-semibold tabular-nums text-[#b5b2ab]">
                                اختر المنطقة
                            </span>
                        </div>
                    </div>

                    {{-- Grand total --}}
                    <div class="flex justify-between items-center border-t border-[#f0ede8] pt-3 mb-5">
                        <span class="text-sm font-bold text-[#1a1917]">الإجمالي</span>
                        <span id="summary-total" class="text-xl font-bold text-[#1a1917] tabular-nums">
                            {{ $cv($summary['subtotal']) }} {{ $sym }}
                        </span>
                    </div>

                    {{-- Delivery info badge (shown when zone selected) --}}
                    <div id="delivery-info-badge"
                         class="hidden mb-4 flex items-center gap-2 bg-blue-50 border border-blue-100 rounded-xl px-3.5 py-3">
                        <svg class="w-3.5 h-3.5 text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-xs text-blue-700 font-medium" id="delivery-days-text"></p>
                    </div>

                    <button type="submit" id="place-btn"
                            class="w-full py-4 rounded-xl bg-[#1a1917] hover:bg-[#2d2c2a] text-white
                                   font-bold text-sm tracking-wide transition-colors
                                   flex items-center justify-center gap-2
                                   shadow-lg shadow-black/15 active:scale-[.98]
                                   disabled:opacity-40 disabled:cursor-not-allowed"
                            disabled id="place-btn">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                  d="M5 13l4 4L19 7"/>
                        </svg>
                        تأكيد الطلب
                    </button>

                    <p class="text-center text-[10px] text-[#b5b2ab] mt-3">
                        اختر منطقة التوصيل لتفعيل الزر
                    </p>

                    <div class="flex items-center justify-center gap-1.5 mt-3 text-[10px] text-[#b5b2ab]">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        دفع آمن ومشفر
                    </div>

                    <div class="mt-4 flex items-center gap-2 bg-amber-50 border border-amber-100 rounded-xl px-3.5 py-3">
                        <svg class="w-3.5 h-3.5 text-amber-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-xs text-amber-700 font-medium leading-snug">
                            ستدفع نقداً عند استلام طلبك
                        </p>
                    </div>

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
/* ═══════════════════════════════════════════════════════════════════════════
   Shipping.loadZones()
   ─ Fetches zones for the selected country via JSON API
   ─ Renders zone option cards
   ─ Updates summary panel live when a zone card is clicked
═══════════════════════════════════════════════════════════════════════════ */
const Shipping = {
    // Format JOD amount into the active display currency
    fmt(jod) {
        const val = Math.round((jod || 0) * window.CHECKOUT.rate * 100) / 100;
        return new Intl.NumberFormat('en-US', {
            minimumFractionDigits: 2, maximumFractionDigits: 2,
        }).format(val) + ' ' + window.CHECKOUT.symbol;
    },

    async loadZones(countryId) {
        const wrapper    = document.getElementById('zone-wrapper');
        const container  = document.getElementById('zones-container');
        const spinner    = document.getElementById('zones-loading');
        const zoneInput  = document.getElementById('zone-id-input');

        // Reset zone selection
        zoneInput.value = '';
        this.updateSummary(null, null, null);

        if (!countryId) {
            wrapper.classList.add('hidden');
            return;
        }

        wrapper.classList.remove('hidden');
        spinner.classList.add('active');
        container.innerHTML = '';

        try {
            const res   = await fetch(`${window.CHECKOUT.zonesApiBase}/${countryId}`, {
                headers: { 'Accept': 'application/json' },
            });
       const data = await res.json();
const zones = data.zones;

            spinner.classList.remove('active');

            if (!zones.length) {
                container.innerHTML = `
                    <p class="text-sm text-[#9a9793] text-center py-3">
                        لا توجد مناطق توصيل متاحة لهذه الدولة حالياً.
                    </p>`;
                return;
            }

            // Render zone option cards
            zones.forEach(zone => {
                const card = document.createElement('label');
                card.className = 'zone-option';
                card.innerHTML = `
                    <input type="radio" name="_zone_radio" value="${zone.id}"
                           class="sr-only zone-radio"
                           data-id="${zone.id}"
                           data-price="${zone.shipping_price}"
                           data-days="${zone.delivery_days ?? ''}"
                           data-name="${zone.name}"
                           onchange="Shipping.selectZone(this)">
                    <div class="flex items-center gap-2.5 flex-1">
                        <div class="w-7 h-7 rounded-full border-2 border-[#e5e3df] flex-shrink-0 flex items-center justify-center
                                    transition-all zone-radio-ring">
                            <div class="w-3 h-3 rounded-full bg-transparent zone-radio-dot transition-all"></div>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-[#1a1917]">${zone.name}</p>
                            ${zone.delivery_days
                                ? `<p class="text-[10px] text-[#9a9793] mt-0.5">التوصيل خلال ${zone.delivery_days} أيام عمل</p>`
                                : ''}
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <span class="text-sm font-black text-[#1a1917] tabular-nums zone-price-display">
                            ${this.fmt(zone.shipping_price)}
                        </span>
                        ${parseFloat(zone.shipping_price) === 0
                            ? '<span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 border border-emerald-100 px-2 py-0.5 rounded-full">مجاني</span>'
                            : ''}
                    </div>`;

                container.appendChild(card);
            });

            // Auto-restore old() selection after validation failure
            const oldZoneId = '{{ old("zone_id") }}';
            if (oldZoneId) {
                const radio = container.querySelector(`input[data-id="${oldZoneId}"]`);
                if (radio) { radio.checked = true; this.selectZone(radio); }
            }

        } catch (e) {
            spinner.classList.remove('active');
            container.innerHTML = `
                <p class="text-sm text-red-500 text-center py-3">
                    تعذّر تحميل المناطق. يرجى المحاولة مجدداً.
                </p>`;
        }
    },

    selectZone(radio) {
        const zoneId    = radio.dataset.id;
        const priceJod  = parseFloat(radio.dataset.price);
        const days      = radio.dataset.days;
        const name      = radio.dataset.name;

        // Set hidden input
        document.getElementById('zone-id-input').value = zoneId;

        // Style selected ring
        document.querySelectorAll('.zone-radio-ring').forEach(r => {
            r.style.borderColor = '#e5e3df';
        });
        document.querySelectorAll('.zone-radio-dot').forEach(d => {
            d.style.background = 'transparent';
        });
        const ring = radio.closest('.zone-option').querySelector('.zone-radio-ring');
        const dot  = radio.closest('.zone-option').querySelector('.zone-radio-dot');
        ring.style.borderColor = 'var(--brand-color, #0ea5e9)';
        dot.style.background   = 'var(--brand-color, #0ea5e9)';

        this.updateSummary(priceJod, days, name);
    },

    updateSummary(priceJod, days, zoneName) {
        const deliveryEl  = document.getElementById('summary-delivery');
        const totalEl     = document.getElementById('summary-total');
        const zoneNameEl  = document.getElementById('summary-zone-name');
        const badge       = document.getElementById('delivery-info-badge');
        const daysText    = document.getElementById('delivery-days-text');
        const placeBtn    = document.getElementById('place-btn');
        const subtip      = placeBtn?.nextElementSibling;

        if (priceJod === null) {
            deliveryEl.textContent  = 'اختر المنطقة';
            deliveryEl.className    = 'font-semibold tabular-nums text-[#b5b2ab]';
            totalEl.textContent     = this.fmt(window.CHECKOUT.subtotalJod);
            zoneNameEl.textContent  = '';
            if (badge) badge.classList.add('hidden');
            if (placeBtn) { placeBtn.disabled = true; }
            if (subtip) subtip.textContent = 'اختر منطقة التوصيل لتفعيل الزر';
            return;
        }

        const total = window.CHECKOUT.subtotalJod + priceJod;

        deliveryEl.textContent = priceJod === 0 ? 'مجاني 🎉' : this.fmt(priceJod);
        deliveryEl.className   = 'font-semibold tabular-nums ' +
            (priceJod === 0 ? 'text-emerald-600' : 'text-[#1a1917]');
        totalEl.textContent    = this.fmt(total);
        zoneNameEl.textContent = zoneName ? `— ${zoneName}` : '';

        if (badge && days) {
            badge.classList.remove('hidden');
            daysText.textContent = `التوصيل إلى ${zoneName} خلال ${days} أيام عمل`;
        } else if (badge) {
            badge.classList.add('hidden');
        }

        if (placeBtn) { placeBtn.disabled = false; }
        if (subtip) subtip.textContent = '';
    },
};

// On page load: if old() has a country_id (validation failure), reload zones
document.addEventListener('DOMContentLoaded', () => {
    const oldCountry = document.getElementById('country-select').value;
    if (oldCountry) Shipping.loadZones(oldCountry);
});

// Prevent double-submit
document.getElementById('checkout-form')?.addEventListener('submit', function () {
    const btn = document.getElementById('place-btn');
    if (btn.disabled) { return false; }
    btn.disabled = true;
    btn.innerHTML = `
        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
        </svg>
        جارٍ تأكيد الطلب...`;
});
</script>
@endpush