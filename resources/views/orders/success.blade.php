{{--
    resources/views/orders/success.blade.php
    ─────────────────────────────────────────────────────────────────────────────
    Displays after a successful order placement.
    Shows the complete breakdown:
      • Subtotal
      • Shipping fee (from the admin-defined zone)
      • Grand total
    Plus delivery info (area name + estimated days).

    $order is passed from the OrderController::success() method.
    $activeCurrency is shared by ResolveCurrency middleware.
    ─────────────────────────────────────────────────────────────────────────────
--}}
@extends('layouts.app')
@section('title', 'تم تأكيد طلبك')

@push('head')
<style>
@keyframes checkDraw {
    from { stroke-dashoffset: 80; }
    to   { stroke-dashoffset: 0; }
}
@keyframes circlePop {
    0%   { transform: scale(0.6); opacity: 0; }
    60%  { transform: scale(1.1); }
    100% { transform: scale(1);   opacity: 1; }
}
@keyframes up {
    from { opacity: 0; transform: translateY(16px); }
    to   { opacity: 1; transform: translateY(0); }
}
.check-circle { animation: circlePop .5s cubic-bezier(.34,1.56,.64,1) .1s both; }
.check-mark   {
    stroke-dasharray: 80;
    stroke-dashoffset: 80;
    animation: checkDraw .4s ease .55s forwards;
}
.u1 { animation: up .4s ease .2s both; }
.u2 { animation: up .4s ease .35s both; }
.u3 { animation: up .4s ease .45s both; }
.u4 { animation: up .4s ease .55s both; }

/* Status timeline */
.tl-step { display: flex; align-items: flex-start; gap: 14px; }
.tl-icon {
    width: 36px; height: 36px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; font-size: 14px;
}
.tl-line {
    width: 2px; background: #f0ede8; margin: 4px auto 0;
    flex-shrink: 0; align-self: stretch; min-height: 24px;
}
</style>
@endpush

@section('content')

@php
    $cur  = $activeCurrency;
    $rate = (float) $cur->exchange_rate;
    $sym  = $cur->symbol;
    $cv   = fn(float $jod): string => number_format(round($jod * $rate, 2), 2);

    // Pull stored values from the order
    // tax_amount column stores the delivery fee (zone shipping_price in JOD)
    $deliveryFeeJod = (float) ($order->tax_amount ?? 0);
    $subtotalJod    = (float) ($order->subtotal   ?? 0);
    $totalJod       = (float) ($order->total_amount ?? $subtotalJod + $deliveryFeeJod);
    $shippingArea   = $order->shipping_area ?? null;
    $deliveryDays   = $order->delivery_days  ?? null;
    $zone           = $order->zone ?? null;          // if you eager-load the relation
@endphp

