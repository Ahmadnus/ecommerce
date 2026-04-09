{{-- ═══════════════════════════════════════════════════════════════════════
     Navbar Partial
     Responsive navigation with sticky behaviour, logo, links, cart icon.
════════════════════════════════════════════════════════════════════════ --}}
<header class="sticky top-0 z-40 bg-white/95 backdrop-blur border-b border-gray-100 shadow-sm">
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Logo --}}
            <a href="/">
    <img src="{{ $logoUrl }}" alt="Logo" class="h-10 w-auto">
</a>

            {{-- Desktop Navigation --}}
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

            {{-- Right side: Cart + Auth --}}
            <div class="flex items-center gap-4">
                {{-- Cart Icon --}}
                <a href="{{ route('cart.index') }}" class="relative group p-2 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-6 h-6 text-gray-700 group-hover:text-brand-600 transition-colors"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    {{-- Dynamic count badge --}}
                    @php $cartCount = app(\App\Services\CartService::class)->getItemCount(); @endphp
                    <span class="cart-count absolute -top-1 -right-1 w-5 h-5 bg-brand-600 text-white text-xs font-bold rounded-full flex items-center justify-center"
                          style="{{ $cartCount > 0 ? '' : 'display:none' }}">
                        {{ $cartCount }}
                    </span>
                </a>

                {{-- Mobile menu toggle --}}
                <button id="mobile-menu-btn"
                        class="md:hidden p-2 rounded-lg hover:bg-gray-100 transition-colors"
                        aria-label="Toggle menu">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div id="mobile-menu" class="hidden md:hidden border-t border-gray-100 py-3 space-y-1 animate-fade-in">
            <a href="{{ route('products.index') }}"
               class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-brand-600 transition-colors">
                All Products
            </a>
            @foreach(\App\Models\Category::where('is_active', true)->take(5)->get() as $cat)
                <a href="{{ route('products.index', ['category' => $cat->id]) }}"
                   class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-brand-600 transition-colors">
                    {{ $cat->name }}
                </a>
            @endforeach
        </div>
    </nav>
</header>

<script>
    // Mobile menu toggle
    document.getElementById('mobile-menu-btn')?.addEventListener('click', () => {
        document.getElementById('mobile-menu').classList.toggle('hidden');
    });
</script>
