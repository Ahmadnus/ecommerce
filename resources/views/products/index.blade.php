@extends('layouts.app')

@section('title', 'Shop All Products')

@section('content')

{{-- ═══ HERO BANNER ═══════════════════════════════════════════════════════════ --}}
<section class="text-white relative overflow-hidden" 
         style="background: linear-gradient(135deg, var(--brand-color) 0%, #0c4a6e 100%);">
    
    <div class="absolute inset-0 bg-black/10"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-20">
        <div class="max-w-2xl">
            <p class="text-white/80 text-sm font-medium uppercase tracking-widest mb-3">Free shipping on orders over $50</p>
            
            <h1 class="font-display text-4xl md:text-5xl font-bold mb-4 leading-tight">
                Discover Products<br>You'll Love
            </h1>
            
            <p class="text-white/70 text-lg mb-8">
                Curated collections across electronics, fashion, home, and more.
            </p>
            
            <a href="#products"
               class="inline-flex items-center gap-2 bg-white font-semibold px-6 py-3 rounded-xl hover:bg-gray-100 transition-colors shadow-lg"
               style="color: var(--brand-color);">
                Shop Now
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </a>
        </div>
    </div>
</section>

{{-- ═══ FEATURED PRODUCTS ════════════════════════════ --}}
@if($featured->isNotEmpty())
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <h2 class="font-display text-2xl font-bold text-gray-900 mb-6">Featured This Week</h2>
    <div class="flex gap-4 overflow-x-auto pb-4 scrollbar-hide">
        @foreach($featured as $product)
        <a href="{{ route('products.show', $product->slug) }}"
           class="flex-shrink-0 w-52 bg-white rounded-2xl overflow-hidden border border-gray-100 hover:shadow-lg transition-shadow group">
            <div class="aspect-square overflow-hidden bg-gray-50">
                {{-- تعديل الصورة هنا --}}
                <img src="{{ $product->getFirstMediaUrl('products') ?: asset('images/default-product.png') }}"
                     alt="{{ $product->name }}"
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
            </div>
            <div class="p-3">
                <p class="text-xs text-brand-600 font-medium mb-1" style="color: var(--brand-color);">{{ $product->category->name }}</p>
                <p class="text-sm font-semibold text-gray-900 line-clamp-2 leading-snug">{{ $product->name }}</p>
                <p class="text-sm font-bold text-gray-900 mt-2">${{ number_format($product->effective_price, 2) }}</p>
            </div>
        </a>
        @endforeach
    </div>
</section>
@endif

{{-- ═══ MAIN SHOP AREA ═══════════════════════════════════════════════════════ --}}
<section id="products" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
    <div class="flex flex-col lg:flex-row gap-8">

        {{-- SIDEBAR FILTERS --}}
        <aside class="lg:w-60 flex-shrink-0">
            <div class="bg-white rounded-2xl border border-gray-100 p-5 sticky top-20">
                <h3 class="font-semibold text-gray-900 mb-4">Filters</h3>

                <form method="GET" action="{{ route('products.index') }}" id="filter-form">
                    <div class="mb-5">
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Search</label>
                        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                               placeholder="Search products..."
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-brand-500">
                    </div>

                    <div class="mb-5">
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Category</label>
                        <div class="space-y-1.5">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="category" value="" {{ empty($filters['category']) ? 'checked' : '' }} onchange="this.form.submit()">
                                <span class="text-sm text-gray-700">All Categories</span>
                            </label>
                            @foreach($categories as $cat)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="category" value="{{ $cat->id }}" {{ ($filters['category'] ?? '') == $cat->id ? 'checked' : '' }} onchange="this.form.submit()">
                                <span class="text-sm text-gray-700">{{ $cat->name }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 bg-brand text-white text-sm font-medium py-2 rounded-lg transition-colors" style="background-color: var(--brand-color);">
                            Apply
                        </button>
                        <a href="{{ route('products.index') }}" class="flex-1 text-center border border-gray-200 hover:bg-gray-50 text-sm text-gray-600 py-2 rounded-lg transition-colors">
                            Clear
                        </a>
                    </div>
                </form>
            </div>
        </aside>

        {{-- PRODUCT GRID --}}
        <div class="flex-1">
            <div class="flex items-center justify-between mb-6">
                <p class="text-sm text-gray-500">
                    Showing <span class="font-semibold text-gray-900">{{ $products->firstItem() }}–{{ $products->lastItem() }}</span>
                    of <span class="font-semibold text-gray-900">{{ $products->total() }}</span> products
                </p>
            </div>

            @if($products->isEmpty())
                <div class="text-center py-20 text-gray-400">
                    <svg class="w-16 h-16 mx-auto mb-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-lg font-medium">No products found</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
                    @foreach($products as $product)
                    <div class="bg-white rounded-2xl overflow-hidden border border-gray-100 flex flex-col shadow-sm">

                        {{-- Image --}}
                        <a href="{{ route('products.show', $product->slug) }}" class="block relative overflow-hidden aspect-square bg-gray-50">
                            {{-- تعديل الصورة هنا --}}
                            <img src="{{ $product->getFirstMediaUrl('products') ?: asset('images/default-product.png') }}"
                                 alt="{{ $product->name }}"
                                 class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                                 loading="lazy">

                            <div class="absolute top-3 left-3 flex flex-col gap-1">
                                @if($product->is_on_sale)
                                    <span class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">SALE</span>
                                @endif
                            </div>
                        </a>

                        {{-- Details --}}
                        <div class="p-4 flex flex-col flex-1">
                            <p class="text-xs font-medium mb-1" style="color: var(--brand-color);">{{ $product->category->name }}</p>
                            <a href="{{ route('products.show', $product->slug) }}" class="font-semibold text-gray-900 hover:text-brand transition-colors line-clamp-2 mb-4 flex-1">
                                {{ $product->name }}
                            </a>

                            <div class="flex items-center justify-between mt-auto">
                                <span class="text-lg font-bold text-gray-900">
                                    ${{ number_format($product->effective_price, 2) }}
                                </span>

                                <button onclick="Cart.add({{ $product->id }}, 1, this)"
                                        {{ !$product->in_stock ? 'disabled' : '' }}
                                        class="text-white text-sm font-medium px-4 py-2 rounded-xl transition-colors bg-brand"
                                        style="background-color: var(--brand-color);">
                                    {{ $product->in_stock ? 'Add to Cart' : 'Sold Out' }}
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-10 flex justify-center">
                    {{ $products->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</section>

@endsection