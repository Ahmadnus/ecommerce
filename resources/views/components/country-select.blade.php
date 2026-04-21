{{--
    components/country-select.blade.php
    ─────────────────────────────────────────────────────────────────────────────
    A reusable, searchable country dropdown that shows only admin-added active
    countries, with labels formatted as "اسم الدولة +رمز" (e.g. "سوريا +963").

    Usage:
        @include('components.country-select', [
            'countries'   => $countries,          // Collection<Country> (active, ordered)
            'selected'    => old('country_id', $user->country_id ?? ''),
            'name'        => 'country_id',        // form field name  (default: country_id)
            'label'       => 'الدولة',            // label text       (default: 'الدولة')
            'required'    => true,                // adds * and required attr
            'hasError'    => $errors->has('country_id'),
            'placeholder' => 'اختر دولتك...',
        ])
    ─────────────────────────────────────────────────────────────────────────────
--}}

@php
    $fieldName   = $name        ?? 'country_id';
    $fieldLabel  = $label       ?? 'الدولة';
    $isRequired  = $required    ?? false;
    $placeholder = $placeholder ?? 'اختر دولتك...';
    $selectedId  = $selected    ?? old($fieldName, '');
    $hasError    = $hasError    ?? false;
    $inputId     = 'cs-' . $fieldName;
@endphp

<div x-data="countrySelect('{{ $inputId }}')" class="country-select-wrapper">

    {{-- Label --}}
    <label for="{{ $inputId }}-display" class="block text-sm font-semibold text-gray-700 mb-1.5">
        {{ $fieldLabel }}
        @if($isRequired) <span class="text-red-500">*</span> @endif
    </label>

    {{-- Hidden real input submitted to Laravel --}}
    <input type="hidden"
           name="{{ $fieldName }}"
           id="{{ $inputId }}"
           value="{{ $selectedId }}"
           x-ref="hiddenInput">

    {{-- Custom searchable trigger --}}
    <div class="relative" @click.outside="close()">

        {{-- Trigger button --}}
        <button type="button"
                id="{{ $inputId }}-display"
                @click="toggle()"
                @keydown.escape="close()"
                class="w-full flex items-center justify-between gap-2 text-sm
                       border rounded-xl px-4 py-3 text-right transition-all outline-none
                       {{ $hasError ? 'border-red-400 bg-red-50/40' : 'border-gray-200 bg-gray-50 hover:bg-white' }}
                       focus:ring-2 focus:border-brand focus:bg-white"
                :class="open ? 'border-brand bg-white ring-2 ring-brand/20' : ''">

            <span class="flex items-center gap-2 min-w-0 flex-1">
                {{-- Flag emoji placeholder (shown when a country is selected) --}}
                <span x-show="selectedLabel" class="font-semibold text-gray-800 truncate" x-text="selectedLabel"></span>
                <span x-show="!selectedLabel" class="text-gray-400">{{ $placeholder }}</span>
            </span>

            {{-- Chevron --}}
            <svg class="w-4 h-4 text-gray-400 flex-shrink-0 transition-transform"
                 :class="open ? 'rotate-180' : ''"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        {{-- Dropdown panel --}}
        <div x-show="open"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="absolute top-full mt-1.5 left-0 right-0 z-50
                    bg-white border border-gray-200 rounded-xl shadow-xl
                    overflow-hidden"
             style="display:none">

            {{-- Search box --}}
            <div class="p-2 border-b border-gray-100">
                <div class="relative">
                    <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400 pointer-events-none"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text"
                           x-model="search"
                           @keydown.enter.prevent
                           @keydown.arrow-down.prevent="moveDown()"
                           @keydown.arrow-up.prevent="moveUp()"
                           placeholder="ابحث عن دولة..."
                           class="w-full bg-gray-50 border border-gray-200 rounded-lg
                                  text-sm py-2 pr-9 pl-3
                                  focus:outline-none focus:ring-2 focus:border-brand focus:bg-white transition-all"
                           x-ref="searchInput">
                </div>
            </div>

            {{-- Options list --}}
            <ul class="max-h-52 overflow-y-auto py-1" role="listbox">

                {{-- Empty state --}}
                <template x-if="filtered.length === 0">
                    <li class="px-4 py-3 text-sm text-gray-400 text-center">لا توجد نتائج</li>
                </template>

                <template x-for="(country, index) in filtered" :key="country.id">
                    <li role="option"
                        :aria-selected="country.id === value"
                        class="flex items-center justify-between gap-3 px-4 py-2.5 cursor-pointer
                               text-sm transition-colors"
                        :class="{
                            'bg-brand text-white': country.id === value,
                            'bg-gray-50': cursor === index && country.id !== value,
                            'hover:bg-gray-50': country.id !== value,
                        }"
                        @click="select(country)"
                        @mouseenter="cursor = index">

                        {{-- Country name --}}
                        <span class="font-semibold" x-text="country.name"></span>

                        {{-- Calling code badge --}}
                        <span x-show="country.calling_code"
                              class="font-mono text-xs flex-shrink-0 px-2 py-0.5 rounded-full"
                              :class="country.id === value
                                       ? 'bg-white/20 text-white'
                                       : 'bg-gray-100 text-gray-500'"
                              x-text="'+' + country.calling_code"></span>
                    </li>
                </template>
            </ul>
        </div>
    </div>

    @error($fieldName)
    <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
        </svg>
        {{ $message }}
    </p>
    @enderror
</div>

@once
@push('scripts')
<script>
document.addEventListener('alpine:init', function () {
    Alpine.data('countrySelect', function (inputId) {
        /*
         * All countries are passed from Blade as a JSON array embedded in
         * the page. We read them from a <script> tag injected by the
         * country-select-data partial (see below), or fall back to an
         * empty array.
         */
        var allCountries = window.__countriesData || [];

        return {
            open:          false,
            search:        '',
            value:         document.getElementById(inputId)?.value ?? '',
            selectedLabel: '',
            cursor:        -1,

            get filtered() {
                if (!this.search) return allCountries;
                var q = this.search.toLowerCase();
                return allCountries.filter(function (c) {
                    return c.name.toLowerCase().includes(q) ||
                           (c.name_en && c.name_en.toLowerCase().includes(q)) ||
                           (c.calling_code && c.calling_code.startsWith(q));
                });
            },

            init() {
                // Pre-select from old() / existing value
                if (this.value) {
                    var match = allCountries.find(c => String(c.id) === String(this.value));
                    if (match) this.selectedLabel = this.labelFor(match);
                }
            },

            labelFor(country) {
                return country.calling_code
                    ? country.name + ' +' + country.calling_code
                    : country.name;
            },

            select(country) {
                this.value         = country.id;
                this.selectedLabel = this.labelFor(country);
                document.getElementById(inputId).value = country.id;
                this.close();
            },

            toggle() {
                this.open = !this.open;
                if (this.open) {
                    this.$nextTick(() => this.$refs.searchInput?.focus());
                }
            },

            close() {
                this.open   = false;
                this.search = '';
                this.cursor = -1;
            },

            moveDown() {
                if (this.cursor < this.filtered.length - 1) this.cursor++;
            },

            moveUp() {
                if (this.cursor > 0) this.cursor--;
            },
        };
    });
});
</script>
@endpush
@endonce