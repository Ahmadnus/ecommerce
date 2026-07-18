{{--
    ═══════════════════════════════════════════════════════════════════════════
    STOREFRONT INDEX — SUPER-DYNAMIC MODULAR HOMEPAGE + SHOP VIEWS
    ───────────────────────────────────────────────────────────────────────────
    Two modes, one file:

    • HOMEPAGE (no category/search/sort filters):
      A single pristine loop over every ACTIVE HomepageSection, rendered
      STRICTLY by sort_order ASC. Each section is one interchangeable block —
      hero_banner, categories_grid, product_grid, custom_image, banner
      (stacked media+text), text_block — dispatched by <x-homepage-section>'s
      @switch. The admin composes the entire page from
      /admin/homepage-sections; there are NO hardcoded zones.

    • SHOP VIEW (category / search / sort present):
      Classic toolbar → paginated product grid → pagination.

    Data contract (from ProductService::getStorefrontIndexData):
      $homepageSections — active sections, sorted, product grids pre-resolved
      $products         — paginator for the shop view
      $currentCategory  — Category|null
      $wishlistedIds    — int[]
    ═══════════════════════════════════════════════════════════════════════════
--}}
@extends('layouts.app')

@php
    $isRtl      = app()->getLocale() === 'ar';
    $isShopView = $currentCategory || request('search') || request('sort');
@endphp

@section('title', $currentCategory ? $currentCategory->name . ($isRtl ? ' — المتجر' : ' — Shop') : __('app.all_products'))

@push('head')
    @include('products._styles')
@endpush

@section('content')
<div class="page-shop">

    {{-- Legacy fixed top hero — shop views only; on the homepage the hero
         is a fully sortable 'hero_banner' section inside the builder. --}}
    @if($isShopView)
        @include('partials.sections.top-hero-media', ['position' => 'top'])
    @endif

    @include('products._announcements')

    {{-- Floating WhatsApp --}}
    @php
        $floatingLink = \App\Models\SocialLink::where('is_active', true)
                            ->where('is_floating', true)->first();
    @endphp
    @if($floatingLink)
        <x-floating-button :number="$floatingLink->whatsapp_number" />
    @endif

    @include('products._sort-drawer')
    @include('partials.bottombar')

    {{-- ════════════════════════════════════════════════════════════════
         PAGE BODY
    ═════════════════════════════════════════════════════════════════════ --}}
    <div class="bg-gray-50 pb-bar md:pb-12" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
    <div class="max-w-screen-2xl mx-auto px-3 sm:px-5 lg:px-8">

        @if($isShopView)

            {{-- ── SHOP VIEW: toolbar → paginated grid ──────────────────── --}}
            @include('products._toolbar')
            @include('products._paginated-grid')

        @else

            {{-- ══════════════════════════════════════════════════════════
                 HOMEPAGE — THE MODULAR BUILDER LOOP
                 ──────────────────────────────────────────────────────────
                 .homepage-builder applies a uniform vertical rhythm
                 (3rem / 4rem between blocks) so alternating hero banners,
                 category grids, product grids, images and text always
                 reads balanced and premium. Sections are pre-fetched and
                 product grids pre-resolved (fault-isolated) in
                 ProductService, so this loop can never silently blank.
            ══════════════════════════════════════════════════════════════ --}}
            <div class="homepage-builder">
                @forelse(($homepageSections ?? collect()) as $section)
                    <x-homepage-section
                        :section="$section"
                        :is-rtl="$isRtl"
                        :wishlisted-ids="$wishlistedIds ?? []" />
                @empty
                    {{-- Diagnostic empty state — local env / logged-in only,
                         so an empty sections table is instantly visible
                         instead of a silent blank homepage. --}}
                    @if(app()->environment('local') || auth()->check())
                    <div class="my-10 mx-auto max-w-xl rounded-2xl border-2 border-dashed border-amber-300 bg-amber-50 p-6 text-center">
                        <p class="text-sm font-bold text-amber-800">
                            No sections found matching current sort_order.
                        </p>
                        <p class="text-xs text-amber-700 mt-2">
                            لا توجد أقسام نشطة للصفحة الرئيسية — أضف أقساماً (شبكة تصنيفات، شبكة منتجات، بانر…) من
                            <a href="{{ url('/admin/homepage-sections') }}" class="underline font-bold">لوحة التحكم</a>.
                        </p>
                    </div>
                    @endif
                @endforelse
            </div>

        @endif

    </div>
    </div>

    {{-- Pagination — shop views only, isolated full-width block directly
         above the footer so it can never inherit layout from an ancestor. --}}
    @if($isShopView && $products->hasPages())
    <div class="w-full block clear-both mt-16 mb-10 py-6 border-t border-gray-100 flex justify-center items-center relative z-10">
        {{ $products->links() }}
    </div>
    @endif

</div>
@endsection

@push('scripts')
    @include('products._scripts')
@endpush
