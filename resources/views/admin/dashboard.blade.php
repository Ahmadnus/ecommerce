@extends('layouts.admin')
@section('title', 'لوحة التحكم')

@section('admin-content')
@php
    $vehicleStats = [
        ['label' => 'إجمالي السيارات', 'value' => \App\Models\Vehicle::count(),                    'icon' => 'fa-solid fa-car',                'color' => 'blue'],
        ['label' => 'سيارات متاحة',    'value' => \App\Models\Vehicle::where('availability_status', \App\Models\Vehicle::STATUS_AVAILABLE)->count(), 'icon' => 'fa-solid fa-circle-check', 'color' => 'green'],
        ['label' => 'الفروع',          'value' => \App\Models\RentalLocation::count(),             'icon' => 'fa-solid fa-location-dot',       'color' => 'amber'],
        ['label' => 'العملاء',         'value' => \App\Models\User::count(),                       'icon' => 'fa-solid fa-users',             'color' => 'emerald'],
    ];

    $bookingStats = [
        ['label' => 'بانتظار التأكيد', 'value' => \App\Models\Booking::status(\App\Models\Booking::STATUS_PENDING)->count(),   'color' => 'yellow'],
        ['label' => 'مؤكدة',           'value' => \App\Models\Booking::status(\App\Models\Booking::STATUS_CONFIRMED)->count(), 'color' => 'blue'],
        ['label' => 'جارية',           'value' => \App\Models\Booking::status(\App\Models\Booking::STATUS_ACTIVE)->count(),    'color' => 'indigo'],
        ['label' => 'مكتملة',          'value' => \App\Models\Booking::status(\App\Models\Booking::STATUS_COMPLETED)->count(), 'color' => 'green'],
    ];

    $revenue = (float) \App\Models\Booking::whereIn('booking_status', [
        \App\Models\Booking::STATUS_CONFIRMED,
        \App\Models\Booking::STATUS_ACTIVE,
        \App\Models\Booking::STATUS_COMPLETED,
    ])->sum('total_amount');

    $upcoming = \App\Models\Booking::with(['vehicle', 'pickupLocation'])
        ->upcoming()
        ->orderBy('pickup_date_time')
        ->take(8)
        ->get();
@endphp

<div class="p-6 space-y-8">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-black text-gray-900 tracking-tight">نظرة عامة</h2>
            <p class="text-gray-500 font-medium mt-1">مرحباً بك، إليك ما يحدث في منصة التأجير الآن.</p>
        </div>
        <div class="flex items-center gap-3 bg-white p-1.5 rounded-2xl border border-gray-200 shadow-sm">
            <span class="flex items-center gap-2 px-4 py-2 text-sm font-bold text-gray-600">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                النظام يعمل بكفاءة
            </span>
        </div>
    </div>

    {{-- Fleet stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($vehicleStats as $stat)
            <div class="bg-white border border-gray-200 rounded-2xl p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500">{{ $stat['label'] }}</p>
                        <p class="text-3xl font-black mt-1">{{ number_format($stat['value']) }}</p>
                    </div>
                    <span class="w-12 h-12 rounded-full bg-{{ $stat['color'] }}-50 text-{{ $stat['color'] }}-600 flex items-center justify-center">
                        <i class="{{ $stat['icon'] }} text-lg"></i>
                    </span>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Bookings + revenue --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 bg-white border border-gray-200 rounded-2xl p-6">
            <h3 class="font-bold text-lg mb-5">الحجوزات</h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                @foreach($bookingStats as $stat)
                    <div class="rounded-xl bg-{{ $stat['color'] }}-50 p-4 text-center">
                        <p class="text-2xl font-black text-{{ $stat['color'] }}-700">{{ number_format($stat['value']) }}</p>
                        <p class="text-xs text-{{ $stat['color'] }}-700/70 mt-1">{{ $stat['label'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-6 flex flex-col justify-center">
            <p class="text-xs text-gray-500">إجمالي الإيرادات</p>
            <p class="text-3xl font-black text-brand mt-2">{{ number_format($revenue, 2) }} <span class="text-base">ر.س</span></p>
            <a href="{{ route('admin.bookings.index') }}" class="mt-4 text-sm font-semibold text-brand hover:underline">
                عرض كل الحجوزات →
            </a>
        </div>
    </div>

    {{-- Upcoming pickups --}}
    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-lg">الاستلامات القادمة</h3>
            <a href="{{ route('admin.bookings.index') }}" class="text-sm font-semibold text-brand hover:underline">الكل</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs">
                    <tr>
                        <th class="text-start p-4 font-semibold">رقم الحجز</th>
                        <th class="text-start p-4 font-semibold">العميل</th>
                        <th class="text-start p-4 font-semibold">السيارة</th>
                        <th class="text-start p-4 font-semibold">الاستلام</th>
                        <th class="text-start p-4 font-semibold">الحالة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($upcoming as $booking)
                        <tr class="hover:bg-gray-50">
                            <td class="p-4 font-mono text-xs font-semibold" dir="ltr">
                                <a href="{{ route('admin.bookings.show', $booking) }}" class="hover:text-brand">
                                    {{ $booking->booking_reference }}
                                </a>
                            </td>
                            <td class="p-4">{{ $booking->driver_name }}</td>
                            <td class="p-4 text-gray-600">{{ $booking->vehicle?->title ?? '—' }}</td>
                            <td class="p-4 text-xs">
                                {{ $booking->pickup_date_time->format('d/m/Y H:i') }}
                                <span class="text-gray-500">· {{ $booking->pickupLocation?->name ?? '—' }}</span>
                            </td>
                            <td class="p-4">
                                <span class="inline-block text-xs font-semibold px-2.5 py-1 rounded-full
                                             bg-{{ $booking->status_color }}-50 text-{{ $booking->status_color }}-700">
                                    {{ $booking->status_label }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-12 text-center text-gray-400">لا توجد استلامات قادمة</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
