{{--
    components/homepage-section.blade.php
    ─────────────────────────────────────────────────────────────────────────────
    Modular homepage-builder dispatcher. Renders a single HomepageSection
    according to its section_type — one sort_order-driven loop of fully
    interchangeable blocks, positioned anywhere by the admin:

      hero_banner / banner / custom_image
          → unified <x-homepage-media-block>: admin-controlled aspect ratio
            (full / landscape / portrait / square) and text position
            (overlay center|left|right, or stacked below the image)
      categories_grid → Didone-styled category card grid (titles above images)
      product_grid    → uniform product-card matrix by product_source
      text_block      → luxury heading + paragraph + CTA

    Unknown/legacy types fall back to the media block (safe default:
    whatever media/text the row has still renders — never a silent skip).

    Usage: <x-homepage-section :section="$section" :is-rtl="$isRtl"
                               :wishlisted-ids="$wishlistedIds" />
--}}
@props([
    'section',
    'isRtl'         => false,
    'wishlistedIds' => [],
])

@if($section->needsGoogleFont())
    @once
        @push('head')
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,opsz,wght@0,6..96,400;0,6..96,700;1,6..96,400&display=swap" rel="stylesheet">
        @endpush
    @endonce
@endif

@switch($section->section_type)

    {{-- ── CATEGORIES GRID — Didone category cards, titles above images ──── --}}
    @case(\App\Models\HomepageSection::TYPE_CATEGORIES_GRID)
        @php
            $catGridItems = \App\Models\Category::active()->roots()
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

    {{-- ── TEXT BLOCK — luxury heading + paragraph + optional CTA ────────── --}}
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

    {{-- ── MEDIA BLOCKS + SAFE DEFAULT ────────────────────────────────────
         hero_banner, banner (legacy stacked), custom_image, and any
         unknown/legacy type all render through the unified media block,
         driven by the section's aspect_ratio and text_position fields
         (backfilled by migration so legacy rows keep their exact look). ── --}}
    @case(\App\Models\HomepageSection::TYPE_HERO_BANNER)
    @case(\App\Models\HomepageSection::TYPE_BANNER)
    @case(\App\Models\HomepageSection::TYPE_CUSTOM_IMAGE)
    @default
        <x-homepage-media-block :section="$section" :is-rtl="$isRtl" />
@endswitch
