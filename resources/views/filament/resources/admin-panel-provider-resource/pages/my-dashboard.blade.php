<x-filament-panels::page>
    <div class="space-y-6">
        
        {{-- استدعاء جدول التصنيفات --}}
        <div class="bg-white p-4 shadow rounded-lg">
            <h2 class="text-xl font-bold mb-4">التصنيفات</h2>
            @include('admin.categories.index')
        </div>

        {{-- استدعاء جدول المنتجات --}}
        <div class="bg-white p-4 shadow rounded-lg">
            <h2 class="text-xl font-bold mb-4">المنتجات</h2>
            @include('admin.products.index')
        </div>

        {{-- استدعاء الطلبات --}}
        <div class="bg-white p-4 shadow rounded-lg">
            <h2 class="text-xl font-bold mb-4">أحدث الطلبات</h2>
            @include('admin.orders.index')
        </div>

    </div>
</x-filament-panels::page>