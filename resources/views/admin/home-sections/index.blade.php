@extends('layouts.admin')
@section('title', 'أقسام الصفحة الرئيسية')

@section('admin-content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-black text-gray-800">أقسام الصفحة الرئيسية</h1>
        <p class="text-sm text-gray-400 mt-1">اسحب الأقسام لإعادة ترتيبها. التغييرات تُحفظ تلقائياً.</p>
    </div>
    <a href="{{ route('admin.home-sections.create') }}"
       class="inline-flex items-center gap-2 text-white text-sm font-bold px-5 py-2.5 rounded-xl shadow-sm hover:opacity-90 transition-all active:scale-95"
       style="background:var(--brand-color)">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
        </svg>
        إضافة قسم
    </a>
</div>

@if($sections->isEmpty())
<div class="bg-white border border-dashed border-gray-200 rounded-2xl p-12 text-center">
    <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
              d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
    </svg>
    <p class="text-gray-500 font-semibold">لا توجد أقسام بعد</p>
    <a href="{{ route('admin.home-sections.create') }}" class="text-sm font-bold mt-2 inline-block hover:underline" style="color:var(--brand-color)">
        أضف أول قسم
    </a>
</div>

@else
<div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
    <div class="grid grid-cols-[32px_1fr_auto_auto_auto] gap-4 px-5 py-3 border-b border-gray-100
                text-[10px] font-bold text-gray-400 uppercase tracking-widest">
        <span></span>
        <span>القسم</span>
        <span>النوع</span>
        <span>الحالة</span>
        <span></span>
    </div>

    <ul id="sections-list" class="divide-y divide-gray-50">
        @foreach($sections as $section)
        <li class="grid grid-cols-[32px_1fr_auto_auto_auto] gap-4 items-center px-5 py-4 hover:bg-gray-50/60 transition-colors"
            data-id="{{ $section->id }}">

            {{-- Drag handle --}}
            <div class="drag-handle cursor-grab active:cursor-grabbing text-gray-300 hover:text-gray-500 flex justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/>
                </svg>
            </div>

            {{-- Title + meta --}}
            <div class="min-w-0">
                <p class="font-semibold text-gray-800 text-sm">{{ $section->title }}</p>
                @if($section->category)
                <p class="text-xs text-gray-400 mt-0.5">→ {{ $section->category->name }}</p>
                @endif
                <p class="text-[10px] text-gray-300 mt-0.5">{{ $section->limit }} منتج</p>
            </div>

            {{-- Type badge --}}
            <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-gray-100 text-gray-600 flex-shrink-0">
                {{ $section->typeLabel() }}
            </span>

            {{-- Active toggle --}}
            <span class="flex items-center gap-1.5 flex-shrink-0">
                @if($section->is_active)
                <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
                <span class="text-xs text-emerald-700 font-semibold">نشط</span>
                @else
                <span class="w-2 h-2 bg-gray-300 rounded-full"></span>
                <span class="text-xs text-gray-400 font-semibold">معطل</span>
                @endif
            </span>

            {{-- Actions --}}
            <div class="flex items-center gap-1 flex-shrink-0">
                <a href="{{ route('admin.home-sections.edit', $section) }}"
                   class="p-1.5 text-blue-500 hover:bg-blue-50 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                </a>
                <form action="{{ route('admin.home-sections.destroy', $section) }}" method="POST"
                      onsubmit="return confirm('حذف قسم \'{{ addslashes($section->title) }}\'؟')">
                    @csrf @method('DELETE')
                    <button type="submit" class="p-1.5 text-red-400 hover:bg-red-50 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </form>
            </div>
        </li>
        @endforeach
    </ul>
</div>
@endif

@endsection

@push('scripts')
{{-- SortableJS from CDN for drag-and-drop reordering --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.2/Sortable.min.js"></script>
<script>
var list = document.getElementById('sections-list');
if (list) {
    Sortable.create(list, {
        handle: '.drag-handle',
        animation: 150,
        ghostClass: 'bg-blue-50',
        onEnd: function () {
            var items = list.querySelectorAll('[data-id]');
            var order = Array.from(items).map(function (el) {
                return parseInt(el.dataset.id);
            });

            fetch('{{ route('admin.home-sections.reorder') }}', {
                method:  'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ order: order }),
            }).catch(function (err) {
                console.error('Reorder failed:', err);
            });
        },
    });
}
</script>
@endpush