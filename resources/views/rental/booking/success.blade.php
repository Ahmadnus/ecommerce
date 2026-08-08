@extends('layouts.rental')

@section('title', __('rental.booking_confirmed') . ' — ' . __('rental.brand'))

@section('content')
<div class="max-w-3xl mx-auto px-4 py-12">

    <div class="text-center mb-10">
        <div class="w-20 h-20 mx-auto rounded-full bg-green-50 flex items-center justify-center">
            <i class="fa-solid fa-circle-check text-4xl text-green-500"></i>
        </div>
        <h1 class="mt-5 text-2xl sm:text-3xl font-black text-ink">{{ __('rental.booking_confirmed') }}</h1>
        <p class="mt-3 text-sm text-gray-500">{{ __('rental.ref_note') }}</p>
    </div>

    @include('rental.booking.partials.details', ['booking' => $booking])

    <div class="flex flex-wrap gap-3 justify-center mt-8 print:hidden">
        <a href="{{ route('rental.home') }}"
           class="px-6 py-3 rounded-lg border border-gray-300 font-bold text-sm hover:border-accent transition-colors">
            {{ __('rental.home') }}
        </a>
        <button onclick="window.print()"
                class="px-6 py-3 rounded-lg bg-accent hover:bg-accent-600 text-accent-fg font-bold text-sm transition-colors">
            <i class="fa-solid fa-print"></i> {{ __('rental.print') }}
        </button>
    </div>
</div>
@endsection
