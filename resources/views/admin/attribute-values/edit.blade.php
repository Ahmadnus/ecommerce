@extends('layouts.admin')

@section('title', 'تعديل قيمة')

@section('admin-content')

<div class="max-w-xl mx-auto bg-white p-6 rounded-2xl border">

    <h2 class="font-bold mb-4">تعديل القيمة</h2>

    <form method="POST"
          action="{{ route('admin.attribute-values.update', $attributeValue) }}">
        @csrf @method('PUT')

        <div class="mb-3">
            <label class="text-sm font-bold">الخاصية</label>
            <select name="attribute_id" class="w-full border p-2 rounded-xl mt-1">
                @foreach($attributes as $attr)
                    <option value="{{ $attr->id }}"
                        {{ $attributeValue->attribute_id == $attr->id ? 'selected' : '' }}>
                        {{ $attr->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="text-sm font-bold">القيمة</label>
            <input type="text" name="value"
                   value="{{ $attributeValue->value }}"
                   class="w-full border p-2 rounded-xl mt-1">
        </div>

        <div class="mb-3">
            <label class="text-sm font-bold">الاسم</label>
            <input type="text" name="label"
                   value="{{ $attributeValue->label }}"
                   class="w-full border p-2 rounded-xl mt-1">
        </div>

        <div class="mb-3">
            <label class="text-sm font-bold">اللون</label>
            <input type="color" name="color_hex"
                   value="{{ $attributeValue->color_hex }}"
                   class="w-12 h-10 mt-1">
        </div>

        <button class="bg-brand text-white px-4 py-2 rounded-xl">
            تحديث
        </button>
    </form>

</div>

@endsection