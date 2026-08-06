{{--
    resources/views/admin/categories/_row.blade.php
    Recursive partial — renders one category row then calls itself for children.
    Variables available: $category (Category model with allChildren loaded)
--}}

@php
    $depth      = $category->depth ?? 0;
    $indent     = $depth * 24;          // px indent per level
    $hasChildren = $category->allChildren->isNotEmpty();
    $childCount  = $category->allChildren->count();
    $rowId       = 'cat-row-' . $category->id;
    $childrenId  = 'children-' . $category->id;
@endphp

<tr id="{{ $rowId }}"
    class="hover:bg-gray-50/70 transition-colors group"
    data-depth="{{ $depth }}">

    {{-- ── Category name + image ── --}}
    <td class="px-6 py-3.5">
        <div class="flex items-center gap-3" style="padding-right: {{ $indent }}px">

            {{-- Collapse/expand toggle --}}
            @if($hasChildren)
            <button type="button"
                    onclick="toggleChildren('{{ $childrenId }}')"
                    id="toggle-{{ $category->id }}"
                    class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-md
                           text-gray-400 hover:text-brand hover:bg-brand/10 transition-colors">
                <svg class="w-3.5 h-3.5 transition-transform duration-200"
                     id="arrow-{{ $category->id }}"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            @else
            {{-- Leaf node dot --}}
            <span class="flex-shrink-0 w-6 h-6 flex items-center justify-center">
                <span class="w-1.5 h-1.5 rounded-full {{ $depth > 0 ? 'bg-gray-300' : 'bg-brand/40' }}"></span>
            </span>
            @endif

            {{-- Image --}}
            <div class="w-9 h-9 rounded-lg overflow-hidden border border-gray-100 bg-gray-50 flex-shrink-0">
                @if($category->getFirstMediaUrl('categories'))
                <img src="{{ $category->getFirstMediaUrl('categories') }}"
                     class="w-full h-full object-cover"
                     alt="{{ $category->name }}">
                @else
                <div class="w-full h-full flex items-center justify-center">
                    <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                    </svg>
                </div>
                @endif
            </div>

            {{-- Name --}}
            <div>
                <p class="text-sm font-semibold text-gray-900 leading-tight">
                    {{ $category->name }}
                    @if($hasChildren)
                    <span class="text-[10px] font-bold text-brand/70 bg-brand/10 px-1.5 py-0.5 rounded-full mr-1">
                        {{ $childCount }}
                    </span>
                    @endif
                </p>
                <p class="text-[11px] text-gray-400 font-mono mt-0.5">{{ $category->slug }}</p>
            </div>

        </div>
    </td>

    {{-- ── Path / breadcrumb ── --}}
    <td class="px-6 py-3.5">
        @if(!$category->isRoot())
        <span class="text-[11px] text-gray-400 font-mono bg-gray-50 px-2 py-1 rounded">
            {{ $category->breadcrumb }}
        </span>
        @else
        <span class="text-[11px] text-brand bg-brand/10 font-bold px-2 py-1 rounded-full">Root</span>
        @endif
    </td>

    {{-- ── Product count ── --}}
    <td class="px-6 py-3.5 text-center">
        <span class="text-sm font-semibold text-gray-700">
            {{ $category->products_count ?? $category->products()->count() }}
        </span>
        <span class="text-xs text-gray-400"> منتج</span>
    </td>

    {{-- ── Status ── --}}
    <td class="px-6 py-3.5 text-center">
        @if($category->is_active)
        <span class="inline-flex items-center gap-1.5 bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold">
            <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
            نشط
        </span>
        @else
        <span class="inline-flex items-center gap-1.5 bg-gray-100 text-gray-500 px-3 py-1 rounded-full text-xs font-bold">
            <span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span>
            معطل
        </span>
        @endif
    </td>

    {{-- ── Actions ── --}}
    <td class="px-6 py-3.5 text-left">
        <div class="flex justify-end items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
            <a href="{{ route('admin.categories.edit', $category->id) }}"
               class="p-2 text-blue-500 hover:bg-blue-50 rounded-lg transition-colors" title="تعديل">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                </svg>
            </a>
            <a href="{{ route('admin.categories.create') }}?parent_id={{ $category->id }}"
               class="p-2 text-brand hover:bg-brand/10 rounded-lg transition-colors" title="إضافة تصنيف فرعي">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </a>
            <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST"
                  onsubmit="return confirm('حذف {{ addslashes($category->name) }}؟ سيتم حذف جميع تصنيفاته الفرعية.')"
                  class="inline-block">
                @csrf
                @method('DELETE')
                <button type="submit" class="p-2 text-red-400 hover:bg-red-50 rounded-lg transition-colors" title="حذف">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </form>
        </div>
    </td>
</tr>

{{-- ── Recursive children rows ── --}}
@if($hasChildren)
<tr id="{{ $childrenId }}-wrapper">
    <td colspan="5" class="p-0">
        <table class="w-full" id="{{ $childrenId }}">
            <tbody>
                @each('admin.categories.row', $category->allChildren, 'category')
            </tbody>
        </table>
    </td>
</tr>
@endif