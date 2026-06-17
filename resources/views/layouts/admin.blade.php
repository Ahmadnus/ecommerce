<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - @yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Tajawal', sans-serif; }
        [x-cloak] { display: none !important; }
        :root {
            --brand-color: {{ \App\Models\Setting::get('primary_color', '#0ea5e9') }};
        }
        .bg-brand         { background-color: var(--brand-color); }
        .text-brand       { color: var(--brand-color); }
        .border-brand     { border-color: var(--brand-color); }
        .focus-ring-brand:focus { --tw-ring-color: var(--brand-color); }
        .custom-scrollbar::-webkit-scrollbar       { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
    </style>
    @stack('head')
</head>
<body class="text-black" style="background:#ffffff;">

<div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: true }">

    {{-- ═══ SIDEBAR ═══════════════════════════════════════════════════════════ --}}
    <aside style="background:#000000; color:#ffffff;"
           class="transition-all duration-300 flex-shrink-0 flex flex-col h-screen"
           :class="sidebarOpen ? 'w-64' : 'w-20'">

        {{-- Header --}}
        <div class="p-4 flex items-center justify-between flex-shrink-0"
             style="border-bottom:1px solid rgba(255,255,255,0.12);">
            <span x-show="sidebarOpen" class="font-bold text-lg tracking-wider truncate"
                  style="color:#ffffff;">لوحة التحكم</span>
            <button @click="sidebarOpen = !sidebarOpen"
                    class="p-1 rounded transition-colors" style="color:#ffffff;"
                    onmouseover="this.style.background='rgba(255,255,255,0.1)'"
                    onmouseout="this.style.background='transparent'">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>

        {{-- Nav links --}}
        <div class="flex-1 overflow-y-auto custom-scrollbar px-2 py-4">
            <nav class="mt-4 px-2 space-y-1">

                @can('manage-catalog')

                    <x-admin-nav-link href="{{ route('admin.dashboard') }}" icon="home"
                        :active="request()->routeIs('admin.dashboard')">
                        الرئيسية
                    </x-admin-nav-link>

                    {{-- ── محتوى المتجر ──────────────────────────────────────── --}}
                    <div x-show="sidebarOpen"
                         class="px-3 mt-4 mb-2 text-[10px] font-bold uppercase tracking-wider"
                         style="color:rgba(255,255,255,0.4);">
                        محتوى المتجر
                    </div>

                    <x-admin-nav-link href="{{ route('admin.announcements.index') }}" icon="speakerphone"
                        :active="request()->routeIs('admin.announcements.*')">
                        شريط الإعلانات
                    </x-admin-nav-link>

                    <x-admin-nav-link href="{{ route('admin.hero-banners.index') }}" icon="photograph"
                        :active="request()->routeIs('admin.hero-banners.*')">
                        البانر الإعلاني (Hero)
                    </x-admin-nav-link>

                    <x-admin-nav-link href="{{ route('admin.home-sections.index') }}" icon="template"
                        :active="request()->routeIs('admin.home-sections.*')">
                        أقسام الصفحة الرئيسية
                    </x-admin-nav-link>

                    <hr class="my-2 mx-2" style="border-color:rgba(255,255,255,0.1);">

                    <x-admin-nav-link href="{{ route('admin.products.index') }}" icon="shopping-bag"
                        :active="request()->routeIs('admin.products.*')">
                        المنتجات
                    </x-admin-nav-link>

                    <x-admin-nav-link href="{{ route('admin.categories.index') }}" icon="folder"
                        :active="request()->routeIs('admin.categories.*')">
                        التصنيفات
                    </x-admin-nav-link>

                    <x-admin-nav-link href="{{ route('admin.attributes.index') }}" icon="tag"
                        :active="request()->routeIs('admin.attributes.*')">
                        السمات والخصائص
                    </x-admin-nav-link>

                    <x-admin-nav-link href="{{ route('admin.attribute-values.index') }}" icon="view-list"
                        :active="request()->routeIs('admin.attribute-values.*')">
                        قيم السمات
                    </x-admin-nav-link>

                @endcan

                @can('manage-all')

                    {{-- ── المبيعات والعملاء ─────────────────────────────────── --}}
                    <div x-show="sidebarOpen"
                         class="px-3 mt-6 mb-2 text-[10px] font-bold uppercase tracking-wider"
                         style="color:rgba(255,255,255,0.4);">
                        المبيعات والعملاء
                    </div>

                    <x-admin-nav-link href="{{ route('admin.orders.index') }}" icon="shopping-cart"
                        :active="request()->routeIs('admin.orders.*')">
                        الطلبات
                        @php $pendingCount = \App\Models\Order::where('status','pending')->count(); @endphp
                        @if($pendingCount > 0)
                            <span x-show="sidebarOpen"
                                  class="mr-auto text-[10px] px-1.5 py-0.5 rounded-full animate-pulse"
                                  style="background:#ef4444; color:#ffffff;">
                                {{ $pendingCount }}
                            </span>
                        @endif
                    </x-admin-nav-link>
