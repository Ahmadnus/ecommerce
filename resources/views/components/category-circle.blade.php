{{--
    resources/views/components/category-circle.blade.php
    ─────────────────────────────────────────────────────
    A single square category tile (luxury minimalist grid style).

    Usage:
        <x-category-circle :category="$cat" />

    Props:
        $category  Category   — The category model
        $active    bool       — highlight as currently selected
--}}

@props([
    'category',
    'size'   => 'md',
    'active' => false,
])

@php
    $href     = route('products.index', ['category' => $category->slug]);
    $imageUrl = $category->getCategoryImageUrl('thumb');
    $hasImg   = $category->hasImage();
@endphp

<a href="{{ $href }}"
   class="flex flex-col w-full group"
   title="{{ $category->name }}">

    {{-- Tall portrait image ───────────────────────────────────────── --}}
    <div class="relative w-full aspect-[3/4] rounded-[2px] overflow-hidden">

        {{-- Shimmer skeleton (hidden once image loads) --}}
        <div class="shimmer absolute inset-0 z-0 rounded-[2px]"
             id="cat-sk-{{ $category->id }}"></div>

        @if($hasImg)
        <img
            src="{{ $imageUrl }}"
            alt="{{ $category->name }}"
            loading="lazy"
            class="w-full h-full object-cover relative z-10 transition-opacity duration-300 group-hover:opacity-80"
            onload="const el=document.getElementById('cat-sk-{{ $category->id }}');if(el)el.remove();">
        @else
        {{-- SVG data-URI placeholder — no extra request ────────── --}}
        <img
            src="{{ $imageUrl }}"
            alt="{{ $category->name }}"
            class="w-full h-full object-cover relative z-10">
        @endif
    </div>

    {{-- Label ─────────────────────────────────────────────────────── --}}
    <div class="pt-1.5 flex flex-col gap-0.5 text-start">
        <p class="text-xs font-medium line-clamp-2 leading-snug"
           style="color: var(--text-product-title);">
            {{ $category->name }}
        </p>
    </div>

</a>
