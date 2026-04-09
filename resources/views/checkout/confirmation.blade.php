@extends('layouts.app')

@section('title', 'Order Confirmed — ' . $order->order_number)

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16">

    {{-- ═══ SUCCESS HEADER ══════════════════════════════════════════════════ --}}
    <div class="text-center mb-10 animate-slide-up">
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-5">
            <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h1 class="font-display text-3xl font-bold text-gray-900 mb-2">Order Confirmed!</h1>
        <p class="text-gray-500 text-lg">Thank you for your purchase, {{ $order->shipping_name }}.</p>
        <div class="inline-flex items-center gap-2 bg-gray-100 text-gray-700 text-sm font-mono font-bold px-4 py-2 rounded-xl mt-4">
            Order #{{ $order->order_number }}
        </div>
    </div>

    {{-- ═══ ORDER DETAILS CARD ═══════════════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden mb-6 animate-fade-in">

        {{-- Status bar --}}
        <div class="bg-green-50 border-b border-green-100 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-2.5 h-2.5 rounded-full bg-green-500"></div>
                <span class="text-sm font-semibold text-green-800">
                    Status: {{ ucfirst($order->status) }}
                </span>
            </div>
            <span class="text-xs text-gray-500">{{ $order->created_at->format('M d, Y — g:i A') }}</span>
        </div>

        {{-- Order items --}}
        <div class="p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Items Ordered</h3>
            <div class="space-y-3">
                @foreach($order->items as $item)
                <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400 text-xs font-bold">
                            {{ $item->quantity }}×
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $item->product_name }}</p>
                            <p class="text-xs text-gray-400">${{ number_format($item->unit_price, 2) }} each</p>
                        </div>
                    </div>
                    <span class="text-sm font-semibold">${{ number_format($item->total_price, 2) }}</span>
                </div>
                @endforeach
            </div>

            {{-- Totals --}}
            <div class="mt-5 space-y-2 text-sm border-t border-gray-100 pt-4">
                <div class="flex justify-between text-gray-500">
                    <span>Subtotal</span><span>${{ number_format($order->subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between text-gray-500">
                    <span>Tax</span><span>${{ number_format($order->tax_amount, 2) }}</span>
                </div>
                <div class="flex justify-between text-gray-500">
                    <span>Shipping</span>
                    <span>{{ $order->shipping_amount == 0 ? 'Free' : '$' . number_format($order->shipping_amount, 2) }}</span>
                </div>
                <div class="flex justify-between font-bold text-gray-900 pt-2 border-t border-gray-100 text-base">
                    <span>Total Paid</span>
                    <span>${{ number_format($order->total_amount, 2) }}</span>
                </div>
            </div>
        </div>

        {{-- Shipping Address --}}
        <div class="border-t border-gray-100 px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Shipping To</h4>
                <p class="text-sm text-gray-800 leading-relaxed">
                    {{ $order->shipping_name }}<br>
                    {{ $order->shipping_address }}<br>
                    {{ $order->shipping_city }}@if($order->shipping_state), {{ $order->shipping_state }}@endif {{ $order->shipping_zip }}<br>
                    {{ $order->shipping_country }}
                </p>
            </div>
            <div>
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Confirmation Sent To</h4>
                <p class="text-sm text-gray-800">{{ $order->shipping_email }}</p>
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2 mt-4">Payment</h4>
                <p class="text-sm text-gray-800 capitalize">{{ str_replace('_', ' ', $order->payment_method) }}</p>
            </div>
        </div>
    </div>

    {{-- ═══ ACTIONS ═══════════════════════════════════════════════════════════ --}}
    <div class="flex flex-col sm:flex-row gap-4 justify-center animate-fade-in">
        <a href="{{ route('products.index') }}"
           class="flex-1 sm:flex-none sm:px-8 bg-brand-600 hover:bg-brand-700 text-white font-semibold py-3 rounded-xl transition-colors text-center">
            Continue Shopping
        </a>
        <button onclick="window.print()"
                class="flex-1 sm:flex-none sm:px-8 border border-gray-200 hover:bg-gray-50 text-gray-700 font-semibold py-3 rounded-xl transition-colors">
            🖨️ Print Receipt
        </button>
    </div>
</div>
@endsection
