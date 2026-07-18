{{--
    products/_announcements.blade.php — brand announcement ticker bars
    (mobile marquee + static desktop row). Needs: $isRtl.
--}}
@php
    $announcements = \App\Models\Announcement::where('is_active', true)
                         ->orderBy('sort_order')->get();
@endphp
@if($announcements->count() > 0)
<div class="announce-bar md:hidden" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
    <div class="announce-ticker" aria-hidden="true">
        @foreach($announcements->concat($announcements) as $item)
            <span>{{ $item->content }}</span>
            <span class="announce-dot"></span>
        @endforeach
    </div>
</div>
<div class="announce-bar hidden md:flex" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
    @foreach($announcements as $item)
        <span>{{ $item->content }}</span>
        @if(!$loop->last)<span class="announce-dot"></span>@endif
    @endforeach
</div>
@endif
