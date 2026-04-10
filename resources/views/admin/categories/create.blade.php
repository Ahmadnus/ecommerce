@extends('layouts.admin')
@section('title', 'إضافة تصنيف جديد')

@section('admin-content')
<div class="max-w-2xl mx-auto">

    {{-- Back --}}
    <div class="mb-6">
        <a href="{{ route('admin.categories.index') }}"
           class="text-gray-500 hover:text-brand flex items-center gap-2 text-sm transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            العودة للتصنيفات
        </a>
    </div>

    {{-- Validation errors --}}
    @if($errors->any())
    <div class="mb-6 p-4 bg-red-50 border-r-4 border-red-500 text-red-700 rounded-xl text-sm">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div class="bg-white p-8 rounded-2xl border border-gray-200 shadow-sm space-y-6">

            {{-- Name --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">اسم التصنيف <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       oninput="autoSlug(this.value)"
                       class="w-full border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand/30 focus:border-brand p-3 bg-gray-50 transition"
                       placeholder="مثال: بناطيل الرجال">
            </div>

            {{-- Slug --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    الـ Slug
                    <span class="text-gray-400 font-normal text-xs">(يُولَّد تلقائياً)</span>
                </label>
                <input type="text" name="slug" id="slug-input" value="{{ old('slug') }}"
                       class="w-full border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand/30 focus:border-brand p-3 bg-gray-50 font-mono text-sm transition"
                       placeholder="men-pants">
            </div>

            {{-- ── Parent Category Selector ─────────────────────────────── --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    التصنيف الأب
                    <span class="text-gray-400 font-normal text-xs">(اتركه فارغاً لإنشاء تصنيف رئيسي)</span>
                </label>

                {{-- Visual nested tree of selectable parents --}}
                <div class="border border-gray-200 rounded-xl overflow-hidden bg-gray-50">
                    {{-- "None" option --}}
                    <label class="flex items-center gap-3 px-4 py-3 cursor-pointer hover:bg-white transition-colors border-b border-gray-100">
                        <input type="radio" name="parent_id" value=""
                               {{ old("parent_id", $preselectedParentId ?? "") === "" ? "checked" : "" }}
                               class="w-4 h-4 text-brand border-gray-300 focus:ring-brand">
                        <span class="text-sm font-semibold text-gray-800">بدون أب — تصنيف رئيسي</span>
                        <span class="mr-auto text-[10px] bg-brand/10 text-brand px-2 py-0.5 rounded-full font-bold">Root</span>
                    </label>

                    {{-- Root categories --}}
                    @foreach($parentOptions->where('depth', 0) as $root)
                    <div class="border-b border-gray-100 last:border-0">
                        <label class="flex items-center gap-3 px-4 py-3 cursor-pointer hover:bg-white transition-colors">
                            <input type="radio" name="parent_id" value="{{ $root->id }}"
                                   {{ old("parent_id", $preselectedParentId ?? "") == $root->id ? "checked" : "" }}
                                   class="w-4 h-4 text-brand border-gray-300 focus:ring-brand">
                            <span class="text-sm font-medium text-gray-800">{{ $root->name }}</span>
                            <span class="mr-auto text-[10px] text-gray-400">{{ $root->slug }}</span>
                        </label>

                        {{-- Sub-categories (depth 1) --}}
                        @foreach($parentOptions->where('parent_id', $root->id) as $sub)
                        <label class="flex items-center gap-3 px-4 py-2.5 ps-10 cursor-pointer hover:bg-white transition-colors bg-gray-50/70 border-t border-gray-100">
                            <input type="radio" name="parent_id" value="{{ $sub->id }}"
                                   {{ old('parent_id') == $sub->id ? 'checked' : '' }}
                                   class="w-4 h-4 text-brand border-gray-300 focus:ring-brand">
                            <svg class="w-3.5 h-3.5 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            <span class="text-sm text-gray-700">{{ $sub->name }}</span>
                            <span class="mr-auto text-[10px] text-gray-400">{{ $sub->slug }}</span>
                        </label>

                        {{-- Sub-sub-categories (depth 2) --}}
                        @foreach($parentOptions->where('parent_id', $sub->id) as $subsub)
                        <label class="flex items-center gap-3 px-4 py-2 ps-16 cursor-pointer hover:bg-white transition-colors bg-gray-50/40 border-t border-gray-100">
                            <input type="radio" name="parent_id" value="{{ $subsub->id }}"
                                   {{ old('parent_id') == $subsub->id ? 'checked' : '' }}
                                   class="w-4 h-4 text-brand border-gray-300 focus:ring-brand">
                            <svg class="w-3 h-3 text-gray-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            <span class="text-sm text-gray-600">{{ $subsub->name }}</span>
                        </label>
                        @endforeach

                        @endforeach
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Description --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">الوصف (اختياري)</label>
                <textarea name="description" rows="3"
                          class="w-full border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand/30 focus:border-brand p-3 bg-gray-50 text-sm transition resize-none"
                          placeholder="وصف مختصر للتصنيف...">{{ old('description') }}</textarea>
            </div>

            {{-- Sort order --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">ترتيب العرض</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                       class="w-32 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand/30 focus:border-brand p-3 bg-gray-50 transition">
            </div>

            {{-- Image --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-3">صورة التصنيف (اختياري)</label>
                <div class="flex items-center gap-5">
                    <div id="img-preview-wrap"
                         class="w-24 h-24 border-2 border-dashed border-gray-200 rounded-2xl flex items-center justify-center overflow-hidden bg-gray-50 flex-shrink-0">
                        <svg id="img-placeholder" class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <img id="img-preview" class="w-full h-full object-cover hidden">
                    </div>
                    <input type="file" name="image" accept="image/*"
                           onchange="previewImg(this)"
                           class="block text-sm text-gray-500 file:ml-4 file:py-2.5 file:px-6 file:rounded-xl file:border-0 file:bg-brand/10 file:text-brand file:font-bold hover:file:bg-brand/20 transition cursor-pointer">
                </div>
            </div>

            {{-- is_active --}}
            <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-xl bg-gray-50 hover:bg-white transition cursor-pointer">
                <input type="checkbox" name="is_active" value="1"
                       {{ old('is_active', true) ? 'checked' : '' }}
                       class="w-5 h-5 text-brand border-gray-300 rounded focus:ring-brand">
                <div>
                    <p class="text-sm font-semibold text-gray-800">تفعيل التصنيف</p>
                    <p class="text-xs text-gray-400">سيظهر في القائمة الأمامية للمتجر</p>
                </div>
            </label>

        </div>

        {{-- Actions --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.categories.index') }}"
               class="px-6 py-3 text-gray-500 font-bold hover:text-red-500 transition text-sm">
                إلغاء
            </a>
            <button type="submit"
                    class="bg-brand text-white px-10 py-3 rounded-xl font-bold shadow-lg shadow-brand/20 hover:bg-brand/90 hover:scale-[1.02] transition-transform active:scale-95">
                حفظ التصنيف
            </button>
        </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
function autoSlug(val) {
    const slugInput = document.getElementById('slug-input');
    // Only auto-fill if user hasn't manually edited slug
    if (!slugInput.dataset.manual) {
        slugInput.value = val
            .toLowerCase()
            .trim()
            .replace(/[\s_]+/g, '-')
            .replace(/[^\u0621-\u064Aa-z0-9-]/g, '')
            .replace(/-+/g, '-');
    }
}

// Mark as manually edited
document.getElementById('slug-input').addEventListener('input', function () {
    this.dataset.manual = '1';
});

function previewImg(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('img-preview').src = e.target.result;
            document.getElementById('img-preview').classList.remove('hidden');
            document.getElementById('img-placeholder').classList.add('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush