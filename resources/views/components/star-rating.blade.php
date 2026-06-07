{{--
    resources/views/components/star-rating.blade.php

    Usage:
      <x-star-rating :rating="4.5" size="sm" />   → small stars (product cards)
      <x-star-rating :rating="3"   size="md" />   → medium (product page)
      <x-star-rating :rating="5"   size="lg" />   → large

    Props:
      $rating  — numeric 0–5 (supports decimals for half-stars)
      $size    — 'sm' | 'md' | 'lg'
      $count   — optional review count to show alongside stars
--}}
@props([
    'rating' => 0,
    'size'   => 'md',
    'count'  => null,
])

@php
    $rating  = (float) $rating;
    $sizeMap = ['sm' => 'w-3 h-3', 'md' => 'w-4 h-4', 'lg' => 'w-5 h-5'];
    $svgSize = $sizeMap[$size] ?? $sizeMap['md'];
    $full    = (int) floor($rating);
    $half    = ($rating - $full) >= 0.5;
    $empty   = 5 - $full - ($half ? 1 : 0);
@endphp

<span class="inline-flex items-center gap-0.5" aria-label="تقييم {{ number_format($rating, 1) }} من 5">

    {{-- Full stars --}}
    @for($i = 0; $i < $full; $i++)
    <svg class="{{ $svgSize }} text-amber-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
    </svg>
    @endfor

    {{-- Half star --}}
    @if($half)
    <svg class="{{ $svgSize }} text-amber-400 flex-shrink-0" viewBox="0 0 20 20">
        <defs>
            <linearGradient id="half-grad-{{ rand(1000,9999) }}" x1="0" x2="1" y1="0" y2="0">
                <stop offset="50%" stop-color="currentColor"/>
                <stop offset="50%" stop-color="#d1d5db"/>
            </linearGradient>
        </defs>
        <path fill="url(#half-grad-{{ rand(1000,9999) }})"
              d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
    </svg>
    @endif

    {{-- Empty stars --}}
    @for($i = 0; $i < $empty; $i++)
    <svg class="{{ $svgSize }} text-gray-300 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
    </svg>
    @endfor

    @if($count !== null)
    <span class="text-xs text-gray-400 font-medium ms-1">({{ $count }})</span>
    @endif

</span>