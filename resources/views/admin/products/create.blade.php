@extends('layouts.admin')
@section('title', 'إضافة منتج جديد')

@push('head')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap');
    
    :root {
        --bg-deep: #0a0a0a;       /* أسود عميق للخلفية */
        --card-bg: #e5e7eb;       /* رمادي فاتح جداً لتبرز عليه النصوص السوداء */
        --text-black: #000000;    /* أسود صريح للخطوط بناءً على طلبك */
        --text-muted: #333333;    /* أسود خفيف للعناصر الأقل أهمية */
        --brand-accent: #4f46e5;  /* لون تفاعلي بسيط */
    }

    body { 
        font-family: 'Cairo', sans-serif; 
        background-color: var(--bg-deep); 
        color: var(--text-black); /* النص الأساسي أسود */
        direction: rtl;
    }

    /* العناوين والخطوط - كلها سوداء صريحة */
    h1, h2, h3, h4, label, span, p, input, textarea, select {
        color: var(--text-black) !important;
        font-weight: 700; /* جعل الخط سميك لزيادة الوضوح */
    }

    .cc-card {
        background-color: var(--card-bg);
        border-radius: 12px;
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        padding: 30px;
        margin-bottom: 30px;
    }

    .cc-input {
        background-color: rgba(255, 255, 255, 0.5);
        border: 2px solid #000; /* حدود سوداء واضحة */
        border-radius: 8px;
        color: var(--text-black) !important;
        padding: 12px 15px;
        width: 100%;
        outline: none;
    }

    .cc-input:focus {
        background-color: #fff;
        border-color: var(--brand-accent);
    }

    .cc-label {
        display: block;
        margin-bottom: 8px;
        font-size: 14px;
        text-transform: uppercase;
    }

    .cc-btn-primary {
        background-color: #000;
        color: #fff !important; /* الكتابة داخل الزر أبيض ليظهر فوق الأسود */
        padding: 15px 30px;
        border-radius: 8px;
        font-weight: 900;
        border: none;
        cursor: pointer;
        transition: 0.3s;
    }

    .cc-btn-primary:hover {
        background-color: var(--brand-accent);
        transform: translateY(-2px);
    }

    /* منطقة الرفع */
    .upload-zone {
        border: 3px dashed #000;
        background-color: rgba(0,0,0,0.05);
        border-radius: 12px;
        padding: 40px;
        text-align: center;
        cursor: pointer;
    }

    .section-number {
        background: #000;
        color: #fff !important;
        width: 35px;
        height: 35px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        margin-left: 10px;
    }
</style>
@endpush

