@extends('layouts.admin')

@section('title', 'إعدادات المتجر')

@section('admin-content')
<div class="max-w-5xl mx-auto pb-20">
    {{-- Header --}}
    <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">إعدادات الهوية البصرية</h1>
            <p class="text-gray-500 mt-2 text-lg">تحكم بمظهر المتجر، الألوان، والشعار من مكان واحد.</p>
        </div>
        <div class="hidden md:block">
            <div class="bg-blue-50 text-blue-700 px-4 py-2 rounded-lg border border-blue-100 text-sm">
                <i class="fas fa-info-circle ml-1"></i> سيتم تطبيق التغييرات فور الحفظ.
            </div>
        </div>
    </div>

    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        
        {{-- القسم الأول: المعلومات الأساسية والشعار --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-50 bg-gray-50/50">
                <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <span class="w-8 h-8 bg-brand/10 text-brand rounded-lg flex items-center justify-center text-sm">01</span>
                    المعلومات الأساسية والشعار
                </h2>
            </div>
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    {{-- اسم المتجر --}}
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">اسم المتجر الرسمي</label>
                        <input type="text" name="site_name" 
                               value="{{ \App\Models\Setting::get('site_name', 'My Store') }}"
                               class="w-full border-gray-200 rounded-xl focus:ring-brand focus:border-brand p-3.5 bg-white shadow-sm transition-all"
                               placeholder="أدخل اسم المتجر هنا...">
                    </div>

                    {{-- الشعار --}}
                  {{-- الشعار --}}
<div class="space-y-4">
    <label class="block text-sm font-bold text-gray-700">شعار المتجر (Logo)</label>
    <div class="flex items-center gap-6">
        <div class="relative group">
            <div class="w-24 h-24 bg-gray-50 rounded-2xl flex items-center justify-center p-3 border-2 border-dashed border-gray-200 group-hover:border-brand transition-colors overflow-hidden">
                @if($logoUrl)
                    <img src="{{ $logoUrl }}"
                         class="max-h-full max-w-full object-contain"
                         alt="الشعار الحالي">
                @else
                    <i class="fas fa-image text-gray-300 text-2xl"></i>
                @endif
            </div>
        </div>
        <div class="flex-1">
            <input type="file" name="logo" accept="image/*"
                   class="block w-full text-sm text-gray-500
                          file:ml-4 file:py-2.5 file:px-4
                          file:rounded-xl file:border-0
                          file:text-sm file:font-bold
                          file:bg-brand file:text-white
                          hover:file:opacity-90 cursor-pointer">
            <p class="mt-2 text-xs text-gray-400">يفضل استخدام صيغة PNG بخلفية شفافة · الحد الأقصى 2MB</p>

            {{-- Favicon --}}
            <div class="mt-4 pt-4 border-t border-gray-100">
                <label class="block text-sm font-bold text-gray-700 mb-2">الفافيكون (Favicon)</label>
                @if($faviconUrl)
                    <div class="flex items-center gap-2 mb-2">
                        <img src="{{ $faviconUrl }}"
                             class="w-8 h-8 object-contain rounded border border-gray-200"
                             alt="الفافيكون الحالي">
                        <span class="text-xs text-gray-400">الفافيكون الحالي</span>
                    </div>
                @endif
                <input type="file" name="favicon" accept=".ico,.png,.svg"
                       class="block w-full text-sm text-gray-500
                              file:ml-4 file:py-2.5 file:px-4
                              file:rounded-xl file:border-0
                              file:text-sm file:font-bold
                              file:bg-gray-100 file:text-gray-700
                              hover:file:bg-gray-200 cursor-pointer">
                <p class="mt-1 text-xs text-gray-400">ICO أو PNG أو SVG · الحد الأقصى 512KB</p>
            </div>
        </div>
    </div>
</div>

        {{-- القسم الثاني: ألوان الثيم والموقع --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-50 bg-gray-50/50">
                <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <span class="w-8 h-8 bg-brand/10 text-brand rounded-lg flex items-center justify-center text-sm">02</span>
                    ألوان المتجر الرئيسية
                </h2>
            </div>
            <div class="p-8">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    {{-- اللون الأساسي --}}
                    <div class="p-4 rounded-2xl border border-gray-100 bg-gray-50/30 space-y-3">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">اللون الأساسي للبراند</label>
                        <input type="color" name="primary_color" 
                               value="{{ \App\Models\Setting::get('primary_color', '#0ea5e9') }}"
                               class="h-12 w-full rounded-lg cursor-pointer border-0 shadow-sm">
                    </div>

                    {{-- لون الخلفية --}}
                    <div class="p-4 rounded-2xl border border-gray-100 bg-gray-50/30 space-y-3">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">خلفية الموقع</label>
                        <input type="color" name="bg_color" 
                               value="{{ \App\Models\Setting::get('bg_color', '#f9fafb') }}"
                               class="h-12 w-full rounded-lg cursor-pointer border-0 shadow-sm">
                    </div>

                    {{-- لون الهيدر --}}
                    <div class="p-4 rounded-2xl border border-gray-100 bg-gray-50/30 space-y-3">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">شريط التنقل (Navbar)</label>
                        <input type="color" name="nav_bg_color" 
                               value="{{ \App\Models\Setting::get('nav_bg_color', '#ffffff') }}"
                               class="h-12 w-full rounded-lg cursor-pointer border-0 shadow-sm">
                    </div>

                    {{-- لون الكروت --}}
                    <div class="p-4 rounded-2xl border border-gray-100 bg-gray-50/30 space-y-3">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">كروت المنتجات</label>
                        <input type="color" name="card_bg_color" 
                               value="{{ \App\Models\Setting::get('card_bg_color', '#ffffff') }}"
                               class="h-12 w-full rounded-lg cursor-pointer border-0 shadow-sm">
                    </div>
                </div>
            </div>
        </div>

        {{-- القسم الثالث: إعدادات التذييل (Footer) --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-50 bg-gray-50/50">
                <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <span class="w-8 h-8 bg-brand/10 text-brand rounded-lg flex items-center justify-center text-sm">03</span>
                    ألوان التذييل (Footer)
                </h2>
            </div>
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-gray-600">خلفية الفوتر</label>
                        <input type="color" name="footer_bg_color" 
                               value="{{ \App\Models\Setting::get('footer_bg_color', '#111827') }}"
                               class="h-10 w-full rounded-lg cursor-pointer border shadow-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-gray-600">لون النصوص</label>
                        <input type="color" name="footer_text_color"
                               value="{{ \App\Models\Setting::get('footer_text_color', '#9ca3af') }}"
                               class="h-10 w-full rounded-lg cursor-pointer border shadow-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-gray-600">لون الروابط</label>
                        <input type="color" name="footer_link_color"
                               value="{{ \App\Models\Setting::get('footer_link_color', '#ffffff') }}"
                               class="h-10 w-full rounded-lg cursor-pointer border shadow-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-gray-600">الجملة الأخيرة</label>
                        <input type="color" name="footer_bottom_text_color"
                               value="{{ \App\Models\Setting::get('footer_bottom_text_color', '#6b7280') }}"
                               class="h-10 w-full rounded-lg cursor-pointer border shadow-sm">
                    </div>
                </div>
            </div>
        </div>

        {{-- أزرار التحكم --}}
        <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-100">
            <button type="reset" class="px-6 py-3 text-sm font-bold text-gray-500 hover:text-gray-700 transition-colors">
                إلغاء التغييرات
            </button>
            <button type="submit" class="bg-brand text-white px-12 py-3.5 rounded-xl font-bold shadow-xl shadow-brand/25 hover:scale-[1.02] active:scale-95 transition-all flex items-center gap-2">
                <i class="fas fa-save"></i>
                حفظ كافة التغييرات
            </button>
        </div>
    </form>
</div>

<style>
    /* لمسة جمالية لمربعات اختيار اللون لتبدو دائرية أو ناعمة */
    input[type="color"]::-webkit-color-swatch-wrapper { padding: 0; }
    input[type="color"]::-webkit-color-swatch { border: none; border-radius: 8px; }
</style>
@endsection