@extends('layouts.app')
@section('title', 'إعادة تعيين كلمة المرور')

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
    .strength-bar { height: 3px; border-radius: 99px; transition: background 0.3s ease; }
</style>
@endpush

@section('content')

<div class="auth-bg-blob w-96 h-96 bg-sky-400 top-0 right-0 fixed"></div>
<div class="auth-bg-blob w-72 h-72 bg-blue-300 bottom-20 left-10 fixed"></div>

{{-- جعل الاتجاه ديناميكي بناءً على لغة النظام --}}
<div class="min-h-screen bg-gray-50 flex items-center justify-center px-4 py-12 relative z-10" 
     dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
    <div class="w-full max-w-md">

        {{-- Logo --}}
        <div class="text-center mb-8">
            <a href="{{ url('/') }}" class="inline-flex flex-col items-center gap-2 group">
                <div class="flex items-center justify-center group-hover:scale-105 transition-transform">
                    <img src="{{ $logoUrl ?? asset('images/logo.png') }}" alt="Logo" class="h-14 w-auto object-contain">
                </div>
            </a>
            <p class="text-gray-500 text-sm mt-2">{{ __('app.reset_subtitle') }}</p>
        </div>

        <div class="auth-card bg-white rounded-2xl shadow-lg border border-gray-100 p-8">

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
                <h1 class="font-display text-2xl font-bold text-gray-900">{{ __('app.reset_title') }}</h1>
                <p class="text-gray-400 text-sm mt-1">{{ __('app.reset_hint') }}</p>
            </div>

            <form action="{{ route('password.update') }}" method="POST" class="space-y-5">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                {{-- Email --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('app.register_email_label') }}</label>
                    <input type="email" name="email" value="{{ old('email', $email) }}" required
                           dir="ltr"
                           class="input-field w-full bg-gray-100 border @error('email') border-red-400 @else border-gray-200 @enderror
                                  rounded-xl px-4 py-3 text-sm text-left focus:outline-none focus:ring-2"
                           style="--tw-ring-color:var(--brand-color,#0ea5e9)">
                    @error('email')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                {{-- New password --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('app.reset_new_password') }}</label>
                    <div class="relative">
                        <input id="password" type="password" name="password" required
                               placeholder="••••••••" oninput="checkStr(this.value)"
                               class="input-field w-full bg-gray-50 border @error('password') border-red-400 @else border-gray-200 @enderror
                                      rounded-xl px-4 pe-11 py-3 text-sm focus:outline-none focus:ring-2"
                               style="--tw-ring-color:var(--brand-color,#0ea5e9)">
                        <button type="button" onclick="togglePw('password', this)"
                                class="pw-toggle absolute inset-y-0 end-0 flex items-center pe-3.5 text-gray-400">
                            {{-- الأيقونات تبقى كما هي --}}
                            <svg id="eye-open-password" class="w-4 h-4" ...></svg>
                            <svg id="eye-closed-password" class="w-4 h-4 hidden" ...></svg>
                        </button>
                    </div>

                    {{-- Strength bar --}}
                    <div id="strength-wrap" style="display:none" class="mt-2 space-y-1">
                        <div class="flex gap-1.5">
                            <div class="strength-bar flex-1 bg-gray-200" id="sb1"></div>
                            <div class="strength-bar flex-1 bg-gray-200" id="sb2"></div>
                            <div class="strength-bar flex-1 bg-gray-200" id="sb3"></div>
                            <div class="strength-bar flex-1 bg-gray-200" id="sb4"></div>
                        </div>
                        <p id="strength-label" class="text-xs text-gray-400"></p>
                    </div>

                    @error('password')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                {{-- Confirm password --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('app.register_password_confirm') }}</label>
                    <input id="password_confirmation" type="password"
                           name="password_confirmation" required
                           placeholder="••••••••" oninput="checkMatch()"
                           class="input-field w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2"
                           style="--tw-ring-color:var(--brand-color,#0ea5e9)">
                    <p id="match-msg" class="mt-1.5 text-xs hidden"></p>
                </div>

                <button type="submit"
                        class="w-full py-3.5 rounded-xl text-white font-bold text-sm tracking-wide
                               hover:opacity-90 transition transform hover:-translate-y-0.5 active:translate-y-0 shadow-lg"
                        style="background:var(--brand-color,#0ea5e9)">
                    {{ __('app.reset_submit') }}
                </button>
            </form>
        </div>

        <p class="text-center mt-5">
            <a href="{{ route('login') }}"
               class="text-xs text-gray-400 hover:text-gray-600 transition-colors flex items-center justify-center gap-1.5">
                <svg class="w-3.5 h-3.5" ...></svg>
                {{ __('app.back_to_login') }}
            </a>
        </p>
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePw(id, btn) {
    const i = document.getElementById(id);
    const h = i.type === 'password';
    i.type = h ? 'text' : 'password';
    document.getElementById('eye-open-' + id).classList.toggle('hidden', h);
    document.getElementById('eye-closed-' + id).classList.toggle('hidden', !h);
}

function checkStr(val) {
    const wrap  = document.getElementById('strength-wrap');
    const label = document.getElementById('strength-label');
    const bars  = ['sb1','sb2','sb3','sb4'].map(id => document.getElementById(id));
    if (!val) { wrap.style.display = 'none'; return; }
    wrap.style.display = 'block';
    let score = 0;
    if (val.length >= 8)          score++;
    if (/[A-Z]/.test(val))        score++;
    if (/[0-9]/.test(val))        score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;
    const colors = ['#ef4444','#f97316','#eab308','#22c55e'];
    const labels = ['ضعيفة جداً', 'ضعيفة', 'متوسطة', 'قوية'];
    bars.forEach((b, i) => { b.style.background = i < score ? colors[score-1] : '#e5e7eb'; });
    label.textContent = 'القوة: ' + (labels[score-1] || '');
    label.className   = 'text-xs ' + (['text-red-500','text-orange-500','text-yellow-500','text-green-500'][score-1] || 'text-gray-400');
}

function checkMatch() {
    const pw   = document.getElementById('password').value;
    const conf = document.getElementById('password_confirmation').value;
    const msg  = document.getElementById('match-msg');
    if (!conf) { msg.classList.add('hidden'); return; }
    if (pw === conf) {
        msg.textContent = '✓ كلمتا المرور متطابقتان';
        msg.className   = 'mt-1.5 text-xs text-green-500';
        msg.classList.remove('hidden');
    } else {
        msg.textContent = '✗ كلمتا المرور غير متطابقتين';
        msg.className   = 'mt-1.5 text-xs text-red-500';
        msg.classList.remove('hidden');
    }
}
</script>
@endpush