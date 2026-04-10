@extends('layouts.admin')
@section('title', 'إدارة المنتجات')

@section('admin-content')
<div class="flex items-center justify-between mb-8">
    <h1 class="text-3xl font-bold text-gray-900">المنتجات</h1>
    <a href="{{ route('admin.products.create') }}"
       class="inline-flex items-center px-4 py-2 bg-brand text-white font-semibold rounded-xl hover:opacity-90 shadow-lg transition">
        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        إضافة منتج
    </a>
</div>

<div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
    <table class="w-full text-right">
        <thead class="bg-gray-50/50 border-b border-gray-200">
            <tr>
                <th class="px-6 py-4 text-sm font-bold text-gray-500 uppercase">المنتج</th>
                <th class="px-6 py-4 text-sm font-bold text-gray-500 uppercase">التصنيف</th>
                <th class="px-6 py-4 text-sm font-bold text-gray-500 uppercase">السعر</th>
                <th class="px-6 py-4 text-sm font-bold text-gray-500 uppercase">المتغيرات</th>
                <th class="px-6 py-4 text-sm font-bold text-gray-500 uppercase text-center">الحالة</th>
                <th class="px-6 py-4 text-sm font-bold text-gray-500 uppercase text-left">العمليات</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($products as $product)
            <tr class="hover:bg-gray-50/80 transition">

                {{-- Product name + image --}}
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <img src="{{ $product->getFirstMediaUrl('products') ?: asset('default.png') }}"
                             class="w-12 h-12 rounded-lg object-cover border border-gray-100 shadow-sm"
                             alt="{{ $product->name }}">
                        <div class="flex flex-col">
                            <span class="font-bold text-gray-900 leading-tight">{{ $product->name }}</span>
                            <span class="text-[10px] text-gray-400 mt-0.5 uppercase tracking-wider font-mono">
                                SKU: {{ $product->sku ?? '---' }}
                            </span>
                        </div>
                    </div>
                </td>

                {{-- Primary category with breadcrumb --}}
                <td class="px-6 py-4">
                    @php $primaryCat = $product->categories->first(fn($c) => $c->pivot->is_primary) ?? $product->categories->first(); @endphp
                    @if($primaryCat)
                    <div class="flex flex-col gap-1">
                        <span class="text-sm text-gray-800 font-medium">{{ $primaryCat->name }}</span>
                        @if(!$primaryCat->isRoot())
                        <span class="text-[10px] text-gray-400 font-mono">{{ $primaryCat->breadcrumb }}</span>
                        @endif
                    </div>
                    @else
                    <span class="text-sm text-gray-400">بدون تصنيف</span>
                    @endif
                </td>

                {{-- Price: base_price / discount_price --}}
                <td class="px-6 py-4">
                    <div class="flex flex-col">
                        @if($product->discount_price && $product->discount_price < $product->base_price)
                        <span class="text-xs text-red-500 font-bold bg-red-50 px-1.5 py-0.5 rounded w-fit mb-1">تخفيض</span>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-gray-900">${{ number_format($product->discount_price, 2) }}</span>
                            <span class="text-xs text-gray-400 line-through">${{ number_format($product->base_price, 2) }}</span>
                        </div>
                        @else
                        <span class="font-bold text-gray-900">${{ number_format($product->base_price, 2) }}</span>
                        @endif
                    </div>
                </td>

                {{-- Variants count --}}
                <td class="px-6 py-4">
                    @php $variantCount = $product->variants->where('is_active', true)->count(); @endphp
                    @if($variantCount > 0)
                    <span class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 px-2.5 py-1 rounded-full text-xs font-bold">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 11h.01M7 15h.01M13 7h.01M13 11h.01M13 15h.01"/>
                        </svg>
                        {{ $variantCount }} متغير
                    </span>
                    @else
                    <span class="text-xs text-gray-400">—</span>
                    @endif
                </td>

                {{-- Status --}}
                <td class="px-6 py-4 text-center">
                    @if($product->status === 'active')
                    <span class="inline-flex items-center gap-1.5 bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold">
                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                        نشط
                    </span>
                    @elseif($product->status === 'draft')
                    <span class="inline-flex items-center gap-1.5 bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-bold">
                        <span class="w-1.5 h-1.5 bg-yellow-500 rounded-full"></span>
                        مسودة
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1.5 bg-gray-100 text-gray-500 px-3 py-1 rounded-full text-xs font-bold">
                        <span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span>
                        مخفي
                    </span>
                    @endif
                </td>

                {{-- Actions --}}
                <td class="px-6 py-4 text-left">
                    <div class="flex justify-end items-center gap-2">
                        <a href="{{ route('admin.products.edit', $product->id) }}"
                           class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="تعديل">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                        </a>
                        <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST"
                              onsubmit="return confirm('هل أنت متأكد؟ سيتم نقل المنتج لسلة المهملات.')" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="حذف">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </td>

            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Pagination --}}
<div class="mt-6">
    {{ $products->links() }}
</div>
@endsection