@extends('layouts.rental')

@section('title', $vehicle->full_title . ' — ' . __('rental.brand'))

@section('content')
@php
    $query = array_filter([
        'pickup_location_id' => $search['pickup_location_id'] ?? null,
        'return_location_id' => $search['return_location_id'] ?? null,
        'pickup_date'        => $search['pickup_date'] ?? null,
        'pickup_time'        => $search['pickup_time'] ?? null,
        'return_date'        => $search['return_date'] ?? null,
        'return_time'        => $search['return_time'] ?? null,
        'different_location' => ($search['different_location'] ?? false) ? 1 : null,
    ], fn($v) => $v !== null && $v !== '');

    $gallery = array_values(array_filter(array_merge(
        [$vehicle->main_image_url],
        $vehicle->gallery_urls
    )));
@endphp

<div class="max-w-[1300px] mx-auto px-4 py-8">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-xs text-gray-500 mb-6">
        <a href="{{ route('rental.home') }}" class="hover:text-accent">{{ __('rental.home') }}</a>
        <i class="fa-solid fa-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }} text-[9px]"></i>
        <a href="{{ route('rental.fleet') }}" class="hover:text-accent">{{ __('rental.fleet') }}</a>
        <i class="fa-solid fa-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }} text-[9px]"></i>
        <span class="text-ink font-semibold">{{ $vehicle->full_title }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- ══ LEFT: gallery + specs ═══════════════════════════════════════ --}}
        <div class="lg:col-span-2 space-y-6">

            <div x-data="{ current: 0 }" class="bg-white border border-gray-200 rounded-2xl p-4">
                <div class="aspect-[16/10] bg-gray-50 rounded-xl overflow-hidden flex items-center justify-center">
                    @if($gallery)
                        @foreach($gallery as $i => $url)
                            <img x-show="current === {{ $i }}" src="{{ $url }}" alt="{{ $vehicle->full_title }}"
                                 class="w-full h-full object-contain p-4">
                        @endforeach
                    @else
                        <i class="fa-solid fa-car text-7xl text-gray-200"></i>
                    @endif
                </div>

                @if(count($gallery) > 1)
                    <div class="flex gap-2 mt-3 overflow-x-auto hide-scrollbar">
                        @foreach($gallery as $i => $url)
                            <button @click="current = {{ $i }}"
                                    :class="current === {{ $i }} ? 'border-accent' : 'border-gray-200'"
                                    class="shrink-0 w-20 h-16 rounded-lg border-2 overflow-hidden bg-gray-50 transition-colors">
                                <img src="{{ $url }}" alt="" class="w-full h-full object-contain p-1">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Title --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        @if($vehicle->category)
                            <span class="inline-block bg-accent/10 text-accent text-xs font-bold px-3 py-1 rounded-full">
                                {{ $vehicle->category->name }}
                            </span>
                        @endif
                        <h1 class="mt-2 text-2xl sm:text-3xl font-black text-ink">{{ $vehicle->title }}</h1>
                        <p class="text-sm text-gray-500 mt-1">{{ $vehicle->year }}</p>
                    </div>
                    <span class="inline-flex items-center gap-2 text-xs font-bold px-3 py-1.5 rounded-full
                                 bg-{{ $vehicle->status_color }}-50 text-{{ $vehicle->status_color }}-700">
                        <span class="w-1.5 h-1.5 rounded-full bg-{{ $vehicle->status_color }}-500"></span>
                        {{ $vehicle->status_label }}
                    </span>
                </div>

                @if($vehicle->description)
                    <p class="mt-4 text-sm text-gray-600 leading-relaxed">{{ $vehicle->description }}</p>
                @endif
            </div>

            {{-- Specifications --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6">
                <h2 class="font-black text-lg text-ink mb-5">{{ __('rental.specifications') }}</h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    @foreach(array_filter([
                        ['fa-solid fa-user-group', __('rental.seats', ['count' => $vehicle->seats]), null],
                        ['fa-solid fa-door-open',  __('rental.doors', ['count' => $vehicle->doors]), null],
                        ['fa-solid fa-suitcase',   __('rental.bags',  ['count' => $vehicle->bags]),  null],
                        ['fa-solid fa-gear',       $vehicle->transmission_label, __('rental.transmission')],
                        ['fa-solid fa-gas-pump',   $vehicle->fuel_label,         __('rental.fuel')],
                        ['fa-solid fa-road',       $vehicle->mileage_label,      __('rental.mileage')],
                        ['fa-solid fa-calendar',   (string) $vehicle->year,      __('rental.year')],
                        $vehicle->color    ? ['fa-solid fa-palette', $vehicle->color, __('rental.color')] : null,
                        $vehicle->engine_cc ? ['fa-solid fa-engine', $vehicle->engine_cc . ' cc', __('rental.engine')] : null,
                        ['fa-solid fa-id-card', $vehicle->min_driver_age . ' ' . __('rental.years'), __('rental.min_age')],
                    ]) as [$icon, $value, $label])
                        <div class="flex items-center gap-3 bg-gray-50 rounded-lg p-3">
                            <i class="{{ $icon }} text-accent"></i>
                            <div class="min-w-0">
                                @if($label)
                                    <p class="text-[11px] text-gray-400 leading-tight">{{ $label }}</p>
                                @endif
                                <p class="text-sm font-bold text-ink truncate">{{ $value }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Features --}}
            @if($vehicle->features)
                <div class="bg-white border border-gray-200 rounded-2xl p-6">
                    <h2 class="font-black text-lg text-ink mb-5">{{ __('rental.features') }}</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        @foreach($vehicle->features as $feature)
                            <div class="flex items-center gap-2.5 text-sm text-gray-600">
                                <i class="fa-solid fa-circle-check text-accent text-xs"></i>{{ $feature }}
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- ══ RIGHT: booking box ══════════════════════════════════════════ --}}
        <div class="lg:col-span-1">
            <div class="bg-white border border-gray-200 rounded-2xl p-6 lg:sticky lg:top-6">

                <div class="flex items-end gap-2 pb-5 border-b border-gray-100">
                    @if($vehicle->is_on_sale)
                        <span class="text-base text-gray-400 line-through">
                            {{ number_format((float) $vehicle->daily_rate, 0) }}
                        </span>
                    @endif
                    <span class="text-3xl font-black text-accent">
                        {{ number_format($vehicle->effective_daily_rate, 0) }}
                    </span>
                    <span class="text-sm font-semibold text-gray-500 pb-1">
                        {{ __('rental.currency') }} {{ __('rental.per_day') }}
                    </span>
                </div>

                @if($quote)
                    <dl class="py-5 space-y-3 text-sm border-b border-gray-100">
                        <div class="flex justify-between">
                            <dt class="text-gray-500">{{ __('rental.rental_period') }}</dt>
                            <dd class="font-bold text-ink">{{ __('rental.days', ['count' => $quote['days']]) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">{{ __('rental.subtotal') }}</dt>
                            <dd class="font-bold text-ink">{{ number_format($quote['subtotal'], 2) }}</dd>
                        </div>
                        @if($quote['location_fee'] > 0)
                            <div class="flex justify-between">
                                <dt class="text-gray-500">{{ __('rental.location_fee') }}</dt>
                                <dd class="font-bold text-ink">{{ number_format($quote['location_fee'], 2) }}</dd>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <dt class="text-gray-500">{{ __('rental.vat', ['rate' => $quote['vat_rate'] * 100]) }}</dt>
                            <dd class="font-bold text-ink">{{ number_format($quote['tax_amount'], 2) }}</dd>
                        </div>
                        <div class="flex justify-between pt-3 border-t border-gray-100">
                            <dt class="font-black text-ink">{{ __('rental.total') }}</dt>
                            <dd class="font-black text-lg text-accent">
                                {{ number_format($quote['total'], 2) }} {{ __('rental.currency') }}
                            </dd>
                        </div>
                    </dl>
                @endif

                @if($vehicle->security_deposit > 0)
                    <p class="mt-4 text-xs text-gray-500 bg-gray-50 rounded-lg p-3 leading-relaxed">
                        <i class="fa-solid fa-circle-info text-accent"></i>
                        {{ __('rental.deposit_note', [
                            'amount' => number_format((float) $vehicle->security_deposit, 0) . ' ' . __('rental.currency')
                        ]) }}
                    </p>
                @endif

                <a href="{{ route('rental.booking.create', array_merge(['slug' => $vehicle->slug], $query)) }}"
                   class="mt-5 block w-full text-center bg-accent hover:bg-accent-600 text-accent-fg font-bold
                          py-4 rounded-lg transition-colors">
                    {{ __('rental.book_now') }}
                </a>

                @if($vehicle->location)
                    <p class="mt-4 text-xs text-gray-500 text-center">
                        <i class="fa-solid fa-location-dot text-accent"></i>
                        {{ __('rental.pickup_branch') }}: {{ $vehicle->location->display_label }}
                    </p>
                @endif
            </div>
        </div>
    </div>

    {{-- ══ SIMILAR ═════════════════════════════════════════════════════════ --}}
    @if($similar->isNotEmpty())
        <section class="mt-14">
            <h2 class="text-xl font-black text-ink mb-6">{{ __('rental.similar_cars') }}</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                @foreach($similar as $item)
                    @include('rental.partials.vehicle-card', ['vehicle' => $item, 'search' => $search])
                @endforeach
            </div>
        </section>
    @endif
</div>

@endsection
