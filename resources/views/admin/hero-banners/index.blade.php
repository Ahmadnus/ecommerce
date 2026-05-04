@extends('layouts.admin')
@section('title', 'إدارة البانر الرئيسي')

@section('admin-content')
<div class="space-y-8">

    {{-- ══════════════════════════════════════════════════════
         ADD FORM
    ══════════════════════════════════════════════════════ --}}
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <h3 class="text-lg font-bold mb-4">{{ __('app.add_banner') ?? 'إضافة بانر جديد' }}</h3>

        <form action="{{ route('admin.hero-banners.store') }}" method="POST"
              enctype="multipart/form-data"
              x-data="{ tab: 'ar' }">
            @csrf

            {{-- Language tabs --}}
            @include('admin.hero-banners._lang-tabs')

            {{-- ── Arabic fields ──────────────────────────────── --}}
            <div x-show="tab === 'ar'" class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">

                <input type="text" name="title[ar]" placeholder="العنوان الرئيسي *"
                       value="{{ old('title.ar') }}" required dir="rtl"
                       class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
                @error('title.ar')<p class="md:col-span-2 text-xs text-red-500 -mt-2">{{ $message }}</p>@enderror

                <input type="text" name="subtitle[ar]" placeholder="العنوان الفرعي"
                       value="{{ old('subtitle.ar') }}" dir="rtl"
                       class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">

                <input type="text" name="badge[ar]" placeholder="النص العلوي (Badge)"
                       value="{{ old('badge.ar') }}" dir="rtl"
                       class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">

                <input type="text" name="button_text[ar]" placeholder="نص الزر *"
                       value="{{ old('button_text.ar', 'اكتشف الآن') }}" required dir="rtl"
                       class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
                @error('button_text.ar')<p class="md:col-span-2 text-xs text-red-500 -mt-2">{{ $message }}</p>@enderror

                <div class="md:col-span-2">
                    <textarea name="description[ar]" placeholder="وصف قصير" dir="rtl"
                              class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">{{ old('description.ar') }}</textarea>
                </div>
            </div>

            {{-- ── English fields ──────────────────────────────── --}}
            <div x-show="tab === 'en'" x-cloak class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">

                <input type="text" name="title[en]" placeholder="Main title"
                       value="{{ old('title.en') }}" dir="ltr"
                       class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">

                <input type="text" name="subtitle[en]" placeholder="Subtitle"
                       value="{{ old('subtitle.en') }}" dir="ltr"
                       class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">

                <input type="text" name="badge[en]" placeholder="Badge text"
                       value="{{ old('badge.en') }}" dir="ltr"
                       class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">

                <input type="text" name="button_text[en]" placeholder="Button text"
                       value="{{ old('button_text.en', 'Shop now') }}" dir="ltr"
                       class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">

                <div class="md:col-span-2">
                    <textarea name="description[en]" placeholder="Short description" dir="ltr"
                              class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">{{ old('description.en') }}</textarea>
                </div>
            </div>

            {{-- ── Shared fields (not translatable) ──────────────── --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <input type="text" name="button_url" placeholder="رابط الزر"
                       value="{{ old('button_url') }}"
                       class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">

                <select name="position"
                        class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
                    <option value="top"            {{ old('position') === 'top'            ? 'selected' : '' }}>أعلى الصفحة</option>
                    <option value="after_featured" {{ old('position') === 'after_featured' ? 'selected' : '' }}>بعد المنتجات المميزة</option>
                    <option value="after_products" {{ old('position') === 'after_products' ? 'selected' : '' }}>بعد جميع المنتجات</option>
                </select>

                <div>
                    <label class="text-xs font-bold mb-1 block">لون الخلفية</label>
                    <input type="color" name="background_color"
                           value="{{ old('background_color', '#0ea5e9') }}"
                           class="w-full h-12 rounded-xl border cursor-pointer">
                </div>

                <div>
                    <label class="text-xs font-bold mb-1 block">لون النص</label>
                    <input type="color" name="text_color"
                           value="{{ old('text_color', '#ffffff') }}"
                           class="w-full h-12 rounded-xl border cursor-pointer">
                </div>

                <div class="md:col-span-2">
                    <input type="file" name="image"
                           class="w-full px-4 py-2 rounded-xl border">
                </div>

                <div class="md:col-span-2">
                    <button type="submit"
                            class="bg-brand text-white font-bold py-2 px-5 rounded-xl hover:opacity-90 transition">
                        حفظ البانر
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- ══════════════════════════════════════════════════════
         TABLE
    ══════════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-right">
            <thead class="bg-gray-50 border-b text-gray-500 text-sm">
                <tr>
                    <th class="px-6 py-4">الصورة</th>
                    <th class="px-6 py-4">العنوان</th>
                    <th class="px-6 py-4">الموقع</th>
                    <th class="px-6 py-4">الحالة</th>
                    <th class="px-6 py-4">الإجراء</th>
                </tr>
            </thead>

            <tbody class="divide-y text-sm">
                @foreach($banners as $banner)
                <tr>
                    <td class="px-6 py-4">
                        <img src="{{ $banner->getFirstMediaUrl('banner_image') }}"
                             class="w-20 h-12 object-cover rounded-lg shadow-sm"
                             alt="{{ $banner->title }}">
                    </td>

                    {{-- $banner->title auto-returns current locale value --}}
                    <td class="px-6 py-4 font-bold">{{ $banner->title }}</td>

                    <td class="px-6 py-4">
                        <span class="text-xs px-2 py-1 rounded bg-gray-100">
                            @switch($banner->position)
                                @case('top')            أعلى الصفحة              @break
                                @case('after_featured') بعد المنتجات المميزة     @break
                                @case('after_products') بعد جميع المنتجات        @break
                                @default                {{ $banner->position }}
                            @endswitch
                        </span>
                    </td>

                    <td class="px-6 py-4">
                        <span class="{{ $banner->is_active ? 'text-green-600' : 'text-red-400' }}">
                            {{ $banner->is_active ? 'نشط' : 'معطل' }}
                        </span>
                    </td>

                    <td class="px-6 py-4">
                        <div class="flex gap-4 items-center">
                            <button type="button" onclick="toggleEdit({{ $banner->id }})"
                                    class="text-blue-500 hover:underline">تعديل</button>

                            <form action="{{ route('admin.hero-banners.destroy', $banner) }}"
                                  method="POST"
                                  onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                @csrf @method('DELETE')
                                <button class="text-red-500 hover:underline" type="submit">حذف</button>
                            </form>
                        </div>
                    </td>
                </tr>

                {{-- ── Inline edit row ──────────────────────────────── --}}
                <tr id="edit-{{ $banner->id }}" class="hidden bg-gray-50">
                    <td colspan="5" class="p-6">
                        <form action="{{ route('admin.hero-banners.update', $banner) }}"
                              method="POST"
                              enctype="multipart/form-data"
                              x-data="{ tab: 'ar' }">
                            @csrf @method('PUT')

                            {{-- Language tabs --}}
                            @include('admin.hero-banners._lang-tabs')

                            {{-- Arabic fields --}}
                            <div x-show="tab === 'ar'" class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <input type="text" name="title[ar]"
                                       value="{{ $banner->getTranslation('title', 'ar') }}"
                                       placeholder="العنوان الرئيسي *" required dir="rtl"
                                       class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">

                                <input type="text" name="subtitle[ar]"
                                       value="{{ $banner->getTranslation('subtitle', 'ar') }}"
                                       placeholder="العنوان الفرعي" dir="rtl"
                                       class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">

                                <input type="text" name="badge[ar]"
                                       value="{{ $banner->getTranslation('badge', 'ar') }}"
                                       placeholder="النص العلوي" dir="rtl"
                                       class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">

                                <input type="text" name="button_text[ar]"
                                       value="{{ $banner->getTranslation('button_text', 'ar') }}"
                                       placeholder="نص الزر *" required dir="rtl"
                                       class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">

                                <div class="md:col-span-2">
                                    <textarea name="description[ar]" dir="rtl"
                                              class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">{{ $banner->getTranslation('description', 'ar') }}</textarea>
                                </div>
                            </div>

                            {{-- English fields --}}
                            <div x-show="tab === 'en'" x-cloak class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <input type="text" name="title[en]"
                                       value="{{ $banner->getTranslation('title', 'en') }}"
                                       placeholder="Main title" dir="ltr"
                                       class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">

                                <input type="text" name="subtitle[en]"
                                       value="{{ $banner->getTranslation('subtitle', 'en') }}"
                                       placeholder="Subtitle" dir="ltr"
                                       class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">

                                <input type="text" name="badge[en]"
                                       value="{{ $banner->getTranslation('badge', 'en') }}"
                                       placeholder="Badge text" dir="ltr"
                                       class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">

                                <input type="text" name="button_text[en]"
                                       value="{{ $banner->getTranslation('button_text', 'en') }}"
                                       placeholder="Button text" dir="ltr"
                                       class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">

                                <div class="md:col-span-2">
                                    <textarea name="description[en]" dir="ltr"
                                              class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">{{ $banner->getTranslation('description', 'en') }}</textarea>
                                </div>
                            </div>

                            {{-- Shared fields --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <input type="text" name="button_url"
                                       value="{{ $banner->button_url }}"
                                       placeholder="رابط الزر"
                                       class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">

                                <select name="position"
                                        class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
                                    <option value="top"            {{ $banner->position === 'top'            ? 'selected' : '' }}>أعلى الصفحة</option>
                                    <option value="after_featured" {{ $banner->position === 'after_featured' ? 'selected' : '' }}>بعد المنتجات المميزة</option>
                                    <option value="after_products" {{ $banner->position === 'after_products' ? 'selected' : '' }}>بعد جميع المنتجات</option>
                                </select>

                                <div>
                                    <label class="text-xs font-bold mb-1 block">لون الخلفية</label>
                                    <input type="color" name="background_color"
                                           value="{{ $banner->background_color ?? '#0ea5e9' }}"
                                           class="w-full h-12 rounded-xl border cursor-pointer">
                                </div>

                                <div>
                                    <label class="text-xs font-bold mb-1 block">لون النص</label>
                                    <input type="color" name="text_color"
                                           value="{{ $banner->text_color ?? '#ffffff' }}"
                                           class="w-full h-12 rounded-xl border cursor-pointer">
                                </div>

                                <div class="md:col-span-2">
                                    <input type="file" name="image"
                                           class="w-full px-4 py-2 rounded-xl border">
                                </div>

                                <div class="md:col-span-2 flex items-center gap-3">
                                    <label class="inline-flex items-center gap-2 text-sm">
                                        <input type="checkbox" name="is_active" value="1"
                                               {{ $banner->is_active ? 'checked' : '' }}>
                                        <span>نشط</span>
                                    </label>

                                    <button type="submit"
                                            class="bg-green-600 text-white font-bold py-2 px-5 rounded-xl hover:opacity-90 transition">
                                        تحديث
                                    </button>

                                    <button type="button" onclick="toggleEdit({{ $banner->id }})"
                                            class="bg-gray-100 text-gray-700 font-bold py-2 px-5 rounded-xl hover:bg-gray-200 transition">
                                        إغلاق
                                    </button>
                                </div>
                            </div>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
function toggleEdit(id) {
    const el = document.getElementById('edit-' + id);
    if (el) el.classList.toggle('hidden');
}
</script>
@endsection