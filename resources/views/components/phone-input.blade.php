{{-- resources/views/components/phone-input.blade.php --}}

@php
    $fieldName = $fieldName ?? 'phone_full';
    $oldValue  = $oldValue ?? '';
    $hasError  = $hasError ?? false;
    $inputId   = 'iti-' . $fieldName;
@endphp

@once
@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/css/intlTelInput.css">

<style>
.iti { width: 100% !important; }

.iti__tel-input {
    padding-top: 0.75rem !important;
    padding-bottom: 0.75rem !important;
}

.iti__selected-flag {
    pointer-events: none; /* يمنع تغيير الدولة */
    border-left: 1px solid #e2e8f0;
}

.iti__country-list {
    display: none !important; /* إخفاء قائمة الدول بالكامل */
}
</style>
@endpush
@endonce

<div class="relative w-full">
    <input type="tel"
           id="{{ $inputId }}"
           class="iti__tel-input w-full bg-gray-50 border {{ $hasError ? 'border-red-500' : 'border-gray-200' }} rounded-xl px-4 py-3"
           data-old="{{ $oldValue }}">

    <input type="hidden" name="{{ $fieldName }}" id="{{ $inputId }}-hidden" value="{{ $oldValue }}">
</div>

@once
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/intlTelInput.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {

    document.querySelectorAll('.iti__tel-input').forEach(function (inputEl) {

        var hiddenEl = document.getElementById(inputEl.id + '-hidden');
        var oldVal   = inputEl.dataset.old || '';

        var iti = window.intlTelInput(inputEl, {
            initialCountry: "jo",     // 🇯🇴 الأردن فقط
            onlyCountries: ["jo"],    // منع أي دولة أخرى
            separateDialCode: true,
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js"
        });

        // إذا في قيمة قديمة
        if (oldVal) iti.setNumber(oldVal);

        function sync() {
            hiddenEl.value = iti.getNumber(); // +9627xxxxxxx
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