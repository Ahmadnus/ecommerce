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

@if($section->media_type !== 'none' && $section->hasMedia())
    <x-hero-media-banner
        :media_type="$section->media_type"
        :file_path="$section->media_url"
        :link_url="$section->button_url"
        :is_rtl="$isRtl"
        height="h-[70vh] md:h-[90vh]" />
@endif

{{-- Text / CTA block — renders independently of media whenever there is a
     title, paragraph, or button_text to show. This is what guarantees the
     button is "forced to render" even on media sections. --}}
@if($section->title || $section->paragraph || $section->button_text)
    <x-home-cta-block
        :title="$section->title"
        :text="$section->paragraph"
        :button_text="$section->button_text"
        :button_url="$section->button_url"
        :title-accent-color="$section->section_title_accent_color"
        :text-color="$section->text_color"
        :button-bg-color="$section->button_bg_color"
        :button-text-color="$section->button_text_color"
        :text-alignment="$section->text_alignment" />
@endif
