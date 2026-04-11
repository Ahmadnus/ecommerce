{{-- resources/views/orders/success.blade.php --}}

@extends('layouts.app')
@section('title', 'تم تأكيد طلبك')

@push('head')
<style>
    @keyframes checkIn {
        0%   { transform: scale(0) rotate(-45deg); opacity: 0; }
        60%  { transform: scale(1.2) rotate(5deg); }
        100% { transform: scale(1) rotate(0deg); opacity: 1; }
    }
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .check-anim  { animation: checkIn 0.55s cubic-bezier(0.16, 1, 0.3, 1) 0.1s both; }
    .fade-up-1   { animation: fadeUp 0.4s ease 0.3s both; }
    .fade-up-2   { animation: fadeUp 0.4s ease 0.45s both; }
    .fade-up-3   { animation: fadeUp 0.4s ease 0.6s both; }
    .fade-up-4   { animation: fadeUp 0.4s ease 0.75s both; }
    .fade-up-5   { animation: fadeUp 0.4s ease 0.9s both; }

    .status-badge-pending    { background: #fef9c3; color: #854d0e; }
    .status-badge-processing { background: #dbeafe; color: #1e40af; }
    .status-badge-shipped    { background: #f3e8ff; color: #6b21a8; }
    .status-badge-delivered  { background: #dcfce7; color: #166534; }
    .status-badge-cancelled  { background: #fee2e2; color: #991b1b; }
</style>
@endpush

@section('content')
<div class="max-w-2xl mx-auto px-4 py-16" dir="rtl">

    {{-- ── Success card ──────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">

        {{-- Green header --}}
        <div class="bg-gradient-to-b from-green-50 to-white px-8 pt-12 pb-6 text-center">
            <div class="check-anim w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-5">
                <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
            </div>

            <h1 class="fade-up-1 font-display text-3xl font-bold text-gray-900 mb-2">
                تم تأكيد طلبك! 🎉
            </h1>
            <p class="fade-up-2 text-gray-500 text-sm">
                شكراً لك. سيتم التواصل معك قريباً لتأكيد التوصيل.
            </p>
        </div>

        <div class="px-8 pb-8 space-y-6">

            {{-- Order number + status --}}
            <div class="fade-up-2 flex items-center justify-between bg-gray-50 rounded-2xl px-5 py-4">
                <div>
                    <p class="text-xs text-gray-400 font-medium mb-0.5">رقم الطلب</p>
                    <p class="font-bold text-gray-900 font-mono text-lg tracking-wider">
                        {{ $order->order_number }}
                    </p>
                </div>
                <span class="status-badge-{{ $order->status }} text-xs font-bold px-3 py-1.5 rounded-full">
                    {{ $order->status_label }}
                </span>
            </div>

            {{-- Shipping info --}}
            <div class="fade-up-3">
                <h3 class="text-sm font-bold text-gray-700 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    بيانات التوصيل
                </h3>
                <div class="bg-gray-50 rounded-xl px-4 py-3 text-sm space-y-1">
                    <p class="font-semibold text-gray-900">{{ $order->shipping_name }}</p>
                    <p class="text-gray-500" dir="ltr">{{ $order->shipping_phone }}</p>
                    <p class="text-gray-500">{{ $order->shipping_address }}، {{ $order->shipping_city }}
                        @if($order->shipping_zip) {{ $order->shipping_zip }} @endif
                    </p>
                </div>
            </div>

            {{-- Order items --}}
            <div class="fade-up-3">
                <h3 class="text-sm font-bold text-gray-700 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    المنتجات المطلوبة ({{ $order->items->count() }})
                </h3>
                <div class="space-y-2">
                    @foreach($order->items as $item)
                    <div class="flex items-center justify-between text-sm bg-gray-50 rounded-xl px-4 py-3">
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900 line-clamp-1">{{ $item->product_name }}</p>
                            @if($item->variant_name)
                                <p class="text-xs text-brand-600 mt-0.5">{{ $item->variant_name }}</p>
                            @endif
                        </div>
                        <div class="text-right flex-shrink-0 mr-4">
                            <p class="text-gray-400 text-xs">{{ $item->quantity }} × ${{ number_format($item->unit_price, 2) }}</p>
                            <p class="font-bold text-gray-900">${{ number_format($item->total_price, 2) }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Price breakdown --}}
            <div class="fade-up-4 border-t border-gray-100 pt-4 space-y-2 text-sm">
                <div class="flex justify-between text-gray-500">
                    <span>المجموع الفرعي</span>
                    <span>${{ number_format($order->subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between text-gray-500">
                    <span>الضريبة</span>
                    <span>${{ number_format($order->tax_amount, 2) }}</span>
                </div>
                <div class="flex justify-between text-gray-500">
                    <span>الشحن</span>
                    <span>{{ $order->shipping_amount == 0 ? 'مجاني' : '$' . number_format($order->shipping_amount, 2) }}</span>
                </div>
                <div class="flex justify-between font-bold text-gray-900 text-base pt-2 border-t border-gray-100">
                    <span>الإجمالي</span>
                    <span>${{ number_format($order->total_amount, 2) }}</span>
                </div>
                <div class="flex justify-between text-amber-600 text-xs font-medium bg-amber-50 rounded-lg px-3 py-2 mt-2">
                    <span>طريقة الدفع</span>
                    <span>💵 الدفع عند الاستلام</span>
                </div>
            </div>

            {{-- CTA buttons --}}
            <div class="fade-up-5 grid grid-cols-2 gap-3 pt-2">
                <a href="{{ route('products.index') }}"
                   class="flex items-center justify-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700
                          font-semibold py-3 rounded-xl text-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    متابعة التسوق
                </a>
                <a href="{{ route('orders.index') }}"
                   class="flex items-center justify-center gap-2 bg-brand-600 hover:bg-brand-700 text-white
                          font-semibold py-3 rounded-xl text-sm transition-colors shadow-lg shadow-brand-600/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    طلباتي
                </a>
            </div>

        </div>
    </div>

</div>
@endsection