{{--
    Shared form partial used by both create.blade.php and edit.blade.php
    Variables expected:
      $zone        — Zone model
      $schedule    — ZoneDeliverySchedule (null on create)
      $monthOptions — array from controller
      $defaultMonth — string "YYYY-MM" (for create)
      $copyFrom    — optional ZoneDeliverySchedule to pre-fill from (create only)
      $isEdit      — bool
--}}

@php
    $s = $schedule ?? null;

    // Pre-fill values: edit > copy > defaults
    $fillMonth   = old('month',         $s?->month          ?? $defaultMonth ?? '');
    $fillDays    = old('delivery_days', $s?->delivery_days  ?? $copyFrom?->delivery_days ?? '');
    $fillAvail   = old('available_days', $s
        ? ($s->available_days ? implode(', ', $s->available_days) : '')
        : ($copyFrom?->available_days ? implode(', ', $copyFrom->available_days) : ''));
    $fillNotes   = old('notes',     $s?->notes    ?? $copyFrom?->notes ?? '');
    $fillActive  = old('is_active', $s?->is_active ?? true);

    $formAction  = $isEdit
        ? route('admin.zones.schedules.update', [$zone, $s])
        : route('admin.zones.schedules.store',  $zone);
@endphp

@if($errors->any())
<div class="mb-6 p-4 bg-red-50 border border-red-100 rounded-xl text-sm text-red-600">
    <ul class="list-disc list-inside space-y-0.5">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
</div>
@endif

@if(isset($copyFrom) && $copyFrom)
<div class="flex items-center gap-3 bg-blue-50 border border-blue-100 rounded-xl px-4 py-3 mb-5 text-sm text-blue-700">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
    </svg>
    تم نسخ البيانات من جدول <strong>{{ $copyFrom->monthLabel() }}</strong> — راجعها وعدّلها قبل الحفظ.
</div>
@endif

