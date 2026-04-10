@extends('layouts.app')

@section('title', 'Your Cart')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <h1 class="font-display text-3xl font-bold text-gray-900 mb-8">Your Cart</h1>

    @if(empty($summary['items']))
    <div class="text-center py-24" id="empty-state">
        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
        </div>
        <h2 class="text-xl font-semibold text-gray-700 mb-2">Your cart is empty</h2>
        <p class="text-gray-400 mb-8">Looks like you haven't added anything yet.</p>
        <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white font-semibold px-8 py-3 rounded-xl transition-colors">
            Start Shopping
        </a>
    </div>
    @else
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Cart Items --}}
        <div class="lg:col-span-2 space-y-4" id="cart-items">
            @foreach($summary['items'] as $itemKey => $item)
            <div class="cart-item bg-white rounded-2xl border border-gray-100 p-4 flex items-center gap-4 animate-fade-in"
                 id="item-{{ $itemKey }}">

                {{-- Product Image - تم استخدام نفس منطق الـ Checkout تماماً --}}
                <a href="{{ route('products.show', $item['slug'] ?? '#') }}" class="flex-shrink-0 w-20 h-20 rounded-xl overflow-hidden bg-gray-50 border">
                    <img src="{{ $item['image'] ?? 'https://picsum.photos/seed/'.$itemKey.'/100/100' }}" 
                         alt="{{ $item['name'] }}" 
                         class="w-full h-full object-cover">
                </a>

                {{-- Details --}}
                <div class="flex-1 min-w-0">
                    <a href="{{ route('products.show', $item['slug'] ?? '#') }}" class="font-semibold text-gray-900 hover:text-brand-600 transition-colors line-clamp-1">
                        {{ $item['name'] }}
                    </a>
                    @if(!empty($item['variant_name']))
                        <p class="text-xs text-brand-600 mt-0.5">{{ $item['variant_name'] }}</p>
                    @endif
                    {{-- حفظ السعر هنا لمنع الـ undefined --}}
                    <p class="text-sm text-gray-500 mt-0.5 unit-price-label" data-unit-price="{{ $item['price'] }}">
                        ${{ number_format($item['price'], 2) }} each
                    </p>
                </div>

                {{-- Quantity controls --}}
                <div class="flex items-center border border-gray-200 rounded-xl overflow-hidden flex-shrink-0">
                    <button onclick="CartPage.updateQty('{{ $itemKey }}', -1)" class="w-9 h-9 flex items-center justify-center text-gray-600 hover:bg-gray-50">−</button>
                    <span class="w-10 text-center text-sm font-semibold qty-display">{{ $item['quantity'] }}</span>
                    <button onclick="CartPage.updateQty('{{ $itemKey }}', 1)" class="w-9 h-9 flex items-center justify-center text-gray-600 hover:bg-gray-50">+</button>
                </div>

                {{-- Subtotal per Item --}}
                <div class="text-right flex-shrink-0 w-24">
                    <p class="font-bold text-gray-900 item-subtotal">
                        ${{ number_format($item['subtotal'], 2) }}
                    </p>
                </div>

                {{-- Remove Button --}}
                <button onclick="CartPage.remove('{{ $itemKey }}')" class="flex-shrink-0 p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            @endforeach
        </div>

        {{-- Order Summary Sidebar --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl border border-gray-100 p-6 sticky top-20">
                <h2 class="font-semibold text-gray-900 text-lg mb-5">Order Summary</h2>
                <div class="space-y-3 text-sm border-b pb-4">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Subtotal</span>
                        <span class="font-medium" id="summary-subtotal">${{ number_format($summary['subtotal'], 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Tax (10%)</span>
                        <span class="font-medium" id="summary-tax">${{ number_format($summary['tax'], 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Shipping</span>
                        <span class="font-medium" id="summary-shipping">
                            {{ $summary['shipping'] == 0 ? 'Free 🎉' : '$' . number_format($summary['shipping'], 2) }}
                        </span>
                    </div>
                </div>
                <div class="pt-4 flex justify-between items-center mb-6">
                    <span class="font-bold text-gray-900">Total</span>
                    <span class="font-bold text-xl text-gray-900" id="summary-total">${{ number_format($summary['total'], 2) }}</span>
                </div>
                <a href="{{ route('checkout.index') }}" class="block w-full bg-brand-600 hover:bg-brand-700 text-white font-semibold text-center py-4 rounded-xl transition-all active:scale-95">
                    Proceed to Checkout
                </a>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
const CartPage = {
    // تنسيق الأرقام لضمان عدم ظهور undefined
    f(num) {
        return new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(num || 0);
    },

    async updateQty(itemKey, delta) {
        const row = document.getElementById('item-' + itemKey);
        const qtyEl = row.querySelector('.qty-display');
        const unitPrice = parseFloat(row.querySelector('.unit-price-label').dataset.unitPrice);
        const newQty = parseInt(qtyEl.textContent) + delta;

        if (newQty <= 0) return this.remove(itemKey);

        try {
            const res = await fetch('/cart/update', {
                method: 'PATCH',
                headers: { 
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content 
                },
                body: JSON.stringify({ item_key: itemKey, quantity: newQty })
            });

            const data = await res.json();

            if (data.success) {
                qtyEl.textContent = newQty;
                // تحديث سعر القطع يدوياً لتجنب الـ undefined
                row.querySelector('.item-subtotal').textContent = '$' + this.f(unitPrice * newQty);
                this.updateSummary(data);
            } else {
                alert("❌ " + data.message); 
            }
        } catch (e) {
            alert("⚠️ حدث خطأ في السيرفر.");
        }
    },

    async remove(itemKey) {
        if (!confirm('حذف من السلة؟')) return;
        try {
            const res = await fetch('/cart/remove/' + itemKey, {
                method: 'DELETE',
                headers: { 
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content 
                }
            });
            const data = await res.json();
            if (data.success) {
                document.getElementById('item-' + itemKey).remove();
                this.updateSummary(data);
                if (data.empty) location.reload();
            }
        } catch (e) {
            alert("⚠️ تعذر الحذف.");
        }
    },

    updateSummary(data) {
        document.getElementById('summary-subtotal').textContent = '$' + this.f(data.subtotal);
        document.getElementById('summary-tax').textContent = '$' + this.f(data.tax);
        document.getElementById('summary-total').textContent = '$' + this.f(data.total);
        
        const shipEl = document.getElementById('summary-shipping');
        if(shipEl) {
            shipEl.textContent = parseFloat(data.shipping) === 0 ? 'Free 🎉' : '$' + this.f(data.shipping);
        }
    }
};
</script>
@endpush