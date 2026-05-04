<div class="flex gap-1 bg-gray-100 p-1 rounded-xl w-fit mb-4">
    <button type="button"
            @click="tab = 'ar'"
            :class="tab === 'ar' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-400 hover:text-gray-600'"
            class="px-4 py-1.5 rounded-lg text-sm font-bold transition-all">
        العربية 🇸🇦
    </button>
    <button type="button"
            @click="tab = 'en'"
            :class="tab === 'en' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-400 hover:text-gray-600'"
            class="px-4 py-1.5 rounded-lg text-sm font-bold transition-all">
        English 🇬🇧
    </button>
</div>