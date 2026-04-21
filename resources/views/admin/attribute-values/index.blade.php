@extends('layouts.admin')

@section('title', 'قيم الخصائص')

@section('admin-content')
<div class="bg-white rounded-2xl shadow-sm border p-6">

    <h2 class="font-bold mb-4">قيم الخصائص</h2>

    <table class="w-full text-sm text-right">
        <thead class="bg-gray-50 text-gray-500">
            <tr>
                <th class="p-3">الخاصية</th>
                <th class="p-3">القيمة</th>
                <th class="p-3">الاسم</th>
                <th class="p-3">الإجراءات</th>
            </tr>
        </thead>

        <tbody>
            @foreach($values as $value)
            <tr class="border-b">
                <td class="p-3">{{ $value->attribute->name }}</td>
                <td class="p-3">{{ $value->value }}</td>
                <td class="p-3">{{ $value->label }}</td>

                <td class="p-3 flex gap-2">
                    <a href="{{ route('admin.attribute-values.edit', $value) }}"
                       class="text-blue-500">تعديل</a>

                    <form method="POST"
                          action="{{ route('admin.attribute-values.destroy', $value) }}">
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