<footer class="text-gray-400 py-14 border-t border-white border-opacity-5" dir="rtl">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
            
            {{-- البراند --}}
            <div class="text-right">
                <span class="font-display font-bold text-lg text-white block mb-4">ShopCraft</span>
                <p class="text-sm">منتجات عالية الجودة، مختارة بعناية لتناسب أسلوب حياتك.</p>
            </div>

            {{-- روابط ديناميكية للسوشيال ميديا --}}
           <div>
    <div class="flex flex-col gap-3">
        @php
            $socialLinks = \App\Models\SocialLink::where('is_active', true)->orderBy('sort_order')->get();
        @endphp

        @foreach($socialLinks as $slink)
            <a href="{{ $slink->url }}" target="_blank"
               class="flex items-center gap-3 hover:text-white transition-all group"
               title="{{ $slink->platform_name }}">

                <span class="w-8 h-8 rounded-full bg-white bg-opacity-5 flex items-center justify-center group-hover:scale-110 transition-transform">
                    @if($slink->icon_svg)
                        {!! $slink->icon_svg !!}
                    @else
                        <span class="text-[10px] font-bold">
                            {{ substr($slink->platform_name, 0, 1) }}
                        </span>
                    @endif
                </span>

                <span class="text-sm">{{ $slink->platform_name }}</span>
            </a>
        @endforeach
    </div>
</div>

            {{-- قسم المساعدة (يمكنك تركه كما هو أو جعله ديناميكياً لاحقاً) --}}
            <div>
             
                <ul class="space-y-2 text-sm">
                    @foreach(\App\Models\Page::active()->ordered()->get() as $fp)
                        <li><a href="{{ route('pages.show', $fp->slug) }}" class="hover:text-white transition-colors">{{ $fp->name }}</a></li>
                    @endforeach
                </ul>
            </div>

            {{-- النشرة البريدية --}}
            <div>
               
                <form class="flex gap-2">
                    <input type="email" placeholder="بريدك..." class="flex-1 bg-white bg-opacity-5 border border-white border-opacity-10 rounded-lg px-3 py-2 text-white text-sm focus:outline-none">
                    <button class="px-4 py-2 rounded-lg text-white text-sm font-bold" style="background-color: var(--brand-color)">انضمام</button>
                </form>
            </div>
<div
style="height: 30px;"></div>
        </div>
        <div class="mt-10 border-t border-white border-opacity-5 pt-6 text-center text-xs text-gray-500">
    © {{ date('Y') }} جلجام. جميع الحقوق محفوظة.
</div>
    </div>
</footer>