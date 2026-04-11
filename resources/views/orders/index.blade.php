@extends('layouts.app')

@section('title', 'طلباتي — المتجر')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10" dir="rtl">

    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">سجل الطلبات</h1>
            <p class="text-sm text-gray-500 mt-1">تتبع حالة طلباتك الحالية والسابقة</p>
        </div>
        <a href="{{ route('products.index') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-bold rounded-2xl transition-all shadow-sm shadow-brand-100">
            مواصلة التسوق
        </a>
    </div>

    {{-- Orders List --}}
    @if($orders->isEmpty())
        <div class="bg-white rounded-3xl border-2 border-dashed border-gray-100 py-16 text-center">
            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900">لا توجد طلبات بعد</h3>
            <p class="text-gray-500 mt-2 max-w-xs mx-auto text-sm">يبدو أنك لم تقم بطلب أي منتج حتى الآن. ابدأ بإضافة المنتجات لسلتك!</p>
        </div>
    @else
        <div class="grid gap-4">
            @foreach($orders as $order)
                <div class="group bg-white rounded-2xl border border-gray-100 p-4 sm:p-6 hover:border-brand-200 hover:shadow-xl hover:shadow-gray-50 transition-all duration-300">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                        
                        {{-- Order Info --}}
                        <div class="flex items-start gap-4">
                            <div class="hidden sm:flex w-14 h-14 bg-brand-50 rounded-2xl items-center justify-center text-brand-600 flex-shrink-0">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-lg font-bold text-gray-900 group-hover:text-brand-600 transition-colors">
                                        {{ $order->order_number }}
                                    </span>
                                </div>
                                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-gray-500">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        {{ $order->created_at->format('Y/m/d') }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                        {{ $order->items->count() }} منتجات
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Status & Total --}}
                        <div class="flex items-center justify-between lg:justify-end gap-8 border-t lg:border-t-0 pt-4 lg:pt-0">
                            
                            {{-- المبلغ --}}
                            <div class="text-right">
                                <p class="text-[11px] text-gray-400 font-bold uppercase tracking-widest mb-1">إجمالي المبلغ</p>
                                <p class="text-xl font-black text-gray-900">
                                    ${{ number_format($order->total_amount, 2) }}
                                </p>
                            </div>

                            {{-- الحالة --}}
                            <div class="flex flex-col items-end gap-2">
                                @php
                                    $statusColor = $order->status_color; // من الـ Model الخاص بك
                                    $tailwindColors = [
                                        'yellow' => 'bg-amber-50 text-amber-700 border-amber-100',
                                        'blue'   => 'bg-blue-50 text-blue-700 border-blue-100',
                                        'purple' => 'bg-purple-50 text-purple-700 border-purple-100',
                                        'green'  => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                        'red'    => 'bg-rose-50 text-rose-700 border-rose-100',
                                    ];
                                    $colorClass = $tailwindColors[$statusColor] ?? 'bg-gray-50 text-gray-600 border-gray-100';
                                @endphp
                                <span class="px-4 py-1.5 rounded-xl text-xs font-bold border {{ $colorClass }}">
                                    {{ $order->status_label }}
                                </span>
                                
                                <a href="{{ route('orders.success', $order->order_number) }}" class="text-xs font-bold text-brand-600 hover:text-brand-700 underline underline-offset-4">
                                    تفاصيل الطلب
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-10">
            {{ $orders->links() }}
        </div>
    @endif

</div>

<style>
    /* إضافة لمسة جمالية للـ Brand Color إذا لم تكن معرفة */
    :root {
        --brand-600: #2563eb; 
        --brand-700: #1d4ed8;
    }
    .bg-brand-600 { background-color: var(--brand-600); }
    .bg-brand-700 { background-color: var(--brand-700); }
    .text-brand-600 { color: var(--brand-600); }
    .border-brand-200 { border-color: #bfdbfe; }
</style>
@endsection