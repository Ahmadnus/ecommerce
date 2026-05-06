@extends('layouts.app')

@section('title', __('app.contact_title') ?? 'اتصل بنا')

@section('content')
@php $isRtl = app()->getLocale() === 'ar'; @endphp

<div class="min-h-screen py-12" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
    <div class="max-w-3xl mx-auto px-4">
        <div class="mb-8">
<h1 class="text-3xl font-black text-black mb-2">
    {{ __('app.contact_title') ?? 'اتصل بنا' }}
</h1>
            <p class="text-gray-400 text-sm">{{ __('app.contact_subtitle') ?? 'أرسل لنا ملاحظاتك أو شكواك أو أي مشكلة تواجهك.' }}</p>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm font-semibold">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('contact.store') }}" method="POST" class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 space-y-4">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold mb-2">{{ __('app.contact_name') ?? 'الاسم' }}</label>
                    <input type="text" name="name" value="{{ old('name', auth()->user()->name ?? '') }}"
                           class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand">
                </div>

                <div>
                    <label class="block text-sm font-bold mb-2">{{ __('app.contact_email') ?? 'البريد الإلكتروني' }}</label>
                    <input type="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}"
                           class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand">
                </div>

                <div>
                    <label class="block text-sm font-bold mb-2">{{ __('app.contact_phone') ?? 'رقم الهاتف' }}</label>
                    <input type="text" name="phone" value="{{ old('phone', auth()->user()->phone ?? '') }}"
                           class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand">
                </div>

                <div>
                    <label class="block text-sm font-bold mb-2">{{ __('app.contact_subject') ?? 'الموضوع' }}</label>
                    <input type="text" name="subject" value="{{ old('subject') }}"
                           class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand">
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold mb-2">{{ __('app.contact_message') ?? 'الرسالة' }}</label>
                <textarea name="message" rows="6"
                          class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand">{{ old('message') }}</textarea>
            </div>

            <button type="submit"
                    class="bg-brand text-white font-bold px-6 py-3 rounded-xl hover:opacity-90 transition">
                {{ __('app.contact_send') ?? 'إرسال' }}
            </button>
        </form>
    </div>
</div>
@endsection