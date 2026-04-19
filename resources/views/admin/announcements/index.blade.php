@extends('layouts.admin') {{-- تأكد من اسم ملف الليأوت عندك --}}
@section('title', 'إدارة شريط الإعلانات')

@section('admin-content')
<div class="max-w-4xl mx-auto">
    {{-- إضافة نص جديد --}}
    <div class="bg-white p-6 rounded-2xl shadow-sm mb-8 border border-gray-100">
        <h3 class="text-lg font-bold mb-4">إضافة نص جديد للبانر</h3>
        <form action="{{ route('admin.announcements.store') }}" method="POST" class="flex gap-4">
            @csrf
            <div class="flex-1">
                <input type="text" name="content" placeholder="مثلاً: 🚚 شحن مجاني فوق $50" required
                       class="w-full px-4 py-2 rounded-xl border focus:ring-2 focus:ring-brand outline-none">
            </div>
            <div class="w-24">
                <input type="number" name="sort_order" placeholder="الترتيب" value="0"
                       class="w-full px-4 py-2 rounded-xl border focus:ring-2 focus:ring-brand outline-none">
            </div>
            <button type="submit" class="bg-brand text-white px-6 py-2 rounded-xl font-bold hover:opacity-90 transition">
                إضافة
            </button>
        </form>
    </div>

    {{-- القائمة الحالية --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-right">
            <thead class="bg-gray-50 border-b text-gray-500 text-sm">
                <tr>
                    <th class="px-6 py-4">النص</th>
                    <th class="px-6 py-4">الترتيب</th>
                    <th class="px-6 py-4">الحالة</th>
                    <th class="px-6 py-4">الإجراء</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($announcements as $item)
                <tr>
                    <td class="px-6 py-4 font-medium">{{ $item->content }}</td>
                    <td class="px-6 py-4">{{ $item->sort_order }}</td>
                    <td class="px-6 py-4">
                        <span class="{{ $item->is_active ? 'text-green-600' : 'text-red-500' }}">
                            {{ $item->is_active ? 'نشط' : 'متوقف' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 flex gap-2">
                        <form action="{{ route('admin.announcements.destroy', $item) }}" method="POST" onsubmit="return confirm('هل أنت متأكد؟')">
                            @csrf @method('DELETE')
                            <button class="text-red-500 hover:text-red-700">حذف</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection