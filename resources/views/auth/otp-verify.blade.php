@extends('layouts.app')

@section('content')
<div class="min-h-[80-screen] flex items-center justify-center px-4 py-12" x-data="otpHandler()">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl p-8 border border-gray-100">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-indigo-50 text-indigo-600 rounded-full mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900">تحقق من الهوية</h2>
            <p class="text-gray-500 mt-2">أدخل الرمز المكون من 4 أرقام المرسل إلى <br> 
                <span class="font-medium text-gray-800">{{ session('otp_phone_display') ?? Auth::user()->email }}</span>
            </p>
        </div>

        <form action="{{ route('otp.verify.submit') }}" method="POST">
            @csrf
            <div class="flex justify-center gap-3 mb-8" dir="ltr">
                <template x-for="(i, index) in Array.from({length: 4})">
                    <input type="text" 
                           maxlength="1" 
                           class="w-14 h-16 text-center text-2xl font-bold bg-gray-50 border-2 border-gray-100 rounded-xl focus:border-indigo-500 focus:ring-0 transition-all"
                           x-on:input="handleInput($event, index)"
                           x-on:keydown.backspace="handleBack($event, index)"
                           :id="'otp-' + index">
                </template>
            </div>
            
            <input type="hidden" name="otp_code" x-model="fullCode">

            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-2xl transition-all shadow-lg shadow-indigo-200">
                تأكيد الرمز
            </button>
        </form>

        <div class="mt-8 text-center text-sm">
            <p class="text-gray-500">لم يصلك الرمز؟ 
                <a href="#" class="text-indigo-600 font-semibold hover:underline">إعادة إرسال</a>
            </p>
        </div>
    </div>
</div>

@push('scripts')
<script>
function otpHandler() {
    return {
        fullCode: '',
        handleInput(e, index) {
            if (e.target.value.length === 1 && index < 3) {
                document.getElementById('otp-' + (index + 1)).focus();
            }
            this.updateFullCode();
        },
        handleBack(e, index) {
            if (e.target.value === '' && index > 0) {
                document.getElementById('otp-' + (index - 1)).focus();
            }
            this.updateFullCode();
        },
        updateFullCode() {
            this.fullCode = Array.from({length: 4})
                .map((_, i) => document.getElementById('otp-' + i).value)
                .join('');
        }
    }
}
</script>
@endpush
@endsection