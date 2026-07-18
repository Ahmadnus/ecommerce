{{--
    components/homepage-product-grid.blade.php
    ─────────────────────────────────────────────────────────────────────────────
    Uniform, elegant product-card grid for a dynamic homepage "product_grid"
    section. Reuses the exact same card styling as the storefront featured
    list so every grid on the page reads as one consistent system.

    Props:
      - section       : HomepageSection (for title / accent color / fonts)
      - products      : Collection of Product models (pre-resolved by controller)
      - wishlistedIds : array of product ids in the current user's wishlist
      - isRtl         : bool
--}}
@props([
    'section',
    'products'      => null,
    'wishlistedIds' => [],
    'isRtl'         => false,
])

@php
    $products = $products ?? collect();
    $titleFont = $section->titleFontFamilyCss();
    $accent    = $section->section_title_accent_color;
@endphp

@if($products->isNotEmpty())
<section class="mb-8">
    @if($section->title)
    <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-2">
            <span class="w-1 h-5 rounded-full" style="background:var(--brand)"></span>
            <h2 class="font-display text-base md:text-lg font-bold {{ $titleFont ? 'font-luxurySerif' : '' }}"
                @if($accent || $titleFont)
                style="{{ $accent ? 'color:'.$accent.' !important;' : '' }}{{ $titleFont ? 'font-family:'.$titleFont.' !important;' : '' }}"
                @endif>
                {{ $section->title }}
            </h2>
        </div>
    </div>
    @endif

    <div class="featured-list">
        @foreach($products as $sp)
        @php $spWishlisted = in_array($sp->id, $wishlistedIds ?? []); @endphp
        <div class="featured-card flex flex-col w-full group" onclick="window.location='{{ route('products.show', $sp->slug) }}'">
            <div class="relative overflow-hidden aspect-square rounded-[2px]">
                <div class="shimmer absolute inset-0 z-0"></div>
                <img src="{{ $sp->getFirstMediaUrl('main') ?: ($sp->getFirstMediaUrl('products') ?: ($sp->image_url ?? asset('images/placeholder.jpg'))) }}"
                     alt="{{ $sp->name }}"
                     class="fc-img absolute inset-0 w-full h-full object-cover z-10
                            transition-opacity duration-300 group-hover:opacity-80"
                     loading="lazy"
                     onload="this.previousElementSibling.style.display='none'">

                <button type="button" class="favorite-btn absolute top-3 {{ $isRtl ? 'left-3' : 'right-3' }} z-20"
                        data-product-id="{{ $sp->id }}"
                        data-wishlisted="{{ $spWishlisted ? 'true' : 'false' }}"
                        onclick="event.stopPropagation(); toggleWishlist(this)"
                        aria-label="{{ $spWishlisted ? __('app.remove_from_wishlist') : __('app.add_to_wishlist') }}">
                    <svg data-heart="outline"
                         class="w-5 h-5 text-white drop-shadow-sm transition-all duration-200 {{ $spWishlisted ? 'hidden' : 'block' }}"
                         fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                    <svg data-heart="filled"
                         class="w-5 h-5 text-red-500 drop-shadow-sm transition-all duration-200 {{ $spWishlisted ? 'block' : 'hidden' }}"
                         fill="currentColor" viewBox="0 0 24 24">
                        <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </button>
            </div>

            {{-- Card info — a strict vertical stack in normal document flow
                 (image above → title → price → button). Nothing here is
                 absolutely positioned, so nothing can ever drift over the
                 image; the only intentional overlay is the wishlist heart
                 inside the image box above. --}}
            <div class="pt-1.5 flex flex-col gap-1 text-start">
                <p class="text-xs font-medium line-clamp-2 leading-snug" style="color: var(--text-product-title);">
                    {{ $sp->name }}
                </p>
                <div class="flex items-center gap-2">
                    @if($sp->is_on_sale)
                        <x-price :amount="$sp->discount_price" class="price-val text-xs tracking-wide" />
                        <x-price :amount="$sp->base_price" class="price-original text-xs tracking-wide" />
                    @else
                        <x-price :amount="$sp->base_price" class="price-val text-xs tracking-wide" />
                    @endif
                </div>
                {{-- Products may have variants/customizations, so the card CTA
                     routes to the product page where the real add-to-cart
                     (with variant selection) lives. --}}
                <a href="{{ route('products.show', $sp->slug) }}"
                   onclick="event.stopPropagation()"
                   class="mt-1 inline-flex items-center justify-center w-full py-2 text-[11px] font-bold
                          tracking-wide uppercase border transition-colors duration-200
                          hover:text-white"
                   style="border-color: var(--brand); color: var(--brand);"
                   onmouseover="this.style.background='var(--brand)'"
                   onmouseout="this.style.background='transparent'">
                    {{ __('app.add_to_cart') }}
                </a>
            </div>
        </div>
        @endforeach
    </div>
</section>
@endif
