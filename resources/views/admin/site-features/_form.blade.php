<div class="space-y-4">

    <input type="text" name="icon" value="{{ old('icon', $site_feature->icon ?? '') }}"
           placeholder="🚚"
           class="w-full border rounded-xl p-2" />

    <input type="text" name="title" value="{{ old('title', $site_feature->title ?? '') }}"
           placeholder="العنوان"
           class="w-full border rounded-xl p-2" />

    <input type="text" name="description" value="{{ old('description', $site_feature->description ?? '') }}"
           placeholder="الوصف"
           class="w-full border rounded-xl p-2" />

    <input type="number" name="sort_order" value="{{ old('sort_order', $site_feature->sort_order ?? 0) }}"
           class="w-full border rounded-xl p-2" />

    <label class="flex items-center gap-2">
        <input type="checkbox" name="is_active" value="1"
               {{ old('is_active', $site_feature->is_active ?? true) ? 'checked' : '' }}>
        مفعل
    </label>

</div>
