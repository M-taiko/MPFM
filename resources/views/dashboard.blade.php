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
@endphp

<div class="ent" id="ent">

    {{-- ═════ HEADER ═════ --}}
    <div class="top hero">
        <div class="who">
            <span class="lg" role="img" aria-label="Company logo"></span>
        <div>
            <div class="eyebrow"><i></i> Operations command center</div>
            <h1>{{ $greet }}, {{ $first }}</h1>
            <div class="meta">
                <span>{{ now()->format('l, d F Y') }}</span>
                <span class="tag">{{ str_replace('_', ' ', $user->roles->pluck('name')->first() ?? 'User') }}</span>
                <span>{{ $counts['activeStrings'] }} active wells · {{ $counts['fields'] }} field(s) · {{ $counts['towers'] }} tower(s)</span>
            </div>
        </div>
        </div>
        <div style="display:flex;align-items:flex-end;gap:14px;flex-wrap:wrap">
        <div class="acts">
            <a href="{{ route('upload.form') }}" class="btn p"><i class="fe fe-upload"></i> Upload MPFM log</a>
            <a href="{{ route('tv_test_requests.create') }}" class="btn"><i class="fe fe-clipboard"></i> Test request</a>
            @if($wl)<a href="{{ route('wireline.create') }}" class="btn"><i class="fe fe-git-branch"></i> Wire Line operation</a>@endif
            <a href="{{ route('tv_tests.compare') }}" class="btn"><i class="fe fe-repeat"></i> Compare vs MPFM</a>
        </div>
        @include('partials.rig')
        </div>
    </div>

    {{-- ═════ EXECUTIVE KPIs ═════ --}}
    <div class="kpis">
        <div class="kpi" style="--c:#22d3ee"><div class="l">Oil production <i class="fe fe-droplet"></i></div>
            <div class="v">{{ $nf($snap['oil'], 0) }}<small>B/D</small></div><div class="d">latest test of {{ $snap['strings'] }} well(s)</div></div>
        <div class="kpi" style="--c:#fbbf24"><div class="l">Gas production <i class="fe fe-wind"></i></div>
            <div class="v">{{ $nf($snap['gas'], 2) }}<small>MMscf/D</small></div><div class="d">field GOR {{ $nf($snap['gor']) }}</div></div>
        <div class="kpi" style="--c:#38bdf8"><div class="l">Water cut <i class="fe fe-percent"></i></div>
            <div class="v">{{ $nf($snap['wc'], 1) }}<small>%</small></div><div class="d">{{ $nf($snap['water'], 0) }} B/D water</div></div>
        <div class="kpi" style="--c:#34d399"><div class="l">Well test coverage <i class="fe fe-target"></i></div>
            <div class="v">{{ $covPct }}<small>%</small></div><div class="d">{{ $counts['testedStrings'] }} of {{ $counts['strings'] }} wells tested in 30 days</div></div>

        <div class="kpi" style="--c:#3b82f6"><div class="l">MPFM tests <i class="fe fe-bar-chart-2"></i></div>
            <div class="v">{{ $mpfmTotal }}</div>
            <div class="d">{{ $mpfmWeek }} this week <span class="{{ $wow >= 0 ? 'up' : 'dn' }}">{{ $wow >= 0 ? '▲' : '▼' }} {{ abs($wow) }}</span> · {{ $approvedRate }}% approved</div></div>
        <div class="kpi" style="--c:#fbbf24"><div class="l">Test vessel tests <i class="fe fe-anchor"></i></div>
            <div class="v">{{ $tvTotal }}</div><div class="d">{{ $tvWeek }} this week · {{ $openRequests }} open request(s)</div></div>
        @if($wl)
        <div class="kpi" style="--c:#a78bfa"><div class="l">Wire Line operations <i class="fe fe-git-branch"></i></div>
            <div class="v">{{ $wl['stats']['total'] }}</div><div class="d">{{ $wl['stats']['month'] }} this month · {{ $wl['stats']['open'] }} open</div></div>
        <div class="kpi" style="--c:{{ $wl['overdueCount'] ? '#f87171' : '#34d399' }}"><div class="l">Pressure surveys <i class="fe fe-activity"></i></div>
            <div class="v">{{ $wl['stats']['runs'] }}</div><div class="d">{{ $wl['overdueCount'] }} well(s) overdue (180 d)</div></div>
        @else
        <div class="kpi" style="--c:#34d399"><div class="l">Approval rate <i class="fe fe-check-circle"></i></div>
            <div class="v">{{ $approvedRate }}<small>%</small></div><div class="d">of all MPFM tests</div></div>
        <div class="kpi" style="--c:#f87171"><div class="l">Meter agreement <i class="fe fe-shuffle"></i></div>
            <div class="v">{{ $avgOilDeviation !== null ? '±' . $avgOilDeviation : '—' }}<small>%</small></div><div class="d">{{ $pairCount }} paired test(s)</div></div>
        @endif
    </div>

    {{-- ═════ FIELD & PROCESS ILLUSTRATIONS ═════ --}}
    <div class="sech"><h2>Field &amp; process overview</h2><span class="ln"></span></div>
    <div class="row r21">
        <div class="card pano ill" style="position:relative">
            <div class="ct"><span>Field overview · live cross-section</span></div>
            @include('partials.dash_platform')
            @include('partials.dash_strata')
            <span class="cap">Figures = latest test of every oil string</span>
        </div>
        <div class="card tankc ill">
            <div class="ct"><span>Fluid split</span><a href="{{ route('mpfm_tests.index') }}">Tests →</a></div>
            @include('partials.dash_tank', ['fill' => 78])
            <div class="sx">
                <div style="--c:#fbbf24"><small>Oil</small><b>{{ number_format($snap['oil'], 0) }}<i>B/D</i></b></div>
                <div style="--c:#fb923c"><small>Gas</small><b>{{ number_format($snap['gas'], 1) }}<i>MMscf/d</i></b></div>
                <div style="--c:#38bdf8"><small>Water</small><b>{{ number_format($snap['water'], 0) }}<i>B/D</i></b></div>
                <div style="--c:#a78bfa"><small>GOR</small><b>{{ $snap['gor'] !== null ? number_format($snap['gor']) : '—' }}<i>scf/stb</i></b></div>
            </div>
        </div>
    </div>
    <div class="card ill" style="margin-bottom:16px">
        <div class="ct"><span>Process flow · wells → MPFM → test separator → storage</span><a href="{{ route('tv_tests.compare') }}">Compare tests →</a></div>
        @include('partials.dash_flow')
    </div>

    {{-- ═════ PRODUCTION ═════ --}}
    <div class="sech"><h2>Production &amp; priorities</h2><span class="ln"></span><a href="{{ route('mpfm_tests.trending_dashboard') }}">Analytics →</a></div>
    <div class="row r21">
        <div class="card"><header><div><h3>Production trend</h3><small>Latest MPFM tests · oil &amp; gas rate with water cut</small></div></header>
            @if(count($trend)) <div class="cv t"><canvas id="cTrend"></canvas></div> @else <div class="empty">No MPFM data yet.</div> @endif</div>
        <div class="card"><header><div><h3>Needs attention</h3><small>Across tests, requests, vessels @if($wl)and surveys @endif</small></div></header>
            @php
                $items = $attention->map(fn ($a) => ['n' => $a['n'], 'c' => $a['color'], 'tx' => $a['label'], 'url' => $a['url']]);
                if ($wl) {
                    if ($wl['overdueCount']) $items->push(['n' => $wl['overdueCount'], 'c' => '#f87171', 'tx' => 'active wells without a pressure survey in 180 days', 'url' => route('wireline.dashboard')]);
                    if ($wl['stats']['open']) $items->push(['n' => $wl['stats']['open'], 'c' => '#a78bfa', 'tx' => 'open Wire Line operations', 'url' => route('wireline.operations', ['status' => 'planned'])]);
                }
            @endphp
            @if($items->isEmpty())
                <div class="ok"><i class="fe fe-check-circle"></i> Nothing needs attention right now.</div>
            @else
                <ul class="list">@foreach($items as $a)<li><span class="n" style="--c:{{ $a['c'] }}">{{ $a['n'] }}</span><span class="tx">{{ $a['tx'] }}</span><a class="go" href="{{ $a['url'] }}">Open →</a></li>@endforeach</ul>
            @endif
        </div>
    </div>

    <div class="row r3">
        <div class="card"><header><div><h3>Test activity</h3><small>Tests per day · last 30 days</small></div></header><div class="cv s"><canvas id="cActivity"></canvas></div></div>
        <div class="card"><header><div><h3>MPFM review pipeline</h3><small>{{ $mpfmTotal }} tests by status</small></div></header>
            @if($mpfmTotal) <div class="cv s"><canvas id="cStatus"></canvas></div> @else <div class="empty">No tests yet.</div> @endif</div>
        <div class="card"><header><div><h3>MPFM vs test vessel</h3><small>Oil rate agreement{{ $avgOilDeviation !== null ? ' · mean ±' . $avgOilDeviation . '%' : '' }}</small></div></header>
            @if(count($scatter)) <div class="cv s"><canvas id="cScatter"></canvas></div> @else <div class="empty">No paired tests yet.</div> @endif</div>
    </div>

    {{-- ═════ WIRE LINE ═════ --}}
    @if($wl)
    <div class="sech"><h2>Wire Line</h2><span class="ln"></span><a href="{{ route('wireline.dashboard') }}">Open Wire Line →</a></div>
    <div class="card ill" style="margin-bottom:16px">
        <div class="ct"><span>Wire Line operation · memory-gauge survey</span><a href="{{ route('wireline.create') }}">New operation →</a></div>
        @include('partials.wireline_rig')
    </div>
    <div class="row r3">
        <div class="card"><header><div><h3>Operations per month</h3><small>Last 12 months</small></div></header><div class="cv s"><canvas id="cWlMonth"></canvas></div></div>
        <div class="card"><header><div><h3>Operations by type</h3><small>{{ $wl['stats']['total'] }} total on {{ $wl['stats']['wells'] }} well(s)</small></div></header>
            @if($wl['byType']->isEmpty()) <div class="empty">No operations recorded.</div> @else <div class="cv s"><canvas id="cWlType"></canvas></div> @endif</div>
        <div class="card"><header><div><h3>Surveys overdue</h3><small>Active wells, no survey in 180 days</small></div><span class="tag">{{ $wl['overdueCount'] }}</span></header>
            @if($wl['overdue']->isEmpty()) <div class="ok"><i class="fe fe-check-circle"></i> All active wells are up to date.</div>
            @else <ul class="list">@foreach($wl['overdue']->take(6) as $o)<li><span class="tx"><a href="{{ route('mpfm_tests.string_analysis', $o['well']->id) }}" style="color:#fff;font-weight:600">{{ $o['well']->name }}</a><br><small style="color:var(--mute)">{{ $o['last'] ? $o['days'] . ' days since last survey' : 'never surveyed' }}</small></span><a class="go" href="{{ route('wireline.create', ['well' => $o['well']->id]) }}">Schedule →</a></li>@endforeach</ul> @endif</div>
    </div>
    <div class="row r2">
        <div class="card"><header><div><h3>Latest memory-gauge surveys</h3><small>Final bottom-hole pressure &amp; temperature</small></div></header>
            @if($wl['runs']->isEmpty()) <div class="empty">No gauge data uploaded yet.</div>
            @else <div class="scroll"><table><thead><tr><th>Well</th><th>Date</th><th>Type</th><th>BHP (psi)</th><th>BHT (°C)</th><th></th></tr></thead><tbody>
                @foreach($wl['runs']->take(6) as $r)
                <tr><td>{{ $r->operation->oilString->name ?? '—' }}</td><td>{{ ($r->started_at ?? $r->operation->operation_date)->format('d M Y') }}</td><td>{{ strtoupper($r->survey_type) }}</td>
                    <td>{{ $nf($r->bhp, 1) }}</td><td>{{ $nf($r->bht, 1) }}</td><td><a href="{{ route('wireline.show', [$r->operation, 'run' => $r->id]) }}">Open</a></td></tr>
                @endforeach</tbody></table></div> @endif</div>
        <div class="card"><header><div><h3>Recent Wire Line operations</h3><small>Latest interventions across the field</small></div></header>
            @if($wl['recent']->isEmpty()) <div class="empty">No operations yet.</div>
            @else <div class="scroll"><table><thead><tr><th>Date</th><th>Well</th><th>Operation</th><th>Status</th><th></th></tr></thead><tbody>
                @foreach($wl['recent']->take(6) as $o)
                <tr><td>{{ $o->operation_date->format('d M') }}</td><td>{{ $o->oilString->name ?? '—' }}</td><td>{{ $o->typeLabel() }}</td><td><span class="pill {{ $o->status }}">{{ $o->statusLabel() }}</span></td><td><a href="{{ route('wireline.show', $o) }}">Open</a></td></tr>
                @endforeach</tbody></table></div> @endif</div>
    </div>
    @endif

    {{-- ═════ WELLS & FIELDS ═════ --}}
    <div class="sech"><h2>Wells &amp; fields</h2><span class="ln"></span><a href="{{ route('oil_fields.index') }}">Field setup →</a></div>
    <div class="row r3">
        <div class="card"><header><div><h3>Oil by field</h3><small>B/D · latest test per well</small></div></header>
            @if($byField->isEmpty()) <div class="empty">No data.</div> @else <div class="cv s"><canvas id="cField"></canvas></div> @endif</div>
        <div class="card"><header><div><h3>Top producing wells</h3><small>Latest oil rate</small></div></header>
            @if($topStrings->isEmpty()) <div class="empty">No data.</div>
            @else <div class="scroll"><table><thead><tr><th>Well</th><th>Oil (B/D)</th><th>WC %</th><th></th></tr></thead><tbody>
                @foreach($topStrings as $s)<tr><td>{{ $s['name'] }}</td><td>{{ $nf($s['oil'], 1) }}</td><td>{{ $nf($s['wc'], 1) }}</td><td style="width:80px"><div class="bar"><i style="--w:{{ $s['oil'] / $maxTop * 100 }}%"></i></div></td></tr>@endforeach
            </tbody></table></div> @endif</div>
        <div class="card"><header><div><h3>Test coverage</h3><small>Wells tested in the last 30 days</small></div></header>
            <div class="cv s"><canvas id="cCover"></canvas></div></div>
    </div>

    {{-- ═════ TEST VESSELS ═════ --}}
    <div class="sech"><h2>Test vessels</h2><span class="ln"></span><a href="{{ route('test_vessels.index') }}">All vessels →</a></div>
    <div class="row r21">
        <div class="card"><header><div><h3>Vessel utilisation</h3><small>Tests performed per vessel</small></div></header>
            @if($vessels->isEmpty()) <div class="empty">No vessels registered.</div>
            @else <div class="scroll"><table><thead><tr><th>Vessel</th><th>Status</th><th>Tests</th><th>Last test</th><th></th></tr></thead><tbody>
                @foreach($vessels as $v)<tr><td><a href="{{ route('test_vessels.show', $v) }}">{{ $v->name }}</a></td><td><span class="pill {{ $v->status }}">{{ $v->status }}</span></td><td>{{ $v->tests_count }}</td>
                    <td>{{ $v->last_test_at ? \Carbon\Carbon::parse($v->last_test_at)->format('d M Y') : '—' }}</td><td style="width:110px"><div class="bar"><i style="--w:{{ $v->tests_count / $maxVessel * 100 }}%;--bc:#fbbf24"></i></div></td></tr>@endforeach
            </tbody></table></div> @endif</div>
        <div class="card"><header><div><h3>Test requests</h3><small>{{ $openRequests }} open</small></div></header>
            @if(empty($requestsByStatus)) <div class="empty">No requests yet.</div> @else <div class="cv s"><canvas id="cReq"></canvas></div> @endif</div>
    </div>

    {{-- ═════ TEAM & ACTIVITY ═════ --}}
    <div class="sech"><h2>Team &amp; activity</h2><span class="ln"></span></div>
    <div class="row r3">
        <div class="card"><header><div><h3>Recent activity</h3><small>Tests, requests and log uploads</small></div></header>
            @if($feed->isEmpty()) <div class="empty">No activity yet.</div>
            @else <ul class="list feed">@foreach($feed->take(7) as $e)<li style="--c:{{ $feedColors[$e['type']] ?? '#64748b' }}"><span class="dot"></span><span><b><a href="{{ $e['url'] }}">{{ $e['title'] }}</a></b><small>{{ $e['sub'] }}{{ $e['who'] ? ' · ' . $e['who'] : '' }} · {{ $e['when']->diffForHumans() }}</small></span></li>@endforeach</ul> @endif</div>
        <div class="card"><header><div><h3>Tests by shift</h3><small>MPFM and vessel tests</small></div></header><div class="cv s"><canvas id="cShift"></canvas></div></div>
        <div class="card"><header><div><h3>Most active operators</h3><small>Tests recorded</small></div></header>
            @if($operators->isEmpty()) <div class="empty">No data.</div>
            @else <ul class="list">@foreach($operators as $o)<li><span class="tx" style="color:#fff;font-weight:600">{{ $o['name'] }}<div class="bar" style="margin-top:6px"><i style="--w:{{ $o['total'] / $maxOp * 100 }}%;--bc:#3b82f6"></i></div></span><span class="n" style="--c:#fff;font-size:15px">{{ $o['total'] }}</span></li>@endforeach</ul> @endif</div>
    </div>

    {{-- ═════ LAUNCHER ═════ --}}
    <div class="sech"><h2>Modules</h2><span class="ln"></span></div>
    <div class="tiles">
        <a class="tile" style="--tc:#22d3ee" href="{{ route('mpfm_tests.index') }}"><i class="fe fe-activity"></i><span><b>MPFM tests</b><small>Review &amp; approve</small></span></a>
        <a class="tile" style="--tc:#34d399" href="{{ route('well_tests.index') }}"><i class="fe fe-folder"></i><span><b>Test archive</b><small>Uploaded logs</small></span></a>
        <a class="tile" style="--tc:#3b82f6" href="{{ route('mpfm_tests.trending_dashboard') }}"><i class="fe fe-trending-up"></i><span><b>Analytics</b><small>Trends &amp; alerts</small></span></a>
        <a class="tile" style="--tc:#fbbf24" href="{{ route('test_vessels.index') }}"><i class="fe fe-anchor"></i><span><b>Test vessels</b><small>Fleet &amp; status</small></span></a>
        <a class="tile" style="--tc:#f59e0b" href="{{ route('tv_tests.index') }}"><i class="fe fe-list"></i><span><b>Vessel tests</b><small>Results</small></span></a>
        <a class="tile" style="--tc:#a78bfa" href="{{ route('tv_test_requests.index') }}"><i class="fe fe-clipboard"></i><span><b>Test requests</b><small>Plan &amp; track</small></span></a>
        @if($wl)
        <a class="tile" style="--tc:#a78bfa" href="{{ route('wireline.dashboard') }}"><i class="fe fe-git-branch"></i><span><b>Wire Line</b><small>Surveys &amp; interventions</small></span></a>
        <a class="tile" style="--tc:#22d3ee" href="{{ route('wireline.operations') }}"><i class="fe fe-list"></i><span><b>Wire Line jobs</b><small>All operations</small></span></a>
        @endif
        <a class="tile" style="--tc:#38bdf8" href="{{ route('oil_fields.index') }}"><i class="fe fe-map"></i><span><b>Field setup</b><small>Fields, towers, wells</small></span></a>
    </div>
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
        { label: 'Test vessel', data: A.tv, backgroundColor: '#fbbf24', borderRadius: 3 }
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
        scales: { x: { title: { display: true, text: 'Test vessel (B/D)' }, grid: grid, min: 0, max: mx }, y: { title: { display: true, text: 'MPFM (B/D)' }, grid: grid, min: 0, max: mx } } }) });

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
})();
</script>
@endsection
