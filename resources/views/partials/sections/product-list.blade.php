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
        <div class="featured-card"
             onclick="window.location='{{ route('products.show', $sp->slug) }}'">
            <div class="relative overflow-hidden aspect-square rounded-[2px]">
                <div class="shimmer absolute inset-0 z-0" id="fsk-{{ $sp->id }}-{{ $section->id }}"></div>
                <img src="{{ $sp->getFirstMediaUrl('products') ?: ($sp->image_url ?? 'https://picsum.photos/seed/'.$sp->id.'/300/390') }}"
                     alt="{{ $sp->name }}"
                     class="fc-img absolute inset-0 w-full h-full object-cover z-10"
                     loading="lazy"
                     onload="this.previousElementSibling.style.display='none'">
            </div>

            <div class="pt-2 text-start">
                <p class="text-xs font-medium line-clamp-2 leading-snug">{{ $sp->name }}</p>
                <div class="flex items-center gap-2">
                    @if($sp->is_on_sale)
                    <x-price :amount="$sp->discount_price" class="text-xs tracking-wide" style="color:var(--sale-red)" />
                    <x-price :amount="$sp->base_price" class="text-xs text-gray-400 line-through tracking-wide" />
                    @else
                    <x-price :amount="$sp->base_price" class="text-xs text-gray-900 tracking-wide" />
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</section>
@endif