@props([
    'title' => null,
    'link' => null,
    'linkLabel' => null,
])

<section {{ $attributes->merge(['class' => 'sf-section']) }}>
    @if($title)
        <div class="sf-section__head">
            <h2 class="sf-section__title">{{ $title }}</h2>
            @if($link)
                <a href="{{ $link }}" class="sf-section__link">
                    {{ $linkLabel ?? __('app.view_all') }}
                </a>
            @endif
        </div>
    @endif

    {{ $slot }}
</section>
