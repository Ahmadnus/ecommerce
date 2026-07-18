{{--
    products/_paginated-grid.blade.php — the paginated "all products" matrix
    for category / search / sort pages, with its friendly empty state.
    Needs: $isRtl, $products, $wishlistedIds.
--}}
@if($products->isEmpty())
<div class="flex flex-col items-center justify-center py-24 text-center">
    <div class="w-16 h-16 bg-white rounded-2xl border border-gray-100 flex items-center justify-center mb-4 shadow-sm">
        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
    </div>
    <p class="text-gray-500 font-semibold text-sm">{{ __('app.no_products') }}</p>
    <a href="{{ route('products.index') }}" class="mt-3 text-xs font-bold hover:underline" style="color:var(--brand)">
        {{ __('app.show_all') }}
    </a>
</div>

@else
<div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-x-8 md:gap-x-6 lg:gap-x-5 xl:gap-x-6 gap-y-10 mt-[30px] px-[10px]">
    @foreach($products as $i => $product)
    @php $isWishlisted = in_array($product->id, $wishlistedIds ?? []); @endphp
    <div class="pcard flex flex-col w-full reveal group"
         style="--i: {{ min($i % 6, 5) }}"
         onclick="window.location='{{ route('products.show', $product->slug) }}'">

        {{-- Image box — the wishlist heart is the ONLY intentional overlay --}}
        <div class="relative overflow-hidden aspect-square rounded-[2px]">
            <div class="shimmer absolute inset-0 z-0" id="sk-{{ $product->id }}"></div>
            <img src="{{ $product->getFirstMediaUrl('products') ?: asset('images/placeholder.jpg') }}"
                 alt="{{ $product->name }}"
                 class="pcard-img absolute inset-0 w-full h-full object-cover z-10
                        transition-opacity duration-300 group-hover:opacity-80"
                 loading="lazy"
                 onload="document.getElementById('sk-{{ $product->id }}').style.display='none'">

            <button type="button" class="favorite-btn absolute top-3 {{ $isRtl ? 'left-3' : 'right-3' }} z-20"
                    data-product-id="{{ $product->id }}"
                    data-wishlisted="{{ $isWishlisted ? 'true' : 'false' }}"
                    onclick="event.stopPropagation(); toggleWishlist(this)"
                    aria-label="{{ $isWishlisted ? __('app.remove_from_wishlist') : __('app.add_to_wishlist') }}">
                <svg data-heart="outline"
                     class="w-5 h-5 text-white drop-shadow-sm transition-all duration-200 {{ $isWishlisted ? 'hidden' : 'block' }}"
                     fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                <svg data-heart="filled"
                     class="w-5 h-5 text-red-500 drop-shadow-sm transition-all duration-200 {{ $isWishlisted ? 'block' : 'hidden' }}"
                     fill="currentColor" viewBox="0 0 24 24">
                    <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </button>
        </div>

        {{-- Info — strict in-flow stack (title → price), nothing absolute --}}
        <div class="pt-1.5 flex flex-col gap-0.5 text-start">
            <p class="text-xs font-medium line-clamp-2 leading-snug"
               style="color: var(--text-product-title);">
                {{ $product->name }}
            </p>

            <div class="flex items-center gap-2">
                @if($product->discount_price && $product->discount_price < $product->base_price)
                <x-price :amount="$product->discount_price"
                         class="price-val text-xs tracking-wide" />
                <x-price :amount="$product->base_price"
                         class="price-original text-xs tracking-wide" />
                @else
                <x-price :amount="$product->base_price"
                         class="price-val text-xs tracking-wide" />
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif
