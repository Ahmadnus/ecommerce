@props([
    /** @var \App\Models\Product */
    'product',
    /** Product ids in the current user's wishlist, from the controller. */
    'wishlisted' => [],
    /** Force the wishlist heart on/off; null follows the card_show_wishlist token. */
    'showWishlist' => null,
    /** Show the add-to-cart action row. */
    'showAction' => true,
    /** Eager-load the image instead of lazy-loading it (above-the-fold rows). */
    'eager' => false,
])

@php
    $tk = $themeTokens ?? \App\Helpers\StorefrontThemeHelper::all();

    // The reference storefront keeps the card bare — image, centred title,
    // centred price, one button. The heart and the discount pill are opt-in
    // from /admin/settings rather than hard-coded either way.
    $withWishlist = $showWishlist ?? (($tk['card_show_wishlist'] ?? 'off') === 'on');
    $withBadges   = ($tk['card_show_badges'] ?? 'off') === 'on';
    $openInModal  = ($tk['product_open_mode'] ?? 'modal') === 'modal';

    $isWishlisted = in_array($product->id, is_array($wishlisted) ? $wishlisted : [], true);

    // Stock lives entirely on variants in this app: Product::$total_stock is
    // the sum of its active variants' quantities, and CartService::validateAdd
    // refuses anything with no stock. So in_stock is the single source of
    // truth for availability here, whatever the variant shape.
    $soldOut = ! $product->in_stock;

    // More than one active variant means there is something to choose, which
    // neither the card nor the quick-view popup has room for — those go to the
    // full product page instead.
    $activeVariants = $product->relationLoaded('variants')
        ? $product->variants
        : $product->variants()->where('is_active', true)->get();

    $needsOptions = $activeVariants->count() > 1;

    $url   = route('products.show', $product->slug);
    $image = $product->getFirstMediaUrl('products')
        ?: $product->getFirstMediaUrl('main')
        ?: ($product->image_url ?: asset('images/placeholder.jpg'));
@endphp

<article class="sf-card">
    {{-- A real href to the product page, so the card still works with
         JavaScript off, stays crawlable, and can be opened in a new tab.
         With quick-view enabled, JS intercepts the click and opens the popup
         instead — see partials/quick-view.blade.php. --}}
    <a href="{{ $url }}"
       class="sf-card__media"
       tabindex="-1"
       aria-hidden="true"
       @if($openInModal) data-quickview="{{ $product->slug }}" @endif>
        <img src="{{ $image }}"
             alt=""
             loading="{{ $eager ? 'eager' : 'lazy' }}"
             decoding="async">

        @if($withBadges)
            <div class="sf-card__badges">
                @if($product->is_on_sale)
                    <span class="sf-badge">-{{ $product->discount_percentage }}%</span>
                @endif
                @if($soldOut)
                    <span class="sf-badge sf-badge--muted">{{ __('app.out_of_stock') }}</span>
                @endif
            </div>
        @endif
    </a>

    @if($withWishlist)
        {{-- Hooks into the global toggleWishlist() in layouts/app.blade.php:
             it swaps the two [data-heart] icons and syncs .wishlist-count. --}}
        <button type="button"
                class="sf-card__wish favorite-btn {{ $isWishlisted ? 'is-active' : '' }}"
                data-product-id="{{ $product->id }}"
                data-wishlisted="{{ $isWishlisted ? 'true' : 'false' }}"
                onclick="toggleWishlist(this)"
                aria-label="{{ $isWishlisted ? __('app.remove_from_wishlist') : __('app.add_to_wishlist') }}">
            <svg data-heart="outline" class="{{ $isWishlisted ? 'hidden' : '' }}"
                 fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
            <svg data-heart="filled" class="{{ $isWishlisted ? '' : 'hidden' }}"
                 fill="currentColor" viewBox="0 0 24 24">
                <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
        </button>
    @endif

    <div class="sf-card__body">
        <h3 class="sf-card__title">
            <a href="{{ $url }}"
               @if($openInModal) data-quickview="{{ $product->slug }}" @endif>{{ $product->name }}</a>
        </h3>

        <div class="sf-card__prices">
            @if($product->is_on_sale)
                <x-price :amount="$product->discount_price" class="sf-price" />
                <x-price :amount="$product->base_price" class="sf-price--old" />
            @else
                <x-price :amount="$product->base_price" class="sf-price" />
            @endif
        </div>

        @if($showAction)
            @if($soldOut)
                <button type="button" class="sf-btn sf-card__action" disabled>
                    {{ __('app.out_of_stock') }}
                </button>
            @elseif($needsOptions)
                <a href="{{ $url }}" class="sf-btn sf-card__action">
                    {{ __('app.choose_options') }}
                </a>
            @else
                <button type="button"
                        class="sf-btn sf-card__action"
                        data-add-to-cart
                        onclick="Cart.add({{ $product->id }}, 1, this)">
                    <svg class="sf-card__action-icon" fill="none" stroke="currentColor"
                         stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17M17 17a2 2 0 100 4 2 2 0 000-4zM9 19a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span>{{ __('app.add_to_cart') }}</span>
                </button>
            @endif
        @endif
    </div>
</article>
