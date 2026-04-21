@extends('layouts.app')
@section('title', 'إنشاء حساب جديد')

@push('head')
<style>
    :root { --brand-color: #0ea5e9; }
    .input-field { transition: border-color 0.2s, box-shadow 0.2s, background 0.2s; }
    .input-field:focus { background: #fff; }
    .auth-bg-blob { position: fixed; border-radius: 50%; filter: blur(80px); opacity: 0.07; pointer-events: none; z-index: 0; }
    
    @keyframes cardIn { 
        from { opacity: 0; transform: translateY(20px) scale(0.98); } 
        to { opacity: 1; transform: translateY(0) scale(1); } 
    }
    .auth-card { animation: cardIn 0.45s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    .auth-card > * { opacity: 0; animation: cardIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    .auth-card > *:nth-child(1) { animation-delay: 0.05s; }
    .auth-card > *:nth-child(2) { animation-delay: 0.10s; }
    .auth-card > *:nth-child(3) { animation-delay: 0.15s; }
    .auth-card > *:nth-child(4) { animation-delay: 0.20s; }
    .auth-card > *:nth-child(5) { animation-delay: 0.25s; }

    .pw-toggle { cursor: pointer; transition: color 0.15s; }
    .pw-toggle:hover { color: var(--brand-color); }
    .strength-bar { height: 3px; border-radius: 99px; transition: background 0.3s ease; }
    .divider { display: flex; align-items: center; gap: 12px; }
    .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: #e5e7eb; }
</style>
@endpush

@section('content')
{{-- الخلفية العائمة --}}
<div class="auth-bg-blob w-96 h-96 bg-sky-400 top-0 left-0 fixed"></div>
<div class="auth-bg-blob w-72 h-72 bg-blue-300 bottom-20 right-10 fixed"></div>

<div class="min-h-screen bg-gray-50 flex items-center justify-center px-4 py-12 relative z-10" dir="rtl">
    <div class="w-full max-w-md">

        {{-- اللوجو واسم الموقع --}}
        <div class="text-center mb-8">
            <a href="{{ url('/') }}" class="inline-flex flex-col items-center gap-2 group">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-105 transition-transform" style="background:var(--brand-color)">
                    <img src="{{ $logoUrl ?? asset('images/logo.png') }}" alt="Logo" class="h-9 w-auto object-contain brightness-0 invert">
                </div>
                <span class="font-display text-2xl font-bold text-gray-900">{{ get_otp_setting('site_name', 'ShopCraft') }}</span>
            </a>
            <p class="text-gray-500 text-sm mt-2">أنشئ حسابك وابدأ التسوق الآن</p>
        </div>

        {{-- الكرت الرئيسي --}}
        <div class="auth-card bg-white rounded-2xl shadow-lg border border-gray-100 p-8">

            {{-- عرض الأخطاء --}}
            @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-100 rounded-xl flex items-start gap-3">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <ul class="text-red-600 text-sm space-y-0.5">
                    @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
            @endif

            <div class="mb-7">
                <h1 class="font-display text-2xl font-bold text-gray-900">إنشاء حساب جديد</h1>
                <p class="text-gray-400 text-sm mt-1">أدخل بياناتك وسيصلك رمز التحقق</p>
            </div>

            {{-- نموذج التسجيل --}}
            <form action="{{ route('register') }}" method="POST" class="space-y-5" novalidate>
                @csrf

                {{-- الاسم الكامل --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">الاسم الكامل</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="محمد أحمد"
                           class="input-field w-full bg-gray-50 border @error('name') border-red-400 @else border-gray-200 @enderror rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:border-transparent" 
                           style="--tw-ring-color:var(--brand-color)">
                </div>

                {{-- رقم الهاتف (نفس حقل اللوغن) --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">رقم الهاتف</label>
                    @include('components.phone-input', [
                        'fieldName'      => 'phone_full',
                        'initialCountry' => 'sy',
                        'oldValue'       => old('phone_full'),
                        'hasError'       => $errors->has('phone_full'),
                    ])
                    <p class="mt-1.5 text-[10px] text-gray-400 italic text-left" dir="ltr">* e.g. +963 9xx xxx xxx</p>
                </div>

                {{-- كلمة المرور --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">كلمة المرور</label>
                    <div class="relative">
                        <input id="password" type="password" name="password" required placeholder="••••••••" oninput="checkStrength(this.value)"
                               class="input-field w-full bg-gray-50 border @error('password') border-red-400 @else border-gray-200 @enderror rounded-xl px-4 pe-11 py-3 text-sm focus:outline-none focus:ring-2 focus:border-transparent" 
                               style="--tw-ring-color:var(--brand-color)">
                        <button type="button" onclick="togglePw('password', this)" class="pw-toggle absolute inset-y-0 end-0 flex items-center pe-3.5 text-gray-400">
                             <svg id="eye-open-password" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                             <svg id="eye-closed-password" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                    {{-- شريط القوة --}}
                    <div class="mt-2 space-y-1.5" id="strength-wrap" style="display:none">
                        <div class="flex gap-1.5">
                            <div class="strength-bar flex-1 bg-gray-200" id="sb1"></div>
                            <div class="strength-bar flex-1 bg-gray-200" id="sb2"></div>
                            <div class="strength-bar flex-1 bg-gray-200" id="sb3"></div>
                            <div class="strength-bar flex-1 bg-gray-200" id="sb4"></div>
                        </div>
                    </div>
                </div>

                {{-- تأكيد كلمة المرور --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">تأكيد كلمة المرور</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required placeholder="••••••••"
                           class="input-field w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:border-transparent" 
                           style="--tw-ring-color:var(--brand-color)">
                </div>

                {{-- زر الإرسال --}}
                <button type="submit" class="w-full py-3 rounded-xl text-white font-bold text-sm shadow-lg transform hover:-translate-y-0.5 transition active:translate-y-0" 
                        style="background:var(--brand-color)">
                    إنشاء الحساب
                </button>

                <p class="text-center text-[11px] text-gray-400 leading-relaxed mt-4">
                    بضغطك على إنشاء الحساب، سيتم إرسال رمز تحقق (OTP) إلى هاتفك لتأكيد ملكية الرقم.
                </p>
            </form>

            <div class="divider my-6 text-xs text-gray-400">أو</div>

            <p class="text-center text-sm text-gray-500">
                لديك حساب بالفعل؟
                <a href="{{ route('login') }}" class="font-bold hover:underline mr-1" style="color:var(--brand-color)">تسجيل الدخول</a>
            </p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // تبديل إظهار كلمة المرور
    function togglePw(id, btn) {
        const input = document.getElementById(id);
        const isPw = input.type === 'password';
        input.type = isPw ? 'text' : 'password';
        document.getElementById('eye-open-' + id).classList.toggle('hidden', isPw);
        document.getElementById('eye-closed-' + id).classList.toggle('hidden', !isPw);
    }

    // فحص قوة كلمة المرور
    function checkStrength(val) {
        const wrap = document.getElementById('strength-wrap');
        const bars = ['sb1','sb2','sb3','sb4'].map(id => document.getElementById(id));
        
        if (!val) { wrap.style.display = 'none'; return; }
        wrap.style.display = 'block';

        let score = 0;
        if (val.length >= 8) score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;

        const colors = ['#ef4444', '#f97316', '#eab308', '#22c55e'];
        bars.forEach((b, i) => {
            b.style.background = (i < score) ? colors[score - 1] : '#e5e7eb';
        });
    }
</script>
@endpush