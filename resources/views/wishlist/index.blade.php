{{-- resources/views/wishlist/index.blade.php --}}

@extends('layouts.app')
@section('title', 'المفضلة')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10" dir="rtl">

    {{-- Page header --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="font-display text-3xl font-bold text-gray-900 flex items-center gap-3">
                <svg class="w-7 h-7 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                المفضلة
            </h1>
            <p class="text-sm text-gray-400 mt-1">
                {{ $products->total() }} {{ $products->total() == 1 ? 'منتج' : 'منتجات' }} محفوظة
            </p>
        </div>

        @if($products->isNotEmpty())
        <a href="{{ route('products.index') }}"
           class="text-sm text-brand-600 hover:underline font-medium transition-colors">
            متابعة التسوق
        </a>
        @endif
    </div>

    {{-- Empty state --}}
    @if($products->isEmpty())
    <div class="flex flex-col items-center justify-center py-24 text-center">
        <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center mb-5">
            <svg class="w-10 h-10 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
        </div>
        <h2 class="text-lg font-bold text-gray-900 mb-2">قائمة المفضلة فارغة</h2>
        <p class="text-gray-400 text-sm mb-6 max-w-xs">
            لم تضف أي منتجات إلى المفضلة بعد. استعرض المتجر واضغط على قلب ❤️ لأي منتج يعجبك.
        </p>
        <a href="{{ route('products.index') }}"
           class="inline-flex items-center gap-2 bg-brand-600 text-white px-6 py-3 rounded-xl font-bold
                  hover:opacity-90 transition shadow-lg shadow-brand-600/20">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
            تصفح المنتجات
        </a>
        
    </div>

    @else

    {{-- Product grid — same card markup as products/index --}}
    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @foreach($products as $product)
        @php
            $cardImage   = $product->image_url ?? 'https://picsum.photos/seed/' . $product->id . '/400/400';
            $isWishlisted = in_array($product->id, $wishlistedIds);
        @endphp

        <a href="{{ route('products.show', $product->slug) }}"
           class="product-card bg-white rounded-2xl overflow-hidden border border-gray-100 flex flex-col group relative"
           aria-label="{{ $product->name }}">

            {{-- Image + heart --}}
            <div class="aspect-square overflow-hidden bg-gray-50 relative">
                <img src="{{ $cardImage }}"
                     alt="{{ $product->name }}"
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                     loading="lazy">

                {{-- Heart button (top-left in RTL = start) --}}
                <div class="absolute top-2 start-2" onclick="event.preventDefault()">
                    <x-wishlist-btn :product="$product" :wishlisted="$isWishlisted" />
                </div>

                {{-- Sale badge --}}
                <div class="absolute top-2 end-2 flex flex-col gap-1">
                    @if($product->is_on_sale)
                    <span class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full leading-snug">
                        {{ $product->discount_percentage }}% خصم
                    </span>
                    @endif
                    @if($product->is_featured)
                    <span class="bg-amber-400 text-amber-900 text-[10px] font-bold px-2 py-0.5 rounded-full leading-snug">
                        مميز
                    </span>
                    @endif
                </div>

                {{-- Out of stock --}}
                @if(!$product->in_stock)
                <div class="absolute inset-0 bg-white/60 flex items-center justify-center">
                    <span class="bg-white border border-gray-200 text-gray-600 text-xs font-semibold px-3 py-1.5 rounded-full shadow-sm">
                        نفد المخزون
                    </span>
                </div>
                @endif
            </div>

            {{-- Body --}}
            <div class="p-3 flex flex-col flex-1">
                @if($product->categories->first())
                <p class="text-[11px] font-medium text-brand-600 mb-1 uppercase tracking-wide">
                    {{ $product->categories->first()->name }}
                </p>
                @endif

                <p class="text-sm font-semibold text-gray-900 line-clamp-2 leading-snug mb-1">
                    {{ $product->name }}
                </p>

                @if($product->short_description)
                <p class="text-xs text-gray-500 line-clamp-2 leading-relaxed flex-1 mb-2">
                    {{ $product->short_description }}
                </p>
                @else
                <div class="flex-1"></div>
                @endif

                {{-- Price + Add to cart --}}
                <div class="flex items-center justify-between mt-auto pt-2 gap-2">
                    <div class="flex flex-col">
                        @if($product->is_on_sale)
                        <span class="text-base font-bold text-red-600">
                            ${{ number_format($product->discount_price, 2) }}
                        </span>
                        <span class="text-xs text-gray-400 line-through">
                            ${{ number_format($product->base_price, 2) }}
                        </span>
                        @else
                        <span class="text-base font-bold text-gray-900">
                            ${{ number_format($product->base_price, 2) }}
                        </span>
                        @endif
                    </div>

                    @if($product->in_stock)
                    <button
                        type="button"
                        onclick="event.preventDefault(); Cart.add({{ $product->id }}, 1, this)"
                        class="flex items-center gap-1 bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold px-3 py-2 rounded-xl transition-colors flex-shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        أضف
                    </button>
                    @endif
                </div>

                @if($product->variants->where('is_active', true)->count() > 1)
                <p class="text-[10px] text-gray-400 mt-1">
                    {{ $product->variants->where('is_active', true)->count() }} خيارات متاحة
                </p>
                @endif
            </div>
        </a>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($products->hasPages())
    <div class="mt-8 flex justify-center">
        {{ $products->links() }}
    </div>
    @endif

    @endif
    @yield('content')
@include('partials.bottombar')
</div>
@endsection
