{{-- resources/views/components/product-skeleton.blade.php --}}
<div class="bg-white rounded-2xl overflow-hidden border border-gray-100 flex flex-col h-full">
    {{-- مساحة الصورة المتحركة --}}
    <div class="aspect-square shimmer bg-gray-200"></div>

    <div class="p-4 flex flex-col flex-1 space-y-3">
        {{-- هيكل التصنيف --}}
        <div class="h-3 w-1/4 rounded shimmer bg-gray-200"></div>
        
        {{-- هيكل العنوان --}}
        <div class="space-y-2">
            <div class="h-4 w-full rounded shimmer bg-gray-200"></div>
            <div class="h-4 w-2/3 rounded shimmer bg-gray-200"></div>
        </div>

        {{-- هيكل الوصف --}}
        <div class="flex-1 space-y-2 py-2">
            <div class="h-3 w-full rounded shimmer bg-gray-100"></div>
            <div class="h-3 w-5/6 rounded shimmer bg-gray-100"></div>
        </div>

        {{-- هيكل السعر والزر --}}
        <div class="flex items-center justify-between pt-2">
            <div class="h-6 w-16 rounded shimmer bg-gray-200"></div>
            <div class="h-9 w-24 rounded-xl shimmer bg-gray-200"></div>
        </div>
    </div>
</div>