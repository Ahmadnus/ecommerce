@props([
    'categories',
    'current'  => null,
    'title'    => '',
    'showAll'  => true,
])

@php $isRtl = app()->getLocale() === 'ar'; @endphp

@if($categories->isNotEmpty())
<section class="category-grid-section mb-6" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">

    @if($title)
    <div class="flex items-center justify-between mb-3 px-1">
        <h2 class="font-display text-base font-bold text-gray-900 flex items-center gap-2">
            <span class="w-1 h-4 rounded-full" style="background:var(--brand-color,#0ea5e9)"></span>
            {{ $title }}
        </h2>
    </div>
    @endif

    {{-- 2-column square grid on all breakpoints --}}
    <div class="grid grid-cols-2 gap-4 md:gap-6">

        @foreach($categories as $cat)
        <x-category-circle
            :category="$cat"
            :active="$current && $current->id === $cat->id"
        />
        @endforeach

    </div>

</section>
@endif
