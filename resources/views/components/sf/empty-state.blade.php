@props([
    'title' => null,
    'text' => null,
    'actionUrl' => null,
    'actionLabel' => null,
])

<div {{ $attributes->merge(['class' => 'sf-state']) }}>
    <div class="sf-state__icon">
        {{-- Callers may pass a custom glyph; this is the neutral default. --}}
        @if(isset($icon))
            {{ $icon }}
        @else
            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
        @endif
    </div>

    @if($title)<p class="sf-state__title">{{ $title }}</p>@endif
    @if($text)<p class="sf-state__text">{{ $text }}</p>@endif

    {{ $slot }}

    @if($actionUrl)
        <a href="{{ $actionUrl }}" class="sf-btn sf-btn--primary">
            {{ $actionLabel ?? __('app.show_all') }}
        </a>
    @endif
</div>
