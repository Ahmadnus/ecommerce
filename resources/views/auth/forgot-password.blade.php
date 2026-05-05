@extends('layouts.app')
@section('title', __('app.auth.forgot_password_title'))

@push('head')
<style>
    .input-field { transition: border-color 0.2s, box-shadow 0.2s, background 0.2s; }
    .input-field:focus { background: #fff; }
    .auth-bg-blob { position: fixed; border-radius: 50%; filter: blur(80px); opacity: 0.07; pointer-events: none; z-index: 0; }

    @keyframes cardIn {
        from { opacity: 0; transform: translateY(20px) scale(0.98); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }
    .auth-card { animation: cardIn 0.45s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
</style>
@endpush

@section('content')

<div class="auth-bg-blob w-96 h-96 bg-sky-400 top-0 right-0 fixed"></div>
<div class="auth-bg-blob w-72 h-72 bg-blue-300 bottom-20 left-10 fixed"></div>

<div class="min-h-screen bg-gray-50 flex items-center justify-center px-4 py-12 relative z-10" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
    <div class="w-full max-w-md">

        {{-- Logo --}}
        <div class="text-center mb-8">
            <a href="{{ url('/') }}" class="inline-flex flex-col items-center gap-2 group">
                <div class="flex items-center justify-center group-hover:scale-105 transition-transform">
                    <img src="{{ $logoUrl ?? asset('images/logo.png') }}" alt="Logo" class="h-14 w-auto object-contain">
                </div>
            </a>
            <p class="text-gray-500 text-sm mt-2">{{ __('app.auth.forgot_password_subtitle') }}</p>
        </div>

        <div class="auth-card bg-white rounded-2xl shadow-lg border border-gray-100 p-8">

            {{-- Success message --}}
            @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-100 rounded-xl flex items-start gap-3">
                <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <p class="text-green-700 text-sm">{{ session('success') }}</p>
            </div>
            @endif

            {{-- Errors --}}
            @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-100 rounded-xl flex items-start gap-3">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <ul class="text-red-600 text-sm space-y-0.5">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
            @endif

            <div class="mb-6">
                <h1 class="font-display text-2xl font-bold text-gray-900">{{ __('app.auth.forgot_password_heading') }}</h1>
                <p class="text-gray-400 text-sm mt-1">{{ __('app.auth.forgot_password_desc') }}</p>
            </div>

            <form action="{{ route('password.email') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('app.auth.email_required') }}</label>
                    <div class="relative">
                        {{-- تم تعديل الأيقونة لتظهر في الجهة المقابلة حسب اللغة --}}
                        <div class="absolute inset-y-0 {{ app()->getLocale() == 'ar' ? 'left-0 pl-3.5' : 'right-0 pr-3.5' }} flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               placeholder="example@mail.com" dir="ltr"
                               class="input-field w-full bg-gray-50 border @error('email') border-red-400 @else border-gray-200 @enderror
                                      rounded-xl {{ app()->getLocale() == 'ar' ? 'pl-10 pr-4' : 'pr-10 pl-4' }} py-3 text-sm text-left focus:outline-none focus:ring-2"
                               style="--tw-ring-color:var(--brand-color,#0ea5e9)">
                    </div>
                    @error('email')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <button type="submit"
                        class="w-full py-3.5 rounded-xl text-white font-bold text-sm tracking-wide
                               hover:opacity-90 transition transform hover:-translate-y-0.5 active:translate-y-0 shadow-lg"
                        style="background:var(--brand-color,#0ea5e9)">
                    {{ __('app.auth.send_reset_link') }}
                </button>
            </form>
        </div>

        <p class="text-center mt-5">
            <a href="{{ route('login') }}"
               class="text-xs text-gray-400 hover:text-gray-600 transition-colors flex items-center justify-center gap-1.5">
                {{-- أيقونة السهم تعكس اتجاهها حسب اللغة --}}
                <svg class="w-3.5 h-3.5 {{ app()->getLocale() == 'en' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                {{ __('app.auth.back_to_login') }}
            </a>
        </p>

    </div>
</div>
@endsection