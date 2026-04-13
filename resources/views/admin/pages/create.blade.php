@extends('layouts.admin')
@section('title', 'إضافة صفحة جديدة')

@push('head')
{{-- CKEditor 5 CDN --}}
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
<style>
    .ck-editor__editable { min-height: 320px; font-family: 'Tajawal', sans-serif; }
    .ck.ck-editor__main>.ck-editor__editable { border-radius: 0 0 12px 12px !important; }
    .ck.ck-toolbar { border-radius: 12px 12px 0 0 !important; }
</style>
@endpush

@section('admin-content')
<div class="max-w-3xl mx-auto">

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
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.pages.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-7 space-y-5">

            <h2 class="font-bold text-gray-800 text-lg border-b border-gray-100 pb-4">بيانات الصفحة</h2>

            {{-- Name --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    اسم الصفحة <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="page-name"
                       value="{{ old('name') }}"
                       oninput="autoSlug(this.value)"
                       required
                       placeholder="مثال: سياسة الخصوصية"
                       class="w-full border border-gray-200 rounded-xl p-3 bg-gray-50 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:border-brand transition-all @error('name') border-red-400 bg-red-50 @enderror">
                @error('name')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Slug --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    الـ Slug (الرابط)
                    <span class="text-gray-400 font-normal text-xs">— يُولَّد تلقائياً، يمكن تعديله</span>
                </label>
                <div class="flex items-center gap-0 border border-gray-200 rounded-xl overflow-hidden focus-within:ring-2 focus-within:border-brand bg-gray-50 transition-all">
                    <span class="px-3 py-3 text-xs text-gray-400 bg-gray-100 border-l border-gray-200 font-mono flex-shrink-0">
                        /p/
                    </span>
                    <input type="text" name="slug" id="slug-input"
                           value="{{ old('slug') }}"
                           placeholder="privacy-policy"
                           class="flex-1 bg-transparent px-3 py-3 text-sm font-mono focus:outline-none @error('slug') text-red-500 @enderror">
                </div>
                @error('slug')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Content --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    المحتوى <span class="text-red-500">*</span>
                </label>
                <textarea name="content" id="editor">{{ old('content') }}</textarea>
                @error('content')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Sort order + Active --}}
            <div class="grid grid-cols-2 gap-4 pt-2">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">ترتيب العرض</label>
                    <input type="number" name="sort_order" min="0"
                           value="{{ old('sort_order', 0) }}"
                           class="w-full border border-gray-200 rounded-xl p-3 bg-gray-50 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:border-brand transition-all">
                </div>
                <div class="flex items-end pb-1">
                    <label class="flex items-center gap-3 cursor-pointer p-3 border border-gray-200 rounded-xl bg-gray-50 hover:bg-white transition-colors w-full">
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

        {{-- Submit --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.pages.index') }}"
               class="px-6 py-2.5 text-sm font-bold text-gray-500 hover:text-red-500 transition-colors">
                إلغاء
            </a>
            <button type="submit"
                    class="bg-brand text-white px-8 py-2.5 rounded-xl font-bold text-sm shadow-lg hover:opacity-90 hover:scale-[1.02] transition-all active:scale-95">
                حفظ الصفحة
            </button>
        </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
// ── Auto-slug from name ────────────────────────────────────────────────
const slugInput = document.getElementById('slug-input');
let slugManuallyEdited = !!slugInput.value.trim();

slugInput.addEventListener('input', () => slugManuallyEdited = true);

function autoSlug(val) {
    if (slugManuallyEdited) return;
    slugInput.value = val
        .toLowerCase()
        .trim()
        .replace(/[\s_]+/g, '-')
        .replace(/[^\u0600-\u06FFa-z0-9-]/g, '')
        .replace(/-+/g, '-')
        .replace(/^-|-$/g, '');
}

// ── CKEditor 5 ────────────────────────────────────────────────────────
ClassicEditor
    .create(document.querySelector('#editor'), {
        language: 'ar',
        toolbar: {
            items: [
                'heading', '|',
                'bold', 'italic', 'underline', 'strikethrough', '|',
                'alignment', '|',
                'bulletedList', 'numberedList', '|',
                'link', 'blockQuote', 'insertTable', '|',
                'undo', 'redo'
            ]
        },
        table: { contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells'] }
    })
    .then(editor => {
        // Sync hidden textarea before form submit
        editor.ui.view.element.closest('form').addEventListener('submit', () => {
            document.querySelector('textarea[name="content"]').value = editor.getData();
        });
    })
    .catch(console.error);
</script>
@endpush