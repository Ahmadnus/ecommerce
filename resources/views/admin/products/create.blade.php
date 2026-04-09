@extends('layouts.admin')
@section('title', 'إضافة منتج جديد')

@section('admin-content')
<div class="max-w-4xl mx-auto" x-data="{ imagePreview: null }">
    <div class="mb-6">
        <a href="{{ route('admin.products.index') }}" class="text-gray-500 hover:text-brand flex items-center gap-2 text-sm transition">
            <span>← العودة للمنتجات</span>
        </a>
    </div>

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div class="bg-white p-8 rounded-2xl border border-gray-200 shadow-sm grid grid-cols-1 md:grid-cols-2 gap-6">
            
            {{-- الاسم --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-gray-700 mb-2">اسم المنتج</label>
                <input type="text" name="name" required class="w-full border-gray-200 rounded-xl focus:ring-brand focus:border-brand p-3 bg-gray-50">
            </div>

            {{-- السعر --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">السعر الأساسي</label>
                <input type="number" step="0.01" name="price" required class="w-full border-gray-200 rounded-xl focus:ring-brand focus:border-brand p-3 bg-gray-50">
            </div>

            {{-- الوزن (الذي طلبته) --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">الوزن (kg)</label>
                <input type="number" step="0.01" name="weight" placeholder="0.50" class="w-full border-gray-200 rounded-xl focus:ring-brand focus:border-brand p-3 bg-gray-50">
            </div>

            {{-- الكمية --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">الكمية</label>
                <input type="number" name="stock_quantity" required class="w-full border-gray-200 rounded-xl focus:ring-brand focus:border-brand p-3 bg-gray-50">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">خصم</label>
              <input type="number" name="sale_price" step="0.01" value="{{ old('sale_price', $product->sale_price ?? '') }}" placeholder="اتركه فارغاً إذا لا يوجد عرض">
            </div>

            {{-- التصنيف --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">التصنيف</label>
                <select name="category_id" required class="w-full border-gray-200 rounded-xl focus:ring-brand focus:border-brand p-3 bg-gray-50">
                    <option value="">اختر تصنيف...</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
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
            {{-- رفع الصورة مع معاينة --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-gray-700 mb-2">صورة المنتج الأساسية</label>
                <div class="flex items-center gap-4">
                    <div class="w-24 h-24 border-2 border-dashed border-gray-200 rounded-2xl flex items-center justify-center overflow-hidden bg-gray-50">
                        <template x-if="imagePreview">
                            <img :src="imagePreview" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!imagePreview">
                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </template>
                    </div>
                    <input type="file" name="main_image" required 
                           @change="const file = $event.target.files[0]; if (file) { const reader = new FileReader(); reader.onload = (e) => { imagePreview = e.target.result }; reader.readAsDataURL(file); }"
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-brand/10 file:text-brand font-semibold hover:file:bg-brand/20 transition">
                </div>
            </div>
        </div>
        

        <div class="flex justify-end">
            <button type="submit" class="bg-brand text-white px-10 py-3 rounded-xl font-bold shadow-lg hover:scale-105 transition">
                حفظ المنتج الجديد
            </button>
        </div>
        
    </form>
</div>
@endsection