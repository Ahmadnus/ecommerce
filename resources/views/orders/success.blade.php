{{-- resources/views/orders/success.blade.php --}}

@extends('layouts.app')
@section('title', 'تم تأكيد طلبك ✓')

@push('head')
<style>
    /* ── التحسينات العامة ──────────────────────────────────────────── */
    .success-page {
        background-color: var(--bg-color); /* التوافق مع خلفية الموقع */
        min-height: 90vh;
    }

    .app-card {
        background: white;
        border-radius: 2.5rem; /* زوايا دائرية كبيرة مثل البروفايل */
        border: 1px solid rgba(0,0,0,0.03);
        box-shadow: 0 15px 35px -5px rgba(0, 0, 0, 0.05);
    }

    /* ── أنيميشن علامة الصح (تم تحديث اللون للبراند) ─────────────────── */
    @keyframes circleIn {
        0%   { transform: scale(0) rotate(-30deg); opacity: 0; }
        60%  { transform: scale(1.12) rotate(3deg); }
        100% { transform: scale(1) rotate(0deg); opacity: 1; }
    }
    @keyframes checkDraw {
        from { stroke-dashoffset: 60; }
        to   { stroke-dashoffset: 0; }
    }
    .check-circle {
        background-color: var(--brand-color);
        animation: circleIn .55s cubic-bezier(.16,1,.3,1) .15s both;
    }
    .check-path {
        stroke-dasharray: 60;
        stroke-dashoffset: 60;
        animation: checkDraw .4s ease .65s forwards;
    }

    /* ── أنيميشن الظهور التدريجي ────────────────────────────────────── */
    @keyframes up {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .u1 { animation: up .4s ease .3s  both; }
    .u2 { animation: up .4s ease .45s both; }
    .u3 { animation: up .4s ease .6s  both; }
    .u4 { animation: up .4s ease .75s both; }

    /* ── التايم لاين (مراحل الطلب) ─────────────────────────────────── */
    .timeline-dot-done {
        background: var(--brand-color);
        box-shadow: 0 4px 10px color-mix(in srgb, var(--brand-color) 30%, transparent);
    }
    .timeline-line-done {
        background: var(--brand-color);
    }
</style>
@endpush

@section('content')
<div class="success-page py-10" dir="rtl">
    <div class="max-w-xl mx-auto px-4">

        {{-- ── أيقونة النجاح والرسالة ── --}}
        <div class="text-center mb-10">
            <div class="check-circle inline-flex w-24 h-24 rounded-[2rem] items-center justify-center mb-6 shadow-xl shadow-brand/20">
                <svg class="w-10 h-10" fill="none" stroke="white" viewBox="0 0 24 24">
                    <path class="check-path" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h1 class="u1 text-3xl font-black text-gray-900 mb-2">طلبك وصل عندنا!</h1>
            <p class="u2 text-gray-500 font-medium">شكراً لثقتك بنا، طلبك الآن قيد المراجعة وسنتواصل معك قريباً.</p>
        </div>

        {{-- ── الكرت الرئيسي للطلب ── --}}
        <div class="u3 app-card overflow-hidden mb-6">
            
            {{-- رأس الكرت: رقم الطلب --}}
            <div class="p-6 border-b border-gray-50 flex items-center justify-between bg-gray-50/50">
                <div>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">رقم التتبع</span>
                    <span class="text-xl font-black text-gray-900 tracking-tighter">#{{ $order->order_number }}</span>
                </div>
                <div class="px-4 py-2 rounded-2xl text-xs font-bold shadow-sm" 
                     style="background-color: white; color: var(--brand-color); border: 1px solid color-mix(in srgb, var(--brand-color) 10%, transparent);">
                    {{ $order->status_label }}
                </div>
            </div>

            {{-- تفاصيل المنتجات --}}
            <div class="p-6 space-y-4">
                <h3 class="text-sm font-bold text-gray-400 mb-4">ملخص المنتجات</h3>
                @foreach($order->items as $item)
                <div class="flex items-center justify-between gap-4">
                    <div class="flex-1">
                        <p class="text-sm font-bold text-gray-800 line-clamp-1">{{ $item->product_name }}</p>
                        <p class="text-[11px] text-gray-400">{{ $item->quantity }} قطعة</p>
                    </div>
                    <div class="text-sm font-bold text-gray-900">
                        <x-price :amount="$item->total_price" />
                    </div>
                </div>
                @endforeach
            </div>

            {{-- الحساب النهائي --}}
            <div class="p-6 bg-gray-50/30 space-y-3">
                <div class="flex justify-between text-sm text-gray-500">
                    <span>التوصيل</span>
                    <span class="font-bold {{ $order->shipping_amount == 0 ? 'text-green-600' : '' }}">
                        {{ $order->shipping_amount == 0 ? 'مجاني' : '' }}
                        @if($order->shipping_amount > 0) <x-price :amount="$order->shipping_amount" /> @endif
                    </span>
                </div>
                <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                    <span class="font-bold text-gray-900">الإجمالي الصافي</span>
                    <span class="text-2xl font-black" style="color: var(--brand-color);">
                        <x-price :amount="$order->total_amount" />
                    </span>
                </div>
            </div>

            {{-- طريقة الدفع --}}
            <div class="px-6 py-4 flex items-center gap-3 bg-white border-t border-gray-50">
                <div class="w-10 h-10 rounded-xl bg-orange-50 flex items-center justify-center text-orange-600">
                    💵
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-900">الدفع عند الاستلام</p>
                    <p class="text-[10px] text-gray-400">يرجى تجهيز المبلغ عند وصول المندوب</p>
                </div>
            </div>
        </div>

        {{-- ── مراحل الطلب (Timeline) ── --}}
        <div class="u4 app-card p-8 mb-8">
            <h3 class="text-sm font-bold text-gray-400 mb-8 text-center uppercase tracking-widest">تتبع حالة الطلب</h3>
            
            @php
                $stages = [
                    'pending'    => ['label' => 'بانتظار التأكيد', 'icon' => '🕒'],
                    'processing' => ['label' => 'قيد التجهيز', 'icon' => '📦'],
                    'shipped'    => ['label' => 'خرج للتوصيل', 'icon' => '🚚'],
                    'delivered'  => ['label' => 'تم الاستلام', 'icon' => '🏠'],
                ];
                $statusOrder = array_keys($stages);
                $currentIdx  = array_search($order->status, $statusOrder) ?? 0;
            @endphp

            <div class="relative flex justify-between items-start">
                @foreach($stages as $key => $data)
                    @php 
                        $idx = array_search($key, $statusOrder); 
                        $isDone = $idx <= $currentIdx; 
                    @endphp
                    
                    <div class="flex flex-col items-center flex-1 z-10">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm mb-2 transition-all duration-500 {{ $isDone ? 'timeline-dot-done text-white' : 'bg-gray-100 text-gray-400' }}">
                            @if($isDone && $key !== $order->status)
                                ✓
                            @else
                                {{ $data['icon'] }}
                            @endif
                        </div>
                        <span class="text-[10px] font-bold text-center {{ $isDone ? 'text-gray-900' : 'text-gray-400' }}">
                            {{ $data['label'] }}
                        </span>
                    </div>

                    {{-- الخط الواصل --}}
                    @if(!$loop->last)
                        <div class="absolute top-5 right-[calc(12.5%+20px)] left-[calc(12.5%+20px)] h-[2px] bg-gray-100 -z-0">
                            <div class="h-full transition-all duration-1000 {{ $isDone && array_search($statusOrder[$idx+1], $statusOrder) <= $currentIdx ? 'timeline-line-done' : '' }}" 
                                 style="width: {{ $isDone && ($idx < $currentIdx) ? '100%' : '0%' }}"></div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        {{-- ── أزرار التحكم ── --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <a href="{{ route('orders.index') }}" 
               class="flex items-center justify-center gap-2 py-4 px-6 rounded-2xl font-bold text-white shadow-lg active:scale-95 transition-all"
               style="background-color: var(--brand-color);">
                <span>تتبع طلباتي</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke-width="2" stroke-linecap="round"/></svg>
            </a>
            <a href="/" 
               class="flex items-center justify-center gap-2 py-4 px-6 rounded-2xl font-bold bg-white text-gray-700 border border-gray-200 hover:bg-gray-50 active:scale-95 transition-all">
                <span>الرئيسية</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" stroke-width="2" stroke-linecap="round"/></svg>
            </a>
        </div>

    </div>
</div>
@endsection