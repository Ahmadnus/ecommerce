@extends('layouts.admin')
@section('title', 'تعديل المنتج')

@section('admin-content')
<div class="max-w-5xl mx-auto" x-data="{ imagePreview: null }">

    <div class="mb-6">
        <a href="{{ route('admin.products.index') }}"
           class="text-gray-500 hover:text-brand flex items-center gap-2 text-sm transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            العودة للمنتجات
        </a>
    </div>

    @if($errors->any())
    <div class="mb-6 p-4 bg-red-50 border-r-4 border-red-500 text-red-700 rounded-xl text-sm">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.products.update', $product->id) }}" method="POST"
          enctype="multipart/form-data" id="product-form" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- ════ SECTION 1 — Basic Info ════ --}}
        <div class="bg-white p-8 rounded-2xl border border-gray-200 shadow-sm">
            <h2 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                <span class="w-7 h-7 bg-brand/10 text-brand rounded-lg flex items-center justify-center text-sm font-black">١</span>
                معلومات المنتج الأساسية
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">اسم المنتج <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                           class="w-full border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand/30 focus:border-brand p-3 bg-gray-50 transition">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">السعر الأساسي ($) <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" min="0" name="base_price"
                           value="{{ old('base_price', $product->base_price) }}" required
                           class="w-full border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand/30 focus:border-brand p-3 bg-gray-50 transition">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">سعر الخصم ($)</label>
                    <input type="number" step="0.01" min="0" name="discount_price"
                           value="{{ old('discount_price', $product->discount_price) }}"
                           placeholder="اتركه فارغاً إذا لا يوجد خصم"
                           class="w-full border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand/30 focus:border-brand p-3 bg-gray-50 transition">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">SKU المنتج</label>
                    <input type="text" name="sku" value="{{ old('sku', $product->sku) }}"
                           class="w-full border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand/30 focus:border-brand p-3 bg-gray-50 font-mono text-sm transition">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">وصف قصير</label>
                    <input type="text" name="short_description"
                           value="{{ old('short_description', $product->short_description) }}" maxlength="500"
                           class="w-full border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand/30 focus:border-brand p-3 bg-gray-50 transition">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">الوصف التفصيلي</label>
                    <textarea name="description" rows="4"
                              class="w-full border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand/30 focus:border-brand p-3 bg-gray-50 text-sm transition resize-none">{{ old('description', $product->description) }}</textarea>
                </div>

                <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-xl bg-gray-50 hover:bg-white transition cursor-pointer">
                        <input type="checkbox" name="is_active" value="1"
                               {{ old('is_active', $product->status === 'active') ? 'checked' : '' }}
                               class="w-5 h-5 text-brand border-gray-300 rounded focus:ring-brand">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">تفعيل المنتج</p>
                            <p class="text-xs text-gray-400">يظهر في المتجر</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-xl bg-gray-50 hover:bg-white transition cursor-pointer">
                        <input type="checkbox" name="is_featured" value="1"
                               {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}
                               class="w-5 h-5 text-yellow-500 border-gray-300 rounded focus:ring-yellow-400">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">منتج مميز ⭐</p>
                            <p class="text-xs text-gray-400">يظهر في التوصيات</p>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        {{-- ════ SECTION 2 — Categories ════ --}}
        <div class="bg-white p-8 rounded-2xl border border-gray-200 shadow-sm">
            <h2 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                <span class="w-7 h-7 bg-brand/10 text-brand rounded-lg flex items-center justify-center text-sm font-black">٢</span>
                التصنيفات
            </h2>
            @php
                $selectedCatIds   = old('category_ids', $product->categories->pluck('id')->toArray());
                $primaryCatId     = old('primary_category_id',
                    $product->categories->first(fn($c) => $c->pivot->is_primary)?->id
                    ?? $product->categories->first()?->id
                );
            @endphp

            <div class="space-y-2" id="category-tree">
                @foreach($categories as $root)
                <div class="border border-gray-200 rounded-xl overflow-hidden">
                    <div class="flex items-center justify-between px-4 py-3 bg-gray-50/80 border-b border-gray-100">
                        <label class="flex items-center gap-3 cursor-pointer flex-1">
                            <input type="checkbox" name="category_ids[]" value="{{ $root->id }}"
                                   {{ in_array($root->id, $selectedCatIds) ? 'checked' : '' }}
                                   onchange="syncPrimaryRadio(this)"
                                   class="cat-checkbox w-4 h-4 text-brand border-gray-300 rounded focus:ring-brand">
                            <span class="text-sm font-bold text-gray-800">{{ $root->name }}</span>
                        </label>
                        @if($root->allActiveChildren->isNotEmpty())
                        <button type="button" onclick="toggleBranch(this)"
                                class="text-gray-400 hover:text-brand transition p-1 rounded">
                            <svg class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        @endif
                    </div>

                    @if($root->allActiveChildren->isNotEmpty())
                    <div class="cat-branch">
                        @foreach($root->allActiveChildren as $sub)
                        <div class="border-b border-gray-100 last:border-0">
                            <label class="flex items-center gap-3 px-4 py-2.5 ps-8 cursor-pointer hover:bg-gray-50/60 transition">
                                <input type="checkbox" name="category_ids[]" value="{{ $sub->id }}"
                                       {{ in_array($sub->id, $selectedCatIds) ? 'checked' : '' }}
                                       onchange="syncPrimaryRadio(this)"
                                       class="cat-checkbox w-4 h-4 text-brand border-gray-300 rounded focus:ring-brand">
                                <svg class="w-3 h-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                                <span class="text-sm text-gray-700">{{ $sub->name }}</span>
                            </label>
                            @foreach($sub->allActiveChildren as $subsub)
                            <label class="flex items-center gap-3 px-4 py-2 ps-14 cursor-pointer hover:bg-gray-50/60 transition border-t border-gray-100">
                                <input type="checkbox" name="category_ids[]" value="{{ $subsub->id }}"
                                       {{ in_array($subsub->id, $selectedCatIds) ? 'checked' : '' }}
                                       onchange="syncPrimaryRadio(this)"
                                       class="cat-checkbox w-4 h-4 text-brand border-gray-300 rounded focus:ring-brand">
                                <svg class="w-3 h-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                                <span class="text-sm text-gray-600">{{ $subsub->name }}</span>
                            </label>
                            @endforeach
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                @endforeach
            </div>

            <div id="primary-cat-section" class="mt-4 {{ count($selectedCatIds) ? '' : 'hidden' }}">
                <label class="block text-sm font-bold text-gray-700 mb-2">التصنيف الأساسي</label>
                <div id="primary-cat-radios" class="flex flex-wrap gap-2"></div>
                <input type="hidden" name="primary_category_id" id="primary-cat-hidden"
                       value="{{ $primaryCatId }}">
            </div>
        </div>

        {{-- ════ SECTION 3 — Image ════ --}}
        <div class="bg-white p-8 rounded-2xl border border-gray-200 shadow-sm">
            <h2 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                <span class="w-7 h-7 bg-brand/10 text-brand rounded-lg flex items-center justify-center text-sm font-black">٣</span>
                صورة المنتج
            </h2>
            <div class="flex items-start gap-6">
                <div class="w-36 h-36 border-2 border-gray-200 rounded-2xl overflow-hidden flex-shrink-0 bg-gray-50">
                    @if($product->getFirstMediaUrl('products'))
                    <img x-show="!imagePreview"
                         src="{{ $product->getFirstMediaUrl('products') }}"
                         class="w-full h-full object-cover">
                    @endif
                    <template x-if="imagePreview">
                        <img :src="imagePreview" class="w-full h-full object-cover">
                    </template>
                </div>
                <div class="flex-1">
                    <input type="file" name="main_image" accept="image/*"
                           @change="const f=$event.target.files[0];if(f){const r=new FileReader();r.onload=e=>imagePreview=e.target.result;r.readAsDataURL(f);}"
                           class="block w-full text-sm text-gray-500 file:ml-4 file:py-2.5 file:px-6 file:rounded-xl file:border-0 file:bg-brand/10 file:text-brand file:font-bold hover:file:bg-brand/20 transition cursor-pointer">
                    <p class="mt-2 text-xs text-gray-400">اترك فارغاً للإبقاء على الصورة الحالية</p>
                </div>
            </div>
        </div>

        {{-- ════ SECTION 4 — Variants ════ --}}
        <div class="bg-white p-8 rounded-2xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <span class="w-7 h-7 bg-brand/10 text-brand rounded-lg flex items-center justify-center text-sm font-black">٤</span>
                    المتغيرات
                </h2>
                <button type="button" onclick="addVariantRow()"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-brand text-white text-sm font-bold rounded-xl hover:bg-brand/90 transition shadow-sm shadow-brand/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    إضافة متغير
                </button>
            </div>
            <p class="text-sm text-gray-400 mb-6">
                سيتم حذف المتغيرات الحالية وإعادة إنشائها عند الحفظ.
                <span class="text-amber-500 font-semibold">{{ $product->variants->count() }} متغير حالي</span>
            </p>

            @php
                $attributesJson = $attributes->map(fn($a) => [
                    'id'     => $a->id,
                    'name'   => $a->name,
                    'type'   => $a->type,
                    'values' => $a->values->map(fn($v) => [
                        'id'        => $v->id,
                        'label'     => $v->label ?? $v->value,
                        'color_hex' => $v->color_hex,
                    ]),
                ]);
                $existingVariants = $product->variants->map(fn($v) => [
                    'sku'              => $v->sku,
                    'stock_quantity'   => $v->stock_quantity,
                    'price_override'   => $v->price_override,
                    'attribute_values' => $v->attributeValues->pluck('id')->toArray(),
                ]);
            @endphp
            <script>
                window.ATTRIBUTES       = @json($attributesJson);
                window.EXISTING_VARIANTS = @json($existingVariants);
            </script>

            <div id="variants-container" class="space-y-4"></div>

            <div id="variants-empty" class="text-center py-10 text-gray-400 text-sm border-2 border-dashed border-gray-200 rounded-xl hidden">
                <svg class="w-10 h-10 mx-auto mb-2 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                لا توجد متغيرات — اضغط "إضافة متغير"
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex justify-end gap-4 pb-8">
            <a href="{{ route('admin.products.index') }}"
               class="px-8 py-3 text-gray-500 font-bold hover:text-red-500 transition text-sm">إلغاء</a>
            <button type="submit"
                    class="bg-brand text-white px-12 py-3 rounded-xl font-bold shadow-lg shadow-brand/20 hover:bg-brand/90 hover:scale-[1.02] transition-transform active:scale-95">
                حفظ التعديلات
            </button>
        </div>

    </form>
