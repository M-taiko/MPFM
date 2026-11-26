@extends('layouts.master')

@section('title', 'Dashboard - Oil Field Management System')

@section('css')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/css/oilfield-anim.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/dash-illustrations.css') }}">
<style>
.ent { --glass:rgba(255,255,255,.05); --grad:linear-gradient(135deg,#3b82f6,#22d3ee); --ease:cubic-bezier(.22,1,.36,1); --blue2:#2563eb; --bg:#070d1c; --card:#0d1730; --card2:#111d3a; --ink:#e7edfb; --sub:#9aaccb; --mute:#64748b; --line:rgba(120,150,210,.16);
    --blue:#3b82f6; --cyan:#22d3ee; --green:#34d399; --amber:#fbbf24; --red:#f87171; --violet:#a78bfa; --orange:#fb923c;
    direction:ltr; text-align:left; padding:24px; border-radius:20px; background:var(--bg); color:var(--ink); font-family:'Inter',sans-serif; -webkit-font-smoothing:antialiased; }
.ent *, .ent *::before, .ent *::after { box-sizing:border-box; }
.ent a { text-decoration:none; color:inherit; }
.ent h1, .ent h2, .ent h3, .ent h4, .ent p { margin:0; color:inherit; }

.ent .top { display:flex; justify-content:space-between; align-items:flex-end; gap:20px; flex-wrap:wrap; padding-bottom:20px; border-bottom:1px solid var(--line); }
.ent .eyebrow { font-size:11px; font-weight:700; letter-spacing:.16em; text-transform:uppercase; color:var(--cyan); display:flex; gap:8px; align-items:center; }
.ent .eyebrow i { width:8px; height:8px; border-radius:50%; background:var(--green); box-shadow:0 0 0 0 rgba(52,211,153,.6); animation:entPing 2s infinite; }
@keyframes entPing { to { box-shadow:0 0 0 10px rgba(52,211,153,0); } }
.ent h1 { font-size:clamp(22px,2.6vw,32px); font-weight:800; letter-spacing:-.02em; margin:6px 0 6px; }
.ent .meta { color:var(--sub); font-size:13px; display:flex; gap:14px; flex-wrap:wrap; align-items:center; }
.ent .who { display:flex; align-items:center; gap:20px; }
.ent .ct { font:700 12px 'Space Grotesk',sans-serif; letter-spacing:.18em; text-transform:uppercase; color:var(--cyan); margin-bottom:12px; display:flex; align-items:center; justify-content:space-between; gap:10px; }
.ent .ct a { color:var(--sub); letter-spacing:.04em; font-size:11px; text-transform:none; font-family:'Inter',sans-serif; font-weight:600; }
.ent .ct a:hover { color:var(--cyan); }
.ent .ill { background:linear-gradient(180deg,rgba(255,255,255,.05),rgba(255,255,255,.015)); }
.ent .acts { display:flex; gap:10px; flex-wrap:wrap; }
.ent .btn { display:inline-flex; align-items:center; gap:8px; padding:9px 16px; border-radius:10px; font-size:13px; font-weight:600; border:1px solid var(--line); background:var(--card); color:var(--ink); transition:border-color .2s, transform .2s; cursor:pointer; font-family:inherit; }
.ent .btn:hover { border-color:var(--cyan); transform:translateY(-2px); color:#fff; }
.ent .btn.p { background:linear-gradient(135deg,#2563eb,#0ea5e9); border:0; color:#fff; box-shadow:0 6px 18px rgba(14,165,233,.28); }

.ent .kpis { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-top:20px; }
.ent .kpi { background:var(--card); border:1px solid var(--line); border-radius:14px; padding:16px 18px; position:relative; overflow:hidden; }
.ent .kpi::before { content:""; position:absolute; left:0; top:0; bottom:0; width:3px; background:var(--c,var(--cyan)); }
.ent .kpi .l { font-size:10.5px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--mute); display:flex; justify-content:space-between; align-items:center; }
.ent .kpi .l i { color:var(--c,var(--cyan)); font-size:15px; }
.ent .kpi .v { font:700 28px/1.1 'Space Grotesk',sans-serif; margin-top:8px; color:#fff; }
.ent .kpi .v small { font-size:13px; font-weight:500; color:var(--sub); margin-left:4px; }
.ent .kpi .d { font-size:12px; color:var(--sub); margin-top:6px; }
.ent .up { color:var(--green); } .ent .dn { color:var(--red); }

.ent .sech { display:flex; align-items:center; gap:12px; margin:30px 0 14px; }
.ent .sech h2 { font-size:12px; font-weight:800; letter-spacing:.16em; text-transform:uppercase; color:var(--sub); white-space:nowrap; }
.ent .sech span.ln { flex:1; height:1px; background:var(--line); }
.ent .sech a { font-size:12px; font-weight:600; color:var(--cyan); white-space:nowrap; }
.ent .tag { font-size:10px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; padding:3px 9px; border-radius:20px; border:1px solid var(--line); color:var(--sub); }

.ent .row { display:grid; gap:16px; margin-bottom:16px; }
.ent .r21 { grid-template-columns:2fr 1fr; } .ent .r3 { grid-template-columns:repeat(3,1fr); } .ent .r2 { grid-template-columns:1fr 1fr; } .ent .r12 { grid-template-columns:1fr 2fr; }
.ent .card { background:var(--card); border:1px solid var(--line); border-radius:14px; padding:18px 20px; min-width:0; }
.ent .card > header { display:flex; justify-content:space-between; align-items:flex-start; gap:10px; margin-bottom:14px; }
.ent .card h3 { font-size:14px; font-weight:700; }
.ent .card header small { display:block; font-size:11.5px; color:var(--mute); margin-top:2px; font-weight:400; }
.ent .cv { position:relative; height:250px; } .ent .cv.s { height:210px; } .ent .cv.t { height:290px; }

.ent table { width:100%; border-collapse:collapse; font-size:12.8px; }
.ent th { text-align:left; font-size:10px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--mute); padding:0 10px 9px 0; white-space:nowrap; }
.ent td { padding:10px 10px 10px 0; border-top:1px solid var(--line); color:var(--ink); vertical-align:middle; }
.ent td a { color:var(--cyan); font-weight:600; }
.ent .scroll { overflow-x:auto; }
.ent .pill { display:inline-block; padding:2px 10px; border-radius:20px; font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; background:rgba(255,255,255,.07); color:var(--sub); }
.ent .pill.approved, .ent .pill.completed, .ent .pill.active { background:rgba(52,211,153,.14); color:var(--green); }
.ent .pill.submitted, .ent .pill.in_progress, .ent .pill.open { background:rgba(59,130,246,.16); color:#93c5fd; }
.ent .pill.rejected, .ent .pill.cancelled { background:rgba(248,113,113,.14); color:var(--red); }
.ent .pill.maintenance, .ent .pill.planned, .ent .pill.pending { background:rgba(251,191,36,.14); color:var(--amber); }
.ent .bar { height:6px; border-radius:4px; background:rgba(255,255,255,.07); overflow:hidden; min-width:70px; }
.ent .bar i { display:block; height:100%; border-radius:4px; background:var(--bc,var(--cyan)); width:var(--w); }

.ent .list { list-style:none; margin:0; padding:0; display:grid; gap:2px; }
.ent .list li { display:flex; gap:12px; align-items:center; padding:10px 0; border-bottom:1px solid var(--line); font-size:13px; }
.ent .list li:last-child { border-bottom:0; }
.ent .list .n { font:700 18px 'Space Grotesk',sans-serif; min-width:34px; color:var(--c); }
.ent .list .tx { flex:1; color:var(--sub); } .ent .list .go { color:var(--cyan); font-size:12px; font-weight:600; }
.ent .feed li { align-items:flex-start; }
.ent .feed .dot { width:9px; height:9px; border-radius:50%; margin-top:6px; background:var(--c); flex:none; box-shadow:0 0 10px var(--c); }
.ent .feed b { display:block; font-weight:600; color:#fff; font-size:13px; } .ent .feed small { color:var(--mute); font-size:11.5px; }
.ent .ok { display:flex; gap:10px; align-items:center; padding:14px; border-radius:12px; background:rgba(52,211,153,.08); border:1px solid rgba(52,211,153,.3); color:#bbf7d0; font-size:13px; }
.ent .empty { text-align:center; color:var(--mute); padding:28px 10px; font-size:13px; }
.ent .tiles { display:grid; grid-template-columns:repeat(auto-fill,minmax(190px,1fr)); gap:12px; }
.ent .tile { display:flex; gap:12px; align-items:center; padding:14px 16px; border-radius:12px; border:1px solid var(--line); background:var(--card); transition:all .2s; }
.ent .tile:hover { border-color:var(--tc); transform:translateY(-3px); background:var(--card2); }
.ent .tile i { width:36px; height:36px; border-radius:10px; display:grid; place-items:center; background:color-mix(in srgb, var(--tc) 16%, transparent); color:var(--tc); font-size:17px; flex:none; }
.ent .tile b { display:block; font-size:13px; font-weight:600; color:#fff; } .ent .tile small { font-size:11px; color:var(--mute); }
.ent .legend { display:flex; gap:14px; flex-wrap:wrap; font-size:11.5px; color:var(--sub); margin-top:10px; }
.ent .legend b { display:inline-block; width:9px; height:9px; border-radius:2px; margin-right:6px; }

@media (max-width:1200px) { .ent .kpis { grid-template-columns:repeat(2,1fr); } .ent .r21, .ent .r3, .ent .r2, .ent .r12 { grid-template-columns:1fr; } }
@media (max-width:560px) { .ent { padding:14px; } .ent .kpis { grid-template-columns:1fr; } }
.ent .vsw { display:flex; gap:6px; flex-wrap:wrap; align-items:center; margin-top:12px; font-size:11px; }
.ent .vsw span { color:var(--mute); font-weight:700; letter-spacing:.1em; text-transform:uppercase; margin-right:4px; }
.ent .vsw a { padding:5px 13px; border-radius:20px; border:1px solid var(--line); color:var(--sub); font-weight:600; transition:all .2s; }
.ent .vsw a:hover { border-color:var(--cyan); color:#fff; }
.ent .vsw a.on { background:linear-gradient(135deg,rgba(59,130,246,.35),rgba(34,211,238,.25)); border-color:var(--cyan); color:#fff; }
.ent .hm { display:grid; grid-template-columns:repeat(12,1fr); gap:5px; }
.ent .hm > div { display:grid; gap:5px; }
.ent .hm i { display:block; aspect-ratio:1; border-radius:4px; background:var(--cyan); }
.ent .card .list .n small { font-weight:500; }
#ent .card { background:var(--card) !important; border:1px solid var(--line) !important; box-shadow:none !important; color:var(--ink) !important; }
#ent .card h3 { color:#fff; }
#ent .card.ill { background:linear-gradient(180deg,rgba(255,255,255,.05),rgba(255,255,255,.015)); }
</style>
@endsection

@section('content')
@php
    $user = auth()->user();
    $hour = (int) now()->format('G');
    $greet = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
    $first = \Illuminate\Support\Str::of($user->name)->explode(' ')->first();
    $nf = fn ($v, $d = 0) => $v === null ? '—' : number_format((float) $v, $d);
    $wow = $mpfmWeek - $mpfmPrevWeek;
    $covPct = $counts['strings'] > 0 ? round($counts['testedStrings'] / $counts['strings'] * 100) : 0;
    $wl = $wireline;
    $maxTop = max(1, (float) $topStrings->max('oil'));
    $maxVessel = max(1, (int) $vessels->max('tests_count'));
    $maxOp = max(1, (int) $operators->max('total'));
    $feedColors = ['mpfm' => '#22d3ee', 'tv' => '#fbbf24', 'req' => '#a78bfa', 'log' => '#34d399'];
    $wlTypes = \App\Models\WirelineOperation::TYPES;
    $pair0 = $pairs->first();
    $flow = $pair0
        ? ['mpfm' => (float) $pair0['mpfm']->oil_rate, 'tv' => (float) $pair0['tv']->oil_rate, 'dev' => $pair0['oil']]
        : ['mpfm' => $latest ? (float) $latest->oil_rate : null, 'tv' => null, 'dev' => null];
    // sections of each role's dashboard, in order
    $layout = [
        'admin'      => ['_illustrations', '_production', '_review', '_activity', '_integrity', '_locations', '_wireline', '_wells', '_vessels', '_team', '_launcher'],
        'manager'    => ['_manager', '_illustrations', '_review', '_integrity', '_locations', '_wireline', '_team', '_launcher'],
        'supervisor' => ['_review', '_watch', '_illustrations', '_activity', '_integrity', '_team', '_launcher'],
        'production' => ['_illustrations', '_production', '_bylocation', '_watch', '_wells', '_integrity', '_locations', '_vessels', '_launcher'],
        'reservoir'  => ['_reservoir', '_wireline', '_watch', '_integrity', '_wells', '_launcher'],
        'operator'   => ['_my_work', '_illustrations', '_launcher'],
    ][$persona];
@endphp

<div class="ent" id="ent" data-persona="{{ $persona }}">
    @include('dashboard._header')
    @include('dashboard._kpis')
    @foreach($layout as $__part)
        @include('dashboard.' . $__part)
    @endforeach
</div>
@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    if (!window.Chart) return;
    Chart.defaults.color = '#9aaccb';
    Chart.defaults.font.family = 'Inter, sans-serif';
    Chart.defaults.borderColor = 'rgba(120,150,210,.12)';
    Chart.defaults.plugins.legend.labels.boxWidth = 10;
    Chart.defaults.plugins.legend.labels.usePointStyle = true;
    var base = { responsive: true, maintainAspectRatio: false };
    var $ = function (id) { return document.getElementById(id); };
    var make = function (id, cfg) { var el = $(id); if (el) new Chart(el, cfg); };
    var grid = { color: 'rgba(120,150,210,.1)' };

    var T = @json($trend);
    make('cTrend', { type: 'line', data: { labels: T.map(function (t) { return t.date.slice(5) + ' ' + t.name; }), datasets: [
        { label: 'Oil (B/D)', data: T.map(function (t) { return t.oil; }), borderColor: '#22d3ee', backgroundColor: 'rgba(34,211,238,.12)', fill: true, tension: .3, pointRadius: 2, yAxisID: 'y' },
        { label: 'Water cut (%)', data: T.map(function (t) { return t.wc; }), borderColor: '#38bdf8', borderDash: [5, 4], tension: .3, pointRadius: 0, yAxisID: 'y1' },
        { label: 'Gas (MMscf/D)', data: T.map(function (t) { return t.gas; }), borderColor: '#fbbf24', tension: .3, pointRadius: 0, yAxisID: 'y1' }
    ] }, options: Object.assign({}, base, { interaction: { mode: 'index', intersect: false }, scales: { x: { ticks: { maxTicksLimit: 8, maxRotation: 0 }, grid: { display: false } }, y: { grid: grid }, y1: { position: 'right', grid: { drawOnChartArea: false } } } }) });

    var A = @json($activity);
    make('cActivity', { type: 'bar', data: { labels: A.labels, datasets: [
        { label: 'MPFM', data: A.mpfm, backgroundColor: '#22d3ee', borderRadius: 3 },
        { label: 'Well testing', data: A.tv, backgroundColor: '#fbbf24', borderRadius: 3 }
    ] }, options: Object.assign({}, base, { scales: { x: { stacked: true, ticks: { maxTicksLimit: 6 }, grid: { display: false } }, y: { stacked: true, grid: grid, ticks: { precision: 0 } } } }) });

    var S = @json($mpfmByStatus), sc = { draft: '#64748b', submitted: '#fbbf24', approved: '#34d399', rejected: '#f87171' };
    make('cStatus', { type: 'doughnut', data: { labels: Object.keys(S), datasets: [{ data: Object.values(S), backgroundColor: Object.keys(S).map(function (k) { return sc[k] || '#3b82f6'; }), borderWidth: 0 }] },
        options: Object.assign({}, base, { cutout: '66%', plugins: { legend: { position: 'bottom' } } }) });

    var P = @json($scatter), mx = Math.max.apply(null, P.map(function (p) { return Math.max(p.tv, p.mpfm); }).concat([1])) * 1.1;
    make('cScatter', { type: 'scatter', data: { datasets: [
        { label: 'Tests', data: P.map(function (p) { return { x: p.tv, y: p.mpfm, n: p.name }; }), backgroundColor: '#22d3ee', pointRadius: 5 },
        { label: 'Perfect agreement', type: 'line', data: [{ x: 0, y: 0 }, { x: mx, y: mx }], borderColor: '#34d399', borderDash: [6, 4], pointRadius: 0 },
        { label: '+10%', type: 'line', data: [{ x: 0, y: 0 }, { x: mx, y: mx * 1.1 }], borderColor: 'rgba(248,113,113,.5)', borderWidth: 1, pointRadius: 0 },
        { label: '-10%', type: 'line', data: [{ x: 0, y: 0 }, { x: mx, y: mx * .9 }], borderColor: 'rgba(248,113,113,.5)', borderWidth: 1, pointRadius: 0 }
    ] }, options: Object.assign({}, base, { plugins: { legend: { display: false }, tooltip: { callbacks: { label: function (c) { return (c.raw.n || '') + ' · TV ' + c.raw.x + ' / MPFM ' + c.raw.y; } } } },
        scales: { x: { title: { display: true, text: 'Well testing (B/D)' }, grid: grid, min: 0, max: mx }, y: { title: { display: true, text: 'MPFM (B/D)' }, grid: grid, min: 0, max: mx } } }) });

    @if($wl)
    make('cWlMonth', { type: 'bar', data: { labels: @json($wl['monthLabels']), datasets: [{ label: 'Operations', data: @json($wl['perMonth']), backgroundColor: '#a78bfa', borderRadius: 4 }] },
        options: Object.assign({}, base, { plugins: { legend: { display: false } }, scales: { x: { grid: { display: false } }, y: { beginAtZero: true, grid: grid, ticks: { precision: 0 } } } }) });
    var WM = @json(collect($wlTypes)->map(fn ($t) => ['l' => $t[0], 'c' => $t[2]])), WT = @json($wl['byType']), wk = Object.keys(WT);
    make('cWlType', { type: 'doughnut', data: { labels: wk.map(function (k) { return WM[k] ? WM[k].l : k; }), datasets: [{ data: wk.map(function (k) { return WT[k]; }), backgroundColor: wk.map(function (k) { return WM[k] ? WM[k].c : '#94a3b8'; }), borderWidth: 0 }] },
        options: Object.assign({}, base, { cutout: '62%', plugins: { legend: { position: 'bottom', labels: { font: { size: 10 } } } } }) });
    @endif

    var F = @json($byField);
    make('cField', { type: 'bar', data: { labels: F.map(function (f) { return f.name; }), datasets: [{ label: 'Oil (B/D)', data: F.map(function (f) { return f.oil; }), backgroundColor: ['#22d3ee', '#fbbf24', '#a78bfa', '#34d399', '#f87171'], borderRadius: 4 }] },
        options: Object.assign({}, base, { indexAxis: 'y', plugins: { legend: { display: false } }, scales: { x: { grid: grid }, y: { grid: { display: false } } } }) });

    make('cCover', { type: 'doughnut', data: { labels: ['Tested (30 d)', 'Not tested'], datasets: [{ data: [{{ $counts['testedStrings'] }}, {{ max(0, $counts['strings'] - $counts['testedStrings']) }}], backgroundColor: ['#34d399', '#26324f'], borderWidth: 0 }] },
        options: Object.assign({}, base, { cutout: '70%', plugins: { legend: { position: 'bottom' } } }) });

    var R = @json($requestsByStatus), rc = { open: '#22d3ee', pending: '#fbbf24', in_progress: '#3b82f6', completed: '#34d399', cancelled: '#f87171' };
    make('cReq', { type: 'doughnut', data: { labels: Object.keys(R).map(function (k) { return k.replace('_', ' '); }), datasets: [{ data: Object.values(R), backgroundColor: Object.keys(R).map(function (k) { return rc[k] || '#a78bfa'; }), borderWidth: 0 }] },
        options: Object.assign({}, base, { cutout: '66%', plugins: { legend: { position: 'bottom' } } }) });

    var SH = @json($shift);
    make('cShift', { type: 'bar', data: { labels: ['Morning', 'Afternoon', 'Night'], datasets: [{ data: [SH.morning, SH.afternoon, SH.night], backgroundColor: ['#fbbf24', '#22d3ee', '#6366f1'], borderRadius: 6 }] },
        options: Object.assign({}, base, { plugins: { legend: { display: false } }, scales: { x: { grid: { display: false } }, y: { beginAtZero: true, grid: grid, ticks: { precision: 0 } } } }) });
    var MD = @json($mineDaily ?? null);
    if (MD) { make('cMyDaily', { type: 'bar', data: { labels: MD.labels, datasets: [{ label: 'My tests', data: MD.data, backgroundColor: '#22d3ee', borderRadius: 4 }] },
        options: Object.assign({}, base, { plugins: { legend: { display: false } }, scales: { x: { grid: { display: false }, ticks: { maxTicksLimit: 7 } }, y: { beginAtZero: true, grid: grid, ticks: { precision: 0 } } } }) }); }
    var MS = @json($mine['byStatus'] ?? null);
    if (MS) { make('cMyStatus', { type: 'doughnut', data: { labels: Object.keys(MS), datasets: [{ data: Object.values(MS), backgroundColor: Object.keys(MS).map(function (k) { return sc[k] || '#3b82f6'; }), borderWidth: 0 }] },
        options: Object.assign({}, base, { cutout: '66%', plugins: { legend: { position: 'bottom' } } }) }); }

    var BH = @json($reservoir['trend'] ?? null);
    if (BH && BH.labels.length > 1) { make('cBhp', { type: 'line', data: { labels: BH.labels, datasets: [{ label: 'Average static BHP (psi)', data: BH.bhp, borderColor: '#a78bfa', backgroundColor: 'rgba(167,139,250,.16)', fill: true, tension: .3, pointRadius: 3 }] },
        options: Object.assign({}, base, { plugins: { legend: { display: false } }, scales: { x: { grid: { display: false } }, y: { grid: grid, title: { display: true, text: 'psi' } } } }) }); }

    var LO = @json($byLocation ?? []);
    make('cLocOil', { type: 'bar', data: { labels: LO.map(function (l) { return l.name; }), datasets: [{ label: 'Oil (B/D)', data: LO.map(function (l) { return l.oil; }), backgroundColor: ['#38bdf8', '#fbbf24', '#a78bfa', '#34d399', '#fb923c', '#22d3ee'], borderRadius: 4 }] },
        options: Object.assign({}, base, { indexAxis: 'y', plugins: { legend: { display: false } }, scales: { x: { grid: grid }, y: { grid: { display: false } } } }) });

    var MO = @json($monthly);
    make('cMonthly', { type: 'bar', data: { labels: MO.map(function (m) { return m.label; }), datasets: [
        { label: 'MPFM', data: MO.map(function (m) { return m.mpfm; }), backgroundColor: '#22d3ee', borderRadius: 3 },
        { label: 'Well testing', data: MO.map(function (m) { return m.tv; }), backgroundColor: '#fbbf24', borderRadius: 3 }
    ] }, options: Object.assign({}, base, { scales: { x: { stacked: true, grid: { display: false } }, y: { stacked: true, grid: grid, ticks: { precision: 0 } } } }) });

    @if($integrity)
    var IS = @json($integrity['stats']);
    make('cIntStatus', { type: 'doughnut', data: { labels: ['Green', 'Amber', 'Red', 'Not assessed'], datasets: [{ data: [IS.green, IS.amber, IS.red, IS.none], backgroundColor: ['#34d399', '#fbbf24', '#f87171', '#64748b'], borderWidth: 0 }] },
        options: Object.assign({}, base, { cutout: '64%', plugins: { legend: { position: 'bottom' } } }) });
    @endif
})();
</script>
@endsection
