@extends('layouts.admin')
@section('title', 'إعدادات الرسائل النصية SMS')

@push('head')
<style>
.setting-card {
    background: #fff;
    border: 1.5px solid #f1f5f9;
    border-radius: 18px;
    padding: 28px;
    box-shadow: 0 1px 3px rgba(0,0,0,.04);
}
.field-label {
    display: block;
    font-size: 13px;
    font-weight: 700;
    color: #374151;
    margin-bottom: 6px;
}
.field-hint {
    display: block;
    font-size: 11px;
    color: #94a3b8;
    margin-top: 4px;
}
.sms-input {
    width: 100%;
    border: 1.5px solid #e5e7eb;
    border-radius: 12px;
    padding: 10px 14px;
    font-size: 13px;
    color: #1e293b;
    background: #f9fafb;
    transition: border-color .2s, background .2s, box-shadow .2s;
    outline: none;
}
.sms-input:focus {
    border-color: var(--brand-color, #0ea5e9);
    background: #fff;
    box-shadow: 0 0 0 3px color-mix(in srgb, var(--brand-color,#0ea5e9) 12%, transparent);
}
.effective-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 10px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 6px;
    background: #f0fdf4;
    color: #16a34a;
    border: 1px solid #bbf7d0;
    margin-top: 5px;
}
.source-db    { background: #eff6ff; color: #2563eb; border-color: #bfdbfe; }
.source-config{ background: #fff7ed; color: #c2410c; border-color: #fed7aa; }

/* Test result area */
.test-result {
    border-radius: 12px;
    padding: 12px 16px;
    font-size: 13px;
    font-weight: 600;
    display: none;
}
.test-result.success { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
.test-result.error   { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
</style>
@endpush

@section('admin-content')
<div dir="rtl">

    {{-- Page header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xl">📱</span>
                <h1 class="text-xl font-black text-gray-900">إعدادات الرسائل النصية (SMS API)</h1>
            </div>
            <p class="text-sm text-gray-400">
                اضبط بيانات اعتماد Broadnet. الحقول الفارغة ستستخدم القيم الافتراضية المضمّنة في الكود.
            </p>
        </div>
        <a href="{{ route('admin.dashboard') }}"
           class="inline-flex items-center gap-1.5 text-sm font-semibold text-gray-400 hover:text-gray-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            لوحة التحكم
        </a>
    </div>

    {{-- Success flash --}}
    @if(session('success'))
    <div class="mb-5 p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center gap-2 text-emerald-700 text-sm font-semibold">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Priority explanation --}}
    <div class="flex flex-wrap gap-2 mb-5 text-xs font-semibold">
        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full effective-badge source-db">
            🗄️ قيمة من قاعدة البيانات (أولوية عليا)
        </span>
        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full effective-badge source-config">
            ⚙️ قيمة افتراضية من config/sms.php
        </span>
    </div>

    <form action="{{ route('admin.settings.sms.update') }}" method="POST">
        @csrf

        {{-- Validation errors --}}
        @if($errors->any())
        <div class="mb-5 p-4 bg-red-50 border border-red-100 rounded-xl text-sm text-red-600">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

            {{-- ── API Credentials ──────────────────────────────────────── --}}
            <div class="setting-card space-y-5">
                <h2 class="font-bold text-gray-700 text-sm border-b border-gray-100 pb-4 uppercase tracking-wider">
                    بيانات الاعتماد
                </h2>

                {{-- URL --}}
                <div>
                    <label class="field-label">رابط API <span class="text-gray-400 font-normal">(SMS_URL)</span></label>
                    <input type="url" name="sms_url"
                           value="{{ old('sms_url', $settings->get('sms_url')?->value) }}"
                           placeholder="{{ config('sms.url') }}"
                           class="sms-input @error('sms_url') border-red-400 @enderror"
                           dir="ltr">
                    @error('sms_url')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    <x-sms-effective-badge :effective="$effective['sms_url']" :db-val="$settings->get('sms_url')?->value" />
                </div>

                {{-- User --}}
                <div>
                    <label class="field-label">اسم المستخدم <span class="text-gray-400 font-normal">(SMS_USER)</span></label>
                    <input type="text" name="sms_user"
                           value="{{ old('sms_user', $settings->get('sms_user')?->value) }}"
                           placeholder="{{ config('sms.user') }}"
                           class="sms-input"
                           dir="ltr" autocomplete="off">
                    <x-sms-effective-badge :effective="$effective['sms_user']" :db-val="$settings->get('sms_user')?->value" />
                </div>

                {{-- Password --}}
                <div x-data="{ show: false }">
                    <label class="field-label">كلمة المرور <span class="text-gray-400 font-normal">(SMS_PASS)</span></label>
                    <div class="relative">
                        <input :type="show ? 'text' : 'password'" name="sms_pass"
                               value="{{ old('sms_pass', $settings->get('sms_pass')?->value) }}"
                               placeholder="••••••••"
                               class="sms-input pe-10"
                               dir="ltr" autocomplete="new-password">
                        <button type="button" @click="show=!show"
                                class="absolute inset-y-0 end-0 flex items-center pe-3 text-gray-400 hover:text-gray-600">
                            <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                    <x-sms-effective-badge :effective="$effective['sms_pass'] ? '••••••' : null" :db-val="$settings->get('sms_pass')?->value ? '••••••' : null" />
                </div>

                {{-- Sender ID --}}
                <div>
                    <label class="field-label">Sender ID <span class="text-gray-400 font-normal">(SMS_SID)</span></label>
                    <input type="text" name="sms_sid"
                           value="{{ old('sms_sid', $settings->get('sms_sid')?->value) }}"
                           placeholder="{{ config('sms.sid') }}"
                           class="sms-input"
                           dir="ltr">
                    <x-sms-effective-badge :effective="$effective['sms_sid']" :db-val="$settings->get('sms_sid')?->value" />
                </div>

                {{-- Type --}}
                <div>
                    <label class="field-label">نوع الرسالة <span class="text-gray-400 font-normal">(SMS_TYPE)</span></label>
                    <select name="sms_type" class="sms-input">
                        @foreach([1=>'1 — ASCII',2=>'2 — Binary',3=>'3 — Flash',4=>'4 — Unicode (عربي)'] as $val => $lbl)
                        <option value="{{ $val }}" {{ (int)(old('sms_type', $settings->get('sms_type')?->value ?? config('sms.type'))) === $val ? 'selected' : '' }}>
                            {{ $lbl }}
                        </option>
                        @endforeach
                    </select>
                    <span class="field-hint">استخدم النوع 4 للرسائل العربية (Unicode).</span>
                </div>
            </div>

            {{-- ── OTP Settings ──────────────────────────────────────────── --}}
            <div class="space-y-5">
                <div class="setting-card space-y-5">
                    <h2 class="font-bold text-gray-700 text-sm border-b border-gray-100 pb-4 uppercase tracking-wider">
                        إعدادات رمز OTP
                    </h2>

                    <div>
                        <label class="field-label">مدة صلاحية الرمز (بالدقائق)</label>
                        <input type="number" name="otp_ttl_minutes" min="1" max="60"
                               value="{{ old('otp_ttl_minutes', $settings->get('otp_ttl_minutes')?->value ?? 5) }}"
                               class="sms-input">
                        <span class="field-hint">القيمة الافتراضية: 5 دقائق.</span>
                    </div>

                    <div>
                        <label class="field-label">طول رمز OTP (عدد الأرقام)</label>
                        <input type="number" name="otp_length" min="4" max="8"
                               value="{{ old('otp_length', $settings->get('otp_length')?->value ?? 6) }}"
                               class="sms-input">
                        <span class="field-hint">يُنصح بـ 6 أرقام. الحد الأقصى 8.</span>
                    </div>
                </div>

                {{-- ── Test Connection ────────────────────────────────────── --}}
                <div class="setting-card" x-data="smsTest()">
                    <h2 class="font-bold text-gray-700 text-sm border-b border-gray-100 pb-4 mb-5 uppercase tracking-wider">
                        اختبار الاتصال
                    </h2>
                    <p class="text-xs text-gray-400 mb-4">
                        سيتم إرسال رسالة نصية تجريبية للرقم المدخل باستخدام الإعدادات الحالية المحفوظة.
                    </p>

                    <div class="flex gap-2 mb-3">
                        <input type="text" x-model="phone"
                               placeholder="07xxxxxxxx أو +962xxxxxxxxx"
                               class="sms-input flex-1" dir="ltr" inputmode="tel">
                        <button type="button" @click="runTest()"
                                :disabled="loading || !phone"
                                class="flex-shrink-0 px-5 py-2.5 rounded-xl text-white text-sm font-bold
                                       hover:opacity-90 transition active:scale-95 disabled:opacity-50"
                                style="background:var(--brand-color,#0ea5e9)">
                            <span x-show="!loading">إرسال</span>
                            <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                            </svg>
                        </button>
                    </div>

                    <div id="test-result" class="test-result"></div>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex justify-end gap-3 mt-6">
            <a href="{{ route('admin.dashboard') }}"
               class="px-6 py-2.5 text-sm font-bold text-gray-400 hover:text-red-500 transition-colors">إلغاء</a>
            <button type="submit"
                    class="inline-flex items-center gap-2 text-white text-sm font-black px-8 py-2.5
                           rounded-xl shadow-lg hover:opacity-90 hover:scale-[1.02] transition-all active:scale-95"
                    style="background:var(--brand-color,#0ea5e9)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
                حفظ الإعدادات
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', function () {
    Alpine.data('smsTest', function () {
        return {
            phone:   '',
            loading: false,

            async runTest() {
                if (!this.phone) return;
                this.loading = true;
                const resultEl = document.getElementById('test-result');
                resultEl.style.display = 'none';
                resultEl.className = 'test-result';

                try {
                    const res = await fetch('{{ route('admin.settings.sms.test') }}', {
                        method:  'POST',
                        headers: {
                            'Content-Type':  'application/json',
                            'X-CSRF-TOKEN':  document.querySelector('meta[name="csrf-token"]').content,
                            'Accept':        'application/json',
                        },
                        body: JSON.stringify({ test_phone: this.phone }),
                    });
                    const data = await res.json();
                    resultEl.textContent = data.message + (data.raw ? ' — الكود: ' + data.raw : '');
                    resultEl.classList.add(data.success ? 'success' : 'error');
                    resultEl.style.display = 'block';
                } catch (e) {
                    resultEl.textContent = 'خطأ في الاتصال: ' + e.message;
                    resultEl.classList.add('error');
                    resultEl.style.display = 'block';
                } finally {
                    this.loading = false;
                }
            }
        };
    });
});
</script>
@endpush