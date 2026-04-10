@extends('layouts.admin')
@section('title', 'إضافة منتج جديد')

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

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data"
          id="product-form" class="space-y-6">
        @csrf

        {{-- ════════════════════════════════════════
             SECTION 1 — Basic Info
        ════════════════════════════════════════ --}}
        <div class="bg-white p-8 rounded-2xl border border-gray-200 shadow-sm">
            <h2 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                <span class="w-7 h-7 bg-brand/10 text-brand rounded-lg flex items-center justify-center text-sm font-black">١</span>
                معلومات المنتج الأساسية
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Name --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">اسم المنتج <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand/30 focus:border-brand p-3 bg-gray-50 transition">
                </div>

                {{-- Base price --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">السعر الأساسي ($) <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" min="0" name="base_price" value="{{ old('base_price') }}" required
                           class="w-full border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand/30 focus:border-brand p-3 bg-gray-50 transition">
                </div>

                {{-- Discount price --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        سعر الخصم ($)
                        <span class="text-gray-400 font-normal text-xs">اختياري — يجب أن يكون أقل من الأساسي</span>
                    </label>
                    <input type="number" step="0.01" min="0" name="discount_price" value="{{ old('discount_price') }}"
                           placeholder="اتركه فارغاً إذا لا يوجد خصم"
                           class="w-full border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand/30 focus:border-brand p-3 bg-gray-50 transition">
                </div>

                {{-- SKU --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        SKU المنتج
                        <span class="text-gray-400 font-normal text-xs">اختياري</span>
                    </label>
                    <input type="text" name="sku" value="{{ old('sku') }}"
                           class="w-full border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand/30 focus:border-brand p-3 bg-gray-50 font-mono text-sm transition"
                           placeholder="PROD-001">
                </div>

                {{-- Short description --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">وصف قصير</label>
                    <input type="text" name="short_description" value="{{ old('short_description') }}" maxlength="500"
                           class="w-full border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand/30 focus:border-brand p-3 bg-gray-50 transition"
                           placeholder="جملة وصفية تظهر في البطاقة...">
                </div>

                {{-- Description --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">الوصف التفصيلي</label>
                    <textarea name="description" rows="4"
                              class="w-full border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand/30 focus:border-brand p-3 bg-gray-50 text-sm transition resize-none"
                              placeholder="وصف كامل للمنتج...">{{ old('description') }}</textarea>
                </div>

                {{-- Checkboxes --}}
                <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-xl bg-gray-50 hover:bg-white transition cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                               class="w-5 h-5 text-brand border-gray-300 rounded focus:ring-brand">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">تفعيل المنتج</p>
                            <p class="text-xs text-gray-400">يظهر في المتجر فور الحفظ</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-xl bg-gray-50 hover:bg-white transition cursor-pointer">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}
                               class="w-5 h-5 text-yellow-500 border-gray-300 rounded focus:ring-yellow-400">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">منتج مميز ⭐</p>
                            <p class="text-xs text-gray-400">يظهر في قسم التوصيات</p>
                        </div>
                    </label>
                </div>

            </div>
        </div>

        {{-- ════════════════════════════════════════
             SECTION 2 — Categories (Nested)
        ════════════════════════════════════════ --}}
        <div class="bg-white p-8 rounded-2xl border border-gray-200 shadow-sm">
            <h2 class="text-lg font-bold text-gray-800 mb-2 flex items-center gap-2">
                <span class="w-7 h-7 bg-brand/10 text-brand rounded-lg flex items-center justify-center text-sm font-black">٢</span>
                التصنيفات
            </h2>
            <p class="text-sm text-gray-400 mb-6">اختر تصنيفاً رئيسياً واحداً على الأقل، ثم حدد أيها هو التصنيف الأساسي</p>

            {{-- Nested category checkboxes --}}
            <div class="space-y-2" id="category-tree">
                @foreach($categories as $root)
                <div class="border border-gray-200 rounded-xl overflow-hidden">

                    {{-- Root level --}}
                    <div class="flex items-center justify-between px-4 py-3 bg-gray-50/80 border-b border-gray-100">
                        <label class="flex items-center gap-3 cursor-pointer flex-1">
                            <input type="checkbox"
                                   name="category_ids[]"
                                   value="{{ $root->id }}"
                                   {{ is_array(old('category_ids')) && in_array($root->id, old('category_ids')) ? 'checked' : '' }}
                                   onchange="syncPrimaryRadio(this)"
                                   class="cat-checkbox w-4 h-4 text-brand border-gray-300 rounded focus:ring-brand">
                            <span class="text-sm font-bold text-gray-800">{{ $root->name }}</span>
                            <span class="text-[10px] text-gray-400 font-mono">{{ $root->slug }}</span>
                        </label>
                        {{-- Expand/collapse if has children --}}
                        @if($root->allActiveChildren->isNotEmpty())
                        <button type="button"
                                onclick="toggleBranch(this)"
                                class="text-gray-400 hover:text-brand transition p-1 rounded">
                            <svg class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        @endif
                    </div>

                    {{-- Children --}}
                    @if($root->allActiveChildren->isNotEmpty())
                    <div class="cat-branch">
                        @foreach($root->allActiveChildren as $sub)
                        <div class="border-b border-gray-100 last:border-0">
                            <label class="flex items-center gap-3 px-4 py-2.5 ps-8 cursor-pointer hover:bg-gray-50/60 transition">
                                <input type="checkbox"
                                       name="category_ids[]"
                                       value="{{ $sub->id }}"
                                       {{ is_array(old('category_ids')) && in_array($sub->id, old('category_ids')) ? 'checked' : '' }}
                                       onchange="syncPrimaryRadio(this)"
                                       class="cat-checkbox w-4 h-4 text-brand border-gray-300 rounded focus:ring-brand">
                                <svg class="w-3 h-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                                <span class="text-sm text-gray-700">{{ $sub->name }}</span>
                                <span class="text-[10px] text-gray-400 font-mono mr-auto">{{ $sub->slug }}</span>
                            </label>

                            {{-- Sub-sub --}}
                            @foreach($sub->allActiveChildren as $subsub)
                            <label class="flex items-center gap-3 px-4 py-2 ps-14 cursor-pointer hover:bg-gray-50/60 transition border-t border-gray-100">
                                <input type="checkbox"
                                       name="category_ids[]"
                                       value="{{ $subsub->id }}"
                                       {{ is_array(old('category_ids')) && in_array($subsub->id, old('category_ids')) ? 'checked' : '' }}
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

            {{-- Primary category selector (shown dynamically) --}}
           <div id="primary-cat-section" class="mt-4 hidden border-t pt-4">
    <p class="text-sm font-bold text-gray-700 mb-2">اختر القسم الأساسي:</p>
    <div id="primary-cat-radios" class="flex flex-wrap gap-2">
        </div>
</div>

        </div>

        {{-- ════════════════════════════════════════
             SECTION 3 — Product Image
        ════════════════════════════════════════ --}}
        <div class="bg-white p-8 rounded-2xl border border-gray-200 shadow-sm">
            <h2 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                <span class="w-7 h-7 bg-brand/10 text-brand rounded-lg flex items-center justify-center text-sm font-black">٣</span>
                صورة المنتج الرئيسية
            </h2>
            <div class="flex items-start gap-6">
                <div class="w-36 h-36 border-2 border-dashed border-gray-200 rounded-2xl flex items-center justify-center overflow-hidden bg-gray-50 flex-shrink-0">
                    <template x-if="imagePreview">
                        <img :src="imagePreview" class="w-full h-full object-cover">
                    </template>
                    <template x-if="!imagePreview">
                        <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </template>
                </div>
                <div class="flex-1">
                    <input type="file" name="main_image" required accept="image/*"
                           @change="const f=$event.target.files[0]; if(f){const r=new FileReader();r.onload=e=>imagePreview=e.target.result;r.readAsDataURL(f);}"
                           class="block w-full text-sm text-gray-500 file:ml-4 file:py-2.5 file:px-6 file:rounded-xl file:border-0 file:bg-brand/10 file:text-brand file:font-bold hover:file:bg-brand/20 transition cursor-pointer">
                    <p class="mt-2 text-xs text-gray-400">يفضل صورة مربعة عالية الجودة — PNG أو JPG، حد أقصى 4MB</p>
                </div>
            </div>
        </div>

        {{-- ════════════════════════════════════════
             SECTION 4 — Product Variants
        ════════════════════════════════════════ --}}
        <div class="bg-white p-8 rounded-2xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <span class="w-7 h-7 bg-brand/10 text-brand rounded-lg flex items-center justify-center text-sm font-black">٤</span>
                    المتغيرات (Variants)
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
                كل متغير يمثل تركيبة من الخصائص — مثلاً: <span class="font-mono bg-gray-100 px-1.5 py-0.5 rounded text-xs">أزرق / مقاس 42</span>
            </p>

            {{-- Attributes JSON for JS --}}
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
            @endphp
            <script>window.ATTRIBUTES = @json($attributesJson);</script>

            {{-- Variant rows container --}}
            <div id="variants-container" class="space-y-4">
                {{-- Rows added dynamically by JS --}}
                {{-- Show old() rows on validation fail --}}
                @if(old('variants'))
                    @foreach(old('variants') as $i => $oldVariant)
                    <div data-variant-row="{{ $i }}" class="variant-row border border-gray-200 rounded-xl p-5 bg-gray-50/50 relative">
                        {{-- populated by JS rebuild on error --}}
                    </div>
                    @endforeach
                @endif
            </div>

            {{-- Empty state --}}
            <div id="variants-empty" class="text-center py-10 text-gray-400 text-sm border-2 border-dashed border-gray-200 rounded-xl {{ old('variants') ? 'hidden' : '' }}">
                <svg class="w-10 h-10 mx-auto mb-2 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                اضغط "إضافة متغير" لبدء إدخال الألوان والمقاسات
            </div>

        </div>

        {{-- Submit --}}
        <div class="flex justify-end gap-4 pb-8">
            <a href="{{ route('admin.products.index') }}"
               class="px-8 py-3 text-gray-500 font-bold hover:text-red-500 transition text-sm">إلغاء</a>
            <button type="submit"
                    class="bg-brand text-white px-12 py-3 rounded-xl font-bold shadow-lg shadow-brand/20 hover:bg-brand/90 hover:scale-[1.02] transition-transform active:scale-95">
                حفظ المنتج
            </button>
        </div>

    </form>
</div>
@endsection

<script>
    // تعريف الدالة مباشرة على نافذة المتصفح لضمان رؤيتها
    window.addVariantRow = function(prefill = null) {
        console.log("تم الضغط على الزر!"); // للتأكد في الـ Console
        
        const container = document.getElementById('variants-container');
        const emptyState = document.getElementById('variants-empty');
        
        if (emptyState) emptyState.classList.add('hidden');

        // تعريف الـ Index
        if (typeof window.vIdx === 'undefined') {
            window.vIdx = {{ count(old('variants', [])) }};
        }
        
        const i = window.vIdx++;
        const row = document.createElement('div');
        row.className = 'variant-row border border-gray-200 rounded-xl p-5 bg-gray-50/50 relative mb-4';
        
        // بناء الخصائص (Attributes)
        let attrCols = '';
        const attrs = @json($attributes ?? []); // تأكد أن المتغير هنا هو نفسه المرسل من الـ Controller
        
        attrs.forEach(attr => {
            let options = '';
            attr.values.forEach(v => {
                const isChecked = (prefill && prefill.attribute_values && prefill.attribute_values.map(String).includes(String(v.id))) ? 'checked' : '';
                options += `
                    <label class="cursor-pointer">
                        <input type="checkbox" name="variants[${i}][attribute_values][]" value="${v.id}" ${isChecked} class="sr-only peer">
                        <span class="block px-3 py-1 border border-gray-200 rounded-lg text-xs peer-checked:bg-brand peer-checked:text-white transition-all">
                            ${v.label || v.value || ''}
                        </span>
                    </label>`;
            });
            attrCols += `<div class="mb-3"><p class="text-xs font-bold text-gray-400 mb-1">${attr.name}</p><div class="flex flex-wrap gap-2">${options}</div></div>`;
        });

        row.innerHTML = `
            <div class="flex justify-between items-center mb-4">
                <span class="text-xs font-bold text-gray-400">متغير #${i + 1}</span>
                <button type="button" onclick="this.closest('.variant-row').remove()" class="text-red-400 hover:text-red-600 text-xs">حذف</button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">${attrCols}</div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-4">
                <input type="number" name="variants[${i}][stock_quantity]" placeholder="الكمية" class="border rounded-lg p-2 text-sm" required>
                <input type="number" step="0.01" name="variants[${i}][price_override]" placeholder="السعر الإضافي" class="border rounded-lg p-2 text-sm">
                <input type="text" name="variants[${i}][sku]" placeholder="SKU" class="border rounded-lg p-2 text-sm font-mono">
            </div>`;
            
        container.appendChild(row);
    };
    window.syncPrimaryRadio = function() {
    const checkedBoxes = document.querySelectorAll('.cat-checkbox:checked');
    const radioSection = document.getElementById('primary-cat-section');
    const radioContainer = document.getElementById('primary-cat-radios');
    
    if (!radioContainer) return; // تأكد أن العنصر موجود

    radioContainer.innerHTML = '';
    
    if (checkedBoxes.length > 0) {
        radioSection.classList.remove('hidden');
        checkedBoxes.forEach(cb => {
            // جلب اسم القسم من العنصر القريب
            const labelText = cb.closest('label').querySelector('span').innerText;
            const id = cb.value;
            
            radioContainer.insertAdjacentHTML('beforeend', `
                <label class="flex items-center gap-2 px-3 py-1 border border-gray-200 rounded-full cursor-pointer hover:bg-gray-50">
                    <input type="radio" name="primary_category_id" value="${id}" class="w-3 h-3 text-brand" required>
                    <span class="text-xs font-bold text-gray-700">${labelText}</span>
                </label>
            `);
        });
    } else {
        radioSection.classList.add('hidden');
    }
}
</script>