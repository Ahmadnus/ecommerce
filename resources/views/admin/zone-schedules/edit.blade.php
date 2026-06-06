@extends('layouts.admin')
@section('title', 'تعديل جدول — ' . $schedule->monthLabel())

@section('admin-content')
<div class="max-w-2xl mx-auto">

    <div class="mb-6">
        <a href="{{ route('admin.zones.schedules.index', $zone) }}"
           class="inline-flex items-center gap-1.5 text-sm font-semibold text-gray-500 hover:text-brand transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            جداول {{ $zone->name }}
        </a>
    </div>

    <div class="mb-6 flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">تعديل جدول: {{ $schedule->monthLabel() }}</h1>
            <p class="text-sm text-gray-500 mt-1">
                منطقة: <span class="font-bold text-gray-700">{{ $zone->name }}</span>
            </p>
        </div>

        @php $badge = $schedule->statusBadge(); @endphp
        <span class="inline-block text-xs font-bold px-3 py-1.5 rounded-full flex-shrink-0
            {{ $badge['color'] === 'green' ? 'bg-green-100 text-green-700' :
               ($badge['color'] === 'blue'  ? 'bg-blue-100 text-blue-700'  :
                                              'bg-gray-100 text-gray-500') }}">
            {{ $badge['label'] }}
        </span>
    </div>

    @php $isEdit = true; $copyFrom = null; $defaultMonth = $schedule->month; @endphp
    @include('admin.zone-schedules._form')

</div>
@endsection