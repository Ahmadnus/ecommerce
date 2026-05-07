@extends('layouts.admin')

@section('title', $item->exists ? 'تعديل معلومات الشركة' : 'إضافة معلومات الشركة')

@section('admin-content')
<div class="max-w-2xl mx-auto">

    <div class="flex items-center gap-3 mb-8">
        <a href="{{ route('admin.footer-company.index') }}"
           class="text-gray-400 hover:text-gray-700 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
        <h1 class="text-xl font-bold text-gray-900">
            {{ $item->exists ? 'تعديل معلومات الشركة' : 'إضافة معلومات الشركة' }}
        </h1>
    </div>

    @if($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ $item->exists
            ? route('admin.footer-company.update', $item)
            : route('admin.footer-company.store') }}"
          method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @if($item->exists) @method('PUT') @endif

        {{-- ── الحقول القابلة للترجمة ──────────────────────────── --}}
        @foreach(['ar' => 'العربية 🇸🇦', 'en' => 'الإنجليزية 🇬🇧'] as $locale => $langLabel)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100 bg-gray-50">
                <span class="text-xs font-bold text-gray-500">{{ $langLabel }}</span>
            </div>
            <div class="p-5 space-y-4">

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">اسم الشركة</label>
                    <input type="text"
                           name="company_name[{{ $locale }}]"
                           value="{{ old("company_name.$locale", $item->getTranslation('company_name', $locale, false)) }}"
                           dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">وصف الشركة</label>
                    <textarea name="description[{{ $locale }}]"
                              rows="3"
                              dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}"
                              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300 resize-none">{{ old("description.$locale", $item->getTranslation('description', $locale, false)) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">الموقع الجغرافي</label>
                    <input type="text"
                           name="location[{{ $locale }}]"
                           value="{{ old("location.$locale", $item->getTranslation('location', $locale, false)) }}"
                           dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}"
                           placeholder="{{ $locale === 'ar' ? 'لندن، المملكة المتحدة' : 'London, United Kingdom' }}"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300">
                </div>

            </div>
        </div>
        @endforeach

        {{-- ── معلومات التواصل والعرض ──────────────────────────── --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100 bg-gray-50">
                <span class="text-xs font-bold text-gray-500">معلومات التواصل والعرض</span>
            </div>
            <div class="p-5 space-y-4">

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                            رمز الدولة
                        </label>
                        <input type="text" name="phone_country_code"
                               value="{{ old('phone_country_code', $item->phone_country_code) }}"
                               placeholder="gb"
                               maxlength="10"
                               dir="ltr"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300 text-center uppercase">
                        <p class="text-xs text-gray-400 mt-1">مثال: gb, us, sa</p>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">رقم الهاتف</label>
                        <input type="text" name="phone"
                               value="{{ old('phone', $item->phone) }}"
                               placeholder="+44 7782 281157"
                               dir="ltr"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-gray-300">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        صورة العلم / الأيقونة
                        <span class="font-normal text-gray-400 mr-1">(اختياري)</span>
                    </label>
                    @if($item->exists && $item->getFirstMediaUrl('flag_icon'))
                        <div class="flex items-center gap-2 mb-2">
                            <img src="{{ $item->getFirstMediaUrl('flag_icon') }}"
                                 class="w-7 h-auto rounded border border-gray-200"
                                 alt="علم">
                            <span class="text-xs text-gray-400">الصورة الحالية</span>
                        </div>
                    @endif
                    <input type="file" name="flag_icon" accept="image/*"
                           class="block w-full text-xs text-gray-500
                                  file:ml-3 file:py-1.5 file:px-3
                                  file:rounded-lg file:border-0
                                  file:text-xs file:font-semibold
                                  file:bg-gray-100 file:text-gray-700
                                  hover:file:bg-gray-200">
                    <p class="text-xs text-gray-400 mt-1">
                        اتركه فارغًا لاستخدام علم flagcdn.com تلقائيًا بناءً على رمز الدولة
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">ترتيب العرض</label>
                        <input type="number" name="sort_order"
                               value="{{ old('sort_order', $item->sort_order ?? 0) }}"
                               min="0"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300">
                    </div>
                    <div class="flex items-end pb-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1"
                                   @checked(old('is_active', $item->is_active ?? true))
                                   class="w-4 h-4 rounded border-gray-300">
                            <span class="text-sm font-medium text-gray-700">مفعّل</span>
                        </label>
                    </div>
                </div>

            </div>
        </div>

        {{-- ── أزرار الحفظ ─────────────────────────────────────── --}}
        <div class="flex gap-3 justify-start pt-2">
            <button type="submit"
                    class="px-6 py-2.5 rounded-lg bg-black text-white text-sm font-semibold hover:bg-gray-800 transition">
                {{ $item->exists ? 'حفظ التغييرات' : 'إنشاء الإدخال' }}
            </button>
            <a href="{{ route('admin.footer-company.index') }}"
               class="px-5 py-2.5 rounded-lg border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">
                إلغاء
            </a>
        </div>

    </form>
</div>
@endsection