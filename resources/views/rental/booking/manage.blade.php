@extends('layouts.rental')

@section('title', __('rental.lookup_title') . ' — ' . __('rental.brand'))

@section('content')
<div class="max-w-3xl mx-auto px-4 py-12">

    <div class="text-center mb-8">
        <h1 class="text-2xl sm:text-3xl font-black text-ink">{{ __('rental.lookup_title') }}</h1>
        <p class="mt-3 text-sm text-gray-500">{{ __('rental.lookup_sub') }}</p>
    </div>

    <form method="POST" action="{{ route('rental.booking.lookup') }}"
          class="bg-white border border-gray-200 rounded-2xl p-6 grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
        @csrf

        <div class="sm:col-span-1">
            <label for="booking_reference" class="block text-xs font-bold text-ink mb-2">{{ __('rental.lookup_ref') }}</label>
            <input type="text" id="booking_reference" name="booking_reference" required
                   value="{{ old('booking_reference') }}" placeholder="KEY-000000-XXXX" dir="ltr"
                   class="w-full text-sm rounded-lg border border-gray-300 bg-white px-3 py-2.5 shadow-sm
                          focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent">
        </div>

        <div class="sm:col-span-1">
            <label for="driver_phone" class="block text-xs font-bold text-ink mb-2">{{ __('rental.lookup_phone') }}</label>
            <input type="tel" id="driver_phone" name="driver_phone" required
                   value="{{ old('driver_phone') }}" dir="ltr" inputmode="tel" placeholder="079 000 0000"
                   class="w-full text-sm rounded-lg border border-gray-300 bg-white px-3 py-2.5 shadow-sm
                          focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent">
        </div>

        <div class="sm:col-span-1">
            <button type="submit"
                    class="w-full bg-accent hover:bg-accent-600 text-white font-bold text-sm py-3 rounded-lg transition-colors">
                {{ __('rental.lookup_btn') }}
            </button>
        </div>
    </form>

    @if($booking)
        <div class="mt-8">
            @include('rental.booking.partials.details', ['booking' => $booking])

            @if($booking->isCancellable())
                <form method="POST" action="{{ route('rental.booking.cancel', $booking->booking_reference) }}"
                      onsubmit="return confirm('{{ __('rental.cancel_confirm') }}')"
                      class="mt-6 text-center">
                    @csrf
                    <input type="hidden" name="driver_phone" value="{{ request('driver_phone') }}">
                    <button type="submit"
                            class="px-6 py-3 rounded-lg border border-red-300 text-red-600 font-bold text-sm
                                   hover:bg-red-50 transition-colors">
                        <i class="fa-solid fa-ban"></i> {{ __('rental.cancel_booking') }}
                    </button>
                </form>
            @endif
        </div>
    @endif
</div>
@endsection
