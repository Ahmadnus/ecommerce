{{-- resources/views/components/seo-head.blade.php --}}
@props(['seo' => null])

@php
    $locale = app()->getLocale();
    $fallback = config('app.fallback_locale', 'en');

    $resolve = fn(?object $seo, string $field): string =>
        $seo ? ($seo->getTranslation($field, $locale, false)
             ?: $seo->getTranslation($field, $fallback, false)
             ?: '') : '';

    $title       = $resolve($seo, 'seo_title');
    $desc        = $resolve($seo, 'seo_description');
    $keywords    = $resolve($seo, 'seo_keywords');
    $ogTitle     = $resolve($seo, 'og_title') ?: $title;
    $ogDesc      = $resolve($seo, 'og_description') ?: $desc;
    $twTitle     = $resolve($seo, 'twitter_title') ?: $title;
    $twDesc      = $resolve($seo, 'twitter_description') ?: $desc;

    $ogImageUrl  = $seo?->getFirstMediaUrl('og_image') ?? '';
    $faviconUrl  = $seo?->getFirstMediaUrl('favicon') ?? '';
    $canonical   = $seo?->canonical_url ?? request()->url();
    $robots      = $seo?->robots ?? 'index, follow';
    $twCard      = $seo?->twitter_card ?? 'summary_large_image';
    $ogType      = $seo?->og_type ?? 'website';
@endphp

{{-- Core --}}
@if($title)
<title>{{ $title }}</title>
@endif

@if($desc)
<meta name="description" content="{{ $desc }}">
@endif

@if($keywords)
<meta name="keywords" content="{{ $keywords }}">
@endif

<meta name="robots" content="{{ $robots }}">
<link rel="canonical" href="{{ $canonical }}">

{{-- Favicon --}}
@if($faviconUrl)
<link rel="icon" href="{{ $faviconUrl }}">
@endif

{{-- Open Graph --}}
<meta property="og:type" content="{{ $ogType }}">
<meta property="og:url" content="{{ $canonical }}">
@if($ogTitle)
<meta property="og:title" content="{{ $ogTitle }}">
@endif
@if($ogDesc)
<meta property="og:description" content="{{ $ogDesc }}">
@endif
@if($ogImageUrl)
<meta property="og:image" content="{{ $ogImageUrl }}">
@endif

{{-- Twitter Card --}}
<meta name="twitter:card" content="{{ $twCard }}">
@if($twTitle)
<meta name="twitter:title" content="{{ $twTitle }}">
@endif
@if($twDesc)
<meta name="twitter:description" content="{{ $twDesc }}">
@endif
@if($ogImageUrl)
<meta name="twitter:image" content="{{ $ogImageUrl }}">
@endif