<div class="min-h-screen bg-[#f7f6f3]" dir="rtl">
<div class="max-w-2xl mx-auto px-4 sm:px-6 py-12 lg:py-16">

    {{-- ══ Animated check ═════════════════════════════════════════════════ --}}
    <div class="flex flex-col items-center text-center mb-10">
        <div class="check-circle w-20 h-20 bg-emerald-500 rounded-full flex items-center justify-center mb-5 shadow-xl shadow-emerald-500/30">
            <svg class="w-9 h-9" viewBox="0 0 40 40" fill="none">
                <path class="check-mark"
                      d="M10 20 L18 28 L30 13"
                      stroke="white" stroke-width="3.5"
                      stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>

        <h1 class="u1 text-2xl sm:text-3xl font-black text-[#1a1917] tracking-tight mb-2">
            تم تأكيد طلبك! 🎉
        </h1>
        <p class="u2 text-[#9a9793] text-sm max-w-sm leading-relaxed">
            شكراً لك! سنبدأ بتجهيز طلبك فوراً وسنتواصل معك عند الشحن.
        </p>
    </div>

    {{-- ══ Order number badge ═════════════════════════════════════════════ --}}
    <div class="u2 flex items-center justify-center gap-3 mb-8">
        <div class="bg-white border border-[#ece9e4] rounded-2xl px-6 py-3 flex items-center gap-3 shadow-sm">
            <svg class="w-4 h-4 text-[#9a9793] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <div>
                <p class="text-[10px] text-[#9a9793] font-bold uppercase tracking-widest">رقم الطلب</p>
                <p class="text-base font-black text-[#1a1917] tracking-tight">{{ $order->order_number }}</p>
            </div>
        </div>
    </div>

    {{-- ══ Order summary card ══════════════════════════════════════════════ --}}
    <div class="u3 bg-white rounded-2xl border border-[#ece9e4] overflow-hidden shadow-sm mb-6">

        {{-- Header --}}
        <div class="flex items-center gap-3 px-5 py-4 border-b border-[#f0ede8]">
            <div class="w-8 h-8 rounded-xl bg-[#f7f6f3] flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-[#9a9793]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <h2 class="font-semibold text-[#1a1917] text-sm">تفاصيل الفاتورة</h2>
        </div>

        {{-- Items --}}
        @if($order->items->isNotEmpty())
        <div class="divide-y divide-[#f7f6f3]">
            @foreach($order->items as $item)
            <div class="flex items-center gap-3 px-5 py-3.5">
                {{-- Thumbnail if product has media --}}
                @php $img = $item->product?->getFirstMediaUrl('products'); @endphp
                @if($img)
                <div class="w-10 h-10 rounded-xl overflow-hidden bg-[#f7f6f3] border border-[#f0ede8] flex-shrink-0">
                    <img src="{{ $img }}" class="w-full h-full object-cover" alt="">
                </div>
                @endif
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-[#1a1917] line-clamp-1">{{ $item->product_name }}</p>
                    @if($item->productVariant?->attributeValues->isNotEmpty())
                    <p class="text-[10px] text-[#9a9793] mt-0.5">
                        {{ $item->productVariant->attributeValues->pluck('value')->implode(' / ') }}
                    </p>
                    @endif
                    <p class="text-[10px] text-[#b5b2ab] mt-0.5">
                        {{ $item->quantity }} × {{ $cv($item->unit_price) }} {{ $sym }}
                    </p>
                </div>
                <p class="text-sm font-bold text-[#1a1917] flex-shrink-0 tabular-nums">
                    {{ $cv($item->total_price) }} {{ $sym }}
                </p>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Pricing breakdown ─────────────────────────────────────── --}}
        <div class="border-t border-[#f0ede8] px-5 py-4 space-y-2.5 text-xs">

            {{-- Subtotal --}}
            <div class="flex justify-between text-[#9a9793]">
                <span>المجموع الفرعي</span>
                <span class="font-semibold text-[#1a1917] tabular-nums">
                    {{ $cv($subtotalJod) }} {{ $sym }}
                </span>
            </div>

            {{-- Shipping fee + area name --}}
            <div class="flex justify-between text-[#9a9793]">
                <span class="flex items-center gap-1.5 flex-wrap">
                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    رسوم التوصيل
                    @if($shippingArea)
                    <span class="text-[10px] font-medium px-2 py-0.5 bg-[#f7f6f3] rounded-full text-[#9a9793]">
                        {{ $shippingArea }}
                    </span>
                    @endif
                </span>
                <span class="font-semibold tabular-nums {{ $deliveryFeeJod == 0 ? 'text-emerald-600' : 'text-[#1a1917]' }}">
                    @if($deliveryFeeJod == 0)
                        مجاني 🎉
                    @else
                        {{ $cv($deliveryFeeJod) }} {{ $sym }}
                    @endif
                </span>
            </div>

            {{-- Delivery days --}}
            @if($deliveryDays)
            <div class="flex justify-between text-[#9a9793]">
                <span class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    وقت التوصيل المقدر
                </span>
                <span class="font-semibold text-[#1a1917]">{{ $deliveryDays }} أيام عمل</span>
            </div>
            @endif

        </div>

        {{-- Grand total ─────────────────────────────────────────────── --}}
        <div class="border-t border-[#f0ede8] px-5 py-4 flex justify-between items-center">
            <span class="font-bold text-[#1a1917]">الإجمالي الكلي</span>
            <span class="text-2xl font-black text-[#1a1917] tabular-nums">
                {{ $cv($totalJod) }} {{ $sym }}
            </span>
        </div>

    </div>

    {{-- ══ Shipping info card ══════════════════════════════════════════════ --}}
    <div class="u3 bg-white rounded-2xl border border-[#ece9e4] overflow-hidden shadow-sm mb-6">
        <div class="flex items-center gap-3 px-5 py-4 border-b border-[#f0ede8]">
            <div class="w-8 h-8 rounded-xl bg-[#f7f6f3] flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-[#9a9793]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <h2 class="font-semibold text-[#1a1917] text-sm">عنوان التوصيل</h2>
        </div>
        <div class="px-5 py-4 space-y-1.5 text-sm">
            <p class="font-semibold text-[#1a1917]">{{ $order->shipping_name }}</p>
            <p class="text-[#6b6966]">{{ $order->shipping_phone }}</p>
            <p class="text-[#6b6966]">{{ $order->shipping_address }}</p>
            <p class="text-[#6b6966]">{{ $order->shipping_city }}@if($order->shipping_zip)، {{ $order->shipping_zip }}@endif</p>
            @if($shippingArea)
            <p class="text-[10px] font-bold text-[#9a9793] pt-1">
                {{ $shippingArea }}
            </p>
            @endif
        </div>
    </div>

    {{-- ══ Order status timeline ═══════════════════════════════════════════ --}}
    <div class="u4 bg-white rounded-2xl border border-[#ece9e4] overflow-hidden shadow-sm mb-8">
        <div class="px-5 py-4 border-b border-[#f0ede8]">
            <h2 class="font-semibold text-[#1a1917] text-sm">مراحل الطلب</h2>
        </div>
        <div class="px-5 py-5 space-y-0">

            {{-- Confirmed --}}
            <div class="tl-step">
                <div class="flex flex-col items-center">
                    <div class="tl-icon bg-emerald-100 text-emerald-600">✓</div>
                    <div class="tl-line"></div>
                </div>
                <div class="pb-5">
                    <p class="text-sm font-bold text-[#1a1917] leading-snug">تم تأكيد الطلب</p>
                    <p class="text-xs text-[#9a9793] mt-0.5">{{ $order->created_at->format('d/m/Y — H:i') }}</p>
                </div>
            </div>

            {{-- Processing --}}
            <div class="tl-step">
                <div class="flex flex-col items-center">
                    <div class="tl-icon bg-amber-50 text-amber-500">⚙</div>
                    <div class="tl-line"></div>
                </div>
                <div class="pb-5">
                    <p class="text-sm font-bold text-[#1a1917] leading-snug">جاري التجهيز</p>
                    <p class="text-xs text-[#9a9793] mt-0.5">يتم تجهيز طلبك الآن</p>
                </div>
            </div>

            {{-- Shipping --}}
            <div class="tl-step">
                <div class="flex flex-col items-center">
                    <div class="tl-icon bg-[#f7f6f3] text-[#b5b2ab]">🚚</div>
                    <div class="tl-line"></div>
                </div>
                <div class="pb-5">
                    <p class="text-sm font-bold text-[#b5b2ab] leading-snug">بالطريق إليك</p>
                    <p class="text-xs text-[#b5b2ab] mt-0.5">
                        @if($shippingArea) إلى {{ $shippingArea }} @endif
                        @if($deliveryDays) — خلال {{ $deliveryDays }} أيام @endif
                    </p>
                </div>
            </div>

            {{-- Delivered --}}
            <div class="tl-step">
                <div class="flex flex-col items-center">
                    <div class="tl-icon bg-[#f7f6f3] text-[#b5b2ab]">📦</div>
                </div>
                <div>
                    <p class="text-sm font-bold text-[#b5b2ab] leading-snug">تم التسليم</p>
                    <p class="text-xs text-[#b5b2ab] mt-0.5">الدفع نقداً عند الاستلام</p>
                </div>
            </div>

        </div>
    </div>

    {{-- ══ Actions ══════════════════════════════════════════════════════════ --}}
    <div class="u4 flex flex-col sm:flex-row gap-3 justify-center">
        <a href="{{ route('products.index') }}"
           class="inline-flex items-center justify-center gap-2 bg-[#1a1917] hover:bg-[#2d2c2a]
                  text-white font-bold text-sm px-8 py-3.5 rounded-xl transition-colors
                  shadow-lg shadow-black/15 active:scale-[.98]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
            متابعة التسوق
        </a>
        @auth
        <a href="{{ route('orders.index') }}"
           class="inline-flex items-center justify-center gap-2 bg-white hover:bg-[#f7f6f3]
                  text-[#1a1917] font-bold text-sm px-8 py-3.5 rounded-xl transition-colors
                  border border-[#ece9e4] shadow-sm active:scale-[.98]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            طلباتي
        </a>
        @endauth
    </div>

</div>
</div>
@endsection