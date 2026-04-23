<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
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
        .bg-brand { background-color: var(--brand-color); }
        .text-brand { color: var(--brand-color); }
        .border-brand { border-color: var(--brand-color); }
        .focus-ring-brand:focus { --tw-ring-color: var(--brand-color); }
        .custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #374151; /* رمادي غامق يناسب الثيم */
    border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #4b5563;
}
    </style>
</head>
<body class="bg-gray-50 text-gray-800">

    <div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: true }">
        
        {{-- Sidebar --}}
    <aside class="bg-gray-900 text-white transition-all duration-300 flex-shrink-0 flex flex-col h-screen" 
       :class="sidebarOpen ? 'w-64' : 'w-20'">
    
    {{-- 1. الجزء الثابت: الهيدر --}}
    <div class="p-4 flex items-center justify-between border-b border-gray-800 flex-shrink-0">
        <span x-show="sidebarOpen" class="font-bold text-lg tracking-wider truncate">لوحة التحكم</span>
        <button @click="sidebarOpen = !sidebarOpen" class="p-1 hover:bg-gray-800 rounded transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
    </div>
    
    {{-- 2. الجزء القابل للتمرير: الروابط --}}
    <div class="flex-1 overflow-y-auto custom-scrollbar px-2 py-4">
     
           <nav class="mt-4 px-2 space-y-1">
    
    @can('manage-catalog')
        {{-- الرئيسية --}}
        <x-admin-nav-link href="{{ route('admin.dashboard') }}" icon="home" :active="request()->routeIs('admin.dashboard')">
            الرئيسية
        </x-admin-nav-link>

        {{-- إدارة محتوى الصفحة الرئيسية --}}
        <div x-show="sidebarOpen" class="px-3 mt-4 mb-2 text-[10px] font-bold text-gray-500 uppercase tracking-wider">
            محتوى المتجر
        </div>

        {{-- شريط الإعلانات (Announcements) - جديد --}}
        <x-admin-nav-link href="{{ route('admin.announcements.index') }}" icon="speakerphone" :active="request()->routeIs('admin.announcements.*')">
            شريط الإعلانات
        </x-admin-nav-link>

        {{-- البانر الرئيسي (Hero Banners) - جديد --}}
        <x-admin-nav-link href="{{ route('admin.hero-banners.index') }}" icon="photograph" :active="request()->routeIs('admin.hero-banners.*')">
            البانر الإعلاني (Hero)
        </x-admin-nav-link>

        {{-- أقسام الصفحة الرئيسية (Home Sections) - تم تعديل الرابط --}}
        <x-admin-nav-link href="{{ route('admin.home-sections.index') }}" icon="template" :active="request()->routeIs('admin.home-sections.*')">
            أقسام الصفحة الرئيسية
        </x-admin-nav-link>

        <hr class="border-gray-800 my-2 mx-2">

        {{-- المنتجات --}}
        <x-admin-nav-link href="{{ route('admin.products.index') }}" icon="shopping-bag" :active="request()->routeIs('admin.products.*')">
            المنتجات
        </x-admin-nav-link>

        {{-- التصنيفات --}}
        <x-admin-nav-link href="{{ route('admin.categories.index') }}" icon="folder" :active="request()->routeIs('admin.categories.*')">
            التصنيفات
        </x-admin-nav-link>

        <x-admin-nav-link href="{{ route('admin.attributes.index') }}" icon="tag" :active="request()->routeIs('admin.attributes.*')">
            السمات والخصائص
        </x-admin-nav-link>

        {{-- قيم السمات (جديد) --}}
        <x-admin-nav-link href="{{ route('admin.attribute-values.index') }}" icon="view-list" :active="request()->routeIs('admin.attribute-values.*')">
            قيم السمات
        </x-admin-nav-link>
    @endcan

    {{-- القسم المخصص للسوبر أدمن --}}
    @can('manage-all')
        <div x-show="sidebarOpen" class="px-3 mt-6 mb-2 text-[10px] font-bold text-gray-500 uppercase tracking-wider">
            المبيعات والعملاء
        </div>

        {{-- الطلبات --}}
        <x-admin-nav-link href="{{ route('admin.orders.index') }}" icon="shopping-cart" :active="request()->routeIs('admin.orders.*')">
            الطلبات
            @php $pendingCount = \App\Models\Order::where('status', 'pending')->count(); @endphp
            @if($pendingCount > 0)
                <span x-show="sidebarOpen" class="mr-auto bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full animate-pulse">
                    {{ $pendingCount }}
                </span>
            @endif
        </x-admin-nav-link>

        <div x-show="sidebarOpen" class="px-3 mt-6 mb-2 text-[10px] font-bold text-gray-500 uppercase tracking-wider">
            إعدادات الموقع
        </div>

        {{-- ميزات الموقع --}}
        <x-admin-nav-link href="{{ route('admin.site-features.index') }}" icon="star" :active="request()->routeIs('admin.site-features.*')">
            ميزات الموقع
        </x-admin-nav-link>

        {{-- روابط التواصل --}}
        <x-admin-nav-link href="{{ route('admin.social-links.index') }}" icon="share" :active="request()->routeIs('admin.social-links.*')">
            روابط التواصل
        </x-admin-nav-link>

        {{-- شاشة الترحيب (Splash Screen) - جديد --}}
        <x-admin-nav-link href="{{ route('admin.splash.edit') }}" icon="lightning-bolt" :active="request()->routeIs('admin.splash.*')">
            شاشة الترحيب (Splash)
        </x-admin-nav-link>

        {{-- الصفحات --}}
        <x-admin-nav-link href="{{ route('admin.pages.index') }}" icon="document-text" :active="request()->routeIs('admin.pages.*')">
            الصفحات
        </x-admin-nav-link>

        <div x-show="sidebarOpen" class="px-3 mt-6 mb-2 text-[10px] font-bold text-gray-500 uppercase tracking-wider">
            النظام والمالية
        </div>

        {{-- الدول --}}
        <x-admin-nav-link href="{{ route('admin.countries.index') }}" icon="globe" :active="request()->routeIs('admin.countries.*')">
            الدول والمناطق
        </x-admin-nav-link>

        {{-- العملات --}}
        <x-admin-nav-link href="{{ route('admin.currencies.index') }}" icon="currency-dollar" :active="request()->routeIs('admin.currencies.*')">
            العملات
        </x-admin-nav-link>
        <x-admin-nav-link
    href="{{ route('admin.settings.sms') }}"
    icon="phone"
    :active="request()->routeIs('admin.settings.sms*')">
    إعدادات SMS API
</x-admin-nav-link>
<x-admin-nav-link href="{{ route('admin.settings.checkout') }}" icon="credit-card" :active="request()->is('admin/settings/checkout')">
    إعدادات الدفع
</x-admin-nav-link>
        {{-- الإعدادات العامة --}}
        <x-admin-nav-link href="{{ route('admin.settings') }}" icon="cog" :active="request()->routeIs('admin.settings')">
            الإعدادات العامة
        </x-admin-nav-link>
    @endcan

    {{-- تسجيل الخروج --}}
 </div>

    {{-- 3. الجزء الثابت: الأسفل (تسجيل الخروج) --}}
    <div class="p-2 border-t border-gray-800 flex-shrink-0 bg-gray-900">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" 
                    class="flex items-center gap-3 px-3 py-2.5 w-full rounded-xl transition-all text-sm font-semibold text-red-400 hover:bg-red-500/10 hover:text-red-300"
                    :class="sidebarOpen ? '' : 'justify-center'">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span x-show="sidebarOpen" class="truncate">تسجيل الخروج</span>
            </button>
        </form>
    </div>
</aside>

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col overflow-hidden">
            {{-- Header --}}
            <header class="h-16 bg-white border-b flex items-center justify-between px-8 shadow-sm">
                <div class="text-sm font-bold text-gray-500 uppercase tracking-wide">
                    @yield('title')
                </div>
                <div class="flex items-center gap-4">
                    <a href="/" target="_blank" class="text-xs font-bold text-gray-400 hover:text-brand transition-colors flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        عرض المتجر
                    </a>
                    <div class="h-8 w-8 rounded-full bg-brand flex items-center justify-center text-white text-xs font-bold shadow-sm shadow-brand/30">
                        {{ substr(auth()->user()->name ?? 'AD', 0, 2) }}
                    </div>
                </div>
            </header>

            {{-- Main Page Content --}}
            <main class="flex-1 overflow-y-auto p-8 bg-gray-50/50">
                {{-- Alerts --}}
                @if(session('success'))
                    <div x-data="{ show: true }" x-show="show" x-transition 
                         class="mb-6 p-4 bg-green-50 text-green-700 rounded-xl border border-green-100 flex justify-between items-center shadow-sm">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path></svg>
                            <span class="font-medium text-sm">{{ session('success') }}</span>
                        </div>
                        <button @click="show = false" class="text-green-400 hover:text-green-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                @endif

                @yield('admin-content')
            </main>
        </div>
    </div>
</body>
</html>