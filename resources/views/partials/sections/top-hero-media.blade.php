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
                            data-hero-play-btn
                            data-target="{{ $topHeroVideoId }}"
                            aria-label="Play / Pause"
                            class="w-8 h-8 md:w-9 md:h-9 rounded-full bg-black/30 backdrop-blur-sm
                                   flex items-center justify-center text-white
                                   hover:bg-black/50 transition-colors duration-200">
                        <svg data-icon="play" class="w-4 h-4 hidden" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8 5.14v13.72a1 1 0 001.5.86l11-6.86a1 1 0 000-1.72l-11-6.86A1 1 0 008 5.14z"/>
                        </svg>
                        <svg data-icon="pause" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M7 5a1 1 0 011-1h2a1 1 0 011 1v14a1 1 0 01-1 1H8a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h2a1 1 0 011 1v14a1 1 0 01-1 1h-2a1 1 0 01-1-1V5z"/>
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
                var playBtn = wrapper.querySelector('[data-hero-play-btn]');

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

                playBtn.addEventListener('click', function (e) {
                    stop(e);
                    if (videoEl.paused) {
                        videoEl.play();
                    } else {
                        videoEl.pause();
                    }
                });

                videoEl.addEventListener('play', function () {
                    playBtn.querySelector('[data-icon="play"]').classList.add('hidden');
                    playBtn.querySelector('[data-icon="pause"]').classList.remove('hidden');
                });

                videoEl.addEventListener('pause', function () {
                    playBtn.querySelector('[data-icon="play"]').classList.remove('hidden');
                    playBtn.querySelector('[data-icon="pause"]').classList.add('hidden');
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
