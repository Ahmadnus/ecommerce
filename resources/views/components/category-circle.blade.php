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

    {{-- Square image ────────────────────────────────────────────── --}}
    <div class="relative w-full aspect-square rounded-[2px] overflow-hidden bg-gray-50">

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
    <span class="mt-2 text-xs uppercase tracking-wider text-start leading-snug
                 transition-colors duration-150
                 {{ $active ? 'text-[var(--brand-color,#0ea5e9)] font-semibold' : 'text-gray-700 group-hover:text-gray-900' }}">
        {{ $category->name }}
    </span>

</a>
