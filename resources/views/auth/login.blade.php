@extends('layouts.app')

@section('title', __('app.auth.login.page_title'))

@php
    $isRtl = app()->getLocale() === 'ar';
@endphp

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
    
    .pw-toggle { cursor: pointer; transition: color 0.15s; }
    .pw-toggle:hover { color: var(--brand-color, #0ea5e9); }

    .divider { display: flex; align-items: center; gap: 12px; }
    .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: #e5e7eb; }

    .method-tab {
        flex: 1; padding: 10px 8px;
        border-radius: 10px;
        cursor: pointer; text-align: center;
        font-size: 13px; font-weight: 700;
        transition: all .18s;
        border: 1.5px solid #e5e7eb;
        background: #f9fafb; color: #64748b;
    }
    .method-tab.active {
        border-color: var(--brand-color, #0ea5e9);
        background: color-mix(in srgb, var(--brand-color, #0ea5e9) 8%, #fff);
        color: var(--brand-color, #0ea5e9);
    }
</style>
@endpush

@section('content')

<div class="auth-bg-blob w-96 h-96 bg-sky-400 top-0 right-0 fixed"></div>
<div class="auth-bg-blob w-72 h-72 bg-blue-300 bottom-20 left-10 fixed"></div>

<div class="min-h-screen bg-gray-50 flex items-center justify-center px-4 py-12 relative z-10" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
    <div class="w-full max-w-md">

        <div class="text-center mb-8">
            <a href="{{ url('/') }}" class="inline-flex flex-col items-center gap-2 group">
                <div class="flex items-center justify-center group-hover:scale-105 transition-transform">
                    <img src="{{ $logoUrl ?? asset('images/logo.png') }}" 
                         alt="Logo"
                         class="h-14 w-auto object-contain"> 
                </div>
            </a>
            <p class="text-gray-500 text-sm mt-2">{{ __('app.auth.login.logo_subtitle') }}</p>
        </div>

        <div class="auth-card bg-white rounded-2xl shadow-lg border border-gray-100 p-8"
             x-data="loginForm('{{ old('identity') ? 'email' : 'phone' }}')">

            @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-100 rounded-xl flex items-start gap-3">
                <svg class="w-5 h-5 text-red-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <ul class="text-red-600 text-sm">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
            @endif

            <div class="mb-6">
                <h1 class="font-display text-2xl font-bold text-gray-900">{{ __('app.auth.login.heading') }}</h1>
                <p class="text-gray-400 text-sm mt-1">{{ __('app.auth.login.subheading') }}</p>
            </div>

            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf

                <div class="flex gap-2 mb-2">
                    <button type="button" class="method-tab" :class="method === 'phone' ? 'active' : ''" @click="method = 'phone'">
                        📱 {{ __('app.auth.login.method_phone') }}
                    </button>
                    <button type="button" class="method-tab" :class="method === 'email' ? 'active' : ''" @click="method = 'email'">
                        ✉️ {{ __('app.auth.login.method_email') }}
                    </button>
                </div>

                <div x-show="method === 'phone'" x-transition>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('app.auth.login.phone_label') }}</label>
                    @include('components.phone-input', [
                        'fieldName' => 'phone_full',
                        'initialCountry' => 'sy',
                        'oldValue' => old('phone_full'),
                        'hasError' => $errors->has('phone_full')
                    ])
                </div>

                <div x-show="method === 'email'" x-transition>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('app.auth.login.email_label') }}</label>
                    <input type="email" name="identity" value="{{ old('identity') }}"
                           placeholder="{{ __('app.auth.login.email_placeholder') }}" dir="ltr"
                           class="input-field w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm text-left focus:outline-none focus:ring-2"
                           style="--tw-ring-color:var(--brand-color,#0ea5e9)">
                </div>

                <div>
                    <div class="flex justify-between mb-1.5">
                        <label class="block text-sm font-semibold text-gray-700">{{ __('app.auth.login.password_label') }}</label>
                        <a href="#" class="text-xs font-bold hover:underline" style="color:var(--brand-color,#0ea5e9)">{{ __('app.auth.login.forgot_password') }}</a>
                    </div>
                    <div class="relative">
                        <input id="password" type="password" name="password" required
                               placeholder="{{ __('app.auth.login.password_placeholder') }}"
                               class="input-field w-full bg-gray-50 border border-gray-200 rounded-xl px-4 pe-11 py-3 text-sm focus:outline-none focus:ring-2"
                               style="--tw-ring-color:var(--brand-color,#0ea5e9)">
                        <button type="button" onclick="togglePw('password')" class="pw-toggle absolute inset-y-0 end-0 flex items-center pe-3.5 text-gray-400">
                            <svg id="eye-open-password" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg id="eye-closed-password" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                </div>

                <label class="flex items-center gap-2 cursor-pointer group">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                    <span class="text-sm text-gray-500 group-hover:text-gray-700 transition-colors">{{ __('app.auth.login.remember_me') }}</span>
                </label>

                <button type="submit" class="w-full py-3.5 rounded-xl text-white font-bold text-sm shadow-lg hover:opacity-90 transition transform hover:-translate-y-0.5"
                        style="background:var(--brand-color,#0ea5e9)">
                    {{ __('app.auth.login.submit') }}
                </button>
            </form>

            <div class="divider my-6 text-xs text-gray-400">{{ __('app.auth.login.or') }}</div>

            <p class="text-center text-sm text-gray-500">
                {{ __('app.auth.login.no_account') }}
                <a href="{{ route('register') }}" class="font-bold hover:underline mr-1" style="color:var(--brand-color,#0ea5e9)">{{ __('app.auth.login.register') }}</a>
            </p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
   Alpine.data('loginForm', () => ({
        method: 'email',
   }));
});

function togglePw(id) {
    const input = document.getElementById(id);
    const isOpen = input.type === 'text';
    input.type = isOpen ? 'password' : 'text';
    document.getElementById('eye-open-' + id).classList.toggle('hidden', !isOpen);
    document.getElementById('eye-closed-' + id).classList.toggle('hidden', isOpen);
}
</script>


@endpush