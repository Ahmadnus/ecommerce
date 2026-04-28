@extends('layouts.admin')
@section('title', 'تعديل: ' . $category->name)

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

    @if($errors->any())
    <div class="mb-6 p-4 bg-red-50 border-r-4 border-red-500 text-red-700 rounded-xl text-sm">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.categories.update', $category->id) }}"
          method="POST"
          enctype="multipart/form-data"
          class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white p-8 rounded-2xl border border-gray-200 shadow-sm space-y-6">

            {{-- Image upload --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-3">
                    صورة التصنيف
                    <span class="text-gray-400 font-normal text-xs">(تُعرض دائرية في المتجر)</span>
                </label>

                <div class="flex items-start gap-6">
                    <div class="flex flex-col items-center gap-1 flex-shrink-0">
                        <div class="w-20 h-20 rounded-full overflow-hidden ring-2 ring-offset-2 ring-[var(--brand-color,#0ea5e9)] bg-gray-100"
                             id="preview-ring">
                            <img id="img-preview"
                                 src="{{ $category->getCategoryImageUrl('thumb') }}"
                                 alt="{{ $category->name }}"
                                 class="w-full h-full object-cover rounded-full">
                        </div>
                        <span class="text-[9px] text-gray-400">المعاينة</span>

                        @if($category->hasImage())
                        <label class="flex items-center gap-1 cursor-pointer mt-1">
                            <input type="checkbox" name="remove_image" value="1"
                                   class="w-3 h-3 text-red-500 border-gray-300 rounded">
                            <span class="text-[10px] text-red-500 font-semibold">حذف الصورة</span>
                        </label>
                        @endif
                    </div>

                    <div class="flex-1">
                        <p class="text-xs text-gray-500 font-semibold mb-2">اختر صورة جديدة (اتركه فارغاً للإبقاء على الحالية)</p>
                        <input type="file" name="image" accept="image/*" onchange="previewImg(this)"
                               class="block w-full text-sm text-gray-500 file:ml-4 file:py-2.5 file:px-6 file:rounded-xl file:border-0 file:bg-brand/10 file:text-brand file:font-bold hover:file:bg-brand/20 transition cursor-pointer">
                    </div>
                </div>
            </div>

            {{-- Name --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">اسم التصنيف <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $category->name) }}" required
                       class="w-full border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand/30 focus:border-brand p-3 bg-gray-50 transition">
            </div>

            {{-- Slug --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">الـ Slug</label>
                <input type="text" name="slug" value="{{ old('slug', $category->slug) }}" required
                       class="w-full border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand/30 focus:border-brand p-3 bg-gray-50 font-mono text-sm transition">
            </div>

            {{-- Parent Category --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">التصنيف الأب</label>
                <div class="border border-gray-200 rounded-xl overflow-hidden bg-gray-50">
                    <label class="flex items-center gap-3 px-4 py-3 cursor-pointer hover:bg-white transition border-b border-gray-100">
                        <input type="radio" name="parent_id" value="" {{ old('parent_id', $category->parent_id) === null ? 'checked' : '' }} class="w-4 h-4 text-brand border-gray-300 focus:ring-brand">
                        <span class="text-sm font-semibold text-gray-800">بدون أب — تصنيف رئيسي</span>
                    </label>

                    @foreach($parentOptions->where('depth', 0) as $root)
                        <div class="border-b border-gray-100 last:border-0">
                            <label class="flex items-center gap-3 px-4 py-3 cursor-pointer hover:bg-white transition">
                                <input type="radio" name="parent_id" value="{{ $root->id }}" {{ old('parent_id', $category->parent_id) == $root->id ? 'checked' : '' }} class="w-4 h-4 text-brand border-gray-300 focus:ring-brand">
                                <span class="text-sm font-medium text-gray-800">{{ $root->name }}</span>
                            </label>

                            @foreach($parentOptions->where('parent_id', $root->id) as $sub)
                                <label class="flex items-center gap-3 px-4 py-2.5 ps-10 cursor-pointer hover:bg-white transition bg-gray-50/70 border-t border-gray-100">
                                    <input type="radio" name="parent_id" value="{{ $sub->id }}" {{ old('parent_id', $category->parent_id) == $sub->id ? 'checked' : '' }} class="w-4 h-4 text-brand border-gray-300 focus:ring-brand">
                                    <span class="text-sm text-gray-700">{{ $sub->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Description & Sort Order --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">الوصف</label>
                <textarea name="description" rows="3" class="w-full border border-gray-200 rounded-xl p-3 bg-gray-50 text-sm transition resize-none">{{ old('description', $category->description) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">ترتيب العرض</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order) }}" class="w-32 border border-gray-200 rounded-xl p-3 bg-gray-50 transition">
            </div>

            <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-xl bg-gray-50 hover:bg-white transition cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }} class="w-5 h-5 text-brand border-gray-300 rounded focus:ring-brand">
                <span class="text-sm font-semibold text-gray-800">تفعيل التصنيف</span>
            </label>

            {{-- Category Banner --}}
            <div class="border-t border-gray-100 pt-6 mt-2">
                <h3 class="text-sm font-bold text-gray-700 mb-1 flex items-center gap-2">
                    <svg class="w-4 h-4 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    بانر صفحة التصنيف
                </h3>
                <p class="text-xs text-gray-400 mb-4">
                    يظهر كصورة كاملة العرض أعلى صفحة التصنيف — لا يوجد نص أو أزرار.
                </p>

                <div class="space-y-3">

                    @php $currentBannerUrl = $category->getBannerImageUrl(); @endphp
                    @if($currentBannerUrl)
                    <div class="relative rounded-xl overflow-hidden border border-gray-200 group">
                        <img src="{{ $currentBannerUrl }}"
                             alt="البانر الحالي"
                             class="w-full h-32 object-cover">
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20
                                    transition-colors flex items-center justify-center">
                            <span class="opacity-0 group-hover:opacity-100 transition-opacity
                                         text-white text-xs font-bold bg-black/50
                                         px-3 py-1 rounded-full">
                                البانر الحالي
                            </span>
                        </div>
                    </div>

                    <label class="flex items-center gap-2 text-xs font-semibold
                                   text-red-500 cursor-pointer w-fit">
                        <input type="checkbox" name="remove_banner_image" value="1"
                               class="w-4 h-4 text-red-500 border-gray-300 rounded
                                      focus:ring-red-400">
                        حذف البانر الحالي
                    </label>
                    @endif

                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">
                            {{ $currentBannerUrl ? 'استبدال البانر' : 'رفع صورة البانر' }}
                            <span class="text-gray-400 font-normal">(1400 × 400 px مثالي)</span>
                        </label>

                        <div id="banner-preview-wrap"
                             class="hidden mb-2 rounded-xl overflow-hidden border border-gray-200 bg-gray-50">
                            <img id="banner-preview" src="" alt="معاينة"
                                 class="w-full h-32 object-cover">
                        </div>

                        <input type="file" name="banner_image" id="banner-image-input"
                               accept="image/jpeg,image/png,image/webp,image/gif"
                               class="block w-full text-sm text-gray-500
                                      file:ml-4 file:py-2 file:px-5 file:rounded-xl
                                      file:border-0 file:bg-brand/10 file:text-brand file:font-bold
                                      hover:file:bg-brand/20 transition cursor-pointer">
                    </div>

                    <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-xl
                                   bg-gray-50 hover:bg-white transition cursor-pointer">
                        <input type="checkbox" name="banner_is_active" value="1"
                               {{ old('banner_is_active', $category->banner_is_active) ? 'checked' : '' }}
                               class="w-5 h-5 text-brand border-gray-300 rounded focus:ring-brand">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">تفعيل البانر</p>
                            <p class="text-xs text-gray-400">سيظهر أعلى صفحة هذا التصنيف عند التفعيل</p>
                        </div>
                    </label>

                </div>
            </div>

        </div>

        <div class="flex justify-between items-center">
            <button type="button"
                    onclick="if(confirm('حذف تصنيف \'{{ addslashes($category->name) }}\'؟ سيتم حذف كل التصنيفات الفرعية.')) document.getElementById('delete-category-form').submit();"
                    class="flex items-center gap-2 text-sm text-red-500 hover:text-red-700 hover:bg-red-50 px-4 py-2.5 rounded-xl transition font-semibold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                حذف التصنيف
            </button>

            <div class="flex gap-3">
                <a href="{{ route('admin.categories.index') }}" class="px-6 py-3 text-gray-500 font-bold hover:text-red-500 transition text-sm">إلغاء</a>
                <button type="submit" class="bg-brand text-white px-10 py-3 rounded-xl font-bold shadow-lg hover:bg-brand/90 hover:scale-[1.02] transition-transform active:scale-95">
                    حفظ التعديلات
                </button>
            </div>
        </div>
    </form>

    <form id="delete-category-form" action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</div>
@endsection

@push('scripts')
<script>
let slugManual = false;

function autoSlug(val) {
    if (slugManual) return;
    document.getElementById('slug-input').value = val
        .toLowerCase().trim()
        .replace(/[\s_]+/g, '-')
        .replace(/[^\u0621-\u064Aa-z0-9-]/g, '')
        .replace(/-+/g, '-');
}
document.getElementById('slug-input')
    .addEventListener('input', () => slugManual = true);

function previewImg(input) {
    if (!input.files?.[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        const prev = document.getElementById('img-preview');
        const ph   = document.getElementById('img-placeholder');
        prev.src = e.target.result;
        prev.classList.remove('hidden');
        ph.classList.add('hidden');
        document.getElementById('preview-ring')
            .classList.replace('border-dashed', 'border-solid');
    };
    reader.readAsDataURL(input.files[0]);
}

document.getElementById('banner-image-input')
    .addEventListener('change', function () {
        if (!this.files?.[0]) return;
        const reader = new FileReader();
        reader.onload = e => {
            const wrap = document.getElementById('banner-preview-wrap');
            const img  = document.getElementById('banner-preview');
            img.src = e.target.result;
            wrap.classList.remove('hidden');
        };
        reader.readAsDataURL(this.files[0]);
    });
</script>
@endpush