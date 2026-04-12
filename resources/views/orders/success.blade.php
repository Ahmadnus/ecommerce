{{-- resources/views/orders/success.blade.php --}}

@extends('layouts.app')
@section('title', 'تم تأكيد طلبك ✓')

@push('head')
<style>
    /* ── Page background ──────────────────────────────────────────── */
    .success-bg { background: #f7f6f3; min-height: 100vh; }

    /* ── Check animation ──────────────────────────────────────────── */
    @keyframes circleIn {
        0%   { transform: scale(0) rotate(-30deg); opacity: 0; }
        60%  { transform: scale(1.12) rotate(3deg); }
        100% { transform: scale(1) rotate(0deg); opacity: 1; }
    }
    @keyframes checkDraw {
        from { stroke-dashoffset: 60; }
        to   { stroke-dashoffset: 0; }
    }
    .check-circle {
        animation: circleIn .55s cubic-bezier(.16,1,.3,1) .15s both;
    }
    .check-path {
        stroke-dasharray: 60;
        stroke-dashoffset: 60;
        animation: checkDraw .4s ease .65s forwards;
    }

    /* ── Staggered fade-up ────────────────────────────────────────── */
    @keyframes up {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .u1 { animation: up .4s ease .3s  both; }
    .u2 { animation: up .4s ease .45s both; }
    .u3 { animation: up .4s ease .6s  both; }
    .u4 { animation: up .4s ease .75s both; }
    .u5 { animation: up .4s ease .9s  both; }

    /* ── Order number ─────────────────────────────────────────────── */
    .order-num {
        font-family: ui-monospace, 'Cascadia Code', 'SF Mono', monospace;
        letter-spacing: .08em;
    }

    /* ── Status badge ─────────────────────────────────────────────── */
    .badge-pending    { background: #fef9c3; color: #713f12; border: 1px solid #fde68a; }
    .badge-processing { background: #dbeafe; color: #1e3a8a; border: 1px solid #bfdbfe; }
    .badge-shipped    { background: #ede9fe; color: #4c1d95; border: 1px solid #ddd6fe; }
    .badge-delivered  { background: #dcfce7; color: #14532d; border: 1px solid #bbf7d0; }
    .badge-cancelled  { background: #fee2e2; color: #7f1d1d; border: 1px solid #fecaca; }

    /* ── Timeline / steps ─────────────────────────────────────────── */
    .timeline-step { display: flex; align-items: center; gap: 10px; }
    .timeline-dot-done {
        width: 24px; height: 24px; border-radius: 50%;
        background: #1a1917; color: #fff;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; font-size: 10px; font-weight: 700;
    }
    .timeline-dot-next {
        width: 24px; height: 24px; border-radius: 50%;
        background: #f1f0ee; border: 1.5px solid #e5e3df; color: #b5b2ab;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; font-size: 10px; font-weight: 700;
    }
    .timeline-line {
        width: 1.5px; height: 20px; background: #e5e3df; margin-right: 11.25px;
    }
</style>
@endpush

@section('content')
<div class="success-bg" dir="rtl">
<div class="max-w-2xl mx-auto px-4 py-12 lg:py-16">

    {{-- ── Success mark ─────────────────────────────────────────────────── --}}
    <div class="text-center mb-8">
        <div class="check-circle inline-flex w-20 h-20 rounded-full bg-[#1a1917] items-center justify-center mb-5">
            <svg class="w-9 h-9" fill="none" stroke="white" viewBox="0 0 24 24">
                <path class="check-path"
                      stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                      d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h1 class="u1 font-display text-2xl lg:text-3xl font-bold text-[#1a1917] tracking-tight mb-2">
            تم تأكيد طلبك!
        </h1>
        <p class="u2 text-[#9a9793] text-sm max-w-xs mx-auto leading-relaxed">
            شكراً لك. سيصلك طلبك قريباً — سنتواصل معك لتأكيد موعد التوصيل.
        </p>
    </div>

    {{-- ── Main card ─────────────────────────────────────────────────────── --}}
    <div class="u3 bg-white rounded-2xl border border-[#ece9e4] overflow-hidden shadow-sm">

        {{-- Order number + status --}}
        <div class="px-6 py-5 border-b border-[#f0ede8] flex items-center justify-between gap-4 flex-wrap">
            <div>
                <p class="text-[10px] font-bold text-[#b5b2ab] uppercase tracking-widest mb-1">رقم الطلب</p>
                <p class="order-num text-xl font-bold text-[#1a1917]">
                    {{ $order->order_number }}
                </p>
            </div>
            <span class="badge-{{ $order->status }} text-xs font-bold px-3 py-1.5 rounded-xl">
                {{ $order->status_label }}
            </span>
        </div>

        {{-- Shipping info --}}
        <div class="px-6 py-5 border-b border-[#f0ede8]">
            <p class="text-[10px] font-bold text-[#b5b2ab] uppercase tracking-widest mb-3">
                بيانات التوصيل
            </p>
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-[#f7f6f3] border border-[#f0ede8] flex items-center justify-center flex-shrink-0 mt-0.5">
                    <svg class="w-4 h-4 text-[#9a9793]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-[#1a1917]">{{ $order->shipping_name }}</p>
                    <p class="text-xs text-[#9a9793] mt-0.5" dir="ltr">{{ $order->shipping_phone }}</p>
                    <p class="text-xs text-[#9a9793] mt-0.5">
                        {{ $order->shipping_address }}،
                        {{ $order->shipping_city }}
                        @if($order->shipping_zip) {{ $order->shipping_zip }} @endif
                    </p>
                </div>
            </div>
        </div>

        {{-- Order items --}}
        <div class="px-6 py-5 border-b border-[#f0ede8]">
            <p class="text-[10px] font-bold text-[#b5b2ab] uppercase tracking-widest mb-3">
                المنتجات ({{ $order->items->count() }})
            </p>
            <div class="space-y-3">
                @foreach($order->items as $item)
                <div class="flex items-center justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-[#1a1917] line-clamp-1">
                            {{ $item->product_name }}
                        </p>
                        @if($item->variant_name)
                        <p class="text-xs font-medium mt-0.5" style="color: var(--brand-color, #0ea5e9)">
                            {{ $item->variant_name }}
                        </p>
                        @endif
                        <p class="text-xs text-[#b5b2ab] mt-0.5 tabular-nums">
                            {{ $item->quantity }} × ${{ number_format($item->unit_price, 2) }}
                        </p>
                    </div>
                    <p class="text-sm font-bold text-[#1a1917] flex-shrink-0 tabular-nums">
                        ${{ number_format($item->total_price, 2) }}
                    </p>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Price breakdown --}}
        <div class="px-6 py-5 border-b border-[#f0ede8] space-y-2.5 text-sm">
            <div class="flex justify-between text-[#6b6966]">
                <span>المجموع الفرعي</span>
                <span class="tabular-nums">${{ number_format($order->subtotal, 2) }}</span>
            </div>
            <div class="flex justify-between text-[#6b6966]">
                <span>الضريبة</span>
                <span class="tabular-nums">${{ number_format($order->tax_amount, 2) }}</span>
            </div>
            <div class="flex justify-between text-[#6b6966]">
                <span>الشحن</span>
                <span class="{{ $order->shipping_amount == 0 ? 'text-emerald-600 font-semibold' : 'tabular-nums' }}">
                    {{ $order->shipping_amount == 0 ? 'مجاني' : '$' . number_format($order->shipping_amount, 2) }}
                </span>
            </div>
            <div class="flex justify-between font-bold text-[#1a1917] text-base pt-2 border-t border-[#f0ede8]">
                <span>الإجمالي</span>
                <span class="tabular-nums">${{ number_format($order->total_amount, 2) }}</span>
            </div>
        </div>

        {{-- Payment + COD note --}}
        <div class="px-6 py-4 bg-[#faf9f7] flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-white border border-[#ece9e4] flex items-center justify-center flex-shrink-0 shadow-sm">
                <svg class="w-4 h-4 text-[#9a9793]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                          d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-[#1a1917]">الدفع عند الاستلام</p>
                <p class="text-[11px] text-[#9a9793]">ستدفع نقداً عند وصول طلبك</p>
            </div>
        </div>

    </div>

    {{-- ── Order status timeline ─────────────────────────────────────────── --}}
    <div class="u4 bg-white rounded-2xl border border-[#ece9e4] p-6 mt-4">
        <p class="text-[10px] font-bold text-[#b5b2ab] uppercase tracking-widest mb-5">
            مراحل الطلب
        </p>
        @php
            $stages = [
                'pending'    => 'قيد الانتظار',
                'processing' => 'جارٍ التجهيز',
                'shipped'    => 'تم الشحن',
                'delivered'  => 'تم التسليم',
            ];
            $statusOrder = array_keys($stages);
            $currentIdx  = array_search($order->status, $statusOrder) ?? 0;
        @endphp
        <div>
            @foreach($stages as $key => $label)
            @php $idx = array_search($key, $statusOrder); $isDone = $idx <= $currentIdx; @endphp
            <div class="timeline-step">
                @if($isDone)
                <div class="timeline-dot-done">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                @else
                <div class="timeline-dot-next">{{ $idx + 1 }}</div>
                @endif
                <span class="text-sm {{ $isDone ? 'font-semibold text-[#1a1917]' : 'text-[#b5b2ab]' }}">
                    {{ $label }}
                    @if($key === $order->status)
                    <span class="text-[10px] font-bold text-[#9a9793] mr-2 bg-[#f1f0ee] px-1.5 py-0.5 rounded">الحالية</span>
                    @endif
                </span>
            </div>
            @if(!$loop->last)
            <div class="timeline-line"></div>
            @endif
            @endforeach
        </div>
    </div>

    {{-- ── CTA Buttons ───────────────────────────────────────────────────── --}}
    <div class="u5 grid grid-cols-2 gap-3 mt-5">
        <a href="{{ route('products.index') }}"
           class="flex items-center justify-center gap-2 bg-white hover:bg-[#f7f6f3]
                  border border-[#ece9e4] text-[#1a1917] font-semibold py-3.5 rounded-xl
                  text-sm transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
            متابعة التسوق
        </a>
        <a href="{{ route('orders.index') }}"
           class="flex items-center justify-center gap-2 bg-[#1a1917] hover:bg-[#2d2c2a]
                  text-white font-semibold py-3.5 rounded-xl text-sm transition-colors
                  shadow-lg shadow-black/15">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            طلباتي
        </a>
    </div>

</div>
</div>
@endsection