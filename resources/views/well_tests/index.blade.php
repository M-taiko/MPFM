@extends('layouts.master')

@section('title', 'Test Archive')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/ofpage.css') }}">
<style>
.ofp .hero { grid-template-columns:1fr 330px; }
.ofp .mini svg { width:100%; height:auto; display:block; }
.ofp .ctl { display:flex; gap:10px; flex-wrap:wrap; align-items:center; margin-bottom:16px; }
.ofp .ctl .sp { flex:1; }
.ofp .ctl select { height:40px; padding:0 34px 0 14px; border-radius:14px; border:1px solid var(--line); background:rgba(255,255,255,.06); color:#fff; font:600 13px 'Inter'; appearance:none; -webkit-appearance:none; outline:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' fill='none' stroke='%2322d3ee' stroke-width='2'%3E%3Cpath d='M1 1l5 5 5-5'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 12px center; }
.ofp .ctl select option { background:#0b1426; }
.ofp table.t td { vertical-align:middle; }
.ofp table.t tr.hide { display:none; }
.ofp table.t tr.sel td { background:#0f2a3a; border-color:rgba(34,211,238,.45); }
.ofp .ck { appearance:none; -webkit-appearance:none; width:20px; height:20px; border-radius:7px; border:2px solid rgba(148,163,184,.5); background:transparent; cursor:pointer; position:relative; transition:all .2s; vertical-align:middle; margin:0; }
.ofp .ck:checked { background:var(--grad); border-color:transparent; }
.ofp .ck:checked::after { content:""; position:absolute; left:5px; top:1px; width:5px; height:10px; border:solid #04111f; border-width:0 2.5px 2.5px 0; transform:rotate(45deg); }
.ofp .wn { font-weight:800; color:#fff; font-size:14px; } .ofp .wn small { display:block; font-weight:500; color:var(--mute); font-size:11.5px; margin-top:2px; }
.ofp .ty { display:inline-block; padding:2px 10px; border-radius:50px; font-size:10px; font-weight:700; letter-spacing:.07em; text-transform:uppercase; border:1px solid var(--line); color:var(--sub); margin-left:6px; }
.ofp .ty.flow { color:var(--green); border-color:rgba(52,211,153,.45); } .ofp .ty.cleanup { color:var(--amber); border-color:rgba(251,191,36,.45); } .ofp .ty.build-up { color:#93c5fd; border-color:rgba(59,130,246,.45); }
.ofp .mt { display:flex; gap:14px; align-items:flex-end; }
.ofp .mt div { text-align:left; } .ofp .mt small { display:block; font-size:9px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--mute); } .ofp .mt b { font:700 14px 'Space Grotesk'; color:#fff; }
.ofp .ob { width:90px; height:5px; border-radius:5px; background:rgba(255,255,255,.08); margin-top:6px; overflow:hidden; } .ofp .ob i { display:block; height:100%; border-radius:5px; background:linear-gradient(90deg,#f59e0b,#fbbf24); }
.ofp .ago { font-size:11.5px; color:var(--mute); }
.ofp .more { text-align:center; margin-top:14px; }
.ofp .bars svg { width:100%; height:auto; display:block; overflow:visible; }
.ofp .bars .ba { transform-origin:bottom; animation:wtGrow .9s var(--ease) both; transform-box:fill-box; } @keyframes wtGrow { from { transform:scaleY(0); } }
.ofp .cbar { position:fixed; left:50%; bottom:22px; transform:translate(-50%,140%); z-index:1100; display:flex; align-items:center; gap:14px; padding:12px 14px 12px 22px; border-radius:50px; background:#0b1426; border:1px solid var(--cyan); box-shadow:0 20px 60px rgba(0,0,0,.6),0 0 40px rgba(34,211,238,.2); transition:transform .5s var(--ease); color:#fff; font-family:'Inter',sans-serif; font-size:13.5px; font-weight:600; }
.ofp .cbar.on { transform:translate(-50%,0); }
.ofp .cbar b { font:700 16px 'Space Grotesk'; color:var(--cyan); }
.ofp .cbar button { padding:9px 20px; border-radius:50px; border:0; font:700 13px 'Inter'; cursor:pointer; background:var(--grad); color:#04111f; } .ofp .cbar button:disabled { opacity:.4; cursor:not-allowed; }
.ofp .cbar a { color:var(--sub); cursor:pointer; font-size:12px; text-decoration:underline; }
@media (max-width:1100px) { .ofp .hero { grid-template-columns:1fr; } .ofp .mini { max-width:340px; } }
</style>
@endsection

@section('content')
@php
    $total = $tests->count();
    $now = now();
    $thisMonth = $tests->filter(fn ($t) => $t['created'] && \Illuminate\Support\Carbon::parse($t['created'])->isSameMonth($now))->count();
    $operators = $tests->pluck('operator')->filter()->unique()->sort()->values();
    $fields = $tests->pluck('field')->filter()->unique()->sort()->values();
    $wells = $tests->pluck('well')->filter()->unique()->count();
    $types = $tests->pluck('test_type')->filter()->unique()->values();
    $maxOil = max(1, (float) $tests->max('oil'));
    $perMonth = collect(range(11, 0))->map(function ($i) use ($tests, $now) {
        $m = $now->copy()->startOfMonth()->subMonths($i);
        return ['label' => $m->format('M'), 'n' => $tests->filter(fn ($t) => $t['date'] && substr($t['date'], 0, 7) === $m->format('Y-m'))->count()];
    });
    $chrono = $tests->filter(fn ($t) => $t['date'])->sortBy('date')->values()->take(-30);
    $chronoJs = $chrono->map(function ($t) { return ['d' => $t['date'], 'w' => $t['well'], 'oil' => $t['oil'], 'gas' => $t['gas']]; })->values();
@endphp
<div class="ofp" id="ofp">
    <div class="bgl"><div class="orb o1"></div><div class="orb o2"></div></div>

    {{-- HERO --}}
    <div class="hero rv">
        <div>
            <div class="who">
                <span class="lg"></span>
                <div>
                    <div class="eyebrow"><i></i> MPFM · Well test archive</div>
                    <h1>Test <em>archive</em></h1>
                    <div class="meta"><span>Every uploaded well test — browse, filter and select two or more to compare</span></div>
                </div>
            </div>
            <div class="actions" style="justify-content:flex-start;margin-top:16px">
                <a class="btn2 p" href="{{ url('/upload') }}"><i class="fe fe-upload"></i> Upload new test</a>
                <a class="btn2" href="{{ route('mpfm_tests.index') }}"><i class="fe fe-activity"></i> MPFM tests</a>
            </div>
        </div>
        <div class="mini">
            <svg viewBox="0 0 330 190" aria-hidden="true">
                <defs><linearGradient id="arSteel" x1="0" y1="0" x2="1" y2="0"><stop offset="0" stop-color="#475569"/><stop offset=".4" stop-color="#cbd5e1"/><stop offset="1" stop-color="#334155"/></linearGradient></defs>
                <rect x="40" y="30" width="250" height="140" rx="10" fill="#0b1426" stroke="#334155" stroke-width="2.5"/>
                @foreach([[44, '#22d3ee'], [82, '#fbbf24'], [120, '#34d399']] as $i => [$y, $c])
                    <g><rect x="52" y="{{ $y + 4 }}" width="226" height="30" rx="6" fill="#0f1b33" stroke="{{ $c }}" stroke-opacity=".55" stroke-width="1.6"/>
                    <rect x="124" y="{{ $y + 14 }}" width="82" height="9" rx="4.5" fill="url(#arSteel)"/>
                    <circle cx="66" cy="{{ $y + 19 }}" r="4" fill="{{ $c }}"/>
                    <path d="M222,{{ $y + 19 }} H262" stroke="{{ $c }}" stroke-width="2" stroke-dasharray="3 5" stroke-opacity=".7"><animate attributeName="stroke-dashoffset" from="0" to="-16" dur="{{ 1.2 + $i * .3 }}s" repeatCount="indefinite"/></path></g>
                @endforeach
                <g><animateTransform attributeName="transform" type="translate" values="0,0;0,-8;0,0" dur="3s" repeatCount="indefinite"/>
                    <path d="M120,6 H168 L184,22 V54 H120 Z" fill="#0b1426" stroke="#fbbf24" stroke-width="2.2" stroke-linejoin="round"/><path d="M168,6 V22 H184" fill="none" stroke="#fbbf24" stroke-width="2.2"/><path d="M130,32 H174 M130,42 H162" stroke="#fbbf24" stroke-width="2.4" stroke-linecap="round"/></g>
            </svg>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="kpis">
        @foreach([
            ['Total tests', $total, 'in the archive', 'fe-file-text', '#22d3ee'],
            ['Added this month', $thisMonth, $now->format('F'), 'fe-calendar', '#34d399'],
            ['Wells tested', $wells, 'distinct wells', 'fe-droplet', '#fbbf24'],
            ['Operators', $operators->count(), 'who uploaded', 'fe-users', '#a78bfa'],
            ['Oil fields', $fields->count(), 'covered', 'fe-map', '#38bdf8'],
        ] as $i => [$l, $v, $u, $ic, $c])
            <div class="kpi rv" style="--d:{{ $i + 1 }};--c:{{ $c }}"><div class="ic"><i class="fe {{ $ic }}"></i></div><div class="l">{{ $l }}</div><div class="v cu" data-to="{{ $v }}" data-dec="0">{{ $v }}</div><div class="u">{{ $u }}</div></div>
        @endforeach
    </div>

    {{-- CHARTS --}}
    <div class="sec g2">
        <div class="panel rv bars">
            <div class="ph"><h3><i class="fe fe-bar-chart-2"></i> Tests per month</h3><span class="sub">Last 12 months by test date</span></div>
            @php $mx = max(1, $perMonth->max('n')); @endphp
            <svg viewBox="0 0 560 210" role="img" aria-label="Tests per month">
                <defs><linearGradient id="wtG" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#22d3ee"/><stop offset="1" stop-color="#3b82f6"/></linearGradient></defs>
                @php $steps = min($mx, 4); @endphp
                @for($g = 0; $g <= $steps; $g++)@php $gy = 14 + (1 - $g / $steps) * 160; @endphp<line x1="30" x2="560" y1="{{ $gy }}" y2="{{ $gy }}" stroke="rgba(255,255,255,.07)"/><text x="24" y="{{ $gy + 4 }}" text-anchor="end" font-size="10.5" fill="#64748b">{{ round($mx * $g / $steps) }}</text>@endfor
                @foreach($perMonth as $i => $m)
                    @php $h = $m['n'] / $mx * 160; $x = 38 + $i * 43.5; @endphp
                    <rect class="ba" x="{{ $x }}" y="{{ 174 - ($m['n'] ? $h : 2) }}" width="30" height="{{ $m['n'] ? $h : 2 }}" rx="5" fill="{{ $m['n'] ? 'url(#wtG)' : 'rgba(255,255,255,.08)' }}" style="animation-delay:{{ $i * .05 }}s"><title>{{ $m['label'] }}: {{ $m['n'] }}</title></rect>
                    @if($m['n'])<text x="{{ $x + 15 }}" y="{{ 168 - $h }}" text-anchor="middle" font-size="11" font-weight="700" fill="#e8eefc">{{ $m['n'] }}</text>@endif
                    <text x="{{ $x + 15 }}" y="196" text-anchor="middle" font-size="10.5" fill="#64748b">{{ $m['label'] }}</text>
                @endforeach
            </svg>
        </div>
        <div class="panel rv" style="--d:1">
            <div class="ph"><h3><i class="fe fe-trending-up"></i> Rates across tests</h3><span class="sub">Oil (left) · gas (right) · latest {{ $chrono->count() }} tests</span></div>
            <div id="rateChart"></div>
        </div>
    </div>

    {{-- LIST --}}
    <div class="sec">
        <div class="panel rv">
            <div class="ph"><h3><i class="fe fe-list"></i> All well tests</h3><span class="sub" id="countInfo"></span></div>
            <div class="ctl">
                <button class="tab on" data-t="all">All ({{ $total }})</button>
                @foreach($types as $ty)<button class="tab" data-t="{{ $ty }}">{{ ucfirst(str_replace('-', ' ', $ty)) }} ({{ $tests->where('test_type', $ty)->count() }})</button>@endforeach
                <span class="sp"></span>
                @if($fields->count() > 1)<select id="fieldF" aria-label="Field"><option value="">All fields</option>@foreach($fields as $f)<option>{{ $f }}</option>@endforeach</select>@endif
                @if($operators->count() > 1)<select id="opF" aria-label="Operator"><option value="">All operators</option>@foreach($operators as $o)<option>{{ $o }}</option>@endforeach</select>@endif
                <select id="sortS" aria-label="Sort"><option value="date_desc">Newest test</option><option value="date_asc">Oldest test</option><option value="oil_desc">Highest oil</option><option value="gor_desc">Highest GOR</option><option value="well_asc">Well A–Z</option></select>
                <span class="srch"><i class="fe fe-search"></i><input type="text" id="q" placeholder="Search well, tower, operator…" autocomplete="off"></span>
            </div>
            @if($total)
            <div class="scroll"><table class="t" id="tbl">
                <thead><tr><th style="width:36px"><input type="checkbox" class="ck" id="selAll" aria-label="Select all"></th><th>Well</th><th>Field · client</th><th>Operator</th><th>Test</th><th>Key metrics</th><th>Added</th><th></th></tr></thead>
                <tbody>
                @foreach($tests as $t)
                    <tr data-id="{{ $t['id'] }}" data-type="{{ $t['test_type'] }}" data-field="{{ $t['field'] }}" data-op="{{ $t['operator'] }}" data-date="{{ $t['date'] }}" data-oil="{{ $t['oil'] ?? 0 }}" data-gor="{{ $t['gor'] ?? 0 }}" data-well="{{ strtolower($t['well']) }}" data-s="{{ strtolower(($t['well'] ?? '').' '.($t['tower'] ?? '').' '.($t['string'] ?? '').' '.($t['operator'] ?? '').' '.($t['field'] ?? '').' '.($t['client'] ?? '').' '.($t['filename'] ?? '')) }}">
                        <td><input type="checkbox" class="ck pick" value="{{ $t['id'] }}" aria-label="Select test {{ $t['id'] }}"></td>
                        <td><div class="wn">{{ $t['well'] ?: 'N/A' }}<small>@if($t['tower'])<i class="fe fe-map-pin"></i> {{ $t['tower'] }}{{ $t['string'] ? ' · '.$t['string'] : '' }}@else #{{ $t['id'] }}@endif</small></div></td>
                        <td>{{ $t['field'] ?: '—' }}<small style="display:block;color:var(--mute);font-size:11.5px">{{ $t['client'] ?: '' }}</small></td>
                        <td>{{ $t['operator'] ?: '—' }}</td>
                        <td>{{ $t['date'] ? \Illuminate\Support\Carbon::parse($t['date'])->format('d M Y') : 'N/A' }}@if($t['test_type'])<span class="ty {{ $t['test_type'] }}">{{ str_replace('-', ' ', $t['test_type']) }}</span>@endif</td>
                        <td>@if($t['oil'] !== null || $t['gas'] !== null || $t['gor'] !== null)
                            <div class="mt">
                                @if($t['oil'] !== null)<div><small>Oil</small><b>{{ number_format($t['oil'], 0) }}</b><div class="ob"><i style="width:{{ round($t['oil'] / $maxOil * 100) }}%"></i></div></div>@endif
                                @if($t['gas'] !== null)<div><small>Gas</small><b>{{ number_format($t['gas'], 2) }}</b></div>@endif
                                @if($t['gor'] !== null)<div><small>GOR</small><b>{{ number_format($t['gor'], 0) }}</b></div>@endif
                            </div>@else<span class="ago">No metrics</span>@endif</td>
                        <td class="ago" data-c="{{ $t['created'] }}">{{ $t['created'] ? \Illuminate\Support\Carbon::parse($t['created'])->diffForHumans() : '' }}</td>
                        <td style="white-space:nowrap"><a class="lnk" href="{{ route('well_tests.show', $t['id']) }}"><i class="fe fe-eye"></i> View</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table></div>
            <div class="more"><button class="btn2" id="moreBtn" style="display:none"><i class="fe fe-chevrons-down"></i> Show more</button></div>
            <div class="empty" id="noMatch" style="display:none">No tests match this filter.</div>
            @else
                <div class="empty">
                    <svg viewBox="0 0 120 90"><rect x="16" y="24" width="88" height="56" rx="8" fill="none" stroke="#64748b" stroke-width="3" stroke-dasharray="6 5"/><path d="M60,66 V40 M48,50 L60,38 L72,50" fill="none" stroke="#64748b" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    The archive is empty.<br><a class="btn2 p" style="margin-top:14px" href="{{ url('/upload') }}"><i class="fe fe-upload"></i> Upload the first test</a>
                </div>
            @endif
        </div>
    </div>

    <div class="cbar" id="cbar"><span><b id="cN">0</b> selected</span><button type="button" id="cGo" disabled><i class="fe fe-shuffle"></i> Compare</button><a id="cClr">Clear</a></div>
    <form id="cForm" method="POST" action="{{ route('well_tests.compare') }}" style="display:none">@csrf</form>
</div>
@endsection

@section('js')
<script src="{{ asset('assets/js/ofcharts.js') }}"></script>
<script>
(function () {
    var root = document.getElementById('ofp');
    var reduce = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;
    var CH = @json($chronoJs);
    if (CH.length) OFCharts.line(document.getElementById('rateChart'), {
        labels: CH.map(function (t) { return t.d + (t.w ? ' · ' + t.w : ''); }), height: 240, dec: 0, decR: 1, smooth: CH.length < 30, xFmt: function (l) { return String(l).slice(5, 10); },
        series: [{ name: 'Oil (STB/d)', data: CH.map(function (t) { return t.oil; }), color: '#fbbf24', axis: 'l', dec: 0 }, { name: 'Gas (MMScf/d)', data: CH.map(function (t) { return t.gas; }), color: '#22d3ee', axis: 'r', dec: 2 }]
    }); else document.getElementById('rateChart').innerHTML = '<div class="empty">No tests to chart yet</div>';

    // ---- list: filter / sort / paging ----
    var tb = document.querySelector('#tbl tbody'); if (!tb) return;
    var rows = [].slice.call(tb.rows), type = 'all', f = '', op = '', q = '', sort = 'date_desc', shown = 25, PAGE = 25;
    function apply() {
        var list = rows.filter(function (r) { return (type === 'all' || r.dataset.type === type) && (!f || r.dataset.field === f) && (!op || r.dataset.op === op) && (!q || r.dataset.s.indexOf(q) > -1); });
        var d = sort.split('_'), key = d[0], asc = d[1] === 'asc';
        list.sort(function (a, b) {
            var x, y;
            if (key === 'well') { x = a.dataset.well; y = b.dataset.well; } else if (key === 'date') { x = a.dataset.date; y = b.dataset.date; } else { x = parseFloat(a.dataset[key]); y = parseFloat(b.dataset[key]); }
            return (x < y ? -1 : x > y ? 1 : 0) * (asc ? 1 : -1);
        });
        rows.forEach(function (r) { r.classList.add('hide'); });
        list.forEach(function (r, i) { tb.appendChild(r); r.classList.toggle('hide', i >= shown); });
        document.getElementById('countInfo').textContent = list.length + ' of ' + rows.length + ' tests';
        document.getElementById('moreBtn').style.display = list.length > shown ? '' : 'none';
        document.getElementById('noMatch').style.display = list.length ? 'none' : 'block';
    }
    root.querySelectorAll('.ctl .tab').forEach(function (t) { t.addEventListener('click', function () { root.querySelectorAll('.ctl .tab').forEach(function (x) { x.classList.remove('on'); }); t.classList.add('on'); type = t.dataset.t; shown = PAGE; apply(); }); });
    var ff = document.getElementById('fieldF'); if (ff) ff.addEventListener('change', function () { f = this.value; shown = PAGE; apply(); });
    var of = document.getElementById('opF'); if (of) of.addEventListener('change', function () { op = this.value; shown = PAGE; apply(); });
    document.getElementById('sortS').addEventListener('change', function () { sort = this.value; apply(); });
    document.getElementById('q').addEventListener('input', function () { q = this.value.toLowerCase(); shown = PAGE; apply(); });
    document.getElementById('moreBtn').addEventListener('click', function () { shown += PAGE; apply(); });
    apply();

    // ---- selection + compare ----
    var bar = document.getElementById('cbar'), go = document.getElementById('cGo');
    function picks() { return [].slice.call(tb.querySelectorAll('.pick:checked')); }
    function sync() {
        var p = picks(); document.getElementById('cN').textContent = p.length;
        bar.classList.toggle('on', p.length > 0); go.disabled = p.length < 2; go.innerHTML = '<i class="fe fe-shuffle"></i> ' + (p.length < 2 ? 'Pick one more' : 'Compare ' + p.length + ' tests');
        rows.forEach(function (r) { r.classList.toggle('sel', r.querySelector('.pick').checked); });
        var vis = rows.filter(function (r) { return !r.classList.contains('hide'); });
        document.getElementById('selAll').checked = vis.length > 0 && vis.every(function (r) { return r.querySelector('.pick').checked; });
    }
    tb.addEventListener('change', sync);
    document.getElementById('selAll').addEventListener('change', function () { var c = this.checked; rows.forEach(function (r) { if (!r.classList.contains('hide')) r.querySelector('.pick').checked = c; }); sync(); });
    document.getElementById('cClr').addEventListener('click', function () { rows.forEach(function (r) { r.querySelector('.pick').checked = false; }); sync(); });
    go.addEventListener('click', function () {
        var form = document.getElementById('cForm'); form.querySelectorAll('input[name="test_ids[]"]').forEach(function (n) { n.remove(); });
        picks().forEach(function (c) { var i = document.createElement('input'); i.type = 'hidden'; i.name = 'test_ids[]'; i.value = c.value; form.appendChild(i); });
        form.submit();
    });

    function countUp(el) {
        var to = parseFloat(el.dataset.to) || 0, t0 = null; if (reduce) return;
        function step(t) { if (t0 === null) t0 = t; var p = Math.min((t - t0) / 1200, 1), e = 1 - Math.pow(1 - p, 4); el.textContent = Math.round(to * e).toLocaleString('en-US'); if (p < 1) requestAnimationFrame(step); }
        requestAnimationFrame(step);
    }
    var io = new IntersectionObserver(function (es) { es.forEach(function (e) { if (!e.isIntersecting) return; e.target.classList.add('in'); e.target.querySelectorAll('.cu').forEach(countUp); io.unobserve(e.target); }); }, { threshold: .08 });
    root.querySelectorAll('.rv').forEach(function (el) { io.observe(el); });
    if (reduce) root.querySelectorAll('svg').forEach(function (s) { if (s.pauseAnimations) s.pauseAnimations(); });
})();
</script>
@endsection
