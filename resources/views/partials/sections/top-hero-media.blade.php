{{--
    partials/sections/top-hero-media.blade.php
    ─────────────────────────────────────────────────────────────────────────────
    Full-bleed cinematic hero (image or video) shown above everything else on
    the homepage. Renders nothing if no active TopHeroMedia record exists.
--}}
@php
    $topHero = \App\Models\TopHeroMedia::where('is_active', true)->orderBy('sort_order')->first();
@endphp

@if($topHero)
    @php
        $topHeroMediaUrl = $topHero->getFirstMediaUrl('hero_media');
        $topHeroTag = $topHero->link_url ? 'a' : 'div';
    @endphp

    @if($topHeroMediaUrl)
    <{{ $topHeroTag }}
        @if($topHero->link_url) href="{{ $topHero->link_url }}" @endif
        class="block w-full h-[50vh] md:h-[65vh] lg:h-[85vh] overflow-hidden">

        @if($topHero->type === 'video')
            <video src="{{ $topHeroMediaUrl }}"
                   class="w-full h-full object-cover"
                   autoplay loop muted playsinline controlslist="nodownload">
            </video>
        @else
            <img src="{{ $topHeroMediaUrl }}"
                 alt="Hero"
                 class="w-full h-full object-cover">
        @endif
    </{{ $topHeroTag }}>
    @endif
@endif
