{{-- resources/views/products/show.blade.php --}}
@extends('layouts.app')
@section('title', $product->name)

{{-- ══════════════════════════════════════════════════════════════════════════
     ALL STYLES IN @push('head') — per layout constraint
══════════════════════════════════════════════════════════════════════════ --}}
@push('head')
<style>
/* ─── Shimmer skeleton ─────────────────────────────────────────────────── */
@keyframes sk-sweep {
    0%   { background-position: -800px 0; }
    100% { background-position:  800px 0; }
}
.sk {
    background: linear-gradient(90deg, #f0f0ef 25%, #e4e3e1 50%, #f0f0ef 75%);
    background-size: 1600px 100%;
    animation: sk-sweep 1.6s ease-in-out infinite;
    border-radius: 4px;
}

/* ─── Gallery: main slider ─────────────────────────────────────────────── */
.gallery-track {
    display: flex;
    transition: transform 0.38s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    will-change: transform;
    touch-action: pan-y;            /* allow vertical scroll, intercept horizontal */
}
.gallery-slide {
    flex-shrink: 0;
    width: 100%;
}
.gallery-slide img {
    width: 100%; height: 100%;
    object-fit: cover;
    display: block;
    transition: opacity .25s ease;
}

/* ─── Thumbnail strip ──────────────────────────────────────────────────── */
.thumb-strip {
    display: flex;
    gap: 8px;
    overflow-x: auto;
    scrollbar-width: none;
    scroll-snap-type: x mandatory;
    -webkit-overflow-scrolling: touch;
}
.thumb-strip::-webkit-scrollbar { display: none; }
.thumb-item {
    flex-shrink: 0;
    width: 60px; height: 60px;
    border-radius: 10px;
    overflow: hidden;
    cursor: pointer;
    scroll-snap-align: start;
    border: 2px solid transparent;
    transition: border-color .15s, transform .15s;
}
.thumb-item:hover { transform: scale(1.06); }
.thumb-item.active { border-color: var(--brand-color, #0ea5e9); }
.thumb-item img { width: 100%; height: 100%; object-fit: cover; display: block; }

/* ─── Dot indicators ───────────────────────────────────────────────────── */
.gallery-dot {
    width: 6px; height: 6px;
    border-radius: 50%;
    background: rgba(0,0,0,.2);
    transition: all .2s;
    cursor: pointer;
    flex-shrink: 0;
}
.gallery-dot.active {
    background: var(--brand-color, #0ea5e9);
    width: 18px;
    border-radius: 99px;
}

/* ─── Zoom lightbox ────────────────────────────────────────────────────── */
.lightbox-overlay {
    position: fixed; inset: 0; z-index: 9999;
    background: rgba(0,0,0,.92);
    display: flex; align-items: center; justify-content: center;
    opacity: 0; pointer-events: none;
    transition: opacity .2s;
}
.lightbox-overlay.open { opacity: 1; pointer-events: auto; }
.lightbox-img {
    max-width: 92vw; max-height: 88vh;
    object-fit: contain;
    border-radius: 8px;
    transition: transform .2s;
}
.lightbox-nav {
    position: absolute; top: 50%; transform: translateY(-50%);
    width: 44px; height: 44px;
    background: rgba(255,255,255,.12);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    transition: background .15s;
    color: #fff;
    border: none;
}
.lightbox-nav:hover { background: rgba(255,255,255,.22); }

/* ─── Variant selectors ────────────────────────────────────────────────── */
.vbtn {
    padding: 8px 18px;
    font-size: 12px; font-weight: 700;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    background: #fff; color: #374151;
    cursor: pointer;
    transition: all .15s;
    line-height: 1;
    white-space: nowrap;
}
.vbtn:hover    { border-color: var(--brand-color, #0ea5e9); color: var(--brand-color, #0ea5e9); }
.vbtn.selected { border-color: var(--brand-color, #0ea5e9); background: var(--brand-color, #0ea5e9); color: #fff; }
.vbtn.unavailable { opacity: .35; cursor: not-allowed; text-decoration: line-through; }

/* ─── Color swatches ───────────────────────────────────────────────────── */
.cswatch {
    width: 34px; height: 34px; border-radius: 50%;
    border: 2px solid transparent;
    cursor: pointer;
    transition: transform .15s, box-shadow .15s;
}
.cswatch:hover   { transform: scale(1.1); }
.cswatch.selected { box-shadow: 0 0 0 2px #fff, 0 0 0 4px var(--brand-color, #0ea5e9); }

/* ─── Attr error shake ─────────────────────────────────────────────────── */
@keyframes shake {
    0%,100% { transform: translateX(0); }
    20%,60% { transform: translateX(-5px); }
    40%,80% { transform: translateX(5px); }
}
.attr-block.has-error  { animation: shake .35s ease; }
.attr-block.has-error .attr-label { color: #ef4444; }
.attr-block.has-error .attr-options {
    outline: 1.5px solid #fca5a5;
    border-radius: 10px; padding: 6px;
}
.attr-error-hint {
    font-size: 11px; font-weight: 600; color: #ef4444;
    display: none; align-items: center; gap: 4px; margin-top: 5px;
}
.attr-block.has-error .attr-error-hint { display: flex; }

/* ─── Error banner ─────────────────────────────────────────────────────── */
#cart-error-banner {
    display: none;
    align-items: center; gap: 10px;
    background: #fff1f2; border: 1px solid #fecdd3;
    border-radius: 12px; padding: 12px 16px;
    font-size: 13px; font-weight: 600; color: #be123c;
}
#cart-error-banner.visible { display: flex; }

/* ─── Section entrance ─────────────────────────────────────────────────── */
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(16px); }
    to   { opacity: 1; transform: translateY(0); }
}
.fade-up { animation: fadeUp .45s cubic-bezier(.16,1,.3,1) both; }
.fade-up-d1 { animation-delay: .05s; }
.fade-up-d2 { animation-delay: .10s; }
.fade-up-d3 { animation-delay: .15s; }
.fade-up-d4 { animation-delay: .20s; }
.fade-up-d5 { animation-delay: .25s; }

/* ─── Features strip ───────────────────────────────────────────────────── */
.features-strip {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    border: 1px solid #f0ede8;
    border-radius: 16px;
    overflow: hidden;
    background: #fff;
}
.feature-cell {
    display: flex; flex-direction: column; align-items: center;
    justify-content: flex-start;
    padding: 14px 8px; text-align: center;
    border-left: 1px solid #f0ede8;
}
.feature-cell:last-child { border-left: none; }
[dir="rtl"] .feature-cell { border-left: none; border-right: 1px solid #f0ede8; }
[dir="rtl"] .feature-cell:last-child { border-right: none; }

/* ─── Qty stepper ──────────────────────────────────────────────────────── */
.qty-btn {
    width: 38px; height: 38px;
    display: flex; align-items: center; justify-content: center;
    border: 1.5px solid #e2e8f0; border-radius: 10px;
    font-size: 18px; color: #6b7280; background: #fff;
    cursor: pointer; transition: all .15s; flex-shrink: 0;
    line-height: 1; user-select: none;
}
.qty-btn:hover { border-color: var(--brand-color, #0ea5e9); color: var(--brand-color, #0ea5e9); }

/* ─── Sticky buy bar (mobile) ──────────────────────────────────────────── */
.sticky-buy-bar {
    position: fixed; bottom: 0; left: 0; right: 0;
    background: rgba(255,255,255,.96);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border-top: 1px solid #f0ede8;
    padding: 12px 16px calc(env(safe-area-inset-bottom, 0px) + 12px);
    z-index: 40;
    display: flex; gap: 10px; align-items: center;
}
@media (min-width: 1024px) { .sticky-buy-bar { display: none; } }
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

@include('partials.bottombar')

{{-- ═══════════════════════════════════════════════════════════════════════
     LIGHTBOX OVERLAY (outside main container so it overlays everything)
═══════════════════════════════════════════════════════════════════════ --}}
<div id="lightbox" class="lightbox-overlay" onclick="closeLightbox(event)">
    <button class="lightbox-nav" style="left:16px" onclick="lbPrev(event)" aria-label="السابق">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </button>
    <img id="lightbox-img" src="" alt="" class="lightbox-img">
    <button class="lightbox-nav" style="right:16px" onclick="lbNext(event)" aria-label="التالي">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
    </button>
    <button onclick="closeLightbox(event, true)"
            style="position:absolute;top:16px;right:16px;"
            class="lightbox-nav" aria-label="إغلاق">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
</div>

{{-- ════════════════════════════════════════════════════════════════════════
     PAGE
════════════════════════════════════════════════════════════════════════ --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12 pb-28 lg:pb-12" dir="rtl">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-1.5 text-xs text-gray-400 mb-8 flex-wrap fade-up">
        <a href="{{ route('products.index') }}" class="hover:text-gray-700 transition-colors">المتجر</a>
        @foreach($product->categories->first()?->getAncestors() ?? collect() as $ancestor)
        <svg class="w-3 h-3 rotate-180 flex-shrink-0 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <a href="{{ route('products.index', ['category' => $ancestor->slug]) }}"
           class="hover:text-gray-700 transition-colors">{{ $ancestor->name }}</a>
        @endforeach
        @if($product->categories->first())
        <svg class="w-3 h-3 rotate-180 flex-shrink-0 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <a href="{{ route('products.index', ['category' => $product->categories->first()->slug]) }}"
           class="hover:text-gray-700 transition-colors">{{ $product->categories->first()->name }}</a>
        @endif
        <svg class="w-3 h-3 rotate-180 flex-shrink-0 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-gray-700 font-medium">{{ Str::limit($product->name, 40) }}</span>
    </nav>

    {{-- ═══ MAIN PRODUCT LAYOUT ════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 xl:gap-16 mb-20">

        {{-- ── GALLERY COLUMN ──────────────────────────────────────────── --}}
        @php
            $allImages = collect();
            foreach ($product->getMedia('products') as $m) {
                $allImages->push($m->getUrl());
            }
            // Fallback chain when no Spatie media
            if ($allImages->isEmpty()) {
                if ($product->image_url) $allImages->push($product->image_url);
                foreach ($product->image_urls ?? [] as $u) { $allImages->push($u); }
            }
            // Variant images
            foreach ($product->variants->whereNotNull('image_url') as $v) {
                if (!$allImages->contains($v->image_url)) $allImages->push($v->image_url);
            }
            // Absolute fallback
            if ($allImages->isEmpty()) {
                $allImages->push('https://picsum.photos/seed/' . $product->id . '/800/800');
            }
            $imagesJson = $allImages->values()->toJson();
        @endphp

        <div class="fade-up fade-up-d1" x-data="gallery({{ $imagesJson }})" x-init="init()">

            {{-- ── Main slide area ─────────────────────────────────────── --}}
            <div class="relative overflow-hidden rounded-2xl bg-gray-100 border border-gray-100"
                 style="aspect-ratio:1/1;"
                 @touchstart="touchStart($event)"
                 @touchmove.prevent="touchMove($event)"
                 @touchend="touchEnd($event)">

                {{-- Skeleton overlay — hides once first image loads --}}
                <div id="gallery-skeleton"
                     class="sk absolute inset-0 z-10 rounded-2xl"
                     style="display:block"></div>

                {{-- Slides --}}
                <div class="gallery-track absolute inset-0"
                     :style="`transform: translateX(${trackOffset}px)`">
                    <template x-for="(img, i) in images" :key="i">
                        <div class="gallery-slide"
                             :style="`width:${sliderWidth}px; height:${sliderWidth}px`">
                            <img :src="img"
                                 :alt="'{{ $product->name }} — صورة ' + (i+1)"
                                 class="w-full h-full object-cover cursor-zoom-in"
                                 loading="lazy"
                                 @load="onImgLoad(i)"
                                 @click="openLightbox(i)">
                        </div>
                    </template>
                </div>

                {{-- Prev/Next arrows (desktop) --}}
                <template x-if="images.length > 1">
                    <div>
                        <button @click="prev()"
                                class="absolute right-3 top-1/2 -translate-y-1/2 z-20
                                       hidden lg:flex w-9 h-9 items-center justify-center
                                       bg-white/90 rounded-full shadow-md
                                       hover:bg-white transition-all">
                            <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                        <button @click="next()"
                                class="absolute left-3 top-1/2 -translate-y-1/2 z-20
                                       hidden lg:flex w-9 h-9 items-center justify-center
                                       bg-white/90 rounded-full shadow-md
                                       hover:bg-white transition-all">
                            <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                    </div>
                </template>

                {{-- Dot indicators (mobile) --}}
                <template x-if="images.length > 1">
                    <div class="absolute bottom-3 left-0 right-0 flex justify-center gap-1.5 z-20 lg:hidden">
                        <template x-for="(img, i) in images" :key="i">
                            <button @click="goTo(i)"
                                    :class="current === i ? 'active' : ''"
                                    class="gallery-dot"></button>
                        </template>
                    </div>
                </template>

                {{-- Image counter badge (desktop) --}}
                <template x-if="images.length > 1">
                    <span class="absolute top-3 left-3 z-20 hidden lg:inline-flex
                                 items-center gap-1 bg-black/40 text-white
                                 text-[10px] font-bold px-2.5 py-1 rounded-full
                                 backdrop-blur-sm">
                        <span x-text="current + 1"></span>
                        <span class="opacity-60">/</span>
                        <span x-text="images.length"></span>
                    </span>
                </template>

                {{-- Badges (sale / featured) --}}
                <div class="absolute top-3 right-3 z-20 flex flex-col gap-1.5">
                    @if($product->is_on_sale)
                    <span class="text-[10px] font-black px-2.5 py-1 rounded-full bg-red-500 text-white leading-tight">
                        {{ $product->discount_percentage }}% OFF
                    </span>
                    @endif
                    @if($product->is_featured)
                    <span class="text-[10px] font-black px-2.5 py-1 rounded-full bg-amber-400 text-amber-900 leading-tight">
                        ⭐ مميز
                    </span>
                    @endif
                </div>
            </div>

            {{-- ── Thumbnail strip ────────────────────────────────────── --}}
            <template x-if="images.length > 1">
                <div class="thumb-strip mt-3" id="thumb-strip">
                    <template x-for="(img, i) in images" :key="i">
                        <div class="thumb-item"
                             :class="current === i ? 'active' : ''"
                             @click="goTo(i)">
                            <img :src="img" :alt="'thumb-' + i" loading="lazy">
                        </div>
                    </template>
                </div>
            </template>

        </div>
        {{-- /gallery --}}

        {{-- ── PRODUCT INFO COLUMN ─────────────────────────────────────── --}}
        <div class="flex flex-col">

            {{-- Category + badges --}}
            <div class="flex items-center gap-2 mb-3 flex-wrap fade-up fade-up-d2">
                @if($product->categories->first())
                <a href="{{ route('products.index', ['category' => $product->categories->first()->slug]) }}"
                   class="text-xs font-black uppercase tracking-widest"
                   style="color:var(--brand-color,#0ea5e9)">
                    {{ $product->categories->first()->name }}
                </a>
                @endif
            </div>

            {{-- Product name --}}
            <h1 class="font-display text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900
                        leading-tight mb-5 fade-up fade-up-d2">
                {{ $product->name }}
            </h1>

            {{-- ── Price block ──────────────────────────────────────────── --}}
            <div class="mb-5 fade-up fade-up-d3" id="price-wrapper">
                @if($product->is_on_sale)
                <div class="flex items-end gap-3 flex-wrap">
                    <span id="price-current"
                          class="text-4xl font-black leading-none tabular-nums text-red-500">
                        <x-price :amount="$product->discount_price" />
                    </span>
                    <span class="text-xl text-gray-300 line-through mb-0.5 tabular-nums">
                        <x-price :amount="$product->base_price" />
                    </span>
                    @php
                        $rate    = (float)($activeCurrency->exchange_rate ?? 1);
                        $sym     = $activeCurrency->symbol ?? 'د.أ';
                        $savings = round(($product->base_price - $product->discount_price) * $rate, 2);
                    @endphp
                    <span class="text-sm text-red-500 font-semibold mb-0.5">
                        وفّر {{ number_format($savings, 2) }} {{ $sym }}
                    </span>
                </div>
                @else
                <span id="price-current"
                      class="text-4xl font-black leading-none tabular-nums text-gray-900">
                    <x-price :amount="$product->base_price" />
                </span>
                @endif
            </div>

            {{-- ── Stock status ─────────────────────────────────────────── --}}
            <div id="stock-status" class="flex items-center gap-2 mb-6 fade-up fade-up-d3">
                @if($product->in_stock)
                <span class="relative flex h-2.5 w-2.5 flex-shrink-0">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                </span>
                <span class="text-sm text-emerald-700 font-semibold">
                    متوفر — {{ $product->total_stock }} قطعة
                </span>
                @else
                <span class="w-2.5 h-2.5 rounded-full bg-red-400 flex-shrink-0"></span>
                <span class="text-sm text-red-600 font-semibold">نفد المخزون</span>
                @endif
            </div>

            {{-- ── Description ──────────────────────────────────────────── --}}
            @if($product->description)
            <p class="text-gray-500 leading-relaxed text-sm mb-6
                       border-t border-b border-gray-100 py-5 fade-up fade-up-d3">
                {{ $product->description }}
            </p>
            @endif

            {{-- ── Variant selectors ────────────────────────────────────── --}}
            @if($product->variants->isNotEmpty())
            @php
                $variantsJson = json_encode(
                    $product->variants->map(fn($v) => [
                        'id'               => $v->id,
                        'sku'              => $v->sku,
                        'price'            => (float) $v->effective_price,
                        'stock'            => (int)   $v->stock_quantity,
                        'is_active'        => (bool)  $v->is_active,
                        'image_url'        => $v->image_url,
                        'attribute_values' => $v->attributeValues->pluck('id')->toArray(),
                    ])->values()->all()
                );
                $requiredAttrIds = collect($variantAttributes)
                    ->map(fn($values) => $values->first()->attribute_id)
                    ->values()->toArray();
            @endphp

            <script>
                window.VARIANTS       = {!! $variantsJson !!};
                window.BASE_PRICE     = {{ (float) $product->effective_price }};
                window.SEL_AVS        = {};
                window.REQUIRED_ATTRS = {!! json_encode($requiredAttrIds) !!};
                window.CURRENCY_RATE   = {{ (float) ($activeCurrency->exchange_rate ?? 1) }};
                window.CURRENCY_SYMBOL = '{{ $activeCurrency->symbol ?? 'د.أ' }}';
            </script>

            <div class="space-y-5 mb-5 fade-up fade-up-d4" id="variant-selectors">
                @foreach($variantAttributes as $attrName => $values)
                @php
                    $attrId  = $values->first()->attribute_id;
                    $isColor = $values->first()->attribute->type === 'color';
                @endphp
                <div class="attr-block" data-attr-id="{{ $attrId }}" id="attr-block-{{ $attrId }}">
                    <p class="attr-label text-xs font-black text-gray-500 uppercase tracking-widest mb-2.5 flex items-center gap-1.5">
                        {{ $attrName }}
                        <span class="text-red-400">*</span>
                        <span id="sel-label-{{ $attrId }}" class="font-semibold text-gray-400 normal-case tracking-normal text-xs"></span>
                    </p>
                    <div class="attr-options flex flex-wrap gap-2">
                        @foreach($values->sortBy('sort_order') as $av)
                        @if($isColor && $av->color_hex)
                        <button type="button" class="cswatch"
                                style="background:{{ $av->color_hex }}"
                                data-av="{{ $av->id }}"
                                data-attr="{{ $attrId }}"
                                data-label="{{ $av->display_label }}"
                                onclick="selectOption(this)"
                                title="{{ $av->display_label }}"></button>
                        @else
                        <button type="button" class="vbtn"
                                data-av="{{ $av->id }}"
                                data-attr="{{ $attrId }}"
                                data-label="{{ $av->display_label }}"
                                onclick="selectOption(this)">
                            {{ $av->display_label }}
                        </button>
                        @endif
                        @endforeach
                    </div>
                    <p class="attr-error-hint" id="hint-{{ $attrId }}">
                        <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        يرجى اختيار {{ $attrName }}
                    </p>
                </div>
                @endforeach
            </div>

            <div id="cart-error-banner" role="alert" class="mb-3">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <span id="cart-error-text">يرجى اختيار جميع الخصائص المطلوبة</span>
            </div>

            <p id="variant-sku" class="text-xs text-gray-400 font-mono mb-3"></p>
            @endif

            {{-- ── Qty + CTA (desktop) ─────────────────────────────────── --}}
            @if($product->in_stock)
            <div class="flex items-center gap-3 mb-6 fade-up fade-up-d5">
                {{-- Qty stepper --}}
                <div class="flex items-center gap-1 border border-gray-200 rounded-xl overflow-hidden p-1 bg-gray-50">
                    <button type="button" class="qty-btn" onclick="adjustQty(-1)">−</button>
                    <input id="qty-input" type="number" value="1"
                           min="1" max="{{ $product->total_stock }}"
                           class="w-11 text-center text-sm font-bold bg-transparent border-none
                                  focus:outline-none tabular-nums text-gray-900">
                    <button type="button" class="qty-btn" onclick="adjustQty(1)">+</button>
                </div>

                {{-- Add to cart (desktop) --}}
                <button id="add-to-cart-btn"
                        type="button"
                        onclick="addToCart()"
                        class="flex-1 text-white font-bold px-6 py-3.5 rounded-xl
                               transition-all flex items-center justify-center gap-2 text-sm
                               hover:opacity-90 active:scale-[.97] shadow-lg shadow-black/10"
                        style="background:var(--brand-color,#0ea5e9)">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    أضف إلى السلة
                </button>
            </div>
            @else
            <div class="bg-gray-100 text-gray-500 text-center py-4 rounded-xl mb-6 text-sm font-semibold fade-up fade-up-d5">
                هذا المنتج غير متوفر حالياً
            </div>
            @endif

            {{-- ── Site features strip ──────────────────────────────────── --}}
            @php $siteFeatures = \App\Models\SiteFeature::active()->get(); @endphp
            @if($siteFeatures->isNotEmpty())
            <div class="features-strip mt-1 fade-up fade-up-d5">
                @foreach($siteFeatures as $feat)
                <div class="feature-cell">
                    <div class="text-2xl mb-1.5">{{ $feat->icon }}</div>
                    <p class="text-[10px] font-bold text-gray-700 leading-tight">{{ $feat->title }}</p>
                    @if($feat->description)
                    <p class="text-[9px] text-gray-400 mt-0.5 leading-tight">{{ $feat->description }}</p>
                    @endif
                </div>
                @endforeach
            </div>
            @endif

            @if($product->sku)
            <p class="text-[10px] text-gray-400 mt-4 font-mono">
                كود المنتج: {{ $product->sku }}
            </p>
            @endif
        </div>
        {{-- /info --}}
    </div>

    {{-- ── Related products ────────────────────────────────────────────── --}}
    @if($related->isNotEmpty())
    <section class="mb-16">
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
                      hover:-translate-y-1 hover:shadow-lg transition-all duration-200">
                <div class="aspect-square overflow-hidden bg-gray-100">
                    <img src="{{ $relImg }}" alt="{{ $rel->name }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                         loading="lazy">
                </div>
                <div class="p-3">
                    <p class="text-xs font-semibold text-gray-800 line-clamp-2 leading-snug mb-1">{{ $rel->name }}</p>
                    @if($rel->is_on_sale)
                    <div class="flex items-baseline gap-1.5">
                        <x-price :amount="$rel->discount_price" class="text-sm font-black tabular-nums text-red-500" />
                        <x-price :amount="$rel->base_price"    class="text-[10px] text-gray-300 line-through tabular-nums" />
                    </div>
                    @else
                    <x-price :amount="$rel->base_price" class="text-sm font-black tabular-nums text-gray-900" />
                    @endif
                </div>
            </a>
            @endforeach
        </div>
    </section>
    @endif

</div>

{{-- ─── Sticky mobile buy bar ───────────────────────────────────────────── --}}
@if($product->in_stock)
<div class="sticky-buy-bar lg:hidden">
    <div class="flex items-center border border-gray-200 rounded-xl overflow-hidden bg-gray-50 p-0.5">
        <button type="button" class="qty-btn" onclick="adjustQty(-1)">−</button>
        <input id="qty-input-mob" type="number" value="1" min="1"
               max="{{ $product->total_stock }}"
               class="w-10 text-center text-sm font-bold bg-transparent border-none focus:outline-none tabular-nums">
        <button type="button" class="qty-btn" onclick="adjustQty(1)">+</button>
    </div>
    <button type="button" onclick="addToCart(true)"
            class="flex-1 text-white font-bold py-3.5 rounded-xl text-sm
                   flex items-center justify-center gap-2
                   hover:opacity-90 active:scale-[.97] shadow-md shadow-black/10"
            style="background:var(--brand-color,#0ea5e9)">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        أضف إلى السلة
    </button>
</div>
@endif

@endsection

{{-- ══════════════════════════════════════════════════════════════════════════
     ALL SCRIPTS IN @push('scripts') — per layout constraint
══════════════════════════════════════════════════════════════════════════ --}}
@push('scripts')
{{-- SweetAlert2 (only on product page) --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>

<script>
/* ═══════════════════════════════════════════════════════════════════════════
   ALPINE.JS: gallery() component
   ─────────────────────────────────────────────────────────────────────────
   • Touch/swipe on mobile
   • Dot + thumbnail navigation
   • Skeleton reveal on first image load
   • Updates the lightbox
═══════════════════════════════════════════════════════════════════════════ */
document.addEventListener('alpine:init', function () {
    Alpine.data('gallery', function (images) {
        return {
            images,
            current:     0,
            sliderWidth: 0,
            trackOffset: 0,
            touchStartX: 0,
            touchDeltaX: 0,
            loadedImages: new Set(),

            get totalImages() { return this.images.length; },

            init() {
                this.measure();
                const ro = new ResizeObserver(() => this.measure());
                ro.observe(this.$el);
            },

            measure() {
                this.sliderWidth = this.$el.offsetWidth;
                this.trackOffset = -this.current * this.sliderWidth;
            },

            goTo(index) {
                this.current = Math.max(0, Math.min(index, this.images.length - 1));
                this.trackOffset = -this.current * this.sliderWidth;
                this.scrollThumbIntoView();
                window._galleryIndex = this.current;
            },

            prev() { this.goTo(this.current > 0 ? this.current - 1 : this.images.length - 1); },
            next() { this.goTo(this.current < this.images.length - 1 ? this.current + 1 : 0); },

            touchStart(e) {
                this.touchStartX = e.changedTouches[0].clientX;
                this.touchDeltaX = 0;
            },
            touchMove(e) {
                this.touchDeltaX = e.changedTouches[0].clientX - this.touchStartX;
            },
            touchEnd() {
                if (Math.abs(this.touchDeltaX) > 40) {
                    this.touchDeltaX < 0 ? this.next() : this.prev();
                }
                this.touchDeltaX = 0;
            },

            onImgLoad(index) {
                this.loadedImages.add(index);
                if (index === 0) {
                    const sk = document.getElementById('gallery-skeleton');
                    if (sk) sk.style.display = 'none';
                }
            },

            openLightbox(index) {
                openLightbox(this.images, index);
            },

            scrollThumbIntoView() {
                const strip = document.getElementById('thumb-strip');
                if (!strip) return;
                const thumb = strip.children[this.current];
                if (thumb) thumb.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
            },
        };
    });
});

/* ═══════════════════════════════════════════════════════════════════════════
   LIGHTBOX
═══════════════════════════════════════════════════════════════════════════ */
var _lbImages = [];
var _lbIndex  = 0;

function openLightbox(images, index) {
    _lbImages = images;
    _lbIndex  = index;
    document.getElementById('lightbox-img').src = _lbImages[_lbIndex];
    document.getElementById('lightbox').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeLightbox(event, force) {
    if (force || event.target.id === 'lightbox') {
        document.getElementById('lightbox').classList.remove('open');
        document.body.style.overflow = '';
    }
}

function lbPrev(event) {
    event.stopPropagation();
    _lbIndex = (_lbIndex > 0) ? _lbIndex - 1 : _lbImages.length - 1;
    document.getElementById('lightbox-img').src = _lbImages[_lbIndex];
}

function lbNext(event) {
    event.stopPropagation();
    _lbIndex = (_lbIndex < _lbImages.length - 1) ? _lbIndex + 1 : 0;
    document.getElementById('lightbox-img').src = _lbImages[_lbIndex];
}

document.addEventListener('keydown', function (e) {
    const lb = document.getElementById('lightbox');
    if (!lb.classList.contains('open')) return;
    if (e.key === 'Escape')     closeLightbox(e, true);
    if (e.key === 'ArrowLeft')  lbNext(e);
    if (e.key === 'ArrowRight') lbPrev(e);
});

/* ═══════════════════════════════════════════════════════════════════════════
   VARIANT LOGIC
═══════════════════════════════════════════════════════════════════════════ */
var MAX_QTY       = {{ (int) $product->total_stock }};
var selectedVariant = null;

window.REQUIRED_ATTRS = (window.REQUIRED_ATTRS || []).map(String);
window.SEL_AVS = {};

function adjustQty(delta) {
    ['qty-input', 'qty-input-mob'].forEach(function (id) {
        var input = document.getElementById(id);
        if (!input) return;
        var maxQty  = selectedVariant ? selectedVariant.stock : MAX_QTY;
        var current = parseInt(input.value || 1);
        input.value = Math.max(1, Math.min(maxQty, current + delta));
    });
    // Sync both inputs
    var main = document.getElementById('qty-input');
    var mob  = document.getElementById('qty-input-mob');
    if (main && mob) mob.value = main.value;
}

function selectOption(btn) {
    var attrId = String(btn.dataset.attr);
    var avId   = Number(btn.dataset.av);

    btn.closest('[data-attr-id]')
       .querySelectorAll('[data-attr="' + attrId + '"]')
       .forEach(function (b) { b.classList.remove('selected'); });

    btn.classList.add('selected');

    var labelEl = document.getElementById('sel-label-' + attrId);
    if (labelEl) labelEl.textContent = '— ' + btn.dataset.label;

    window.SEL_AVS[attrId] = avId;
    clearAttrError(attrId);
    resolveVariant();
}

function resolveVariant() {
    var selectedIds = Object.values(window.SEL_AVS).map(Number);
    if (!selectedIds.length) return;

    var match = (window.VARIANTS || []).find(function (v) {
        return v.is_active &&
            selectedIds.every(function (id) { return v.attribute_values.indexOf(id) !== -1; });
    }) || null;

    selectedVariant = match;

    // Update price
    var priceEl = document.getElementById('price-current');
    if (priceEl) {
        var raw       = match ? match.price : window.BASE_PRICE;
        var converted = Math.round(raw * (window.CURRENCY_RATE || 1) * 100) / 100;
        priceEl.textContent = new Intl.NumberFormat('en-US', {
            minimumFractionDigits: 2, maximumFractionDigits: 2,
        }).format(converted) + ' ' + (window.CURRENCY_SYMBOL || 'د.أ');
    }

    // Update stock
    var stockEl = document.getElementById('stock-status');
    if (stockEl && match) {
        stockEl.innerHTML = match.stock > 0
            ? '<span class="relative flex h-2.5 w-2.5 flex-shrink-0"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span></span><span class="text-sm text-emerald-700 font-semibold">متوفر — ' + match.stock + ' قطعة</span>'
            : '<span class="w-2.5 h-2.5 rounded-full bg-red-400 flex-shrink-0"></span><span class="text-sm text-red-600 font-semibold">نفد المخزون</span>';
    }

    // SKU
    var skuEl = document.getElementById('variant-sku');
    if (skuEl) skuEl.textContent = match ? 'كود: ' + match.sku : '';

    // Max qty
    ['qty-input', 'qty-input-mob'].forEach(function (id) {
        var inp = document.getElementById(id);
        if (inp && match) {
            inp.max   = match.stock;
            inp.value = Math.min(parseInt(inp.value || 1), match.stock || 1);
        }
    });

    // CTA state
    ['add-to-cart-btn'].forEach(function (id) {
        var btn = document.getElementById(id);
        if (btn) {
            var disabled = match && match.stock <= 0;
            btn.disabled       = disabled;
            btn.style.opacity  = disabled ? '0.4' : '1';
            btn.style.cursor   = disabled ? 'not-allowed' : 'pointer';
        }
    });

    // Switch gallery to variant image if available
    if (match && match.image_url) {
        // Find the index in the gallery's images array and navigate to it
        var imgs = window._galleryImages || [];
        var idx  = imgs.indexOf(match.image_url);
        if (idx > -1) {
            // Dispatch a custom event that Alpine listens to
            document.dispatchEvent(new CustomEvent('gallery-goto', { detail: { index: idx } }));
        }
    }
}

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
    var required     = (window.REQUIRED_ATTRS || []).map(String);
    var missingCount = 0;

    required.forEach(function (attrId) {
        if (!window.SEL_AVS[attrId]) {
            markAttrError(attrId);
            missingCount++;
        } else {
            clearAttrError(attrId);
        }
    });

    var banner = document.getElementById('cart-error-banner');
    var errTxt = document.getElementById('cart-error-text');

    if (missingCount > 0) {
        if (errTxt) errTxt.textContent = 'يرجى اختيار جميع الخصائص المطلوبة';
        if (banner) banner.classList.add('visible');
        return false;
    }

    if (banner) banner.classList.remove('visible');
    return true;
}

/* ═══════════════════════════════════════════════════════════════════════════
   ADD TO CART
═══════════════════════════════════════════════════════════════════════════ */
function addToCart(isMobile) {
    if (!validateSelections()) return;

    if (window.REQUIRED_ATTRS.length > 0 && (!selectedVariant || !selectedVariant.id)) {
        return;
    }

    var qtyId = isMobile ? 'qty-input-mob' : 'qty-input';
    // Fallback: try both
    var qtyInput = document.getElementById(qtyId) || document.getElementById('qty-input');
    var qty      = parseInt(qtyInput ? qtyInput.value : 1) || 1;

    var btn          = document.getElementById('add-to-cart-btn');
    var originalHTML = btn ? btn.innerHTML : '';

    if (btn) {
        btn.disabled  = true;
        btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg> جاري الإضافة...';
    }

    fetch('{{ route('cart.add') }}', {
        method:  'POST',
        headers: {
            'Content-Type':  'application/json',
            'X-CSRF-TOKEN':  '{{ csrf_token() }}',
            'Accept':        'application/json',
        },
        body: JSON.stringify({
            product_id: '{{ $product->id }}',
            variant_id: selectedVariant ? selectedVariant.id : null,
            quantity:   qty,
        }),
    })
    .then(async function (res) {
        var data = await res.json();
        if (!res.ok) throw new Error(data.message || 'حدث خطأ');
        return data;
    })
    .then(function (data) {
        // Update cart badge
        if (typeof Cart !== 'undefined' && Cart.updateBadge) {
            Cart.updateBadge(data.item_count);
        }
        if (window.Livewire) Livewire.dispatch('cartUpdated');

        // SweetAlert2 toast
        Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2800,
            timerProgressBar: true,
        }).fire({ icon: 'success', title: 'تمت الإضافة إلى السلة ✓' });
    })
    .catch(function (err) {
        Swal.fire({ icon: 'warning', title: 'تنبيه', text: err.message, confirmButtonColor: 'var(--brand-color,#0ea5e9)' });
    })
    .finally(function () {
        if (btn) { btn.disabled = false; btn.innerHTML = originalHTML; }
    });
}

/* Listen for Alpine gallery-goto event */
document.addEventListener('gallery-goto', function (e) {
    // Alpine component will handle this via window._galleryGoTo if needed
    window._galleryGoTo = e.detail.index;
});
</script>
@endpush