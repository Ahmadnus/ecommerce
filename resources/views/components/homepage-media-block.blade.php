{{--
    components/homepage-media-block.blade.php
    ─────────────────────────────────────────────────────────────────────────────
    Universal media block for the homepage builder — powers hero_banner,
    banner, and custom_image sections with full admin layout control:

    ASPECT RATIO ($section->aspect_ratio):
      'full'      → edge-to-edge full-screen (h-screen, .homepage-fullbleed)
      'landscape' → aspect-[21/9] md:aspect-[3/1]  (wide banner)
      'portrait'  → aspect-[3/4]  md:aspect-[2/3]  (tall luxury imagery)
      'square'    → aspect-square

    TEXT POSITION ($section->text_position):
      'overlay_center' | 'overlay_left' | 'overlay_right'
          → relative wrapper + absolute inset-0 flex flex-col justify-center,
            aligned per choice, over a contrast scrim (only when content exists)
      'below_image'
          → media frame alone, then title/paragraph/CTA stacked cleanly below
            via <x-home-cta-block> with tight uniform margins

    Layering contract (overlay mode):
      wrapper = relative overflow-hidden   media = absolute inset-0 z-0
      scrim   = absolute inset-0 z-10      content = absolute inset-0 z-20
--}}
@props([
    'section',
    'isRtl' => false,
])

@php
    $hasText    = $section->title || $section->paragraph || $section->button_text;
    $overlay    = $section->isOverlayText() && $hasText;
    $titleFont  = $section->titleFontFamilyCss();
    $bodyFont   = $section->paragraphFontFamilyCss();
    $isFull     = $section->isFullScreenMedia();
    $frameClass = $isFull
        ? 'homepage-fullbleed h-screen min-h-screen'
        : ($section->aspectRatioClasses() ?? 'aspect-[21/9] md:aspect-[3/1]') . ' rounded-2xl';
    // Whole frame becomes a link when there is a URL but no visible CTA button.
    $frameTag  = ($section->button_url && ! $hasText) ? 'a' : 'div';
    // Overlay content horizontal padding hugs the chosen edge gracefully.
    $overlayAlign = $section->overlayAlignmentClasses();
@endphp

