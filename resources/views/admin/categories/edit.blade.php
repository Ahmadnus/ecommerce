@extends('layouts.admin')
@section('title', 'تعديل التصنيف')

@section('admin-content')
<div class="max-w-3xl mx-auto">
    <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf @method('PUT')
        
        <div class="bg-white p-8 rounded-2xl border border-gray-200 shadow-sm space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">اسم التصنيف</label>
                    <input type="text" name="name" value="{{ $category->name }}" required class="w-full border-gray-200 rounded-xl p-3 bg-gray-50">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">الرابط (Slug)</label>
                    <input type="text" name="slug" value="{{ $category->slug }}" required class="w-full border-gray-200 rounded-xl p-3 bg-gray-50">
                </div>
                
            </div>

            <div class="flex items-center gap-6">
                <img src="{{ $category->getFirstMediaUrl('categories') ?: asset('default.png') }}" class="w-20 h-20 rounded-xl object-cover">
                <input type="file" name="image" class="flex-1 text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-brand/10 file:text-brand">
            </div>
        </div>

        <button type="submit" class="bg-brand text-white px-10 py-3 rounded-xl font-bold shadow-lg float-right">تحديث</button>
    </form>
</div>
@endsection