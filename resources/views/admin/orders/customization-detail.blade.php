{{--
    admin/orders/customization-detail.blade.php
    Standalone full-page view for a single OrderCustomization record.
    Route: GET /admin/order-customizations/{customization}
--}}
@extends('layouts.admin')  {{-- adjust to your admin layout name --}}

@section('title', 'تفاصيل التخصيص #' . $customization->id)

@section('admin-content')

<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-xl font-bold text-gray-900">
            تخصيص #{{ $customization->id }}
        </h1>
        <p class="text-sm text-gray-500 mt-1">
            منتج: {{ $customization->product->name ?? 'غير مرتبط بمنتج' }}
            · طلب: #{{ $customization->order_id ?: 'لم يُسجَّل بعد' }}
        </p>
    </div>
    <a href="{{ url()->previous() }}"
       class="text-sm font-medium text-gray-500 hover:text-gray-800 flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        رجوع
    </a>
</div>

{{-- Reuse the partial --}}
@include('admin.orders.partials.customization', [
    'customization' => $customization,
    'config'        => $config,
    'orderId'       => $customization->order_id,
])

@endsection