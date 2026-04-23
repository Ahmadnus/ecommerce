@extends('layouts.app')
@section('title', 'التحقق من الهوية')

@push('head')
<style>
    @keyframes cardIn {
        from { opacity:0; transform:translateY(18px) scale(.98); }
        to   { opacity:1; transform:translateY(0) scale(1); }
    }
    .otp-card { animation: cardIn .45s cubic-bezier(.16,1,.3,1) forwards; }

    /* Single-character OTP boxes */
    .otp-box {
        width: 56px; height: 64px;
        text-align: center;
        font-size: 1.75rem; font-weight: 800;
        border: 2px solid #e5e7eb;
        border-radius: 14px;
        background: #f9fafb;
        outline: none;
        transition: border-color .18s, box-shadow .18s, background .18s;
        color: #111827;
        caret-color: var(--brand-color, #0ea5e9);
    }
    .otp-box:focus {
        border-color: var(--brand-color, #0ea5e9);
        background: #fff;
        box-shadow: 0 0 0 3px color-mix(in srgb, var(--brand-color,#0ea5e9) 14%, transparent);
    }
    .otp-box.filled  { border-color: var(--brand-color, #0ea5e9); }
    .otp-box.invalid { border-color: #ef4444; background: #fff5f5; }

    /* Channel icon badge */
    .channel-badge {
        display: inline-flex; align-items: center; gap: 6px;
        background: #f1f5f9; border: 1px solid #e2e8f0;
        border-radius: 99px; padding: 4px 14px;
        font-size: 13px; font-weight: 700; color: #374151;
        direction: ltr;
    }

    .auth-bg-blob {
        position: fixed; border-radius: 50%;
        filter: blur(80px); opacity: .07;
        pointer-events: none; z-index: 0;
    }

    /* Spinner in submit btn */
    .btn-spin {
        display: none;
        width: 16px; height: 16px;
        border: 2px solid rgba(255,255,255,.4);
        border-top-color: #fff;
        border-radius: 50%;
        animation: spin .6s linear infinite;
        flex-shrink: 0;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* Countdown ring */
    .cring { transform: rotate(-90deg); transform-origin: center; }
</style>
@endpush

@section('content')
<div class="auth-bg-blob w-80 h-80 bg-sky-400 top-10 right-10 fixed"></div>
<div class="auth-bg-blob w-64 h-64 bg-blue-300 bottom-10 left-10 fixed"></div>

@php
    $channel    = $channel ?? 'sms';       // 'sms' | 'email'
    $isEmail    = $channel === 'email';
    $displayVal = $isEmail ? ($email ?? '') : ($phone ?? '');
    $otpLen     = $isEmail ? 4 : (int) get_otp_setting('otp_length', 6);
    $ttlSec     = (int) get_otp_setting('otp_ttl_minutes', 5) * 60;
@endphp

<div class="min-h-screen bg-gray-50 flex items-center justify-center px-4 py-16 relative z-10" dir="rtl">
<div class="w-full max-w-sm">

    {{-- Icon + heading --}}
    <div class="text-center mb-6">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl shadow-lg mb-3"
             style="background:var(--brand-color,#0ea5e9)">
            @if($isEmail)
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            @else
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            @endif
        </div>

        <h1 class="text-2xl font-black text-gray-900">التحقق من الهوية</h1>
        <p class="text-gray-500 text-sm mt-1">
            أرسلنا رمزاً مكوناً من {{ $otpLen }} أرقام إلى
        </p>

        <div class="mt-2 flex justify-center">
            <span class="channel-badge">
                @if($isEmail)
                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                @else
                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                @endif
                {{ $displayVal ?: '—' }}
            </span>
        </div>
    </div>

    <div class="otp-card bg-white rounded-2xl shadow-lg border border-gray-100 p-8">

        {{-- Flash --}}
        @if(session('success'))
        <div class="mb-5 p-3.5 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm font-semibold flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
        @endif

        {{-- Error --}}
        @if($errors->has('otp'))
        <div class="mb-5 p-3.5 bg-red-50 border border-red-100 rounded-xl text-red-600 text-sm font-semibold flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            {{ $errors->first('otp') }}
        </div>
        @endif

        {{-- OTP form --}}
        <form id="otp-form" action="{{ route('otp.submit') }}" method="POST" novalidate>
            @csrf

            {{-- Individual character boxes --}}
            <div class="flex justify-center gap-2.5 mb-2 ltr" dir="ltr" id="otp-boxes">
                @for($i = 0; $i < $otpLen; $i++)
                <input type="text"
                       inputmode="numeric"
                       maxlength="1"
                       autocomplete="{{ $i === 0 ? 'one-time-code' : 'off' }}"
                       class="otp-box"
                       id="otp-{{ $i }}"
                       {{ $i === 0 ? 'autofocus' : '' }}>
                @endfor
            </div>

            {{-- Hidden full OTP sent to server --}}
            <input type="hidden" name="otp" id="otp-hidden">

            <p class="text-center text-[10px] text-gray-400 mb-5">أدخل الأرقام من اليسار لليمين</p>

            <button type="submit" id="submit-btn"
                    class="w-full py-3.5 rounded-xl text-white font-black text-base
                           hover:opacity-90 transition-all active:scale-[.98] shadow-lg
                           flex items-center justify-center gap-2 disabled:opacity-60"
                    style="background:var(--brand-color,#0ea5e9)">
                <span class="btn-spin" id="btn-spin"></span>
                <span id="btn-label">تأكيد الرمز</span>
            </button>
        </form>

        {{-- Countdown + Resend --}}
        <div class="mt-6 flex items-center justify-center gap-3"
             x-data="countdown({{ $ttlSec }})">

            {{-- Ring timer --}}
            <svg width="36" height="36" viewBox="0 0 36 36" class="flex-shrink-0">
                <circle cx="18" cy="18" r="15.9" fill="none" stroke="#e5e7eb" stroke-width="3"/>
                <circle cx="18" cy="18" r="15.9" fill="none"
                        stroke="var(--brand-color,#0ea5e9)" stroke-width="3"
                        stroke-linecap="round"
                        class="cring"
                        :stroke-dasharray="circ"
                        :stroke-dashoffset="offset"/>
                <text x="50%" y="55%" dominant-baseline="middle" text-anchor="middle"
                      fill="#374151" font-size="9" font-weight="800" x-text="display"></text>
            </svg>

            <div class="text-sm">
                <span class="text-gray-500 font-bold" x-show="remaining > 0">
                    إعادة الإرسال خلال <span class="tabular-nums" x-text="display"></span>
                </span>

                <form action="{{ route('otp.resend') }}" method="POST"
                      x-show="remaining <= 0" x-transition>
                    @csrf
                    <button type="submit" class="text-sm font-bold hover:underline"
                            style="color:var(--brand-color,#0ea5e9)">
                        إعادة إرسال الرمز
                    </button>
                </form>
            </div>
        </div>
    </div>

    <p class="text-center mt-5">
        <a href="{{ route('login') }}"
           class="text-xs text-gray-400 hover:text-gray-600 transition-colors inline-flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            العودة لتسجيل الدخول
        </a>
    </p>
</div>
</div>
@endsection

@push('scripts')
<script>
/* ─── OTP box navigation ──────────────────────────────────────────────── */
(function () {
    const LEN    = {{ $otpLen }};
    const boxes  = Array.from({ length: LEN }, (_, i) => document.getElementById('otp-' + i));
    const hidden = document.getElementById('otp-hidden');
    const form   = document.getElementById('otp-form');

    function syncHidden() {
        hidden.value = boxes.map(b => b.value).join('');
    }

    function markFilled(box) {
        box.classList.toggle('filled', box.value.length === 1);
    }

    boxes.forEach(function (box, index) {
        box.addEventListener('input', function (e) {
            // Strip non-digits
            box.value = box.value.replace(/\D/, '').slice(-1);
            markFilled(box);
            syncHidden();

            if (box.value && index < LEN - 1) {
                boxes[index + 1].focus();
            }

            // Auto-submit when all filled
            if (hidden.value.length === LEN) {
                setTimeout(function () { submitForm(); }, 80);
            }
        });

        box.addEventListener('keydown', function (e) {
            if (e.key === 'Backspace' && !box.value && index > 0) {
                boxes[index - 1].focus();
                boxes[index - 1].value = '';
                markFilled(boxes[index - 1]);
                syncHidden();
            }
            // Allow arrow key navigation
            if (e.key === 'ArrowLeft'  && index > 0)       { e.preventDefault(); boxes[index - 1].focus(); }
            if (e.key === 'ArrowRight' && index < LEN - 1) { e.preventDefault(); boxes[index + 1].focus(); }
        });

        // Handle paste (e.g. paste "123456" into first box)
        box.addEventListener('paste', function (e) {
            e.preventDefault();
            const text = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
            text.split('').slice(0, LEN).forEach(function (char, i) {
                if (boxes[i]) { boxes[i].value = char; markFilled(boxes[i]); }
            });
            syncHidden();
            const nextEmpty = boxes.findIndex(b => !b.value);
            (boxes[nextEmpty] || boxes[LEN - 1]).focus();
            if (hidden.value.length === LEN) setTimeout(submitForm, 80);
        });
    });

    function submitForm() {
        const btn   = document.getElementById('submit-btn');
        const spin  = document.getElementById('btn-spin');
        const label = document.getElementById('btn-label');
        if (btn.disabled) return;
        btn.disabled         = true;
        spin.style.display   = 'block';
        label.textContent    = 'جارٍ التحقق...';
        form.submit();
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        syncHidden();
        if (hidden.value.length < LEN) {
            boxes.forEach(b => b.classList.toggle('invalid', !b.value));
            return;
        }
        submitForm();
    });
})();

/* ─── Alpine countdown component ─────────────────────────────────────── */
document.addEventListener('alpine:init', function () {
    Alpine.data('countdown', function (totalSec) {
        return {
            remaining: totalSec,
            circ: 2 * Math.PI * 15.9,

            get display() {
                const m = Math.floor(this.remaining / 60);
                const s = this.remaining % 60;
                return m + ':' + String(s).padStart(2, '0');
            },

            get offset() {
                return this.circ * (1 - this.remaining / totalSec);
            },

            init() {
                const t = setInterval(() => {
                    if (this.remaining > 0) this.remaining--;
                    else clearInterval(t);
                }, 1000);
            },
        };
    });
});
</script>
@endpush