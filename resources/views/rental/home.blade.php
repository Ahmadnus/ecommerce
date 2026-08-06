@extends('layouts.rental')

@section('title', __('rental.brand'))

@section('content')

@php
    /*
     * Hero slides. An admin banner supplies its own artwork via the
     * 'banner_image' media collection; when no banners exist we build the
     * slider straight from the featured fleet, reusing $vehicle->main_image_url
     * — the exact accessor the fleet cards use. Same source means that if the
     * cards show photos on the server, so does the hero.
     */
    $slides = $banners->map(fn($b) => [
        'image'       => $b->getFirstMediaUrl('banner_image'),
        'badge'       => $b->badge,
        'title'       => $b->title,
        'subtitle'    => $b->subtitle,
        'description' => $b->description,
        'cta_text'    => $b->button_text,
        'cta_url'     => $b->button_url,
        'bg'          => $b->background_color ?: '#1a1a1a',
        'color'       => $b->text_color ?: '#ffffff',
    ]);

    if ($slides->isEmpty()) {
        $slides = $featured
            ->filter(fn($v) => $v->main_image_url)
            ->take(5)
            ->map(fn($v) => [
                'image'       => $v->main_image_url,
                'badge'       => $v->category?->name,
                'title'       => $v->full_title,
                'subtitle'    => number_format($v->effective_daily_rate, 0)
                                 . ' ' . __('rental.currency') . ' ' . __('rental.per_day'),
                'description' => null,
                'cta_text'    => __('rental.book_now'),
                'cta_url'     => route('rental.vehicles.show', $v->slug),
                'bg'          => '#1a1a1a',
                'color'       => '#ffffff',
            ])
            ->values();
    }
@endphp

