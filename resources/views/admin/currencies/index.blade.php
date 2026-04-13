{{-- resources/views/admin/currencies/index.blade.php --}}
@extends('layouts.admin')
@section('title', 'العملات')

@section('admin-content')

<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">إدارة العملات</h1>
        <p class="text-gray-500 text-sm mt-1">حدد العملات المدعومة وأسعار الصرف.</p>
    </div>
    <a href="{{ route('admin.currencies.create') }}"
       class="inline-flex items-center gap-2 bg-brand text-white font-bold px-5 py-2.5 rounded-xl hover:opacity-90 transition shadow-sm text-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        إضافة عملة
    </a>
</div>

@if($currencies->isEmpty())
<div class="bg-white border border-gray-200 rounded-2xl p-16 text-center shadow-sm">
    <p class="text-gray-500 font-semibold">لا توجد عملات بعد.</p>
    <a href="{{ route('admin.currencies.create') }}"
       class="mt-3 inline-block text-sm font-bold text-brand hover:underline">إضافة عملة</a>
</div>

@else
<div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
    <table class="w-full text-right text-sm">
        <thead class="bg-gray-50/80 border-b border-gray-200">
            <tr>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">العملة</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">الرمز</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">سعر الصرف</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">الحالة</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-left">العمليات</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($currencies as $currency)
            <tr class="hover:bg-gray-50/60 transition-colors group">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white font-black text-xs flex-shrink-0"
                             style="background:var(--brand-color)">
                            {{ $currency->symbol }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $currency->name }}</p>
                            @if($currency->is_base)
                            <span class="text-[10px] font-black text-amber-600 bg-amber-100 px-1.5 py-0.5 rounded">
                                أساسية
                            </span>
                            @endif
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <span class="font-mono text-xs font-bold bg-gray-100 text-gray-600 px-2 py-1 rounded-lg">
                        {{ $currency->code }}
                    </span>
                </td>
                <td class="px-6 py-4 text-center tabular-nums font-semibold text-gray-700">
                    {{ number_format((float) $currency->exchange_rate, 4) }}
                </td>
                <td class="px-6 py-4 text-center">
                    @if($currency->is_active)
                    <span class="inline-flex items-center gap-1.5 bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">
                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>نشطة
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1.5 bg-gray-100 text-gray-500 text-xs font-bold px-3 py-1 rounded-full">
                        <span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span>معطلة
                    </span>
                    @endif
                </td>
                <td class="px-6 py-4 text-left">
                    <div class="flex justify-end items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        <a href="{{ route('admin.currencies.edit', $currency) }}"
                           class="p-2 text-blue-500 hover:bg-blue-50 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                        </a>
                        @if(!$currency->is_base)
                        <form action="{{ route('admin.currencies.destroy', $currency) }}" method="POST"
                              onsubmit="return confirm('حذف عملة \'{{ addslashes($currency->name) }}\'؟')">
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