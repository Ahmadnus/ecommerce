{{--
    resources/views/partials/reviews.blade.php
    @include('partials.reviews')
--}}

@php
    $avgRating   = $product->averageRating();
    $reviewCount = $product->reviewCount();
@endphp

{{-- ✅ Star JS inline — NOT in @push so it's always available before onclick fires --}}
<script>
(function () {
    var _selected = parseInt('{{ (int) old('rating', 0) }}') || 0;

    function _paint(n) {
        document.querySelectorAll('.star-pick').forEach(function (btn) {
            btn.style.color = parseInt(btn.dataset.star) <= n ? '#fbbf24' : '#d1d5db';
        });
    }

    window.setRating = function (n) {
        _selected = n;
        var inp = document.getElementById('rating-input');
        if (inp) inp.value = n;
        _paint(n);
    };
    window.hoverRating = function (n) { _paint(n); };
    window.resetHover  = function ()  { _paint(_selected); };

    // Restore star state after validation failure (old value present)
    if (_selected > 0) {
        // Defer until DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function () { _paint(_selected); });
        } else {
            _paint(_selected);
        }
    }
})();
</script>

<section id="reviews"
         class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16"
         dir="{{ $isRtl ? 'rtl' : 'ltr' }}">

    <div class="border-t border-gray-100 pt-12">

        {{-- Section header --}}
        <div class="flex items-center gap-3 mb-8">
            <span class="w-1 h-6 rounded-full flex-shrink-0"
                  style="background:var(--brand-color,#0ea5e9)"></span>
            <h2 class="font-display text-2xl font-bold"
                style="color:var(--text-heading,#0f172a);">
                آراء العملاء
            </h2>
            @if($reviewCount > 0)
            <span class="text-sm font-semibold px-2.5 py-0.5 rounded-full bg-gray-100"
                  style="color:var(--text-muted,#9ca3af);">
                {{ $reviewCount }} تقييم
            </span>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

            {{-- ── LEFT: Summary + Form ─────────────────────────────── --}}
            <div class="lg:col-span-1 space-y-6">

                {{-- Rating summary --}}
                @if($reviewCount > 0)
                <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm text-center">
                    <div class="text-5xl font-black mb-2"
                         style="color:var(--text-heading,#0f172a);">
                        {{ number_format($avgRating, 1) }}
                    </div>
                    <div class="flex justify-center gap-0.5 mb-1">
                        @for($i = 1; $i <= 5; $i++)
                        <svg class="w-5 h-5 {{ $i <= round($avgRating) ? 'text-amber-400' : 'text-gray-200' }}"
                             fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        @endfor
                    </div>
                    <p class="text-xs mt-1" style="color:var(--text-muted,#9ca3af);">
                        بناءً على {{ $reviewCount }} تقييم
                    </p>

                    {{-- Breakdown bars --}}
                    @php
                        $allApproved = $reviews->getCollection();
                        $breakdown   = [];
                        for ($s = 5; $s >= 1; $s--) {
                            $cnt         = $allApproved->where('rating', $s)->count();
                            $breakdown[] = [
                                'stars' => $s,
                                'count' => $cnt,
                                'pct'   => $reviewCount > 0 ? round(($cnt / $reviewCount) * 100) : 0,
                            ];
                        }
                    @endphp
                    <div class="mt-4 space-y-1.5" dir="ltr">
                        @foreach($breakdown as $bar)
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-bold text-amber-500 w-4 flex-shrink-0">
                                {{ $bar['stars'] }}★
                            </span>
                            <div class="flex-1 bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                <div class="h-1.5 rounded-full bg-amber-400"
                                     style="width:{{ $bar['pct'] }}%"></div>
                            </div>
                            <span class="text-[10px] text-gray-400 w-4 flex-shrink-0">
                                {{ $bar['count'] }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Flash messages --}}
                @if(session('review_success'))
                <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800
                            px-4 py-3 rounded-xl text-sm">
                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                              d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                              clip-rule="evenodd"/>
                    </svg>
                    {{ session('review_success') }}
                </div>
                @endif

                @if(session('review_error'))
                <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-700
                            px-4 py-3 rounded-xl text-sm">
                    <svg class="w-5 h-5 text-red-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                              d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                              clip-rule="evenodd"/>
                    </svg>
                    {{ session('review_error') }}
                </div>
                @endif

                {{-- Review form --}}
                @if(! $userHasReviewed)
                <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                    <h3 class="font-bold text-sm mb-5"
                        style="color:var(--text-heading,#0f172a);">
                        أضف تقييمك
                    </h3>

                    <form method="POST"
                          action="{{ route('products.reviews.store', $product->slug) }}"
                          class="space-y-4">
                        @csrf

                        {{-- Validation errors --}}
                        @if($errors->any())
                        <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3">
                            <ul class="space-y-1">
                                @foreach($errors->all() as $err)
                                <li class="text-red-600 text-xs font-semibold flex items-center gap-1.5">
                                    <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                    {{ $err }}
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        {{-- Star picker --}}
                        <div>
                            <label class="block text-xs font-bold mb-2 uppercase tracking-wide"
                                   style="color:var(--text-muted,#9ca3af);">
                                التقييم <span class="text-red-400 font-normal">*</span>
                            </label>

                            {{-- ✅ Inline onclick — functions already defined above --}}
                            <div class="flex items-center gap-1" dir="ltr">
                                @for($si = 1; $si <= 5; $si++)
                                <button type="button"
                                        data-star="{{ $si }}"
                                        onclick="setRating({{ $si }})"
                                        onmouseover="hoverRating({{ $si }})"
                                        onmouseout="resetHover()"
                                        class="star-pick w-8 h-8 transition-colors cursor-pointer"
                                        style="color:#d1d5db">
                                    <svg fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </button>
                                @endfor
                            </div>

                            <input type="hidden" name="rating" id="rating-input"
                                   value="{{ old('rating', '') }}">

                            @error('rating')
                            <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Guest fields --}}
                        @guest
                        <div>
                            <label class="block text-xs font-bold mb-1.5 uppercase tracking-wide"
                                   style="color:var(--text-muted,#9ca3af);">
                                الاسم <span class="text-red-400 font-normal">*</span>
                            </label>
                            <input type="text" name="reviewer_name"
                                   value="{{ old('reviewer_name') }}"
                                   required placeholder="اسمك الكريم"
                                   class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm
                                          focus:outline-none focus:ring-2 focus:border-transparent"
                                   style="--tw-ring-color:var(--brand-color,#0ea5e9);
                                          color:var(--text-body,#111827);">
                            @error('reviewer_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold mb-1.5 uppercase tracking-wide"
                                   style="color:var(--text-muted,#9ca3af);">
                                البريد
                                <span class="font-normal normal-case"
                                      style="color:var(--text-muted,#9ca3af);">(اختياري)</span>
                            </label>
                            <input type="email" name="reviewer_email"
                                   value="{{ old('reviewer_email') }}"
                                   placeholder="email@example.com" dir="ltr"
                                   class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm
                                          focus:outline-none focus:ring-2 focus:border-transparent"
                                   style="--tw-ring-color:var(--brand-color,#0ea5e9);
                                          color:var(--text-body,#111827);">
                        </div>
                        @endguest

                        {{-- Comment --}}
                        <div>
                            <label class="block text-xs font-bold mb-1.5 uppercase tracking-wide"
                                   style="color:var(--text-muted,#9ca3af);">
                                تعليقك <span class="text-red-400 font-normal">*</span>
                            </label>
                            <textarea name="comment" rows="4" required
                                      placeholder="شاركنا رأيك في هذا المنتج..."
                                      class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm
                                             resize-none focus:outline-none focus:ring-2 focus:border-transparent"
                                      style="--tw-ring-color:var(--brand-color,#0ea5e9);
                                             color:var(--text-body,#111827);">{{ old('comment') }}</textarea>
                            @error('comment')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit"
                                class="w-full text-white font-bold text-sm px-5 py-3 rounded-xl
                                       hover:opacity-90 active:scale-[.98] transition-all shadow-sm"
                                style="background:var(--brand-color,#0ea5e9);">
                            إرسال التقييم
                        </button>

                        <p class="text-[11px] text-center"
                           style="color:var(--text-muted,#9ca3af);">
                            سيتم نشر تقييمك بعد المراجعة.
                        </p>
                    </form>
                </div>

                @else
                <div class="bg-gray-50 border border-gray-100 rounded-2xl p-5 text-center">
                    <svg class="w-8 h-8 text-green-400 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                              d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                              clip-rule="evenodd"/>
                    </svg>
                    <p class="text-sm" style="color:var(--text-muted,#9ca3af);">
                        لقد قمت بتقييم هذا المنتج مسبقاً.
                    </p>
                </div>
                @endif

            </div>

            {{-- ── RIGHT: Reviews list ──────────────────────────────── --}}
            <div class="lg:col-span-2">

                @if($reviews->isEmpty())
                <div class="flex flex-col items-center justify-center py-16 text-center">
                    <svg class="w-12 h-12 text-gray-200 mb-3"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <p class="font-semibold text-sm" style="color:var(--text-muted,#9ca3af);">
                        لا توجد تقييمات بعد
                    </p>
                    <p class="text-xs mt-1" style="color:var(--text-muted,#9ca3af);">
                        كن أول من يقيّم هذا المنتج!
                    </p>
                </div>

                @else
                <div class="space-y-4">
                    @foreach($reviews as $review)
                    <div class="bg-white rounded-2xl p-5 shadow-sm
                                {{ $review->is_pinned ? 'border-2' : 'border border-gray-100' }}"
                         style="{{ $review->is_pinned
                                    ? 'border-color:var(--brand-color,#0ea5e9)'
                                    : '' }}">

                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center
                                            text-white text-sm font-black flex-shrink-0"
                                     style="background:var(--brand-color,#0ea5e9);">
                                    {{ mb_substr($review->displayName(), 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold leading-tight"
                                       style="color:var(--text-heading,#0f172a);">
                                        {{ $review->displayName() }}
                                        @if($review->user_id)
                                        <span class="text-[10px] font-normal ms-1 px-1.5 py-0.5
                                                     rounded-full bg-green-50 text-green-700">
                                            مشترٍ موثق
                                        </span>
                                        @endif
                                        @if($review->is_pinned)
                                        <span class="text-[10px] font-bold ms-1 px-1.5 py-0.5 rounded-full"
                                              style="background:color-mix(in srgb,var(--brand-color,#0ea5e9) 10%,#fff);
                                                     color:var(--brand-color,#0ea5e9);">
                                            📌 مثبّت
                                        </span>
                                        @endif
                                    </p>
                                    <p class="text-[11px] mt-0.5"
                                       style="color:var(--text-muted,#9ca3af);">
                                        {{ $review->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>

                            {{-- Stars --}}
                            <div class="flex items-center gap-0.5 flex-shrink-0" dir="ltr">
                                @for($i = 1; $i <= 5; $i++)
                                <svg class="w-3.5 h-3.5 {{ $i <= $review->rating ? 'text-amber-400' : 'text-gray-200' }}"
                                     fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                @endfor
                            </div>
                        </div>

                        <p class="text-sm leading-relaxed"
                           style="color:var(--text-body,#111827);">
                            {{ $review->comment }}
                        </p>
                    </div>
                    @endforeach
                </div>

                @if($reviews->hasPages())
                <div class="mt-6">{{ $reviews->links() }}</div>
                @endif
                @endif

            </div>
        </div>
    </div>
</section>