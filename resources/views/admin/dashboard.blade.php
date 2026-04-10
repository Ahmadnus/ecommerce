@extends('layouts.admin')
@section('title', 'لوحة التحكم')

@section('admin-content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-800">نظرة عامة</h2>
    <p class="text-gray-500 text-sm">مرحباً بك مجدداً، إليك ملخص أداء المتجر.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    {{-- كرت المنتجات --}}
    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex items-center gap-4">
        <div class="p-4 bg-blue-50 text-blue-600 rounded-2xl">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
        </div>
        <div>
            <div class="text-gray-400 text-sm font-medium">إجمالي المنتجات</div>
            <div class="text-3xl font-bold text-gray-900">{{ \App\Models\Product::count() }}</div>
        </div>
    </div>

    {{-- كرت المستخدمين --}}
    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex items-center gap-4">
        <div class="p-4 bg-green-50 text-green-600 rounded-2xl">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
        </div>
        <div>
            <div class="text-gray-400 text-sm font-medium">إجمالي المستخدمين</div>
            <div class="text-3xl font-bold text-gray-900">{{ \App\Models\User::count() }}</div>
        </div>
    </div>

    {{-- كرت التصنيفات --}}
    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex items-center gap-4">
        <div class="p-4 bg-amber-50 text-amber-600 rounded-2xl">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
        </div>
        <div>
            <div class="text-gray-400 text-sm font-medium">التصنيفات</div>
            <div class="text-3xl font-bold text-gray-900">{{ \App\Models\Category::count() }}</div>
        </div>
    </div>
</div>

{{-- إضافة قسم سريع لعرض آخر المنتجات المضافة --}}
<div class="mt-8">
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-gray-800">آخر المنتجات المضافة</h3>
            <a href="{{ route('admin.products.index') }}" class="text-sm text-brand font-bold hover:underline">عرض الكل</a>
        </div>
        <div class="p-0">
            <table class="w-full text-right text-sm">
                <tbody class="divide-y divide-gray-50">
                    @foreach(\App\Models\Product::latest()->take(5)->get() as $p)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-4 flex items-center gap-3">
                            <img src="{{ $p->getFirstMediaUrl('products') ?: asset('default.png') }}" class="w-8 h-8 rounded-lg object-cover">
                            <span class="font-medium">{{ $p->name }}</span>
                        </td>
                       <td class="px-6 py-4 text-gray-500">{{ $p->category->first()->name ?? 'بدون تصنيف' }}</td>
                        <td class="px-6 py-4 font-bold">${{ $p->price }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection