{{-- resources/views/cart/index.blade.php --}}

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

    /* Cart row */
    .cart-item-row {
        transition: background .15s;
    }
    .cart-item-row:hover { background: #faf9f7; }

    /* Qty controls */
    .qty-ctrl { display: flex; align-items: center; gap: 6px; }
    .qty-b {
        width: 28px; height: 28px;
        border: 1.5px solid #e5e3df;
        border-radius: 8px;
        background: #fff;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        font-size: 15px;
        color: #6b6966;
        transition: all .15s;
        flex-shrink: 0;
        line-height: 1;
    }
    .qty-b:hover { border-color: #1a1917; color: #1a1917; background: #f5f4f2; }

    /* Remove btn */
    .rm-btn {
        width: 30px; height: 30px;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        color: #c5c2bc;
        transition: all .15s;
        cursor: pointer;
        flex-shrink: 0;
    }
    .rm-btn:hover { background: #fee2e2; color: #ef4444; }
    .rm-btn svg { width: 14px; height: 14px; }

    /* Remove animation */
    .cart-item-row.removing {
        transition: opacity .2s, transform .2s;
        opacity: 0;
        transform: translateX(16px);
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-[#f7f6f3]" dir="rtl">
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">

    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
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
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"/>
            </svg>
            متابعة التسوق
        </a>
    </div>

    {{-- ═══════════════════════════════════════════════
         EMPTY STATE
    ═══════════════════════════════════════════════ --}}
    @if(empty($summary['items']))
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
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
            تصفح المنتجات
        </a>
    </div>

    @else

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 lg:gap-8 items-start">

        {{-- ═══════════════════════════════════════════════
             CART ITEMS
        ═══════════════════════════════════════════════ --}}
        <div class="lg:col-span-3 u2">

            {{-- Desktop table (hidden on mobile) --}}
            <div class="hidden sm:block bg-white rounded-2xl border border-[#ece9e4] overflow-hidden">

                {{-- Table head --}}
                <div class="grid grid-cols-[2fr_1fr_1fr_36px] gap-4 px-5 py-3 border-b border-[#f0ede8] text-[10px] font-bold text-[#9a9793] uppercase tracking-widest">
                    <span>المنتج</span>
                    <span class="text-center">الكمية</span>
                    <span class="text-left">الإجمالي</span>
                    <span></span>
                </div>

                <div id="cart-items-desktop" class="divide-y divide-[#f7f6f3]">
                    @foreach($summary['items'] as $itemKey => $item)
                    <div class="cart-item-row grid grid-cols-[2fr_1fr_1fr_36px] gap-4 items-center px-5 py-4"
                         id="item-{{ $itemKey }}">

                        {{-- Product --}}
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="relative w-14 h-14 rounded-xl overflow-hidden bg-[#f7f6f3] border border-[#f0ede8] flex-shrink-0">
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
                                <p class="text-xs font-medium mt-0.5" style="color: var(--brand-color, #0ea5e9)">
                                    {{ $item['variant_name'] }}
                                </p>
                                @endif
                                <p class="text-xs text-[#b5b2ab] mt-0.5 unit-price-label tabular-nums"
                                   data-unit-price="{{ $item['price'] }}">
                                    ${{ number_format($item['price'], 2) }} / قطعة
                                </p>
                            </div>
                        </div>

                        {{-- Qty --}}
                        <div class="flex justify-center">
                            <div class="qty-ctrl">
                                <button type="button" class="qty-b"
                                        onclick="CartPage.updateQty('{{ $itemKey }}', -1)">−</button>
                                <span class="qty-display w-8 text-center text-sm font-bold text-[#1a1917] tabular-nums">
                                    {{ $item['quantity'] }}
                                </span>
                                <button type="button" class="qty-b"
                                        onclick="CartPage.updateQty('{{ $itemKey }}', 1)">+</button>
                            </div>
                        </div>

                        {{-- Total --}}
                        <div>
                            <p class="item-subtotal text-sm font-bold text-[#1a1917] tabular-nums">
                                ${{ number_format($item['subtotal'], 2) }}
                            </p>
                        </div>

                        {{-- Remove --}}
                        <button type="button" class="rm-btn"
                                onclick="CartPage.remove('{{ $itemKey }}')"
                                title="إزالة">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>

                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Mobile cards (hidden on sm+) --}}
            <div class="sm:hidden space-y-3" id="cart-items-mobile">
                @foreach($summary['items'] as $itemKey => $item)
                <div class="cart-item-row bg-white rounded-2xl border border-[#ece9e4] p-4"
                     id="item-mob-{{ $itemKey }}">
                    <div class="flex gap-3">
                        <div class="relative w-18 h-18 w-[72px] h-[72px] rounded-xl overflow-hidden bg-[#f7f6f3] border border-[#f0ede8] flex-shrink-0">
                            <img src="{{ $item['image'] ?? 'https://picsum.photos/seed/'.$loop->index.'/120/120' }}"
                                 alt="{{ $item['name'] }}"
                                 class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <p class="text-sm font-semibold text-[#1a1917] line-clamp-2 leading-snug flex-1">
                                    {{ $item['name'] }}
                                </p>
                                <button type="button" class="rm-btn flex-shrink-0"
                                        onclick="CartPage.removeAll('{{ $itemKey }}')">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                            @if(!empty($item['variant_name']))
                            <p class="text-xs font-medium mt-0.5" style="color: var(--brand-color, #0ea5e9)">
                                {{ $item['variant_name'] }}
                            </p>
                            @endif
                            <div class="flex items-center justify-between mt-3">
                                <div class="qty-ctrl">
                                    <button type="button" class="qty-b"
                                            onclick="CartPage.updateQty('{{ $itemKey }}', -1)">−</button>
                                    <span class="qty-display w-7 text-center text-sm font-bold text-[#1a1917] tabular-nums">
                                        {{ $item['quantity'] }}
                                    </span>
                                    <button type="button" class="qty-b"
                                            onclick="CartPage.updateQty('{{ $itemKey }}', 1)">+</button>
                                </div>
                                <p class="item-subtotal text-sm font-bold text-[#1a1917] tabular-nums">
                                    ${{ number_format($item['subtotal'], 2) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

        </div>

        {{-- ═══════════════════════════════════════════════
             ORDER SUMMARY
        ═══════════════════════════════════════════════ --}}
        <div class="lg:col-span-2 u3">
            <div class="bg-white rounded-2xl border border-[#ece9e4] overflow-hidden sticky top-20">

                <div class="px-5 py-4 border-b border-[#f0ede8]">
                    <h2 class="font-semibold text-[#1a1917] text-sm">ملخص الطلب</h2>
                </div>

                <div class="p-5 space-y-2.5 text-sm border-b border-[#f0ede8]">
                    <div class="flex justify-between text-[#6b6966]">
                        <span>المجموع الفرعي</span>
                        <span id="summary-subtotal" class="font-semibold text-[#1a1917] tabular-nums">
                            ${{ number_format($summary['subtotal'], 2) }}
                        </span>
                    </div>
                    <div class="flex justify-between text-[#6b6966]">
                        <span>الضريبة (10%)</span>
                        <span id="summary-tax" class="font-semibold text-[#1a1917] tabular-nums">
                            ${{ number_format($summary['tax'], 2) }}
                        </span>
                    </div>
                    <div class="flex justify-between text-[#6b6966]">
                        <span>الشحن</span>
                        <span id="summary-shipping"
                              class="font-semibold tabular-nums {{ $summary['shipping'] == 0 ? 'text-emerald-600' : 'text-[#1a1917]' }}">
                            {{ $summary['shipping'] == 0 ? 'مجاني' : '$' . number_format($summary['shipping'], 2) }}
                        </span>
                    </div>
                </div>

                <div class="px-5 py-4">
                    <div class="flex justify-between items-center mb-5">
                        <span class="font-bold text-[#1a1917]">الإجمالي</span>
                        <span id="summary-total" class="font-bold text-xl text-[#1a1917] tabular-nums">
                            ${{ number_format($summary['total'], 2) }}
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

                    {{-- Secure badge --}}
                    <div class="mt-3 flex items-center justify-center gap-1.5 text-[10px] text-[#b5b2ab]">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        معاملات آمنة ومشفرة بالكامل
                    </div>

                    {{-- Payment icons --}}
                    <div class="flex items-center justify-center gap-2 mt-3">
                        @foreach(['Visa', 'Mastercard', 'COD'] as $pm)
                        <span class="text-[10px] font-semibold text-[#9a9793] bg-[#f7f6f3] border border-[#ece9e4] px-2 py-1 rounded-lg">
                            {{ $pm }}
                        </span>
                        @endforeach
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
const CartPage = {
    f(n) {
        return new Intl.NumberFormat('en-US', {
            minimumFractionDigits: 2, maximumFractionDigits: 2
        }).format(n || 0);
    },

    getRows(itemKey) {
        return [
            document.getElementById('item-' + itemKey),
            document.getElementById('item-mob-' + itemKey),
        ].filter(Boolean);
    },

    async updateQty(itemKey, delta) {
        const rows    = this.getRows(itemKey);
        const anyRow  = rows[0];
        if (!anyRow) return;

        const qtyEls    = anyRow.querySelectorAll('.qty-display');
        const unitEl    = anyRow.querySelector('.unit-price-label');
        const unitPrice = parseFloat(unitEl?.dataset?.unitPrice ?? 0);
        const newQty    = parseInt(qtyEls[0]?.textContent ?? 1) + delta;

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
                        el.textContent = '$' + this.f(unitPrice * newQty);
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
            if (typeof Cart !== 'undefined') Cart.toast('تعذر الحذف', 'error');
        }
    },

    removeAll(itemKey) { this.remove(itemKey); },

    updateSummary(data) {
        const s = document.getElementById('summary-subtotal');
        const t = document.getElementById('summary-tax');
        const sh = document.getElementById('summary-shipping');
        const tot = document.getElementById('summary-total');

        if (s)   s.textContent   = '$' + this.f(data.subtotal);
        if (t)   t.textContent   = '$' + this.f(data.tax);
        if (tot) tot.textContent = '$' + this.f(data.total);
        if (sh) {
            const isFree = parseFloat(data.shipping) === 0;
            sh.textContent = isFree ? 'مجاني' : '$' + this.f(data.shipping);
            sh.className   = 'font-semibold tabular-nums ' + (isFree ? 'text-emerald-600' : 'text-[#1a1917]');
        }
    }
};
</script>
@endpush