</div>
@endsection

@push('scripts')
{{-- Shared variant logic (same as create page) --}}
<script>
let variantIndex = 0;

function addVariantRow(prefill = {}) {
    const i         = variantIndex++;
    const container = document.getElementById('variants-container');
    const empty     = document.getElementById('variants-empty');

    if (empty) empty.classList.add('hidden');

    const row        = document.createElement('div');
    row.className    = 'variant-row border border-gray-200 rounded-xl p-5 bg-gray-50/50 relative';
    row.dataset.index = i;
    row.innerHTML    = buildVariantRowHTML(i, prefill);
    container.appendChild(row);

    row.style.opacity = '0'; row.style.transform = 'translateY(8px)';
    requestAnimationFrame(() => {
        row.style.transition = 'opacity 0.2s ease, transform 0.2s ease';
        row.style.opacity = '1'; row.style.transform = 'translateY(0)';
    });
}

function buildVariantRowHTML(i, prefill) {
    const attrs = window.ATTRIBUTES;
    let attrCols = '';
    attrs.forEach(attr => {
        const selectedVals = (prefill.attribute_values || []).map(String);
        if (attr.type === 'color') {
            let s = '';
            attr.values.forEach(v => {
                const chk = selectedVals.includes(String(v.id)) ? 'checked' : '';
                s += `<label class="cursor-pointer" title="${v.label}">
                    <input type="checkbox" name="variants[${i}][attribute_values][]" value="${v.id}" ${chk} class="sr-only peer">
                    <span class="block w-7 h-7 rounded-full border-2 border-transparent peer-checked:border-brand peer-checked:scale-110 hover:scale-105 transition-all" style="background:${v.color_hex||'#ccc'}"></span>
                </label>`;
            });
            attrCols += `<div><p class="text-xs font-bold text-gray-500 mb-2">${attr.name}</p><div class="flex flex-wrap gap-2">${s}</div></div>`;
        } else {
            let p = '';
            attr.values.forEach(v => {
                const chk = selectedVals.includes(String(v.id)) ? 'checked' : '';
                p += `<label class="cursor-pointer">
                    <input type="checkbox" name="variants[${i}][attribute_values][]" value="${v.id}" ${chk} class="sr-only peer">
                    <span class="block px-3 py-1.5 border border-gray-200 rounded-lg text-xs font-semibold text-gray-600 bg-white peer-checked:bg-brand peer-checked:text-white peer-checked:border-brand hover:border-brand/50 transition-all select-none">${v.label}</span>
                </label>`;
            });
            attrCols += `<div><p class="text-xs font-bold text-gray-500 mb-2">${attr.name}</p><div class="flex flex-wrap gap-2">${p}</div></div>`;
        }
    });

    return `
    <div class="flex items-center justify-between mb-4">
        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">متغير #${i+1}</span>
        <button type="button" onclick="removeVariantRow(this)" class="text-gray-300 hover:text-red-500 transition p-1 rounded-lg hover:bg-red-50">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-${Math.min(attrs.length,3)} gap-5 mb-5">${attrCols}</div>
    <div class="grid grid-cols-3 gap-4">
        <div>
            <label class="block text-xs font-bold text-gray-500 mb-1.5">الكمية <span class="text-red-400">*</span></label>
            <input type="number" min="0" name="variants[${i}][stock_quantity]" value="${prefill.stock_quantity??''}" placeholder="0"
                   class="w-full border border-gray-200 rounded-xl p-2.5 text-sm bg-white focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-500 mb-1.5">تجاوز السعر ($)</label>
            <input type="number" step="0.01" min="0" name="variants[${i}][price_override]" value="${prefill.price_override??''}" placeholder="يرث السعر الأساسي"
                   class="w-full border border-gray-200 rounded-xl p-2.5 text-sm bg-white focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-500 mb-1.5">SKU</label>
            <input type="text" name="variants[${i}][sku]" value="${prefill.sku??''}" placeholder="يُولَّد تلقائياً"
                   class="w-full border border-gray-200 rounded-xl p-2.5 text-sm bg-white font-mono focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
        </div>
    </div>`;
}

