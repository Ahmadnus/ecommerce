@extends('layouts.admin')
@section('title', 'إضافة منتج جديد')

@push('head')
<style>
@import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600&family=Cairo:wght@400;600;700;800&display=swap');

:root {
    --bg-deep: #ffffff;
    --card-bg: #ffffff;
    --text-black: #000000;
    --text-muted: #6b7280;
    --brand-accent: #000000;
    --border-color: #e5e7eb;
    --border-strong: #d1d5db;
    --bg-subtle: #f9fafb;
}

body {
    font-family: 'Cairo', sans-serif;
    background-color: var(--bg-deep);
    color: var(--text-black);
    direction: rtl;
}

h1, h2, h3, h4 { color: var(--text-black) !important; font-weight: 700; }

.cc-page label, .cc-page span, .cc-page p,
.cc-page input, .cc-page textarea, .cc-page select {
    color: var(--text-black);
}

.cc-card {
    background-color: var(--card-bg);
    border-radius: 12px;
    border: 1px solid var(--border-color);
    box-shadow: 0 1px 4px rgba(0,0,0,0.06);
    padding: 30px;
    margin-bottom: 30px;
}

.cc-input {
    background-color: #ffffff;
    border: 1px solid var(--border-strong);
    border-radius: 8px;
    color: var(--text-black) !important;
    padding: 12px 15px;
    width: 100%;
    outline: none;
    transition: border-color .15s, box-shadow .15s;
}
.cc-input:focus {
    background-color: #ffffff;
    border-color: #000000;
    box-shadow: 0 0 0 3px rgba(0,0,0,0.06);
}
.cc-input::placeholder { color: #9ca3af; }

.cc-label {
    display: block;
    margin-bottom: 8px;
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: #374151;
}

.cc-btn-primary {
    background-color: #000000;
    color: #ffffff !important;
    padding: 12px 28px;
    border-radius: 8px;
    font-weight: 800;
    border: none;
    cursor: pointer;
    transition: background .15s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.cc-btn-primary:hover { background-color: #1f2937; }

.upload-zone {
    border: 2px dashed #d1d5db;
    background-color: #f9fafb;
    border-radius: 12px;
    padding: 40px;
    text-align: center;
    cursor: pointer;
    transition: border-color .15s, background .15s;
}
.upload-zone:hover { border-color: #000000; background-color: #f3f4f6; }

.section-number {
    background: #000000;
    color: #ffffff !important;
    width: 32px; height: 32px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 800;
}
</style>
@endpush

@section('admin-content')
<div class="cc-page p-4 sm:p-10 max-w-7xl mx-auto" dir="rtl" style="background:#ffffff;">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-12">
        <div>
            <nav class="flex gap-2 text-xs font-bold mb-3 uppercase tracking-widest" style="color:#6b7280;">
                <a href="{{ route('admin.products.index') }}" class="transition-colors"
                   onmouseover="this.style.color='#000'" onmouseout="this.style.color='#6b7280'">المنتجات</a>
                <span>/</span>
                <span style="color:#9ca3af;">إضافة جديد</span>
            </nav>
            <h1 class="text-3xl font-extrabold tracking-tighter" style="color:#000000;">إنشاء منتج جديد</h1>
        </div>
        <button type="submit" form="product-form" class="cc-btn-primary">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M5 13l4 4L19 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            نشر المنتج في المتجر
        </button>
    </div>

    @if($errors->any())
    <div class="mb-10 p-5 rounded-2xl" style="background:#fff5f5; border:1px solid #fecaca;">
        <h4 class="font-bold text-sm mb-3 flex items-center gap-2" style="color:#dc2626;">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/>
            </svg>
            مراجعة الحقول التالية:
        </h4>
        <ul class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-1.5">
            @foreach($errors->all() as $e)
            <li class="text-xs flex items-center gap-2.5" style="color:#b91c1c;">
                <span class="w-1.5 h-1.5 rounded-full shrink-0" style="background:#dc2626;"></span>{{ $e }}
            </li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.products.store') }}" method="POST"
          enctype="multipart/form-data" id="product-form">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">

            {{-- ── Main column ─────────────────────────────────────── --}}
            <div class="lg:col-span-8 space-y-10">

                {{-- 01: Basic info (Arabic only) --}}
                <div class="cc-card p-8 md:p-10">
                    <div class="flex items-center gap-4 mb-10 pb-4" style="border-bottom:1px solid #f3f4f6;">
                        <div class="section-number">01</div>
                        <h2 class="text-2xl font-bold" style="color:#000000;">المعلومات الأساسية</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                        <div class="md:col-span-2">
                            <label class="cc-label">
                                اسم المنتج
                                <span style="color:#dc2626;">*</span>
                            </label>
                            <input type="text" name="name[ar]"
                                   value="{{ old('name.ar') }}"
                                   dir="rtl"
                                   class="cc-input"
                                   placeholder="اسم المنتج بالعربية">
                            @error('name.ar')
                                <p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="cc-label">وصف المنتج</label>
                            <textarea name="description[ar]" rows="5" dir="rtl"
                                      class="cc-input"
                                      placeholder="تفاصيل ومواصفات المنتج...">{{ old('description.ar') }}</textarea>
                        </div>

                        <div class="md:col-span-2">
                            <label class="cc-label">وصف مختصر</label>
                            <textarea name="short_description[ar]" rows="2" dir="rtl"
                                      class="cc-input"
                                      placeholder="وصف قصير يظهر في قائمة المنتجات...">{{ old('short_description.ar') }}</textarea>
                        </div>

                        {{-- Prices --}}
                        <div>
                            <label class="cc-label">السعر الأساسي <span style="color:#dc2626;">*</span></label>
                            <input type="number" step="0.01" name="base_price"
                                   value="{{ old('base_price') }}"
                                   required class="cc-input"
                                   style="font-family:'JetBrains Mono',monospace;"
                                   placeholder="0.00">
                            @error('base_price')
                                <p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="cc-label">سعر الخصم (اختياري)</label>
                            <input type="number" step="0.01" name="discount_price"
                                   value="{{ old('discount_price') }}"
                                   class="cc-input"
                                   style="font-family:'JetBrains Mono',monospace;"
                                   placeholder="0.00">
                            @error('discount_price')
                                <p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="cc-label">رمز SKU (اختياري)</label>
                            <input type="text" name="sku"
                                   value="{{ old('sku') }}"
                                   class="cc-input"
                                   style="font-family:'JetBrains Mono',monospace;"
                                   placeholder="يُولَّد تلقائياً إن تُرك فارغاً">
                            @error('sku')
                                <p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                </div>

                {{-- 02: Images --}}
                <div class="cc-card p-8 md:p-10">
                    <div class="flex items-center gap-4 mb-10 pb-4" style="border-bottom:1px solid #f3f4f6;">
                        <div class="section-number">02</div>
                        <h2 class="text-2xl font-bold" style="color:#000000;">صور المنتج</h2>
                    </div>

                    <div class="space-y-10">

                        {{-- Main image --}}
                        <div class="flex flex-col md:flex-row gap-8 items-center p-6 rounded-2xl"
                             style="background:#f9fafb; border:1px solid #f3f4f6;">
                            <div class="relative">
                                <div class="w-40 h-40 rounded-2xl overflow-hidden flex items-center justify-center"
                                     style="border:2px dashed #d1d5db; background:#ffffff;"
                                     id="main-preview-container">
                                    <img id="img-preview" class="w-full h-full object-cover hidden" alt="">
                                    <div id="img-ph" class="text-center" style="color:#9ca3af;">
                                        <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24" style="opacity:0.3;">
                                            <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="1.5"/>
                                        </svg>
                                        <span class="text-[10px] font-bold uppercase tracking-widest">غلاف المنتج</span>
                                    </div>
                                </div>
                                <label class="absolute -bottom-2 -left-2 w-11 h-11 rounded-xl flex items-center justify-center cursor-pointer shadow-lg transition-all active:scale-95"
                                       style="background:#000000;">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                         style="color:#ffffff;">
                                        <path d="M12 4v16m8-8H4" stroke-width="2.5" stroke-linecap="round"/>
                                    </svg>
                                    <input type="file" name="main_image" accept="image/*"
                                           onchange="previewMainImg(this)" class="hidden">
                                </label>
                            </div>
                            <div class="flex-1 text-center md:text-right">
                                <h3 class="text-lg font-bold mb-1" style="color:#000000;">صورة الغلاف</h3>
                                <p class="text-sm leading-relaxed max-w-sm" style="color:#6b7280;">
                                    هذه الصورة ستظهر كواجهة للمنتج في صفحات المتجر. اختر صورة واضحة وعالية الجودة.
                                </p>
                                @error('main_image')
                                    <p class="text-xs mt-2" style="color:#dc2626;">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Gallery --}}
                        <div>
                            <label class="cc-label">صور إضافية للألبوم (حتى ١٠ صور)</label>
                            <div class="upload-zone" onclick="document.getElementById('multi-input').click()">
                                <input type="file" id="multi-input" name="product_images[]" multiple
                                       accept="image/*" class="hidden" onchange="handleMultiImages(this)">
                                <div class="space-y-4">
                                    <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto"
                                         style="background:#f3f4f6; color:#000000;">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"
                                                  stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-bold" style="color:#000000;">اسحب الصور هنا أو اضغط للاختيار</p>
                                        <p class="text-xs mt-2" style="color:#9ca3af;">PNG, JPG, WebP (بحد أقصى 5MB لكل صورة)</p>
                                    </div>
                                </div>
                            </div>
                            <div id="multi-preview" class="grid grid-cols-4 md:grid-cols-6 gap-5 mt-8"></div>
                        </div>

                    </div>
                </div>

            </div>

            {{-- ── Sidebar ──────────────────────────────────────────── --}}
            <div class="lg:col-span-4 space-y-10">

                {{-- Visibility --}}
                <div class="cc-card p-7">
                    <h3 class="text-lg font-bold mb-7 flex items-center gap-3" style="color:#000000;">
                        <span class="w-2.5 h-2.5 rounded-full animate-pulse" style="background:#059669;"></span>
                        النشر والظهور
                    </h3>
                    <div class="space-y-5">
                        <label class="flex items-center justify-between p-4 rounded-xl cursor-pointer transition-colors"
                               style="background:#f9fafb; border:1px solid #e5e7eb;"
                               onmouseover="this.style.borderColor='#d1d5db'"
                               onmouseout="this.style.borderColor='#e5e7eb'">
                            <span class="text-sm font-bold" style="color:#000000;">تفعيل المنتج للعملاء</span>
                            <div class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1"
                                       {{ old('is_active', true) ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-300 rounded-full peer
                                            peer-checked:bg-green-600
                                            peer-checked:after:translate-x-full
                                            after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                            after:bg-white after:rounded-full after:h-5 after:w-5
                                            after:transition-all transition-colors"></div>
                            </div>
                        </label>
                        <label class="flex items-center justify-between p-4 rounded-xl cursor-pointer transition-colors"
                               style="background:#f9fafb; border:1px solid #e5e7eb;"
                               onmouseover="this.style.borderColor='#d1d5db'"
                               onmouseout="this.style.borderColor='#e5e7eb'">
                            <span class="text-sm font-bold" style="color:#000000;">تمييز كمنتج مميز ⭐</span>
                            <div class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_featured" value="1"
                                       class="sr-only peer" {{ old('is_featured') ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-300 rounded-full peer peer-checked:bg-green-600
                                            peer-checked:after:translate-x-full after:content-[''] after:absolute
                                            after:top-[2px] after:left-[2px] after:bg-white after:rounded-full
                                            after:h-5 after:w-5 after:transition-all transition-colors"></div>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Categories --}}
                <div class="cc-card p-7">
                    <h3 class="text-lg font-bold mb-7" style="color:#000000;">أقسام المنتج</h3>
                    @error('category_ids')
                        <p class="text-xs mb-3" style="color:#dc2626;">{{ $message }}</p>
                    @enderror
                    @error('primary_category_id')
                        <p class="text-xs mb-3" style="color:#dc2626;">{{ $message }}</p>
                    @enderror
                    <div class="max-h-[550px] overflow-y-auto pr-3 space-y-4">
                        @foreach($categories as $root)
                        <div class="p-4 rounded-xl" style="background:#f9fafb; border:1px solid #e5e7eb;">
                            <div class="flex items-center gap-3.5">
                                <input type="checkbox" name="category_ids[]"
                                       value="{{ $root->id }}" id="cat-{{ $root->id }}"
                                       {{ in_array($root->id, old('category_ids', [])) ? 'checked' : '' }}
                                       class="w-5 h-5 rounded-md accent-gray-900">
                                <label for="cat-{{ $root->id }}"
                                       class="text-sm font-extrabold flex-1 cursor-pointer"
                                       style="color:#000000;">{{ $root->getTranslation('name', 'ar') }}</label>
                                <input type="radio" name="primary_category_id"
                                       value="{{ $root->id }}"
                                       {{ old('primary_category_id') == $root->id ? 'checked' : '' }}
                                       class="w-4 h-4 accent-gray-900" title="تعيين كأساسي">
                            </div>
                            @if($root->allActiveChildren->count() > 0)
                            <div class="mt-4 mr-7 space-y-3.5 pr-4" style="border-right:1px solid #e5e7eb;">
                                @foreach($root->allActiveChildren as $sub)
                                <div class="flex items-center gap-3 group">
                                    <input type="checkbox" name="category_ids[]"
                                           value="{{ $sub->id }}" id="cat-{{ $sub->id }}"
                                           {{ in_array($sub->id, old('category_ids', [])) ? 'checked' : '' }}
                                           class="w-4 h-4 rounded accent-gray-900">
                                    <label for="cat-{{ $sub->id }}"
                                           class="text-xs font-bold cursor-pointer flex-1 transition-colors"
                                           style="color:#6b7280;"
                                           onmouseover="this.style.color='#000'"
                                           onmouseout="this.style.color='#6b7280'">{{ $sub->getTranslation('name', 'ar') }}</label>
                                    <input type="radio" name="primary_category_id"
                                           value="{{ $sub->id }}"
                                           {{ old('primary_category_id') == $sub->id ? 'checked' : '' }}
                                           class="w-3 h-3 accent-gray-900">
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Info tip --}}
                <div class="p-6 rounded-2xl" style="background:#f9fafb; border:1px solid #e5e7eb;">
                    <div class="flex gap-4">
                        <svg class="w-6 h-6 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24" style="color:#6b7280;">
                            <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                  stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                        <p class="text-xs leading-relaxed" style="color:#374151;">
                            سيتم توليد رمز SKU تلقائياً إذا تركته فارغاً. يمكنك إضافة متغيرات المنتج بعد الإنشاء من صفحة التعديل.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function previewMainImg(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('img-preview');
            const placeholder = document.getElementById('img-ph');
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            if (placeholder) placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function handleMultiImages(input) {
    const container = document.getElementById('multi-preview');
    container.innerHTML = '';
    if (input.files) {
        Array.from(input.files).forEach(function(file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const html = `
                    <div class="aspect-square rounded-xl overflow-hidden relative group"
                         style="border:1px solid #e5e7eb;">
                        <img src="${e.target.result}" class="w-full h-full object-cover">
                        <div class="absolute top-2 right-2 text-white rounded-lg p-1.5 cursor-pointer
                                    opacity-0 group-hover:opacity-100 transition-all"
                             style="background:rgba(220,38,38,0.9);"
                             onclick="this.parentElement.remove()">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" stroke-linecap="round"/>
                            </svg>
                        </div>
                    </div>`;
                container.insertAdjacentHTML('beforeend', html);
            };
            reader.readAsDataURL(file);
        });
    }
}
</script>
@endpush