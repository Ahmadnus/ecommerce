@props(['active' => false, 'href' => '#', 'icon' => ''])

<a href="{{ $href }}" 
   class="flex items-center gap-3 p-3 rounded-lg transition-all group {{ $active ? 'bg-brand text-white shadow-md' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
    <span class="w-6 h-6 flex items-center justify-center">
        {{-- هنا يمكنك وضع أي مكتبة أيقونات، مؤقتاً سأستخدم نقطة --}}
        <div class="w-1.5 h-1.5 rounded-full bg-current"></div>
    </span>
    <span x-show="sidebarOpen" class="font-medium">{{ $slot }}</span>
</a>