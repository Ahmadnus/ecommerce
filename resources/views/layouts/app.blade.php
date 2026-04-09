<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ShopCraft') — Modern E-Commerce</title>

    {{-- 1. جلب الإعدادات من قاعدة البيانات --}}
@php
    // 1. جلب كل الإعدادات مرة واحدة وتخزينها في مصفوفة (أفضل للأداء)
    $siteSettings = \App\Models\Setting::pluck('value', 'key');

    // 2. استخراج القيم من المصفوفة مع وضع قيم افتراضية في حال كانت القاعدة فارغة
    $primaryColor = $siteSettings['primary_color'] ?? '#0ea5e9';
    
    // 3. جلب الشعار
    $logoPath = $siteSettings['site_logo'] ?? null;
    $logoUrl = $logoPath ? asset('storage/' . $logoPath) : asset('images/default-logo.png');
@endphp

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
                            900: '#0c4a6e',
                        },
                        accent: '#f59e0b',
                    },
                    // بقية الإعدادات كما هي...
                    fontFamily: {
                        sans: ['"DM Sans"', 'system-ui', 'sans-serif'],
                        display: ['"Playfair Display"', 'serif'],
                    },
                }
            }
        }
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

    <style>
    
      :root {
            --brand-color: {{ $primaryColor }};
        }
        
        {{-- هنا نربط كلاسات Tailwind باللون الديناميكي --}}
        .bg-brand-600 { background-color: var(--brand-color) !important; }
        .text-brand-600 { color: var(--brand-color) !important; }
        .border-brand-600 { border-color: var(--brand-color) !important; }
        .bg-brand-50 { background-color: {{ $primaryColor }}15 !important; }
        
        /* باقي الستايلات التي كانت عندك... */

        body { font-family: 'DM Sans', sans-serif; }
        .font-display { font-family: 'Playfair Display', serif; }

        /* الأنميشن والستايلات الأخرى التي كانت لديك تبقى كما هي */
        .cart-badge { animation: pop 0.3s cubic-bezier(0.36, 0.07, 0.19, 0.97); }
        @keyframes pop { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.4); } }
        .product-card { transition: transform 0.25s ease, box-shadow 0.25s ease; }
        .product-card:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
        #toast { transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1); }
        #toast.hidden { transform: translateY(-80px); opacity: 0; }
        .spinner { animation: spin 0.8s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>

    @stack('head')
</head>
<body class="bg-gray-50 text-gray-800 antialiased">

    {{-- ═══ NAVBAR ═══════════════════════════════════════════════════════════ --}}
    @include('partials.navbar')

    {{-- ═══ TOAST NOTIFICATION ════════════════════════════════════════════════ --}}
    <div id="toast"
         class="hidden fixed top-5 right-5 z-50 flex items-center gap-3 bg-gray-900 text-white text-sm font-medium px-5 py-3 rounded-xl shadow-2xl max-w-xs">
        <span id="toast-icon">✓</span>
        <span id="toast-msg">Item added to cart</span>
    </div>

    {{-- ═══ FLASH MESSAGES ══════════════════════════════════════════════════════ --}}
    @if(session('success') || session('error'))
    <div class="max-w-7xl mx-auto px-4 pt-4">
        @if(session('success'))
            <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm animate-fade-in">
                <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm animate-fade-in">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                {{ session('error') }}
            </div>
        @endif
    </div>
    @endif

    {{-- ═══ MAIN CONTENT ════════════════════════════════════════════════════════ --}}
    <main class="min-h-screen">
        @yield('content')
    </main>

    {{-- ═══ FOOTER ══════════════════════════════════════════════════════════════ --}}
    @include('partials.footer')

    {{-- ═══ GLOBAL JAVASCRIPT ════════════════════════════════════════════════════ --}}
    <script>
    /**
     * Global Cart Utilities
     * Available on every page — handles toast notifications and cart badge updates.
     */
    const Cart = {
        csrfToken: document.querySelector('meta[name="csrf-token"]').content,

        /** Show a temporary toast message */
        toast(message, type = 'success') {
            const el   = document.getElementById('toast');
            const msg  = document.getElementById('toast-msg');
            const icon = document.getElementById('toast-icon');

            msg.textContent  = message;
            icon.textContent = type === 'success' ? '✓' : '✕';
            el.classList.remove('hidden');

            // Auto-hide after 2.5s
            setTimeout(() => el.classList.add('hidden'), 2500);
        },

        /** Update all cart count badges in the navbar */
        updateBadge(count) {
            document.querySelectorAll('.cart-count').forEach(el => {
                el.textContent  = count;
                el.style.display = count > 0 ? 'flex' : 'none';
                // Trigger pop animation
                el.classList.remove('cart-badge');
                void el.offsetWidth; // reflow
                el.classList.add('cart-badge');
            });
        },

        /** Add a product to cart via AJAX */
        async add(productId, quantity = 1, btn = null) {
            if (btn) {
                btn.disabled    = true;
                btn.innerHTML   = '<svg class="spinner w-5 h-5 mx-auto" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg>';
            }

            try {
                const res  = await fetch('/cart/add', {
                    method:  'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken },
                    body:    JSON.stringify({ product_id: productId, quantity }),
                });
                const data = await res.json();

                if (data.success) {
                    this.toast(data.message);
                    this.updateBadge(data.item_count);
                } else {
                    this.toast(data.message, 'error');
                }
            } catch (e) {
                this.toast('Something went wrong. Please try again.', 'error');
            } finally {
                if (btn) {
                    btn.disabled  = false;
                    btn.innerHTML = 'Add to Cart';
                }
            }
        },
    };
    </script>

    @stack('scripts')
</body>
</html>
