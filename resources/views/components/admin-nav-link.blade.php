@props(['active' => false, 'href' => '#', 'icon' => ''])
<a href="{{ $href }}" 
   class="flex items-center gap-3 p-3 rounded-xl transition-all group
   {{ $active 
        ? 'bg-brand text-white shadow-md' 
        : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
    
    <span class="w-6 h-6 flex items-center justify-center">
        <div class="w-1.5 h-1.5 rounded-full bg-current"></div>
    </span>

    <span x-show="sidebarOpen" class="font-medium">{{ $slot }}</span>
</a>