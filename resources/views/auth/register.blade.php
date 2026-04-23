@extends('layouts.app')
@section('title', 'إنشاء حساب جديد')

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
    .auth-card > * { opacity: 0; animation: cardIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    .auth-card > *:nth-child(1) { animation-delay: 0.05s; }
    .auth-card > *:nth-child(2) { animation-delay: 0.10s; }
    .auth-card > *:nth-child(3) { animation-delay: 0.15s; }
    .auth-card > *:nth-child(4) { animation-delay: 0.20s; }
    .auth-card > *:nth-child(5) { animation-delay: 0.25s; }
    .auth-card > *:nth-child(6) { animation-delay: 0.30s; }
    .auth-card > *:nth-child(7) { animation-delay: 0.35s; }

    .pw-toggle { cursor: pointer; transition: color 0.15s; }
    .pw-toggle:hover { color: var(--brand-color, #0ea5e9); }

    .strength-bar { height: 3px; border-radius: 99px; transition: background 0.3s ease; }

    .divider { display: flex; align-items: center; gap: 12px; }
    .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: #e5e7eb; }

    /* Method toggle pills */
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
    .method-tab:hover:not(.active) {
        border-color: var(--brand-color, #0ea5e9);
        color: var(--brand-color, #0ea5e9);
    }
</style>
@endpush

@section('content')

{{-- Inject countries JSON for country-select component --}}
@include('partials.country-select-data', ['countries' => $countries])

<div class="auth-bg-blob w-96 h-96 bg-sky-400 top-0 left-0 fixed"></div>
<div class="auth-bg-blob w-72 h-72 bg-blue-300 bottom-20 right-10 fixed"></div>

<div class="min-h-screen bg-gray-50 flex items-center justify-center px-4 py-12 relative z-10" dir="rtl">
<div class="w-full max-w-md">

    {{-- Logo --}}
    <div class="text-center mb-8">
        <a href="{{ url('/') }}" class="inline-flex flex-col items-center gap-2 group">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-105 transition-transform"
                 style="background:var(--brand-color,#0ea5e9)">
                <img src="{{ $logoUrl ?? asset('images/logo.png') }}" alt="Logo"
                     class="h-9 w-auto object-contain brightness-0 invert">
            </div>
            <span class="font-display text-2xl font-bold text-gray-900">{{ get_otp_setting('site_name', config('app.name')) }}</span>
        </a>
        <p class="text-gray-500 text-sm mt-2">أنشئ حسابك وابدأ التسوق الآن</p>
    </div>

    <div class="auth-card bg-white rounded-2xl shadow-lg border border-gray-100 p-8"
         x-data="registerForm('{{ old('email') ? 'email' : 'phone' }}')">

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
            <h1 class="font-display text-2xl font-bold text-gray-900">إنشاء حساب جديد</h1>
            <p class="text-gray-400 text-sm mt-1">أدخل بياناتك وسيصلك رمز التحقق</p>
        </div>

        <form action="{{ route('register') }}" method="POST" class="space-y-5" novalidate>
            @csrf

            {{-- ── Method selector ─────────────────────────────────────── --}}
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2.5">طريقة التسجيل</p>
                <div class="flex gap-2">
                    <button type="button" class="method-tab" :class="method === 'phone' ? 'active' : ''"
                            @click="method = 'phone'">
                        📱 رقم الهاتف
                    </button>
                    <button type="button" class="method-tab" :class="method === 'email' ? 'active' : ''"
                            @click="method = 'email'">
                        ✉️ البريد الإلكتروني
                    </button>
                </div>
            </div>

            {{-- Full name --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">الاسم الكامل</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       placeholder="محمد أحمد"
                       class="input-field w-full bg-gray-50 border @error('name') border-red-400 @else border-gray-200 @enderror
                              rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2"
                       style="--tw-ring-color:var(--brand-color,#0ea5e9)">
                @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            {{-- ── PHONE section ───────────────────────────────────────── --}}
            <div x-show="method === 'phone'" x-transition>
                {{-- Country select --}}
                <div class="mb-4">
                    @include('components.country-select', [
                        'countries'   => $countries,
                        'name'        => 'country_id',
                        'label'       => 'دولة الحساب',
                        'required'    => false,
                        'selected'    => old('country_id', ''),
                        'hasError'    => $errors->has('country_id'),
                        'placeholder' => 'اختر الدولة...',
                    ])
                </div>

                {{-- Phone input --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">رقم الهاتف</label>
                    @include('components.phone-input', [
                        'fieldName'      => 'phone_full',
                        'initialCountry' => 'sy',
                        'oldValue'       => old('phone_full'),
                        'hasError'       => $errors->has('phone_full'),
                    ])
                    @error('phone_full')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                    <p class="mt-1 text-[10px] text-gray-400 italic" dir="ltr">* e.g. +963 9xx xxx xxx</p>
                </div>
            </div>

            {{-- ── EMAIL section ───────────────────────────────────────── --}}
            <div x-show="method === 'email'" x-transition>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">البريد الإلكتروني</label>
                <div class="relative">
                    <div class="absolute inset-y-0 end-0 flex items-center pe-3.5 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <input type="email" name="email" value="{{ old('email') }}"
                           :required="method === 'email'"
                           placeholder="example@mail.com"
                           dir="ltr"
                           class="input-field w-full bg-gray-50 border @error('email') border-red-400 @else border-gray-200 @enderror
                                  rounded-xl px-4 pe-10 py-3 text-sm text-left focus:outline-none focus:ring-2"
                           style="--tw-ring-color:var(--brand-color,#0ea5e9)">
                </div>
                @error('email')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
                <p class="mt-1 text-[10px] text-gray-400">سيُرسل رمز التحقق إلى بريدك الإلكتروني</p>
            </div>

            {{-- Password --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">كلمة المرور</label>
                <div class="relative">
                    <input id="password" type="password" name="password" required
                           placeholder="••••••••" oninput="checkStr(this.value)"
                           class="input-field w-full bg-gray-50 border @error('password') border-red-400 @else border-gray-200 @enderror
                                  rounded-xl px-4 pe-11 py-3 text-sm focus:outline-none focus:ring-2"
                           style="--tw-ring-color:var(--brand-color,#0ea5e9)">
                    <button type="button" onclick="togglePw('password',this)"
                            class="pw-toggle absolute inset-y-0 end-0 flex items-center pe-3.5 text-gray-400">
                        <svg id="eye-open-password" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg id="eye-closed-password" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
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
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">تأكيد كلمة المرور</label>
                <input id="password_confirmation" type="password"
                       name="password_confirmation" required
                       placeholder="••••••••" oninput="checkMatch()"
                       class="input-field w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2"
                       style="--tw-ring-color:var(--brand-color,#0ea5e9)">
                <p id="match-msg" class="mt-1.5 text-xs hidden"></p>
            </div>

            <p class="text-xs text-gray-400 text-center leading-relaxed">
                بإنشاء الحساب، أنت توافق على
                <a href="#" class="hover:underline" style="color:var(--brand-color,#0ea5e9)">شروط الاستخدام</a>
                وسياسة الخصوصية
            </p>

            <button type="submit"
                    class="w-full py-3.5 rounded-xl text-white font-bold text-sm tracking-wide
                           hover:opacity-90 transition transform hover:-translate-y-0.5 active:translate-y-0 shadow-lg"
                    style="background:var(--brand-color,#0ea5e9)">
                إنشاء الحساب
                <span x-text="method === 'email' ? '— سيصلك رمز على بريدك' : '— سيصلك رمز على هاتفك'"
                      class="block text-[10px] font-normal opacity-70 mt-0.5"></span>
            </button>
        </form>

        <div class="divider my-6 text-xs text-gray-400">أو</div>

        <p class="text-center text-sm text-gray-500">
            لديك حساب بالفعل؟
            <a href="{{ route('login') }}" class="font-bold hover:underline mr-1"
               style="color:var(--brand-color,#0ea5e9)">تسجيل الدخول</a>
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
document.addEventListener('alpine:init', function () {
  Alpine.data('registerForm', () => ({
    method: 'email',
}));
});

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
    const labels = ['ضعيفة جداً','ضعيفة','متوسطة','قوية'];
    bars.forEach((b, i) => { b.style.background = i < score ? colors[score-1] : '#e5e7eb'; });
    label.textContent = 'قوة كلمة المرور: ' + (labels[score-1] || '');
    label.className   = 'text-xs ' + ['text-red-500','text-orange-500','text-yellow-500','text-green-500'][score-1] || 'text-gray-400';
}

function checkMatch() {
    const pw   = document.getElementById('password').value;
    const conf = document.getElementById('password_confirmation').value;
    const msg  = document.getElementById('match-msg');
    if (!conf) { msg.classList.add('hidden'); return; }
    if (pw === conf) {
        msg.textContent = 'كلمتا المرور متطابقتان ✓';
        msg.className   = 'mt-1.5 text-xs text-green-500';
        msg.classList.remove('hidden');
    } else {
        msg.textContent = 'كلمتا المرور غير متطابقتين';
        msg.className   = 'mt-1.5 text-xs text-red-500';
        msg.classList.remove('hidden');
    }
}
</script>
@endpush