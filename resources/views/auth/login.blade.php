@extends('layouts.app')
@section('title', 'تسجيل الدخول')

@push('head')
<style>
.input-field { transition: border-color .2s, box-shadow .2s, background .2s; }
.input-field:focus { background:#fff; }
.auth-bg-blob { position:fixed;border-radius:50%;filter:blur(80px);opacity:.07;pointer-events:none;z-index:0; }
@keyframes cardIn { from{opacity:0;transform:translateY(20px) scale(.98)}to{opacity:1;transform:translateY(0) scale(1)} }
.auth-card{animation:cardIn .45s cubic-bezier(.16,1,.3,1) forwards}
.auth-card > *{opacity:0;animation:cardIn .4s cubic-bezier(.16,1,.3,1) forwards}
.auth-card > *:nth-child(1){animation-delay:.08s}.auth-card > *:nth-child(2){animation-delay:.14s}
.auth-card > *:nth-child(3){animation-delay:.20s}.auth-card > *:nth-child(4){animation-delay:.26s}
.auth-card > *:nth-child(5){animation-delay:.32s}
.pw-toggle{cursor:pointer;transition:color .15s}.pw-toggle:hover{color:var(--brand-color,#0ea5e9)}
.divider{display:flex;align-items:center;gap:12px}
.divider::before,.divider::after{content:'';flex:1;height:1px;background:#e5e7eb}
</style>
@endpush

@section('content')
<div class="auth-bg-blob w-96 h-96 bg-blue-400 top-0 right-0 fixed"></div>
<div class="auth-bg-blob w-80 h-80 bg-sky-300 bottom-10 left-10 fixed"></div>

<div class="min-h-screen bg-gray-50 flex items-center justify-center px-4 py-16 relative z-10" dir="rtl">
<div class="w-full max-w-md">

    <div class="text-center mb-8">
        <a href="{{ url('/') }}" class="inline-flex flex-col items-center gap-2 group">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-105 transition-transform"
                 style="background:var(--brand-color,#0ea5e9)">
                <img src="{{ $logoUrl ?? asset('images/logo.png') }}" alt="Logo" class="h-9 w-auto object-contain brightness-0 invert">
            </div>
            <span class="font-display text-xl font-bold text-gray-900">{{ get_otp_setting('site_name', config('app.name')) }}</span>
        </a>
        <p class="text-gray-500 text-sm mt-2">مرحباً بعودتك — سجّل دخولك للمتابعة</p>
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

        <div class="mb-7">
            <h1 class="font-display text-2xl font-bold text-gray-900">تسجيل الدخول</h1>
            <p class="text-gray-400 text-sm mt-1">أدخل رقم هاتفك وكلمة المرور</p>
        </div>

        <form action="{{ route('login') }}" method="POST" class="space-y-5" novalidate>
            @csrf

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">رقم الهاتف</label>
                @include('components.phone-input', [
                    'fieldName'      => 'phone_full',
                    'initialCountry' => 'sy',
                    'oldValue'       => old('phone_full'),
                    'hasError'       => $errors->has('phone_full'),
                ])
                @error('phone_full')
                <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    {{ $message }}
                </p>
                @enderror
            </div>

            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-sm font-semibold text-gray-700">كلمة المرور</label>
                    @if(Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-xs font-medium hover:underline" style="color:var(--brand-color,#0ea5e9)">نسيت كلمة المرور؟</a>
                    @endif
                </div>
                <div class="relative">
                    <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••"
                           class="input-field w-full bg-gray-50 border @error('password') border-red-400 @else border-gray-200 @enderror rounded-xl px-4 pe-11 py-3 text-sm text-gray-800 focus:outline-none focus:ring-2 placeholder:text-gray-400"
                           style="--tw-ring-color:var(--brand-color,#0ea5e9)">
                    <button type="button" onclick="togglePw('password',this)" class="pw-toggle absolute inset-y-0 end-0 flex items-center pe-3.5 text-gray-400">
                        <svg id="eye-open-password" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg id="eye-closed-password" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    </button>
                </div>
                @error('password')<p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center">
                <label class="flex items-center gap-2.5 cursor-pointer select-none">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}
                           class="w-4 h-4 rounded border-gray-300 cursor-pointer" style="accent-color:var(--brand-color,#0ea5e9)">
                    <span class="text-sm text-gray-600">تذكرني</span>
                </label>
            </div>

            <button type="submit" class="w-full py-3 rounded-xl text-white font-bold text-sm tracking-wide hover:opacity-90 transition transform hover:-translate-y-0.5 active:translate-y-0 shadow-lg" style="background:var(--brand-color,#0ea5e9)">
                تسجيل الدخول
            </button>

            <p class="text-center text-xs text-gray-400 flex items-center justify-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                سيتم إرسال رمز تحقق لهاتفك بعد تسجيل الدخول
            </p>
        </form>

        <div class="divider my-6 text-xs text-gray-400">أو</div>
        <p class="text-center text-sm text-gray-500">
            ليس لديك حساب؟
            <a href="{{ route('register') }}" class="font-bold hover:underline mr-1" style="color:var(--brand-color,#0ea5e9)">إنشاء حساب جديد</a>
        </p>
    </div>

    <p class="text-center mt-5">
        <a href="{{ url('/') }}" class="text-xs text-gray-400 hover:text-gray-600 transition-colors flex items-center justify-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            العودة إلى المتجر
        </a>
    </p>
</div>
</div>
@endsection

@push('scripts')
<script>
function togglePw(id,btn){const i=document.getElementById(id);const h=i.type==='password';i.type=h?'text':'password';document.getElementById('eye-open-'+id).classList.toggle('hidden',h);document.getElementById('eye-closed-'+id).classList.toggle('hidden',!h);}
</script>
@endpush