@extends('layouts.app')
@section('title', 'اختر منتجك')

@push('head')
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
:root {
    --ink:        #0a0a0a;
    --ink-light:  #3d3d3d;
    --cream:      #f5f2ec;
    --cream-dark: #eae6de;
    --gold:       #c9a14a;
    --border:     rgba(0,0,0,0.10);
    --shadow-sm:  0 2px 12px rgba(0,0,0,0.06);
    --shadow-lg:  0 24px 60px rgba(0,0,0,0.14);
    --ff-display: 'Playfair Display', Georgia, serif;
    --ff-body:    'DM Sans', system-ui, sans-serif;
}
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: var(--ff-body); background: var(--cream); color: var(--ink); }

.page-customize { min-height: 100vh; padding: 60px 24px 100px; }

/* Hero */
.hero { text-align: center; max-width: 640px; margin: 0 auto 56px; }
.hero__eyebrow {
    display: inline-flex; align-items: center; gap: 10px;
    font-size: 11px; font-weight: 500; letter-spacing: .16em;
    text-transform: uppercase; color: var(--gold); margin-bottom: 20px;
}
.hero__eyebrow::before, .hero__eyebrow::after {
    content: ''; display: block; width: 32px; height: 1px;
    background: var(--gold); opacity: .6;
}
.hero__title {
    font-family: var(--ff-display);
    font-size: clamp(34px, 5vw, 56px);
    font-weight: 800; line-height: 1.08; color: var(--ink);
    margin-bottom: 18px; letter-spacing: -.02em;
}
.hero__title span { position: relative; display: inline-block; }
.hero__title span::after {
    content: ''; position: absolute; bottom: 2px; left: 0; right: 0;
    height: 3px; background: var(--gold); border-radius: 2px;
    transform: scaleX(0); transform-origin: left;
    animation: underline-in .6s .7s cubic-bezier(.22,1,.36,1) forwards;
}
@keyframes underline-in { to { transform: scaleX(1); } }
.hero__sub { font-size: 15px; color: var(--ink-light); line-height: 1.6; font-weight: 300; }

/* Steps */
.steps {
    display: flex; align-items: center; justify-content: center;
    gap: 0; max-width: 480px; margin: 0 auto 52px;
}
.step { display: flex; flex-direction: column; align-items: center; flex: 1; }
.step__num {
    width: 32px; height: 32px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; font-weight: 700; margin-bottom: 6px;
}
.step--active .step__num  { background: var(--ink); color: #fff; }
.step--inactive .step__num { background: var(--cream-dark); color: var(--ink-light); }
.step__label { font-size: 11px; font-weight: 500; white-space: nowrap; }
.step--active .step__label   { color: var(--ink); }
.step--inactive .step__label { color: var(--ink-light); opacity: .6; }
.step__line { flex: 1; height: 1px; background: var(--border); margin-bottom: 18px; max-width: 60px; }

/* Cards grid */
.cards-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 28px; max-width: 1100px; margin: 0 auto;
}
@media (max-width: 900px) { .cards-grid { grid-template-columns: 1fr 1fr; } }
@media (max-width: 580px) { .cards-grid { grid-template-columns: 1fr; max-width: 400px; } }

/* Card */
.garment-card {
    background: #fff; border: 1px solid var(--border); border-radius: 24px;
    overflow: hidden; display: flex; flex-direction: column;
    cursor: pointer; text-decoration: none; color: inherit;
    box-shadow: var(--shadow-sm); position: relative;
    transition: transform .28s cubic-bezier(.22,1,.36,1), box-shadow .28s cubic-bezier(.22,1,.36,1);
    opacity: 0; transform: translateY(28px);
    animation: card-in .55s cubic-bezier(.22,1,.36,1) forwards;
}
.garment-card:nth-child(1) { animation-delay: .10s; }
.garment-card:nth-child(2) { animation-delay: .22s; }
.garment-card:nth-child(3) { animation-delay: .34s; }
@keyframes card-in { to { opacity: 1; transform: translateY(0); } }
.garment-card:hover { transform: translateY(-6px) scale(1.01); box-shadow: var(--shadow-lg); }

