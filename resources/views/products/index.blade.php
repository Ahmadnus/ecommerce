{{--
    resources/views/products/index.blade.php
    ─────────────────────────────────────────────────────────────────────────────
    Changes from previous version:
    1. Live search bar with Alpine.js dropdown (replaces static form submission)
    2. Product cards include a share button (Web Share API + clipboard fallback)
    3. Home page sections driven by HomeSection model (no hardcoded blocks)
    ─────────────────────────────────────────────────────────────────────────────
--}}
@extends('layouts.app')

@section('title', $currentCategory ? $currentCategory->name . ' — المتجر' : 'جميع المنتجات')

@push('head')
<style>
:root {
    --brand:       var(--brand-color, #0ea5e9);
    --brand-dark:  color-mix(in srgb, var(--brand) 75%, #000);
    --brand-light: color-mix(in srgb, var(--brand) 12%, #fff);
    --surface:     var(--nav-bg-color, #ffffff);
    --surface-2:   var(--bg-color, #f8f8f8);
    --sale-red:    #ff3366;
    --border:      #efefef;
    --text-1:      #111827;
    --text-2:      #6b7280;
    --radius-card: 16px;
}

/* ── Announcement bar ─────────────────────────────────────────────────── */
.announce-bar {
    background: var(--brand); color: #fff;
    font-size: 12px; font-weight: 700; letter-spacing: .04em;
    padding: 9px 16px; display: flex; align-items: center; justify-content: center;
    gap: 10px; overflow: hidden; position: relative;
}
.announce-bar::before, .announce-bar::after {
    content:''; position:absolute; top:50%; transform:translateY(-50%);
    width:120px; height:120px; border-radius:50%;
    background:rgba(255,255,255,.06); pointer-events:none;
}
.announce-bar::before{left:-30px}.announce-bar::after{right:-30px}
.announce-ticker {
    display:flex; align-items:center; gap:28px;
    animation:ticker 18s linear infinite; white-space:nowrap;
}
@media(min-width:768px){.announce-ticker{animation:none;gap:40px}}
@keyframes ticker{0%{transform:translateX(0)}100%{transform:translateX(-50%)}}
.announce-dot { width:5px;height:5px;border-radius:50%;background:rgba(255,255,255,.55);flex-shrink:0; }

/* ── Hero banner ──────────────────────────────────────────────────────── */
.hero-banner {
    background: linear-gradient(135deg,
        color-mix(in srgb, var(--brand-color) 40%, #000) 0%,
        color-mix(in srgb, var(--brand-color) 20%, #111) 55%,
        var(--bg-color) 100%
    ) !important;
    border-radius: 20px; overflow: hidden; position: relative;
}
.hero-banner::before{background:transparent!important}
.hero-banner::after{
    content:''; position:absolute; inset:0;
    background:url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.02'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    pointer-events:none;
}
@keyframes heroFloat{0%,100%{transform:translateY(0) rotate(-2deg) scale(1)}50%{transform:translateY(-10px) rotate(1deg) scale(1.02)}}
.hero-img{animation:heroFloat 6s ease-in-out infinite}

/* ── Category pills ───────────────────────────────────────────────────── */
.cat-pill {
    white-space:nowrap; padding:7px 17px; border-radius:99px;
    font-size:12px; font-weight:700; border:1.5px solid var(--border);
    background:var(--surface); color:var(--text-2); cursor:pointer;
    flex-shrink:0; text-decoration:none; display:inline-block;
    transition:all .15s;
}
.cat-pill:hover { border-color:var(--brand); color:var(--brand); }
.cat-pill.active {
    background:var(--brand); border-color:var(--brand); color:#fff;
    box-shadow:0 3px 12px color-mix(in srgb, var(--brand) 35%, transparent);
}
.scrollbar-hide::-webkit-scrollbar{display:none}
.scrollbar-hide{-ms-overflow-style:none;scrollbar-width:none}

/* ── Featured horizontal list ─────────────────────────────────────────── */
.featured-list {
    display:flex; gap:12px; overflow-x:auto; padding-bottom:6px;
    scroll-snap-type:x mandatory; -webkit-overflow-scrolling:touch;
    scrollbar-width:none;
}
.featured-list::-webkit-scrollbar{display:none}
.featured-card {
    min-width:148px; max-width:148px; flex-shrink:0;
    scroll-snap-align:start; background:var(--card-bg)!important;
    border-radius:var(--radius-card); overflow:hidden; cursor:pointer;
    transition:transform .15s, box-shadow .15s; position:relative;
}
@media(min-width:640px){.featured-card{min-width:175px;max-width:175px}}
.featured-card:hover{transform:translateY(-4px);box-shadow:0 12px 32px rgba(0,0,0,.1)}
.featured-card:hover .fc-img{transform:scale(1.07)}
.fc-img{transition:transform .35s ease}
.fc-ribbon{
    position:absolute;top:0;right:0;background:var(--sale-red);
    color:#fff;font-size:9px;font-weight:800;padding:3px 9px 3px 7px;
    border-bottom-left-radius:9px;z-index:5;letter-spacing:.04em;
}

/* ── Regular grid ─────────────────────────────────────────────────────── */
.pcard {
    background-color:var(--card-bg)!important;
    border-radius:var(--radius-card); overflow:hidden;
    cursor:pointer; position:relative;
    border:1px solid rgba(0,0,0,.05);
    transition:transform .15s, box-shadow .15s;
}
.pcard:hover{transform:translateY(-3px);box-shadow:0 12px 32px rgba(0,0,0,.08)}
.pcard:hover .pcard-img{transform:scale(1.06)}
.pcard-img{transition:transform .35s ease}
.ribbon{
    position:absolute;top:0;left:0;background:var(--sale-red);
    color:#fff;font-size:9px;font-weight:800;padding:2px 8px 2px 5px;
    border-bottom-right-radius:8px;letter-spacing:.04em;line-height:1.7;z-index:5;
}

/* ── Shimmer ──────────────────────────────────────────────────────────── */
@keyframes shimmer{0%{background-position:-900px 0}100%{background-position:900px 0}}
.shimmer{
    background:linear-gradient(90deg,#f4f4f4 25%,#ececec 50%,#f4f4f4 75%);
    background-size:1800px 100%;animation:shimmer 1.8s ease-in-out infinite;
}

/* ── Heart / wishlist ─────────────────────────────────────────────────── */
.heart-btn {
    width:30px;height:30px;background:rgba(255,255,255,.93);
    border-radius:50%;display:flex;align-items:center;justify-content:center;
    backdrop-filter:blur(6px);-webkit-backdrop-filter:blur(6px);
    box-shadow:0 1px 7px rgba(0,0,0,.13);border:none;cursor:pointer;flex-shrink:0;
    transition:transform .15s, background .15s;
}
.heart-btn:hover{transform:scale(1.18);background:#fff}
.heart-btn svg{width:15px;height:15px}

/* ── Share button ─────────────────────────────────────────────────────── */
.share-btn {
    width:30px;height:30px;background:rgba(255,255,255,.93);
    border-radius:50%;display:flex;align-items:center;justify-content:center;
    backdrop-filter:blur(6px);-webkit-backdrop-filter:blur(6px);
    box-shadow:0 1px 7px rgba(0,0,0,.13);border:none;cursor:pointer;flex-shrink:0;
    transition:transform .15s, background .15s;
}
.share-btn:hover{transform:scale(1.18);background:#fff}
.share-btn svg{width:14px;height:14px}

/* ── Scroll reveal ────────────────────────────────────────────────────── */
.reveal{
    opacity:0;transform:translateY(22px);
    transition:opacity .55s cubic-bezier(.22,1,.36,1),transform .55s cubic-bezier(.22,1,.36,1);
    will-change:opacity,transform;
}
.reveal.visible{opacity:1;transform:translateY(0)}
.reveal{transition-delay:calc(var(--i,0) * 60ms)}

/* ── Sort drawer ──────────────────────────────────────────────────────── */
.sort-drawer-overlay{
    position:fixed;inset:0;background:rgba(0,0,0,.42);
    z-index:60;opacity:0;pointer-events:none;transition:opacity .25s;
}
.sort-drawer-overlay.open{opacity:1;pointer-events:auto}
.sort-drawer{
    position:fixed;bottom:0;left:0;right:0;background:var(--surface);
    border-radius:22px 22px 0 0;
    padding:20px 20px calc(env(safe-area-inset-bottom,0px) + 20px);
    z-index:61;transform:translateY(100%);
    transition:transform .32s cubic-bezier(.16,1,.3,1);
}
.sort-drawer.open{transform:translateY(0)}
.sort-option{
    display:flex;align-items:center;justify-content:space-between;
    padding:14px 0;border-bottom:1px solid #f5f5f5;
    font-size:13.5px;font-weight:600;color:var(--text-2);
    cursor:pointer;transition:color .15s;text-decoration:none;
}
.sort-option:hover,.sort-option.chosen{color:var(--brand)}
.pb-bar{padding-bottom:calc(68px + env(safe-area-inset-bottom,0px))}

/* ── Live search dropdown ─────────────────────────────────────────────── */
.search-dropdown {
    position:absolute;top:calc(100% + 6px);left:0;right:0;
    background:#fff;border:1px solid #e5e7eb;border-radius:16px;
    box-shadow:0 16px 40px rgba(0,0,0,.12);z-index:100;
    overflow:hidden;max-height:420px;overflow-y:auto;
}
[dir="rtl"] .search-dropdown{left:auto}
.search-result-item {
    display:flex;align-items:center;gap:12px;
    padding:10px 14px;cursor:pointer;
    transition:background .12s;text-decoration:none;
    border-bottom:1px solid #f7f6f3;
}
.search-result-item:last-child{border-bottom:none}
.search-result-item:hover{background:#f9fafb}
.search-result-img {
    width:44px;height:44px;border-radius:10px;object-fit:cover;
    flex-shrink:0;background:#f3f4f6;
}
.scrollbar-hide, [x-category-grid] {
    padding-top: 12px !important;   /* نعطي مساحة داخلية للبوردر */
    margin-top: -12px !important;   /* نسحب الكومبوننت لفوق ليعوض المساحة */
    padding-bottom: 4px !important; 
    overflow-y: visible !important; /* السماح بظهور العناصر الخارجة عمودياً */
}

/* تأكد أن الحاوية الأب لا تقص المحتوى */
.max-w-screen-2xl {
    overflow: visible !important;
}
/* بنعطي مساحة "تنفّس" داخل حاوية الكاتيغوري عشان البوردر ما ينقص */
    [x-category-grid] .flex, 
    .overflow-x-auto {
        padding-top: 8px !important;
        padding-bottom: 8px !important;
        margin-top: -8px !important;
    }
</style>
@endpush

@section('content')

{{-- ── Announcement banners ─────────────────────────────────────────────────── --}}
@php
    $announcements = \App\Models\Announcement::where('is_active', true)
                         ->orderBy('sort_order')->get();
@endphp
@if($announcements->count() > 0)
<div class="announce-bar md:hidden" dir="rtl">
    <div class="announce-ticker" aria-hidden="true">
        @foreach($announcements->concat($announcements) as $item)
            <span>{{ $item->content }}</span>
            <span class="announce-dot"></span>
        @endforeach
    </div>
</div>
<div class="announce-bar hidden md:flex" dir="rtl">
    @foreach($announcements as $item)
        <span>{{ $item->content }}</span>
        @if(!$loop->last)<span class="announce-dot"></span>@endif
    @endforeach
</div>
@endif

{{-- ── Floating WhatsApp ─────────────────────────────────────────────────────── --}}
@php
    $floatingLink = \App\Models\SocialLink::where('is_active',true)
                        ->where('is_floating',true)->first();
@endphp
@if($floatingLink)
    <x-floating-button :number="$floatingLink->whatsapp_number" />
@endif

{{-- ── Sort drawer ──────────────────────────────────────────────────────────── --}}
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
        @foreach(['featured'=>['label'=>'المميزة أولاً','icon'=>'⭐'],'price_asc'=>['label'=>'السعر: من الأقل','icon'=>'↑'],'price_desc'=>['label'=>'السعر: من الأعلى','icon'=>'↓'],'newest'=>['label'=>'الأحدث أولاً','icon'=>'🆕']] as $val => $opt)
        @php $isChosen = request('sort','featured') === $val; @endphp
        <a href="{{ request()->fullUrlWithQuery(['sort' => $val]) }}" onclick="closeSortDrawer()"
           class="sort-option {{ $isChosen ? 'chosen' : '' }}">
            <span class="flex items-center gap-2"><span class="text-base">{{ $opt['icon'] }}</span>{{ $opt['label'] }}</span>
            @if($isChosen)
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--brand)">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
            @endif
        </a>
        @endforeach
    </div>
</div>

@include('partials.bottombar')

{{-- ════════════════════════════════════════════════════════════════════════════
     PAGE BODY
════════════════════════════════════════════════════════════════════════════ --}}
<div class="bg-gray-50 pb-bar md:pb-12" dir="rtl">
<div class="max-w-screen-2xl mx-auto px-3 sm:px-5 lg:px-8">

    {{-- ── Hero banners ─────────────────────────────────────────────────────── --}}
    @if(!$currentCategory)
    @php
        $banners = \App\Models\HeroBanner::where('is_active', true)->orderBy('sort_order')->get();
    @endphp
    @foreach($banners as $banner)
    <div class="hero-banner mt-4 mb-5 reveal" style="--i:{{ $loop->index }}">
        <div class="relative z-10 flex items-center gap-6 px-6 md:px-14 py-10 md:py-12">
            <div class="flex-1 text-right">
                @if($banner->badge)
                <span class="inline-block text-[10px] font-black px-3 py-1 rounded-full mb-3 tracking-widest uppercase"
                      style="background:rgba(255,255,255,.12);color:rgba(255,255,255,.85);border:1px solid rgba(255,255,255,.18)">
                    {{ $banner->badge }}
                </span>
                @endif
                <h2 class="font-display text-2xl md:text-4xl font-bold text-white leading-tight mb-3">
                    {{ $banner->title }}
                    @if($banner->subtitle)
                    <br>
                    <span class="text-transparent bg-clip-text"
                          style="background:linear-gradient(90deg,#60a5fa,#a78bfa);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">
                        {{ $banner->subtitle }}
                    </span>
                    @endif
                </h2>
                <p class="text-gray-400 text-sm mb-6 leading-relaxed max-w-sm hidden sm:block">
                    {{ $banner->description }}
                </p>
                <a href="{{ $banner->button_url ?? '#' }}"
                   class="inline-flex items-center gap-2 bg-white text-gray-900 font-black text-sm px-6 py-3 rounded-xl hover:bg-gray-50 transition-colors shadow-xl active:scale-95">
                    {{ $banner->button_text }}
                    <svg class="w-4 h-4 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
            <div class="w-32 sm:w-40 md:w-52 flex-shrink-0 relative">
                <div class="absolute inset-0 rounded-2xl opacity-30"
                     style="background:radial-gradient(circle,var(--brand-color) 0%,transparent 70%);transform:scale(1.3)"></div>
                <img src="{{ $banner->getFirstMediaUrl('banner_image') }}" alt="{{ $banner->title }}"
                     class="hero-img relative z-10 w-full h-32 sm:h-44 md:h-56 object-cover rounded-2xl">
            </div>
        </div>
    </div>
    @endforeach
    @endif

    {{-- ── Category grid ────────────────────────────────────────────────────── --}}
    @php
        $topCategories = \App\Models\Category::active()->roots()
            ->with(['allActiveChildren','media'])
            ->orderBy('sort_order')->take(20)->get();
    @endphp


    {{-- ── Toolbar: search + sort ──────────────────────────────────────────── --}}
 <div class="relative overflow-hidden pt-2"> 
        <x-category-grid :categories="$topCategories" :current="$currentCategory ?? null" :show-all="true" />
    </div>

    {{-- ── Toolbar: search + sort ──────────────────────────────────────────── --}}
    <div class="flex items-center justify-between mb-4 gap-3 mt-4">
        <div>
            @if($currentCategory)
            <h1 class="font-display text-lg md:text-2xl font-bold text-gray-900">{{ $currentCategory->name }}</h1>
            @endif
            <p class="text-xs text-gray-400 {{ $currentCategory ? 'mt-0.5' : '' }}">
                {{ $products->total() }} منتج
                @if(request('search'))
                    لـ "<span class="text-gray-700 font-medium">{{ request('search') }}</span>"
                @endif
            </p>
        </div>

        <div class="flex items-center gap-2">

            {{-- ═══════════════════════════════════════════════════════════════
                 LIVE SEARCH — Alpine.js component
                 ─────────────────────────────────────────────────────────────
                 • Debounce 280ms to limit API calls while typing
                 • Min 2 chars before fetching
                 • Keyboard: arrow keys navigate results, Enter follows link
                 • Click outside closes dropdown
                 • Spinner shows during fetch
            ═══════════════════════════════════════════════════════════════ --}}
            <div class="hidden sm:block relative"
                 x-data="liveSearch()"
                 x-init="init()"
                 @click.outside="close()"
                 @keydown.escape="close()">

                <div class="relative">
                    <input
                        type="text"
                        x-model="query"
                        @input="onInput()"
                        @keydown.arrow-down.prevent="moveDown()"
                        @keydown.arrow-up.prevent="moveUp()"
                        @keydown.enter.prevent="followActive()"
                        @focus="query.length >= 2 && open()"
                        placeholder="بحث..."
                        autocomplete="off"
                        class="pe-9 ps-3 py-2 text-xs border border-gray-200 rounded-xl
                               focus:ring-2 focus:border-transparent outline-none w-40 bg-white
                               transition-all focus:w-56"
                        style="--tw-ring-color:var(--brand-color)">

                    {{-- Search icon / spinner --}}
                    <div class="absolute inset-y-0 end-0 flex items-center pe-2.5 pointer-events-none">
                        <svg x-show="!loading" class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <svg x-show="loading" class="w-3.5 h-3.5 text-gray-400 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                        </svg>
                    </div>
                </div>

                {{-- Results dropdown --}}
                <div x-show="isOpen && (results.length > 0 || query.length >= 2)"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-1 scale-98"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="search-dropdown"
                     style="display:none">

                    {{-- No results --}}
                    <template x-if="results.length === 0 && !loading && query.length >= 2">
                        <div class="px-4 py-6 text-center">
                            <p class="text-sm text-gray-400 font-medium">لا توجد نتائج لـ "<span x-text="query" class="text-gray-700"></span>"</p>
                        </div>
                    </template>

                    {{-- Result items --}}
                    <template x-for="(item, index) in results" :key="item.id">
                        <a :href="item.url"
                           class="search-result-item"
                           :class="activeIndex === index ? 'bg-gray-50' : ''"
                           @mouseenter="activeIndex = index"
                           @click="close()">

                            {{-- Thumbnail --}}
                            <template x-if="item.image">
                                <img :src="item.image" :alt="item.name" class="search-result-img">
                            </template>
                            <template x-if="!item.image">
                                <div class="search-result-img flex items-center justify-center text-gray-300">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            </template>

                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-800 line-clamp-1" x-text="item.name"></p>
                                <p x-show="item.category" class="text-[10px] text-gray-400 font-medium mt-0.5" x-text="item.category"></p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-sm font-black tabular-nums"
                                          :style="item.is_on_sale ? 'color:var(--sale-red)' : 'color:#111827'"
                                          x-text="item.price_formatted"></span>
                                    <span x-show="item.is_on_sale"
                                          class="text-[10px] text-gray-400 line-through tabular-nums"
                                          x-text="item.original_price"></span>
                                </div>
                            </div>

                            {{-- Arrow icon --}}
                            <svg class="w-4 h-4 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </a>
                    </template>

                    {{-- "View all results" footer --}}
                    <template x-if="results.length > 0">
                        <a :href="'{{ route('products.index') }}?search=' + encodeURIComponent(query)"
                           class="flex items-center justify-center gap-2 px-4 py-3 text-xs font-bold
                                  bg-gray-50 hover:bg-gray-100 transition-colors"
                           style="color:var(--brand-color)"
                           @click="close()">
                            عرض جميع النتائج (<span x-text="results.length"></span>)
                            <svg class="w-3.5 h-3.5 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </template>
                </div>
            </div>

            {{-- Desktop sort --}}
            <div class="hidden sm:block">
                <select onchange="window.location.href=this.value"
                        class="text-xs border border-gray-200 rounded-xl px-3 py-2 bg-white cursor-pointer outline-none focus:ring-2"
                        style="--tw-ring-color:var(--brand)">
                    @foreach(['featured'=>'المميزة','price_asc'=>'السعر ↑','price_desc'=>'السعر ↓','newest'=>'الأحدث'] as $v => $l)
                    <option value="{{ request()->fullUrlWithQuery(['sort'=>$v]) }}"
                            {{ request('sort','featured') === $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Mobile sort --}}
            <button onclick="openSortDrawer()"
                    class="flex sm:hidden items-center gap-1.5 bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs font-bold text-gray-600 shadow-sm active:scale-95 transition-transform">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h18M7 8h10m-7 4h4"/>
                </svg>
                ترتيب
            </button>
        </div>
    </div>

    {{-- ── Breadcrumb ───────────────────────────────────────────────────────── --}}
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

    {{-- ══════════════════════════════════════════════════════════════════════
         DYNAMIC HOME SECTIONS
         ─────────────────────────────────────────────────────────────────
         When on the root /products page (no category filter):
         → Loop through HomeSection records from the DB, each resolves its
           own products. Admin controls title, type, limit, order.

         When filtering by category:
         → Show the regular paginated grid (no sections).
    ══════════════════════════════════════════════════════════════════════ --}}
    @if(!$currentCategory && !request('search') && !request('sort'))

    @foreach($homeSections as $section)
    @php $sectionProducts = $section->resolveProducts(); @endphp
    @if($sectionProducts->isNotEmpty())
    <section class="mb-8">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <span class="w-1 h-5 rounded-full" style="background:var(--brand)"></span>
                <h2 class="font-display text-base md:text-lg font-bold text-gray-900">{{ $section->title }}</h2>
            </div>
            <a href="{{ route('products.index', ['sort' => $section->type === 'category' ? 'featured' : $section->type]) }}"
               class="text-xs font-bold hover:underline" style="color:var(--brand)">
                عرض الكل ←
            </a>
        </div>

        <div class="featured-list">
            @foreach($sectionProducts as $sp)
            @php
                $spWishlisted = in_array($sp->id, $wishlistedIds ?? []);
            @endphp
            <div class="featured-card group"
                 onclick="window.location='{{ route('products.show', $sp->slug) }}'">
                <div class="relative overflow-hidden bg-gray-100" style="padding-top:126%">
                    <div class="shimmer absolute inset-0 z-0" id="fsk-{{ $sp->id }}"></div>
                    <img src="{{ $sp->getFirstMediaUrl('main') ?: ($sp->image_url ?? 'https://picsum.photos/seed/'.$sp->id.'/300/390') }}"
                         alt="{{ $sp->name }}"
                         class="fc-img absolute inset-0 w-full h-full object-cover z-10"
                         loading="lazy"
                         onload="this.previousElementSibling.style.display='none'">
                    @if($sp->is_on_sale)
                    <div class="fc-ribbon">{{ $sp->discount_percentage }}% OFF</div>
                    @endif
                    {{-- Share button on featured card --}}
                    <div class="absolute top-2 left-2 z-20" onclick="event.stopPropagation()">
                        <button type="button" class="share-btn"
                                onclick="shareProduct('{{ route('products.show', $sp->slug) }}', '{{ addslashes($sp->name) }}')"
                                title="مشاركة">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                            </svg>
                        </button>
                    </div>
                    <div class="absolute bottom-2 left-2 z-20" onclick="event.stopPropagation()">
                        @auth
                        <button type="button" class="heart-btn wishlist-btn"
                                data-product-id="{{ $sp->id }}"
                                data-wishlisted="{{ $spWishlisted ? 'true' : 'false' }}"
                                onclick="toggleWishlist(this)">
                            <svg data-heart="outline" class="{{ $spWishlisted ? 'hidden' : '' }}" fill="none" stroke="#888" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                            <svg data-heart="filled" class="{{ $spWishlisted ? '' : 'hidden' }}" fill="#ff3366" stroke="#ff3366" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </button>
                        @endauth
                    </div>
                </div>
                <div class="px-2.5 pt-2 pb-3">
                    <p class="text-xs font-semibold text-gray-800 line-clamp-2 leading-snug mb-1">{{ $sp->name }}</p>
                    <div class="flex items-center gap-1.5">
                        @if($sp->is_on_sale)
                        <x-price :amount="$sp->discount_price" class="text-sm font-black leading-none tabular-nums" style="color:var(--sale-red)" />
                        <x-price :amount="$sp->base_price" class="text-[10px] text-gray-400 line-through tabular-nums" />
                        @else
                        <x-price :amount="$sp->base_price" class="text-sm font-black text-gray-900 leading-none tabular-nums" />
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif
    @endforeach

    <div class="flex items-center gap-3 mb-5">
        <div class="h-px bg-gray-200 flex-1"></div>
        <span class="text-xs font-bold text-gray-500 flex-shrink-0">جميع المنتجات</span>
        <div class="h-px bg-gray-200 flex-1"></div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════
         REGULAR PRODUCT GRID (filtered / search / sort view)
    ══════════════════════════════════════════════════════════════════════ --}}
    @if($products->isEmpty())
    <div class="flex flex-col items-center justify-center py-24 text-center">
        <div class="w-16 h-16 bg-white rounded-2xl border border-gray-100 flex items-center justify-center mb-4 shadow-sm">
            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <p class="text-gray-500 font-semibold text-sm">لا توجد منتجات</p>
        <a href="{{ route('products.index') }}" class="mt-3 text-xs font-bold hover:underline" style="color:var(--brand)">عرض الكل</a>
    </div>
    @else
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-2 sm:gap-3">
        @foreach($products as $i => $product)
        @php $isWishlisted = in_array($product->id, $wishlistedIds ?? []); @endphp
        <div class="pcard flex flex-col group reveal"
             style="--i: {{ min($i % 6, 5) }}"
             onclick="window.location='{{ route('products.show', $product->slug) }}'">

            <div class="relative overflow-hidden bg-gray-100" style="padding-top:130%">
                <div class="shimmer absolute inset-0 z-0" id="sk-{{ $product->id }}"></div>
              {{-- CORRECT: matches the collection name used in the controller --}}
<<img src="{{ $product->getFirstMediaUrl('products') ?: asset('images/placeholder.jpg') }}"
                     alt="{{ $product->name }}"
                     class="pcard-img absolute inset-0 w-full h-full object-cover z-10"
                     loading="lazy"
                     onload="document.getElementById('sk-{{ $product->id }}').style.display='none'">

                @if($product->is_on_sale)
                <div class="ribbon">{{ $product->discount_percentage }}% OFF</div>
                @endif

                @if($product->is_featured)
                <span class="absolute top-0 right-0 z-10 text-amber-900 text-[9px] font-black px-2 py-0.5"
                      style="background:var(--gold);border-bottom-left-radius:9px">مميز ⭐</span>
                @endif

                {{-- Wishlist + Share buttons --}}
                <div class="absolute bottom-2 left-2 z-20 flex gap-1.5" onclick="event.stopPropagation()">
                    @auth
                    <button type="button" class="heart-btn wishlist-btn"
                            data-product-id="{{ $product->id }}"
                            data-wishlisted="{{ $isWishlisted ? 'true' : 'false' }}"
                            onclick="toggleWishlist(this)"
                            aria-label="{{ $isWishlisted ? 'إزالة من المفضلة' : 'إضافة للمفضلة' }}">
                        <svg data-heart="outline" class="{{ $isWishlisted ? 'hidden' : '' }}" fill="none" stroke="#999" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        <svg data-heart="filled" class="{{ $isWishlisted ? '' : 'hidden' }}" fill="#ff3366" stroke="#ff3366" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </button>
                    @endauth

                    {{-- ── SHARE BUTTON ── --}}
                    <button type="button" class="share-btn"
                            onclick="shareProduct('{{ route('products.show', $product->slug) }}', '{{ addslashes($product->name) }}')"
                            title="مشاركة">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                        </svg>
                    </button>
                </div>

                @if(!$product->in_stock)
                <div class="absolute inset-0 bg-white/55 z-10 flex items-end justify-center pb-3">
                    <span class="bg-white/95 text-gray-500 text-[10px] font-bold px-3 py-1 rounded-full border border-gray-100 shadow-sm">نفد المخزون</span>
                </div>
                @endif
            </div>

            <div class="px-2 pt-2 pb-2.5 flex flex-col gap-1">
                @if($product->categories->first())
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide leading-none truncate">
                    {{ $product->categories->first()->name }}
                </p>
                @endif
                <p class="text-xs font-semibold text-gray-800 line-clamp-2 leading-snug">
                    {{ $product->name }}
                </p>
                <div class="flex flex-col">
                    @if($product->discount_price && $product->discount_price < $product->base_price)
                    <span class="text-xs text-red-500 font-bold bg-red-50 px-1.5 py-0.5 rounded w-fit mb-1">تخفيض</span>
                    <div class="flex items-center gap-2">
                        <x-price :amount="$product->discount_price" class="font-bold text-gray-900 text-sm" />
                        <x-price :amount="$product->base_price" class="text-xs text-gray-400 line-through" />
                    </div>
                    @else
                    <x-price :amount="$product->base_price" class="font-bold text-gray-900 text-sm" />
                    @endif
                </div>
                @if($product->variants->where('is_active',true)->count() > 1)
                <p class="text-[10px] text-gray-400 leading-none">
                    {{ $product->variants->where('is_active',true)->count() }} خيارات
                </p>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    @if($products->hasPages())
    <div class="mt-10 flex justify-center">{{ $products->links() }}</div>
    @endif
    @endif

</div>
</div>

@endsection

@push('scripts')
<script>
/* ── Sort drawer ────────────────────────────────────────────────────── */
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

/* ── Scroll-reveal ──────────────────────────────────────────────────── */
(function () {
    var targets = document.querySelectorAll('.reveal');
    if (!targets.length) return;
    var io = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                io.unobserve(entry.target);
            }
        });
    }, { threshold: 0.08, rootMargin: '0px 0px -30px 0px' });
    targets.forEach(function(el) { io.observe(el); });
})();

/* ── Stop card navigation on inner button clicks ────────────────────── */
document.querySelectorAll('.pcard button, .featured-card button').forEach(function(btn) {
    btn.addEventListener('click', function(e) { e.stopPropagation(); });
});

/* ════════════════════════════════════════════════════════════════════════
   WEB SHARE API — shareProduct()
   ─────────────────────────────────────────────────────────────────────
   1. Tries navigator.share() — native OS share sheet (mobile + modern desktop)
   2. Falls back to navigator.clipboard.writeText() — copies URL to clipboard
   3. Last resort: window.prompt() — user can manually copy
════════════════════════════════════════════════════════════════════════ */
function shareProduct(url, title) {
    var shareData = {
        title: title,
        text:  title + ' — تسوق الآن',
        url:   url,
    };

    if (navigator.share) {
        /* Native share sheet — Android, iOS Safari, Edge, Chrome desktop */
        navigator.share(shareData).catch(function(err) {
            /* User cancelled — not an error worth logging */
            if (err.name !== 'AbortError') {
                console.warn('Share failed:', err);
            }
        });
        return;
    }

    /* Fallback 1: Modern clipboard API */
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(url).then(function() {
            showShareToast('تم نسخ الرابط ✓');
        }).catch(function() {
            /* Clipboard denied — use prompt */
            showSharePrompt(url);
        });
        return;
    }

    /* Fallback 2: Legacy prompt */
    showSharePrompt(url);
}

function showShareToast(message) {
    /* Reuse the global Cart toast if available */
    if (typeof Cart !== 'undefined' && Cart.toast) {
        Cart.toast(message, 'success');
        return;
    }
    /* Otherwise create a quick inline toast */
    var toast = document.createElement('div');
    toast.textContent = message;
    toast.style.cssText = [
        'position:fixed','top:20px','right:20px','z-index:9999',
        'background:#111827','color:#fff','font-size:13px','font-weight:600',
        'padding:10px 18px','border-radius:12px','box-shadow:0 8px 30px rgba(0,0,0,.2)',
        'transition:opacity .3s','pointer-events:none'
    ].join(';');
    document.body.appendChild(toast);
    setTimeout(function() { toast.style.opacity = '0'; }, 2000);
    setTimeout(function() { document.body.removeChild(toast); }, 2400);
}

function showSharePrompt(url) {
    window.prompt('انسخ رابط المنتج:', url);
}

/* ════════════════════════════════════════════════════════════════════════
   ALPINE.JS — liveSearch() component
   ─────────────────────────────────────────────────────────────────────
   Registered as a global Alpine component via Alpine.data().
   The init() call happens via x-init on the parent element.
════════════════════════════════════════════════════════════════════════ */
document.addEventListener('alpine:init', function () {
    Alpine.data('liveSearch', function () {
        return {
            query:       '',
            results:     [],
            isOpen:      false,
            loading:     false,
            activeIndex: -1,
            timer:       null,

            init() {
                /* Nothing extra needed — x-model handles reactivity */
            },

            onInput() {
                clearTimeout(this.timer);
                this.activeIndex = -1;

                if (this.query.length < 2) {
                    this.results = [];
                    this.isOpen  = false;
                    return;
                }

                /* Debounce: wait 280ms after the user stops typing */
                this.timer = setTimeout(() => { this.fetch(); }, 280);
            },

            async fetch() {
                this.loading = true;
                this.isOpen  = true;

                try {
                    var url = '/api/search?q=' + encodeURIComponent(this.query);
                    var res  = await window.fetch(url, {
                        headers: { 'Accept': 'application/json' },
                    });
                    var data = await res.json();
                    this.results = data.results || [];
                } catch (e) {
                    console.warn('Live search error:', e);
                    this.results = [];
                } finally {
                    this.loading = false;
                }
            },

            open()  { this.isOpen = true; },
            close() { this.isOpen = false; this.activeIndex = -1; },

            moveDown() {
                if (this.activeIndex < this.results.length - 1) this.activeIndex++;
            },
            moveUp() {
                if (this.activeIndex > 0) this.activeIndex--;
            },
            followActive() {
                if (this.activeIndex >= 0 && this.results[this.activeIndex]) {
                    window.location.href = this.results[this.activeIndex].url;
                } else if (this.query.length >= 2) {
                    window.location.href = '{{ route('products.index') }}?search=' + encodeURIComponent(this.query);
                }
            },
        };
    });
});
</script>
@endpush