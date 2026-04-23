@extends('layouts.admin')

@section('admin-content') 
<div class="max-w-6xl mx-auto p-6 bg-white rounded-2xl shadow-sm" dir="rtl">
    <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
        <span class="w-2 h-6 bg-black rounded-full"></span>
        إعدادات الشاشة الافتتاحية
    </h2>

    <form action="{{ route('admin.splash.update') }}" method="POST" class="space-y-6">
        @csrf 
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 p-6 rounded-xl border border-gray-100">
            
            {{-- النص الأساسي --}}
            <div class="flex flex-col gap-1">
                <label class="text-sm font-bold text-gray-600 mr-2">النص الأساسي</label>
                <input type="text" name="splash_title_main" value="{{ $settings['splash_title_main'] ?? '' }}" 
                       class="p-3 border rounded-xl focus:ring-2 focus:ring-black outline-none transition-all">
            </div>

            {{-- النص الفرعي --}}
            <div class="flex flex-col gap-1">
                <label class="text-sm font-bold text-gray-600 mr-2">النص الفرعي</label>
                <input type="text" name="splash_title_sub" value="{{ $settings['splash_title_sub'] ?? '' }}" 
                       class="p-3 border rounded-xl focus:ring-2 focus:ring-black outline-none transition-all">
            </div>

            {{-- نوع الخط (كان خارج الفورم والآن داخله) --}}
            <div class="flex flex-col gap-1">
                <label class="text-sm font-bold text-gray-600 mr-2">نوع الخط</label>
                <select name="splash_font_family" class="p-3 border rounded-xl focus:ring-2 focus:ring-black outline-none bg-white font-mono">
                    <option value="'Montserrat', sans-serif" {{ ($settings['splash_font_family'] ?? '') == "'Montserrat', sans-serif" ? 'selected' : '' }}>Modern (Montserrat)</option>
                    <option value="'Cairo', sans-serif" {{ ($settings['splash_font_family'] ?? '') == "'Cairo', sans-serif" ? 'selected' : '' }}>Arabic (Cairo)</option>
                    <option value="serif" {{ ($settings['splash_font_family'] ?? '') == "serif" ? 'selected' : '' }}>Classic (Serif)</option>
                </select>
            </div>

            {{-- حجم الخط (كان خارج الفورم والآن داخله) --}}
            <div class="flex flex-col gap-1">
                <label class="text-sm font-bold text-gray-600 mr-2">حجم الخط</label>
                <select name="splash_font_size" class="p-3 border rounded-xl focus:ring-2 focus:ring-black outline-none bg-white">
                    <option value="text-4xl" {{ ($settings['splash_font_size'] ?? '') == 'text-4xl' ? 'selected' : '' }}>صغير (4xl)</option>
                    <option value="text-6xl" {{ ($settings['splash_font_size'] ?? '') == 'text-6xl' ? 'selected' : '' }}>متوسط (6xl)</option>
                    <option value="text-8xl" {{ ($settings['splash_font_size'] ?? '') == 'text-8xl' ? 'selected' : '' }}>كبير جداً (8xl)</option>
                </select>
            </div>

            {{-- لون النص الأول --}}
            <div class="flex flex-col gap-1">
                <label class="text-sm font-bold text-gray-600 mr-2">لون النص الأول</label>
                <div class="flex items-center gap-3 bg-white p-2 border rounded-xl">
                    <input type="color" name="splash_color_main" value="{{ $settings['splash_color_main'] ?? '#000000' }}" 
                           class="w-10 h-10 border-none bg-transparent cursor-pointer">
                    <span class="text-xs font-mono text-gray-500">{{ $settings['splash_color_main'] ?? '#000000' }}</span>
                </div>
            </div>

            {{-- لون النص الثاني --}}
            <div class="flex flex-col gap-1">
                <label class="text-sm font-bold text-gray-600 mr-2">لون النص الثاني</label>
                <div class="flex items-center gap-3 bg-white p-2 border rounded-xl">
                    <input type="color" name="splash_color_sub" value="{{ $settings['splash_color_sub'] ?? '#D1D5DB' }}" 
                           class="w-10 h-10 border-none bg-transparent cursor-pointer">
                    <span class="text-xs font-mono text-gray-500">{{ $settings['splash_color_sub'] ?? '#D1D5DB' }}</span>
                </div>
            </div>

            {{-- رسالة التحميل --}}
            <div class="flex flex-col gap-1 md:col-span-2">
                <label class="text-sm font-bold text-gray-600 mr-2">رسالة التحميل</label>
                <input type="text" name="splash_loading_text" value="{{ $settings['splash_loading_text'] ?? '' }}" 
                       class="p-3 border rounded-xl focus:ring-2 focus:ring-black outline-none transition-all placeholder-gray-300">
            </div>
        </div>

        <div class="pt-4 flex items-center justify-between">
            <a href="/products" target="_blank" class="text-xs font-bold text-gray-400 hover:text-black transition-colors underline">معاينة الصفحة</a>
            
            <button type="submit" class="bg-black text-white px-10 py-3 rounded-xl font-bold hover:shadow-lg hover:shadow-black/20 transform active:scale-95 transition-all">
                حفظ التغييرات
            </button>
        </div>
    </form>
</div>
@endsection