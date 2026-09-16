{{--
    components/category-grid.blade.php — the category browser block.

    Uses the .sf-cat-grid primitive (3 up on phones, 4 on tablets, 6 on
    desktop) so tile density matches the reference store's category strip,
    and delegates each tile to <x-category-circle>.
--}}

@props([
    'categories',
    'current'  => null,
    'title'    => '',
    'showAll'  => true,
])

@php $isRtl = app()->getLocale() === 'ar'; @endphp

@if($categories->isNotEmpty())
    <x-sf.section :title="$title ?: null"
                  :link="$showAll ? route('products.index') : null"
                  dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
        <div class="sf-cat-grid">
            @foreach($categories as $cat)
                <x-category-circle :category="$cat"
                                   :active="$current && $current->id === $cat->id" />
            @endforeach
        </div>
    </x-sf.section>
@endif
