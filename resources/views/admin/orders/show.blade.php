@extends('layouts.admin')
@section('title', 'تفاصيل الطلب #' . $order->order_number)

@section('admin-content')
<div dir="rtl">

    {{-- Back --}}
    <div class="mb-6">
        <a href="{{ route('admin.orders.index') }}"
           class="inline-flex items-center gap-2 text-sm font-semibold text-gray-500 hover:text-gray-800 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            العودة للطلبات
        </a>
    </div>

    @if(session('success'))
    <div class="mb-5 p-4 bg-green-50 border border-green-100 rounded-xl text-sm text-green-700 font-semibold flex items-center gap-2">
        <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- ── Order header ──────────────────────────────────────────────────────── --}}
    <div class="bg-white border border-gray-200 rounded-2xl p-6 mb-5 shadow-sm">
        <div class="flex items-start justify-between flex-wrap gap-4">
            <div>
                <div class="flex items-center gap-3 mb-1 flex-wrap">
                    <h1 class="text-xl font-black text-gray-800">#{{ $order->order_number }}</h1>
                    @php
                        $statusClasses = [
                            'pending'    => 'bg-amber-100 text-amber-700',
                            'confirmed'  => 'bg-blue-100 text-blue-700',
                            'processing' => 'bg-purple-100 text-purple-700',
                            'shipped'    => 'bg-indigo-100 text-indigo-700',
                            'delivered'  => 'bg-emerald-100 text-emerald-700',
                            'cancelled'  => 'bg-red-100 text-red-600',
                        ];
                        $cls = $statusClasses[$order->status] ?? 'bg-gray-100 text-gray-600';
                    @endphp
                    <span class="text-[10px] font-black px-2.5 py-1 rounded-full {{ $cls }}">
                        {{ $order->status }}
                    </span>
                </div>
                <p class="text-sm text-gray-400">{{ $order->created_at->format('d/m/Y — H:i') }}</p>
            </div>

            {{-- Status update form --}}
            
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- ── LEFT: Items ───────────────────────────────────────────────────── --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Items card --}}
            <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="font-bold text-gray-800 text-sm">
                        المنتجات المطلوبة
                        <span class="text-gray-400 font-normal">
                            ({{ $order->items->count() }} {{ $order->items->count() === 1 ? 'منتج' : 'منتجات' }})
                        </span>
                    </h2>
                </div>

                <div class="divide-y divide-gray-50">
                    @foreach($order->items as $item)
                    @php
                        $img = $item->product?->getFirstMediaUrl('products');
                        $attrs = $item->productVariant?->attributeValues ?? collect();
                    @endphp
                    <div class="flex items-start gap-4 px-5 py-4">

                        {{-- Thumbnail --}}
                        @if($img)
                        <div class="w-16 h-16 rounded-xl overflow-hidden bg-gray-100 border border-gray-100 flex-shrink-0">
                            <img src="{{ $img }}" class="w-full h-full object-cover" alt="{{ $item->product_name }}">
                        </div>
                        @else
                        <div class="w-16 h-16 rounded-xl bg-gray-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        @endif

                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800 leading-snug">
                                {{ $item->product_name }}
                            </p>

                            {{-- ══ ATTRIBUTE BADGES ══════════════════════════════════════════
                                 Shows each selected attribute as a coloured pill.
                                 Requires eager-load: items.productVariant.attributeValues.attribute
                            ════════════════════════════════════════════════════════════════ --}}
                            @if($attrs->isNotEmpty())
                            <div class="flex flex-wrap gap-1.5 mt-1.5">
                                @foreach($attrs as $av)
                                <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold
                                             px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">
                                    @if($av->color_hex)
                                    <span class="w-3 h-3 rounded-full inline-block flex-shrink-0 border border-white shadow-sm"
                                          style="background:{{ $av->color_hex }}"></span>
                                    @endif
                                    <span class="text-gray-400">{{ $av->attribute->name }}:</span>
                                    {{ $av->display_label }}
                                </span>
                                @endforeach
                            </div>
                            @endif

                            <p class="text-xs text-gray-400 mt-1.5 font-mono">
                                {{ $item->quantity }} × {{ number_format($item->unit_price, 2) }}
                                {{ $activeCurrency->symbol ?? 'د.أ' }}
                            </p>
                        </div>

                        <p class="text-sm font-bold text-gray-800 tabular-nums flex-shrink-0 font-mono">
                            {{ number_format($item->total_price, 2) }}
                            {{ $activeCurrency->symbol ?? 'د.أ' }}
                        </p>
                    </div>
                    @endforeach
                </div>

                {{-- Totals --}}
                <div class="border-t border-gray-100 px-5 py-4 space-y-2 text-sm">
                    <div class="flex justify-between text-gray-500">
                        <span>المجموع الفرعي</span>
                        <span class="font-semibold text-gray-800 tabular-nums font-mono">
                            {{ number_format($order->subtotal, 2) }} {{ $activeCurrency->symbol ?? 'د.أ' }}
                        </span>
                    </div>

                    {{-- Delivery fee (tax_amount column) --}}
                    @php $deliveryFee = (float)($order->tax_amount ?? 0); @endphp
                    <div class="flex justify-between text-gray-500">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            رسوم التوصيل
                            @if($order->shipping_area)
                            <span class="text-[10px] bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded-full">
                                {{ $order->shipping_area }}
                            </span>
                            @endif
                        </span>
                        <span class="font-semibold tabular-nums font-mono {{ $deliveryFee == 0 ? 'text-emerald-600' : 'text-gray-800' }}">
                            @if($deliveryFee == 0)
                                مجاني
                            @else
                                {{ number_format($deliveryFee, 2) }} {{ $activeCurrency->symbol ?? 'د.أ' }}
                            @endif
                        </span>
                    </div>

                    @if($order->delivery_days)
                    <div class="flex justify-between text-gray-400 text-xs">
                        <span>وقت التوصيل المقدر</span>
                        <span class="font-semibold text-gray-600">{{ $order->delivery_days }} أيام عمل</span>
                    </div>
                    @endif
                </div>

                <div class="border-t border-gray-100 px-5 py-3 flex justify-between items-center">
                    <span class="font-bold text-gray-800">الإجمالي الكلي</span>
                    <span class="text-xl font-black text-gray-900 tabular-nums font-mono">
                        {{ number_format($order->total_amount, 2) }} {{ $activeCurrency->symbol ?? 'د.أ' }}
                    </span>
                </div>
            </div>

        </div>

        {{-- ── RIGHT: Customer + Shipping ───────────────────────────────────── --}}
        <div class="space-y-5">

            {{-- Shipping address --}}
            <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="font-bold text-gray-800 text-sm">عنوان التوصيل</h2>
                </div>
                <div class="px-5 py-4 space-y-1.5 text-sm">
                    <p class="font-semibold text-gray-800">{{ $order->shipping_name }}</p>
                    <p class="text-gray-500">{{ $order->shipping_phone }}</p>
                    <p class="text-gray-500">{{ $order->shipping_address }}</p>
                    <p class="text-gray-500">
                        {{ $order->shipping_city }}
                        @if($order->shipping_zip), {{ $order->shipping_zip }}@endif
                    </p>
                    @if($order->shipping_area)
                    <span class="inline-block text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100 px-2 py-0.5 rounded-full mt-1">
                        {{ $order->shipping_area }}
                    </span>
                    @endif
                </div>
            </div>

            {{-- Payment + notes --}}
            <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="font-bold text-gray-800 text-sm">الدفع والملاحظات</h2>
                </div>
                <div class="px-5 py-4 space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-400">طريقة الدفع</span>
                        <span class="font-semibold text-gray-800">
                            {{ $order->payment_method === 'cod' ? 'عند الاستلام' : $order->payment_method }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">حالة الدفع</span>
                        <span class="font-semibold text-gray-800">{{ $order->payment_status }}</span>
                    </div>
                    @if($order->notes)
                    <div class="pt-2 border-t border-gray-100">
                        <p class="text-gray-400 text-xs mb-1">ملاحظات العميل</p>
                        <p class="text-gray-600 leading-relaxed">{{ $order->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection