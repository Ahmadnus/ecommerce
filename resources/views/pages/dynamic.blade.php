{{--
    Public dynamic page — renders trusted admin-authored HTML content.
    {!! !!} is safe here: only authenticated admins can write this content.
--}}
@extends('layouts.app')

@php
    $locale   = app()->getLocale();           // 'ar' or 'en'
    $isRtl    = in_array($locale, ['ar', 'fa', 'he', 'ur']);
    $dir      = $isRtl ? 'rtl' : 'ltr';
    $pageName = $page->getTranslation('name', $locale, false)
                ?: $page->getTranslation('name', 'ar', false)
                ?: $page->getTranslation('name', 'en', false);
    $content  = $page->getTranslation('content', $locale, false)
                ?: $page->getTranslation('content', 'ar', false)
                ?: $page->getTranslation('content', 'en', false);
@endphp

@section('title', $pageName)

@push('head')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&family=Lora:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
<style>
/* ────────────────────────────────────────────────────────────────────
   Rich-text content renderer — scope: .prose-content
   Supports both RTL (Arabic) and LTR (English) layouts cleanly.
──────────────────────────────────────────────────────────────────── */

:root {
    --brand:        {{ config('app.brand_color', '#0ea5e9') }};
    --brand-light:  color-mix(in srgb, var(--brand) 12%, white);
    --text-body:    #374151;
    --text-head:    #111827;
    --text-muted:   #6b7280;
    --border:       #e5e7eb;
    --bg-subtle:    #f9fafb;
    --radius:       10px;
    --font-ar:      'Tajawal', 'Segoe UI', sans-serif;
    --font-en:      'Lora', Georgia, serif;
}

/* ── Page shell ─────────────────────────────────────────────────── */
.dynamic-page-wrap {
    max-width: 740px;
    margin-inline: auto;
    padding: 3rem 1.25rem 5rem;
}
@media (min-width: 640px) {
    .dynamic-page-wrap { padding: 4rem 2rem 6rem; }
}

/* ── Header ─────────────────────────────────────────────────────── */
.dynamic-page-header {
    margin-bottom: 2.5rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid var(--border);
}
.dynamic-page-title {
    font-size: clamp(1.75rem, 4vw, 2.5rem);
    font-weight: 800;
    color: var(--text-head);
    line-height: 1.25;
    letter-spacing: -0.02em;
}
[dir="rtl"] .dynamic-page-title {
    font-family: var(--font-ar);
    letter-spacing: 0;
}
[dir="ltr"] .dynamic-page-title {
    font-family: var(--font-en);
}
.dynamic-page-meta {
    margin-top: 0.75rem;
    font-size: 0.8rem;
    color: var(--text-muted);
}

/* ── Breadcrumb ─────────────────────────────────────────────────── */
.page-breadcrumb {
    display: flex;
    align-items: center;
    gap: 0.375rem;
    font-size: 0.78rem;
    color: var(--text-muted);
    margin-bottom: 2rem;
}
.page-breadcrumb a {
    color: var(--text-muted);
    text-decoration: none;
    transition: color .15s;
}
.page-breadcrumb a:hover { color: var(--brand); }
.page-breadcrumb .sep { opacity: .4; }

/* ══════════════════════════════════════════════════════════════════
   PROSE CONTENT — all rich-text element styles
══════════════════════════════════════════════════════════════════ */
.prose-content {
    color: var(--text-body);
    font-size: 1.0625rem;
    line-height: 1.85;
    word-break: break-word;
    overflow-wrap: break-word;
}
[dir="rtl"] .prose-content { font-family: var(--font-ar); }
[dir="ltr"] .prose-content { font-family: var(--font-en); }

/* ── Headings ───────────────────────────────────────────────────── */
.prose-content h1,
.prose-content h2,
.prose-content h3,
.prose-content h4,
.prose-content h5,
.prose-content h6 {
    color: var(--text-head);
    font-weight: 700;
    line-height: 1.3;
    margin-top: 2em;
    margin-bottom: 0.6em;
}
[dir="rtl"] .prose-content h1,
[dir="rtl"] .prose-content h2,
[dir="rtl"] .prose-content h3,
[dir="rtl"] .prose-content h4 { font-family: var(--font-ar); }

.prose-content h1 { font-size: 1.7rem; margin-top: 0; }
.prose-content h2 {
    font-size: 1.3rem;
    padding-bottom: 0.4em;
    border-bottom: 2px solid var(--border);
}
.prose-content h3 { font-size: 1.1rem; }
.prose-content h4 { font-size: 1rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: .05em; }

/* ── Paragraphs ─────────────────────────────────────────────────── */
.prose-content p {
    margin-top: 0;
    margin-bottom: 1.15em;
}
.prose-content p:last-child { margin-bottom: 0; }

/* ── Links ──────────────────────────────────────────────────────── */
.prose-content a {
    color: var(--brand);
    text-decoration: underline;
    text-decoration-thickness: 1.5px;
    text-underline-offset: 3px;
    transition: opacity .15s;
}
.prose-content a:hover { opacity: .75; }

/* ── Lists ──────────────────────────────────────────────────────── */
.prose-content ul,
.prose-content ol {
    margin: 0.8em 0 1.2em;
    padding-inline-start: 1.6em;
}
.prose-content li { margin-bottom: 0.4em; }
.prose-content ul { list-style-type: disc; }
.prose-content ol { list-style-type: decimal; }
.prose-content ul ul,
.prose-content ol ol,
.prose-content ul ol,
.prose-content ol ul {
    margin-top: 0.3em;
    margin-bottom: 0.3em;
}

/* ── Blockquote ─────────────────────────────────────────────────── */
.prose-content blockquote {
    margin: 1.5em 0;
    padding: 1em 1.4em;
    background: var(--brand-light);
    border-inline-start: 4px solid var(--brand);
    border-radius: 0 var(--radius) var(--radius) 0;
    color: #4b5563;
    font-style: italic;
    font-size: 1.025rem;
}
[dir="rtl"] .prose-content blockquote {
    border-radius: var(--radius) 0 0 var(--radius);
}
.prose-content blockquote p { margin-bottom: 0; }

/* ── Code ───────────────────────────────────────────────────────── */
.prose-content code {
    font-size: 0.875em;
    background: #f3f4f6;
    border: 1px solid var(--border);
    border-radius: 4px;
    padding: 0.15em 0.45em;
    font-family: 'Fira Code', 'Consolas', monospace;
    direction: ltr;
    display: inline-block;
}
.prose-content pre {
    background: #1e293b;
    color: #e2e8f0;
    border-radius: var(--radius);
    padding: 1.2em 1.5em;
    overflow-x: auto;
    margin: 1.5em 0;
    font-size: 0.875rem;
    direction: ltr;
    text-align: left;
}
.prose-content pre code {
    background: none;
    border: none;
    padding: 0;
    color: inherit;
    font-size: inherit;
}

/* ── Tables ─────────────────────────────────────────────────────── */
.prose-content .table-wrap {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    margin: 1.5em 0;
    border-radius: var(--radius);
    border: 1px solid var(--border);
    box-shadow: 0 1px 4px rgba(0,0,0,.04);
}
.prose-content table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.9rem;
    min-width: 480px;
}
.prose-content thead tr {
    background: var(--bg-subtle);
    border-bottom: 2px solid var(--border);
}
.prose-content th {
    font-weight: 700;
    color: var(--text-head);
    padding: 0.7em 1em;
    text-align: start;
    white-space: nowrap;
}
.prose-content td {
    padding: 0.65em 1em;
    border-bottom: 1px solid var(--border);
    vertical-align: top;
}
.prose-content tbody tr:last-child td { border-bottom: none; }
.prose-content tbody tr:hover td { background: #fafafa; }

/* ── HR ─────────────────────────────────────────────────────────── */
.prose-content hr {
    border: none;
    border-top: 1px solid var(--border);
    margin: 2.5em 0;
}

/* ── Images ─────────────────────────────────────────────────────── */
.prose-content img {
    max-width: 100%;
    height: auto;
    border-radius: var(--radius);
    display: block;
    margin: 1.5em auto;
    box-shadow: 0 2px 12px rgba(0,0,0,.08);
}

/* ── Strong / Em ────────────────────────────────────────────────── */
.prose-content strong { font-weight: 700; color: var(--text-head); }
.prose-content em     { font-style: italic; }

/* ── Quill-specific output fixes ────────────────────────────────── */
/* Quill uses .ql-align-* classes for alignment */
.prose-content .ql-align-center { text-align: center; }
.prose-content .ql-align-right  { text-align: right; }
.prose-content .ql-align-left   { text-align: left; }
.prose-content .ql-align-justify{ text-align: justify; }

/* Quill indents */
.prose-content .ql-indent-1 { padding-inline-start: 2em; }
.prose-content .ql-indent-2 { padding-inline-start: 4em; }
.prose-content .ql-indent-3 { padding-inline-start: 6em; }

/* Quill font-sizes (if using Snow toolbar size options) */
.prose-content .ql-size-small  { font-size: 0.875em; }
.prose-content .ql-size-large  { font-size: 1.25em; }
.prose-content .ql-size-huge   { font-size: 1.75em; }

/* Strip Quill's inline direction spans — let our [dir] handle it */
.prose-content [dir="ltr"],
.prose-content [dir="rtl"] { unicode-bidi: normal; }
</style>
@endpush

@section('content')
<div class="dynamic-page-wrap" dir="{{ $dir }}">

    {{-- Breadcrumb --}}
    <nav class="page-breadcrumb" aria-label="breadcrumb">
        <a href="{{ url('/') }}">
            @if($isRtl) الرئيسية @else Home @endif
        </a>
        <span class="sep">›</span>
        <span>{{ $pageName }}</span>
    </nav>

    {{-- Page header --}}
    <header class="dynamic-page-header">
        <h1 class="dynamic-page-title">{{ $pageName }}</h1>
        <p class="dynamic-page-meta">
            @if($isRtl)
                آخر تحديث: {{ $page->updated_at->locale('ar')->translatedFormat('d F Y') }}
            @else
                Last updated: {{ $page->updated_at->format('F j, Y') }}
            @endif
        </p>
    </header>

    {{-- Rich-text content — admin-only trusted HTML --}}
    <article class="prose-content" lang="{{ $locale }}">
        {!! $content !!}
    </article>

    {{-- Back link --}}
    <div class="mt-12 pt-8" style="border-top:1px solid var(--border)">
        <a href="{{ url('/') }}"
           class="inline-flex items-center gap-2 text-sm font-bold"
           style="color:var(--brand); text-decoration:none">
            @if($isRtl)
                <svg class="w-4 h-4" style="transform:scaleX(-1)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                العودة للرئيسية
            @else
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Home
            @endif
        </a>
    </div>

</div>
@endsection