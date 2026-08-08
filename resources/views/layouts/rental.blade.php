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
        /*
         * The whole palette — tints, shades, and the on-accent text colour — is
         * derived from the two admin-chosen colours by App\Support\Brand, so a
         * new logo colour cascades everywhere from one setting.
         */
        $palette = \App\Support\Brand::palette();
        $accent  = $palette['accent'];
        $ink     = $palette['ink'];

        $logoUrl   = \App\Support\Brand::logoUrl();
        $supportNo = \App\Models\Setting::get('rental_support_phone', '+962 6 500 0000');

        /*
         * Floating contact button. The admin enables it by ticking "floating"
         * on a social link that carries a WhatsApp number; nothing is emitted
         * unless such an active row exists, so switching it off removes the
         * markup rather than just hiding it.
         */
        $floatingLink = \App\Models\SocialLink::query()
            ->where('is_floating', true)
            ->when(\Illuminate\Support\Facades\Schema::hasColumn('social_links', 'is_active'),
                fn($q) => $q->where('is_active', true))
            ->whereNotNull('whatsapp_number')
            ->where('whatsapp_number', '!=', '')
            ->orderBy('sort_order')
            ->first();
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
                            50:  '{{ $palette['accent-50'] }}',  100: '{{ $palette['accent-100'] }}',
                            200: '{{ $palette['accent-200'] }}', 300: '{{ $palette['accent-300'] }}',
                            400: '{{ $palette['accent-400'] }}', 500: '{{ $accent }}',
                            600: '{{ $palette['accent-600'] }}', 700: '{{ $palette['accent-700'] }}',
                            800: '{{ $palette['accent-800'] }}',
                            // Text/icons on a filled accent surface — flips to
                            // ink when the accent is too light to carry white.
                            fg:  '{{ $palette['accent-fg'] }}',
                        },
                        ink: {
                            DEFAULT: '{{ $ink }}',
                            500: '{{ $palette['ink-500'] }}',
                            700: '{{ $palette['ink-700'] }}',
                        },
                    },
                    fontFamily: { sans: ['var(--app-font)', 'system-ui', 'sans-serif'] },
                }
            }
        }
    </script>

    <style>
        :root {
            @foreach($palette as $token => $value)
            --{{ $token }}: {{ $value }};
            @endforeach
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

@if($floatingLink)
    <x-floating-button :number="$floatingLink->whatsapp_number" />
@endif

@stack('scripts')
</body>
</html>
