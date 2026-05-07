@extends('layouts.admin')

@section('title', 'تعديل SEO – ' . ($type === 'main' ? 'الموقع الرئيسي' : 'شاشة الترحيب'))

@section('admin-content')
<div class="max-w-3xl mx-auto">

    <div class="flex items-center gap-3 mb-8">
        <a href="{{ route('admin.seo.index') }}"
           class="text-gray-400 hover:text-gray-700 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">
                إعدادات SEO —
                {{ $type === 'main' ? 'الموقع الرئيسي' : 'شاشة الترحيب' }}
            </h1>
            <p class="text-xs text-gray-400 mt-0.5">
                التغييرات تُطبَّق على تخطيط {{ $type === 'main' ? 'الموقع الرئيسي' : 'شاشة الترحيب' }}
            </p>
        </div>
    </div>

    <form action="{{ route('admin.seo.update', $type) }}" method="POST"
          enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        @if($errors->any())
            <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ── الحقول القابلة للترجمة ──────────────────────────── --}}
        @foreach(['ar' => 'العربية 🇸🇦', 'en' => 'الإنجليزية 🇬🇧'] as $locale => $langLabel)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100 bg-gray-50">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">
                    {{ $langLabel }}
                </span>
            </div>
            <div class="p-5 space-y-4">

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        عنوان SEO
                        <span class="font-normal text-gray-400 mr-1">الحد الأقصى 160 حرف</span>
                    </label>
                    <input type="text"
                           name="seo_title[{{ $locale }}]"
                           value="{{ old("seo_title.$locale", $seo->getTranslation('seo_title', $locale, false)) }}"
                           dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}"
                           maxlength="160"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        وصف SEO
                        <span class="font-normal text-gray-400 mr-1">الحد الأقصى 320 حرف</span>
                    </label>
                    <textarea name="seo_description[{{ $locale }}]"
                              rows="2"
                              dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}"
                              maxlength="320"
                              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300 resize-none">{{ old("seo_description.$locale", $seo->getTranslation('seo_description', $locale, false)) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">الكلمات المفتاحية</label>
                    <input type="text"
                           name="seo_keywords[{{ $locale }}]"
                           value="{{ old("seo_keywords.$locale", $seo->getTranslation('seo_keywords', $locale, false)) }}"
                           dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}"
                           maxlength="500"
                           placeholder="كلمة1, كلمة2, كلمة3"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2 border-t border-gray-100">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                            عنوان OG
                            <span class="font-normal text-gray-400 mr-1">(Open Graph)</span>
                        </label>
                        <input type="text"
                               name="og_title[{{ $locale }}]"
                               value="{{ old("og_title.$locale", $seo->getTranslation('og_title', $locale, false)) }}"
                               dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}"
                               maxlength="160"
                               placeholder="افتراضي: عنوان SEO"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">عنوان تويتر</label>
                        <input type="text"
                               name="twitter_title[{{ $locale }}]"
                               value="{{ old("twitter_title.$locale", $seo->getTranslation('twitter_title', $locale, false)) }}"
                               dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}"
                               maxlength="160"
                               placeholder="افتراضي: عنوان SEO"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">وصف OG</label>
                        <textarea name="og_description[{{ $locale }}]"
                                  rows="2"
                                  dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}"
                                  maxlength="320"
                                  placeholder="افتراضي: وصف SEO"
                                  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300 resize-none">{{ old("og_description.$locale", $seo->getTranslation('og_description', $locale, false)) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">وصف تويتر</label>
                        <textarea name="twitter_description[{{ $locale }}]"
                                  rows="2"
                                  dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}"
                                  maxlength="320"
                                  placeholder="افتراضي: وصف SEO"
                                  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300 resize-none">{{ old("twitter_description.$locale", $seo->getTranslation('twitter_description', $locale, false)) }}</textarea>
                    </div>
                </div>

            </div>
        </div>
        @endforeach

        {{-- ── الإعدادات التقنية ───────────────────────────────── --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100 bg-gray-50">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">الإعدادات التقنية</span>
            </div>
            <div class="p-5 space-y-4">

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">الرابط الأساسي (Canonical URL)</label>
                    <input type="url" name="canonical_url"
                           value="{{ old('canonical_url', $seo->canonical_url) }}"
                           placeholder="https://yoursite.com"
                           dir="ltr"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300">
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Robots</label>
                        <select name="robots"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300">
                            @foreach(['index, follow','noindex, nofollow','noindex, follow','index, nofollow'] as $opt)
                                <option value="{{ $opt }}" @selected(old('robots', $seo->robots) === $opt)>
                                    {{ $opt }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">بطاقة تويتر</label>
                        <select name="twitter_card"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300">
                            @foreach(['summary','summary_large_image','app','player'] as $opt)
                                <option value="{{ $opt }}" @selected(old('twitter_card', $seo->twitter_card) === $opt)>
                                    {{ $opt }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">نوع OG</label>
                        <input type="text" name="og_type"
                               value="{{ old('og_type', $seo->og_type) }}"
                               placeholder="website"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300">
                    </div>
                </div>

            </div>
        </div>

        {{-- ── الصور ───────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100 bg-gray-50">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">الصور</span>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                            صورة OG
                            <span class="font-normal text-gray-400 mr-1">(Open Graph)</span>
                        </label>
                        @if($seo->exists && $seo->getFirstMediaUrl('og_image'))
                            <img src="{{ $seo->getFirstMediaUrl('og_image') }}"
                                 class="w-full h-24 object-cover rounded-lg mb-2 border border-gray-200"
                                 alt="OG Image">
                        @endif
                        <input type="file" name="og_image" accept="image/*"
                               class="block w-full text-xs text-gray-500
                                      file:ml-3 file:py-1.5 file:px-3
                                      file:rounded-lg file:border-0
                                      file:text-xs file:font-semibold
                                      file:bg-gray-100 file:text-gray-700
                                      hover:file:bg-gray-200">
                        <p class="text-xs text-gray-400 mt-1.5">المقاس المثالي: 1200×630px</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">الفافيكون (Favicon)</label>
                        @if($seo->exists && $seo->getFirstMediaUrl('favicon'))
                            <div class="flex items-center gap-2 mb-2">
                                <img src="{{ $seo->getFirstMediaUrl('favicon') }}"
                                     class="w-8 h-8 rounded border border-gray-200"
                                     alt="Favicon">
                                <span class="text-xs text-gray-400">الفافيكون الحالي</span>
                            </div>
                        @endif
                        <input type="file" name="favicon" accept=".ico,.png,.svg"
                               class="block w-full text-xs text-gray-500
                                      file:ml-3 file:py-1.5 file:px-3
                                      file:rounded-lg file:border-0
                                      file:text-xs file:font-semibold
                                      file:bg-gray-100 file:text-gray-700
                                      hover:file:bg-gray-200">
                        <p class="text-xs text-gray-400 mt-1.5">صيغ مقبولة: .ico / .png / .svg · الحد الأقصى 512KB</p>
                    </div>

                </div>
            </div>
        </div>

        {{-- ── الحالة والحفظ ───────────────────────────────────── --}}
        <div class="flex items-center justify-between pt-2">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1"
                       @checked(old('is_active', $seo->is_active ?? true))
                       class="w-4 h-4 rounded border-gray-300">
                <span class="text-sm font-medium text-gray-700">مفعّل</span>
            </label>

            <div class="flex gap-3">
                <a href="{{ route('admin.seo.index') }}"
                   class="px-5 py-2.5 rounded-lg border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">
                    إلغاء
                </a>
                <button type="submit"
                        class="px-6 py-2.5 rounded-lg bg-black text-white text-sm font-semibold hover:bg-gray-800 transition">
                    حفظ الإعدادات
                </button>
            </div>
        </div>

    </form>
</div>
@endsection