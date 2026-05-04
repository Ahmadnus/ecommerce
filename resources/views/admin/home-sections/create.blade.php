@extends('layouts.admin')
@section('title', 'إضافة قسم جديد')

@section('admin-content')
<div class="max-w-xl mx-auto">

    <div class="mb-6">
        <a href="{{ route('admin.home-sections.index') }}"
           class="inline-flex items-center gap-1.5 text-sm font-semibold text-gray-500 hover:text-brand transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            العودة للأقسام
        </a>
    </div>

    @if($errors->any())
    <div class="mb-6 p-4 bg-red-50 border border-red-100 rounded-xl text-sm text-red-600">
        <ul class="list-disc list-inside space-y-0.5">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.home-sections.store') }}" method="POST" class="space-y-5"
          x-data="{ type: '{{ old('type', 'featured') }}' }">
        @csrf

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 space-y-5">
            <h2 class="font-bold text-gray-800 text-base border-b border-gray-100 pb-4">
                قسم جديد
            </h2>

            {{-- Title: Arabic --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    العنوان باللغة العربية <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title[ar]" value="{{ old('title.ar') }}"
                       required placeholder="مثال: المنتجات المميزة" dir="rtl"
                       class="w-full border border-gray-200 rounded-xl p-3 bg-gray-50 text-sm
                              focus:bg-white focus:outline-none focus:ring-2 focus:border-brand transition-all
                              @error('title.ar') border-red-400 @enderror">
                @error('title.ar')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            {{-- Title: English --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    العنوان باللغة الإنجليزية <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title[en]" value="{{ old('title.en') }}"
                       required placeholder="e.g. Featured Products" dir="ltr"
                       class="w-full border border-gray-200 rounded-xl p-3 bg-gray-50 text-sm
                              focus:bg-white focus:outline-none focus:ring-2 focus:border-brand transition-all
                              @error('title.en') border-red-400 @enderror">
                @error('title.en')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            {{-- Type --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    نوع القسم <span class="text-red-500">*</span>
                </label>
                <select name="type" x-model="type" required
                        class="w-full border border-gray-200 rounded-xl p-3 bg-gray-50 text-sm
                               focus:bg-white focus:outline-none focus:ring-2 focus:border-brand transition-all">
                    @foreach($typeLabels as $value => $label)
                    <option value="{{ $value }}" {{ old('type') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Category (only when type = 'category') --}}
            <div x-show="type === 'category'" x-transition>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    التصنيف <span class="text-red-500">*</span>
                </label>
                <select name="category_id"
                        class="w-full border border-gray-200 rounded-xl p-3 bg-gray-50 text-sm
                               focus:bg-white focus:outline-none focus:ring-2 focus:border-brand transition-all">
                    <option value="">اختر التصنيف...</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->getTranslation('name', app()->getLocale(), false) ?? $cat->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- Limit + Sort order --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        عدد المنتجات
                    </label>
                    <input type="number" name="limit" value="{{ old('limit', 10) }}"
                           min="1" max="50"
                           class="w-full border border-gray-200 rounded-xl p-3 bg-gray-50 text-sm
                                  focus:bg-white focus:outline-none focus:ring-2 focus:border-brand transition-all">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        ترتيب العرض
                    </label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}"
                           min="0"
                           class="w-full border border-gray-200 rounded-xl p-3 bg-gray-50 text-sm
                                  focus:bg-white focus:outline-none focus:ring-2 focus:border-brand transition-all">
                </div>
            </div>

            {{-- is_active --}}
            <label class="flex items-center gap-3 cursor-pointer p-3 border border-gray-200 rounded-xl bg-gray-50 hover:bg-white transition-colors">
                <input type="checkbox" name="is_active" value="1"
                       {{ old('is_active', true) ? 'checked' : '' }}
                       class="w-5 h-5 text-brand border-gray-300 rounded focus:ring-brand/30">
                <div>
                    <p class="text-sm font-semibold text-gray-800">تفعيل القسم</p>
                    <p class="text-xs text-gray-400">سيظهر هذا القسم في الصفحة الرئيسية عند تفعيله</p>
                </div>
            </label>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.home-sections.index') }}"
               class="px-6 py-2.5 text-sm font-bold text-gray-500 hover:text-red-500 transition-colors">
                إلغاء
            </a>
            <button type="submit"
                    class="bg-brand text-white px-8 py-2.5 rounded-xl font-bold text-sm shadow-lg hover:opacity-90 hover:scale-[1.02] transition-all active:scale-95">
                حفظ القسم
            </button>
        </div>
    </form>
</div>
@endsection