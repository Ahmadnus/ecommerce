@extends('layouts.admin')
@section('title', 'إدارة البانر الرئيسي')

@section('admin-content')
<div class="space-y-8">
    {{-- فورم الإضافة --}}
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <h3 class="text-lg font-bold mb-4">إضافة بانر جديد</h3>
        <form action="{{ route('admin.hero-banners.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf
            <input type="text" name="title" placeholder="العنوان الرئيسي" required class="w-full px-4 py-2 rounded-xl border focus:ring-2 focus:ring-brand outline-none">
            <input type="text" name="subtitle" placeholder="العنوان الفرعي (اختياري)" class="w-full px-4 py-2 rounded-xl border focus:ring-2 focus:ring-brand outline-none">
            <input type="text" name="badge" placeholder="النص العلوي (مثلاً: عروض محدودة)" class="w-full px-4 py-2 rounded-xl border focus:ring-2 focus:ring-brand outline-none">
            <input type="text" name="button_url" placeholder="رابط الزر" class="w-full px-4 py-2 rounded-xl border focus:ring-2 focus:ring-brand outline-none">
            <div class="md:col-span-2">
                <textarea name="description" placeholder="وصف قصير" class="w-full px-4 py-2 rounded-xl border focus:ring-2 focus:ring-brand outline-none"></textarea>
            </div>
            <input type="file" name="image" required class="w-full px-4 py-2 rounded-xl border">
            <button type="submit" class="bg-brand text-white font-bold py-2 rounded-xl hover:opacity-90 transition">حفظ البانر</button>
        </form>
    </div>

    {{-- جدول العرض --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-right">
            <thead class="bg-gray-50 border-b text-gray-500 text-sm">
                <tr>
                    <th class="px-6 py-4">الصورة</th>
                    <th class="px-6 py-4">العنوان</th>
                    <th class="px-6 py-4">الحالة</th>
                    <th class="px-6 py-4">الإجراء</th>
                </tr>
            </thead>
            <tbody class="divide-y text-sm">
                @foreach($banners as $banner)
                <tr>
                    <td class="px-6 py-4">
                        <img src="{{ $banner->getFirstMediaUrl('banner_image') }}" class="w-20 h-12 object-cover rounded-lg shadow-sm">
                    </td>
                    <td class="px-6 py-4 font-bold">{{ $banner->title }}</td>
                    <td class="px-6 py-4">
                        <span class="{{ $banner->is_active ? 'text-green-600' : 'text-red-400' }}">
                            {{ $banner->is_active ? 'نشط' : 'معطل' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 flex gap-4">
                        {{-- زر الحذف --}}
                        <form action="{{ route('admin.hero-banners.destroy', $banner) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                            @csrf @method('DELETE')
                            <button class="text-red-500 hover:underline">حذف</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection