{{--
    products/partials/quick-view.blade.php

    Body of the product quick-view popup, fetched over AJAX by the script in
    partials/quick-view.blade.php and injected into the modal shell.

    Mirrors the reference store's popup: a sticky header naming the product, a
    scrollable column of the product's images, and a sticky footer holding the
    price, a quantity stepper, a WhatsApp button and the add-to-cart action.

    Anything needing a choice (a product with several variants) is deliberately
    sent to the full product page instead, so the cart never receives an item
    with unselected options.
--}}

@php
    $images = collect();
    foreach ($product->getMedia('products') as $m) { $images->push($m->getUrl()); }
    if ($images->isEmpty()) {
        foreach ($product->getMedia('main') as $m) { $images->push($m->getUrl()); }
    }
    if ($images->isEmpty() && $product->image_url) { $images->push($product->image_url); }
    if ($images->isEmpty()) { $images->push(asset('images/placeholder.jpg')); }

    $activeVariants = $product->variants->where('is_active', true);
    $needsOptions   = $activeVariants->count() > 1;
    $soldOut        = ! $product->in_stock;
    $maxQty         = max(1, (int) $product->total_stock);

    $whatsapp = \App\Models\SocialLink::where('is_active', true)
        ->where('is_floating', true)
        ->first()?->whatsapp_number;
@endphp

<div class="sf-modal__head">
    <p class="sf-modal__title">
        <span class="sf-modal__eyebrow">{{ __('app.product_details') }}</span>
        <span class="sf-modal__name">{{ $product->name }}</span>
    </p>

    <button type="button" class="sf-modal__close" data-quickview-close
            aria-label="{{ __('app.close') }}">
        <svg fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" d="M6 6l12 12M18 6L6 18"/>
        </svg>
    </button>
</div>

<div class="sf-modal__body">
    {{-- Every image stacked in one scrolling column, as the reference does,
         rather than a thumbnail gallery. --}}
    @foreach($images as $i => $img)
        <img src="{{ $img }}"
             alt="{{ $i === 0 ? $product->name : '' }}"
             class="sf-modal__image"
             loading="{{ $i === 0 ? 'eager' : 'lazy' }}"
             decoding="async">
    @endforeach

    @if($product->short_description || $product->description)
        <div class="sf-modal__text">
            {{ $product->short_description ?: $product->description }}
        </div>
    @endif
</div>

<div class="sf-modal__foot">
    <div class="sf-modal__row">
        <span class="sf-modal__price">
            @if($product->is_on_sale)
                <x-price :amount="$product->discount_price" />
                <x-price :amount="$product->base_price" class="sf-price--old" />
            @else
                <x-price :amount="$product->base_price" />
            @endif
        </span>

        @unless($soldOut || $needsOptions)
            <span class="sf-modal__qty-label">{{ __('app.quantity') }}:</span>

            <div class="sf-qty sf-qty--solid" dir="ltr">
                <button type="button" class="sf-qty__btn" data-qty-step="-1"
                        aria-label="{{ __('app.decrease_quantity') }}">&minus;</button>
                <input type="number" class="sf-qty__input" id="sf-qv-qty"
                       value="1" min="1" max="{{ $maxQty }}"
                       aria-label="{{ __('app.quantity') }}">
                <button type="button" class="sf-qty__btn" data-qty-step="1"
                        aria-label="{{ __('app.increase_quantity') }}">+</button>
            </div>
        @endunless
    </div>

    <div class="sf-modal__row sf-modal__row--actions">
        @if($soldOut)
            <button type="button" class="sf-btn sf-btn--primary sf-btn--lg" style="flex:1" disabled>
                {{ __('app.out_of_stock') }}
            </button>
        @elseif($needsOptions)
            {{-- Several variants: the popup has nowhere to pick size/colour, so
                 send the shopper to the full page rather than guess for them. --}}
            <a href="{{ route('products.show', $product->slug) }}"
               class="sf-btn sf-btn--primary sf-btn--lg" style="flex:1">
                {{ __('app.choose_options') }}
            </a>
        @else
            <button type="button"
                    class="sf-btn sf-btn--primary sf-btn--lg"
                    style="flex:1"
                    data-quickview-add="{{ $product->id }}">
                <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17M17 17a2 2 0 100 4 2 2 0 000-4zM9 19a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                {{ __('app.add_to_cart') }}
            </button>
        @endif
        @if($whatsapp)
            <a href="https://wa.me/{{ preg_replace('/\D+/', '', $whatsapp) }}?text={{ urlencode($product->name . ' — ' . route('products.show', $product->slug)) }}"
               target="_blank" rel="noopener noreferrer"
               class="sf-wa-btn" aria-label="WhatsApp">
                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.174.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.29.173-1.414-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884a9.82 9.82 0 016.988 2.898 9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                </svg>
            </a>
        @endif

    </div>
</div>
