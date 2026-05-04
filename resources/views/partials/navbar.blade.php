@php
    $isRtl   = app()->getLocale() === 'ar';
    $locale  = app()->getLocale();
@endphp

<header class="sticky top-0 z-40 bg-white/92 backdrop-blur-md border-b border-gray-100 shadow-sm"
        dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <nav class="max-w-screen-2xl mx-auto px-3 sm:px-5 lg:px-8">
        <div class="flex items-center justify-between h-14 md:h-16">

            {{-- Logo + Desktop Nav --}}
            <div class="flex items-center gap-6 lg:gap-10">

                <a href="/" class="flex-shrink-0 hover:opacity-80 transition-opacity">
                    <img src="{{ $logoUrl }}" alt="Logo" class="h-8 md:h-10 w-auto">
                </a>

                {{-- Currency switcher --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click.stop="open = !open" type="button"
                            class="flex items-center gap-2 px-3 py-2 bg-white border border-gray-100 rounded-xl shadow-sm hover:border-brand-200 transition-all">
                        <span class="text-xs font-bold text-gray-700">{{ $activeCurrency->symbol }}</span>
                        <span class="text-xs font-black text-brand-600">{{ $activeCurrency->code }}</span>
                        <svg class="w-3 h-3 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open" x-cloak @click.outside="open = false" @keydown.escape.window="open = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="absolute {{ $isRtl ? 'right-0' : 'left-0' }} mt-2 w-40 bg-white border border-gray-100 rounded-2xl shadow-xl z-[100] overflow-hidden"
                         style="display: none;">
                        <div class="py-1">
                            @foreach(\App\Models\Currency::active()->get() as $cur)
                                <a href="{{ route('currency.user.switch', $cur->code) }}"
                                   @click="open = false"
                                   class="flex items-center justify-between px-4 py-2.5 text-xs hover:bg-gray-50 transition-colors {{ $activeCurrency->code === $cur->code ? 'bg-brand-50 text-brand-700 font-bold' : 'text-gray-600' }}">
                                    <span>{{ $cur->name }}</span>
                                    <span class="opacity-50 uppercase text-[10px]">{{ $cur->code }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Desktop nav links --}}
                <div class="hidden md:flex items-center gap-1">
                    <a href="{{ route('products.index') }}"
                       class="px-3 py-2 rounded-xl text-sm font-bold transition-all
                              {{ request()->routeIs('products.index') && !request('category')
                                 ? 'text-brand-600 bg-brand-50'
                                 : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        {{ __('app.shop') }}
                    </a>

                    @foreach(\App\Models\Category::whereNull('parent_id')->where('is_active', true)->take(6)->get() as $parent)
                    <div class="relative group">
                        <a href="{{ route('products.index', ['category' => $parent->slug]) }}"
                           class="flex items-center gap-1 px-3 py-2 rounded-xl text-sm font-bold transition-all text-gray-600 hover:bg-gray-50 hover:text-gray-900">
                            {{ $parent->name }}
                            @if($parent->children->isNotEmpty())
                            <svg class="w-3.5 h-3.5 text-gray-400 group-hover:rotate-180 transition-transform duration-200"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                            @endif
                        </a>

                        @if($parent->children->isNotEmpty())
                        <div class="absolute top-full {{ $isRtl ? 'right-0' : 'left-0' }} mt-1 w-52 bg-white border border-gray-100
                                    rounded-2xl shadow-xl py-2 px-1
                                    opacity-0 invisible group-hover:opacity-100 group-hover:visible
                                    transition-all duration-200 z-50">
                            <div class="absolute -top-1.5 {{ $isRtl ? 'right-5' : 'left-5' }} w-3 h-3 bg-white border-t border-r border-gray-100 rotate-[-45deg]"></div>

                            @foreach($parent->children as $child)
                            <a href="{{ route('products.index', ['category' => $child->slug]) }}"
                               class="flex items-center justify-between px-3 py-2 rounded-xl text-sm text-gray-700
                                      hover:bg-brand-50 hover:text-brand-700 transition-all group/item font-medium">
                                {{ $child->name }}
                                <svg class="w-3 h-3 text-gray-300 opacity-0 -translate-x-1
                                            group-hover/item:opacity-100 group-hover/item:translate-x-0
                                            transition-all {{ $isRtl ? 'rotate-180' : '' }}"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                            @endforeach

                            <div class="mt-1 pt-1 border-t border-gray-50 px-3">
                                <a href="{{ route('products.index', ['category' => $parent->slug]) }}"
                                   class="text-[11px] font-black uppercase tracking-wider block py-1 text-center"
                                   style="color:var(--brand-color)">
                                    {{ __('app.view_all') }}
                                </a>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Right side: search + icons --}}
            <div class="flex items-center gap-1.5 md:gap-2">

                {{-- Desktop search --}}
                <form method="GET" action="{{ route('products.index') }}" class="hidden lg:flex relative">
                    <input type="text" name="search"
                           value="{{ request('search') }}"
                           placeholder="{{ __('app.search_placeholder') }}"
                           class="w-48 xl:w-60 py-2 pe-9 ps-4 text-sm bg-gray-50 border border-gray-200
                                  rounded-xl focus:bg-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500
                                  outline-none transition-all">
                    <button type="submit"
                            class="absolute inset-y-0 {{ $isRtl ? 'left-0' : 'right-0' }} flex items-center {{ $isRtl ? 'pl-3' : 'pr-3' }} text-gray-400 hover:text-gray-700">
                        <svg class="w-4 h-4 {{ $isRtl ? 'scale-x-[-1]' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                </form>

                {{-- Mobile search icon --}}
                <button class="md:hidden p-2 rounded-xl hover:bg-gray-50 transition-colors text-gray-600"
                        onclick="document.getElementById('mobile-search').classList.toggle('hidden')">
                    <svg class="w-5 h-5 {{ $isRtl ? 'scale-x-[-1]' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>

                <div class="hidden md:flex items-center gap-1">

                    {{-- Language switcher --}}
                    <form method="POST" action="{{ route('language.switch') }}">
                        @csrf
                        <input type="hidden" name="locale" value="{{ $locale === 'ar' ? 'en' : 'ar' }}">
                        <button type="submit"
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-black border border-gray-200 hover:border-gray-300 hover:bg-gray-50 transition-all text-gray-600">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                            </svg>
                            {{ $locale === 'ar' ? 'EN' : 'ع' }}
                        </button>
                    </form>

                    @auth
                    <a href="{{ route('wishlist.index') }}"
                       class="relative p-2 rounded-xl hover:bg-gray-50 group transition-colors">
                        <svg class="w-5 h-5 text-gray-600 group-hover:text-red-500 transition-colors"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        @php $wishlistCount = auth()->user()->wishlistedProducts()->count(); @endphp
                        @if($wishlistCount > 0)
                        <span class="wishlist-count absolute -top-0.5 {{ $isRtl ? '-left-0.5' : '-right-0.5' }} w-4 h-4 bg-red-500 text-white
                                     text-[9px] font-black rounded-full flex items-center justify-center border border-white">
                            {{ $wishlistCount }}
                        </span>
                        @endif
                    </a>
                    @endauth

                    <a href="{{ route('cart.index') }}"
                       class="relative p-2 rounded-xl hover:bg-gray-50 group transition-colors">
                        <svg class="w-5 h-5 text-gray-600 group-hover:text-brand-600 transition-colors"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        @php $cartCount = app(\App\Services\CartService::class)->getItemCount(); @endphp
                        @if($cartCount > 0)
                        <span class="cart-count absolute -top-0.5 {{ $isRtl ? '-left-0.5' : '-right-0.5' }} w-4 h-4 bg-brand-600 text-white
                                     text-[9px] font-black rounded-full flex items-center justify-center border border-white">
                            {{ $cartCount }}
                        </span>
                        @endif
                    </a>

                    <div class="w-px h-5 bg-gray-200 mx-1"></div>

                    @guest
                    <div class="flex items-center gap-2">
                        <a href="{{ route('login') }}"
                           class="text-sm font-bold text-gray-600 hover:text-gray-900 px-2 transition-colors">
                            {{ __('app.login') }}
                        </a>
                        <a href="{{ route('register') }}"
                           class="text-sm font-black text-white px-4 py-2 rounded-xl transition-all hover:opacity-90 active:scale-95 shadow-sm"
                           style="background:var(--brand-color)">
                            {{ __('app.register') }}
                        </a>
                    </div>
                    @endguest

                    @auth
                    <div class="relative" x-data="{ open: false }" @mouseleave="open = false">
                        <div class="flex items-center">
                            <a href="{{ route('myprofile.show') }}"
                               class="flex items-center gap-2 ps-1 pe-2 py-1 rounded-xl transition-all border border-transparent {{ request()->routeIs('myprofile.*') ? 'bg-gray-100 border-gray-200' : 'hover:bg-gray-50 hover:border-gray-100' }}">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center font-black text-xs text-white flex-shrink-0"
                                     style="background:var(--brand-color)">
                                    {{ mb_substr(auth()->user()->name, 0, 1) }}
                                </div>
                                <span class="text-sm font-medium text-gray-700">{{ auth()->user()->name }}</span>
                            </a>
                            <button @click="open = !open" class="p-1 hover:bg-gray-100 rounded-full transition-colors">
                                <svg class="w-3.5 h-3.5 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                        </div>

                        <div x-show="open" @click.outside="open = false"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="absolute {{ $isRtl ? 'left-0' : 'right-0' }} top-full mt-2 w-56 bg-white rounded-2xl shadow-xl border border-gray-100 py-2 z-50"
                             style="display:none">
                            <div class="px-4 py-3 border-b border-gray-50 mb-1">
                                <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">
                                    {{ __('app.account_info') }}
                                </p>
                                <p class="text-xs font-bold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                                <p class="text-[11px] text-gray-400 truncate">{{ auth()->user()->phone ?? __('app.no_phone') }}</p>
                            </div>

                            <a href="{{ route('wishlist.index') }}"
                               class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors font-medium">
                                <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                                {{ __('app.wishlist') }}
                            </a>

                            <a href="{{ route('orders.index') }}"
                               class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors font-medium">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
  {{ __('app.orders.heading') }}                            </a>

                            <form action="{{ route('logout') }}" method="POST" class="mt-1 border-t border-gray-50">
                                @csrf
                                <button type="submit"
                                        class="flex items-center gap-2.5 w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors font-medium {{ $isRtl ? 'text-right' : 'text-left' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    {{ __('app.logout') }}
                                </button>
                            </form>
                        </div>
                    </div>
                    @endauth
                </div>

                {{-- Mobile menu button --}}
                <button id="mobile-menu-btn" class="md:hidden p-2 rounded-xl hover:bg-gray-50 transition-colors text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile search --}}
        <div id="mobile-search" class="hidden pb-3">
            <form method="GET" action="{{ route('products.index') }}" class="relative">
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                <input type="text" name="search"
                       value="{{ request('search') }}"
                       placeholder="{{ __('app.search_mobile_placeholder') }}"
                       autofocus
                       class="w-full py-2.5 pe-10 ps-4 text-sm bg-gray-50 border border-gray-200
                              rounded-xl focus:bg-white focus:ring-2 focus:ring-brand-500 outline-none {{ $isRtl ? 'text-right' : 'text-left' }}">
                <button type="submit"
                        class="absolute inset-y-0 {{ $isRtl ? 'left-0' : 'right-0' }} flex items-center {{ $isRtl ? 'pl-3' : 'pr-3' }} text-gray-400">
                    <svg class="w-4 h-4 {{ $isRtl ? 'scale-x-[-1]' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>
            </form>
        </div>

        {{-- Mobile menu --}}
        <div id="mobile-menu" class="hidden md:hidden border-t border-gray-100 py-3 space-y-1">
            <a href="{{ route('products.index') }}"
               class="block px-3 py-2.5 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50">
                {{ __('app.all_products') }}
            </a>

            @foreach(\App\Models\Category::where('is_active', true)->take(6)->get() as $cat)
                <a href="{{ route('products.index', ['category' => $cat->id]) }}"
                   class="block px-3 py-2.5 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50">
                    {{ $cat->name }}
                </a>
            @endforeach

            {{-- Mobile language switcher --}}
            <div class="pt-2 border-t border-gray-100">
                <form method="POST" action="{{ route('language.switch') }}">
                    @csrf
                    <input type="hidden" name="locale" value="{{ $locale === 'ar' ? 'en' : 'ar' }}">
                    <button type="submit"
                            class="flex items-center gap-2 w-full px-3 py-2.5 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                        </svg>
                        {{ $locale === 'ar' ? 'English' : 'العربية' }}
                    </button>
                </form>
            </div>

            @guest
            <div class="pt-2 flex gap-2 border-t border-gray-100">
                <a href="{{ route('login') }}"
                   class="flex-1 text-center text-sm font-bold text-gray-700 py-2.5 border border-gray-200 rounded-xl">
                    {{ __('app.login') }}
                </a>
                <a href="{{ route('register') }}"
                   class="flex-1 text-center text-sm font-bold text-white py-2.5 rounded-xl"
                   style="background:var(--brand-color)">
                    {{ __('app.create_account') }}
                </a>
            </div>
            @endguest
        </div>
    </nav>
</header>

<script>
document.getElementById('mobile-menu-btn')?.addEventListener('click', () => {
    document.getElementById('mobile-menu')?.classList.toggle('hidden');
});
</script>