{{-- ══ HERO CAROUSEL ═══════════════════════════════════════════════════════ --}}
<section x-data="{
             active: 0,
             count: {{ max($slides->count(), 1) }},
             next() { this.active = (this.active + 1) % this.count },
             prev() { this.active = (this.active - 1 + this.count) % this.count }
         }"
         x-init="if (count > 1) setInterval(() => next(), 6000)"
         class="relative bg-ink overflow-hidden">

    <div class="relative h-[380px] sm:h-[460px] lg:h-[560px]">
        @forelse($slides as $i => $slide)
            <div x-show="active === {{ $i }}"
                 x-transition:enter="transition ease-out duration-700"
                 x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 class="absolute inset-0 flex items-center"
                 style="background: linear-gradient(115deg, {{ $slide['bg'] }} 0%, #111 100%);">

                @if($slide['image'])
                    {{-- Photo fills the frame; the scrim keeps the headline
                         readable over a bright car body. --}}
                    <img src="{{ $slide['image'] }}" alt="{{ $slide['title'] }}"
                         @if($i === 0) fetchpriority="high" @else loading="lazy" @endif
                         class="absolute inset-0 w-full h-full object-cover">
                    <div class="absolute inset-0"
                         style="background: linear-gradient(
                             {{ app()->getLocale() === 'ar' ? '270deg' : '90deg' }},
                             rgba(0,0,0,.78) 0%, rgba(0,0,0,.55) 45%, rgba(0,0,0,.15) 100%);"></div>
                @endif

                <div class="relative max-w-[1300px] mx-auto px-6 sm:px-10 w-full">
                    <div class="max-w-2xl" style="color: {{ $slide['color'] }};">
                        @if($slide['badge'])
                            <span class="inline-block bg-accent text-white text-xs font-bold px-3 py-1.5 rounded-full mb-4">
                                {{ $slide['badge'] }}
                            </span>
                        @endif
                        <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black leading-tight drop-shadow">
                            {{ $slide['title'] }}
                        </h1>
                        @if($slide['subtitle'])
                            <p class="mt-3 text-lg sm:text-2xl font-bold opacity-90">{{ $slide['subtitle'] }}</p>
                        @endif
                        @if($slide['description'])
                            <p class="mt-3 text-sm sm:text-base opacity-75 leading-relaxed">{{ $slide['description'] }}</p>
                        @endif
                        @if($slide['cta_text'] && $slide['cta_url'])
                            <a href="{{ $slide['cta_url'] }}"
                               class="inline-block mt-6 bg-accent hover:bg-accent-600 text-white font-bold
                                      px-8 py-3.5 rounded-md transition-colors">
                                {{ $slide['cta_text'] }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            {{-- Fallback hero when the admin hasn't added any banners yet --}}
            <div class="absolute inset-0 flex items-center"
                 style="background: linear-gradient(115deg, var(--accent) 0%, var(--ink) 65%);">
                <div class="max-w-[1300px] mx-auto px-6 sm:px-10 w-full">
                    <div class="max-w-2xl text-white">
                        <span class="inline-block bg-white/20 backdrop-blur text-white text-xs font-bold px-3 py-1.5 rounded-full mb-4">
                            {{ __('rental.brand') }}
                        </span>
                        <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black leading-tight">
                            {{ __('rental.search_cars') }}
                        </h1>
                        <p class="mt-4 text-base sm:text-xl opacity-90 leading-relaxed">{{ __('rental.why_sub') }}</p>
                        <a href="{{ route('rental.fleet') }}"
                           class="inline-block mt-6 bg-white text-ink hover:bg-gray-100 font-bold px-8 py-3.5 rounded-md transition-colors">
                            {{ __('rental.view_all') }}
                        </a>
                    </div>
                </div>
            </div>
        @endforelse

        {{-- Arrows --}}
        @if($slides->count() > 1)
            <button @click="prev()" aria-label="Previous"
                    class="absolute {{ app()->getLocale() === 'ar' ? 'right-4' : 'left-4' }} top-1/2 -translate-y-1/2
                           w-11 h-11 rounded-full bg-white/25 hover:bg-white/40 backdrop-blur text-white transition-colors">
                <i class="fa-solid fa-chevron-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}"></i>
            </button>
            <button @click="next()" aria-label="Next"
                    class="absolute {{ app()->getLocale() === 'ar' ? 'left-4' : 'right-4' }} top-1/2 -translate-y-1/2
                           w-11 h-11 rounded-full bg-white/25 hover:bg-white/40 backdrop-blur text-white transition-colors">
                <i class="fa-solid fa-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i>
            </button>

            <div class="absolute bottom-24 left-1/2 -translate-x-1/2 flex gap-2">
                @foreach($slides as $i => $b)
                    <button @click="active = {{ $i }}" aria-label="Slide {{ $i + 1 }}"
                            :class="active === {{ $i }} ? 'bg-accent w-7' : 'bg-white/50 w-2.5'"
                            class="h-2.5 rounded-full transition-all"></button>
                @endforeach
            </div>
        @endif
    </div>
</section>

{{-- ══ SEARCH WIDGET (overlapping the hero) ════════════════════════════════ --}}
@include('rental.partials.search-widget', ['floating' => true, 'locations' => $locations, 'search' => $search])

{{-- ══ CATEGORIES ══════════════════════════════════════════════════════════ --}}
@if($categories->isNotEmpty())
<section class="py-14 sm:py-20">
    <div class="max-w-[1300px] mx-auto px-4">
        <div class="text-center mb-10">
            <h2 class="text-2xl sm:text-3xl font-black text-ink">{{ __('rental.browse_categories') }}</h2>
            <div class="w-16 h-1 bg-accent mx-auto mt-3 rounded-full"></div>
            <p class="mt-3 text-sm text-gray-500">{{ __('rental.categories_sub') }}</p>
        </div>

        <div class="flex gap-4 overflow-x-auto hide-scrollbar pb-2 sm:grid sm:grid-cols-3 lg:grid-cols-6 sm:overflow-visible">
            @foreach($categories as $category)
                @php $rate = $category->lowestDailyRate(); @endphp
                <a href="{{ route('rental.fleet', ['category' => $category->slug]) }}"
                   class="group shrink-0 w-36 sm:w-auto bg-white border border-gray-200 rounded-2xl p-5
                          text-center hover:border-accent hover:shadow-lg transition-all">
                    <div class="w-16 h-16 mx-auto rounded-full bg-accent/10 group-hover:bg-accent
                                flex items-center justify-center transition-colors">
                        @if($category->image_url)
                            <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="w-10 h-10 object-contain">
                        @else
                            <i class="{{ $category->icon ?: 'fa-solid fa-car' }} text-2xl text-accent group-hover:text-white transition-colors"></i>
                        @endif
                    </div>
                    <h3 class="mt-3 font-bold text-sm text-ink">{{ $category->name }}</h3>
                    @if($rate)
                        <p class="mt-1 text-xs text-gray-500">
                            {{ __('rental.from') }}
                            <span class="font-bold text-accent">{{ number_format($rate, 0) }}</span>
                            {{ __('rental.currency') }}
                        </p>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ══ FEATURED FLEET ══════════════════════════════════════════════════════ --}}
@if($featured->isNotEmpty())
<section class="py-14 sm:py-20 bg-gray-50">
    <div class="max-w-[1300px] mx-auto px-4">
        <div class="flex flex-wrap items-end justify-between gap-4 mb-10">
            <div>
                <h2 class="text-2xl sm:text-3xl font-black text-ink">{{ __('rental.featured_fleet') }}</h2>
                <div class="w-16 h-1 bg-accent mt-3 rounded-full"></div>
                <p class="mt-3 text-sm text-gray-500">{{ __('rental.featured_sub') }}</p>
            </div>
            <a href="{{ route('rental.fleet') }}"
               class="inline-flex items-center gap-2 text-sm font-bold text-accent hover:gap-3 transition-all">
                {{ __('rental.view_all') }}
                <i class="fa-solid fa-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach($featured as $vehicle)
                @include('rental.partials.vehicle-card', ['vehicle' => $vehicle, 'search' => $search])
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ══ WHY CHOOSE US ═══════════════════════════════════════════════════════ --}}
<section class="py-14 sm:py-20">
    <div class="max-w-[1300px] mx-auto px-4">
        <div class="text-center max-w-3xl mx-auto mb-12">
            <h2 class="text-2xl sm:text-3xl font-black text-ink">{{ __('rental.why_title') }}</h2>
            <div class="w-16 h-1 bg-accent mx-auto mt-3 rounded-full"></div>
            <p class="mt-4 text-sm sm:text-base text-gray-500 leading-relaxed">{{ __('rental.why_sub') }}</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach([
                ['fa-solid fa-car-side',       __('rental.why_fleet'),    __('rental.why_fleet_text')],
                ['fa-solid fa-map-location-dot', __('rental.why_branches'), __('rental.why_branches_text')],
                ['fa-solid fa-tags',           __('rental.why_pricing'),  __('rental.why_pricing_text')],
                ['fa-solid fa-headset',        __('rental.why_support'),  __('rental.why_support_text')],
            ] as [$icon, $title, $text])
                <div class="bg-white border border-gray-200 rounded-2xl p-6 text-center hover:shadow-lg transition-shadow">
                    <i class="{{ $icon }} text-3xl text-accent"></i>
                    <h3 class="mt-4 font-bold text-base text-ink">{{ $title }}</h3>
                    <p class="mt-2 text-sm text-gray-500 leading-relaxed">{{ $text }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

@endsection
