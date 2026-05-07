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
@endphp

<footer class="text-gray-400 py-14 border-t border-white border-opacity-5"
        dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-10">

            <div>
                <div class="{{ $isRtl ? 'text-right' : 'text-left' }}">
                    @if($companyName)
    <span class="font-display font-bold text-lg text-white block mb-4">
        {{ $companyName }}
    </span>
@endif
@if($companyDescription)
    <p class="text-sm leading-relaxed mb-2">
        {{ $companyDescription }}
    </p>
@endif

@if($companyLocation)
    <p class="text-sm mb-1">
        {{ $companyLocation }}
    </p>
@endif

@if($companyPhone)
    <p class="text-sm flex items-center gap-2 {{ $isRtl ? 'justify-end' : 'justify-start' }}">
        @if($flagUrl)
            <img src="{{ $flagUrl }}" alt="flag" class="inline-block w-5 h-auto">
        @endif

        <a href="{{ $phoneHref }}" dir="ltr" class="select-all hover:underline">
            {{ $companyPhone }}
        </a>
    </p>
@endif
                </div>
            </div>

            <div>
<div class="flex flex-col gap-3">
    @forelse($socialLinks as $slink)

        <a href="{{ $slink->url ?? '#' }}"
           target="_blank"
           rel="noopener noreferrer"
           class="flex items-center gap-3 hover:text-white transition-all group"
           title="{{ $slink->platform_name }}">

            <span class="w-12 h-12 rounded-full bg-white flex items-center justify-center group-hover:scale-110 transition-transform overflow-hidden shadow-sm">

                @if($slink->icon_svg)
                    <i class="fa-brands {{ $slink->icon_svg }} text-2xl text-gray-700"></i>
                @else
                    <span class="text-base font-bold text-gray-700">
                        {{ mb_substr($slink->platform_name, 0, 1) }}
                    </span>
                @endif

            </span>

            <span class="text-sm">{{ $slink->platform_name }}</span>
        </a>

    @empty
        <span class="text-sm text-gray-500">
            No social links available.
        </span>
    @endforelse
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