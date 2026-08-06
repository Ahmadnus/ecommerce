@php
    /**
     * The key.sa-style booking widget.
     *
     * @var \Illuminate\Support\Collection $locations
     * @var array $search   Normalised search state from FleetController::resolveSearch()
     * @var bool  $floating Overlap the hero (homepage) vs. sit inline (fleet page)
     */
    $floating = $floating ?? false;
    $locale   = app()->getLocale();
@endphp

<div class="{{ $floating ? 'relative z-30 -mt-16 sm:-mt-20' : '' }} max-w-[1300px] mx-auto px-4">
    <form method="GET" action="{{ route('rental.fleet') }}"
          x-data="{
              plan: '{{ $search['plan'] ?? 'daily' }}',
              mode: 'pickup',
              differentLocation: {{ ($search['different_location'] ?? false) ? 'true' : 'false' }}
          }"
          class="bg-white rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.12)] overflow-hidden">

        {{-- ── Rate plan tabs ──────────────────────────────────────────── --}}
        <div class="grid grid-cols-3 border-b border-gray-100">
            @foreach([
                ['daily',   __('rental.daily'),   'fa-solid fa-car-side'],
                ['weekly',  __('rental.weekly'),  'fa-regular fa-calendar-days'],
                ['monthly', __('rental.monthly'), 'fa-regular fa-calendar-check'],
            ] as [$key, $label, $icon])
                <button type="button" @click="plan = '{{ $key }}'"
                        :class="plan === '{{ $key }}' ? 'bg-accent text-white' : 'bg-white text-ink hover:bg-gray-50'"
                        class="flex items-center justify-center gap-2 sm:gap-3 py-4 px-2 font-bold text-xs sm:text-base transition-colors">
                    <i class="{{ $icon }} text-sm sm:text-lg"></i>
                    <span class="truncate">{{ $label }}</span>
                </button>
            @endforeach
        </div>
        <input type="hidden" name="plan" :value="plan">

        <div class="p-4 sm:p-6 bg-gray-50/70">

            {{-- ── Pickup vs Delivery ──────────────────────────────────── --}}
            <div class="flex gap-3 mb-5">
                @foreach([['pickup', __('rental.pickup')], ['delivery', __('rental.delivery')]] as [$key, $label])
                    <button type="button" @click="mode = '{{ $key }}'"
                            :class="mode === '{{ $key }}'
                                ? 'bg-accent text-white border-accent'
                                : 'bg-white text-ink border-gray-300 hover:border-accent'"
                            class="px-8 sm:px-12 py-2.5 rounded-md border font-semibold text-sm transition-colors">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
            <input type="hidden" name="mode" :value="mode">

            {{-- ── Fields ──────────────────────────────────────────────── --}}
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-9 gap-x-6 gap-y-5 items-end">

                {{-- Pickup location --}}
                <div class="xl:col-span-2">
                    <label for="pickup_location_id" class="block text-xs font-bold text-ink mb-1.5">
                        {{ __('rental.pickup_location') }}
                    </label>
                    <select id="pickup_location_id" name="pickup_location_id"
                            class="field-underline w-full text-sm py-2 text-gray-600">
                        <option value="">{{ __('rental.select_location') }}</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}"
                                @selected(($search['pickup_location_id'] ?? null) == $location->id)>
                                {{ $location->display_label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Return location --}}
                <div class="xl:col-span-2" x-show="differentLocation" x-cloak>
                    <label for="return_location_id" class="block text-xs font-bold text-ink mb-1.5">
                        {{ __('rental.return_location') }}
                    </label>
                    <select id="return_location_id" name="return_location_id"
                            class="field-underline w-full text-sm py-2 text-gray-600">
                        <option value="">{{ __('rental.select_location') }}</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}"
                                @selected(($search['return_location_id'] ?? null) == $location->id)>
                                {{ $location->display_label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Pickup date & time --}}
                <div class="xl:col-span-2">
                    <span class="block text-xs font-bold text-ink mb-1.5">{{ __('rental.pickup_datetime') }}</span>
                    <div class="flex gap-2">
                        <input type="date" name="pickup_date" aria-label="{{ __('rental.pickup_datetime') }}"
                               value="{{ $search['pickup_date'] ?? '' }}"
                               min="{{ now()->format('Y-m-d') }}"
                               class="field-underline w-full text-sm py-2 text-gray-600">
                        <input type="time" name="pickup_time" aria-label="{{ __('rental.pickup_datetime') }}"
                               value="{{ $search['pickup_time'] ?? '10:00' }}"
                               class="field-underline w-24 text-sm py-2 text-gray-600">
                    </div>
                </div>

                {{-- Return date & time --}}
                <div class="xl:col-span-2">
                    <span class="block text-xs font-bold text-ink mb-1.5">{{ __('rental.return_datetime') }}</span>
                    <div class="flex gap-2">
                        <input type="date" name="return_date" aria-label="{{ __('rental.return_datetime') }}"
                               value="{{ $search['return_date'] ?? '' }}"
                               min="{{ now()->format('Y-m-d') }}"
                               class="field-underline w-full text-sm py-2 text-gray-600">
                        <input type="time" name="return_time" aria-label="{{ __('rental.return_datetime') }}"
                               value="{{ $search['return_time'] ?? '10:00' }}"
                               class="field-underline w-24 text-sm py-2 text-gray-600">
                    </div>
                </div>

                {{-- Submit --}}
                <div class="xl:col-span-1">
                    <button type="submit"
                            class="w-full bg-accent hover:bg-accent-600 text-white font-bold text-sm uppercase
                                   tracking-wide py-3.5 px-6 rounded-md transition-colors">
                        {{ __('rental.search') }}
                    </button>
                </div>
            </div>

            {{-- ── Different-location toggle ───────────────────────────── --}}
            <label class="inline-flex items-center gap-2.5 mt-5 cursor-pointer select-none">
                <input type="checkbox" name="different_location" value="1"
                       x-model="differentLocation"
                       class="w-4 h-4 rounded border-gray-300 text-accent focus:ring-accent">
                <span class="text-sm text-gray-600">{{ __('rental.different_location') }}</span>
            </label>
        </div>
    </form>
</div>
