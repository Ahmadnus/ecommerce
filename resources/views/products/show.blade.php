{{-- resources/views/products/show.blade.php --}}

@extends('layouts.app')

@section('title', $product->name)

@push('head')
<style>
    .variant-btn {
        padding: 6px 14px;
        font-size: 12px;
        font-weight: 600;
        border: 1.5px solid #e5e7eb;
        border-radius: 10px;
        background: #fff;
        color: #374151;
        cursor: pointer;
        transition: all 0.15s;
    }
    .variant-btn:hover       { border-color: var(--brand-color); color: var(--brand-color); }
    .variant-btn.active      { border-color: var(--brand-color); background: var(--brand-color); color: #fff; }
    .variant-btn.unavailable { opacity: 0.4; cursor: not-allowed; text-decoration: line-through; }

    .color-swatch {
        width: 30px; height: 30px;
        border-radius: 50%;
        border: 2px solid transparent;
        cursor: pointer;
        transition: transform 0.15s, box-shadow 0.15s;
    }
    .color-swatch:hover  { transform: scale(1.15); }
    .color-swatch.active { box-shadow: 0 0 0 2px #fff, 0 0 0 4px var(--brand-color); }
</style>
@endpush

@section('content')
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
               class="hover:text-brand-600 transition-colors">
                {{ $product->categories->first()->name }}
            </a>
        @endif

        <svg class="w-3.5 h-3.5 rotate-180 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-gray-900 font-medium">{{ Str::limit($product->name, 40) }}</span>
    </nav>

    {{-- ═══ PRODUCT DETAIL ════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 mb-20">

        {{-- ── Image Gallery ─────────────────────────────────────────────── --}}
        <div>
            <div class="aspect-square rounded-2xl overflow-hidden bg-gray-50 border border-gray-100 mb-3">
                <img id="main-image"
                     src="{{ $product->image_url ?? 'https://picsum.photos/seed/'.$product->id.'/700/700' }}"
                     alt="{{ $product->name }}"
                     class="w-full h-full object-cover transition-opacity duration-300">
            </div>

            @php
                $allImages = collect();
                if ($product->image_url) $allImages->push($product->image_url);
                foreach ($product->image_urls as $url) $allImages->push($url);
                foreach ($product->variants->whereNotNull('variant_image') as $v) {
                    if ($v->image_url && !$allImages->contains($v->image_url)) {
                        $allImages->push($v->image_url);
                    }
                }
            @endphp

            @if($allImages->count() > 1)
            <div class="flex gap-2 overflow-x-auto pb-1">
                @foreach($allImages as $i => $imgUrl)
                <button type="button"
                        onclick="switchImage('{{ $imgUrl }}', this)"
                        class="thumb-btn flex-shrink-0 w-16 h-16 rounded-xl overflow-hidden border-2 transition-colors {{ $i === 0 ? 'border-brand-500' : 'border-transparent hover:border-brand-300' }}">
                    <img src="{{ $imgUrl }}" class="w-full h-full object-cover" loading="lazy" alt="">
                </button>
                @endforeach
            </div>
            @endif
        </div>

        {{-- ── Product Info ──────────────────────────────────────────────── --}}
        <div class="flex flex-col">

            {{-- Badges + category --}}
            <div class="flex items-center gap-2 mb-3 flex-wrap">
                @if($product->categories->first())
                <a href="{{ route('products.index', ['category' => $product->categories->first()->slug]) }}"
                   class="text-sm text-brand-600 font-medium hover:underline">
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

            {{-- Price (updates when variant selected) --}}
            <div class="flex items-end gap-3 mb-5">
                <span id="price-current" class="text-4xl font-bold text-gray-900">
                    ${{ number_format($product->effective_price, 2) }}
                </span>
                @if($product->is_on_sale)
                <span class="text-xl text-gray-400 line-through mb-0.5">
                    ${{ number_format($product->base_price, 2) }}
                </span>
                <span class="text-sm text-red-500 font-semibold mb-0.5">
                    وفّر ${{ number_format($product->base_price - $product->discount_price, 2) }}
                </span>
                @endif
            </div>

            {{-- Stock (updates when variant selected) --}}
            <div id="stock-status" class="flex items-center gap-2 mb-5">
                @if($product->in_stock)
                    <div class="w-2.5 h-2.5 rounded-full bg-green-500 flex-shrink-0"></div>
                    <span class="text-sm text-green-700 font-medium">متوفر ({{ $product->total_stock }} قطعة)</span>
                @else
                    <div class="w-2.5 h-2.5 rounded-full bg-red-500 flex-shrink-0"></div>
                    <span class="text-sm text-red-600 font-medium">نفد المخزون</span>
                @endif
            </div>

            <p class="text-gray-600 leading-relaxed mb-6 text-sm border-b border-gray-100 pb-6">
                {{ $product->description }}
            </p>

            {{-- ── Variant Selectors ─────────────────────────────────────── --}}
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
                window.VARIANTS   = @json($variantsJson);
                window.BASE_PRICE = {{ (float) $product->effective_price }};
                window.SEL_AVS    = {}; // attrId => avId
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

            {{-- ── Quantity + Add to Cart ────────────────────────────────── --}}
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
                        class="flex-1 bg-brand-600 hover:bg-brand-700 text-white font-semibold px-6 py-3 rounded-xl transition-colors flex items-center justify-center gap-2 text-sm">
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

    {{-- ═══ RELATED PRODUCTS ═══════════════════════════════════════════════ --}}
    @if($related->isNotEmpty())
    <section>
        <h2 class="font-display text-2xl font-bold text-gray-900 mb-6">قد يعجبك أيضاً</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($related as $rel)
            <a href="{{ route('products.show', $rel->slug) }}"
               class="product-card bg-white rounded-2xl overflow-hidden border border-gray-100 group">
                <div class="aspect-square overflow-hidden bg-gray-50">
                    <img src="{{ $rel->image_url ?? 'https://picsum.photos/seed/'.$rel->id.'/400/400' }}"
                         alt="{{ $rel->name }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                         loading="lazy">
                </div>
                <div class="p-3">
                    <p class="text-sm font-semibold text-gray-900 line-clamp-2 leading-snug mb-1">{{ $rel->name }}</p>
                    <p class="text-sm font-bold text-brand-600">${{ number_format($rel->effective_price, 2) }}</p>
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

function adjustQty(delta) {
    const input  = document.getElementById('qty-input');
    const maxQty = selectedVariant ? selectedVariant.stock : MAX_QTY;
    input.value  = Math.max(1, Math.min(maxQty, parseInt(input.value || 1) + delta));
}

function switchImage(src, btn) {
    document.getElementById('main-image').src = src;
    document.querySelectorAll('.thumb-btn').forEach(b => {
        b.classList.toggle('border-brand-500', b === btn);
        b.classList.toggle('border-transparent', b !== btn);
        b.classList.toggle('hover:border-brand-300', b !== btn);
    });
}

function selectOption(btn) {
    const attrId = btn.dataset.attr;
    const avId   = parseInt(btn.dataset.av);
    const label  = btn.dataset.label;

    // Deactivate siblings in same attribute group
    btn.closest('[data-attribute]')
       .querySelectorAll('[data-attr="' + attrId + '"]')
       .forEach(b => b.classList.remove('active'));

    btn.classList.add('active');

    const labelEl = document.getElementById('sel-' + attrId);
    if (labelEl) labelEl.textContent = label;

    window.SEL_AVS[attrId] = avId;
    resolveVariant();
}

function resolveVariant() {
    const selectedIds = Object.values(window.SEL_AVS).map(Number);
    if (!selectedIds.length) return;

    const match = window.VARIANTS.find(v =>
        v.is_active &&
        selectedIds.every(id => v.attribute_values.includes(id))
    ) || null;

    selectedVariant = match;

    // ── Price ──
    const priceEl = document.getElementById('price-current');
    if (priceEl) priceEl.textContent = '$' + (match ? match.price : window.BASE_PRICE).toFixed(2);

    // ── Stock ──
    const stockEl = document.getElementById('stock-status');
    if (stockEl && match) {
        stockEl.innerHTML = match.stock > 0
            ? `<div class="w-2.5 h-2.5 rounded-full bg-green-500 flex-shrink-0"></div>
               <span class="text-sm text-green-700 font-medium">متوفر (${match.stock} قطعة)</span>`
            : `<div class="w-2.5 h-2.5 rounded-full bg-red-500 flex-shrink-0"></div>
               <span class="text-sm text-red-600 font-medium">نفد المخزون</span>`;
    }

    // ── SKU ──
    const skuEl = document.getElementById('variant-sku');
    if (skuEl) skuEl.textContent = match ? 'كود: ' + match.sku : '';

    // ── Qty bounds ──
    const qtyInput = document.getElementById('qty-input');
    if (qtyInput && match) {
        qtyInput.max   = match.stock;
        qtyInput.value = Math.min(parseInt(qtyInput.value) || 1, match.stock) || 1;
    }

    // ── Cart button ──
    const cartBtn = document.getElementById('add-to-cart-btn');
    const canBuy  = !match || match.stock > 0;
    if (cartBtn) {
        cartBtn.disabled = !canBuy;
        cartBtn.classList.toggle('opacity-50', !canBuy);
        cartBtn.classList.toggle('cursor-not-allowed', !canBuy);
    }

    // ── Variant image ──
    if (match?.image_url) {
        document.getElementById('main-image').src = match.image_url;
    }
}

function addToCart() {
    const qty       = parseInt(document.getElementById('qty-input').value) || 1;
    const btn       = document.getElementById('add-to-cart-btn');
    const variantId = selectedVariant?.id ?? null;
    Cart.add({{ $product->id }}, qty, btn, variantId);
}
</script>
@endpush