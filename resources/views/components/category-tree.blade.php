{{--
    resources/views/components/category-tree.blade.php
    
    Recursive RTL sidebar category tree.
    Usage: <x-category-tree :categories="$categoryTree" :current="$currentCategory" />
--}}

@props([
    'categories',          // Collection of Category models (with allActiveChildren loaded)
    'current'   => null,   // Currently active Category model
    'depth'     => 0,      // Current nesting depth (internal, increments recursively)
])

@foreach($categories as $category)
@php
    $isActive    = $current && $current->id === $category->id;
    $isAncestor  = $current && str_starts_with($current->path ?? '', $category->path . '/');
    $hasChildren = $category->allActiveChildren->isNotEmpty();
    $isOpen      = $isActive || $isAncestor;
    $paddingEnd  = $depth * 14; // RTL: padding-inline-end
@endphp

<div class="category-node" data-depth="{{ $depth }}">
    <div class="flex items-center gap-1">

        {{-- Expand/Collapse toggle (only if has children) --}}
        @if($hasChildren)
        <button
            type="button"
            onclick="toggleCategory(this)"
            class="flex-shrink-0 w-5 h-5 flex items-center justify-center rounded text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors"
            aria-label="{{ $isOpen ? 'طي' : 'توسيع' }}"
        >
            {{-- Arrow pointing left in RTL, rotates down when open --}}
            <svg class="w-3.5 h-3.5 transition-transform duration-200 {{ $isOpen ? 'rotate-90' : '' }} rtl-arrow"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>
        @else
        {{-- Spacer to keep alignment consistent --}}
        <span class="flex-shrink-0 w-5 h-5 flex items-center justify-center">
            <span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span>
        </span>
        @endif

        {{-- Category link --}}
        <a
            href="{{ route('products.index', ['category' => $category->slug]) }}"
            style="padding-inline-start: {{ $paddingEnd }}px"
            class="
                flex-1 flex items-center gap-2 py-1.5 px-2 rounded-lg text-sm transition-colors
                {{ $isActive
                    ? 'bg-brand-50 text-brand-700 font-semibold'
                    : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900 font-medium' }}
                {{ $depth > 0 ? 'text-[13px]' : '' }}
            "
        >
            {{-- Depth indicator line for nested items --}}
            @if($depth > 0)
            <span class="w-px h-4 bg-gray-200 flex-shrink-0"></span>
            @endif

            <span class="leading-snug">{{ $category->name }}</span>

            {{-- Active indicator dot --}}
            @if($isActive)
            <span class="mr-auto w-1.5 h-1.5 rounded-full bg-brand-500 flex-shrink-0"></span>
            @endif
        </a>
    </div>

    {{-- Nested children --}}
    @if($hasChildren)
    <div class="category-children {{ $isOpen ? '' : 'hidden' }} ms-5 border-s border-gray-100 ps-1 mt-0.5 space-y-0.5">
        <x-category-tree
            :categories="$category->allActiveChildren"
            :current="$current"
            :depth="$depth + 1"
        />
    </div>
    @endif
</div>
@endforeach