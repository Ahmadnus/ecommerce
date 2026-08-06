@extends('layouts.rental')

@section('title', __('app.profile.page_title'))

@php
    $isRtl = app()->getLocale() === 'ar';
@endphp

@push('head')
<style>
    :root {
        --subtle-bg: #f8fafc;
    }

    .mobile-profile-header {
        background: linear-gradient(to bottom, var(--brand-color) 50%, transparent 50%);
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05);
    }

    .app-input {
        @apply w-full px-4 py-3.5 bg-gray-50 border-transparent rounded-2xl transition-all duration-200 text-sm;
        border: 1.5px solid transparent;
    }
    .app-input:focus {
        @apply bg-white shadow-sm;
        border-color: var(--brand-color);
        outline: none;
    }

    .menu-item {
        @apply flex items-center justify-between p-4 rounded-2xl transition-all active:scale-[0.98];
        background: white;
        border: 1px solid #f1f5f9;
    }
    .menu-item:hover {
        border-color: var(--brand-color);
        background: color-mix(in srgb, var(--brand-color) 5%, transparent);
    }

    body { overflow-x: hidden; }
</style>
@endpush

@section('content')

<div class="min-h-screen pb-12" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
    
    <div class="lg:hidden h-32 w-full" style="background-color: var(--brand-color);"></div>

    <div class="max-w-6xl mx-auto px-4 -mt-16 lg:mt-12">
        <div class="flex flex-col lg:flex-row gap-8">
            
            <div class="w-full lg:w-1/3">
                <div class="glass-card rounded-[2.5rem] p-6 text-center">
                    <div class="relative -mt-16 lg:mt-0 mb-4 inline-block">
                        <div class="w-24 h-24 lg:w-28 lg:h-28 rounded-3xl rotate-3 bg-white shadow-xl flex items-center justify-center text-3xl font-bold mx-auto border-4 border-white overflow-hidden">
                           <span class="-rotate-3 text-white w-full h-full flex items-center justify-center" style="background-color: var(--brand-color);">
                                {{ mb_substr($user->name, 0, 1) }}
                           </span>
                        </div>
                        <div class="absolute -bottom-2 -right-2 bg-emerald-500 w-6 h-6 rounded-full border-4 border-white"></div>
                    </div>

                    <h2 class="text-xl font-bold text-gray-900">{{ $user->name }}</h2>
                    <p class="text-gray-400 text-sm mb-8">{{ $user->phone }}</p>

                    <div class="grid grid-cols-1 gap-3">
                        <a href="{{ route('rental.my-bookings') }}" class="menu-item group">
                            <div class="flex items-center gap-3">
                                <span class="w-10 h-10 flex items-center justify-center rounded-xl bg-blue-50 text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-all">🚗</span>
                                <span class="font-bold text-gray-700">{{ __('rental.my_bookings') }}</span>
                            </div>
                            <svg class="w-5 h-5 text-gray-300 {{ $isRtl ? '' : 'rotate-180' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </a>

                        <a href="{{ route('rental.fleet') }}" class="menu-item group">
                            <div class="flex items-center gap-3">
                                <span class="w-10 h-10 flex items-center justify-center rounded-xl bg-orange-50 text-orange-600 group-hover:bg-orange-600 group-hover:text-white transition-all">🔑</span>
                                <span class="font-bold text-gray-700">{{ __('rental.search_cars') }}</span>
                            </div>
                            <svg class="w-5 h-5 text-gray-300 {{ $isRtl ? '' : 'rotate-180' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </a>

                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="menu-item w-full group border-red-50 hover:border-red-200">
                                <div class="flex items-center gap-3">
                                    <span class="w-10 h-10 flex items-center justify-center rounded-xl bg-red-50 text-red-600 group-hover:bg-red-600 group-hover:text-white transition-all">🚪</span>
                                    <span class="font-bold text-gray-700">{{ __('app.profile.logout') }}</span>
                                </div>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="w-full lg:w-2/3 space-y-6">
                
                <div class="bg-white rounded-[2.5rem] p-6 lg:p-8 shadow-sm border border-gray-100">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-2 h-6 rounded-full" style="background-color: var(--brand-color);"></div>
                        <h3 class="text-lg font-bold text-gray-900">{{ __('app.profile.personal_info') }}</h3>
                    </div>

                    <form action="{{ route('myprofile.update') }}" method="POST" class="space-y-4">
                        @csrf @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs font-bold text-gray-400 mr-2 mb-1 block">{{ __('app.profile.name') }}</label>
                                <input type="text" name="name" value="{{ $user->name }}" class="app-input">
                            </div>
                            <div>
                                <label class="text-xs font-bold text-gray-400 mr-2 mb-1 block">{{ __('app.profile.phone') }}</label>
                                <input type="text" name="phone" value="{{ $user->phone }}" class="app-input" dir="ltr">
                            </div>
                        </div>
                        <button type="submit" class="w-full lg:w-auto px-10 py-3.5 text-white font-bold rounded-2xl shadow-lg active:scale-95 transition-all" style="background-color: var(--brand-color);">
                            {{ __('app.profile.save_changes') }}
                        </button>
                    </form>
                </div>

                <div class="bg-white rounded-[2.5rem] p-6 lg:p-8 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-6 rounded-full" style="background-color: var(--brand-color);"></div>
                            <h3 class="text-lg font-bold text-gray-900">{{ __('rental.my_bookings') }}</h3>
                        </div>
                    </div>

                    @forelse($bookings as $booking)
                    <a href="{{ route('rental.booking.success', $booking->booking_reference) }}"
                       class="flex items-center justify-between p-4 mb-3 rounded-2xl bg-gray-50/50 hover:bg-gray-50 transition-colors border border-transparent hover:border-gray-200">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow-sm shrink-0">
                                <span class="text-lg">🚗</span>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-gray-900 truncate">
                                    {{ $booking->vehicle?->title ?? '—' }}
                                </p>
                                <p class="text-[10px] text-gray-400 font-medium" dir="ltr">
                                    {{ $booking->booking_reference }} ·
                                    {{ $booking->pickup_date_time->format('Y/m/d') }}
                                </p>
                            </div>
                        </div>
                        <div class="text-end shrink-0">
                            <div class="text-sm font-black mb-1" style="color: var(--brand-color);">
                                {{ number_format((float) $booking->total_amount, 2) }} {{ $booking->currency }}
                            </div>
                            <span class="text-[9px] px-2 py-0.5 rounded-md font-bold uppercase
                                         bg-{{ $booking->status_color }}-100 text-{{ $booking->status_color }}-600">
                                {{ $booking->status_label }}
                            </span>
                        </div>
                    </a>
                    @empty
                    <div class="text-center py-8">
                        <p class="text-gray-400 text-sm">—</p>
                    </div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
</div>

@endsection