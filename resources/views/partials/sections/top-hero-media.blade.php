{{--
    partials/sections/top-hero-media.blade.php
    ─────────────────────────────────────────────────────────────────────────────
    Full-bleed cinematic hero (image or video) shown above everything else on
    the homepage. Renders nothing if no active TopHeroMedia record exists.
--}}
@php
    $topHero = \App\Models\TopHeroMedia::where('is_active', true)->orderBy('sort_order')->first();
@endphp

@if($topHero)
    @php
        $topHeroMediaUrl = $topHero->getFirstMediaUrl('hero_media');
        $topHeroTag = $topHero->link_url ? 'a' : 'div';
    @endphp

    @if($topHeroMediaUrl)
    <{{ $topHeroTag }}
        @if($topHero->link_url) href="{{ $topHero->link_url }}" @endif
        class="block w-full h-[50vh] md:h-[65vh] lg:h-[85vh] overflow-hidden">

        @if($topHero->type === 'video')
            @php $topHeroVideoId = 'top-hero-video-' . $topHero->id; @endphp
            <div class="relative w-full h-full">
                <video id="{{ $topHeroVideoId }}"
                       src="{{ $topHeroMediaUrl }}"
                       class="w-full h-full object-cover"
                       autoplay loop muted playsinline controlslist="nodownload">
                </video>

                <div class="absolute bottom-4 {{ $isRtl ?? false ? 'left-4' : 'right-4' }} z-20 flex items-center gap-2">
                    <button type="button"
                            data-hero-mute-btn
                            data-target="{{ $topHeroVideoId }}"
                            aria-label="Mute / Unmute"
                            class="w-8 h-8 md:w-9 md:h-9 rounded-full bg-black/30 backdrop-blur-sm
                                   flex items-center justify-center text-white
                                   hover:bg-black/50 transition-colors duration-200">
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
                            data-hero-restart-btn
                            data-target="{{ $topHeroVideoId }}"
                            aria-label="Restart"
                            class="w-8 h-8 md:w-9 md:h-9 rounded-full bg-black/30 backdrop-blur-sm
                                   flex items-center justify-center text-white
                                   hover:bg-black/50 transition-colors duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12a7.5 7.5 0 0113.06-5.03M19.5 12a7.5 7.5 0 01-13.06 5.03"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.5 3.5v3.5H14M6.5 20.5V17H10"/>
                        </svg>
                    </button>
                </div>
            </div>

            <script>
            (function () {
                var videoEl = document.getElementById('{{ $topHeroVideoId }}');
                if (!videoEl) return;

                var wrapper = videoEl.closest('.relative');

                var muteBtn = wrapper.querySelector('[data-hero-mute-btn]');
                var restartBtn = wrapper.querySelector('[data-hero-restart-btn]');

                function stop(e) {
                    e.preventDefault();
                    e.stopPropagation();
                }

                muteBtn.addEventListener('click', function (e) {
                    stop(e);
                    videoEl.muted = !videoEl.muted;
                    muteBtn.querySelector('[data-icon="muted"]').classList.toggle('hidden', !videoEl.muted);
                    muteBtn.querySelector('[data-icon="unmuted"]').classList.toggle('hidden', videoEl.muted);
                });

                restartBtn.addEventListener('click', function (e) {
                    stop(e);
                    videoEl.currentTime = 0;
                    videoEl.play();
                });
            })();
            </script>
        @else
            <img src="{{ $topHeroMediaUrl }}"
                 alt="Hero"
                 class="w-full h-full object-cover">
        @endif
    </{{ $topHeroTag }}>
    @endif
@endif
