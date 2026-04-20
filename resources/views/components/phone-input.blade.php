{{-- resources/views/components/phone-input.blade.php --}}
@php
    // الإعدادات الافتراضية
    $fieldName      = $fieldName      ?? 'phone_full';
    $initialCountry = $initialCountry ?? 'sy';
    $oldValue       = $oldValue       ?? '';
    $hasError       = $hasError       ?? false;
    $dir            = $dir            ?? 'rtl'; // التنسيق الافتراضي
    $inputId        = 'iti-' . $fieldName;
@endphp

@once
@push('head')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/css/intlTelInput.css">
    <style>
        /* التنسيقات العامة للمكتبة */
        .iti { width: 100% !important; }
        .iti__country-list { 
            z-index: 99999 !important; 
            border-radius: 0.75rem;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
        }
        
        /* ضبط الحقل ليتناسب مع تصميمك */
        .iti__tel-input {
            padding-top: 0.75rem !important;
            padding-bottom: 0.75rem !important;
        }

        /* تنسيق خاص عند اختيار RTL */
        [dir="rtl"] .iti__tel-input {
            text-align: right !important;
            direction: ltr !important; /* الأرقام تبقى LTR برمجياً لتجنب مشاكل الرموز */
        }

        /* تحسين مظهر العلم في حالة الـ RTL */
        .iti--rtl .iti__selected-flag {
            border-left: 1px solid #e2e8f0;
            border-radius: 0 0.75rem 0.75rem 0 !important;
        }
    </style>
@endpush
@endonce

<div class="relative w-full phone-input-container" dir="{{ $dir }}">
    {{-- الحقل المرئي --}}
    <input type="tel" 
           id="{{ $inputId }}" 
           class="iti__tel-input w-full bg-gray-50 border {{ $hasError ? 'border-red-500 bg-red-50' : 'border-gray-200' }} rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand-600/30 focus:border-brand-600 transition-all text-gray-800" 
           data-field="{{ $fieldName }}"
           data-dir="{{ $dir }}"
           data-old="{{ $oldValue }}">
    
    {{-- الحقل المخفي --}}
    <input type="hidden" name="{{ $fieldName }}" id="{{ $inputId }}-hidden" value="{{ $oldValue }}">
</div>

@once
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/intlTelInput.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll('.iti__tel-input').forEach(function (inputEl) {
                var fieldName = inputEl.dataset.field;
                var direction = inputEl.dataset.dir; // rtl or ltr
                var hiddenEl  = document.getElementById('iti-' + fieldName + '-hidden');
                var oldVal    = inputEl.dataset.old || '';

                var iti = window.intlTelInput(inputEl, {
                    utilsScript : "https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js",
                    initialCountry      : '{{ $initialCountry }}',
                    separateDialCode    : true,
                    rtl                 : (direction === 'rtl'), // تفعيل خاصية الـ RTL في المكتبة
                    preferredCountries  : ['sy', 'sa', 'ae', 'jo', 'lb', 'iq', 'eg'],
                    autoPlaceholder     : 'polite'
                });

                if (oldVal) { iti.setNumber(oldVal); }

                function sync() {
                    if (hiddenEl) hiddenEl.value = iti.getNumber();
                }

                inputEl.addEventListener('input', sync);
                inputEl.addEventListener('countrychange', sync);
                if (inputEl.closest('form')) {
                    inputEl.closest('form').addEventListener('submit', sync);
                }
            });
        });
    </script>
@endpush
@endonce