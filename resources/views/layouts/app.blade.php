<!DOCTYPE html>
@php $locale = app()->getLocale(); @endphp
<html lang="{{ $locale }}" dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        // ── Existing settings ───────────────────────────────────────────────
        $siteSettings = \App\Models\Setting::pluck('value', 'key');

        $primaryColor = $siteSettings['primary_color'] ?? '#364851';
        $bgColor      = $siteSettings['bg_color']      ?? '#f9fafb';
        $navColor     = $siteSettings['nav_bg_color']  ?? '#ffffff';
        $cardColor    = $siteSettings['card_bg_color'] ?? '#ffffff';
        $footerColor  = $siteSettings['footer_bg_color'] ?? '#111827';

        $fontAr = \App\Models\Setting::get('font_ar', 'Tajawal');
        $fontEn = \App\Models\Setting::get('font_en', 'Inter');

        $fontMap = [
            'Tajawal'              => "'Tajawal', sans-serif",
            'Cairo'                => "'Cairo', sans-serif",
            'Almarai'              => "'Almarai', sans-serif",
            'Amiri'                => "'Amiri', serif",
            'Changa'               => "'Changa', sans-serif",
            'El Messiri'           => "'El Messiri', sans-serif",
            'Readex Pro'           => "'Readex Pro', sans-serif",
            'Reem Kufi'            => "'Reem Kufi', sans-serif",
            'Markazi Text'         => "'Markazi Text', serif",
            'Noto Kufi Arabic'     => "'Noto Kufi Arabic', sans-serif",
            'Noto Sans Arabic'     => "'Noto Sans Arabic', sans-serif",
            'IBM Plex Sans Arabic' => "'IBM Plex Sans Arabic', sans-serif",
            'Aref Ruqaa'           => "'Aref Ruqaa', serif",
            'Lateef'               => "'Lateef', serif",
            'Scheherazade New'     => "'Scheherazade New', serif",
            'Harmattan'            => "'Harmattan', sans-serif",
            'Katibeh'              => "'Katibeh', cursive",
            'Lalezar'              => "'Lalezar', cursive",
            'Mada'                 => "'Mada', sans-serif",
            'Mirza'                => "'Mirza', serif",
            'Inter'                => "'Inter', sans-serif",
            'Roboto'               => "'Roboto', sans-serif",
            'DM Sans'              => "'DM Sans', sans-serif",
            'Open Sans'            => "'Open Sans', sans-serif",
            'Poppins'              => "'Poppins', sans-serif",
            'Montserrat'           => "'Montserrat', sans-serif",
            'Lato'                 => "'Lato', sans-serif",
            'Nunito'               => "'Nunito', sans-serif",
            'Raleway'              => "'Raleway', sans-serif",
            'Oswald'               => "'Oswald', sans-serif",
            'Merriweather'         => "'Merriweather', serif",
            'Playfair Display'     => "'Playfair Display', serif",
            'Source Sans Pro'      => "'Source Sans Pro', sans-serif",
            'Work Sans'            => "'Work Sans', sans-serif",
            'Ubuntu'               => "'Ubuntu', sans-serif",
            'Mulish'               => "'Mulish', sans-serif",
            'Fira Sans'            => "'Fira Sans', sans-serif",
            'PT Sans'              => "'PT Sans', sans-serif",
            'Quicksand'            => "'Quicksand', sans-serif",
            'Bebas Neue'           => "'Bebas Neue', sans-serif",
            'Great Vibes'          => "'Great Vibes', cursive",
            'Allura'               => "'Allura', cursive",
            'Alex Brush'           => "'Alex Brush', cursive",
            'Dancing Script'       => "'Dancing Script', cursive",
            'Pacifico'             => "'Pacifico', cursive",
            'Satisfy'              => "'Satisfy', cursive",
            'Cinzel'               => "'Cinzel', serif",
            'Cormorant Garamond'   => "'Cormorant Garamond', serif",
            'Curlz MT'             => "'Curlz MT', cursive",
            'Edwardian Script ITC' => "'Edwardian Script ITC', cursive",
            'Kunstler Script'      => "'Kunstler Script', cursive",
            'Vivaldi'              => "'Vivaldi', cursive",
            'Bickham Script Pro'   => "'Bickham Script Pro', cursive",
            'Mea Culpa'            => "'Mea Culpa', cursive",
        ];

        $fontArCss = $fontMap[$fontAr] ?? "'Tajawal', sans-serif";
        $fontEnCss = $fontMap[$fontEn] ?? "'Inter', sans-serif";

        $logoUrl = \App\Models\Setting::mediaHolder()->getFirstMediaUrl('logo')
            ?: asset('images/default-logo.png');

        $mainSeo = \App\Models\SeoSetting::forType('main');

        // ── NEW: Typography settings ────────────────────────────────────────
        // $typoSettings is shared by ViewServiceProvider.
        // The @php fallback below runs only if the composer isn't registered yet.
        if (!isset($typoSettings)) {
            $typoSettings = \App\Helpers\TypographySettingsHelper::all();
        }
        $ts = $typoSettings;
    @endphp

    <x-seo-head :seo="$mainSeo" />

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50:  '#f0f9ff',
                            100: '#e0f2fe',
                            500: 'var(--brand-color)',
                            600: 'var(--brand-color-dark)',
                            700: 'var(--brand-color-darker)',
                        },
                        accent: '#f59e0b',
                    },
                    fontFamily: {
                        sans: ['var(--app-font)', 'system-ui', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&family=Cairo:wght@400;500;700&family=Almarai:wght@400;700&family=Amiri:wght@400;700&family=Changa:wght@400;600;700&family=El+Messiri:wght@400;500;700&family=Readex+Pro:wght@400;500;700&family=Reem+Kufi:wght@400;500;700&family=Markazi+Text:wght@400;500;700&family=Noto+Kufi+Arabic:wght@400;500;700&family=IBM+Plex+Sans+Arabic:wght@400;500;700&family=Inter:wght@400;500;700&family=Roboto:wght@400;500;700&family=DM+Sans:wght@400;500;700&family=Poppins:wght@400;500;700&family=Montserrat:wght@400;500;700&family=Playfair+Display:wght@400;600;700&family=Great+Vibes&family=Allura&family=Alex+Brush&family=Dancing+Script:wght@400;700&family=Mea+Culpa&display=swap" rel="stylesheet">

    <style>
        /* ═══════════════════════════════════════════════════════════════════
           :root — ALL CSS variables in one place
           ═══════════════════════════════════════════════════════════════════ */
        :root {
            /* ── Brand / layout (existing) ─────────────────────────────── */
            --brand-color:        {{ $primaryColor }};
            --brand-color-dark:   color-mix(in srgb, {{ $primaryColor }} 80%, #000);
            --brand-color-darker: color-mix(in srgb, {{ $primaryColor }} 60%, #000);
            --nav-bg-color:       {{ $navColor }};
            --bg-color:           {{ $bgColor }};
            --card-bg:            {{ $cardColor }};
            --hero-grad-start:    color-mix(in srgb, var(--brand-color) 40%, #000);
            --hero-grad-mid:      color-mix(in srgb, var(--brand-color) 20%, #111);

            /* ── Fonts (existing) ──────────────────────────────────────── */
            --font-ar: {!! $fontArCss !!};
            --font-en: {!! $fontEnCss !!};

            /* ── Font sizes (NEW — from settings table) ────────────────── */
            --base-font-size:          {{ $ts['base_font_size'] }};
            --navbar-font-size:        {{ $ts['navbar_font_size'] }};
            --card-font-size:          {{ $ts['card_font_size'] }};
            --heading-font-size:       {{ $ts['heading_font_size'] }};
            --subheading-font-size:    {{ $ts['subheading_font_size'] }};
            --footer-font-size:        {{ $ts['footer_font_size'] }};
            --button-font-size:        {{ $ts['button_font_size'] }};
            --product-title-font-size: {{ $ts['product_title_font_size'] }};
            --product-price-font-size: {{ $ts['product_price_font_size'] }};

            /* ── Text colors (NEW — from settings table) ───────────────── */
            --text-body:          {{ $ts['body_text_color'] }};
            --text-heading:       {{ $ts['heading_text_color'] }};
            --text-muted:         {{ $ts['muted_text_color'] }};
            --text-navbar:        {{ $ts['navbar_text_color'] }};
            --text-card:          {{ $ts['card_text_color'] }};
            --text-footer:        {{ $ts['footer_text_color'] }};
            --text-button:        {{ $ts['button_text_color'] }};
            --text-badge:         {{ $ts['badge_text_color'] }};
            --text-price:         {{ $ts['price_text_color'] }};
            --text-input:         {{ $ts['input_text_color'] }};
            --text-product-title: {{ $ts['product_title_text_color'] }};
            --text-product-desc:  {{ $ts['product_description_text_color'] }};

            /* ── Legacy aliases (keeps old partials working) ───────────── */
            --text-primary:   {{ $ts['body_text_color'] }};
            --text-secondary: {{ $ts['muted_text_color'] }};
        }

        /* ── Font switching (existing logic preserved) ─────────────────── */
        html[lang="ar"] { --app-font: var(--font-ar); }
        html[lang="en"] { --app-font: var(--font-en); }

        body, button, input, select, textarea {
            font-family: var(--app-font) !important;
        }

        /* ── Base font size drives the rem scale ───────────────────────── */
        html {
            font-size: var(--base-font-size);
        }

        /* ── Body ──────────────────────────────────────────────────────── */
        body {
            color: var(--text-body) !important;
            background-color: var(--bg-color) !important;
        }

        main {
            background-color: var(--bg-color) !important;
            padding-bottom: 0 !important;
            margin-bottom: 0 !important;
        }

        /* ── Navbar ────────────────────────────────────────────────────── */
        nav, .navbar-custom, header {
            background-color: var(--nav-bg-color) !important;
        }
        nav a, nav button, .nav-link {
            color: var(--text-navbar) !important;
            font-size: var(--navbar-font-size);
        }

        /* ── Headings ──────────────────────────────────────────────────── */
        h1, h2 {
            color: var(--text-heading) !important;
            font-size: var(--heading-font-size);
        }
        h3, h4 {
            color: var(--text-heading) !important;
            font-size: var(--subheading-font-size);
        }

        /* ── Cards ─────────────────────────────────────────────────────── */
        .pcard, .featured-card, .product-card {
            background-color: var(--card-bg) !important;
            color: var(--text-card);
            font-size: var(--card-font-size);
        }

        /* ── Utility classes consumed by product views ─────────────────── */
        .product-title {
            color: var(--text-product-title) !important;
            font-size: var(--product-title-font-size) !important;
        }
        .product-description {
            color: var(--text-product-desc) !important;
        }
        .product-price,
        .price-current,
        .price-sale {
            color: var(--text-price) !important;
            font-size: var(--product-price-font-size) !important;
        }

        /* ── Buttons ───────────────────────────────────────────────────── */
        .btn-primary,
        .bg-brand {
            background-color: var(--brand-color) !important;
            color: var(--text-button) !important;
            font-size: var(--button-font-size) !important;
        }

        /* ── Badges ────────────────────────────────────────────────────── */
        .badge {
            color: var(--text-badge) !important;
            font-size: calc(var(--button-font-size) * 0.8) !important;
        }

        /* ── Inputs ────────────────────────────────────────────────────── */
        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="number"],
        input[type="search"],
        select,
        textarea {
            color: var(--text-input) !important;
        }

        /* ── Footer ────────────────────────────────────────────────────── */
        footer, [class*="footer"] {
            background-color: {{ $footerColor }} !important;
            color: var(--text-footer) !important;
            font-size: var(--footer-font-size) !important;
            margin-top: 0 !important;
        }

        /* ── Override Tailwind gray utilities with CSS vars ────────────── */
        .text-gray-900, .text-gray-800,
        .text-slate-900, .text-slate-800,
        .text-black {
            color: var(--text-heading) !important;
        }
        .text-gray-700, .text-gray-600,
        .text-slate-700, .text-slate-600 {
            color: var(--text-body) !important;
        }
        .text-gray-500, .text-gray-400,
        .text-slate-500, .text-slate-400 {
            color: var(--text-muted) !important;
        }
        .text-gray-300, .text-gray-200,
        .text-slate-300, .text-slate-200 {
            color: color-mix(in srgb, var(--text-muted) 60%, transparent) !important;
        }

        /* ── Background overrides (existing) ───────────────────────────── */
        .bg-gray-50,
        main > .bg-white:not(.pcard) {
            background-color: transparent !important;
        }
        .bg-brand-500, .bg-brand-600 {
            background-color: var(--brand-color) !important;
        }
        .text-brand, .text-brand-600 {
            color: var(--brand-color) !important;
        }
        .border-brand {
            border-color: var(--brand-color) !important;
        }

        /* ── Hero banner (existing) ─────────────────────────────────────── */
        .hero-banner {
            background: linear-gradient(135deg,
                var(--hero-grad-start) 0%,
                var(--hero-grad-mid) 55%,
                var(--bg-color) 100%
            ) !important;
        }
        .hero-banner::before { background: transparent !important; }

        /* ── Misc (existing) ───────────────────────────────────────────── */
        .font-display { font-family: 'Playfair Display', serif; }
        .cart-badge   { animation: pop 0.3s cubic-bezier(0.36, 0.07, 0.19, 0.97); }

        @keyframes pop {
            0%, 100% { transform: scale(1); }
            50%       { transform: scale(1.4); }
        }

        .product-card { transition: transform 0.25s ease, box-shadow 0.25s ease; }
        .product-card:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(0,0,0,0.1); }

        #toast { transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1); }
        #toast.hidden { transform: translateY(-80px); opacity: 0; }
        .spinner { animation: spin 0.8s linear infinite; }

        @keyframes spin { to { transform: rotate(360deg); } }
    </style>

    @stack('head')
</head>
<body class="bg-gray-50 antialiased">

    @include('partials.navbar')

    {{-- Toast --}}
    <div id="toast"
         class="hidden fixed top-5 right-5 z-50 flex items-center gap-3 bg-gray-900 text-white
                text-sm font-medium px-5 py-3 rounded-xl shadow-2xl max-w-xs">
        <span id="toast-icon">✓</span>
        <span id="toast-msg">Item added to cart</span>
    </div>

    {{-- Flash messages --}}
    @if(session('success') || session('error'))
    <div class="max-w-7xl mx-auto px-4 pt-4">
        @if(session('success'))
        <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800
                    px-4 py-3 rounded-lg text-sm">
            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800
                    px-4 py-3 rounded-lg text-sm">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            {{ session('error') }}
        </div>
        @endif
    </div>
    @endif

    <main class="min-h-screen">
        @yield('content')
    </main>

    @include('partials.footer')

    <script>
        const Cart = {
            csrfToken: document.querySelector('meta[name="csrf-token"]').content,

            toast(message, type = 'success') {
                const el   = document.getElementById('toast');
                const msg  = document.getElementById('toast-msg');
                const icon = document.getElementById('toast-icon');
                msg.textContent  = message;
                icon.textContent = type === 'success' ? '✓' : '✕';
                el.classList.remove('hidden');
                setTimeout(() => el.classList.add('hidden'), 2500);
            },

            updateBadge(count) {
                document.querySelectorAll('.cart-count').forEach(el => {
                    el.textContent   = count;
                    el.style.display = count > 0 ? 'flex' : 'none';
                    el.classList.remove('cart-badge');
                    void el.offsetWidth;
                    el.classList.add('cart-badge');
                });
            },

            async add(productId, quantity = 1, btn = null) {
                if (btn) {
                    btn.disabled  = true;
                    btn.innerHTML = '<svg class="spinner w-5 h-5 mx-auto" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg>';
                }
                try {
                    const res  = await fetch('/cart/add', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken },
                        body: JSON.stringify({ product_id: productId, quantity }),
                    });
                    const data = await res.json();
                    if (data.success) { this.toast(data.message); this.updateBadge(data.item_count); }
                    else              { this.toast(data.message, 'error'); }
                } catch (e) {
                    this.toast('Something went wrong. Please try again.', 'error');
                } finally {
                    if (btn) { btn.disabled = false; btn.innerHTML = 'Add to Cart'; }
                }
            },
        };

        async function toggleWishlist(btn) {
            if (!btn) return;
            const productId    = btn.dataset.productId;
            const isWishlisted = btn.dataset.wishlisted === 'true';
            const csrfToken    = document.querySelector('meta[name="csrf-token"]')?.content;
            if (!csrfToken) return;
            setHeartState(btn, !isWishlisted);
            try {
                const res = await fetch(`/wishlist/toggle/${productId}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                });
                if (res.status === 401) { window.location.href = '/login'; return; }
                const data = await res.json();
                setHeartState(btn, data.wishlisted);
                updateWishlistBadge(data.count);
                if (typeof Cart !== 'undefined' && Cart.toast) Cart.toast(data.message, 'success');
            } catch (err) {
                setHeartState(btn, isWishlisted);
            }
        }

        function setHeartState(btn, wishlisted) {
            const outline = btn.querySelector('[data-heart="outline"]');
            const filled  = btn.querySelector('[data-heart="filled"]');
            if (outline) outline.classList.toggle('hidden', wishlisted);
            if (filled)  filled.classList.toggle('hidden', !wishlisted);
            btn.dataset.wishlisted = wishlisted ? 'true' : 'false';
            btn.classList.add('scale-125');
            setTimeout(() => btn.classList.remove('scale-125'), 150);
        }

        function updateWishlistBadge(count) {
            document.querySelectorAll('.wishlist-count').forEach(el => {
                el.textContent   = count;
                el.style.display = count > 0 ? 'flex' : 'none';
            });
        }
    </script>

    @stack('scripts')
</body>
</html>