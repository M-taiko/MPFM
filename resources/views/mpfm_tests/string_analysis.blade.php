@extends('layouts.master')

@section('title', 'Oil String Analysis - ' . $oilString->name)

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/ofpage.css') }}">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
<style>
.ofp .top { display:grid; grid-template-columns:minmax(300px,.85fr) 1.5fr; gap:18px; margin-top:18px; align-items:stretch; }
.ofp .top .kpis { margin-top:0; grid-template-columns:repeat(2,1fr); }
.ofp .well svg { width:100%; height:auto; display:block; }
.ofp .well text { font-family:'Inter',sans-serif; }
.ofp .wl { font-size:8.5px; font-weight:700; letter-spacing:.12em; }
.ofp .wv { font:700 15px 'Space Grotesk',sans-serif; fill:#fff; }
.ofp .wu { font-size:8.5px; fill:#9fb0cf; }
.ofp .wflow { fill:none; stroke-width:2; stroke-linecap:round; stroke-dasharray:3 8; animation:saDash 1.1s linear infinite; }
@keyframes saDash { to { stroke-dashoffset:-22; } }
.ofp .wpulse { animation:saPulse 2.4s ease-in-out infinite; } @keyframes saPulse { 50% { opacity:.35; } }
.ofp .latest { display:flex; gap:10px; flex-wrap:wrap; margin-top:14px; }
.ofp .latest div { flex:1; min-width:120px; padding:11px 13px; border-radius:14px; background:var(--glass); border:1px solid var(--line); }
.ofp .latest small { display:block; font-size:9.5px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--mute); }
.ofp .latest b { font:700 18px 'Space Grotesk'; color:#fff; } .ofp .latest span { font-size:11px; color:var(--sub); margin-left:4px; }
.ofp .ftabs { display:flex; gap:8px; flex-wrap:wrap; }
.ofp .ty { display:inline-flex; align-items:center; gap:6px; padding:3px 11px; border-radius:50px; font-size:10.5px; font-weight:700; letter-spacing:.06em; border:1px solid var(--line); }
.ofp .ty.mpfm { color:var(--cyan); border-color:rgba(34,211,238,.5); background:rgba(34,211,238,.1); }
.ofp .ty.tv { color:var(--amber); border-color:rgba(251,191,36,.5); background:rgba(251,191,36,.1); }
.ofp tr.hide { display:none; }
.ofp .alerts { display:grid; gap:10px; }
.ofp .meta .sep { color:var(--mute); }
@media (max-width:1100px) { .ofp .top { grid-template-columns:1fr; } }
</style>
@endsection

@section('content')
@php
    $f = fn ($v, $d = 2) => is_numeric($v) ? number_format((float) $v, $d) : '—';
    $tower = $oilString->tower; $field = $tower->oilField ?? null;
    $total = $allTests->count();
    $latest = $allTests->first();
    $lt = $latest['test'] ?? null; $ltType = $latest['type'] ?? null;

    // chart data: one point per date, MPFM and TV on the same timeline
    $dates = $allTests->map(fn ($i) => $i['test']->test_date->format('Y-m-d'))->unique()->sort()->values()->take(-40)->values();
    $series = function ($coll, $col) use ($dates) {
        $by = $coll->groupBy(fn ($t) => $t->test_date->format('Y-m-d'));
        return $dates->map(fn ($d) => isset($by[$d]) ? round((float) $by[$d]->avg($col), 3) : null)->all();
    };
    $chart = [
        'labels' => $dates->all(),
        'oil' => ['mpfm' => $series($mpfmTests, 'oil_rate'), 'tv' => $series($tvTests, 'oil_rate')],
        'gas' => ['mpfm' => $series($mpfmTests, 'gas_rate'), 'tv' => $series($tvTests, 'gas_rate')],
        'wc' => ['mpfm' => $series($mpfmTests, 'water_cut'), 'tv' => $series($tvTests, 'water_cut')],
    ];
    $alerts = $trendingData['alerts'] ?? [];
    $nearest = function ($tv) use ($mpfmTests) {
        return $mpfmTests->sortBy(fn ($m) => abs($m->test_date->diffInDays($tv->test_date)))->first();
    };
    $oilV = $lt ? (float) $lt->oil_rate : 0; $gasV = $lt ? (float) $lt->gas_rate : 0; $wcV = $lt ? (float) $lt->water_cut : 0;
@endphp

<div class="ofp" id="ofp">
    <div class="bgl"><div class="orb o1"></div><div class="orb o2"></div></div>

    {{-- HERO --}}
    <div class="hero rv">
        <div class="who">
            <span class="lg"></span>
            <div>
                <div class="eyebrow"><i></i> Oil string analysis</div>
                <h1>{{ $oilString->name }} <em>· full test history</em></h1>
                <div class="meta">
                    @if($field)<a href="{{ route('oil_fields.show', $field->id) }}"><i class="fe fe-map-pin"></i> {{ $field->name }}</a><span class="sep">/</span>@endif
                    @if($tower)<a href="{{ route('towers.show', $tower->id) }}"><i class="fe fe-layers"></i> {{ $tower->name }}</a><span class="sep">/</span>@endif
                    <span>{{ $oilString->name }}</span>
                </div>
            </div>
        </div>
        <div class="actions">
            @if($wlAccess)<a class="btn2 p" href="{{ route('wireline.create', ['well' => $oilString->id]) }}"><i class="fe fe-git-branch"></i> Wire Line op</a>@endif
            @if($intAccess)<a class="btn2 p" href="{{ route('integrity.show', $oilString) }}"><i class="fe fe-shield"></i> Well integrity</a>@endif
            <a class="btn2 g" href="{{ route('tv_tests.compare') }}"><i class="fe fe-shuffle"></i> Compare vs TV</a>
            <a class="btn2" href="{{ route('mpfm_tests.trending_dashboard') }}"><i class="fe fe-bar-chart-2"></i> Trending dashboard</a>
            <a class="btn2" href="{{ route('mpfm_tests.index') }}"><i class="fe fe-arrow-left"></i> MPFM tests</a>
        </div>
    </div>

    @if($analysis)
        @include('mpfm_tests._string_insight')
    @endif

    {{-- WELL DRAWING + STATS --}}
    <div class="top">
        <div class="panel well rv">
            <div class="ph" style="margin-bottom:6px"><h3><i class="fe fe-map-pin"></i> The string</h3><span class="sub">{{ $lt ? 'Latest reading · '.($ltType === 'mpfm' ? 'MPFM' : 'Well testing') : 'No tests yet' }}</span></div>
<svg viewBox="0 0 360 430" role="img" aria-label="Well string drawing">
<defs>
    <linearGradient id="saSteel" x1="0" y1="0" x2="1" y2="0"><stop offset="0" stop-color="#475569"/><stop offset=".35" stop-color="#e2e8f0"/><stop offset=".65" stop-color="#94a3b8"/><stop offset="1" stop-color="#334155"/></linearGradient>
    <linearGradient id="saRock" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#3b2a1d"/><stop offset="1" stop-color="#1a120b"/></linearGradient>
    <linearGradient id="saOil" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#f59e0b"/><stop offset="1" stop-color="#78350f"/></linearGradient>
    <linearGradient id="saSky" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#0b1426" stop-opacity="0"/><stop offset="1" stop-color="#22d3ee" stop-opacity=".1"/></linearGradient>
    <path id="saTube" d="M180,372 V150"/>
    <path id="saLine" d="M180,96 H330"/>
</defs>
<rect x="0" y="0" width="360" height="120" fill="url(#saSky)"/>
{{-- ground + strata --}}
<rect x="0" y="120" width="360" height="310" fill="url(#saRock)"/>
<path d="M0,120 H360" stroke="#64748b" stroke-width="2"/>
<path d="M0,190 Q90,180 180,192 T360,186 M0,262 Q100,274 190,260 T360,266" fill="none" stroke="#5a4330" stroke-width="1.5" opacity=".7"/>
<path d="M0,330 Q90,318 180,332 T360,326 V430 H0 Z" fill="url(#saOil)" opacity=".85"/>
<text x="12" y="352" class="wl" fill="#fde68a">RESERVOIR</text>
<text x="12" y="140" class="wl" fill="#94a3b8">SURFACE</text>
<text x="12" y="212" class="wl" fill="#8a6d52">CAP ROCK</text>
{{-- casing + tubing --}}
<rect x="164" y="120" width="32" height="226" fill="#1e293b" stroke="#0b1220"/>
<rect x="172" y="120" width="16" height="252" fill="url(#saSteel)" stroke="#0b1220"/>
<rect x="176" y="150" width="8" height="222" fill="#0a1222"/>
<path d="M176,150 V372" stroke="url(#saOil)" stroke-width="8" opacity=".9"/>
{{-- perforations --}}
@foreach([338, 348, 358] as $py)
    <path d="M164,{{ $py }} H146 M196,{{ $py }} H214" stroke="#fbbf24" stroke-width="2.2" stroke-linecap="round"/>
    <path class="wflow" d="M122,{{ $py }} H162 M238,{{ $py }} H198" stroke="#fde68a"/>
@endforeach
{{-- wellhead / christmas tree --}}
<rect x="160" y="108" width="40" height="14" rx="2" fill="url(#saSteel)" stroke="#0b1220"/>
<rect x="168" y="82" width="24" height="28" fill="url(#saSteel)" stroke="#0b1220"/>
<path d="M180,82 V56" stroke="#94a3b8" stroke-width="7"/><circle cx="180" cy="48" r="9" fill="#0a1222" stroke="#22d3ee" stroke-width="1.8"/><path d="M180,48 L184,43" stroke="#fbbf24" stroke-width="1.8" stroke-linecap="round"/>
<rect x="172" y="60" width="16" height="6" fill="#64748b" stroke="#0b1220"/>
<g fill="none"><use href="#saLine" stroke="#0b1220" stroke-width="13"/><use href="#saLine" stroke="#64748b" stroke-width="9"/></g>
<path d="M240,88 l12,8 l-12,8 Z M262,88 l-12,8 l12,8 Z" fill="#1e293b" stroke="#94a3b8" stroke-width="1.6" transform="translate(-6,0)"/>
<path d="M246,88 V72" stroke="#94a3b8" stroke-width="2"/><rect x="238" y="66" width="16" height="5" rx="2" fill="#94a3b8"/>
<text x="330" y="78" text-anchor="end" class="wl" fill="#67e8f9">FLOWLINE → MANIFOLD</text>
<text x="196" y="132" class="wl" fill="#cbd5e1">{{ \Illuminate\Support\Str::limit($oilString->name, 14) }}</text>
{{-- moving fluid --}}
@foreach([0, .9, 1.8, 2.7, 3.6] as $b)
    <circle r="3.4" fill="#fde68a"><animateMotion dur="4.5s" begin="{{ $b }}s" repeatCount="indefinite"><mpath href="#saTube"/></animateMotion></circle>
    <circle r="2.4" fill="#a5f3fc"><animateMotion dur="4.5s" begin="{{ $b + .45 }}s" repeatCount="indefinite"><mpath href="#saTube"/></animateMotion></circle>
    <circle r="3.2" fill="#fde68a"><animateMotion dur="2.6s" begin="{{ $b }}s" repeatCount="indefinite"><mpath href="#saLine"/></animateMotion></circle>
@endforeach
{{-- readouts --}}
<g>
    <rect x="228" y="150" width="124" height="44" rx="10" fill="#0a1222" stroke="#fbbf24" stroke-opacity=".6"/><text x="238" y="165" class="wl" fill="#fcd34d">OIL RATE</text><text x="238" y="185" class="wv">{{ $lt ? $f($oilV, 1) : '—' }}</text><text x="{{ 238 + 10 + strlen($lt ? $f($oilV, 1) : '—') * 8.4 }}" y="185" class="wu">B/D</text>
    <rect x="228" y="204" width="124" height="44" rx="10" fill="#0a1222" stroke="#22d3ee" stroke-opacity=".6"/><text x="238" y="219" class="wl" fill="#67e8f9">GAS RATE</text><text x="238" y="239" class="wv">{{ $lt ? $f($gasV, 2) : '—' }}</text><text x="{{ 238 + 10 + strlen($lt ? $f($gasV, 2) : '—') * 8.4 }}" y="239" class="wu">MMscf/D</text>
    <rect x="228" y="258" width="124" height="44" rx="10" fill="#0a1222" stroke="#38bdf8" stroke-opacity=".6"/><text x="238" y="273" class="wl" fill="#7dd3fc">WATER CUT</text><text x="238" y="293" class="wv">{{ $lt ? $f($wcV, 1) : '—' }}</text><text x="{{ 238 + 10 + strlen($lt ? $f($wcV, 1) : '—') * 8.4 }}" y="293" class="wu">%</text>
    <circle cx="340" cy="160" r="3.4" fill="#34d399" class="wpulse"/>
</g>
</svg>
        </div>

        <div style="display:grid;gap:18px;align-content:start">
            <div class="kpis rv">
                <div class="kpi" style="--c:#22d3ee"><div class="ic"><i class="fe fe-activity"></i></div><div class="l">Total tests</div><div class="v cu" data-to="{{ $total }}" data-dec="0">{{ $total }}</div><div class="u">on this string</div></div>
                <div class="kpi" style="--c:#a78bfa"><div class="ic"><i class="fe fe-calendar"></i></div><div class="l">Latest test</div><div class="v" style="font-size:19px">{{ $lt ? $lt->test_date->format('d M Y') : 'N/A' }}</div><div class="u">{{ $lt ? ($ltType === 'mpfm' ? 'MPFM' : 'Well testing') : '' }}</div></div>
                <div class="kpi" style="--c:#22d3ee"><div class="ic"><i class="fe fe-bar-chart-2"></i></div><div class="l">MPFM tests</div><div class="v cu" data-to="{{ $mpfmTests->count() }}" data-dec="0">{{ $mpfmTests->count() }}</div><div class="u">{{ $f($mpfmTests->avg('oil_rate'), 0) }} B/D avg oil</div></div>
                <div class="kpi" style="--c:#fbbf24"><div class="ic"><i class="fe fe-anchor"></i></div><div class="l">Well testing runs</div><div class="v cu" data-to="{{ $tvTests->count() }}" data-dec="0">{{ $tvTests->count() }}</div><div class="u">{{ $f($tvTests->avg('oil_rate'), 0) }} B/D avg oil</div></div>
            </div>
            <div class="panel rv" style="--d:1">
                <div class="ph"><h3><i class="fe fe-info"></i> String information</h3></div>
                <div class="infog">
                    <div class="info"><div class="l"><i class="fe fe-anchor"></i> Oil string</div><div class="v">{{ $oilString->name }}</div></div>
                    <div class="info"><div class="l"><i class="fe fe-layers"></i> Tower</div><div class="v">{{ $tower->name ?? '—' }}</div></div>
                    <div class="info"><div class="l"><i class="fe fe-map-pin"></i> Oil field</div><div class="v">{{ $field->name ?? '—' }}</div></div>
                    <div class="info"><div class="l"><i class="fe fe-activity"></i> Status</div><div class="v">{{ ucfirst($oilString->status ?? '—') }}</div></div>
                </div>
                @if($lt)
                    <div class="latest">
                        @if($lt->pressure)<div><small>Pressure</small><b>{{ $f($lt->pressure, 0) }}</b><span>psi</span></div>@endif
                        @if($lt->temperature)<div><small>Temperature</small><b>{{ $f($lt->temperature, 1) }}</b><span>°</span></div>@endif
                        <div><small>Shift</small><b style="font-size:15px">{{ ucfirst($lt->shift) }}</b></div>
                        <div><small>Status</small><b style="font-size:15px">{{ ucfirst($lt->status) }}</b></div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ALERTS --}}
    @if(count($alerts))
    <div class="sec"><div class="panel rv">
        <div class="ph"><h3><i class="fe fe-alert-triangle"></i> Trend alerts</h3><span class="sub">{{ count($alerts) }} flagged</span></div>
        <div class="alerts">
            @foreach($alerts as $a)
                <div class="msg warn"><i class="fe fe-alert-triangle"></i><div>{{ is_array($a) ? ($a['message'] ?? ($a['type'] ?? json_encode($a))) : $a }}</div></div>
            @endforeach
        </div>
    </div></div>
    @endif

    {{-- CHARTS --}}
    @if($total)
    <div class="sec">
        <div class="panel rv">
            <div class="ph"><h3><i class="fe fe-trending-up"></i> Oil rate — MPFM vs well testing</h3><span class="sub">{{ $dates->count() }} test dates · click a legend chip to hide a series</span></div>
            <div id="chOil"></div>
        </div>
    </div>
    <div class="sec g2">
        <div class="panel rv"><div class="ph"><h3><i class="fe fe-wind"></i> Gas rate</h3><span class="sub">MMscf/D</span></div><div id="chGas"></div></div>
        <div class="panel rv" style="--d:1"><div class="ph"><h3><i class="fe fe-percent"></i> Water cut</h3><span class="sub">%</span></div><div id="chWc"></div></div>
    </div>
    @endif

    @if($wlAccess)
        @include('mpfm_tests._string_wireline')
    @endif

    {{-- TABLE --}}
    <div class="sec">
        <div class="panel rv">
            <div class="ph"><h3><i class="fe fe-list"></i> Complete test history</h3>
                <span style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
                    <span class="ftabs">
                        <button class="tab on" data-t="all">All ({{ $total }})</button>
                        <button class="tab" data-t="mpfm">MPFM ({{ $mpfmTests->count() }})</button>
                        <button class="tab" data-t="tv">Well testing ({{ $tvTests->count() }})</button>
                    </span>
                    <span class="srch"><i class="fe fe-search"></i><input type="text" id="q" placeholder="Search…" autocomplete="off"></span>
                </span></div>
            @if($total)
                <div class="scroll"><table class="t" id="hist">
                    <thead><tr><th>Date</th><th>Type</th><th>Test</th><th>Status</th><th>Shift</th><th>Oil (B/D)</th><th>Gas (MMscf/D)</th><th>WC (%)</th><th>Operator</th><th></th></tr></thead>
                    <tbody>
                    @foreach($allTests as $item)
                        @php $t = $item['test']; $ty = $item['type']; @endphp
                        <tr data-type="{{ $ty }}">
                            <td>{{ $t->test_date->format('Y-m-d') }}</td>
                            <td><span class="ty {{ $ty }}"><i class="fe {{ $ty === 'mpfm' ? 'fe-bar-chart-2' : 'fe-anchor' }}"></i> {{ $ty === 'mpfm' ? 'MPFM' : 'TV' }}</span></td>
                            <td><a class="lnk" href="{{ $ty === 'mpfm' ? route('mpfm_tests.show', $t->id) : route('tv_tests.show', $t->id) }}">{{ $ty === 'mpfm' ? 'MPFM' : 'TV' }} #{{ $t->id }}</a></td>
                            <td><span class="chip {{ $t->status }}">{{ $t->status }}</span></td>
                            <td>{{ ucfirst($t->shift) }}</td>
                            <td>{{ $f($t->oil_rate) }}</td><td>{{ $f($t->gas_rate) }}</td><td>{{ $f($t->water_cut) }}</td>
                            <td>{{ $t->user->name ?? '-' }}</td>
                            <td style="white-space:nowrap">
                                @if($ty === 'mpfm')
                                    <a class="lnk" href="{{ route('mpfm_tests.show', $t->id) }}"><i class="fe fe-eye"></i> View</a>
                                @else
                                    <a class="lnk" href="{{ route('tv_tests.show', $t->id) }}"><i class="fe fe-eye"></i> View</a>
                                    @php $nm = $nearest($t); @endphp
                                    @if($nm)<a class="lnk w" href="{{ route('tv_tests.compare_result', ['tv_test_id' => $t->id, 'mpfm_test_id' => $nm->id]) }}" title="Closest MPFM test: #{{ $nm->id }}"><i class="fe fe-shuffle"></i> Compare</a>@endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table></div>
            @else
                <div class="empty">
                    <svg viewBox="0 0 120 90"><circle cx="52" cy="40" r="26" fill="none" stroke="#64748b" stroke-width="4"/><path d="M72,60 L96,82" stroke="#64748b" stroke-width="6" stroke-linecap="round"/><path d="M40,40 H64" stroke="#64748b" stroke-width="4" stroke-linecap="round" stroke-dasharray="4 6"/></svg>
                    No tests recorded for this oil string yet.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('assets/js/ofcharts.js') }}"></script>
