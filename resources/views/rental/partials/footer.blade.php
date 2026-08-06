@php
    $socialLinks = \App\Models\SocialLink::query()
        ->when(\Illuminate\Support\Facades\Schema::hasColumn('social_links', 'is_active'),
            fn($q) => $q->where('is_active', true))
        ->get();

    $supportNo   = \App\Models\Setting::get('rental_support_phone', '920000000');
    $footerPages = \App\Models\Page::query()
        ->when(\Illuminate\Support\Facades\Schema::hasColumn('pages', 'is_active'),
            fn($q) => $q->where('is_active', true))
        ->take(6)->get();
@endphp

<footer class="bg-ink text-white mt-16">
    <div class="max-w-[1300px] mx-auto px-4 py-14">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">

            {{-- Brand --}}
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-xl font-black tracking-[0.2em]">KEY</span>
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-md bg-accent">
                        <i class="fa-solid fa-key text-xs"></i>
                    </span>
                </div>
                <p class="mt-4 text-sm text-white/60 leading-relaxed">{{ __('rental.why_sub') }}</p>

                @if($socialLinks->isNotEmpty())
                    <div class="flex gap-2 mt-5">
                        @foreach($socialLinks as $social)
                            <a href="{{ $social->url }}" target="_blank" rel="noopener noreferrer"
                               aria-label="{{ $social->name ?? 'social' }}"
                               class="w-9 h-9 rounded-full bg-white/10 hover:bg-accent flex items-center justify-center transition-colors">
                                <i class="{{ $social->icon ?: 'fa-solid fa-link' }} text-sm"></i>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Quick links --}}
            <div>
                <h3 class="font-bold text-base mb-4">{{ __('rental.fleet') }}</h3>
                <ul class="space-y-2.5 text-sm text-white/60">
                    <li><a href="{{ route('rental.home') }}" class="hover:text-accent transition-colors">{{ __('rental.home') }}</a></li>
                    <li><a href="{{ route('rental.fleet') }}" class="hover:text-accent transition-colors">{{ __('rental.fleet') }}</a></li>
                    <li><a href="{{ route('rental.branches') }}" class="hover:text-accent transition-colors">{{ __('rental.branches') }}</a></li>
                    <li><a href="{{ route('rental.booking.manage') }}" class="hover:text-accent transition-colors">{{ __('rental.manage_booking') }}</a></li>
                </ul>
            </div>

            {{-- Pages --}}
            <div>
                <h3 class="font-bold text-base mb-4">{{ __('rental.about') }}</h3>
                <ul class="space-y-2.5 text-sm text-white/60">
                    @forelse($footerPages as $page)
                        <li>
                            <a href="{{ route('pages.show', $page->slug) }}" class="hover:text-accent transition-colors">
                                {{ $page->title }}
                            </a>
                        </li>
                    @empty
                        <li><a href="{{ route('contact.create') }}" class="hover:text-accent transition-colors">{{ __('rental.contact') }}</a></li>
                    @endforelse
                </ul>
            </div>

            {{-- Contact --}}
            <div>
                <h3 class="font-bold text-base mb-4">{{ __('rental.contact') }}</h3>
                <ul class="space-y-3 text-sm text-white/60">
                    <li class="flex items-center gap-3">
                        <i class="fa-solid fa-headset text-accent"></i>
                        <a href="tel:{{ $supportNo }}" dir="ltr" class="hover:text-accent transition-colors">{{ $supportNo }}</a>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fa-solid fa-clock text-accent"></i>
                        <span>{{ __('rental.why_support') }}</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="mt-12 pt-6 border-t border-white/10 text-center text-xs text-white/40">
            &copy; {{ date('Y') }} {{ __('rental.brand') }}. All rights reserved.
        </div>
    </div>
</footer>
