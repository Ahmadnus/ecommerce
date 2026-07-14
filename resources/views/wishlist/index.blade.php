@extends('layouts.app')

@section('title')
    @php
        $localeMode = session('locale_mode', 'ar');
        $isBoth = $localeMode === 'both';
        $isRtl = app()->getLocale() === 'ar';
    @endphp

    @if($isBoth)
        {{ \Illuminate\Support\Facades\Lang::get('app.wishlist_page.title', [], 'ar') }} / {{ \Illuminate\Support\Facades\Lang::get('app.wishlist_page.title', [], 'en') }}
    @else
        {{ __('app.wishlist_page.title') }}
    @endif
@endsection

@section('content')
@php
    $localeMode = session('locale_mode', 'ar');
    $isBoth = $localeMode === 'both';
    $isRtl = app()->getLocale() === 'ar';
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">

    {{-- Page header --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="font-display text-3xl font-bold text-gray-900 flex items-center gap-3">
                <svg class="w-7 h-7 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>

                @if($isBoth)
                    {{ \Illuminate\Support\Facades\Lang::get('app.wishlist_page.title', [], 'ar') }} / {{ \Illuminate\Support\Facades\Lang::get('app.wishlist_page.title', [], 'en') }}
                @else
                    {{ __('app.wishlist_page.title') }}
                @endif
            </h1>

            <p class="text-sm text-gray-400 mt-1">
                @if($isBoth)
                    {{ $products->total() }} {{ \Illuminate\Support\Facades\Lang::get('app.wishlist_page.saved_items', ['count' => $products->total()], 'ar') }}
                    /
                    {{ $products->total() }} {{ \Illuminate\Support\Facades\Lang::get('app.wishlist_page.saved_items', ['count' => $products->total()], 'en') }}
                @else
                    {{ $products->total() }} {{ $products->total() == 1 ? __('app.wishlist_page.saved_items_single') : __('app.wishlist_page.saved_items') }}
                @endif
            </p>
        </div>

        @if($products->isNotEmpty())
            <a href="{{ route('products.index') }}"
               class="text-sm text-brand-600 hover:underline font-medium transition-colors">
                @if($isBoth)
                    {{ \Illuminate\Support\Facades\Lang::get('app.wishlist_page.continue_shopping', [], 'ar') }} / {{ \Illuminate\Support\Facades\Lang::get('app.wishlist_page.continue_shopping', [], 'en') }}
                @else
                    {{ __('app.wishlist_page.continue_shopping') }}
                @endif
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

            <h2 class="text-lg font-bold text-gray-900 mb-2">
                @if($isBoth)
                    {{ \Illuminate\Support\Facades\Lang::get('app.wishlist_page.empty_title', [], 'ar') }} / {{ \Illuminate\Support\Facades\Lang::get('app.wishlist_page.empty_title', [], 'en') }}
                @else
                    {{ __('app.wishlist_page.empty_title') }}
                @endif
            </h2>

            <p class="text-gray-400 text-sm mb-6 max-w-xs">
                @if($isBoth)
                    {{ \Illuminate\Support\Facades\Lang::get('app.wishlist_page.empty_sub', [], 'ar') }} / {{ \Illuminate\Support\Facades\Lang::get('app.wishlist_page.empty_sub', [], 'en') }}
                @else
                    {{ __('app.wishlist_page.empty_sub') }}
                @endif
            </p>

            <a href="{{ route('products.index') }}"
               class="inline-flex items-center gap-2 bg-brand-600 text-white px-6 py-3 rounded-xl font-bold
                      hover:opacity-90 transition shadow-lg shadow-brand-600/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>

                @if($isBoth)
                    {{ \Illuminate\Support\Facades\Lang::get('app.wishlist_page.browse_products', [], 'ar') }} / {{ \Illuminate\Support\Facades\Lang::get('app.wishlist_page.browse_products', [], 'en') }}
                @else
                    {{ __('app.wishlist_page.browse_products') }}
                @endif
            </a>
        </div>

    @else

        {{-- Product grid --}}
        <div class="grid grid-cols-2 gap-x-8 gap-y-10 mt-[30px] px-[10px]">
            @foreach($products as $product)
                @php
                    $cardImage = $product->getFirstMediaUrl('products');
                @endphp

                <a href="{{ route('products.show', $product->slug) }}"
                   class="flex flex-col"
                   aria-label="{{ $product->name }}">

                    <div class="relative overflow-hidden aspect-square rounded-[2px]">
                        @if($cardImage)
                        <img src="{{ $cardImage }}"
                             alt="{{ $product->name }}"
                             class="w-full h-full object-cover"
                             loading="lazy">
                        @endif

                        <button type="button" class="favorite-btn absolute top-3 {{ $isRtl ? 'left-3' : 'right-3' }} z-20"
                                data-product-id="{{ $product->id }}"
                                data-wishlisted="true"
                                onclick="event.preventDefault(); event.stopPropagation(); toggleWishlist(this)"
                                aria-label="{{ __('app.remove_from_wishlist') }}">
                            <svg data-heart="outline"
                                 class="w-5 h-5 text-white drop-shadow-sm transition-all duration-200 hidden"
                                 fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                            <svg data-heart="filled"
                                 class="w-5 h-5 text-red-500 drop-shadow-sm transition-all duration-200 block"
                                 fill="currentColor" viewBox="0 0 24 24">
                                <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </button>
                    </div>

                    <div class="pt-1.5 text-start">
                        <p class="text-xs uppercase tracking-wider font-medium text-gray-900 line-clamp-2 leading-snug">
                            {{ $product->name }}
                        </p>

                        <div class="flex items-center gap-2">
                            @if($product->is_on_sale)
                                <span class="text-xs uppercase tracking-wider text-red-600">
                                    ${{ number_format($product->discount_price, 2) }}
                                </span>
                                <span class="text-xs uppercase tracking-wider text-gray-400 line-through">
                                    ${{ number_format($product->base_price, 2) }}
                                </span>
                            @else
                                <span class="text-xs uppercase tracking-wider text-gray-900">
                                    ${{ number_format($product->base_price, 2) }}
                                </span>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
@include('partials.bottombar')
        {{-- Pagination --}}
        @if($products->hasPages())
            <div class="mt-8 flex justify-center">
                {{ $products->links() }}
            </div>
        @endif
    @endif
</div>
@endsection