<x-admin-nav-link href="{{ route('admin.customizations.index') }}"
    icon="color-swatch"
    :active="request()->routeIs('admin.order-customizations.*')">
    طلبات التخصيص
</x-admin-nav-link>
                    <x-admin-nav-link href="{{ route('admin.contact-messages.index') }}" icon="mail"
                        :active="request()->routeIs('admin.contact-messages.*')">
                        رسائل العملاء
                    </x-admin-nav-link>

                    {{-- ── إعدادات الموقع ────────────────────────────────────── --}}
                    <div x-show="sidebarOpen"
                         class="px-3 mt-6 mb-2 text-[10px] font-bold uppercase tracking-wider"
                         style="color:rgba(255,255,255,0.4);">
                        إعدادات الموقع
                    </div>

                    <x-admin-nav-link href="{{ route('admin.site-features.index') }}" icon="star"
                        :active="request()->routeIs('admin.site-features.*')">
                        ميزات الموقع
                    </x-admin-nav-link>

                    <x-admin-nav-link href="{{ route('admin.social-links.index') }}" icon="share"
                        :active="request()->routeIs('admin.social-links.*')">
                        روابط التواصل
                    </x-admin-nav-link>

                    <x-admin-nav-link href="{{ route('admin.splash.edit') }}" icon="lightning-bolt"
                        :active="request()->routeIs('admin.splash.*')">
                        شاشة الترحيب (Splash)
                    </x-admin-nav-link>

                    <x-admin-nav-link href="{{ route('admin.pages.index') }}" icon="document-text"
                        :active="request()->routeIs('admin.pages.*')">
                        الصفحات
                    </x-admin-nav-link>

                    <x-admin-nav-link href="{{ route('admin.seo.index') }}" icon="search"
                        :active="request()->routeIs('admin.seo.*')">
                        SEO Settings
                    </x-admin-nav-link>

                    <x-admin-nav-link href="{{ route('admin.footer-company.index') }}" icon="collection"
                        :active="request()->routeIs('admin.footer-company.*')">
                        معلومات الفوتر
                    </x-admin-nav-link>

                    {{-- ── النظام والمالية ───────────────────────────────────── --}}
                    <div x-show="sidebarOpen"
                         class="px-3 mt-6 mb-2 text-[10px] font-bold uppercase tracking-wider"
                         style="color:rgba(255,255,255,0.4);">
                        النظام والمالية
                    </div>

                    <x-admin-nav-link href="{{ route('admin.countries.index') }}" icon="globe"
                        :active="request()->routeIs('admin.countries.*') || request()->routeIs('admin.zones.*')">
                        الدول والمناطق
                    </x-admin-nav-link>



                    
                    <x-admin-nav-link href="{{ route('admin.currencies.index') }}" icon="currency-dollar"
                        :active="request()->routeIs('admin.currencies.*')">
                        العملات
                    </x-admin-nav-link>

                    {{-- ── الإعدادات ─────────────────────────────────────────── --}}
                    <div x-show="sidebarOpen"
                         class="px-3 mt-6 mb-2 text-[10px] font-bold uppercase tracking-wider"
                         style="color:rgba(255,255,255,0.4);">
                        الإعدادات
                    </div>

                    <x-admin-nav-link href="{{ route('admin.settings') }}" icon="cog"
                        :active="request()->routeIs('admin.settings') && !request()->routeIs('admin.settings.*')">
                        الإعدادات العامة
                    </x-admin-nav-link>

                    <x-admin-nav-link href="{{ route('admin.settings.typography') }}" icon="text"
                        :active="request()->routeIs('admin.settings.typography*')">
                        الخطوط والألوان
                    </x-admin-nav-link>

                    <x-admin-nav-link href="{{ route('admin.settings.checkout') }}" icon="credit-card"
                        :active="request()->is('admin/settings/checkout')">
                        إعدادات الدفع
                    </x-admin-nav-link>
                    

                    <x-admin-nav-link href="{{ route('admin.reviews.index') }}" icon="star"
    :active="request()->routeIs('admin.reviews.*')">
    تقييمات المنتجات
    @php $pendingReviews = \App\Models\ProductReview::where('status','pending')->count(); @endphp
    @if($pendingReviews > 0)
        <span x-show="sidebarOpen"
              class="mr-auto text-[10px] px-1.5 py-0.5 rounded-full animate-pulse"
              style="background:#f59e0b; color:#ffffff;">
            {{ $pendingReviews }}
        </span>
    @endif
