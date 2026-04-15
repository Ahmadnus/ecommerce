{{--
    resources/views/products/index.blade.php
    ─────────────────────────────────────────
    Layout order:
      1. Announcement Banner   (text-only, full-width, brand-colored)
      2. Hero Banner           (dark card — only on root /products)
      3. Category Pill Bar     (horizontal scroll)
      4. Toolbar               (title + search + sort)
      5. Featured Horizontal   (horizontal ListView — only on root /products)
      6. Regular Grid          (2-col mobile → up to 6-col desktop, scroll-reveal)

    Backend variables untouched:
      $products, $currentCategory, $wishlistedIds, $categoryTree
--}}
@extends('layouts.app')

@section('title', $currentCategory ? $currentCategory->name . ' — المتجر' : 'جميع المنتجات')

@push('head')
<style>
/* ════════════════════════════════════════════════════════════════
   0. CSS DESIGN TOKENS  — change everything from here
════════════════════════════════════════════════════════════════ */
:root {
    /* سحب اللون الأساسي من الإعدادات أو استخدام افتراضي */
    --brand:          var(--brand-color, #0ea5e9); 
    --brand-dark:     color-mix(in srgb, var(--brand) 75%, #000);
    --brand-light:    color-mix(in srgb, var(--brand) 12%, #fff);
    
    /* سحب لون الخلفية العام الذي حدده الأدمن */
    --surface:        var(--nav-bg-color, #ffffff); /* خلفية الكروت (غالباً مثل لون النافبار) */
    --surface-2:      var(--bg-color, #f8f8f8);    /* خلفية الصفحة العامة */
    
    /* باقي المتغيرات تبقى كما هي */
    --sale-red:       #ff3366;
    --border:         #efefef;
    --text-1:         #111827;
    --text-2:         #6b7280;
    --radius-card:    16px;
    --shadow-card:    0 4px 24px rgba(0,0,0,.07);
}

/* ════════════════════════════════════════════════════════════════
   1. ANNOUNCEMENT BANNER
════════════════════════════════════════════════════════════════ */
.announce-bar {
    background: var(--brand);
    color: #fff;
    text-align: center;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .04em;
    padding: 9px 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    overflow: hidden;
    position: relative;
}
.announce-bar::before,
.announce-bar::after {
    content: '';
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: rgba(255,255,255,.06);
    pointer-events: none;
}
.announce-bar::before { left: -30px; }
.announce-bar::after  { right: -30px; }

.announce-ticker {
    display: flex;
    align-items: center;
    gap: 28px;
    animation: ticker 18s linear infinite;
    white-space: nowrap;
}
@media (min-width: 768px) {
    .announce-ticker { animation: none; gap: 40px; }
}
@keyframes ticker {
    0%   { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}
.announce-dot {
    width: 5px; height: 5px;
    border-radius: 50%;
    background: rgba(255,255,255,.55);
    flex-shrink: 0;
}

/* ════════════════════════════════════════════════════════════════
   2. HERO BANNER
════════════════════════════════════════════════════════════════ */
.hero-banner {
    /* التدرج سيبدأ بلون البراند الغامق وينتهي بلون خلفية الموقع المختار */
    background: linear-gradient(135deg, 
        color-mix(in srgb, var(--brand-color) 40%, #000) 0%, 
        color-mix(in srgb, var(--brand-color) 20%, #111) 55%, 
        var(--bg-color) 100%
    ) !important;
    
    border-radius: 20px;
    overflow: hidden;
    position: relative;
}

/* لضمان عدم وجود طبقات بيضاء تغطي التدرج */
.hero-banner::before {
    background: transparent !important;
}
.hero-banner::after {
    content: '';
    position: absolute;
    inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.02'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    pointer-events: none;
}
@keyframes heroFloat {
    0%, 100% { transform: translateY(0) rotate(-2deg) scale(1); }
    50%       { transform: translateY(-10px) rotate(1deg) scale(1.02); }
}
.hero-img { animation: heroFloat 6s ease-in-out infinite; transform-origin: center; }

/* ════════════════════════════════════════════════════════════════
   3. CATEGORY PILLS
════════════════════════════════════════════════════════════════ */
.cat-pill {
    white-space: nowrap;
    padding: 7px 17px;
    border-radius: 99px;
    font-size: 12px;
    font-weight: 700;
    border: 1.5px solid var(--border);
    background: var(--surface);
    color: var(--text-2);
    transition: all var(--transition-fast);
    cursor: pointer;
    flex-shrink: 0;
    text-decoration: none;
    display: inline-block;
}
.cat-pill:hover { border-color: var(--brand); color: var(--brand); }
.cat-pill.active {
    background: var(--brand);
    border-color: var(--brand);
    color: #fff;
    box-shadow: 0 3px 12px color-mix(in srgb, var(--brand) 35%, transparent);
}
/* hide native scrollbar on pill row */
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

/* ════════════════════════════════════════════════════════════════
   4. FEATURED — HORIZONTAL LISTVIEW
════════════════════════════════════════════════════════════════ */
.featured-list {
    display: flex;
    gap: 12px;
    overflow-x: auto;
    padding-bottom: 6px;
    scroll-snap-type: x mandatory;
    -webkit-overflow-scrolling: touch;
}
.featured-list::-webkit-scrollbar { display: none; }
.featured-list { scrollbar-width: none; }

.featured-card {
    min-width: 148px;
    max-width: 148px;
    flex-shrink: 0;
    scroll-snap-align: start;
  background: var(--card-bg) !important;
    border-radius: var(--radius-card);
    overflow: hidden;
    cursor: pointer;
    transition: transform var(--transition-fast), box-shadow var(--transition-fast);
    position: relative;
}
@media (min-width: 640px) {
    .featured-card { min-width: 175px; max-width: 175px; }
}
.featured-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-hover);
}
.featured-card:hover .fc-img { transform: scale(1.07); }
.fc-img { transition: transform var(--transition-med); }

.fc-ribbon {
    position: absolute; top: 0; right: 0;
    background: var(--sale-red);
    color: #fff; font-size: 9px; font-weight: 800;
    padding: 3px 9px 3px 7px;
    border-bottom-left-radius: 9px;
    z-index: 5;
    letter-spacing: .04em;
}

/* ════════════════════════════════════════════════════════════════
   5. REGULAR GRID CARDS
════════════════════════════════════════════════════════════════ */
.pcard {
    transition: transform var(--transition-fast), box-shadow var(--transition-fast);
    /* ربط الخلفية بالمتغير الديناميكي */
  background-color: var(--card-bg) !important;
    
    border-radius: var(--radius-card);
    overflow: hidden;
    cursor: pointer;
    position: relative;
    border: 1px solid rgba(0,0,0,0.05); /* إطار خفيف جداً */
}
.pcard:hover { transform: translateY(-3px); box-shadow: var(--shadow-hover); }
.pcard:hover .pcard-img { transform: scale(1.06); }
.pcard-img { transition: transform var(--transition-med); }

.ribbon {
    position: absolute; top: 0; left: 0;
    background: var(--sale-red);
    color: #fff; font-size: 9px; font-weight: 800;
    padding: 2px 8px 2px 5px;
    border-bottom-right-radius: 8px;
    letter-spacing: .04em;
    line-height: 1.7;
    z-index: 5;
}

/* ════════════════════════════════════════════════════════════════
   6. SHARED — SHIMMER SKELETON
════════════════════════════════════════════════════════════════ */
@keyframes shimmer {
    0%   { background-position: -900px 0; }
    100% { background-position:  900px 0; }
}
.shimmer {
    background: linear-gradient(90deg, #f4f4f4 25%, #ececec 50%, #f4f4f4 75%);
    background-size: 1800px 100%;
    animation: shimmer 1.8s ease-in-out infinite;
}

/* ════════════════════════════════════════════════════════════════
   7. HEART / WISHLIST BUTTON
════════════════════════════════════════════════════════════════ */
.heart-btn {
    width: 30px; height: 30px;
    background: rgba(255,255,255,.93);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    transition: transform var(--transition-fast), background var(--transition-fast);
    box-shadow: 0 1px 7px rgba(0,0,0,.13);
    border: none; cursor: pointer; flex-shrink: 0;
}
.heart-btn:hover { transform: scale(1.18); background: #fff; }
.heart-btn svg   { width: 15px; height: 15px; }

/* ════════════════════════════════════════════════════════════════
   8. SCROLL-REVEAL ANIMATION
════════════════════════════════════════════════════════════════ */
.reveal {
    opacity: 0;
    transform: translateY(22px);
    transition: opacity .55s cubic-bezier(.22,1,.36,1),
                transform .55s cubic-bezier(.22,1,.36,1);
    will-change: opacity, transform;
}
.reveal.visible {
    opacity: 1;
    transform: translateY(0);
}
/* stagger siblings automatically via --i custom property */
.reveal { transition-delay: calc(var(--i, 0) * 60ms); }

/* ════════════════════════════════════════════════════════════════
   9. MOBILE BOTTOM-BAR
════════════════════════════════════════════════════════════════ */


/* ════════════════════════════════════════════════════════════════
   10. SORT DRAWER
════════════════════════════════════════════════════════════════ */
.sort-drawer-overlay {
    position: fixed; inset: 0; background: rgba(0,0,0,.42);
    z-index: 60; opacity: 0; pointer-events: none; transition: opacity .25s;
}
.sort-drawer-overlay.open { opacity: 1; pointer-events: auto; }
.sort-drawer {
    position: fixed; bottom: 0; left: 0; right: 0;
    background: var(--surface); border-radius: 22px 22px 0 0;
    padding: 20px 20px calc(env(safe-area-inset-bottom,0px) + 20px);
    z-index: 61;
    transform: translateY(100%);
    transition: transform .32s cubic-bezier(.16,1,.3,1);
}
.sort-drawer.open { transform: translateY(0); }
.sort-option {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 0; border-bottom: 1px solid #f5f5f5;
    font-size: 13.5px; font-weight: 600; color: var(--text-2);
    cursor: pointer; transition: color var(--transition-fast);
    text-decoration: none;
}
.sort-option:hover, .sort-option.chosen { color: var(--brand); }

/* bottom padding for page content */
.pb-bar { padding-bottom: calc(68px + env(safe-area-inset-bottom, 0px)); }
</style>
@endpush

@section('content')

{{-- ══════════════════════════════════════════════════════════════════
     ANNOUNCEMENT BANNER
══════════════════════════════════════════════════════════════════ --}}
<div class="announce-bar md:hidden" dir="rtl">
    {{-- Mobile: auto-scrolling ticker --}}
    <div class="announce-ticker" aria-hidden="true">
        <span>🚚 شحن مجاني فوق $50</span>
        <span class="announce-dot"></span>
        <span>🎉 عروض حصرية يومياً</span>
        <span class="announce-dot"></span>
        <span>↩️ إرجاع مجاني 30 يوماً</span>
        <span class="announce-dot"></span>
        <span>🔒 دفع آمن ومشفر</span>
        <span class="announce-dot"></span>
        {{-- duplicate for seamless loop --}}
        <span>🚚 شحن مجاني فوق $50</span>
        <span class="announce-dot"></span>
        <span>🎉 عروض حصرية يومياً</span>
        <span class="announce-dot"></span>
        <span>↩️ إرجاع مجاني 30 يوماً</span>
        <span class="announce-dot"></span>
        <span>🔒 دفع آمن ومشفر</span>
    </div>
</div>
<div class="announce-bar hidden md:flex" dir="rtl">
    {{-- Desktop: static row --}}
    <span>🚚 شحن مجاني فوق $50</span>
    <span class="announce-dot"></span>
    <span>🎉 عروض حصرية يومياً</span>
    <span class="announce-dot"></span>
    <span>↩️ إرجاع مجاني خلال 30 يوماً</span>
    <span class="announce-dot"></span>
    <span>🔒 دفع آمن ومشفر</span>
</div>

{{-- ══════════════════════════════════════════════════════════════════
     MOBILE BOTTOM BAR
══════════════════════════════════════════════════════════════════ --}}
@php
    // جلب الرابط المفعل والمحدد كزر عائم من قاعدة البيانات
    $floatingLink = \App\Models\SocialLink::where('is_active', true)
                    ->where('is_floating', true)
                    ->first();
@endphp

{{-- إذا وجدنا رابط عائم، نقوم بإظهار الكومبونانت --}}
@if($floatingLink)
    <x-floating-button :number="$floatingLink->whatsapp_number" />
@endif

{{-- ══════════════════════════════════════════════════════════════════
     SORT DRAWER (mobile bottom sheet)
══════════════════════════════════════════════════════════════════ --}}
<div class="sort-drawer-overlay" id="sort-overlay" onclick="closeSortDrawer()">
    <div class="sort-drawer" id="sort-drawer" dir="rtl" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between mb-2">
            <p class="font-bold text-gray-900">ترتيب حسب</p>
            <button onclick="closeSortDrawer()" class="p-1.5 text-gray-400 hover:text-gray-700 rounded-lg hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        @foreach([
            'featured'   => ['label' => 'المميزة أولاً',            'icon' => '⭐'],
            'price_asc'  => ['label' => 'السعر: من الأقل للأعلى',  'icon' => '↑'],
            'price_desc' => ['label' => 'السعر: من الأعلى للأقل',  'icon' => '↓'],
            'newest'     => ['label' => 'الأحدث أولاً',             'icon' => '🆕'],
        ] as $val => $opt)
        @php $isChosen = request('sort', 'featured') === $val; @endphp
        <a href="{{ request()->fullUrlWithQuery(['sort' => $val]) }}"
           onclick="closeSortDrawer()"
           class="sort-option {{ $isChosen ? 'chosen' : '' }}">
            <span class="flex items-center gap-2">
                <span class="text-base">{{ $opt['icon'] }}</span>
                {{ $opt['label'] }}
            </span>
            @if($isChosen)
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--brand)">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
            @endif
        </a>
        @endforeach
    </div>
</div>
@yield('content')
@include('partials.bottombar')
{{-- ══════════════════════════════════════════════════════════════════
     PAGE BODY
══════════════════════════════════════════════════════════════════ --}}
<div class="bg-gray-50 pb-bar md:pb-12" dir="rtl">
<div class="max-w-screen-2xl mx-auto px-3 sm:px-5 lg:px-8">

    {{-- ── Hero Banner (root page only) ────────────────────────────── --}}
    @if(!$currentCategory)
    <div class="hero-banner mt-4 mb-5 reveal" style="--i:0">
        <div class="relative z-10 flex items-center gap-6 px-6 md:px-14 py-10 md:py-12">

            {{-- Text side --}}
            <div class="flex-1 text-right">
                <span class="inline-block text-[10px] font-black px-3 py-1 rounded-full mb-3 tracking-widest uppercase"
                      style="background: rgba(255,255,255,.12); color: rgba(255,255,255,.85); border: 1px solid rgba(255,255,255,.18)">
                    عروض محدودة
                </span>
                <h2 class="font-display text-2xl md:text-4xl font-bold text-white leading-tight mb-3">
                    تشكيلة صيف 2026
                    <br>
                    <span class="text-transparent bg-clip-text"
                          style="background: linear-gradient(90deg, #60a5fa, #a78bfa); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                        خصم يصل لـ 50%
                    </span>
                </h2>
                <p class="text-gray-400 text-sm mb-6 leading-relaxed max-w-sm hidden sm:block">
                    آلاف المنتجات بأسعار لا تصدق — شحن مجاني على كل طلب فوق $50
                </p>
                <a href="{{ route('products.index', ['sort' => 'price_asc']) }}"
                   class="inline-flex items-center gap-2 bg-white text-gray-900 font-black text-sm px-6 py-3 rounded-xl hover:bg-gray-50 transition-colors shadow-xl active:scale-95">
                    اكتشف الآن
                    <svg class="w-4 h-4 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            {{-- Image side --}}
         <div class="w-32 sm:w-40 md:w-52 flex-shrink-0 relative block">
    {{-- Glow ring --}}
    <div class="absolute inset-0 rounded-2xl opacity-30"
         style="background: radial-gradient(circle, var(--brand) 0%, transparent 70%); transform: scale(1.3)"></div>
    
    <img src="https://images.unsplash.com/photo-1483985988355-763728e1935b?w=500&q=80"
         alt="أحدث تشكيلة"
         class="hero-img relative z-10 w-full h-32 sm:h-44 md:h-56 object-cover rounded-2xl">
</div>
        </div>
    </div>

    @endif

<div
style="height: 10px;">
<p></p>
</div>
   
   
     @php
        $topCategories = \App\Models\Category::active()->roots()
            ->with('children')->orderBy('sort_order')->take(12)->get();
    @endphp
    @if($topCategories->isNotEmpty())
 
    @endif
 


 
@php
    $topCategories = \App\Models\Category::active()
        ->roots()
        ->with(['allActiveChildren', 'media'])   // eager-load media = no N+1
        ->orderBy('sort_order')
        ->take(20)
        ->get();
@endphp
 
<x-category-grid
    :categories="$topCategories"
    :current="$currentCategory ?? null"
    :show-all="true"
/>

            {{-- Desktop sort --}}
            <div class="hidden sm:block">
                <select onchange="window.location.href=this.value"
                        class="text-xs border border-gray-200 rounded-xl px-3 py-2 bg-white cursor-pointer outline-none focus:ring-2"
                        style="--tw-ring-color: var(--brand)">
                    @foreach(['featured' => 'المميزة', 'price_asc' => 'السعر ↑', 'price_desc' => 'السعر ↓', 'newest' => 'الأحدث'] as $v => $l)
                    <option value="{{ request()->fullUrlWithQuery(['sort' => $v]) }}"
                            {{ request('sort', 'featured') === $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Mobile sort trigger --}}
            <button onclick="openSortDrawer()"
                    class="flex sm:hidden items-center gap-1.5 bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs font-bold text-gray-600 shadow-sm active:scale-95 transition-transform">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h18M7 8h10m-7 4h4"/>
                </svg>
                ترتيب
            </button>
        </div>
    </div>

    {{-- ── Breadcrumb (category pages) ────────────────────────────────── --}}
    @if($currentCategory)
    <nav class="flex items-center gap-1 text-xs text-gray-400 mb-5 flex-wrap">
        <a href="{{ route('products.index') }}" class="hover:text-gray-700 transition-colors">المتجر</a>
        @foreach($currentCategory->getAncestors() as $ancestor)
            <span class="text-gray-300">/</span>
            <a href="{{ route('products.index', ['category' => $ancestor->slug]) }}"
               class="hover:text-gray-700 transition-colors">{{ $ancestor->name }}</a>
        @endforeach
        <span class="text-gray-300">/</span>
        <span class="text-gray-900 font-semibold">{{ $currentCategory->name }}</span>
    </nav>
    @endif

    {{-- ════════════════════════════════════════════════════════════════
         FEATURED — HORIZONTAL LISTVIEW  (root page only)
    ════════════════════════════════════════════════════════════════ --}}
    @if(!$currentCategory)
    @php $featuredProducts = $products->getCollection()->where('is_featured', true)->take(10); @endphp
    @if($featuredProducts->isNotEmpty())
    <section class="mb-7">
        {{-- Section header --}}
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <span class="w-1 h-5 rounded-full" style="background:var(--brand)"></span>
                <h2 class="font-display text-base md:text-lg font-bold text-gray-900">المنتجات المميزة</h2>
            </div>
            <a href="{{ route('products.index', ['sort' => 'featured']) }}"
               class="text-xs font-bold hover:underline transition-colors"
               style="color:var(--brand)">
                عرض الكل ←
            </a>
        </div>

        {{-- Horizontal list --}}
        <div class="featured-list">
            @foreach($featuredProducts as $fp)
            @php
                $fpImage = $fp->getFirstMediaUrl('products')
                    ?: ($fp->image_url ?? 'https://picsum.photos/seed/'.$fp->id.'/300/390');
                $fpWishlisted = in_array($fp->id, $wishlistedIds ?? []);
            @endphp
            <div class="featured-card group"
                 onclick="window.location='{{ route('products.show', $fp->slug) }}'">

                {{-- Image --}}
                <div class="relative overflow-hidden bg-gray-100" style="padding-top: 126%">
                    <div class="shimmer absolute inset-0 z-0" id="fsk-{{ $fp->id }}"></div>
                    <img src="{{ $fp->getFirstMediaUrl('products') ?: asset('images/default-product.jpg') }}"
             alt="{{ $fp->name }}"
             class="fc-img absolute inset-0 w-full h-full object-cover z-10 transition-opacity duration-300"
             loading="lazy"
             onload="document.getElementById('fsk-{{ $fp->id }}').style.opacity='0'; setTimeout(() => document.getElementById('fsk-{{ $fp->id }}').style.display='none', 300)">
                    {{-- Sale ribbon --}}
                    @if($fp->is_on_sale)
                    <div class="fc-ribbon">{{ $fp->discount_percentage }}% OFF</div>
                    @endif

                    {{-- Heart --}}
                    <div class="absolute bottom-2 left-2 z-20" onclick="event.stopPropagation()">
                        @auth
                        <button type="button"
                                class="heart-btn wishlist-btn"
                                data-product-id="{{ $fp->id }}"
                                data-wishlisted="{{ $fpWishlisted ? 'true' : 'false' }}"
                                onclick="toggleWishlist(this)">
                            <svg data-heart="outline" class="{{ $fpWishlisted ? 'hidden' : '' }}"
                                 fill="none" stroke="#888" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                            <svg data-heart="filled" class="{{ $fpWishlisted ? '' : 'hidden' }}"
                                 fill="#ff3366" stroke="#ff3366" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </button>
                        @endauth
                    </div>
                </div>

                {{-- Info --}}
                <div class="px-2.5 pt-2 pb-3">
                    <p class="text-xs font-semibold text-gray-800 line-clamp-2 leading-snug mb-1">
                        {{ $fp->name }}
                    </p>
                    <div class="flex items-center gap-1.5">
                        @if($fp->is_on_sale)
                        <span class="text-sm font-black leading-none tabular-nums" style="color:var(--sale-red)">
                            ${{ number_format($fp->discount_price, 2) }}
                        </span>
                        <span class="text-[10px] text-gray-400 line-through tabular-nums">
                            ${{ number_format($fp->base_price, 2) }}
                        </span>
                        @else
                        <span class="text-sm font-black text-gray-900 leading-none tabular-nums">
                            ${{ number_format($fp->base_price, 2) }}
                        </span>
                        @endif
                    </div>
                </div>

            </div>
            @endforeach
        </div>
    </section>
    @endif

    {{-- Section divider --}}
    <div class="flex items-center gap-3 mb-5">
        <div class="h-px bg-gray-200 flex-1"></div>
        <span class="text-xs font-bold text-gray-500 flex-shrink-0">جميع المنتجات</span>
        <div class="h-px bg-gray-200 flex-1"></div>
    </div>
    @endif

    {{-- ════════════════════════════════════════════════════════════════
         PRODUCT GRID  — with scroll-reveal
    ════════════════════════════════════════════════════════════════ --}}
    @if($products->isEmpty())
    <div class="flex flex-col items-center justify-center py-24 text-center">
        <div class="w-16 h-16 bg-white rounded-2xl border border-gray-100 flex items-center justify-center mb-4 shadow-sm">
            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <p class="text-gray-500 font-semibold text-sm mb-1">لا توجد منتجات</p>
        <a href="{{ route('products.index') }}" class="text-xs font-bold hover:underline" style="color:var(--brand)">
            عرض جميع المنتجات
        </a>
    </div>

    @else
    {{-- 2-col mobile → 3 sm → 4 md → 5 lg → 6 xl --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-2 sm:gap-3">

        @foreach($products as $i => $product)
        @php
            $cardImage    = $product->getFirstMediaUrl('products')
                ?: ($product->image_url ?? 'https://picsum.photos/seed/'.$product->id.'/400/600');
            $isWishlisted = in_array($product->id, $wishlistedIds ?? []);
        @endphp

        {{-- reveal class + --i for staggered delay (capped at 5 to avoid long waits) --}}
        <div class="pcard flex flex-col group reveal"
             style="--i: {{ min($i % 6, 5) }}"
             onclick="window.location='{{ route('products.show', $product->slug) }}'">

            {{-- Image container --}}
            <div class="relative overflow-hidden bg-gray-100" style="padding-top: 130%">
                {{-- Shimmer skeleton --}}
                <div class="shimmer absolute inset-0 z-0" id="sk-{{ $product->id }}"></div>

                {{-- Spatie image --}}
              <img src="{{ $product->getFirstMediaUrl('products') ?: asset('images/placeholder.jpg') }}"
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
                <span class="absolute top-0 right-0 z-10 text-amber-900 text-[9px] font-black px-2 py-0.5"
                      style="background:var(--gold); border-bottom-left-radius:9px">
                    مميز ⭐
                </span>
                @endif

                {{-- Wishlist heart --}}
                <div class="absolute bottom-2 left-2 z-20" onclick="event.stopPropagation()">
                    @auth
                    <button type="button"
                            class="heart-btn wishlist-btn"
                            data-product-id="{{ $product->id }}"
                            data-wishlisted="{{ $isWishlisted ? 'true' : 'false' }}"
                            onclick="toggleWishlist(this)"
                            aria-label="{{ $isWishlisted ? 'إزالة من المفضلة' : 'إضافة للمفضلة' }}">
                        <svg data-heart="outline"
                             class="{{ $isWishlisted ? 'hidden' : '' }}"
                             fill="none" stroke="#999" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        <svg data-heart="filled"
                             class="{{ $isWishlisted ? '' : 'hidden' }}"
                             fill="#ff3366" stroke="#ff3366" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </button>
                    @endauth
                </div>

                {{-- Desktop quick-add (hover reveal) --}}
                @if($product->in_stock)
                
                @endif

                {{-- Out of stock overlay --}}
                @if(!$product->in_stock)
                <div class="absolute inset-0 bg-white/55 z-10 flex items-end justify-center pb-3">
                    <span class="bg-white/95 text-gray-500 text-[10px] font-bold px-3 py-1 rounded-full border border-gray-100 shadow-sm">
                        نفد المخزون
                    </span>
                </div>
                @endif
            </div>

            {{-- Card body --}}
            <div class="px-2 pt-2 pb-2.5 flex flex-col gap-1">

                @if($product->categories->first())
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide leading-none truncate">
                    {{ $product->categories->first()->name }}
                </p>
                @endif

                <p class="text-xs font-semibold text-gray-800 line-clamp-2 leading-snug">
                    {{ $product->name }}
                </p>

               <td class="px-6 py-4">
    <div class="flex flex-col">
        @if($product->discount_price && $product->discount_price < $product->base_price)
            {{-- ملصق التخفيض --}}
            <span class="text-xs text-red-500 font-bold bg-red-50 px-1.5 py-0.5 rounded w-fit mb-1 u1">تخفيض</span>
            
            <div class="flex items-center gap-2 u2">
                {{-- السعر بعد الخصم (يتحول تلقائياً حسب العملة النشطة) --}}
                <x-price :amount="$product->discount_price" class="font-bold text-gray-900" />
                
                {{-- السعر الأصلي مشطوب --}}
                <x-price :amount="$product->base_price" class="text-xs text-gray-400 line-through" />
            </div>
        @else
            {{-- السعر الأساسي في حال عدم وجود خصم --}}
            <div class="u2">
                <x-price :amount="$product->base_price" class="font-bold text-gray-900" />
            </div>
        @endif
    </div>
</td>

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
    <div class="mt-10 flex justify-center">
        {{ $products->links() }}
    </div>
    @endif

    @endif {{-- /products not empty --}}

</div>{{-- /container --}}
</div>{{-- /bg-gray-50 --}}

@endsection

@push('scripts')
<script>
/* ─── Sort drawer ──────────────────────────────────────────────────── */
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

/* ─── Scroll-reveal (IntersectionObserver) ─────────────────────────── */
(function () {
    const targets = document.querySelectorAll('.reveal');
    if (!targets.length) return;

    const io = new IntersectionObserver(
        (entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    io.unobserve(entry.target);   // fire once
                }
            });
        },
        {
            threshold: 0.08,       // trigger when 8% visible
            rootMargin: '0px 0px -30px 0px'  // slightly before reaching viewport edge
        }
    );

    targets.forEach(el => io.observe(el));
})();

/* ─── Stop card navigation on inner button clicks ──────────────────── */
document.querySelectorAll('.pcard button, .featured-card button').forEach(btn => {
    btn.addEventListener('click', e => e.stopPropagation());
});
</script>
@endpush