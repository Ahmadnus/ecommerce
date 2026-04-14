<footer class="text-gray-400 py-14 border-t border-white border-opacity-5" dir="rtl">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
            
            {{-- البراند / الشعار --}}
            <div class="text-right">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: var(--brand-color)">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                    <span class="font-display font-bold text-lg text-white">ShopCraft</span>
                </div>
                <p class="text-sm">منتجات عالية الجودة، مختارة بعناية لتناسب أسلوب حياتك العصري.</p>
            </div>

            {{-- تسوق --}}
            <div>
                <h4 class="text-white font-semibold text-sm mb-4 uppercase">تسوق</h4>
                <ul class="space-y-2 text-sm">
                    @foreach(\App\Models\Category::where('is_active', true)->take(4)->get() as $cat)
                        <li><a href="{{ route('products.index', ['category' => $cat->id]) }}" class="hover:text-white transition-colors">{{ $cat->name }}</a></li>
                    @endforeach
                </ul>
            </div>

            {{-- المساعدة --}}
            <div>
                <h4 class="text-white font-semibold text-sm mb-4 uppercase">المساعدة</h4>
                <ul class="space-y-2 text-sm">
                    @foreach(\App\Models\Page::active()->ordered()->get() as $fp)
                        <li><a href="{{ route('pages.show', $fp->slug) }}" class="hover:text-white transition-colors">{{ $fp->name }}</a></li>
                    @endforeach
                </ul>
            </div>

            {{-- النشرة البريدية --}}
            <div>
                <h4 class="text-white font-semibold text-sm mb-4 uppercase">النشرة البريدية</h4>
                <p class="text-xs mb-4">اشترك للحصول على آخر العروض والتحديثات.</p>
                <form class="flex gap-2">
                    <input type="email" placeholder="البريد الإلكتروني" class="flex-1 bg-white bg-opacity-5 border border-white border-opacity-10 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-opacity-40">
                    <button class="px-4 py-2 rounded-lg text-white text-sm font-bold transition-transform active:scale-95" style="background-color: var(--brand-color)">انضمام</button>
                </form>
            </div>

        </div>

        {{-- حقوق النشر (إضافة اختيارية لمسة جمالية) --}}
        <div class="mt-12 pt-8 border-t border-white border-opacity-5 text-center text-xs">
            <p>&copy; {{ date('Y') }} جميع الحقوق محفوظة لـ ShopCraft.</p>
        </div>
    </div>
</footer>