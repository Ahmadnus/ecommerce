@extends('layouts.rental')

@section('title', __('rental.complete_booking') . ' — ' . __('rental.brand'))

@section('content')
<div class="max-w-[1300px] mx-auto px-4 py-10">

    <h1 class="text-2xl sm:text-3xl font-black text-ink mb-8">{{ __('rental.complete_booking') }}</h1>

    @php
        /*
         * Tailwind's preflight zeroes border-width on every element, so the old
         * `border-gray-300` (a colour only) rendered these inputs with no visible
         * outline at all. `border` restores the width; the white background and
         * padding make the box legible against the white card.
         */
        $fieldClass = 'w-full text-sm rounded-lg border border-gray-300 bg-white text-ink px-3 py-2.5 '
                    . 'shadow-sm focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent';
    @endphp

    @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 rounded-lg p-4">
            <ul class="list-disc list-inside text-sm space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('rental.booking.store', $vehicle->slug) }}"
          class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        @csrf

        <input type="hidden" name="different_location" value="1">

        {{-- ══ LEFT: driver form ═══════════════════════════════════════════ --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Trip window + branches. These used to be hidden fields carried
                 from the search widget, which left the summary showing "—"
                 whenever the URL had no location. Editable here instead. --}}
            <section class="bg-white border border-gray-200 rounded-2xl p-6">
                <h2 class="font-black text-lg text-ink mb-5">
                    <i class="fa-solid fa-route text-accent"></i> {{ __('rental.rental_period') }}
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="pickup_location_id" class="block text-xs font-bold text-ink mb-2">{{ __('rental.pickup_location') }}</label>
                        <select id="pickup_location_id" name="pickup_location_id" class="{{ $fieldClass }}">
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" @selected($search['pickup_location_id'] == $loc->id)>{{ $loc->display_label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="return_location_id" class="block text-xs font-bold text-ink mb-2">{{ __('rental.return_location') }}</label>
                        <select id="return_location_id" name="return_location_id" class="{{ $fieldClass }}">
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" @selected($search['return_location_id'] == $loc->id)>{{ $loc->display_label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="pickup_date" class="block text-xs font-bold text-ink mb-2">{{ __('rental.pickup_datetime') }}</label>
                        <div class="flex gap-2">
                            <input type="date" id="pickup_date" name="pickup_date" value="{{ old('pickup_date', $search['pickup_date']) }}" class="{{ $fieldClass }}">
                            <input type="time" name="pickup_time" value="{{ old('pickup_time', $search['pickup_time']) }}" class="{{ $fieldClass }} w-32">
                        </div>
                    </div>
                    <div>
                        <label for="return_date" class="block text-xs font-bold text-ink mb-2">{{ __('rental.return_datetime') }}</label>
                        <div class="flex gap-2">
                            <input type="date" id="return_date" name="return_date" value="{{ old('return_date', $search['return_date']) }}" class="{{ $fieldClass }}">
                            <input type="time" name="return_time" value="{{ old('return_time', $search['return_time']) }}" class="{{ $fieldClass }} w-32">
                        </div>
                    </div>
                </div>
            </section>

            <section class="bg-white border border-gray-200 rounded-2xl p-6">
                <h2 class="font-black text-lg text-ink mb-5">
                    <i class="fa-solid fa-id-card text-accent"></i> {{ __('rental.driver_details') }}
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    @php
                        $me = auth()->user();

                        /*
                         * Demo defaults are Jordanian so the form can be
                         * submitted as-is during a walkthrough. A signed-in
                         * user's own details still win.
                         * [name, label, type, required, default, placeholder]
                         */
                        $fields = [
                            ['driver_name',            __('rental.full_name'),       'text',  true,  $me?->name ?? 'عمر المجالي', 'عمر المجالي'],
                            ['driver_phone',           __('rental.phone'),           'tel',   true,  $me?->getAttribute('phone') ?? '0790000000', '079 000 0000'],
                            ['driver_email',           __('rental.email'),           'email', false, $me?->email ?? '', 'name@example.jo'],
                            ['driver_date_of_birth',   __('rental.date_of_birth'),   'date',  false, '', ''],
                            ['driver_license_number',  __('rental.license_number'),  'text',  true,  'JO-962000', 'JO-962000'],
                            ['driver_license_country', __('rental.license_country'), 'text',  false, __('rental.country_jordan'), __('rental.country_jordan')],
                            ['driver_license_expiry',  __('rental.license_expiry'),  'date',  false, '', ''],
                            ['driver_national_id',     __('rental.national_id'),     'text',  false, '', '9901012345'],
                        ];
                    @endphp

                    @foreach($fields as [$name, $label, $type, $required, $default, $placeholder])
                        <div>
                            <label for="{{ $name }}" class="block text-xs font-bold text-ink mb-2">
                                {{ $label }}
                                @if($required)<span class="text-red-500">*</span>@endif
                            </label>
                            <input type="{{ $type }}" id="{{ $name }}" name="{{ $name }}"
                                   value="{{ old($name, $default) }}"
                                   placeholder="{{ $placeholder ?: $label }}"
                                   @if($type === 'tel') dir="ltr" inputmode="tel" @endif
                                   class="{{ $fieldClass }} @error($name) border-red-400 @enderror">
                            @error($name)
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- Extras --}}
            <section class="bg-white border border-gray-200 rounded-2xl p-6">
                <h2 class="font-black text-lg text-ink">
                    <i class="fa-solid fa-plus text-accent"></i> {{ __('rental.extras_title') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1 mb-5">{{ __('rental.extras_sub') }}</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($availableExtras as $key => $extra)
                        <label class="flex items-start gap-3 border border-gray-200 rounded-xl p-4 cursor-pointer
                                      hover:border-accent transition-colors has-[:checked]:border-accent has-[:checked]:bg-accent/5">
                            <input type="checkbox" name="extras[]" value="{{ $key }}"
                                   @checked(in_array($key, old('extras', $selectedExtras), true))
                                   class="mt-0.5 rounded border-gray-300 text-accent focus:ring-accent">
                            <span class="min-w-0 flex-1">
                                <span class="flex items-center gap-2 font-bold text-sm text-ink">
                                    <i class="{{ $extra['icon'] }} text-accent"></i>
                                    {{ app()->getLocale() === 'ar' ? $extra['label_ar'] : $extra['label'] }}
                                </span>
                                <span class="block mt-1 text-xs text-gray-500">
                                    {{ number_format($extra['price'], 0) }} {{ __('rental.currency') }}
                                    {{ $extra['per_day'] ? __('rental.per_day') : '' }}
                                </span>
                            </span>
                        </label>
                    @endforeach
                </div>

                <p class="mt-4 text-xs text-gray-400">
                    <i class="fa-solid fa-circle-info"></i>
                    {{ app()->getLocale() === 'ar'
                        ? 'يتم تحديث الإجمالي بعد اختيار الإضافات وتأكيد الحجز.'
                        : 'Totals update once extras are applied at confirmation.' }}
                </p>
            </section>

            {{-- Notes --}}
            <section class="bg-white border border-gray-200 rounded-2xl p-6">
                <label for="notes" class="block font-black text-lg text-ink mb-3">
                    <i class="fa-regular fa-comment text-accent"></i> {{ __('rental.notes') }}
                </label>
                <textarea id="notes" name="notes" rows="3" placeholder="{{ __('rental.notes_ph') }}"
                          class="{{ $fieldClass }}">{{ old('notes') }}</textarea>
            </section>
        </div>

        {{-- ══ RIGHT: summary ══════════════════════════════════════════════ --}}
        <div class="lg:col-span-1">
            <div class="bg-white border border-gray-200 rounded-2xl p-6 lg:sticky lg:top-6">
                <h2 class="font-black text-lg text-ink mb-5">{{ __('rental.summary') }}</h2>

                {{-- Vehicle --}}
                <div class="flex gap-3 pb-5 border-b border-gray-100">
                    <div class="w-24 h-16 bg-gray-50 rounded-lg shrink-0 flex items-center justify-center overflow-hidden">
                        @if($vehicle->main_image_url)
                            <img src="{{ $vehicle->main_image_url }}" alt="{{ $vehicle->full_title }}" class="w-full h-full object-contain p-1">
                        @else
                            <i class="fa-solid fa-car text-2xl text-gray-300"></i>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <p class="font-bold text-sm text-ink truncate">{{ $vehicle->title }}</p>
                        <p class="text-xs text-gray-500">{{ $vehicle->year }}</p>
                        @if($vehicle->category)
                            <span class="inline-block mt-1 bg-accent/10 text-accent text-[10px] font-bold px-2 py-0.5 rounded-full">
                                {{ $vehicle->category->name }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Itinerary --}}
                <div class="py-5 border-b border-gray-100 space-y-4 text-sm">
                    <div class="flex gap-3">
                        <i class="fa-solid fa-location-dot text-accent mt-0.5"></i>
                        <div class="min-w-0">
                            <p class="text-[11px] text-gray-400">{{ __('rental.pickup_location') }}</p>
                            <p class="font-bold text-ink">{{ $search['pickup_location']?->display_label ?? '—' }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">
                                {{ $search['pickup_at']->format('d M Y — H:i') }}
                            </p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <i class="fa-solid fa-flag-checkered text-accent mt-0.5"></i>
                        <div class="min-w-0">
                            <p class="text-[11px] text-gray-400">{{ __('rental.return_location') }}</p>
                            <p class="font-bold text-ink">
                                {{ $search['return_location']?->display_label ?? $search['pickup_location']?->display_label ?? '—' }}
                            </p>
                            <p class="text-xs text-gray-500 mt-0.5">
                                {{ $search['return_at']->format('d M Y — H:i') }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Price breakdown --}}
                <dl class="py-5 space-y-3 text-sm border-b border-gray-100">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">{{ __('rental.rental_period') }}</dt>
                        <dd class="font-bold text-ink">{{ __('rental.days', ['count' => $quote['days']]) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">
                            {{ number_format($quote['per_day'], 2) }} × {{ $quote['days'] }}
                        </dt>
                        <dd class="font-bold text-ink">{{ number_format($quote['subtotal'], 2) }}</dd>
                    </div>

                    @if($quote['pickup_fee'] > 0)
                        <div class="flex justify-between">
                            <dt class="text-gray-500">{{ __('rental.location_fee') }}</dt>
                            <dd class="font-bold text-ink">{{ number_format($quote['pickup_fee'], 2) }}</dd>
                        </div>
                    @endif
                    @if($quote['one_way_fee'] > 0)
                        <div class="flex justify-between">
                            <dt class="text-gray-500">{{ __('rental.one_way_fee') }}</dt>
                            <dd class="font-bold text-ink">{{ number_format($quote['one_way_fee'], 2) }}</dd>
                        </div>
                    @endif

                    @foreach($quote['extras'] as $extra)
                        <div class="flex justify-between">
                            <dt class="text-gray-500 truncate">
                                {{ app()->getLocale() === 'ar' ? $extra['label_ar'] : $extra['label'] }}
                            </dt>
                            <dd class="font-bold text-ink">{{ number_format($extra['total'], 2) }}</dd>
                        </div>
                    @endforeach

                    <div class="flex justify-between">
                        <dt class="text-gray-500">{{ __('rental.vat', ['rate' => $quote['vat_rate'] * 100]) }}</dt>
                        <dd class="font-bold text-ink">{{ number_format($quote['tax_amount'], 2) }}</dd>
                    </div>
                </dl>

                <div class="flex justify-between items-center py-5">
                    <span class="font-black text-ink">{{ __('rental.total') }}</span>
                    <span class="font-black text-2xl text-accent">
                        {{ number_format($quote['total'], 2) }}
                        <span class="text-sm">{{ __('rental.currency') }}</span>
                    </span>
                </div>

                @if($quote['security_deposit'] > 0)
                    <p class="text-xs text-gray-500 bg-gray-50 rounded-lg p-3 leading-relaxed">
                        <i class="fa-solid fa-circle-info text-accent"></i>
                        {{ __('rental.deposit_note', [
                            'amount' => number_format($quote['security_deposit'], 0) . ' ' . __('rental.currency')
                        ]) }}
                    </p>
                @endif

                <label class="flex items-start gap-2.5 mt-5 cursor-pointer">
                    <input type="checkbox" name="terms" value="1" @checked(old('terms', true))
                           class="mt-0.5 rounded border-gray-300 text-accent focus:ring-accent">
                    <span class="text-xs text-gray-600">{{ __('rental.terms_agree') }}</span>
                </label>

                <button type="submit"
                        class="mt-5 w-full bg-accent hover:bg-accent-600 text-accent-fg font-bold py-4 rounded-lg transition-colors">
                    {{ __('rental.confirm_booking') }}
                </button>

                <p class="mt-3 text-center text-xs text-gray-400">
                    <i class="fa-solid fa-money-bill-wave"></i> {{ __('rental.pay_on_pickup') }}
                </p>
            </div>
        </div>
    </form>
</div>
@endsection
