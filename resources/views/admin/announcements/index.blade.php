@extends('layouts.admin')
@section('title', 'إدارة شريط الإعلانات')

@section('admin-content')
<div class="max-w-4xl mx-auto">

    {{-- Session feedback --}}
    @if(session('success'))
    <div class="mb-4 p-3 bg-green-50 border border-green-100 rounded-xl text-sm text-green-700">
        {{ session('success') }}
    </div>
    @endif

    {{-- ─── Add Form ─── --}}
    <div class="bg-white p-6 rounded-2xl shadow-sm mb-8 border border-gray-100">
        <h3 class="text-lg font-bold mb-4">إضافة نص جديد للبانر</h3>

        <form action="{{ route('admin.announcements.store') }}" method="POST" class="space-y-3">
            @csrf

            {{-- Arabic content --}}
            <div class="flex items-center gap-3">
                <span class="w-8 text-xs font-bold text-gray-400 text-center">AR</span>
                <input type="text" name="content[ar]"
                       value="{{ old('content.ar') }}"
                       placeholder="مثلاً: 🚚 شحن مجاني فوق 50 د.أ"
                       required dir="rtl"
                       class="flex-1 px-4 py-2 rounded-xl border focus:ring-2 focus:ring-brand outline-none
                              @error('content.ar') border-red-400 @enderror">
                @error('content.ar')
                    <p class="text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- English content --}}
            <div class="flex items-center gap-3">
                <span class="w-8 text-xs font-bold text-gray-400 text-center">EN</span>
                <input type="text" name="content[en]"
                       value="{{ old('content.en') }}"
                       placeholder="e.g. 🚚 Free shipping over $50"
                       required dir="ltr"
                       class="flex-1 px-4 py-2 rounded-xl border focus:ring-2 focus:ring-brand outline-none
                              @error('content.en') border-red-400 @enderror">
                @error('content.en')
                    <p class="text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3">
                {{-- Sort order --}}
                <input type="number" name="sort_order"
                       value="{{ old('sort_order', 0) }}"
                       placeholder="الترتيب"
                       class="w-28 px-4 py-2 rounded-xl border focus:ring-2 focus:ring-brand outline-none">

                {{-- Active by default on create --}}
                <input type="hidden" name="is_active" value="1">

                <button type="submit"
                        class="bg-brand text-white px-6 py-2 rounded-xl font-bold hover:opacity-90 transition">
                    إضافة
                </button>
            </div>
        </form>
    </div>

    {{-- ─── Announcements Table ─── --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-right">
            <thead class="bg-gray-50 border-b text-gray-500 text-sm">
                <tr>
                    <th class="px-6 py-4">النص (حسب اللغة الحالية)</th>
                    <th class="px-6 py-4">AR / EN</th>
                    <th class="px-6 py-4">الترتيب</th>
                    <th class="px-6 py-4">الحالة</th>
                    <th class="px-6 py-4">الإجراء</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($announcements as $item)
                <tr>
                    {{-- Auto-resolves to current app locale --}}
                    <td class="px-6 py-4 font-medium">{{ $item->content }}</td>

                    {{-- Show both translations for admin context --}}
                    <td class="px-6 py-4 text-xs text-gray-400 space-y-0.5">
                        <div dir="rtl">{{ $item->getTranslation('content', 'ar') }}</div>
                        <div dir="ltr">{{ $item->getTranslation('content', 'en') }}</div>
                    </td>

                    <td class="px-6 py-4">{{ $item->sort_order }}</td>

                    {{-- Active toggle --}}
                    <td class="px-6 py-4">
                        <form action="{{ route('admin.announcements.update', $item) }}" method="POST">
                            @csrf @method('PUT')
                            {{-- Preserve existing translations when toggling --}}
                            <input type="hidden" name="content[ar]" value="{{ $item->getTranslation('content', 'ar') }}">
                            <input type="hidden" name="content[en]" value="{{ $item->getTranslation('content', 'en') }}">
                            <input type="hidden" name="sort_order"  value="{{ $item->sort_order }}">
                            <input type="hidden" name="is_active"   value="{{ $item->is_active ? 0 : 1 }}">
                            <button type="submit"
                                    class="text-sm font-semibold {{ $item->is_active ? 'text-green-600 hover:text-red-500' : 'text-red-500 hover:text-green-600' }} transition-colors">
                                {{ $item->is_active ? 'نشط' : 'متوقف' }}
                            </button>
                        </form>
                    </td>

                    {{-- Delete --}}
                    <td class="px-6 py-4">
                        <form action="{{ route('admin.announcements.destroy', $item) }}"
                              method="POST"
                              onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                            @csrf @method('DELETE')
                            <button class="text-red-500 hover:text-red-700 text-sm font-semibold transition-colors">
                                حذف
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-10 text-center text-gray-400 text-sm">
                        لا توجد إعلانات حتى الآن
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection