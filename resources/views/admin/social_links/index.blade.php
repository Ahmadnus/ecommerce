@extends('layouts.admin')

{{-- تأكد أن هذا الاسم هو 'admin-content' كما في صفحاتك الأخرى --}}
@section('admin-content') 
<div class="max-w-6xl mx-auto p-6 bg-white rounded-2xl shadow-sm" dir="rtl">
    <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
        <span class="w-2 h-6 bg-blue-600 rounded-full"></span>
        إدارة روابط السوشيال ميديا والزر العائم
    </h2>

    {{-- فورم الإضافة --}}
    <form action="{{ route('admin.social-links.store') }}" method="POST" class="bg-gray-50 p-6 rounded-xl mb-8 border border-gray-100">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="flex flex-col gap-1">
                <label class="text-sm font-bold text-gray-600 mr-2">اسم المنصة</label>
                <input type="text" name="platform_name" placeholder="مثلاً: واتساب" class="p-3 border rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" required>
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-sm font-bold text-gray-600 mr-2">الرابط (اختياري للواتساب)</label>
                <input type="url" name="url" placeholder="https://..." class="p-3 border rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-sm font-bold text-gray-600 mr-2">رقم الواتساب (للعائم)</label>
                <input type="text" name="whatsapp_number" placeholder="966500000000" class="p-3 border rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            <div class="flex flex-col gap-1 md:col-span-2 lg:col-span-2">
                <label class="text-sm font-bold text-gray-600 mr-2">كود الأيقونة (SVG)</label>
                <input type="text" name="icon_svg" placeholder="<svg>...</svg>" class="p-3 border rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            <div class="flex items-center gap-2 mt-auto mb-4">
                <input type="checkbox" name="is_floating" value="1" id="is_floating" class="w-5 h-5 accent-blue-600">
                <label for="is_floating" class="text-sm font-bold text-gray-700 select-none cursor-pointer">تفعيل كـ زر عائم</label>
            </div>
        </div>
        
        <button type="submit" class="mt-4 bg-blue-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-200">
            حفظ الرابط
        </button>
    </form>

    {{-- جدول العرض --}}
    <div class="overflow-x-auto">
        <table class="w-full text-right border-collapse">
            <thead class="bg-gray-100 text-gray-600">
                <tr>
                    <th class="p-4 rounded-r-xl">المنصة</th>
                    <th class="p-4">الرابط / الرقم</th>
                    <th class="p-4 text-center">نوع الظهور</th>
                    <th class="p-4 text-center">أيقونة</th>
                    <th class="p-4 rounded-l-xl text-center">إجراء</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($links as $link)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="p-4 font-bold text-gray-800">{{ $link->platform_name }}</td>
                    <td class="p-4">
                        @if($link->whatsapp_number)
                            <span class="text-green-600 font-mono text-sm block">📱 {{ $link->whatsapp_number }}</span>
                        @endif
                        @if($link->url)
                            <span class="text-blue-500 text-xs truncate max-w-[150px] block">{{ $link->url }}</span>
                        @endif
                    </td>
                    <td class="p-4 text-center">
                        <span class="{{ $link->is_floating ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-500' }} text-[10px] px-2 py-1 rounded-full font-bold">
                            {{ $link->is_floating ? 'عائم 🚀' : 'عادي' }}
                        </span>
                    </td>
                    <td class="p-4 text-center">
                        <div class="w-6 h-6 mx-auto text-gray-600">
                            {!! $link->icon_svg ?? '—' !!}
                        </div>
                    </td>
                    <td class="p-4 text-center">
                        <form action="{{ route('admin.social-links.destroy', $link->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد؟')">
                            @csrf @method('DELETE')
                            <button class="text-red-500 hover:text-red-700 p-2">حذف</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection