{{-- امسح الـ extends مؤقتاً إذا كان معطلاً --}}

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;900&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f9fafb; font-family: 'sans-serif'; }
        .font-logo { font-family: 'Montserrat', sans-serif; }
    </style>
</head>
<body class="p-4 md:p-12">

{{-- حجم الخط --}}
<div class="col-span-1">
    <label class="text-[10px] uppercase font-black text-gray-400 tracking-widest px-1">حجم الخط (مثلاً: 6xl)</label>
    <select name="splash_font_size" class="w-full bg-gray-50 border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-black">
        <option value="text-4xl" {{ ($settings['splash_font_size'] ?? '') == 'text-4xl' ? 'selected' : '' }}>صغير</option>
        <option value="text-6xl" {{ ($settings['splash_font_size'] ?? '') == 'text-6xl' ? 'selected' : '' }}>متوسط</option>
        <option value="text-8xl" {{ ($settings['splash_font_size'] ?? '') == 'text-8xl' ? 'selected' : '' }}>كبير جداً</option>
    </select>
</div>

{{-- نوع الخط --}}
<div class="col-span-1">
    <label class="text-[10px] uppercase font-black text-gray-400 tracking-widest px-1">نوع الخط</label>
    <select name="splash_font_family" class="w-full bg-gray-50 border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-black">
        <option value="'Montserrat', sans-serif" {{ ($settings['splash_font_family'] ?? '') == "'Montserrat', sans-serif" ? 'selected' : '' }}>Modern (Montserrat)</option>
        <option value="'Cairo', sans-serif" {{ ($settings['splash_font_family'] ?? '') == "'Cairo', sans-serif" ? 'selected' : '' }}>Arabic (Cairo)</option>
        <option value="serif" {{ ($settings['splash_font_family'] ?? '') == "serif" ? 'selected' : '' }}>Classic (Serif)</option>
    </select>
</div>
<div class="max-w-2xl mx-auto bg-white p-8 rounded-3xl shadow-xl border border-gray-100">
    <div class="flex items-center gap-4 mb-8">
        <div class="w-2 h-8 bg-black rounded-full"></div>
        <h2 class="text-2xl font-black uppercase tracking-tight">إعدادات الشاشة الافتتاحية</h2>
    </div>

    <form action="{{ route('admin.splash.update') }}" method="POST" class="space-y-6">
        @csrf @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- النصوص --}}
            <div class="space-y-2">
                <label class="text-[10px] uppercase font-black text-gray-400 tracking-widest px-1">النص الأساسي</label>
                <input type="text" name="splash_title_main" value="{{ $settings['splash_title_main'] ?? '' }}" 
                       class="w-full bg-gray-50 border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-black transition-all">
            </div>
            
            <div class="space-y-2">
                <label class="text-[10px] uppercase font-black text-gray-400 tracking-widest px-1">النص الفرعي</label>
                <input type="text" name="splash_title_sub" value="{{ $settings['splash_title_sub'] ?? '' }}" 
                       class="w-full bg-gray-50 border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-black transition-all">
            </div>

            {{-- الألوان --}}
            <div class="space-y-2">
                <label class="text-[10px] uppercase font-black text-gray-400 tracking-widest px-1">لون النص الأول</label>
                <div class="flex items-center gap-3 bg-gray-50 p-2 rounded-xl">
                    <input type="color" name="splash_color_main" value="{{ $settings['splash_color_main'] ?? '#000000' }}" 
                           class="w-12 h-10 border-none bg-transparent cursor-pointer">
                    <span class="text-xs font-mono text-gray-500 uppercase">{{ $settings['splash_color_main'] ?? '#000000' }}</span>
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] uppercase font-black text-gray-400 tracking-widest px-1">لون النص الثاني</label>
                <div class="flex items-center gap-3 bg-gray-50 p-2 rounded-xl">
                    <input type="color" name="splash_color_sub" value="{{ $settings['splash_color_sub'] ?? '#D1D5DB' }}" 
                           class="w-12 h-10 border-none bg-transparent cursor-pointer">
                    <span class="text-xs font-mono text-gray-500 uppercase">{{ $settings['splash_color_sub'] ?? '#D1D5DB' }}</span>
                </div>
            </div>

            {{-- جملة التحميل --}}
            <div class="col-span-1 md:col-span-2 space-y-2">
                <label class="text-[10px] uppercase font-black text-gray-400 tracking-widest px-1">رسالة التحميل</label>
                <input type="text" name="splash_loading_text" value="{{ $settings['splash_loading_text'] ?? '' }}" 
                       class="w-full bg-gray-50 border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-black transition-all">
            </div>
        </div>

        <div class="pt-4 flex items-center justify-between">
            <a href="/products" target="_blank" class="text-xs font-bold text-gray-400 hover:text-black transition-colors underline">معاينة الصفحة</a>
            
            <button type="submit" class="bg-black text-white px-8 py-3 rounded-xl font-bold hover:shadow-lg hover:shadow-black/20 transform active:scale-95 transition-all">
                حفظ التغييرات
            </button>
        </div>
    </form>
</div>

</body>
</html>