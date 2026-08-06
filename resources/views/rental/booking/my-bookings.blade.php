@extends('layouts.rental')

@section('title', __('rental.my_bookings') . ' — ' . __('rental.brand'))

@section('content')
<div class="max-w-4xl mx-auto px-4 py-12">

    <h1 class="text-2xl sm:text-3xl font-black text-ink mb-8">{{ __('rental.my_bookings') }}</h1>

    @forelse($bookings as $booking)
        <div class="mb-6">
            @include('rental.booking.partials.details', ['booking' => $booking])
        </div>
    @empty
        <div class="bg-white border border-gray-200 rounded-2xl py-20 text-center">
            <i class="fa-regular fa-calendar-xmark text-5xl text-gray-200"></i>
            <p class="mt-5 text-sm text-gray-500">—</p>
            <a href="{{ route('rental.fleet') }}"
               class="inline-block mt-6 bg-accent hover:bg-accent-600 text-accent-fg font-bold px-6 py-3 rounded-lg transition-colors">
                {{ __('rental.search_cars') }}
            </a>
        </div>
    @endforelse

    <div class="mt-8">{{ $bookings->links() }}</div>
</div>
@endsection
