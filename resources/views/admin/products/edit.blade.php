@extends('layouts.admin')
@section('title', 'تعديل المنتج')

@section('admin-content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.products.index') }}" class="text-gray-500 hover:text-brand flex items-center gap-2 text-sm transition">
            <span>← العودة للمنتجات</span>
        </a>
    </div>

    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT') {{-- مهم جداً للتعديل --}}

        <div class="bg-white p-8 rounded-2xl border border-gray-200 shadow-sm grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- الاسم --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-gray-700 mb-2">اسم المنتج</label>
                <input type="text" name="name" value="{{ old('name', $product->name) }}" 
                       class="w-full border-gray-200 rounded-xl focus:ring-brand focus:border-brand p-3 bg-gray-50">
            </div>

            {{-- السعر --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">السعر ($)</label>
                <input type="number" step="0.01" name="price" value="{{ old('price', $product->price) }}"
                       class="w-full border-gray-200 rounded-xl focus:ring-brand focus:border-brand p-3 bg-gray-50">
            </div>

            {{-- الكمية --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">الكمية في المخزن</label>
                <input type="number" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}"
                       class="w-full border-gray-200 rounded-xl focus:ring-brand focus:border-brand p-3 bg-gray-50">
            </div>
            <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">خصم (Slug)</label>
                   <input type="number" name="sale_price" step="0.01" value="{{ old('sale_price', $product->sale_price ?? '') }}" placeholder="اتركه فارغاً إذا لا يوجد عرض">
                </div>

            {{-- التصنيف --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-gray-700 mb-2">التصنيف</label>
                <select name="category_id" class="w-full border-gray-200 rounded-xl focus:ring-brand focus:border-brand p-3 bg-gray-50">
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
    {{-- خانة تفعيل المنتج --}}
    <div class="flex items-center p-4 border border-gray-200 rounded-xl bg-gray-50 hover:bg-white transition-colors">
        <input type="checkbox" 
               name="is_active" 
               id="is_active" 
               value="1" 
               {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}
               class="w-5 h-5 text-brand-600 border-gray-300 rounded focus:ring-brand-500 cursor-pointer">
        <label for="is_active" class="mr-3 ml-3 block text-sm font-medium text-gray-700 cursor-pointer">
            تفعيل المنتج (سيظهر في المتجر)
        </label>
    </div>

    {{-- خانة منتج مميز --}}
    <div class="flex items-center p-4 border border-gray-200 rounded-xl bg-gray-50 hover:bg-white transition-colors">
        <input type="checkbox" 
               name="is_featured" 
               id="is_featured" 
               value="1" 
               {{ old('is_featured', $product->is_featured ?? false) ? 'checked' : '' }}
               class="w-5 h-5 text-yellow-500 border-gray-300 rounded focus:ring-yellow-400 cursor-pointer">
        <label for="is_featured" class="mr-3 ml-3 block text-sm font-medium text-gray-700 cursor-pointer">
            منتج مميز (يظهر في السلايدر أو المميز)
        </label>
    </div>
</div>
            {{-- الصورة الحالية والجديدة --}}
            <div class="md:col-span-2 space-y-4">
                <label class="block text-sm font-bold text-gray-700 mb-2">صورة المنتج</label>
                <div class="flex items-center gap-6">
                    <img src="{{ $product->getFirstMediaUrl('products') }}" class="w-24 h-24 rounded-xl object-cover border-2 border-brand/20">
                    <input type="file" name="main_image" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-brand/10 file:text-brand font-semibold">
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-brand text-white px-10 py-3 rounded-xl font-bold shadow-lg shadow-brand/30 hover:scale-105 transition-transform">
                حفظ التعديلات
            </button>
        </div>
    </form>
</div>
@endsection