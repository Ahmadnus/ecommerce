@extends('layouts.admin')
@section('title', 'ميزات الموقع')

@section('admin-content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

    <div class="p-6 border-b flex justify-between items-center">
        <h3 class="font-bold text-gray-800">ميزات الموقع</h3>
        <a href="{{ route('admin.site-features.create') }}" 
           class="bg-brand text-white px-4 py-2 rounded-xl text-sm font-bold">
            + إضافة
        </a>
    </div>

    <table class="w-full text-right">
        <thead class="bg-gray-50 text-xs text-gray-500">
            <tr>
                <th class="px-6 py-3">#</th>
                <th class="px-6 py-3">الأيقونة</th>
                <th class="px-6 py-3">العنوان</th>
                <th class="px-6 py-3">الوصف</th>
                <th class="px-6 py-3">الحالة</th>
                <th class="px-6 py-3">إجراءات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($features as $feature)
            <tr class="border-t">
                <td class="px-6 py-3">{{ $feature->id }}</td>
                <td class="px-6 py-3 text-xl">{{ $feature->icon }}</td>
                <td class="px-6 py-3">{{ $feature->title }}</td>
                <td class="px-6 py-3 text-sm text-gray-500">{{ $feature->description }}</td>
                <td class="px-6 py-3">
                    @if($feature->is_active)
                        <span class="text-green-600 text-xs font-bold">مفعل</span>
                    @else
                        <span class="text-red-500 text-xs font-bold">معطل</span>
                    @endif
                </td>
                <td class="px-6 py-3 flex gap-2">
                    <a href="{{ route('admin.site-features.edit', $feature) }}" class="text-blue-500">تعديل</a>
                    <form method="POST" action="{{ route('admin.site-features.destroy', $feature) }}">
                        @csrf @method('DELETE')
                        <button class="text-red-500">حذف</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
