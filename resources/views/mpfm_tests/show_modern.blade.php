@extends('layouts.master')

@section('title', 'MPFM Test Details - ' . ($wellTest->well ?? $mpfmTest->oilString->name ?? 'N/A'))

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/ofpage.css') }}">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
<style>
.ofp .tvx-dv { font-family:'Space Grotesk',sans-serif; font-weight:700; fill:#fff; }
.ofp .tvx-du { fill:#7dd3fc; font-weight:600; letter-spacing:.06em; }
.ofp .tvx-needle { animation:msNeedle 2s var(--ease) both; }
.ofp .tvx-jit { animation:msJit 2.6s ease-in-out 2s infinite; }
@keyframes msNeedle { from { transform:rotate(-120deg); } to { transform:rotate(var(--a)); } }
@keyframes msJit { 25% { transform:rotate(.9deg); } 50% { transform:rotate(-.7deg); } 75% { transform:rotate(.5deg); } }
.ofp .tg { font-size:9.5px; font-weight:700; letter-spacing:.1em; fill:#94a3b8; }
.ofp .mflow { fill:none; stroke-width:2; stroke-linecap:round; stroke-dasharray:3 8; animation:msDash 1.1s linear infinite; }
@keyframes msDash { to { stroke-dashoffset:-22; } }
.ofp .mbeam { fill:none; stroke:#fbbf24; stroke-width:1.8; stroke-dasharray:3 6; animation:msBeam .8s linear infinite; }
@keyframes msBeam { to { stroke-dashoffset:-18; } }
.ofp .mspin { animation:msSpin 6s linear infinite; transform-origin:0 0; } @keyframes msSpin { to { transform:rotate(360deg); } }
.ofp .mpulse { animation:msPulse 2.2s ease-in-out infinite; } @keyframes msPulse { 50% { opacity:.35; } }
.ofp .cv { font:700 17px 'Space Grotesk',sans-serif; fill:#fff; }
.ofp .cl { font-size:9px; font-weight:700; letter-spacing:.12em; }
.ofp .cu2 { font-size:8.5px; fill:#9fb0cf; }
.ofp .donut { width:170px; height:170px; flex:none; } .ofp .donut .arc { stroke-width:14; }
.ofp .dw { display:flex; align-items:center; gap:22px; flex-wrap:wrap; justify-content:center; margin-bottom:22px; }
.ofp .props { display:grid; grid-template-columns:repeat(auto-fit,minmax(130px,1fr)); gap:10px; margin-top:6px; }
.ofp .props div { padding:10px 12px; border-radius:13px; background:var(--glass); border:1px solid var(--line); }
.ofp .props small { display:block; font-size:9.5px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--mute); }
.ofp .props b { font:700 15px 'Space Grotesk'; color:#fff; }
.ofp .dtw { max-height:440px; overflow:auto; border-radius:14px; border:1px solid var(--line); scrollbar-width:thin; scrollbar-color:rgba(96,165,250,.35) transparent; }
.ofp .dtw table { border-collapse:collapse; width:100%; font-size:12px; }
.ofp .dtw th { position:sticky; top:0; background:#0b1426; color:var(--cyan); padding:9px 12px; text-align:left; font-size:10.5px; letter-spacing:.06em; white-space:nowrap; border-bottom:1px solid var(--line); z-index:1; }
.ofp .dtw td { padding:7px 12px; white-space:nowrap; border-bottom:1px solid rgba(255,255,255,.05); color:var(--ink); }
.ofp .dtw tr:hover td { background:rgba(34,211,238,.07); }
@media print { .ofp .actions, .ofp .acts, .ofp .srch { display:none !important; } .ofp .rv { opacity:1 !important; transform:none !important; } }
</style>
@endsection

@section('content')
@php
    $f = fn ($v, $d = 2) => is_numeric($v) ? number_format((float) $v, $d) : '—';
    $m = $metrics ?? [];
    $kp = $kpis ?? [];
    $pick = function (...$vals) { foreach ($vals as $v) { if (is_numeric($v)) return (float) $v; } return null; };
    $oil = $pick($m['oil_rate'] ?? null, $kp['oil_rate'] ?? null, $mpfmTest->oil_rate);
    $gas = $pick($m['gas_rate'] ?? null, $kp['gas_rate'] ?? null, $mpfmTest->gas_rate);
    $wrate = $pick($m['water_rate'] ?? null);
    $bsw = $pick($m['bsw'] ?? null, $mpfmTest->water_cut);
    $gor = $pick($m['gor'] ?? null, $kp['gor'] ?? null, $mpfmTest->gor);
    $gvf = $pick($m['gvf'] ?? null, $mpfmTest->gvf);
    $wlr = $pick($m['wlr'] ?? null, $mpfmTest->wlr, $bsw);
    $pAvg = $pick($m['avg_pressure'] ?? null, $kp['pressure'] ?? null, $mpfmTest->pressure);
    $pMin = $pick($m['min_pressure'] ?? null); $pMax = $pick($m['max_pressure'] ?? null);
    $tAvg = $pick($m['avg_temperature'] ?? null, $kp['temperature'] ?? null, $mpfmTest->temperature);
    $tMin = $pick($m['min_temperature'] ?? null); $tMax = $pick($m['max_temperature'] ?? null);

    // temperature unit as written in the log columns
    $tUnit = '°C';
    foreach (($columns ?? []) as $c) { if (stripos($c, 'temp') !== false && preg_match('/\[(.*?)\]/', $c, $mm)) { $tUnit = $mm[1]; break; } }
    $pUnit = 'psi';

    $nice = function ($v) { $v = max(($v ?? 0) * 1.25, 1); $mag = 10 ** floor(log10($v)); foreach ([1, 2, 2.5, 5, 10] as $s) { if ($v <= $s * $mag) return $s * $mag; } return 10 * $mag; };
    $pScale = $nice($pMax ?? $pAvg); $tScale = $nice($tMax ?? $tAvg);

    // phase fractions in the pipe cross-section
    $gasF = $gvf !== null ? min(max($gvf, 0), 100) / 100 : 0;
    $waterF = (1 - $gasF) * (min(max($wlr ?? 0, 0), 100) / 100);
    $oilF = max(1 - $gasF - $waterF, 0);

    $status = $mpfmTest->status;
    $title = $wellTest->well ?? $mpfmTest->oilString->name ?? 'MPFM Test #'.$mpfmTest->id;
    $tdate = ($wellTest->test_date ?? $mpfmTest->test_date);
    $deptDone = $mpfmTest->approved_by_department_at || $status === 'approved';
    $survDone = $mpfmTest->approved_by_surveillance_at || $status === 'approved';

    $safety = $m['safety_issues'] ?? [];
    $flagged = [];
    foreach ($safety as $group => $items) { foreach ((array) $items as $it) { $col = $it['column'] ?? '?'; $flagged[$col]['n'] = ($flagged[$col]['n'] ?? 0) + 1; $flagged[$col]['v'][] = $it['value'] ?? null; } }
    $props = [['Bo', 'bo'], ['Bw', 'bw'], ['Bg', 'bg'], ['Z factor', 'z_factor'], ['Rs', 'rst'], ['ρ oil', 'po'], ['ρ water', 'pw'], ['ρ gas', 'pg']];
    $cums = [['Oil', 'cumulative_oil_volume', 'bbl'], ['Water', 'cumulative_water_volume', 'bbl'], ['Gas', 'cumulative_gas_volume', 'scf']];
    $rowsShown = array_slice($rows ?? [], 0, 100);
@endphp

<div class="ofp" id="ofp">
    <div class="bgl"><div class="orb o1"></div><div class="orb o2"></div></div>

    {{-- HERO --}}
    <div class="hero rv">
        <div class="who">
            <span class="lg"></span>
            <div>
                <div class="eyebrow"><i></i> MPFM test #{{ $mpfmTest->id }}</div>
                <h1>{{ $title }} <em>· {{ $mpfmTest->oilString->name ?? '' }}</em></h1>
                <div class="meta">
                    <span class="chip {{ $status }}">{{ ucfirst($status) }}</span>
                    <span><i class="fe fe-calendar"></i> {{ $tdate->format('d M Y') }}</span>
                    <span><i class="fe fe-clock"></i> {{ ucfirst($mpfmTest->shift) }} shift</span>
                    <span><i class="fe fe-user"></i> {{ $wellTest->operator ?? $mpfmTest->user->name ?? 'N/A' }}</span>
                    <span>{{ $mpfmTest->oilField->name ?? '' }} · {{ $mpfmTest->tower->name ?? '' }}</span>
                </div>
            </div>
        </div>
        <div class="actions">
            <a class="btn2 g" href="{{ route('tv_tests.compare', ['mpfm_test_id' => $mpfmTest->id]) }}"><i class="fe fe-shuffle"></i> Compare vs TV</a>
            <a class="btn2 w" href="{{ route('mpfm_tests.show_history', $mpfmTest->id) }}"><i class="fe fe-check-circle"></i> Validation &amp; history</a>
            <a class="btn2" href="{{ route('mpfm_tests.string_analysis', $mpfmTest->oil_string_id) }}"><i class="fe fe-trending-up"></i> String analysis</a>
            @can('update', $mpfmTest)<a class="btn2 p" href="{{ route('mpfm_tests.edit', $mpfmTest->id) }}"><i class="fe fe-edit"></i> Edit</a>@endcan
            <a class="btn2" href="{{ route('mpfm_tests.index') }}"><i class="fe fe-arrow-left"></i> Back</a>
        </div>
    </div>

    {{-- WORKFLOW --}}
    <div class="sec">
        <div class="panel rv">
            <div class="ph"><h3><i class="fe fe-git-merge"></i> Review workflow</h3><span class="sub">Created {{ $mpfmTest->created_at->format('d M Y H:i') }}</span></div>
            <div class="steps">
                <div class="step done"><div class="dot"><i class="fe fe-upload"></i></div><b>Log uploaded</b><small>{{ $mpfmTest->created_at->format('d M') }}</small></div>
                <div class="step {{ $deptDone ? 'done' : '' }} {{ $status === 'submitted' && ! $deptDone ? 'cur' : '' }}"><div class="dot"><i class="fe fe-briefcase"></i></div><b>Department</b><small>{{ $mpfmTest->approved_by_department_at ? $mpfmTest->approved_by_department_at->format('d M') : ($deptDone ? 'Approved' : 'Pending') }}</small></div>
                <div class="step {{ $survDone ? 'done' : '' }} {{ $deptDone && ! $survDone ? 'cur' : '' }}"><div class="dot"><i class="fe fe-eye"></i></div><b>Surveillance</b><small>{{ $mpfmTest->approved_by_surveillance_at ? $mpfmTest->approved_by_surveillance_at->format('d M') : ($survDone ? 'Approved' : 'Pending') }}</small></div>
                <div class="step {{ $status === 'approved' ? 'done ok' : '' }} {{ $status === 'rejected' ? 'done bad' : '' }}"><div class="dot"><i class="fe {{ $status === 'rejected' ? 'fe-x' : 'fe-check' }}"></i></div><b>{{ $status === 'rejected' ? 'Rejected' : 'Approved' }}</b><small>{{ in_array($status, ['approved', 'rejected']) ? 'Final' : 'Pending' }}</small></div>
            </div>
            @if($status === 'rejected' && ($mpfmTest->rejected_reason || $mpfmTest->rejected_details))
                <div class="note bad"><small>Rejected{{ $mpfmTest->rejected_by_role ? ' by '.str_replace('_', ' ', $mpfmTest->rejected_by_role) : '' }}</small>{{ $mpfmTest->rejected_reason }}{{ $mpfmTest->rejected_details ? "\n".$mpfmTest->rejected_details : '' }}</div>
            @endif
            @if($mpfmTest->retest_requested_at)
                <div class="note"><small>Retest requested</small>{{ $mpfmTest->retest_reason }}{{ $mpfmTest->retest_details ? "\n".$mpfmTest->retest_details : '' }}</div>
            @endif
            @if($mpfmTest->supervisor_remarks)<div class="note"><small>Supervisor remarks</small>{{ $mpfmTest->supervisor_remarks }}</div>@endif
        </div>
    </div>

    {{-- KPIs --}}
    <div class="kpis">
        @foreach([
            ['Oil rate', $oil, 'STB/d', 'fe-droplet', '#fbbf24', 2],
            ['Gas rate', $gas, 'MMScf/d', 'fe-wind', '#22d3ee', 2],
            ['Water rate', $wrate, 'STB/d', 'fe-cloud-drizzle', '#38bdf8', 2],
            ['GOR', $gor, 'Scf/STB', 'fe-layers', '#34d399', 0],
            ['GVF', $gvf, '%', 'fe-percent', '#a78bfa', 1],
            ['BSW', $bsw, '%', 'fe-activity', '#f87171', 1],
        ] as $i => [$lbl, $val, $un, $ic, $col, $dec])
            <div class="kpi rv" style="--d:{{ $i + 1 }};--c:{{ $col }}"><div class="ic"><i class="fe {{ $ic }}"></i></div><div class="l">{{ $lbl }}</div>
                @if($val !== null)<div class="v cu" data-to="{{ $val }}" data-dec="{{ $dec }}">{{ $f($val, $dec) }}</div>@else<div class="v">N/A</div>@endif
                <div class="u">{{ $un }}</div></div>
        @endforeach
    </div>

    {{-- METER DRAWING + CROSS SECTION --}}
    <div class="sec g21">
        <div class="panel rv">
            <div class="ph"><h3><i class="fe fe-cpu"></i> Multiphase flow meter</h3><span class="sub">Readings of this test on the meter</span></div>
            <div class="scroll"><div class="fit">
<svg viewBox="0 0 900 350" role="img" aria-label="MPFM drawing">
<defs>
    <linearGradient id="tvSteel" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#9aa8bd"/><stop offset=".18" stop-color="#e2e8f0"/><stop offset=".5" stop-color="#64748b"/><stop offset="1" stop-color="#1e293b"/></linearGradient>
    <linearGradient id="msBody" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#a8b6cb"/><stop offset=".16" stop-color="#f1f5f9"/><stop offset=".5" stop-color="#7c8aa1"/><stop offset="1" stop-color="#1b2433"/></linearGradient>
    <radialGradient id="msGlow" cx=".5" cy=".5" r=".5"><stop offset="0" stop-color="#22d3ee" stop-opacity=".25"/><stop offset="1" stop-color="#22d3ee" stop-opacity="0"/></radialGradient>
    <path id="mPipe" d="M16,230 H884"/>
</defs>
<ellipse cx="450" cy="230" rx="300" ry="110" fill="url(#msGlow)"/>
{{-- stems for the dials --}}
<rect x="106" y="142" width="8" height="74" fill="#64748b" stroke="#0b1220"/><rect x="206" y="142" width="8" height="74" fill="#64748b" stroke="#0b1220"/>
<g fill="none"><use href="#mPipe" stroke="#0b1220" stroke-width="32"/><use href="#mPipe" stroke="#64748b" stroke-width="26"/><use href="#mPipe" stroke="#e2e8f0" stroke-opacity=".3" stroke-width="3" transform="translate(0,-6)"/></g>
{{-- venturi --}}
<path d="M290,200 H350 L400,216 H500 L550,200 H610 V260 H550 L500,244 H400 L350,260 H290 Z" fill="url(#msBody)" stroke="#0b1220" stroke-width="1.6"/>
<path d="M350,200 V260 M550,200 V260" stroke="#0b1220" stroke-opacity=".4"/>
<rect x="283" y="196" width="10" height="68" rx="2" fill="url(#tvSteel)" stroke="#0b1220"/><rect x="607" y="196" width="10" height="68" rx="2" fill="url(#tvSteel)" stroke="#0b1220"/>
<text x="450" y="236" text-anchor="middle" class="tg" style="fill:#0b1220;font-size:8px">VENTURI</text>
{{-- DP transmitter + impulse lines --}}
<path d="M322,200 V150 H350 V122 M410,216 V150 H380 V122" fill="none" stroke="#94a3b8" stroke-width="2.4"/>
<rect x="337" y="88" width="56" height="34" rx="7" fill="#0a1222" stroke="#22d3ee" stroke-width="1.8"/><text x="365" y="109" text-anchor="middle" font-size="11" font-weight="800" fill="#67e8f9">DP</text>
{{-- gamma densitometer --}}
<rect x="441" y="132" width="58" height="46" rx="7" fill="#0a1222" stroke="#34d399" stroke-width="1.8"/><text x="470" y="160" text-anchor="middle" font-size="9.5" font-weight="800" fill="#6ee7b7">γ DETECTOR</text>
<rect x="445" y="180" width="50" height="22" fill="#64748b" stroke="#0b1220"/>
<rect x="440" y="284" width="60" height="50" rx="8" fill="#0a1222" stroke="#fbbf24" stroke-width="1.8"/>
<g transform="translate(470,309)"><circle r="14" fill="none" stroke="#fbbf24" stroke-width="1.4"/><g class="mspin"><path d="M0,-11 L5,-1 H-5 Z M9.5,5.5 L0,3 L5,-4 Z M-9.5,5.5 L-5,-4 L0,3 Z" fill="#fbbf24"/></g></g>
<rect x="445" y="262" width="50" height="22" fill="#64748b" stroke="#0b1220"/>
<path class="mbeam" d="M458,262 V204 M470,262 V204 M482,262 V204"/>
<text x="470" y="346" text-anchor="middle" class="tg" style="fill:#fcd34d">γ SOURCE</text>
{{-- PT --}}
<path d="M598,196 V158" stroke="#94a3b8" stroke-width="2.4"/><circle cx="598" cy="140" r="19" fill="#0a1222" stroke="#a78bfa" stroke-width="1.8"/><text x="598" y="144" text-anchor="middle" font-size="11" font-weight="800" fill="#c4b5fd">PT</text>
{{-- signal lines to flow computer --}}
<path class="mflow" d="M393,105 H640 M470,132 V105 M598,121 V105" stroke="#22d3ee"/>
{{-- flow computer --}}
<rect x="640" y="22" width="244" height="136" rx="12" fill="#0a1222" stroke="#22d3ee" stroke-opacity=".7" stroke-width="1.6"/>
<circle cx="866" cy="38" r="3.6" fill="#34d399" class="mpulse"/>
<text x="656" y="42" class="cl" fill="#67e8f9">FLOW COMPUTER</text>
<text x="656" y="72" class="cl" fill="#fbbf24">OIL</text><text x="790" y="74" text-anchor="end" class="cv cu" data-to="{{ $oil ?? 0 }}" data-dec="1">{{ $f($oil, 1) }}</text><text x="798" y="74" class="cu2">STB/d</text>
<text x="656" y="102" class="cl" fill="#67e8f9">GAS</text><text x="790" y="104" text-anchor="end" class="cv cu" data-to="{{ $gas ?? 0 }}" data-dec="2">{{ $f($gas, 2) }}</text><text x="798" y="104" class="cu2">MMScf/d</text>
<text x="656" y="132" class="cl" fill="#7dd3fc">WATER</text><text x="790" y="134" text-anchor="end" class="cv cu" data-to="{{ $wrate ?? 0 }}" data-dec="1">{{ $f($wrate, 1) }}</text><text x="798" y="134" class="cu2">STB/d</text>
{{-- dials --}}
@include('partials.tv_dial', ['cx' => 110, 'cy' => 104, 'r' => 30, 'val' => $pAvg ?? 0, 'max' => $pScale, 'text' => $f($pAvg, 0), 'unit' => 'PSI'])
@include('partials.tv_dial', ['cx' => 210, 'cy' => 104, 'r' => 30, 'val' => $tAvg ?? 0, 'max' => $tScale, 'text' => $f($tAvg, 0), 'unit' => $tUnit])
<text x="110" y="56" text-anchor="middle" class="tg">PI · AVG PRESSURE</text><text x="210" y="56" text-anchor="middle" class="tg">TI · AVG TEMP</text>
{{-- moving fluid --}}
@foreach([0, .8, 1.6, 2.4, 3.2, 4] as $b)
    <circle r="3.6" fill="#fde68a"><animateMotion dur="5s" begin="{{ $b }}s" repeatCount="indefinite"><mpath href="#mPipe"/></animateMotion></circle>
    <circle r="2.6" fill="#a5f3fc"><animateMotion dur="5s" begin="{{ $b + .4 }}s" repeatCount="indefinite"><mpath href="#mPipe"/></animateMotion></circle>
@endforeach
<text x="16" y="204" class="tg" style="fill:#fcd34d">WELL STREAM</text>
<text x="884" y="262" text-anchor="end" class="tg" style="fill:#67e8f9">→ PRODUCTION HEADER</text>
</svg>
            </div></div>
        </div>

        <div class="panel rv" style="--d:1">
            <div class="ph"><h3><i class="fe fe-pie-chart"></i> In the pipe</h3><span class="sub">Cross-section at line conditions</span></div>
            <div class="dw">
                <svg class="donut" viewBox="0 0 200 200" role="img" aria-label="Phase fractions">
                    <circle class="arc t" cx="100" cy="100" r="76" pathLength="100"/>
                    @php $o2 = 0; @endphp
                    @foreach([['#22d3ee', $gasF], ['#fbbf24', $oilF], ['#38bdf8', $waterF]] as [$col, $fr])
                        @if($fr > 0)<circle class="arc" cx="100" cy="100" r="76" pathLength="100" stroke="{{ $col }}" style="stroke-dasharray:{{ round($fr * 100, 2) }} 100;stroke-dashoffset:-{{ round($o2, 2) }}"/>@endif
                        @php $o2 += $fr * 100; @endphp
                    @endforeach
                    <text x="100" y="98" text-anchor="middle" font-family="Space Grotesk" font-weight="700" font-size="30" fill="#fff">{{ $gvf !== null ? $f($gvf, 1).'%' : '—' }}</text>
                    <text x="100" y="118" text-anchor="middle" font-size="10" font-weight="700" letter-spacing="2" fill="#9fb0cf">GAS (GVF)</text>
                </svg>
                <div class="lgd" style="min-width:150px">
                    <div><b style="background:#22d3ee"></b> Gas <strong>{{ $f($gasF * 100, 1) }}%</strong></div>
                    <div><b style="background:#fbbf24"></b> Oil <strong>{{ $f($oilF * 100, 1) }}%</strong></div>
                    <div><b style="background:#38bdf8"></b> Water <strong>{{ $f($waterF * 100, 1) }}%</strong></div>
                </div>
            </div>
            <div class="rng">
                @foreach([['Pressure', $pMin, $pAvg, $pMax, $pScale, $pUnit], ['Temperature', $tMin, $tAvg, $tMax, $tScale, $tUnit]] as [$lbl, $mn, $av, $mx, $sc, $un])
                    @if($av !== null)
                        @php $lo = $mn ?? $av; $hi = $mx ?? $av; @endphp
                        <div>
                            <div class="h"><span>{{ $lbl }}</span><b>{{ $f($av, 1) }} {{ $un }}</b></div>
                            <div class="tr"><i style="left:{{ round($lo / $sc * 100, 1) }}%;--w:{{ max(round(($hi - $lo) / $sc * 100, 1), 1.5) }}%"></i><em style="left:calc({{ round($av / $sc * 100, 1) }}% - 2px)"></em></div>
                            <div class="s"><span>min {{ $f($lo, 1) }}</span><span>max {{ $f($hi, 1) }}</span></div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    {{-- CHARTS --}}
    @if($flowRatesChartData)
    <div class="sec">
        <div class="panel rv">
            <div class="ph"><h3><i class="fe fe-bar-chart-2"></i> Flow rates during the test</h3><span class="sub">{{ count($rows ?? []) }} readings · click a legend chip to hide a series</span></div>
            <div id="chFlow"></div>
        </div>
    </div>
    @endif
    @if($gorChartData || $pressureTempChartData)
    <div class="sec g2">
        @if($gorChartData)<div class="panel rv"><div class="ph"><h3><i class="fe fe-trending-up"></i> GOR</h3></div><div id="chGor"></div></div>@endif
        @if($pressureTempChartData)<div class="panel rv" style="--d:1"><div class="ph"><h3><i class="fe fe-thermometer"></i> Pressure &amp; temperature</h3></div><div id="chPt"></div></div>@endif
    </div>
    @endif

    {{-- INFO / FLUID / QUALITY --}}
    <div class="sec g21">
        <div class="panel rv">
            <div class="ph"><h3><i class="fe fe-info"></i> Test information</h3></div>
            <div class="infog">
                @if($wellTest)
                    @foreach([['Client', $wellTest->client], ['Field', $wellTest->field], ['Well', $wellTest->well], ['Tower', $wellTest->wellhead_tower], ['String', $wellTest->wellhead_string], ['Operator', $wellTest->operator], ['Test type', $wellTest->test_type], ['Choke size', $wellTest->choke_size], ['Upstream choke', $wellTest->upstream_choke], ['Downstream choke', $wellTest->downstream_choke], ['Start', $wellTest->start_time], ['End', $wellTest->end_time], ['Log file', $wellTest->filename]] as [$l, $v])
                        @if(filled($v))<div class="info"><div class="l">{{ $l }}</div><div class="v" title="{{ $v }}">{{ $v }}</div></div>@endif
                    @endforeach
                @else
                    <div class="info"><div class="l">Oil field</div><div class="v">{{ $mpfmTest->oilField->name ?? 'N/A' }}</div></div>
                    <div class="info"><div class="l">Tower</div><div class="v">{{ $mpfmTest->tower->name ?? 'N/A' }}</div></div>
                    <div class="info"><div class="l">Oil string</div><div class="v">{{ $mpfmTest->oilString->name ?? 'N/A' }}</div></div>
                    <div class="info"><div class="l">Operator</div><div class="v">{{ $mpfmTest->user->name ?? 'N/A' }}</div></div>
                @endif
            </div>
            @if($mpfmTest->notes)<div class="note"><small>Operator notes</small>{{ $mpfmTest->notes }}</div>@endif
        </div>
        <div class="panel rv" style="--d:1">
            <div class="ph"><h3><i class="fe fe-droplet"></i> Fluid properties</h3></div>
            @php $has = array_filter($props, fn ($p) => is_numeric($m[$p[1]] ?? null)); @endphp
            @if(count($has))
                <div class="props">@foreach($has as [$l, $k])<div><small>{{ $l }}</small><b>{{ $f($m[$k], 3) }}</b></div>@endforeach</div>
            @else
                <div class="empty">No PVT properties in this test</div>
            @endif
            @php $hc = array_filter($cums, fn ($c) => is_numeric($m[$c[1]] ?? null)); @endphp
            @if(count($hc))
                <div class="ph" style="margin:20px 0 10px"><h3 style="font-size:13px">Cumulative volumes</h3></div>
                <div class="props">@foreach($hc as [$l, $k, $u])<div><small>{{ $l }} · {{ $u }}</small><b>{{ number_format($m[$k], 0) }}</b></div>@endforeach</div>
            @endif
            @if(count($flagged))
                <div class="msg warn" style="margin-top:18px"><i class="fe fe-alert-triangle"></i><div><b style="color:#fff">Flagged readings</b><br>
                    @foreach($flagged as $col => $d){{ $col }} — {{ $d['n'] }} readings @if(count(array_filter($d['v'], 'is_numeric'))) ({{ $f(min(array_filter($d['v'], 'is_numeric')), 1) }}–{{ $f(max(array_filter($d['v'], 'is_numeric')), 1) }})@endif<br>@endforeach
                </div></div>
            @endif
        </div>
    </div>

    {{-- RELATED TV --}}
    @if($relatedTv->count())
    <div class="sec">
        <div class="panel rv">
            <div class="ph"><h3><i class="fe fe-anchor"></i> Test vessel tests on this string</h3><span class="sub">Compare this MPFM test with a reference</span></div>
            <div class="scroll"><table class="t">
                <thead><tr><th>Test</th><th>Date</th><th>Vessel</th><th>Oil</th><th>Gas</th><th>WC</th><th>Status</th><th></th></tr></thead>
                <tbody>@foreach($relatedTv as $t)
                    <tr><td>TV #{{ $t->id }}</td><td>{{ $t->test_date->format('Y-m-d') }}</td><td>{{ $t->testVessel->name ?? '—' }}</td><td>{{ $f($t->oil_rate, 0) }}</td><td>{{ $f($t->gas_rate, 1) }}</td><td>{{ $f($t->water_cut, 1) }}%</td><td><span class="chip {{ $t->status }}">{{ $t->status }}</span></td>
                        <td style="white-space:nowrap"><a class="lnk" href="{{ route('tv_tests.show', $t->id) }}">View</a> <a class="lnk w" href="{{ route('tv_tests.compare_result', ['tv_test_id' => $t->id, 'mpfm_test_id' => $mpfmTest->id]) }}"><i class="fe fe-shuffle"></i> Compare</a></td></tr>
                @endforeach</tbody>
            </table></div>
        </div>
    </div>
    @endif

    {{-- RAW DATA --}}
    @if($wellTest && count($rowsShown))
    <div class="sec">
        <div class="panel rv">
            <div class="ph"><h3><i class="fe fe-list"></i> Detailed test data</h3>
                <span style="display:flex;gap:10px;align-items:center;flex-wrap:wrap"><span class="sub">first {{ count($rowsShown) }} of {{ count($rows) }} rows</span>
                <span class="srch"><i class="fe fe-search"></i><input type="text" id="rowQ" placeholder="Filter rows…" autocomplete="off"></span>
                <button type="button" class="btn2" id="csvBtn"><i class="fe fe-download"></i> CSV</button></span></div>
            <div class="dtw"><table id="vxDetailTable">
                <thead><tr>@foreach($columns as $c)<th>{{ $c }}</th>@endforeach</tr></thead>
                <tbody>@foreach($rowsShown as $row)<tr>@foreach($row as $v)<td>{{ is_numeric($v) ? number_format((float) $v, 2) : $v }}</td>@endforeach</tr>@endforeach</tbody>
            </table></div>
        </div>
    </div>
    @endif

    <div class="acts">
        @if($mpfmTest->log_file_path)<a class="btn2" href="{{ route('mpfm_tests.download_log', $mpfmTest->id) }}"><i class="fe fe-download"></i> Download log</a>@endif
        <button class="btn2" onclick="window.print()"><i class="fe fe-printer"></i> Print</button>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('assets/js/ofcharts.js') }}"></script>
<script>
(function () {
    var root = document.getElementById('ofp');
    var reduce = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;
    var C = { oil: '#fbbf24', water: '#38bdf8', gas: '#22d3ee' };
    function num(a) { return (a || []).map(function (v) { var n = parseFloat(v); return isNaN(n) ? null : n; }); }
    function colorFor(name, i) {
        if (/^qo|oil/i.test(name)) return C.oil; if (/^qw|water/i.test(name)) return C.water; if (/^qg|gas/i.test(name)) return C.gas;
        return OFCharts.colors[i % OFCharts.colors.length];
    }
    function fromChartJs(cfg, opts) {
        var d = (cfg && cfg.data) || {};
        return Object.assign({
            labels: d.labels || [],
            series: (d.datasets || []).map(function (s, i) {
                var name = s.label || ('Series ' + (i + 1));
                return { name: name, data: num(s.data), color: colorFor(name, i), axis: (s.yAxisID === 'y1' || /^qg|gas/i.test(name) && opts.gasRight) ? 'r' : 'l' };
            })
        }, opts);
    }
    @if($flowRatesChartData) OFCharts.line(document.getElementById('chFlow'), fromChartJs(@json($flowRatesChartData), { height: 300, gasRight: true, dec: 0, decR: 1, smooth: false })); @endif
    @if($gorChartData) OFCharts.line(document.getElementById('chGor'), fromChartJs(@json($gorChartData), { height: 260, dec: 0, area: true, smooth: false })); @endif
    @if($pressureTempChartData) OFCharts.line(document.getElementById('chPt'), fromChartJs(@json($pressureTempChartData), { height: 260, dec: 0, decR: 1, zero: false, smooth: false })); @endif

    function fmt(v, d) { return v.toLocaleString('en-US', { minimumFractionDigits: d, maximumFractionDigits: d }); }
    function countUp(el) {
        var to = parseFloat(el.dataset.to) || 0, d = parseInt(el.dataset.dec || 0, 10), t0 = null;
        if (reduce) return;
        function step(t) { if (t0 === null) t0 = t; var p = Math.min((t - t0) / 1400, 1), e = 1 - Math.pow(1 - p, 4); el.textContent = fmt(to * e, d); if (p < 1) requestAnimationFrame(step); else el.textContent = fmt(to, d); }
        requestAnimationFrame(step);
    }
    var io = new IntersectionObserver(function (es) { es.forEach(function (e) { if (!e.isIntersecting) return; e.target.classList.add('in'); e.target.querySelectorAll('.cu').forEach(countUp); io.unobserve(e.target); }); }, { threshold: .1 });
    root.querySelectorAll('.rv').forEach(function (el) { io.observe(el); });

    var q = document.getElementById('rowQ');
    if (q) q.addEventListener('input', function () {
        var s = q.value.toLowerCase();
        document.querySelectorAll('#vxDetailTable tbody tr').forEach(function (tr) { tr.style.display = !s || tr.textContent.toLowerCase().indexOf(s) > -1 ? '' : 'none'; });
    });
    var csv = document.getElementById('csvBtn');
    if (csv) csv.addEventListener('click', function () {
        var lines = [].map.call(document.querySelectorAll('#vxDetailTable tr'), function (tr) {
            return [].map.call(tr.children, function (c) { return '"' + c.textContent.replace(/"/g, '""') + '"'; }).join(',');
        });
        var a = document.createElement('a'); a.href = URL.createObjectURL(new Blob([lines.join('\n')], { type: 'text/csv' })); a.download = 'mpfm-test-{{ $mpfmTest->id }}.csv'; a.click();
    });
    if (reduce) root.querySelectorAll('svg').forEach(function (s) { if (s.pauseAnimations) s.pauseAnimations(); });
})();
</script>
@endsection
