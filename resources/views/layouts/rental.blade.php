<!DOCTYPE html>
@php
    $locale = app()->getLocale();
    $rtl    = $locale === 'ar';
@endphp
<html lang="{{ $locale }}" dir="{{ $rtl ? 'rtl' : 'ltr' }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('rental.brand'))</title>

    @php
        $accent    = \App\Models\Setting::get('rental_accent_color', '#F47B20');
        $ink       = \App\Models\Setting::get('rental_ink_color', '#2B2B2B');
        $logoUrl   = \App\Models\Setting::mediaHolder()->getFirstMediaUrl('logo');
        $supportNo = \App\Models\Setting::get('rental_support_phone', '+962 6 500 0000');

        /*
         * Derive the accent tint/shade ramp from the single admin-chosen colour
         * so hover states and soft backgrounds re-theme with it. Mixing towards
         * white gives the 50–200 tints, towards black the 600–700 shades.
         */
        $mix = function (string $hex, string $towards, float $amount): string {
            $hex = ltrim($hex, '#');
            if (strlen($hex) === 3) {
                $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
            }
            if (! preg_match('/^[0-9a-fA-F]{6}$/', $hex)) {
                $hex = 'F47B20';
            }
            $target = $towards === 'white' ? 255 : 0;

            $out = '#';
            foreach ([0, 2, 4] as $i) {
                $c = hexdec(substr($hex, $i, 2));
                $out .= str_pad(dechex((int) round($c + ($target - $c) * $amount)), 2, '0', STR_PAD_LEFT);
            }

            return $out;
        };

        $accent50  = $mix($accent, 'white', 0.92);
        $accent100 = $mix($accent, 'white', 0.84);
        $accent200 = $mix($accent, 'white', 0.68);
        $accent600 = $mix($accent, 'black', 0.14);
        $accent700 = $mix($accent, 'black', 0.30);
    @endphp

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800;900&family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        accent: {
                            DEFAULT: '{{ $accent }}',
                            50:  '{{ $accent50 }}', 100: '{{ $accent100 }}', 200: '{{ $accent200 }}',
                            500: '{{ $accent }}', 600: '{{ $accent600 }}', 700: '{{ $accent700 }}',
                        },
                        ink: '{{ $ink }}',
                    },
                    fontFamily: { sans: ['var(--app-font)', 'system-ui', 'sans-serif'] },
                }
            }
        }
    </script>

    <style>
        :root {
            --accent: {{ $accent }};
            --accent-50: {{ $accent50 }};
            --accent-100: {{ $accent100 }};
            --accent-200: {{ $accent200 }};
            --accent-600: {{ $accent600 }};
            --accent-700: {{ $accent700 }};
            --ink: {{ $ink }};
            --font-ar: 'Tajawal', sans-serif;
            --font-en: 'Inter', sans-serif;
        }
        html[lang="ar"] { --app-font: var(--font-ar); }
        html[lang="en"] { --app-font: var(--font-en); }
        body { font-family: var(--app-font); color: var(--ink); background: #fff; }
        [x-cloak] { display: none !important; }

        /* Hide the native calendar/clock button so our own icon stays aligned
           in both LTR and RTL — the native one always renders on the right. */
        input[type="date"]::-webkit-calendar-picker-indicator,
        input[type="time"]::-webkit-calendar-picker-indicator { opacity: 0; cursor: pointer; }

        .field-underline {
            border: 0; border-bottom: 1px solid #d4d4d4; border-radius: 0;
            padding-inline-start: 0; background: transparent;
        }
        .field-underline:focus {
            outline: none; box-shadow: none; border-bottom-color: var(--accent);
        }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>

    @stack('head')
</head>
<body class="antialiased">

@include('rental.partials.header')

{{-- Flash messages --}}
@if(session('success') || session('error'))
    <div class="max-w-7xl mx-auto px-4 pt-4">
        @if(session('success'))
            <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">
                <i class="fa-solid fa-circle-check text-green-500"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
                <i class="fa-solid fa-circle-exclamation text-red-500"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif
    </div>
@endif

<main class="min-h-screen">
    @yield('content')
</main>

@include('rental.partials.footer')

@stack('scripts')
</body>
</html>
