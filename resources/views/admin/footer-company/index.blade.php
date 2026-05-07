@extends('layouts.admin')

@section('title', 'معلومات الشركة في الفوتر')

@section('admin-content')
<div class="max-w-5xl mx-auto">

    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">معلومات الشركة في الفوتر</h1>
            <p class="text-sm text-gray-500 mt-1">إدارة بيانات الشركة التي تظهر في أسفل الموقع</p>
        </div>
        <a href="{{ route('admin.footer-company.create') }}"
           class="inline-flex items-center gap-2 bg-black text-white text-xs font-semibold px-4 py-2.5 rounded-lg hover:bg-gray-800 transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            إضافة إدخال
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50">
                    <th class="px-5 py-3 text-right text-xs font-bold text-gray-500">الشركة</th>
                    <th class="px-5 py-3 text-right text-xs font-bold text-gray-500">الهاتف</th>
                    <th class="px-5 py-3 text-right text-xs font-bold text-gray-500">الموقع</th>
                    <th class="px-5 py-3 text-right text-xs font-bold text-gray-500">الترتيب</th>
                    <th class="px-5 py-3 text-right text-xs font-bold text-gray-500">الحالة</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($items as $item)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-4 font-semibold text-gray-800">
                            {{ $item->getTranslation('company_name', 'ar', false)
                               ?: $item->getTranslation('company_name', 'en', false)
                               ?: '—' }}
                        </td>
                        <td class="px-5 py-4 font-mono text-gray-600" dir="ltr">
                            {{ $item->phone ?: '—' }}
                        </td>
                        <td class="px-5 py-4 text-gray-600">
                            {{ $item->getTranslation('location', 'ar', false)
                               ?: $item->getTranslation('location', 'en', false)
                               ?: '—' }}
                        </td>
                        <td class="px-5 py-4 text-gray-500">{{ $item->sort_order }}</td>
                        <td class="px-5 py-4">
                            @if($item->is_active)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                    مفعّل
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                    معطّل
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2 justify-end">
                                <a href="{{ route('admin.footer-company.edit', $item) }}"
                                   class="text-xs px-3 py-1.5 border border-gray-200 rounded-lg hover:bg-gray-50 font-medium transition">
                                    تعديل
                                </a>
                               <form action="{{ route('admin.footer-company.destroy', $item) }}"
      method="POST"
      onsubmit="return confirm('هل أنت متأكد من حذف هذا الإدخال؟')">
    @csrf
    @method('DELETE')   {{-- ← this line is critical, without it Laravel treats it as POST not DELETE --}}
    <button type="submit"
            class="text-xs px-3 py-1.5 border border-red-200 text-red-600 rounded-lg hover:bg-red-50 font-medium transition">
        حذف
    </button>
</form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-gray-400 text-sm">
                            لا توجد إدخالات بعد.
                            <a href="{{ route('admin.footer-company.create') }}"
                               class="text-black font-semibold hover:underline mr-1">
                                أضف واحدًا ←
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection