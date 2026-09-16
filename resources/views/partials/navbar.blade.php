{{--
    partials/navbar.blade.php — storefront header.

    Layout mirrors the reference store exactly:

        [ ☰ menu ]        [ logo, centred ]        [ ( cart ) ]

    The bar is a solid block of the brand colour (--nav-bg-color), the logo
    is absolutely centred so it stays put whatever the side controls weigh,
    and there is no search field. Categories are NOT shown on the page — they
    live behind the ☰ button and expand as a list inside the header, which is
    how the reference behaves.

    Everything the previous header did is preserved, just relocated into the
    menu panel: category links, currency switcher, language switcher,
    wishlist, orders, account and logout.

    JS contracts other files depend on, kept intact:
      .cart-count / .wishlist-count   — Cart.updateBadge() / updateWishlistBadge()
      #mobile-menu-btn / #mobile-menu — toggled by the script at the bottom
      .navbar-logo                    — sized by --logo-size
--}}

@php
    $isRtl  = app()->getLocale() === 'ar';
    $locale = app()->getLocale();

    $cartCount     = app(\App\Services\CartService::class)->getItemCount();
    $wishlistCount = auth()->check() ? auth()->user()->wishlistedProducts()->count() : 0;

    // Every active top-level category — the menu is the only place they
    // appear, so it is not truncated the way a strip would have to be.
    $navCategories = \App\Models\Category::query()
        ->whereNull('parent_id')
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->get();

    $activeCategory = request('category');
    $storeName      = \App\Models\Setting::get('site_name', config('app.name'));

    // layouts/app always hands us a $logoUrl, falling back to a default asset
    // that may not exist on a fresh install, so only an actual upload counts.
    $uploadedLogo = \App\Models\Setting::mediaHolder()->getFirstMediaUrl('logo');

    // header_brand_mode (admin setting) decides what the header shows:
    //   auto — logo when one is uploaded, otherwise the store name
    //   logo — always the logo
    //   text — always the store name, even if a logo exists
    //   both — logo and store name side by side
    // "logo"/"both" still fall back to text when nothing has been uploaded,
    // so the header can never render a broken image.
    $brandMode = $themeTokens['header_brand_mode'] ?? 'auto';

    $showLogo = $uploadedLogo && in_array($brandMode, ['auto', 'logo', 'both'], true);
    $showText = $brandMode === 'text'
             || $brandMode === 'both'
             || ! $uploadedLogo;
@endphp

<header class="sf-header sf-header--solid" id="sf-header" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">

    <div class="sf-container">
        <div class="sf-header__bar">

            {{-- Menu toggle — leading edge --}}
            <button type="button"
                    id="mobile-menu-btn"
                    class="sf-menu-btn"
                    aria-controls="mobile-menu"
                    aria-expanded="false"
                    aria-label="{{ __('app.menu') }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/>
                </svg>
            </button>

            {{-- Logo — absolutely centred --}}
            <a href="{{ route('products.index') }}"
               class="sf-header__logo"
               aria-label="{{ $storeName }}">
                @if($showLogo)
                    <img src="{{ $uploadedLogo }}" alt="{{ $storeName }}" class="navbar-logo">
                @endif

                @if($showText)
                    <span class="sf-header__wordmark">{{ $storeName }}</span>
                @endif
            </a>

            {{-- Cart — trailing edge --}}
            <a href="{{ route('cart.index') }}" class="sf-cart-circle" aria-label="{{ __('app.cart.heading') }}">
                <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17M17 17a2 2 0 100 4 2 2 0 000-4zM9 19a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span class="sf-icon-btn__count cart-count"
                      @if($cartCount < 1) style="display:none" @endif>{{ $cartCount }}</span>
            </a>
        </div>
    </div>

    {{-- ── Menu panel ───────────────────────────────────────────────────
         Sits inside the header's colour block and expands on ☰. This is the
         only place categories are listed. ───────────────────────────────── --}}
    <div id="mobile-menu" class="sf-menu" hidden>
        <div class="sf-container">
            <nav class="sf-menu__list" aria-label="{{ __('app.categories') }}">

                <a href="{{ route('products.index') }}"
                   class="sf-menu__item {{ $activeCategory ? '' : 'is-active' }}">
                    {{ __('app.all_products') }}
                </a>

                @foreach($navCategories as $cat)
                    <a href="{{ route('products.index', ['category' => $cat->slug]) }}"
                       class="sf-menu__item {{ $activeCategory === $cat->slug ? 'is-active' : '' }}"
                       @if($activeCategory === $cat->slug) aria-current="page" @endif>
                        {{ $cat->name }}
                    </a>
                @endforeach

                @if(\Illuminate\Support\Facades\Route::has('customize.index'))
                    <a href="{{ route('customize.index') }}" class="sf-menu__item">
                        {{ __('app.customize') }}
                    </a>
                @endif

                <hr class="sf-menu__sep">

                @auth
                    <a href="{{ route('myprofile.show') }}" class="sf-menu__item">{{ __('app.account') }}</a>
                    <a href="{{ route('orders.index') }}" class="sf-menu__item">{{ __('app.orders.heading') }}</a>
                    <a href="{{ route('wishlist.index') }}" class="sf-menu__item">
                        {{ __('app.wishlist') }}
                        <span class="sf-icon-btn__count wishlist-count"
                              style="position:static; {{ $wishlistCount < 1 ? 'display:none' : '' }}">{{ $wishlistCount }}</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="sf-menu__item">{{ __('app.login') }}</a>
                    <a href="{{ route('register') }}" class="sf-menu__item">{{ __('app.create_account') }}</a>
                @endauth

                <a href="{{ route('contact.create') }}" class="sf-menu__item">{{ __('app.contact_us') }}</a>

                {{-- Currency + language, kept compact on one row --}}
                <div class="sf-menu__row">
                    @foreach(\App\Models\Currency::active()->get() as $cur)
                        <a href="{{ route('currency.user.switch', $cur->code) }}" class="sf-menu__pill">
                            {{ $cur->code }}
                        </a>
                    @endforeach

                    @if(($locale_mode ?? 'both') === 'both')
                        <form method="POST" action="{{ route('language.switch') }}">
                            @csrf
                            <input type="hidden" name="locale" value="{{ $locale === 'ar' ? 'en' : 'ar' }}">
                            <button type="submit" class="sf-menu__pill">
                                {{ $locale === 'ar' ? 'English' : 'العربية' }}
                            </button>
                        </form>
                    @endif
                </div>

                @auth
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="sf-menu__item" style="width:100%; text-align:inherit">
                            {{ __('app.logout') }}
                        </button>
                    </form>
                @endauth
            </nav>
        </div>
    </div>
</header>

<script>
    (function () {
        const btn    = document.getElementById('mobile-menu-btn');
        const menu   = document.getElementById('mobile-menu');
        const header = document.getElementById('sf-header');
        if (!btn || !menu) return;

        function setOpen(open) {
            menu.hidden = !open;
            btn.setAttribute('aria-expanded', String(open));
            btn.classList.toggle('is-open', open);
        }

        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            setOpen(menu.hidden);
        });

        // Clicking anywhere outside the header, or pressing Escape, closes it.
        document.addEventListener('click', function (e) {
            if (!menu.hidden && !header.contains(e.target)) setOpen(false);
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !menu.hidden) { setOpen(false); btn.focus(); }
        });

        // Elevation appears only once the page has scrolled.
        let ticking = false;
        const sync = function () {
            header.classList.toggle('is-scrolled', window.scrollY > 4);
            ticking = false;
        };
        window.addEventListener('scroll', function () {
            if (!ticking) { ticking = true; requestAnimationFrame(sync); }
        }, { passive: true });
        sync();
    })();
</script>