<script>
(function () {
    var root = document.getElementById('ofp');
    var reduce = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;
    var D = @json($chart);
    function pair(key, o) {
        var el = document.getElementById(key.el); if (!el) return;
        OFCharts.line(el, Object.assign({
            labels: D.labels, height: key.h || 260,
            series: [
                { name: 'MPFM', data: D[key.k].mpfm, color: '#22d3ee', unit: key.u },
                { name: 'Well testing', data: D[key.k].tv, color: '#fbbf24', unit: key.u }
            ]
        }, o));
    }
    if (D.labels.length) {
        pair({ el: 'chOil', k: 'oil', u: 'B/D', h: 300 }, { dec: 0 });
        pair({ el: 'chGas', k: 'gas', u: 'MMscf/D' }, { dec: 2 });
        pair({ el: 'chWc', k: 'wc', u: '%' }, { dec: 1 });
    }

    var type = 'all', qv = '';
    function apply() {
        document.querySelectorAll('#hist tbody tr').forEach(function (tr) {
            var ok = (type === 'all' || tr.dataset.type === type) && (!qv || tr.textContent.toLowerCase().indexOf(qv) > -1);
            tr.classList.toggle('hide', !ok);
        });
    }
    root.querySelectorAll('.ftabs .tab').forEach(function (t) {
        t.addEventListener('click', function () { root.querySelectorAll('.ftabs .tab').forEach(function (x) { x.classList.remove('on'); }); t.classList.add('on'); type = t.dataset.t; apply(); });
    });
    var q = document.getElementById('q'); if (q) q.addEventListener('input', function () { qv = q.value.toLowerCase(); apply(); });

    function fmt(v, d) { return v.toLocaleString('en-US', { minimumFractionDigits: d, maximumFractionDigits: d }); }
    function countUp(el) {
        var to = parseFloat(el.dataset.to) || 0, d = parseInt(el.dataset.dec || 0, 10), t0 = null;
        if (reduce) return;
        function step(t) { if (t0 === null) t0 = t; var p = Math.min((t - t0) / 1300, 1), e = 1 - Math.pow(1 - p, 4); el.textContent = fmt(to * e, d); if (p < 1) requestAnimationFrame(step); else el.textContent = fmt(to, d); }
        requestAnimationFrame(step);
    }
    var io = new IntersectionObserver(function (es) { es.forEach(function (e) { if (!e.isIntersecting) return; e.target.classList.add('in'); e.target.querySelectorAll('.cu').forEach(countUp); io.unobserve(e.target); }); }, { threshold: .1 });
    root.querySelectorAll('.rv').forEach(function (el) { io.observe(el); });
    if (reduce) root.querySelectorAll('svg').forEach(function (s) { if (s.pauseAnimations) s.pauseAnimations(); });
})();
</script>
@endsection