</x-admin-nav-link>
                    <x-admin-nav-link href="{{ route('admin.locale-mode') }}" icon="translate"
                        :active="request()->is('admin/locale-mode')">
                        اللغة
                    </x-admin-nav-link>

                    <x-admin-nav-link href="{{ route('admin.password.edit') }}" icon="key"
                        :active="request()->routeIs('admin.password.*')">
                        تغيير كلمة السر
                    </x-admin-nav-link>

                @endcan

            </nav>
        </div>

        {{-- Logout --}}
        <div class="p-2 flex-shrink-0"
             style="border-top:1px solid rgba(255,255,255,0.12); background:#000000;">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="flex items-center gap-3 px-3 py-2.5 w-full rounded-xl transition-all text-sm font-semibold"
                        style="color:#fca5a5;"
                        onmouseover="this.style.background='rgba(239,68,68,0.1)'; this.style.color='#f87171';"
                        onmouseout="this.style.background='transparent'; this.style.color='#fca5a5';"
                        :class="sidebarOpen ? '' : 'justify-center'">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span x-show="sidebarOpen" class="truncate">تسجيل الخروج</span>
                </button>
            </form>
        </div>

    </aside>

    {{-- ═══ MAIN AREA ══════════════════════════════════════════════════════════ --}}
    <div class="flex-1 flex flex-col overflow-hidden" style="background:#ffffff;">

        {{-- Top header --}}
        <header class="h-16 flex items-center justify-between px-8"
                style="background:#ffffff; border-bottom:1px solid #e5e7eb;">
            <div class="text-sm font-bold uppercase tracking-wide" style="color:#000000;">
                @yield('title')
            </div>
            <div class="flex items-center gap-4">
                <a href="/" target="_blank"
                   class="text-xs font-bold transition-colors flex items-center gap-1"
                   style="color:#6b7280;"
                   onmouseover="this.style.color='var(--brand-color)'"
                   onmouseout="this.style.color='#6b7280'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    عرض المتجر
                </a>
                <div class="h-8 w-8 rounded-full flex items-center justify-center text-xs font-bold"
                     style="background:#000000; color:#ffffff;">
                    {{ substr(auth()->user()->name ?? 'AD', 0, 2) }}
                </div>
            </div>
        </header>

        {{-- Page content --}}
        <main class="flex-1 overflow-y-auto p-8" style="background:#f9fafb;">

            @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition
                 class="mb-6 p-4 rounded-xl flex justify-between items-center"
                 style="background:#f0fdf4; color:#166534; border:1px solid #bbf7d0;">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" style="color:#16a34a;">
                        <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                    </svg>
                    <span class="font-medium text-sm">{{ session('success') }}</span>
                </div>
                <button @click="show = false" style="color:#16a34a;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            @endif

            @if(session('error'))
            <div x-data="{ show: true }" x-show="show" x-transition
                 class="mb-6 p-4 rounded-xl flex justify-between items-center"
                 style="background:#fef2f2; color:#991b1b; border:1px solid #fecaca;">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" style="color:#dc2626;">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <span class="font-medium text-sm">{{ session('error') }}</span>
                </div>
                <button @click="show = false" style="color:#dc2626;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            @endif

            @yield('admin-content')

        </main>
    </div>

</div>

@stack('scripts')

</body>
</html>