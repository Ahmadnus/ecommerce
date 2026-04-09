@extends('layouts.admin')
@section('title', 'التصنيفات')

@section('admin-content')
<div class="flex items-center justify-between mb-8">
    <h1 class="text-3xl font-bold text-gray-900">التصنيفات</h1>
    <a href="{{ route('admin.categories.create') }}" class="bg-brand text-white px-4 py-2 rounded-xl font-bold shadow-lg hover:opacity-90 transition">
        + إضافة تصنيف
    </a>
</div>

<div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
    <table class="w-full text-right">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="px-6 py-4 text-sm font-bold text-gray-500 uppercase">الأيقونة/الصورة</th>
                <th class="px-6 py-4 text-sm font-bold text-gray-500 uppercase">الاسم</th>
                <th class="px-6 py-4 text-sm font-bold text-gray-500 uppercase">عدد المنتجات</th>
                <th class="px-6 py-4 text-sm font-bold text-gray-500 uppercase">الحالة</th>
                <th class="px-6 py-4 text-sm font-bold text-gray-500 uppercase text-left">العمليات</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($categories as $category)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-6 py-4">
                    <img src="{{ $category->getFirstMediaUrl('categories') ?: asset('default-cat.png') }}" class="w-10 h-10 rounded-lg object-cover">
                </td>
                <td class="px-6 py-4 font-medium text-gray-900">{{ $category->name }}</td>
                <td class="px-6 py-4 text-gray-500">{{ $category->products_count }} منتج</td>
                <td class="px-6 py-4">
                    <span class="{{ $category->is_active ? 'text-green-600 bg-green-50' : 'text-red-600 bg-red-50' }} px-3 py-1 rounded-full text-xs font-bold">
                        {{ $category->is_active ? 'نشط' : 'معطل' }}
                    </span>
                </td>
                <td class="px-6 py-4 text-left">
                    <div class="flex justify-end gap-2">
                        <a href="{{ route('admin.categories.edit', $category->id) }}" class="text-blue-600 hover:bg-blue-50 p-2 rounded-lg"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></a>
                        <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('حذف التصنيف؟');">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:bg-red-50 p-2 rounded-lg"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection