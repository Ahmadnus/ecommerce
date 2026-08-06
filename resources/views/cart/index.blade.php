{{--
    resources/views/cart/index.blade.php
    ─────────────────────────────────────────────────────────────────────────────
    Delivery fee row and free-delivery progress bar have been REMOVED.
    The summary sidebar now shows only:
        • المجموع الفرعي  (subtotal)
        • الإجمالي        (total  — same value, no delivery fee on this page)

    The backend CartService still computes delivery_fee internally; it is NOT
    displayed here. The fee will appear at checkout once the user selects a
    shipping zone.

    All amounts in JOD, converted client-side via CURRENCY_RATE.
    $activeCurrency shared by ResolveCurrency middleware.
─────────────────────────────────────────────────────────────────────────────
--}}
@extends('layouts.app')
@section('title', 'سلة التسوق')

@push('head')
<style>
/* ─── Shimmer skeleton ─────────────────────────────────────────────────── */
@keyframes shimmer {
    0%   { background-position: -600px 0; }
    100% { background-position:  600px 0; }
}
.sk {
    background: linear-gradient(90deg, #f1f0ee 25%, #e8e7e4 50%, #f1f0ee 75%);
    background-size: 1200px 100%;
    animation: shimmer 1.6s ease-in-out infinite;
    border-radius: 6px;
}

/* ─── Entrance animations ──────────────────────────────────────────────── */
@keyframes up {
    from { opacity: 0; transform: translateY(14px); }
    to   { opacity: 1; transform: translateY(0); }
}
.u1 { animation: up .35s ease .05s both; }
.u2 { animation: up .35s ease .12s both; }
.u3 { animation: up .35s ease .19s both; }

/* ─── Cart item rows ───────────────────────────────────────────────────── */
.cart-item-row { transition: background .15s; }
.cart-item-row:hover { background: #faf9f7; }
.cart-item-row.removing {
    transition: opacity .2s, transform .2s;
    opacity: 0; transform: translateX(16px);
}

/* ─── Qty stepper buttons ──────────────────────────────────────────────── */
.qty-b {
    width: 28px; height: 28px;
    border: 1.5px solid #e5e3df; border-radius: 8px;
    background: #fff; display: flex; align-items: center; justify-content: center;
    cursor: pointer; font-size: 15px; color: #6b6966;
    transition: all .15s; flex-shrink: 0; line-height: 1;
}
.qty-b:hover { border-color: #1a1917; color: #1a1917; background: #f5f4f2; }

/* ─── Remove button ────────────────────────────────────────────────────── */
.rm-btn {
    width: 30px; height: 30px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    color: #c5c2bc; transition: all .15s; cursor: pointer; flex-shrink: 0;
}
.rm-btn:hover { background: #fee2e2; color: #ef4444; }
</style>
@endpush
@section('content')

@php
    $isRtl = app()->getLocale() === 'ar';

    $cur  = $activeCurrency;
    $rate = (float) $cur->exchange_rate;
    $sym  = $cur->symbol;
    $cv   = fn(float $jod): string => number_format(round($jod * $rate, 2), 2);
@endphp

<div class="min-h-screen" dir="{{ $isRtl ? 'rtl' : 'ltr' }}" style="background-color: var(--bg-color);">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">

        {{-- Header --}}
        <div class="u1 flex items-baseline justify-between mb-8 gap-4 flex-wrap">
            <div>
                <h1 class="font-display text-2xl lg:text-3xl font-bold text-[#1a1917] tracking-tight">
                    {{ __('app.cart.heading') }}
                </h1>

                @if(!empty($summary['items']))
                    <p class="text-sm text-[#9a9793] mt-1">
                        {{ array_sum(array_column($summary['items'], 'quantity')) }} {{ __('app.cart.items_count') }}
                    </p>
                @endif
            </div>

            <a href="{{ route('products.index') }}"
               class="text-sm font-medium text-[#9a9793] hover:text-[#1a1917] transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4 {{ $isRtl ? '' : 'rotate-180' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M7 16l-4-4m0 0l4-4m-4 4h18"/>
                </svg>
                {{ __('app.cart.continue_shopping') }}
            </a>
        </div>

        @if(empty($summary['items']))
        <div class="u2 text-center py-24">
            <div class="w-20 h-20 bg-white border border-[#ece9e4] rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-sm">
                <svg class="w-9 h-9 text-[#c5c2bc]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>

            <h2 class="font-display text-xl font-bold text-[#1a1917] mb-2">
                {{ __('app.cart.empty_heading') }}
            </h2>

            <p class="text-[#9a9793] text-sm mb-8 max-w-xs mx-auto leading-relaxed">
                {{ __('app.cart.empty_sub') }}
            </p>

            <a href="{{ route('products.index') }}"
               class="inline-flex items-center gap-2 text-white font-bold px-8 py-3.5 rounded-xl
                      transition-colors text-sm shadow-lg shadow-black/15 hover:opacity-90"
               style="background: var(--brand-color, #0ea5e9)">
                {{ __('app.cart.browse_products') }}
            </a>
        </div>

        @else
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 lg:gap-8 items-start">

            <div class="lg:col-span-3 u2">

                <div class="hidden sm:block bg-white rounded-2xl border border-[#ece9e4] overflow-hidden">
                    <div class="grid grid-cols-[2fr_1fr_1fr_36px] gap-4 px-5 py-3 border-b border-[#f0ede8]
                                 text-[10px] font-bold text-[#9a9793] uppercase tracking-widest">
                        <span>{{ __('app.cart.col_product') }}</span>
                        <span class="text-center">{{ __('app.cart.col_quantity') }}</span>
                        <span>{{ __('app.cart.col_total') }}</span>
                        <span></span>
                    </div>

                    <div id="cart-items-desktop" class="divide-y divide-[#f7f6f3]">
                        @foreach($summary['items'] as $itemKey => $item)
                        <div class="cart-item-row grid grid-cols-[2fr_1fr_1fr_36px] gap-4 items-center px-5 py-4"
                             id="item-{{ $itemKey }}">

                            <div class="flex items-center gap-3 min-w-0">
                                <div class="relative w-14 h-14 rounded-xl overflow-hidden bg-[#f7f6f3]
                                            border border-[#f0ede8] flex-shrink-0">
                                    <div class="sk absolute inset-0" id="sk-{{ $loop->index }}"></div>
                                    <img src="{{ $item['image'] ?? 'https://picsum.photos/seed/'.$loop->index.'/100/100' }}"
                                         alt="{{ $item['name'] }}"
                                         class="w-full h-full object-cover relative z-10"
                                         onload="this.previousElementSibling.style.display='none'">
                                </div>

                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-[#1a1917] line-clamp-1 leading-snug">
                                        {{ $item['name'] }}
                                    </p>

                                    @if(!empty($item['variant_name']))
                                    <p class="text-xs font-medium mt-0.5" style="color:var(--brand-color)">
                                        {{ $item['variant_name'] }}
                                    </p>
                                    @endif

                                    <p class="text-xs text-[#b5b2ab] mt-0.5 unit-price-label tabular-nums"
                                       data-unit-jod="{{ $item['price'] }}">
                                        {{ $cv($item['price']) }} {{ $sym }} {{ __('app.cart.per_piece') }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex justify-center">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="qty-b"
                                            onclick="CartPage.updateQty('{{ $itemKey }}', -1)">−</button>
                                    <span class="qty-display w-8 text-center text-sm font-bold text-[#1a1917] tabular-nums">
                                        {{ $item['quantity'] }}
                                    </span>
                                    <button type="button" class="qty-b"
                                            onclick="CartPage.updateQty('{{ $itemKey }}', 1)">+</button>
                                </div>
                            </div>

                            <p class="item-subtotal text-sm font-bold text-[#1a1917] tabular-nums">
                                {{ $cv($item['subtotal']) }} {{ $sym }}
                            </p>

                            <button type="button" class="rm-btn"
                                    onclick="CartPage.remove('{{ $itemKey }}')"
                                    title="{{ __('app.cart.remove_title') }}">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>

                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="sm:hidden space-y-3">
                    @foreach($summary['items'] as $itemKey => $item)
                    <div class="cart-item-row bg-white rounded-2xl border border-[#ece9e4] p-4"
                         id="item-mob-{{ $itemKey }}">
                        <div class="flex gap-3">
                            <div class="w-[72px] h-[72px] rounded-xl overflow-hidden bg-[#f7f6f3]
                                        border border-[#f0ede8] flex-shrink-0">
                                <img src="{{ $item['image'] ?? 'https://picsum.photos/seed/'.$loop->index.'/120/120' }}"
                                     alt="{{ $item['name'] }}" class="w-full h-full object-cover">
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2">
                                    <p class="text-sm font-semibold text-[#1a1917] line-clamp-2 flex-1">
                                        {{ $item['name'] }}
                                    </p>
                                    <button type="button" class="rm-btn flex-shrink-0"
                                            onclick="CartPage.remove('{{ $itemKey }}')"
                                            title="{{ __('app.cart.remove_title') }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>

                                @if(!empty($item['variant_name']))
                                <p class="text-xs font-medium mt-0.5" style="color:var(--brand-color)">
                                    {{ $item['variant_name'] }}
                                </p>
                                @endif

                                <div class="flex items-center justify-between mt-3">
                                    <div class="flex items-center gap-1.5">
                                        <button type="button" class="qty-b"
                                                onclick="CartPage.updateQty('{{ $itemKey }}', -1)">−</button>
                                        <span class="qty-display w-7 text-center text-sm font-bold text-[#1a1917] tabular-nums">
                                            {{ $item['quantity'] }}
                                        </span>
                                        <button type="button" class="qty-b"
                                                onclick="CartPage.updateQty('{{ $itemKey }}', 1)">+</button>
                                    </div>
                                    <p class="item-subtotal text-sm font-bold text-[#1a1917] tabular-nums">
                                        {{ $cv($item['subtotal']) }} {{ $sym }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

            </div>

            <div class="lg:col-span-2 u3">
                <div class="bg-white rounded-2xl border border-[#ece9e4] overflow-hidden sticky top-20">

                    <div class="px-5 py-4 border-b border-[#f0ede8] flex items-center justify-between">
                        <h2 class="font-semibold text-[#1a1917] text-sm">
                            {{ __('app.cart.order_summary') }}
                        </h2>
                        <span class="text-[10px] font-bold text-[#b5b2ab] bg-[#f7f6f3]
                                     px-2 py-1 rounded-lg tracking-wide">
                            {{ $cur->code }}
                        </span>
                    </div>

                    <div class="px-5 pt-5 space-y-3 pb-4 border-b border-[#f0ede8]">
                        @foreach($summary['items'] as $item)
                        <div class="flex items-start gap-2.5">
                            <div class="w-9 h-9 rounded-lg overflow-hidden bg-[#f7f6f3]
                                        border border-[#f0ede8] flex-shrink-0">
                                <img src="{{ $item['image'] ?? '' }}"
                                     class="w-full h-full object-cover" alt="">
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-[#1a1917] line-clamp-1">
                                    {{ $item['name'] }}
                                </p>
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

                    <div class="px-5 pt-4 pb-4 border-b border-[#f0ede8] space-y-2.5 text-sm">
                        <div class="flex justify-between text-[#6b6966]">
                            <span>{{ __('app.cart.subtotal') }}</span>
                            <span id="summary-subtotal"
                                  class="font-semibold text-[#1a1917] tabular-nums">
                                {{ $cv($summary['subtotal']) }} {{ $sym }}
                            </span>
                        </div>
                    </div>

                    <div class="px-5 py-4">
                        <div class="flex justify-between items-center mb-1">
                            <span class="font-bold text-[#1a1917]">{{ __('app.cart.grand_total') }}</span>
                            <span id="summary-total"
                                  class="text-2xl font-black text-[#1a1917] tabular-nums">
                                {{ $cv($summary['subtotal']) }} {{ $sym }}
                            </span>
                        </div>

                        <p class="text-[10px] text-[#b5b2ab] mb-5 leading-relaxed">
                            {{ __('app.cart.delivery_note') }}
                        </p>

                        <a href="{{ route('checkout.index') }}"
                           class="flex items-center justify-center gap-2 w-full text-white font-bold
                                  text-sm py-4 rounded-xl transition-colors
                                  shadow-lg shadow-black/15 active:scale-[.98] hover:opacity-90"
                           style="background: var(--brand-color, #0ea5e9)">
                            <svg class="w-4 h-4 {{ $isRtl ? '' : 'rotate-180' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            {{ __('app.cart.checkout_btn') }}
                        </a>

                        <div class="mt-3 flex items-center justify-center gap-1.5 text-[10px] text-[#b5b2ab]">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            {{ __('app.cart.secure_transactions') }}
                        </div>
                    </div>
                </div>
            </div>

        </div>
        @endif

    </div>
</div>
@endsection

@push('scripts')
<script>
/* ─── Currency constants (server-injected for AJAX updates) ─────────────── */
var CURRENCY_RATE   = {{ (float) $cur->exchange_rate }};
var CURRENCY_SYMBOL = '{{ $cur->symbol }}';

/* ─── CartPage helpers ──────────────────────────────────────────────────── */
var CartPage = {

    /* Convert JOD → display currency */
    fmt: function (jod) {
        var val = Math.round((jod || 0) * CURRENCY_RATE * 100) / 100;
        return new Intl.NumberFormat('en-US', {
            minimumFractionDigits: 2, maximumFractionDigits: 2
        }).format(val) + ' ' + CURRENCY_SYMBOL;
    },

    rows: function (key) {
        return [
            document.getElementById('item-' + key),
            document.getElementById('item-mob-' + key)
        ].filter(Boolean);
    },

    updateQty: async function (itemKey, delta) {
        var rows   = this.rows(itemKey);
        if (!rows.length) return;

        var qtyEl  = rows[0].querySelector('.qty-display');
        var unitEl = rows[0].querySelector('.unit-price-label');
        var unitJod = parseFloat(unitEl ? unitEl.dataset.unitJod : 0) || 0;
        var newQty  = parseInt(qtyEl ? qtyEl.textContent : 1) + delta;

        if (newQty <= 0) return this.remove(itemKey);

        try {
            var res  = await fetch('/cart/update', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ item_key: itemKey, quantity: newQty })
            });
            var data = await res.json();

            if (data.success) {
                rows.forEach(function (row) {
                    row.querySelectorAll('.qty-display').forEach(function (el) {
                        el.textContent = newQty;
                    });
                    row.querySelectorAll('.item-subtotal').forEach(function (el) {
                        el.textContent = CartPage.fmt(unitJod * newQty);
                    });
                });
                this.syncSummary(data);
            }
        } catch (e) {
            if (typeof Cart !== 'undefined') Cart.toast('حدث خطأ، حاول مجدداً', 'error');
        }
    },

    remove: async function (itemKey) {
        if (!confirm('إزالة هذا المنتج من السلة؟')) return;

        var rows = this.rows(itemKey);
        rows.forEach(function (r) { r.classList.add('removing'); });

        try {
            var res  = await fetch('/cart/remove/' + itemKey, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            var data = await res.json();

            if (data.success) {
                setTimeout(function () {
                    rows.forEach(function (r) { r.remove(); });
                    CartPage.syncSummary(data);
                    if (data.empty) location.reload();
                }, 220);
            } else {
                rows.forEach(function (r) { r.classList.remove('removing'); });
            }
        } catch (e) {
            rows.forEach(function (r) { r.classList.remove('removing'); });
            if (typeof Cart !== 'undefined') Cart.toast('تعذّر الحذف، حاول مجدداً', 'error');
        }
    },

    /*
     * syncSummary()
     * Updates the summary sidebar after any AJAX cart operation.
     * Only touches #summary-subtotal and #summary-total.
     * Delivery fee is NOT displayed on this page — no element to update.
     */
    syncSummary: function (data) {
        var subEl = document.getElementById('summary-subtotal');
        var totEl = document.getElementById('summary-total');

        if (subEl) subEl.textContent = this.fmt(data.subtotal);
        if (totEl) totEl.textContent = this.fmt(data.subtotal); // total = subtotal here
    }
};
</script>
@endpush