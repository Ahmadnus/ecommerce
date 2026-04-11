{{-- resources/views/partials/navbar.blade.php --}}

<header class="sticky top-0 z-40 bg-white/95 backdrop-blur border-b border-gray-100 shadow-sm">
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- ── Logo ── --}}
            <a href="/">
                <img src="{{ $logoUrl }}" alt="Logo" class="h-10 w-auto">
            </a>

            {{-- ── Desktop Navigation ── --}}
            <div class="hidden md:flex items-center gap-8">
                <a href="{{ route('products.index') }}"
                   class="text-sm font-medium text-gray-600 hover:text-brand-600 transition-colors
                          {{ request()->routeIs('products.index') ? 'text-brand-600' : '' }}">
                    Shop
                </a>
                @foreach(\App\Models\Category::where('is_active', true)->take(4)->get() as $cat)
                    <a href="{{ route('products.index', ['category' => $cat->id]) }}"
                       class="text-sm font-medium text-gray-600 hover:text-brand-600 transition-colors">
                        {{ $cat->name }}
                    </a>
                @endforeach
            </div>

            {{-- ── Right side: Wishlist + Cart + Mobile toggle ── --}}
            <div class="flex items-center gap-1">

                {{-- ❤️ Wishlist Icon (authenticated users only) --}}
                @auth
                <a href="{{ route('wishlist.index') }}"
                   class="relative group p-2 rounded-lg hover:bg-gray-100 transition-colors"
                   title="المفضلة"
                   aria-label="المفضلة">

                    {{-- Outline heart — default state --}}
                    <svg class="w-6 h-6 text-gray-700 group-hover:text-red-500 transition-colors"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>

                    {{-- Live count badge --}}
                    @php
                        $wishlistCount = auth()->user()->wishlistedProducts()->count();
                    @endphp
                    <span class="wishlist-count absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white
                                 text-xs font-bold rounded-full flex items-center justify-center"
                          style="{{ $wishlistCount > 0 ? '' : 'display:none' }}">
                        {{ $wishlistCount }}
                    </span>
                </a>
                @endauth

                {{-- 🛒 Cart Icon --}}
                <a href="{{ route('cart.index') }}"
                   class="relative group p-2 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-6 h-6 text-gray-700 group-hover:text-brand-600 transition-colors"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    @php $cartCount = app(\App\Services\CartService::class)->getItemCount(); @endphp
                    <span class="cart-count absolute -top-1 -right-1 w-5 h-5 bg-brand-600 text-white
                                 text-xs font-bold rounded-full flex items-center justify-center"
                          style="{{ $cartCount > 0 ? '' : 'display:none' }}">
                        {{ $cartCount }}
                    </span>
                </a>

                {{-- Auth links (guest) --}}
                @guest
                <div class="hidden md:flex items-center gap-2 mr-2">
                    <a href="{{ route('login') }}"
                       class="text-sm font-medium text-gray-600 hover:text-brand-600 transition-colors px-3 py-1.5">
                        تسجيل الدخول
                    </a>
                    <a href="{{ route('register') }}"
                       class="text-sm font-bold bg-brand-600 text-white px-4 py-1.5 rounded-xl hover:opacity-90 transition">
                        إنشاء حساب
                    </a>
                </div>
                @endguest

                {{-- Auth links (logged in) --}}
                @auth
                <div class="hidden md:flex items-center gap-2 mr-2" x-data="{ open: false }">
                    <button @click="open = !open"
                            class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-100 transition-colors text-sm font-medium text-gray-700">
                        <span class="w-7 h-7 bg-brand-100 text-brand-700 rounded-full flex items-center justify-center font-bold text-xs flex-shrink-0">
                            {{ mb_substr(auth()->user()->name, 0, 1) }}
                        </span>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    {{-- Dropdown --}}
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         @click.outside="open = false"
                         class="absolute top-14 left-4 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50"
                         style="display:none">
                        <div class="px-4 py-2 border-b border-gray-100">
                            <p class="text-xs font-bold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-[11px] text-gray-400 truncate">{{ auth()->user()->phone }}</p>
                        </div>
                        <a href="{{ route('wishlist.index') }}"
                           class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                            المفضلة
                        </a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="flex items-center gap-2.5 w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                تسجيل الخروج
                            </button>
                        </form>
                    </div>
                </div>
                @endauth

                {{-- Mobile hamburger --}}
                <button id="mobile-menu-btn"
                        class="md:hidden p-2 rounded-lg hover:bg-gray-100 transition-colors"
                        aria-label="Toggle menu">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- ── Mobile Menu ── --}}
        <div id="mobile-menu" class="hidden md:hidden border-t border-gray-100 py-3 space-y-1">
            <a href="{{ route('products.index') }}"
               class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-brand-600 transition-colors">
                جميع المنتجات
            </a>
            @foreach(\App\Models\Category::where('is_active', true)->take(5)->get() as $cat)
                <a href="{{ route('products.index', ['category' => $cat->id]) }}"
                   class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-brand-600 transition-colors">
                    {{ $cat->name }}
                </a>
            @endforeach

            {{-- Mobile auth links --}}
            <div class="pt-2 border-t border-gray-100 space-y-1">
                @guest
                <a href="{{ route('login') }}"
                   class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    تسجيل الدخول
                </a>
                <a href="{{ route('register') }}"
                   class="block px-3 py-2 rounded-lg text-sm font-bold text-brand-600 hover:bg-brand-50 transition-colors">
                    إنشاء حساب
                </a>
                @endguest

                @auth
                <a href="{{ route('wishlist.index') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                    المفضلة
                </a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="flex items-center gap-2 w-full px-3 py-2 rounded-lg text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        تسجيل الخروج
                    </button>
                </form>
                @endauth
            </div>
        </div>

    </nav>
</header>

<script>
    document.getElementById('mobile-menu-btn')?.addEventListener('click', () => {
        document.getElementById('mobile-menu').classList.toggle('hidden');
    });
</script>