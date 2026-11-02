@extends('layouts.master')
@use('App\Support\MpfmGlossary', 'G')

@section('title', 'Well Test - ' . ($wellTest->well ?? '#'.$wellTest->id))

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/ofpage.css') }}">
<style>
.ofp .ph .hq { margin-left:8px; }
.ofp .kpi .l .hq { margin-left:6px; }
.ofp .info .l { align-items:center; }
.ofp .donut { width:170px; height:170px; flex:none; } .ofp .donut .arc { stroke-width:14; }
.ofp .dw { display:flex; align-items:center; gap:22px; flex-wrap:wrap; justify-content:center; margin-bottom:22px; }
.ofp .props { display:grid; grid-template-columns:repeat(auto-fit,minmax(150px,1fr)); gap:10px; }
.ofp .props > div { padding:11px 13px; border-radius:14px; background:var(--glass); border:1px solid var(--line); }
.ofp .props small { display:flex; align-items:center; font-size:9.5px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--mute); }
.ofp .props b { font:700 16px 'Space Grotesk'; color:#fff; } .ofp .props span.u { font-size:10.5px; color:var(--sub); margin-left:4px; }
.ofp .sub2 { margin:22px 0 10px; font-size:12px; font-weight:700; letter-spacing:.12em; text-transform:uppercase; color:var(--sub); display:flex; align-items:center; }
.ofp .sf { display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:12px; }
.ofp .sf > div { padding:14px 16px; border-radius:16px; border:1px solid rgba(251,191,36,.4); background:rgba(251,191,36,.07); }
.ofp .sf b { display:block; font:800 24px 'Space Grotesk'; color:var(--amber); } .ofp .sf small { color:#fde68a; font-size:12px; display:flex; align-items:center; } .ofp .sf em { font-style:normal; display:block; margin-top:3px; font-size:11.5px; color:var(--sub); }
.ofp .chk { width:100%; border-collapse:separate; border-spacing:0 6px; min-width:560px; }
.ofp .chk th { font-size:10.5px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--mute); padding:2px 12px; text-align:left; }
.ofp .chk td { padding:10px 12px; background:#0c1528; border-top:1px solid var(--line); border-bottom:1px solid var(--line); font-size:13px; }
.ofp .chk td:first-child { border-left:1px solid var(--line); border-radius:12px 0 0 12px; font-weight:700; color:#fff; } .ofp .chk td:last-child { border-right:1px solid var(--line); border-radius:0 12px 12px 0; }
.ofp .mx { display:grid; grid-template-columns:repeat(auto-fill,minmax(210px,1fr)); gap:10px; }
.ofp .mx > div { padding:11px 13px; border-radius:14px; background:var(--glass); border:1px solid var(--line); }
.ofp .mx small { display:flex; align-items:center; font-size:10.5px; color:var(--sub); font-weight:600; } .ofp .mx b { display:block; font:700 15px 'Space Grotesk'; color:#fff; margin-top:3px; word-break:break-word; } .ofp .mx code { font-size:10px; color:var(--mute); }
.ofp .exp { display:grid; grid-template-columns:1fr 1.7fr; gap:18px; align-items:start; }
.ofp .plist { max-height:520px; overflow:auto; border-radius:14px; border:1px solid var(--line); scrollbar-width:thin; scrollbar-color:rgba(96,165,250,.35) transparent; }
.ofp .plist table { width:100%; border-collapse:collapse; font-size:12px; }
.ofp .plist th { position:sticky; top:0; background:#0b1426; color:var(--cyan); padding:8px 10px; text-align:left; font-size:10px; letter-spacing:.08em; text-transform:uppercase; z-index:1; border-bottom:1px solid var(--line); }
.ofp .plist td { padding:7px 10px; border-bottom:1px solid rgba(255,255,255,.05); white-space:nowrap; color:var(--ink); }
.ofp .plist td.n { font-family:'Space Grotesk'; font-weight:600; text-align:right; }
.ofp .plist tr { cursor:pointer; transition:background .15s; } .ofp .plist tr:hover td { background:rgba(34,211,238,.07); } .ofp .plist tr.cur td { background:rgba(34,211,238,.14); }
.ofp .plist td.p { max-width:210px; overflow:hidden; text-overflow:ellipsis; }
.ofp .plist td.p small { display:block; color:var(--mute); font-size:10px; }
.ofp .sel-h { display:flex; align-items:center; gap:10px; flex-wrap:wrap; margin-bottom:6px; }
.ofp .sel-h b { font:800 17px 'Space Grotesk'; color:#fff; } .ofp .sel-h small { color:var(--sub); }
.ofp .stt { display:grid; grid-template-columns:repeat(4,1fr); gap:10px; margin:12px 0; }
.ofp .stt > div { padding:10px 12px; border-radius:12px; background:var(--glass); border:1px solid var(--line); text-align:center; } .ofp .stt small { display:block; font-size:9.5px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--mute); } .ofp .stt b { font:700 15px 'Space Grotesk'; color:#fff; }
.ofp .dtw { max-height:460px; overflow:auto; border-radius:14px; border:1px solid var(--line); scrollbar-width:thin; scrollbar-color:rgba(96,165,250,.35) transparent; }
.ofp .dtw table { border-collapse:collapse; width:100%; font-size:12px; }
.ofp .dtw th { position:sticky; top:0; background:#0b1426; color:var(--cyan); padding:9px 12px; text-align:left; font-size:10.5px; letter-spacing:.05em; white-space:nowrap; border-bottom:1px solid var(--line); z-index:1; }
.ofp .dtw td { padding:7px 12px; white-space:nowrap; border-bottom:1px solid rgba(255,255,255,.05); color:var(--ink); } .ofp .dtw tr:hover td { background:rgba(34,211,238,.07); }
.ofp .pager { display:flex; align-items:center; gap:10px; margin-top:12px; flex-wrap:wrap; font-size:12.5px; color:var(--sub); } .ofp .pager .sp { flex:1; }
.ofp .pager button { padding:6px 14px; border-radius:50px; border:1px solid var(--line); background:var(--glass); color:var(--ink); font:600 12px 'Inter'; cursor:pointer; } .ofp .pager button:disabled { opacity:.4; cursor:default; }
.ofp .note2 { margin-bottom:14px; padding:11px 15px; border-radius:14px; border:1px solid rgba(34,211,238,.3); background:rgba(34,211,238,.06); color:#a5f3fc; font-size:12.5px; display:flex; gap:10px; align-items:center; }
.ofp .kpis.k7 { grid-template-columns:repeat(7,1fr); }
.ofp .kpis.k7 .kpi { padding:14px 12px; } .ofp .kpis.k7 .kpi .l { min-height:30px; }
@media (max-width:1400px) { .ofp .kpis.k7 { grid-template-columns:repeat(4,1fr); } }
@media (max-width:700px) { .ofp .kpis.k7 { grid-template-columns:repeat(2,1fr); } }
@media (max-width:1100px) { .ofp .exp { grid-template-columns:1fr; } .ofp .stt { grid-template-columns:repeat(2,1fr); } }
@media print { .ofp .actions { display:none !important; } .ofp .rv { opacity:1 !important; transform:none !important; } }
</style>
@endsection

@section('content')
@php
    $f = fn ($v, $d = 2) => is_numeric($v) ? number_format((float) $v, $d) : '—';
    $hq = function (array $g) { return '<span class="hq" tabindex="0" role="button" aria-label="What is '.e($g[0]).'?" data-h="'.e($g[0]).'" data-t="'.e($g[1]).'" data-u="'.e($g[2]).'">?</span>'; };
    $hm = fn ($k) => $hq(G::metric($k));
    $hf = fn ($k) => $hq(G::field($k));

    $M = $metrics ?? [];
    $g = fn ($k) => isset($M[$k]) && is_numeric($M[$k]) ? (float) $M[$k] : null;
    $oil = $g('oil_rate'); $gas = $g('gas_rate'); $wrate = $g('water_rate'); $gor = $g('gor'); $bsw = $g('bsw'); $wlr = $g('wlr'); $gvf = $g('gvf');
    $pAvg = $g('avg_pressure'); $pMin = $g('min_pressure'); $pMax = $g('max_pressure');
    $tAvg = $g('avg_temperature'); $tMin = $g('min_temperature'); $tMax = $g('max_temperature');
    $tUnit = '°C';
    foreach (($columns ?? []) as $c) { if (stripos($c, 'temp') !== false && preg_match('/\[(.*?)\]/', $c, $mm)) { $tUnit = $mm[1]; break; } }
    $nice = function ($v) { $v = max(($v ?? 0) * 1.25, 1); $mag = 10 ** floor(log10($v)); foreach ([1, 2, 2.5, 5, 10] as $s) { if ($v <= $s * $mag) return $s * $mag; } return 10 * $mag; };
    $pScale = $nice($pMax ?? $pAvg); $tScale = $nice($tMax ?? $tAvg);
    $gasF = $gvf !== null ? min(max($gvf, 0), 100) / 100 : 0;
    $waterF = (1 - $gasF) * (min(max($wlr ?? $bsw ?? 0, 0), 100) / 100);
    $oilF = max(1 - $gasF - $waterF, 0);

    $start = $wellTest->start_time; $end = $wellTest->end_time;
    $mins = ($start && $end) ? (int) round(abs($end->diffInMinutes($start))) : null;
    $duration = $mins !== null ? intdiv($mins, 60).'h '.($mins % 60).'m' : null;
    $nRows = count($rows ?? []); $nCols = count($columns ?? []);
    $meta = $wellTest->metadata ?? [];
    $metaKeys = ['Log File Version', 'Vx DAFC version', 'File created:', 'DAFC Serial Number', 'DAFC HW Version', 'DAFC SW Version', 'DAFC FW Version', 'GD Serial Number', 'Configuration name', 'Mode'];

    $details = [
        ['client', 'Client', $wellTest->client], ['field', 'Oil field', $wellTest->field], ['well', 'Well', $wellTest->well],
        ['wellhead_tower', 'Wellhead tower', $wellTest->wellhead_tower], ['wellhead_string', 'Wellhead string', $wellTest->wellhead_string],
        ['operator', 'Operator', $wellTest->operator], ['test_date', 'Test date', $wellTest->test_date ? $wellTest->test_date->format('Y-m-d') : null], ['test_type', 'Test type', $wellTest->test_type ? ucfirst(str_replace('-', ' ', $wellTest->test_type)) : null],
        ['choke_size', 'Choke size', $wellTest->choke_size], ['upstream_choke', 'Upstream choke', $wellTest->upstream_choke], ['downstream_choke', 'Downstream choke', $wellTest->downstream_choke],
        ['start_time', 'Start time', $start ? $start->format('Y-m-d H:i:s') : null], ['end_time', 'End time', $end ? $end->format('Y-m-d H:i:s') : null], ['duration', 'Duration', $duration],
        ['rows', 'Readings', number_format($nRows)], ['columns', 'Logged parameters', $nCols], ['filename', 'Log file', $wellTest->filename], ['created_at', 'Uploaded', $wellTest->created_at ? $wellTest->created_at->format('Y-m-d H:i') : null],
    ];

    $props = [['bo', 'Bo', 4, 'Sm³/m³'], ['bw', 'Bw', 4, 'Sm³/m³'], ['bg', 'Bg', 4, 'Sm³/m³'], ['z_factor', 'Z factor', 4, ''], ['rst', 'Rs', 2, 'Scf/STB'], ['po', 'ρ oil', 2, 'kg/m³'], ['pw', 'ρ water', 2, 'kg/m³'], ['pg', 'ρ gas', 4, 'kg/m³']];
    $cums = [['cumulative_oil_volume', 'Cumulative oil', 'bbl'], ['cumulative_water_volume', 'Cumulative water', 'bbl'], ['cumulative_gas_volume', 'Cumulative gas', 'Scf']];
    $h2 = [['h2s_avg', 'Average'], ['h2s_max', 'Maximum'], ['h2s_min', 'Minimum']];
    $cross = [['GOR', 'gor', 'gor_formula', 0, ''], ['BSW', 'bsw', 'bsw_formula', 2, '%'], ['WLR', 'wlr', 'wlr_formula', 2, '%'], ['GVF', 'gvf', 'gvf_formula', 2, '%']];

    $flagged = [];
    foreach (($M['safety_issues'] ?? []) as $grp => $items) {
        foreach ((array) $items as $it) { $col = $it['column'] ?? '?'; $flagged[$col]['n'] = ($flagged[$col]['n'] ?? 0) + 1; $flagged[$col]['v'][] = $it['value'] ?? null; $flagged[$col]['g'] = $grp; }
    }

    $colHelp = [];
    foreach (($columns ?? []) as $c) { $colHelp[$c] = G::column($c); }
    $metricRows = [];
    foreach ($M as $k => $v) { if ($k === 'safety_issues') continue; $metricRows[] = [$k, G::metric($k), is_numeric($v) ? number_format((float) $v, 4) : (is_scalar($v) ? (string) $v : json_encode($v))]; }
@endphp

<div class="ofp" id="ofp">
    <div class="bgl"><div class="orb o1"></div><div class="orb o2"></div></div>

    {{-- HERO --}}
    <div class="hero rv">
        <div class="who">
            <span class="lg"></span>
            <div>
                <div class="eyebrow"><i></i> Well test #{{ $wellTest->id }}</div>
                <h1>{{ $wellTest->well ?: 'Well test' }} <em>· {{ $wellTest->wellhead_string ?: ($wellTest->wellhead_tower ?: '') }}</em></h1>
                <div class="meta">
                    <span>{{ $wellTest->client }} · {{ $wellTest->field }}</span>
                    @if($wellTest->test_type)<span class="chip {{ $wellTest->test_type === 'flow' ? 'ok' : ($wellTest->test_type === 'cleanup' ? 'warn' : 'info') }}">{{ str_replace('-', ' ', $wellTest->test_type) }}</span>@endif
                    <span><i class="fe fe-calendar"></i> {{ $start ? $start->format('d M Y H:i') : ($wellTest->test_date ? $wellTest->test_date->format('d M Y') : 'N/A') }}{{ $end ? ' → '.$end->format('H:i') : '' }}</span>
                    @if($duration)<span><i class="fe fe-clock"></i> {{ $duration }}</span>@endif
                    <span><i class="fe fe-database"></i> {{ number_format($nRows) }} readings · {{ $nCols }} parameters</span>
                </div>
            </div>
        </div>
        <div class="actions">
            <a class="btn2 p" href="{{ route('well_tests.print', $wellTest->id) }}?auto=1" target="_blank" rel="noopener" title="Open a formal A4 report — choose “Save as PDF” to attach it to an email"><i class="fe fe-printer"></i> Print report</a>
            <a class="btn2 g" href="{{ route('well_tests.compare') }}"><i class="fe fe-shuffle"></i> Compare tests</a>
            <a class="btn2" href="{{ route('well_tests.index') }}"><i class="fe fe-arrow-left"></i> Archive</a>
        </div>
    </div>

    <div class="note2 rv" style="margin-top:14px"><i class="fe fe-help-circle"></i><span>Every number on this page has a small <span class="hq" style="margin:0 4px;opacity:1">?</span> next to it — hover or tap it for a plain-language explanation and the unit.</span></div>

    {{-- KPIs --}}
    <div class="kpis k7">
        @foreach([
            ['oil_rate', $oil, 'STB/d', 'fe-droplet', '#fbbf24', 2],
            ['gas_rate', $gas, 'MMScf/d', 'fe-wind', '#22d3ee', 2],
            ['water_rate', $wrate, 'STB/d', 'fe-cloud-drizzle', '#38bdf8', 2],
            ['gor', $gor, 'Scf/STB', 'fe-layers', '#34d399', 0],
            ['gvf', $gvf, '%', 'fe-percent', '#a78bfa', 2],
            ['bsw', $bsw, '%', 'fe-activity', '#f87171', 2],
            ['wlr', $wlr, '%', 'fe-droplet', '#7dd3fc', 2],
        ] as $i => [$k, $val, $un, $ic, $c, $d])
            <div class="kpi rv" style="--d:{{ $i % 4 + 1 }};--c:{{ $c }}"><div class="ic"><i class="fe {{ $ic }}"></i></div><div class="l">{{ G::metric($k)[0] }} {!! $hm($k) !!}</div>
                @if($val !== null)<div class="v cu" data-to="{{ $val }}" data-dec="{{ $d }}">{{ $f($val, $d) }}</div>@else<div class="v">N/A</div>@endif<div class="u">{{ $un }}</div></div>
        @endforeach
    </div>

    {{-- DETAILS + META --}}
    <div class="sec g21">
        <div class="panel rv">
            <div class="ph"><h3><i class="fe fe-info"></i> Test details</h3></div>
            <div class="infog">@foreach($details as [$k, $l, $v])@if(filled($v) && $v !== 'N/A')<div class="info"><div class="l">{{ $l }} {!! $hf($k) !!}</div><div class="v" title="{{ $v }}">{{ $v }}</div></div>@endif @endforeach</div>
        </div>
        <div class="panel rv" style="--d:1">
            <div class="ph"><h3><i class="fe fe-cpu"></i> Meter &amp; log header</h3></div>
            <div class="infog" style="grid-template-columns:1fr 1fr">
                @foreach($metaKeys as $k)@if(isset($meta[$k]) && filled($meta[$k]))<div class="info"><div class="l">{{ G::field($k)[0] }} {!! $hf($k) !!}</div><div class="v" title="{{ $meta[$k] }}">{{ $meta[$k] }}</div></div>@endif @endforeach
            </div>
            @if(empty($meta))<div class="empty">No header information was stored with this log.</div>@endif
        </div>
    </div>

    {{-- METER + PIPE --}}
    <div class="sec g21">
        <div class="panel rv">
            <div class="ph"><h3><i class="fe fe-cpu"></i> Multiphase flow meter</h3><span class="sub">Averages over the whole test</span></div>
            <div class="scroll"><div class="fit">@include('partials.mpfm_meter', ['oil' => $oil, 'gas' => $gas, 'wrate' => $wrate, 'pAvg' => $pAvg, 'tAvg' => $tAvg, 'pScale' => $pScale, 'tScale' => $tScale, 'tUnit' => $tUnit])</div></div>
        </div>
        <div class="panel rv" style="--d:1">
            <div class="ph"><h3><i class="fe fe-pie-chart"></i> In the pipe {!! $hq(['Phase fractions', 'How the flowing volume splits between gas, oil and water inside the pipe. Gas share is the GVF; the liquid share is split using the water-liquid ratio.', '% of flow volume']) !!}</h3></div>
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
                @foreach([['Pressure', 'pressure', $pMin, $pAvg, $pMax, $pScale, 'psi', 'min_pressure', 'avg_pressure', 'max_pressure'], ['Temperature', 'temperature', $tMin, $tAvg, $tMax, $tScale, $tUnit, 'min_temperature', 'avg_temperature', 'max_temperature']] as [$lbl, $kk, $mn, $av, $mx, $sc, $un, $kmn, $kav, $kmx])
                    @if($av !== null)
                        @php $lo = $mn ?? $av; $hi = $mx ?? $av; @endphp
                        <div><div class="h"><span>{{ $lbl }} {!! $hm($kk) !!}</span><b>{{ $f($av, 1) }} {{ $un }}</b></div>
                            <div class="tr"><i style="left:{{ round($lo / $sc * 100, 1) }}%;--w:{{ max(round(($hi - $lo) / $sc * 100, 1), 1.5) }}%"></i><em style="left:calc({{ round($av / $sc * 100, 1) }}% - 2px)"></em></div>
                            <div class="s"><span>min {{ $f($lo, 1) }} {!! $hm($kmn) !!}</span><span>avg {{ $f($av, 1) }} {!! $hm($kav) !!}</span><span>max {{ $f($hi, 1) }} {!! $hm($kmx) !!}</span></div></div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    {{-- PVT / CUMULATIVE / H2S --}}
    <div class="sec g2">
        <div class="panel rv">
            <div class="ph"><h3><i class="fe fe-droplet"></i> Fluid properties &amp; PVT</h3><span class="sub">At the test conditions</span></div>
            @php $has = array_filter($props, fn ($p) => $g($p[0]) !== null); @endphp
            @if(count($has))<div class="props">@foreach($has as [$k, $l, $d, $u])<div><small>{{ $l }} {!! $hm($k) !!}</small><b>{{ $f($g($k), $d) }}</b><span class="u">{{ $u }}</span></div>@endforeach</div>@else<div class="empty">No PVT properties in this log</div>@endif
            @php $hc = array_filter($cums, fn ($c) => $g($c[0]) !== null); @endphp
            @if(count($hc))<div class="sub2">Cumulative volumes</div><div class="props">@foreach($hc as [$k, $l, $u])<div><small>{{ $l }} {!! $hm($k) !!}</small><b>{{ number_format($g($k), 0) }}</b><span class="u">{{ $u }}</span></div>@endforeach</div>@endif
            <div class="sub2">Hydrogen sulfide (H₂S)</div>
            <div class="props">@foreach($h2 as [$k, $l])<div><small>{{ $l }} {!! $hm($k) !!}</small><b>{{ isset($M[$k]) && is_numeric($M[$k]) ? $f($M[$k], 2) : 'N/A' }}</b><span class="u">ppm</span></div>@endforeach</div>
        </div>
        <div class="panel rv" style="--d:1">
            <div class="ph"><h3><i class="fe fe-check-circle"></i> Meter value vs recalculated {!! $hq(['Cross-check', 'Each ratio is reported by the meter and also recomputed from the phase rates with a formula. Close agreement means the numbers are consistent; a large gap means one of the rates deserves a second look.', '']) !!}</h3></div>
            <div class="scroll"><table class="chk">
                <thead><tr><th>Quantity</th><th>Meter</th><th>Formula</th><th>Difference</th></tr></thead>
                <tbody>
                @foreach($cross as [$l, $k, $kf, $d, $u])
                    @php $a = $g($k); $b = $g($kf); $df = ($a !== null && $b !== null) ? $a - $b : null; $big = ($a && $b) ? max(abs($a), abs($b)) / max(min(abs($a), abs($b)), 1e-9) : null; $apart = $big !== null && $big > 20; @endphp
                    <tr><td>{{ $l }} {!! $hm($k) !!}</td><td>{{ $f($a, $d) }} {{ $u }}</td><td>{{ $f($b, abs($b ?? 1) < 1 && $b ? 4 : $d) }} {{ $u }} {!! $hm($kf) !!}</td><td>@if($apart)<span class="bd na" title="The two values are in very different ranges, so they are probably not expressed in the same unit">not comparable</span>@elseif($df !== null)<span class="bd {{ abs($df) < ($k === 'gor' ? max(abs($a), 1) * .05 : 1) ? 'ok' : 'warn' }}">{{ $df > 0 ? '+' : '' }}{{ $f($df, $d) }}</span>@else<span class="bd na">n/a</span>@endif</td></tr>
                @endforeach
                </tbody>
            </table></div>
            @if(count($flagged))
                <div class="sub2">Flagged readings {!! $hm('safety_issues') !!}</div>
                <div class="sf">@foreach($flagged as $col => $d)@php $vals = array_filter($d['v'], 'is_numeric'); @endphp<div><small>{{ $col }} {!! $hq(G::column($col)) !!}</small><b>{{ $d['n'] }}</b><em>readings flagged{{ count($vals) ? ' · '.$f(min($vals), 1).' – '.$f(max($vals), 1) : '' }}</em></div>@endforeach</div>
            @endif
        </div>
    </div>

    {{-- CHARTS --}}
    @if(!empty($rates_chart_data) || !empty($gor_chart_data))
    <div class="sec">
        <div class="panel rv"><div class="ph"><h3><i class="fe fe-bar-chart-2"></i> Flow rates during the test {!! $hq(['Flow rates', 'Oil, water and gas flow measured every sampling interval. Oil and water use the left axis, gas the right axis. Rates at line conditions (lc) are the real pipe flow; standard-condition (sc) rates are what gets reported.', '']) !!}</h3><span class="sub">Click a legend chip to hide a series</span></div><div id="chFlow"></div></div>
    </div>
    <div class="sec g2">
        @if(!empty($gor_chart_data))<div class="panel rv"><div class="ph"><h3><i class="fe fe-trending-up"></i> GOR {!! $hm('gor') !!}</h3></div><div id="chGor"></div></div>@endif
        @if(!empty($pressure_temp_chart_data))<div class="panel rv" style="--d:1"><div class="ph"><h3><i class="fe fe-thermometer"></i> Pressure &amp; temperature {!! $hq(['Pressure & temperature', 'How the inlet pressure and the line temperature moved during the test. Large swings can mean unstable flow or a change in choke.', '']) !!}</h3></div><div id="chPt"></div></div>@endif
    </div>
    @endif

    {{-- EXPLORER --}}
    <div class="sec">
        <div class="panel rv">
            <div class="ph"><h3><i class="fe fe-sliders"></i> All logged parameters <span class="sub" style="margin-left:6px">{{ $nCols }} columns</span></h3>
                <span class="srch"><i class="fe fe-search"></i><input type="text" id="pq" placeholder="Filter parameters…" autocomplete="off"></span></div>
            <div class="exp">
                <div class="plist"><table id="ptbl"><thead><tr><th>Parameter</th><th style="text-align:right">Last</th></tr></thead><tbody></tbody></table></div>
                <div>
                    <div class="sel-h"><b id="selName">—</b><span id="selHq"></span><small id="selUnit"></small></div>
                    <div class="stt"><div><small>Min</small><b id="stMin">—</b></div><div><small>Average</small><b id="stAvg">—</b></div><div><small>Max</small><b id="stMax">—</b></div><div><small>Last</small><b id="stLast">—</b></div></div>
                    <div id="chSel"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ALL METRICS --}}
    <div class="sec"><div class="panel rv">
        <div class="ph"><h3><i class="fe fe-database"></i> All calculated metrics <span class="sub" style="margin-left:6px">{{ count($metricRows) }} values</span></h3></div>
        <div class="mx">@foreach($metricRows as [$k, $gl, $v])<div><small>{{ $gl[0] }} {!! $hq($gl) !!}</small><b>{{ $v }}</b><code>{{ $k }}</code></div>@endforeach</div>
    </div></div>

    {{-- RAW --}}
    <div class="sec"><div class="panel rv">
        <div class="ph"><h3><i class="fe fe-list"></i> Raw readings</h3>
            <span style="display:flex;gap:10px;align-items:center;flex-wrap:wrap"><span class="sub" id="rowInfo"></span><span class="srch"><i class="fe fe-search"></i><input type="text" id="rowQ" placeholder="Search readings…" autocomplete="off"></span><button type="button" class="btn2" id="csvBtn"><i class="fe fe-download"></i> CSV</button></span></div>
        <div class="dtw"><table id="rawTbl"><thead></thead><tbody></tbody></table></div>
        <div class="pager"><button type="button" id="pPrev">‹ Prev</button><span id="pInfo"></span><button type="button" id="pNext">Next ›</button><span class="sp"></span><label>Rows <select id="pSize" style="background:#0b1426;color:#fff;border:1px solid var(--line);border-radius:8px;padding:3px 8px"><option>25</option><option>50</option><option>100</option></select></label></div>
    </div></div>
</div>
@endsection

@section('js')
<script src="{{ asset('assets/js/ofcharts.js') }}"></script>
<script src="{{ asset('assets/js/ofhelp.js') }}"></script>
<script>
(function () {
    var root = document.getElementById('ofp');
    var reduce = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;
    var COLS = @json($columns ?? []), ROWS = @json($rows ?? []), HELP = @json($colHelp);
    function esc(s) { return String(s).replace(/[&<>"]/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]; }); }
    function hq(h) { return '<span class="hq" tabindex="0" role="button" data-h="' + esc(h[0]) + '" data-t="' + esc(h[1]) + '" data-u="' + esc(h[2] || '') + '">?</span>'; }
    function num(v) { var n = parseFloat(v); return isNaN(n) ? null : n; }
    function fm(v) { if (v === null || v === undefined || isNaN(v)) return '—'; var a = Math.abs(v); return v.toLocaleString('en-US', { maximumFractionDigits: a >= 1000 ? 0 : a >= 100 ? 1 : a >= 1 ? 2 : 4 }); }

    // ---- charts from the server-built Chart.js configs ----
    var C = { oil: '#fbbf24', water: '#38bdf8', gas: '#22d3ee' };
    function colorFor(n, i) { if (/^qo|oil/i.test(n)) return C.oil; if (/^qw|water/i.test(n)) return C.water; if (/^qg|gas/i.test(n)) return C.gas; return OFCharts.colors[i % OFCharts.colors.length]; }
    function fromCfg(cfg, o) {
        var d = (cfg && cfg.data) || {};
        return Object.assign({ labels: d.labels || [], series: (d.datasets || []).map(function (s, i) { var n = s.label || ('Series ' + (i + 1)); return { name: n, data: (s.data || []).map(num), color: colorFor(n, i), axis: (s.yAxisID === 'y1' || (/^qg|gas/i.test(n) && o.gasRight)) ? 'r' : 'l' }; }) }, o);
    }
    @if(!empty($rates_chart_data)) OFCharts.line(document.getElementById('chFlow'), fromCfg(@json($rates_chart_data), { height: 300, gasRight: true, dec: 0, decR: 1, smooth: false })); @endif
    @if(!empty($gor_chart_data)) OFCharts.line(document.getElementById('chGor'), fromCfg(@json($gor_chart_data), { height: 250, dec: 0, area: true, smooth: false })); @endif
    @if(!empty($pressure_temp_chart_data)) OFCharts.line(document.getElementById('chPt'), fromCfg(@json($pressure_temp_chart_data), { height: 250, dec: 0, decR: 1, zero: false, smooth: false })); @endif

    // ---- parameter statistics + explorer ----
    var timeCol = COLS.filter(function (c) { return /time|date/i.test(c); })[0];
    var labels = ROWS.map(function (r) { return timeCol ? (r[timeCol] || '') : ''; });
    var stats = COLS.map(function (c) {
        var vals = ROWS.map(function (r) { return num(r[c]); }), ok = vals.filter(function (v) { return v !== null; });
        var numeric = ok.length >= ROWS.length * .5 && ok.length > 0, mn = null, mx = null, sum = 0;
        ok.forEach(function (v) { mn = mn === null || v < mn ? v : mn; mx = mx === null || v > mx ? v : mx; sum += v; });
        var last = ROWS.length ? ROWS[ROWS.length - 1][c] : null;
        return { c: c, h: HELP[c] || [c, '', ''], vals: vals, numeric: numeric, min: mn, max: mx, avg: ok.length ? sum / ok.length : null, last: numeric ? ok[ok.length - 1] : last };
    });
    var ptb = document.querySelector('#ptbl tbody'), curIdx = -1;
    function drawList(q) {
        ptb.innerHTML = '';
        stats.forEach(function (s, i) {
            if (q && (s.c + ' ' + s.h[0]).toLowerCase().indexOf(q) === -1) return;
            var tr = document.createElement('tr'); tr.dataset.i = i; if (i === curIdx) tr.className = 'cur';
            tr.innerHTML = '<td class="p">' + esc(s.h[0]) + ' ' + hq(s.h) + '<small>' + esc(s.c) + '</small></td><td class="n">' + (s.numeric ? fm(s.last) : esc(s.last === null ? '—' : s.last)) + '</td>';
            ptb.appendChild(tr);
        });
    }
    function select(i) {
        var s = stats[i]; if (!s) return; curIdx = i;
        document.getElementById('selName').textContent = s.h[0]; document.getElementById('selHq').innerHTML = hq(s.h); document.getElementById('selUnit').textContent = s.h[2] ? 'unit: ' + s.h[2] : '';
        document.getElementById('stMin').textContent = fm(s.min); document.getElementById('stAvg').textContent = fm(s.avg); document.getElementById('stMax').textContent = fm(s.max); document.getElementById('stLast').textContent = s.numeric ? fm(s.last) : '—';
        var el = document.getElementById('chSel');
        if (s.numeric) OFCharts.line(el, { labels: labels, height: 230, dec: undefined, zero: false, smooth: false, area: true, series: [{ name: s.h[0], data: s.vals, color: '#22d3ee', unit: s.h[2] }] });
        else el.innerHTML = '<div class="empty">This parameter is text, so it has no curve.</div>';
        [].forEach.call(ptb.querySelectorAll('tr'), function (tr) { tr.classList.toggle('cur', +tr.dataset.i === i); });
    }
    drawList('');
    ptb.addEventListener('click', function (e) { if (e.target.closest('.hq')) return; var tr = e.target.closest('tr'); if (tr) select(+tr.dataset.i); });
    document.getElementById('pq').addEventListener('input', function () { drawList(this.value.toLowerCase()); });
    var first = stats.findIndex(function (s) { return /^qo_sc/.test(s.c); }); select(first > -1 ? first : Math.max(stats.findIndex(function (s) { return s.numeric; }), 0));

    // ---- raw readings table ----
    var tbl = document.getElementById('rawTbl'), page = 0, size = 25, q = '';
    tbl.tHead.innerHTML = '<tr>' + COLS.map(function (c) { return '<th>' + esc(c) + ' ' + hq(HELP[c] || [c, '', '']) + '</th>'; }).join('') + '</tr>';
    function filtered() { if (!q) return ROWS; return ROWS.filter(function (r) { return COLS.some(function (c) { return String(r[c] === undefined ? '' : r[c]).toLowerCase().indexOf(q) > -1; }); }); }
    function draw() {
        var list = filtered(), total = list.length, pages = Math.max(1, Math.ceil(total / size)); page = Math.min(page, pages - 1);
        tbl.tBodies[0].innerHTML = list.slice(page * size, page * size + size).map(function (r) { return '<tr>' + COLS.map(function (c) { var v = r[c]; return '<td>' + esc(v === undefined || v === null ? '' : v) + '</td>'; }).join('') + '</tr>'; }).join('');
        document.getElementById('pInfo').textContent = total ? 'Page ' + (page + 1) + ' of ' + pages : 'No rows';
        document.getElementById('rowInfo').textContent = total + ' of ' + ROWS.length + ' rows';
        document.getElementById('pPrev').disabled = page <= 0; document.getElementById('pNext').disabled = page >= pages - 1;
    }
    document.getElementById('pPrev').addEventListener('click', function () { page--; draw(); });
    document.getElementById('pNext').addEventListener('click', function () { page++; draw(); });
    document.getElementById('pSize').addEventListener('change', function () { size = parseInt(this.value, 10); page = 0; draw(); });
    document.getElementById('rowQ').addEventListener('input', function () { q = this.value.toLowerCase(); page = 0; draw(); });
    document.getElementById('csvBtn').addEventListener('click', function () {
        var e2 = function (v) { return '"' + String(v === undefined || v === null ? '' : v).replace(/"/g, '""') + '"'; };
        var lines = [COLS.map(e2).join(',')].concat(filtered().map(function (r) { return COLS.map(function (c) { return e2(r[c]); }).join(','); }));
        var a = document.createElement('a'); a.href = URL.createObjectURL(new Blob([lines.join('\n')], { type: 'text/csv' })); a.download = 'well-test-{{ $wellTest->id }}.csv'; a.click();
    });
    draw();

    function fmt(v, d) { return v.toLocaleString('en-US', { minimumFractionDigits: d, maximumFractionDigits: d }); }
    function countUp(el) {
        var to = parseFloat(el.dataset.to) || 0, d = parseInt(el.dataset.dec || 0, 10), t0 = null; if (reduce) return;
        function step(t) { if (t0 === null) t0 = t; var p = Math.min((t - t0) / 1400, 1), e = 1 - Math.pow(1 - p, 4); el.textContent = fmt(to * e, d); if (p < 1) requestAnimationFrame(step); else el.textContent = fmt(to, d); }
        requestAnimationFrame(step);
    }
    var io = new IntersectionObserver(function (es) { es.forEach(function (e) { if (!e.isIntersecting) return; e.target.classList.add('in'); e.target.querySelectorAll('.cu').forEach(countUp); io.unobserve(e.target); }); }, { threshold: .08 });
    root.querySelectorAll('.rv').forEach(function (el) { io.observe(el); });
    if (reduce) root.querySelectorAll('svg').forEach(function (s) { if (s.pauseAnimations) s.pauseAnimations(); });
})();
</script>
@endsection
