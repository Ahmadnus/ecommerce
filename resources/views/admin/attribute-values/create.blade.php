@extends('layouts.admin')
@section('title', 'إضافة قيمة')

@section('admin-content')
<div class="max-w-xl mx-auto bg-white p-6 rounded-2xl border">
    <h2 class="font-bold mb-4">إضافة قيمة جديدة</h2>

    @if($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-red-600 text-sm">
            <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.attribute-values.store') }}">
        @csrf

        {{-- Attribute --}}
        <div class="mb-4">
            <label class="text-sm font-bold">الخاصية</label>
            <select name="attribute_id" class="w-full border p-2 rounded-xl mt-1">
                @foreach($attributes as $attr)
                    <option value="{{ $attr->id }}" @selected(old('attribute_id') == $attr->id)>
                        {{ $attr->getTranslation('name', 'ar') }} / {{ $attr->getTranslation('name', 'en') }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Value --}}
        <div class="grid grid-cols-2 gap-3 mb-4">
            <div>
                <label class="text-sm font-bold">القيمة (عربي) <span class="text-red-500">*</span></label>
                <input type="text" name="value[ar]" value="{{ old('value.ar') }}" required
                       dir="rtl"
                       class="w-full border p-2 rounded-xl mt-1 @error('value.ar') border-red-400 @enderror"
                       placeholder="مثال: أحمر">
                @error('value.ar')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="text-sm font-bold">Value (English) <span class="text-red-500">*</span></label>
                <input type="text" name="value[en]" value="{{ old('value.en') }}" required
                       dir="ltr"
                       class="w-full border p-2 rounded-xl mt-1 @error('value.en') border-red-400 @enderror"
                       placeholder="e.g. Red">
                @error('value.en')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Label (display override) --}}
        <div class="grid grid-cols-2 gap-3 mb-4">
            <div>
                <label class="text-sm font-bold">اسم العرض (عربي)</label>
                <input type="text" name="label[ar]" value="{{ old('label.ar') }}"
                       dir="rtl"
                       class="w-full border p-2 rounded-xl mt-1"
                       placeholder="اختياري">
            </div>
            <div>
                <label class="text-sm font-bold">Display Label (English)</label>
                <input type="text" name="label[en]" value="{{ old('label.en') }}"
                       dir="ltr"
                       class="w-full border p-2 rounded-xl mt-1"
                       placeholder="Optional">
            </div>
        </div>

        {{-- Color --}}
        <div class="mb-4">
            <label class="text-sm font-bold">اللون (اختياري)</label>
            <input type="color" name="color_hex" class="w-12 h-10 mt-1 rounded border">
        </div>

        <button class="bg-brand text-white px-4 py-2 rounded-xl">حفظ</button>
    </form>
</div>
@endsection