@extends('layouts.admin')
@section('title', 'إضافة صفحة جديدة')

@php
    $defaultTab = $errors->has('name.en') || $errors->has('content.en') ? 'en' : 'ar';
@endphp

@push('head')
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<style>
    /* ── Quill toolbar ───────────────────────────────────────── */
    .ql-toolbar.ql-snow {
        border: 1px solid #e5e7eb;
        border-bottom: none;
        border-radius: 12px 12px 0 0;
        background: #f9fafb;
        padding: 8px 10px;
        flex-wrap: wrap;
    }

    /* ── Quill editable container ────────────────────────────── */
    .ql-container.ql-snow {
        border: 1px solid #e5e7eb;
        border-top: none;
        border-radius: 0 0 12px 12px;
        font-size: 0.9375rem;
        background: #fff;
    }

    /* Focus ring on the whole wrapper ───────────────────────── */
    .quill-wrap:focus-within .ql-toolbar.ql-snow,
    .quill-wrap:focus-within .ql-container.ql-snow {
        border-color: var(--brand-color, #0ea5e9);
        box-shadow: 0 0 0 3px color-mix(in srgb, var(--brand-color, #0ea5e9) 12%, transparent);
    }

    /* ── Editor content area ─────────────────────────────────── */
    .ql-editor {
        min-height: 300px;
        line-height: 1.85;
        color: #374151;
        padding: 16px 20px;
        font-family: 'Tajawal', 'Segoe UI', sans-serif;
    }

    /* placeholder */
    .ql-editor.ql-blank::before {
        color: #9ca3af;
        font-style: normal;
        font-size: 0.9rem;
    }

    /* ── Headings inside editor ──────────────────────────────── */
    .ql-editor h1 { font-size: 1.75rem; font-weight: 800; margin: 0.4em 0; line-height: 1.25; }
    .ql-editor h2 { font-size: 1.35rem; font-weight: 700; margin: 0.5em 0 0.4em; }
    .ql-editor h3 { font-size: 1.1rem;  font-weight: 700; margin: 0.5em 0 0.35em; }
    .ql-editor p  { margin-bottom: 0.8em; }

    /* ── Lists ───────────────────────────────────────────────── */
    .ql-editor ul,
    .ql-editor ol { padding-inline-start: 1.6em; margin-bottom: 0.8em; }
    .ql-editor li { margin-bottom: 0.25em; }

    /* ── Blockquote ──────────────────────────────────────────── */
    .ql-editor blockquote {
        border-inline-start: 4px solid var(--brand-color, #0ea5e9);
        margin: 1em 0;
        padding: 0.75em 1.1em;
        background: #f0f9ff;
        color: #4b5563;
        font-style: italic;
        border-radius: 0 10px 10px 0;
    }

    /* ── Code block ──────────────────────────────────────────── */
    .ql-editor pre.ql-syntax {
        background: #1e293b;
        color: #e2e8f0;
        border-radius: 10px;
        padding: 1em 1.2em;
        font-size: 0.875rem;
        overflow-x: auto;
        direction: ltr;
        text-align: left;
    }

    /* ── Links & images ──────────────────────────────────────── */
    .ql-editor a { color: var(--brand-color, #0ea5e9); text-decoration: underline; }
    .ql-editor img { max-width: 100%; border-radius: 10px; display: block; margin: 1em auto; }

    /* ── Toolbar button sizing ───────────────────────────────── */
    .ql-toolbar .ql-formats { margin-inline-end: 6px; }
    .ql-toolbar button      { width: 26px; height: 26px; padding: 2px; }
    .ql-toolbar button svg  { width: 16px; height: 16px; }
</style>
@endpush

@section('admin-content')
<div class="max-w-3xl mx-auto"
     x-data="{ tab: '{{ $defaultTab }}' }"
     x-init="
         $watch('tab', val => {
             requestAnimationFrame(() => {
                 if (val === 'ar' && window.__quillAr) window.__quillAr.update();
                 if (val === 'en' && window.__quillEn) window.__quillEn.update();
             });
         })
     ">

    {{-- Back --}}
    <div class="mb-6">
        <a href="{{ route('admin.pages.index') }}"
           class="inline-flex items-center gap-1.5 text-sm font-semibold text-gray-500 hover:text-brand transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            العودة للصفحات
        </a>
    </div>

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-100 rounded-xl text-sm text-red-600">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.pages.store') }}" method="POST" id="page-form" class="space-y-6">
        @csrf

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-7 space-y-5">

            {{-- Header + tab switcher --}}
            <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                <h2 class="font-bold text-gray-800 text-lg">بيانات الصفحة</h2>
                <div class="flex gap-1 bg-gray-100 p-1 rounded-xl">
                    <button type="button" @click="tab = 'ar'"
                            :class="tab === 'ar' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-400 hover:text-gray-600'"
                            class="px-4 py-1.5 rounded-lg text-sm font-bold transition-all">
                        العربية 🇸🇦
                    </button>
                    <button type="button" @click="tab = 'en'"
                            :class="tab === 'en' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-400 hover:text-gray-600'"
                            class="px-4 py-1.5 rounded-lg text-sm font-bold transition-all">
                        English 🇬🇧
                    </button>
                </div>
            </div>

            {{-- ══ Arabic tab ══════════════════════════════════ --}}
            <div x-show="tab === 'ar'">
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            اسم الصفحة (عربي) <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name[ar]" id="name-ar"
                               value="{{ old('name.ar') }}"
                               oninput="autoSlug(this.value)"
                               required dir="rtl"
                               placeholder="مثال: سياسة الخصوصية"
                               class="w-full border border-gray-200 rounded-xl p-3 bg-gray-50 text-sm
                                      focus:bg-white focus:outline-none focus:ring-2 focus:border-brand
                                      transition-all @error('name.ar') border-red-400 bg-red-50 @enderror">
                        @error('name.ar')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            المحتوى (عربي) <span class="text-red-500">*</span>
                        </label>
                        <textarea name="content[ar]" id="content-ar" class="hidden"></textarea>
                        <div class="quill-wrap">
                            <div id="editor-ar"></div>
                        </div>
                        @error('content.ar')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- ══ English tab ══════════════════════════════════ --}}
            <div x-show="tab === 'en'">
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Page Name (English)
                        </label>
                        <input type="text" name="name[en]" id="name-en"
                               value="{{ old('name.en') }}"
                               dir="ltr"
                               placeholder="e.g. Privacy Policy"
                               class="w-full border border-gray-200 rounded-xl p-3 bg-gray-50 text-sm
                                      focus:bg-white focus:outline-none focus:ring-2 focus:border-brand
                                      transition-all @error('name.en') border-red-400 bg-red-50 @enderror">
                        @error('name.en')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Content (English)
                        </label>
                        <textarea name="content[en]" id="content-en" class="hidden"></textarea>
                        <div class="quill-wrap">
                            <div id="editor-en"></div>
                        </div>
                        @error('content.en')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Slug ──────────────────────────────────────────── --}}
            <div class="pt-2 border-t border-gray-50">
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    الـ Slug (الرابط)
                    <span class="text-gray-400 font-normal text-xs">— يُولَّد تلقائياً، يمكن تعديله</span>
                </label>
                <div class="flex items-center border border-gray-200 rounded-xl overflow-hidden
                            focus-within:ring-2 focus-within:border-brand bg-gray-50 transition-all">
                    <span class="px-3 py-3 text-xs text-gray-400 bg-gray-100
                                 border-l border-gray-200 font-mono flex-shrink-0">/p/</span>
                    <input type="text" name="slug" id="slug-input"
                           value="{{ old('slug') }}" placeholder="privacy-policy"
                           class="flex-1 bg-transparent px-3 py-3 text-sm font-mono
                                  focus:outline-none @error('slug') text-red-500 @enderror">
                </div>
                @error('slug')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Sort + Active ─────────────────────────────────── --}}
            <div class="grid grid-cols-2 gap-4 pt-2">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">ترتيب العرض</label>
                    <input type="number" name="sort_order" min="0"
                           value="{{ old('sort_order', 0) }}"
                           class="w-full border border-gray-200 rounded-xl p-3 bg-gray-50 text-sm
                                  focus:bg-white focus:outline-none focus:ring-2 focus:border-brand transition-all">
                </div>
                <div class="flex items-end pb-1">
                    <label class="flex items-center gap-3 cursor-pointer p-3 border border-gray-200
                                  rounded-xl bg-gray-50 hover:bg-white transition-colors w-full">
                        <input type="checkbox" name="is_active" value="1"
                               {{ old('is_active', true) ? 'checked' : '' }}
                               class="w-5 h-5 text-brand border-gray-300 rounded focus:ring-brand/30">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">تفعيل الصفحة</p>
                            <p class="text-xs text-gray-400">ستظهر في الفوتر فور الحفظ</p>
                        </div>
                    </label>
                </div>
            </div>

        </div>

        {{-- Submit ────────────────────────────────────────────── --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.pages.index') }}"
               class="px-6 py-2.5 text-sm font-bold text-gray-500 hover:text-red-500 transition-colors">
                إلغاء
            </a>
            <button type="submit"
                    class="bg-brand text-white px-8 py-2.5 rounded-xl font-bold text-sm
                           shadow-lg hover:opacity-90 hover:scale-[1.02] transition-all active:scale-95">
                حفظ الصفحة
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
(function () {

    const TOOLBAR = [
        [{ header: [1, 2, 3, 4, false] }],
        ['bold', 'italic', 'underline', 'strike'],
        [{ list: 'ordered' }, { list: 'bullet' }],
        [{ indent: '-1' }, { indent: '+1' }],
        [{ align: [] }],
        ['link', 'blockquote', 'code-block'],
        ['clean'],
    ];

    function makeQuill(selector, dir, placeholder) {
        const quill = new Quill(selector, {
            theme: 'snow',
            modules: { toolbar: TOOLBAR },
            placeholder: placeholder,
        });
        quill.root.setAttribute('dir', dir);
        quill.root.style.direction  = dir;
        quill.root.style.textAlign  = dir === 'rtl' ? 'right' : 'left';
        return quill;
    }

    window.__quillAr = makeQuill('#editor-ar', 'rtl', 'اكتب المحتوى هنا...');
    window.__quillEn = makeQuill('#editor-en', 'ltr', 'Write content here...');

    // Restore content after validation failure
    const oldAr = @json(old('content.ar', ''));
    const oldEn = @json(old('content.en', ''));
    if (oldAr) window.__quillAr.root.innerHTML = oldAr;
    if (oldEn) window.__quillEn.root.innerHTML = oldEn;

    // Sync to hidden textareas on submit
    document.getElementById('page-form').addEventListener('submit', function () {
        document.getElementById('content-ar').value = window.__quillAr.root.innerHTML;
        document.getElementById('content-en').value = window.__quillEn.root.innerHTML;
    });

    // Auto-slug from Arabic name
    const slugInput = document.getElementById('slug-input');
    let slugManuallyEdited = !!slugInput.value.trim();
    slugInput.addEventListener('input', () => { slugManuallyEdited = true; });

    window.autoSlug = function (val) {
        if (slugManuallyEdited) return;
        slugInput.value = val
            .toLowerCase().trim()
            .replace(/[\s_]+/g, '-')
            .replace(/[^\u0600-\u06FFa-z0-9-]/g, '')
            .replace(/-+/g, '-')
            .replace(/^-|-$/g, '');
    };

})();
</script>
@endpush