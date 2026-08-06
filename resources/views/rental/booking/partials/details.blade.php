@php /** @var \App\Models\Booking $booking */ @endphp

<div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">

    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-4 p-6 border-b border-gray-100">
        <div>
            <p class="text-xs text-gray-400">{{ __('rental.booking_ref') }}</p>
            <p class="text-xl font-black text-ink tracking-wider" dir="ltr">{{ $booking->booking_reference }}</p>
        </div>
        <span class="inline-flex items-center gap-2 text-xs font-bold px-3 py-1.5 rounded-full
                     bg-{{ $booking->status_color }}-50 text-{{ $booking->status_color }}-700">
            <span class="w-1.5 h-1.5 rounded-full bg-{{ $booking->status_color }}-500"></span>
            {{ $booking->status_label }}
        </span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6">

        {{-- Vehicle --}}
        <div>
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-3">{{ __('rental.fleet') }}</h3>
            @if($booking->vehicle)
                <div class="flex gap-3">
                    <div class="w-28 h-20 bg-gray-50 rounded-lg shrink-0 flex items-center justify-center overflow-hidden">
                        @if($booking->vehicle->main_image_url)
                            <img src="{{ $booking->vehicle->main_image_url }}" alt="{{ $booking->vehicle->full_title }}"
                                 class="w-full h-full object-contain p-1">
                        @else
                            <i class="fa-solid fa-car text-2xl text-gray-300"></i>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <p class="font-bold text-ink">{{ $booking->vehicle->title }}</p>
                        <p class="text-xs text-gray-500">{{ $booking->vehicle->year }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $booking->vehicle->transmission_label }} · {{ $booking->vehicle->fuel_label }}
                        </p>
                    </div>
                </div>
            @else
                <p class="text-sm text-gray-400">—</p>
            @endif
        </div>

        {{-- Driver --}}
        <div>
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-3">{{ __('rental.driver_details') }}</h3>
            <dl class="space-y-1.5 text-sm">
                <div class="flex justify-between gap-4">
                    <dt class="text-gray-500">{{ __('rental.full_name') }}</dt>
                    <dd class="font-semibold text-ink text-end">{{ $booking->driver_name }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-gray-500">{{ __('rental.phone') }}</dt>
                    <dd class="font-semibold text-ink text-end" dir="ltr">{{ $booking->driver_phone }}</dd>
                </div>
                @if($booking->driver_email)
                    <div class="flex justify-between gap-4">
                        <dt class="text-gray-500">{{ __('rental.email') }}</dt>
                        <dd class="font-semibold text-ink text-end break-all">{{ $booking->driver_email }}</dd>
                    </div>
                @endif
                @if($booking->driver_license_number)
                    <div class="flex justify-between gap-4">
                        <dt class="text-gray-500">{{ __('rental.license_number') }}</dt>
                        <dd class="font-semibold text-ink text-end" dir="ltr">{{ $booking->driver_license_number }}</dd>
                    </div>
                @endif
            </dl>
        </div>

        {{-- Pickup --}}
        <div>
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-3">{{ __('rental.pickup') }}</h3>
            <p class="font-bold text-ink">{{ $booking->pickupLocation?->display_label ?? '—' }}</p>
            <p class="text-sm text-gray-500 mt-1">
                <i class="fa-regular fa-clock text-accent"></i>
                {{ $booking->pickup_date_time->format('d M Y — H:i') }}
            </p>
        </div>

        {{-- Return --}}
        <div>
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-3">{{ __('rental.return_location') }}</h3>
            <p class="font-bold text-ink">{{ $booking->returnLocation?->display_label ?? '—' }}</p>
            <p class="text-sm text-gray-500 mt-1">
                <i class="fa-regular fa-clock text-accent"></i>
                {{ $booking->return_date_time->format('d M Y — H:i') }}
            </p>
        </div>
    </div>

    {{-- Price breakdown --}}
    <div class="bg-gray-50 p-6 border-t border-gray-100">
        <dl class="space-y-2.5 text-sm max-w-md {{ app()->getLocale() === 'ar' ? 'mr-auto' : 'ml-auto' }}">
            <div class="flex justify-between">
                <dt class="text-gray-500">{{ __('rental.rental_period') }}</dt>
                <dd class="font-semibold text-ink">{{ __('rental.days', ['count' => $booking->total_days]) }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-500">{{ __('rental.subtotal') }}</dt>
                <dd class="font-semibold text-ink">{{ number_format((float) $booking->subtotal, 2) }}</dd>
            </div>
            @if((float) $booking->location_fee > 0)
                <div class="flex justify-between">
                    <dt class="text-gray-500">{{ __('rental.location_fee') }}</dt>
                    <dd class="font-semibold text-ink">{{ number_format((float) $booking->location_fee, 2) }}</dd>
                </div>
            @endif
            @foreach(($booking->extras ?? []) as $extra)
                <div class="flex justify-between">
                    <dt class="text-gray-500">
                        {{ app()->getLocale() === 'ar' ? ($extra['label_ar'] ?? $extra['label']) : $extra['label'] }}
                    </dt>
                    <dd class="font-semibold text-ink">{{ number_format((float) $extra['total'], 2) }}</dd>
                </div>
            @endforeach
            <div class="flex justify-between">
                <dt class="text-gray-500">{{ __('rental.vat', ['rate' => 15]) }}</dt>
                <dd class="font-semibold text-ink">{{ number_format((float) $booking->tax_amount, 2) }}</dd>
            </div>
            <div class="flex justify-between pt-3 border-t border-gray-200">
                <dt class="font-black text-ink">{{ __('rental.total') }}</dt>
                <dd class="font-black text-xl text-accent">
                    {{ number_format((float) $booking->total_amount, 2) }} {{ $booking->currency }}
                </dd>
            </div>
        </dl>
    </div>
</div>
