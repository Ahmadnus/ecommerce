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
    'linkText'          => null,
    'linkUrl'           => null,
    'linkColor'         => null,
    'linkFontFamily'    => null,
    'linkStyle'         => 'underline',
    'titleAccentColor'  => null,
    'textColor'         => null,
    'titleFontFamily'   => null,
    'paragraphFontFamily' => null,
    'buttonBgColor'     => null,
    'buttonTextColor'   => null,
    'textAlignment'     => null,
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

    $titleFont = \App\Models\HomepageSection::fontFamilyValue($titleFontFamily);
    $textFont  = \App\Models\HomepageSection::fontFamilyValue($paragraphFontFamily);
    $linkFont  = \App\Models\HomepageSection::fontFamilyValue($linkFontFamily);
@endphp

<section class="home-block reveal {{ $sectionAlign }}">
    @if($title)
        <h1 style="{{ $titleAccentColor ? 'color: ' . $titleAccentColor . ' !important;' : '' }}{{ $titleFont['family'] ? 'font-family: ' . $titleFont['family'] . ' !important;' : '' }}{{ $titleFont['style'] === 'italic' ? 'font-style: italic;' : '' }}">{{ $title }}</h1>
    @endif
    @if($text)
        <p style="{{ $textColor ? 'color: ' . $textColor . ';' : '' }}{{ $textFont['family'] ? 'font-family: ' . $textFont['family'] . ' !important;' : '' }}{{ $textFont['style'] === 'italic' ? 'font-style: italic;' : '' }}">{{ $text }}</p>
    @endif

    {{-- Small underlined text link — independent of the big CTA button below. --}}
    @if($linkText)
        <a href="{{ $linkUrl ?: '#' }}"
           class="inline-block underline text-xs md:text-sm mt-1 opacity-90 hover:opacity-100 transition-opacity"
           style="{{ $linkColor ? 'color: ' . $linkColor . ';' : '' }}{{ $linkFont['family'] ? 'font-family: ' . $linkFont['family'] . ' !important;' : '' }}{{ $linkFont['style'] === 'italic' ? 'font-style: italic;' : '' }}">
            {{ $linkText }}
        </a>
    @endif

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
