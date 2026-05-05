@extends('layouts.admin')
@section('title', 'إضافة خاصية')

@section('admin-content')
<div class="max-w-xl mx-auto bg-white p-6 rounded-2xl border">
    <h2 class="font-bold mb-4">إضافة خاصية</h2>

    @if($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-red-600 text-sm">
            <ul class="space-y-0.5">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.attributes.store') }}">
        @csrf

        {{-- Name: Arabic --}}
        <div class="mb-4">
            <label class="text-sm font-bold">الاسم (عربي) <span class="text-red-500">*</span></label>
            <input type="text" name="name[ar]" value="{{ old('name.ar') }}" required
                   dir="rtl"
                   class="w-full border rounded-xl p-2 mt-1 @error('name.ar') border-red-400 @enderror"
                   placeholder="مثال: الحجم">
            @error('name.ar')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Name: English --}}
        <div class="mb-4">
            <label class="text-sm font-bold">Name (English) <span class="text-red-500">*</span></label>
            <input type="text" name="name[en]" value="{{ old('name.en') }}" required
                   dir="ltr"
                   class="w-full border rounded-xl p-2 mt-1 @error('name.en') border-red-400 @enderror"
                   placeholder="e.g. Size">
            @error('name.en')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Type --}}
        <div class="mb-4">
            <label class="text-sm font-bold">النوع</label>
            <select name="type" class="w-full border rounded-xl p-2 mt-1">
                <option value="select" @selected(old('type') === 'select')>اختيار / Select</option>
                <option value="color"  @selected(old('type') === 'color')>لون / Color</option>
                <option value="text"   @selected(old('type') === 'text')>نص / Text</option>
            </select>
        </div>

        <button class="bg-brand text-white px-4 py-2 rounded-xl">حفظ</button>
    </form>
</div>
@endsection