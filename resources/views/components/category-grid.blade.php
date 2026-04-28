{{-- 
    resources/views/components/category-grid.blade.php
    ────────────────────────────────────────────────────────────────
    Full SHEIN-style category section.
    Renders a scrollable horizontal strip on mobile, and a
    multi-row responsive grid on desktop.

    Usage (in products/index.blade.php or any page):
        <x-category-grid :categories="$topCategories" :current="$currentCategory" />

    Props:
        $categories   Collection   — flat list of Category models
        $current      ?Category    — currently active category (for highlight)
        $title        string       — optional section header  ('' = no header)
        $showAll      bool         — prepend an "All" pill
--}}

@props([
    'categories',
    'current'  => null,
    'title'    => '',
    'showAll'  => true,
])

@if($categories->isNotEmpty())
<section class="category-grid-section mb-6" dir="rtl">

    @if($title)
    <div class="flex items-center justify-between mb-3 px-1">
        <h2 class="font-display text-base font-bold text-gray-900 flex items-center gap-2">
            <span class="w-1 h-4 rounded-full" style="background:var(--brand-color,#0ea5e9)"></span>
            {{ $title }}
        </h2>
    </div>
    @endif

    {{-- Mobile: horizontal scroll strip --}}
    <div class="flex md:hidden items-start gap-5 overflow-x-auto pb-4
                scrollbar-hide -mx-3 px-3">

        @if($showAll)
        <a href="{{ route('products.index') }}"
           class="flex flex-col items-center gap-2 flex-shrink-0 w-20 group">
            <div class="w-16 h-16 rounded-full flex items-center justify-center flex-shrink-0
                        ring-2 transition-all duration-200
                        {{ !$current
                            ? 'ring-[var(--brand-color,#0ea5e9)] ring-offset-2'
                            : 'ring-gray-200 group-hover:ring-[var(--brand-color,#0ea5e9)]' }}"
                 style="{{ !$current ? 'background:var(--brand-color,#0ea5e9)' : 'background:#f3f4f6' }}">
                <svg class="w-6 h-6 {{ !$current ? 'text-white' : 'text-gray-500' }}"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
            </div>
            <span class="text-[10px] font-semibold text-center leading-snug
                         {{ !$current ? 'text-[var(--brand-color,#0ea5e9)]' : 'text-gray-600' }}">
                الكل
            </span>
        </a>
        @endif

        @foreach($categories as $cat)
        <x-category-circle
            :category="$cat"
            size="md"
            :active="$current && $current->id === $cat->id"
        />
        @endforeach
    </div>

    {{-- Desktop: responsive grid --}}
    <div class="hidden md:grid grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-x-8 gap-y-8">

        @if($showAll)
        <a href="{{ route('products.index') }}"
           class="flex flex-col items-center gap-2 group">
            <div class="w-20 h-20 rounded-full flex items-center justify-center flex-shrink-0
                        ring-2 transition-all duration-200
                        {{ !$current
                            ? 'ring-[var(--brand-color,#0ea5e9)] ring-offset-2'
                            : 'ring-gray-100 group-hover:ring-[var(--brand-color,#0ea5e9)]' }}"
                 style="{{ !$current ? 'background:var(--brand-color,#0ea5e9)' : 'background:#f3f4f6' }}">
                <svg class="w-8 h-8 {{ !$current ? 'text-white' : 'text-gray-500' }}"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
            </div>
            <span class="text-[11px] font-semibold text-center
                         {{ !$current ? 'text-[var(--brand-color,#0ea5e9)]' : 'text-gray-600' }}">
                الكل
            </span>
        </a>
        @endif

        @foreach($categories as $cat)
        <x-category-circle
            :category="$cat"
            size="xl"
            :active="$current && $current->id === $cat->id"
        />
        @endforeach

    </div>

</section>
@endif