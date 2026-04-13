@extends('layouts.admin')

@section('title', 'إعدادات المتجر')

@section('admin-content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">إعدادات الهوية</h1>
        <p class="text-gray-500 mt-2">تحكم بمظهر المتجر، الألوان، والشعار من هنا.</p>
    </div>

    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        
        <div class="bg-white p-8 rounded-2xl border border-gray-200 shadow-sm space-y-8">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                {{-- تحكم باللون --}}
                <div class="space-y-4">
                    <label class="block text-sm font-bold text-gray-700">اللون الأساسي للثيم</label>
                    <div class="flex items-center gap-4 p-4 border border-gray-100 rounded-xl bg-gray-50">
                        <input type="color" name="primary_color" 
                               value="{{ \App\Models\Setting::get('primary_color', '#0ea5e9') }}"
                               class="h-14 w-20 rounded-lg border-none cursor-pointer bg-transparent">
                        <div>
                            <span class="block text-sm font-medium text-gray-900">اختر لون البراند</span>
                            <span class="text-xs text-gray-400">سيتم تطبيقه على الأزرار والعناصر الرئيسية</span>
                        </div>
                    </div>
                </div>

                {{-- تحكم بالشعار --}}
                <div class="space-y-4">
                    <label class="block text-sm font-bold text-gray-700">شعار المتجر (Logo)</label>
                    <div class="space-y-4">
                        {{-- عرض الشعار الحالي --}}
                        @php $logo = \App\Models\Setting::get('site_logo'); @endphp
                        @if($logo)
                            <div class="w-32 h-16 bg-gray-100 rounded-lg flex items-center justify-center p-2 border border-gray-100">
                                <img src="{{ asset('storage/' . $logo) }}" class="max-h-full">
                            </div>
                        @endif

                        <input type="file" name="logo" class="block w-full text-sm text-gray-500
                            file:ml-4 file:py-2.5 file:px-4
                            file:rounded-xl file:border-0
                            file:text-sm file:font-bold
                            file:bg-brand file:text-white
                            hover:file:opacity-90 cursor-pointer">
                    </div>
                </div>
            </div>
{{-- أضف هذه الحقول داخل الفورم في صفحة admin/settings.blade.php --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    {{-- اللون الأساسي --}}
    <div class="space-y-2">
        <label class="block text-sm font-bold text-gray-700">اللون الأساسي (أزرار وعناصر)</label>
        <input type="color" name="primary_color" 
               value="{{ \App\Models\Setting::get('primary_color', '#0ea5e9') }}"
               class="h-12 w-full rounded-lg cursor-pointer">
    </div>

    {{-- لون الخلفية --}}
    <div class="space-y-2">
        <label class="block text-sm font-bold text-gray-700">لون خلفية الموقع</label>
        <input type="color" name="bg_color" 
               value="{{ \App\Models\Setting::get('bg_color', '#f9fafb') }}"
               class="h-12 w-full rounded-lg cursor-pointer">
    </div>

    {{-- لون شريط التنقل --}}
    <div class="space-y-2">
        <label class="block text-sm font-bold text-gray-700">لون الهيدر (Navbar)</label>
        <input type="color" name="nav_bg_color" 
               value="{{ \App\Models\Setting::get('nav_bg_color', '#ffffff') }}"
               class="h-12 w-full rounded-lg cursor-pointer">
    </div>
</div>
<div class="form-group">
        <label>لون خلفية الكروت (Products Cards):</label>
        <input type="color" name="card_bg_color" value="{{ $siteSettings['card_bg_color'] ?? '#ffffff' }}">
    </div>

    <label>لون الفوتر:</label>
<input type="color" name="footer_bg_color" value="{{ $siteSettings['footer_bg_color'] ?? '#111827' }}">
            {{-- خيارات إضافية (مثال) --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">اسم المتجر</label>
                <input type="text" name="site_name" value="{{ \App\Models\Setting::get('site_name', 'My Store') }}"
                       class="w-full border-gray-200 rounded-xl focus:ring-brand focus:border-brand p-3 bg-gray-50">
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-brand text-white px-10 py-3 rounded-xl font-bold shadow-lg shadow-brand/20 hover:scale-105 transition-all">
                حفظ كافة التغييرات
            </button>
        </div>
    </form>
</div>
@endsection