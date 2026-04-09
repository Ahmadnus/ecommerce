@extends('layouts.admin')
@section('title', 'إضافة تصنيف')

@section('admin-content')
<div class="max-w-3xl mx-auto" x-data="{ name: '', slug: '' }">
    <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        <div class="bg-white p-8 rounded-2xl border border-gray-200 shadow-sm space-y-6">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- الاسم --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">اسم التصنيف</label>
                    <input type="text" name="name" x-model="name" 
                           @input="slug = name.toLowerCase().replace(/[^\w\u0621-\u064A\s]/g, '').replace(/\s+/g, '-')"
                           required class="w-full border-gray-200 rounded-xl focus:ring-brand focus:border-brand p-3 bg-gray-50">
                </div>

                {{-- الـ Slug --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">الرابط (Slug)</label>
                    <input type="text" name="slug" x-model="slug" required 
                           class="w-full border-gray-200 rounded-xl focus:ring-brand focus:border-brand p-3 bg-gray-50 text-gray-500">
                </div>
            </div>

            {{-- الوصف --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">الوصف (اختياري)</label>
                <textarea name="description" rows="3" class="w-full border-gray-200 rounded-xl focus:ring-brand focus:border-brand p-3 bg-gray-50"></textarea>
            </div>

            {{-- الصورة والحالة --}}
            <div class="flex flex-wrap items-center gap-8">
                <div class="flex-1">
                    <label class="block text-sm font-bold text-gray-700 mb-2">صورة القسم</label>
                    <input type="file" name="image" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-brand/10 file:text-brand font-semibold">
                </div>
                
                <div class="flex items-center gap-2 mt-6">
                    <input type="checkbox" name="is_active" value="1" checked class="w-5 h-5 text-brand border-gray-300 rounded focus:ring-brand">
                    <label class="text-sm font-bold text-gray-700">نشط على الموقع</label>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <button type="submit" class="bg-brand text-white px-10 py-3 rounded-xl font-bold shadow-lg hover:scale-105 transition">حفظ التصنيف</button>
        </div>
        
    </form>
</div>
@endsection