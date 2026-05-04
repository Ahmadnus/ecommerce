@extends('layouts.admin')
@section('title', 'إعداد لغة الموقع')

@section('admin-content')
<div class="max-w-lg mx-auto mt-8">
    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-8">

        <h2 class="text-2xl font-black text-gray-900 mb-1">إعداد لغة الموقع</h2>
        <p class="text-gray-500 text-sm mb-8">اختر اللغة التي يعمل بها الموقع للزوار</p>

        @if(session('success'))
        <div class="mb-6 px-4 py-3 bg-emerald-50 text-emerald-700 rounded-xl text-sm font-bold">
            {{ session('success') }}
        </div>
        @endif

        <form method="POST" action="{{ route('admin.locale-mode.update') }}">
            @csrf
            <div class="grid grid-cols-3 gap-4 mb-8">
                @foreach([
                    ['value' => 'ar',   'flag' => '🇸🇦', 'label' => 'عربي فقط'],
                    ['value' => 'en',   'flag' => '🇬🇧', 'label' => 'English Only'],
                    ['value' => 'both', 'flag' => '🌐',  'label' => 'كلاهما'],
                ] as $opt)
                <label class="cursor-pointer">
                    <input type="radio" name="mode" value="{{ $opt['value'] }}"
                           {{ $mode === $opt['value'] ? 'checked' : '' }}
                           class="peer sr-only">
                    <div class="flex flex-col items-center gap-2 p-5 rounded-2xl border-2 border-gray-100
                                peer-checked:border-blue-500 peer-checked:bg-blue-50
                                hover:border-gray-200 transition-all text-center">
                        <span class="text-3xl">{{ $opt['flag'] }}</span>
                        <span class="text-sm font-black text-gray-700">{{ $opt['label'] }}</span>
                    </div>
                </label>
                @endforeach
            </div>

            <button type="submit"
                    class="w-full py-3 rounded-xl text-white text-sm font-black hover:opacity-90 transition-all"
                    style="background:var(--brand-color)">
                حفظ الإعداد
            </button>
        </form>
    </div>
</div>
@endsection