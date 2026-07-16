{{--
    partials/sections/top-hero-media.blade.php
    ─────────────────────────────────────────────────────────────────────────────
    Full-bleed cinematic banner (image or video) for a given page slot.
    Renders nothing if no active TopHeroMedia record exists for that position.

    Usage: @include('partials.sections.top-hero-media', ['position' => 'top'])
--}}
@php
    $position = $position ?? 'top';
    $heroMedia = \App\Models\TopHeroMedia::where('is_active', true)
        ->where('position', $position)
        ->orderBy('sort_order')
        ->first();
@endphp

@if($heroMedia)
    @php $heroMediaUrl = $heroMedia->getFirstMediaUrl('hero_media'); @endphp

    @if($heroMediaUrl)
        <x-hero-media-banner
            :media_type="$heroMedia->type"
            :file_path="$heroMediaUrl"
            :link_url="$heroMedia->link_url"
            :is_rtl="$isRtl ?? false"
            :height="$height ?? 'h-screen min-h-screen'"
        />
    @endif
@endif
