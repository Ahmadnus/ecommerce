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
/* Entrance animations — kept from the previous design; they stagger the
   header, the item list and the summary card as the page settles. */
@keyframes cart-up {
    from { opacity: 0; transform: translateY(14px); }
    to   { opacity: 1; transform: translateY(0); }
}
.u1 { animation: cart-up .35s ease .05s both; }
.u2 { animation: cart-up .35s ease .12s both; }
.u3 { animation: cart-up .35s ease .19s both; }

@media (prefers-reduced-motion: reduce) {
    .u1, .u2, .u3 { animation: none; }
}

/* Row hover, and the exit transition CartPage.remove() triggers by adding
   .removing before it deletes the node. */
.cart-item-row { transition: border-color .15s ease, box-shadow .15s ease; }
.cart-item-row:hover {
    border-color: color-mix(in srgb, var(--brand-color) 30%, var(--border-color));
}
.cart-item-row.removing {
    transition: opacity .2s, transform .2s;
    opacity: 0;
    transform: translateX(16px);
}
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
    <div class="sf-container" style="max-width:1100px; padding-block: var(--section-gap)">

        {{-- Header --}}
        <div class="u1 flex items-baseline justify-between mb-8 gap-4 flex-wrap">
            <div>
                <h1 class="sf-section__title">{{ __('app.cart.heading') }}</h1>

                @if(!empty($summary['items']))
                    <p class="sf-card__meta mt-2">
                        {{ array_sum(array_column($summary['items'], 'quantity')) }}
                        {{ __('app.cart.items_count') }}
                    </p>
                @endif
            </div>

            <a href="{{ route('products.index') }}" class="sf-section__link flex items-center gap-1.5">
                <svg class="w-4 h-4 {{ $isRtl ? '' : 'rotate-180' }}" fill="none" stroke="currentColor"
                     stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 16l-4-4m0 0l4-4m-4 4h18"/>
                </svg>
                {{ __('app.cart.continue_shopping') }}
            </a>
        </div>

        @if(empty($summary['items']))
            {{-- Empty state --}}
            <div class="u2">
                <x-sf.empty-state :title="__('app.cart.empty_heading')"
                                  :text="__('app.cart.empty_sub')"
                                  :action-url="route('products.index')"
                                  :action-label="__('app.cart.browse_products')">
                    <x-slot:icon>
                        <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </x-slot:icon>
                </x-sf.empty-state>
            </div>

        @else
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 lg:gap-8 items-start">

            {{-- Line items.

                 One responsive list at every breakpoint, replacing the
                 separate desktop-table / mobile-card markup this page used
                 to carry. CartPage.rows() looks up both "item-<key>" and
                 "item-mob-<key>" and filters out misses, so dropping the
                 duplicate mobile markup needs no JS change. The hooks it
                 does rely on are all preserved below: .qty-display,
                 .item-subtotal, .unit-price-label[data-unit-jod], and the
                 .removing class applied on delete. --}}
            <div class="lg:col-span-3 u2 flex flex-col gap-3">
                @foreach($summary['items'] as $itemKey => $item)
                    <div class="cart-item-row sf-cart-row" id="item-{{ $itemKey }}">

                        <div class="sf-cart-row__media">
                            <img src="{{ $item['image'] ?? asset('images/placeholder.jpg') }}"
                                 alt="" loading="lazy" decoding="async">
                        </div>

                        <div class="flex flex-col gap-2 min-w-0">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="sf-card__title" style="min-height:0">{{ $item['name'] }}</p>

                                    @if(!empty($item['variant_name']))
                                        <p class="sf-card__meta" style="color: var(--brand-color)">
                                            {{ $item['variant_name'] }}
                                        </p>
                                    @endif

                                    <p class="sf-card__meta unit-price-label tabular-nums"
                                       data-unit-jod="{{ $item['price'] }}">
                                        {{ $cv($item['price']) }} {{ $sym }}
                                    </p>
                                </div>

                                <button type="button"
                                        class="rm-btn sf-remove-btn"
                                        onclick="CartPage.remove('{{ $itemKey }}')"
                                        aria-label="{{ __('app.remove') }}">
                                    <svg fill="none" stroke="currentColor" stroke-width="1.8"
                                         viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>

                            <div class="flex items-center justify-between gap-3 flex-wrap">
                                <div class="sf-qty">
                                    <button type="button" class="qty-b sf-qty__btn"
                                            onclick="CartPage.updateQty('{{ $itemKey }}', -1)"
                                            aria-label="{{ __('app.decrease_quantity') }}">&minus;</button>
                                    <span class="qty-display sf-qty__input tabular-nums"
                                          style="width:48px; display:flex; align-items:center; justify-content:center">{{ $item['quantity'] }}</span>
                                    <button type="button" class="qty-b sf-qty__btn"
                                            onclick="CartPage.updateQty('{{ $itemKey }}', 1)"
                                            aria-label="{{ __('app.increase_quantity') }}">+</button>
                                </div>

                                <p class="item-subtotal sf-price tabular-nums">
                                    {{ $cv($item['subtotal']) }} {{ $sym }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Order summary --}}
            <div class="lg:col-span-2 u3">
                <div class="sf-panel overflow-hidden sticky" style="top: calc(var(--header-height) + 16px)">

                    <div class="px-5 py-4 flex items-center justify-between"
                         style="border-block-end: var(--card-border-width) solid var(--border-color)">
                        <h2 class="font-semibold" style="color: var(--text-heading)">
                            {{ __('app.cart.order_summary') }}
                        </h2>
                        <span class="sf-badge sf-badge--soft">{{ $cur->code }}</span>
                    </div>

                    <div class="px-5 pt-5 pb-4 flex flex-col gap-3"
                         style="border-block-end: var(--card-border-width) solid var(--border-color)">
                        @foreach($summary['items'] as $item)
                            <div class="flex items-start gap-2.5">
                                <div class="w-9 h-9 overflow-hidden flex-shrink-0"
                                     style="border-radius: var(--radius-button);
                                            background: var(--subtle-bg);
                                            border: var(--card-border-width) solid var(--border-color)">
                                    <img src="{{ $item['image'] ?? asset('images/placeholder.jpg') }}"
                                         class="w-full h-full" style="object-fit: var(--card-image-fit)"
                                         alt="" loading="lazy">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold line-clamp-1"
                                       style="color: var(--text-heading); font-size: var(--card-font-size)">
                                        {{ $item['name'] }}
                                    </p>
                                    @if(!empty($item['variant_name']))
                                        <p class="sf-card__meta">{{ $item['variant_name'] }}</p>
                                    @endif
                                    <p class="sf-card__meta">&times; {{ $item['quantity'] }}</p>
                                </div>
                                <p class="font-bold flex-shrink-0 tabular-nums"
                                   style="color: var(--text-heading); font-size: var(--card-font-size)">
                                    {{ $cv($item['subtotal']) }} {{ $sym }}
                                </p>
                            </div>
                        @endforeach
                    </div>

                    <div class="px-5 py-4"
                         style="border-block-end: var(--card-border-width) solid var(--border-color)">
                        <div class="flex justify-between" style="color: var(--text-body)">
                            <span>{{ __('app.cart.subtotal') }}</span>
                            <span id="summary-subtotal" class="font-semibold tabular-nums"
                                  style="color: var(--text-heading)">
                                {{ $cv($summary['subtotal']) }} {{ $sym }}
                            </span>
                        </div>
                    </div>

                    <div class="px-5 py-4">
                        <div class="flex justify-between items-center mb-1">
                            <span class="font-bold" style="color: var(--text-heading)">
                                {{ __('app.cart.grand_total') }}
                            </span>
                            <span id="summary-total" class="font-black tabular-nums"
                                  style="color: var(--text-heading);
                                         font-size: calc(var(--product-price-font-size) * 1.6)">
                                {{ $cv($summary['subtotal']) }} {{ $sym }}
                            </span>
                        </div>

                        <p class="sf-card__meta mb-5 leading-relaxed">
                            {{ __('app.cart.delivery_note') }}
                        </p>

                        <a href="{{ route('checkout.index') }}"
                           class="sf-btn sf-btn--primary sf-btn--lg sf-btn--block">
                            <svg class="{{ $isRtl ? '' : 'rotate-180' }}" fill="none" stroke="currentColor"
                                 stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            {{ __('app.cart.checkout_btn') }}
                        </a>

                        <div class="mt-3 flex items-center justify-center gap-1.5 sf-card__meta">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2"
                                 viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
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