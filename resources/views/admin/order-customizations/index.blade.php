@extends('layouts.admin')

@section('title', 'طلبات التخصيص')

@section('admin-content')

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

    {{-- ── Header + filters ──────────────────────────────────────────────── --}}
    <div class="p-6 border-b border-gray-100 flex flex-wrap gap-4 items-center justify-between">
        <div>
            <h3 class="font-bold text-gray-900 text-base">طلبات التخصيص</h3>
            <p class="text-xs text-gray-400 mt-0.5">
                {{ $customizations->total() }} طلب · عرض {{ $customizations->firstItem() }}–{{ $customizations->lastItem() }}
            </p>
        </div>

        <form method="GET" class="flex flex-wrap gap-2 items-center">
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="بحث برقم الطلب / التخصيص / المنتج"
                   class="text-xs rounded-xl px-3 py-2 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent min-w-[220px]">

            <select name="status"
                    onchange="this.form.submit()"
                    class="text-xs rounded-xl px-3 py-2 border border-gray-200 focus:outline-none cursor-pointer">
                <option value="">كل الحالات</option>
                <option value="pending"    {{ request('status') === 'pending'    ? 'selected' : '' }}>قيد الانتظار</option>
                <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>جارٍ المعالجة</option>
                <option value="ready"      {{ request('status') === 'ready'      ? 'selected' : '' }}>جاهز</option>
                <option value="error"      {{ request('status') === 'error'      ? 'selected' : '' }}>خطأ</option>
            </select>

            <button type="submit"
                    class="text-xs px-4 py-2 rounded-xl bg-black text-white hover:bg-gray-800 transition-colors">
                بحث
            </button>

            @if(request()->hasAny(['search', 'status']))
            <a href="{{ route('admin.customizations.index') }}"
               class="text-xs px-3 py-2 rounded-xl border border-gray-200 text-gray-500 hover:bg-gray-50 transition-colors">
                مسح
            </a>
            @endif
        </form>
    </div>

    {{-- ── Cards grid ────────────────────────────────────────────────────── --}}
    <div class="p-6 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">

        @forelse($customizations as $customization)
        @php
            $product = $customization->product;

            // Product thumbnail
            $image = null;
            if ($product) {
                if (method_exists($product, 'getFirstMediaUrl')) {
                    $image = $product->getFirstMediaUrl('images') ?: null;
                } elseif (! empty($product->image)) {
                    $image = \Illuminate\Support\Str::startsWith($product->image, 'http')
                        ? $product->image
                        : \Illuminate\Support\Facades\Storage::disk('public')->url($product->image);
                }
            }

            $statusMeta = [
                'pending'    => ['class' => 'bg-yellow-100 text-yellow-800', 'label' => 'قيد الانتظار'],
                'processing' => ['class' => 'bg-blue-100 text-blue-800',    'label' => 'جارٍ المعالجة'],
                'ready'      => ['class' => 'bg-green-100 text-green-800',  'label' => 'جاهز'],
                'error'      => ['class' => 'bg-red-100 text-red-800',      'label' => 'خطأ'],
            ];
            $sm = $statusMeta[$customization->status] ?? ['class' => 'bg-gray-100 text-gray-700', 'label' => $customization->status];

            // Guess garment type for badge from zones
            $zones = $customization->selected_zones ?? [];
            $garmentBadge = 'جاكيت';
            if (! empty(array_intersect($zones, ['1','2','4','5','6']))) $garmentBadge = 'ثوب تخرج';
            elseif (! empty(array_intersect($zones, ['D1','D2','D3','F']))) $garmentBadge = 'هودي';
        @endphp

        <a href="{{ route('admin.customizations.show', $customization) }}"
           class="group block bg-gray-50 hover:bg-white border border-gray-100 hover:border-gray-300
                  rounded-2xl p-4 transition-all duration-200 shadow-sm hover:shadow-md">

            <div class="flex items-start gap-3">

                {{-- Thumbnail --}}
                <div class="w-16 h-16 flex-shrink-0 rounded-xl overflow-hidden border border-gray-200 bg-white">
                    @if($image)
                        <img src="{{ $image }}"
                             class="w-full h-full object-cover"
                             alt="{{ $product->name ?? 'منتج' }}">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gray-100">
                            <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                            </svg>
                        </div>
                    @endif
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2 mb-1">
                        <p class="font-semibold text-sm text-gray-900 truncate">
                            {{ $product->name ?? 'منتج غير متاح' }}
                        </p>
                        <span class="text-[10px] px-2 py-0.5 rounded-full flex-shrink-0 {{ $sm['class'] }}">
                            {{ $sm['label'] }}
                        </span>
                    </div>

                    <p class="text-xs text-gray-400">
                        تخصيص #{{ $customization->id }}
                        @if($customization->order_id) · طلب #{{ $customization->order_id }} @endif
                    </p>

                    {{-- Garment type badge --}}
                    <span class="inline-block mt-1 text-[10px] px-2 py-0.5 rounded-md bg-gray-200 text-gray-600">
                        {{ $garmentBadge }}
                    </span>

                    {{-- Notes preview --}}
                    @if($customization->notes)
                    <p class="text-xs text-gray-500 mt-1.5 line-clamp-2">
                        {{ $customization->notes }}
                    </p>
                    @endif

                    {{-- Zone chips --}}
                    @if(! empty($customization->selected_zones))
                    <div class="mt-2 flex flex-wrap gap-1">
                        @foreach(array_slice($customization->selected_zones, 0, 6) as $zone)
                        <span class="text-[10px] px-1.5 py-0.5 rounded bg-blue-50 text-blue-700 font-mono">
                            {{ $zone }}
                        </span>
                        @endforeach
                        @if(count($customization->selected_zones) > 6)
                        <span class="text-[10px] px-1.5 py-0.5 rounded bg-gray-100 text-gray-500">
                            +{{ count($customization->selected_zones) - 6 }}
                        </span>
                        @endif
                    </div>
                    @endif

                    {{-- Timestamp --}}
                    <p class="text-[10px] text-gray-300 mt-2">
                        {{ $customization->created_at->diffForHumans() }}
                    </p>
                </div>
            </div>
        </a>

        @empty
        <div class="col-span-full text-center py-16 text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
            </svg>
            <p class="text-sm font-medium">لا توجد طلبات تخصيص</p>
            <p class="text-xs mt-1">جرّب تغيير معايير البحث أو الفلتر</p>
        </div>
        @endforelse

    </div>

    {{-- ── Pagination ─────────────────────────────────────────────────────── --}}
    @if($customizations->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $customizations->links() }}
    </div>
    @endif

</div>

@endsection