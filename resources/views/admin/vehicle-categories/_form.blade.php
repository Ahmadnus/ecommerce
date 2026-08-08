@php $category = $category ?? null; @endphp

@if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg p-4 mb-6">
        <ul class="list-disc list-inside text-sm space-y-1">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
@endif

<div class="bg-white border border-gray-200 rounded-xl p-6 space-y-6 max-w-3xl">

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
            <label for="name_ar" class="block text-sm font-semibold mb-2">الاسم (عربي) <span class="text-red-500">*</span></label>
            <input type="text" id="name_ar" name="name[ar]" required
                   value="{{ old('name.ar', $category?->getTranslation('name', 'ar', false)) }}"
                   class="w-full rounded-lg border-gray-300 text-sm">
        </div>
        <div>
            <label for="name_en" class="block text-sm font-semibold mb-2">الاسم (إنجليزي)</label>
            <input type="text" id="name_en" name="name[en]" dir="ltr"
                   value="{{ old('name.en', $category?->getTranslation('name', 'en', false)) }}"
                   class="w-full rounded-lg border-gray-300 text-sm">
        </div>
        <div>
            <label for="desc_ar" class="block text-sm font-semibold mb-2">الوصف (عربي)</label>
            <textarea id="desc_ar" name="description[ar]" rows="3"
                      class="w-full rounded-lg border-gray-300 text-sm">{{ old('description.ar', $category?->getTranslation('description', 'ar', false)) }}</textarea>
        </div>
        <div>
            <label for="desc_en" class="block text-sm font-semibold mb-2">الوصف (إنجليزي)</label>
            <textarea id="desc_en" name="description[en]" rows="3" dir="ltr"
                      class="w-full rounded-lg border-gray-300 text-sm">{{ old('description.en', $category?->getTranslation('description', 'en', false)) }}</textarea>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 pt-5 border-t border-gray-100">
        <div>
            <label for="icon" class="block text-sm font-semibold mb-2">أيقونة (Font Awesome)</label>
            <input type="text" id="icon" name="icon" dir="ltr" value="{{ old('icon', $category?->icon) }}"
                   placeholder="fa-solid fa-car" class="w-full rounded-lg border-gray-300 text-sm">
        </div>
        <div>
            <label for="price_from" class="block text-sm font-semibold mb-2">السعر يبدأ من (د.أ)</label>
            <input type="number" step="0.01" min="0" id="price_from" name="price_from"
                   value="{{ old('price_from', $category?->price_from) }}" class="w-full rounded-lg border-gray-300 text-sm">
            <p class="mt-1 text-xs text-gray-500">يُستخدم فقط إذا لم توجد سيارات في الفئة.</p>
        </div>
        <div>
            <label for="sort_order" class="block text-sm font-semibold mb-2">ترتيب العرض</label>
            <input type="number" min="0" id="sort_order" name="sort_order"
                   value="{{ old('sort_order', $category?->sort_order ?? 0) }}" class="w-full rounded-lg border-gray-300 text-sm">
        </div>
    </div>

    <div class="pt-5 border-t border-gray-100">
        <label for="image" class="block text-sm font-semibold mb-2">صورة الفئة</label>
        @if($category?->image_url)
            <img src="{{ $category->image_url }}" alt="" class="w-32 h-24 object-contain bg-gray-50 rounded-lg mb-2 border border-gray-200">
        @endif
        <input type="file" id="image" name="image" accept="image/*"
               class="w-full max-w-md text-sm file:me-3 file:py-2 file:px-4 file:rounded-lg file:border-0
                      file:bg-brand file:text-white file:text-sm file:font-semibold">
    </div>

    <div class="space-y-3 pt-5 border-t border-gray-100">
        <label class="flex items-center gap-3 cursor-pointer">
            <input type="checkbox" name="is_active" value="1"
                   @checked(old('is_active', $category?->is_active ?? true)) class="rounded border-gray-300 text-brand">
            <span class="text-sm font-semibold">مفعّلة</span>
        </label>
        <label class="flex items-center gap-3 cursor-pointer">
            <input type="checkbox" name="is_featured" value="1"
                   @checked(old('is_featured', $category?->is_featured ?? true)) class="rounded border-gray-300 text-brand">
            <span class="text-sm font-semibold">تظهر في الصفحة الرئيسية</span>
        </label>
    </div>

    <div class="flex gap-3 pt-5 border-t border-gray-100">
        <button type="submit" class="bg-brand text-white font-bold px-8 py-3 rounded-lg hover:opacity-90 transition">
            <i class="fa-solid fa-floppy-disk"></i> حفظ
        </button>
        <a href="{{ route('admin.vehicle-categories.index') }}"
           class="px-6 py-3 border border-gray-300 rounded-lg font-semibold text-sm">إلغاء</a>
    </div>
</div>
