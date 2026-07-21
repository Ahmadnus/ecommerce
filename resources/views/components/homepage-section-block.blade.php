{{--
    components/homepage-section-block.blade.php
    ─────────────────────────────────────────────────────────────────────────────
    Renders a single admin-managed HomepageSection record: a tall portrait
    image/video banner (media_type = image|video) AND/OR an elegant title +
    paragraph + sharp-cornered CTA button block — whichever fields are
    actually filled in. These are NOT mutually exclusive: a section with
    both a tall image and a button text must render both, stacked, so the
    CTA never silently disappears just because media is also configured.

    Usage: <x-homepage-section-block :section="$section" :is-rtl="$isRtl" />
--}}
@props([
    'section',
    'isRtl' => false,
])

@php
    $hasText = $section->title || $section->paragraph || $section->button_text || $section->link_text;
    $showText = $hasText && ($section->show_text_below_media ?? true);
@endphp

@if($section->position === \App\Models\HomepageSection::POSITION_TOP_HERO)
    {{-- SECTION 1 — Full-height (100vh) TikTok-style media with bottom-left
         title/paragraph/link overlay directly on top of the media, and the
         mute/restart controls bottom-right (handled inside the component). --}}
    @if($section->media_type !== 'none' && $section->hasMedia())
        <x-hero-media-banner
            :media_type="$section->media_type"
            :file_path="$section->media_url"
            :is_rtl="$isRtl"
            height="h-screen"
            :overlay="$showText ? [
                'title'      => $section->title,
                'text'       => $section->paragraph,
                'linkText'   => $section->link_text,
                'linkUrl'    => $section->link_url,
                'titleColor' => $section->section_title_accent_color,
                'textColor'  => $section->text_color,
                'linkColor'  => $section->link_color,
                'titleFont'  => $section->title_font_family,
                'textFont'   => $section->paragraph_font_family,
                'linkFont'   => $section->link_font_family,
            ] : null" />
    @elseif($showText)
        <x-home-cta-block
            :title="$section->title"
            :text="$section->paragraph"
            :link-text="$section->link_text"
            :link-url="$section->link_url"
            :link-color="$section->link_color"
            :link-font-family="$section->link_font_family"
            :link-style="$section->link_style"
            :title-accent-color="$section->section_title_accent_color"
            :text-color="$section->text_color"
            :title-font-family="$section->title_font_family"
            :paragraph-font-family="$section->paragraph_font_family"
            :text-alignment="$section->text_alignment" />
    @endif
@elseif($section->position === \App\Models\HomepageSection::POSITION_BELOW_CATEGORIES)
    {{-- SECTION 2 — Text block on top, vertical media below. --}}
    @if($showText)
        <x-home-cta-block
            :title="$section->title"
            :text="$section->paragraph"
            :button_text="$section->button_text"
            :button_url="$section->button_url"
            :link-text="$section->link_text"
            :link-url="$section->link_url"
            :link-color="$section->link_color"
            :link-font-family="$section->link_font_family"
            :link-style="$section->link_style"
            :title-accent-color="$section->section_title_accent_color"
            :text-color="$section->text_color"
            :title-font-family="$section->title_font_family"
            :paragraph-font-family="$section->paragraph_font_family"
            :button-bg-color="$section->button_bg_color"
            :button-text-color="$section->button_text_color"
            :text-alignment="$section->text_alignment" />
    @endif
    @if($section->media_type !== 'none' && $section->hasMedia())
        <x-hero-media-banner
            :media_type="$section->media_type"
            :file_path="$section->media_url"
            :link_url="$section->button_url"
            :is_rtl="$isRtl"
            height="h-[70vh] md:h-[90vh]" />
    @endif
@else
    {{-- SECTION 3 (above_footer / extra dynamic sections) — media first,
         with an admin-controlled toggle for whether a caption/text block
         renders below it. --}}
    @if($section->media_type !== 'none' && $section->hasMedia())
        <x-hero-media-banner
            :media_type="$section->media_type"
            :file_path="$section->media_url"
            :link_url="$section->button_url"
            :is_rtl="$isRtl"
            height="h-[70vh] md:h-[90vh]" />
    @endif
    @if($showText)
        <x-home-cta-block
            :title="$section->title"
            :text="$section->paragraph"
            :button_text="$section->button_text"
            :button_url="$section->button_url"
            :link-text="$section->link_text"
            :link-url="$section->link_url"
            :link-color="$section->link_color"
            :link-font-family="$section->link_font_family"
            :link-style="$section->link_style"
            :title-accent-color="$section->section_title_accent_color"
            :text-color="$section->text_color"
            :title-font-family="$section->title_font_family"
            :paragraph-font-family="$section->paragraph_font_family"
            :button-bg-color="$section->button_bg_color"
            :button-text-color="$section->button_text_color"
            :text-alignment="$section->text_alignment" />
    @endif
@endif
