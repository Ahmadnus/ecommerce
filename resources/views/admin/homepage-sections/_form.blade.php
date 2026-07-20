{{--
    admin/homepage-sections/_form.blade.php
    ─────────────────────────────────────────────────────────────────────────────
    Shared Block/Cube form — used by BOTH the "add cube" card ($item = null)
    and every inline edit row ($item = HomepageSection). All values fall back
    through old() → $item → sensible default, so one partial serves both.
--}}
@php
    /** @var \App\Models\HomepageSection|null $item */
    $v = fn (string $key, $default = null) => old($key, $item?->{$key} ?? $default);
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">

    {{-- ═══ 1) هوية المكعب ═══════════════════════════════════════════ --}}
    <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4 p-4 rounded-xl bg-indigo-50/50 border border-indigo-100">
        <div>
            <label class="text-xs font-bold mb-1 block text-indigo-900">نوع المكعب (Block Type) <span class="text-red-500">*</span></label>
            <select name="section_type" required
                    class="section-type-select w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
                @foreach(\App\Models\HomepageSection::SECTION_TYPES as $value => $label)
                <option value="{{ $value }}" {{ $v('section_type', 'hero_banner') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
                @if($item && isset(\App\Models\HomepageSection::LEGACY_SECTION_TYPES[$item->section_type]))
                <option value="{{ $item->section_type }}" selected>{{ \App\Models\HomepageSection::LEGACY_SECTION_TYPES[$item->section_type] }}</option>
                @endif
            </select>
            <p class="text-[10px] text-indigo-400 mt-1">كل مكعب عنصر مستقل — بانر، وسائط، نص حر، تصنيفات أو منتجات.</p>
            @error('section_type')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="text-xs font-bold mb-1 block text-indigo-900">ترتيب الظهور (Sort Order)</label>
            <input type="number" name="sort_order" min="0" value="{{ $v('sort_order', 0) }}"
                   class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
            <p class="text-[10px] text-indigo-400 mt-1">1 = أول مكعب أعلى الصفحة، 2 = تحته، وهكذا نزولاً.</p>
            @error('sort_order')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>
    </div>

    {{-- ═══ 2) المحتوى النصي ═════════════════════════════════════════ --}}
    <div class="md:col-span-2">
        <label class="text-xs font-bold mb-1 block">العنوان الرئيسي</label>
        <input type="text" name="title" value="{{ $v('title') }}"
               class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
        @error('title')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
    </div>

    <div class="md:col-span-2">
        <label class="text-xs font-bold mb-1 block">الفقرة الوصفية</label>
        <textarea name="paragraph" rows="3"
                  class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">{{ $v('paragraph') }}</textarea>
        @error('paragraph')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
    </div>

    {{-- ═══ 3) مصدر المنتجات — شبكة المنتجات فقط ═════════════════════ --}}
    <div class="md:col-span-2 js-product-source">
        <label class="text-xs font-bold mb-1 block">مصدر المنتجات (Product Source)</label>
        <select name="product_source"
                class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
            @foreach(\App\Models\HomepageSection::PRODUCT_SOURCES as $value => $label)
            <option value="{{ $value }}" {{ (string) $v('product_source') === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
            <optgroup label="حسب التصنيف (By Category)">
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ (string) $v('product_source') === (string) $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </optgroup>
        </select>
        <p class="text-[10px] text-gray-400 mt-1">يُستخدم فقط لمكعب "شبكة منتجات": أحدث المنتجات، الأكثر مبيعاً، المميزة، أو تصنيف محدد.</p>
    </div>

    {{-- ═══ 4) الوسائط ═══════════════════════════════════════════════ --}}
    <div class="js-media-type">
        <label class="text-xs font-bold mb-1 block">نوع الوسائط</label>
        <select name="media_type"
                class="section-media-type w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
            <option value="none"  {{ $v('media_type', 'none') === 'none' ? 'selected' : '' }}>بدون وسائط (نص فقط)</option>
            <option value="image" {{ $v('media_type') === 'image' ? 'selected' : '' }}>صورة</option>
            <option value="video" {{ $v('media_type') === 'video' ? 'selected' : '' }}>فيديو (يعمل تلقائياً بصمت وبشكل متكرر)</option>
        </select>
    </div>

    <div class="js-media-file">
        <label class="text-xs font-bold mb-1 block">{{ $item ? 'استبدال ملف الوسائط (اختياري)' : 'ملف الوسائط (صورة أو فيديو)' }}</label>
        <input type="file" name="media" accept="image/*,video/mp4,video/webm" class="w-full px-4 py-2 rounded-xl border">
        <p class="text-[10px] text-gray-400 mt-1">يُحفظ الملف مباشرة على قرص التخزين العام للموقع.</p>
        @error('media')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
    </div>

    <div class="md:col-span-2 js-video-url">
        <label class="text-xs font-bold mb-1 block">رابط فيديو خارجي (بديل عن رفع ملف)</label>
        <input type="url" name="video_url" value="{{ $v('video_url') }}"
               placeholder="https://example.com/video.mp4"
               class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand" dir="ltr">
        <p class="text-[10px] text-gray-400 mt-1">رابط مباشر لملف mp4/webm — الملف المرفوع (إن وُجد) له الأولوية.</p>
        @error('video_url')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
    </div>

    {{-- ═══ 5) هندسة الإطار والتموضع ═════════════════════════════════ --}}
    <div class="js-media-layout">
        <label class="text-xs font-bold mb-1 block">أبعاد وطبيعة الإطار (Aspect Ratio)</label>
        <select name="aspect_ratio"
                class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
            @foreach(\App\Models\HomepageSection::ASPECT_RATIOS as $value => $label)
            <option value="{{ $value }}" {{ $v('aspect_ratio', 'landscape') === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        @error('aspect_ratio')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
    </div>

    <div class="js-media-layout">
        <label class="text-xs font-bold mb-1 block">تموضع النصوص والأزرار (Text & Button Position)</label>
        <select name="text_position"
                class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
            @foreach(\App\Models\HomepageSection::TEXT_POSITIONS_MAP as $value => $label)
            <option value="{{ $value }}" {{ $v('text_position', 'overlay_center') === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        @error('text_position')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="text-xs font-bold mb-1 block">المسافة الرأسية حول المكعب (Padding)</label>
        <select name="padding_settings"
                class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
            <option value="" {{ $v('padding_settings') ? '' : 'selected' }}>-- افتراضي (إيقاع الصفحة الموحد) --</option>
            @foreach(\App\Models\HomepageSection::PADDING_OPTIONS as $value => $label)
            <option value="{{ $value }}" {{ $v('padding_settings') === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        @error('padding_settings')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="text-xs font-bold mb-1 block">لون خلفية المكعب (اختياري)</label>
        @php $bg = $v('background_color'); @endphp
        <div class="flex items-center gap-2">
            <input type="checkbox" class="js-bg-toggle" {{ $bg ? 'checked' : '' }}
                   title="تفعيل لون الخلفية">
            <input type="color" name="background_color" value="{{ $bg ?: '#f8f7f4' }}"
                   {{ $bg ? '' : 'disabled' }}
                   class="js-bg-color w-full h-10 rounded-lg border cursor-pointer disabled:opacity-30">
        </div>
        <p class="text-[10px] text-gray-400 mt-1">فعّل المربع لاختيار لون؛ اتركه بدون تفعيل = خلفية شفافة.</p>
        @error('background_color')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
    </div>

    {{-- ═══ 6) الزر (CTA) ════════════════════════════════════════════ --}}
    <div>
        <label class="text-xs font-bold mb-1 block">نص الزر (CTA)</label>
        <input type="text" name="button_text" value="{{ $v('button_text') }}"
               class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
    </div>

    <div>
        <label class="text-xs font-bold mb-1 block">رابط الزر (وجهة مخصصة)</label>
        <input type="text" name="button_url" value="{{ $v('button_url') }}"
               placeholder="/products أو https://..."
               class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand" dir="ltr">
    </div>

    {{-- ═══ 7) الألوان والخطوط الفاخرة ═══════════════════════════════ --}}
    <div class="md:col-span-2 grid grid-cols-2 md:grid-cols-4 gap-4 p-4 rounded-xl bg-gray-50 border border-gray-100">
        <div>
            <label class="text-xs font-bold mb-1 block">لون العنوان</label>
            <input type="color" name="section_title_accent_color" value="{{ $v('section_title_accent_color') ?: '#111827' }}"
                   class="w-full h-10 rounded-lg border cursor-pointer">
        </div>
        <div>
            <label class="text-xs font-bold mb-1 block">لون الفقرة</label>
            <input type="color" name="text_color" value="{{ $v('text_color') ?: '#111827' }}"
                   class="w-full h-10 rounded-lg border cursor-pointer">
        </div>
        <div>
            <label class="text-xs font-bold mb-1 block">لون خلفية الزر</label>
            <input type="color" name="button_bg_color" value="{{ $v('button_bg_color') ?: '#0ea5e9' }}"
                   class="w-full h-10 rounded-lg border cursor-pointer">
        </div>
        <div>
            <label class="text-xs font-bold mb-1 block">لون نص الزر</label>
            <input type="color" name="button_text_color" value="{{ $v('button_text_color') ?: '#ffffff' }}"
                   class="w-full h-10 rounded-lg border cursor-pointer">
        </div>
    </div>

    <div>
        <label class="text-xs font-bold mb-1 block">محاذاة النص</label>
        <select name="text_alignment"
                class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
            <option value="" {{ $v('text_alignment') ? '' : 'selected' }}>-- افتراضي --</option>
            @foreach(\App\Models\HomepageSection::TEXT_ALIGNMENTS as $value => $label)
            <option value="{{ $value }}" {{ $v('text_alignment') === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="text-xs font-bold mb-1 block">خط العنوان</label>
            <select name="title_font_family"
                    class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
                @foreach(\App\Models\HomepageSection::FONT_FAMILIES as $value => $label)
                <option value="{{ $value }}" {{ $v('title_font_family') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-xs font-bold mb-1 block">خط الفقرة</label>
            <select name="paragraph_font_family"
                    class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
                @foreach(\App\Models\HomepageSection::FONT_FAMILIES as $value => $label)
                <option value="{{ $value }}" {{ $v('paragraph_font_family') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Legacy position — kept for old data, collapsed out of the way --}}
    <div class="js-legacy-position">
        <label class="text-xs font-bold mb-1 block text-gray-400">مكان العرض القديم (اختياري)</label>
        <select name="position"
                class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
            <option value="" {{ $v('position') ? '' : 'selected' }}>-- بدون (يُرتّب حسب "ترتيب الظهور") --</option>
            @foreach(\App\Models\HomepageSection::POSITIONS as $value => $label)
            <option value="{{ $value }}" {{ $v('position') === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    {{-- ═══ 8) الحالة والحفظ ═════════════════════════════════════════ --}}
    <div class="md:col-span-2 flex items-center gap-3 pt-1">
        <label class="inline-flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_active" value="1" {{ $v('is_active', ! $item) ? 'checked' : '' }}>
            <span>نشط (يظهر في الصفحة الرئيسية)</span>
        </label>

        <button type="submit"
                class="{{ $item ? 'bg-green-600' : 'bg-brand' }} text-white font-bold py-2 px-6 rounded-xl hover:opacity-90 transition">
            {{ $item ? 'تحديث المكعب' : 'إضافة المكعب' }}
        </button>

        @if($item)
        <button type="button" onclick="toggleEdit({{ $item->id }})"
                class="bg-gray-100 text-gray-700 font-bold py-2 px-5 rounded-xl hover:bg-gray-200 transition">
            إغلاق
        </button>
        @endif
    </div>
</div>
