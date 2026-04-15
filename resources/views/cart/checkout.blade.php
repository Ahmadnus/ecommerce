{{--
    resources/views/cart/checkout.blade.php
    ─────────────────────────────────────────────────────────────────────────────
    All $summary values are in JOD (base currency).
    $activeCurrency is shared by ResolveCurrency middleware (defaults to JOD).

    CHANGED FROM PREVIOUS VERSION:
      - $summary['tax']     → $summary['delivery_fee']
      - "الضريبة (10%)"      → "رسوم التوصيل"
      - Total: subtotal + delivery_fee  (no percentage tax)
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

<div class="min-h-screen bg-[#f7f6f3]" dir="rtl">
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">

    {{-- Page header --}}
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

    {{-- Validation errors --}}
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

    <form action="{{ route('checkout.place') }}" method="POST" id="checkout-form">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 lg:gap-8 items-start">

        {{-- ════ LEFT: Steps ══════════════════════════════════════════ --}}
        <div class="lg:col-span-3 space-y-5">

            {{-- STEP 1: Cart Review --}}
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
                            <p class="text-sm font-semibold text-[#1a1917] line-clamp-1 leading-snug">
                                {{ $item['name'] }}
                            </p>
                            @if(!empty($item['variant_name']))
                            <p class="text-xs font-medium mt-0.5" style="color:var(--brand-color)">
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

            {{-- STEP 2: Shipping Form --}}
            <div class="u3 bg-white rounded-2xl border border-[#ece9e4] overflow-hidden">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-[#f0ede8]">
                    <div class="step-n">٢</div>
                    <span class="font-semibold text-[#1a1917] text-sm">بيانات الشحن</span>
                </div>
                <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-[#6b6966] mb-2 uppercase tracking-wide">
                            الاسم الكامل <span class="text-red-400 normal-case">*</span>
                        </label>
                        <input type="text" name="shipping_name"
                               value="{{ old('shipping_name', $user->name ?? '') }}"
                               required placeholder="محمد أحمد"
                               class="field @error('shipping_name') has-error @enderror">
                        @error('shipping_name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-[#6b6966] mb-2 uppercase tracking-wide">
                            رقم الهاتف <span class="text-red-400 normal-case">*</span>
                        </label>
                        <input type="tel" name="shipping_phone"
                               value="{{ old('shipping_phone', $user->phone ?? '') }}"
                               required placeholder="07XXXXXXXX" dir="ltr"
                               class="field @error('shipping_phone') has-error @enderror">
                        @error('shipping_phone')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-[#6b6966] mb-2 uppercase tracking-wide">
                            العنوان <span class="text-red-400 normal-case">*</span>
                        </label>
                        <input type="text" name="shipping_address"
                               value="{{ old('shipping_address') }}"
                               required placeholder="الشارع، الحي، رقم البناء..."
                               class="field @error('shipping_address') has-error @enderror">
                        @error('shipping_address')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#6b6966] mb-2 uppercase tracking-wide">
                            المدينة <span class="text-red-400 normal-case">*</span>
                        </label>
                        <input type="text" name="shipping_city"
                               value="{{ old('shipping_city') }}"
                               required placeholder="عمّان"
                               class="field @error('shipping_city') has-error @enderror">
                        @error('shipping_city')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
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

                    <div class="sm:col-span-2">
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

        {{-- ════ RIGHT: Sticky Summary ════════════════════════════════ --}}
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

                        {{-- DELIVERY FEE row ── replaces tax row ─────── --}}
                        <div class="flex justify-between text-[#9a9793]">
                            <span class="flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1 12a2 2 0 002 2h8a2 2 0 002-2l1-12m-9 4v8m4-8v8"/>
                                </svg>
                                رسوم التوصيل
                            </span>
                            <span class="font-semibold tabular-nums {{ $summary['delivery_fee'] == 0 ? 'text-emerald-600' : 'text-[#1a1917]' }}">
                                @if($summary['delivery_fee'] == 0)
                                    مجاني 🎉
                                @else
                                    {{ $cv($summary['delivery_fee']) }} {{ $sym }}
                                @endif
                            </span>
                        </div>

                    </div>

                    {{-- Grand total --}}
                    <div class="flex justify-between items-center border-t border-[#f0ede8] pt-3 mb-2">
                        <span class="text-sm font-bold text-[#1a1917]">الإجمالي</span>
                        <span class="text-xl font-bold text-[#1a1917] tabular-nums">
                            {{ $cv($summary['total']) }} {{ $sym }}
                        </span>
                    </div>

                    {{-- Formula hint --}}
                    <p class="text-[10px] text-[#b5b2ab] mb-5">
                        المجموع الفرعي + رسوم التوصيل
                        @if($summary['delivery_fee'] == 0)
                            (توصيل مجاني فوق {{ $cv($freeThreshold) }} {{ $sym }})
                        @endif
                    </p>

                    {{-- CTA --}}
                    <button type="submit" id="place-btn"
                            class="w-full py-4 rounded-xl bg-[#1a1917] hover:bg-[#2d2c2a] text-white
                                   font-bold text-sm tracking-wide transition-colors
                                   flex items-center justify-center gap-2
                                   shadow-lg shadow-black/15 active:scale-[.98]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                  d="M5 13l4 4L19 7"/>
                        </svg>
                        تأكيد الطلب
                    </button>

                    {{-- Secure badge --}}
                    <div class="flex items-center justify-center gap-1.5 mt-4 text-[10px] text-[#b5b2ab]">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        دفع آمن ومشفر — الدينار الأردني (د.أ)
                    </div>

                    {{-- COD reminder --}}
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
document.getElementById('checkout-form')?.addEventListener('submit', function () {
    const btn = document.getElementById('place-btn');
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