<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>مرحباً بك | My Store</title>
    
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

        /* أنيميشن دخول اللوغو الاحترافي */
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

        /* أنيميشن تمدد الخط السفلي */
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

@php
    // جلب جميع الإعدادات مرة واحدة لتقليل الضغط على الداتابيز
    $siteSettings = \App\Models\Setting::pluck('value', 'key');
    
    // إعدادات اللوغو
    $logoPath = $siteSettings['site_logo'] ?? null;
    $logoUrl  = $logoPath ? asset('storage/' . $logoPath) : asset('images/default-logo.png');
    
    // إعدادات النصوص
    $mainTitle   = $siteSettings['splash_title_main'] ?? 'COOL';
    $subTitle    = $siteSettings['splash_title_sub'] ?? 'VIBES';
    $loadingText = $siteSettings['splash_loading_text'] ?? 'جاري تحضير تجربتك الخاصة...';
    
    // إعدادات الألوان
    $mainColor = $siteSettings['splash_color_main'] ?? '#000000';
    $subColor  = $siteSettings['splash_color_sub'] ?? '#D1D5DB';
    
    // إعدادات الخطوط
    $fontSize   = $siteSettings['splash_font_size'] ?? 'text-6xl';
    $fontFamily = $siteSettings['splash_font_family'] ?? "'Montserrat', sans-serif";
@endphp

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
                <h1 class="{{ $fontSize }} font-black leading-none tracking-[0.3em] uppercase mb-5 transition-all duration-300" style="color: {{ $mainColor }};">
                    {{ $mainTitle }}
                    <span class="font-light tracking-tight transition-all duration-300" style="color: {{ $subColor }};">
                        {{ $subTitle }}
                    </span>
                </h1>

                <div class="h-1 w-0 animate-line rounded-full" style="background-color: {{ $mainColor }};"></div>
            </div>

            <div class="mt-16">
                <p class="text-[10px] font-bold text-gray-400 tracking-[0.5em] uppercase animate-pulse-slow">
                    {{ $loadingText }}
                </p>
            </div>
        </div>

        <div class="absolute bottom-10 text-[10px] font-black text-gray-200 tracking-[0.4em] uppercase">
            &copy; {{ date('Y') }} PREMIUM FASHION EXPERIENCE
        </div>
    </div>

</body>
</html>