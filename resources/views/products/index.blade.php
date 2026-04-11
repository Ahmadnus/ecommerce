{{-- resources/views/products/index.blade.php --}}

@extends('layouts.app')

@section('title', $currentCategory ? $currentCategory->name . ' — المتجر' : 'جميع المنتجات')

@push('head')
<style>
    @keyframes shimmer {
        0%   { background-position: -400px 0; }
        100% { background-position:  400px 0; }
    }
    .shimmer {
        background: linear-gradient(90deg, #f3f4f6 25%, #e9eaec 50%, #f3f4f6 75%);
        background-size: 800px 100%;
        animation: shimmer 1.4s ease-in-out infinite;
    }
    .category-children.hidden { display: none; }
    [dir="rtl"] .rtl-arrow { transform: rotate(180deg); }
    [dir="rtl"] .rtl-arrow.rotate-90 { transform: rotate(270deg); }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" dir="rtl">

    {{-- Breadcrumb --}}
    @if($currentCategory)
    <nav class="flex items-center gap-1.5 text-sm text-gray-500 mb-6 flex-wrap">
        <a href="{{ route('products.index') }}" class="hover:text-brand-600 transition-colors">المتجر</a>
        @foreach($currentCategory->getAncestors() as $ancestor)
            <svg class="w-3.5 h-3.5 rotate-180 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <a href="{{ route('products.index', ['category' => $ancestor->slug]) }}"
               class="hover:text-brand-600 transition-colors">{{ $ancestor->name }}</a>
        @endforeach
        <svg class="w-3.5 h-3.5 rotate-180 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-gray-900 font-medium">{{ $currentCategory->name }}</span>
    </nav>
    @endif

    <div class="flex gap-6 lg:gap-8">

        {{-- ═══ SIDEBAR ═══════════════════════════════════════════════════════ --}}
        <aside class="hidden lg:block w-60 flex-shrink-0">
            <div class="bg-white rounded-2xl border border-gray-100 p-4 sticky top-24">

                <h3 class="text-sm font-bold text-gray-900 mb-4 pb-3 border-b border-gray-100">
                    التصنيفات
                </h3>

                <div class="mb-1">
                    <a href="{{ route('products.index') }}"
                       class="flex items-center gap-2 py-1.5 px-2 rounded-lg text-sm transition-colors
                              {{ !$currentCategory ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-gray-700 hover:bg-gray-100 font-medium' }}">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                        جميع المنتجات
                    </a>
                </div>

                <div class="space-y-0.5">
                    <x-category-tree
                        :categories="$categoryTree"
                        :current="$currentCategory"
                    />
                </div>
            </div>
        </aside>

        {{-- ═══ MAIN ════════════════════════════════════════════════════════ --}}
        <div class="flex-1 min-w-0">

            {{-- Toolbar --}}
            <div class="flex items-center justify-between mb-5 gap-3 flex-wrap">
                <div>
                    <h1 class="text-xl font-bold text-gray-900">
                        {{ $currentCategory ? $currentCategory->name : 'جميع المنتجات' }}
                    </h1>
                    <p class="text-sm text-gray-500 mt-0.5">
                        {{ $products->total() }} منتج
                        @if(request('search'))
                            لـ "<span class="text-gray-700">{{ request('search') }}</span>"
                        @endif
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    {{-- Search --}}
                    <form method="GET" action="{{ route('products.index') }}" class="flex">
                        @if(request('category'))
                            <input type="hidden" name="category" value="{{ request('category') }}">
                        @endif
                        <div class="relative">
                            <input
                                type="text"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="ابحث عن منتج..."
                                class="pe-9 ps-3 py-2 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none w-48"
                            >
                            <button type="submit" class="absolute inset-y-0 end-0 flex items-center pe-2.5 text-gray-400 hover:text-gray-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </button>
                        </div>
                    </form>

                    {{-- Sort --}}
                    <select
                        onchange="window.location.href=this.value"
                        class="text-sm border border-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-brand-500 outline-none bg-white cursor-pointer"
                    >
                        @foreach([
                            'featured'   => 'المميزة أولاً',
                            'price_asc'  => 'السعر: من الأقل',
                            'price_desc' => 'السعر: من الأعلى',
                            'newest'     => 'الأحدث',
                        ] as $value => $label)
                        <option value="{{ request()->fullUrlWithQuery(['sort' => $value]) }}"
                                {{ request('sort', 'featured') === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Skeleton --}}
            <div id="skeleton-grid" class="hidden grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
                <x-product-skeleton :count="8" />
            </div>

            {{-- Product Grid --}}
            @if($products->isEmpty())
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-gray-500 font-medium">لا توجد منتجات حالياً</p>
                <a href="{{ route('products.index') }}" class="mt-3 text-sm text-brand-600 hover:underline">
                    عرض جميع المنتجات
                </a>
            </div>
            @else
  <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
    @foreach($products as $product)
        @php
            $cardImage = $product->image_url ?? 'https://picsum.photos/seed/' . $product->id . '/400/400';
            $isWishlisted = in_array($product->id, $wishlistedIds ?? []);
        @endphp

        {{-- الكرت الآن div وليس a لتجنب مشاكل الضغط --}}
        <div class="product-card bg-white rounded-2xl overflow-hidden border border-gray-100 flex flex-col group relative transition-all duration-300 hover:shadow-lg">
            
            {{-- 1. منطقة الصورة والعناصر العلوية --}}
            <div class="aspect-square overflow-hidden bg-gray-50 relative">
                {{-- رابط الصورة فقط يفتح صفحة المنتج --}}
                <a href="{{ route('products.show', $product->slug) }}" class="block w-full h-full">
                    <img src="{{ $cardImage }}" 
                         alt="{{ $product->name }}" 
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                         loading="lazy">
                </a>

                {{-- ❤️ زر المفضلة - جهة اليسار (في تصميم RTL) --}}
                <div class="absolute top-2 end-2 z-30">
                    {{-- استخدمنا div لمنع تداخل الضغط --}}
                    <div onclick="event.preventDefault(); event.stopPropagation();">
                        <x-wishlist-btn :product="$product" :wishlisted="$isWishlisted" />
                    </div>
                </div>

                {{-- 🏷️ البانر (خصم / مميز) - جهة اليمين (في تصميم RTL) --}}
                <div class="absolute top-2 start-2 flex flex-col gap-1 z-20 pointer-events-none">
                    @if($product->is_on_sale)
                        <span class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm">
                            {{ $product->discount_percentage }}% خصم
                        </span>
                    @endif
                    
                    @if($product->is_featured)
                        <span class="bg-amber-400 text-amber-900 text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm">
                            مميز
                        </span>
                    @endif
                </div>

                {{-- غطاء نفد المخزون --}}
                @if(!$product->in_stock)
                    <div class="absolute inset-0 bg-white/60 flex items-center justify-center z-10 pointer-events-none">
                        <span class="bg-white border border-gray-200 text-gray-600 text-xs font-semibold px-3 py-1.5 rounded-full">
                            نفد المخزون
                        </span>
                    </div>
                @endif
            </div>

            {{-- 2. تفاصيل المنتج --}}
            <div class="p-3 flex flex-col flex-1">
                @if($product->categories->first())
                    <p class="text-[10px] font-bold text-brand-600 mb-1 uppercase">
                        {{ $product->categories->first()->name }}
                    </p>
                @endif

                <a href="{{ route('products.show', $product->slug) }}" class="text-sm font-semibold text-gray-900 line-clamp-2 hover:text-brand-600 transition-colors mb-1">
                    {{ $product->name }}
                </a>

                {{-- السعر وزر السلة --}}
                <div class="mt-auto pt-2 flex items-center justify-between gap-2 border-t border-gray-50">
                    <div class="flex flex-col">
                        @if($product->is_on_sale)
                            <span class="text-sm font-bold text-red-600">
                                ${{ number_format($product->discount_price, 2) }}
                            </span>
                            <span class="text-[10px] text-gray-400 line-through leading-none">
                                ${{ number_format($product->base_price, 2) }}
                            </span>
                        @else
                            <span class="text-sm font-bold text-gray-900">
                                ${{ number_format($product->base_price, 2) }}
                            </span>
                        @endif
                    </div>

                    @if($product->in_stock)
                        <button type="button" 
                                onclick="event.preventDefault(); event.stopPropagation(); Cart.add({{ $product->id }}, 1, this)"
                                class="bg-brand-600 hover:bg-brand-700 text-white p-2 rounded-lg transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>

            @if($products->hasPages())
            <div class="mt-8 flex justify-center">
                {{ $products->links() }}
            </div>
            @endif
            @endif

        </div>{{-- /main --}}
    </div>{{-- /flex --}}
</div>
@endsection

@push('scripts')
<script>
function toggleCategory(btn) {
    const node     = btn.closest('.category-node');
    const children = node.querySelector('.category-children');
    const arrow    = btn.querySelector('.rtl-arrow');
    if (!children) return;
    const isHidden = children.classList.toggle('hidden');
    arrow.classList.toggle('rotate-90', !isHidden);
    btn.setAttribute('aria-label', isHidden ? 'توسيع' : 'طي');
}
window.openSidebar  = () => document.getElementById('mobile-sidebar')?.classList.remove('-translate-x-full');
window.closeSidebar = () => document.getElementById('mobile-sidebar')?.classList.add('-translate-x-full');
</script>
@endpush