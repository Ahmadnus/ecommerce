@extends('layouts.app')
@section('title', 'التحقق من الهاتف')

@push('head')
<style>
    @keyframes cardIn {
        from { opacity:0; transform:translateY(18px) scale(.98); }
        to   { opacity:1; transform:translateY(0) scale(1); }
    }
    .otp-card { animation: cardIn .45s cubic-bezier(.16,1,.3,1) forwards; }

    .otp-input {
        font-size: 1.75rem;
        font-weight: 800;
        letter-spacing: .5rem;
        text-align: center;
        border: 2px solid #e5e7eb;
        border-radius: 16px;
        background: #f9fafb;
        width: 100%;
        padding: 16px 12px;
        transition: border-color .2s, box-shadow .2s, background .2s;
        outline: none;
        font-variant-numeric: tabular-nums;
        color: #111827;
    }
    .otp-input:focus {
        border-color: var(--brand-color, #0ea5e9);
        background: #fff;
        box-shadow: 0 0 0 4px color-mix(in srgb, var(--brand-color,#0ea5e9) 12%, transparent);
    }
    .otp-input.error { border-color: #f87171; background: #fff5f5; }
    .otp-input.success { border-color: #34d399; background: #f0fdf4; }

    .phone-chip {
        display: inline-flex; align-items: center; gap: 6px;
        background: #f1f5f9; border: 1px solid #e2e8f0;
        border-radius: 99px; padding: 4px 14px 4px 10px;
        font-size: 13px; font-weight: 700; color: #374151; direction: ltr;
    }

    .auth-bg-blob {
        position: fixed; border-radius: 50%;
        filter: blur(80px); opacity: .07;
        pointer-events: none; z-index: 0;
    }

    /* spinner inside submit button */
    .btn-spinner {
        display: none;
        width: 18px; height: 18px;
        border: 2px solid rgba(255,255,255,.4);
        border-top-color: #fff;
        border-radius: 50%;
        animation: spin .6s linear infinite;
        flex-shrink: 0;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* inline error/success message below the input */
    .otp-msg {
        font-size: 13px; font-weight: 600;
        padding: 10px 14px;
        border-radius: 12px;
        display: none;
        margin-top: 12px;
    }
    .otp-msg.error   { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
    .otp-msg.success { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
</style>
@endpush

@section('content')
<div class="auth-bg-blob w-80 h-80 bg-sky-400 top-10 right-10 fixed"></div>
<div class="auth-bg-blob w-64 h-64 bg-sky-300 bottom-10 left-10 fixed"></div>

<div class="min-h-screen bg-gray-50 flex items-center justify-center px-4 py-16 relative z-10" dir="rtl">
    <div class="w-full max-w-sm">

        {{-- Header --}}
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl shadow-lg mb-3"
                 style="background:var(--brand-color,#0ea5e9)">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-black text-gray-900">التحقق من الهاتف</h1>
            <p class="text-gray-500 text-sm mt-1">أرسلنا رمزاً مكوناً من 6 أرقام إلى</p>
            <div class="mt-2 flex justify-center">
                <span class="phone-chip">
                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    {{ $phone }}
                </span>
            </div>
        </div>

        <div class="otp-card bg-white rounded-2xl shadow-lg border border-gray-100 p-8">

            {{-- Server-side flash (first load only) --}}
            @if(session('success'))
            <div class="mb-5 p-3.5 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm font-semibold flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
                {{ session('success') }}
            </div>
            @endif

            {{-- OTP input form (no action — handled by fetch) --}}
            <form id="otp-form" novalidate>
                @csrf

                <div class="mb-2">
                    <input type="text"
                           name="otp"
                           id="otp-input"
                           inputmode="numeric"
                           autocomplete="one-time-code"
                           maxlength="6"
                           placeholder="• • • • • •"
                           autofocus
                           class="otp-input"
                           dir="ltr">
                </div>

                {{-- Inline feedback message (populated by JS) --}}
                <div id="otp-msg" class="otp-msg" role="alert"></div>

                <button type="submit"
                        id="otp-btn"
                        class="w-full mt-5 py-3.5 rounded-xl text-white font-black text-base
                               hover:opacity-90 transition-all active:scale-[.98] shadow-lg
                               flex items-center justify-center gap-2 disabled:opacity-60"
                        style="background:var(--brand-color,#0ea5e9)">
                    <span id="btn-spinner" class="btn-spinner"></span>
                    <span id="btn-label">تحقق من الرمز</span>
                </button>
            </form>

            {{-- Countdown + Resend --}}
            <div class="mt-6 flex items-center justify-center gap-3"
                 x-data="otpCountdown({{ (int) get_otp_setting('otp_ttl_minutes', 5) * 60 }})">

                <svg width="36" height="36" viewBox="0 0 36 36" class="flex-shrink-0">
                    <circle cx="18" cy="18" r="15.9" fill="none" stroke="#e5e7eb" stroke-width="3"/>
                    <circle cx="18" cy="18" r="15.9" fill="none"
                            stroke="var(--brand-color,#0ea5e9)" stroke-width="3"
                            stroke-linecap="round"
                            :stroke-dasharray="circumference"
                            :stroke-dashoffset="dashOffset"
                            style="transform:rotate(-90deg);transform-origin:center"/>
                    <text x="50%" y="55%" dominant-baseline="middle" text-anchor="middle"
                          fill="#374151" font-size="9" font-weight="800" x-text="display"></text>
                </svg>

                <div class="text-sm">
                    <span class="text-gray-500 font-bold" x-show="remaining > 0">
                        أعد الإرسال خلال <span class="tabular-nums" x-text="display"></span>
                    </span>

                    <form action="{{ route('otp.resend') }}" method="POST"
                          x-show="remaining <= 0" x-transition>
                        @csrf
                        <button type="submit"
                                class="text-sm font-bold hover:underline"
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
/* ═══════════════════════════════════════════════════════════════════════════
   OTP FETCH SUBMISSION
   ───────────────────────────────────────────────────────────────────────────
   1. Sends the 6-digit code to {{ route('otp.submit') }} as a JSON/AJAX
      request (X-Requested-With header makes Laravel detect it as AJAX).
   2. Logs the FULL raw response object to the browser console exactly
      like the working /send-test-sms route does.
   3. On success → shows green message → redirects after 1 second.
   4. On error  → shows red message → keeps input editable.
═══════════════════════════════════════════════════════════════════════════ */

(function () {
    const form     = document.getElementById('otp-form');
    const input    = document.getElementById('otp-input');
    const msgBox   = document.getElementById('otp-msg');
    const btn      = document.getElementById('otp-btn');
    const spinner  = document.getElementById('btn-spinner');
    const btnLabel = document.getElementById('btn-label');

    // CSRF token (read from the meta tag OR the hidden input Laravel puts in every form)
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content
              || document.querySelector('input[name="_token"]')?.value
              || '';

    // ── UI helpers ─────────────────────────────────────────────────────────

    function setLoading(on) {
        btn.disabled        = on;
        spinner.style.display = on ? 'block' : 'none';
        btnLabel.textContent  = on ? 'جارٍ التحقق...' : 'تحقق من الرمز';
        input.disabled        = on;
    }

    function showMsg(text, type /* 'error' | 'success' */) {
        msgBox.textContent = text;
        msgBox.className   = 'otp-msg ' + type;
        msgBox.style.display = 'block';
        input.classList.toggle('error',   type === 'error');
        input.classList.toggle('success', type === 'success');
    }

    function clearMsg() {
        msgBox.style.display = 'none';
        msgBox.className     = 'otp-msg';
        input.classList.remove('error', 'success');
    }

    // ── Core submit function ───────────────────────────────────────────────

    async function submitOtp(code) {
        setLoading(true);
        clearMsg();

        let data;

        try {
            const res = await fetch('{{ route('otp.submit') }}', {
                method:  'POST',
                headers: {
                    'Content-Type':     'application/json',
                    'Accept':           'application/json',
                    'X-CSRF-TOKEN':     csrf,
                    'X-Requested-With': 'XMLHttpRequest',   // makes Laravel see it as AJAX
                },
                body: JSON.stringify({ otp: code }),
            });

            data = await res.json();

            /* ─────────────────────────────────────────────────────────────
               CONSOLE LOG — identical structure to /send-test-sms output:
               {
                 sent_to:    "962xxxxxxxxx",
                 message:    "رمز التحقق الخاص بك: xxxxxx",
                 response:   "1701...",          ← raw Broadnet API response
                 error:      "",
                 status:     200,
                 verified:   true | false,
                 redirect:   "/",
               }
            ───────────────────────────────────────────────────────────── */
            console.log('%c[OTP Verification Response]', 'color:#0ea5e9;font-weight:bold;font-size:13px');
            console.log(data);

            if (res.ok && data.verified) {
                // ── Success ──────────────────────────────────────────────
                showMsg('✓ تم التحقق بنجاح! جارٍ التوجيه...', 'success');
                console.log('%c✓ OTP verified — redirecting to: ' + (data.redirect || '/'),
                            'color:#22c55e;font-weight:bold');

                setTimeout(function () {
                    window.location.href = data.redirect || '/';
                }, 1000);

            } else {
                // ── Validation / API error ───────────────────────────────
                const errorText = data.message
                               || (data.errors && Object.values(data.errors).flat()[0])
                               || 'رمز التحقق غير صحيح أو منتهي الصلاحية.';

                showMsg(errorText, 'error');
                console.warn('%c✗ OTP failed:', 'color:#ef4444;font-weight:bold', data);

                // Clear input so user can retype
                input.value = '';
                input.focus();
            }

        } catch (networkErr) {
            // Network / JSON parse failure
            console.error('%c[OTP Network Error]', 'color:#ef4444;font-weight:bold', networkErr);
            showMsg('خطأ في الاتصال بالخادم. يرجى المحاولة مرة أخرى.', 'error');
            input.value = '';
            input.focus();
        } finally {
            setLoading(false);
        }
    }

    // ── Auto-submit when 6 digits are entered ──────────────────────────────

    input.addEventListener('input', function () {
        // Strip non-digits and cap at 6
        this.value = this.value.replace(/\D/g, '').slice(0, 6);

        if (this.value.length === 6) {
            submitOtp(this.value);
        }
    });

    // ── Manual button submit ────────────────────────────────────────────────

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const code = input.value.trim();
        if (code.length < 4) {
            showMsg('أدخل رمز التحقق كاملاً.', 'error');
            return;
        }
        submitOtp(code);
    });

})();

/* Alpine countdown (unchanged) */
document.addEventListener('alpine:init', function () {
    Alpine.data('otpCountdown', function (totalSeconds) {
        return {
            remaining:     totalSeconds,
            circumference: 2 * Math.PI * 15.9,

            get display() {
                const m = Math.floor(this.remaining / 60);
                const s = this.remaining % 60;
                return m + ':' + String(s).padStart(2, '0');
            },

            get dashOffset() {
                return this.circumference * (1 - this.remaining / totalSeconds);
            },

            init() {
                const timer = setInterval(() => {
                    if (this.remaining > 0) this.remaining--;
                    else clearInterval(timer);
                }, 1000);
            }
        };
    });
});
</script>
@endpush