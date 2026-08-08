@extends('layouts.admin')
@section('title', 'أسطول السيارات')

@section('admin-content')
<div class="p-6 space-y-6">

    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold">إدارة أسطول السيارات</h1>
            <p class="text-sm text-gray-500 mt-1">إضافة وتعديل السيارات المتاحة للإيجار</p>
        </div>
        <a href="{{ route('admin.vehicles.create') }}"
           class="bg-brand text-white px-5 py-2.5 rounded-lg font-semibold text-sm hover:opacity-90 transition">
            <i class="fa-solid fa-plus"></i> إضافة سيارة
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['إجمالي السيارات', $stats['total'],       'fa-solid fa-car',           'gray'],
            ['متاحة',           $stats['available'],   'fa-solid fa-circle-check',  'green'],
            ['مؤجرة حالياً',     $stats['rented'],      'fa-solid fa-key',           'blue'],
            ['في الصيانة',      $stats['maintenance'], 'fa-solid fa-screwdriver-wrench', 'yellow'],
        ] as [$label, $value, $icon, $color])
            <div class="bg-white border border-gray-200 rounded-xl p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500">{{ $label }}</p>
                        <p class="text-2xl font-bold mt-1">{{ number_format($value) }}</p>
                    </div>
                    <span class="w-11 h-11 rounded-full bg-{{ $color }}-50 text-{{ $color }}-600 flex items-center justify-center">
                        <i class="{{ $icon }}"></i>
                    </span>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Filters --}}
    <form method="GET" class="bg-white border border-gray-200 rounded-xl p-4 grid grid-cols-1 md:grid-cols-5 gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث بالماركة أو رقم اللوحة…"
               class="rounded-lg border-gray-300 text-sm md:col-span-2">

        <select name="category" class="rounded-lg border-gray-300 text-sm">
            <option value="">كل الفئات</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>

        <select name="status" class="rounded-lg border-gray-300 text-sm">
            <option value="">كل الحالات</option>
            @foreach(\App\Models\Vehicle::statuses() as $key => $status)
                <option value="{{ $key }}" @selected(request('status') === $key)>{{ $status['label'] }}</option>
            @endforeach
        </select>

        <div class="flex gap-2">
            <button type="submit" class="flex-1 bg-brand text-white rounded-lg text-sm font-semibold py-2">تصفية</button>
            <a href="{{ route('admin.vehicles.index') }}"
               class="px-4 py-2 border border-gray-300 rounded-lg text-sm">مسح</a>
        </div>
    </form>

    {{-- Table --}}
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs">
                    <tr>
                        <th class="text-start p-4 font-semibold">السيارة</th>
                        <th class="text-start p-4 font-semibold">الفئة</th>
                        <th class="text-start p-4 font-semibold">المواصفات</th>
                        <th class="text-start p-4 font-semibold">السعر / يوم</th>
                        <th class="text-start p-4 font-semibold">الوحدات</th>
                        <th class="text-start p-4 font-semibold">الحالة</th>
                        <th class="text-start p-4 font-semibold">الفرع</th>
                        <th class="text-center p-4 font-semibold">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($vehicles as $vehicle)
                        <tr class="hover:bg-gray-50">
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-16 h-12 bg-gray-50 rounded-lg shrink-0 flex items-center justify-center overflow-hidden">
                                        @if($vehicle->main_image_url)
                                            <img src="{{ $vehicle->main_image_url }}" alt="" class="w-full h-full object-contain p-0.5">
                                        @else
                                            <i class="fa-solid fa-car text-gray-300"></i>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold">{{ $vehicle->title }}</p>
                                        <p class="text-xs text-gray-500">
                                            {{ $vehicle->year }}
                                            @if($vehicle->registration_number)
                                                · <span dir="ltr">{{ $vehicle->registration_number }}</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4 text-gray-600">{{ $vehicle->category?->name ?? '—' }}</td>
                            <td class="p-4 text-xs text-gray-500">
                                {{ $vehicle->seats }} مقاعد · {{ $vehicle->transmission_label }}<br>
                                {{ $vehicle->fuel_label }} · {{ $vehicle->bags }} حقائب
                            </td>
                            <td class="p-4">
                                @if($vehicle->is_on_sale)
                                    <span class="text-xs text-gray-400 line-through block">{{ number_format((float) $vehicle->daily_rate, 0) }}</span>
                                @endif
                                <span class="font-bold text-brand">{{ number_format($vehicle->effective_daily_rate, 0) }}</span>
                                <span class="text-xs text-gray-500">د.أ</span>
                            </td>
                            <td class="p-4 font-semibold">{{ $vehicle->units_available }}</td>
                            <td class="p-4">
                                <form method="POST" action="{{ route('admin.vehicles.status', $vehicle) }}">
                                    @csrf @method('PATCH')
                                    <select name="availability_status" onchange="this.form.submit()"
                                            class="text-xs rounded-lg border-gray-300 py-1.5
                                                   bg-{{ $vehicle->status_color }}-50 text-{{ $vehicle->status_color }}-700 font-semibold">
                                        @foreach(\App\Models\Vehicle::statuses() as $key => $status)
                                            <option value="{{ $key }}" @selected($vehicle->availability_status === $key)>
                                                {{ $status['label'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
                            <td class="p-4 text-xs text-gray-500">{{ $vehicle->location?->name ?? '—' }}</td>
                            <td class="p-4">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('rental.vehicles.show', $vehicle->slug) }}" target="_blank"
                                       class="w-8 h-8 rounded-lg hover:bg-gray-100 text-gray-500" title="معاينة">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.vehicles.edit', $vehicle) }}"
                                       class="w-8 h-8 rounded-lg hover:bg-blue-50 text-blue-600" title="تعديل">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.vehicles.destroy', $vehicle) }}"
                                          onsubmit="return confirm('حذف هذه السيارة؟')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="w-8 h-8 rounded-lg hover:bg-red-50 text-red-600" title="حذف">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-16 text-center text-gray-400">
                                <i class="fa-solid fa-car-side text-4xl block mb-3 opacity-30"></i>
                                لا توجد سيارات
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($vehicles->hasPages())
            <div class="p-4 border-t border-gray-100">{{ $vehicles->links() }}</div>
        @endif
    </div>
</div>
@endsection
