{{-- resources/views/admin/footer-texts/index.blade.php --}}
@extends('layouts.admin')
@section('title', 'نصوص الفوتر')

@section('admin-content')
<div class="max-w-5xl mx-auto space-y-6">

    @if(session('success'))
        <div class="p-4 rounded-xl bg-green-50 text-green-700">{{ session('success') }}</div>
    @endif

    <div class="bg-white p-6 rounded-2xl shadow-sm border">
        <h2 class="font-bold mb-4">إضافة نص جديد</h2>

        <form method="POST" action="{{ route('admin.footer-texts.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf

            <input type="text" name="slug" placeholder="footer_brand" class="border rounded-xl p-3" required>
            <input type="number" name="sort_order" placeholder="الترتيب" class="border rounded-xl p-3">

            <input type="text" name="text_ar" placeholder="النص بالعربي" class="border rounded-xl p-3" required>
            <input type="text" name="text_en" placeholder="Text in English" class="border rounded-xl p-3" required>

            <label class="flex items-center gap-2 md:col-span-2">
                <input type="checkbox" name="is_active" value="1" checked>
                <span>مفعّل</span>
            </label>

            <button type="submit" class="md:col-span-2 bg-black text-white px-5 py-3 rounded-xl">
                إضافة
            </button>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border overflow-hidden">
        <table class="w-full text-right">
            <thead class="bg-gray-50">
                <tr>
                    <th class="p-4">Slug</th>
                    <th class="p-4">العربي</th>
                    <th class="p-4">English</th>
                    <th class="p-4">الحالة</th>
                    <th class="p-4">إجراء</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    <tr class="border-t">
                        <td class="p-4 font-mono">{{ $item->slug }}</td>
                        <td class="p-4">{{ $item->getTranslation('text', 'ar') }}</td>
                        <td class="p-4">{{ $item->getTranslation('text', 'en') }}</td>
                        <td class="p-4">{{ $item->is_active ? 'مفعّل' : 'غير مفعّل' }}</td>
                        <td class="p-4">
                            <form method="POST" action="{{ route('admin.footer-texts.destroy', $item) }}"
                                  onsubmit="return confirm('حذف هذا النص؟')">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 font-semibold">حذف</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection