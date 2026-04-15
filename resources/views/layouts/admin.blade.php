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
    </style>
</head>
<body class="bg-gray-50 text-gray-800">

    <div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: true }">
        
        {{-- Sidebar --}}
        <aside class="bg-gray-900 text-white transition-all duration-300 flex-shrink-0" 
               :class="sidebarOpen ? 'w-64' : 'w-20'">
            <div class="p-4 flex items-center justify-between border-b border-gray-800">
                <span x-show="sidebarOpen" class="font-bold text-lg tracking-wider truncate">لوحة التحكم</span>
                <button @click="sidebarOpen = !sidebarOpen" class="p-1 hover:bg-gray-800 rounded transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
            
            <nav class="mt-4 px-2 space-y-1">
                {{-- الرئيسية (متاحة للكل لأن لديهم manage-catalog) --}}
                @can('manage-catalog')
                    <x-admin-nav-link href="{{ route('admin.dashboard') }}" icon="home" :active="request()->routeIs('admin.dashboard')">
                        الرئيسية
                    </x-admin-nav-link>

                    {{-- المنتجات --}}
                    <x-admin-nav-link href="{{ route('admin.products.index') }}" icon="shopping-bag" :active="request()->routeIs('admin.products.*')">
                        المنتجات
                    </x-admin-nav-link>

                    {{-- التصنيفات --}}
                    <x-admin-nav-link href="{{ route('admin.categories.index') }}" icon="folder" :active="request()->routeIs('admin.categories.*')">
                        التصنيفات
                    </x-admin-nav-link>
                    <x-admin-nav-link href="{{ route('admin.social-links.index') }}" icon="share" :active="request()->routeIs('admin.social-links.*')">
    روابط التواصل
</x-admin-nav-link>
                @endcan

                {{-- القسم المخصص للسوبر أدمن فقط (الطلبات، الإعدادات، الصفحات) --}}
                @can('manage-all')
                    {{-- الطلبات --}}
                    <x-admin-nav-link href="{{ route('admin.orders.index') }}" icon="shopping-cart" :active="request()->routeIs('admin.orders.*')">
                        الطلبات
                        @php
                            $pendingCount = \App\Models\Order::where('status', 'pending')->count();
                        @endphp
                        @if($pendingCount > 0)
                            <span x-show="sidebarOpen" class="mr-auto bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full animate-pulse">
                                {{ $pendingCount }}
                            </span>
                        @endif
                    </x-admin-nav-link>

                    {{-- قسم الإعدادات الجغرافية والمالية --}}
                    <div x-show="sidebarOpen" class="px-3 mt-6 mb-2 text-[10px] font-bold text-gray-500 uppercase tracking-wider">
                        الإعدادات المتقدمة
                    </div>

                    {{-- الدول --}}
                    <x-admin-nav-link href="{{ route('admin.countries.index') }}" icon="globe" :active="request()->routeIs('admin.countries.*')">
                        الدول
                    </x-admin-nav-link>

                    {{-- العملات --}}
                    <x-admin-nav-link href="{{ route('admin.currencies.index') }}" icon="currency-dollar" :active="request()->routeIs('admin.currencies.*')">
                        العملات
                    </x-admin-nav-link>

                    <hr class="border-gray-800 my-4 mx-2">

                    {{-- الإعدادات العامة --}}
                    <x-admin-nav-link href="{{ route('admin.settings') }}" icon="settings" :active="request()->routeIs('admin.settings')">
                        الإعدادات العامة
                    </x-admin-nav-link>

                    {{-- الصفحات --}}
                    <a href="{{ route('admin.pages.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all text-sm font-semibold
                              {{ request()->routeIs('admin.pages.*') ? 'bg-white/10 text-white' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}"
                       :class="sidebarOpen ? '' : 'justify-center'">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span x-show="sidebarOpen" class="truncate">الصفحات</span>
                    </a>
                @endcan
            </nav>
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