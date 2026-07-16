{{--
    components/hero-media-banner.blade.php
    ─────────────────────────────────────────────────────────────────────────────
    Reusable cinematic full-width media banner (image or video) with self-contained
    Mute / Restart controls. Safe to render multiple times on the same page —
    every instance gets its own unique wrapper id and the JS below only ever
    queries *within* that instance's own `.banner-container`, so clicking a
    control on one banner never affects another banner's video.

    Props:
      - media_type : 'image' | 'video'
      - file_path  : string  (public URL of the image/video)
      - link_url   : string|null (optional wrap-in-<a> link)
      - height     : string  (Tailwind height classes, default cinematic sizing)
      - is_rtl     : bool    (controls placement of the control cluster)
--}}
@props([
    'media_type' => 'image',
    'file_path'  => null,
    'link_url'   => null,
    'height'     => 'h-[50vh] md:h-[65vh] lg:h-[85vh]',
    'is_rtl'     => false,
])

@if($file_path)
@php
    $bannerId = 'hero-banner-' . \Illuminate\Support\Str::random(8);
    $tag      = $link_url ? 'a' : 'div';
@endphp

<{{ $tag }}
    @if($link_url) href="{{ $link_url }}" @endif
    id="{{ $bannerId }}"
    class="banner-container block w-full {{ $height }} overflow-hidden relative">

    @if($media_type === 'video')
        <video src="{{ $file_path }}"
               class="banner-video w-full h-full object-cover"
               autoplay loop muted playsinline controlslist="nodownload">
        </video>

        <div class="absolute bottom-4 {{ $is_rtl ? 'left-4' : 'right-4' }} z-20 flex items-center gap-2">
            <button type="button"
                    class="banner-mute-btn w-8 h-8 md:w-9 md:h-9 rounded-full bg-black/30 backdrop-blur-sm
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
                    class="banner-restart-btn w-8 h-8 md:w-9 md:h-9 rounded-full bg-black/30 backdrop-blur-sm
                           flex items-center justify-center text-white
                           hover:bg-black/50 transition-colors duration-200"
                    aria-label="Restart">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12a7.5 7.5 0 0113.06-5.03M19.5 12a7.5 7.5 0 01-13.06 5.03"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.5 3.5v3.5H14M6.5 20.5V17H10"/>
                </svg>
            </button>
        </div>
    @else
        <img src="{{ $file_path }}" alt="" class="w-full h-full object-cover">
    @endif
</{{ $tag }}>

@once
    @push('scripts')
    <script>
    (function () {
        function stop(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        // Delegate from the document so it works for any number of banners,
        // regardless of render order, and each click is scoped to the
        // clicked control's own .banner-container via closest().
        document.addEventListener('click', function (e) {
            var muteBtn = e.target.closest('.banner-mute-btn');
            var restartBtn = e.target.closest('.banner-restart-btn');
            if (!muteBtn && !restartBtn) return;

            var container = (muteBtn || restartBtn).closest('.banner-container');
            if (!container) return;
            var video = container.querySelector('.banner-video');
            if (!video) return;

            stop(e);

            if (muteBtn) {
                video.muted = !video.muted;
                muteBtn.querySelector('[data-icon="muted"]').classList.toggle('hidden', !video.muted);
                muteBtn.querySelector('[data-icon="unmuted"]').classList.toggle('hidden', video.muted);
            } else {
                video.currentTime = 0;
                video.play();
            }
        });
    })();
    </script>
    @endpush
@endonce
@endif
