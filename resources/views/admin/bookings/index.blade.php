@extends('layouts.admin')
@section('title', 'الحجوزات')

@section('admin-content')
<div class="p-6 space-y-6">

    <div>
        <h1 class="text-2xl font-bold">إدارة الحجوزات والتأجير</h1>
        <p class="text-sm text-gray-500 mt-1">متابعة الحجوزات الجارية والمكتملة وتواريخ الاستلام والتسليم</p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        @foreach([
            ['بانتظار التأكيد', number_format($stats['pending']),   'fa-solid fa-clock',         'yellow'],
            ['مؤكدة',          number_format($stats['confirmed']), 'fa-solid fa-circle-check',  'blue'],
            ['جارية',          number_format($stats['active']),    'fa-solid fa-key',           'indigo'],
            ['مكتملة',         number_format($stats['completed']), 'fa-solid fa-flag-checkered','green'],
            ['الإيرادات',      number_format($stats['revenue'], 0) . ' ر.س', 'fa-solid fa-sack-dollar', 'emerald'],
        ] as [$label, $value, $icon, $color])
            <div class="bg-white border border-gray-200 rounded-xl p-4">
                <div class="flex items-center justify-between gap-2">
                    <div class="min-w-0">
                        <p class="text-xs text-gray-500">{{ $label }}</p>
                        <p class="text-xl font-bold mt-1 truncate">{{ $value }}</p>
                    </div>
                    <span class="w-10 h-10 shrink-0 rounded-full bg-{{ $color }}-50 text-{{ $color }}-600 flex items-center justify-center">
                        <i class="{{ $icon }}"></i>
                    </span>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Filters --}}
    <form method="GET" class="bg-white border border-gray-200 rounded-xl p-4 grid grid-cols-1 md:grid-cols-6 gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="رقم الحجز / الاسم / الجوال"
               class="rounded-lg border-gray-300 text-sm md:col-span-2">

        <select name="status" class="rounded-lg border-gray-300 text-sm">
            <option value="">كل الحالات</option>
            @foreach($statuses as $key => $status)
                <option value="{{ $key }}" @selected(request('status') === $key)>{{ $status['label'] }}</option>
            @endforeach
        </select>

        <select name="location" class="rounded-lg border-gray-300 text-sm">
            <option value="">كل الفروع</option>
            @foreach($locations as $location)
                <option value="{{ $location->id }}" @selected(request('location') == $location->id)>{{ $location->name }}</option>
            @endforeach
        </select>

        <input type="date" name="from" value="{{ request('from') }}" aria-label="من تاريخ"
               class="rounded-lg border-gray-300 text-sm">
        <input type="date" name="to" value="{{ request('to') }}" aria-label="إلى تاريخ"
               class="rounded-lg border-gray-300 text-sm">

        <div class="md:col-span-6 flex gap-2">
            <button type="submit" class="bg-brand text-white rounded-lg text-sm font-semibold px-6 py-2">تصفية</button>
            <a href="{{ route('admin.bookings.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm">مسح</a>
        </div>
    </form>

    {{-- Table --}}
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs">
                    <tr>
                        <th class="text-start p-4 font-semibold">رقم الحجز</th>
                        <th class="text-start p-4 font-semibold">العميل</th>
                        <th class="text-start p-4 font-semibold">السيارة</th>
                        <th class="text-start p-4 font-semibold">الاستلام</th>
                        <th class="text-start p-4 font-semibold">التسليم</th>
                        <th class="text-start p-4 font-semibold">المدة</th>
                        <th class="text-start p-4 font-semibold">الإجمالي</th>
                        <th class="text-start p-4 font-semibold">الحالة</th>
                        <th class="text-center p-4 font-semibold"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($bookings as $booking)
                        <tr class="hover:bg-gray-50">
                            <td class="p-4 font-mono font-semibold text-xs" dir="ltr">{{ $booking->booking_reference }}</td>
                            <td class="p-4">
                                <p class="font-semibold">{{ $booking->driver_name }}</p>
                                <p class="text-xs text-gray-500" dir="ltr">{{ $booking->driver_phone }}</p>
                            </td>
                            <td class="p-4 text-gray-600">{{ $booking->vehicle?->title ?? '—' }}</td>
                            <td class="p-4 text-xs">
                                <p class="font-semibold">{{ $booking->pickup_date_time->format('d/m/Y') }}</p>
                                <p class="text-gray-500">{{ $booking->pickup_date_time->format('H:i') }} · {{ $booking->pickupLocation?->name ?? '—' }}</p>
                            </td>
                            <td class="p-4 text-xs">
                                <p class="font-semibold">{{ $booking->return_date_time->format('d/m/Y') }}</p>
                                <p class="text-gray-500">{{ $booking->return_date_time->format('H:i') }} · {{ $booking->returnLocation?->name ?? '—' }}</p>
                            </td>
                            <td class="p-4">{{ $booking->total_days }} يوم</td>
                            <td class="p-4 font-bold text-brand">{{ number_format((float) $booking->total_amount, 2) }}</td>
                            <td class="p-4">
                                <span class="inline-block text-xs font-semibold px-2.5 py-1 rounded-full
                                             bg-{{ $booking->status_color }}-50 text-{{ $booking->status_color }}-700">
                                    {{ $booking->status_label }}
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                <a href="{{ route('admin.bookings.show', $booking) }}"
                                   class="inline-flex w-8 h-8 rounded-lg hover:bg-gray-100 text-gray-600 items-center justify-center" title="تفاصيل">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-16 text-center text-gray-400">
                                <i class="fa-regular fa-calendar-xmark text-4xl block mb-3 opacity-30"></i>
                                لا توجد حجوزات
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bookings->hasPages())
            <div class="p-4 border-t border-gray-100">{{ $bookings->links() }}</div>
        @endif
    </div>
</div>
@endsection
