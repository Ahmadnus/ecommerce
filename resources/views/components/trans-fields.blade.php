{{--
    <x-trans-fields>
        Wraps translatable input pairs.
        Reads session('admin_input_mode', 'both') to decide which slots to render.

        Usage:
            <x-trans-fields label="اسم المنتج" required>
                <x-slot name="ar">
                    <input type="text" name="name[ar]" value="..." dir="rtl" ...>
                </x-slot>
                <x-slot name="en">
                    <input type="text" name="name[en]" value="..." dir="ltr" ...>
                </x-slot>
            </x-trans-fields>

    Props:
        label     — field label shown above the inputs
        required  — bool, adds red asterisk to label
        hint      — optional helper text below the field
--}}

@props([
    'label'    => '',
    'required' => false,
    'hint'     => '',
])

@php
    $mode     = session('admin_input_mode', 'both');
    $showAr   = in_array($mode, ['ar', 'both']);
    $showEn   = in_array($mode, ['en', 'both']);
    $showBoth = $mode === 'both';
@endphp

<div class="trans-field-group">

    @if($label)
    <label class="block text-sm font-bold text-gray-700 mb-2">
        {{ $label }}
        @if($required) <span class="text-red-500">*</span> @endif
        {{-- Small mode indicator so admins know which languages are active --}}
        <span class="mr-2 text-[10px] font-normal text-gray-400 uppercase tracking-wider">
            @if($mode === 'ar') [ عربي فقط ]
            @elseif($mode === 'en') [ English only ]
            @else [ AR + EN ]
            @endif
        </span>
    </label>
    @endif

    <div class="{{ $showBoth ? 'grid grid-cols-1 md:grid-cols-2 gap-4' : '' }}">

        @if($showAr)
        <div class="{{ !$showBoth ? 'w-full' : '' }}">
            <div class="flex items-center gap-1.5 mb-1">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-wider">AR</span>
                <span class="h-px flex-1 bg-gray-100"></span>
            </div>
            {{ $ar }}
        </div>
        @endif

        @if($showEn)
        <div class="{{ !$showBoth ? 'w-full' : '' }}">
            <div class="flex items-center gap-1.5 mb-1">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-wider">EN</span>
                <span class="h-px flex-1 bg-gray-100"></span>
            </div>
            {{ $en }}
        </div>
        @endif

    </div>

    @if($hint)
    <p class="text-xs text-gray-400 mt-1">{{ $hint }}</p>
    @endif

</div>