@php $isRtl = app()->getLocale() === 'ar'; @endphp

<style>
    .bottom-bar {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(255, 255, 255, .94);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-top: 1px solid rgba(0, 0, 0, .07);
        padding-bottom: env(safe-area-inset-bottom, 0);
        z-index: 50;
    }

    .bb-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 3px;
        padding: 8px 0 5px;
        font-size: 10px;
        font-weight: 600;
        color: #999;
        transition: color .18s ease;
        flex: 1;
        cursor: pointer;
        text-decoration: none;
        position: relative;
        min-height: 58px;
        justify-content: center;
    }

    .bb-item svg {
        width: 22px;
        height: 22px;
        stroke-width: 1.8;
        flex-shrink: 0;
    }

    .bb-item.active {
        color: var(--brand-color, #0ea5e9);
    }

    .bb-item.active svg {
        stroke-width: 2.4;
    }

    .bb-badge {
        position: absolute;
        top: 5px;
        inset-inline-end: calc(50% - 18px);
        background: #ff3366;
        color: #fff;
        font-size: 8px;
        font-weight: 800;
        min-width: 16px;
        height: 16px;
        border-radius: 999px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 3px;
        border: 1.5px solid #fff;
    }

    .bb-cart-bubble {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: -18px;
        box-shadow: 0 4px 16px color-mix(in srgb, var(--brand-color, #0ea5e9) 45%, transparent);
        background: var(--brand-color, #0ea5e9);
        flex-shrink: 0;
    }

    /* حتى لا يغطي الشريط المحتوى */
    @media (max-width: 767px) {
        main {
            padding-bottom: 76px;
        }
    }
</style>

<nav class="bottom-bar md:hidden"
     role="navigation"
     aria-label="Mobile navigation"
     dir="{{ $isRtl ? 'rtl' : 'ltr' }}">

    <div class="flex items-stretch">

        <a href="{{ route('products.index') }}"
           class="bb-item {{ request()->routeIs('products.index') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3"/>
            </svg>
            {{ __('app.shop') }}
        </a>

        <a href="{{ auth()->check() ? route('wishlist.index') : route('login') }}"
           class="bb-item relative {{ request()->routeIs('wishlist.*') ? 'active' : '' }}">
            <div class="relative">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>

                @auth
                    @php $wlCount = auth()->user()->wishlistedProducts()->count(); @endphp
                    @if($wlCount > 0)
                        <span class="bb-badge">{{ $wlCount }}</span>
                    @endif
                @endauth
            </div>
            {{ __('app.wishlist') }}
        </a>

        <a href="{{ route('cart.index') }}"
           class="bb-item relative {{ request()->routeIs('cart.*') ? 'active' : '' }}">
            <div class="relative">
                <div class="bb-cart-bubble">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>

                @php $cartCount = app(\App\Services\CartService::class)->getItemCount(); @endphp
                @if($cartCount > 0)
                    <span class="absolute -top-5 {{ $isRtl ? '-left-1' : '-right-1' }}
                                 min-w-[16px] h-4 bg-red-500 text-white text-[9px] font-black
                                 rounded-full flex items-center justify-center border border-white px-0.5 leading-none">
                        {{ $cartCount }}
                    </span>
                @endif
            </div>
       {{ __('app.cart.heading') }}
        </a>

        <a href="{{ url('/orders') }}"
           class="bb-item {{ request()->is('orders*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M9 5H7a2 2 0 00-2 2v14h14V7a2 2 0 00-2-2h-2M9 5a3 3 0 006 0M9 5a3 3 0 013-3 3 3 0 013 3"/>
            </svg>
            {{ __('app.orders.heading') }}
        </a>

        <a href="{{ auth()->check() ? route('myprofile.show') : route('login') }}"
           class="bb-item {{ request()->routeIs('myprofile.*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            {{ auth()->check() ? __('app.account') : __('app.login') }}
        </a>

    </div>
</nav>