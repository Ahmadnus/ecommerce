{{--
    resources/views/components/category-circle.blade.php
    ─────────────────────────────────────────────────────
    A single category tile in the storefront design system.

    The component name is kept for backwards compatibility — several views
    already render <x-category-circle :category="$cat" /> — but the markup now
    uses the .sf-cat-tile primitives from public/css/storefront.css, so its
    radius, border, surface and hover treatment all come from the theme
    tokens rather than being baked in here.

    Props:
        $category  Category  — the category model
        $active    bool      — highlight as currently selected
--}}

@props([
    'category',
    'size'   => 'md',
    'active' => false,
])

@php
    $href     = route('products.index', ['category' => $category->slug]);
    $imageUrl = $category->getCategoryImageUrl('thumb');
@endphp

<a href="{{ $href }}"
   class="sf-cat-tile group"
   @if($active) aria-current="page" @endif
   title="{{ $category->name }}">

    <div class="sf-cat-tile__media"
         @if($active) style="border-color: var(--brand-color)" @endif>
        {{-- getCategoryImageUrl() always returns something: a real upload or
             an inline SVG data-URI placeholder, so there is no broken-image
             state and no second request to guard against. --}}
        <img src="{{ $imageUrl }}"
             alt=""
             loading="lazy"
             decoding="async">
    </div>

    <span class="sf-cat-tile__name">{{ $category->name }}</span>
</a>
