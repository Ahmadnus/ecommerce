@extends('layouts.admin')
@section('title', 'إدارة الهيرو العلوي (صورة/فيديو)')

@section('admin-content')
<div class="space-y-8">

    {{-- ══════════════════════════════════════════════════════
         ADD FORM
    ══════════════════════════════════════════════════════ --}}
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <h3 class="text-lg font-bold mb-4">إضافة عنصر جديد</h3>

        <form action="{{ route('admin.top-hero-media.store') }}"
              method="POST"
              enctype="multipart/form-data"
              class="hero-media-form space-y-5">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-bold mb-1 block">نوع الوسائط</label>
                    <select name="type"
                            class="hero-media-type w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
                        <option value="image" {{ old('type', 'image') === 'image' ? 'selected' : '' }}>صورة</option>
                        <option value="video" {{ old('type', 'image') === 'video' ? 'selected' : '' }}>فيديو</option>
                    </select>
                </div>

                <div>
                    <label class="text-xs font-bold mb-1 block">موضع العرض في الصفحة</label>
                    <select name="position"
                            class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
                        <option value="top" {{ old('position', 'top') === 'top' ? 'selected' : '' }}>أعلى الصفحة</option>
                        <option value="middle" {{ old('position') === 'middle' ? 'selected' : '' }}>منتصف الصفحة</option>
                        <option value="bottom" {{ old('position') === 'bottom' ? 'selected' : '' }}>أسفل الصفحة</option>
                    </select>
                </div>

                <div>
                    <label class="text-xs font-bold mb-1 block">ترتيب العرض</label>
                    <input type="number" name="sort_order" min="0" value="{{ old('sort_order', 0) }}"
                           class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
                </div>

                <div class="md:col-span-2">
                    <label class="text-xs font-bold mb-1 block">رابط عند الضغط (اختياري)</label>
                    <input type="text" name="link_url" value="{{ old('link_url') }}"
                           placeholder="https://example.com/..."
                           class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
                    @error('link_url')<p class="text-xs text-red-500 -mt-2">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label class="text-xs font-bold mb-1 block">الملف (صورة: jpg/png/webp — فيديو: mp4/webm)</label>
                    <input type="file" name="media" class="w-full px-4 py-2 rounded-xl border">
                    @error('media')<p class="text-xs text-red-500 -mt-2">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2 flex items-center gap-3">
                    <label class="inline-flex items-center gap-2 text-sm">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <span>نشط (سيتم تعطيل أي عنصر آخر نشط تلقائياً)</span>
                    </label>
                </div>

                <div class="md:col-span-2">
                    <button type="submit"
                            class="bg-brand text-white font-bold py-2 px-5 rounded-xl hover:opacity-90 transition">
                        حفظ
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- ══════════════════════════════════════════════════════
         TABLE
    ══════════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-right">
            <thead class="bg-gray-50 border-b text-gray-500 text-sm">
                <tr>
                    <th class="px-6 py-4">المعاينة</th>
                    <th class="px-6 py-4">النوع</th>
                    <th class="px-6 py-4">الموضع</th>
                    <th class="px-6 py-4">الرابط</th>
                    <th class="px-6 py-4">الترتيب</th>
                    <th class="px-6 py-4">الحالة</th>
                    <th class="px-6 py-4">الإجراء</th>
                </tr>
            </thead>

            <tbody class="divide-y text-sm">
                @forelse($items as $item)
                <tr>
                    <td class="px-6 py-4">
                        @if($item->type === 'video')
                            <video class="w-24 h-14 object-cover rounded-lg shadow-sm" muted>
                                <source src="{{ $item->getFirstMediaUrl('hero_media') }}">
                            </video>
                        @else
                            <img src="{{ $item->getFirstMediaUrl('hero_media') }}"
                                 class="w-24 h-14 object-cover rounded-lg shadow-sm" alt="hero media">
                        @endif
                    </td>

                    <td class="px-6 py-4">
                        <span class="text-xs px-2 py-1 rounded bg-blue-50 text-blue-700 border border-blue-100">
                            {{ $item->type === 'video' ? 'فيديو' : 'صورة' }}
                        </span>
                    </td>

                    <td class="px-6 py-4">
                        <span class="text-xs px-2 py-1 rounded bg-gray-50 text-gray-600 border border-gray-100">
                            {{ ['top' => 'أعلى', 'middle' => 'منتصف', 'bottom' => 'أسفل'][$item->position] ?? $item->position }}
                        </span>
                    </td>

                    <td class="px-6 py-4 text-xs text-gray-500 max-w-[200px] truncate">
                        {{ $item->link_url ?: '—' }}
                    </td>

                    <td class="px-6 py-4">{{ $item->sort_order }}</td>

                    <td class="px-6 py-4">
                        <span class="{{ $item->is_active ? 'text-green-600' : 'text-red-400' }}">
                            {{ $item->is_active ? 'نشط' : 'معطل' }}
                        </span>
                    </td>

                    <td class="px-6 py-4">
                        <div class="flex gap-4 items-center">
                            <button type="button" onclick="toggleEdit({{ $item->id }})"
                                    class="text-blue-500 hover:underline">تعديل</button>

                            <form action="{{ route('admin.top-hero-media.destroy', $item) }}"
                                  method="POST"
                                  onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                @csrf @method('DELETE')
                                <button class="text-red-500 hover:underline" type="submit">حذف</button>
                            </form>
                        </div>
                    </td>
                </tr>

                {{-- ── Inline edit row ──────────────────────────────── --}}
                <tr id="edit-{{ $item->id }}" class="hidden bg-gray-50">
                    <td colspan="7" class="p-6">
                        <form action="{{ route('admin.top-hero-media.update', $item) }}"
                              method="POST"
                              enctype="multipart/form-data"
                              class="hero-media-form space-y-5">
                            @csrf @method('PUT')

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-xs font-bold mb-1 block">نوع الوسائط</label>
                                    <select name="type"
                                            class="hero-media-type w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
                                        <option value="image" {{ $item->type === 'image' ? 'selected' : '' }}>صورة</option>
                                        <option value="video" {{ $item->type === 'video' ? 'selected' : '' }}>فيديو</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="text-xs font-bold mb-1 block">موضع العرض في الصفحة</label>
                                    <select name="position"
                                            class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
                                        <option value="top" {{ $item->position === 'top' ? 'selected' : '' }}>أعلى الصفحة</option>
                                        <option value="middle" {{ $item->position === 'middle' ? 'selected' : '' }}>منتصف الصفحة</option>
                                        <option value="bottom" {{ $item->position === 'bottom' ? 'selected' : '' }}>أسفل الصفحة</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="text-xs font-bold mb-1 block">ترتيب العرض</label>
                                    <input type="number" name="sort_order" min="0" value="{{ $item->sort_order }}"
                                           class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
                                </div>

                                <div class="md:col-span-2">
                                    <label class="text-xs font-bold mb-1 block">رابط عند الضغط (اختياري)</label>
                                    <input type="text" name="link_url" value="{{ $item->link_url }}"
                                           class="w-full px-4 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-brand">
                                </div>

                                <div class="md:col-span-2">
                                    <label class="text-xs font-bold mb-1 block">استبدال الملف (اختياري)</label>
                                    <input type="file" name="media" class="w-full px-4 py-2 rounded-xl border">
                                </div>

                                <div class="md:col-span-2 flex items-center gap-3">
                                    <label class="inline-flex items-center gap-2 text-sm">
                                        <input type="checkbox" name="is_active" value="1" {{ $item->is_active ? 'checked' : '' }}>
                                        <span>نشط</span>
                                    </label>

                                    <button type="submit"
                                            class="bg-green-600 text-white font-bold py-2 px-5 rounded-xl hover:opacity-90 transition">
                                        تحديث
                                    </button>

                                    <button type="button" onclick="toggleEdit({{ $item->id }})"
                                            class="bg-gray-100 text-gray-700 font-bold py-2 px-5 rounded-xl hover:bg-gray-200 transition">
                                        إغلاق
                                    </button>
                                </div>
                            </div>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-10 text-center text-gray-400">لا توجد عناصر بعد</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
function toggleEdit(id) {
    const el = document.getElementById('edit-' + id);
    if (el) el.classList.toggle('hidden');
}
</script>
@endsection
