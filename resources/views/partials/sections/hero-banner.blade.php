{{--
    partials/sections/hero-banner.blade.php
    ─────────────────────────────────────────────────────────────────────────────
    Receives: $section  (HomeSection model)
    Config keys used:
      badge, title, subtitle, description, button_text, button_url,
      image_path, overlay_color
    ─────────────────────────────────────────────────────────────────────────────
--}}
@php
    $overlayColor = $section->cfg('overlay_color', '#0f172a');
    $imagePath    = $section->cfg('image_path');
    $buttonUrl    = $section->cfg('button_url', '#');
    $buttonText   = $section->cfg('button_text', 'تسوق الآن');
@endphp

<div class="hero-banner mt-4 mb-5 reveal"
     style="background: linear-gradient(135deg,
                color-mix(in srgb, {{ $overlayColor }} 85%, #000) 0%,
                color-mix(in srgb, {{ $overlayColor }} 55%, #111) 55%,
                var(--bg-color, #f8f8f8) 100%) !important;">

    <div class="relative z-10 flex items-center gap-6 px-6 md:px-14 py-10 md:py-12">

        {{-- Text block --}}
        <div class="flex-1 text-right">

            @if($section->cfg('badge'))
            <span class="inline-block text-[10px] font-black px-3 py-1 rounded-full mb-3 tracking-widest uppercase"
                  style="background:rgba(255,255,255,.12);color:rgba(255,255,255,.85);border:1px solid rgba(255,255,255,.18)">
                {{ $section->cfg('badge') }}
            </span>
            @endif

            <h2 class="font-display text-2xl md:text-4xl font-bold text-white leading-tight mb-3">
                {{ $section->cfg('title') }}
                @if($section->cfg('subtitle'))
                <br>
                <span class="text-transparent bg-clip-text"
                      style="background:linear-gradient(90deg,#60a5fa,#a78bfa);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">
                    {{ $section->cfg('subtitle') }}
                </span>
                @endif
            </h2>

            @if($section->cfg('description'))
            <p class="text-gray-400 text-sm mb-6 leading-relaxed max-w-sm hidden sm:block">
                {{ $section->cfg('description') }}
            </p>
            @endif

            <a href="{{ $buttonUrl }}"
               class="inline-flex items-center gap-2 bg-white text-gray-900 font-black text-sm
                      px-6 py-3 rounded-xl hover:bg-gray-50 transition-colors shadow-xl active:scale-95">
                {{ $buttonText }}
                <svg class="w-4 h-4 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        {{-- Hero image --}}
        @if($imagePath)
        <div class="w-32 sm:w-40 md:w-52 flex-shrink-0 relative">
            <div class="absolute inset-0 rounded-2xl opacity-30"
                 style="background:radial-gradient(circle,var(--brand-color,#0ea5e9) 0%,transparent 70%);transform:scale(1.3)"></div>
            <img src="{{ $imagePath }}"
                 alt="{{ $section->cfg('title') }}"
                 class="hero-img relative z-10 w-full h-32 sm:h-44 md:h-56 object-cover rounded-2xl">
        </div>
        @endif

    </div>
</div>