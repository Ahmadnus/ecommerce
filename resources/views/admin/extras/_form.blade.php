@php $extra = $extra ?? null; @endphp

@if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg p-4 mb-6">
        <ul class="list-disc list-inside text-sm space-y-1">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
@endif

<div class="bg-white border border-gray-200 rounded-xl p-6 space-y-6 max-w-3xl">

    {{-- Bilingual name --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
            <label for="name_ar" class="block text-sm font-semibold mb-2">
                اسم الخدمة (عربي) <span class="text-red-500">*</span>
            </label>
            <input type="text" id="name_ar" name="name[ar]" required
                   value="{{ old('name.ar', $extra?->getTranslation('name', 'ar', false)) }}"
                   placeholder="تأمين ضد الحوادث"
                   class="w-full rounded-lg border-gray-300 text-sm">
        </div>
        <div>
            <label for="name_en" class="block text-sm font-semibold mb-2">
                اسم الخدمة (إنجليزي) <span class="text-red-500">*</span>
            </label>
            <input type="text" id="name_en" name="name[en]" required dir="ltr"
                   value="{{ old('name.en', $extra?->getTranslation('name', 'en', false)) }}"
                   placeholder="Collision Damage Waiver"
                   class="w-full rounded-lg border-gray-300 text-sm">
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 pt-5 border-t border-gray-100">
        <div>
            <label for="price" class="block text-sm font-semibold mb-2">
                السعر (د.أ) <span class="text-red-500">*</span>
            </label>
            <input type="number" step="0.01" min="0" id="price" name="price" required
                   value="{{ old('price', $extra?->price ?? 0) }}"
                   class="w-full rounded-lg border-gray-300 text-sm">
        </div>

        <div>
            <label for="icon" class="block text-sm font-semibold mb-2">الأيقونة</label>
            <input type="text" id="icon" name="icon" dir="ltr"
                   value="{{ old('icon', $extra?->icon) }}"
                   placeholder="fa-solid fa-shield-halved"
                   class="w-full rounded-lg border-gray-300 text-sm">
            <p class="mt-1 text-xs text-gray-500">
                كلاس Font Awesome — مثال: <span dir="ltr">fa-solid fa-baby</span>
            </p>
        </div>

        <div>
            <label for="sort_order" class="block text-sm font-semibold mb-2">الترتيب</label>
            <input type="number" min="0" id="sort_order" name="sort_order"
                   value="{{ old('sort_order', $extra?->sort_order ?? 0) }}"
                   class="w-full rounded-lg border-gray-300 text-sm">
        </div>
    </div>

    @unless($extra)
        {{-- Only on create: bookings reference the code in their extras JSON,
             so it is locked once the row exists. --}}
        <div class="pt-5 border-t border-gray-100">
            <label for="code" class="block text-sm font-semibold mb-2">المعرّف (اختياري)</label>
            <input type="text" id="code" name="code" dir="ltr"
                   value="{{ old('code') }}" placeholder="cdw"
                   class="w-full max-w-xs rounded-lg border-gray-300 text-sm">
            <p class="mt-1 text-xs text-gray-500">
                حروف إنجليزية صغيرة وأرقام وشرطة سفلية فقط. إذا تركته فاضي بيتولّد من الاسم الإنجليزي.
                لا يمكن تعديله بعد الحفظ لأن الحجوزات بتشير إليه.
            </p>
        </div>
    @else
        <div class="pt-5 border-t border-gray-100">
            <span class="block text-sm font-semibold mb-2">المعرّف</span>
            <code class="text-sm bg-gray-100 px-2 py-1 rounded" dir="ltr">{{ $extra->code }}</code>
            <p class="mt-1 text-xs text-gray-500">ثابت — الحجوزات السابقة بتشير إليه.</p>
        </div>
    @endunless

    <div class="flex flex-wrap gap-6 pt-5 border-t border-gray-100">
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="per_day" value="1"
                   @checked(old('per_day', $extra?->per_day ?? true))
                   class="rounded border-gray-300">
            <span class="text-sm font-semibold">يُحتسب لكل يوم</span>
        </label>

        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="is_active" value="1"
                   @checked(old('is_active', $extra?->is_active ?? true))
                   class="rounded border-gray-300">
            <span class="text-sm font-semibold">مفعّلة</span>
        </label>
    </div>
    <p class="text-xs text-gray-500 -mt-3">
        إذا ألغيت "لكل يوم" بينحسب السعر مرة وحدة على الحجز كامل.
    </p>

    <div class="flex items-center gap-3 pt-5 border-t border-gray-100">
        <button type="submit" class="bg-brand text-white px-6 py-2.5 rounded-lg font-semibold text-sm hover:opacity-90 transition">
            حفظ
        </button>
        <a href="{{ route('admin.extras.index') }}" class="px-6 py-2.5 rounded-lg border border-gray-300 text-sm font-semibold">
            إلغاء
        </a>
    </div>
</div>
