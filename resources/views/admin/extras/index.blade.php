@extends('layouts.admin')
@section('title', 'الإضافات الاختيارية')

@section('admin-content')
<div class="p-6 space-y-6">

    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold">الإضافات الاختيارية</h1>
            <p class="text-sm text-gray-500 mt-1">
                الخدمات اللي بتظهر للعميل في صفحة الحجز — التأمين، سائق إضافي، جهاز ملاحة…
            </p>
        </div>
        <a href="{{ route('admin.extras.create') }}"
           class="bg-brand text-white px-5 py-2.5 rounded-lg font-semibold text-sm hover:opacity-90 transition">
            <i class="fa-solid fa-plus"></i> إضافة خدمة
        </a>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs">
                    <tr>
                        <th class="text-start p-4 font-semibold">الخدمة</th>
                        <th class="text-start p-4 font-semibold">المعرّف</th>
                        <th class="text-start p-4 font-semibold">السعر</th>
                        <th class="text-start p-4 font-semibold">الاحتساب</th>
                        <th class="text-start p-4 font-semibold">الترتيب</th>
                        <th class="text-center p-4 font-semibold">الحالة</th>
                        <th class="text-center p-4 font-semibold"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($extras as $extra)
                        <tr class="hover:bg-gray-50">
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <span class="w-9 h-9 rounded-full bg-brand/10 text-brand flex items-center justify-center shrink-0">
                                        <i class="{{ $extra->icon }}"></i>
                                    </span>
                                    <div>
                                        <div class="font-bold text-gray-800">{{ $extra->getTranslation('name', 'ar', false) }}</div>
                                        <div class="text-xs text-gray-400" dir="ltr">{{ $extra->getTranslation('name', 'en', false) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4">
                                <code class="text-xs bg-gray-100 px-2 py-1 rounded" dir="ltr">{{ $extra->code }}</code>
                            </td>
                            <td class="p-4 font-bold text-brand">
                                {{ number_format((float) $extra->price, 2) }}
                                <span class="text-xs font-normal text-gray-500">د.أ</span>
                            </td>
                            <td class="p-4 text-gray-500">
                                {{ $extra->per_day ? 'لكل يوم' : 'مرة واحدة' }}
                            </td>
                            <td class="p-4 text-gray-500">{{ $extra->sort_order }}</td>
                            <td class="p-4 text-center">
                                {{-- Enabling/disabling is stored, so a disabled
                                     add-on disappears from the booking form. --}}
                                <form action="{{ route('admin.extras.toggle', $extra) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="{{ $extra->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}
                                                   text-xs px-3 py-1 rounded-full font-bold hover:opacity-80 transition">
                                        {{ $extra->is_active ? 'مفعّلة' : 'معطّلة' }}
                                    </button>
                                </form>
                            </td>
                            <td class="p-4">
                                <div class="flex items-center justify-center gap-3">
                                    <a href="{{ route('admin.extras.edit', $extra) }}"
                                       class="text-brand hover:underline text-sm">تعديل</a>
                                    <form action="{{ route('admin.extras.destroy', $extra) }}" method="POST"
                                          onsubmit="return confirm('حذف هذه الخدمة؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-500 hover:text-red-700 text-sm">حذف</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-10 text-center text-gray-400 text-sm">
                                ما في خدمات مضافة. لما يكون الجدول فاضي بترجع الأربع خدمات الافتراضية تلقائياً.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $extras->links() }}
</div>
@endsection