@section('admin-content')
<div class="cc-page p-4 sm:p-10 max-w-7xl mx-auto" dir="rtl">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-12">
        <div>
            <nav class="flex gap-2 text-xs font-bold mb-3 uppercase tracking-widest text-zinc-600">
                <a href="{{ route('admin.products.index') }}" class="hover:text-indigo-400 transition-colors">المنتجات</a>
                <span>/</span>
                <span class="text-zinc-400">إضافة جديد</span>
            </nav>
            <h1 class="text-3xl font-extrabold text-zinc-100tracking-tighter">إنشاء منتج جديد</h1>
        </div>
        <button type="submit" form="product-form" class="cc-btn-primary shadow-lg shadow-indigo-500/10">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            نشر المنتج في المتجر
        </button>
    </div>

    @if($errors->any())
    <div class="mb-10 p-5 bg-rose-950/30 border border-rose-500/20 rounded-2xl">
        <h4 class="text-rose-400 font-bold text-sm mb-3 flex items-center gap-2">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/></svg>
            مراجعة الحقول التالية:
        </h4>
        <ul class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-1.5">
            @foreach($errors->all() as $e)
            <li class="text-rose-300/90 text-xs flex items-center gap-2.5">
                <span class="w-1.5 h-1.5 rounded-full bg-rose-500 shrink-0"></span>{{ $e }}
            </li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" id="product-form">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            
            {{-- Main Form Column --}}
            <div class="lg:col-span-8 space-y-10">
                
                {{-- 01: Information --}}
                <div class="cc-card p-8 md:p-10">
                    <div class="flex items-center gap-4 mb-10 pb-4 border-b border-white/[0.03]">
                        <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center font-bold">01</div>
                        <h2 class="text-2xl font-bold text-zinc-100">المعلومات الأساسية</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="md:col-span-2">
                            <label class="cc-label">اسم المنتج</label>
                            <input type="text" name="name" value="{{ old('name') }}" required class="cc-input" placeholder="اسم المنتج الواضح للعملاء">
                        </div>
                        
                        <div>
                            <label class="cc-label">السعر الأساسي ($)</label>
                            <input type="number" step="0.01" name="base_price" required class="cc-input font-mono text-emerald-400" placeholder="0.00">
                        </div>

                        <div>
                            <label class="cc-label">سعر الخصم (اختياري)</label>
                            <input type="number" step="0.01" name="discount_price" class="cc-input font-mono text-zinc-400" placeholder="0.00">
                        </div>

                        <div class="md:col-span-2">
                            <label class="cc-label">وصف المنتج الكامل</label>
                            <textarea name="description" rows="6" class="cc-input custom-scroll" placeholder="اكتب هنا تفاصيل ومواصفات المنتج..."></textarea>
                        </div>
                    </div>
                </div>

                {{-- 02: Visuals --}}
                <div class="cc-card p-8 md:p-10">
                    <div class="flex items-center gap-4 mb-10 pb-4 border-b border-white/[0.03]">
                        <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center font-bold">02</div>
                        <h2 class="text-2xl font-bold text-zinc-100">صور المنتج</h2>
                    </div>

                    <div class="space-y-10">
                        {{-- Main Upload --}}
                        <div class="flex flex-col md:flex-row gap-8 items-center p-6 rounded-3xl bg-black/10 border border-white/[0.02]">
                            <div class="relative">
                                <div class="w-40 h-40 rounded-3xl overflow-hidden border-2 border-dashed border-zinc-700 bg-black/20 flex items-center justify-center" id="main-preview-container">
                                    <img id="img-preview" class="w-full h-full object-cover hidden" alt="">
                                    <div id="img-ph" class="text-center text-zinc-700">
                                        <svg class="w-12 h-12 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="1.5"/></svg>
                                        <span class="text-[10px] font-bold uppercase tracking-widest">Cover</span>
                                    </div>
                                </div>
                                <label class="absolute -bottom-2 -left-2 bg-indigo-600 hover:bg-indigo-500 w-11 h-11 rounded-2xl flex items-center justify-center cursor-pointer shadow-xl transition-all active:scale-95">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="2.5" stroke-linecap="round"/></svg>
                                    <input type="file" name="main_image" accept="image/*" onchange="previewMainImg(this)" class="hidden">
                                </label>
                            </div>
                            <div class="flex-1 text-center md:text-right">
                                <h3 class="text-lg font-bold text-zinc-100 mb-1">صورة الغلاف</h3>
                                <p class="text-sm text-zinc-500 leading-relaxed max-w-sm">هذه الصورة ستظهر كواجهة للمنتج في صفحات المتجر. اختر صورة واضحة وعالية الجودة.</p>
                            </div>
                        </div>

                        {{-- Gallery --}}
                        <div>
                            <label class="cc-label">صور إضافية للألبوم (حتى ١٠ صور)</label>
                            <div class="upload-zone" onclick="document.getElementById('multi-input').click()">
                                <input type="file" id="multi-input" name="product_images[]" multiple accept="image/*" class="hidden" onchange="handleMultiImages(this)">
                                <div class="space-y-4">
                                    <div class="w-16 h-16 bg-white/[0.03] rounded-full flex items-center justify-center mx-auto text-indigo-400">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-zinc-200 font-bold">اسحب الصور هنا أو اضغط للاختيار</p>
                                        <p class="text-xs text-zinc-600 mt-2">PNG, JPG, WebP (Max 5MB لكل صورة)</p>
                                    </div>
                                </div>
                            </div>
                            <div id="multi-preview" class="grid grid-cols-4 md:grid-cols-6 gap-5 mt-8"></div>
                        </div>
                    </div>
                </div>

                {{-- 03: Inventory & Variants --}}
                <div class="cc-card p-8 md:p-10">
                    <div class="flex items-center justify-between mb-10 gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center font-bold">03</div>
                            <h2 class="text-2xl font-bold text-zinc-100">المخزون والمتغيرات</h2>
                        </div>
                        <button type="button" onclick="addVariantRow()" class="px-5 py-2.5 bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-300 text-xs font-bold rounded-xl transition-colors">
                            + إضافة خيار
                        </button>
                    </div>

                    <div id="variants-container" class="space-y-6"></div>

                    <div id="variants-empty" class="text-center py-20 bg-black/10 border-2 border-dashed border-white/[0.02] rounded-3xl">
                        <svg class="w-16 h-16 mx-auto mb-5 text-zinc-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" stroke-width="1"/></svg>
                        <p class="text-zinc-700 text-sm font-medium">اضغط على "إضافة خيار" لتحديد الألوان أو المقاسات المتاحة.</p>
                    </div>
                </div>
            </div>

            {{-- Sidebar Column --}}
            <div class="lg:col-span-4 space-y-10">
                
                {{-- Visibility --}}
                <div class="cc-card p-7">
                    <h3 class="text-lg font-bold text-zinc-100 mb-7 flex items-center gap-3">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-600 animate-pulse"></span>
                        النشر والظهور
                    </h3>
                    <div class="space-y-5">
                        <label class="flex items-center justify-between p-4 rounded-2xl bg-black/10 border border-white/[0.02] hover:border-white/[0.05] cursor-pointer transition-colors">
                            <span class="text-sm font-bold text-zinc-300">تفعيل المنتج للعملاء</span>
                            <div class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" checked class="sr-only peer">
                                <div class="w-11 h-6 bg-zinc-700 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-zinc-300 after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                            </div>
                        </label>
                        <label class="flex items-center justify-between p-4 rounded-2xl bg-black/10 border border-white/[0.02] hover:border-white/[0.05] cursor-pointer transition-colors">
                            <span class="text-sm font-bold text-zinc-300">تمييز كمنتج مميز ⭐</span>
                            <div class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_featured" value="1" class="sr-only peer">
                                <div class="w-11 h-6 bg-zinc-700 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-zinc-300 after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-600"></div>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Categories --}}
                <div class="cc-card p-7">
                    <h3 class="text-lg font-bold text-zinc-100 mb-7">أقسام المنتج</h3>
                    <div class="max-h-[550px] overflow-y-auto custom-scroll pr-3 space-y-4">
                        @foreach($categories as $root)
                        <div class="p-4 rounded-2xl bg-black/10 border border-white/[0.02]">
                            <div class="flex items-center gap-3.5">
                                <input type="checkbox" name="category_ids[]" value="{{ $root->id }}" id="cat-{{ $root->id }}" class="w-5 h-5 rounded-md border-zinc-700 text-indigo-600 focus:ring-offset-0 focus:ring-indigo-600 bg-zinc-900">
                                <label for="cat-{{ $root->id }}" class="text-sm font-extrabold text-zinc-100 flex-1 cursor-pointer">{{ $root->name }}</label>
                                <input type="radio" name="primary_category_id" value="{{ $root->id }}" class="w-4 h-4 text-emerald-500 bg-zinc-900 border-zinc-700 focus:ring-offset-0 focus:ring-emerald-500" title="تعيين كأساسي">
                            </div>

                            @if($root->allActiveChildren->count() > 0)
                            <div class="mt-4 mr-7 space-y-3.5 border-r border-white/[0.03] pr-4">
                                @foreach($root->allActiveChildren as $sub)
                                <div class="flex items-center gap-3 group">
                                    <input type="checkbox" name="category_ids[]" value="{{ $sub->id }}" id="cat-{{ $sub->id }}" class="w-4 h-4 rounded border-zinc-700 text-indigo-500 bg-zinc-900">
                                    <label for="cat-{{ $sub->id }}" class="text-xs font-bold text-zinc-500 group-hover:text-zinc-300 cursor-pointer flex-1 transition-colors">{{ $sub->name }}</label>
                                    <input type="radio" name="primary_category_id" value="{{ $sub->id }}" class="w-3 h-3 text-emerald-500 bg-zinc-900 border-zinc-700 focus:ring-offset-0 focus:ring-emerald-500">
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Information Box --}}
                <div class="p-6 rounded-3xl bg-indigo-950/20 border border-indigo-500/10">
                    <div class="flex gap-4">
                        <svg class="w-6 h-6 text-indigo-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="1.5" stroke-linecap="round"/></svg>
                        <p class="text-xs text-indigo-300/90 leading-relaxed">
                            في حال وجود متغيرات، تأكد من ضبط الكميات بدقة لكل متغير. سيتم استخدام "السعر الأساسي" كافتراضي إذا لم يتم تحديد سعر خاص للمتغير.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    // الحفاظ على كافة البراميتارز البرمجية دون تغيير
    window.ATTRIBUTES = {!! json_encode($attributes->map(fn($a) => [
        'id'     => $a->id,
        'name'   => $a->name,
        'type'   => $a->type,
        'values' => $a->values->map(fn($v) => [
            'id'        => $v->id,
            'label'     => $v->label ?? $v->value,
            'color_hex' => $v->color_hex,
        ])
    ])) !!};
    window.variantIndex = 0;

    function previewMainImg(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('img-preview').src = e.target.result;
                document.getElementById('img-preview').classList.remove('hidden');
                document.getElementById('img-ph').classList.add('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function handleMultiImages(input) {
        const container = document.getElementById('multi-preview');
        container.innerHTML = ''; 
        if (input.files) {
            Array.from(input.files).forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const html = `
                        <div class="preview-item aspect-ratio-1 rounded-2xl overflow-hidden border border-white/5 relative group animation-slideIn">
                            <img src="${e.target.result}" class="w-full h-full object-cover">
                            <div class="remove-img absolute top-2 right-2 bg-rose-600/80 text-white rounded-lg p-1.5 cursor-pointer opacity-0 group-hover:opacity-100 transition-all scale-90 group-hover:scale-100" onclick="this.parentElement.remove()">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" stroke-linecap="round"/></svg>
                            </div>
                        </div>
                    `;
                    container.insertAdjacentHTML('beforeend', html);
                }
                reader.readAsDataURL(file);
            });
        }
    }

    function addVariantRow() {
        const container = document.getElementById('variants-container');
        const emptyState = document.getElementById('variants-empty');
        if (emptyState) emptyState.style.display = 'none';

        const i = window.variantIndex++;
        const row = document.createElement('div');
        row.className = "variant-card p-6 rounded-2xl bg-black/10 border border-white/[0.02] relative hover:border-indigo-500/20 transition-all";
        row.innerHTML = buildVariantRowHTML(i);
        container.appendChild(row);
    }

    function buildVariantRowHTML(i) {
        let attrHTML = '';
        window.ATTRIBUTES.forEach(attr => {
            let options = '';
            attr.values.forEach(v => {
                const isColor = attr.type === 'color';
                options += `
                    <label class="cursor-pointer group">
                        <input type="checkbox" name="variants[${i}][attribute_values][]" value="${v.id}" class="sr-only peer">
                        <span class="${isColor ? 'w-9 h-9 rounded-full block border-2 border-zinc-800 peer-checked:border-zinc-200 peer-checked:scale-105 shadow-xl' : 'px-5 py-2 bg-zinc-800 rounded-xl text-xs font-bold text-zinc-400 peer-checked:bg-indigo-600 peer-checked:text-zinc-50 group-hover:bg-zinc-700'} transition-all flex items-center justify-center text-center" 
                              style="${isColor ? 'background-color:' + v.color_hex : ''}" title="${v.label}">
                              ${isColor ? '' : v.label}
                        </span>
                    </label>
                `;
            });

            attrHTML += `
                <div class="mb-7 last:mb-0">
                    <p class="text-[10px] font-black text-zinc-600 mb-3.5 uppercase tracking-widest">${attr.name}</p>
                    <div class="flex flex-wrap gap-3">${options}</div>
                </div>
            `;
        });

        return `
            <div class="flex justify-between items-center mb-6 pb-4 border-b border-white/[0.03]">
                <div class="flex items-center gap-3.5">
                    <span class="w-9 h-9 rounded-xl bg-black/20 text-indigo-400 flex items-center justify-center text-sm font-black mono border border-white/5">#${i + 1}</span>
                    <h4 class="text-base font-bold text-zinc-200">تخصيص خيارات المتغير</h4>
                </div>
                <button type="button" onclick="this.closest('.variant-card').remove()" class="text-zinc-600 hover:text-rose-500 transition-colors p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round"/></svg>
                </button>
            </div>
            <div class="p-5 bg-black/20 rounded-xl mb-7 border border-white/[0.02]">
                ${attrHTML}
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="cc-label">الكمية في المخزن</label>
                    <input type="number" name="variants[${i}][stock_quantity]" value="0" class="cc-input py-3 text-sm">
                </div>
                <div>
                    <label class="cc-label">سعر المتغير (اختياري)</label>
                    <input type="number" step="0.01" name="variants[${i}][price_override]" class="cc-input py-3 text-sm" placeholder="يجاوز السعر الأساسي">
                </div>
                <div>
                    <label class="cc-label">رمز SKU الخاص</label>
                    <input type="text" name="variants[${i}][sku]" class="cc-input py-3 text-sm font-mono text-zinc-500" placeholder="يترك للتوليد التلقائي">
                </div>
            </div>
        `;
    }
</script>
@endsection