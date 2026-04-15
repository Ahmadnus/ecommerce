@extends('layouts.admin')
@section('title', 'إدارة المنتجات')

@section('admin-content')
{{-- ─── الترويسة العلوية ─────────────────────────────────────────── --}}
<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">إدارة المنتجات</h2>
        <p class="text-gray-500 text-sm mt-1">استعرض كافة المنتجات، تتبع حالة المخزون المتوفر، وقم بإدارة التفاصيل.</p>
    </div>
    <a href="{{ route('admin.products.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-6 rounded-xl shadow-sm hover:shadow-md transition-all flex items-center gap-2 w-full md:w-auto justify-center">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        إضافة منتج جديد
    </a>
</div>

{{-- ─── جدول المنتجات ────────────────────────────────────────────── --}}
<div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-right text-sm">
            <thead class="bg-gray-50/80 text-gray-600 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4 font-bold whitespace-nowrap">المنتج</th>
                    <th class="px-6 py-4 font-bold whitespace-nowrap">الرمز (SKU)</th>
                    <th class="px-6 py-4 font-bold whitespace-nowrap">التصنيف</th>
                    <th class="px-6 py-4 font-bold whitespace-nowrap">السعر</th>
                    <th class="px-6 py-4 font-bold whitespace-nowrap">إجمالي المخزون</th>
                    <th class="px-6 py-4 font-bold whitespace-nowrap">الحالة</th>
                    <th class="px-6 py-4 font-bold whitespace-nowrap text-center">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($products as $product)
                    @php
                        // حساب إجمالي المخزون بجمع كميات كل المتغيرات (Variants) التابعة للمنتج
                        $totalStock = $product->variants->sum('stock_quantity');
                    @endphp
                    <tr class="hover:bg-gray-50/70 transition-colors">
                        
                        {{-- صورة واسم المنتج --}}
                        <td class="px-6 py-4 flex items-center gap-4 min-w-[250px]">
                            <div class="w-12 h-12 rounded-xl bg-gray-100 border border-gray-200 overflow-hidden shrink-0 flex items-center justify-center">
                                @if($product->getFirstMediaUrl('products'))
                                    <img src="{{ $product->getFirstMediaUrl('products') }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                @else
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                @endif
                            </div>
                            <div>
                                <div class="font-bold text-gray-900 text-base">{{ $product->name }}</div>
                                @if($product->is_featured)
                                    <span class="inline-block mt-1 text-[10px] bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full font-bold">⭐ مميز</span>
                                @endif
                            </div>
                        </td>

                        {{-- رقم الصنف SKU --}}
                        <td class="px-6 py-4 text-gray-500 font-mono text-xs">
                            {{ $product->sku ?? '---' }}
                        </td>

                        {{-- التصنيف --}}
                        <td class="px-6 py-4 text-gray-600">
                            {{ $product->categories->first()->name ?? 'بدون تصنيف' }}
                            @if($product->categories->count() > 1)
                                <span class="text-xs text-blue-500 font-bold bg-blue-50 px-2 py-1 rounded-md ml-1">
                                    +{{ $product->categories->count() - 1 }}
                                </span>
                            @endif
                        </td>

                        {{-- السعر --}}
                        <td class="px-6 py-4">
                            @if($product->discount_price)
                                <div class="font-bold text-green-600">${{ number_format($product->discount_price, 2) }}</div>
                                <div class="text-xs text-gray-400 line-through">${{ number_format($product->base_price, 2) }}</div>
                            @else
                                <div class="font-bold text-gray-900">${{ number_format($product->base_price, 2) }}</div>
                            @endif
                        </td>

                        {{-- المخزون الكلي --}}
                       <td class="px-6 py-4">
    <div x-data="{ open: false }" class="relative">
        <button @click="open = !open" type="button" class="focus:outline-none">
            @if($totalStock > 10)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-green-50 text-green-700 border border-green-100 hover:bg-green-100">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                    {{ $totalStock }} قطعة
                </span>
            @elseif($totalStock > 0)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-amber-50 text-amber-700 border border-amber-100 hover:bg-amber-100">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                    {{ $totalStock }} قطعة
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-red-50 text-red-700 border border-red-100">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                    نفذت الكمية
                </span>
            @endif
        </button>

        <div x-show="open" 
             @click.away="open = false"
             x-cloak
             class="absolute z-[99] mt-2 w-56 bg-white rounded-xl shadow-2xl border border-gray-200 p-3"
             style="display: none;" {{-- لمنع الوميض عند التحميل --}}
             dir="rtl">
            
            <p class="text-[11px] font-bold text-gray-400 mb-3 border-b pb-2">تفاصيل المخزون المتوفر:</p>
            
            <ul class="space-y-2">
                {{-- انتبه لاسم المتغير هنا، يجب أن يكون نفس اسم العلاقة في موديل المنتج --}}
                @forelse($product->variants as $variant)
                    <li class="flex items-center justify-between gap-4 p-2 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-2">
                            {{-- عرض اللون أو المقاس --}}
                            <span class="text-xs font-bold text-gray-700">
                                {{ $variant->color ?? '' }} {{ $variant->size ?? '' }} 
                                {{ $variant->name ?? '' }}
                            </span>
                        </div>
                        <span class="text-xs font-black text-brand-600 bg-white px-2 py-1 rounded shadow-sm">
                            {{ $variant->stock ?? $variant->quantity ?? 0 }}
                        </span>
                    </li>
                @empty
                    <li class="text-xs text-center text-gray-400 py-2">لا توجد متغيرات لهذا المنتج</li>
                @endforelse
            </ul>
        </div>
    </div>
</td>

                        {{-- الحالة --}}
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-lg text-xs font-bold {{ $product->status === 'active' ? 'bg-blue-50 text-blue-700 border border-blue-100' : 'bg-gray-100 text-gray-600 border border-gray-200' }}">
                                {{ $product->status === 'active' ? 'نشط' : 'مسودة' }}
                            </span>
                        </td>

                        {{-- الإجراءات --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.products.edit', $product) }}" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all" title="تعديل">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من نقل هذا المنتج إلى سلة المهملات؟');" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all" title="حذف">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center text-gray-500 bg-gray-50/30">
                            <div class="flex flex-col items-center justify-center">
                                <span class="text-6xl mb-4">🛍️</span>
                                <span class="font-bold text-xl text-gray-800">لا توجد منتجات مضافة بعد</span>
                                <p class="text-sm text-gray-500 mt-2">ابدأ بإضافة منتجاتك الأولى لعرضها هنا.</p>
                                <a href="{{ route('admin.products.create') }}" class="mt-6 bg-blue-50 text-blue-700 font-bold px-6 py-2 rounded-xl border border-blue-100 hover:bg-blue-100 transition-colors">أضف منتجك الأول الآن</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    {{-- روابط التصفح (Pagination) --}}
    @if($products->hasPages())
        <div class="p-5 border-t border-gray-100 bg-white">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection