@extends('layouts.admin')
@section('title', 'التصنيفات')

@section('admin-content')

<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">التصنيفات</h1>
        <p class="text-sm text-gray-400 mt-1">{{ $categories->count() }} تصنيف رئيسي</p>
    </div>
    <a href="{{ route('admin.categories.create') }}"
       class="inline-flex items-center gap-2 bg-brand text-white px-5 py-2.5 rounded-xl font-bold shadow-lg hover:opacity-90 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        إضافة تصنيف
    </a>
</div>

{{-- Flash messages --}}
@if(session('success'))
<div class="mb-6 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm">
    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
    </svg>
    {{ session('success') }}
</div>
@endif

<div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
    <table class="w-full text-right">
        <thead class="bg-gray-50/80 border-b border-gray-200">
            <tr>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">التصنيف</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">المسار</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">المنتجات</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">الحالة</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-left">العمليات</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">

            {{-- Recursive macro: renders a category row + all its children --}}
            @each('admin.categories.row', $categories, 'category')

        </tbody>
    </table>

    @if($categories->isEmpty())
    <div class="py-16 text-center text-gray-400">
        <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
        </svg>
        <p class="text-sm font-medium">لا توجد تصنيفات بعد</p>
        <a href="{{ route('admin.categories.create') }}" class="mt-2 inline-block text-brand text-sm hover:underline">
            أضف أول تصنيف
        </a>
    </div>
    @endif
</div>

@endsection