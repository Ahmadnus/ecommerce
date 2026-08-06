@extends('layouts.rental')

@section('title', __('rental.available_cars') . ' — ' . __('rental.brand'))

@section('content')

<div class="bg-gray-50 border-b border-gray-100 py-6">
    @include('rental.partials.search-widget', ['floating' => false, 'locations' => $locations, 'search' => $search])
</div>

<div class="max-w-[1300px] mx-auto px-4 py-10">
    <div class="flex flex-col lg:flex-row gap-8">

        {{-- ══ FILTERS ═════════════════════════════════════════════════════ --}}
        <aside x-data="{ open: false }" class="lg:w-72 shrink-0">
            <button @click="open = !open"
                    class="lg:hidden w-full flex items-center justify-between bg-white border border-gray-200
                           rounded-lg px-4 py-3 font-bold text-sm mb-4">
                <span><i class="fa-solid fa-sliders text-accent"></i> {{ __('rental.filters') }}</span>
                <i class="fa-solid fa-chevron-down text-xs" :class="open && 'rotate-180'"></i>
            </button>

            <form method="GET" action="{{ route('rental.fleet') }}"
                  x-show="open || window.innerWidth >= 1024" x-cloak
                  class="bg-white border border-gray-200 rounded-xl p-5 space-y-6 lg:sticky lg:top-6">

                {{-- Carry the date/location context through filtering --}}
                @foreach(['pickup_location_id', 'return_location_id', 'pickup_date', 'pickup_time', 'return_date', 'return_time'] as $field)
                    @if(request()->filled($field))
                        <input type="hidden" name="{{ $field }}" value="{{ request($field) }}">
                    @endif
                @endforeach
                @if(request()->boolean('different_location'))
                    <input type="hidden" name="different_location" value="1">
                @endif

                <div class="flex items-center justify-between">
                    <h2 class="font-black text-base text-ink">
                        <i class="fa-solid fa-sliders text-accent"></i> {{ __('rental.filters') }}
                    </h2>
                    <a href="{{ route('rental.fleet') }}" class="text-xs font-semibold text-accent hover:underline">
                        {{ __('rental.clear_filters') }}
                    </a>
                </div>

                {{-- Category --}}
                <div>
                    <label for="f-category" class="block text-xs font-bold text-ink mb-2">{{ __('rental.category') }}</label>
                    <select id="f-category" name="category"
                            class="w-full text-sm rounded-lg border-gray-300 focus:border-accent focus:ring-accent">
                        <option value="">{{ __('rental.all_categories') }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Brand --}}
                <div>
                    <label for="f-brand" class="block text-xs font-bold text-ink mb-2">{{ __('rental.car_brand') }}</label>
                    <select id="f-brand" name="brand"
                            class="w-full text-sm rounded-lg border-gray-300 focus:border-accent focus:ring-accent">
                        <option value="">{{ __('rental.all_brands') }}</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand }}" @selected(request('brand') === $brand)>{{ $brand }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Transmission --}}
                <div>
                    <span class="block text-xs font-bold text-ink mb-2">{{ __('rental.transmission') }}</span>
                    <div class="space-y-2">
                        @foreach(\App\Models\Vehicle::transmissions() as $key => $t)
                            <label class="flex items-center gap-2.5 cursor-pointer">
                                <input type="radio" name="transmission" value="{{ $key }}"
                                       @checked(request('transmission') === $key)
                                       class="text-accent focus:ring-accent border-gray-300">
                                <span class="text-sm text-gray-600">
                                    {{ app()->getLocale() === 'ar' ? $t['label'] : $t['label_en'] }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Fuel --}}
                <div>
                    <label for="f-fuel" class="block text-xs font-bold text-ink mb-2">{{ __('rental.fuel') }}</label>
                    <select id="f-fuel" name="fuel_type"
                            class="w-full text-sm rounded-lg border-gray-300 focus:border-accent focus:ring-accent">
                        <option value="">—</option>
                        @foreach(\App\Models\Vehicle::fuelTypes() as $key => $f)
                            <option value="{{ $key }}" @selected(request('fuel_type') === $key)>
                                {{ app()->getLocale() === 'ar' ? $f['label'] : $f['label_en'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Price range --}}
                <div>
                    <span class="block text-xs font-bold text-ink mb-2">{{ __('rental.price_range') }}</span>
                    <div class="flex items-center gap-2">
                        <input type="number" name="min_price" min="0" value="{{ request('min_price') }}"
                               placeholder="{{ __('rental.min_price') }}" aria-label="{{ __('rental.min_price') }}"
                               class="w-full text-sm rounded-lg border-gray-300 focus:border-accent focus:ring-accent">
                        <span class="text-gray-400">—</span>
                        <input type="number" name="max_price" min="0" value="{{ request('max_price') }}"
                               placeholder="{{ __('rental.max_price') }}" aria-label="{{ __('rental.max_price') }}"
                               class="w-full text-sm rounded-lg border-gray-300 focus:border-accent focus:ring-accent">
                    </div>
                </div>

                <button type="submit"
                        class="w-full bg-accent hover:bg-accent-600 text-white font-bold text-sm py-3 rounded-lg transition-colors">
                    {{ __('rental.apply') }}
                </button>
            </form>
        </aside>

        {{-- ══ RESULTS ═════════════════════════════════════════════════════ --}}
        <div class="flex-1 min-w-0">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-xl sm:text-2xl font-black text-ink">{{ __('rental.available_cars') }}</h1>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ __('rental.cars_found', ['count' => $vehicles->total()]) }}
                        @if($days)
                            · {{ __('rental.days', ['count' => $days]) }}
                        @endif
                    </p>
                </div>

                <form method="GET" action="{{ route('rental.fleet') }}">
                    @foreach(request()->except(['sort', 'page']) as $key => $value)
                        @if(! is_array($value))
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endif
                    @endforeach
                    <label for="sort" class="sr-only">{{ __('rental.sort_by') }}</label>
                    <select id="sort" name="sort" onchange="this.form.submit()"
                            class="text-sm rounded-lg border-gray-300 focus:border-accent focus:ring-accent">
                        <option value="">{{ __('rental.sort_recommended') }}</option>
                        <option value="price_asc"  @selected(request('sort') === 'price_asc')>{{ __('rental.sort_price_asc') }}</option>
                        <option value="price_desc" @selected(request('sort') === 'price_desc')>{{ __('rental.sort_price_desc') }}</option>
                        <option value="newest"     @selected(request('sort') === 'newest')>{{ __('rental.sort_newest') }}</option>
                    </select>
                </form>
            </div>

            @if($vehicles->isEmpty())
                <div class="bg-white border border-gray-200 rounded-xl py-20 text-center">
                    <i class="fa-solid fa-car-burst text-5xl text-gray-200"></i>
                    <h2 class="mt-5 font-bold text-lg text-ink">{{ __('rental.no_cars') }}</h2>
                    <p class="mt-2 text-sm text-gray-500">{{ __('rental.no_cars_hint') }}</p>
                    <a href="{{ route('rental.fleet') }}"
                       class="inline-block mt-6 bg-accent hover:bg-accent-600 text-white font-bold px-6 py-3 rounded-lg transition-colors">
                        {{ __('rental.clear_filters') }}
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
                    @foreach($vehicles as $vehicle)
                        @include('rental.partials.vehicle-card', ['vehicle' => $vehicle, 'search' => $search])
                    @endforeach
                </div>

                <div class="mt-10">{{ $vehicles->links() }}</div>
            @endif
        </div>
    </div>
</div>

@endsection
