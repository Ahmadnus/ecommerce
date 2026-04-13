@extends('layouts.admin')
@section('title', 'تعديل: ' . $page->name)

@push('head')
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

    {{-- Live preview link --}}
    <div class="mb-4 flex items-center justify-end">
        <a href="{{ route('pages.show', $page->slug) }}" target="_blank"
           class="inline-flex items-center gap-1.5 text-xs font-bold text-brand hover:underline">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
            </svg>
            معاينة الصفحة
        </a>
    </div>

    <form action="{{ route('admin.pages.update', $page) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-7 space-y-5">

            <h2 class="font-bold text-gray-800 text-lg border-b border-gray-100 pb-4">بيانات الصفحة</h2>

            {{-- Name --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    اسم الصفحة <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="page-name"
                       value="{{ old('name', $page->name) }}"
                       required
                       class="w-full border border-gray-200 rounded-xl p-3 bg-gray-50 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:border-brand transition-all @error('name') border-red-400 @enderror">
                @error('name')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Slug --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    الـ Slug (الرابط)
                    <span class="text-gray-400 font-normal text-xs">— تغييره يكسر الروابط القديمة</span>
                </label>
                <div class="flex items-center border border-gray-200 rounded-xl overflow-hidden focus-within:ring-2 focus-within:border-brand bg-gray-50 transition-all @error('slug') border-red-400 @enderror">
                    <span class="px-3 py-3 text-xs text-gray-400 bg-gray-100 border-l border-gray-200 font-mono flex-shrink-0">
                        /p/
                    </span>
                    <input type="text" name="slug"
                           value="{{ old('slug', $page->slug) }}"
                           class="flex-1 bg-transparent px-3 py-3 text-sm font-mono focus:outline-none">
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
                <textarea name="content" id="editor">{{ old('content', $page->content) }}</textarea>
                @error('content')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Sort + Active --}}
            <div class="grid grid-cols-2 gap-4 pt-2">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">ترتيب العرض</label>
                    <input type="number" name="sort_order" min="0"
                           value="{{ old('sort_order', $page->sort_order) }}"
                           class="w-full border border-gray-200 rounded-xl p-3 bg-gray-50 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:border-brand transition-all">
                </div>
                <div class="flex items-end pb-1">
                    <label class="flex items-center gap-3 cursor-pointer p-3 border border-gray-200 rounded-xl bg-gray-50 hover:bg-white transition-colors w-full">
                        <input type="checkbox" name="is_active" value="1"
                               {{ old('is_active', $page->is_active) ? 'checked' : '' }}
                               class="w-5 h-5 text-brand border-gray-300 rounded">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">تفعيل الصفحة</p>
                            <p class="text-xs text-gray-400">تظهر في الفوتر</p>
                        </div>
                    </label>
                </div>
            </div>

        </div>

        {{-- Actions --}}
        <div class="flex justify-between items-center">
            {{-- Delete --}}
            <form action="{{ route('admin.pages.destroy', $page) }}" method="POST"
                  onsubmit="return confirm('حذف هذه الصفحة نهائياً؟')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="flex items-center gap-2 text-sm text-red-500 hover:text-red-700 hover:bg-red-50 px-4 py-2.5 rounded-xl transition-colors font-semibold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    حذف الصفحة
                </button>
            </form>

            <div class="flex gap-3">
                <a href="{{ route('admin.pages.index') }}"
                   class="px-6 py-2.5 text-sm font-bold text-gray-500 hover:text-red-500 transition-colors">
                    إلغاء
                </a>
                <button type="submit"
                        class="bg-brand text-white px-8 py-2.5 rounded-xl font-bold text-sm shadow-lg hover:opacity-90 hover:scale-[1.02] transition-all active:scale-95">
                    حفظ التعديلات
                </button>
            </div>
        </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
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
        editor.ui.view.element.closest('form').addEventListener('submit', () => {
            document.querySelector('textarea[name="content"]').value = editor.getData();
        });
    })
    .catch(console.error);
</script>
@endpush
