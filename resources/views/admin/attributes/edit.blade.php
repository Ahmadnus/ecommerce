@extends('layouts.admin')
@section('title', 'تعديل خاصية')

@section('admin-content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- ── Edit Attribute ───────────────────────────────────────────── --}}
    <div class="bg-white p-6 rounded-2xl border">
        <h2 class="font-bold mb-4">تعديل الخاصية</h2>

        @if($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-red-600 text-sm">
                <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.attributes.update', $attribute) }}">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="text-sm font-bold">الاسم (عربي)</label>
                    <input type="text" name="name[ar]"
                           value="{{ old('name.ar', $attribute->getTranslation('name', 'ar')) }}"
                           dir="rtl"
                           class="w-full border rounded-xl p-2 mt-1 @error('name.ar') border-red-400 @enderror">
                    @error('name.ar')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="text-sm font-bold">Name (English)</label>
                    <input type="text" name="name[en]"
                           value="{{ old('name.en', $attribute->getTranslation('name', 'en')) }}"
                           dir="ltr"
                           class="w-full border rounded-xl p-2 mt-1 @error('name.en') border-red-400 @enderror">
                    @error('name.en')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="mb-4">
                <label class="text-sm font-bold">النوع</label>
                <select name="type" class="w-full border rounded-xl p-2 mt-1">
                    <option value="select" @selected($attribute->type === 'select')>اختيار / Select</option>
                    <option value="color"  @selected($attribute->type === 'color')>لون / Color</option>
                    <option value="text"   @selected($attribute->type === 'text')>نص / Text</option>
                </select>
            </div>

            <button class="bg-brand text-white px-4 py-2 rounded-xl">حفظ التعديلات</button>
        </form>
    </div>

    {{-- ── Attribute Values ─────────────────────────────────────────── --}}
    <div class="bg-white p-6 rounded-2xl border">
        <h3 class="font-bold mb-4">القيم / Values</h3>

        @if(session('success'))
            <div class="mb-3 p-3 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- Add value inline --}}
        <form method="POST" action="{{ route('admin.attribute-values.store') }}" class="mb-6">
            @csrf
            <input type="hidden" name="attribute_id" value="{{ $attribute->id }}">

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mb-2">
                <input type="text" name="value[ar]" placeholder="القيمة (عربي)"
                       dir="rtl"
                       class="border p-2 rounded-xl text-sm @error('value.ar') border-red-400 @enderror">
                <input type="text" name="value[en]" placeholder="Value (English)"
                       dir="ltr"
                       class="border p-2 rounded-xl text-sm @error('value.en') border-red-400 @enderror">
                <input type="text" name="label[ar]" placeholder="العرض (عربي)"
                       dir="rtl"
                       class="border p-2 rounded-xl text-sm">
                <input type="text" name="label[en]" placeholder="Label (English)"
                       dir="ltr"
                       class="border p-2 rounded-xl text-sm">
            </div>

            <div class="flex items-center gap-2">
                @if($attribute->type === 'color')
                    <input type="color" name="color_hex" class="w-12 h-10 rounded border">
                @endif
                <button class="bg-green-500 text-white px-4 py-2 rounded-xl text-sm">+ إضافة</button>
            </div>
        </form>

        {{-- Values list --}}
        <div class="space-y-2">
            @forelse($attribute->values as $val)
            <div class="flex items-center justify-between bg-gray-50 p-3 rounded-xl">
                <div class="flex items-center gap-3">
                    @if($val->color_hex)
                        <span class="w-5 h-5 rounded-full border border-gray-200 flex-shrink-0"
                              style="background:{{ $val->color_hex }}"></span>
                    @endif
                    {{-- Displays in current locale automatically --}}
                    <div>
                        <span class="font-medium text-sm">{{ $val->display_label }}</span>
                        <span class="text-xs text-gray-400 ms-2">
                            ar: {{ $val->getTranslation('value', 'ar') }} /
                            en: {{ $val->getTranslation('value', 'en') }}
                        </span>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.attribute-values.destroy', $val) }}">
                    @csrf @method('DELETE')
                    <button class="text-red-500 text-sm hover:underline">حذف</button>
                </form>
            </div>
            @empty
                <p class="text-gray-400 text-sm text-center py-4">لا توجد قيم بعد.</p>
            @endforelse
        </div>
    </div>

</div>
@endsection