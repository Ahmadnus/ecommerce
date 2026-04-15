{{-- resources/views/products/show.blade.php --}}

@extends('layouts.app')
@section('title', $product->name)

@push('head')
<style>
    /* ── Variant buttons ─────────────────────────────────────────── */
    .variant-btn {
        padding: 6px 14px;
        font-size: 12px; font-weight: 600;
        border: 1.5px solid #e5e7eb;
        border-radius: 10px;
        background: #fff; color: #374151;
        cursor: pointer;
        transition: all .15s;
    }
    .variant-btn:hover       { border-color: var(--brand-color); color: var(--brand-color); }
    .variant-btn.active      { border-color: var(--brand-color); background: var(--brand-color); color: #fff; }
    .variant-btn.unavailable { opacity: .4; cursor: not-allowed; text-decoration: line-through; }

    /* ── Color swatches ──────────────────────────────────────────── */
    .color-swatch {
        width: 30px; height: 30px;
        border-radius: 50%;
        border: 2px solid transparent;
        cursor: pointer;
        transition: transform .15s, box-shadow .15s;
    }
    .color-swatch:hover  { transform: scale(1.15); }
    .color-swatch.active { box-shadow: 0 0 0 2px #fff, 0 0 0 4px var(--brand-color); }

    /* ── Gallery thumbnail hover ─────────────────────────────────── */
    .thumb-btn { transition: border-color .15s, transform .15s; }
    .thumb-btn:hover { transform: scale(1.04); }

    /* ── Main image zoom-on-hover ────────────────────────────────── */
    .main-img-wrap { overflow: hidden; }
    .main-img-wrap img { transition: transform .4s ease; }
    .main-img-wrap:hover img { transform: scale(1.04); }

    /* ── Price consistency with index cards ──────────────────────── */
    .price-sale   { color: var(--sale-red, #ff3366); }
    .price-normal { color: #111827; }

    /* ── Shimmer (skeleton while loading) ───────────────────────── */
    @keyframes shimmer {
        0%   { background-position: -900px 0; }
        100% { background-position:  900px 0; }
    }
    .shimmer {
        background: linear-gradient(90deg, #f4f4f4 25%, #ececec 50%, #f4f4f4 75%);
        background-size: 1800px 100%;
        animation: shimmer 1.8s ease-in-out infinite;
    }
</style>
@endpush

@section('content')

{{-- ── Floating WhatsApp (mobile only) ─────────────────────────────── --}}
@php
    $floatingLink = \App\Models\SocialLink::where('is_active', true)
                        ->where('is_floating', true)
                        ->first();
@endphp
@if($floatingLink)
    <x-floating-button :number="$floatingLink->whatsapp_number" />
@endif

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10" dir="rtl">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-1.5 text-sm text-gray-500 mb-8 flex-wrap">
        <a href="{{ route('products.index') }}" class="hover:text-brand-600 transition-colors">المتجر</a>

        @foreach($product->categories->first()?->getAncestors() ?? collect() as $ancestor)
            <svg class="w-3.5 h-3.5 rotate-180 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <a href="{{ route('products.index', ['category' => $ancestor->slug]) }}"
               class="hover:text-brand-600 transition-colors">{{ $ancestor->name }}</a>
        @endforeach

        @if($product->categories->first())
            <svg class="w-3.5 h-3.5 rotate-180 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <a href="{{ route('products.index', ['category' => $product->categories->first()->slug]) }}"
               class="hover:text-brand-600 transition-colors">{{ $product->categories->first()->name }}</a>
        @endif

        <svg class="w-3.5 h-3.5 rotate-180 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-gray-900 font-medium">{{ Str::limit($product->name, 40) }}</span>
    </nav>

    {{-- ══ PRODUCT DETAIL ═══════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 mb-20">

        {{-- ── Image Gallery ─────────────────────────────────────────────── --}}
        <div>
            {{-- ────────────────────────────────────────────────────────────
                 MAIN IMAGE
                 Aspect ratio  : aspect-square   (matches index card ratio)
                 Fit            : object-cover    (same as .pcard-img)
                 Border         : same subtle border as index cards
                 Radius         : rounded-2xl     (matches pcard border-radius-card)
            ──────────────────────────────────────────────────────────────── --}}
            <div class="aspect-square rounded-2xl overflow-hidden
                        bg-gray-100 border border-gray-100
                        main-img-wrap mb-3 relative"
                 id="main-img-container">

                {{-- Shimmer while first image loads --}}
                <div class="shimmer absolute inset-0 z-0" id="main-img-shimmer"></div>

                {{-- ── Spatie image with fallback chain (same as index) ── --}}
                @php
                    $mainImage = $product->getFirstMediaUrl('products')
                        ?: ($product->image_url
                            ?? 'https://picsum.photos/seed/' . $product->id . '/700/700');
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
                // First: Spatie media collection
                foreach ($product->getMedia('products') as $m) {
                    $allImages->push($m->getUrl());
                }
                // Fallback: legacy image_url / image_urls fields
                if ($allImages->isEmpty()) {
                    if ($product->image_url) $allImages->push($product->image_url);
                    foreach ($product->image_urls ?? [] as $url) { $allImages->push($url); }
                }
                // Variant images
                foreach ($product->variants->whereNotNull('variant_image') as $v) {
                    if ($v->image_url && !$allImages->contains($v->image_url)) {
                        $allImages->push($v->image_url);
                    }
                }
            @endphp

            @if($allImages->count() > 1)
            <div class="flex gap-2 overflow-x-auto pb-1">
                @foreach($allImages as $idx => $imgUrl)
                <button type="button"
                        onclick="switchImage('{{ $imgUrl }}', this)"
                        class="thumb-btn flex-shrink-0 w-16 h-16 rounded-xl overflow-hidden border-2
                               {{ $idx === 0 ? 'border-[var(--brand-color,#0ea5e9)]' : 'border-transparent hover:border-[var(--brand-color,#0ea5e9)]/50' }}">
                    <img src="{{ $imgUrl }}"
                         class="w-full h-full object-cover"
                         loading="lazy" alt="">
                </button>
                @endforeach
            </div>
            @endif
        </div>

        {{-- ── Product Info ──────────────────────────────────────────────── --}}
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

            {{-- ────────────────────────────────────────────────────────────
                 PRICE DISPLAY — perfectly synced with index card logic:
                   • Discount price in sale-red (--sale-red / red-500)
                   • Original price struck through in gray-400
                   • Normal price in gray-900 font-black
                   • Currency via x-price component (same as index)
                   • "تخفيض" badge same styling as index cards
            ──────────────────────────────────────────────────────────────── --}}
            <div class="flex flex-col mb-5" id="price-wrapper">

                @if($product->is_on_sale)

                {{-- Sale badge — identical to index card --}}
                <span class="text-xs text-red-500 font-bold bg-red-50 px-1.5 py-0.5 rounded w-fit mb-2">
                    تخفيض
                </span>

                <div class="flex items-end gap-3 flex-wrap">
                    {{-- Discounted price --}}
                    <span id="price-current"
                          class="text-4xl font-black leading-none tabular-nums price-sale">
                        <x-price :amount="$product->discount_price" class="" />
                    </span>

                    {{-- Original price struck through --}}
                    <span class="text-xl text-gray-400 line-through mb-1 tabular-nums">
                        <x-price :amount="$product->base_price" class="" />
                    </span>

                    {{-- Savings badge --}}
                    <span class="text-sm text-red-500 font-semibold mb-1">
                        @php
                            $cur      = $activeCurrency ?? null;
                            $rate     = $cur ? (float) $cur->exchange_rate : 1;
                            $sym      = $cur ? $cur->symbol : '$';
                            $savings  = round(($product->base_price - $product->discount_price) * $rate, 2);
                        @endphp
                        وفّر {{ number_format($savings, 2) }} {{ $sym }}
                    </span>
                </div>

                @else

                {{-- Normal price --}}
                <span id="price-current"
                      class="text-4xl font-black leading-none tabular-nums price-normal">
                    <x-price :amount="$product->base_price" class="" />
                </span>

                @endif
            </div>

            {{-- Stock indicator --}}
            <div id="stock-status" class="flex items-center gap-2 mb-5">
                @if($product->in_stock)
                    <div class="w-2.5 h-2.5 rounded-full bg-green-500 flex-shrink-0"></div>
                    <span class="text-sm text-green-700 font-medium">
                        متوفر ({{ $product->total_stock }} قطعة)
                    </span>
                @else
                    <div class="w-2.5 h-2.5 rounded-full bg-red-500 flex-shrink-0"></div>
                    <span class="text-sm text-red-600 font-medium">نفد المخزون</span>
                @endif
            </div>

            <p class="text-gray-600 leading-relaxed mb-6 text-sm border-b border-gray-100 pb-6">
                {{ $product->description }}
            </p>

            {{-- Variant selectors --}}
            @if($product->variants->isNotEmpty())
            @php
                $variantsJson = $product->variants->map(fn($v) => [
                    'id'               => $v->id,
                    'sku'              => $v->sku,
                    'price'            => (float) $v->effective_price,
                    'stock'            => $v->stock_quantity,
                    'is_active'        => $v->is_active,
                    'image_url'        => $v->image_url,
                    'attribute_values' => $v->attributeValues->pluck('id')->toArray(),
                ]);
            @endphp
            <script>
                window.VARIANTS    = @json($variantsJson);
                window.BASE_PRICE  = {{ (float) $product->effective_price }};
                window.SEL_AVS     = {};
                window.CURRENCY_RATE   = {{ (float) ($activeCurrency->exchange_rate ?? 1) }};
                window.CURRENCY_SYMBOL = '{{ $activeCurrency->symbol ?? '$' }}';
            </script>

            <div class="space-y-4 mb-6">
                @foreach($variantAttributes as $attrName => $values)
                @php
                    $attrId  = $values->first()->attribute_id;
                    $isColor = $values->first()->attribute->type === 'color';
                @endphp
                <div data-attribute="{{ $attrId }}">
                    <p class="text-sm font-semibold text-gray-700 mb-2">
                        {{ $attrName }}:
                        <span id="sel-{{ $attrId }}" class="font-normal text-gray-500"></span>
                    </p>
                    <div class="flex flex-wrap gap-2">
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
                </div>
                @endforeach
                <p id="variant-sku" class="text-xs text-gray-400 mt-1"></p>
            </div>
            @endif

            {{-- Qty + Add to Cart --}}
            @if($product->in_stock)
            <div class="flex items-center gap-4 mb-6">
                <div class="flex items-center border border-gray-200 rounded-xl overflow-hidden">
                    <button type="button" onclick="adjustQty(-1)"
                            class="w-10 h-12 flex items-center justify-center text-gray-600 hover:bg-gray-50 transition-colors text-xl">
                        −
                    </button>
                    <input id="qty-input" type="number" value="1"
                           min="1" max="{{ $product->total_stock }}"
                           class="w-14 h-12 text-center border-x border-gray-200 text-sm font-semibold focus:outline-none">
                    <button type="button" onclick="adjustQty(1)"
                            class="w-10 h-12 flex items-center justify-center text-gray-600 hover:bg-gray-50 transition-colors text-xl">
                        +
                    </button>
                </div>

                <button id="add-to-cart-btn"
                        type="button"
                        onclick="addToCart()"
                        class="flex-1 text-white font-semibold px-6 py-3 rounded-xl
                               transition-colors flex items-center justify-center gap-2 text-sm
                               hover:opacity-90 active:scale-95"
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

            {{-- Trust badges --}}
            <div class="grid grid-cols-3 gap-3 py-5 border-t border-gray-100 text-center">
                <div class="text-xs text-gray-500"><div class="text-xl mb-1">🚚</div>شحن مجاني<br>فوق $50</div>
                <div class="text-xs text-gray-500"><div class="text-xl mb-1">↩️</div>إرجاع خلال<br>30 يوماً</div>
                <div class="text-xs text-gray-500"><div class="text-xl mb-1">🔒</div>دفع آمن<br>ومشفر</div>
            </div>

            @if($product->sku)
            <p class="text-xs text-gray-400 mt-1">كود المنتج: {{ $product->sku }}</p>
            @endif
        </div>
    </div>

    {{-- Related products --}}
    @if($related->isNotEmpty())
    <section>
        <h2 class="font-display text-2xl font-bold text-gray-900 mb-6">قد يعجبك أيضاً</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($related as $rel)
            @php
                $relImg = $rel->getFirstMediaUrl('products')
                    ?: ($rel->image_url ?? 'https://picsum.photos/seed/' . $rel->id . '/400/400');
            @endphp
            <a href="{{ route('products.show', $rel->slug) }}"
               class="bg-white rounded-2xl overflow-hidden border border-gray-100 group
                      transition-all duration-200 hover:-translate-y-1 hover:shadow-lg">
                <div class="aspect-square overflow-hidden bg-gray-100">
                    <img src="{{ $relImg }}"
                         alt="{{ $rel->name }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                         loading="lazy">
                </div>
                <div class="p-3">
                    <p class="text-xs font-semibold text-gray-800 line-clamp-2 leading-snug mb-1">
                        {{ $rel->name }}
                    </p>
                    {{-- Price synced with index using x-price --}}
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

</div>
@endsection

@push('scripts')
<script>
const MAX_QTY = {{ $product->total_stock }};
let selectedVariant = null;

/* ── Qty ───────────────────────────────────────────────────────── */
function adjustQty(delta) {
    const input  = document.getElementById('qty-input');
    const maxQty = selectedVariant ? selectedVariant.stock : MAX_QTY;
    input.value  = Math.max(1, Math.min(maxQty, parseInt(input.value || 1) + delta));
}

/* ── Gallery ────────────────────────────────────────────────────── */
function switchImage(src, btn) {
    const main = document.getElementById('main-image');
    main.style.opacity = '0';
    setTimeout(() => { main.src = src; main.style.opacity = '1'; }, 150);

    document.querySelectorAll('.thumb-btn').forEach(b => {
        const active = b === btn;
        b.style.borderColor = active ? 'var(--brand-color, #0ea5e9)' : 'transparent';
    });
}

/* ── Variant selection ──────────────────────────────────────────── */
function selectOption(btn) {
    const attrId = btn.dataset.attr;
    const avId   = parseInt(btn.dataset.av);

    btn.closest('[data-attribute]')
       .querySelectorAll('[data-attr="' + attrId + '"]')
       .forEach(b => b.classList.remove('active'));

    btn.classList.add('active');

    const labelEl = document.getElementById('sel-' + attrId);
    if (labelEl) labelEl.textContent = btn.dataset.label;

    window.SEL_AVS[attrId] = avId;
    resolveVariant();
}

function resolveVariant() {
    const selectedIds = Object.values(window.SEL_AVS).map(Number);
    if (!selectedIds.length) return;

    const match = (window.VARIANTS ?? []).find(v =>
        v.is_active && selectedIds.every(id => v.attribute_values.includes(id))
    ) || null;

    selectedVariant = match;

    /* ── Update price (uses same currency rate as middleware) ─── */
    const priceEl = document.getElementById('price-current');
    if (priceEl) {
        const rawPrice = match ? match.price : window.BASE_PRICE;
        const converted = Math.round(rawPrice * (window.CURRENCY_RATE ?? 1) * 100) / 100;
        priceEl.textContent = new Intl.NumberFormat('en-US', {
            minimumFractionDigits: 2, maximumFractionDigits: 2,
        }).format(converted) + ' ' + (window.CURRENCY_SYMBOL ?? '$');
    }

    /* ── Update stock ─────────────────────────────────────────── */
    const stockEl = document.getElementById('stock-status');
    if (stockEl && match) {
        stockEl.innerHTML = match.stock > 0
            ? `<div class="w-2.5 h-2.5 rounded-full bg-green-500 flex-shrink-0"></div>
               <span class="text-sm text-green-700 font-medium">متوفر (${match.stock} قطعة)</span>`
            : `<div class="w-2.5 h-2.5 rounded-full bg-red-500 flex-shrink-0"></div>
               <span class="text-sm text-red-600 font-medium">نفد المخزون</span>`;
    }

    /* ── Update SKU ───────────────────────────────────────────── */
    const skuEl = document.getElementById('variant-sku');
    if (skuEl) skuEl.textContent = match ? 'كود: ' + match.sku : '';

    /* ── Qty bounds ───────────────────────────────────────────── */
    const qtyInput = document.getElementById('qty-input');
    if (qtyInput && match) {
        qtyInput.max   = match.stock;
        qtyInput.value = Math.min(parseInt(qtyInput.value) || 1, match.stock) || 1;
    }

    /* ── Cart button state ────────────────────────────────────── */
    const cartBtn = document.getElementById('add-to-cart-btn');
    const canBuy  = !match || match.stock > 0;
    if (cartBtn) {
        cartBtn.disabled = !canBuy;
        cartBtn.classList.toggle('opacity-50', !canBuy);
        cartBtn.classList.toggle('cursor-not-allowed', !canBuy);
    }

    /* ── Variant image ────────────────────────────────────────── */
    if (match?.image_url) {
        switchImage(match.image_url, null);
    }
}

/* ── Add to cart ────────────────────────────────────────────────── */
function addToCart() {
    const qty       = parseInt(document.getElementById('qty-input').value) || 1;
    const btn       = document.getElementById('add-to-cart-btn');
    const variantId = selectedVariant?.id ?? null;
    Cart.add({{ $product->id }}, qty, btn, variantId);
}
</script>
@endpush