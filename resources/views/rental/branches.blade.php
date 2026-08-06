@extends('layouts.rental')

@section('title', __('rental.branches') . ' — ' . __('rental.brand'))

@section('content')
<div class="max-w-[1300px] mx-auto px-4 py-12">

    <div class="text-center mb-10">
        <h1 class="text-2xl sm:text-3xl font-black text-ink">{{ __('rental.branches') }}</h1>
        <div class="w-16 h-1 bg-accent mx-auto mt-3 rounded-full"></div>
        <p class="mt-3 text-sm text-gray-500">{{ __('rental.why_branches_text') }}</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($locations as $location)
            <div class="bg-white border border-gray-200 rounded-2xl p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-full bg-accent/10 flex items-center justify-center shrink-0">
                        <i class="{{ $location->type_icon }} text-accent"></i>
                    </div>
                    <div class="min-w-0">
                        <h2 class="font-bold text-base text-ink">{{ $location->name }}</h2>
                        <p class="text-sm text-gray-500 mt-0.5">{{ $location->city }}</p>

                        @if($location->address)
                            <p class="text-xs text-gray-400 mt-2 leading-relaxed">{{ $location->address }}</p>
                        @endif

                        <div class="mt-3 space-y-1.5 text-xs text-gray-500">
                            <p><i class="fa-regular fa-clock text-accent"></i> {{ $location->hours_label }}</p>
                            @if($location->phone)
                                <p dir="ltr" class="text-start">
                                    <i class="fa-solid fa-phone text-accent"></i> {{ $location->phone }}
                                </p>
                            @endif
                        </div>

                        <a href="{{ route('rental.fleet', ['pickup_location_id' => $location->id]) }}"
                           class="inline-flex items-center gap-2 mt-4 text-sm font-bold text-accent hover:gap-3 transition-all">
                            {{ __('rental.search_cars') }}
                            <i class="fa-solid fa-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }} text-xs"></i>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <p class="col-span-full text-center text-gray-400 py-16">—</p>
        @endforelse
    </div>
</div>
@endsection
