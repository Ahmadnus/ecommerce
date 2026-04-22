@extends('layouts.admin')
@section('title', 'إعدادات الرسائل النصية SMS')

@section('admin-content')
<div dir="rtl" class="max-w-6xl mx-auto px-4 py-8 select-none">

    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-200">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-black text-slate-800 tracking-tight">بوابة الرسائل النصية (SMS)</h1>
            </div>
            <p class="text-slate-500 font-medium max-w-xl">
                إدارة بروتوكول الارتباط مع Broadnet. الحقول المتروكة فارغة ستعتمد القيم الافتراضية من النظام.
            </p>
        </div>
        
        <a href="{{ route('admin.dashboard') }}" class="group flex items-center gap-2 text-sm font-bold text-slate-400 hover:text-indigo-600 transition-all">
            <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
            </svg>
            العودة للرئيسية
        </a>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="animate-in fade-in slide-in-from-top-4 duration-300 mb-8 p-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center gap-3 text-emerald-700 shadow-sm shadow-emerald-100">
            <div class="w-8 h-8 bg-emerald-500 rounded-full flex items-center justify-center flex-shrink-0 text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
            </div>
            <span class="font-bold">{{ session('success') }}</span>
        </div>
    @endif

    <form action="{{ route('admin.settings.sms.update') }}" method="POST" class="space-y-8">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            {{-- Right Column: Credentials --}}
            <div class="lg:col-span-7 space-y-6">
                <div class="bg-white border border-slate-100 shadow-sm rounded-3xl overflow-hidden">
                    <div class="px-8 py-6 border-b border-slate-50 bg-slate-50/50">
                        <h2 class="text-sm font-black text-slate-700 uppercase tracking-widest">بيانات الارتباط API</h2>
                    </div>
                    
                    <div class="p-8 space-y-6">
                        {{-- API URL --}}
                        <div class="space-y-2">
                            <label class="flex justify-between items-center">
                                <span class="text-sm font-bold text-slate-700">رابط الخدمة (Endpoint)</span>
                                <span class="text-[10px] bg-slate-100 text-slate-500 px-2 py-0.5 rounded font-mono uppercase">SMS_URL</span>
                            </label>
                            <input type="url" name="sms_url" value="{{ old('sms_url', $settings->get('sms_url')?->value) }}" placeholder="https://api.broadnet.me/..."
                                   class="w-full px-4 py-3.5 bg-slate-50 border-slate-200 rounded-xl focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 transition-all outline-none text-sm font-medium" dir="ltr">
                            <x-sms-effective-badge :effective="$effective['sms_url']" :db-val="$settings->get('sms_url')?->value" key="sms_url" />
                        </div>

                        {{-- Username & Password Row --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700">اسم المستخدم</label>
                                <input type="text" name="sms_user" value="{{ old('sms_user', $settings->get('sms_user')?->value) }}" 
                                       class="w-full px-4 py-3.5 bg-slate-50 border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 transition-all outline-none text-sm font-medium" dir="ltr">
                                <x-sms-effective-badge :effective="$effective['sms_user']" :db-val="$settings->get('sms_user')?->value" key="sms_user" />
                            </div>

                            <div class="space-y-2" x-data="{ show: false }">
                                <label class="text-sm font-bold text-slate-700">كلمة المرور</label>
                                <div class="relative">
                                    <input :type="show ? 'text' : 'password'" name="sms_pass" value="{{ old('sms_pass', $settings->get('sms_pass')?->value) }}"
                                           class="w-full px-4 py-3.5 bg-slate-50 border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 transition-all outline-none text-sm font-medium pe-12" dir="ltr">
                                    <button type="button" @click="show = !show" class="absolute inset-y-0 left-0 px-3 text-slate-400 hover:text-indigo-600 transition-colors">
                                        <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                    </button>
                                </div>
                                <x-sms-effective-badge :effective="$effective['sms_pass'] ? '••••••' : null" :db-val="$settings->get('sms_pass')?->value ? '••••••' : null" key="sms_pass" />
                            </div>
                        </div>

                        {{-- Sender ID & Type --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700">هوية المرسل (Sender ID)</label>
                                <input type="text" name="sms_sid" value="{{ old('sms_sid', $settings->get('sms_sid')?->value) }}" placeholder="مثلاً: MY_STORE"
                                       class="w-full px-4 py-3.5 bg-slate-50 border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 transition-all outline-none text-sm font-bold tracking-widest" dir="ltr">
                                <x-sms-effective-badge :effective="$effective['sms_sid']" :db-val="$settings->get('sms_sid')?->value" key="sms_sid" />
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700">نوع الترميز</label>
                                <select name="sms_type" class="w-full px-4 py-3.5 bg-slate-50 border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 transition-all outline-none text-sm font-bold">
                                    @foreach([1=>'1 — ASCII', 4=>'4 — Unicode (عربي)'] as $val => $lbl)
                                        <option value="{{ $val }}" {{ (int)(old('sms_type', $settings->get('sms_type')?->value ?? config('sms.type'))) === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Left Column: OTP & Test --}}
            <div class="lg:col-span-5 space-y-6">
                {{-- OTP Security --}}
                <div class="bg-white border border-slate-100 shadow-sm rounded-3xl p-8 space-y-6">
                    <h3 class="text-sm font-black text-slate-700 uppercase tracking-widest border-b border-slate-50 pb-4">أمن رموز الـ OTP</h3>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-500 uppercase">الصلاحية (دقيقة)</label>
                            <input type="number" name="otp_ttl_minutes" min="1" max="60" value="{{ old('otp_ttl_minutes', $settings->get('otp_ttl_minutes')?->value ?? 5) }}"
                                   class="w-full px-4 py-3 bg-slate-50 border-slate-200 rounded-xl text-center font-black text-indigo-600 focus:ring-4 focus:ring-indigo-50">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-500 uppercase">طول الرمز</label>
                            <input type="number" name="otp_length" min="4" max="8" value="{{ old('otp_length', $settings->get('otp_length')?->value ?? 6) }}"
                                   class="w-full px-4 py-3 bg-slate-50 border-slate-200 rounded-xl text-center font-black text-indigo-600 focus:ring-4 focus:ring-indigo-50">
                        </div>
                    </div>
                </div>

                {{-- Connection Test --}}
                <div class="bg-indigo-900 rounded-3xl p-8 shadow-xl shadow-indigo-100 relative overflow-hidden" x-data="smsTest()">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/5 rounded-full blur-2xl"></div>
                    
                    <h3 class="text-white font-black text-sm uppercase tracking-widest mb-2 relative">اختبار حي للاتصال</h3>
                    <p class="text-indigo-200 text-xs mb-6 relative">سيتم إرسال رسالة تجريبية باستخدام البيانات المحفوظة حالياً.</p>
                    
                    <div class="flex flex-col gap-3 relative">
                        <input type="text" x-model="phone" placeholder="9665xxxxxxxx" 
                               class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/40 outline-none focus:bg-white/20 transition-all text-sm font-bold" dir="ltr">
                        
                        <button type="button" @click="runTest()" :disabled="loading || !phone"
                                class="w-full py-3.5 bg-white text-indigo-900 rounded-xl font-black text-sm hover:bg-indigo-50 transition-all active:scale-95 disabled:opacity-50 shadow-lg">
                            <span x-show="!loading">بدء الاختبار</span>
                            <div x-show="loading" class="flex items-center justify-center gap-2">
                                <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                جارِ الإرسال...
                            </div>
                        </button>
                    </div>

                    <div id="test-result" class="mt-4 hidden p-3 rounded-xl text-xs font-bold animate-in zoom-in-95"></div>
                </div>
            </div>
        </div>

        {{-- Footer Actions --}}
        <div class="sticky bottom-6 mt-10 p-4 bg-white/80 backdrop-blur-md border border-slate-100 rounded-3xl shadow-2xl flex items-center justify-between">
            <p class="text-[11px] text-slate-400 font-medium px-4">تأكد من مراجعة البيانات جيداً قبل الحفظ.</p>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.dashboard') }}" class="px-6 py-3 text-sm font-bold text-slate-400 hover:text-slate-600 transition-colors">تجاهل</a>
                <button type="submit" class="px-10 py-3 bg-indigo-600 text-white rounded-2xl font-black text-sm shadow-xl shadow-indigo-200 hover:bg-indigo-700 hover:scale-[1.02] active:scale-95 transition-all">
                    تحديث الإعدادات
                </button>
            </div>
        </div>
    </form>
</div>
@endsection