@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';

    $footerTexts = \App\Models\FooterText::where('is_active', true)
        ->orderBy('sort_order')
        ->get()
        ->keyBy('slug');

    $socialLinks = \App\Models\SocialLink::where('is_active', true)
        ->orderBy('sort_order')
        ->get();

    $pages = \App\Models\Page::active()->ordered()->get();

    $getFooterText = function (?\App\Models\FooterText $item, string $fallback = '') use ($locale) {
        if (!$item) {
            return $fallback;
        }

        return $item->text[$locale]
            ?? $item->text[config('app.fallback_locale', 'en')]
            ?? $fallback;
    };

    $brandText = $footerTexts->get('footer_brand');
    $taglineText = $footerTexts->get('footer_tagline');
@endphp

<footer class="text-gray-400 py-14 border-t border-white border-opacity-5"
        dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-10">

          <div>
<div class="{{ $isRtl ? 'text-right' : 'text-left' }}">
    <span class="font-display font-bold text-lg text-white block mb-4">
        Creacure Ltd
    </span>

    <p class="text-sm leading-relaxed mb-2">
        Creacure UK is a modern eyewear brand offering stylish and comfortable reading glasses for everyday use.
        We combine clear vision, lightweight design, and modern style for both men and women.
    </p>

    <p class="text-sm mb-1">
        London, United Kingdom
    </p>

    <p class="text-sm flex items-center gap-2 {{ $isRtl ? 'justify-end' : 'justify-start' }}">
    <img src="https://flagcdn.com/w20/gb.png" alt="UK" class="inline-block">
      <a href="tel:+447782281157" dir="ltr" class="select-all hover:underline">
            +44 7782 281157
        </a>
</p>
</div>
</div>

            <div>
                <div class="flex flex-col gap-3">
                    @forelse($socialLinks as $slink)
                        @php
                            $icon = $slink->getFirstMediaUrl('icons');
                        @endphp

                        <a href="{{ $slink->url ?? '#' }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="flex items-center gap-3 hover:text-white transition-all group"
                           title="{{ $slink->platform_name }}">
                            <span class="w-12 h-12 rounded-full bg-white bg-opacity-5 flex items-center justify-center group-hover:scale-110 transition-transform overflow-hidden">
                                @if($icon)
                                    <img src="{{ $icon }}" class="w-8 h-8 object-contain" alt="{{ $slink->platform_name }}">
                                @else
                                    <span class="text-base font-bold">{{ mb_substr($slink->platform_name, 0, 1) }}</span>
                                @endif
                            </span>
                            <span class="text-sm">{{ $slink->platform_name }}</span>
                        </a>
                    @empty
                        <span class="text-sm text-gray-500">No social links available.</span>
                    @endforelse
                </div>
            </div>

            <div>
                <ul class="space-y-2 text-sm">
                    @forelse($pages as $fp)
                        <li>
                            <a href="{{ route('pages.show', $fp->slug) }}"
                               class="hover:text-white transition-colors">
                                {{ $fp->name }}
                            </a>
                        </li>
                    @empty
                        <li class="text-gray-500">No pages available.</li>
                    @endforelse
                </ul>
            </div>

            <div>
                <a href="{{ route('contact.create') }}" class="hover:text-white transition-colors">
                    {{ __('app.contact_us') ?: 'اتصل بنا' }}
                </a>
            </div>
        </div>

        <div class="mt-10 border-t border-white border-opacity-5 pt-6 text-center text-xs text-gray-500">
            {{ __('app.footer_copyright', ['year' => date('Y')]) }}
        </div>

        <div style="height: 30px"></div>
    </div>
</footer>