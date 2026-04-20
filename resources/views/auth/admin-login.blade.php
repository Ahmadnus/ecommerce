@extends('layouts.app')
@section('title', 'دخول الإدارة')

@push('head')
<style>
    :root {
        --admin-navy: #0f172a;
        --admin-navy-light: #1e293b;
        --admin-accent: #38bdf8;
    }

    .admin-auth-bg {
        background: radial-gradient(circle at top right, #1e293b, #0f172a);
    }

    .input-field {
        transition: all 0.2s ease;
        background: #f8fafc;
    }

    .input-field:focus {
        background: #fff;
        border-color: var(--admin-accent);
        box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.1);
    }

    @keyframes adminCardIn {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .admin-card {
        animation: adminCardIn 0.5s ease-out forwards;
    }

    .admin-btn {
        background: var(--admin-navy);
        transition: all 0.3s ease;
    }
    .admin-btn:hover {
        background: var(--admin-navy-light);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
    }

    /* ── intl-tel-input dark-mode overrides for the admin card ── */
    .admin-card .iti__tel-input {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #0f172a;
    }
    .admin-card .iti__tel-input:focus {
        background: #fff;
        border-color: var(--admin-accent);
        box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.1);
    }
    .admin-card .iti--separate-dial-code .iti__selected-flag {
        border-right-color: #cbd5e1;
    }
</style>
@endpush

@section('content')

<div class="min-h-screen admin-auth-bg flex items-center justify-center px-4 py-12 relative overflow-hidden" dir="rtl">

    <div class="absolute top-0 -left-20 w-96 h-96 bg-sky-500/10 rounded-full blur-3xl"></div>
    <div class="absolute bottom-0 -right-20 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl"></div>

    <div class="w-full max-w-md relative z-10">

        {{-- Logo --}}
        <div class="text-center mb-8">
            <div class="inline-block p-4 bg-white/10 backdrop-blur-md rounded-2xl border border-white/20 mb-4">
                <img src="{{ $logoUrl }}" alt="Logo" class="h-12 w-auto object-contain brightness-0 invert">
            </div>
            <h2 class="text-white font-display text-2xl font-bold tracking-tight">بوابة الإدارة</h2>
            <p class="text-slate-400 text-sm mt-2">نظام الإدارة المركزية — سجل دخولك</p>
        </div>

        <div class="admin-card bg-white rounded-3xl shadow-2xl border border-slate-200 p-8 sm:p-10">

            @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border-r-4 border-red-500 rounded-xl">
                <ul class="text-red-700 text-sm space-y-1">
                    @foreach($errors->all() as $error)
                        <li class="flex items-center gap-2">
                            <span class="w-1 h-1 bg-red-500 rounded-full"></span>
                            {{ $error }}
                        </li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="is_admin_login" value="1">

                {{-- ── Phone Number with Country Selector ── --}}
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">
                        رقم هاتف المسؤول
                    </label>

                    @include('components.phone-input', [
                        'fieldName'      => 'phone_full',
                        'initialCountry' => 'sy',
                        'oldValue'       => old('phone_full'),
                        'hasError'       => $errors->has('phone_full'),
                    ])

                    @error('phone_full')
                        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">كلمة المرور</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </span>
                        <input type="password" name="password" required
                               placeholder="••••••••"
                               class="input-field w-full pr-12 pl-4 py-3.5 rounded-xl border border-slate-200 text-slate-900 focus:outline-none focus:ring-0">
                    </div>
                </div>

                <div class="flex items-center">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                        <span class="text-sm text-slate-500 group-hover:text-slate-700 transition-colors">البقاء متصلاً</span>
                    </label>
                </div>

                <button type="submit" class="admin-btn w-full py-4 rounded-xl text-white font-bold text-lg shadow-xl shadow-slate-900/20 active:scale-[0.98] transition-all">
                    دخول المسؤول
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-slate-100 text-center">
                <a href="{{ url('/') }}" class="text-sm text-slate-400 hover:text-slate-600 flex items-center justify-center gap-2 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    العودة للواجهة الرئيسية
                </a>
            </div>
        </div>
    </div>
</div>

@endsection