@extends('layouts.admin')
@section('title', 'الخصائص')

@section('admin-content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100">

    <div class="p-6 border-b flex justify-between items-center">
        <h2 class="font-bold text-gray-800">الخصائص</h2>

        <a href="{{ route('admin.attributes.create') }}"
           class="bg-brand text-white px-4 py-2 rounded-xl text-sm font-bold">
            + إضافة خاصية
        </a>
    </div>

    <table class="w-full text-right text-sm">
        <thead class="bg-gray-50 text-gray-500 text-xs">
            <tr>
                <th class="px-6 py-3">الاسم</th>
                <th class="px-6 py-3">عدد القيم</th>
                <th class="px-6 py-3">الإجراءات</th>
            </tr>
        </thead>

        <tbody class="divide-y">
            @foreach($attributes as $attr)
            <tr>
                <td class="px-6 py-4 font-semibold">{{ $attr->name }}</td>
                <td class="px-6 py-4">{{ $attr->values_count }}</td>

                <td class="px-6 py-4 flex gap-2">
                    <a href="{{ route('admin.attributes.edit', $attr) }}"
                       class="text-blue-500">تعديل</a>

                    <form method="POST" action="{{ route('admin.attributes.destroy', $attr) }}">
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