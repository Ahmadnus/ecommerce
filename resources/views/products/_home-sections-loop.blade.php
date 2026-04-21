{{--
    ══════════════════════════════════════════════════════════════════════════════
    MODULAR PAGE BUILDER — Homepage Sections Loop
    ──────────────────────────────────────────────────────────────────────────────
    Drop this block into products/index.blade.php in place of the old
    @foreach($homeSections …) block.

    Controller must pass:
      $homeSections  — HomeSection::active()->ordered()->get()
      $wishlistedIds — array of wishlisted product IDs for the current user

    The controller should pre-resolve product lists and attach them to each
    section to avoid N+1 — see ProductController note below.
    ══════════════════════════════════════════════════════════════════════════════
--}}

@if(!$currentCategory && !request('search') && !request('sort'))

@foreach($homeSections as $section)
@php
    /*
     * Product lists have their products pre-resolved by the controller
     * and stored on the model as a dynamic property: $section->products
     * For other block types we pass null — the partial won't use it.
     */
    $sectionProducts = $section->isProductList()
                        ? ($section->products ?? $section->resolveProducts())
                        : collect();
@endphp

@include($section->partialView(), [
    'section'         => $section,
    'sectionProducts' => $sectionProducts,
    'wishlistedIds'   => $wishlistedIds ?? [],
])

@endforeach

{{-- Divider before "all products" grid --}}
<div class="flex items-center gap-3 mb-5 mt-2">
    <div class="h-px bg-gray-200 flex-1"></div>
    <span class="text-xs font-bold text-gray-500 flex-shrink-0">جميع المنتجات</span>
    <div class="h-px bg-gray-200 flex-1"></div>
</div>

@endif