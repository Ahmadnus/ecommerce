{{--
    partials/bottombar.blade.php — fixed mobile tab bar.

    Same five destinations as before (shop, wishlist, cart, orders, account)
    with the same active-route logic; the inline <style> block it used to
    carry has moved into the .sf-bottombar primitives in storefront.css so
    its colours and border follow the theme tokens.

    The cart counter reuses the .cart-count class, so Cart.updateBadge()
    keeps it in sync with the header without any extra wiring.
--}}

@php
    $isRtl     = app()->getLocale() === 'ar';
    $cartCount = app(\App\Services\CartService::class)->getItemCount();
    $wlCount   = auth()->check() ? auth()->user()->wishlistedProducts()->count() : 0;
@endphp

<nav class="sf-bottombar"
     role="navigation"
     aria-label="{{ __('app.menu') }}"
     dir="{{ $isRtl ? 'rtl' : 'ltr' }}">

    <a href="{{ route('products.index') }}"
       class="sf-bottombar__item {{ request()->routeIs('products.index') ? 'is-active' : '' }}"
       @if(request()->routeIs('products.index')) aria-current="page" @endif>
        <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3"/>
        </svg>
        {{ __('app.shop') }}
    </a>

    <a href="{{ auth()->check() ? route('wishlist.index') : route('login') }}"
       class="sf-bottombar__item {{ request()->routeIs('wishlist.*') ? 'is-active' : '' }}">
        <span class="relative">
            <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
            <span class="sf-icon-btn__count wishlist-count"
                  @if($wlCount < 1) style="display:none" @endif>{{ $wlCount }}</span>
        </span>
        {{ __('app.wishlist') }}
    </a>

    <a href="{{ route('cart.index') }}"
       class="sf-bottombar__item {{ request()->routeIs('cart.*') ? 'is-active' : '' }}">
        <span class="relative">
            <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17M17 17a2 2 0 100 4 2 2 0 000-4zM9 19a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <span class="sf-icon-btn__count cart-count"
                  @if($cartCount < 1) style="display:none" @endif>{{ $cartCount }}</span>
        </span>
        {{ __('app.cart.heading') }}
    </a>

    <a href="{{ url('/orders') }}"
       class="sf-bottombar__item {{ request()->is('orders*') ? 'is-active' : '' }}">
        <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M9 5H7a2 2 0 00-2 2v14h14V7a2 2 0 00-2-2h-2M9 5a3 3 0 006 0M9 5a3 3 0 013-3 3 3 0 013 3"/>
        </svg>
        {{ __('app.orders.heading') }}
    </a>

    <a href="{{ auth()->check() ? route('myprofile.show') : route('login') }}"
       class="sf-bottombar__item {{ request()->routeIs('myprofile.*') ? 'is-active' : '' }}">
        <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
        </svg>
        {{ auth()->check() ? __('app.account') : __('app.login') }}
    </a>
</nav>
