{{--
    resources/views/components/wishlist-btn.blade.php

    Reusable heart button. Handles its own AJAX toggle logic.

    Props:
        $product      — Product model instance
        $wishlisted   — bool: whether current user has this wishlisted
        $class        — extra CSS classes (optional)

    Usage:
        <x-wishlist-btn :product="$product" :wishlisted="$isWishlisted" />
--}}

@props([
    'product',
    'wishlisted' => false,
    'class'      => '',
])

@auth
<button
    type="button"
    data-product-id="{{ $product->id }}"
    data-wishlisted="{{ $wishlisted ? 'true' : 'false' }}"
    onclick="toggleWishlist(this)"
    aria-label="{{ $wishlisted ? 'إزالة من المفضلة' : 'إضافة إلى المفضلة' }}"
    title="{{ $wishlisted ? 'إزالة من المفضلة' : 'إضافة إلى المفضلة' }}"
    class="wishlist-btn group relative flex items-center justify-center
           w-8 h-8 rounded-full bg-white/90 border border-gray-100 shadow-sm
           hover:scale-110 active:scale-95 transition-transform duration-150
           {{ $class }}"
>
    {{-- Outline heart (inactive) --}}
    <svg class="w-4 h-4 transition-all duration-200 {{ $wishlisted ? 'hidden' : 'block' }} text-gray-400 group-hover:text-red-400"
         data-heart="outline"
         fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
    </svg>

    {{-- Filled heart (active) --}}
    <svg class="w-4 h-4 transition-all duration-200 {{ $wishlisted ? 'block' : 'hidden' }} text-red-500"
         data-heart="filled"
         fill="currentColor" viewBox="0 0 24 24">
        <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
    </svg>
</button>
@endauth
