@extends('layouts.admin')
@section('title', 'رسائل التواصل')

@section('admin-content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b flex items-center justify-between">
        <h3 class="font-bold text-gray-800">رسائل التواصل</h3>
        <span class="text-sm text-gray-500">{{ $messages->total() }} رسالة</span>
    </div>

    <div class="divide-y">
        @forelse($messages as $message)
            <a href="{{ route('admin.contact-messages.show', $message) }}" class="block p-5 hover:bg-gray-50 transition">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <p class="font-bold text-gray-900">{{ $message->name }}</p>
                            @if(! $message->is_read)
                                <span class="text-[10px] px-2 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-100">جديدة</span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-500">{{ $message->subject ?? 'بدون موضوع' }}</p>
                        <p class="text-xs text-gray-400 mt-1 line-clamp-2">{{ $message->message }}</p>
                    </div>

                    <div class="text-left text-xs text-gray-400 flex-shrink-0">
                        <p>{{ $message->created_at->format('Y/m/d') }}</p>
                        <p>{{ $message->created_at->format('H:i') }}</p>
                    </div>
                </div>
            </a>
        @empty
            <div class="p-12 text-center text-gray-400">لا توجد رسائل بعد</div>
        @endforelse
    </div>

    <div class="p-5 border-t">
        {{ $messages->links() }}
    </div>
</div>
@endsection