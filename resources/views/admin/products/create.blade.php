@extends('layouts.admin')
@section('title', 'إضافة منتج جديد')

@push('head')
<style>
@import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600&family=Cairo:wght@400;600;700;800&display=swap');
:root {
    --cc-bg:#0f1117; --cc-surface:#1a1d27; --cc-border:rgba(255,255,255,.07);
    --cc-text:#e2e8f0; --cc-muted:#64748b; --cc-amber:#f59e0b;
    --cc-emerald:#10b981; --cc-rose:#f43f5e; --cc-brand:var(--brand-color,#6366f1);
    --cc-mono:'JetBrains Mono',monospace; --cc-sans:'Cairo',sans-serif;
}
body { font-family:var(--cc-sans); }
.cc-page { background:var(--cc-bg); min-height:100vh; }
.cc-card { background:var(--cc-surface); border:1px solid var(--cc-border); border-radius:16px; }
.cc-label { display:block; font-size:11px; font-weight:700; text-transform:uppercase;
    letter-spacing:.06em; margin-bottom:6px; color:var(--cc-muted); }
.cc-input {
    background:rgba(255,255,255,.05); border:1px solid var(--cc-border);
    border-radius:10px; color:var(--cc-text); padding:10px 14px; font-size:13px;
    outline:none; transition:border-color .15s, background .15s; width:100%;
    font-family:var(--cc-sans);
}
.cc-input:focus { border-color:var(--cc-brand); background:rgba(255,255,255,.07); }
.cc-input::placeholder { color:var(--cc-muted); }
.cc-input.mono { font-family:var(--cc-mono); }
.cc-input.has-error { border-color:var(--cc-rose); }
.cc-btn { display:inline-flex; align-items:center; gap:6px; padding:10px 20px;
    border-radius:10px; font-size:13px; font-weight:700; cursor:pointer; border:none;
    transition:all .15s; font-family:var(--cc-sans); }
