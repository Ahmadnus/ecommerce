@extends('layouts.app')

@php
    $isRtl  = app()->getLocale() === 'ar';
    $locale = app()->getLocale();
@endphp

@section('title', $currentCategory ? $currentCategory->name . ($isRtl ? ' — المتجر' : ' — Shop') : __('app.all_products'))

@push('head')
<style>
/* ═══════════════════════════════════════════════════════════════════
   LOCAL CSS VARIABLES
   ═══════════════════════════════════════════════════════════════════ */
:root {
    --brand:       var(--brand-color, #0ea5e9);
    --brand-dark:  color-mix(in srgb, var(--brand) 75%, #000);
    --brand-light: color-mix(in srgb, var(--brand) 12%, #fff);
    --surface:     var(--nav-bg-color, #ffffff);
    --surface-2:   var(--bg-color, #f8f8f8);
    --sale-red:    #ff3366;
    --border:      #efefef;
    --radius-card: 16px;

    --ui-text:        var(--text-body);
    --ui-text-strong: var(--text-heading);
    --ui-text-soft:   var(--text-muted);

    --card-bg:           var(--card-bg, #ffffff);
    --card-font-color:   var(--text-card);
    --card-font-strong:  var(--text-heading);
    --card-font-muted:   var(--text-muted);
    --card-price-color:  var(--text-price);
    --card-sale-color:   var(--text-price);

    --text-1: var(--text-heading);
    --text-2: var(--text-body);
}

/* ── Font application ───────────────────────────────────────────── */
html[lang="ar"], [dir="rtl"] { font-family: var(--font-ar) !important; }
html[lang="en"], [dir="ltr"] { font-family: var(--font-en) !important; }
.page-shop,
.page-shop * { font-family: var(--app-font) !important; }

/* ── Page-level text ────────────────────────────────────────────── */
.page-shop { color: var(--text-body); }

/* ── Gray utility overrides ─────────────────────────────────────── */
.page-shop :is(.text-gray-900, .text-gray-800, .text-slate-900, .text-slate-800, .text-black) {
    color: var(--text-heading) !important;
}
.page-shop :is(.text-gray-700, .text-gray-600, .text-slate-700, .text-slate-600) {
    color: var(--text-body) !important;
}
.page-shop :is(.text-gray-500, .text-gray-400, .text-gray-300, .text-slate-500, .text-slate-400) {
    color: var(--text-muted) !important;
}

/* ── Cards ──────────────────────────────────────────────────────── */
.page-shop :is(.pcard, .featured-card) { color: var(--text-card); }
.page-shop :is(.pcard, .featured-card) :is(.text-gray-900, .text-gray-800, .text-slate-900, .text-slate-800, .text-black) {
    color: var(--text-product-title) !important;
}
.page-shop :is(.pcard, .featured-card) :is(.text-gray-500, .text-gray-400, .text-gray-300, .text-slate-500, .text-slate-400) {
    color: var(--text-muted) !important;
}

/* ── PRICE — highest specificity, applied last ──────────────────── */
/* All elements with class "price-val" render the actual price amount */
.page-shop .price-val {
    color: var(--text-price) !important;
    font-size: var(--product-price-font-size) !important;
}
/* Strikethrough original prices are always muted */
.page-shop .price-original {
    color: var(--text-muted) !important;
    text-decoration: line-through;
}

/* ── Search dropdown + sort drawer ──────────────────────────────── */
.page-shop :is(.search-dropdown, .sort-drawer) { color: var(--text-body); }
.page-shop :is(.search-dropdown, .sort-drawer) :is(.text-gray-900, .text-gray-800) {
    color: var(--text-heading) !important;
}
.page-shop :is(.search-dropdown, .sort-drawer) :is(.text-gray-500, .text-gray-400, .text-gray-300) {
    color: var(--text-muted) !important;
}

/* ── Announcement bar ───────────────────────────────────────────── */
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
.announce-bar::before{left:-30px} .announce-bar::after{right:-30px}
.announce-ticker {
    display:flex; align-items:center; gap:28px;
    animation:ticker 18s linear infinite; white-space:nowrap;
}
@media(min-width:768px){.announce-ticker{animation:none;gap:40px}}
@keyframes ticker{0%{transform:translateX(0)}100%{transform:translateX(-50%)}}
.announce-dot { width:5px;height:5px;border-radius:50%;background:rgba(255,255,255,.55);flex-shrink:0; }

/* ── Hero banner ────────────────────────────────────────────────── */
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

/* ── Category pills ─────────────────────────────────────────────── */
.cat-pill {
    white-space:nowrap; padding:7px 17px; border-radius:99px;
    font-size:12px; font-weight:700; border:1.5px solid var(--border);
    background:var(--surface); color:var(--text-body); cursor:pointer;
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

/* ── Featured list ──────────────────────────────────────────────── */
.featured-list {
    display:grid; grid-template-columns:repeat(2, 1fr);
    column-gap:32px; row-gap:40px; margin-top:30px; padding:0 10px;
}
@media (min-width:768px) {
    .featured-list { grid-template-columns:repeat(4, 1fr); column-gap:24px; }
}
@media (min-width:1024px) {
    .featured-list { grid-template-columns:repeat(6, 1fr); column-gap:20px; }
}
@media (min-width:1280px) {
    .featured-list { grid-template-columns:repeat(8, 1fr); column-gap:24px; }
}
.featured-card {
    cursor:pointer; position:relative;
}
.fc-ribbon{
    position:absolute;top:0;right:0;background:var(--sale-red);
    color:#fff;font-size:9px;font-weight:800;padding:3px 9px 3px 7px;
    border-bottom-left-radius:9px;z-index:5;letter-spacing:.04em;
}

/* ── Product card (grid) ────────────────────────────────────────── */
.pcard {
    cursor:pointer; position:relative;
}
.ribbon{
    position:absolute;top:0;left:0;background:var(--sale-red);
    color:#fff;font-size:9px;font-weight:800;padding:2px 8px 2px 5px;
    border-bottom-right-radius:8px;letter-spacing:.04em;line-height:1.7;z-index:5;
}

/* ── Shimmer ────────────────────────────────────────────────────── */
@keyframes shimmer{0%{background-position:-900px 0}100%{background-position:900px 0}}
.shimmer{
    background:linear-gradient(90deg,#f4f4f4 25%,#ececec 50%,#f4f4f4 75%);
    background-size:1800px 100%;animation:shimmer 1.8s ease-in-out infinite;
}

/* ── Heart / wishlist ───────────────────────────────────────────── */
.heart-btn {
    width:30px;height:30px;background:rgba(255,255,255,.93);
    border-radius:50%;display:flex;align-items:center;justify-content:center;
    backdrop-filter:blur(6px);-webkit-backdrop-filter:blur(6px);
    box-shadow:0 1px 7px rgba(0,0,0,.13);border:none;cursor:pointer;flex-shrink:0;
    transition:transform .15s, background .15s;
}
.heart-btn:hover{transform:scale(1.18);background:#fff}
.heart-btn svg{width:15px;height:15px}

/* ── Share button ───────────────────────────────────────────────── */
.share-btn {
    width:30px;height:30px;background:rgba(255,255,255,.93);
    border-radius:50%;display:flex;align-items:center;justify-content:center;
    backdrop-filter:blur(6px);-webkit-backdrop-filter:blur(6px);
    box-shadow:0 1px 7px rgba(0,0,0,.13);border:none;cursor:pointer;flex-shrink:0;
    transition:transform .15s, background .15s;
}
.share-btn:hover{transform:scale(1.18);background:#fff}
.share-btn svg{width:14px;height:14px}

/* ── Scroll reveal ──────────────────────────────────────────────── */
.reveal{
    opacity:0;transform:translateY(22px);
    transition:opacity .55s cubic-bezier(.22,1,.36,1),transform .55s cubic-bezier(.22,1,.36,1);
    will-change:opacity,transform;
}
.reveal.visible{opacity:1;transform:translateY(0)}
.reveal{transition-delay:calc(var(--i,0) * 60ms)}

/* ── Sort drawer ────────────────────────────────────────────────── */
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
    font-size:13.5px;font-weight:600;color:var(--text-body);
    cursor:pointer;transition:color .15s;text-decoration:none;
}
.sort-option:hover,.sort-option.chosen{color:var(--brand)}
.pb-bar{padding-bottom:calc(68px + env(safe-area-inset-bottom,0px))}

/* ── Live search dropdown ───────────────────────────────────────── */
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
    padding-top: 12px !important;
    margin-top: -12px !important;
    padding-bottom: 4px !important;
    overflow-y: visible !important;
}
.max-w-screen-2xl { overflow: visible !important; }
[x-category-grid] .flex,
.overflow-x-auto {
    padding-top: 8px !important;
    padding-bottom: 8px !important;
    margin-top: -8px !important;
}

/* ── Homepage divider / intro blocks (tall-media redesign) ───────── */
.home-block { text-align: center; max-width: 640px; margin: 0 auto; padding: 40px 16px; }
/* Higher specificity than plain Tailwind .text-left/.text-center/.text-right
   utilities so admin-chosen alignment reliably wins regardless of CSS load order. */
.home-block.text-left   { text-align: left; }
.home-block.text-center { text-align: center; }
.home-block.text-right  { text-align: right; }
.home-block h1 {
    font-family: var(--font-display, inherit);
    font-size: clamp(1.6rem, 4vw, 2.75rem);
    font-weight: 800; line-height: 1.15; letter-spacing: -.01em;
    color: var(--text-heading, #111827); margin-bottom: 14px;
}
.home-block p {
    font-size: 14.5px; line-height: 1.7; color: var(--text-muted, #6b7280);
    margin-bottom: 26px;
}
.home-cta-btn {
    background: var(--brand); color: #fff;
    border-radius: 0 !important; /* sharp corners, per client spec */
    box-shadow: 0 10px 28px color-mix(in srgb, var(--brand) 35%, transparent);
    transition: transform .18s ease, box-shadow .18s ease, opacity .18s ease;
    text-decoration: none;
}
.home-cta-btn:hover { transform: translateY(-2px); opacity: .93; box-shadow: 0 14px 34px color-mix(in srgb, var(--brand) 45%, transparent); }

/* ── Category banner ────────────────────────────────────────────── */
.cat-banner { border-radius: 20px; overflow: hidden; position: relative; margin-bottom: 20px; }
.cat-banner-inner { display: flex; align-items: center; gap: 24px; padding: 36px 28px; position: relative; z-index: 10; }
@media(min-width: 768px) { .cat-banner-inner { padding: 48px 56px; gap: 40px; } }
.cat-banner-img {
    width: 120px; height: 120px; flex-shrink: 0;
    border-radius: 16px; object-fit: cover;
    box-shadow: 0 12px 32px rgba(0,0,0,.25);
    animation: heroFloat 6s ease-in-out infinite;
}
@media(min-width: 640px) { .cat-banner-img { width: 160px; height: 160px; } }
@media(min-width: 768px) { .cat-banner-img { width: 200px; height: 200px; } }
.cat-banner-img-wrap {
    width: 100%; max-height: 320px; overflow: hidden;
    border-radius: 20px; margin-bottom: 20px; background: #f3f4f6;
}
.cat-banner-img-wrap img { width: 100%; height: 100%; max-height: 320px; object-fit: cover; display: block; }
@media (max-width: 640px) {
    .cat-banner-img-wrap { max-height: 160px; border-radius: 14px; }
    .cat-banner-img-wrap img { max-height: 160px; }
}
</style>
@endpush

@section('content')
<div class="page-shop">

@include('partials.sections.top-hero-media', ['position' => 'top', 'pullUnderNavbar' => true])

{{-- ── Announcement banners ──────────────────────────────────────────── --}}
@php
    $announcements = \App\Models\Announcement::where('is_active', true)
                         ->orderBy('sort_order')->get();
@endphp
@if($announcements->count() > 0)
<div class="announce-bar md:hidden" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
    <div class="announce-ticker" aria-hidden="true">
        @foreach($announcements->concat($announcements) as $item)
            <span>{{ $item->content }}</span>
            <span class="announce-dot"></span>
        @endforeach
    </div>
</div>
<div class="announce-bar hidden md:flex" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
    @foreach($announcements as $item)
        <span>{{ $item->content }}</span>
        @if(!$loop->last)<span class="announce-dot"></span>@endif
    @endforeach
</div>
@endif

{{-- ── Floating WhatsApp ─────────────────────────────────────────────── --}}
@php
    $floatingLink = \App\Models\SocialLink::where('is_active',true)
                        ->where('is_floating',true)->first();
@endphp
@if($floatingLink)
    <x-floating-button :number="$floatingLink->whatsapp_number" />
@endif

{{-- ── Sort drawer ───────────────────────────────────────────────────── --}}
<div class="sort-drawer-overlay" id="sort-overlay" onclick="closeSortDrawer()">
    <div class="sort-drawer" id="sort-drawer" dir="{{ $isRtl ? 'rtl' : 'ltr' }}" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between mb-2">
            <p class="font-bold text-gray-900">{{ __('app.sort_by') }}</p>
            <button onclick="closeSortDrawer()" class="p-1.5 text-gray-400 hover:text-gray-700 rounded-lg hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        @php
        $sortOptions = [
            'featured'   => ['label' => __('app.sort_featured'),   'icon' => '⭐'],
            'price_asc'  => ['label' => __('app.sort_price_asc'),  'icon' => '↑'],
            'price_desc' => ['label' => __('app.sort_price_desc'), 'icon' => '↓'],
            'newest'     => ['label' => __('app.sort_newest'),     'icon' => '🆕'],
        ];
        @endphp

        @foreach($sortOptions as $val => $opt)
        @php $isChosen = request('sort', 'featured') === $val; @endphp
        <a href="{{ request()->fullUrlWithQuery(['sort' => $val]) }}" onclick="closeSortDrawer()"
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

@include('partials.bottombar')

{{-- ════════════════════════════════════════════════════════════════════
     PAGE BODY
════════════════════════════════════════════════════════════════════════ --}}
<div class="bg-gray-50 pb-bar md:pb-12" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<div class="max-w-screen-2xl mx-auto px-3 sm:px-5 lg:px-8">

    @if($currentCategory || request('search') || request('sort'))
    {{-- ══════════════════════════════════════════════════════════════
         CATEGORY / SEARCH / SORT VIEW — unchanged toolbar-first layout
    ══════════════════════════════════════════════════════════════════ --}}

    {{-- ── Toolbar ──────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between mb-4 gap-3 mt-4">
        <div>
            @if($currentCategory && !$currentCategory->shouldShowBanner())
                <h1 class="font-display text-lg md:text-2xl font-bold text-gray-900">{{ $currentCategory->name }}</h1>
            @endif
            <p class="text-xs text-gray-400 {{ $currentCategory ? 'mt-0.5' : '' }}">
                {{ __('app.products_count', ['count' => $products->total()]) }}
                @if(request('search')){{ __('app.search_for', ['term' => request('search')]) }}@endif
            </p>
        </div>

        <div class="flex items-center gap-2">

            {{-- Live search --}}
            <div class="hidden sm:block relative"
                 x-data="liveSearch()" x-init="init()"
                 @click.outside="close()" @keydown.escape="close()">
                <div class="relative">
                    <input type="text" x-model="query"
                           @input="onInput()"
                           @keydown.arrow-down.prevent="moveDown()"
                           @keydown.arrow-up.prevent="moveUp()"
                           @keydown.enter.prevent="followActive()"
                           @focus="query.length >= 2 && open()"
                           placeholder="{{ __('app.search_placeholder_short') }}"
                           autocomplete="off"
                           class="pe-9 ps-3 py-2 text-xs border border-gray-200 rounded-xl
                                  focus:ring-2 focus:border-transparent outline-none w-40 bg-white
                                  transition-all focus:w-56 {{ $isRtl ? 'text-right' : 'text-left' }}"
                           style="--tw-ring-color:var(--brand-color)">
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

                <div x-show="isOpen && (results.length > 0 || query.length >= 2)"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="search-dropdown" style="display:none">

                    <template x-if="results.length === 0 && !loading && query.length >= 2">
                        <div class="px-4 py-6 text-center">
                            <p class="text-sm text-gray-400 font-medium"
                               x-text="'{{ addslashes(__('app.no_results', ['term' => ''])) }}'.replace(':term', query)"></p>
                        </div>
                    </template>

                    <template x-for="(item, index) in results" :key="item.id">
                        <a :href="item.url" class="search-result-item"
                           :class="activeIndex === index ? 'bg-gray-50' : ''"
                           @mouseenter="activeIndex = index" @click="close()">
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
                            <div class="flex-1 min-w-0 {{ $isRtl ? 'text-right' : 'text-left' }}">
                                <p class="text-sm font-semibold text-gray-800 line-clamp-1" x-text="item.name"></p>
                                <p x-show="item.category" class="text-[10px] text-gray-400 font-medium mt-0.5" x-text="item.category"></p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-sm font-black tabular-nums price-val" x-text="item.price_formatted"></span>
                                    <span x-show="item.is_on_sale" class="text-[10px] tabular-nums price-original" x-text="item.original_price"></span>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-300 flex-shrink-0 {{ $isRtl ? '' : 'rotate-180' }}"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </a>
                    </template>

                    <template x-if="results.length > 0">
                        <a :href="'{{ route('products.index') }}?search=' + encodeURIComponent(query)"
                           class="flex items-center justify-center gap-2 px-4 py-3 text-xs font-bold
                                  bg-gray-50 hover:bg-gray-100 transition-colors"
                           style="color:var(--brand-color)" @click="close()">
                            {{ __('app.view_all_results') }} (<span x-text="results.length"></span>)
                            <svg class="w-3.5 h-3.5 {{ $isRtl ? 'rotate-180' : '' }}"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    @php
                    $shortSorts = [
                        'featured'   => __('app.sort_featured_short'),
                        'price_asc'  => __('app.sort_price_asc_short'),
                        'price_desc' => __('app.sort_price_desc_short'),
                        'newest'     => __('app.sort_newest_short'),
                    ];
                    @endphp
                    @foreach($shortSorts as $v => $l)
                    <option value="{{ request()->fullUrlWithQuery(['sort' => $v]) }}"
                            {{ request('sort','featured') === $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Mobile sort --}}
            <button onclick="openSortDrawer()"
                    class="flex sm:hidden items-center gap-1.5 bg-white border border-gray-200 rounded-xl
                           px-3 py-2 text-xs font-bold text-gray-600 shadow-sm active:scale-95 transition-transform">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h18M7 8h10m-7 4h4"/>
                </svg>
                {{ __('app.sort_btn') }}
            </button>
        </div>
    </div>

    {{-- ── Category banner image ────────────────────────────────────── --}}
    @if($currentCategory && $currentCategory->shouldShowBanner())
    <div class="cat-banner-img-wrap reveal">
        <img src="{{ $currentCategory->getBannerImageUrl() }}" alt="{{ $currentCategory->name }}" loading="eager">
    </div>
    @endif

    {{-- ── Breadcrumb ───────────────────────────────────────────────── --}}
    @if($currentCategory)
    <nav class="flex items-center gap-1 text-xs text-gray-400 mb-5 flex-wrap">
        <a href="{{ route('products.index') }}" class="hover:text-gray-700 transition-colors">
            {{ __('app.store_breadcrumb') }}
        </a>
        @foreach($currentCategory->getAncestors() as $ancestor)
            <span class="text-gray-300">/</span>
            <a href="{{ route('products.index', ['category' => $ancestor->slug]) }}"
               class="hover:text-gray-700 transition-colors">{{ $ancestor->name }}</a>
        @endforeach
        <span class="text-gray-300">/</span>
        <span class="text-gray-900 font-semibold">{{ $currentCategory->name }}</span>
    </nav>
    @endif

    @else
    {{-- ══════════════════════════════════════════════════════════════
         HOMEPAGE — STRICT 9-SECTION SEQUENTIAL LAYOUT
    ══════════════════════════════════════════════════════════════════ --}}
    @php
        $banners = \App\Models\HeroBanner::where('is_active', true)->orderBy('sort_order')->get();

        // ── Admin-managed dynamic content blocks (title/paragraph/CTA and/or
        //    tall portrait image/video) — fully database-driven via the
        //    "homepage-sections" admin screen. Multiple active sections can
        //    share the same `position`; they are grouped (not keyBy'd, which
        //    would silently overwrite all but the last one) and rendered
        //    sequentially in `sort_order` within their slot.
        $sections = \App\Models\HomepageSection::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->groupBy('position');
    @endphp

    {{-- ── LOCATION 1 (Top of Page): position = top_hero ─────────────────
         Full-bleed breakout: the parent container below applies
         max-w-screen-2xl + px padding, so this wrapper cancels both with
         negative margins + max-w-none, letting the hero media touch the
         exact left/right edges of the viewport. ──────────────────────── --}}
    <div class="w-screen max-w-none -mx-3 sm:-mx-5 lg:-mx-8 p-0 overflow-hidden">
        @foreach($sections->get('top_hero', collect()) as $dynSection)
            <x-homepage-section-block :section="$dynSection" :is-rtl="$isRtl" />
        @endforeach
    </div>

    {{-- ── SECTION 3: Categories ───────────────────────────────────── --}}
    @php
        $topCategories = \App\Models\Category::active()->roots()
            ->with(['allActiveChildren','media'])
            ->orderBy('sort_order')->take(20)->get();
    @endphp
    <div class="relative overflow-hidden pt-2">
        <x-category-grid :categories="$topCategories" :current="$currentCategory ?? null" :show-all="true" />
    </div>

    {{-- ── LOCATION 2 (Middle of Page): position = below_categories ──────
         Same full-bleed breakout as top_hero above. ───────────────────── --}}
    <div class="w-screen max-w-none -mx-3 sm:-mx-5 lg:-mx-8 p-0 overflow-hidden">
        @foreach($sections->get('below_categories', collect()) as $dynSection)
            <x-homepage-section-block :section="$dynSection" :is-rtl="$isRtl" />
        @endforeach
    </div>

    @foreach($banners as $banner)
        @if($banner->position === 'top')
        @php
            $layout      = $banner->layout ?? 'text_image';
            $image       = $banner->getFirstMediaUrl('banner_image');
            $hasImage    = !empty($image);
            $isImageOnly = $layout === 'image_only';
            $hasText     = in_array($layout, ['text_image', 'text_only']);
        @endphp
        <div class="hero-banner mt-4 mb-5 reveal relative overflow-hidden"
             style="--i:{{ $loop->index }}; background: {{ $banner->background_color ?? '#0ea5e9' }} !important;">
            @if($isImageOnly && $hasImage)
                <img src="{{ $image }}" class="w-full h-40 sm:h-52 md:h-64 object-cover rounded-2xl">
            @else
                <div class="relative z-10 flex items-center gap-6 px-6 md:px-14 py-10 md:py-12
                            {{ $hasImage ? '' : 'justify-center text-center' }}">
                    @if($hasText)
                    <div class="{{ $hasImage ? 'flex-1 ' . ($isRtl ? 'text-right' : 'text-left') : 'max-w-xl mx-auto text-center' }}">
                        @if($banner->badge)
                        <span class="inline-block text-[10px] font-black px-3 py-1 rounded-full mb-3 tracking-widest uppercase"
                              style="background:rgba(255,255,255,.12);color:{{ $banner->text_color ?? '#fff' }};">
                            {{ $banner->badge }}
                        </span>
                        @endif
                        <h2 class="font-display text-2xl md:text-4xl font-bold leading-tight mb-3"
                            style="color: {{ $banner->text_color ?? '#fff' }};">
                            {{ $banner->title }}
                            @if($banner->subtitle)<br><span>{{ $banner->subtitle }}</span>@endif
                        </h2>
                        <p class="text-sm mb-6 leading-relaxed {{ $hasImage ? 'max-w-sm' : 'max-w-md mx-auto' }}"
                           style="color: {{ $banner->text_color ?? '#ffffffcc' }};">
                            {{ $banner->description }}
                        </p>
                        <a href="{{ $banner->button_url ?? '#' }}"
                           class="inline-flex items-center gap-2 font-black text-sm px-6 py-3 rounded-xl shadow-xl"
                           style="background: {{ $banner->text_color ?? '#fff' }}; color: {{ $banner->background_color ?? '#000' }};">
                            {{ $banner->button_text }}
                        </a>
                    </div>
                    @endif
                    @if($hasImage && !$isImageOnly)
                    <div class="w-32 sm:w-40 md:w-52 flex-shrink-0">
                        <img src="{{ $image }}" class="w-full h-32 sm:h-44 md:h-56 object-cover rounded-2xl">
                    </div>
                    @endif
                </div>
            @endif
        </div>
        @endif
    @endforeach

    {{-- ── SECTION 5: First products block ─────────────────────────── --}}
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
                {{ __('app.view_all_arrow') }}
            </a>
        </div>

        <div class="featured-list">
            @foreach($sectionProducts as $sp)
            @php $spWishlisted = in_array($sp->id, $wishlistedIds ?? []); @endphp
            <div class="featured-card flex flex-col w-full group" onclick="window.location='{{ route('products.show', $sp->slug) }}'">
                <div class="relative overflow-hidden aspect-square rounded-[2px]">
                    <div class="shimmer absolute inset-0 z-0" id="fsk-{{ $sp->id }}"></div>
                    <img src="{{ $sp->getFirstMediaUrl('main') ?: ($sp->image_url ?? 'https://picsum.photos/seed/'.$sp->id.'/300/390') }}"
                         alt="{{ $sp->name }}"
                         class="fc-img absolute inset-0 w-full h-full object-cover z-10
                                transition-opacity duration-300 group-hover:opacity-80"
                         loading="lazy"
                         onload="this.previousElementSibling.style.display='none'">

                    <button type="button" class="favorite-btn absolute top-3 {{ $isRtl ? 'left-3' : 'right-3' }} z-20"
                            data-product-id="{{ $sp->id }}"
                            data-wishlisted="{{ $spWishlisted ? 'true' : 'false' }}"
                            onclick="event.stopPropagation(); toggleWishlist(this)"
                            aria-label="{{ $spWishlisted ? __('app.remove_from_wishlist') : __('app.add_to_wishlist') }}">
                        <svg data-heart="outline"
                             class="w-5 h-5 text-white drop-shadow-sm transition-all duration-200 {{ $spWishlisted ? 'hidden' : 'block' }}"
                             fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        <svg data-heart="filled"
                             class="w-5 h-5 text-red-500 drop-shadow-sm transition-all duration-200 {{ $spWishlisted ? 'block' : 'hidden' }}"
                             fill="currentColor" viewBox="0 0 24 24">
                            <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </button>
                </div>
                {{-- Featured card info --}}
                <div class="pt-1.5 flex flex-col gap-0.5 text-start">
                    <p class="text-xs font-medium line-clamp-2 leading-snug"
                       style="color: var(--text-product-title);">
                        {{ $sp->name }}
                    </p>
                    <div class="flex items-center gap-2">
                        @if($sp->is_on_sale)
                            <x-price :amount="$sp->discount_price"
                                     class="price-val text-xs tracking-wide" />
                            <x-price :amount="$sp->base_price"
                                     class="price-original text-xs tracking-wide" />
                        @else
                            <x-price :amount="$sp->base_price"
                                     class="price-val text-xs tracking-wide" />
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif
    @endforeach

    {{-- After-featured banners --}}
    @foreach($banners as $banner)
        @if($banner->position === 'after_featured')
        @php $image = $banner->getFirstMediaUrl('banner_image'); $hasImage = !empty($image); @endphp
        <div class="hero-banner mt-4 mb-5 reveal"
             style="--i:{{ $loop->index }}; background: {{ $banner->background_color ?? '#0ea5e9' }} !important;">
            <div class="relative z-10 flex items-center gap-6 px-6 md:px-14 py-10 md:py-12
                        {{ $hasImage ? '' : 'justify-center text-center' }}">
                <div class="{{ $hasImage ? 'flex-1 ' . ($isRtl ? 'text-right' : 'text-left') : 'max-w-xl mx-auto text-center' }}">
                    @if($banner->badge)
                    <span class="inline-block text-[10px] font-black px-3 py-1 rounded-full mb-3 tracking-widest uppercase"
                          style="background:rgba(255,255,255,.12);color:{{ $banner->text_color ?? '#fff' }};">
                        {{ $banner->badge }}
                    </span>
                    @endif
                    <h2 class="font-display text-2xl md:text-4xl font-bold leading-tight mb-3"
                        style="color: {{ $banner->text_color ?? '#fff' }};">
                        {{ $banner->title }}
                        @if($banner->subtitle)<br><span style="color: {{ $banner->text_color ?? '#fff' }};">{{ $banner->subtitle }}</span>@endif
                    </h2>
                    <p class="text-sm mb-6 leading-relaxed {{ $hasImage ? 'max-w-sm' : 'max-w-md mx-auto' }}"
                       style="color: {{ $banner->text_color ?? '#ffffffcc' }};">{{ $banner->description }}</p>
                    <a href="{{ $banner->button_url ?? '#' }}"
                       class="inline-flex items-center gap-2 font-black text-sm px-6 py-3 rounded-xl shadow-xl"
                       style="background: {{ $banner->text_color ?? '#fff' }}; color: {{ $banner->background_color ?? '#000' }};">
                        {{ $banner->button_text }}
                    </a>
                </div>
                @if($hasImage)
                <div class="w-32 sm:w-40 md:w-52 flex-shrink-0 relative">
                    <img src="{{ $image }}" class="hero-img w-full h-32 sm:h-44 md:h-56 object-cover rounded-2xl">
                </div>
                @endif
            </div>
        </div>
        @endif
    @endforeach

    {{-- ── SECTION 7 lead-in: "All products" divider + sort control ─── --}}
    {{-- (home view only — category/search/sort pages already have their
         own toolbar with search/sort at the very top of the page) ──── --}}
    <div class="flex items-center gap-3 mb-5 mt-2">
        <div class="h-px bg-gray-200 flex-1"></div>
        <span class="text-xs font-bold text-gray-500 flex-shrink-0">{{ __('app.all_products_divider') }}</span>
        <div class="h-px bg-gray-200 flex-1"></div>
    </div>

    <div class="flex items-center justify-end gap-2 mb-4">
        {{-- Desktop sort --}}
        <div class="hidden sm:block">
            <select onchange="window.location.href=this.value"
                    class="text-xs border border-gray-200 rounded-xl px-3 py-2 bg-white cursor-pointer outline-none focus:ring-2"
                    style="--tw-ring-color:var(--brand)">
                @php
                $shortSorts = [
                    'featured'   => __('app.sort_featured_short'),
                    'price_asc'  => __('app.sort_price_asc_short'),
                    'price_desc' => __('app.sort_price_desc_short'),
                    'newest'     => __('app.sort_newest_short'),
                ];
                @endphp
                @foreach($shortSorts as $v => $l)
                <option value="{{ request()->fullUrlWithQuery(['sort' => $v]) }}"
                        {{ request('sort','featured') === $v ? 'selected' : '' }}>{{ $l }}</option>
                @endforeach
            </select>
        </div>

        {{-- Mobile sort --}}
        <button onclick="openSortDrawer()"
                class="flex sm:hidden items-center gap-1.5 bg-white border border-gray-200 rounded-xl
                       px-3 py-2 text-xs font-bold text-gray-600 shadow-sm active:scale-95 transition-transform">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h18M7 8h10m-7 4h4"/>
            </svg>
            {{ __('app.sort_btn') }}
        </button>
    </div>

    @endif {{-- end home-view-only SECTIONS 2–6 (intro/categories/dividers/homeSections) --}}

    {{-- ══════════════════════════════════════════════════════════════
         SHARED PRODUCT GRID + PAGINATION
         Renders for EVERY view: home (as Section 7), category, search,
         and sort pages. Was previously trapped inside the home-only
         branch above, which silently hid the entire grid AND the
         pagination on category/search/sort pages — fixed here.
    ══════════════════════════════════════════════════════════════════ --}}
    @if($products->isEmpty())
    <div class="flex flex-col items-center justify-center py-24 text-center">
        <div class="w-16 h-16 bg-white rounded-2xl border border-gray-100 flex items-center justify-center mb-4 shadow-sm">
            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <p class="text-gray-500 font-semibold text-sm">{{ __('app.no_products') }}</p>
        <a href="{{ route('products.index') }}" class="mt-3 text-xs font-bold hover:underline" style="color:var(--brand)">
            {{ __('app.show_all') }}
        </a>
    </div>

    @else
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-x-8 md:gap-x-6 lg:gap-x-5 xl:gap-x-6 gap-y-10 mt-[30px] px-[10px]">
        @foreach($products as $i => $product)
        @php $isWishlisted = in_array($product->id, $wishlistedIds ?? []); @endphp
        <div class="pcard flex flex-col w-full reveal group"
             style="--i: {{ min($i % 6, 5) }}"
             onclick="window.location='{{ route('products.show', $product->slug) }}'">

            <div class="relative overflow-hidden aspect-square rounded-[2px]">
                <div class="shimmer absolute inset-0 z-0" id="sk-{{ $product->id }}"></div>
                <img src="{{ $product->getFirstMediaUrl('products') ?: asset('images/placeholder.jpg') }}"
                     alt="{{ $product->name }}"
                     class="pcard-img absolute inset-0 w-full h-full object-cover z-10
                            transition-opacity duration-300 group-hover:opacity-80"
                     loading="lazy"
                     onload="document.getElementById('sk-{{ $product->id }}').style.display='none'">

                <button type="button" class="favorite-btn absolute top-3 {{ $isRtl ? 'left-3' : 'right-3' }} z-20"
                        data-product-id="{{ $product->id }}"
                        data-wishlisted="{{ $isWishlisted ? 'true' : 'false' }}"
                        onclick="event.stopPropagation(); toggleWishlist(this)"
                        aria-label="{{ $isWishlisted ? __('app.remove_from_wishlist') : __('app.add_to_wishlist') }}">
                    <svg data-heart="outline"
                         class="w-5 h-5 text-white drop-shadow-sm transition-all duration-200 {{ $isWishlisted ? 'hidden' : 'block' }}"
                         fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                    <svg data-heart="filled"
                         class="w-5 h-5 text-red-500 drop-shadow-sm transition-all duration-200 {{ $isWishlisted ? 'block' : 'hidden' }}"
                         fill="currentColor" viewBox="0 0 24 24">
                        <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </button>
            </div>

            <div class="pt-1.5 flex flex-col gap-0.5 text-start">
                <p class="text-xs font-medium line-clamp-2 leading-snug"
                   style="color: var(--text-product-title);">
                    {{ $product->name }}
                </p>

                <div class="flex items-center gap-2">
                    @if($product->discount_price && $product->discount_price < $product->base_price)
                    <x-price :amount="$product->discount_price"
                             class="price-val text-xs tracking-wide" />
                    <x-price :amount="$product->base_price"
                             class="price-original text-xs tracking-wide" />
                    @else
                    <x-price :amount="$product->base_price"
                             class="price-val text-xs tracking-wide" />
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @endif

</div>
</div>

@if(!$currentCategory && !request('search') && !request('sort'))
{{-- ── SECTION 8: Bottom brand image — tall/vertical, majestic ────────── --}}
@include('partials.sections.top-hero-media', ['position' => 'bottom', 'height' => 'h-[70vh] md:h-[90vh]'])
@endif

{{-- ── LOCATION 3 (Bottom of Page, above footer): position = above_footer ──
     ($sections is only set on the home-view branch above; guard against it
     being undefined on category/search/sort pages.) ───────────────────── --}}
@php $footerSections = ($sections ?? collect())->get('above_footer', collect()); @endphp
@if($footerSections->isNotEmpty())
<div class="bg-gray-50">
    @foreach($footerSections as $dynSection)
        <x-homepage-section-block :section="$dynSection" :is-rtl="$isRtl" />
    @endforeach
</div>
@endif

{{-- ── Pagination — absolute last content block on the page, directly
     above the footer. Isolated, full-width container of its own; not
     nested inside the product grid, the category grid, or any flex/grid
     ancestor, so it can never inherit float/position behavior from
     something else and jump out of place. ──────────────────────────── --}}
@if($products->hasPages())
<div class="w-full block clear-both mt-16 mb-10 py-6 border-t border-gray-100 flex justify-center items-center relative z-10">
    {{ $products->links() }}
</div>
@endif

</div>
@endsection

@push('scripts')
<script>
/* ── Sort drawer ──────────────────────────────────────────────────── */
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

/* ── Scroll reveal ────────────────────────────────────────────────── */
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

document.querySelectorAll('.pcard button, .featured-card button').forEach(function(btn) {
    btn.addEventListener('click', function(e) { e.stopPropagation(); });
});

/* ── Share product ────────────────────────────────────────────────── */
function shareProduct(url, title) {
    var shareData = { title: title, text: '{{ addslashes(__('app.share_text', ['name' => ''])) }}'.replace(':name', title), url: url };
    if (navigator.share) {
        navigator.share(shareData).catch(function(err) { if (err.name !== 'AbortError') console.warn('Share failed:', err); });
        return;
    }
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(url).then(function() { showShareToast('{{ __('app.share_copied') }}'); }).catch(function() { showSharePrompt(url); });
        return;
    }
    showSharePrompt(url);
}
function showShareToast(message) {
    if (typeof Cart !== 'undefined' && Cart.toast) { Cart.toast(message, 'success'); return; }
    var toast = document.createElement('div');
    toast.textContent = message;
    toast.style.cssText = 'position:fixed;top:20px;right:20px;z-index:9999;background:#111827;color:#fff;font-size:13px;font-weight:600;padding:10px 18px;border-radius:12px;box-shadow:0 8px 30px rgba(0,0,0,.2);transition:opacity .3s;pointer-events:none';
    document.body.appendChild(toast);
    setTimeout(function() { toast.style.opacity = '0'; }, 2000);
    setTimeout(function() { document.body.removeChild(toast); }, 2400);
}
function showSharePrompt(url) { window.prompt('{{ __('app.share_prompt') }}', url); }

/* ── Alpine.js liveSearch ─────────────────────────────────────────── */
document.addEventListener('alpine:init', function () {
    Alpine.data('liveSearch', function () {
        return {
            query: '', results: [], isOpen: false,
            loading: false, activeIndex: -1, timer: null,
            init() {},
            onInput() {
                clearTimeout(this.timer);
                this.activeIndex = -1;
                if (this.query.length < 2) { this.results = []; this.isOpen = false; return; }
                this.timer = setTimeout(() => { this.fetch(); }, 280);
            },
            async fetch() {
                this.loading = true; this.isOpen = true;
                try {
                    var res  = await window.fetch('/api/search?q=' + encodeURIComponent(this.query), { headers: { 'Accept': 'application/json' } });
                    var data = await res.json();
                    this.results = data.results || [];
                } catch (e) { console.warn('Live search error:', e); this.results = []; }
                finally { this.loading = false; }
            },
            open()  { this.isOpen = true; },
            close() { this.isOpen = false; this.activeIndex = -1; },
            moveDown() { if (this.activeIndex < this.results.length - 1) this.activeIndex++; },
            moveUp()   { if (this.activeIndex > 0) this.activeIndex--; },
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