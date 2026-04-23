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

    {{-- شبكة الإحصائيات: تصميم Minimalist بلمسات فنية --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @php
            $stats = [
                [
                    'label' => 'إجمالي المنتجات',
                    'value' => \App\Models\Product::count(),
                    'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z',
                    'color' => 'blue'
                ],
                [
                    'label' => 'إجمالي المستخدمين',
                    'value' => \App\Models\User::count(),
                    'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
                    'color' => 'emerald'
                ],
                [
                    'label' => 'التصنيفات',
                    'value' => \App\Models\Category::count(),
                    'icon' => 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z',
                    'color' => 'amber'
                ]
            ];
        @endphp

        @foreach($stats as $stat)
        <div class="relative group overflow-hidden bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
            {{-- تأثير خلفية ناعم --}}
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-{{ $stat['color'] }}-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500"></div>
            
            <div class="relative flex items-center gap-6">
                <div class="flex-shrink-0 w-14 h-14 bg-{{ $stat['color'] }}-50 text-{{ $stat['color'] }}-600 rounded-2xl flex items-center justify-center group-hover:bg-{{ $stat['color'] }}-600 group-hover:text-white transition-colors duration-300">
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

    {{-- جدول المنتجات: احترافي ونظيف --}}
    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-8 border-b border-gray-50 flex justify-between items-center bg-gray-50/30">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-brand/10 text-brand rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
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