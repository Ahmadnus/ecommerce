{{--
    components/homepage-section.blade.php
    ─────────────────────────────────────────────────────────────────────────────
    Block/Cube Builder dispatcher. Renders a single HomepageSection cube
    according to its section_type — one sort_order-driven loop of fully
    interchangeable blocks, positioned anywhere by the admin:

      hero_banner                        → full-width premium promo banner
      portrait_media                     → tall magazine-style media cube
      custom_media / custom_image        → standalone image/video space-breaker
      banner (legacy)                    → stacked media + text
          all of the above → unified <x-homepage-media-block>: admin-driven
          aspect ratio (full / landscape / portrait / square), text position
          (overlay center|left|right or stacked below), image OR HTML5 video
          (uploaded file or external video_url)
      pure_text_cta / text_block         → luxury heading + paragraph + CTA
      categories_grid                    → Didone-styled category card grid
      product_grid                       → product matrix by product_source

    Every cube is wrapped in a layout shell carrying the admin-chosen
    background_color and padding_settings so blocks can breathe (or sit
    flush) independently. Unknown/legacy types fall back to the media block
    (safe default: whatever media/text the row has still renders).

    Usage: <x-homepage-section :section="$section" :is-rtl="$isRtl"
                               :wishlisted-ids="$wishlistedIds" />
--}}
@props([
    'section',
    'isRtl'         => false,
    'wishlistedIds' => [],
])

@php
    $shellClasses = trim('hb-cube ' . ($section->paddingClasses() ?? ''));
    $shellStyle   = $section->background_color ? 'background:' . $section->background_color . ';' : '';
@endphp

@if($section->needsGoogleFont())
    @once
        @push('head')
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,opsz,wght@0,6..96,400;0,6..96,700;1,6..96,400&display=swap" rel="stylesheet">
        @endpush
    @endonce
@endif

<div @class([$shellClasses, 'rounded-2xl' => $section->background_color && ! $section->isFullScreenMedia()])
     @if($shellStyle) style="{{ $shellStyle }}" @endif>
@switch($section->section_type)

    {{-- ── CATEGORIES GRID — Didone category cards, titles above images ──── --}}
    @case(\App\Models\HomepageSection::TYPE_CATEGORIES_GRID)
        @php
            // Pre-resolved once per request by ProductService; the query here
            // is only a fallback for renders outside the homepage pipeline.
            $catGridItems = $section->relationLoaded('resolvedCategories')
                ? $section->resolvedCategories
                : \App\Models\Category::active()->roots()
                    ->with(['allActiveChildren', 'media'])
                    ->orderBy('sort_order')->take(20)->get();
        @endphp
        @if($catGridItems->isNotEmpty())
        <div class="relative overflow-hidden pt-2">
            <x-category-grid
                :categories="$catGridItems"
                :current="null"
                :title="$section->title ?? ''"
                :show-all="true" />
        </div>
        @endif
        @break

    {{-- ── PRODUCT GRID — uniform product cards for the chosen source ────── --}}
    @case(\App\Models\HomepageSection::TYPE_PRODUCT_GRID)
        <x-homepage-product-grid
            :section="$section"
            :products="$section->relationLoaded('resolvedProducts') ? $section->resolvedProducts : $section->resolveProducts()"
            :wishlisted-ids="$wishlistedIds"
            :is-rtl="$isRtl" />
        @break

    {{-- ── PURE TEXT / CTA — luxury heading + paragraph + standalone CTA ─── --}}
    @case(\App\Models\HomepageSection::TYPE_PURE_TEXT_CTA)
    @case(\App\Models\HomepageSection::TYPE_TEXT_BLOCK)
        <x-home-cta-block
            :title="$section->title"
            :text="$section->paragraph"
            :button_text="$section->button_text"
            :button_url="$section->button_url"
            :title-accent-color="$section->section_title_accent_color"
            :text-color="$section->text_color"
            :button-bg-color="$section->button_bg_color"
            :button-text-color="$section->button_text_color"
            :text-alignment="$section->text_alignment"
            :title-font-family="$section->titleFontFamilyCss()"
            :paragraph-font-family="$section->paragraphFontFamilyCss()" />
        @break

    {{-- ── MEDIA CUBES + SAFE DEFAULT ─────────────────────────────────────
         hero_banner, portrait_media, custom_media (+ legacy banner /
         custom_image, and any unknown type) all render through the unified
         media block, driven by aspect_ratio, text_position, and the
         image/video (file or video_url) on the row. ─────────────────────── --}}
    @case(\App\Models\HomepageSection::TYPE_HERO_BANNER)
    @case(\App\Models\HomepageSection::TYPE_PORTRAIT_MEDIA)
    @case(\App\Models\HomepageSection::TYPE_CUSTOM_MEDIA)
    @case(\App\Models\HomepageSection::TYPE_BANNER)
    @case(\App\Models\HomepageSection::TYPE_CUSTOM_IMAGE)
    @default
        <x-homepage-media-block :section="$section" :is-rtl="$isRtl" />
@endswitch
</div>
