@extends('layouts.admin')

@section('title', 'إضافة قيمة')

@section('admin-content')

<div class="max-w-xl mx-auto bg-white p-6 rounded-2xl border">

    <h2 class="font-bold mb-4">إضافة قيمة جديدة</h2>

    <form method="POST" action="{{ route('admin.attribute-values.store') }}">
        @csrf

        <div class="mb-3">
            <label class="text-sm font-bold">الخاصية</label>
            <select name="attribute_id" class="w-full border p-2 rounded-xl mt-1">
                @foreach($attributes as $attr)
                    <option value="{{ $attr->id }}">{{ $attr->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="text-sm font-bold">القيمة</label>
            <input type="text" name="value" class="w-full border p-2 rounded-xl mt-1">
        </div>

        <div class="mb-3">
            <label class="text-sm font-bold">الاسم (اختياري)</label>
            <input type="text" name="label" class="w-full border p-2 rounded-xl mt-1">
        </div>

        <div class="mb-3">
            <label class="text-sm font-bold">اللون (اختياري)</label>
            <input type="color" name="color_hex" class="w-12 h-10 mt-1">
        </div>

        <button class="bg-brand text-white px-4 py-2 rounded-xl">
            حفظ
        </button>
    </form>

</div>

@endsection