@props([
    'products' => [],
    'wishlisted' => [],
    /** "grid" tiles and wraps; "rail" scrolls horizontally on small screens. */
    'variant' => 'grid',
    /** How many leading cards load eagerly (above the fold). */
    'eagerCount' => 4,
])

<div {{ $attributes->merge(['class' => $variant === 'rail' ? 'sf-rail' : 'sf-grid']) }}>
    @foreach($products as $i => $product)
        <x-sf.product-card :product="$product"
                           :wishlisted="$wishlisted"
                           :eager="$i < $eagerCount" />
    @endforeach
</div>
