@extends('layouts.admin')
@section('title', 'الدول')

@extends('layouts.admin')
@section('title', 'الدول')

@section('admin-content')

<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">إدارة الدول</h1>
        <p class="text-gray-500 text-sm mt-1">حدد الدول المدعومة للشحن مع مناطقها وأسعارها.</p>
    </div>
    <a href="{{ route('admin.countries.create') }}"
       class="inline-flex items-center gap-2 bg-brand text-white font-bold px-5 py-2.5 rounded-xl hover:opacity-90 transition shadow-sm text-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        إضافة دولة
    </a>
</div>

@if($countries->isEmpty())
<div class="bg-white border border-gray-200 rounded-2xl p-16 text-center shadow-sm">
    <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
        </svg>
    </div>
    <p class="text-gray-600 font-semibold mb-1">لا توجد دول مضافة بعد</p>
    <p class="text-gray-400 text-sm mb-4">ابدأ بإضافة الدول التي تشحن إليها.</p>
    <a href="{{ route('admin.countries.create') }}"
       class="inline-flex items-center gap-2 bg-brand text-white font-bold px-5 py-2 rounded-xl hover:opacity-90 transition text-sm">
        إضافة أول دولة
    </a>
</div>

@else
<div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
    <table class="w-full text-right text-sm">
        <thead class="bg-gray-50/80 border-b border-gray-200">
            <tr>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">الدولة</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">الرمز</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">المناطق</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">الترتيب</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">الحالة</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-left">العمليات</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($countries as $country)
            <tr class="hover:bg-gray-50/60 transition-colors group">

                {{-- Name --}}
                <td class="px-6 py-4">
                    <div class="flex items-center gap-2">
                        <div>
                            <p class="font-semibold text-gray-900">{{ $country->name }}</p>
                            @if($country->name_en)
                                <p class="text-xs text-gray-400 mt-0.5">{{ $country->name_en }}</p>
                            @endif
                        </div>

                        @if($country->is_system)
                        <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-amber-50 text-amber-600
                                     border border-amber-200 px-2 py-0.5 rounded-full">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                            </svg>
                            نظامية
                        </span>
                        @endif
                    </div>
                </td>

                {{-- Code --}}
                <td class="px-6 py-4">
                    <span class="font-mono text-xs font-bold bg-gray-100 text-gray-600 px-2 py-1 rounded-lg">
                        {{ $country->code }}
                    </span>
                </td>

                {{-- Zones count --}}
                <td class="px-6 py-4 text-center">
                    <a href="{{ route('admin.countries.zones.index', $country) }}"
                       class="inline-flex items-center gap-1 font-semibold text-brand hover:underline text-sm">
                        {{ $country->zones_count }}
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                </td>

                {{-- Sort --}}
                <td class="px-6 py-4 text-center text-gray-500 tabular-nums">
                    {{ $country->sort_order }}
                </td>

                {{-- Status --}}
                <td class="px-6 py-4 text-center">
                    @if($country->is_active)
                    <span class="inline-flex items-center gap-1.5 bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">
                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                        نشطة
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1.5 bg-gray-100 text-gray-500 text-xs font-bold px-3 py-1 rounded-full">
                        <span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span>
                        معطلة
                    </span>
                    @endif
                </td>

                {{-- Actions --}}
                <td class="px-6 py-4 text-left">
                    <div class="flex justify-end items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        {{-- Manage Zones --}}
                        <a href="{{ route('admin.countries.zones.index', $country) }}"
                           class="inline-flex items-center gap-1 px-3 py-1.5 bg-brand/10 text-brand text-xs font-bold rounded-lg hover:bg-brand/20 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            </svg>
                            المناطق
                        </a>

                        {{-- Edit --}}
                        <a href="{{ route('admin.countries.edit', $country) }}"
                           class="p-2 text-blue-500 hover:bg-blue-50 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                        </a>

                        {{-- Delete --}}
                        @if($country->is_system)
                            <span class="p-2 text-gray-300 cursor-not-allowed"
                                  title="لا يمكن حذف هذه الدولة لأنها نظامية">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </span>
                        @else
                            <form action="{{ route('admin.countries.destroy', $country) }}" method="POST"
                                  onsubmit="return confirm('حذف دولة \'{{ addslashes($country->name) }}\' وكل مناطقها؟')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-red-400 hover:bg-red-50 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        @endif
                    </div>
                </td>

            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

@endsection