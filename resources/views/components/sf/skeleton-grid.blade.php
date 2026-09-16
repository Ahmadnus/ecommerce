@props(['count' => 8, 'variant' => 'grid'])

{{-- Placeholder grid matching the real card geometry, so swapping skeletons
     for products causes no layout shift. --}}
<div {{ $attributes->merge(['class' => $variant === 'rail' ? 'sf-rail' : 'sf-grid']) }}
     aria-hidden="true">
    @for($i = 0; $i < $count; $i++)
        <div class="sf-card">
            <div class="sf-card__media sf-skeleton" style="border-radius:0"></div>
            <div class="sf-card__body">
                <div class="sf-skeleton" style="height:.9rem;width:90%"></div>
                <div class="sf-skeleton" style="height:.9rem;width:60%"></div>
                <div class="sf-skeleton" style="height:1.1rem;width:40%;margin-block-start:auto"></div>
                <div class="sf-skeleton" style="height:2.4rem;width:100%"></div>
            </div>
        </div>
    @endfor
</div>
