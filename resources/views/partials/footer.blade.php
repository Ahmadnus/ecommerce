@php
    $locale  = app()->getLocale();
    $isRtl   = $locale === 'ar';

    $footerTexts = \App\Models\FooterText::where('is_active', true)
        ->orderBy('sort_order')->get()->keyBy('slug');

    $socialLinks = \App\Models\SocialLink::where('is_active', true)
        ->orderBy('sort_order')->get();

    $pages = \App\Models\Page::active()->ordered()->get();

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
    $phoneHref    = $companyInfo?->tel_href ?? '';

    $flagUrl = null;
    if ($companyInfo) {
        $flagUrl = $companyInfo->getFirstMediaUrl('flag_icon');
        if (!$flagUrl && $companyInfo->phone_country_code) {
            $flagUrl = 'https://flagcdn.com/w20/' . strtolower($companyInfo->phone_country_code) . '.png';
        }
    }

    // Settings-based colors (existing dedicated footer settings stay supported)
    $footerBgColor         = \App\Models\Setting::get('footer_bg_color', '#111827');
    $footerLinkColor       = \App\Models\Setting::get('footer_link_color', '#ffffff');
    $footerBottomTextColor = \App\Models\Setting::get('footer_bottom_text_color', '#6b7280');

    // Font size: prefer the new CSS var, fall back to the legacy DB value
    $legacySize   = (int) \App\Models\Setting::get('footer_text_size', 14);
    $footerFontSz = 'var(--footer-font-size, ' . $legacySize . 'px)';

    // Text color: prefer the new CSS var, fall back to the legacy DB value
    $legacyColor   = \App\Models\Setting::get('footer_text_color', '#9ca3af');
    $footerTextClr = 'var(--text-footer, ' . $legacyColor . ')';
@endphp

<footer
    class="py-14 border-t border-white border-opacity-5"
    dir="{{ $isRtl ? 'rtl' : 'ltr' }}"
    style="background-color: {{ $footerBgColor }};">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-10">

            {{-- Column 1: Company info --}}
            <div class="{{ $isRtl ? 'text-right' : 'text-left' }}">
                @if($companyName)
                <span class="font-display font-bold block mb-4"
                      style="color: {{ $footerTextClr }}; font-size: {{ $footerFontSz }};">
                    {{ $companyName }}
                </span>
                @endif

                @if($companyDescription)
                <p class="leading-relaxed mb-4"
                   style="color: {{ $footerTextClr }}; font-size: {{ $footerFontSz }};">
                    {{ $companyDescription }}
                </p>
                @endif

                @if($companyLocation)
                <p class="mb-1" style="color: {{ $footerTextClr }}; font-size: {{ $footerFontSz }};">
                    {{ $companyLocation }}
                </p>
                @endif

                @if($companyPhone)
                <p class="flex items-center gap-2 {{ $isRtl ? 'justify-end' : 'justify-start' }}">
                    @if($flagUrl)
                        <img src="{{ $flagUrl }}" alt="flag" class="inline-block w-5 h-auto">
                    @endif
                    <a href="{{ $phoneHref }}" dir="ltr" class="select-all hover:underline"
                       style="color: {{ $footerLinkColor }}; font-size: {{ $footerFontSz }};">
                        {{ $companyPhone }}
                    </a>
                </p>
                @endif
            </div>

            {{-- Column 2: Quick links --}}
            <div>
                <h4 class="font-bold mb-4"
                    style="color: {{ $footerTextClr }}; font-size: {{ $footerFontSz }};">
                    {{ $isRtl ? 'روابط سريعة' : 'Quick Links' }}
                </h4>
                <div class="flex flex-col gap-3">
                    @forelse($pages as $page)
                    <a href="{{ route('pages.show', $page->slug) }}"
                       class="hover:underline transition-all"
                       style="color: {{ $footerLinkColor }}; font-size: {{ $footerFontSz }};">
                        {{ $page->name ?? $page->title ?? $page->slug }}
                    </a>
                    @empty
                    <span style="color: {{ $footerTextClr }}; font-size: {{ $footerFontSz }};">
                        {{ $isRtl ? 'لا توجد صفحات متاحة' : 'No pages available' }}
                    </span>
                    @endforelse
                </div>
            </div>

            {{-- Column 3: Social --}}
            <div>
                <h4 class="font-bold mb-4"
                    style="color: {{ $footerTextClr }}; font-size: {{ $footerFontSz }};">
                    {{ $isRtl ? 'تابعنا على' : 'Follow Us' }}
                </h4>
                <div class="flex flex-wrap items-center gap-3">
                    @forelse($socialLinks as $slink)
                    <a href="{{ $slink->url ?? '#' }}"
                       target="_blank" rel="noopener noreferrer"
                       class="w-9 h-9 rounded-full bg-white flex items-center justify-center
                              hover:opacity-80 transition-all shadow-sm"
                       title="{{ $slink->platform_name }}">
                        @if($slink->icon_svg)
                            <i class="{{ $slink->icon_svg }} text-lg text-gray-700"></i>
                        @else
                            <span class="text-sm font-bold text-gray-700">
                                {{ mb_substr($slink->platform_name, 0, 1) }}
                            </span>
                        @endif
                    </a>
                    @empty
                    <span style="color: {{ $footerTextClr }}; font-size: {{ $footerFontSz }};">
                        No social links available.
                    </span>
                    @endforelse
                </div>
            </div>

            {{-- Column 4: Support --}}
            <div>
                <h4 class="font-bold mb-4"
                    style="color: {{ $footerTextClr }}; font-size: {{ $footerFontSz }};">
                    {{ $isRtl ? 'الدعم الفني' : 'Support' }}
                </h4>
                <a href="{{ route('contact.create') }}"
                   class="hover:underline transition-colors block"
                   style="color: {{ $footerLinkColor }}; font-size: {{ $footerFontSz }};">
                    {{ __('app.contact_us') ?: 'اتصل بنا' }}
                </a>
            </div>

        </div>

        {{-- Copyright --}}
        <div class="mt-10 border-t border-white border-opacity-5 pt-6 text-center"
             style="color: {{ $footerBottomTextColor }}; font-size: {{ $footerFontSz }};">
            {{ __('app.footer_copyright', ['year' => date('Y')]) }}
        </div>

        <div style="height: 30px"></div>
    </div>
</footer>