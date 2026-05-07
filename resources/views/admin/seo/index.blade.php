@extends('layouts.admin')

@section('title', 'إعدادات SEO')

@section('admin-content')
<div class="max-w-4xl mx-auto">

    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">إعدادات SEO</h1>
            <p class="text-sm text-gray-500 mt-1">إدارة الوسوم التعريفية لكل تخطيط صفحة</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach(['main' => 'الموقع الرئيسي', 'splash' => 'شاشة الترحيب'] as $type => $label)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex flex-col gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="inline-block w-2 h-2 rounded-full bg-green-400"></span>
                        <h2 class="text-base font-bold text-gray-900">{{ $label }}</h2>
                    </div>
                    <p class="text-sm text-gray-500">
                        إدارة عنوان SEO والوصف والصورة والفافيكون وبطاقة تويتر
                        لتخطيط <strong class="text-gray-700">{{ $label }}</strong>.
                    </p>
                </div>
                <a href="{{ route('admin.seo.edit', $type) }}"
                   class="inline-flex items-center gap-2 bg-black text-white text-xs font-semibold px-4 py-2 rounded-lg hover:bg-gray-800 transition self-start">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    تعديل SEO
                </a>
            </div>
        @endforeach
    </div>

</div>
@endsection