function removeVariantRow(btn) {
    const row = btn.closest('.variant-row');
    row.style.transition = 'opacity 0.2s, transform 0.2s';
    row.style.opacity = '0'; row.style.transform = 'translateY(-6px)';
    setTimeout(() => {
        row.remove();
        if (!document.querySelectorAll('.variant-row').length)
            document.getElementById('variants-empty').classList.remove('hidden');
    }, 200);
}

function toggleBranch(btn) {
    const branch = btn.closest('.border').querySelector('.cat-branch');
    if (!branch) return;
    const h = branch.classList.toggle('hidden');
    btn.querySelector('svg').style.transform = h ? 'rotate(-90deg)' : '';
}

function syncPrimaryRadio(checkbox) {
    const section = document.getElementById('primary-cat-section');
    const radios  = document.getElementById('primary-cat-radios');
    const hidden  = document.getElementById('primary-cat-hidden');
    const checked = [...document.querySelectorAll('.cat-checkbox:checked')];

    if (!checked.length) { section.classList.add('hidden'); return; }
    section.classList.remove('hidden');
    radios.innerHTML = '';
    checked.forEach(cb => {
        const label = cb.closest('label')?.querySelector('span.text-sm')?.textContent?.trim() || cb.value;
        const btn   = document.createElement('label');
        btn.className = 'cursor-pointer';
        btn.innerHTML = `<input type="radio" name="_primary_cat_radio" value="${cb.value}"
                                onchange="document.getElementById('primary-cat-hidden').value=this.value"
                                class="sr-only peer">
                         <span class="block px-4 py-2 border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 bg-white
                                peer-checked:bg-brand peer-checked:text-white peer-checked:border-brand hover:border-brand/50 transition-all select-none">${label}</span>`;
        radios.appendChild(btn);
    });
    // restore selection
    const toSelect = radios.querySelector(`input[value="${hidden.value}"]`)
                  || radios.querySelector('input[type="radio"]');
    if (toSelect) { toSelect.checked = true; hidden.value = toSelect.value; }
}

// ── Seed existing variants on page load ──────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    const existing = window.EXISTING_VARIANTS || [];
    if (existing.length) {
        existing.forEach(v => addVariantRow(v));
    } else {
        document.getElementById('variants-empty').classList.remove('hidden');
    }

    // Bootstrap primary category radios from existing selection
    const anyCatChecked = document.querySelector('.cat-checkbox:checked');
    if (anyCatChecked) syncPrimaryRadio(anyCatChecked);
});
</script>
@endpush