{{--
    partials/admin/product-image-uploader.blade.php
    ─────────────────────────────────────────────────────────────────────────────
    Alpine.js multi-image uploader with:
    • Drag-and-drop zone
    • Live preview grid (up to 10 images)
    • Remove individual preview before upload
    • On edit: shows existing Spatie media with individual delete checkboxes

    Usage in create form:
        @include('partials.admin.product-image-uploader')

    Usage in edit form:
        @include('partials.admin.product-image-uploader', [
            'existingImages' => $existingImages   // Collection of {id, url}
        ])
--}}

@php $existingImages = $existingImages ?? collect(); @endphp

<div x-data="productImageUploader()" class="space-y-4">

    {{-- Drag-drop zone --}}
    <div class="relative"
         @dragover.prevent="dragOver = true"
         @dragleave.prevent="dragOver = false"
         @drop.prevent="onDrop($event)">

        <label :class="dragOver
                    ? 'border-[var(--brand-color,#0ea5e9)] bg-blue-50/60'
                    : 'border-gray-300 hover:border-[var(--brand-color,#0ea5e9)] hover:bg-gray-50'"
               class="flex flex-col items-center justify-center w-full h-36 border-2 border-dashed
                      rounded-2xl cursor-pointer transition-all duration-200">

            <svg class="w-8 h-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p class="text-sm font-semibold text-gray-600">اسحب الصور هنا أو</p>
            <p class="text-xs text-[var(--brand-color,#0ea5e9)] font-bold mt-0.5">اضغط للاختيار</p>
            <p class="text-[10px] text-gray-400 mt-1">JPG, PNG, WEBP — حتى 5MB لكل صورة، 10 صور كحد أقصى</p>

            <input type="file"
                   name="product_images[]"
                   multiple
                   accept="image/jpeg,image/png,image/webp,image/avif"
                   class="hidden"
                   @change="onFileSelect($event)">
        </label>
    </div>

    {{-- Error message --}}
    <p x-show="errorMsg" x-text="errorMsg" class="text-xs text-red-500 font-semibold"></p>

    {{-- NEW image preview grid --}}
    <template x-if="previews.length > 0">
        <div>
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                صور جديدة (<span x-text="previews.length"></span>)
            </p>
            <div class="grid grid-cols-4 sm:grid-cols-6 gap-2">
                <template x-for="(preview, index) in previews" :key="index">
                    <div class="relative group aspect-square rounded-xl overflow-hidden bg-gray-100 border border-gray-200">
                        <img :src="preview.url" class="w-full h-full object-cover">
                        {{-- First image badge --}}
                        <template x-if="index === 0 && {{ $existingImages->isEmpty() ? 'true' : 'false' }}">
                            <span class="absolute top-1 right-1 text-[9px] font-black bg-[var(--brand-color,#0ea5e9)] text-white px-1.5 py-0.5 rounded-full leading-tight">
                                رئيسية
                            </span>
                        </template>
                        {{-- Remove button --}}
                        <button type="button"
                                @click="removePreview(index)"
                                class="absolute inset-0 bg-black/40 flex items-center justify-center
                                       opacity-0 group-hover:opacity-100 transition-opacity">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </template>
            </div>
        </div>
    </template>

    {{-- EXISTING images (edit mode) --}}
    @if($existingImages->isNotEmpty())
    <div>
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
            الصور الحالية ({{ $existingImages->count() }})
        </p>
        <div class="grid grid-cols-4 sm:grid-cols-6 gap-2">
            @foreach($existingImages as $media)
            <div class="relative group aspect-square rounded-xl overflow-hidden bg-gray-100 border border-gray-200"
                 x-data="{ deleted: false }">

                <img src="{{ $media['url'] }}" class="w-full h-full object-cover" :class="deleted ? 'opacity-30 grayscale' : ''">

                {{-- First existing = main image --}}
                @if($loop->first)
                <span class="absolute top-1 right-1 text-[9px] font-black bg-[var(--brand-color,#0ea5e9)] text-white px-1.5 py-0.5 rounded-full leading-tight">
                    رئيسية
                </span>
                @endif

                {{-- Delete toggle --}}
                <button type="button"
                        @click="deleted = !deleted"
                        class="absolute top-1 left-1 w-6 h-6 rounded-full flex items-center justify-center transition-all
                               shadow-sm"
                        :class="deleted
                            ? 'bg-red-500 text-white'
                            : 'bg-white/90 text-gray-600 opacity-0 group-hover:opacity-100'">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                {{-- Hidden input to flag deletion --}}
                <input type="checkbox"
                       name="delete_media_ids[]"
                       value="{{ $media['id'] }}"
                       class="hidden"
                       :checked="deleted">
            </div>
            @endforeach
        </div>
        <p class="text-[10px] text-gray-400 mt-1.5">اضغط على الصورة لحذفها (تصبح رمادية) — التغييرات تُطبَّق عند الحفظ</p>
    </div>
    @endif

</div>

@once
@push('scripts')
<script>
document.addEventListener('alpine:init', function () {
    Alpine.data('productImageUploader', function () {
        return {
            previews:  [],
            files:     [],
            dragOver:  false,
            errorMsg:  '',
            MAX:       10,

            onFileSelect(event) {
                this.processFiles(Array.from(event.target.files));
                event.target.value = ''; // reset so same files can be re-added after remove
            },

            onDrop(event) {
                this.dragOver = false;
                this.processFiles(Array.from(event.dataTransfer.files));
            },

            processFiles(newFiles) {
                this.errorMsg = '';
                const allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/avif'];
                const maxSize = 5 * 1024 * 1024; // 5 MB

                for (const file of newFiles) {
                    if (this.previews.length >= this.MAX) {
                        this.errorMsg = `الحد الأقصى ${this.MAX} صور`;
                        break;
                    }
                    if (!allowedTypes.includes(file.type)) {
                        this.errorMsg = `نوع الملف غير مدعوم: ${file.name}`;
                        continue;
                    }
                    if (file.size > maxSize) {
                        this.errorMsg = `الملف ${file.name} يتجاوز 5MB`;
                        continue;
                    }

                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.previews.push({ url: e.target.result, file });
                        // Sync the actual file input using a DataTransfer object
                        this.syncInput();
                    };
                    reader.readAsDataURL(file);
                }
            },

            removePreview(index) {
                this.previews.splice(index, 1);
                this.syncInput();
            },

            syncInput() {
                // Rebuild the file input's FileList from the current previews array
                const input = this.$el.querySelector('input[type="file"]');
                if (!input) return;

                const dt = new DataTransfer();
                this.previews.forEach(p => dt.items.add(p.file));
                input.files = dt.files;
            },
        };
    });
});
</script>
@endpush
@endonce