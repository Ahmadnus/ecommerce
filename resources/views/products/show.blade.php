{{-- resources/views/products/show.blade.php --}}

@extends('layouts.app')
@section('title', $product->name)

@push('head')
<style>
/* ─── Variant buttons ──────────────────────────────────────────────────── */
.variant-btn {
    padding: 7px 16px;
    font-size: 12px; font-weight: 700;
    border: 1.5px solid #e5e7eb;
    border-radius: 10px;
    background: #fff; color: #374151;
    cursor: pointer; transition: all .15s;
    line-height: 1;
}
.variant-btn:hover                { border-color: var(--brand-color); color: var(--brand-color); }
.variant-btn.selected             { border-color: var(--brand-color); background: var(--brand-color); color: #fff; }
.variant-btn.unavailable          { opacity: .38; cursor: not-allowed; text-decoration: line-through; }

/* ─── Color swatches ───────────────────────────────────────────────────── */
.color-swatch {
    width: 32px; height: 32px; border-radius: 50%;
    border: 2px solid transparent; cursor: pointer;
    transition: transform .15s, box-shadow .15s;
}
.color-swatch:hover   { transform: scale(1.12); }
.color-swatch.selected {
    box-shadow: 0 0 0 2px #fff, 0 0 0 4px var(--brand-color);
}

/* ─── Per-attribute error state ────────────────────────────────────────── */
.attr-block                               { transition: all .2s; }
.attr-block.has-error .attr-label         { color: #ef4444; }
.attr-block.has-error .attr-options       {
    outline: 1.5px solid #fca5a5;
    border-radius: 10px; padding: 6px;
}
@keyframes shake {
    0%,100% { transform: translateX(0); }
    20%,60% { transform: translateX(-5px); }
    40%,80% { transform: translateX(5px); }
}
.attr-block.has-error { animation: shake .35s ease; }
.attr-error-hint {
    font-size: 11px; font-weight: 600; color: #ef4444;
    display: none; align-items: center; gap: 4px; margin-top: 5px;
}
.attr-block.has-error .attr-error-hint { display: flex; }

/* ─── Global add-to-cart error banner ─────────────────────────────────── */
#cart-error-banner {
    display: none;
    align-items: center; gap: 10px;
    background: #fff1f2; border: 1px solid #fecdd3;
    border-radius: 12px; padding: 12px 16px;
    font-size: 13px; font-weight: 600; color: #be123c;
}
#cart-error-banner.visible { display: flex; }

/* ─── Gallery ──────────────────────────────────────────────────────────── */
.main-img-wrap { overflow: hidden; }
.main-img-wrap img { transition: transform .4s ease; }
.main-img-wrap:hover img { transform: scale(1.04); }
.thumb-btn { transition: border-color .15s, transform .15s; }
.thumb-btn:hover { transform: scale(1.04); }

/* ─── Price ────────────────────────────────────────────────────────────── */
.price-sale   { color: #ff3366; }
.price-normal { color: #111827; }

/* ─── Shimmer ──────────────────────────────────────────────────────────── */
@keyframes shimmer {
    0%   { background-position: -900px 0; }
    100% { background-position:  900px 0; }
}
.shimmer {
    background: linear-gradient(90deg, #f4f4f4 25%, #ebebeb 50%, #f4f4f4 75%);
    background-size: 1800px 100%;
    animation: shimmer 1.8s ease-in-out infinite;
}

/* ─── Dynamic features grid ────────────────────────────────────────────── */
/*
   Keeps the same grid-cols-3 layout the static version had.
   Each cell matches the app's design system (white bg, brand icon bubble,
   consistent border-radius, gray typography scale).
*/
.features-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0;
    border: 1px solid #f3f4f6;
    border-radius: 16px;
    overflow: hidden;
    background: #fff;
}
.feature-cell {
    display: flex; flex-direction: column; align-items: center;
    justify-content: flex-start;
    padding: 16px 10px;
    text-align: center;
    border-right: 1px solid #f3f4f6;
}
.feature-cell:last-child { border-right: none; }

/* RTL: reverse the border direction */
[dir="rtl"] .feature-cell             { border-right: none; border-left: 1px solid #f3f4f6; }
[dir="rtl"] .feature-cell:last-child  { border-left: none; }

.feature-icon-wrap {
    width: 40px; height: 40px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; margin-bottom: 8px;
    background: color-mix(in srgb, var(--brand-color, #0ea5e9) 10%, #fff);
}
</style>
@endpush

@section('content')

{{-- Floating WhatsApp --}}
@php
    $floatingLink = \App\Models\SocialLink::where('is_active', true)
                        ->where('is_floating', true)->first();
@endphp
@if($floatingLink)
    <x-floating-button :number="$floatingLink->whatsapp_number" />
@endif
    @yield('content')
@include('partials.bottombar')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10" dir="rtl">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Breadcrumb ──────────────────────────────────────────────────────── --}}
    <nav class="flex items-center gap-1.5 text-sm text-gray-500 mb-8 flex-wrap">
        <a href="{{ route('products.index') }}" class="hover:text-gray-800 transition-colors">المتجر</a>

        @foreach($product->categories->first()?->getAncestors() ?? collect() as $ancestor)
        <svg class="w-3.5 h-3.5 rotate-180 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <a href="{{ route('products.index', ['category' => $ancestor->slug]) }}"
           class="hover:text-gray-800 transition-colors">{{ $ancestor->name }}</a>
        @endforeach

        @if($product->categories->first())
        <svg class="w-3.5 h-3.5 rotate-180 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <a href="{{ route('products.index', ['category' => $product->categories->first()->slug]) }}"
           class="hover:text-gray-800 transition-colors">{{ $product->categories->first()->name }}</a>
        @endif

        <svg class="w-3.5 h-3.5 rotate-180 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-gray-900 font-medium">{{ Str::limit($product->name, 40) }}</span>
    </nav>

    {{-- ═══ PRODUCT LAYOUT ════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 mb-20">

        {{-- ── Gallery ────────────────────────────────────────────────── --}}
        <div>
            {{-- Main image: aspect-square + object-cover, same as index cards --}}
            <div class="aspect-square rounded-2xl overflow-hidden bg-gray-100
                        border border-gray-100 main-img-wrap mb-3 relative">
                <div class="shimmer absolute inset-0 z-0" id="main-img-shimmer"></div>
                @php
                    $mainImage = $product->getFirstMediaUrl('products')
                        ?: ($product->image_url
                            ?? 'https://picsum.photos/seed/'.$product->id.'/700/700');
                @endphp
                <img id="main-image"
                     src="{{ $mainImage }}"
                     alt="{{ $product->name }}"
                     class="w-full h-full object-cover relative z-10 transition-opacity duration-300"
                     onload="document.getElementById('main-img-shimmer').style.display='none'">
            </div>

            {{-- Thumbnails --}}
            @php
                $allImages = collect();
                foreach ($product->getMedia('products') as $m) { $allImages->push($m->getUrl()); }
                if ($allImages->isEmpty()) {
                    if ($product->image_url) $allImages->push($product->image_url);
                    foreach ($product->image_urls ?? [] as $u) { $allImages->push($u); }
                }
                foreach ($product->variants->whereNotNull('image_url') as $v) {
                    if (!$allImages->contains($v->image_url)) $allImages->push($v->image_url);
                }
            @endphp
            @if($allImages->count() > 1)
            <div class="flex gap-2 overflow-x-auto pb-1">
                @foreach($allImages as $idx => $imgUrl)
                <button type="button"
                        onclick="switchImage('{{ $imgUrl }}', this)"
                        class="thumb-btn flex-shrink-0 w-16 h-16 rounded-xl overflow-hidden border-2
                               {{ $idx === 0 ? 'border-[var(--brand-color,#0ea5e9)]' : 'border-transparent' }}">
                    <img src="{{ $imgUrl }}" class="w-full h-full object-cover" loading="lazy" alt="">
                </button>
                @endforeach
            </div>
            @endif
        </div>

        {{-- ── Product Info ────────────────────────────────────────────── --}}
        <div class="flex flex-col">

            {{-- Category + badges --}}
            <div class="flex items-center gap-2 mb-3 flex-wrap">
                @if($product->categories->first())
                <a href="{{ route('products.index', ['category' => $product->categories->first()->slug]) }}"
                   class="text-sm font-bold uppercase tracking-wide"
                   style="color:var(--brand-color,#0ea5e9)">
                    {{ $product->categories->first()->name }}
                </a>
                @endif

                @if($product->is_on_sale)
                <span class="bg-red-100 text-red-700 text-xs font-bold px-2.5 py-0.5 rounded-full">
                    {{ $product->discount_percentage }}% خصم
                </span>
                @endif

                @if($product->is_featured)
                <span class="bg-amber-100 text-amber-700 text-xs font-bold px-2.5 py-0.5 rounded-full">
                    ⭐ مميز
                </span>
                @endif
            </div>

            <h1 class="font-display text-3xl md:text-4xl font-bold text-gray-900 leading-tight mb-4">
                {{ $product->name }}
            </h1>

            {{-- Price — identical logic & classes to index card --}}
            <div class="flex flex-col mb-5" id="price-wrapper">
                @if($product->is_on_sale)
                <span class="text-xs text-red-500 font-bold bg-red-50 px-1.5 py-0.5 rounded w-fit mb-2">
                    تخفيض
                </span>
                <div class="flex items-end gap-3 flex-wrap">
                    <span id="price-current"
                          class="text-4xl font-black leading-none tabular-nums price-sale">
                        <x-price :amount="$product->discount_price" />
                    </span>
                    <span class="text-xl text-gray-400 line-through mb-1 tabular-nums">
                        <x-price :amount="$product->base_price" />
                    </span>
                    @php
                        $rate    = (float)($activeCurrency->exchange_rate ?? 1);
                        $sym     = $activeCurrency->symbol ?? 'د.أ';
                        $savings = round(($product->base_price - $product->discount_price) * $rate, 2);
                    @endphp
                    <span class="text-sm text-red-500 font-semibold mb-1">
                        وفّر {{ number_format($savings, 2) }} {{ $sym }}
                    </span>
                </div>
                @else
                <span id="price-current"
                      class="text-4xl font-black leading-none tabular-nums price-normal">
                    <x-price :amount="$product->base_price" />
                </span>
                @endif
            </div>

            {{-- Stock indicator --}}
            <div id="stock-status" class="flex items-center gap-2 mb-5">
                @if($product->in_stock)
                <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 flex-shrink-0"></div>
                <span class="text-sm text-emerald-700 font-medium">
                    متوفر ({{ $product->total_stock }} قطعة)
                </span>
                @else
                <div class="w-2.5 h-2.5 rounded-full bg-red-400 flex-shrink-0"></div>
                <span class="text-sm text-red-600 font-medium">نفد المخزون</span>
                @endif
            </div>

            @if($product->description)
            <p class="text-gray-600 leading-relaxed mb-6 text-sm border-b border-gray-100 pb-6">
                {{ $product->description }}
            </p>
            @endif

      
            @if($product->variants->isNotEmpty())

            @php
                /*
                 * Pre-encode in a @php block to avoid Blade parser issues
                 * when arrow functions appear inside {!! !!} tags.
                 */
                $variantsJson = json_encode(
                    $product->variants->map(function ($v) {
                        return [
                            'id'               => $v->id,
                            'sku'              => $v->sku,
                            'price'            => (float) $v->effective_price,
                            'stock'            => (int)   $v->stock_quantity,
                            'is_active'        => (bool)  $v->is_active,
                            'image_url'        => $v->image_url,
                            'attribute_values' => $v->attributeValues->pluck('id')->toArray(),
                        ];
                    })->values()->all()
                );

                /*
                 * Build the array of attribute IDs that MUST be selected.
                 * Drives client-side validation — fully dynamic.
                 */
                $requiredAttrIds = collect($variantAttributes)
                    ->map(fn($values) => $values->first()->attribute_id)
                    ->values()
                    ->toArray();
            @endphp

            <script>
                window.VARIANTS        = {!! $variantsJson !!};
                window.BASE_PRICE      = {{ (float) $product->effective_price }};
                window.SEL_AVS         = {};                     // { attrId: avId }
                window.REQUIRED_ATTRS  = {!! json_encode($requiredAttrIds) !!};
                window.CURRENCY_RATE   = {{ (float) ($activeCurrency->exchange_rate ?? 1) }};
                window.CURRENCY_SYMBOL = '{{ $activeCurrency->symbol ?? 'د.أ' }}';
            </script>

            <div class="space-y-5 mb-4" id="variant-selectors">
                @foreach($variantAttributes as $attrName => $values)
                @php
                    $attrId  = $values->first()->attribute_id;
                    $isColor = $values->first()->attribute->type === 'color';
                @endphp

         
                <div class="attr-block" data-attr-id="{{ $attrId }}" id="attr-block-{{ $attrId }}">

                    <p class="attr-label text-sm font-semibold text-gray-700 mb-2 flex items-center gap-1">
                        {{ $attrName }}
                        <span class="text-red-400 text-xs">*</span>
                        <span id="sel-label-{{ $attrId }}"
                              class="font-normal text-gray-400 text-xs"></span>
                    </p>

                    <div class="attr-options flex flex-wrap gap-2">
                        @foreach($values->sortBy('sort_order') as $av)
                        @if($isColor && $av->color_hex)
                        <button type="button"
                                class="color-swatch"
                                style="background:{{ $av->color_hex }}"
                                data-av="{{ $av->id }}"
                                data-attr="{{ $attrId }}"
                                data-label="{{ $av->display_label }}"
                                onclick="selectOption(this)"
                                title="{{ $av->display_label }}">
                        </button>
                        @else
                        <button type="button"
                                class="variant-btn"
                                data-av="{{ $av->id }}"
                                data-attr="{{ $attrId }}"
                                data-label="{{ $av->display_label }}"
                                onclick="selectOption(this)">
                            {{ $av->display_label }}
                        </button>
                        @endif
                        @endforeach
                    </div>

                    {{-- Per-attribute inline error hint --}}
                    <p class="attr-error-hint" id="hint-{{ $attrId }}">
                        <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                  d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                  clip-rule="evenodd"/>
                        </svg>
                        يرجى اختيار {{ $attrName }}
                    </p>
                </div>
                @endforeach
            </div>

            {{-- Global validation banner (shown when CTA tapped without full selection) --}}
            <div id="cart-error-banner" role="alert">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                          d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                          clip-rule="evenodd"/>
                </svg>
                <span id="cart-error-text">يرجى اختيار جميع الخصائص المطلوبة</span>
            </div>

            <p id="variant-sku" class="text-xs text-gray-400 mt-2 mb-4 font-mono"></p>

            @endif {{-- /variants --}}

            {{-- ── Qty + CTA ───────────────────────────────────────────── --}}
            @if($product->in_stock)
            <div class="flex items-center gap-3 mb-6 mt-2">
                {{-- Qty stepper --}}
                <div class="flex items-center border border-gray-200 rounded-xl overflow-hidden flex-shrink-0">
                    <button type="button" onclick="adjustQty(-1)"
                            class="w-10 h-12 flex items-center justify-center text-gray-600
                                   hover:bg-gray-50 transition-colors text-xl select-none">−</button>
                    <input id="qty-input" type="number" value="1"
                           min="1" max="{{ $product->total_stock }}"
                           class="w-12 h-12 text-center border-x border-gray-200
                                  text-sm font-bold focus:outline-none">
                    <button type="button" onclick="adjustQty(1)"
                            class="w-10 h-12 flex items-center justify-center text-gray-600
                                   hover:bg-gray-50 transition-colors text-xl select-none">+</button>
                </div>

                {{-- Add to cart --}}
                <button id="add-to-cart-btn"
                        type="button"
                        onclick="addToCart()"
                        class="flex-1 text-white font-bold px-6 py-3 rounded-xl
                               transition-all flex items-center justify-center gap-2 text-sm
                               hover:opacity-90 active:scale-[.97]"
                        style="background: var(--brand-color, #0ea5e9)">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    أضف إلى السلة
                </button>
            </div>
            @else
            <div class="bg-gray-100 text-gray-500 text-center py-4 rounded-xl mb-6 font-medium text-sm">
                هذا المنتج غير متوفر حالياً
            </div>
            @endif

            {{-- ════════════════════════════════════════════════════════════
                 DYNAMIC SITE FEATURES
                 ─────────────────────────────────────────────────────────
                 Replaces the old static 3-column trust-badge block.
                 Data comes from the `site_features` table via SiteFeature model.
                 Layout stays grid-cols-3 — matching the previous static version.
                 Admin can add/remove/reorder badges from the dashboard.
            ════════════════════════════════════════════════════════════ --}}
            @php
                $siteFeatures = \App\Models\SiteFeature::active()->get();
            @endphp

            @if($siteFeatures->isNotEmpty())
            <div class="features-grid mt-1">
                @foreach($siteFeatures as $feat)
                <div class="feature-cell">
                    <div class="feature-icon-wrap">{{ $feat->icon }}</div>
                    <p class="text-xs font-bold text-gray-800 leading-tight">{{ $feat->title }}</p>
                    @if($feat->description)
                    <p class="text-[10px] text-gray-400 mt-0.5 leading-tight">{{ $feat->description }}</p>
                    @endif
                </div>
                @endforeach
            </div>
            @endif

            @if($product->sku)
            <p class="text-xs text-gray-400 mt-4">
                كود المنتج: <span class="font-mono">{{ $product->sku }}</span>
            </p>
            @endif
        </div>
    </div>

    {{-- Related products ─────────────────────────────────────────────────── --}}
    @if($related->isNotEmpty())
    <section>
        <div class="flex items-center gap-2 mb-6">
            <span class="w-1 h-5 rounded-full" style="background:var(--brand-color,#0ea5e9)"></span>
            <h2 class="font-display text-2xl font-bold text-gray-900">قد يعجبك أيضاً</h2>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($related as $rel)
            @php
                $relImg = $rel->getFirstMediaUrl('products')
                    ?: ($rel->image_url ?? 'https://picsum.photos/seed/'.$rel->id.'/400/400');
            @endphp
            <a href="{{ route('products.show', $rel->slug) }}"
               class="bg-white rounded-2xl overflow-hidden border border-gray-100 group
                      transition-all duration-200 hover:-translate-y-1 hover:shadow-lg">
                <div class="aspect-square overflow-hidden bg-gray-100">
                    <img src="{{ $relImg }}" alt="{{ $rel->name }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                         loading="lazy">
                </div>
                <div class="p-3">
                    <p class="text-xs font-semibold text-gray-800 line-clamp-2 leading-snug mb-1">
                        {{ $rel->name }}
                    </p>
                    @if($rel->is_on_sale)
                    <div class="flex items-baseline gap-1.5">
                        <x-price :amount="$rel->discount_price" class="text-sm font-black tabular-nums price-sale" />
                        <x-price :amount="$rel->base_price"    class="text-[10px] text-gray-400 line-through tabular-nums" />
                    </div>
                    @else
                    <x-price :amount="$rel->base_price" class="text-sm font-black tabular-nums price-normal" />
                    @endif
                </div>
            </a>
            @endforeach
        </div>
    </section>
    @endif
<script>
console.log("TEST");
function selectOption(){console.log('ok');}
function addToCart(){console.log('ok');}
function adjustQty(){console.log('ok');}
</script>
</div>
@endsection
@push('scripts')
<script>
var MAX_QTY = {{ (int) $product->total_stock }};
var selectedVariant = null;

window.REQUIRED_ATTRS = (window.REQUIRED_ATTRS || []).map(String);
window.SEL_AVS = {};

/* ───────────── Qty ───────────── */
function adjustQty(delta) {
    var input = document.getElementById('qty-input');
    var maxQty = selectedVariant ? selectedVariant.stock : MAX_QTY;

    var current = parseInt(input.value || 1);
    input.value = Math.max(1, Math.min(maxQty, current + delta));
}

/* ───────────── Gallery ───────────── */
function switchImage(src, btn) {
    var main = document.getElementById('main-image');

    main.style.opacity = '0';
    setTimeout(function () {
        main.src = src;
        main.style.opacity = '1';
    }, 120);

    document.querySelectorAll('.thumb-btn').forEach(function (b) {
        b.style.borderColor = (b === btn)
            ? 'var(--brand-color,#0ea5e9)'
            : 'transparent';
    });
}

/* ───────────── Select Option ───────────── */
function selectOption(btn) {
    var attrId = String(btn.dataset.attr);
    var avId = Number(btn.dataset.av);

    btn.closest('[data-attr-id]')
        .querySelectorAll('[data-attr="' + attrId + '"]')
        .forEach(function (b) {
            b.classList.remove('selected');
        });

    btn.classList.add('selected');

    var labelEl = document.getElementById('sel-label-' + attrId);
    if (labelEl) labelEl.textContent = '— ' + btn.dataset.label;

    window.SEL_AVS[attrId] = avId;

    clearAttrError(attrId);
    resolveVariant();
}

/* ───────────── Resolve Variant ───────────── */
function resolveVariant() {
    var selectedIds = Object.values(window.SEL_AVS).map(Number);
    if (!selectedIds.length) return;

    var match = (window.VARIANTS || []).find(function (v) {
        return v.is_active &&
            selectedIds.every(function (id) {
                return v.attribute_values.indexOf(id) !== -1;
            });
    }) || null;

    selectedVariant = match;

    var priceEl = document.getElementById('price-current');
    if (priceEl) {
        var raw = match ? match.price : window.BASE_PRICE;
        var converted = Math.round(raw * (window.CURRENCY_RATE || 1) * 100) / 100;

        priceEl.textContent =
            new Intl.NumberFormat('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(converted) +
            ' ' + (window.CURRENCY_SYMBOL || 'د.أ');
    }

    var stockEl = document.getElementById('stock-status');
    if (stockEl && match) {
        stockEl.innerHTML = match.stock > 0
            ? '<div class="w-2.5 h-2.5 rounded-full bg-emerald-500"></div>' +
              '<span class="text-sm text-emerald-700 font-medium">متوفر (' + match.stock + ')</span>'
            : '<div class="w-2.5 h-2.5 rounded-full bg-red-400"></div>' +
              '<span class="text-sm text-red-600 font-medium">نفد المخزون</span>';
    }

    var skuEl = document.getElementById('variant-sku');
    if (skuEl) skuEl.textContent = match ? 'كود: ' + match.sku : '';

    var qtyInput = document.getElementById('qty-input');
    if (qtyInput && match) {
        qtyInput.max = match.stock;
        qtyInput.value = Math.min(parseInt(qtyInput.value || 1), match.stock || 1);
    }

    var cartBtn = document.getElementById('add-to-cart-btn');
    if (cartBtn) {
        var disabled = match && match.stock <= 0;

        cartBtn.disabled = disabled;
        cartBtn.style.opacity = disabled ? '0.4' : '1';
        cartBtn.style.cursor = disabled ? 'not-allowed' : 'pointer';
    }

    if (match && match.image_url) {
        switchImage(match.image_url, null);
    }
}

/* ───────────── Errors ───────────── */
function clearAttrError(attrId) {
    var el = document.getElementById('attr-block-' + attrId);
    if (el) el.classList.remove('has-error');
}

function markAttrError(attrId) {
    var el = document.getElementById('attr-block-' + attrId);
    if (!el) return;

    el.classList.remove('has-error');
    void el.offsetWidth;
    el.classList.add('has-error');
}

function validateSelections() {
    var required = (window.REQUIRED_ATTRS || []).map(String);
    if (!required.length) return true;

    var missingCount = 0;

    required.forEach(function (attrId) {
        if (typeof window.SEL_AVS[attrId] === 'undefined') {
            markAttrError(attrId);
            missingCount++;
        } else {
            clearAttrError(attrId);
        }
    });

    var banner = document.getElementById('cart-error-banner');
    var errText = document.getElementById('cart-error-text');

    if (missingCount > 0) {
        if (errText) {
            errText.textContent =
                'يرجى اختيار جميع الخصائص المطلوبة — ' + missingCount + ' خصائص ناقصة';
        }
        if (banner) banner.classList.add('visible');
        return false;
    }

    if (banner) banner.classList.remove('visible');
    return true;
}

/* ───────────── Add to cart ───────────── */
function validateSelections() {
    var required = (window.REQUIRED_ATTRS || []).map(String);
    var missingCount = 0;

    required.forEach(function (attrId) {
        if (!window.SEL_AVS[attrId]) {
            var block = document.getElementById('attr-block-' + attrId);
            if (block) block.classList.add('has-error');
            missingCount++;
        }
    });

    var banner = document.getElementById('cart-error-banner');
    if (missingCount > 0) {
        if (banner) banner.classList.add('visible');
        return false;
    }
    if (banner) banner.classList.remove('visible');
    return true;
}

function addToCart() {
    // 1. التحقق من الاختيارات قبل الإرسال
    if (!validateSelections()) return;

    if (!selectedVariant || !selectedVariant.id) {
        Swal.fire({ icon: 'error', title: 'عذراً', text: 'هذا الخيار غير متوفر حالياً' });
        return;
    }

    var btn = document.getElementById('add-to-cart-btn');
    var qty = parseInt(document.getElementById('qty-input').value) || 1;
    
    // حفظ المحتوى الأصلي للزر
    var originalContent = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="animate-spin">🔄</span> جاري الإضافة...';

    // الإرسال للسيرفر
    fetch("{{ route('cart.add') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': "{{ csrf_token() }}",
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            product_id: "{{ $product->id }}",
            variant_id: selectedVariant.id,
            quantity: qty
        })
    })
    .then(async response => {
        const data = await response.json();
        if (!response.ok) {
            // معالجة خطأ 422 بالتحديد
            if (response.status === 422) {
                throw new Error(data.message || "يرجى التأكد من الاختيارات");
            }
            throw new Error("حدث خطأ في السيرفر");
        }
        return data;
    })
   .then(data => {
    // 1. تحديث سلة Livewire إذا كانت موجودة
    if(window.Livewire) {
        Livewire.dispatch('cartUpdated');
    }

    // 2. إظهار إشعار نجاح (Toast) جذاب
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    Toast.fire({
        icon: 'success',
        title: 'تمت إضافة المنتج للسلة بنجاح'
    });
})
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({ icon: 'warning', title: 'تنبيه', text: error.message });
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalContent;
    });
}
</script>
@endpush