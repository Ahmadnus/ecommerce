@extends('layouts.admin')
@section('title', 'جداول التوصيل — ' . $zone->name)

@section('admin-content')

{{-- Breadcrumb --}}
<div class="flex items-center gap-2 text-sm text-gray-400 mb-6 flex-wrap">
    <a href="{{ route('admin.countries.index') }}"
       class="hover:text-brand transition-colors font-semibold">الدول</a>
    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
    </svg>
    @if($zone->country)
    <a href="{{ route('admin.countries.zones.index', $zone->country) }}"
       class="hover:text-brand transition-colors font-semibold">{{ $zone->country->name }}</a>
    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
    </svg>
    @endif
    <span class="text-gray-700 font-semibold">{{ $zone->name }}</span>
    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
    </svg>
    <span class="text-gray-500">جداول التوصيل</span>
</div>

{{-- Header --}}
<div class="flex items-start justify-between gap-4 mb-6 flex-wrap">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">جداول التوصيل</h1>
        <p class="text-gray-500 text-sm mt-1">
            منطقة: <span class="font-bold text-gray-700">{{ $zone->name }}</span>
            @if($zone->country)
                — {{ $zone->country->name }}
            @endif
        </p>
    </div>
    <a href="{{ route('admin.zones.schedules.create', $zone) }}"
       class="inline-flex items-center gap-2 bg-brand text-white px-5 py-2.5 rounded-xl
              font-bold text-sm hover:opacity-90 active:scale-95 transition-all shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
        </svg>
        إضافة جدول جديد
    </a>
</div>

{{-- Quick-action banners --}}
@if(! $hasCurrentMonth)
<div class="flex items-center gap-3 bg-amber-50 border border-amber-200 rounded-2xl px-5 py-4 mb-5">
    <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
    </svg>
    <div class="flex-1">
        <p class="text-sm font-bold text-amber-800">لا يوجد جدول للشهر الحالي</p>
        <p class="text-xs text-amber-600 mt-0.5">
            العملاء لن يروا أيام توصيل محددة هذا الشهر.
            سيظهر الوقت الافتراضي للمنطقة إن وجد.
        </p>
    </div>
    <a href="{{ route('admin.zones.schedules.create', ['zone' => $zone->id, 'month' => $currentMonth]) }}"
       class="flex-shrink-0 text-xs font-bold px-4 py-2 bg-amber-500 text-white rounded-xl
              hover:bg-amber-600 transition-colors">
        إضافة الآن
    </a>
</div>
@endif

@if(! $hasNextMonth)
<div class="flex items-center gap-3 bg-blue-50 border border-blue-100 rounded-2xl px-5 py-4 mb-5">
    <svg class="w-5 h-5 text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
    </svg>
    <div class="flex-1">
        <p class="text-sm font-bold text-blue-800">الشهر القادم ليس له جدول بعد</p>
        <p class="text-xs text-blue-600 mt-0.5">يمكنك إضافة جدول مسبقاً أو نسخ الجدول الحالي.</p>
    </div>
    <a href="{{ route('admin.zones.schedules.create', ['zone' => $zone->id, 'month' => $nextMonth]) }}"
       class="flex-shrink-0 text-xs font-bold px-4 py-2 bg-blue-500 text-white rounded-xl
              hover:bg-blue-600 transition-colors">
        إضافة
    </a>
</div>
@endif

{{-- Schedules list --}}
@if($schedules->isEmpty())
<div class="bg-white border border-dashed border-gray-200 rounded-2xl p-16 text-center">
    <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
    </svg>
    <p class="text-gray-500 font-semibold text-sm">لا توجد جداول توصيل بعد</p>
    <p class="text-gray-400 text-xs mt-1 mb-4">أضف جدولاً لتحديد أيام التوصيل المتاحة لكل شهر.</p>
    <a href="{{ route('admin.zones.schedules.create', $zone) }}"
       class="inline-flex items-center gap-2 bg-brand text-white px-5 py-2.5 rounded-xl
              font-bold text-sm hover:opacity-90 transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
        </svg>
        إضافة أول جدول
    </a>
</div>

@else
<div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">
            {{ $schedules->count() }} جدول
        </span>
        <span class="text-xs text-gray-400">مرتب من الأحدث للأقدم</span>
    </div>

    <div class="divide-y divide-gray-100">
        @foreach($schedules as $schedule)
        @php $badge = $schedule->statusBadge(); @endphp
        <div class="flex items-center gap-4 px-5 py-4 hover:bg-gray-50/60 transition-colors group flex-wrap">

            {{-- Month label + status --}}
            <div class="min-w-[140px]">
                <p class="font-bold text-gray-900 text-sm">{{ $schedule->monthLabel() }}</p>
                <span class="inline-block mt-1 text-[10px] font-bold px-2 py-0.5 rounded-full
                    {{ $badge['color'] === 'green' ? 'bg-green-100 text-green-700' :
                       ($badge['color'] === 'blue'  ? 'bg-blue-100 text-blue-700'  :
                                                      'bg-gray-100 text-gray-500') }}">
                    {{ $badge['label'] }}
                </span>
            </div>

            {{-- Delivery days --}}
            <div class="flex-1 min-w-[120px]">
                @if($schedule->delivery_days)
                <div class="flex items-center gap-1.5 text-xs text-gray-600">
                    <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-semibold">{{ $schedule->deliveryLabel() }}</span>
                </div>
                @else
                <span class="text-xs text-gray-400">لا يوجد وقت توصيل</span>
                @endif
            </div>

            {{-- Available days --}}
            <div class="flex-1 min-w-[160px]">
                <p class="text-xs text-gray-500 leading-relaxed line-clamp-2">
                    <span class="font-semibold text-gray-700">الأيام:</span>
                    {{ $schedule->daysDisplay() }}
                </p>
                <p class="text-[10px] text-gray-400 mt-0.5">
                    {{ $schedule->availableDayCount() }} يوم متاح
                </p>
            </div>

            {{-- Notes --}}
            @if($schedule->notes)
            <div class="flex-1 min-w-[120px]">
                <p class="text-xs text-gray-400 italic line-clamp-1">{{ $schedule->notes }}</p>
            </div>
            @endif

            {{-- Active toggle badge --}}
            <div class="flex-shrink-0">
                @if($schedule->is_active)
                <span class="w-2 h-2 bg-green-400 rounded-full inline-block" title="نشط"></span>
                @else
                <span class="w-2 h-2 bg-gray-300 rounded-full inline-block" title="معطّل"></span>
                @endif
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0">

                {{-- Duplicate → next month --}}
                <form action="{{ route('admin.zones.schedules.duplicate', [$zone, $schedule]) }}"
                      method="POST" class="inline">
                    @csrf
                    <button type="submit"
                            title="نسخ إلى الشهر القادم"
                            class="p-1.5 text-gray-400 hover:text-brand hover:bg-gray-100 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </button>
                </form>

                <a href="{{ route('admin.zones.schedules.edit', [$zone, $schedule]) }}"
                   class="p-1.5 text-blue-400 hover:bg-blue-50 rounded-lg transition-colors"
                   title="تعديل">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                </a>

                <form action="{{ route('admin.zones.schedules.destroy', [$zone, $schedule]) }}"
                      method="POST"
                      onsubmit="return confirm('حذف جدول {{ $schedule->monthLabel() }}؟')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="p-1.5 text-red-400 hover:bg-red-50 rounded-lg transition-colors"
                            title="حذف">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </form>
            </div>

        </div>
        @endforeach
    </div>
</div>
@endif

@endsection