.cc-btn-primary { background:var(--cc-brand); color:#fff; }
.cc-btn-primary:hover { filter:brightness(1.1); transform:translateY(-1px); }
.cc-btn-ghost { background:rgba(255,255,255,.06); border:1px solid var(--cc-border); color:var(--cc-text); }
.cc-btn-ghost:hover { background:rgba(255,255,255,.1); }
.cc-btn-danger { background:rgba(244,63,94,.12); border:1px solid rgba(244,63,94,.3); color:#f43f5e; }
.cc-btn-sm { padding:6px 12px; font-size:11.5px; border-radius:8px; }

.section-num {
    width:26px; height:26px; border-radius:8px;
    background:rgba(255,255,255,.08); color:var(--cc-text);
    font-size:11px; font-weight:800; display:flex; align-items:center; justify-content:center;
    flex-shrink:0;
}

.variant-card {
    background:rgba(255,255,255,.02); border:1px solid rgba(255,255,255,.07);
    border-radius:12px; padding:16px;
    transition:border-color .15s;
}
.variant-card:hover { border-color:rgba(255,255,255,.14); }
.variant-card .drag-handle { cursor:grab; opacity:.4; }
.variant-card .drag-handle:hover { opacity:.8; }

/* Attribute value chip toggle */
.av-check { display:none; }
.av-chip {
    display:inline-flex; align-items:center; gap:5px;
    padding:5px 11px; border-radius:8px; font-size:11.5px; font-weight:600;
    border:1.5px solid rgba(255,255,255,.1);
    color:var(--cc-muted); cursor:pointer;
    transition:all .15s; user-select:none;
}
.av-check:checked + .av-chip {
    border-color:var(--cc-brand); color:var(--cc-text);
    background:rgba(99,102,241,.15);
}
.color-dot { width:11px; height:11px; border-radius:50%; flex-shrink:0; }

/* Preview ring for image --*/
.img-ring {
    width:88px; height:88px; border-radius:14px; overflow:hidden;
    border:2px dashed rgba(255,255,255,.15); background:rgba(255,255,255,.03);
    display:flex; align-items:center; justify-content:center; flex-shrink:0;
    transition:border-color .2s;
}
.img-ring.has-img { border-style:solid; border-color:var(--cc-brand); }

/* Category tree --*/
.cat-radio { display:none; }
.cat-label {
    display:flex; align-items:center; gap:10px; padding:10px 14px;
    cursor:pointer; border-bottom:1px solid rgba(255,255,255,.04);
    transition:background .12s; font-size:13px; color:var(--cc-text);
}
.cat-label:hover { background:rgba(255,255,255,.04); }
.cat-radio:checked + .cat-label {
    background:rgba(99,102,241,.1); color:white;
}
.cat-check { display:none; }
.cat-check-label {
    display:flex; align-items:center; gap:10px; padding:10px 14px;
    cursor:pointer; border-bottom:1px solid rgba(255,255,255,.04);
    transition:background .12s; font-size:13px; color:var(--cc-text);
}
.cat-check-label:hover { background:rgba(255,255,255,.04); }
.cat-check:checked + .cat-check-label { color:white; }
.cat-check:checked + .cat-check-label .cat-radio-indicator {
    background:var(--cc-brand); border-color:var(--cc-brand);
}
.cat-radio-indicator {
    width:15px; height:15px; border-radius:50%;
    border:2px solid rgba(255,255,255,.3); flex-shrink:0;
    transition:all .12s;
}
</style>
@endpush

@section('admin-content')
<div class="cc-page p-4 sm:p-6 lg:p-8 max-w-5xl mx-auto" dir="rtl">

    {{-- Back --}}
    <div class="mb-6">
        <a href="{{ route('admin.products.index') }}"
           class="flex items-center gap-2 text-sm transition-colors"
           style="color:var(--cc-muted)">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            العودة
        </a>
    </div>

    <h1 class="text-xl font-black text-white mb-1 flex items-center gap-3">
        <span class="w-8 h-8 rounded-xl text-sm font-black flex items-center justify-center"
              style="background:var(--cc-brand)">+</span>
        إضافة منتج جديد
    </h1>
    <p class="text-sm mb-8" style="color:var(--cc-muted)">
        أنشئ منتجاً أساسياً مع جميع متغيراته دفعة واحدة
    </p>

    @if($errors->any())
    <div class="mb-6 cc-card p-4 border-rose-500/30">
        <p class="text-rose-400 text-sm font-bold mb-2">يوجد أخطاء في البيانات:</p>
        <ul class="space-y-1">
            @foreach($errors->all() as $e)
            <li class="text-rose-300 text-xs flex items-center gap-2">
                <span class="w-1 h-1 rounded-full bg-rose-400 flex-shrink-0"></span>{{ $e }}
            </li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.products.store') }}" method="POST"
          enctype="multipart/form-data" id="product-form" class="space-y-5">
        @csrf

        {{-- ══ SECTION 1: Basic Info ══════════════════════════════════ --}}
        <div class="cc-card p-6">
            <h2 class="text-sm font-bold text-white mb-5 flex items-center gap-2">
                <span class="section-num">١</span>
                معلومات المنتج الأساسية
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                {{-- Name --}}
                <div class="sm:col-span-2">
                    <label class="cc-label">اسم المنتج <span class="text-rose-400">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="cc-input @error('name') has-error @enderror"
                           placeholder="مثال: حذاء رياضي Air Max">
                </div>

                {{-- Base price --}}
                <div>
                    <label class="cc-label">السعر الأساسي <span class="text-rose-400">*</span></label>
                    <input type="number" step="0.01" min="0" name="base_price"
                           value="{{ old('base_price') }}" required
                           class="cc-input mono @error('base_price') has-error @enderror"
                           placeholder="0.00">
                </div>

                {{-- Discount price --}}
                <div>
                    <label class="cc-label">
                        سعر الخصم
                        <span class="text-[9px] font-normal" style="color:var(--cc-muted)">(اختياري — أقل من الأساسي)</span>
                    </label>
                    <input type="number" step="0.01" min="0" name="discount_price"
                           value="{{ old('discount_price') }}"
                           class="cc-input mono @error('discount_price') has-error @enderror"
                           placeholder="0.00">
                </div>

                {{-- SKU --}}
                <div>
                    <label class="cc-label">SKU المنتج <span class="text-[9px] font-normal">(اختياري)</span></label>
                    <input type="text" name="sku" value="{{ old('sku') }}"
                           class="cc-input mono @error('sku') has-error @enderror"
                           placeholder="PROD-001">
                </div>

                {{-- Short description --}}
                <div>
                    <label class="cc-label">وصف قصير</label>
                    <input type="text" name="short_description"
                           value="{{ old('short_description') }}" maxlength="500"
                           class="cc-input @error('short_description') has-error @enderror"
                           placeholder="جملة وصفية مختصرة...">
                </div>

                {{-- Description --}}
                <div class="sm:col-span-2">
                    <label class="cc-label">الوصف التفصيلي</label>
                    <textarea name="description" rows="3"
                              class="cc-input @error('description') has-error @enderror"
                              placeholder="وصف كامل للمنتج...">{{ old('description') }}</textarea>
                </div>

                {{-- Toggles --}}
                <div class="sm:col-span-2 flex flex-wrap gap-3">
                    <label class="flex items-center gap-3 px-4 py-3 rounded-xl cursor-pointer transition-colors"
                           style="background:rgba(255,255,255,.04); border:1px solid rgba(255,255,255,.07)">
                        <input type="checkbox" name="is_active" value="1"
                               {{ old('is_active', '1') ? 'checked' : '' }}
                               class="w-4 h-4 accent-emerald-500">
                        <div>
                            <p class="text-sm font-semibold text-white leading-tight">تفعيل المنتج</p>
                            <p class="text-[10px]" style="color:var(--cc-muted)">يظهر في المتجر</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 px-4 py-3 rounded-xl cursor-pointer transition-colors"
                           style="background:rgba(255,255,255,.04); border:1px solid rgba(255,255,255,.07)">
                        <input type="checkbox" name="is_featured" value="1"
                               {{ old('is_featured') ? 'checked' : '' }}
                               class="w-4 h-4 accent-amber-400">
                        <div>
                            <p class="text-sm font-semibold text-white leading-tight">منتج مميز ⭐</p>
                            <p class="text-[10px]" style="color:var(--cc-muted)">يظهر في الواجهة</p>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        {{-- ══ SECTION 2: Categories ══════════════════════════════════ --}}
        <div class="cc-card p-6">
            <h2 class="text-sm font-bold text-white mb-2 flex items-center gap-2">
                <span class="section-num">٢</span>
                التصنيفات <span class="text-rose-400">*</span>
            </h2>
            <p class="text-[11px] mb-4" style="color:var(--cc-muted)">
                اختر تصنيفاً أو أكثر ثم حدد التصنيف الأساسي بواسطة زر الاختيار على اليمين
            </p>

            <div class="rounded-xl overflow-hidden border" style="border-color:var(--cc-border)">
                {{-- Header row --}}
                <div class="grid grid-cols-[1fr_auto] px-4 py-2 text-[10px] font-bold uppercase tracking-widest border-b"
                     style="color:var(--cc-muted); border-color:var(--cc-border); background:rgba(255,255,255,.02)">
                    <span>التصنيف</span>
                    <span>أساسي</span>
                </div>

                @foreach($categories as $root)
                {{-- Root --}}
                <div class="grid grid-cols-[1fr_auto] items-center border-b"
                     style="border-color:var(--cc-border)">
                    <label class="cat-check-label ps-4">
                        <input type="checkbox" name="category_ids[]"
                               value="{{ $root->id }}"
                               {{ is_array(old('category_ids')) && in_array($root->id, old('category_ids')) ? 'checked' : '' }}
                               onchange="syncPrimaryRadios()"
                               class="cat-check" id="cat-{{ $root->id }}">
                        <label for="cat-{{ $root->id }}" class="cat-check-label p-0 border-0 bg-transparent flex-1 cursor-pointer">
                            <div class="cat-radio-indicator"></div>
                            <span class="font-bold">{{ $root->name }}</span>
                            <span class="text-[10px] ms-auto mono" style="color:var(--cc-muted)">{{ $root->slug }}</span>
                        </label>
                    </label>
                    <div class="px-4">
                        <input type="radio" name="primary_category_id" value="{{ $root->id }}"
                               {{ old('primary_category_id') == $root->id ? 'checked' : '' }}
                               class="primary-radio w-4 h-4 accent-indigo-400">
                    </div>
                </div>

                {{-- Children --}}
                @foreach($root->allActiveChildren as $sub)
                <div class="grid grid-cols-[1fr_auto] items-center border-b ps-5"
                     style="border-color:var(--cc-border); background:rgba(0,0,0,.15)">
                    <label class="cat-check-label">
                        <input type="checkbox" name="category_ids[]"
                               value="{{ $sub->id }}"
                               {{ is_array(old('category_ids')) && in_array($sub->id, old('category_ids')) ? 'checked' : '' }}
                               onchange="syncPrimaryRadios()"
                               class="cat-check" id="cat-{{ $sub->id }}">
                        <label for="cat-{{ $sub->id }}" class="cat-check-label p-0 border-0 bg-transparent flex-1 cursor-pointer">
                            <svg class="w-3 h-3 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            <div class="cat-radio-indicator"></div>
                            <span>{{ $sub->name }}</span>
                            <span class="text-[10px] ms-auto mono" style="color:var(--cc-muted)">{{ $sub->slug }}</span>
                        </label>
                    </label>
                    <div class="px-4">
                        <input type="radio" name="primary_category_id" value="{{ $sub->id }}"
                               {{ old('primary_category_id') == $sub->id ? 'checked' : '' }}
                               class="primary-radio w-4 h-4 accent-indigo-400">
                    </div>
                </div>
                @endforeach

                @endforeach
            </div>
        </div>

        {{-- ══ SECTION 3: Image ═══════════════════════════════════════ --}}
        <div class="cc-card p-6">
            <h2 class="text-sm font-bold text-white mb-5 flex items-center gap-2">
                <span class="section-num">٣</span>
                الصورة الرئيسية
            </h2>
            <div class="flex items-center gap-5">
                <div class="img-ring" id="img-ring">
                    <img id="img-preview" class="w-full h-full object-cover hidden" alt="">
                    <svg id="img-ph" class="w-8 h-8" style="color:var(--cc-muted)"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586
                                 a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6
                                 a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <input type="file" name="main_image" accept="image/*"
                           onchange="previewImg(this)"
                           class="block w-full text-sm file:ml-4 file:py-2 file:px-5
                                  file:rounded-lg file:border-0 file:text-sm file:font-bold
                                  file:transition-colors cursor-pointer"
                           style="color:var(--cc-muted)"
                           x-file-style>
                    <p class="text-[10px] mt-2" style="color:var(--cc-muted)">PNG / JPG / WebP — حد 4MB</p>
                </div>
            </div>
        </div>

        {{-- ══ SECTION 4: Variants ════════════════════════════════════ --}}
        <div class="cc-card p-6">
            <div class="flex items-center justify-between mb-2">
                <h2 class="text-sm font-bold text-white flex items-center gap-2">
                    <span class="section-num">٤</span>
                    المتغيرات (Variants)
                    <span class="text-[10px] font-normal" style="color:var(--cc-muted)">
                        — كل متغير هو وحدة مستقلة في المخزن
                    </span>
                </h2>
                <button type="button" onclick="addVariantRow()" class="cc-btn cc-btn-primary cc-btn-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    إضافة متغير
                </button>
            </div>

            {{-- Pricing priority reminder --}}
            <div class="flex items-center gap-2 text-[10px] mb-5 flex-wrap"
                 style="color:var(--cc-muted)">
                <span>السعر الفعّال:</span>
                <span class="px-2 py-0.5 rounded" style="background:rgba(245,158,11,.1);color:#f59e0b">تجاوز المتغير</span>
                <span>›</span>
                <span class="px-2 py-0.5 rounded" style="background:rgba(244,63,94,.1);color:#f43f5e">خصم المنتج</span>
                <span>›</span>
                <span>السعر الأساسي</span>
            </div>

           
            <script>
    window.ATTRIBUTES = {!! json_encode($attributes->map(fn($a) => [
        'id'     => $a->id,
        'name'   => $a->name,
        'type'   => $a->type,
        'values' => $a->values->map(fn($v) => [
            'id'        => $v->id,
            'value'     => $v->value ?? $v->label,
            'label'     => $v->label ?? $v->value,
            'color_hex' => $v->color_hex,
        ])->toArray(), // تحويل القيم لمصفوفة
    ])->toArray()) !!}; // تحويل الخصائص لمصفوفة

    window.variantIndex = 0;
</script>

            <div id="variants-container" class="space-y-3">
                {{-- Populated by JS --}}
            </div>

            {{-- Empty state --}}
            <div id="variants-empty"
                 class="text-center py-10 rounded-xl border border-dashed"
                 style="border-color:rgba(255,255,255,.08); color:var(--cc-muted)">
                <svg class="w-10 h-10 mx-auto mb-2 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                اضغط "إضافة متغير" — كل متغير يمثل تركيبة لون/مقاس في المستودع
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex justify-end gap-3 pb-8">
            <a href="{{ route('admin.products.index') }}" class="cc-btn cc-btn-ghost">إلغاء</a>
            <button type="submit" class="cc-btn cc-btn-primary px-10">
                حفظ المنتج
            </button>
        </div>

    </form>
</div>
@endsection


<script>
// 1. تأكد من تهيئة المتغيرات الأساسية أولاً
window.variantIndex = window.variantIndex || 0;
window.ATTRIBUTES = window.ATTRIBUTES || [];

function buildVariantRowHTML(i, prefill = {}) {
    // فلتر أمان لضمان أن attrs هي مصفوفة دائماً
    const attrs = window.ATTRIBUTES || [];
    let attrHTML = '';

    attrs.forEach(attr => {
        // تحويل القيم المختارة مسبقاً لمصفوفة نصوص للمقارنة
        const selected = (prefill.attribute_values || []).map(val => String(val.id || val));
        let valsHTML = '';

        if (attr.type === 'color') {
            attr.values.forEach(v => {
                const chk = selected.includes(String(v.id)) ? 'checked' : '';
                valsHTML += `
                    <label class="cursor-pointer" title="${v.label || v.value}">
                        <input type="checkbox" name="variants[${i}][attribute_values][]"
                               value="${v.id}" ${chk} class="sr-only peer">
                        <span class="inline-flex w-8 h-8 rounded-full border-2 border-transparent
                                     peer-checked:border-indigo-400 peer-checked:scale-110
                                     hover:scale-105 transition-all"
                              style="background:${v.color_hex || '#888'}"></span>
                    </label>`;
            });
        } else {
            attr.values.forEach(v => {
                const chk = selected.includes(String(v.id)) ? 'checked' : '';
                valsHTML += `
                    <label class="cursor-pointer">
                        <input type="checkbox" name="variants[${i}][attribute_values][]"
                               value="${v.id}" ${chk} class="sr-only peer">
                        <span class="inline-block px-3 py-1.5 text-xs font-semibold rounded-lg
                                     border border-white/10 bg-white/5 text-zinc-400
                                     peer-checked:bg-indigo-500/20 peer-checked:border-indigo-400/60
                                     peer-checked:text-white transition-all select-none">
                            ${v.label || v.value}
                        </span>
                    </label>`;
            });
        }

        attrHTML += `
            <div>
                <p class="text-[10px] font-bold uppercase tracking-widest mb-2"
                   style="color:rgba(255,255,255,0.4)">${attr.name}</p>
                <div class="flex flex-wrap gap-2">${valsHTML}</div>
            </div>`;
    });

    return `
    <div class="variant-card bg-zinc-900/50 border border-white/5 p-5 rounded-2xl mb-4 relative" data-idx="${i}">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold text-zinc-500 flex items-center gap-2">
                <span class="drag-handle cursor-move opacity-30 hover:opacity-100">⠿</span>
                متغير #${i + 1}
            </span>
            <button type="button" onclick="removeVariantRow(this)"
                    class="text-zinc-500 hover:text-rose-400 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        ${attrs.length > 0 ? `
        <div class="grid grid-cols-1 gap-6 mb-6">
            ${attrHTML}
        </div>` : ''}

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 border-t border-white/5 pt-5">
            <div>
                <label class="block text-[10px] font-bold text-zinc-500 uppercase mb-2">الكمية</label>
                <input type="number" name="variants[${i}][stock_quantity]"
                       value="${prefill.stock_quantity ?? 0}"
                       min="0" required class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 outline-none transition-all">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-zinc-500 uppercase mb-2">تجاوز السعر</label>
                <input type="number" step="0.01" name="variants[${i}][price_override]"
                       value="${prefill.price_override ?? ''}"
                       placeholder="سعر افتراضي"
                       class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 outline-none transition-all">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-zinc-500 uppercase mb-2">SKU</label>
                <input type="text" name="variants[${i}][sku]"
                       value="${prefill.sku ?? ''}"
                       placeholder="تلقائي"
                       class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:border-indigo-500 outline-none transition-all">
            </div>
        </div>
    </div>`;
}

function addVariantRow(prefill = {}) {
    const container = document.getElementById('variants-container');
    if (!container) return; // حماية لو العنصر مش موجود

    const i = window.variantIndex++;
    const empty = document.getElementById('variants-empty');

    if (empty) empty.style.display = 'none';

    const el = document.createElement('div');
    el.innerHTML = buildVariantRowHTML(i, prefill);
    const row = el.firstElementChild;

    row.style.opacity = '0';
    row.style.transform = 'translateY(10px)';
    container.appendChild(row);

    requestAnimationFrame(() => {
        row.style.transition = 'all .3s ease-out';
        row.style.opacity = '1';
        row.style.transform = 'translateY(0)';
    });
}
</script>
