@extends('layouts.app')

@section('title', 'Your Cart')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <h1 class="font-display text-3xl font-bold text-gray-900 mb-8">Your Cart</h1>

    @if(empty($summary['items']))
    {{-- ── Empty Cart State ─────────────────────────────────────────────── --}}
    <div class="text-center py-24" id="empty-state">
        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
        </div>
        <h2 class="text-xl font-semibold text-gray-700 mb-2">Your cart is empty</h2>
        <p class="text-gray-400 mb-8">Looks like you haven't added anything yet.</p>
        <a href="{{ route('products.index') }}"
           class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white font-semibold px-8 py-3 rounded-xl transition-colors">
            Start Shopping
        </a>
    </div>

    @else
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- ── Cart Items ───────────────────────────────────────────────────── --}}
        <div class="lg:col-span-2 space-y-4" id="cart-items">
            @foreach($summary['items'] as $productId => $item)
            <div class="cart-item bg-white rounded-2xl border border-gray-100 p-4 flex items-center gap-4 animate-fade-in"
                 data-product-id="{{ $productId }}" id="item-{{ $productId }}">

                {{-- Image --}}
                <a href="{{ route('products.show', $item['slug']) }}"
                   class="flex-shrink-0 w-20 h-20 rounded-xl overflow-hidden bg-gray-50">
                    <img src="{{ $item['image'] ?? 'https://picsum.photos/seed/'.$productId.'/200/200' }}"
                         alt="{{ $item['name'] }}"
                         class="w-full h-full object-cover">
                </a>

                {{-- Details --}}
                <div class="flex-1 min-w-0">
                    <a href="{{ route('products.show', $item['slug']) }}"
                       class="font-semibold text-gray-900 hover:text-brand-600 transition-colors line-clamp-1">
                        {{ $item['name'] }}
                    </a>
                    <p class="text-sm text-gray-500 mt-0.5">
                        ${{ number_format($item['price'], 2) }} each
                    </p>
                </div>

                {{-- Quantity controls --}}
                <div class="flex items-center border border-gray-200 rounded-xl overflow-hidden flex-shrink-0">
                    <button onclick="CartPage.updateQty({{ $productId }}, -1)"
                            class="w-9 h-9 flex items-center justify-center text-gray-600 hover:bg-gray-50 transition-colors">−</button>
                    <span class="w-10 text-center text-sm font-semibold qty-display">{{ $item['quantity'] }}</span>
                    <button onclick="CartPage.updateQty({{ $productId }}, 1)"
                            class="w-9 h-9 flex items-center justify-center text-gray-600 hover:bg-gray-50 transition-colors">+</button>
                </div>

                {{-- Subtotal --}}
                <div class="text-right flex-shrink-0 w-20">
                    <p class="font-bold text-gray-900 item-subtotal">${{ number_format($item['subtotal'], 2) }}</p>
                </div>

                {{-- Remove --}}
                <button onclick="CartPage.remove({{ $productId }})"
                        class="flex-shrink-0 p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors"
                        title="Remove item">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            @endforeach
        </div>

        {{-- ── Order Summary Sidebar ─────────────────────────────────────────── --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl border border-gray-100 p-6 sticky top-20" id="order-summary">
                <h2 class="font-semibold text-gray-900 text-lg mb-5">Order Summary</h2>

                <div class="space-y-3 text-sm">
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
                    @if($summary['subtotal'] < 50)
                    <div class="text-xs text-brand-600 bg-brand-50 px-3 py-2 rounded-lg">
                        Add ${{ number_format(50 - $summary['subtotal'], 2) }} more for free shipping!
                    </div>
                    @endif
                </div>

                <div class="border-t border-gray-100 mt-4 pt-4 flex justify-between items-center">
                    <span class="font-bold text-gray-900">Total</span>
                    <span class="font-bold text-xl text-gray-900" id="summary-total">${{ number_format($summary['total'], 2) }}</span>
                </div>

                <a href="{{ route('checkout.index') }}"
                   class="mt-5 block w-full bg-brand-600 hover:bg-brand-700 text-white font-semibold text-center py-4 rounded-xl transition-colors">
                    Proceed to Checkout →
                </a>

                <a href="{{ route('products.index') }}"
                   class="mt-3 block w-full text-center text-sm text-gray-500 hover:text-brand-600 transition-colors py-2">
                    ← Continue Shopping
                </a>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
/**
 * CartPage — handles real-time cart updates on the cart page.
 * All mutations call the backend API and update the DOM without a full reload.
 */
const CartPage = {
    async updateQty(productId, delta) {
        // Find current qty from the DOM
        const row = document.getElementById('item-' + productId);
        if (!row) return;
        const qtyEl = row.querySelector('.qty-display');
        const newQty = Math.max(0, parseInt(qtyEl.textContent) + delta);

        if (newQty === 0) {
            return this.remove(productId);
        }

        try {
            const res  = await fetch('/cart/update', {
                method:  'PATCH',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': Cart.csrfToken },
                body:    JSON.stringify({ product_id: productId, quantity: newQty }),
            });
            const data = await res.json();
            if (!data.success) { Cart.toast(data.message, 'error'); return; }

            // Update the specific row
            qtyEl.textContent = newQty;
            row.querySelector('.item-subtotal').textContent =
                '$' + (parseFloat(row.querySelector('.qty-display').textContent.replace(/[^0-9.]/g, '')) * 0).toFixed(2); // recalculated server-side

            // Update summary panel
            this.updateSummary(data);
            Cart.updateBadge(data.item_count);

        } catch(e) {
            Cart.toast('Update failed. Please refresh.', 'error');
        }
    },

    async remove(productId) {
        const row = document.getElementById('item-' + productId);
        if (row) {
            // Animate out
            row.style.transition = 'opacity 0.3s, transform 0.3s';
            row.style.opacity    = '0';
            row.style.transform  = 'translateX(20px)';
        }

        try {
            const res  = await fetch('/cart/remove/' + productId, {
                method:  'DELETE',
                headers: { 'X-CSRF-TOKEN': Cart.csrfToken },
            });
            const data = await res.json();

            if (data.success) {
                setTimeout(() => row?.remove(), 300);
                this.updateSummary(data);
                Cart.updateBadge(data.item_count);
                Cart.toast(data.message);

                // Show empty state if cart is now empty
                if (data.empty) {
                    setTimeout(() => location.reload(), 500);
                }
            }
        } catch(e) {
            if (row) { row.style.opacity = '1'; row.style.transform = ''; }
            Cart.toast('Remove failed. Please refresh.', 'error');
        }
    },

    updateSummary(data) {
        const set = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
        set('summary-subtotal', '$' + data.subtotal);
        set('summary-tax',      '$' + data.tax);
        set('summary-total',    '$' + data.total);
        set('summary-shipping', parseFloat(data.shipping) === 0 ? 'Free 🎉' : '$' + data.shipping);
    },
};
</script>
@endpush
