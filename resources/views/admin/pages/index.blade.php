@extends('layouts.admin')
@section('title', 'الصفحات')

@section('admin-content')

<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">الصفحات الديناميكية</h1>
        <p class="text-gray-500 text-sm mt-1">روابط الفوتر والصفحات الثابتة كسياسة الخصوصية والشروط.</p>
    </div>
    <a href="{{ route('admin.pages.create') }}"
       class="inline-flex items-center gap-2 bg-brand text-white font-bold px-5 py-2.5 rounded-xl hover:opacity-90 transition shadow-sm text-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        صفحة جديدة
    </a>
</div>

@if($pages->isEmpty())
<div class="bg-white border border-gray-200 rounded-2xl p-16 text-center shadow-sm">
    <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
    </svg>
    <p class="text-gray-500 font-semibold mb-1">لا توجد صفحات بعد</p>
    <p class="text-gray-400 text-sm mb-4">أضف أول صفحة كسياسة الخصوصية أو شروط الاستخدام.</p>
    <a href="{{ route('admin.pages.create') }}"
       class="inline-flex items-center gap-2 bg-brand text-white font-bold px-5 py-2 rounded-xl hover:opacity-90 transition text-sm">
        إضافة صفحة
    </a>
</div>

@else
<div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
    <table class="w-full text-right text-sm">
        <thead class="bg-gray-50/80 border-b border-gray-200">
            <tr>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">الصفحة</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">الرابط</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">الترتيب</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">الحالة</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-left">العمليات</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($pages as $page)
            <tr class="hover:bg-gray-50/60 transition-colors group">

                {{-- Name --}}
                <td class="px-6 py-4">
                    <p class="font-semibold text-gray-900">{{ $page->name }}</p>
                    <p class="text-xs text-gray-400 mt-0.5 truncate max-w-xs">
                        {{ Str::limit(strip_tags($page->content), 60) }}
                    </p>
                </td>

                {{-- Slug / URL --}}
                <td class="px-6 py-4">
                    <a href="{{ route('pages.show', $page->slug) }}"
                       target="_blank"
                       class="inline-flex items-center gap-1 font-mono text-xs text-brand hover:underline">
                        /p/{{ $page->slug }}
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </a>
                </td>

                {{-- Sort order --}}
                <td class="px-6 py-4 text-center">
                    <span class="text-gray-500 tabular-nums">{{ $page->sort_order }}</span>
                </td>

                {{-- Status --}}
                <td class="px-6 py-4 text-center">
                    @if($page->is_active)
                    <span class="inline-flex items-center gap-1.5 bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">
                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                        نشطة
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1.5 bg-gray-100 text-gray-500 text-xs font-bold px-3 py-1 rounded-full">
                        <span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span>
                        مخفية
                    </span>
                    @endif
                </td>

                {{-- Actions --}}
                <td class="px-6 py-4 text-left">
                    <div class="flex justify-end items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        <a href="{{ route('admin.pages.edit', $page) }}"
                           class="p-2 text-blue-500 hover:bg-blue-50 rounded-lg transition-colors" title="تعديل">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                        </a>
                        <form action="{{ route('admin.pages.destroy', $page) }}" method="POST"
                              onsubmit="return confirm('حذف صفحة \'{{ addslashes($page->name) }}\'؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-red-400 hover:bg-red-50 rounded-lg transition-colors" title="حذف">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

@endsection