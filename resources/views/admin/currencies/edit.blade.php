@extends('layouts.admin')
@section('title', 'تعديل العملة: ' . $currency->code)

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

    <form action="{{ route('admin.currencies.update', $currency) }}" method="POST" class="space-y-6">
        @csrf @method('PUT')

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-7 space-y-5">
            <h2 class="font-bold text-gray-800 text-base border-b border-gray-100 pb-4">تعديل بيانات العملة</h2>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">اسم العملة <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $currency->name) }}" required
                       class="w-full border border-gray-200 rounded-xl p-3 bg-gray-50 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:border-brand transition-all">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">رمز العملة <span class="text-red-500">*</span></label>
                    <input type="text" name="code" value="{{ old('code', $currency->code) }}" required maxlength="10"
                           class="w-full border border-gray-200 rounded-xl p-3 bg-gray-50 text-sm font-mono uppercase focus:bg-white focus:outline-none focus:ring-2 focus:border-brand transition-all @error('code') border-red-400 @enderror">
                    @error('code')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">رمز العرض <span class="text-red-500">*</span></label>
                    <input type="text" name="symbol" value="{{ old('symbol', $currency->symbol) }}" required maxlength="10"
                           class="w-full border border-gray-200 rounded-xl p-3 bg-gray-50 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:border-brand transition-all">
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">سعر الصرف <span class="text-red-500">*</span></label>
                <input type="number" name="exchange_rate" value="{{ old('exchange_rate', $currency->exchange_rate) }}"
                       step="0.000001" min="0.000001" required
                       class="w-full border border-gray-200 rounded-xl p-3 bg-gray-50 text-sm font-mono focus:bg-white focus:outline-none focus:ring-2 focus:border-brand transition-all">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <label class="flex items-center gap-3 cursor-pointer p-3 border border-gray-200 rounded-xl bg-gray-50 hover:bg-white transition-colors">
                    <input type="checkbox" name="is_base" value="1"
                           {{ old('is_base', $currency->is_base) ? 'checked' : '' }}
                           class="w-5 h-5 text-amber-500 border-gray-300 rounded">
                    <div>
                        <p class="text-sm font-semibold text-gray-800">عملة أساسية</p>
                        <p class="text-xs text-gray-400">يُلغي الأساسية الأخرى</p>
                    </div>
                </label>
                <label class="flex items-center gap-3 cursor-pointer p-3 border border-gray-200 rounded-xl bg-gray-50 hover:bg-white transition-colors">
                    <input type="checkbox" name="is_active" value="1"
                           {{ old('is_active', $currency->is_active) ? 'checked' : '' }}
                           class="w-5 h-5 text-brand border-gray-300 rounded">
                    <div>
                        <p class="text-sm font-semibold text-gray-800">تفعيل العملة</p>
                        <p class="text-xs text-gray-400">تظهر في الإعدادات</p>
                    </div>
                </label>
            </div>
        </div>

        <div class="flex justify-between items-center">
            @if(!$currency->is_base)
            <form action="{{ route('admin.currencies.destroy', $currency) }}" method="POST"
                  onsubmit="return confirm('حذف عملة \'{{ addslashes($currency->name) }}\'؟')">
                @csrf @method('DELETE')
                <button type="submit"
                        class="flex items-center gap-2 text-sm text-red-500 hover:text-red-700 hover:bg-red-50 px-4 py-2.5 rounded-xl transition-colors font-semibold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    حذف العملة
                </button>
            </form>
            @else
            <div></div>
            @endif
            <div class="flex gap-3">
                <a href="{{ route('admin.currencies.index') }}"
                   class="px-6 py-2.5 text-sm font-bold text-gray-500 hover:text-red-500 transition-colors">إلغاء</a>
                <button type="submit"
                        class="bg-brand text-white px-8 py-2.5 rounded-xl font-bold text-sm shadow-lg hover:opacity-90 hover:scale-[1.02] transition-all active:scale-95">
                    حفظ التعديلات
                </button>
            </div>
        </div>
    </form>
</div>
@endsection