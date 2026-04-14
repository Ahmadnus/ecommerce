{{--
    resources/views/cart/index.blade.php
    ─────────────────────────────────────────────────────────────────────────────
    All $summary values are in JOD.
    $activeCurrency is shared by ResolveCurrency middleware.
    Prices are displayed via the <x-price> component which converts on the fly.
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
    .cart-item-row.removing { transition: opacity .2s, transform .2s; opacity: 0; transform: translateX(16px); }
</style>
@endpush

@section('content')

@php
    // Active currency (set by ResolveCurrency middleware, defaults to JOD)
    $cur  = $activeCurrency;
    $rate = (float) $cur->exchange_rate;
    $sym  = $cur->symbol;

    // Helper: convert JOD → display currency
    $cv = fn(float $jod): string => number_format(round($jod * $rate, 2), 2);
@endphp

<div class="min-h-screen bg-[#f7f6f3]" dir="rtl">
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">

    {{-- Header --}}
    <div class="u1 flex items-baseline justify-between mb-8 gap-4 flex-wrap">
        <div>
            <h1 class="font-display text-2xl lg:text-3xl font-bold text-[#1a1917] tracking-tight">سلة التسوق</h1>
            @if(!empty($summary['items']))
            <p class="text-sm text-[#9a9793] mt-1">
                {{ array_sum(array_column($summary['items'], 'quantity')) }} قطعة
                — بالدينار الأردني ({{ $sym }})
            </p>
            @endif
        </div>
        <a href="{{ route('products.index') }}"
           class="text-sm font-medium text-[#9a9793] hover:text-[#1a1917] transition-colors flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"/>
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
        <p class="text-[#9a9793] text-sm mb-8 max-w-xs mx-auto leading-relaxed">لم تضف أي منتجات بعد.</p>
        <a href="{{ route('products.index') }}"
           class="inline-flex items-center gap-2 bg-[#1a1917] hover:bg-[#2d2c2a] text-white font-semibold px-8 py-3.5 rounded-xl transition-colors text-sm shadow-lg shadow-black/15">
            تصفح المنتجات
        </a>
    </div>

    @else
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 lg:gap-8 items-start">

        {{-- Cart items --}}
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

                        <div class="flex items-center gap-3 min-w-0">
                            <div class="relative w-14 h-14 rounded-xl overflow-hidden bg-[#f7f6f3] border border-[#f0ede8] flex-shrink-0">
                                <div class="sk absolute inset-0" id="sk-{{ $loop->index }}"></div>
                                <img src="{{ $item['image'] ?? 'https://picsum.photos/seed/'.$loop->index.'/100/100' }}"
                                     alt="{{ $item['name'] }}"
                                     class="w-full h-full object-cover relative z-10"
                                     onload="document.getElementById('sk-{{ $loop->index }}').style.display='none'">
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-[#1a1917] line-clamp-1">{{ $item['name'] }}</p>
                                @if(!empty($item['variant_name']))
                                <p class="text-xs font-medium mt-0.5" style="color:var(--brand-color)">{{ $item['variant_name'] }}</p>
                                @endif
                                {{-- Unit price in active currency --}}
                                <p class="text-xs text-[#b5b2ab] mt-0.5 unit-price-label tabular-nums"
                                   data-unit-price-jod="{{ $item['price'] }}">
                                    {{ $cv($item['price']) }} {{ $sym }} / قطعة
                                </p>
                            </div>
                        </div>

                        <div class="flex justify-center">
                            <div class="flex items-center gap-1.5">
                                <button type="button" class="qty-b" onclick="CartPage.updateQty('{{ $itemKey }}', -1)">−</button>
                                <span class="qty-display w-8 text-center text-sm font-bold text-[#1a1917] tabular-nums">
                                    {{ $item['quantity'] }}
                                </span>
                                <button type="button" class="qty-b" onclick="CartPage.updateQty('{{ $itemKey }}', 1)">+</button>
                            </div>
                        </div>

                        <p class="item-subtotal text-sm font-bold text-[#1a1917] tabular-nums">
                            {{ $cv($item['subtotal']) }} {{ $sym }}
                        </p>

                        <button type="button" class="rm-btn" onclick="CartPage.remove('{{ $itemKey }}')">
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
                        <div class="w-[72px] h-[72px] rounded-xl overflow-hidden bg-[#f7f6f3] border border-[#f0ede8] flex-shrink-0">
                            <img src="{{ $item['image'] ?? 'https://picsum.photos/seed/'.$loop->index.'/120/120' }}"
                                 class="w-full h-full object-cover" alt="{{ $item['name'] }}">
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <p class="text-sm font-semibold text-[#1a1917] line-clamp-2 flex-1">{{ $item['name'] }}</p>
                                <button type="button" class="rm-btn flex-shrink-0" onclick="CartPage.removeAll('{{ $itemKey }}')">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                            @if(!empty($item['variant_name']))
                            <p class="text-xs font-medium mt-0.5" style="color:var(--brand-color)">{{ $item['variant_name'] }}</p>
                            @endif
                            <div class="flex items-center justify-between mt-3">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" class="qty-b" onclick="CartPage.updateQty('{{ $itemKey }}', -1)">−</button>
                                    <span class="qty-display w-7 text-center text-sm font-bold text-[#1a1917] tabular-nums">{{ $item['quantity'] }}</span>
                                    <button type="button" class="qty-b" onclick="CartPage.updateQty('{{ $itemKey }}', 1)">+</button>
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

        {{-- Order summary --}}
        <div class="lg:col-span-2 u3">
            <div class="bg-white rounded-2xl border border-[#ece9e4] overflow-hidden sticky top-20">
                <div class="px-5 py-4 border-b border-[#f0ede8]">
                    <h2 class="font-semibold text-[#1a1917] text-sm">ملخص الطلب</h2>
                    <p class="text-[10px] text-[#b5b2ab] mt-0.5">
                        الأسعار بـ {{ $cur->name }} ({{ $sym }})
                    </p>
                </div>
                <div class="p-5 space-y-2.5 text-sm border-b border-[#f0ede8]">
                    <div class="flex justify-between text-[#6b6966]">
                        <span>المجموع الفرعي</span>
                        <span id="summary-subtotal" class="font-semibold text-[#1a1917] tabular-nums">
                            {{ $cv($summary['subtotal']) }} {{ $sym }}
                        </span>
                    </div>
                    <div class="flex justify-between text-[#6b6966]">
                        <span>الضريبة (10%)</span>
                        <span id="summary-tax" class="font-semibold text-[#1a1917] tabular-nums">
                            {{ $cv($summary['tax']) }} {{ $sym }}
                        </span>
                    </div>
                    <div class="flex justify-between text-[#6b6966]">
                        <span>الشحن</span>
                        <span id="summary-shipping"
                              class="font-semibold tabular-nums {{ $summary['shipping'] == 0 ? 'text-emerald-600' : 'text-[#1a1917]' }}">
                            {{ $summary['shipping'] == 0 ? 'مجاني' : $cv($summary['shipping']) . ' ' . $sym }}
                        </span>
                    </div>
                </div>
                <div class="px-5 py-4">
                    <div class="flex justify-between items-center mb-5">
                        <span class="font-bold text-[#1a1917]">الإجمالي</span>
                        <span id="summary-total" class="font-bold text-xl text-[#1a1917] tabular-nums">
                            {{ $cv($summary['total']) }} {{ $sym }}
                        </span>
                    </div>
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
                    <p class="text-center text-[10px] text-[#b5b2ab] mt-3">
                        الأسعار بالدينار الأردني — {{ $sym }}
                    </p>
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
// Exchange rate from server — used to re-convert after AJAX updates
const CURRENCY_RATE   = {{ (float) $cur->exchange_rate }};
const CURRENCY_SYMBOL = '{{ $sym }}';

const CartPage = {
    f(jod) {
        const converted = Math.round(jod * CURRENCY_RATE * 100) / 100;
        return new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(converted);
    },

    getRows(k) {
        return [document.getElementById('item-' + k), document.getElementById('item-mob-' + k)].filter(Boolean);
    },

    async updateQty(itemKey, delta) {
        const rows  = this.getRows(itemKey);
        if (!rows.length) return;

        const qtyEls    = rows[0].querySelectorAll('.qty-display');
        const unitEl    = rows[0].querySelector('.unit-price-label');
        // unit price stored in JOD
        const unitJod   = parseFloat(unitEl?.dataset?.unitPriceJod ?? 0);
        const newQty    = parseInt(qtyEls[0]?.textContent ?? 1) + delta;

        if (newQty <= 0) return this.remove(itemKey);

        try {
            const res  = await fetch('/cart/update', {
                method:  'PATCH',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json',
                           'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ item_key: itemKey, quantity: newQty }),
            });
            const data = await res.json();
            if (data.success) {
                rows.forEach(row => {
                    row.querySelectorAll('.qty-display').forEach(el => el.textContent = newQty);
                    row.querySelectorAll('.item-subtotal').forEach(el => {
                        el.textContent = this.f(unitJod * newQty) + ' ' + CURRENCY_SYMBOL;
                    });
                });
                this.updateSummary(data);
            }
        } catch (e) {
            if (typeof Cart !== 'undefined') Cart.toast('حدث خطأ', 'error');
        }
    },

    async remove(itemKey) {
        if (!confirm('إزالة هذا المنتج؟')) return;
        const rows = this.getRows(itemKey);
        rows.forEach(r => r.classList.add('removing'));
        try {
            const res  = await fetch('/cart/remove/' + itemKey, {
                method:  'DELETE',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            });
            const data = await res.json();
            if (data.success) {
                setTimeout(() => {
                    rows.forEach(r => r.remove());
                    this.updateSummary(data);
                    if (data.empty) location.reload();
                }, 220);
            } else rows.forEach(r => r.classList.remove('removing'));
        } catch (e) {
            rows.forEach(r => r.classList.remove('removing'));
        }
    },

    removeAll(k) { this.remove(k); },

    // data.subtotal etc. come from server in JOD
    updateSummary(data) {
        const f = v => this.f(v) + ' ' + CURRENCY_SYMBOL;
        const s = id => document.getElementById(id);
        if (s('summary-subtotal')) s('summary-subtotal').textContent = f(data.subtotal);
        if (s('summary-tax'))      s('summary-tax').textContent      = f(data.tax);
        if (s('summary-total'))    s('summary-total').textContent    = f(data.total);
        const sh = s('summary-shipping');
        if (sh) {
            const isFree = parseFloat(data.shipping) === 0;
            sh.textContent = isFree ? 'مجاني' : f(data.shipping);
            sh.className   = 'font-semibold tabular-nums ' + (isFree ? 'text-emerald-600' : 'text-[#1a1917]');
        }
    }
};
</script>
@endpush