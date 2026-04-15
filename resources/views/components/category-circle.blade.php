{{--
    resources/views/components/category-circle.blade.php
    ─────────────────────────────────────────────────────
    A single circular category tile (SHEIN style).

    Usage:
        <x-category-circle :category="$cat" />
        <x-category-circle :category="$cat" size="lg" />

    Props:
        $category  Category   — The category model
        $size      string     — 'sm' | 'md' | 'lg'  (default: 'md')
        $active    bool       — highlight as currently selected
--}}

@props([
    'category',
    'size'   => 'md',
    'active' => false,
])

@php
    $sizes = [
        'sm' => ['circle' => 'w-14 h-14',  'text' => 'text-[10px]', 'wrap' => 'w-16'],
        'md' => ['circle' => 'w-16 h-16',  'text' => 'text-[11px]', 'wrap' => 'w-20'],
        'lg' => ['circle' => 'w-20 h-20',  'text' => 'text-xs',     'wrap' => 'w-24'],
    ];
    $s = $sizes[$size] ?? $sizes['md'];

    $href     = route('products.index', ['category' => $category->slug]);
    $imageUrl = $category->getCategoryImageUrl('thumb');
    $hasImg   = $category->hasImage();
@endphp

<a href="{{ $href }}"
   class="flex flex-col items-center gap-2 flex-shrink-0 {{ $s['wrap'] }} group"
   title="{{ $category->name }}">

    {{-- Circle image ────────────────────────────────────────────── --}}
    <div class="relative {{ $s['circle'] }} rounded-full overflow-hidden flex-shrink-0
                ring-2 transition-all duration-200
                {{ $active
                    ? 'ring-[var(--brand-color,#0ea5e9)] ring-offset-2'
                    : 'ring-transparent group-hover:ring-[var(--brand-color,#0ea5e9)] group-hover:ring-offset-1' }}">

        {{-- Shimmer skeleton (hidden once image loads) --}}
        <div class="shimmer absolute inset-0 z-0 rounded-full"
             id="cat-sk-{{ $category->id }}"></div>

        @if($hasImg)
        <img
            src="{{ $imageUrl }}"
            alt="{{ $category->name }}"
            loading="lazy"
            class="w-full h-full object-cover relative z-10 transition-transform duration-300 group-hover:scale-110"
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
    <span class="{{ $s['text'] }} font-semibold text-center leading-snug
                 transition-colors duration-150
                 {{ $active ? 'text-[var(--brand-color,#0ea5e9)]' : 'text-gray-700 group-hover:text-[var(--brand-color,#0ea5e9)]' }}"
          style="word-break: break-word; max-width: 100%">
        {{ $category->name }}
    </span>

</a>