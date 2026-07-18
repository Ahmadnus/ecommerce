{{--
    products/_toolbar.blade.php — category / search / sort page header:
    result count + live-search box + desktop sort select + mobile sort
    button, followed by the optional category banner and breadcrumb.
    Needs: $isRtl, $products, $currentCategory.
--}}
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

{{-- ── Category banner image ────────────────────────────────────────── --}}
@if($currentCategory && $currentCategory->shouldShowBanner())
<div class="cat-banner-img-wrap reveal">
    <img src="{{ $currentCategory->getBannerImageUrl() }}" alt="{{ $currentCategory->name }}" loading="eager">
</div>
@endif

{{-- ── Breadcrumb ───────────────────────────────────────────────────── --}}
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
