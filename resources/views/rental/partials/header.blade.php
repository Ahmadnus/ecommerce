@php
    $locale   = app()->getLocale();
    $altLocale = $locale === 'ar' ? 'en' : 'ar';
    $logoUrl  = \App\Models\Setting::mediaHolder()->getFirstMediaUrl('logo');
    $navLinks = [
        ['route' => 'rental.home',  'label' => __('rental.home')],
        ['route' => 'rental.fleet', 'label' => __('rental.fleet')],
        ['route' => 'rental.branches', 'label' => __('rental.branches')],
        ['route' => 'contact.create', 'label' => __('rental.contact')],
    ];
@endphp

<header x-data="{ mobileOpen: false }" class="relative z-40 bg-white border-b border-gray-100">
    <div class="max-w-[1400px] mx-auto px-4 sm:px-6">
        <div class="flex items-center justify-between h-20 gap-4">

            {{-- Menu (slide-out trigger) --}}
            <button @click="mobileOpen = true"
                    class="flex flex-col items-center justify-center gap-1 w-14 shrink-0 text-ink"
                    aria-label="{{ __('rental.menu') }}">
                <span class="block w-7 h-0.5 bg-ink"></span>
                <span class="block w-7 h-0.5 bg-ink"></span>
                <span class="block w-7 h-0.5 bg-ink"></span>
                <span class="text-[11px] font-bold mt-1 hidden sm:block">{{ __('rental.menu') }}</span>
            </button>

            {{-- Logo --}}
            <a href="{{ route('rental.home') }}" class="flex items-center gap-2 shrink-0">
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="{{ __('rental.brand') }}" class="h-12 w-auto object-contain">
                @else
                    <span class="text-xl sm:text-2xl font-black tracking-[0.2em] text-ink">KEY</span>
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-md bg-accent text-white">
                        <i class="fa-solid fa-key text-sm"></i>
                    </span>
                @endif
            </a>

            {{-- Desktop nav --}}
            <nav class="hidden lg:flex items-center gap-7 flex-1 justify-center">
                @foreach($navLinks as $link)
                    @php $isActive = request()->routeIs($link['route']); @endphp
                    <a href="{{ route($link['route']) }}"
                       class="text-sm font-semibold transition-colors {{ $isActive ? 'text-accent' : 'text-ink hover:text-accent' }}">
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </nav>

            {{-- Actions --}}
            <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                <a href="{{ route('rental.booking.manage') }}"
                   class="hidden sm:inline-flex items-center gap-2 px-4 py-2.5 rounded-md bg-gray-600 hover:bg-gray-700
                          text-white text-sm font-semibold transition-colors">
                    <i class="fa-regular fa-calendar-check"></i>
                    <span class="hidden md:inline">{{ __('rental.manage_booking') }}</span>
                </a>

                @auth
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-md bg-accent hover:bg-accent-600
                                       text-white text-sm font-semibold transition-colors">
                            <i class="fa-regular fa-user"></i>
                            <span class="hidden md:inline">{{ Str::limit(auth()->user()->name, 12) }}</span>
                        </button>
                        <div x-show="open" x-cloak @click.outside="open = false"
                             class="absolute {{ $locale === 'ar' ? 'left-0' : 'right-0' }} mt-2 w-52 bg-white rounded-lg shadow-xl border border-gray-100 py-2">
                            <a href="{{ route('rental.my-bookings') }}"
                               class="block px-4 py-2 text-sm hover:bg-gray-50">{{ __('rental.my_bookings') }}</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="w-full text-start px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                    {{ __('rental.logout') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-md bg-accent hover:bg-accent-600
                              text-white text-sm font-semibold transition-colors">
                        <i class="fa-regular fa-user"></i>
                        <span class="hidden md:inline">{{ __('rental.login_register') }}</span>
                    </a>
                @endauth

                {{-- Language switcher --}}
                @if(($locale_mode ?? 'both') === 'both')
                    <form method="POST" action="{{ route('language.switch') }}" class="shrink-0">
                        @csrf
                        <input type="hidden" name="locale" value="{{ $altLocale }}">
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-2 sm:px-3 py-2 rounded-md border border-gray-200
                                       hover:border-accent text-xs font-bold uppercase tracking-wide transition-colors">
                            <i class="fa-solid fa-globe text-accent"></i>
                            <span>{{ __('rental.language') }}</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Mobile / slide-out menu ──────────────────────────────────────── --}}
    <div x-show="mobileOpen" x-cloak class="fixed inset-0 z-50" role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-black/50" @click="mobileOpen = false"></div>

        <aside x-show="mobileOpen"
               x-transition:enter="transition ease-out duration-300"
               x-transition:enter-start="{{ $locale === 'ar' ? 'translate-x-full' : '-translate-x-full' }}"
               x-transition:enter-end="translate-x-0"
               x-transition:leave="transition ease-in duration-200"
               x-transition:leave-start="translate-x-0"
               x-transition:leave-end="{{ $locale === 'ar' ? 'translate-x-full' : '-translate-x-full' }}"
               class="absolute inset-y-0 {{ $locale === 'ar' ? 'right-0' : 'left-0' }} w-80 max-w-[85vw] bg-white shadow-2xl flex flex-col">

            <div class="flex items-center justify-between p-5 border-b border-gray-100">
                <span class="font-black text-lg tracking-wider">{{ __('rental.menu') }}</span>
                <button @click="mobileOpen = false" class="w-9 h-9 rounded-full hover:bg-gray-100" aria-label="Close">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <nav class="flex-1 overflow-y-auto p-4 space-y-1">
                @foreach($navLinks as $link)
                    <a href="{{ route($link['route']) }}"
                       class="flex items-center justify-between px-4 py-3 rounded-lg font-semibold text-sm
                              hover:bg-accent/10 hover:text-accent transition-colors">
                        <span>{{ $link['label'] }}</span>
                        <i class="fa-solid {{ $locale === 'ar' ? 'fa-chevron-left' : 'fa-chevron-right' }} text-xs opacity-40"></i>
                    </a>
                @endforeach

                <div class="pt-4 mt-4 border-t border-gray-100 space-y-1">
                    <a href="{{ route('rental.booking.manage') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-lg font-semibold text-sm hover:bg-gray-50">
                        <i class="fa-regular fa-calendar-check text-accent"></i>
                        {{ __('rental.manage_booking') }}
                    </a>
                    @auth
                        <a href="{{ route('rental.my-bookings') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-lg font-semibold text-sm hover:bg-gray-50">
                            <i class="fa-regular fa-rectangle-list text-accent"></i>
                            {{ __('rental.my_bookings') }}
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-lg font-semibold text-sm hover:bg-gray-50">
                            <i class="fa-regular fa-user text-accent"></i>
                            {{ __('rental.login_register') }}
                        </a>
                    @endauth
                </div>
            </nav>
        </aside>
    </div>
</header>
