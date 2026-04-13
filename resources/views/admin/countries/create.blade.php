@extends('layouts.admin')
@section('title', 'إضافة دولة')

@section('admin-content')
<div class="max-w-2xl mx-auto">

    <div class="mb-6">
        <a href="{{ route('admin.countries.index') }}"
           class="inline-flex items-center gap-1.5 text-sm font-semibold text-gray-500 hover:text-brand transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            العودة للدول
        </a>
    </div>

    @if($errors->any())
    <div class="mb-6 p-4 bg-red-50 border border-red-100 rounded-xl text-sm text-red-600">
        <ul class="list-disc list-inside space-y-0.5">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.countries.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-7 space-y-5">
            <h2 class="font-bold text-gray-800 text-base border-b border-gray-100 pb-4">بيانات الدولة</h2>

            {{-- Name + Name EN --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        الاسم بالعربية <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           required placeholder="سوريا"
                           class="w-full border border-gray-200 rounded-xl p-3 bg-gray-50 text-sm
                                  focus:bg-white focus:outline-none focus:ring-2 focus:border-brand transition-all
                                  @error('name') border-red-400 @enderror">
                    @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">الاسم بالإنجليزية</label>
                    <input type="text" name="name_en" value="{{ old('name_en') }}"
                           placeholder="Syria"
                           class="w-full border border-gray-200 rounded-xl p-3 bg-gray-50 text-sm
                                  focus:bg-white focus:outline-none focus:ring-2 focus:border-brand transition-all">
                </div>
            </div>

            {{-- Code + Sort --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        رمز الدولة (ISO) <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="code" value="{{ old('code') }}"
                           required maxlength="3" placeholder="SY"
                           class="w-full border border-gray-200 rounded-xl p-3 bg-gray-50 text-sm font-mono uppercase
                                  focus:bg-white focus:outline-none focus:ring-2 focus:border-brand transition-all
                                  @error('code') border-red-400 @enderror">
                    @error('code')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">ترتيب العرض</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}"
                           min="0"
                           class="w-full border border-gray-200 rounded-xl p-3 bg-gray-50 text-sm
                                  focus:bg-white focus:outline-none focus:ring-2 focus:border-brand transition-all">
                </div>
            </div>

            {{-- Currencies --}}
            @if($currencies->isNotEmpty())
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-3">العملات المقبولة</label>
                <div class="space-y-2 max-h-48 overflow-y-auto border border-gray-200 rounded-xl p-3 bg-gray-50">
                    @foreach($currencies as $currency)
                    <label class="flex items-center gap-3 cursor-pointer hover:bg-white rounded-lg px-2 py-1.5 transition-colors">
                        <input type="checkbox" name="currencies[]" value="{{ $currency->id }}"
                               {{ in_array($currency->id, old('currencies', [])) ? 'checked' : '' }}
                               class="w-4 h-4 text-brand border-gray-300 rounded focus:ring-brand/30"
                               onchange="updateDefaultOptions()">
                        <span class="font-mono text-xs font-bold text-gray-500">{{ $currency->code }}</span>
                        <span class="text-sm text-gray-700">{{ $currency->name }}</span>
                        <span class="text-xs text-gray-400">({{ $currency->symbol }})</span>
                    </label>
                    @endforeach
                </div>
                <div id="default-currency-wrap" class="mt-3 hidden">
                    <label class="block text-xs font-bold text-gray-600 mb-2">العملة الافتراضية للدولة</label>
                    <select name="default_currency"
                            class="w-full border border-gray-200 rounded-xl p-2.5 bg-gray-50 text-sm
                                   focus:outline-none focus:ring-2 focus:border-brand transition-all"
                            id="default-currency-select">
                        <option value="">اختر العملة الافتراضية...</option>
                        @foreach($currencies as $currency)
                        <option value="{{ $currency->id }}" class="currency-opt hidden">
                            {{ $currency->code }} — {{ $currency->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
            @endif

            {{-- is_active --}}
            <label class="flex items-center gap-3 cursor-pointer p-3 border border-gray-200 rounded-xl bg-gray-50 hover:bg-white transition-colors">
                <input type="checkbox" name="is_active" value="1"
                       {{ old('is_active', true) ? 'checked' : '' }}
                       class="w-5 h-5 text-brand border-gray-300 rounded focus:ring-brand/30">
                <div>
                    <p class="text-sm font-semibold text-gray-800">تفعيل الدولة</p>
                    <p class="text-xs text-gray-400">تظهر في قائمة الشحن عند الدفع</p>
                </div>
            </label>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.countries.index') }}"
               class="px-6 py-2.5 text-sm font-bold text-gray-500 hover:text-red-500 transition-colors">إلغاء</a>
            <button type="submit"
                    class="bg-brand text-white px-8 py-2.5 rounded-xl font-bold text-sm shadow-lg hover:opacity-90 hover:scale-[1.02] transition-all active:scale-95">
                حفظ الدولة
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function updateDefaultOptions() {
    const checked = [...document.querySelectorAll('input[name="currencies[]"]:checked')];
    const wrap    = document.getElementById('default-currency-wrap');
    const select  = document.getElementById('default-currency-select');

    if (checked.length === 0) { wrap.classList.add('hidden'); return; }
    wrap.classList.remove('hidden');

    const checkedIds = checked.map(c => c.value);
    select.querySelectorAll('.currency-opt').forEach(opt => {
        opt.classList.toggle('hidden', !checkedIds.includes(opt.value));
    });

    // Auto-select first if nothing selected
    if (!checkedIds.includes(select.value)) {
        select.value = checkedIds[0] || '';
    }
}

// Run on load in case old() repopulates checkboxes
document.addEventListener('DOMContentLoaded', updateDefaultOptions);
</script>
@endpush