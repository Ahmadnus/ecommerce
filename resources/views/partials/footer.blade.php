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

    $companyInfo = \App\Models\FooterCompanyInfo::active()->first();

    $companyName = $companyInfo
        ? ($companyInfo->getTranslation('company_name', $locale, false)
            ?: $companyInfo->getTranslation('company_name', config('app.fallback_locale', 'en'), false))
        : '';

    $companyDescription = $companyInfo
        ? ($companyInfo->getTranslation('description', $locale, false)
            ?: $companyInfo->getTranslation('description', config('app.fallback_locale', 'en'), false))
        : '';

    $companyLocation = $companyInfo
        ? ($companyInfo->getTranslation('location', $locale, false)
            ?: $companyInfo->getTranslation('location', config('app.fallback_locale', 'en'), false))
        : '';

    $companyPhone = $companyInfo?->phone ?? '';
    $phoneHref = $companyInfo?->tel_href ?? '';

    $flagUrl = null;
    if ($companyInfo) {
        $flagUrl = $companyInfo->getFirstMediaUrl('flag_icon');
        if (!$flagUrl && $companyInfo->phone_country_code) {
            $flagUrl = 'https://flagcdn.com/w20/' . strtolower($companyInfo->phone_country_code) . '.png';
        }
    }

    $footerBgColor = \App\Models\Setting::get('footer_bg_color', '#111827');
    $footerTextColor = \App\Models\Setting::get('footer_text_color', '#9ca3af');
    $footerLinkColor = \App\Models\Setting::get('footer_link_color', '#ffffff');
    $footerBottomTextColor = \App\Models\Setting::get('footer_bottom_text_color', '#6b7280');
    $footerTextSize = (int) \App\Models\Setting::get('footer_text_size', 14);
@endphp

<footer
    class="py-14 border-t border-white border-opacity-5"
    dir="{{ $isRtl ? 'rtl' : 'ltr' }}"
    style="background-color: {{ $footerBgColor }};"
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-10">

            {{-- العمود الأول: معلومات الشركة --}}
            <div>
                <div class="{{ $isRtl ? 'text-right' : 'text-left' }}">
                    @if($companyName)
                        <span class="font-display font-bold block mb-4"
                              style="font-size: {{ $footerTextSize + 4 }}px; color: {{ $footerTextColor }};">
                            {{ $companyName }}
                        </span>
                    @endif

                    @if($companyDescription)
                        <p class="leading-relaxed mb-4"
                           style="font-size: {{ $footerTextSize }}px; color: {{ $footerTextColor }};">
                            {{ $companyDescription }}
                        </p>
                    @endif

                    @if($companyLocation)
                        <p class="mb-1"
                           style="font-size: {{ $footerTextSize }}px; color: {{ $footerTextColor }};">
                            {{ $companyLocation }}
                        </p>
                    @endif

                    @if($companyPhone)
                        <p class="flex items-center gap-2 {{ $isRtl ? 'justify-end' : 'justify-start' }}"
                           style="font-size: {{ $footerTextSize }}px;">
                            @if($flagUrl)
                                <img src="{{ $flagUrl }}" alt="flag" class="inline-block w-5 h-auto">
                            @endif

                            <a href="{{ $phoneHref }}" dir="ltr" class="select-all hover:underline"
                               style="color: {{ $footerLinkColor }};">
                                {{ $companyPhone }}
                            </a>
                        </p>
                    @endif
                </div>
            </div>

            {{-- العمود الثاني: الصفحات --}}
            <div>
                <h4 class="font-bold mb-4" style="color: {{ $footerTextColor }}; font-size: {{ $footerTextSize + 2 }}px;">
                    {{ $isRtl ? 'روابط سريعة' : 'Quick Links' }}
                </h4>
                <div class="flex flex-col gap-3">
                    @foreach($pages as $page)
                        <a href="{{ route('pages.show', $page->slug) }}" 
                           class="hover:underline transition-all"
                           style="font-size: {{ $footerTextSize }}px; color: {{ $footerLinkColor }};">
                            {{ $page->title }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- العمود الثالث: السوشال ميديا --}}
            <div>
                <h4 class="font-bold mb-4" style="color: {{ $footerTextColor }}; font-size: {{ $footerTextSize + 2 }}px;">
                    {{ $isRtl ? 'تابعنا على' : 'Follow Us' }}
                </h4>
                <div class="flex flex-col gap-3">
                    @forelse($socialLinks as $slink)
                        <a href="{{ $slink->url ?? '#' }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="flex items-center gap-3 hover:opacity-80 transition-all group"
                           title="{{ $slink->platform_name }}">

                            <span class="w-8 h-8 rounded-full bg-white flex items-center justify-center group-hover:scale-110 transition-transform overflow-hidden shadow-sm">
                                @if($slink->icon_svg)
                                    <i class="{{ $slink->icon_svg }} text-lg text-gray-700"></i>
                                @else
                                    <span class="text-sm font-bold text-gray-700">
                                        {{ mb_substr($slink->platform_name, 0, 1) }}
                                    </span>
                                @endif
                            </span>

                            <span class="text-sm"
                                  style="font-size: {{ $footerTextSize }}px; color: {{ $footerLinkColor }};">
                                {{ $slink->platform_name }}
                            </span>
                        </a>
                    @empty
                        <span class="text-sm text-gray-500"
                              style="font-size: {{ $footerTextSize }}px;">
                            No social links available.
                        </span>
                    @endforelse
                </div>
            </div>

            {{-- العمود الرابع: اتصل بنا --}}
            <div>
                <h4 class="font-bold mb-4" style="color: {{ $footerTextColor }}; font-size: {{ $footerTextSize + 2 }}px;">
                    {{ $isRtl ? 'الدعم الفني' : 'Support' }}
                </h4>
                <a href="{{ route('contact.create') }}"
                   class="hover:underline transition-colors block"
                   style="font-size: {{ $footerTextSize }}px; color: {{ $footerLinkColor }};">
                    {{ __('app.contact_us') ?: 'اتصل بنا' }}
                </a>
            </div>
        </div>

        {{-- الحقوق --}}
        <div class="mt-10 border-t border-white border-opacity-5 pt-6 text-center text-xs"
             style="color: {{ $footerBottomTextColor }}; font-size: {{ max($footerTextSize - 2, 10) }}px;">
            {{ __('app.footer_copyright', ['year' => date('Y')]) }}
        </div>

        <div style="height: 30px"></div>
    </div>
</footer>