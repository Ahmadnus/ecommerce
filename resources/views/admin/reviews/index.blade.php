@extends('layouts.admin')
@section('title', 'تقييمات المنتجات')

@section('admin-content')

{{-- Stats --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    @foreach([
        ['label' => 'إجمالي التقييمات', 'val' => $stats['total'],    'color' => '#6b7280'],
        ['label' => 'قيد المراجعة',      'val' => $stats['pending'],  'color' => '#f59e0b'],
        ['label' => 'معتمدة',            'val' => $stats['approved'], 'color' => '#10b981'],
        ['label' => 'مرفوضة',            'val' => $stats['rejected'], 'color' => '#ef4444'],
    ] as $stat)
    <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
        <p class="text-2xl font-black text-gray-900">{{ $stat['val'] }}</p>
        <p class="text-xs font-semibold mt-1" style="color:{{ $stat['color'] }}">{{ $stat['label'] }}</p>
    </div>
    @endforeach
</div>

{{-- Filters --}}
<form method="GET" class="flex flex-wrap gap-3 mb-6">
    <select name="status"
            onchange="this.form.submit()"
            class="border border-gray-200 rounded-xl px-3 py-2 text-sm bg-white outline-none focus:ring-2"
            style="--tw-ring-color:var(--brand-color)">
        <option value="">كل الحالات</option>
        <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>قيد المراجعة</option>
        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>معتمدة</option>
        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>مرفوضة</option>
    </select>

    <select name="rating"
            onchange="this.form.submit()"
            class="border border-gray-200 rounded-xl px-3 py-2 text-sm bg-white outline-none focus:ring-2"
            style="--tw-ring-color:var(--brand-color)">
        <option value="">كل التقييمات</option>
        @foreach(range(5,1) as $r)
        <option value="{{ $r }}" {{ request('rating') == $r ? 'selected' : '' }}>{{ $r }} نجوم</option>
        @endforeach
    </select>
</form>

{{-- Reviews table --}}
<div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">
            {{ $reviews->total() }} تقييم
        </span>
    </div>

    @if($reviews->isEmpty())
    <div class="p-16 text-center text-gray-400">
        <p class="font-semibold">لا توجد تقييمات</p>
    </div>
    @else
    <div class="divide-y divide-gray-100">
        @foreach($reviews as $review)
        @php $badge = $review->statusColor(); @endphp
        <div class="flex items-start gap-4 px-5 py-4 hover:bg-gray-50/60 transition-colors">

            {{-- Stars --}}
            <div class="flex-shrink-0 pt-0.5">
                <x-star-rating :rating="$review->rating" size="sm" />
            </div>

            {{-- Content --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap mb-1">
                    <span class="font-bold text-sm text-gray-900">{{ $review->displayName() }}</span>
                    @if($review->user_id)
                    <span class="text-[10px] bg-green-50 text-green-700 px-1.5 py-0.5 rounded-full font-bold">موثق</span>
                    @endif
                    @if($review->is_pinned)
                    <span class="text-[10px] bg-blue-50 text-blue-700 px-1.5 py-0.5 rounded-full font-bold">📌 مثبت</span>
                    @endif
                    <span class="text-[10px] text-gray-400">·</span>
                    <span class="text-[10px] text-gray-400">{{ $review->product->name ?? '—' }}</span>
                    <span class="text-[10px] text-gray-400">·</span>
                    <span class="text-[10px] text-gray-400">{{ $review->created_at->diffForHumans() }}</span>
                </div>
                <p class="text-sm text-gray-600 line-clamp-2">{{ $review->comment }}</p>
            </div>

            {{-- Status badge --}}
            <div class="flex-shrink-0">
                <span class="inline-block text-[10px] font-bold px-2 py-1 rounded-full
                    {{ $badge === 'green'  ? 'bg-green-100 text-green-700'  :
                       ($badge === 'red'   ? 'bg-red-100 text-red-700'     :
                                            'bg-yellow-100 text-yellow-700') }}">
                    {{ $review->statusLabel() }}
                </span>
            </div>

            {{-- Actions --}}
            <div class="flex-shrink-0 flex items-center gap-1">

                @if($review->status !== 'approved')
                <form action="{{ route('admin.reviews.approve', $review) }}" method="POST">
                    @csrf @method('PATCH')
                    <button type="submit"
                            title="اعتماد"
                            class="p-1.5 text-green-500 hover:bg-green-50 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </button>
                </form>
                @endif

                @if($review->status !== 'rejected')
                <form action="{{ route('admin.reviews.reject', $review) }}" method="POST">
                    @csrf @method('PATCH')
                    <button type="submit"
                            title="رفض"
                            class="p-1.5 text-red-400 hover:bg-red-50 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </form>
                @endif

                <form action="{{ route('admin.reviews.pin', $review) }}" method="POST">
                    @csrf @method('PATCH')
                    <button type="submit"
                            title="{{ $review->is_pinned ? 'إلغاء التثبيت' : 'تثبيت' }}"
                            class="p-1.5 transition-colors rounded-lg
                                   {{ $review->is_pinned ? 'text-brand' : 'text-gray-400 hover:text-brand hover:bg-gray-100' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                        </svg>
                    </button>
                </form>

                <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST"
                      onsubmit="return confirm('حذف هذا التقييم؟')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="p-1.5 text-red-400 hover:bg-red-50 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    @if($reviews->hasPages())
    <div class="px-5 py-4 border-t border-gray-100">
        {{ $reviews->links() }}
    </div>
    @endif
    @endif
</div>

@endsection