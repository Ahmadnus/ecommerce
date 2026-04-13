{{-- resources/views/admin/currencies/create.blade.php --}}
@extends('layouts.admin')
@section('title', 'إضافة عملة')

@section('admin-content')
<div class="max-w-lg mx-auto">

    <div class="mb-6">
        <a href="{{ route('admin.currencies.index') }}"
           class="inline-flex items-center gap-1.5 text-sm font-semibold text-gray-500 hover:text-brand transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            العودة للعملات
        </a>
    </div>

    @if($errors->any())
    <div class="mb-6 p-4 bg-red-50 border border-red-100 rounded-xl text-sm text-red-600">
        <ul class="list-disc list-inside space-y-0.5">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.currencies.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-7 space-y-5">
            <h2 class="font-bold text-gray-800 text-base border-b border-gray-100 pb-4">بيانات العملة</h2>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    اسم العملة <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name') }}"
                       required placeholder="الليرة السورية"
                       class="w-full border border-gray-200 rounded-xl p-3 bg-gray-50 text-sm
                              focus:bg-white focus:outline-none focus:ring-2 focus:border-brand transition-all
                              @error('name') border-red-400 @enderror">
                @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        رمز العملة (ISO) <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="code" value="{{ old('code') }}"
                           required maxlength="10" placeholder="SYP"
                           class="w-full border border-gray-200 rounded-xl p-3 bg-gray-50 text-sm font-mono uppercase
                                  focus:bg-white focus:outline-none focus:ring-2 focus:border-brand transition-all
                                  @error('code') border-red-400 @enderror">
                    @error('code')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        رمز العرض <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="symbol" value="{{ old('symbol') }}"
                           required maxlength="10" placeholder="ل.س"
                           class="w-full border border-gray-200 rounded-xl p-3 bg-gray-50 text-sm
                                  focus:bg-white focus:outline-none focus:ring-2 focus:border-brand transition-all
                                  @error('symbol') border-red-400 @enderror">
                    @error('symbol')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    سعر الصرف (مقابل العملة الأساسية) <span class="text-red-500">*</span>
                </label>
                <input type="number" name="exchange_rate" value="{{ old('exchange_rate', '1.000000') }}"
                       step="0.000001" min="0.000001" required
                       class="w-full border border-gray-200 rounded-xl p-3 bg-gray-50 text-sm font-mono
                              focus:bg-white focus:outline-none focus:ring-2 focus:border-brand transition-all
                              @error('exchange_rate') border-red-400 @enderror">
                <p class="text-xs text-gray-400 mt-1">
                    مثال: إذا كانت العملة الأساسية هي USD، واردت إضافة الليرة السورية — اكتب 13200
                </p>
                @error('exchange_rate')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <label class="flex items-center gap-3 cursor-pointer p-3 border border-gray-200 rounded-xl bg-gray-50 hover:bg-white transition-colors">
                    <input type="checkbox" name="is_base" value="1"
                           {{ old('is_base') ? 'checked' : '' }}
                           class="w-5 h-5 text-amber-500 border-gray-300 rounded focus:ring-amber-400/30">
                    <div>
                        <p class="text-sm font-semibold text-gray-800">عملة أساسية</p>
                        <p class="text-xs text-gray-400">يُلغي الأساسية الأخرى</p>
                    </div>
                </label>
                <label class="flex items-center gap-3 cursor-pointer p-3 border border-gray-200 rounded-xl bg-gray-50 hover:bg-white transition-colors">
                    <input type="checkbox" name="is_active" value="1"
                           {{ old('is_active', true) ? 'checked' : '' }}
                           class="w-5 h-5 text-brand border-gray-300 rounded focus:ring-brand/30">
                    <div>
                        <p class="text-sm font-semibold text-gray-800">تفعيل العملة</p>
                        <p class="text-xs text-gray-400">تظهر في الإعدادات</p>
                    </div>
                </label>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.currencies.index') }}"
               class="px-6 py-2.5 text-sm font-bold text-gray-500 hover:text-red-500 transition-colors">إلغاء</a>
            <button type="submit"
                    class="bg-brand text-white px-8 py-2.5 rounded-xl font-bold text-sm shadow-lg hover:opacity-90 hover:scale-[1.02] transition-all active:scale-95">
                حفظ العملة
            </button>
        </div>
    </form>
</div>
@endsection