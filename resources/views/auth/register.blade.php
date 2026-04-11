@extends('layouts.app')
@section('title', 'إنشاء حساب جديد')

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
    .auth-card > *:nth-child(2) { animation-delay: 0.13s; }
    .auth-card > *:nth-child(3) { animation-delay: 0.18s; }
    .auth-card > *:nth-child(4) { animation-delay: 0.23s; }
    .auth-card > *:nth-child(5) { animation-delay: 0.28s; }
    .auth-card > *:nth-child(6) { animation-delay: 0.33s; }
    .auth-card > *:nth-child(7) { animation-delay: 0.38s; }

    .pw-toggle { cursor: pointer; transition: color 0.15s; }
    .pw-toggle:hover { color: var(--brand-color, #0ea5e9); }

    /* Strength bar */
    .strength-bar { height: 3px; border-radius: 99px; transition: width 0.3s ease, background 0.3s ease; }

    .divider { display: flex; align-items: center; gap: 12px; }
    .divider::before, .divider::after {
        content: ''; flex: 1; height: 1px; background: #e5e7eb;
    }

    /* Step indicator */
    .step-dot {
        width: 6px; height: 6px;
        border-radius: 50%;
        background: #e5e7eb;
        transition: background 0.3s;
    }
    .step-dot.filled { background: var(--brand-color, #0ea5e9); }
</style>
@endpush

@section('content')

<div class="auth-bg-blob w-96 h-96 bg-sky-400 top-0 left-0 fixed"></div>
<div class="auth-bg-blob w-72 h-72 bg-blue-300 bottom-20 right-10 fixed"></div>

<div class="min-h-screen bg-gray-50 flex items-center justify-center px-4 py-12 relative z-10" dir="rtl">
    <div class="w-full max-w-md">

        {{-- Logo / Brand --}}
        <div class="text-center mb-8">
            <a href="{{ url('/') }}" class="inline-flex flex-col items-center gap-2 group">
                <div class="w-14 h-14 bg-brand-600 rounded-2xl flex items-center justify-center shadow-lg shadow-brand-600/25 group-hover:scale-105 transition-transform">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <span class="font-display text-2xl font-bold text-gray-900">ShopCraft</span>
            </a>
            <p class="text-gray-500 text-sm mt-2">أنشئ حسابك وابدأ التسوق الآن</p>
        </div>

        {{-- Card --}}
        <div class="auth-card bg-white rounded-2xl shadow-lg border border-gray-100 p-8">

            {{-- Global errors --}}
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
                <h1 class="font-display text-2xl font-bold text-gray-900">إنشاء حساب جديد</h1>
                <p class="text-gray-400 text-sm mt-1">أدخل بياناتك لإنشاء حسابك</p>
            </div>

            {{-- Form --}}
            <form action="{{ route('register') }}" method="POST" class="space-y-5" novalidate>
                @csrf

                {{-- Full Name --}}
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">
                        الاسم الكامل
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 end-0 flex items-center pe-3.5 pointer-events-none">
                            <svg class="w-4.5 h-4.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <input
                            id="name"
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            required
                            autocomplete="name"
                            placeholder="محمد أحمد"
                            class="input-field w-full bg-gray-50 border @error('name') border-red-400 bg-red-50/30 @else border-gray-200 @enderror
                                   rounded-xl px-4 pe-10 py-3 text-sm text-gray-800
                                   focus:outline-none focus:ring-2 focus:ring-brand-600/30 focus:border-brand-600
                                   placeholder:text-gray-400"
                        >
                    </div>
                    @error('name')
                        <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Phone Number --}}
                <div>
                    <label for="phone" class="block text-sm font-semibold text-gray-700 mb-1.5">
                        رقم الهاتف
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 end-0 flex items-center pe-3.5 pointer-events-none">
                            <svg class="w-4.5 h-4.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        <input
                            id="phone"
                            type="tel"
                            name="phone"
                            value="{{ old('phone') }}"
                            required
                            autocomplete="tel"
                            placeholder="05XXXXXXXX"
                            dir="ltr"
                            class="input-field w-full bg-gray-50 border @error('phone') border-red-400 bg-red-50/30 @else border-gray-200 @enderror
                                   rounded-xl px-4 pe-10 py-3 text-sm text-gray-800 text-left
                                   focus:outline-none focus:ring-2 focus:ring-brand-600/30 focus:border-brand-600
                                   placeholder:text-gray-400"
                        >
                    </div>
                    @error('phone')
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
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">
                        كلمة المرور
                    </label>
                    <div class="relative">
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="new-password"
                            placeholder="••••••••"
                            oninput="checkStrength(this.value)"
                            class="input-field w-full bg-gray-50 border @error('password') border-red-400 bg-red-50/30 @else border-gray-200 @enderror
                                   rounded-xl px-4 pe-11 py-3 text-sm text-gray-800
                                   focus:outline-none focus:ring-2 focus:ring-brand-600/30 focus:border-brand-600
                                   placeholder:text-gray-400"
                        >
                        <button type="button"
                                onclick="togglePassword('password', this)"
                                class="pw-toggle absolute inset-y-0 end-0 flex items-center pe-3.5 text-gray-400">
                            <svg id="eye-open-password" class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg id="eye-closed-password" class="w-4.5 h-4.5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Password strength bar --}}
                    <div class="mt-2 space-y-1.5" id="strength-wrap" style="display:none">
                        <div class="flex gap-1.5">
                            <div class="strength-bar flex-1 bg-gray-200" id="sb1"></div>
                            <div class="strength-bar flex-1 bg-gray-200" id="sb2"></div>
                            <div class="strength-bar flex-1 bg-gray-200" id="sb3"></div>
                            <div class="strength-bar flex-1 bg-gray-200" id="sb4"></div>
                        </div>
                        <p id="strength-label" class="text-xs text-gray-400"></p>
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

                {{-- Password Confirmation --}}
                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1.5">
                        تأكيد كلمة المرور
                    </label>
                    <div class="relative">
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            required
                            autocomplete="new-password"
                            placeholder="••••••••"
                            oninput="checkMatch()"
                            class="input-field w-full bg-gray-50 border border-gray-200 rounded-xl px-4 pe-11 py-3
                                   text-sm text-gray-800
                                   focus:outline-none focus:ring-2 focus:ring-brand-600/30 focus:border-brand-600
                                   placeholder:text-gray-400"
                        >
                        <button type="button"
                                onclick="togglePassword('password_confirmation', this)"
                                class="pw-toggle absolute inset-y-0 end-0 flex items-center pe-3.5 text-gray-400">
                            <svg id="eye-open-password_confirmation" class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg id="eye-closed-password_confirmation" class="w-4.5 h-4.5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                        {{-- Match indicator --}}
                        <span id="match-icon" class="absolute inset-y-0 start-0 flex items-center ps-3.5 hidden">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </span>
                    </div>
                    <p id="match-msg" class="mt-1.5 text-xs hidden"></p>
                </div>

                {{-- Terms note --}}
                <p class="text-xs text-gray-400 text-center leading-relaxed">
                    بإنشاء الحساب، أنت توافق على
                    <a href="#" class="text-brand-600 hover:underline">شروط الاستخدام</a>
                    و
                    <a href="#" class="text-brand-600 hover:underline">سياسة الخصوصية</a>
                </p>

                {{-- Submit --}}
                <button
                    type="submit"
                    class="w-full bg-brand-600 text-white rounded-xl py-3 font-bold
                           hover:opacity-90 transition transform hover:-translate-y-0.5
                           active:translate-y-0 active:opacity-100
                           shadow-lg shadow-brand-600/20 text-sm tracking-wide">
                    إنشاء الحساب
                </button>

            </form>

            {{-- Divider --}}
            <div class="divider my-6 text-xs text-gray-400">أو</div>

            {{-- Login link --}}
            <p class="text-center text-sm text-gray-500">
                لديك حساب بالفعل؟
                <a href="{{ route('login') }}"
                   class="text-brand-600 font-bold hover:underline transition-colors mr-1">
                    تسجيل الدخول
                </a>
            </p>

        </div>

        {{-- Back to shop --}}
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
// ── Show / hide password ──────────────────────────────────────────────────────
function togglePassword(inputId, btn) {
    const input     = document.getElementById(inputId);
    const eyeOpen   = document.getElementById('eye-open-' + inputId);
    const eyeClosed = document.getElementById('eye-closed-' + inputId);
    const isHidden  = input.type === 'password';
    input.type = isHidden ? 'text' : 'password';
    eyeOpen.classList.toggle('hidden',  isHidden);
    eyeClosed.classList.toggle('hidden', !isHidden);
}

// ── Password strength meter ───────────────────────────────────────────────────
function checkStrength(val) {
    const wrap  = document.getElementById('strength-wrap');
    const label = document.getElementById('strength-label');
    const bars  = [document.getElementById('sb1'), document.getElementById('sb2'),
                   document.getElementById('sb3'), document.getElementById('sb4')];

    if (!val) { wrap.style.display = 'none'; return; }
    wrap.style.display = 'block';

    let score = 0;
    if (val.length >= 8)          score++;
    if (/[A-Z]/.test(val))        score++;
    if (/[0-9]/.test(val))        score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;

    const colors  = ['#ef4444', '#f97316', '#eab308', '#22c55e'];
    const labels  = ['ضعيفة جداً', 'ضعيفة', 'متوسطة', 'قوية'];
    const txtCols = ['text-red-500', 'text-orange-500', 'text-yellow-500', 'text-green-500'];

    bars.forEach((b, i) => {
        b.style.background = i < score ? colors[score - 1] : '#e5e7eb';
    });

    label.textContent  = 'قوة كلمة المرور: ' + labels[score - 1];
    label.className    = 'text-xs ' + (txtCols[score - 1] || 'text-gray-400');
}

// ── Password confirmation match ───────────────────────────────────────────────
function checkMatch() {
    const pw    = document.getElementById('password').value;
    const conf  = document.getElementById('password_confirmation').value;
    const msg   = document.getElementById('match-msg');
    const icon  = document.getElementById('match-icon');

    if (!conf) { msg.classList.add('hidden'); icon.classList.add('hidden'); return; }

    if (pw === conf) {
        msg.textContent = 'كلمتا المرور متطابقتان ✓';
        msg.className   = 'mt-1.5 text-xs text-green-500';
        msg.classList.remove('hidden');
        icon.classList.remove('hidden');
    } else {
        msg.textContent = 'كلمتا المرور غير متطابقتين';
        msg.className   = 'mt-1.5 text-xs text-red-500';
        msg.classList.remove('hidden');
        icon.classList.add('hidden');
    }
}
</script>
@endpush