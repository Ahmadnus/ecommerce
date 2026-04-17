@extends('layouts.admin')
@section('title', 'تعديل: ' . $product->name)

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
.cc-btn-primary:hover { filter:brightness(1.1); }
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
    border-radius:12px; padding:16px; transition:border-color .15s;
}
.variant-card:hover { border-color:rgba(255,255,255,.14); }
</style>
@endpush

@section('admin-content')
<div class="cc-page p-4 sm:p-6 lg:p-8 max-w-5xl mx-auto" dir="rtl">

    {{-- Back --}}
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('admin.products.show', $product) }}"
           class="flex items-center gap-2 text-sm transition-colors"
           style="color:var(--cc-muted)">
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

    <h1 class="text-xl font-black text-white mb-8 flex items-center gap-3">
        <span class="w-8 h-8 rounded-xl text-sm font-black flex items-center justify-center"
              style="background:rgba(245,158,11,.2); color:var(--cc-amber)">✎</span>
        تعديل: {{ Str::limit($product->name, 40) }}
    </h1>

    @if($errors->any())
    <div class="mb-6 cc-card p-4 border-rose-500/30">
        <p class="text-rose-400 text-sm font-bold mb-2">يوجد أخطاء:</p>
        <ul class="space-y-1">
            @foreach($errors->all() as $e)
            <li class="text-rose-300 text-xs flex items-center gap-2">
                <span class="w-1 h-1 rounded-full bg-rose-400 flex-shrink-0"></span>{{ $e }}
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
            <h2 class="text-sm font-bold text-white mb-5 flex items-center gap-2">
                <span class="section-num">١</span>
                معلومات المنتج
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <label class="cc-label">اسم المنتج <span class="text-rose-400">*</span></label>
                    <input type="text" name="name"
                           value="{{ old('name', $product->name) }}" required
                           class="cc-input @error('name') has-error @enderror">
                </div>
                <div>
                    <label class="cc-label">السعر الأساسي <span class="text-rose-400">*</span></label>
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
                <div>
                    <label class="cc-label">وصف قصير</label>
                    <input type="text" name="short_description"
                           value="{{ old('short_description', $product->short_description) }}" maxlength="500"
                           class="cc-input">
                </div>
                <div class="sm:col-span-2">
                    <label class="cc-label">الوصف التفصيلي</label>
                    <textarea name="description" rows="3"
                              class="cc-input">{{ old('description', $product->description) }}</textarea>
                </div>
                <div class="sm:col-span-2 flex flex-wrap gap-3">
                    <label class="flex items-center gap-3 px-4 py-3 rounded-xl cursor-pointer"
                           style="background:rgba(255,255,255,.04); border:1px solid rgba(255,255,255,.07)">
                        <input type="checkbox" name="is_active" value="1"
                               {{ old('is_active', $product->status === 'active') ? 'checked' : '' }}
                               class="w-4 h-4 accent-emerald-500">
                        <span class="text-sm font-semibold text-white">تفعيل المنتج</span>
                    </label>
                    <label class="flex items-center gap-3 px-4 py-3 rounded-xl cursor-pointer"
                           style="background:rgba(255,255,255,.04); border:1px solid rgba(255,255,255,.07)">
                        <input type="checkbox" name="is_featured" value="1"
                               {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}
                               class="w-4 h-4 accent-amber-400">
                        <span class="text-sm font-semibold text-white">منتج مميز ⭐</span>
                    </label>
                </div>
            </div>
        </div>

        {{-- ══ SECTION 2: Categories ══════════════════════════════════ --}}
        <div class="cc-card p-6">
            <h2 class="text-sm font-bold text-white mb-5 flex items-center gap-2">
                <span class="section-num">٢</span>
                التصنيفات
            </h2>
            <div class="rounded-xl overflow-hidden border" style="border-color:var(--cc-border)">
                <div class="grid grid-cols-[1fr_auto] px-4 py-2 text-[10px] font-bold uppercase tracking-widest border-b"
                     style="color:var(--cc-muted); border-color:var(--cc-border); background:rgba(255,255,255,.02)">
                    <span>التصنيف</span><span>أساسي</span>
                </div>
                @foreach($categories as $root)
                <div class="grid grid-cols-[1fr_auto] items-center border-b"
                     style="border-color:var(--cc-border)">
                    <label class="flex items-center gap-3 px-4 py-3 cursor-pointer hover:bg-white/5 transition">
                        <input type="checkbox" name="category_ids[]" value="{{ $root->id }}"
                               {{ in_array($root->id, $selectedCatIds) ? 'checked' : '' }}
                               class="w-4 h-4 accent-indigo-400 rounded">
                        <span class="font-bold text-sm text-white">{{ $root->name }}</span>
                        <span class="text-[10px] mono ms-auto" style="color:var(--cc-muted)">{{ $root->slug }}</span>
                    </label>
                    <div class="px-4">
                        <input type="radio" name="primary_category_id" value="{{ $root->id }}"
                               {{ $primaryCatId == $root->id ? 'checked' : '' }}
                               class="w-4 h-4 accent-indigo-400">
                    </div>
                </div>
                @foreach($root->allActiveChildren as $sub)
                <div class="grid grid-cols-[1fr_auto] items-center border-b ps-5"
                     style="border-color:var(--cc-border); background:rgba(0,0,0,.1)">
                    <label class="flex items-center gap-3 px-4 py-2.5 cursor-pointer hover:bg-white/5 transition">
                        <input type="checkbox" name="category_ids[]" value="{{ $sub->id }}"
                               {{ in_array($sub->id, $selectedCatIds) ? 'checked' : '' }}
                               class="w-4 h-4 accent-indigo-400 rounded">
                        <svg class="w-3 h-3 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                        <span class="text-sm text-white">{{ $sub->name }}</span>
                    </label>
                    <div class="px-4">
                        <input type="radio" name="primary_category_id" value="{{ $sub->id }}"
                               {{ $primaryCatId == $sub->id ? 'checked' : '' }}
                               class="w-4 h-4 accent-indigo-400">
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
                صورة المنتج
            </h2>
            <div class="flex items-center gap-5">
                <div class="w-20 h-20 rounded-xl overflow-hidden flex-shrink-0 bg-zinc-800 border border-white/5"
                     id="img-ring">
                    @php $img = $product->getFirstMediaUrl('products') ?: $product->image_url; @endphp
                    <img id="img-preview"
                         src="{{ $img }}"
                         class="w-full h-full object-cover {{ $img ? '' : 'hidden' }}"
                         alt="">
                    @if(!$img)
                    <div id="img-ph" class="w-full h-full flex items-center justify-center"
                         style="color:var(--cc-muted)">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586
                                     a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6
                                     a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    @endif
                </div>
                <div class="flex-1">
                    <input type="file" name="main_image" accept="image/*"
                           onchange="previewImg(this)"
                           class="block w-full text-sm cursor-pointer"
                           style="color:var(--cc-muted)">
                    <p class="text-[10px] mt-2" style="color:var(--cc-muted)">
                        اترك فارغاً للإبقاء على الصورة الحالية
                    </p>
                </div>
            </div>
        </div>

        {{-- ══ SECTION 4: Variants ════════════════════════════════════ --}}
        <div class="cc-card p-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-sm font-bold text-white flex items-center gap-2">
                    <span class="section-num">٤</span>
                    المتغيرات
                    <span class="text-amber-400 text-[10px] font-normal">
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
                 style="border-color:rgba(255,255,255,.08); color:var(--cc-muted)">
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
                    <span class="inline-flex w-8 h-8 rounded-full border-2 border-transparent peer-checked:border-indigo-400 peer-checked:scale-110 hover:scale-105 transition-all" style="background:${v.color_hex||'#888'}"></span>
                </label>`;
            });
        } else {
            attr.values.forEach(v => {
                const chk = selected.includes(String(v.id)) ? 'checked' : '';
                valsHTML += `<label class="cursor-pointer">
                    <input type="checkbox" name="variants[${i}][attribute_values][]" value="${v.id}" ${chk} class="sr-only peer">
                    <span class="inline-block px-3 py-1.5 text-xs font-semibold rounded-lg border border-white/10 bg-white/5 text-zinc-400 peer-checked:bg-indigo-500/20 peer-checked:border-indigo-400/60 peer-checked:text-white transition-all select-none">${v.label||v.value}</span>
                </label>`;
            });
        }
        attrHTML += `<div><p class="text-[10px] font-bold uppercase tracking-widest mb-2" style="color:var(--cc-muted)">${attr.name}</p><div class="flex flex-wrap gap-2">${valsHTML}</div></div>`;
    });
    return `<div class="variant-card" data-idx="${i}">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold text-zinc-500">متغير #${i+1}</span>
            <button type="button" onclick="removeVariantRow(this)" class="cc-btn cc-btn-danger cc-btn-sm">✕</button>
        </div>
        ${attrs.length ? `<div class="grid grid-cols-1 sm:grid-cols-${Math.min(attrs.length,3)} gap-4 mb-4">${attrHTML}</div>` : ''}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 border-t pt-4" style="border-color:rgba(255,255,255,.07)">
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
</script>
