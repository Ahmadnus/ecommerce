{{--
    components/home-cta-block.blade.php
    ─────────────────────────────────────────────────────────────────────────────
    Elegant h1 + p + optional big CTA button block used to break up the
    homepage flow (intro block, divider blocks, footer intro block).

    Props:
      - title              : string
      - text               : string
      - button_text        : string|null
      - button_url         : string|null
      - title_accent_color : string|null  hex — applied to the h1 ONLY. Fully
                              isolated from the site-wide navbar/heading
                              color setting (see note below), falls back to
                              default heading styling when not set.
      - text_color         : string|null  hex — applied to the p only, falls back to default body color
      - button_bg_color    : string|null  hex — CTA button background, falls back to brand color
      - button_text_color  : string|null  hex — CTA button label color, falls back to white
      - text_alignment     : string|null  'left'|'center'|'right' — applied to the
                              title, paragraph, AND the CTA button's position.
                              Falls back to the default centered layout when
                              not set (or set to anything else).
      - titleFontFamily    : string|null  CSS font-family stack (e.g. from
                              HomepageSection::titleFontFamilyCss()) —
                              applied to the h1 title only, falls back to
                              the site's default heading font when not set.
      - paragraphFontFamily: string|null  CSS font-family stack (e.g. from
                              HomepageSection::paragraphFontFamilyCss()) —
                              applied to the p description only, falls back
                              to the site's default body font when not set.

    NOTE on the !important below: resources/views/layouts/app.blade.php
    defines a SITE-WIDE rule `h1, h2 { color: var(--text-heading) !important; }`
    driven by the global "Heading Text Color" setting. A plain inline style
    cannot win against an !important stylesheet rule, so this component's
    h1 color is intentionally also !important — that's what gives the
    per-section title_accent_color true, reliable priority over the global
    heading color, with zero risk of ever inheriting/leaking into it.
--}}
@props([
    'title'             => null,
    'text'              => null,
    'button_text'       => null,
    'button_url'        => null,
    'titleAccentColor'  => null,
    'textColor'         => null,
    'buttonBgColor'     => null,
    'buttonTextColor'   => null,
    'textAlignment'     => null,
    'titleFontFamily'      => null,
    'paragraphFontFamily'  => null,
])

@php
    // Physical (not RTL-logical) alignment, since the admin explicitly picks
    // an absolute left/center/right side regardless of page language.
    // Unknown/empty values safely fall back to the default centered layout.
    $alignmentMap = [
        'left'   => ['section' => 'text-left',   'button' => 'mr-auto ml-0'],
        'center' => ['section' => 'text-center', 'button' => 'mx-auto'],
        'right'  => ['section' => 'text-right',  'button' => 'ml-auto mr-0'],
    ];
    $alignment      = $alignmentMap[$textAlignment] ?? $alignmentMap['center'];
    $sectionAlign   = $alignment['section'];
    $buttonAlign    = $alignment['button'];
@endphp

<section class="home-block reveal {{ $sectionAlign }}">
    @if($title)<h1 @if($titleAccentColor || $titleFontFamily) style="{{ $titleAccentColor ? 'color: '.$titleAccentColor.' !important;' : '' }}{{ $titleFontFamily ? 'font-family: '.$titleFontFamily.';' : '' }}" @endif>{{ $title }}</h1>@endif
    @if($text)<p @if($textColor || $paragraphFontFamily) style="{{ $textColor ? 'color: '.$textColor.';' : '' }}{{ $paragraphFontFamily ? 'font-family: '.$paragraphFontFamily.';' : '' }}" @endif>{{ $text }}</p>@endif
    {{-- Button renders whenever button_text is present, even if the admin
         left button_url empty — falls back to "#" so the CTA never
         silently disappears. Custom colors apply via inline style with a
         safe fallback to the default .home-cta-btn CSS (brand bg / white text)
         when no custom color was saved. The button's own alignment classes
         handle its position on mobile (block + margin); on desktop it
         becomes inline-flex, so it also inherits the section's text-align. --}}
    @if($button_text)
        <a href="{{ $button_url ?: '#' }}"
           @if($buttonBgColor || $buttonTextColor)
           style="{{ $buttonBgColor ? 'background:' . $buttonBgColor . ' !important;' : '' }}{{ $buttonTextColor ? 'color:' . $buttonTextColor . ' !important;' : '' }}"
           @endif
           class="home-cta-btn block w-[90%] {{ $buttonAlign }} md:w-auto md:inline-flex md:items-center md:justify-center
                  text-center py-5 px-10 text-lg md:text-xl font-extrabold !rounded-none tracking-wide">
            {{ $button_text }}
        </a>
    @endif
</section>
