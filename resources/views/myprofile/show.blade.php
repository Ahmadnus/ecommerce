@extends('layouts.app')

@section('title', 'الملف الشخصي')

@push('head')
<style>
    .profile-card {
        background: #fff;
        border-radius: var(--radius-card);
        border: 1px solid var(--border);
        box-shadow: var(--shadow-card);
    }
    .input-field {
        width: 100%;
        padding: 12px 16px;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        background: #f9fafb;
        transition: all 0.2s;
    }
    .input-field:focus {
        border-color: var(--brand);
        background: #fff;
        box-shadow: 0 0 0 4px var(--brand-light);
        outline: none;
    }
    .order-item {
        border-bottom: 1px solid #f3f4f6;
        padding: 16px 0;
    }
    .order-item:last-child { border: none; }
</style>
@endpush

@section('content')

<div class="max-w-6xl mx-auto px-4 py-10" dir="rtl">
    
    <div class="flex flex-col md:flex-row gap-8">
        
        {{-- ─── القائمة الجانبية (Sidebar) ────────────────────────────────── --}}
        <div class="w-full md:w-1/3 space-y-6">
            <div class="profile-card p-6 text-center">
                <div class="w-24 h-24 bg-brand-50 text-brand-600 rounded-full flex items-center justify-center text-3xl font-bold mx-auto mb-4">
                    {{ substr($user->name, 0, 1) }}
                </div>
                <h2 class="font-display text-xl font-bold text-gray-900">{{ $user->name }}</h2>
                <p class="text-gray-500 text-sm mb-6">{{ $user->phone }}</p>
                
                <div class="space-y-2">
                    <a href="{{ route('orders.index') }}" class="flex items-center gap-3 w-full p-3 rounded-xl bg-gray-50 text-gray-700 hover:bg-brand-50 hover:text-brand-600 transition-colors font-medium">
                        <span class="text-xl">📦</span>
                        طلباتي
                    </a>
                    <a href="{{ route('wishlist.index') }}" class="flex items-center gap-3 w-full p-3 rounded-xl bg-gray-50 text-gray-700 hover:bg-brand-50 hover:text-brand-600 transition-colors font-medium">
                        <span class="text-xl">❤️</span>
                        المفضلة
                    </a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="flex items-center gap-3 w-full p-3 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 transition-colors font-medium mt-4">
                            <span class="text-xl">🚪</span>
                            تسجيل الخروج
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- ─── المحتوى الرئيسي ────────────────────────────────────────── --}}
        <div class="w-full md:w-2/3 space-y-8">
            
            {{-- معلوماتي --}}
            <div class="profile-card p-8">
                <h3 class="font-display text-2xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                    معلوماتي الشخصية
                    <span class="h-1 w-12 bg-brand rounded-full"></span>
                </h3>
                
                <form action="{{ route('myprofile.update') }}" method="POST" class="space-y-5">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-700">الاسم الكامل</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="input-field">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-700">رقم الهاتف</label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="input-field" dir="ltr">
                        </div>
                    </div>

                   

                    <button type="submit" class="w-full md:w-auto px-10 py-3 bg-brand-600 text-white font-bold rounded-xl hover:bg-brand-700 active:scale-95 transition-all shadow-lg shadow-brand-light">
                        حفظ التغييرات
                    </button>
                </form>
            </div>

            {{-- آخر الطلبات --}}
            <div class="profile-card p-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="font-display text-2xl font-bold text-gray-900">آخر الطلبات</h3>
                    <a href="{{ route('orders.index') }}" class="text-brand-600 font-bold text-sm hover:underline">مشاهدة الكل</a>
                </div>

                @if($orders->isEmpty())
                    <div class="text-center py-10">
                        <div class="text-5xl mb-4">🛍️</div>
                        <p class="text-gray-500">لم تقم بأي طلبات بعد</p>
                        <a href="/" class="text-brand-600 font-bold inline-block mt-2">ابدأ التسوق الآن</a>
                    </div>
                @else
                    <div class="space-y-1">
                        @foreach($orders as $order)
                        <div class="order-item flex items-center justify-between">
                            <div>
                                <p class="font-bold text-gray-900 text-lg">طلب #{{ $order->id }}</p>
                                <p class="text-sm text-gray-500">{{ $order->created_at->format('Y/m/d') }}</p>
                            </div>
                            <div class="text-left">
                                <p class="font-bold text-brand-600">${{ number_format($order->total_amount, 2) }}</p>
                                <span class="inline-block px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider 
                                    {{ $order->status == 'completed' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ $order->status_label }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection