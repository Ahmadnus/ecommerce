{{--
    partials/sections/product-list.blade.php
    ─────────────────────────────────────────────────────────────────────────────
    Receives:
      $section        HomeSection model instance
      $wishlistedIds  array of product IDs the current user has wishlisted
      $sectionProducts  EloquentCollection resolved by the controller
    ─────────────────────────────────────────────────────────────────────────────
--}}
@if($sectionProducts->isNotEmpty())
<section class="mb-8">
    {{-- Section header --}}
    <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-2">
            <span class="w-1 h-5 rounded-full flex-shrink-0" style="background:var(--brand)"></span>
            <h2 class="font-display text-base md:text-lg font-bold text-gray-900">{{ $section->title }}</h2>
        </div>
        <a href="{{ route('products.index', ['sort' => $section->cfg('source', 'featured')]) }}"
           class="text-xs font-bold hover:underline" style="color:var(--brand)">
            عرض الكل ←
        </a>
    </div>

    {{-- Horizontal scrollable product shelf --}}
    <div class="featured-list">
        @foreach($sectionProducts as $sp)
        @php $spWishlisted = in_array($sp->id, $wishlistedIds ?? []); @endphp
        <div class="featured-card group"
             onclick="window.location='{{ route('products.show', $sp->slug) }}'">
            <div class="relative overflow-hidden bg-gray-100" style="padding-top:126%">
                <div class="shimmer absolute inset-0 z-0" id="fsk-{{ $sp->id }}-{{ $section->id }}"></div>
                <img src="{{ $sp->getFirstMediaUrl('products') ?: ($sp->image_url ?? 'https://picsum.photos/seed/'.$sp->id.'/300/390') }}"
                     alt="{{ $sp->name }}"
                     class="fc-img absolute inset-0 w-full h-full object-cover z-10"
                     loading="lazy"
                     onload="this.previousElementSibling.style.display='none'">

                @if($sp->is_on_sale)
                <div class="fc-ribbon">{{ $sp->discount_percentage }}% OFF</div>
                @endif

                {{-- Share --}}
                <div class="absolute top-2 left-2 z-20" onclick="event.stopPropagation()">
                    <button type="button" class="share-btn"
                            onclick="shareProduct('{{ route('products.show', $sp->slug) }}', '{{ addslashes($sp->name) }}')"
                            title="مشاركة">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                        </svg>
                    </button>
                </div>

                {{-- Wishlist --}}
                <div class="absolute bottom-2 left-2 z-20" onclick="event.stopPropagation()">
                    @auth
                    <button type="button" class="heart-btn wishlist-btn"
                            data-product-id="{{ $sp->id }}"
                            data-wishlisted="{{ $spWishlisted ? 'true' : 'false' }}"
                            onclick="toggleWishlist(this)">
                        <svg data-heart="outline" class="{{ $spWishlisted ? 'hidden' : '' }}" fill="none" stroke="#888" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        <svg data-heart="filled" class="{{ $spWishlisted ? '' : 'hidden' }}" fill="#ff3366" stroke="#ff3366" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </button>
                    @endauth
                </div>
            </div>

            <div class="px-2.5 pt-2 pb-3">
                <p class="text-xs font-semibold text-gray-800 line-clamp-2 leading-snug mb-1">{{ $sp->name }}</p>
                <div class="flex items-center gap-1.5">
                    @if($sp->is_on_sale)
                    <x-price :amount="$sp->discount_price" class="text-sm font-black leading-none tabular-nums" style="color:var(--sale-red)" />
                    <x-price :amount="$sp->base_price" class="text-[10px] text-gray-400 line-through tabular-nums" />
                    @else
                    <x-price :amount="$sp->base_price" class="text-sm font-black text-gray-900 leading-none tabular-nums" />
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</section>
@endif