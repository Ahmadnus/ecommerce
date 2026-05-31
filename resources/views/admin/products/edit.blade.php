@extends('layouts.admin')
@section('title', 'تعديل: ' . $product->name)

@push('head')
<style>
@import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600&family=Cairo:wght@400;600;700;800&display=swap');
:root {
    --cc-bg:#ffffff;
    --cc-surface:#ffffff;
    --cc-border:#e5e7eb;
    --cc-border-strong:#d1d5db;
    --cc-text:#000000;
    --cc-muted:#6b7280;
    --cc-amber:#d97706;
    --cc-emerald:#059669;
    --cc-rose:#dc2626;
    --cc-brand:var(--brand-color, #000000);
    --cc-mono:'JetBrains Mono',monospace;
    --cc-sans:'Cairo',sans-serif;
}
body { font-family:var(--cc-sans); background:#ffffff; color:#000000; }
.cc-page { background:#ffffff; min-height:100vh; }
.cc-card {
    background:#ffffff;
    border:1px solid var(--cc-border);
    border-radius:12px;
    box-shadow:0 1px 4px rgba(0,0,0,0.06);
}
.cc-label {
    display:block;
    font-size:11px;
    font-weight:800;
    text-transform:uppercase;
    letter-spacing:.06em;
    margin-bottom:6px;
    color:#374151;
}
.cc-input {
    background:#ffffff;
    border:1px solid #d1d5db;
    border-radius:8px;
    color:#000000;
    padding:10px 14px;
    font-size:13px;
    outline:none;
    transition:border-color .15s, box-shadow .15s;
    width:100%;
    font-family:var(--cc-sans);
}

.cc-input:focus {
    border-color:#000000;
    box-shadow:0 0 0 3px rgba(0,0,0,0.06);
}
.cc-input::placeholder { color:#9ca3af; }
.cc-input.mono { font-family:var(--cc-mono); }
.cc-input.has-error { border-color:var(--cc-rose); }
.cc-btn {
    display:inline-flex;
    align-items:center;
    gap:6px;
    padding:10px 20px;
    border-radius:8px;
    font-size:13px;
    font-weight:700;
    cursor:pointer;
    border:none;
    transition:all .15s;
    font-family:var(--cc-sans);
}
.cc-btn-primary {
    background:#000000;
    color:#ffffff;
}
.cc-btn-primary:hover { background:#1f2937; }
.cc-btn-ghost {
    background:#ffffff;
    border:1px solid #d1d5db;
    color:#000000;
}
.cc-btn-ghost:hover { background:#f9fafb; }
.cc-btn-danger {
    background:#fff5f5;
    border:1px solid #fecaca;
    color:#dc2626;
}
.cc-btn-danger:hover { background:#fee2e2; }
.cc-btn-sm { padding:6px 12px; font-size:11.5px; border-radius:6px; }
.section-num {
    width:26px;
    height:26px;
    border-radius:6px;
    background:#000000;
    color:#ffffff;
    font-size:11px;
    font-weight:800;
    display:flex;
    align-items:center;
    justify-content:center;
    flex-shrink:0;
}
.variant-card {
    background:#fafafa;
    border:1px solid #e5e7eb;
    border-radius:10px;
    padding:16px;
    transition:border-color .15s;
}
.variant-card:hover { border-color:#9ca3af; }
</style>
@endpush

@section('admin-content')
<div class="cc-page p-4 sm:p-6 lg:p-8 max-w-5xl mx-auto" dir="rtl" style="background:#ffffff;">

    {{-- Back --}}
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('admin.products.show', $product) }}"
           class="flex items-center gap-2 text-sm transition-colors"
           style="color:#6b7280;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            {{ $product->name }}
        </a>

        {{-- Delete --}}
        <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
              onsubmit="return confirm('حذف هذا المنتج نهائياً؟')">
            @csrf @method('DELETE')
            <button type="submit" class="cc-btn cc-btn-danger cc-btn-sm">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                حذف
            </button>
        </form>
    </div>

    <h1 class="text-xl font-black mb-8 flex items-center gap-3" style="color:#000000;">
        <span class="w-8 h-8 rounded-lg text-sm font-black flex items-center justify-center"
              style="background:#fef3c7; color:#d97706;">✎</span>
        تعديل: {{ Str::limit($product->name, 40) }}
    </h1>

    @if($errors->any())
    <div class="mb-6 cc-card p-4" style="border-color:#fecaca; background:#fff5f5;">
        <p class="text-sm font-bold mb-2" style="color:#dc2626;">يوجد أخطاء:</p>
        <ul class="space-y-1">
            @foreach($errors->all() as $e)
            <li class="text-xs flex items-center gap-2" style="color:#b91c1c;">
                <span class="w-1 h-1 rounded-full flex-shrink-0" style="background:#dc2626;"></span>{{ $e }}
            </li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.products.update', $product) }}" method="POST"
          enctype="multipart/form-data" id="product-form" class="space-y-5">
        @csrf
        @method('PUT')

        {{-- ══ SECTION 1: Basic Info ══════════════════════════════════ --}}
        <div class="cc-card p-6">
    <h2 class="text-sm font-bold mb-5 flex items-center gap-2" style="color:#000000;">
        <span class="section-num">١</span>
        معلومات المنتج
    </h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

        <div class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="cc-label">اسم المنتج (AR) <span style="color:#dc2626;">*</span></label>
                <input type="text" name="name[ar]"
                       value="{{ old('name.ar', $product->getTranslation('name', 'ar')) }}" required
                       class="cc-input @error('name.ar') has-error @enderror">
                @error('name.ar')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="cc-label">اسم المنتج (EN) <span style="color:#dc2626;">*</span></label>
                <input type="text" name="name[en]"
                       value="{{ old('name.en', $product->getTranslation('name', 'en')) }}" required
                       class="cc-input @error('name.en') has-error @enderror">
                @error('name.en')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label class="cc-label">السعر الأساسي <span style="color:#dc2626;">*</span></label>
            <input type="number" step="0.01" min="0" name="base_price"
                   value="{{ old('base_price', $product->base_price) }}" required
                   class="cc-input mono @error('base_price') has-error @enderror">
        </div>

        <div>
            <label class="cc-label">سعر الخصم</label>
            <input type="number" step="0.01" min="0" name="discount_price"
                   value="{{ old('discount_price', $product->discount_price) }}"
                   class="cc-input mono @error('discount_price') has-error @enderror">
        </div>

        <div>
            <label class="cc-label">SKU المنتج</label>
            <input type="text" name="sku"
                   value="{{ old('sku', $product->sku) }}"
                   class="cc-input mono @error('sku') has-error @enderror">
        </div>

        <div class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="cc-label">وصف قصير (AR)</label>
                <input type="text" name="short_description[ar]"
                       value="{{ old('short_description.ar', $product->getTranslation('short_description', 'ar')) }}" maxlength="500"
                       class="cc-input @error('short_description.ar') has-error @enderror">
                @error('short_description.ar')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="cc-label">وصف قصير (EN)</label>
                <input type="text" name="short_description[en]"
                       value="{{ old('short_description.en', $product->getTranslation('short_description', 'en')) }}" maxlength="500"
                       class="cc-input @error('short_description.en') has-error @enderror">
                @error('short_description.en')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="cc-label">الوصف التفصيلي (AR)</label>
                <textarea name="description[ar]" rows="3"
                          class="cc-input @error('description.ar') has-error @enderror">{{ old('description.ar', $product->getTranslation('description', 'ar')) }}</textarea>
                @error('description.ar')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="cc-label">الوصف التفصيلي (EN)</label>
                <textarea name="description[en]" rows="3"
                          class="cc-input @error('description.en') has-error @enderror">{{ old('description.en', $product->getTranslation('description', 'en')) }}</textarea>
                @error('description.en')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="sm:col-span-2 flex flex-wrap gap-3">
            <label class="flex items-center gap-3 px-4 py-3 rounded-xl cursor-pointer"
                   style="background:#f9fafb; border:1px solid #e5e7eb;">
                <input type="checkbox" name="is_active" value="1"
                       {{ old('is_active', $product->status === 'active') ? 'checked' : '' }}
                       class="w-4 h-4 accent-emerald-600">
                <span class="text-sm font-semibold" style="color:#000000;">تفعيل المنتج</span>
            </label>

            <label class="flex items-center gap-3 px-4 py-3 rounded-xl cursor-pointer"
                   style="background:#f9fafb; border:1px solid #e5e7eb;">
                <input type="checkbox" name="is_featured" value="1"
                       {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}
                       class="w-4 h-4 accent-amber-500">
                <span class="text-sm font-semibold" style="color:#000000;">منتج مميز ⭐</span>
            </label>
        </div>

    </div>
</div>

        {{-- ══ SECTION 2: Categories ══════════════════════════════════ --}}
        <div class="cc-card p-6">
            <h2 class="text-sm font-bold mb-5 flex items-center gap-2" style="color:#000000;">
                <span class="section-num">٢</span>
                التصنيفات
            </h2>
            <div class="rounded-xl overflow-hidden border" style="border-color:#e5e7eb;">
                <div class="grid grid-cols-[1fr_auto] px-4 py-2 text-[10px] font-bold uppercase tracking-widest border-b"
                     style="color:#6b7280; border-color:#e5e7eb; background:#f9fafb;">
                    <span>التصنيف</span><span>أساسي</span>
                </div>
                @foreach($categories as $root)
                <div class="grid grid-cols-[1fr_auto] items-center border-b"
                     style="border-color:#f3f4f6;">
                    <label class="flex items-center gap-3 px-4 py-3 cursor-pointer transition" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='transparent'">
                        <input type="checkbox" name="category_ids[]" value="{{ $root->id }}"
                               {{ in_array($root->id, $selectedCatIds) ? 'checked' : '' }}
                               class="w-4 h-4 accent-gray-800 rounded">
                        <span class="font-bold text-sm" style="color:#000000;">{{ $root->name }}</span>
                        <span class="text-[10px] mono ms-auto" style="color:#9ca3af; font-family:'JetBrains Mono',monospace;">{{ $root->slug }}</span>
                    </label>
                    <div class="px-4">
                        <input type="radio" name="primary_category_id" value="{{ $root->id }}"
                               {{ $primaryCatId == $root->id ? 'checked' : '' }}
                               class="w-4 h-4 accent-gray-800">
                    </div>
                </div>
                @foreach($root->allActiveChildren as $sub)
                <div class="grid grid-cols-[1fr_auto] items-center border-b ps-5"
                     style="border-color:#f3f4f6; background:#fafafa;">
                    <label class="flex items-center gap-3 px-4 py-2.5 cursor-pointer transition" onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='transparent'">
                        <input type="checkbox" name="category_ids[]" value="{{ $sub->id }}"
                               {{ in_array($sub->id, $selectedCatIds) ? 'checked' : '' }}
                               class="w-4 h-4 accent-gray-800 rounded">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#d1d5db;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                        <span class="text-sm" style="color:#111827;">{{ $sub->name }}</span>
                    </label>
                    <div class="px-4">
                        <input type="radio" name="primary_category_id" value="{{ $sub->id }}"
                               {{ $primaryCatId == $sub->id ? 'checked' : '' }}
                               class="w-4 h-4 accent-gray-800">
                    </div>
                </div>
                @endforeach
                @endforeach
            </div>
        </div>

        {{-- ══ SECTION 3: Images (Main & Gallery) ════════════════════════════════ --}}
        <div class="cc-card p-6">
            <h2 class="text-sm font-bold mb-6 flex items-center gap-2" style="color:#000000;">
                <span class="section-num">٣</span>
                إدارة الصور
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- A. الصورة الأساسية (Collection: main) --}}
                <div class="space-y-4">
                    <label class="cc-label">الصورة الأساسية (Main Image)</label>
                    <div class="relative group w-full aspect-video rounded-xl overflow-hidden flex items-center justify-center" style="background:#f3f4f6; border:2px dashed #d1d5db;">
                        @php $mainImg = $product->getFirstMediaUrl('main') ?: asset('images/placeholder.jpg'); @endphp
                        <img id="main-preview" src="{{ $mainImg }}" class="w-full h-full object-cover">
                        <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition flex items-center justify-center" style="background:rgba(0,0,0,0.45);">
                            <label class="cc-btn cc-btn-primary cc-btn-sm cursor-pointer">
                                تغيير الصورة
                                <input type="file" name="main_image" accept="image/*" class="hidden" onchange="previewMain(this)">
                            </label>
                        </div>
                    </div>
                    <p class="text-[10px] text-center" style="color:#9ca3af;">سيتم استبدال الصورة القديمة في حال رفع صورة جديدة</p>
                </div>

                {{-- B. معرض الصور (Collection: products) --}}
                <div class="space-y-4">
                    <label class="cc-label">صور المعرض (Gallery)</label>
                    
                    {{-- الصور الموجودة حالياً --}}
                    <div class="grid grid-cols-4 gap-2" id="existing-gallery">
                        @foreach($product->getMedia('products') as $media)
                            <div class="relative aspect-square rounded-lg overflow-hidden group" id="media-{{ $media->id }}" style="border:1px solid #e5e7eb;">
                                <img src="{{ $media->getUrl() }}" class="w-full h-full object-cover">
                                <button type="button" onclick="markForDeletion({{ $media->id }})" 
                                        class="absolute top-1 right-1 w-6 h-6 text-white rounded-md flex items-center justify-center opacity-0 group-hover:opacity-100 transition shadow-lg" style="background:#dc2626;">
                                    ✕
                                </button>
                                <input type="hidden" 
                                       name="delete_media_ids[]" 
                                       value="{{ $media->id }}" 
                                       id="delete-input-{{ $media->id }}" 
                                       disabled>
                            </div>
                        @endforeach
                    </div>

                    {{-- رفع صور جديدة --}}
                    <div class="pt-4" style="border-top:1px solid #f3f4f6;">
                        <label class="cc-btn cc-btn-ghost w-full justify-center" style="border-style:dashed; cursor:pointer;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M12 4v16m8-8H4" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            إضافة صور للمعرض
                            <input type="file" name="product_images[]" multiple accept="image/*" class="hidden" onchange="previewGallery(this)">
                        </label>
                        <div id="new-gallery-preview" class="grid grid-cols-4 gap-2 mt-3"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ SECTION 4: Variants ════════════════════════════════════ --}}
        <div class="cc-card p-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-sm font-bold flex items-center gap-2" style="color:#000000;">
                    <span class="section-num">٤</span>
                    المتغيرات
                    <span class="text-[10px] font-normal" style="color:#d97706;">
                        (سيتم حذف المتغيرات الحالية وإعادة إنشائها)
                    </span>
                </h2>
                <button type="button" onclick="addVariantRow()" class="cc-btn cc-btn-primary cc-btn-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    إضافة متغير
                </button>
            </div>

           <script>
    window.ATTRIBUTES = {!! json_encode($attributes->map(function($a) {
        return [
            'id'     => $a->id,
            'name'   => $a->name,
            'type'   => $a->type,
            'values' => $a->values->map(function($v) {
                return [
                    'id'        => $v->id,
                    'value'     => $v->value ?? $v->label,
                    'label'     => $v->label ?? $v->value,
                    'color_hex' => $v->color_hex,
                ];
            })->toArray(),
        ];
    })->toArray()) !!};
    
    window.EXISTING_VARIANTS = {!! json_encode($existingVariants) !!};
    window.variantIndex = 0;
</script>

            <div id="variants-container" class="space-y-3"></div>

            <div id="variants-empty" class="hidden text-center py-10 rounded-xl border border-dashed"
                 style="border-color:#e5e7eb; color:#9ca3af;">
                لا توجد متغيرات — اضغط "إضافة متغير"
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex justify-end gap-3 pb-8">
            <a href="{{ route('admin.products.show', $product) }}" class="cc-btn cc-btn-ghost">إلغاء</a>
            <button type="submit" class="cc-btn cc-btn-primary px-10">
                حفظ التعديلات
            </button>
        </div>

    </form>
</div>
@endsection


<script>
function previewImg(input) {
    if (!input.files?.[0]) return;
    const r = new FileReader();
    r.onload = e => {
        const prev = document.getElementById('img-preview');
        prev.src = e.target.result;
        prev.classList.remove('hidden');
        const ph = document.getElementById('img-ph');
        if (ph) ph.classList.add('hidden');
    };
    r.readAsDataURL(input.files[0]);
}

function buildVariantRowHTML(i, prefill = {}) {
    const attrs = window.ATTRIBUTES;
    let attrHTML = '';
    attrs.forEach(attr => {
        const selected = (prefill.attribute_values || []).map(String);
        let valsHTML = '';
        if (attr.type === 'color') {
            attr.values.forEach(v => {
                const chk = selected.includes(String(v.id)) ? 'checked' : '';
                valsHTML += `<label class="cursor-pointer" title="${v.label||v.value}">
                    <input type="checkbox" name="variants[${i}][attribute_values][]" value="${v.id}" ${chk} class="sr-only peer">
                    <span class="inline-flex w-8 h-8 rounded-full border-2 border-transparent peer-checked:border-gray-900 peer-checked:scale-110 hover:scale-105 transition-all" style="background:${v.color_hex||'#888'}"></span>
                </label>`;
            });
        } else {
            attr.values.forEach(v => {
                const chk = selected.includes(String(v.id)) ? 'checked' : '';
                valsHTML += `<label class="cursor-pointer">
                    <input type="checkbox" name="variants[${i}][attribute_values][]" value="${v.id}" ${chk} class="sr-only peer">
                    <span class="inline-block px-3 py-1.5 text-xs font-semibold rounded-lg transition-all select-none" style="border:1px solid #e5e7eb; background:#f9fafb; color:#374151;" onmouseover="this.style.borderColor='#000'" onmouseout="this.style.borderColor='#e5e7eb'">${v.label||v.value}</span>
                </label>`;
            });
        }
        attrHTML += `<div><p class="text-[10px] font-bold uppercase tracking-widest mb-2" style="color:#6b7280;">${attr.name}</p><div class="flex flex-wrap gap-2">${valsHTML}</div></div>`;
    });
    return `<div class="variant-card" data-idx="${i}">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold" style="color:#6b7280;">متغير #${i+1}</span>
            <button type="button" onclick="removeVariantRow(this)" class="cc-btn cc-btn-danger cc-btn-sm">✕</button>
        </div>
        ${attrs.length ? `<div class="grid grid-cols-1 sm:grid-cols-${Math.min(attrs.length,3)} gap-4 mb-4">${attrHTML}</div>` : ''}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 border-t pt-4" style="border-color:#f3f4f6;">
            <div>
                <label class="cc-label">الكمية *</label>
                <input type="number" name="variants[${i}][stock_quantity]" value="${prefill.stock_quantity??0}" min="0" required class="cc-input mono">
            </div>
            <div>
                <label class="cc-label">تجاوز السعر</label>
                <input type="number" step="0.01" min="0" name="variants[${i}][price_override]" value="${prefill.price_override??''}" placeholder="يرث سعر المنتج" class="cc-input mono">
            </div>
            <div>
                <label class="cc-label">SKU</label>
                <input type="text" name="variants[${i}][sku]" value="${prefill.sku??''}" placeholder="يُولَّد تلقائياً" class="cc-input mono">
            </div>
        </div>
    </div>`;
}

function addVariantRow(prefill = {}) {
    const i         = window.variantIndex++;
    const container = document.getElementById('variants-container');
    document.getElementById('variants-empty').classList.add('hidden');
    const el = document.createElement('div');
    el.innerHTML = buildVariantRowHTML(i, prefill);
    const row = el.firstElementChild;
    row.style.opacity = '0'; row.style.transform = 'translateY(8px)';
    container.appendChild(row);
    requestAnimationFrame(() => {
        row.style.transition = 'opacity .2s, transform .2s';
        row.style.opacity = '1'; row.style.transform = 'translateY(0)';
    });
}

function removeVariantRow(btn) {
    const row = btn.closest('.variant-card');
    row.style.transition = 'opacity .15s, transform .15s';
    row.style.opacity = '0'; row.style.transform = 'translateY(-6px)';
    setTimeout(() => {
        row.remove();
        if (!document.querySelectorAll('.variant-card').length) {
            document.getElementById('variants-empty').classList.remove('hidden');
        }
    }, 160);
}

// Seed existing variants on page load
document.addEventListener('DOMContentLoaded', () => {
    const existing = window.EXISTING_VARIANTS || [];
    if (existing.length) {
        existing.forEach(v => addVariantRow(v));
    } else {
        document.getElementById('variants-empty').classList.remove('hidden');
    }
});

// 1. معاينة الصورة الأساسية
function previewMain(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('main-preview').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// 2. معاينة صور المعرض الجديدة قبل الرفع
function previewGallery(input) {
    const container = document.getElementById('new-gallery-preview');
    container.innerHTML = '';
    if (input.files) {
        Array.from(input.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = "relative aspect-square rounded-lg overflow-hidden";
                div.style.border = "1px solid #6ee7b7";
                div.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-full object-cover">
                    <div class="absolute top-0 left-0 text-[8px] px-1 font-bold" style="background:#059669; color:#ffffff;">NEW</div>
                `;
                container.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    }
}

// 3. وضع علامة حذف على الصور القديمة
function markForDeletion(mediaId) {
    const wrapper = document.getElementById('media-' + mediaId);
    const input = document.getElementById('delete-input-' + mediaId);
    
    if (input.disabled) {
        input.disabled = false;
        wrapper.style.opacity = "0.3";
        wrapper.style.filter = "grayscale(1)";
        wrapper.style.border = "2px solid #dc2626";
    } else {
        input.disabled = true;
        wrapper.style.opacity = "1";
        wrapper.style.filter = "none";
        wrapper.style.border = "1px solid #e5e7eb";
    }
}
</script>