<form action="{{ $formAction }}" method="POST" class="space-y-5">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-7 space-y-6">

        {{-- ── Month picker ───────────────────────────────────────────── --}}
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">
                الشهر <span class="text-red-500">*</span>
            </label>
            <select name="month"
                    class="w-full border border-gray-200 rounded-xl p-3 bg-gray-50 text-sm
                           focus:bg-white focus:outline-none focus:ring-2 focus:border-brand transition-all
                           @error('month') border-red-400 @enderror">
                @foreach($monthOptions as $key => $opt)
                <option value="{{ $key }}"
                        {{ $fillMonth === $key ? 'selected' : '' }}
                        {{ $opt['taken'] && $fillMonth !== $key ? 'disabled' : '' }}
                        class="{{ $opt['taken'] ? 'text-gray-400' : '' }}">
                    {{ $opt['label'] }}
                    @if($opt['taken']) (محجوز) @endif
                </option>
                @endforeach
            </select>
            <p class="mt-1.5 text-[11px] text-gray-400">
                الأشهر المحجوزة محمية بتسجيل موجود — يمكن تعديلها من قائمة الجداول.
            </p>
            @error('month')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        {{-- ── Delivery lead time ──────────────────────────────────────── --}}
        <div class="grid grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    وقت التوصيل (بالأيام)
                    <span class="text-gray-400 font-normal text-xs">(اختياري)</span>
                </label>
                <div class="relative">
                    <input type="number" name="delivery_days"
                           value="{{ $fillDays }}"
                           min="1" max="365" placeholder="مثال: 3"
                           class="w-full border border-gray-200 rounded-xl p-3 bg-gray-50 text-sm
                                  focus:bg-white focus:outline-none focus:ring-2 focus:border-brand transition-all
                                  @error('delivery_days') border-red-400 @enderror">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3
                                 text-[11px] text-gray-400 pointer-events-none font-semibold">يوم</span>
                </div>
                <p class="mt-1 text-[11px] text-gray-400">يظهر للعميل عند اختيار المنطقة.</p>
                @error('delivery_days')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            {{-- ── Active toggle ───────────────────────────────────────── --}}
            <div class="flex items-end pb-0.5">
                <label class="flex items-center gap-3 cursor-pointer p-3 border border-gray-200
                              rounded-xl bg-gray-50 hover:bg-white transition-colors w-full">
                    <input type="checkbox" name="is_active" value="1"
                           {{ $fillActive ? 'checked' : '' }}
                           class="w-4 h-4 text-brand border-gray-300 rounded focus:ring-brand/30">
                    <div>
                        <p class="text-sm font-semibold text-gray-800">تفعيل الجدول</p>
                        <p class="text-xs text-gray-400">يظهر في صفحة الدفع</p>
                    </div>
                </label>
            </div>
        </div>

        {{-- ── Available days ──────────────────────────────────────────── --}}
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">
                أيام التوصيل المتاحة
                <span class="text-gray-400 font-normal text-xs">(اختياري — اتركه فارغاً لتفعيل جميع الأيام)</span>
            </label>
            <input type="text" name="available_days"
                   id="available-days-input"
                   value="{{ $fillAvail }}"
                   placeholder="مثال: 1, 5, 10, 15, 20, 25"
                   dir="ltr"
                   class="w-full border border-gray-200 rounded-xl p-3 bg-gray-50 text-sm font-mono
                          focus:bg-white focus:outline-none focus:ring-2 focus:border-brand transition-all
                          @error('available_days') border-red-400 @enderror">
            <p class="mt-1.5 text-[11px] text-gray-400">
                أدخل أرقام أيام الشهر مفصولة بفاصلة (1–31). مثال: 1, 5, 10, 15, 20, 25
            </p>
            @error('available_days')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror

            {{-- Day picker grid (interactive) --}}
            <div class="mt-3">
                <p class="text-[11px] font-bold text-gray-500 mb-2 uppercase tracking-wide">
                    أو اختر من هنا:
                </p>
                <div class="grid grid-cols-7 gap-1" id="day-picker">
                    @for($d = 1; $d <= 31; $d++)
                    <button type="button"
                            data-day="{{ $d }}"
                            onclick="toggleDay({{ $d }})"
                            class="day-btn h-8 rounded-lg text-xs font-bold border border-gray-200
                                   bg-gray-50 text-gray-500 hover:border-brand hover:text-brand
                                   transition-all select-none">
                        {{ $d }}
                    </button>
                    @endfor
                </div>
                <div class="flex gap-2 mt-2">
                    <button type="button" onclick="selectAllDays()"
                            class="text-[11px] font-bold text-brand hover:underline">
                        تحديد الكل
                    </button>
                    <span class="text-gray-300">·</span>
                    <button type="button" onclick="clearAllDays()"
                            class="text-[11px] font-bold text-gray-400 hover:text-red-500 hover:underline">
                        مسح الكل
                    </button>
                </div>
            </div>
        </div>

        {{-- ── Notes ──────────────────────────────────────────────────── --}}
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">
                ملاحظات داخلية
                <span class="text-gray-400 font-normal text-xs">(اختياري — لا تظهر للعملاء)</span>
            </label>
            <input type="text" name="notes"
                   value="{{ $fillNotes }}"
                   placeholder="مثال: جدول رمضان، تعديل لأجازة رسمية..."
                   class="w-full border border-gray-200 rounded-xl p-3 bg-gray-50 text-sm
                          focus:bg-white focus:outline-none focus:ring-2 focus:border-brand transition-all">
            @error('notes')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('admin.zones.schedules.index', $zone) }}"
           class="px-6 py-2.5 text-sm font-bold text-gray-500 hover:text-red-500 transition-colors">
            إلغاء
        </a>
        <button type="submit"
                class="bg-brand text-white px-8 py-2.5 rounded-xl font-bold text-sm
                       shadow-lg hover:opacity-90 hover:scale-[1.02] transition-all active:scale-95">
            {{ $isEdit ? 'حفظ التغييرات' : 'حفظ الجدول' }}
        </button>
    </div>
</form>

@push('scripts')
<script>
(function () {
    // Parse existing days from input and highlight them in the picker
    function parseDays() {
        const raw = document.getElementById('available-days-input').value;
        return raw.split(',').map(d => parseInt(d.trim())).filter(d => d >= 1 && d <= 31);
    }

    function refreshPicker() {
        const selected = parseDays();
        document.querySelectorAll('.day-btn').forEach(btn => {
            const day = parseInt(btn.dataset.day);
            const on  = selected.includes(day);
            btn.style.background   = on ? 'var(--brand-color, #0ea5e9)' : '';
            btn.style.color        = on ? '#fff' : '';
            btn.style.borderColor  = on ? 'var(--brand-color, #0ea5e9)' : '';
        });
    }

    window.toggleDay = function (day) {
        let days = parseDays();
        const idx = days.indexOf(day);
        if (idx === -1) { days.push(day); } else { days.splice(idx, 1); }
        days.sort((a, b) => a - b);
        document.getElementById('available-days-input').value = days.join(', ');
        refreshPicker();
    };

    window.selectAllDays = function () {
        const all = Array.from({length: 31}, (_, i) => i + 1);
        document.getElementById('available-days-input').value = all.join(', ');
        refreshPicker();
    };

    window.clearAllDays = function () {
        document.getElementById('available-days-input').value = '';
        refreshPicker();
    };

    // Sync manual text input → picker
    document.getElementById('available-days-input')
        .addEventListener('input', refreshPicker);

    // Init
    refreshPicker();
})();
</script>
@endpush