{{--
    partials/sections/ad-banner.blade.php
    ─────────────────────────────────────────────────────────────────────────────
    Receives: $section  (HomeSection model)
    Config keys: text, sub_text, link_url, link_text, image_path, bg_color, text_color
    ─────────────────────────────────────────────────────────────────────────────
--}}
@php
    $bgColor    = $section->cfg('bg_color',    'var(--brand-color, #0ea5e9)');
    $textColor  = $section->cfg('text_color',  '#ffffff');
    $linkUrl    = $section->cfg('link_url',    '#');
    $linkText   = $section->cfg('link_text',   'تسوق الآن');
    $imagePath  = $section->cfg('image_path');
@endphp

<div class="mb-6 reveal">
    <a href="{{ $linkUrl }}"
       class="flex items-center justify-between gap-4 px-6 py-5 rounded-2xl overflow-hidden relative
              transition-transform hover:scale-[1.01] active:scale-[0.99]"
       style="background:{{ $bgColor }}; color:{{ $textColor }};">

        {{-- Optional background image overlay --}}
        @if($imagePath)
        <div class="absolute inset-0 z-0">
            <img src="{{ $imagePath }}" alt="" class="w-full h-full object-cover opacity-20">
        </div>
        @endif

        {{-- Decorative circles --}}
        <div class="absolute -left-6 -top-6 w-28 h-28 rounded-full opacity-10"
             style="background:rgba(255,255,255,.4)"></div>
        <div class="absolute -right-4 -bottom-4 w-20 h-20 rounded-full opacity-10"
             style="background:rgba(255,255,255,.4)"></div>

        {{-- Text content --}}
        <div class="relative z-10 flex-1 min-w-0">
            <p class="font-black text-sm md:text-base leading-snug">
                {{ $section->cfg('text') }}
            </p>
            @if($section->cfg('sub_text'))
            <p class="text-xs mt-1 opacity-80 font-medium">
                {{ $section->cfg('sub_text') }}
            </p>
            @endif
        </div>

        {{-- CTA arrow badge --}}
        <div class="relative z-10 flex-shrink-0 flex items-center gap-2
                    bg-white/20 backdrop-blur-sm px-4 py-2 rounded-xl font-bold text-xs
                    border border-white/30">
            {{ $linkText }}
            <svg class="w-3.5 h-3.5 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </div>
    </a>
</div>