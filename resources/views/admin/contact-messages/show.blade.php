@extends('layouts.admin')
@section('title', 'تفاصيل الرسالة')

@section('admin-content')
<div class="max-w-4xl mx-auto bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-gray-900">{{ $contactMessage->name }}</h1>
            <p class="text-sm text-gray-500">{{ $contactMessage->subject ?? 'بدون موضوع' }}</p>
        </div>

        <div class="text-xs text-gray-400 text-left">
            <p>{{ $contactMessage->created_at->format('Y/m/d') }}</p>
            <p>{{ $contactMessage->created_at->format('H:i') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
        <div><span class="font-bold">الإيميل:</span> {{ $contactMessage->email ?? '-' }}</div>
        <div><span class="font-bold">الهاتف:</span> {{ $contactMessage->phone ?? '-' }}</div>
        <div><span class="font-bold">الحالة:</span> {{ $contactMessage->is_read ? 'مقروءة' : 'جديدة' }}</div>
        <div><span class="font-bold">المستخدم:</span> {{ $contactMessage->user?->name ?? '-' }}</div>
    </div>

    <div class="pt-4 border-t">
        <h3 class="font-bold mb-2">الرسالة</h3>
        <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 whitespace-pre-wrap text-sm leading-7">
            {{ $contactMessage->message }}
        </div>
    </div>

    <div class="flex gap-3">
        <form action="{{ route('admin.contact-messages.read', $contactMessage) }}" method="POST">
            @csrf
            @method('PATCH')
            <button class="px-4 py-2 rounded-xl bg-blue-600 text-white font-bold">تعليم كمقروءة</button>
        </form>

        <form action="{{ route('admin.contact-messages.destroy', $contactMessage) }}" method="POST"
              onsubmit="return confirm('حذف الرسالة؟')">
            @csrf
            @method('DELETE')
            <button class="px-4 py-2 rounded-xl bg-red-600 text-white font-bold">حذف</button>
        </form>
    </div>
</div>
@endsection