@extends('layouts.admin')
@section('title', 'إضافة جدول توصيل — ' . $zone->name)

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

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">إضافة جدول توصيل جديد</h1>
        <p class="text-sm text-gray-500 mt-1">
            منطقة: <span class="font-bold text-gray-700">{{ $zone->name }}</span>
        </p>
    </div>

    @php $isEdit = false; $schedule = null; @endphp
    @include('admin.zone-schedules._form')

</div>
@endsection