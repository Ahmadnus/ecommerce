@extends('layouts.admin')
@section('title', 'مركز إدارة المنتجات')

@push('head')
<style>
/* ══════════════════════════════════════════════════════════════
   COMMAND CENTER — dark industrial aesthetic
   Dominant: slate-900 / zinc-800
   Accent: amber-400 (stock warnings), emerald-400 (in stock),
           rose-500 (critical)
══════════════════════════════════════════════════════════════ */
@import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600&family=Cairo:wght@400;600;700;800&display=swap');

:root {
    --cc-bg:        #0f1117;
    --cc-surface:   #1a1d27;
    --cc-border:    rgba(255,255,255,.07);
    --cc-text:      #e2e8f0;
    --cc-muted:     #64748b;
    --cc-amber:     #f59e0b;
    --cc-emerald:   #10b981;
    --cc-rose:      #f43f5e;
    --cc-brand:     var(--brand-color, #6366f1);
    --cc-mono:      'JetBrains Mono', monospace;
    --cc-sans:      'Cairo', sans-serif;
}

body { font-family: var(--cc-sans); }

.cc-page { background: var(--cc-bg); min-height: 100vh; }

/* ── Stat cards ──────────────────────────────────────────────── */
.stat-card {
    background: var(--cc-surface);
    border: 1px solid var(--cc-border);
    border-radius: 16px;
    padding: 20px 22px;
    position: relative;
    overflow: hidden;
    transition: border-color .2s, transform .2s;
}
.stat-card::before {
    content: '';
    position: absolute; top: 0; left: 0; right: 0; height: 2px;
    border-radius: 16px 16px 0 0;
}
.stat-card.amber::before { background: var(--cc-amber); }
.stat-card.emerald::before { background: var(--cc-emerald); }
.stat-card.rose::before { background: var(--cc-rose); }
.stat-card.brand::before { background: var(--cc-brand); }
.stat-card:hover { border-color: rgba(255,255,255,.15); transform: translateY(-2px); }

/* ── Product row card ────────────────────────────────────────── */
.product-row {
    background: var(--cc-surface);
    border: 1px solid var(--cc-border);
    border-radius: 14px;
    transition: border-color .15s, box-shadow .15s;
}
.product-row:hover {
    border-color: rgba(255,255,255,.14);
    box-shadow: 0 4px 24px rgba(0,0,0,.4);
}

/* ── Stock badges ────────────────────────────────────────────── */
.badge-out    { background: rgba(244,63,94,.12);  color: #f43f5e; border: 1px solid rgba(244,63,94,.25); }
.badge-low    { background: rgba(245,158,11,.12); color: #f59e0b; border: 1px solid rgba(245,158,11,.25); }
.badge-ok     { background: rgba(16,185,129,.12); color: #10b981; border: 1px solid rgba(16,185,129,.25); }
.badge-draft  { background: rgba(148,163,184,.1); color: #94a3b8; border: 1px solid rgba(148,163,184,.2); }

/* ── Variant chips ───────────────────────────────────────────── */
.variant-chip {
    display: inline-flex; align-items: center; gap: 5px;
    background: rgba(255,255,255,.05);
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 8px;
    padding: 3px 8px;
    font-size: 10.5px;
    font-family: var(--cc-mono);
    color: var(--cc-text);
    white-space: nowrap;
}
.variant-chip.zero { border-color: rgba(244,63,94,.3); color: #f43f5e; }
.variant-chip.low  { border-color: rgba(245,158,11,.3); color: #f59e0b; }

/* ── Toolbar ─────────────────────────────────────────────────── */
.cc-input {
    background: rgba(255,255,255,.05);
    border: 1px solid var(--cc-border);
    border-radius: 10px;
    color: var(--cc-text);
    padding: 8px 14px;
    font-size: 12.5px;
    font-family: var(--cc-sans);
    outline: none;
    transition: border-color .15s, background .15s;
}
.cc-input:focus { border-color: var(--cc-brand); background: rgba(255,255,255,.07); }
.cc-input::placeholder { color: var(--cc-muted); }
.cc-input option { background: #1a1d27; }

.cc-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 9px 18px;
    border-radius: 10px;
    font-size: 12.5px; font-weight: 700;
    cursor: pointer; border: none;
    transition: all .15s;
}
.cc-btn-primary {
    background: var(--cc-brand); color: #fff;
}
.cc-btn-primary:hover { filter: brightness(1.12); transform: translateY(-1px); }
.cc-btn-ghost {
    background: rgba(255,255,255,.06);
    border: 1px solid var(--cc-border);
    color: var(--cc-text);
}
.cc-btn-ghost:hover { background: rgba(255,255,255,.1); }

/* ── Progress bar (stock fill) ───────────────────────────────── */
.stock-bar { height: 3px; background: rgba(255,255,255,.08); border-radius: 99px; }
.stock-bar-fill { height: 100%; border-radius: 99px; transition: width .4s ease; }

/* ── Mono price tag ──────────────────────────────────────────── */
.price-tag { font-family: var(--cc-mono); font-size: 12px; letter-spacing: -.01em; }

/* ── Expand / collapse variants ─────────────────────────────── */
.variants-panel {
    max-height: 0; overflow: hidden;
    transition: max-height .3s cubic-bezier(.4,0,.2,1);
}
.variants-panel.open { max-height: 2000px; }

/* ── Scrollbar ───────────────────────────────────────────────── */
::-webkit-scrollbar { width: 5px; height: 5px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: rgba(255,255,255,.15); border-radius: 99px; }
</style>
@endpush

@section('admin-content')
<div class="cc-page p-4 sm:p-6 lg:p-8" dir="rtl">

    {{-- ══ Header ═════════════════════════════════════════════════════════ --}}
    <div class="flex items-start justify-between mb-8 flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl flex items-center justify-center text-sm"
                      style="background:var(--cc-brand)">⬡</span>
                مركز المنتجات
            </h1>
            <p class="text-sm mt-1" style="color:var(--cc-muted)">
                إدارة المخزون · التسعير · الصنف
            </p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="cc-btn cc-btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            منتج جديد
        </a>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="mb-5 flex items-center gap-3 bg-emerald-500/10 border border-emerald-500/25 rounded-xl px-4 py-3">
        <svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
        </svg>
        <span class="text-emerald-300 text-sm font-semibold">{{ session('success') }}</span>
    </div>
    @endif

   {{-- ══ Stat cards ══════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    
    {{-- بطاقة إجمالي المنتجات - لون براند مضيء --}}
<div class="stat-card brand group hover:cursor-default">
        <p class="text-[10px] font-bold uppercase tracking-[0.2em] mb-2 text-[--cc-muted]">المنتجات</p>
        <div class="flex items-baseline gap-2">
            {{-- الرقم الآن بلون سماوي مشع (Cyan-400) يبرز بقوة فوق الخلفية الداكنة --}}
            <p class="text-4xl font-black text-cyan-400 leading-none tracking-tighter drop-shadow-[0_0_12px_rgba(34,211,238,0.4)]">
                {{ number_format($stats['total']) }}
            </p>
        </div>
        <p class="text-[11px] mt-4 font-bold flex items-center gap-1.5 text-cyan-500/90">
            <span class="relative flex h-1.5 w-1.5">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-cyan-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-cyan-500"></span>
            </span>
            {{ $stats['active'] }} وحدة نشطة
        </p>
    </div>

    {{-- بطاقة النشطة - لون زمردي مشع --}}
    <div class="stat-card emerald group hover:cursor-default">
        <p class="text-[10px] font-bold uppercase tracking-[0.2em] mb-2" style="color: var(--cc-muted)">نشطة</p>
        <div class="flex items-baseline gap-2">
            {{-- الرقم بلون أخضر زمردي فاتح جداً لينافس الظلام --}}
            <p class="text-3xl font-black text-emerald-400 leading-none tracking-tighter drop-shadow-[0_0_15px_rgba(16,185,129,0.2)]">
                {{ number_format($stats['active']) }}
            </p>
        </div>
        <p class="text-[11px] mt-3 font-bold flex items-center gap-1.5 text-emerald-500">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            متاح للبيع
        </p>
    </div>

    {{-- بطاقة مخزون منخفض - لون برتقالي إنذار --}}
    <div class="stat-card amber group hover:cursor-default">
        <p class="text-[10px] font-bold uppercase tracking-[0.2em] mb-2" style="color: var(--cc-muted)">مخزون منخفض</p>
        <div class="flex items-baseline gap-2">
            <p class="text-3xl font-black text-amber-400 leading-none tracking-tighter font-mono drop-shadow-[0_0_15px_rgba(245,158,11,0.2)]">
                {{ number_format($stats['low']) }}
            </p>
        </div>
        <p class="text-[11px] mt-3 font-bold text-amber-500/90 flex items-center gap-1">
            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"></path></svg>
            تحذير المخزون
        </p>
    </div>

    {{-- بطاقة نفد المخزون - لون أحمر صارخ --}}
    <div class="stat-card rose group hover:cursor-default">
        <p class="text-[10px] font-bold uppercase tracking-[0.2em] mb-2" style="color: var(--cc-muted)">نفد المخزون</p>
        <div class="flex items-baseline gap-2">
            <p class="text-3xl font-black text-rose-500 leading-none tracking-tighter font-mono drop-shadow-[0_0_15px_rgba(244,63,94,0.3)]">
                {{ number_format($stats['out']) }}
            </p>
        </div>
        <p class="text-[11px] mt-3 font-bold text-rose-500 flex items-center gap-1.5">
            <span class="relative flex h-2 w-2">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-500 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-600"></span>
            </span>
            مطلوب فوراً
        </p>
    </div>

</div>
    {{-- ══ Filters / toolbar ══════════════════════════════════════════════ --}}
    <form method="GET" action="{{ route('admin.products.index') }}"
          class="flex flex-wrap items-center gap-3 mb-6">

        <div class="relative flex-1 min-w-[180px]">
            <svg class="absolute top-1/2 -translate-y-1/2 right-3 w-4 h-4 pointer-events-none"
                 style="color:var(--cc-muted)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="بحث بالاسم أو SKU..."
                   class="cc-input w-full pr-9">
        </div>

        <select name="status" class="cc-input">
            <option value="">كل الحالات</option>
            <option value="active"  {{ request('status') === 'active'  ? 'selected' : '' }}>نشط</option>
            <option value="draft"   {{ request('status') === 'draft'   ? 'selected' : '' }}>مسودة</option>
        </select>

        <select name="stock" class="cc-input">
            <option value="">كل المخزون</option>
            <option value="out" {{ request('stock') === 'out' ? 'selected' : '' }}>نفد</option>
            <option value="low" {{ request('stock') === 'low' ? 'selected' : '' }}>منخفض</option>
        </select>

        <select name="category" class="cc-input">
            <option value="">كل التصنيفات</option>
            @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                {{ $cat->name }}
            </option>
            @endforeach
        </select>

        <select name="sort" class="cc-input">
            <option value="newest"     {{ request('sort','newest') === 'newest'    ? 'selected' : '' }}>الأحدث</option>
            <option value="name_asc"   {{ request('sort') === 'name_asc'           ? 'selected' : '' }}>الاسم ↑</option>
            <option value="price_desc" {{ request('sort') === 'price_desc'         ? 'selected' : '' }}>السعر ↓</option>
            <option value="stock_asc"  {{ request('sort') === 'stock_asc'          ? 'selected' : '' }}>المخزون ↑</option>
        </select>

        <button type="submit" class="cc-btn cc-btn-ghost">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h18M7 8h10m-5 4h2"/>
            </svg>
            تصفية
        </button>
        @if(request()->hasAny(['search','status','stock','category','sort']))
        <a href="{{ route('admin.products.index') }}" class="cc-btn cc-btn-ghost text-rose-400">✕ مسح</a>
        @endif
    </form>

    {{-- ══ Product rows ════════════════════════════════════════════════════ --}}
    <div class="space-y-3">

        @forelse($products as $product)
        @php
            $totalStock    = $product->total_stock;
            $variantCount  = $product->variants->count();
            $zeroVariants  = $product->variants->where('stock_quantity', 0)->count();
            $lowVariants   = $product->variants
                ->where('stock_quantity', '>', 0)
                ->where('stock_quantity', '<=', 5)->count();

            // Stock badge logic
            $stockLabel = match(true) {
                $totalStock === 0         => ['label' => 'نفد المخزون', 'cls' => 'badge-out'],
                $zeroVariants > 0         => ['label' => $zeroVariants . ' متغير نفد', 'cls' => 'badge-amber'],
                $lowVariants > 0          => ['label' => 'مخزون منخفض', 'cls' => 'badge-low'],
                default                   => ['label' => 'متوفر', 'cls' => 'badge-ok'],
            };

            $imgUrl = $product->getFirstMediaUrl('main')
                ?: ($product->image_url ?? null);
        @endphp

        <div class="product-row" id="product-{{ $product->id }}">

            {{-- ── Main row ─────────────────────────────────────────── --}}
            <div class="flex items-center gap-4 p-4">

                {{-- Image --}}
                <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-xl overflow-hidden flex-shrink-0
                             bg-zinc-800 border border-white/5">
                    @if($imgUrl)
                    <img src="{{ $imgUrl }}" alt="{{ $product->name }}"
                         class="w-full h-full object-cover">
                    @else
                    <div class="w-full h-full flex items-center justify-center text-zinc-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    @endif
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                        <a href="{{ route('admin.products.show', $product) }}"
                           class="font-bold text-white hover:text-blue-400 transition-colors text-sm sm:text-base truncate">
                            {{ $product->name }}
                        </a>
                        {{-- Status --}}
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full flex-shrink-0
                                     {{ $product->status === 'active' ? 'badge-ok' : 'badge-draft' }}">
                            {{ $product->status === 'active' ? 'نشط' : 'مسودة' }}
                        </span>
                        @if($product->is_featured)
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full flex-shrink-0 badge-amber">
                            ⭐ مميز
                        </span>
                        @endif
                    </div>

                    {{-- Meta row --}}
                    <div class="flex items-center gap-3 flex-wrap">
                        {{-- Category --}}
                        @if($product->categories->first())
                        <span class="text-[11px]" style="color:var(--cc-muted)">
                            {{ $product->categories->first()->name }}
                        </span>
                        @endif

                        {{-- SKU --}}
                        @if($product->sku)
                        <span class="price-tag text-[10px]" style="color:var(--cc-muted)">
                            {{ $product->sku }}
                        </span>
                        @endif

                        {{-- Stock badge --}}
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $stockLabel['cls'] }}">
                            {{ $stockLabel['label'] }}
                        </span>
                    </div>
                </div>

                {{-- Pricing --}}
                <div class="hidden sm:flex flex-col items-end gap-0.5 flex-shrink-0">
                    @if($product->is_on_sale)
                    <span class="price-tag text-base font-bold text-rose-400">
                        {{ number_format($product->discount_price, 2) }}
                    </span>
                    <span class="price-tag text-xs line-through" style="color:var(--cc-muted)">
                        {{ number_format($product->base_price, 2) }}
                    </span>
                    @else
                    <span class="price-tag text-base font-bold text-white">
                        {{ number_format($product->base_price, 2) }}
                    </span>
                    @endif
                </div>

                {{-- Stock total --}}
                <div class="hidden md:flex flex-col items-end flex-shrink-0 min-w-[60px]">
                    <span class="price-tag text-lg font-black
                                 {{ $totalStock === 0 ? 'text-rose-400' : ($totalStock <= 10 ? 'text-amber-400' : 'text-emerald-400') }}">
                        {{ $totalStock }}
                    </span>
                    <span class="text-[10px]" style="color:var(--cc-muted)">إجمالي</span>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-2 flex-shrink-0">
                    {{-- Expand variants --}}
                    @if($variantCount > 0)
                    <button type="button"
                            onclick="toggleVariants({{ $product->id }})"
                            class="cc-btn cc-btn-ghost px-3 py-2 text-[11px]"
                            id="toggle-btn-{{ $product->id }}">
                        <svg class="w-3.5 h-3.5 transition-transform" id="toggle-icon-{{ $product->id }}"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                        {{ $variantCount }}
                    </button>
                    @endif

                    {{-- Edit --}}
                    <a href="{{ route('admin.products.edit', $product) }}"
                       class="cc-btn cc-btn-ghost px-3 py-2" title="تعديل">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </a>

                    {{-- View --}}
                    <a href="{{ route('admin.products.show', $product) }}"
                       class="cc-btn cc-btn-ghost px-3 py-2" title="تفاصيل">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </a>

                    {{-- Delete --}}
                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                          onsubmit="return confirm('حذف {{ addslashes($product->name) }}؟')">
                        @csrf @method('DELETE')
                        <button type="submit" class="cc-btn cc-btn-ghost px-3 py-2 hover:text-rose-400" title="حذف">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>

            {{-- ── Variants expandable panel ─────────────────────────── --}}
            @if($variantCount > 0)
            <div class="variants-panel" id="variants-{{ $product->id }}">
                <div class="border-t px-4 py-3 space-y-1.5" style="border-color:var(--cc-border)">

                    {{-- Mini stock bar --}}
                    @php
                        $maxStock = $product->variants->max('stock_quantity') ?: 1;
                    @endphp

                    @foreach($product->variants->sortByDesc('stock_quantity') as $variant)
                    @php
                        $attrs = $variant->attributeValues->map(fn($av) =>
                            $av->attribute->name . ': ' . $av->value
                        )->implode(' · ');

                        $eff = $variant->price_override
                            ?? $product->discount_price
                            ?? $product->base_price;

                        $chipCls = match(true) {
                            $variant->stock_quantity === 0 => 'zero',
                            $variant->stock_quantity <= 5  => 'low',
                            default                        => '',
                        };

                        $barPct = $maxStock > 0
                            ? min(100, round(($variant->stock_quantity / $maxStock) * 100))
                            : 0;

                        $barColor = match(true) {
                            $variant->stock_quantity === 0 => '#f43f5e',
                            $variant->stock_quantity <= 5  => '#f59e0b',
                            default                        => '#10b981',
                        };
                    @endphp

                    <div class="flex items-center gap-3 group py-1">

                        {{-- SKU --}}
                        <span class="price-tag text-[10px] w-24 truncate flex-shrink-0"
                              style="color:var(--cc-muted)">
                            {{ $variant->sku }}
                        </span>

                        {{-- Attribute chips --}}
                        <div class="flex items-center gap-1 flex-wrap flex-1 min-w-0">
                            @foreach($variant->attributeValues as $av)
                            <span class="variant-chip {{ $chipCls }}">
                                @if($av->color_hex)
                                <span class="inline-block w-2.5 h-2.5 rounded-full flex-shrink-0"
                                      style="background:{{ $av->color_hex }}"></span>
                                @endif
                                {{ $av->value }}
                            </span>
                            @endforeach
                            @if(!$variant->is_active)
                            <span class="variant-chip badge-draft">معطل</span>
                            @endif
                        </div>

                        {{-- Stock bar --}}
                        <div class="hidden sm:flex items-center gap-2 w-28 flex-shrink-0">
                            <div class="stock-bar flex-1">
                                <div class="stock-bar-fill"
                                     style="width:{{ $barPct }}%; background:{{ $barColor }}"></div>
                            </div>
                            <span class="price-tag text-[11px] font-bold w-8 text-right"
                                  style="color:{{ $barColor }}">
                                {{ $variant->stock_quantity }}
                            </span>
                        </div>

                        {{-- Effective price --}}
                        <span class="price-tag text-[11px] w-16 text-right flex-shrink-0
                                     {{ $variant->price_override ? 'text-amber-400' : 'text-slate-400' }}">
                            {{ number_format($eff, 2) }}
                        </span>

                        {{-- Inline edit button --}}
                        <a href="{{ route('admin.products.edit', $product) }}#variant-{{ $variant->id }}"
                           class="opacity-0 group-hover:opacity-100 transition-opacity cc-btn cc-btn-ghost px-2 py-1 text-[10px]">
                            تعديل
                        </a>
                    </div>
                    @endforeach

                </div>
            </div>
            @endif

        </div>
        @empty
        <div class="text-center py-20" style="color:var(--cc-muted)">
            <svg class="w-16 h-16 mx-auto mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                      d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            <p class="text-lg font-bold text-slate-500">لا توجد منتجات</p>
            <a href="{{ route('admin.products.create') }}" class="cc-btn cc-btn-primary mt-4 inline-flex">
                إضافة أول منتج
            </a>
        </div>
        @endforelse

    </div>

    {{-- Pagination --}}
    @if($products->hasPages())
    <div class="mt-8 flex justify-center">
        {{ $products->links() }}
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
function toggleVariants(id) {
    const panel = document.getElementById('variants-' + id);
    const icon  = document.getElementById('toggle-icon-' + id);
    const open  = panel.classList.toggle('open');
    icon.style.transform = open ? 'rotate(180deg)' : '';
}
</script>
@endpush