<section class="reveal">
    {{-- ── Media frame ─────────────────────────────────────────────────── --}}
    <{{ $frameTag }}
        @if($frameTag === 'a') href="{{ $section->button_url }}" @endif
        class="relative overflow-hidden block w-full {{ $frameClass }}">

        {{-- Layer 0: media --}}
        @if($section->hasMedia() && $section->media_type === 'video')
            <video src="{{ $section->media_url }}"
                   class="mb-video absolute inset-0 z-0 w-full h-full object-cover"
                   autoplay loop muted playsinline controlslist="nodownload"></video>

            {{-- Self-contained Mute / Restart controls (scoped per block) --}}
            <div class="absolute bottom-4 {{ $isRtl ? 'left-4' : 'right-4' }} z-30 flex items-center gap-2">
                <button type="button"
                        class="mb-mute-btn w-8 h-8 md:w-9 md:h-9 rounded-full bg-black/30 backdrop-blur-sm
                               flex items-center justify-center text-white
                               hover:bg-black/50 transition-colors duration-200"
                        aria-label="Mute / Unmute">
                    <svg data-icon="muted" class="w-4 h-4 hidden" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 9.75H3a.75.75 0 00-.75.75v3c0 .414.336.75.75.75h2.25l3.9 3.15a.375.375 0 00.6-.3V6.9a.375.375 0 00-.6-.3L5.25 9.75z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 9l4.5 6M22.5 9L18 15"/>
                    </svg>
                    <svg data-icon="unmuted" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 9.75H3a.75.75 0 00-.75.75v3c0 .414.336.75.75.75h2.25l3.9 3.15a.375.375 0 00.6-.3V6.9a.375.375 0 00-.6-.3L5.25 9.75z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9.75a3 3 0 010 4.5M18.5 7.5a6.5 6.5 0 010 9"/>
                    </svg>
                </button>
                <button type="button"
                        class="mb-restart-btn w-8 h-8 md:w-9 md:h-9 rounded-full bg-black/30 backdrop-blur-sm
                               flex items-center justify-center text-white
                               hover:bg-black/50 transition-colors duration-200"
                        aria-label="Restart">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12a7.5 7.5 0 0113.06-5.03M19.5 12a7.5 7.5 0 01-13.06 5.03"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.5 3.5v3.5H14M6.5 20.5V17H10"/>
                    </svg>
                </button>
            </div>

            @once
                @push('scripts')
                <script>
                (function () {
                    // Delegated, scoped to each block's own <section>/<a> frame —
                    // clicking one block's controls never affects another block.
                    document.addEventListener('click', function (e) {
                        var mute = e.target.closest('.mb-mute-btn');
                        var restart = e.target.closest('.mb-restart-btn');
                        if (!mute && !restart) return;
                        var frame = (mute || restart).closest('.relative');
                        var video = frame && frame.querySelector('.mb-video');
                        if (!video) return;
                        e.preventDefault(); e.stopPropagation();
                        if (mute) {
                            video.muted = !video.muted;
                            mute.querySelector('[data-icon="muted"]').classList.toggle('hidden', !video.muted);
                            mute.querySelector('[data-icon="unmuted"]').classList.toggle('hidden', video.muted);
                        } else {
                            video.currentTime = 0; video.play();
                        }
                    });
                })();
                </script>
                @endpush
            @endonce
        @elseif($section->hasMedia())
            <img src="{{ $section->media_url }}" alt="{{ $section->title }}"
                 class="absolute inset-0 z-0 w-full h-full object-cover"
                 loading="{{ $isFull ? 'eager' : 'lazy' }}">
        @else
            <div class="absolute inset-0 z-0" style="background: linear-gradient(135deg,
                color-mix(in srgb, var(--brand-color) 45%, #000) 0%,
                color-mix(in srgb, var(--brand-color) 20%, #111) 60%,
                var(--bg-color) 100%);"></div>
        @endif

        @if($overlay)
            {{-- Layer 1: contrast scrim (never intercepts clicks) --}}
            <div class="absolute inset-0 z-10 pointer-events-none"
                 style="background: linear-gradient(to bottom, rgba(0,0,0,.25) 0%, rgba(0,0,0,.15) 45%, rgba(0,0,0,.45) 100%);"></div>

            {{-- Layer 2: overlay content — vertically centered, horizontally
                 aligned per admin choice, comfortable edge padding --}}
            <div class="absolute inset-0 z-20 flex flex-col justify-center {{ $overlayAlign }}
                        gap-4 md:gap-5 px-6 md:px-14 py-8">
                @if($section->title)
                <h2 class="{{ $titleFont ? 'font-luxurySerif' : 'font-display' }} font-extrabold leading-tight max-w-3xl drop-shadow-md
                           {{ $isFull ? 'text-3xl md:text-6xl' : 'text-xl md:text-4xl' }}"
                    style="{{ $section->section_title_accent_color ? 'color:'.$section->section_title_accent_color.' !important;' : 'color:#fff !important;' }}{{ $titleFont ? 'font-family:'.$titleFont.' !important;' : '' }}">
                    {{ $section->title }}
                </h2>
                @endif
                @if($section->paragraph)
                <p class="{{ $bodyFont ? 'font-luxurySerif' : '' }} leading-relaxed max-w-xl drop-shadow
                          {{ $isFull ? 'text-sm md:text-lg' : 'text-xs md:text-base' }}"
                   style="{{ $section->text_color ? 'color:'.$section->text_color.';' : 'color:#f5f5f5;' }}{{ $bodyFont ? 'font-family:'.$bodyFont.' !important;' : '' }}">
                    {{ $section->paragraph }}
                </p>
                @endif
                @if($section->button_text)
                <a href="{{ $section->button_url ?: '#' }}"
                   class="home-cta-btn inline-flex items-center justify-center !rounded-none tracking-wide font-extrabold mt-1
                          {{ $isFull ? 'py-4 px-10 text-base md:text-lg' : 'py-3 px-8 text-sm md:text-base' }}"
                   @if($section->button_bg_color || $section->button_text_color)
                   style="{{ $section->button_bg_color ? 'background:'.$section->button_bg_color.' !important;' : '' }}{{ $section->button_text_color ? 'color:'.$section->button_text_color.' !important;' : '' }}"
                   @endif>
                    {{ $section->button_text }}
                </a>
                @endif
            </div>
        @endif
    </{{ $frameTag }}>

    {{-- ── Below-image stack — clean, in-flow, tight uniform margins ────── --}}
    @if(! $overlay && $hasText)
        <div class="mt-6">
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
        </div>
    @endif
</section>
