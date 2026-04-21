@extends('layouts.admin')
@section('title', 'إضافة خاصية')

@section('admin-content')
<div class="max-w-xl mx-auto bg-white p-6 rounded-2xl border">

    <h2 class="font-bold mb-4">إضافة خاصية</h2>

    <form method="POST" action="{{ route('admin.attributes.store') }}">
        @csrf

        <div class="mb-4">
            <label class="text-sm font-bold">الاسم</label>
            <input type="text" name="name" required
                   class="w-full border rounded-xl p-2 mt-1">
        </div>

        <div class="mb-4">
            <label class="text-sm font-bold">النوع</label>
            <select name="type" class="w-full border rounded-xl p-2 mt-1">
                <option value="select">اختيار</option>
                <option value="color">لون</option>
                <option value="text">نص</option>
            </select>
        </div>

        <button class="bg-brand text-white px-4 py-2 rounded-xl">
            حفظ
        </button>
    </form>
</div>
@endsection