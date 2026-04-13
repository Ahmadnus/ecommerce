{{-- resources/views/pages/dynamic.blade.php --}}
{{--
    Public dynamic page — renders content stored in the database.
    {!! $page->content !!} is intentionally used here because the content
    is ONLY editable by authenticated admins, making it trusted HTML.
    Never render user-submitted content with {!! !!}.
--}}

@extends('layouts.app')
@section('title', $page->name)

@push('head')
<style>
    /* ── Rich text content styles ─────────────────────────────────── */
    .page-content h1,
    .page-content h2,
    .page-content h3,
    .page-content h4 {
        font-family: var(--font-display, serif);
        font-weight: 700;
        color: #111827;
        margin-top: 1.8em;
        margin-bottom: .6em;
        line-height: 1.3;
    }
    .page-content h1 { font-size: 1.75rem; }
    .page-content h2 { font-size: 1.35rem; border-bottom: 2px solid #f3f4f6; padding-bottom: .4em; }
    .page-content h3 { font-size: 1.1rem; }

    .page-content p  { margin-bottom: 1.1em; line-height: 1.8; color: #374151; }
    .page-content ul,
    .page-content ol { margin: 1em 0 1em 1.5em; color: #374151; line-height: 1.8; }
    .page-content li { margin-bottom: .4em; }

    .page-content a {
        color: var(--brand-color, #0ea5e9);
        text-decoration: underline;
        text-underline-offset: 3px;
    }
    .page-content a:hover { opacity: .8; }

    .page-content blockquote {
        border-right: 4px solid var(--brand-color, #0ea5e9);
        padding: .8em 1.2em;
        margin: 1.2em 0;
        background: #f8fafc;
        color: #4b5563;
        border-radius: 0 8px 8px 0;
        font-style: italic;
    }

    .page-content table {
        width: 100%; border-collapse: collapse;
        margin: 1.5em 0; font-size: .9rem;
    }
    .page-content th,
    .page-content td {
        border: 1px solid #e5e7eb;
        padding: .65em 1em;
        text-align: right;
    }
    .page-content th { background: #f9fafb; font-weight: 700; }
    .page-content tr:hover td { background: #fafafa; }

    .page-content hr {
        border: none; border-top: 1px solid #e5e7eb;
        margin: 2em 0;
    }
</style>
@endpush

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 py-12 md:py-16" dir="rtl">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-1.5 text-xs text-gray-400 mb-8">
        <a href="{{ url('/') }}" class="hover:text-gray-700 transition-colors">الرئيسية</a>
        <span>/</span>
        <span class="text-gray-700 font-semibold">{{ $page->name }}</span>
    </nav>

    {{-- Page header --}}
    <header class="mb-10 pb-8 border-b border-gray-100">
        <h1 class="font-display text-3xl md:text-4xl font-bold text-gray-900 leading-tight">
            {{ $page->name }}
        </h1>
        <p class="text-xs text-gray-400 mt-3">
            آخر تحديث: {{ $page->updated_at->translatedFormat('d F Y') }}
        </p>
    </header>

    {{-- Rich text content — admin-only, trusted HTML --}}
    <article class="page-content">
        {!! $page->content !!}
    </article>

    {{-- Back to home --}}
    <div class="mt-12 pt-8 border-t border-gray-100">
        <a href="{{ url('/') }}"
           class="inline-flex items-center gap-2 text-sm font-bold transition-colors hover:underline"
           style="color: var(--brand-color, #0ea5e9)">
            <svg class="w-4 h-4 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            العودة للرئيسية
        </a>
    </div>

</div>
@endsection