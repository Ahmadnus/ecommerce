<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    @php
        // جلب جميع الإعدادات مرة واحدة لتقليل الضغط على الداتابيز
        $siteSettings = \App\Models\Setting::pluck('value', 'key');

        // إعدادات SEO الخاصة بالـ splash
        $seo = \App\Models\SeoSetting::forType('splash');
        $locale = app()->getLocale();

        $seoTitle = $seo->getTranslation('seo_title', $locale, false)
            ?: ($siteSettings['site_name'] ?? 'My Store');

        $seoDescription = $seo->getTranslation('seo_description', $locale, false) ?: '';
        $seoKeywords = $seo->getTranslation('seo_keywords', $locale, false) ?: '';

        $ogTitle = $seo->getTranslation('og_title', $locale, false) ?: $seoTitle;
        $ogDescription = $seo->getTranslation('og_description', $locale, false) ?: $seoDescription;
        $twitterTitle = $seo->getTranslation('twitter_title', $locale, false) ?: $seoTitle;
        $twitterDescription = $seo->getTranslation('twitter_description', $locale, false) ?: $seoDescription;

        $ogImage = $seo->getFirstMediaUrl('og_image');
        $favicon = $seo->getFirstMediaUrl('favicon');

        $robots = $seo->robots ?? 'index, follow';
        $ogType = $seo->og_type ?? 'website';
        $canonical = $seo->canonical_url ?: url()->current();
        $twitterCard = $seo->twitter_card ?? 'summary_large_image';

        // إعدادات اللوغو
        $logoUrl = \App\Models\Setting::mediaHolder()->getFirstMediaUrl('logo')
            ?: asset('images/default-logo.png');

        // إعدادات النصوص
        $mainTitle   = $siteSettings['splash_title_main'] ?? 'COOL';
        $subTitle    = $siteSettings['splash_title_sub'] ?? 'VIBES';

        // إعدادات الألوان
        $mainColor = $siteSettings['splash_color_main'] ?? '#000000';
        $subColor  = $siteSettings['splash_color_sub'] ?? '#D1D5DB';

        // إعدادات الخطوط
        $fontSize   = $siteSettings['splash_font_size'] ?? 'text-6xl';
        $fontFamily = $siteSettings['splash_font_family'] ?? "'Montserrat', sans-serif";
    @endphp

    <title>{{ $seoTitle }}</title>

    @if($seoDescription)
        <meta name="description" content="{{ $seoDescription }}">
    @endif

    @if($seoKeywords)
        <meta name="keywords" content="{{ $seoKeywords }}">
    @endif

    <meta name="robots" content="{{ $robots }}">
    <meta name="googlebot" content="{{ $robots }}">
    <link rel="canonical" href="{{ $canonical }}">

    <meta property="og:type" content="{{ $ogType }}">
    <meta property="og:title" content="{{ $ogTitle }}">
    <meta property="og:url" content="{{ url()->current() }}">
    @if($ogDescription)
        <meta property="og:description" content="{{ $ogDescription }}">
    @endif
    @if($ogImage)
        <meta property="og:image" content="{{ $ogImage }}">
    @endif

    <meta name="twitter:card" content="{{ $twitterCard }}">
    <meta name="twitter:title" content="{{ $twitterTitle }}">
    @if($twitterDescription)
        <meta name="twitter:description" content="{{ $twitterDescription }}">
    @endif
    @if($ogImage)
        <meta name="twitter:image" content="{{ $ogImage }}">
    @endif

    @if($favicon)
        <link rel="icon" href="{{ $favicon }}">
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;400;900&family=Cairo:wght@400;700;900&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    animation: {
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'float': 'float 4s ease-in-out infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0px) rotate(0deg)' },
                            '50%': { transform: 'translateY(-15px) rotate(2deg)' },
                        }
                    }
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }

        body {
            background-color: #ffffff;
            overflow: hidden;
            -webkit-font-smoothing: antialiased;
        }

        @keyframes logoEntry {
            0% {
                transform: scale(0.3) rotate(-15deg);
                opacity: 0;
                filter: blur(15px);
            }
            70% {
                transform: scale(1.1) rotate(5deg);
            }
            100% {
                transform: scale(1) rotate(0deg);
                opacity: 1;
                filter: blur(0);
            }
        }

        @keyframes lineGrow {
            0% { width: 0; opacity: 0; }
            100% { width: 100px; opacity: 1; }
        }

        .animate-logo-entry {
            animation: logoEntry 1.8s cubic-bezier(0.17, 0.67, 0.83, 0.67) forwards;
        }

        .animate-line {
            animation: lineGrow 2s cubic-bezier(0.22, 1, 0.36, 1) 1s forwards;
        }
    </style>
</head>

<body class="font-sans antialiased text-gray-900">

    <div x-data="{ startFloating: false }"
         x-init="
            setTimeout(() => { window.location.href = '/products' }, 3800);
            setTimeout(() => { startFloating = true }, 1800);
         "
         class="h-screen w-full flex flex-col items-center justify-center relative bg-white">

        <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-32 -left-32 w-full h-full bg-gray-50/50 rounded-full blur-3xl transform rotate-12"></div>
            <div class="absolute -bottom-32 -right-32 w-full h-full bg-gray-50/50 rounded-full blur-3xl transform -rotate-12"></div>
        </div>

        <div class="relative z-10 flex flex-col items-center p-6 text-center">

            <div class="animate-logo-entry mb-12 flex flex-col items-center">
                <div :class="{ 'animate-float': startFloating }" class="transition-all duration-1000">
                    <img src="{{ $logoUrl }}"
                         alt="{{ $siteSettings['site_name'] ?? 'Logo' }}"
                         class="w-56 md:w-80 h-auto object-contain drop-shadow-[0_40px_40px_rgba(0,0,0,0.1)]">
                </div>
            </div>

            <div class="overflow-hidden flex flex-col items-center" style="font-family: {!! $fontFamily !!};">
                <h1 class="{{ $fontSize }} font-black leading-none tracking-[0.3em] uppercase mb-5 transition-all duration-300"
                    style="color: {{ $mainColor }};">
                    {{ $mainTitle }}
                    <span class="font-light tracking-tight transition-all duration-300"
                          style="color: {{ $subColor }};">
                        {{ $subTitle }}
                    </span>
                </h1>

                <div class="h-1 w-0 animate-line rounded-full" style="background-color: {{ $mainColor }};"></div>
            </div>

        </div>

        <div class="absolute bottom-10 text-[10px] font-black text-gray-200 tracking-[0.4em] uppercase">
            &copy; {{ date('Y') }} PREMIUM FASHION EXPERIENCE
        </div>
    </div>

</body>
</html>