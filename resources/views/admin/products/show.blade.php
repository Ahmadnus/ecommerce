@extends('layouts.admin')
@section('title', $product->name . ' — مركز المخزون')

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
.cc-input {
    background:rgba(255,255,255,.05); border:1px solid var(--cc-border);
    border-radius:10px; color:var(--cc-text); padding:8px 12px; font-size:13px;
    font-family:var(--cc-mono); outline:none; transition:border-color .15s;
    width:100%;
}
.cc-input:focus { border-color:var(--cc-brand); }
.cc-btn { display:inline-flex; align-items:center; gap:6px; padding:9px 16px;
    border-radius:10px; font-size:12.5px; font-weight:700; cursor:pointer; border:none;
    transition:all .15s; font-family:var(--cc-sans); }
.cc-btn-primary { background:var(--cc-brand); color:#fff; }
.cc-btn-primary:hover { filter:brightness(1.1); }
.cc-btn-ghost { background:rgba(255,255,255,.06); border:1px solid var(--cc-border); color:var(--cc-text); }
.cc-btn-ghost:hover { background:rgba(255,255,255,.1); }
.cc-btn-danger { background:rgba(244,63,94,.15); border:1px solid rgba(244,63,94,.3); color:#f43f5e; }
.cc-btn-danger:hover { background:rgba(244,63,94,.25); }

.badge-out  { background:rgba(244,63,94,.12);  color:#f43f5e; border:1px solid rgba(244,63,94,.25); }
.badge-low  { background:rgba(245,158,11,.12); color:#f59e0b; border:1px solid rgba(245,158,11,.25); }
.badge-ok   { background:rgba(16,185,129,.12); color:#10b981; border:1px solid rgba(16,185,129,.25); }

.variant-row {
    background:rgba(255,255,255,.03); border:1px solid rgba(255,255,255,.06);
    border-radius:12px; padding:14px 16px;
    transition:border-color .15s, background .15s;
}
.variant-row:hover { border-color:rgba(255,255,255,.12); background:rgba(255,255,255,.05); }

.stock-bar { height:4px; background:rgba(255,255,255,.08); border-radius:99px; }
.stock-bar-fill { height:100%; border-radius:99px; }

.price-tag { font-family:var(--cc-mono); }

/* pricing priority labels */
.price-override-badge {
    font-size:9px; font-weight:800; text-transform:uppercase; letter-spacing:.06em;
    padding:1px 6px; border-radius:4px;
    background:rgba(245,158,11,.15); color:#f59e0b; border:1px solid rgba(245,158,11,.3);
}
.price-discount-badge {
    font-size:9px; font-weight:800; text-transform:uppercase;
    padding:1px 6px; border-radius:4px;
    background:rgba(244,63,94,.12); color:#f43f5e;
}

/* Save flash animation */
@keyframes save-flash {
    0%,100% { box-shadow: none; }
    50% { box-shadow: 0 0 0 3px rgba(16,185,129,.4); }
}
.saving { animation: save-flash .6s ease; }
</style>
@endpush

@section('admin-content')
<div class="cc-page p-4 sm:p-6 lg:p-8" dir="rtl">

    {{-- Back --}}
    <div class="mb-6">
        <a href="{{ route('admin.products.index') }}"
           class="flex items-center gap-2 text-sm transition-colors"
           style="color:var(--cc-muted)"
           onmouseover="this.style.color=getComputedStyle(document.documentElement).getPropertyValue('--cc-text')"
           onmouseout="this.style.color='#64748b'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            العودة للمنتجات
        </a>
    </div>

    @if(session('success'))
    <div class="mb-5 flex items-center gap-3 bg-emerald-500/10 border border-emerald-500/25 rounded-xl px-4 py-3">
        <svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
        </svg>
        <span class="text-emerald-300 text-sm font-semibold">{{ session('success') }}</span>
    </div>
    @endif

    {{-- ══ Product header ════════════════════════════════════════════════ --}}
    <div class="cc-card p-6 mb-6 flex items-start gap-6 flex-wrap">
        {{-- Image --}}
        <div class="w-24 h-24 rounded-xl overflow-hidden flex-shrink-0 bg-zinc-800 border border-white/5">
            @php $img = $product->getFirstMediaUrl('products') ?: $product->image_url; @endphp
            @if($img)
            <img src="{{ $img }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
            @else
            <div class="w-full h-full flex items-center justify-center" style="color:var(--cc-muted)">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            @endif
        </div>

        {{-- Info --}}
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-3 flex-wrap mb-2">
                <h1 class="text-xl font-black text-white">{{ $product->name }}</h1>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full
                             {{ $product->status === 'active' ? 'badge-ok' : 'badge-draft' }}">
                    {{ $product->status === 'active' ? 'نشط' : 'مسودة' }}
                </span>
            </div>
            <div class="flex flex-wrap gap-4 text-sm" style="color:var(--cc-muted)">
                @if($product->sku)
                <span class="price-tag">{{ $product->sku }}</span>
                @endif
                @if($product->categories->first())
                <span>{{ $product->categories->pluck('name')->implode(' · ') }}</span>
                @endif
            </div>

            {{-- Pricing summary --}}
            <div class="flex items-center gap-4 mt-3 flex-wrap">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest mb-1" style="color:var(--cc-muted)">
                        السعر الأساسي
                    </p>
                    <span class="price-tag text-xl font-bold text-white">
                        {{ number_format($product->base_price, 2) }}
                    </span>
                </div>
                @if($product->discount_price)
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest mb-1 text-rose-400">سعر الخصم</p>
                    <span class="price-tag text-xl font-bold text-rose-400">
                        {{ number_format($product->discount_price, 2) }}
                    </span>
                </div>
                @endif
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest mb-1 text-emerald-400">إجمالي المخزون</p>
                    <span class="price-tag text-2xl font-black
                                 {{ $product->total_stock === 0 ? 'text-rose-400' : 'text-emerald-400' }}">
                        {{ $product->total_stock }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex gap-2 flex-shrink-0">
            <a href="{{ route('admin.products.edit', $product) }}" class="cc-btn cc-btn-ghost">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                تعديل
            </a>
        </div>
    </div>

    {{-- ══ Bulk stock editor ════════════════════════════════════════════ --}}
    <form method="POST"
          action="{{ route('admin.products.stock', $product) }}"
          id="stock-form">
        @csrf
        @method('PATCH')

        <div class="cc-card overflow-hidden mb-6">

            {{-- Header --}}
            <div class="flex items-center justify-between px-5 py-4 border-b" style="border-color:var(--cc-border)">
                <h2 class="font-bold text-white text-sm flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1 12a2 2 0 002 2h8a2 2 0 002-2l1-12M10 12v4m4-4v4"/>
                    </svg>
                    تحديث مخزون المتغيرات
                    <span class="text-xs font-normal" style="color:var(--cc-muted)">
                        {{ $product->variants->count() }} متغير
                    </span>
                </h2>
                <button type="submit" class="cc-btn cc-btn-primary" id="save-stock-btn">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    حفظ التغييرات
                </button>
            </div>

            {{-- Pricing priority legend --}}
            <div class="px-5 py-3 border-b text-[11px] flex items-center gap-4 flex-wrap"
                 style="border-color:var(--cc-border); color:var(--cc-muted)">
                <span>أولوية السعر الفعّال:</span>
                <span class="price-override-badge">تجاوز المتغير</span>
                <span style="color:var(--cc-muted)">›</span>
                <span class="price-discount-badge">سعر الخصم</span>
                <span style="color:var(--cc-muted)">›</span>
                <span>السعر الأساسي</span>
            </div>

            {{-- Variants table --}}
            @php $maxQty = $product->variants->max('stock_quantity') ?: 1; @endphp

            <div class="divide-y" style="border-color:var(--cc-border)">
                @foreach($product->variants as $idx => $variant)
                @php
                    $effectivePrice = $variant->price_override
                        ?? $product->discount_price
                        ?? $product->base_price;

                    $stockPct   = min(100, round(($variant->stock_quantity / $maxQty) * 100));
                    $barColor   = match(true) {
                        $variant->stock_quantity === 0 => '#f43f5e',
                        $variant->stock_quantity <= $lowThreshold => '#f59e0b',
                        default => '#10b981',
                    };
                    $stockBadge = match(true) {
                        $variant->stock_quantity === 0 => 'badge-out',
                        $variant->stock_quantity <= $lowThreshold => 'badge-low',
                        default => 'badge-ok',
                    };
                @endphp

                <div class="variant-row m-3" id="variant-{{ $variant->id }}">
                    <input type="hidden" name="variants[{{ $idx }}][id]" value="{{ $variant->id }}">

                    <div class="flex items-start gap-3 flex-wrap">

                        {{-- Attributes --}}
                        <div class="flex-1 min-w-[160px]">
                            <div class="flex flex-wrap gap-1.5 mb-2">
                                @forelse($variant->attributeValues as $av)
                                <span class="inline-flex items-center gap-1.5 text-[11px] font-bold px-2 py-0.5 rounded-full"
                                      style="background:rgba(255,255,255,.06); color:var(--cc-text)">
                                    @if($av->color_hex)
                                    <span class="w-3 h-3 rounded-full inline-block flex-shrink-0"
                                          style="background:{{ $av->color_hex }}"></span>
                                    @endif
                                    {{ $av->attribute->name }}: {{ $av->value }}
                                </span>
                                @empty
                                <span class="text-xs" style="color:var(--cc-muted)">بدون سمات</span>
                                @endforelse
                            </div>

                            {{-- SKU --}}
                            <p class="price-tag text-[10px]" style="color:var(--cc-muted)">
                                SKU: {{ $variant->sku }}
                            </p>
                        </div>

                        {{-- Stock input + bar --}}
                        <div class="min-w-[120px]">
                            <label class="block text-[10px] font-bold uppercase tracking-widest mb-1.5"
                                   style="color:var(--cc-muted)">الكمية</label>
                            <input type="number"
                                   name="variants[{{ $idx }}][stock_quantity]"
                                   value="{{ $variant->stock_quantity }}"
                                   min="0"
                                   class="cc-input"
                                   onchange="updateBar(this, {{ $idx }}, {{ $maxQty }})">
                            <div class="stock-bar mt-2">
                                <div class="stock-bar-fill"
                                     id="bar-{{ $idx }}"
                                     style="width:{{ $stockPct }}%; background:{{ $barColor }}"></div>
                            </div>
                        </div>

                        {{-- Price override --}}
                        <div class="min-w-[120px]">
                            <label class="block text-[10px] font-bold uppercase tracking-widest mb-1.5"
                                   style="color:var(--cc-muted)">
                                تجاوز السعر
                                <span class="price-override-badge">اختياري</span>
                            </label>
                            <input type="number" step="0.01" min="0"
                                   name="variants[{{ $idx }}][price_override]"
                                   value="{{ $variant->price_override }}"
                                   placeholder="{{ number_format($product->base_price, 2) }}"
                                   class="cc-input">
                        </div>

                        {{-- Effective price display --}}
                        <div class="min-w-[100px] flex flex-col justify-center">
                            <p class="text-[10px] font-bold uppercase tracking-widest mb-1"
                               style="color:var(--cc-muted)">السعر الفعّال</p>
                            <p class="price-tag text-base font-bold
                                      {{ $variant->price_override ? 'text-amber-400' : ($product->discount_price ? 'text-rose-400' : 'text-white') }}">
                                {{ number_format($effectivePrice, 2) }}
                            </p>
                            <p class="text-[9px] mt-0.5" style="color:var(--cc-muted)">
                                {{ $variant->price_override ? 'تجاوز' : ($product->discount_price ? 'خصم' : 'أساسي') }}
                            </p>
                        </div>

                        {{-- Active toggle + status --}}
                        <div class="flex flex-col gap-2 items-end">
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $stockBadge }}">
                                {{ $variant->stock_quantity }} قطعة
                            </span>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <span class="text-[11px]" style="color:var(--cc-muted)">نشط</span>
                                <input type="hidden"
                                       name="variants[{{ $idx }}][is_active]" value="0">
                                <input type="checkbox"
                                       name="variants[{{ $idx }}][is_active]"
                                       value="1"
                                       {{ $variant->is_active ? 'checked' : '' }}
                                       class="w-4 h-4 rounded accent-emerald-500">
                            </label>
                        </div>

                    </div>
                </div>
                @endforeach
            </div>

            {{-- Bottom save --}}
            <div class="px-5 py-4 border-t flex justify-end" style="border-color:var(--cc-border)">
                <button type="submit" class="cc-btn cc-btn-primary">
                    حفظ جميع التغييرات
                </button>
            </div>
        </div>
    </form>

</div>
@endsection

@push('scripts')
<script>
function updateBar(input, idx, maxQty) {
    const bar   = document.getElementById('bar-' + idx);
    if (!bar) return;
    const val   = parseInt(input.value) || 0;
    const pct   = maxQty > 0 ? Math.min(100, Math.round((val / maxQty) * 100)) : 0;
    bar.style.width = pct + '%';
    bar.style.background = val === 0 ? '#f43f5e' : val <= 5 ? '#f59e0b' : '#10b981';
}

document.getElementById('stock-form')?.addEventListener('submit', function () {
    const btn = document.getElementById('save-stock-btn');
    btn.innerHTML = `<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg> جاري الحفظ...`;
    btn.disabled = true;
});
</script>
@endpush