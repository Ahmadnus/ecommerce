@extends('layouts.admin')
@section('title', 'فئات السيارات')

@section('admin-content')
<div class="p-6 space-y-6">

    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold">فئات السيارات</h1>
            <p class="text-sm text-gray-500 mt-1">اقتصادية، سيدان، دفع رباعي، فاخرة…</p>
        </div>
        <a href="{{ route('admin.vehicle-categories.create') }}"
           class="bg-brand text-white px-5 py-2.5 rounded-lg font-semibold text-sm hover:opacity-90 transition">
            <i class="fa-solid fa-plus"></i> إضافة فئة
        </a>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs">
                    <tr>
                        <th class="text-start p-4 font-semibold">الفئة</th>
                        <th class="text-start p-4 font-semibold">يبدأ من</th>
                        <th class="text-start p-4 font-semibold">عدد السيارات</th>
                        <th class="text-start p-4 font-semibold">الترتيب</th>
                        <th class="text-start p-4 font-semibold">الحالة</th>
                        <th class="text-center p-4 font-semibold"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($categories as $category)
                        <tr class="hover:bg-gray-50">
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <span class="w-10 h-10 rounded-full bg-brand/10 text-brand flex items-center justify-center shrink-0 overflow-hidden">
                                        @if($category->image_url)
                                            <img src="{{ $category->image_url }}" alt="" class="w-full h-full object-contain">
                                        @else
                                            <i class="{{ $category->icon ?: 'fa-solid fa-car' }}"></i>
                                        @endif
                                    </span>
                                    <div>
                                        <p class="font-semibold">{{ $category->name }}</p>
                                        <p class="text-xs text-gray-400" dir="ltr">{{ $category->slug }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4">
                                @php $rate = $category->lowestDailyRate(); @endphp
                                {{ $rate ? number_format($rate, 0) . ' د.أ' : '—' }}
                            </td>
                            <td class="p-4 font-semibold">{{ $category->vehicles_count }}</td>
                            <td class="p-4 text-gray-500">{{ $category->sort_order }}</td>
                            <td class="p-4">
                                <span class="inline-block text-xs font-semibold px-2.5 py-1 rounded-full
                                             {{ $category->is_active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $category->is_active ? 'مفعّلة' : 'معطّلة' }}
                                </span>
                            </td>
                            <td class="p-4">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('admin.vehicle-categories.edit', $category) }}"
                                       class="w-8 h-8 rounded-lg hover:bg-blue-50 text-blue-600 inline-flex items-center justify-center">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.vehicle-categories.destroy', $category) }}"
                                          onsubmit="return confirm('حذف هذه الفئة؟')">
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
                            <td colspan="6" class="p-16 text-center text-gray-400">
                                <i class="fa-solid fa-layer-group text-4xl block mb-3 opacity-30"></i>
                                لا توجد فئات
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($categories->hasPages())
            <div class="p-4 border-t border-gray-100">{{ $categories->links() }}</div>
        @endif
    </div>
</div>
@endsection
