@extends('layouts.app')
@section('title', 'تسجيل الدخول')

@push('head')
<style>
    .input-field {
        transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
    }
    .input-field:focus { background: #fff; }

    .auth-bg-blob {
        position: fixed;
        border-radius: 50%;
        filter: blur(80px);
        opacity: 0.07;
        pointer-events: none;
        z-index: 0;
    }

    @keyframes cardIn {
        from { opacity: 0; transform: translateY(20px) scale(0.98); }
        to   { opacity: 1; transform: translateY(0)   scale(1); }
    }
    .auth-card {
        animation: cardIn 0.45s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    .auth-card > * {
        opacity: 0;
        animation: cardIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    .auth-card > *:nth-child(1) { animation-delay: 0.08s; }
    .auth-card > *:nth-child(2) { animation-delay: 0.14s; }
    .auth-card > *:nth-child(3) { animation-delay: 0.20s; }
    .auth-card > *:nth-child(4) { animation-delay: 0.26s; }
    .auth-card > *:nth-child(5) { animation-delay: 0.32s; }
    .auth-card > *:nth-child(6) { animation-delay: 0.38s; }

    .pw-toggle { cursor: pointer; transition: color 0.15s; }
    .pw-toggle:hover { color: var(--brand-color, #0ea5e9); }

    .divider { display: flex; align-items: center; gap: 12px; }
    .divider::before, .divider::after {
        content: ''; flex: 1; height: 1px; background: #e5e7eb;
    }
</style>
@endpush

@section('content')

<div class="auth-bg-blob w-96 h-96 bg-blue-400 top-0 right-0 fixed"></div>
<div class="auth-bg-blob w-80 h-80 bg-sky-300 bottom-10 left-10 fixed"></div>

<div class="min-h-screen bg-gray-50 flex items-center justify-center px-4 py-16 relative z-10" dir="rtl">
    <div class="w-full max-w-md">

        {{-- Logo / Brand --}}
        <div class="text-center mb-8">
            <a href="{{ url('/') }}" class="inline-flex flex-col items-center gap-2 group">
                <div class="w-14 h-14 bg-brand-600 rounded-2xl flex items-center justify-center shadow-lg shadow-brand-600/25 group-hover:scale-105 transition-transform">
                    <div class="w-20 h-20 flex items-center justify-center mb-4">
                        <img src="{{ $logoUrl }}" alt="Logo" class="max-h-full w-auto object-contain">
                    </div>
                </div>
                <span class="font-display text-2xl font-bold text-gray-900">ShopCraft</span>
            </a>
            <p class="text-gray-500 text-sm mt-2">مرحباً بعودتك — سجّل دخولك للمتابعة</p>
        </div>

        {{-- Card --}}
        <div class="auth-card bg-white rounded-2xl shadow-lg border border-gray-100 p-8">

            {{-- Global validation errors --}}
            @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-100 rounded-xl flex items-start gap-3">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <ul class="text-red-600 text-sm space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Heading --}}
            <div class="mb-7">
                <h1 class="font-display text-2xl font-bold text-gray-900">تسجيل الدخول</h1>
                <p class="text-gray-400 text-sm mt-1">أدخل بيانات حسابك للمتابعة</p>
            </div>

            {{-- Form --}}
            <form action="{{ route('login') }}" method="POST" class="space-y-5" novalidate>
                @csrf

                {{-- ── Phone Number with Country Selector ── --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        رقم الهاتف
                    </label>

                    @include('components.phone-input', [
                        'fieldName'     => 'phone_full',
                        'initialCountry'=> 'sy',
                        'oldValue'      => old('phone_full'),
                        'hasError'      => $errors->has('phone_full'),
                    ])

                    @error('phone_full')
                        <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-sm font-semibold text-gray-700">
                            كلمة المرور
                        </label>
                        @if(Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                           class="text-xs text-brand-600 hover:underline font-medium transition-colors">
                            نسيت كلمة المرور؟
                        </a>
                        @endif
                    </div>
                    <div class="relative">
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••"
                            class="input-field w-full bg-gray-50 border @error('password') border-red-400 bg-red-50/30 @else border-gray-200 @enderror
                                   rounded-xl px-4 pe-11 py-3 text-sm text-gray-800
                                   focus:outline-none focus:ring-2 focus:ring-brand-600/30 focus:border-brand-600
                                   placeholder:text-gray-400"
                        >
                        <button type="button"
                                onclick="togglePassword('password', this)"
                                class="pw-toggle absolute inset-y-0 end-0 flex items-center pe-3.5 text-gray-400">
                            <svg id="eye-open-password" class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg id="eye-closed-password" class="w-4.5 h-4.5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Remember me --}}
                <div class="flex items-center">
                    <label class="flex items-center gap-2.5 cursor-pointer select-none">
                        <input
                            type="checkbox"
                            name="remember"
                            id="remember"
                            {{ old('remember') ? 'checked' : '' }}
                            class="w-4 h-4 text-brand-600 border-gray-300 rounded focus:ring-brand-600/30 cursor-pointer"
                        >
                        <span class="text-sm text-gray-600">تذكرني</span>
                    </label>
                </div>

                {{-- Submit --}}
                <button
                    type="submit"
                    class="w-full bg-brand-600 text-white rounded-xl py-3 font-bold
                           hover:opacity-90 transition transform hover:-translate-y-0.5
                           active:translate-y-0 active:opacity-100
                           shadow-lg shadow-brand-600/20 text-sm tracking-wide">
                    تسجيل الدخول
                </button>

            </form>

            <div class="divider my-6 text-xs text-gray-400">أو</div>

            <p class="text-center text-sm text-gray-500">
                ليس لديك حساب؟
                <a href="{{ route('register') }}"
                   class="text-brand-600 font-bold hover:underline transition-colors mr-1">
                    إنشاء حساب جديد
                </a>
            </p>

        </div>

        <p class="text-center mt-5">
            <a href="{{ url('/') }}"
               class="text-xs text-gray-400 hover:text-gray-600 transition-colors flex items-center justify-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                العودة إلى المتجر
            </a>
        </p>

    </div>
</div>

@endsection

@push('scripts')
<script>
function togglePassword(inputId, btn) {
    const input     = document.getElementById(inputId);
    const eyeOpen   = document.getElementById('eye-open-' + inputId);
    const eyeClosed = document.getElementById('eye-closed-' + inputId);
    const isHidden  = input.type === 'password';
    input.type      = isHidden ? 'text' : 'password';
    eyeOpen.classList.toggle('hidden',  isHidden);
    eyeClosed.classList.toggle('hidden', !isHidden);
}
</script>
@endpush