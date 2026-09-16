{{--
    partials/footer.blade.php — storefront footer.

    Four admin-driven columns (company info, pages, social, support) over a
    light bottom strip, mirroring the reference store's footer rhythm.

    All content remains dynamic: FooterCompanyInfo, Page, SocialLink and the
    footer_* Settings keys drive everything. Presentation now comes from the
    .sf-footer primitives, so colours and sizing follow the theme tokens
    instead of being written inline on every element.
--}}

@php
    $locale = app()->getLocale();
    $isRtl  = $locale === 'ar';

    $socialLinks = \App\Models\SocialLink::where('is_active', true)
        ->orderBy('sort_order')->get();

    $pages = \App\Models\Page::active()->ordered()->get();

    $companyInfo = \App\Models\FooterCompanyInfo::active()->first();

    $trans = fn (?string $field) => $companyInfo
        ? ($companyInfo->getTranslation($field, $locale, false)
            ?: $companyInfo->getTranslation($field, config('app.fallback_locale', 'en'), false))
        : '';

    $companyName        = $trans('company_name');
    $companyDescription = $trans('description');
    $companyLocation    = $trans('location');

    $companyPhone = $companyInfo?->phone ?? '';
    $phoneHref    = $companyInfo?->tel_href ?? '';

    $flagUrl = null;
    if ($companyInfo) {
        $flagUrl = $companyInfo->getFirstMediaUrl('flag_icon');
        if (! $flagUrl && $companyInfo->phone_country_code) {
            $flagUrl = 'https://flagcdn.com/w20/' . strtolower($companyInfo->phone_country_code) . '.png';
        }
    }

    // The dedicated footer_link_color setting still wins for links; the rest
    // of the footer reads --text-footer / --footer-bg from :root.
    $footerLinkColor = \App\Models\Setting::get('footer_link_color', '');
@endphp

<footer class="sf-footer" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
    <div class="sf-container">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">

            {{-- Column 1 — company --}}
            <div>
                @if($companyName)
                    <p class="sf-footer__title">{{ $companyName }}</p>
                @endif

                @if($companyDescription)
                    <p class="leading-relaxed mb-3">{{ $companyDescription }}</p>
                @endif

                @if($companyLocation)
                    <p class="mb-2 flex items-start gap-2">
                        <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor"
                             stroke-width="1.6" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>{{ $companyLocation }}</span>
                    </p>
                @endif

                @if($companyPhone)
                    <p class="flex items-center gap-2">
                        @if($flagUrl)
                            <img src="{{ $flagUrl }}" alt="" class="inline-block w-5 h-auto" loading="lazy">
                        @endif
                        <a href="{{ $phoneHref }}" dir="ltr" class="select-all hover:underline"
                           @if($footerLinkColor) style="color: {{ $footerLinkColor }}" @endif>
                            {{ $companyPhone }}
                        </a>
                    </p>
                @endif
            </div>

            {{-- Column 2 — pages --}}
            <nav aria-label="{{ $isRtl ? 'روابط سريعة' : 'Quick Links' }}">
                <p class="sf-footer__title">{{ $isRtl ? 'روابط سريعة' : 'Quick Links' }}</p>
                <ul class="flex flex-col gap-2.5">
                    <li>
                        <a href="{{ route('products.index') }}"
                           @if($footerLinkColor) style="color: {{ $footerLinkColor }}" @endif>
                            {{ __('app.all_products') }}
                        </a>
                    </li>
                    @foreach($pages as $page)
                        <li>
                            <a href="{{ route('pages.show', $page->slug) }}"
                               @if($footerLinkColor) style="color: {{ $footerLinkColor }}" @endif>
                                {{ $page->name ?? $page->title ?? $page->slug }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>

            {{-- Column 3 — social --}}
            <div>
                <p class="sf-footer__title">{{ $isRtl ? 'تابعنا على' : 'Follow Us' }}</p>
                @if($socialLinks->isNotEmpty())
                    <ul class="flex flex-wrap items-center gap-2.5">
                        @foreach($socialLinks as $slink)
                            <li>
                                <a href="{{ $slink->url ?? '#' }}"
                                   target="_blank" rel="noopener noreferrer"
                                   class="w-9 h-9 flex items-center justify-center transition-all hover:opacity-80"
                                   style="border-radius: var(--radius-badge);
                                          background: color-mix(in srgb, var(--text-footer) 18%, transparent);"
                                   aria-label="{{ $slink->platform_name }}">
                                    @if($slink->icon_svg)
                                        <i class="{{ $slink->icon_svg }} text-lg" aria-hidden="true"></i>
                                    @else
                                        <span class="text-sm font-bold" aria-hidden="true">
                                            {{ mb_substr($slink->platform_name, 0, 1) }}
                                        </span>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p style="color: var(--text-muted)">
                        {{ $isRtl ? 'لا توجد روابط بعد' : 'No social links yet.' }}
                    </p>
                @endif
            </div>

            {{-- Column 4 — support --}}
            <div>
                <p class="sf-footer__title">{{ $isRtl ? 'الدعم الفني' : 'Support' }}</p>
                <ul class="flex flex-col gap-2.5">
                    <li>
                        <a href="{{ route('contact.create') }}"
                           @if($footerLinkColor) style="color: {{ $footerLinkColor }}" @endif>
                            {{ __('app.contact_us') }}
                        </a>
                    </li>
                    @auth
                        <li>
                            <a href="{{ route('orders.index') }}"
                               @if($footerLinkColor) style="color: {{ $footerLinkColor }}" @endif>
                                {{ __('app.orders.heading') }}
                            </a>
                        </li>
                    @endauth
                    <li>
                        <a href="{{ route('cart.index') }}"
                           @if($footerLinkColor) style="color: {{ $footerLinkColor }}" @endif>
                            {{ __('app.cart.heading') }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="sf-footer__bottom">
        {{ __('app.footer_copyright', ['year' => date('Y')]) }}
    </div>
</footer>
