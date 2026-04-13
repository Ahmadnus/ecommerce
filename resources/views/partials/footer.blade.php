<footer class="text-gray-400 py-14 border-t border-white border-opacity-5">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
            {{-- Brand --}}
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: var(--brand-color)">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    </div>
                    <span class="font-display font-bold text-lg text-white">ShopCraft</span>
                </div>
                <p class="text-sm">Quality products, curated for modern living.</p>
            </div>

            {{-- Shop & Help (Dynamic) --}}
            <div>
                <h4 class="text-white font-semibold text-sm mb-4 uppercase">Shop</h4>
                <ul class="space-y-2 text-sm">
                    @foreach(\App\Models\Category::where('is_active', true)->take(4)->get() as $cat)
                        <li><a href="{{ route('products.index', ['category' => $cat->id]) }}" class="hover:text-white">{{ $cat->name }}</a></li>
                    @endforeach
                </ul>
            </div>

            <div>
                <h4 class="text-white font-semibold text-sm mb-4 uppercase">Help</h4>
                <ul class="space-y-2 text-sm">
                    @foreach(\App\Models\Page::active()->ordered()->get() as $fp)
                        <li><a href="{{ route('pages.show', $fp->slug) }}" class="hover:text-white">{{ $fp->name }}</a></li>
                    @endforeach
                </ul>
            </div>

            {{-- Newsletter --}}
            <div>
                <h4 class="text-white font-semibold text-sm mb-4 uppercase">Newsletter</h4>
                <form class="flex gap-2">
                    <input type="email" placeholder="Email" class="flex-1 bg-white bg-opacity-5 border border-white border-opacity-10 rounded-lg px-3 py-2 text-white text-sm">
                    <button class="px-4 py-2 rounded-lg text-white text-sm" style="background-color: var(--brand-color)">Join</button>
                </form>
            </div>
        </div>
    </div>
</footer>