@props([
    'categories',
    'current'  => null,
    'title'    => '',
    'showAll'  => true,
])

@php $isRtl = app()->getLocale() === 'ar'; @endphp

@if($categories->isNotEmpty())
<section class="category-grid-section mb-6 mt-[30px]" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">

    @if($title)
    <div class="flex items-center justify-between mb-3 px-1">
        <h2 class="font-display text-base font-bold text-gray-900 flex items-center gap-2">
            <span class="w-1 h-4 rounded-full" style="background:var(--brand-color,#0ea5e9)"></span>
            {{ $title }}
        </h2>
    </div>
    @endif

    {{-- 2-column square grid on all breakpoints --}}
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-x-8 md:gap-x-6 lg:gap-x-5 xl:gap-x-6 gap-y-10 px-[10px]">

        @foreach($categories as $cat)
        <x-category-circle
            :category="$cat"
            :active="$current && $current->id === $cat->id"
        />
        @endforeach

    </div>

</section>
@endif
