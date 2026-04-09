{{-- Footer Partial --}}
<footer class="bg-gray-900 text-gray-400 mt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-10">

            {{-- Brand --}}
            <div class="md:col-span-1">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 bg-brand-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                    <span class="font-display font-bold text-lg text-white">ShopCraft</span>
                </div>
                <p class="text-sm leading-relaxed">
                    Quality products, curated for modern living. Free shipping on orders over $50.
                </p>
            </div>

            {{-- Shop --}}
            <div>
                <h4 class="text-white font-semibold text-sm mb-4 uppercase tracking-wide">Shop</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('products.index') }}" class="hover:text-white transition-colors">All Products</a></li>
                    @foreach(\App\Models\Category::where('is_active', true)->take(4)->get() as $cat)
                        <li><a href="{{ route('products.index', ['category' => $cat->id]) }}" class="hover:text-white transition-colors">{{ $cat->name }}</a></li>
                    @endforeach
                </ul>
            </div>

            {{-- Help --}}
            <div>
                <h4 class="text-white font-semibold text-sm mb-4 uppercase tracking-wide">Help</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="#" class="hover:text-white transition-colors">FAQ</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Shipping Policy</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Returns</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Contact Us</a></li>
                </ul>
            </div>

            {{-- Newsletter --}}
            <div>
                <h4 class="text-white font-semibold text-sm mb-4 uppercase tracking-wide">Stay in the Loop</h4>
                <p class="text-sm mb-3">Get new arrivals and exclusive deals.</p>
                <form class="flex gap-2" onsubmit="return false">
                    <input type="email" placeholder="your@email.com"
                           class="flex-1 bg-gray-800 border border-gray-700 text-white text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-brand-500 placeholder-gray-500">
                    <button class="bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                        Join
                    </button>
                </form>
            </div>
        </div>

        <div class="border-t border-gray-800 mt-10 pt-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
            <p>© {{ date('Y') }} ShopCraft. All rights reserved.</p>
            <div class="flex items-center gap-4">
                {{-- Payment icons --}}
                <span class="bg-gray-800 px-2 py-1 rounded text-gray-400">Visa</span>
                <span class="bg-gray-800 px-2 py-1 rounded text-gray-400">Mastercard</span>
                <span class="bg-gray-800 px-2 py-1 rounded text-gray-400">PayPal</span>
            </div>
        </div>
    </div>
</footer>