.card__ribbon {
    position: absolute; top: 18px; right: 18px;
    background: var(--gold); color: #fff; font-size: 9px;
    font-weight: 700; letter-spacing: .12em; text-transform: uppercase;
    padding: 4px 10px; border-radius: 999px; z-index: 2;
}

/* SVG stage */
.card__stage {
    background: linear-gradient(160deg, #f7f4ef 0%, #ece8e0 100%);
    padding: 32px 20px 20px; display: flex; align-items: center;
    justify-content: center; min-height: 260px; position: relative; overflow: hidden;
}
.card__stage::before {
    content: ''; position: absolute; inset: 0;
    background: radial-gradient(ellipse at 50% 0%, rgba(201,161,74,.12) 0%, transparent 70%);
}
.card__stage svg {
    width: 100%; max-width: 180px; height: auto;
    filter: drop-shadow(0 16px 32px rgba(0,0,0,0.22));
    transition: transform .35s cubic-bezier(.22,1,.36,1); position: relative; z-index: 1;
}
.garment-card:hover .card__stage svg { transform: scale(1.06) translateY(-4px); }

.card__colors {
    display: flex; gap: 6px; position: absolute; bottom: 14px;
    left: 50%; transform: translateX(-50%); z-index: 2;
}
.card__color-dot {
    width: 10px; height: 10px; border-radius: 50%;
    border: 1.5px solid rgba(255,255,255,.7); box-shadow: 0 1px 4px rgba(0,0,0,.18);
}

/* Card info */
.card__info { padding: 20px 22px 24px; display: flex; flex-direction: column; flex: 1; }
.card__type { font-size: 10px; font-weight: 600; letter-spacing: .14em; text-transform: uppercase; color: var(--gold); margin-bottom: 6px; }
.card__name { font-family: var(--ff-display); font-size: 20px; font-weight: 700; color: var(--ink); margin-bottom: 8px; line-height: 1.2; }
.card__desc { font-size: 13px; color: var(--ink-light); line-height: 1.55; flex: 1; font-weight: 300; margin-bottom: 18px; }
.card__meta { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
.card__zones { display: flex; align-items: center; gap: 5px; font-size: 12px; color: var(--ink-light); }
.card__zones svg { opacity: .5; }
.card__price { font-family: var(--ff-display); font-size: 18px; font-weight: 700; color: var(--ink); }
.card__cta {
    display: flex; align-items: center; justify-content: center; gap: 8px;
    background: var(--ink); color: #fff; font-family: var(--ff-body);
    font-size: 13px; font-weight: 500; letter-spacing: .03em;
    padding: 13px 20px; border-radius: 14px; transition: background .18s, gap .22s;
}
.garment-card:hover .card__cta { background: #222; gap: 14px; }
.card__cta svg { transition: transform .22s cubic-bezier(.22,1,.36,1); }
.garment-card:hover .card__cta svg { transform: translateX(4px); }
</style>
@endpush

@section('content')
<div class="page-customize">

    {{-- Hero --}}
    <div class="hero">
        <p class="hero__eyebrow">استوديو التصميم</p>
        <h1 class="hero__title">
            اختر منتجك<br><span>وصمّمه بنفسك</span>
        </h1>
        <p class="hero__sub">
            ثلاثة منتجات، ألوان لا محدودة، مناطق تخصيص متعددة.<br>
            التصميم الذي تراه هو ما ستحصل عليه.
        </p>
    </div>

    {{-- Steps --}}
    <div class="steps" aria-label="خطوات الطلب">
        <div class="step step--active">
            <div class="step__num">١</div>
            <span class="step__label">اختر المنتج</span>
        </div>
        <div class="step__line"></div>
        <div class="step step--inactive">
            <div class="step__num">٢</div>
            <span class="step__label">خصّص التصميم</span>
        </div>
        <div class="step__line"></div>
        <div class="step step--inactive">
            <div class="step__num">٣</div>
            <span class="step__label">أضف للسلة</span>
        </div>
    </div>

    {{-- Cards --}}
    <div class="cards-grid">

        @if($useHardcoded)
        {{-- MODE A: hardcoded cards with inline SVG thumbnails --}}
        @foreach($garmentCards as $card)
        <a class="garment-card"
           href="{{ route('customize.show', $card['key']) }}"
           aria-label="تخصيص {{ $card['name'] }}">

            @if($card['popular'])
            <span class="card__ribbon">الأكثر طلباً</span>
            @endif

            <div class="card__stage">
                @if($card['svg_key'] === 'jacket')
                    @include('customize.index-svgs.jacket-thumb')
                @elseif($card['svg_key'] === 'hoodie')
                    @include('customize.index-svgs.hoodie-thumb')
                @else
                    @include('customize.index-svgs.robe-thumb')
                @endif
                <div class="card__colors">
                    @foreach($card['colors'] as $dot)
                    <div class="card__color-dot" style="background:{{ $dot }};"></div>
                    @endforeach
                </div>
            </div>

            <div class="card__info">
                <p class="card__type">{{ $card['type'] }}</p>
                <h2 class="card__name">{{ $card['name'] }}</h2>
                <p class="card__desc">{{ $card['desc'] }}</p>
                <div class="card__meta">
                    <span class="card__zones">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="7" height="7" rx="1"/>
                            <rect x="14" y="3" width="7" height="7" rx="1"/>
                            <rect x="3" y="14" width="7" height="7" rx="1"/>
                            <rect x="14" y="14" width="7" height="7" rx="1"/>
                        </svg>
                        {{ $card['zones'] }} منطقة
                    </span>
                    <span class="card__price">{{ $card['price'] }} ر.س</span>
                </div>
                <div class="card__cta">
                    <span>ابدأ التخصيص</span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </div>
            </div>
        </a>
        @endforeach

        @else
        {{-- MODE B: real DB products --}}
        @foreach($products as $product)
        @php
            $cfg    = $product->customizationConfig();
            $gType  = $cfg->garmentType();
            $svgKey = match($gType) {
                'hoodie'          => 'hoodie',
                'graduation_robe' => 'robe',
                default           => 'jacket',
            };
            $staticCard = collect($garmentCards)->firstWhere('key', $gType) ?? $garmentCards[0];
            $imgUrl = method_exists($product, 'getFirstMediaUrl')
                    ? $product->getFirstMediaUrl('images') : null;
        @endphp
        <a class="garment-card"
           href="{{ route('customize.show', $product) }}"
           aria-label="تخصيص {{ $product->name }}">

            <div class="card__stage">
                @if($imgUrl)
                    <img src="{{ $imgUrl }}" alt="{{ $product->name }}"
                         style="max-height:200px;object-fit:contain;">
                @elseif($svgKey === 'jacket')
                    @include('customize.index-svgs.jacket-thumb')
                @elseif($svgKey === 'hoodie')
                    @include('customize.index-svgs.hoodie-thumb')
                @else
                    @include('customize.index-svgs.robe-thumb')
                @endif
                <div class="card__colors">
                    @foreach(array_slice(array_map(fn($p) => $p[0], array_filter($cfg->availableColors())), 0, 4) as $dot)
                    <div class="card__color-dot" style="background:{{ $dot }};"></div>
                    @endforeach
                </div>
            </div>

            <div class="card__info">
                <p class="card__type">{{ $staticCard['type'] ?? $gType }}</p>
                <h2 class="card__name">{{ $product->name }}</h2>
                <p class="card__desc">{{ $product->short_description ?? ($staticCard['desc'] ?? '') }}</p>
                <div class="card__meta">
                    <span class="card__zones">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="7" height="7" rx="1"/>
                            <rect x="14" y="3" width="7" height="7" rx="1"/>
                            <rect x="3" y="14" width="7" height="7" rx="1"/>
                            <rect x="14" y="14" width="7" height="7" rx="1"/>
                        </svg>
                        {{ count($cfg->zones()) }} منطقة
                    </span>
                    <span class="card__price">
                        {{ $product->formatted_price ?? number_format($product->price, 2) }} ر.س
                    </span>
                </div>
                <div class="card__cta">
                    <span>ابدأ التخصيص</span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </div>
            </div>
        </a>
        @endforeach
        @endif

    </div>{{-- /cards-grid --}}

</div>{{-- /page-customize --}}
@endsection