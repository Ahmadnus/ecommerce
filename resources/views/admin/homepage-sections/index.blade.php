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
                    <label class="text-xs font-bold mb-1 block">مكان العرض (Position)</label>
                    <select name="position" required
                            class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
                        <option value="" disabled {{ old('position') ? '' : 'selected' }}>-- اختر مكان العرض --</option>
                        @foreach(\App\Models\HomepageSection::POSITIONS as $value => $label)
                        <option value="{{ $value }}" {{ old('position') === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                        @endforeach
                    </select>
                    @error('position')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
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

                <div class="md:col-span-2">
                    <label class="text-xs font-bold mb-1 block">ملف الوسائط (صورة طولية أو فيديو)</label>
                    <input type="file" name="media" accept="image/*,video/mp4,video/webm" class="w-full px-4 py-2 rounded-xl border">
                    @error('media')<p class="text-xs text-red-500 -mt-2">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="text-xs font-bold mb-1 block">ترتيب فرعي (عند تعدد الأقسام لنفس المكان)</label>
                    <input type="number" name="sort_order" min="0" value="{{ old('sort_order', 0) }}"
                           class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
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

                {{-- ── Small underlined text link (independent of the CTA button) ── --}}
                <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-4 p-4 rounded-xl bg-gray-50 border border-gray-100">
                    <div>
                        <label class="text-xs font-bold mb-1 block">نص الرابط الصغير (Link Text)</label>
                        <input type="text" name="link_text" value="{{ old('link_text') }}"
                               class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
                        @error('link_text')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-xs font-bold mb-1 block">رابط الوجهة (Link URL)</label>
                        <input type="text" name="link_url" value="{{ old('link_url') }}"
                               placeholder="https://example.com/..."
                               class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
                        @error('link_url')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-xs font-bold mb-1 block">شكل الرابط (Link Style)</label>
                        <select name="link_style"
                                class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
                            @foreach(\App\Models\HomepageSection::LINK_STYLES as $value => $label)
                            <option value="{{ $value }}" {{ old('link_style', 'underline') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <p class="text-[10px] text-gray-400 md:col-span-3">
                        رابط نصي صغير تحته خط — مستقل تماماً عن زر الـ CTA أعلاه.
                    </p>
                </div>

                {{-- ── Custom colors ────────────────────────────────────── --}}
                <div class="md:col-span-2 grid grid-cols-2 md:grid-cols-5 gap-4 p-4 rounded-xl bg-gray-50 border border-gray-100">
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
                        <label class="text-xs font-bold mb-1 block">لون الرابط الصغير</label>
                        <input type="color" name="link_color" value="{{ old('link_color', '#111827') }}"
                               class="w-full h-10 rounded-lg border cursor-pointer">
                        @error('link_color')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
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
                    <p class="text-[10px] text-gray-400 col-span-2 md:col-span-5">
                        اترك بدون تغيير لاستخدام الألوان الافتراضية للموقع.
                    </p>
                </div>

                {{-- ── Typography (independent font family per text element) ── --}}
                <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-4 p-4 rounded-xl bg-gray-50 border border-gray-100">
                    <div>
                        <label class="text-xs font-bold mb-1 block">خط العنوان (Title Font)</label>
                        <select name="title_font_family"
                                class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
                            @foreach(\App\Models\HomepageSection::FONT_FAMILIES as $value => $label)
                            <option value="{{ $value }}" {{ old('title_font_family', 'default') === $value ? 'selected' : '' }}>
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
                            <option value="{{ $value }}" {{ old('paragraph_font_family', 'default') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-bold mb-1 block">خط الرابط (Link Font)</label>
                        <select name="link_font_family"
                                class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
                            @foreach(\App\Models\HomepageSection::FONT_FAMILIES as $value => $label)
                            <option value="{{ $value }}" {{ old('link_font_family', 'default') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                            @endforeach
                        </select>
                    </div>
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

                <div class="md:col-span-2 flex flex-wrap items-center gap-6">
                    <label class="inline-flex items-center gap-2 text-sm">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <span>نشط (يظهر في الصفحة الرئيسية)</span>
                    </label>
                    <label class="inline-flex items-center gap-2 text-sm">
                        <input type="checkbox" name="show_text_below_media" value="1" {{ old('show_text_below_media', true) ? 'checked' : '' }}>
                        <span>إظهار النص أسفل/فوق الوسائط (Show Text)</span>
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
                    <th class="px-6 py-4">مكان العرض</th>
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
                            {{ \App\Models\HomepageSection::POSITIONS[$item->position] ?? $item->position }}
                        </span>
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
                    <td colspan="7" class="p-6">
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
                                    <label class="text-xs font-bold mb-1 block">مكان العرض (Position)</label>
                                    <select name="position" required
                                            class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
                                        @foreach(\App\Models\HomepageSection::POSITIONS as $value => $label)
                                        <option value="{{ $value }}" {{ $item->position === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="text-xs font-bold mb-1 block">نوع الوسائط</label>
                                    <select name="media_type"
                                            class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
                                        <option value="none" {{ $item->media_type === 'none' ? 'selected' : '' }}>بدون وسائط (نص فقط)</option>
                                        <option value="image" {{ $item->media_type === 'image' ? 'selected' : '' }}>صورة طولية (Portrait)</option>
                                        <option value="video" {{ $item->media_type === 'video' ? 'selected' : '' }}>فيديو طولي (Portrait)</option>
                                    </select>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="text-xs font-bold mb-1 block">استبدال ملف الوسائط (اختياري)</label>
                                    <input type="file" name="media" accept="image/*,video/mp4,video/webm" class="w-full px-4 py-2 rounded-xl border">
                                </div>

                                <div>
                                    <label class="text-xs font-bold mb-1 block">ترتيب فرعي (عند تعدد الأقسام لنفس المكان)</label>
                                    <input type="number" name="sort_order" min="0" value="{{ $item->sort_order }}"
                                           class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
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

                                {{-- ── Small underlined text link ─────────────────────── --}}
                                <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-4 p-4 rounded-xl bg-white border border-gray-200">
                                    <div>
                                        <label class="text-xs font-bold mb-1 block">نص الرابط الصغير (Link Text)</label>
                                        <input type="text" name="link_text" value="{{ $item->link_text }}"
                                               class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
                                    </div>
                                    <div>
                                        <label class="text-xs font-bold mb-1 block">رابط الوجهة (Link URL)</label>
                                        <input type="text" name="link_url" value="{{ $item->link_url }}"
                                               class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
                                    </div>
                                    <div>
                                        <label class="text-xs font-bold mb-1 block">شكل الرابط (Link Style)</label>
                                        <select name="link_style"
                                                class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
                                            @foreach(\App\Models\HomepageSection::LINK_STYLES as $value => $label)
                                            <option value="{{ $value }}" {{ ($item->link_style ?: 'underline') === $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- ── Custom colors ────────────────────────────────────── --}}
                                <div class="md:col-span-2 grid grid-cols-2 md:grid-cols-5 gap-4 p-4 rounded-xl bg-white border border-gray-200">
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
                                        <label class="text-xs font-bold mb-1 block">لون الرابط الصغير</label>
                                        <input type="color" name="link_color" value="{{ $item->link_color ?: '#111827' }}"
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

                                {{-- ── Typography ──────────────────────────────────────── --}}
                                <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-4 p-4 rounded-xl bg-white border border-gray-200">
                                    <div>
                                        <label class="text-xs font-bold mb-1 block">خط العنوان (Title Font)</label>
                                        <select name="title_font_family"
                                                class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
                                            @foreach(\App\Models\HomepageSection::FONT_FAMILIES as $value => $label)
                                            <option value="{{ $value }}" {{ ($item->title_font_family ?: 'default') === $value ? 'selected' : '' }}>
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
                                            <option value="{{ $value }}" {{ ($item->paragraph_font_family ?: 'default') === $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="text-xs font-bold mb-1 block">خط الرابط (Link Font)</label>
                                        <select name="link_font_family"
                                                class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
                                            @foreach(\App\Models\HomepageSection::FONT_FAMILIES as $value => $label)
                                            <option value="{{ $value }}" {{ ($item->link_font_family ?: 'default') === $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                            @endforeach
                                        </select>
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

                                <div class="md:col-span-2 flex flex-wrap items-center gap-6">
                                    <label class="inline-flex items-center gap-2 text-sm">
                                        <input type="checkbox" name="is_active" value="1" {{ $item->is_active ? 'checked' : '' }}>
                                        <span>نشط</span>
                                    </label>
                                    <label class="inline-flex items-center gap-2 text-sm">
                                        <input type="checkbox" name="show_text_below_media" value="1" {{ ($item->show_text_below_media ?? true) ? 'checked' : '' }}>
                                        <span>إظهار النص أسفل/فوق الوسائط</span>
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
                    <td colspan="7" class="px-6 py-10 text-center text-gray-400">لا توجد أقسام بعد</td>
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
</script>
@endsection
