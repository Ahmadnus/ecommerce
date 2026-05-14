@extends('layouts.admin')

@section('title', 'تغيير كلمة السر')

@section('admin-content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 md:p-8">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">تغيير كلمة السر</h1>
            <p class="text-sm text-gray-500 mt-1">
                أدخل كلمة السر الحالية ثم اختر كلمة سر جديدة قوية.
            </p>
        </div>

        <form method="POST" action="{{ route('admin.password.update') }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">كلمة السر الحالية</label>
                <input
                    type="password"
                    name="current_password"
                    class="w-full rounded-xl border-gray-300 focus:border-brand focus:ring-brand"
                    placeholder="••••••••"
                    required
                >
                @error('current_password')
                    <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">كلمة السر الجديدة</label>
                <input
                    type="password"
                    name="password"
                    class="w-full rounded-xl border-gray-300 focus:border-brand focus:ring-brand"
                    placeholder="••••••••"
                    required
                >
                @error('password')
                    <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">تأكيد كلمة السر الجديدة</label>
                <input
                    type="password"
                    name="password_confirmation"
                    class="w-full rounded-xl border-gray-300 focus:border-brand focus:ring-brand"
                    placeholder="••••••••"
                    required
                >
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('admin.dashboard') }}"
                   class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 text-sm font-semibold hover:bg-gray-50 transition-colors">
                    إلغاء
                </a>

                <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-brand text-white text-sm font-semibold hover:opacity-90 transition-opacity">
                    حفظ التغيير
                </button>
            </div>
        </form>
    </div>
</div>
@endsection