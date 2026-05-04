<div class="space-y-4">

    {{-- Icon (not translatable) --}}
    <div>
        <label class="block text-sm font-medium mb-1">Icon</label>
        <input type="text" name="icon"
               value="{{ old('icon', $site_feature->icon ?? '') }}"
               placeholder="🚚"
               class="w-full border rounded-xl p-2" />
    </div>

    {{-- Title (translatable) --}}
    <div class="border rounded-xl p-4 space-y-2">
        <p class="text-sm font-semibold text-gray-600">العنوان / Title</p>

        <div class="flex items-center gap-2">
            <span class="w-8 text-center text-xs font-bold text-gray-400">AR</span>
            <input type="text" name="title[ar]"
                   value="{{ old('title.ar', $site_feature->getTranslation('title', 'ar') ?? '') }}"
                   placeholder="شحن مجاني"
                   dir="rtl"
                   class="flex-1 border rounded-xl p-2" />
        </div>

        <div class="flex items-center gap-2">
            <span class="w-8 text-center text-xs font-bold text-gray-400">EN</span>
            <input type="text" name="title[en]"
                   value="{{ old('title.en', $site_feature->getTranslation('title', 'en') ?? '') }}"
                   placeholder="Free Shipping"
                   dir="ltr"
                   class="flex-1 border rounded-xl p-2" />
        </div>
    </div>

    {{-- Description (translatable) --}}
    <div class="border rounded-xl p-4 space-y-2">
        <p class="text-sm font-semibold text-gray-600">الوصف / Description</p>

        <div class="flex items-center gap-2">
            <span class="w-8 text-center text-xs font-bold text-gray-400">AR</span>
            <input type="text" name="description[ar]"
                   value="{{ old('description.ar', $site_feature->getTranslation('description', 'ar') ?? '') }}"
                   placeholder="على كل طلب فوق 50 د.أ"
                   dir="rtl"
                   class="flex-1 border rounded-xl p-2" />
        </div>

        <div class="flex items-center gap-2">
            <span class="w-8 text-center text-xs font-bold text-gray-400">EN</span>
            <input type="text" name="description[en]"
                   value="{{ old('description.en', $site_feature->getTranslation('description', 'en') ?? '') }}"
                   placeholder="On all orders above $50"
                   dir="ltr"
                   class="flex-1 border rounded-xl p-2" />
        </div>
    </div>

    {{-- Sort order --}}
    <div>
        <label class="block text-sm font-medium mb-1">الترتيب</label>
        <input type="number" name="sort_order"
               value="{{ old('sort_order', $site_feature->sort_order ?? 0) }}"
               class="w-full border rounded-xl p-2" />
    </div>

    {{-- Active toggle --}}
    <label class="flex items-center gap-2 cursor-pointer">
        <input type="checkbox" name="is_active" value="1"
               {{ old('is_active', $site_feature->is_active ?? true) ? 'checked' : '' }}>
        <span>مفعل</span>
    </label>

</div>