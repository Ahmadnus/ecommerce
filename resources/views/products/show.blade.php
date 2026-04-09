@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-gray-500 mb-8">
        <a href="{{ route('products.index') }}" class="hover:text-brand-600 transition-colors">Shop</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('products.index', ['category' => $product->category_id]) }}"
           class="hover:text-brand-600 transition-colors">{{ $product->category->name }}</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-gray-900 font-medium">{{ Str::limit($product->name, 40) }}</span>
    </nav>

    {{-- ═══ PRODUCT DETAIL ════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-20">

        {{-- ── Image Gallery ────────────────────────────────────────────────── --}}
        <div>
            <div class="aspect-square rounded-2xl overflow-hidden bg-gray-50 mb-4">
                <img id="main-image"
                     src="{{ $product->image ?? 'https://picsum.photos/seed/'.$product->id.'/700/700' }}"
                     alt="{{ $product->name }}"
                     class="w-full h-full object-cover">
            </div>
            {{-- Thumbnail strip (if extra images exist) --}}
            @if($product->images)
            <div class="flex gap-3">
                <button onclick="document.getElementById('main-image').src='{{ $product->image }}'"
                        class="w-20 h-20 rounded-xl overflow-hidden border-2 border-brand-500 flex-shrink-0">
                    <img src="{{ $product->image }}" class="w-full h-full object-cover">
                </button>
                @foreach($product->images as $img)
                <button onclick="document.getElementById('main-image').src='{{ $img }}'"
                        class="w-20 h-20 rounded-xl overflow-hidden border-2 border-transparent hover:border-brand-400 transition-colors flex-shrink-0">
                    <img src="{{ $img }}" class="w-full h-full object-cover">
                </button>
                @endforeach
            </div>
            @endif
        </div>

        {{-- ── Product Info ──────────────────────────────────────────────────── --}}
        <div class="flex flex-col">

            {{-- Category + badges --}}
            <div class="flex items-center gap-2 mb-3">
                <a href="{{ route('products.index', ['category' => $product->category_id]) }}"
                   class="text-sm text-brand-600 font-medium hover:underline">
                    {{ $product->category->name }}
                </a>
                @if($product->is_on_sale)
                    <span class="bg-red-100 text-red-600 text-xs font-bold px-2 py-0.5 rounded-full">
                        {{ $product->discount_percentage }}% OFF
                    </span>
                @endif
                @if($product->is_featured)
                    <span class="bg-amber-100 text-amber-600 text-xs font-bold px-2 py-0.5 rounded-full">
                        ⭐ Featured
                    </span>
                @endif
            </div>

            <h1 class="font-display text-3xl md:text-4xl font-bold text-gray-900 leading-tight mb-4">
                {{ $product->name }}
            </h1>

            {{-- Price --}}
            <div class="flex items-end gap-3 mb-6">
                <span class="text-4xl font-bold text-gray-900">
                    ${{ number_format($product->effective_price, 2) }}
                </span>
                @if($product->is_on_sale)
                    <span class="text-xl text-gray-400 line-through mb-1">
                        ${{ number_format($product->price, 2) }}
                    </span>
                    <span class="text-sm text-red-500 font-semibold mb-1">
                        Save ${{ number_format($product->price - $product->sale_price, 2) }}
                    </span>
                @endif
            </div>

            {{-- Stock status --}}
            <div class="flex items-center gap-2 mb-6">
                @if($product->in_stock)
                    <div class="w-2.5 h-2.5 rounded-full bg-green-500"></div>
                    <span class="text-sm text-green-700 font-medium">
                        In Stock ({{ $product->stock_quantity }} available)
                    </span>
                @else
                    <div class="w-2.5 h-2.5 rounded-full bg-red-500"></div>
                    <span class="text-sm text-red-600 font-medium">Out of Stock</span>
                @endif
            </div>

            <p class="text-gray-600 leading-relaxed mb-8">{{ $product->description }}</p>

            {{-- Quantity + Add to Cart --}}
            @if($product->in_stock)
            <div class="flex items-center gap-4 mb-6">
                {{-- Quantity selector --}}
                <div class="flex items-center border border-gray-200 rounded-xl overflow-hidden">
                    <button id="qty-minus" onclick="adjustQty(-1)"
                            class="w-10 h-12 flex items-center justify-center text-gray-600 hover:bg-gray-50 transition-colors text-lg">−</button>
                    <input id="qty-input" type="number" value="1" min="1" max="{{ $product->stock_quantity }}"
                           class="w-14 h-12 text-center border-x border-gray-200 text-sm font-semibold focus:outline-none">
                    <button id="qty-plus" onclick="adjustQty(1)"
                            class="w-10 h-12 flex items-center justify-center text-gray-600 hover:bg-gray-50 transition-colors text-lg">+</button>
                </div>

                {{-- Add to Cart CTA --}}
                <button id="add-to-cart-btn"
                        onclick="addToCart()"
                        class="flex-1 bg-brand-600 hover:bg-brand-700 text-white font-semibold px-6 py-3 rounded-xl transition-colors flex items-center justify-center gap-2 text-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Add to Cart
                </button>
            </div>
            @else
            <div class="bg-gray-100 text-gray-500 text-center py-4 rounded-xl mb-6 font-medium">
                This product is currently out of stock
            </div>
            @endif

            {{-- Trust badges --}}
            <div class="grid grid-cols-3 gap-3 py-6 border-t border-gray-100 text-center">
                <div class="text-xs text-gray-500">
                    <div class="text-xl mb-1">🚚</div>Free shipping<br>over $50
                </div>
                <div class="text-xs text-gray-500">
                    <div class="text-xl mb-1">↩️</div>30-day<br>returns
                </div>
                <div class="text-xs text-gray-500">
                    <div class="text-xl mb-1">🔒</div>Secure<br>checkout
                </div>
            </div>

            {{-- SKU --}}
            @if($product->sku)
            <p class="text-xs text-gray-400">SKU: {{ $product->sku }}</p>
            @endif
        </div>
    </div>

    {{-- ═══ RELATED PRODUCTS ════════════════════════════════════════════════ --}}
    @if($related->isNotEmpty())
    <section>
        <h2 class="font-display text-2xl font-bold text-gray-900 mb-6">You Might Also Like</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($related as $rel)
            @if($rel->id !== $product->id)
            <a href="{{ route('products.show', $rel->slug) }}"
               class="product-card bg-white rounded-2xl overflow-hidden border border-gray-100 group">
                <div class="aspect-square overflow-hidden bg-gray-50">
                    <img src="{{ $rel->image ?? 'https://picsum.photos/seed/'.$rel->id.'/400/400' }}"
                         alt="{{ $rel->name }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                         loading="lazy">
                </div>
                <div class="p-3">
                    <p class="text-sm font-semibold text-gray-900 line-clamp-2 leading-snug mb-1">{{ $rel->name }}</p>
                    <p class="text-sm font-bold text-brand-600">${{ number_format($rel->effective_price, 2) }}</p>
                </div>
            </a>
            @endif
            @endforeach
        </div>
    </section>
    @endif

</div>
@endsection

@push('scripts')
<script>
    const MAX_QTY = {{ $product->stock_quantity }};

    function adjustQty(delta) {
        const input = document.getElementById('qty-input');
        const newVal = Math.max(1, Math.min(MAX_QTY, parseInt(input.value || 1) + delta));
        input.value = newVal;
    }

    function addToCart() {
        const qty = parseInt(document.getElementById('qty-input').value);
        const btn = document.getElementById('add-to-cart-btn');
        Cart.add({{ $product->id }}, qty, btn);
    }
</script>
@endpush
