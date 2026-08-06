@extends('layouts.admin')
@section('title', 'لوحة التحكم')

@section('admin-content')
<div class="space-y-8">
    {{-- هيدر الصفحة بتصميم هادئ --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-black text-gray-900 tracking-tight">نظرة عامة</h2>
            <p class="text-gray-500 font-medium mt-1">مرحباً بك، إليك ما يحدث في متجرك الآن.</p>
        </div>
        <div class="flex items-center gap-3 bg-white p-1.5 rounded-2xl border border-gray-200 shadow-sm">
            <span class="flex items-center gap-2 px-4 py-2 text-sm font-bold text-gray-600">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                النظام يعمل بكفاءة
            </span>
        </div>
    </div>

    @php
        $stats = [
            [
                'label' => 'إجمالي المنتجات',
                'value' => \App\Models\Product::count(),
                'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z',
                'bg' => 'bg-blue-50',
                'text' => 'text-blue-600',
                'hoverBg' => 'group-hover:bg-blue-600',
            ],
            [
                'label' => 'إجمالي المستخدمين',
                'value' => \App\Models\User::count(),
                'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
                'bg' => 'bg-emerald-50',
                'text' => 'text-emerald-600',
                'hoverBg' => 'group-hover:bg-emerald-600',
            ],
            [
                'label' => 'التصنيفات',
                'value' => \App\Models\Category::count(),
                'icon' => 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z',
                'bg' => 'bg-amber-50',
                'text' => 'text-amber-600',
                'hoverBg' => 'group-hover:bg-amber-600',
            ],
        ];

        $orderStats = [
            [
                'label' => 'إجمالي الطلبات',
                'value' => \App\Models\Order::count(),
                'bg' => 'bg-slate-50',
                'text' => 'text-slate-600',
            ],
            [
                'label' => 'قيد المعالجة',
                'value' => \App\Models\Order::where('status', 'processing')->count(),
                'bg' => 'bg-yellow-50',
                'text' => 'text-yellow-700',
            ],
            [
                'label' => 'قيد التوصيل',
                'value' => \App\Models\Order::where('status', 'shipped')->count(),
                'bg' => 'bg-blue-50',
                'text' => 'text-blue-700',
            ],
            [
                'label' => 'تم التسليم',
                'value' => \App\Models\Order::where('status', 'delivered')->count(),
                'bg' => 'bg-emerald-50',
                'text' => 'text-emerald-700',
            ],
            [
                'label' => 'ملغي',
                'value' => \App\Models\Order::where('status', 'cancelled')->count(),
                'bg' => 'bg-red-50',
                'text' => 'text-red-600',
            ],
        ];
    @endphp

    {{-- شبكة الإحصائيات --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($stats as $stat)
        <div class="relative group overflow-hidden bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
            <div class="absolute -right-4 -top-4 w-24 h-24 {{ $stat['bg'] }} rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500"></div>

            <div class="relative flex items-center gap-6">
                <div class="flex-shrink-0 w-14 h-14 {{ $stat['bg'] }} {{ $stat['text'] }} rounded-2xl flex items-center justify-center {{ $stat['hoverBg'] }} group-hover:text-white transition-colors duration-300">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stat['icon'] }}"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-1">{{ $stat['label'] }}</p>
                    <h3 class="text-4xl font-black text-gray-900 leading-none">{{ number_format($stat['value']) }}</h3>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- حالات الطلبات --}}
    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-8 border-b border-gray-50 flex justify-between items-center bg-gray-50/30">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-brand/10 text-brand rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6M9 8h6M5 4h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-black text-gray-800">حالات الطلبات</h3>
            </div>
        </div>

        <div class="p-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            @foreach($orderStats as $stat)
            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="w-11 h-11 rounded-2xl {{ $stat['bg'] }} {{ $stat['text'] }} flex items-center justify-center mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3M4 6h16M6 6v12m12-12v12"></path>
                    </svg>
                </div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">{{ $stat['label'] }}</p>
                <div class="text-3xl font-black text-gray-900">{{ number_format($stat['value']) }}</div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- جدول المنتجات: احترافي ونظيف --}}
    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-8 border-b border-gray-50 flex justify-between items-center bg-gray-50/30">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-brand/10 text-brand rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-black text-gray-800">أحدث الإضافات</h3>
            </div>
            <a href="{{ route('admin.products.index') }}" class="px-5 py-2 text-sm font-bold text-brand hover:bg-brand/5 rounded-xl transition-colors">
                عرض الكتالوج كاملاً
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead>
                    <tr class="text-gray-400 text-xs font-bold uppercase tracking-tighter border-b border-gray-50">
                        <th class="px-8 py-5">المنتج</th>
                        <th class="px-8 py-5">التصنيف</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse(\App\Models\Product::latest()->take(5)->get() as $p)
                    <tr class="group hover:bg-gray-50/80 transition-all duration-200">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-4">
                                <div class="relative flex-shrink-0 w-12 h-12">
                                    <img src="{{ $p->getFirstMediaUrl('products') ?: asset('default.png') }}"
                                         class="w-full h-full rounded-2xl object-cover shadow-sm group-hover:shadow-md transition-shadow">
                                </div>
                                <div>
                                    <div class="font-bold text-gray-900 group-hover:text-brand transition-colors">{{ $p->name }}</div>
                                    <div class="text-xs text-gray-400 font-medium">تمت الإضافة {{ $p->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            <span class="inline-flex items-center px-3 py-1 rounded-lg bg-gray-100 text-gray-600 text-xs font-bold group-hover:bg-white transition-colors">
                                {{ $p->category->first()->name ?? 'عام' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-8 py-20 text-center">
                            <div class="text-gray-300 font-bold">لا توجد بيانات لعرضها حالياً</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection