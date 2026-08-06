@extends('layouts.admin')
@section('title', 'الفروع')

@section('admin-content')
<div class="p-6 space-y-6">

    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold">الفروع ومواقع الاستلام</h1>
            <p class="text-sm text-gray-500 mt-1">نقاط الاستلام والتسليم المتاحة للعملاء</p>
        </div>
        <a href="{{ route('admin.locations.create') }}"
           class="bg-brand text-white px-5 py-2.5 rounded-lg font-semibold text-sm hover:opacity-90 transition">
            <i class="fa-solid fa-plus"></i> إضافة فرع
        </a>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs">
                    <tr>
                        <th class="text-start p-4 font-semibold">الفرع</th>
                        <th class="text-start p-4 font-semibold">النوع</th>
                        <th class="text-start p-4 font-semibold">المدينة</th>
                        <th class="text-start p-4 font-semibold">الدوام</th>
                        <th class="text-start p-4 font-semibold">رسوم الاستلام</th>
                        <th class="text-start p-4 font-semibold">رسوم موقع مختلف</th>
                        <th class="text-start p-4 font-semibold">السيارات</th>
                        <th class="text-start p-4 font-semibold">الحجوزات</th>
                        <th class="text-start p-4 font-semibold">الحالة</th>
                        <th class="text-center p-4 font-semibold"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($locations as $location)
                        <tr class="hover:bg-gray-50">
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <span class="w-9 h-9 rounded-full bg-brand/10 text-brand flex items-center justify-center shrink-0">
                                        <i class="{{ $location->type_icon }}"></i>
                                    </span>
                                    <div>
                                        <p class="font-semibold">{{ $location->name }}</p>
                                        @if($location->code)
                                            <p class="text-xs text-gray-400" dir="ltr">{{ $location->code }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="p-4 text-gray-600">{{ $types[$location->type]['label'] ?? $location->type }}</td>
                            <td class="p-4 text-gray-600">{{ $location->city }}</td>
                            <td class="p-4 text-xs text-gray-500">{{ $location->hours_label }}</td>
                            <td class="p-4">{{ number_format((float) $location->pickup_fee, 2) }}</td>
                            <td class="p-4">{{ number_format((float) $location->one_way_fee, 2) }}</td>
                            <td class="p-4 font-semibold">{{ $location->vehicles_count }}</td>
                            <td class="p-4 font-semibold">{{ $location->pickup_bookings_count }}</td>
                            <td class="p-4">
                                <span class="inline-block text-xs font-semibold px-2.5 py-1 rounded-full
                                             {{ $location->is_active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $location->is_active ? 'مفعّل' : 'معطّل' }}
                                </span>
                            </td>
                            <td class="p-4">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('admin.locations.edit', $location) }}"
                                       class="w-8 h-8 rounded-lg hover:bg-blue-50 text-blue-600 inline-flex items-center justify-center">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.locations.destroy', $location) }}"
                                          onsubmit="return confirm('حذف هذا الفرع؟')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="w-8 h-8 rounded-lg hover:bg-red-50 text-red-600">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="p-16 text-center text-gray-400">
                                <i class="fa-solid fa-location-dot text-4xl block mb-3 opacity-30"></i>
                                لا توجد فروع
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($locations->hasPages())
            <div class="p-4 border-t border-gray-100">{{ $locations->links() }}</div>
        @endif
    </div>
</div>
@endsection
