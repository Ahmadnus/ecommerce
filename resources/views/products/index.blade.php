{{-- resources/views/products/index.blade.php --}}

@extends('layouts.app')

@section('title', $currentCategory ? $currentCategory->name . ' — المتجر' : 'جميع المنتجات')

@push('head')
<style>
/* ── Shimmer skeleton ────────────────────────────────────────────── */
@keyframes shimmer {
    0%   { background-position: -800px 0; }
    100% { background-position:  800px 0; }
}
.shimmer {
    background: linear-gradient(90deg, #f5f5f5 25%, #ebebeb 50%, #f5f5f5 75%);
    background-size: 1600px 100%;
    animation: shimmer 1.8s ease-in-out infinite;
}

/* ── Product card ─────────────────────────────────────────────────── */
.pcard { transition: transform .22s ease, box-shadow .22s ease; }
.pcard:hover { transform: translateY(-3px); box-shadow: 0 12px 32px rgba(0,0,0,.08); }
.pcard:hover .pcard-img { transform: scale(1.06); }
.pcard-img { transition: transform .4s ease; }

/* ── Wishlist heart btn ───────────────────────────────────────────── */
.heart-btn {
    width: 30px; height: 30px;
    background: rgba(255,255,255,.92);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    backdrop-filter: blur(4px);
    transition: transform .15s, background .15s;
    box-shadow: 0 1px 6px rgba(0,0,0,.12);
    border: none; cursor: pointer;
    flex-shrink: 0;
}
.heart-btn:hover { transform: scale(1.15); background: #fff; }
.heart-btn svg   { width: 15px; height: 15px; }

/* ── Sale ribbon ─────────────────────────────────────────────────── */
.ribbon {
    position: absolute; top: 0; left: 0;
    background: #ff3366;
    color: #fff; font-size: 9px; font-weight: 800;
    padding: 2px 7px 2px 4px;
    border-bottom-right-radius: 8px;
    letter-spacing: .04em;
    line-height: 1.6;
    z-index: 5;
}

/* ── Category pill filter bar ────────────────────────────────────── */
.cat-pill {
    white-space: nowrap;
    padding: 6px 16px;
    border-radius: 99px;
    font-size: 12px; font-weight: 700;
    border: 1.5px solid #e5e5e5;
    background: #fff; color: #555;
    transition: all .18s;
    cursor: pointer;
    flex-shrink: 0;
}
.cat-pill:hover, .cat-pill.active {
    background: var(--brand-color, #0ea5e9);
    border-color: var(--brand-color, #0ea5e9);
    color: #fff;
}

/* ── Floating filter btn (mobile) ────────────────────────────────── */
.filter-fab {
    position: fixed; bottom: 76px; left: 50%;
    transform: translateX(-50%);
    display: flex; align-items: center; gap: 6px;
    background: #fff;
    border: 1.5px solid #e0e0e0;
    border-radius: 99px;
    padding: 9px 20px;
    font-size: 12px; font-weight: 700;
    box-shadow: 0 4px 20px rgba(0,0,0,.12);
    z-index: 40;
    cursor: pointer;
}

/* ── Mobile bottom-bar ───────────────────────────────────────────── */
.bottom-bar {
    position: fixed; bottom: 0; left: 0; right: 0;
    background: rgba(255,255,255,.92);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border-top: 1px solid rgba(0,0,0,.07);
    padding-bottom: env(safe-area-inset-bottom, 0);
    z-index: 50;
}
.bb-item {
    display: flex; flex-direction: column; align-items: center;
    gap: 3px; padding: 8px 0 6px;
    font-size: 10px; font-weight: 600;
    color: #888;
    transition: color .15s;
    flex: 1; cursor: pointer;
    text-decoration: none;
}
.bb-item svg { width: 22px; height: 22px; stroke-width: 1.8; }
.bb-item.active { color: var(--brand-color, #0ea5e9); }
.bb-item.active svg { stroke-width: 2.4; }
.bb-badge {
    position: absolute; top: 6px; right: calc(50% - 18px);
    background: #ff3366;
    color: #fff; font-size: 8px; font-weight: 800;
    min-width: 16px; height: 16px;
    border-radius: 99px;
    display: flex; align-items: center; justify-content: center;
    padding: 0 3px;
    border: 1.5px solid #fff;
}

/* ── Hero banner ─────────────────────────────────────────────────── */
.hero-banner {
    background: linear-gradient(135deg, #0f0f0f 0%, #1a1a2e 60%, #0f3460 100%);
    border-radius: 20px;
    overflow: hidden;
    position: relative;
}
@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(-2deg); }
    50%       { transform: translateY(-10px) rotate(1deg); }
}
.hero-img { animation: float 5s ease-in-out infinite; }

/* ── Sort drawer ─────────────────────────────────────────────────── */
.sort-drawer-overlay {
    position: fixed; inset: 0;
    background: rgba(0,0,0,.4);
    z-index: 60;
    opacity: 0; pointer-events: none;
    transition: opacity .25s;
}
.sort-drawer-overlay.open { opacity: 1; pointer-events: auto; }
.sort-drawer {
    position: fixed; bottom: 0; left: 0; right: 0;
    background: #fff;
    border-radius: 24px 24px 0 0;
    padding: 20px 20px calc(env(safe-area-inset-bottom, 0px) + 20px);
    z-index: 61;
    transform: translateY(100%);
    transition: transform .3s cubic-bezier(.16,1,.3,1);
}
.sort-drawer.open { transform: translateY(0); }

/* safe area bottom for main content */
.pb-bottombar { padding-bottom: calc(64px + env(safe-area-inset-bottom, 0px)); }

/* star rating */
.star-fill { color: #fbbf24; }
</style>
@endpush

@section('content')

{{-- ═══ MOBILE BOTTOM BAR ════════════════════════════════════════════ --}}
<div class="bottom-bar md:hidden">
    <div class="flex items-stretch">

        {{-- Home --}}
        <a href="{{ url('/') }}"
           class="bb-item {{ request()->routeIs('home') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            الرئيسية
        </a>

        {{-- Categories --}}
        <a href="{{ route('products.index') }}"
           class="bb-item {{ request()->routeIs('products.index') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h8m-8 6h16"/>
            </svg>
            التصنيفات
        </a>

        {{-- Cart (center, big) --}}
        <a href="{{ route('cart.index') }}"
           class="bb-item relative {{ request()->routeIs('cart.*') ? 'active' : '' }}">
            <div class="relative">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center -mt-5 shadow-lg shadow-brand-600/30"
                     style="background: var(--brand-color, #0ea5e9)">
                    <svg class="w-5 h-5 text-white" style="stroke-width:2.2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                @php $cartCount = app(\App\Services\CartService::class)->getItemCount(); @endphp
                @if($cartCount > 0)
                <span class="absolute -top-4 -right-1 w-4 h-4 bg-red-500 text-white text-[9px] font-black rounded-full flex items-center justify-center border border-white leading-none">
                    {{ $cartCount }}
                </span>
                @endif
            </div>
            السلة
        </a>

        {{-- Wishlist --}}
        @auth
        <a href="{{ route('wishlist.index') }}"
           class="bb-item relative {{ request()->routeIs('wishlist.*') ? 'active' : '' }}">
            <div class="relative">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                @php $wishlistCount = auth()->user()->wishlistedProducts()->count(); @endphp
                @if($wishlistCount > 0)
                <span class="bb-badge">{{ $wishlistCount }}</span>
                @endif
            </div>
            المفضلة
        </a>
        @else
        <a href="{{ route('login') }}" class="bb-item">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
            المفضلة
        </a>
        @endauth

        {{-- Profile --}}
        @auth
        <div class="bb-item relative" x-data="{open:false}" @click="open=!open">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            حسابي
            {{-- Profile mini-menu --}}
            <div x-show="open" @click.outside="open=false"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="absolute bottom-full right-0 mb-2 w-44 bg-white rounded-2xl shadow-xl border border-gray-100 py-2 z-50"
                 style="display:none">
                <a href="{{ route('orders.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    طلباتي
                </a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex items-center gap-2.5 w-full px-4 py-2.5 text-sm font-medium text-red-600 hover:bg-red-50">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        تسجيل الخروج
                    </button>
                </form>
            </div>
        </div>
        @else
        <a href="{{ route('login') }}" class="bb-item">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            سجل دخول
        </a>
        @endauth

    </div>
</div>

{{-- ═══ SORT DRAWER (mobile) ═══════════════════════════════════════════ --}}
<div class="sort-drawer-overlay" id="sort-overlay" onclick="closeSortDrawer()">
    <div class="sort-drawer" id="sort-drawer" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between mb-4">
            <p class="font-bold text-gray-900 text-sm">ترتيب حسب</p>
            <button onclick="closeSortDrawer()" class="text-gray-400 p-1 hover:text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        @foreach([
            'featured'   => 'المميزة أولاً',
            'price_asc'  => 'السعر: من الأقل للأعلى',
            'price_desc' => 'السعر: من الأعلى للأقل',
            'newest'     => 'الأحدث أولاً',
        ] as $val => $lbl)
        <a href="{{ request()->fullUrlWithQuery(['sort' => $val]) }}"
           onclick="closeSortDrawer()"
           class="flex items-center justify-between py-3.5 border-b border-gray-50 text-sm font-medium
                  {{ request('sort', 'featured') === $val ? 'text-brand-600 font-bold' : 'text-gray-700' }}">
            {{ $lbl }}
            @if(request('sort', 'featured') === $val)
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--brand-color)">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
            @endif
        </a>
        @endforeach
    </div>
</div>

{{-- ═══ PAGE CONTENT ════════════════════════════════════════════════════ --}}
<div class="bg-gray-50 pb-bottombar md:pb-10" dir="rtl">
<div class="max-w-screen-2xl mx-auto px-3 sm:px-5 lg:px-8">

    {{-- ── Hero Banner ─────────────────────────────────────────────── --}}
    @if(!$currentCategory)
    <div class="hero-banner mb-5 mt-4">
        <div class="flex items-center gap-6 px-6 md:px-12 py-8 md:py-10">
            {{-- Text --}}
            <div class="flex-1 text-right">
                <span class="inline-block bg-red-500 text-white text-[10px] font-black px-3 py-1 rounded-full mb-3 tracking-wider uppercase">
                    عروض خاصة
                </span>
                <h2 class="font-display text-2xl md:text-4xl font-bold text-white leading-tight mb-3">
                    تشكيلة صيف 2026
                    <br>
                    <span class="text-transparent bg-clip-text" style="background: linear-gradient(90deg, #60a5fa, #a78bfa)">
                        خصم يصل لـ 50%
                    </span>
                </h2>
                <p class="text-gray-400 text-sm mb-5 leading-relaxed hidden sm:block">
                    آلاف المنتجات بأسعار لا تصدق — شحن مجاني فوق $50
                </p>
                <a href="{{ route('products.index', ['sort' => 'price_asc']) }}"
                   class="inline-flex items-center gap-2 bg-white text-gray-900 font-black text-sm px-6 py-3 rounded-xl hover:bg-gray-100 transition-colors shadow-lg">
                    اكتشف الآن
                    <svg class="w-4 h-4 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
            {{-- Image --}}
            <div class="hidden sm:block w-40 md:w-56 flex-shrink-0">
                <img src="https://images.unsplash.com/photo-1483985988355-763728e1935b?w=400&q=80"
                     alt="Hero"
                     class="hero-img w-full h-44 md:h-56 object-cover rounded-2xl opacity-90">
            </div>
        </div>
    </div>
    @endif

    {{-- ── Category Pill Bar ────────────────────────────────────────── --}}
    @php
        $topCategories = \App\Models\Category::active()->roots()
            ->with('children')
            ->orderBy('sort_order')
            ->take(12)->get();
    @endphp
    @if($topCategories->isNotEmpty())
    <div class="flex items-center gap-2 overflow-x-auto pb-2 mb-4 scrollbar-hide -mx-3 px-3">
        <a href="{{ route('products.index') }}"
           class="cat-pill {{ !request('category') ? 'active' : '' }}">
            الكل
        </a>
        @foreach($topCategories as $cat)
        <a href="{{ route('products.index', ['category' => $cat->slug]) }}"
           class="cat-pill {{ request('category') == $cat->slug ? 'active' : '' }}">
            {{ $cat->name }}
        </a>
        @endforeach
    </div>
    @endif

    {{-- ── Toolbar ──────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between mb-4 gap-3">
        <div>
            @if($currentCategory)
            <h1 class="font-display text-lg md:text-2xl font-bold text-gray-900">
                {{ $currentCategory->name }}
            </h1>
            @endif
            <p class="text-xs text-gray-400 {{ $currentCategory ? 'mt-0.5' : '' }}">
                {{ $products->total() }} منتج
                @if(request('search'))
                لـ "<span class="text-gray-700 font-medium">{{ request('search') }}</span>"
                @endif
            </p>
        </div>

        <div class="flex items-center gap-2">
            {{-- Search (desktop) --}}
            <form method="GET" action="{{ route('products.index') }}" class="hidden sm:flex">
                @if(request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                <div class="relative">
                    <input type="text" name="search"
                           value="{{ request('search') }}"
                           placeholder="بحث..."
                           class="pe-9 ps-3 py-2 text-xs border border-gray-200 rounded-xl
                                  focus:ring-2 focus:ring-brand-500 focus:border-brand-500
                                  outline-none w-36 bg-white">
                    <button type="submit"
                            class="absolute inset-y-0 end-0 flex items-center pe-2.5 text-gray-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                </div>
            </form>

            {{-- Sort (desktop) --}}
            <div class="hidden sm:block">
                <select onchange="window.location.href=this.value"
                        class="text-xs border border-gray-200 rounded-xl px-3 py-2 bg-white cursor-pointer focus:ring-2 focus:ring-brand-500 outline-none">
                    @foreach(['featured' => 'المميزة', 'price_asc' => 'السعر ↑', 'price_desc' => 'السعر ↓', 'newest' => 'الأحدث'] as $v => $l)
                    <option value="{{ request()->fullUrlWithQuery(['sort' => $v]) }}"
                            {{ request('sort', 'featured') === $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter + Sort pill (mobile) --}}
            <div class="flex sm:hidden items-center gap-2">
                <button onclick="openSortDrawer()"
                        class="flex items-center gap-1.5 bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs font-bold text-gray-600 shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h18M7 8h10m-7 4h4"/>
                    </svg>
                    ترتيب
                </button>
            </div>
        </div>
    </div>

    {{-- ── Breadcrumb ───────────────────────────────────────────────── --}}
    @if($currentCategory)
    <nav class="flex items-center gap-1 text-xs text-gray-400 mb-4 flex-wrap">
        <a href="{{ route('products.index') }}" class="hover:text-gray-700 transition-colors">المتجر</a>
        @foreach($currentCategory->getAncestors() as $ancestor)
            <span class="text-gray-300">/</span>
            <a href="{{ route('products.index', ['category' => $ancestor->slug]) }}"
               class="hover:text-gray-700 transition-colors">{{ $ancestor->name }}</a>
        @endforeach
        <span class="text-gray-300">/</span>
        <span class="text-gray-700 font-semibold">{{ $currentCategory->name }}</span>
    </nav>
    @endif

    {{-- ═════════════════════════════════════════════════════════════════
         PRODUCT GRID
    ═════════════════════════════════════════════════════════════════ --}}
    @if($products->isEmpty())
    <div class="flex flex-col items-center justify-center py-24 text-center">
        <div class="w-16 h-16 bg-white rounded-2xl border border-gray-100 flex items-center justify-center mb-4 shadow-sm">
            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <p class="text-gray-500 font-semibold text-sm">لا توجد منتجات</p>
        <a href="{{ route('products.index') }}" class="mt-3 text-xs font-bold" style="color:var(--brand-color)">
            عرض الكل
        </a>
    </div>

    @else
    {{-- Dense Shein-style grid: 2 cols mobile → 3 sm → 4 md → 5 lg → 6 xl --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-2 sm:gap-3">

        @foreach($products as $product)
        @php
            $cardImage    = $product->image_url ?? 'https://picsum.photos/seed/'.$product->id.'/400/600';
            $isWishlisted = in_array($product->id, $wishlistedIds ?? []);
        @endphp

        <div class="pcard bg-white rounded-2xl overflow-hidden flex flex-col group relative cursor-pointer"
             onclick="window.location='{{ route('products.show', $product->slug) }}'">

            {{-- ── Image area ──────────────────────────────────────── --}}
            <div class="relative overflow-hidden bg-gray-50" style="padding-top: 130%">
                {{-- Skeleton --}}
                <div class="shimmer absolute inset-0 z-0" id="sk-{{ $product->id }}"></div>

                <img src="{{ $cardImage }}"
                     alt="{{ $product->name }}"
                     class="pcard-img absolute inset-0 w-full h-full object-cover z-10"
                     loading="lazy"
                     onload="document.getElementById('sk-{{ $product->id }}').style.display='none'">

                {{-- Sale ribbon --}}
                @if($product->is_on_sale)
                <div class="ribbon">{{ $product->discount_percentage }}% OFF</div>
                @endif

                {{-- Featured badge --}}
                @if($product->is_featured)
                <span class="absolute top-0 right-0 bg-amber-400 text-amber-900 text-[9px] font-black px-2 py-0.5 z-10"
                      style="border-bottom-left-radius:8px">
                    مميز ⭐
                </span>
                @endif

                {{-- Heart btn --}}
                <div class="absolute bottom-2 left-2 z-20"
                     onclick="event.stopPropagation()">
                    @auth
                    <button type="button"
                            class="heart-btn wishlist-btn"
                            data-product-id="{{ $product->id }}"
                            data-wishlisted="{{ $isWishlisted ? 'true' : 'false' }}"
                            onclick="toggleWishlist(this)"
                            aria-label="{{ $isWishlisted ? 'إزالة' : 'إضافة للمفضلة' }}">
                        {{-- Outline --}}
                        <svg data-heart="outline"
                             class="{{ $isWishlisted ? 'hidden' : '' }}"
                             fill="none" stroke="#888" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        {{-- Filled --}}
                        <svg data-heart="filled"
                             class="{{ $isWishlisted ? '' : 'hidden' }}"
                             fill="#ff3366" stroke="#ff3366" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </button>
                    @endauth
                </div>

                {{-- Quick-add (shows on image hover, desktop only) --}}
                @if($product->in_stock)
                <div class="absolute bottom-2 right-2 z-20 opacity-0 group-hover:opacity-100 transition-opacity duration-200 hidden sm:block"
                     onclick="event.stopPropagation()">
                    <button type="button"
                            onclick="Cart.add({{ $product->id }}, 1, this)"
                            class="heart-btn"
                            style="width:30px;height:30px;background:var(--brand-color);border-radius:8px"
                            title="أضف للسلة">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="white" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                    </button>
                </div>
                @endif

                {{-- Out of stock --}}
                @if(!$product->in_stock)
                <div class="absolute inset-0 bg-white/50 z-10 flex items-end justify-center pb-3">
                    <span class="bg-white/95 text-gray-600 text-[10px] font-bold px-3 py-1 rounded-full border border-gray-100">
                        نفد المخزون
                    </span>
                </div>
                @endif
            </div>

            {{-- ── Info area ───────────────────────────────────────── --}}
            <div class="px-2 pt-2 pb-2.5 flex flex-col gap-1">

                {{-- Category --}}
                @if($product->categories->first())
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider leading-none truncate">
                    {{ $product->categories->first()->name }}
                </p>
                @endif

                {{-- Name --}}
                <p class="text-xs font-semibold text-gray-800 line-clamp-2 leading-snug">
                    {{ $product->name }}
                </p>

                {{-- Price row --}}
                <div class="flex items-center justify-between mt-0.5 gap-1">
                    <div class="flex items-baseline gap-1.5 flex-wrap">
                        @if($product->is_on_sale)
                        <span class="text-sm font-black text-red-500 leading-none tabular-nums">
                            ${{ number_format($product->discount_price, 2) }}
                        </span>
                        <span class="text-[10px] text-gray-400 line-through leading-none tabular-nums">
                            ${{ number_format($product->base_price, 2) }}
                        </span>
                        @else
                        <span class="text-sm font-black text-gray-900 leading-none tabular-nums">
                            ${{ number_format($product->base_price, 2) }}
                        </span>
                        @endif
                    </div>

                    {{-- Mobile add btn (always visible on mobile) --}}
                    @if($product->in_stock)
                    <button type="button"
                            class="sm:hidden w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0 transition-colors active:scale-90"
                            style="background:var(--brand-color)"
                            onclick="event.stopPropagation(); Cart.add({{ $product->id }}, 1, this)">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="white" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                    </button>
                    @endif
                </div>

                {{-- Variant count --}}
                @if($product->variants->where('is_active', true)->count() > 1)
                <p class="text-[10px] text-gray-400 leading-none">
                    {{ $product->variants->where('is_active', true)->count() }} خيارات
                </p>
                @endif

            </div>
        </div>

        @endforeach
    </div>

    {{-- Pagination --}}
    @if($products->hasPages())
    <div class="mt-8 flex justify-center">
        {{ $products->links() }}
    </div>
    @endif
    @endif

</div>{{-- /container --}}
</div>{{-- /page --}}

@endsection

@push('scripts')
<script>
// ── Sort drawer ────────────────────────────────────────────────────────
function openSortDrawer() {
    document.getElementById('sort-overlay').classList.add('open');
    document.getElementById('sort-drawer').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeSortDrawer() {
    document.getElementById('sort-overlay').classList.remove('open');
    document.getElementById('sort-drawer').classList.remove('open');
    document.body.style.overflow = '';
}

// ── Category tree (sidebar, legacy) ───────────────────────────────────
function toggleCategory(btn) {
    const node     = btn.closest('.category-node');
    const children = node?.querySelector('.category-children');
    const arrow    = btn.querySelector('.rtl-arrow');
    if (!children) return;
    const hidden = children.classList.toggle('hidden');
    arrow?.classList.toggle('rotate-90', !hidden);
}

// Prevent card navigation when inner buttons clicked (belt + suspenders)
document.querySelectorAll('.pcard [onclick]').forEach(el => {
    el.addEventListener('click', e => e.stopPropagation());
});
</script>
@endpush
