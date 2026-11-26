@extends('layouts.master')

@section('title', 'Well Test Results')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/ofpage.css') }}">
<style>
.ofp .stepper { display:flex; align-items:center; gap:0; margin-top:16px; flex-wrap:wrap; }
.ofp .stp { display:flex; align-items:center; gap:10px; padding:8px 16px; border-radius:50px; border:1px solid var(--line); background:var(--glass); font-size:12.5px; font-weight:700; color:var(--mute); }
.ofp .stp i { width:22px; height:22px; border-radius:50%; display:grid; place-items:center; font-size:12px; background:rgba(255,255,255,.08); }
.ofp .stp.done { color:#bbf7d0; border-color:rgba(52,211,153,.45); } .ofp .stp.done i { background:rgba(52,211,153,.2); color:var(--green); }
.ofp .stp.cur { color:#fff; border-color:var(--cyan); box-shadow:0 0 22px rgba(34,211,238,.25); } .ofp .stp.cur i { background:var(--grad); color:#04111f; }
.ofp .stl { width:34px; border-top:2px dashed rgba(34,211,238,.45); }
.ofp .banner { margin-top:18px; display:flex; gap:16px; align-items:center; flex-wrap:wrap; padding:16px 20px; border-radius:20px; border:1px solid rgba(251,191,36,.4); background:linear-gradient(120deg,rgba(251,191,36,.1),rgba(251,191,36,.03)); }
.ofp .banner i { font-size:24px; color:var(--amber); }
.ofp .banner b { display:block; color:#fff; font-size:15px; } .ofp .banner small { color:var(--sub); font-size:12.5px; }
.ofp .banner .sp { flex:1; }
.ofp .donut { width:170px; height:170px; flex:none; } .ofp .donut .arc { stroke-width:14; }
.ofp .dw { display:flex; align-items:center; gap:22px; flex-wrap:wrap; justify-content:center; margin-bottom:22px; }
.ofp .props { display:grid; grid-template-columns:repeat(auto-fit,minmax(130px,1fr)); gap:10px; }
.ofp .props div { padding:10px 12px; border-radius:13px; background:var(--glass); border:1px solid var(--line); }
.ofp .props small { display:block; font-size:9.5px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--mute); }
.ofp .props b { font:700 15px 'Space Grotesk'; color:#fff; } .ofp .props span { font-size:10.5px; color:var(--sub); margin-left:3px; }
.ofp .sf { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:12px; }
.ofp .sf > div { padding:14px 16px; border-radius:16px; border:1px solid rgba(251,191,36,.4); background:rgba(251,191,36,.07); }
.ofp .sf b { display:block; font:800 24px 'Space Grotesk'; color:var(--amber); } .ofp .sf small { color:#fde68a; font-size:12px; } .ofp .sf em { font-style:normal; display:block; margin-top:3px; font-size:11.5px; color:var(--sub); }
.ofp .cc { display:grid; grid-template-columns:1fr 1fr; gap:18px; margin-top:18px; }
.ofp .chart { position:relative; height:300px; }
.ofp .chart canvas { position:absolute; inset:0; }
.ofp .chart .nd { position:absolute; inset:0; display:grid; place-items:center; color:var(--mute); font-size:13px; text-align:center; }
.ofp .ctl { display:flex; gap:8px; align-items:center; }
.ofp .ctl select { height:34px; padding:0 28px 0 12px; border-radius:12px; border:1px solid var(--line); background:rgba(255,255,255,.06); color:#fff; font:600 12px 'Inter'; appearance:none; -webkit-appearance:none; outline:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='7' fill='none' stroke='%2322d3ee' stroke-width='2'%3E%3Cpath d='M1 1l4 4 4-4'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 10px center; }
.ofp .ctl select option { background:#0b1426; }
.ofp .ctl button { width:34px; height:34px; border-radius:11px; border:1px solid var(--line); background:var(--glass); color:var(--sub); cursor:pointer; display:grid; place-items:center; transition:border-color .2s, color .2s; }
.ofp .ctl button:hover { border-color:var(--cyan); color:#fff; }
.ofp .avg { display:grid; grid-template-columns:repeat(auto-fill,minmax(170px,1fr)); gap:8px; max-height:340px; overflow:auto; scrollbar-width:thin; scrollbar-color:rgba(96,165,250,.35) transparent; padding-right:4px; }
.ofp .avg div { padding:9px 12px; border-radius:12px; background:var(--glass); border:1px solid var(--line); }
.ofp .avg small { display:block; font-size:9.5px; font-weight:700; color:var(--mute); letter-spacing:.04em; word-break:break-all; } .ofp .avg b { font:700 13.5px 'Space Grotesk'; color:#fff; }
.ofp .save { margin-top:20px; display:grid; grid-template-columns:auto 1fr auto; gap:22px; align-items:center; padding:24px 26px; border-radius:24px; border:1px solid rgba(52,211,153,.45); background:linear-gradient(120deg,rgba(52,211,153,.12),rgba(34,211,238,.05)); box-shadow:0 0 50px rgba(52,211,153,.1); }
.ofp .save svg { width:88px; height:88px; }
.ofp .save h3 { font-size:20px; font-weight:800; display:block; } .ofp .save p { color:var(--sub); font-size:13px; margin-top:4px; line-height:1.55; }
.ofp .save .go { display:flex; gap:10px; flex-wrap:wrap; justify-content:flex-end; }
.ofp .btn2.big { padding:14px 28px; font-size:14px; }
.ofp .tinfo { padding:14px 16px; border-radius:16px; margin-top:16px; border:1px solid rgba(59,130,246,.4); background:rgba(59,130,246,.08); color:#bfdbfe; font-size:13px; display:flex; gap:12px; align-items:center; }
.ofp .tinfo i { font-size:20px; color:#60a5fa; }
.ofp .dtw { max-height:460px; overflow:auto; border-radius:14px; border:1px solid var(--line); scrollbar-width:thin; scrollbar-color:rgba(96,165,250,.35) transparent; }
.ofp .dtw table { border-collapse:collapse; width:100%; font-size:12px; }
.ofp .dtw th { position:sticky; top:0; background:#0b1426; color:var(--cyan); padding:9px 12px; text-align:left; font-size:10.5px; letter-spacing:.05em; white-space:nowrap; border-bottom:1px solid var(--line); z-index:1; }
.ofp .dtw td { padding:7px 12px; white-space:nowrap; border-bottom:1px solid rgba(255,255,255,.05); color:var(--ink); }
.ofp .dtw td.bad { background:rgba(248,113,113,.14); color:#fecaca; } .ofp .dtw td.warn { background:rgba(251,191,36,.12); color:#fde68a; }
.ofp .dtw tr:hover td { background:rgba(34,211,238,.07); }
.ofp .pager { display:flex; align-items:center; gap:10px; margin-top:12px; flex-wrap:wrap; font-size:12.5px; color:var(--sub); } .ofp .pager .sp { flex:1; }
.ofp .pager button { padding:6px 14px; border-radius:50px; border:1px solid var(--line); background:var(--glass); color:var(--ink); font:600 12px 'Inter'; cursor:pointer; } .ofp .pager button:disabled { opacity:.4; cursor:default; }
.ofp details.raw summary { cursor:pointer; list-style:none; display:flex; align-items:center; gap:10px; font-size:16px; font-weight:800; }
.ofp details.raw summary::-webkit-details-marker { display:none; }
.ofp details.raw summary i { width:30px; height:30px; border-radius:10px; display:grid; place-items:center; background:var(--grad); color:#04111f; font-size:14px; }
.ofp details.raw summary span { margin-left:auto; color:var(--sub); font-size:12px; font-weight:500; }
.ofp details.raw pre { margin:16px 0 0; padding:16px; border-radius:14px; background:#02060f; border:1px solid var(--line); color:#a5f3fc; font:12px/1.55 'Consolas','Courier New',monospace; max-height:420px; overflow:auto; white-space:pre; }
@media (max-width:1100px) { .ofp .cc { grid-template-columns:1fr; } .ofp .save { grid-template-columns:1fr; text-align:center; } .ofp .save svg { margin:0 auto; } .ofp .save .go { justify-content:center; } }
@media print { .ofp .actions, .ofp .save .go, .ofp .ctl { display:none !important; } .ofp .rv { opacity:1 !important; transform:none !important; } }
</style>
@endsection

@section('content')
@php
    $f = fn ($v, $d = 2) => is_numeric($v) ? number_format((float) $v, $d) : '—';
    $A = $averages ?? [];
    $g = fn ($k) => isset($A[$k]) && is_numeric($A[$k]) ? (float) $A[$k] : null;
    $oil = $g('oil_rate'); $gas = $g('gas_rate'); $wrate = $g('water_rate');
    $gor = $g('gor'); $bsw = $g('bsw'); $wlr = $g('wlr'); $gvf = $g('gvf');
    $pAvg = $g('avg_pressure'); $pMin = $g('min_pressure'); $pMax = $g('max_pressure');
    $tAvg = $g('avg_temperature'); $tMin = $g('min_temperature'); $tMax = $g('max_temperature');
    $cols = $parsed_data['test_data']['columns'] ?? [];
    $nRows = count($parsed_data['test_data']['rows'] ?? []);
    $tUnit = '°C';
    foreach ($cols as $c) { if (stripos($c, 'temp') !== false && preg_match('/\[(.*?)\]/', $c, $mm)) { $tUnit = $mm[1]; break; } }
    $nice = function ($v) { $v = max(($v ?? 0) * 1.25, 1); $mag = 10 ** floor(log10($v)); foreach ([1, 2, 2.5, 5, 10] as $s) { if ($v <= $s * $mag) return $s * $mag; } return 10 * $mag; };
    $pScale = $nice($pMax ?? $pAvg); $tScale = $nice($tMax ?? $tAvg);
    $gasF = $gvf !== null ? min(max($gvf, 0), 100) / 100 : 0;
    $waterF = (1 - $gasF) * (min(max($wlr ?? $bsw ?? 0, 0), 100) / 100);
    $oilF = max(1 - $gasF - $waterF, 0);
    $start = $summary['start_time'] ?? null; $end = $summary['end_time'] ?? null;
    $mins = ($start && $end) ? (int) round(abs($end->diffInMinutes($start))) : null;
    $hasTask = (isset($task_id) && $task_id) || (isset($new_task_id) && $new_task_id);
    $safety = $A['safety_issues'] ?? [];
    $sfCards = [];
    foreach (['voltage_issues' => ['Voltage issues', 'V', 'fe-zap'], 'pressure_issues' => ['Pressure issues', 'psi', 'fe-activity'], 'temperature_issues' => ['Temperature issues', '', 'fe-thermometer']] as $k => [$lbl, $un, $ic]) {
        $items = $safety[$k] ?? [];
        if (count($items)) {
            $vals = array_filter(array_column($items, 'value'), 'is_numeric');
            $cn = [];
            foreach ($items as $it) { $cn[$it['column'] ?? '?'] = true; }
            $sfCards[] = ['lbl' => $lbl, 'n' => count($items), 'avg' => count($vals) ? array_sum($vals) / count($vals) : null, 'un' => $un, 'cols' => implode(', ', array_slice(array_keys($cn), 0, 3))];
        }
    }
    $props = [['Bo', 'bo', 4, 'Sm³/m³'], ['Bw', 'bw', 4, 'Sm³/m³'], ['Bg', 'bg', 4, 'Sm³/m³'], ['ρ oil', 'po', 2, 'kg/m³'], ['ρ water', 'pw', 2, 'kg/m³'], ['ρ gas', 'pg', 4, 'kg/m³'], ['Rs', 'rst', 2, 'Scf/STB'], ['Z factor', 'z_factor', 4, '']];
    $cums = [['Oil', 'cumulative_oil_volume', 'bbl'], ['Gas', 'cumulative_gas_volume', 'Mcf'], ['Water', 'cumulative_water_volume', 'bbl']];
    $info = [['Client', $summary['client'] ?? null], ['Field', $summary['field'] ?? null], ['Well', $summary['well'] ?? null], ['Wellhead tower', $summary['wellhead_tower'] ?? null], ['Wellhead string', $summary['wellhead_string'] ?? null], ['Operator', $summary['operator'] ?? null], ['Test date', $summary['test_date'] ?? null], ['Test type', $summary['test_type'] ?? null], ['Choke size', $summary['choke_size'] ?? null], ['Upstream choke', $summary['upstream_choke'] ?? null], ['Downstream choke', $summary['downstream_choke'] ?? null], ['Start', $start ? $start->format('Y-m-d H:i') : null], ['End', $end ? $end->format('Y-m-d H:i') : null]];
@endphp

<div class="ofp" id="ofp">
    <div class="bgl"><div class="orb o1"></div><div class="orb o2"></div></div>

    {{-- HERO --}}
    <div class="hero rv">
        <div class="who">
            <span class="lg"></span>
            <div>
                <div class="eyebrow"><i></i> Log analysed · not saved yet</div>
                <h1>Test results <em>· {{ $summary['well'] ?? '' }}</em></h1>
                <div class="meta">
                    <span>{{ $summary['client'] ?? '' }} · {{ $summary['field'] ?? '' }}</span>
                    <span><i class="fe fe-calendar"></i> {{ $start ? $start->format('d M Y H:i') : 'N/A' }}{{ $end ? ' → '.$end->format('H:i') : '' }}</span>
                    @if($mins !== null)<span><i class="fe fe-clock"></i> {{ intdiv($mins, 60) }}h {{ $mins % 60 }}m</span>@endif
                    <span><i class="fe fe-database"></i> {{ number_format($nRows) }} readings</span>
                </div>
            </div>
        </div>
        <div class="actions">
            <a class="btn2 g" href="#saveSec"><i class="fe fe-save"></i> Save test</a>
            <a class="btn2" href="{{ route('upload.form') }}"><i class="fe fe-upload"></i> Upload another</a>
        </div>
    </div>

    <div class="stepper rv">
        <span class="stp done"><i class="fe fe-check"></i> Log uploaded</span><span class="stl"></span>
        <span class="stp done"><i class="fe fe-check"></i> Analysed</span><span class="stl"></span>
        <span class="stp cur"><i class="fe fe-eye"></i> Review</span><span class="stl"></span>
        <span class="stp"><i class="fe fe-save"></i> Save to archive</span>
    </div>

    <div class="banner rv">
        <i class="fe fe-alert-triangle"></i>
        <div><b>Review the numbers before saving</b><small>Nothing is stored yet. Check the rates, the curves and the flagged readings below, then save the test.</small></div>
        <span class="sp"></span>
        <a class="btn2 g" href="#saveSec"><i class="fe fe-arrow-down"></i> Go to save</a>
    </div>

    {{-- KPIs --}}
    <div class="kpis">
        @foreach([
            ['Oil rate', $oil, 'STB/d', 'fe-droplet', '#fbbf24', 2],
            ['Gas rate', $gas, 'MMScf/d', 'fe-wind', '#22d3ee', 2],
            ['Water rate', $wrate, 'STB/d', 'fe-cloud-drizzle', '#38bdf8', 2],
            ['GOR', $gor, 'Scf/STB', 'fe-layers', '#34d399', 0],
            ['GVF', $gvf, '%', 'fe-percent', '#a78bfa', 2],
            ['BSW', $bsw, '%', 'fe-activity', '#f87171', 2],
        ] as $i => [$lbl, $val, $un, $ic, $c, $d])
            <div class="kpi rv" style="--d:{{ $i + 1 }};--c:{{ $c }}"><div class="ic"><i class="fe {{ $ic }}"></i></div><div class="l">{{ $lbl }}</div>
                @if($val !== null)<div class="v cu" data-to="{{ $val }}" data-dec="{{ $d }}">{{ $f($val, $d) }}</div>@else<div class="v">N/A</div>@endif<div class="u">{{ $un }}</div></div>
        @endforeach
    </div>

    {{-- METER + PIPE --}}
    <div class="sec g21">
        <div class="panel rv">
            <div class="ph"><h3><i class="fe fe-cpu"></i> Multiphase flow meter</h3><span class="sub">Averages over the whole test</span></div>
            <div class="scroll"><div class="fit">@include('partials.mpfm_meter', ['oil' => $oil, 'gas' => $gas, 'wrate' => $wrate, 'pAvg' => $pAvg, 'tAvg' => $tAvg, 'pScale' => $pScale, 'tScale' => $tScale, 'tUnit' => $tUnit])</div></div>
        </div>
        <div class="panel rv" style="--d:1">
            <div class="ph"><h3><i class="fe fe-pie-chart"></i> In the pipe</h3><span class="sub">Phase fractions</span></div>
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
                @foreach([['Pressure', $pMin, $pAvg, $pMax, $pScale, 'psi'], ['Temperature', $tMin, $tAvg, $tMax, $tScale, $tUnit]] as [$lbl, $mn, $av, $mx, $sc, $un])
                    @if($av !== null)
                        @php $lo = $mn ?? $av; $hi = $mx ?? $av; @endphp
                        <div><div class="h"><span>{{ $lbl }}</span><b>{{ $f($av, 1) }} {{ $un }}</b></div>
                            <div class="tr"><i style="left:{{ round($lo / $sc * 100, 1) }}%;--w:{{ max(round(($hi - $lo) / $sc * 100, 1), 1.5) }}%"></i><em style="left:calc({{ round($av / $sc * 100, 1) }}% - 2px)"></em></div>
                            <div class="s"><span>min {{ $f($lo, 1) }}</span><span>max {{ $f($hi, 1) }}</span></div></div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    {{-- SAFETY --}}
    @if(count($sfCards))
    <div class="sec"><div class="panel rv">
        <div class="ph"><h3><i class="fe fe-alert-triangle"></i> Flagged readings</h3><span class="sub">Readings the checker marked as out of range</span></div>
        <div class="sf">@foreach($sfCards as $c)<div><small>{{ $c['lbl'] }}</small><b>{{ $c['n'] }}</b><em>{{ $c['avg'] !== null ? 'avg '.$f($c['avg'], 2).' '.$c['un'] : '' }}{{ $c['cols'] ? ' · '.$c['cols'] : '' }}</em></div>@endforeach</div>
    </div></div>
    @endif

    {{-- INFO / FLUID --}}
    <div class="sec g21">
        <div class="panel rv">
            <div class="ph"><h3><i class="fe fe-info"></i> Test information</h3></div>
            <div class="infog">@foreach($info as [$l, $v])@if(filled($v) && $v !== 'N/A')<div class="info"><div class="l">{{ $l }}</div><div class="v" title="{{ $v }}">{{ $v }}</div></div>@endif @endforeach</div>
        </div>
        <div class="panel rv" style="--d:1">
            <div class="ph"><h3><i class="fe fe-droplet"></i> Fluid properties</h3></div>
            @php $has = array_filter($props, fn ($p) => $g($p[1]) !== null); @endphp
            @if(count($has))<div class="props">@foreach($has as [$l, $k, $d, $u])<div><small>{{ $l }}</small><b>{{ $f($g($k), $d) }}</b><span>{{ $u }}</span></div>@endforeach</div>@else<div class="empty">No PVT properties in this log</div>@endif
            @php $hc = array_filter($cums, fn ($c) => $g($c[1]) !== null); $h2 = array_filter(['avg' => $g('h2s_avg'), 'max' => $g('h2s_max'), 'min' => $g('h2s_min')], fn ($v) => $v !== null); @endphp
            @if(count($hc))<div class="ph" style="margin:18px 0 10px"><h3 style="font-size:13px">Cumulative volumes</h3></div><div class="props">@foreach($hc as [$l, $k, $u])<div><small>{{ $l }}</small><b>{{ number_format($g($k), 0) }}</b><span>{{ $u }}</span></div>@endforeach</div>@endif
            @if(count($h2))<div class="ph" style="margin:18px 0 10px"><h3 style="font-size:13px">H₂S</h3></div><div class="props">@foreach($h2 as $k => $v)<div><small>{{ $k }}</small><b>{{ $f($v, 2) }}</b><span>ppm</span></div>@endforeach</div>@endif
        </div>
    </div>

    {{-- CHARTS --}}
    <div class="sec">
        <div class="ph" style="margin:0 0 0"><h3><i class="fe fe-bar-chart-2"></i> Test curves</h3><span class="sub">Scroll or pinch to zoom · drag to pan · each chart has its own controls</span></div>
        <div class="cc">
            @foreach([
                ['rates', 'Production rates over time', 'fe-trending-up', true],
                ['gor', 'Gas-oil ratio (GOR)', 'fe-bar-chart-2', false],
                ['bsw', 'Basic sediment & water (BSW)', 'fe-droplet', false],
                ['wlr', 'Water-liquid ratio (WLR)', 'fe-activity', false],
                ['pressure', 'Pressure', 'fe-activity', false],
                ['temp', 'Temperature', 'fe-thermometer', false],
            ] as $i => [$k, $t, $ic, $units])
                <div class="panel rv" style="--d:{{ $i % 2 }}">
                    <div class="ph"><h3><i class="fe {{ $ic }}"></i> {{ $t }}</h3>
                        <div class="ctl">
                            @if($units)<select class="unit-selector" data-chart="rates" aria-label="Units"><option value="stb">STB/d</option><option value="m3">m³/d</option><option value="bbl">BBL/d</option></select>@endif
                            <button type="button" data-z="{{ $k }}:in" title="Zoom in"><i class="fe fe-zoom-in"></i></button><button type="button" data-z="{{ $k }}:out" title="Zoom out"><i class="fe fe-zoom-out"></i></button><button type="button" data-z="{{ $k }}:reset" title="Reset"><i class="fe fe-rotate-ccw"></i></button>
                        </div></div>
                    <div class="chart" id="box_{{ $k }}"><canvas id="{{ $k }}Chart"></canvas></div>
                </div>
            @endforeach
        </div>
        <div class="panel rv" style="margin-top:18px">
            <div class="ph"><h3><i class="fe fe-wind"></i> Gas void fraction (GVF)</h3>
                <div class="ctl"><button type="button" data-z="gvf:in"><i class="fe fe-zoom-in"></i></button><button type="button" data-z="gvf:out"><i class="fe fe-zoom-out"></i></button><button type="button" data-z="gvf:reset"><i class="fe fe-rotate-ccw"></i></button></div></div>
            <div class="chart" id="box_gvf"><canvas id="gvfChart"></canvas></div>
        </div>
    </div>

    {{-- AVERAGE ROW --}}
    @if(!empty($full_average_row))
    <div class="sec"><div class="panel rv">
        <div class="ph"><h3><i class="fe fe-database"></i> Complete average metrics</h3><span class="srch"><i class="fe fe-search"></i><input type="text" id="avgQ" placeholder="Filter metrics…" autocomplete="off"></span></div>
        <div class="avg" id="avgBox">@foreach($full_average_row as $col => $value)<div data-k="{{ strtolower($col) }}"><small>{{ $col }}</small><b>{{ is_numeric($value) ? number_format((float) $value, 2) : $value }}</b></div>@endforeach</div>
    </div></div>
    @endif

    {{-- SAVE --}}
    <div id="saveSec">
        @if($hasTask)<div class="tinfo rv"><i class="fe fe-info"></i><div><b style="color:#fff">Task completion</b><br>Saving this test marks your assigned task as completed and notifies the supervisor.</div></div>@endif
        <div class="save rv">
            <svg viewBox="0 0 96 96" aria-hidden="true"><path d="M20,16 H62 L78,32 V80 H20 Z" fill="#0b1426" stroke="#34d399" stroke-width="2.6" stroke-linejoin="round"/><path d="M62,16 V32 H78" fill="none" stroke="#34d399" stroke-width="2.6"/><rect x="32" y="52" width="32" height="28" rx="3" fill="rgba(52,211,153,.18)" stroke="#34d399" stroke-width="2"/><rect x="38" y="56" width="16" height="9" rx="1.5" fill="#34d399"/></svg>
            <div><h3>{{ $hasTask ? 'Complete task & save test' : 'Save test to archive' }}</h3><p>Everything looks right? Saving stores the metrics, the curves and the original log so the test can be reviewed, compared with the well testing and tracked over time.</p></div>
            <form action="{{ route('tests.store') }}" method="POST" class="go">
                @csrf
                <input type="hidden" name="summary" value="{{ json_encode($summary) }}">
                <input type="hidden" name="averages" value="{{ json_encode($averages) }}">
                <input type="hidden" name="parsed_data" value="{{ json_encode($parsed_data) }}">
                <input type="hidden" name="temp_file_path" value="{{ $temp_file_path }}">
                <input type="hidden" name="filename" value="{{ $filename }}">
                <input type="hidden" name="operator" value="{{ $summary['operator'] ?? 'N/A' }}">
                <input type="hidden" name="test_date" value="{{ $summary['test_date'] ?? 'N/A' }}">
                <input type="hidden" name="test_type" value="{{ $summary['test_type'] ?? 'N/A' }}">
                <input type="hidden" name="full_average_row" value="{{ json_encode($full_average_row) }}">
                @if(isset($task_id) && $task_id)<input type="hidden" name="task_id" value="{{ $task_id }}">@endif
                @if(isset($new_task_id) && $new_task_id)<input type="hidden" name="new_task_id" value="{{ $new_task_id }}">@endif
                <a class="btn2" href="{{ route('upload.form') }}"><i class="fe fe-x"></i> Discard</a>
                <button type="submit" class="btn2 g big"><i class="fe fe-save"></i> {{ $hasTask ? 'Complete task & save test' : 'Save test to archive' }}</button>
            </form>
        </div>
    </div>

    {{-- LOG TABLE --}}
    <div class="sec"><div class="panel rv">
        <div class="ph"><h3><i class="fe fe-list"></i> Detailed VX log data</h3>
            <span style="display:flex;gap:10px;align-items:center;flex-wrap:wrap"><span class="sub" id="rowInfo"></span><span class="srch"><i class="fe fe-search"></i><input type="text" id="rowQ" placeholder="Search log data…" autocomplete="off"></span><button type="button" class="btn2" id="csvBtn"><i class="fe fe-download"></i> CSV</button></span></div>
        <div class="dtw"><table id="vxDetailTable"><thead></thead><tbody></tbody></table></div>
        <div class="pager"><button type="button" id="pPrev">‹ Prev</button><span id="pInfo"></span><button type="button" id="pNext">Next ›</button><span class="sp"></span><label>Rows <select id="pSize" style="background:#0b1426;color:#fff;border:1px solid var(--line);border-radius:8px;padding:3px 8px"><option>25</option><option>50</option><option>100</option><option value="0">All</option></select></label></div>
    </div></div>

    {{-- RAW --}}
    <div class="sec"><div class="panel rv">
        <details class="raw"><summary><i class="fe fe-file-text"></i> Raw log file <span>{{ $filename }}</span></summary>
            <pre>{{ $raw_log_content }}</pre></details>
    </div></div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/hammerjs@2.0.8"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@2.0.1/dist/chartjs-plugin-zoom.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var root = document.getElementById('ofp');
    var reduce = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;
    var ratesData = @json($rates_chart_data), gorData = @json($gor_chart_data), parsed = @json($parsed_data);
    var test = (parsed && parsed.test_data) || {}, rows = test.rows || [], columns = test.columns || [];

    // ---------- charts (Chart.js, dark theme) ----------
    var charts = {};
    if (window.Chart) {
        Chart.defaults.color = '#9fb0cf'; Chart.defaults.borderColor = 'rgba(255,255,255,.07)'; Chart.defaults.font.family = "'Inter',sans-serif";
    }
    function base(ytitle) {
        return {
            responsive: true, maintainAspectRatio: false, interaction: { intersect: false, mode: 'index' }, animation: { duration: reduce ? 0 : 900 },
            elements: { point: { radius: 0, hoverRadius: 4 } },
            plugins: {
                legend: { position: 'bottom', labels: { usePointStyle: true, padding: 14, font: { size: 11, weight: '600' } } },
                tooltip: { backgroundColor: '#0b1426', borderColor: 'rgba(96,165,250,.3)', borderWidth: 1, padding: 12, cornerRadius: 10, titleColor: '#fff', bodyColor: '#cbd5e1' },
                zoom: { zoom: { wheel: { enabled: true, speed: .1 }, pinch: { enabled: true }, mode: 'xy' }, pan: { enabled: true, mode: 'xy' }, limits: { x: { min: 'original', max: 'original' }, y: { min: 'original', max: 'original' } } }
            },
            scales: { x: { grid: { display: false }, ticks: { font: { size: 10 }, maxTicksLimit: 8 } }, y: { beginAtZero: true, title: { display: true, text: ytitle, color: '#9fb0cf', font: { size: 11, weight: '600' } }, ticks: { font: { size: 10 } } } }
        };
    }
    function none(k, msg) { var b = document.getElementById('box_' + k); if (b) b.innerHTML = '<div class="nd"><span><i class="fe fe-alert-circle"></i> ' + msg + '</span></div>'; }
    function make(k, cfg, ytitle) {
        var el = document.getElementById(k + 'Chart'); if (!el || !window.Chart) return;
        charts[k] = new Chart(el.getContext('2d'), { type: 'line', data: cfg, options: base(ytitle) });
    }
    function colData(names) {
        var found = null;
        for (var i = 0; i < names.length && !found; i++) if (columns.indexOf(names[i]) > -1) found = names[i];
        for (var j = 0; j < names.length && !found; j++) found = columns.filter(function (c) { return c.toLowerCase().indexOf(names[j].toLowerCase()) > -1 || names[j].toLowerCase().indexOf(c.toLowerCase()) > -1; })[0];
        if (!found) return [];
        return rows.map(function (r) { var v = r[found]; return v !== undefined && v !== null && v !== '' ? parseFloat(v) : null; }).filter(function (v) { return v !== null && !isNaN(v); });
    }
    function labels(n) { var tc = columns.filter(function (c) { return c.toLowerCase().indexOf('time') > -1; })[0]; return tc ? rows.slice(0, n).map(function (r) { return r[tc] || ''; }) : Array.from({ length: n }, function (_, i) { return 'Point ' + (i + 1); }); }
    function single(k, names, label, color, title) {
        var d = colData(names); if (!d.length) return none(k, 'No ' + label + ' data in the log file');
        make(k, { labels: labels(d.length), datasets: [{ label: label, data: d, borderColor: color, backgroundColor: color + '22', borderWidth: 2, tension: .35, fill: true }] }, title);
    }
    if (ratesData && ratesData.labels) { ratesData.datasets.forEach(function (d) { d.originalData = d.data.slice(); d.borderWidth = 2; d.tension = .3; d.fill = false; }); make('rates', ratesData, 'Rate'); } else none('rates', 'No rate data');
    if (gorData && gorData.labels) { gorData.datasets.forEach(function (d) { d.borderWidth = 2; d.tension = .3; d.fill = true; d.backgroundColor = 'rgba(52,211,153,.12)'; d.borderColor = '#34d399'; }); make('gor', gorData, 'GOR (Scf/STB)'); } else none('gor', 'No GOR data');
    single('bsw', ['BSW', 'BS&W', 'Basic Sediment & Water', 'BS_W', 'BSWC'], 'BSW (%)', '#a78bfa', 'BSW (%)');
    single('wlr', ['WLR', 'Water-Liquid Ratio', 'Water Liquid Ratio', 'WLR%', 'WLRC'], 'WLR (%)', '#38bdf8', 'WLR (%)');
    single('pressure', ['Pressure', 'WHP', 'Press', 'Wellhead Pressure', 'P', 'WHPC'], 'Pressure (psi)', '#f472b6', 'Pressure (psi)');
    single('temp', ['Temperature', 'Temp', 'T', 'WHT', 'Wellhead Temperature', 'WHTC'], 'Temperature', '#f87171', 'Temperature');
    single('gvf', ['GVF', 'Gas Void Fraction', 'GVF%', 'GVFC'], 'GVF (%)', '#22d3ee', 'GVF (%)');

    root.querySelectorAll('[data-z]').forEach(function (b) {
        b.addEventListener('click', function () {
            var p = b.dataset.z.split(':'), c = charts[p[0]]; if (!c) return;
            if (p[1] === 'in' && c.zoom) c.zoom(1.2); else if (p[1] === 'out' && c.zoom) c.zoom(.8); else if (c.resetZoom) c.resetZoom();
        });
    });
    var conv = { stb: { f: 1, l: 'STB/d', g: 'MMScf/d' }, m3: { f: 0.158987, l: 'm³/d', g: 'Mm³/d' }, bbl: { f: 1, l: 'BBL/d', g: 'MMScf/d' } };
    root.querySelectorAll('.unit-selector').forEach(function (sel) {
        sel.addEventListener('change', function () {
            var c = charts[sel.dataset.chart], u = conv[sel.value]; if (!c) return;
            c.data.datasets.forEach(function (d) {
                var lb = d.label || '';
                if (!d.originalData || !/Oil|Water|Gas Rate/.test(lb)) return;
                d.data = d.originalData.map(function (v) { if (v === null || v === undefined) return v; return /Gas/.test(lb) ? (sel.value === 'm3' ? v * 28.317 : v) : v * u.f; });
                d.label = /Gas/.test(lb) ? 'Gas Rate (' + u.g + ')' : (/Oil/.test(lb) ? 'Oil Rate (' + u.l + ')' : 'Water Rate (' + u.l + ')');
            });
            if (c.options.scales.y.title) c.options.scales.y.title.text = u.l;
            c.update();
        });
    });

    // ---------- log table (paged, searchable) ----------
    var tbl = document.getElementById('vxDetailTable'), page = 0, size = 25, q = '';
    tbl.tHead.innerHTML = '<tr>' + columns.map(function (c) { return '<th>' + String(c).replace(/</g, '&lt;') + '</th>'; }).join('') + '</tr>';
    function cls(col, v) {
        var n = parseFloat(v); if (isNaN(n)) return '';
        var c = col.toLowerCase();
        if (c.indexOf('whp') > -1 && n < 400) return 'bad';
        if (c.indexOf('temp') > -1 && n > 200) return 'warn';
        if (c.indexOf('press') > -1 && n < 200) return 'bad';
        if (c.indexOf('voltage') > -1 && (n < 100 || n > 250)) return 'warn';
        return '';
    }
    function filtered() { if (!q) return rows; return rows.filter(function (r) { return columns.some(function (c) { return String(r[c] === undefined ? '' : r[c]).toLowerCase().indexOf(q) > -1; }); }); }
    function draw() {
        var list = filtered(), total = list.length, pages = size ? Math.max(1, Math.ceil(total / size)) : 1;
        page = Math.min(page, pages - 1);
        var part = size ? list.slice(page * size, page * size + size) : list;
        tbl.tBodies[0].innerHTML = part.map(function (r) { return '<tr>' + columns.map(function (c) { var v = r[c] === undefined || r[c] === null ? '' : r[c]; return '<td class="' + cls(c, v) + '">' + String(v).replace(/</g, '&lt;') + '</td>'; }).join('') + '</tr>'; }).join('');
        document.getElementById('pInfo').textContent = total ? 'Page ' + (page + 1) + ' of ' + pages : 'No rows';
        document.getElementById('rowInfo').textContent = total + ' of ' + rows.length + ' rows';
        document.getElementById('pPrev').disabled = page <= 0; document.getElementById('pNext').disabled = page >= pages - 1;
    }
    document.getElementById('pPrev').addEventListener('click', function () { page--; draw(); });
    document.getElementById('pNext').addEventListener('click', function () { page++; draw(); });
    document.getElementById('pSize').addEventListener('change', function () { size = parseInt(this.value, 10); page = 0; draw(); });
    document.getElementById('rowQ').addEventListener('input', function () { q = this.value.toLowerCase(); page = 0; draw(); });
    document.getElementById('csvBtn').addEventListener('click', function () {
        var esc = function (v) { return '"' + String(v === undefined || v === null ? '' : v).replace(/"/g, '""') + '"'; };
        var lines = [columns.map(esc).join(',')].concat(filtered().map(function (r) { return columns.map(function (c) { return esc(r[c]); }).join(','); }));
        var a = document.createElement('a'); a.href = URL.createObjectURL(new Blob([lines.join('\n')], { type: 'text/csv' })); a.download = 'vx-log-data.csv'; a.click();
    });
    draw();

    var aq = document.getElementById('avgQ');
    if (aq) aq.addEventListener('input', function () { var s = aq.value.toLowerCase(); document.querySelectorAll('#avgBox > div').forEach(function (d) { d.style.display = !s || d.dataset.k.indexOf(s) > -1 ? '' : 'none'; }); });

    // ---------- reveal + count-up ----------
    function fmt(v, d) { return v.toLocaleString('en-US', { minimumFractionDigits: d, maximumFractionDigits: d }); }
    function countUp(el) {
        var to = parseFloat(el.dataset.to) || 0, d = parseInt(el.dataset.dec || 0, 10), t0 = null; if (reduce) return;
        function step(t) { if (t0 === null) t0 = t; var p = Math.min((t - t0) / 1400, 1), e = 1 - Math.pow(1 - p, 4); el.textContent = fmt(to * e, d); if (p < 1) requestAnimationFrame(step); else el.textContent = fmt(to, d); }
        requestAnimationFrame(step);
    }
    var io = new IntersectionObserver(function (es) { es.forEach(function (e) { if (!e.isIntersecting) return; e.target.classList.add('in'); e.target.querySelectorAll('.cu').forEach(countUp); io.unobserve(e.target); }); }, { threshold: .08 });
    root.querySelectorAll('.rv').forEach(function (el) { io.observe(el); });
    if (reduce) root.querySelectorAll('svg').forEach(function (s) { if (s.pauseAnimations) s.pauseAnimations(); });
});
</script>
@endsection
