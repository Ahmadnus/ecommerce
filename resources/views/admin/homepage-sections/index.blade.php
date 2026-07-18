@extends('layouts.admin')
@section('title', 'الأقسام الديناميكية للصفحة الرئيسية')

@section('admin-content')
<div class="space-y-8">

    {{-- ══════════════════════════════════════════════════════
         ADD FORM
    ══════════════════════════════════════════════════════ --}}
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <h3 class="text-lg font-bold mb-4">إضافة قسم جديد</h3>

        <form action="{{ route('admin.homepage-sections.store') }}"
              method="POST"
              enctype="multipart/form-data"
              class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="text-xs font-bold mb-1 block">العنوان (H1)</label>
                    <input type="text" name="title" value="{{ old('title') }}"
                           class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
                    @error('title')<p class="text-xs text-red-500 -mt-2">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label class="text-xs font-bold mb-1 block">الفقرة الوصفية</label>
                    <textarea name="paragraph" rows="3"
                              class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">{{ old('paragraph') }}</textarea>
                    @error('paragraph')<p class="text-xs text-red-500 -mt-2">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="text-xs font-bold mb-1 block">نوع القسم (Section Type)</label>
                    <select name="section_type" required
                            class="section-type-select w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
                        @foreach(\App\Models\HomepageSection::SECTION_TYPES as $value => $label)
                        <option value="{{ $value }}" {{ old('section_type', 'hero_banner') === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                        @endforeach
                    </select>
                    @error('section_type')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="text-xs font-bold mb-1 block">الترتيب (Sort Order)</label>
                    <input type="number" name="sort_order" min="0" value="{{ old('sort_order', 0) }}"
                           class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
                    <p class="text-[10px] text-gray-400 mt-1">
                        رقم يحدد تسلسل ظهور القسم في الصفحة (1 = الأعلى، 2 = تحته، وهكذا).
                    </p>
                </div>

                {{-- ── Product source — only for product_grid sections ─────── --}}
                <div class="md:col-span-2 js-product-source">
                    <label class="text-xs font-bold mb-1 block">مصدر المنتجات (Product Source)</label>
                    <select name="product_source"
                            class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
                        @foreach(\App\Models\HomepageSection::PRODUCT_SOURCES as $value => $label)
                        <option value="{{ $value }}" {{ old('product_source') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                        <optgroup label="حسب التصنيف (By Category)">
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ (string) old('product_source') === (string) $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                            @endforeach
                        </optgroup>
                    </select>
                    <p class="text-[10px] text-gray-400 mt-1">
                        يُستخدم فقط عندما يكون نوع القسم "شبكة منتجات".
                    </p>
                </div>

                <div class="js-media-type">
                    <label class="text-xs font-bold mb-1 block">نوع الوسائط</label>
                    <select name="media_type"
                            class="section-media-type w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
                        <option value="none" {{ old('media_type', 'none') === 'none' ? 'selected' : '' }}>بدون وسائط (نص فقط)</option>
                        <option value="image" {{ old('media_type') === 'image' ? 'selected' : '' }}>صورة طولية (Portrait)</option>
                        <option value="video" {{ old('media_type') === 'video' ? 'selected' : '' }}>فيديو طولي (Portrait)</option>
                    </select>
                    <p class="text-[10px] text-gray-400 mt-1">
                        يتم عرض الصورة/الفيديو دائماً بنسبة طولية (Portrait) على الواجهة — لا حاجة لأي إعداد إضافي.
                    </p>
                </div>

                <div class="md:col-span-2 js-media-file">
                    <label class="text-xs font-bold mb-1 block">ملف الوسائط (صورة أو فيديو)</label>
                    <input type="file" name="media" accept="image/*,video/mp4,video/webm" class="w-full px-4 py-2 rounded-xl border">
                    @error('media')<p class="text-xs text-red-500 -mt-2">{{ $message }}</p>@enderror
                </div>

                {{-- ── Media layout: aspect ratio + text/button position ──── --}}
                <div class="js-media-layout">
                    <label class="text-xs font-bold mb-1 block">شكل الإطار (Aspect Ratio)</label>
                    <select name="aspect_ratio"
                            class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
                        @foreach(\App\Models\HomepageSection::ASPECT_RATIOS as $value => $label)
                        <option value="{{ $value }}" {{ old('aspect_ratio', 'landscape') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('aspect_ratio')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="js-media-layout">
                    <label class="text-xs font-bold mb-1 block">موضع النص والزر (Text & Button Position)</label>
                    <select name="text_position"
                            class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
                        @foreach(\App\Models\HomepageSection::TEXT_POSITIONS_MAP as $value => $label)
                        <option value="{{ $value }}" {{ old('text_position', 'overlay_center') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('text_position')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="js-legacy-position">
                    <label class="text-xs font-bold mb-1 block">مكان العرض القديم (Position — اختياري)</label>
                    <select name="position"
                            class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
                        <option value="">-- بدون (يُرتّب حسب "الترتيب") --</option>
                        @foreach(\App\Models\HomepageSection::POSITIONS as $value => $label)
                        <option value="{{ $value }}" {{ old('position') === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                        @endforeach
                    </select>
                    @error('position')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="text-xs font-bold mb-1 block">نص الزر (CTA)</label>
                    <input type="text" name="button_text" value="{{ old('button_text') }}"
                           class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
                </div>

                <div>
                    <label class="text-xs font-bold mb-1 block">رابط الزر</label>
                    <input type="text" name="button_url" value="{{ old('button_url') }}"
                           placeholder="https://example.com/..."
                           class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
                </div>

                {{-- ── Custom colors ────────────────────────────────────── --}}
                <div class="md:col-span-2 grid grid-cols-2 md:grid-cols-4 gap-4 p-4 rounded-xl bg-gray-50 border border-gray-100">
                    <div>
                        <label class="text-xs font-bold mb-1 block">لون تمييز عنوان القسم (Section Title Accent Color)</label>
                        <input type="color" name="section_title_accent_color" value="{{ old('section_title_accent_color', '#111827') }}"
                               class="w-full h-10 rounded-lg border cursor-pointer">
                        @error('section_title_accent_color')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-xs font-bold mb-1 block">لون الفقرة الوصفية</label>
                        <input type="color" name="text_color" value="{{ old('text_color', '#111827') }}"
                               class="w-full h-10 rounded-lg border cursor-pointer">
                        @error('text_color')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-xs font-bold mb-1 block">لون خلفية الزر</label>
                        <input type="color" name="button_bg_color" value="{{ old('button_bg_color', '#0ea5e9') }}"
                               class="w-full h-10 rounded-lg border cursor-pointer">
                        @error('button_bg_color')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-xs font-bold mb-1 block">لون نص الزر</label>
                        <input type="color" name="button_text_color" value="{{ old('button_text_color', '#ffffff') }}"
                               class="w-full h-10 rounded-lg border cursor-pointer">
                        @error('button_text_color')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <p class="text-[10px] text-gray-400 col-span-2 md:col-span-4">
                        اترك بدون تغيير لاستخدام الألوان الافتراضية للموقع.
                    </p>
                </div>

                <div>
                    <label class="text-xs font-bold mb-1 block">محاذاة النص (Text Alignment)</label>
                    <select name="text_alignment"
                            class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
                        <option value="" {{ old('text_alignment') ? '' : 'selected' }}>-- افتراضي (Default) --</option>
                        @foreach(\App\Models\HomepageSection::TEXT_ALIGNMENTS as $value => $label)
                        <option value="{{ $value }}" {{ old('text_alignment') === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                        @endforeach
                    </select>
                    @error('text_alignment')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="text-xs font-bold mb-1 block">خط العنوان (Title Font)</label>
                    <select name="title_font_family"
                            class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
                        @foreach(\App\Models\HomepageSection::FONT_FAMILIES as $value => $label)
                        <option value="{{ $value }}" {{ old('title_font_family') === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                        @endforeach
                    </select>
                    @error('title_font_family')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="text-xs font-bold mb-1 block">خط الفقرة (Paragraph Font)</label>
                    <select name="paragraph_font_family"
                            class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
                        @foreach(\App\Models\HomepageSection::FONT_FAMILIES as $value => $label)
                        <option value="{{ $value }}" {{ old('paragraph_font_family') === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                        @endforeach
                    </select>
                    @error('paragraph_font_family')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2 flex items-center gap-3">
                    <label class="inline-flex items-center gap-2 text-sm">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <span>نشط (يظهر في الصفحة الرئيسية)</span>
                    </label>
                </div>

                <div class="md:col-span-2">
                    <button type="submit"
                            class="bg-brand text-white font-bold py-2 px-5 rounded-xl hover:opacity-90 transition">
                        حفظ
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
                    <th class="px-6 py-4">المعاينة</th>
                    <th class="px-6 py-4">العنوان</th>
                    <th class="px-6 py-4">النوع</th>
                    <th class="px-6 py-4">الترتيب</th>
                    <th class="px-6 py-4">الوسائط</th>
                    <th class="px-6 py-4">الزر</th>
                    <th class="px-6 py-4">الحالة</th>
                    <th class="px-6 py-4">الإجراء</th>
                </tr>
            </thead>

            <tbody class="divide-y text-sm">
                @forelse($items as $item)
                <tr>
                    <td class="px-6 py-4">
                        @if($item->hasMedia() && $item->media_type === 'video')
                            <video class="w-16 h-20 object-cover rounded-lg shadow-sm" muted>
                                <source src="{{ $item->media_url }}">
                            </video>
                        @elseif($item->hasMedia())
                            <img src="{{ $item->media_url }}"
                                 class="w-16 h-20 object-cover rounded-lg shadow-sm" alt="">
                        @else
                            <span class="text-gray-300 text-xs">—</span>
                        @endif
                    </td>

                    <td class="px-6 py-4 max-w-[220px] truncate">{{ $item->title ?: '—' }}</td>

                    <td class="px-6 py-4">
                        <span class="text-xs px-2 py-1 rounded bg-purple-50 text-purple-700 border border-purple-100 whitespace-nowrap">
                            {{ \App\Models\HomepageSection::SECTION_TYPES[$item->section_type] ?? $item->section_type }}
                        </span>
                        @if($item->isProductGrid() && $item->productSourceLabel())
                        <span class="block text-[10px] text-gray-400 mt-1">{{ $item->productSourceLabel() }}</span>
                        @endif
                    </td>

                    <td class="px-6 py-4">
                        <span class="text-xs font-bold text-gray-700">{{ $item->sort_order }}</span>
                    </td>

                    <td class="px-6 py-4">
                        <span class="text-xs px-2 py-1 rounded bg-blue-50 text-blue-700 border border-blue-100">
                            {{ ['image' => 'صورة', 'video' => 'فيديو', 'none' => 'بدون'][$item->media_type] ?? $item->media_type }}
                        </span>
                    </td>

                    <td class="px-6 py-4 text-xs text-gray-500 max-w-[160px] truncate">
                        {{ $item->button_text ?: '—' }}
                    </td>

                    <td class="px-6 py-4">
                        <span class="{{ $item->is_active ? 'text-green-600' : 'text-red-400' }}">
                            {{ $item->is_active ? 'نشط' : 'معطل' }}
                        </span>
                    </td>

                    <td class="px-6 py-4">
                        <div class="flex gap-4 items-center">
                            <button type="button" onclick="toggleEdit({{ $item->id }})"
                                    class="text-blue-500 hover:underline">تعديل</button>

                            <form action="{{ route('admin.homepage-sections.destroy', $item) }}"
                                  method="POST"
                                  onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                @csrf @method('DELETE')
                                <button class="text-red-500 hover:underline" type="submit">حذف</button>
                            </form>
                        </div>
                    </td>
                </tr>

                {{-- ── Inline edit row ──────────────────────────────── --}}
                <tr id="edit-{{ $item->id }}" class="hidden bg-gray-50">
                    <td colspan="8" class="p-6">
                        <form action="{{ route('admin.homepage-sections.update', $item) }}"
                              method="POST"
                              enctype="multipart/form-data"
                              class="space-y-5">
                            @csrf @method('PUT')

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label class="text-xs font-bold mb-1 block">العنوان (H1)</label>
                                    <input type="text" name="title" value="{{ $item->title }}"
                                           class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
                                </div>

                                <div class="md:col-span-2">
                                    <label class="text-xs font-bold mb-1 block">الفقرة الوصفية</label>
                                    <textarea name="paragraph" rows="3"
                                              class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">{{ $item->paragraph }}</textarea>
                                </div>

                                <div>
                                    <label class="text-xs font-bold mb-1 block">نوع القسم (Section Type)</label>
                                    <select name="section_type" required
                                            class="section-type-select w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
                                        @foreach(\App\Models\HomepageSection::SECTION_TYPES as $value => $label)
                                        <option value="{{ $value }}" {{ ($item->section_type ?? 'banner') === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="text-xs font-bold mb-1 block">الترتيب (Sort Order)</label>
                                    <input type="number" name="sort_order" min="0" value="{{ $item->sort_order }}"
                                           class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
                                    <p class="text-[10px] text-gray-400 mt-1">
                                        رقم يحدد تسلسل ظهور القسم (1 = الأعلى).
                                    </p>
                                </div>

                                <div class="md:col-span-2 js-product-source">
                                    <label class="text-xs font-bold mb-1 block">مصدر المنتجات (Product Source)</label>
                                    <select name="product_source"
                                            class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
                                        @foreach(\App\Models\HomepageSection::PRODUCT_SOURCES as $value => $label)
                                        <option value="{{ $value }}" {{ (string) $item->product_source === $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                        <optgroup label="حسب التصنيف (By Category)">
                                            @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}" {{ (string) $item->product_source === (string) $cat->id ? 'selected' : '' }}>
                                                {{ $cat->name }}
                                            </option>
                                            @endforeach
                                        </optgroup>
                                    </select>
                                </div>

                                <div class="js-media-type">
                                    <label class="text-xs font-bold mb-1 block">نوع الوسائط</label>
                                    <select name="media_type"
                                            class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
                                        <option value="none" {{ $item->media_type === 'none' ? 'selected' : '' }}>بدون وسائط (نص فقط)</option>
                                        <option value="image" {{ $item->media_type === 'image' ? 'selected' : '' }}>صورة طولية (Portrait)</option>
                                        <option value="video" {{ $item->media_type === 'video' ? 'selected' : '' }}>فيديو طولي (Portrait)</option>
                                    </select>
                                </div>

                                <div class="md:col-span-2 js-media-file">
                                    <label class="text-xs font-bold mb-1 block">استبدال ملف الوسائط (اختياري)</label>
                                    <input type="file" name="media" accept="image/*,video/mp4,video/webm" class="w-full px-4 py-2 rounded-xl border">
                                </div>

                                <div class="js-media-layout">
                                    <label class="text-xs font-bold mb-1 block">شكل الإطار (Aspect Ratio)</label>
                                    <select name="aspect_ratio"
                                            class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
                                        @foreach(\App\Models\HomepageSection::ASPECT_RATIOS as $value => $label)
                                        <option value="{{ $value }}" {{ ($item->aspect_ratio ?? 'landscape') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="js-media-layout">
                                    <label class="text-xs font-bold mb-1 block">موضع النص والزر (Text & Button Position)</label>
                                    <select name="text_position"
                                            class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
                                        @foreach(\App\Models\HomepageSection::TEXT_POSITIONS_MAP as $value => $label)
                                        <option value="{{ $value }}" {{ ($item->text_position ?? 'overlay_center') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="js-legacy-position">
                                    <label class="text-xs font-bold mb-1 block">مكان العرض القديم (Position — اختياري)</label>
                                    <select name="position"
                                            class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
                                        <option value="" {{ $item->position ? '' : 'selected' }}>-- بدون (يُرتّب حسب "الترتيب") --</option>
                                        @foreach(\App\Models\HomepageSection::POSITIONS as $value => $label)
                                        <option value="{{ $value }}" {{ $item->position === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="text-xs font-bold mb-1 block">نص الزر (CTA)</label>
                                    <input type="text" name="button_text" value="{{ $item->button_text }}"
                                           class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
                                </div>

                                <div>
                                    <label class="text-xs font-bold mb-1 block">رابط الزر</label>
                                    <input type="text" name="button_url" value="{{ $item->button_url }}"
                                           class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
                                </div>

                                {{-- ── Custom colors ────────────────────────────────────── --}}
                                <div class="md:col-span-2 grid grid-cols-2 md:grid-cols-4 gap-4 p-4 rounded-xl bg-white border border-gray-200">
                                    <div>
                                        <label class="text-xs font-bold mb-1 block">لون تمييز عنوان القسم (Section Title Accent Color)</label>
                                        <input type="color" name="section_title_accent_color" value="{{ $item->section_title_accent_color ?: '#111827' }}"
                                               class="w-full h-10 rounded-lg border cursor-pointer">
                                    </div>
                                    <div>
                                        <label class="text-xs font-bold mb-1 block">لون الفقرة الوصفية</label>
                                        <input type="color" name="text_color" value="{{ $item->text_color ?: '#111827' }}"
                                               class="w-full h-10 rounded-lg border cursor-pointer">
                                    </div>
                                    <div>
                                        <label class="text-xs font-bold mb-1 block">لون خلفية الزر</label>
                                        <input type="color" name="button_bg_color" value="{{ $item->button_bg_color ?: '#0ea5e9' }}"
                                               class="w-full h-10 rounded-lg border cursor-pointer">
                                    </div>
                                    <div>
                                        <label class="text-xs font-bold mb-1 block">لون نص الزر</label>
                                        <input type="color" name="button_text_color" value="{{ $item->button_text_color ?: '#ffffff' }}"
                                               class="w-full h-10 rounded-lg border cursor-pointer">
                                    </div>
                                </div>

                                <div>
                                    <label class="text-xs font-bold mb-1 block">محاذاة النص (Text Alignment)</label>
                                    <select name="text_alignment"
                                            class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
                                        <option value="" {{ $item->text_alignment ? '' : 'selected' }}>-- افتراضي (Default) --</option>
                                        @foreach(\App\Models\HomepageSection::TEXT_ALIGNMENTS as $value => $label)
                                        <option value="{{ $value }}" {{ $item->text_alignment === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="text-xs font-bold mb-1 block">خط العنوان (Title Font)</label>
                                    <select name="title_font_family"
                                            class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
                                        @foreach(\App\Models\HomepageSection::FONT_FAMILIES as $value => $label)
                                        <option value="{{ $value }}" {{ $item->title_font_family === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="text-xs font-bold mb-1 block">خط الفقرة (Paragraph Font)</label>
                                    <select name="paragraph_font_family"
                                            class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
                                        @foreach(\App\Models\HomepageSection::FONT_FAMILIES as $value => $label)
                                        <option value="{{ $value }}" {{ $item->paragraph_font_family === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="md:col-span-2 flex items-center gap-3">
                                    <label class="inline-flex items-center gap-2 text-sm">
                                        <input type="checkbox" name="is_active" value="1" {{ $item->is_active ? 'checked' : '' }}>
                                        <span>نشط</span>
                                    </label>

                                    <button type="submit"
                                            class="bg-green-600 text-white font-bold py-2 px-5 rounded-xl hover:opacity-90 transition">
                                        تحديث
                                    </button>

                                    <button type="button" onclick="toggleEdit({{ $item->id }})"
                                            class="bg-gray-100 text-gray-700 font-bold py-2 px-5 rounded-xl hover:bg-gray-200 transition">
                                        إغلاق
                                    </button>
                                </div>
                            </div>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-10 text-center text-gray-400">لا توجد أقسام بعد</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
function toggleEdit(id) {
    const el = document.getElementById('edit-' + id);
    if (el) el.classList.toggle('hidden');
}

/*
 * Show only the fields relevant to the chosen section_type, per form:
 *   - hero_banner     → media (image/video) + overlay text/CTA
 *   - custom_image    → media (image)
 *   - product_grid    → product source
 *   - categories_grid → (no media, no source — just title/sort_order)
 *   - text_block      → text/CTA fields only
 */
function applySectionType(select) {
    const form = select.closest('form');
    if (!form) return;
    const type = select.value;
    const usesMedia = (type === 'hero_banner' || type === 'custom_image' || type === 'banner');

    const setVisible = (selector, visible) => {
        form.querySelectorAll(selector).forEach(el => {
            el.style.display = visible ? '' : 'none';
        });
    };

    setVisible('.js-media-type', usesMedia);
    setVisible('.js-media-file', usesMedia);
    setVisible('.js-media-layout', usesMedia);
    setVisible('.js-product-source', type === 'product_grid');
}

document.querySelectorAll('.section-type-select').forEach(sel => {
    applySectionType(sel);
    sel.addEventListener('change', () => applySectionType(sel));
});
</script>
@endsection
