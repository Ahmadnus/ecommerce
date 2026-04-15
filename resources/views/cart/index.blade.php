{{--
    resources/views/cart/index.blade.php
    ─────────────────────────────────────────────────────────────────────────────
    All $summary values are in JOD (base currency).
    $activeCurrency is shared by ResolveCurrency middleware (defaults to JOD).

    CHANGED FROM PREVIOUS VERSION:
      - $summary['tax']           → $summary['delivery_fee']
      - "الضريبة (10%)"            → "رسوم التوصيل"
      - data.tax (AJAX)           → data.delivery_fee
      - Total formula: subtotal + delivery_fee  (no tax)
      - Free delivery badge when delivery_fee == 0
    ─────────────────────────────────────────────────────────────────────────────
--}}
@extends('layouts.app')
@section('title', 'سلة التسوق')

@push('head')
<style>
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
    @keyframes up {
        from { opacity: 0; transform: translateY(14px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .u1 { animation: up .35s ease .05s both; }
    .u2 { animation: up .35s ease .12s both; }
    .u3 { animation: up .35s ease .19s both; }

    .cart-item-row { transition: background .15s; }
    .cart-item-row:hover { background: #faf9f7; }

    .qty-b {
        width: 28px; height: 28px;
        border: 1.5px solid #e5e3df; border-radius: 8px;
        background: #fff; display: flex; align-items: center; justify-content: center;
        cursor: pointer; font-size: 15px; color: #6b6966;
        transition: all .15s; flex-shrink: 0; line-height: 1;
    }
    .qty-b:hover { border-color: #1a1917; color: #1a1917; background: #f5f4f2; }

    .rm-btn {
        width: 30px; height: 30px; border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        color: #c5c2bc; transition: all .15s; cursor: pointer; flex-shrink: 0;
    }
    .rm-btn:hover { background: #fee2e2; color: #ef4444; }

    .cart-item-row.removing {
        transition: opacity .2s, transform .2s;
        opacity: 0; transform: translateX(16px);
    }

    /* Free delivery progress bar */
    .delivery-bar-track {
        height: 4px; background: #f0ede8; border-radius: 99px; overflow: hidden;
    }
    .delivery-bar-fill {
        height: 100%; border-radius: 99px;
        background: var(--brand-color, #0ea5e9);
        transition: width .4s ease;
    }
</style>
@endpush

@section('content')

@php
    $cur           = $activeCurrency;
    $rate          = (float) $cur->exchange_rate;
    $sym           = $cur->symbol;
    $cv            = fn(float $jod): string => number_format(round($jod * $rate, 2), 2);
    $freeThreshold = $summary['free_threshold'] ?? 50.0;
    $subtotalJod   = $summary['subtotal'] ?? 0;
    // Progress toward free delivery (capped at 100%)
    $progress      = $freeThreshold > 0
        ? min(100, round(($subtotalJod / $freeThreshold) * 100))
        : 100;
    $remaining     = max(0, $freeThreshold - $subtotalJod);
@endphp

<div class="min-h-screen bg-[#f7f6f3]" dir="rtl">
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">

    {{-- Header --}}
    <div class="u1 flex items-baseline justify-between mb-8 gap-4 flex-wrap">
        <div>
            <h1 class="font-display text-2xl lg:text-3xl font-bold text-[#1a1917] tracking-tight">
                سلة التسوق
            </h1>
            @if(!empty($summary['items']))
            <p class="text-sm text-[#9a9793] mt-1">
                {{ array_sum(array_column($summary['items'], 'quantity')) }} قطعة
            </p>
            @endif
        </div>
        <a href="{{ route('products.index') }}"
           class="text-sm font-medium text-[#9a9793] hover:text-[#1a1917] transition-colors flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M7 16l-4-4m0 0l4-4m-4 4h18"/>
            </svg>
            متابعة التسوق
        </a>
    </div>

    @if(empty($summary['items']))
    {{-- Empty state --}}
    <div class="u2 text-center py-24">
        <div class="w-20 h-20 bg-white border border-[#ece9e4] rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-sm">
            <svg class="w-9 h-9 text-[#c5c2bc]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
        </div>
        <h2 class="font-display text-xl font-bold text-[#1a1917] mb-2">السلة فارغة</h2>
        <p class="text-[#9a9793] text-sm mb-8 max-w-xs mx-auto leading-relaxed">
            لم تضف أي منتجات بعد. استعرض متجرنا واختر ما يعجبك.
        </p>
        <a href="{{ route('products.index') }}"
           class="inline-flex items-center gap-2 bg-[#1a1917] hover:bg-[#2d2c2a] text-white
                  font-semibold px-8 py-3.5 rounded-xl transition-colors text-sm shadow-lg shadow-black/15">
            تصفح المنتجات
        </a>
    </div>

    @else
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 lg:gap-8 items-start">

        {{-- ── Cart items ─────────────────────────────────────────────── --}}
        <div class="lg:col-span-3 u2">

            {{-- Desktop table --}}
            <div class="hidden sm:block bg-white rounded-2xl border border-[#ece9e4] overflow-hidden">
                <div class="grid grid-cols-[2fr_1fr_1fr_36px] gap-4 px-5 py-3 border-b border-[#f0ede8]
                             text-[10px] font-bold text-[#9a9793] uppercase tracking-widest">
                    <span>المنتج</span>
                    <span class="text-center">الكمية</span>
                    <span>الإجمالي</span>
                    <span></span>
                </div>

                <div id="cart-items-desktop" class="divide-y divide-[#f7f6f3]">
                    @foreach($summary['items'] as $itemKey => $item)
                    <div class="cart-item-row grid grid-cols-[2fr_1fr_1fr_36px] gap-4 items-center px-5 py-4"
                         id="item-{{ $itemKey }}">

                        {{-- Product --}}
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="relative w-14 h-14 rounded-xl overflow-hidden bg-[#f7f6f3]
                                        border border-[#f0ede8] flex-shrink-0">
                                <div class="sk absolute inset-0" id="sk-{{ $loop->index }}"></div>
                                <img src="{{ $item['image'] ?? 'https://picsum.photos/seed/'.$loop->index.'/100/100' }}"
                                     alt="{{ $item['name'] }}"
                                     class="w-full h-full object-cover relative z-10"
                                     onload="document.getElementById('sk-{{ $loop->index }}').style.display='none'">
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
                                   data-unit-price-jod="{{ $item['price'] }}">
                                    {{ $cv($item['price']) }} {{ $sym }} / قطعة
                                </p>
                            </div>
                        </div>

                        {{-- Qty --}}
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

                        {{-- Subtotal --}}
                        <p class="item-subtotal text-sm font-bold text-[#1a1917] tabular-nums">
                            {{ $cv($item['subtotal']) }} {{ $sym }}
                        </p>

                        {{-- Remove --}}
                        <button type="button" class="rm-btn"
                                onclick="CartPage.remove('{{ $itemKey }}')" title="إزالة">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>

                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Mobile cards --}}
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
                                        onclick="CartPage.removeAll('{{ $itemKey }}')">
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

        {{-- ── Order Summary ────────────────────────────────────────────── --}}
        <div class="lg:col-span-2 u3">
            <div class="bg-white rounded-2xl border border-[#ece9e4] overflow-hidden sticky top-20">

                <div class="px-5 py-4 border-b border-[#f0ede8]">
                    <h2 class="font-semibold text-[#1a1917] text-sm">ملخص الطلب</h2>
                </div>

                {{-- Free delivery progress ─────────────────────────── --}}
                @if($summary['delivery_fee'] > 0)
                <div class="px-5 pt-4 pb-3 border-b border-[#f0ede8]">
                    <p class="text-xs text-[#6b6966] mb-2">
                        أضف
                        <span class="font-bold text-[#1a1917]">{{ $cv($remaining) }} {{ $sym }}</span>
                        للحصول على توصيل مجاني
                    </p>
                    <div class="delivery-bar-track">
                        <div class="delivery-bar-fill" id="delivery-progress-bar"
                             style="width: {{ $progress }}%"></div>
                    </div>
                </div>
                @else
                <div class="px-5 pt-3 pb-3 border-b border-[#f0ede8]">
                    <div class="flex items-center gap-2 text-xs text-emerald-700 font-semibold">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        🎉 مبروك! طلبك يستحق التوصيل المجاني
                    </div>
                </div>
                @endif

                {{-- Totals ──────────────────────────────────────────── --}}
                <div class="p-5 space-y-2.5 text-sm border-b border-[#f0ede8]">
                    <div class="flex justify-between text-[#6b6966]">
                        <span>المجموع الفرعي</span>
                        <span id="summary-subtotal"
                              class="font-semibold text-[#1a1917] tabular-nums">
                            {{ $cv($summary['subtotal']) }} {{ $sym }}
                        </span>
                    </div>

                    {{-- DELIVERY FEE row (was: tax row) --}}
                    <div class="flex justify-between text-[#6b6966]">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-[#b5b2ab]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1 12a2 2 0 002 2h8a2 2 0 002-2l1-12m-9 4v8m4-8v8"/>
                            </svg>
                            رسوم التوصيل
                        </span>
                        <span id="summary-delivery"
                              class="font-semibold tabular-nums {{ $summary['delivery_fee'] == 0 ? 'text-emerald-600' : 'text-[#1a1917]' }}">
                            @if($summary['delivery_fee'] == 0)
                                مجاني
                            @else
                                {{ $cv($summary['delivery_fee']) }} {{ $sym }}
                            @endif
                        </span>
                    </div>
                </div>

                <div class="px-5 py-4">
                    {{-- Grand total --}}
                    <div class="flex justify-between items-center mb-5">
                        <span class="font-bold text-[#1a1917]">الإجمالي</span>
                        <span id="summary-total"
                              class="font-bold text-xl text-[#1a1917] tabular-nums">
                            {{ $cv($summary['total']) }} {{ $sym }}
                        </span>
                    </div>

                    {{-- Formula note --}}
                    <p class="text-[10px] text-[#b5b2ab] text-center mb-4 leading-relaxed">
                        الإجمالي = المجموع الفرعي + رسوم التوصيل
                        @if($summary['delivery_fee'] == 0)
                        (التوصيل مجاني فوق {{ $cv($freeThreshold) }} {{ $sym }})
                        @endif
                    </p>

                    <a href="{{ route('checkout.index') }}"
                       class="flex items-center justify-center gap-2 w-full bg-[#1a1917] hover:bg-[#2d2c2a]
                              text-white font-bold text-sm py-4 rounded-xl transition-colors
                              shadow-lg shadow-black/15 active:scale-[.98]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        إتمام الطلب بأمان
                    </a>

                    <div class="mt-3 flex items-center justify-center gap-1.5 text-[10px] text-[#b5b2ab]">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        معاملات آمنة ومشفرة
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
// Server-injected constants for AJAX conversions
const CURRENCY_RATE     = {{ (float) $cur->exchange_rate }};
const CURRENCY_SYMBOL   = '{{ $sym }}';
const FREE_THRESHOLD_JOD = {{ (float) ($summary['free_threshold'] ?? 50) }};

const CartPage = {
    // Convert JOD → active currency, return formatted string with symbol
    f(jod) {
        const val = Math.round((jod || 0) * CURRENCY_RATE * 100) / 100;
        return new Intl.NumberFormat('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        }).format(val) + ' ' + CURRENCY_SYMBOL;
    },

    getRows(key) {
        return [
            document.getElementById('item-' + key),
            document.getElementById('item-mob-' + key),
        ].filter(Boolean);
    },

    async updateQty(itemKey, delta) {
        const rows     = this.getRows(itemKey);
        if (!rows.length) return;

        const qtyEls   = rows[0].querySelectorAll('.qty-display');
        const unitEl   = rows[0].querySelector('.unit-price-label');
        const unitJod  = parseFloat(unitEl?.dataset?.unitPriceJod ?? 0);
        const newQty   = parseInt(qtyEls[0]?.textContent ?? 1) + delta;

        if (newQty <= 0) return this.remove(itemKey);

        try {
            const res  = await fetch('/cart/update', {
                method:  'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept':       'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ item_key: itemKey, quantity: newQty }),
            });
            const data = await res.json();

            if (data.success) {
                rows.forEach(row => {
                    row.querySelectorAll('.qty-display').forEach(el => el.textContent = newQty);
                    row.querySelectorAll('.item-subtotal').forEach(el => {
                        el.textContent = this.f(unitJod * newQty);
                    });
                });
                this.updateSummary(data);
            }
        } catch (e) {
            if (typeof Cart !== 'undefined') Cart.toast('حدث خطأ في التحديث', 'error');
        }
    },

    async remove(itemKey) {
        if (!confirm('إزالة هذا المنتج من السلة؟')) return;

        const rows = this.getRows(itemKey);
        rows.forEach(r => r.classList.add('removing'));

        try {
            const res  = await fetch('/cart/remove/' + itemKey, {
                method:  'DELETE',
                headers: {
                    'Accept':       'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
            });
            const data = await res.json();

            if (data.success) {
                setTimeout(() => {
                    rows.forEach(r => r.remove());
                    this.updateSummary(data);
                    if (data.empty) location.reload();
                }, 220);
            } else {
                rows.forEach(r => r.classList.remove('removing'));
            }
        } catch (e) {
            rows.forEach(r => r.classList.remove('removing'));
            if (typeof Cart !== 'undefined') Cart.toast('تعذر الحذف، حاول مجدداً', 'error');
        }
    },

    removeAll(key) { this.remove(key); },

    /**
     * Update the summary sidebar after any AJAX cart operation.
     * data.delivery_fee and data.subtotal are in JOD (from CartController).
     */
    updateSummary(data) {
        const sub  = document.getElementById('summary-subtotal');
        const del  = document.getElementById('summary-delivery');
        const tot  = document.getElementById('summary-total');
        const bar  = document.getElementById('delivery-progress-bar');

        if (sub) sub.textContent = this.f(data.subtotal);
        if (tot) tot.textContent = this.f(data.total);

        // Delivery fee: update text + colour
        if (del) {
            const isFree = parseFloat(data.delivery_fee) === 0;
            del.textContent = isFree
                ? 'مجاني'
                : this.f(data.delivery_fee);
            del.className = 'font-semibold tabular-nums '
                + (isFree ? 'text-emerald-600' : 'text-[#1a1917]');
        }

        // Update free-delivery progress bar
        if (bar && FREE_THRESHOLD_JOD > 0) {
            const pct = Math.min(100, Math.round((data.subtotal / FREE_THRESHOLD_JOD) * 100));
            bar.style.width = pct + '%';
        }
    },
};
</script>
@endpush