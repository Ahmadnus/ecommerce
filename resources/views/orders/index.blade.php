@extends('layouts.app')

@section('title', 'طلباتي — المتجر')

@push('head')
<style>
    /* تنسيق كرت الطلب ليكون متناسق مع قائمة البروفايل */
    .order-item-card {
        @apply flex items-center justify-between p-5 mb-4 rounded-[2.5rem] bg-white transition-all border border-gray-100;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
    }
    .order-item-card:hover {
        @apply border-gray-200;
        background-color: #f8fafc;
    }
    .brand-dot {
        width: 8px;
        height: 24px;
        border-radius: 99px;
        background-color: var(--brand-color);
    }
</style>
@endpush

@section('content')
<div class="max-w-6xl mx-auto px-4 py-12" dir="rtl">

    {{-- الهيدر العلوي --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-6">
        <div class="flex items-center gap-3">
            <div class="brand-dot"></div>
            <h1 class="text-2xl font-black text-gray-900">سجل الطلبات</h1>
        </div>
        
        <a href="{{ route('products.index') }}" 
           class="inline-flex items-center justify-center px-8 py-3 text-white text-sm font-bold rounded-2xl transition-all shadow-lg active:scale-95"
           style="background-color: var(--brand-color);">
            مواصلة التسوق
        </a>
    </div>

    {{-- قائمة الطلبات --}}
    @if($orders->isEmpty())
        <div class="bg-white rounded-[2.5rem] p-12 text-center border border-gray-100 shadow-sm">
            <p class="text-gray-400 font-bold">لا توجد طلبات سابقة حالياً</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($orders as $order)
                <div class="order-item-card group">
                    
                    {{-- معلومات الطلب (نفس تقسيم البروفايل) --}}
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center shadow-sm border border-gray-100 group-hover:bg-white transition-colors">
                            <span class="text-xs font-black text-gray-400">#{{ $order->id }}</span>
                        </div>
                        
                        <div>
                            <p class="text-sm font-black text-gray-900">طلب رقم {{ $order->id }}</p>
                            <p class="text-[11px] text-gray-400 font-bold mt-0.5">
                                🗓️ {{ $order->created_at->format('Y/m/d') }}
                            </p>
                        </div>
                    </div>

                    {{-- السعر والحالة --}}
                    <div class="flex items-center gap-6">
                        <div class="text-left hidden sm:block">
                            <div class="text-sm font-black" style="color: var(--brand-color);">
                                <x-price :amount="$order->total_amount" />
                            </div>
                        </div>

                        <div class="flex flex-col items-end gap-2">
                            {{-- الحالة - استخدمت نفس الـ Logic الموجود في كود البروفايل الخاص بك --}}
                            <span class="text-[10px] px-3 py-1 rounded-lg font-black uppercase {{ $order->status == 'completed' ? 'bg-green-100 text-green-600' : 'bg-orange-100 text-orange-600' }}">
                                {{ $order->status_label }}
                            </span>

                        
                            
                        </div>
                    </div>

                </div>
            @endforeach
        </div>

        {{-- الترقيم --}}
        <div class="mt-8">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection