<!DOCTYPE html>
@use('App\Support\MpfmGlossary', 'G')
@php
    $f = fn ($v, $d = 2) => is_numeric($v) ? number_format((float) $v, $d) : '—';
    $M = $metrics ?? [];
    $g = fn ($k) => isset($M[$k]) && is_numeric($M[$k]) ? (float) $M[$k] : null;
    $oil = $g('oil_rate'); $gas = $g('gas_rate'); $wrate = $g('water_rate'); $gor = $g('gor'); $bsw = $g('bsw'); $wlr = $g('wlr'); $gvf = $g('gvf');
    $tUnit = '°C';
    foreach (($columns ?? []) as $c) { if (stripos($c, 'temp') !== false && preg_match('/\[(.*?)\]/', $c, $mm)) { $tUnit = $mm[1]; break; } }
    $start = $wellTest->start_time; $end = $wellTest->end_time;
    $mins = ($start && $end) ? (int) round(abs($end->diffInMinutes($start))) : null;
    $duration = $mins !== null ? intdiv($mins, 60).' h '.($mins % 60).' min' : null;
    $meta = $wellTest->metadata ?? [];
    $ref = 'WT-'.str_pad((string) $wellTest->id, 5, '0', STR_PAD_LEFT);
    $gasF = $gvf !== null ? min(max($gvf, 0), 100) : 0;
    $waterF = (100 - $gasF) * (min(max($wlr ?? $bsw ?? 0, 0), 100) / 100);
    $oilF = max(100 - $gasF - $waterF, 0);

    $details = [
        ['Client', $wellTest->client], ['Oil field', $wellTest->field], ['Well', $wellTest->well], ['Wellhead tower', $wellTest->wellhead_tower], ['Wellhead string', $wellTest->wellhead_string],
        ['Operator', $wellTest->operator], ['Test date', $wellTest->test_date ? $wellTest->test_date->format('d M Y') : null], ['Test type', $wellTest->test_type ? ucfirst(str_replace('-', ' ', $wellTest->test_type)) : null],
        ['Start', $start ? $start->format('d M Y H:i:s') : null], ['End', $end ? $end->format('d M Y H:i:s') : null], ['Duration', $duration],
        ['Choke size (1/64")', $wellTest->choke_size], ['Upstream choke', $wellTest->upstream_choke], ['Downstream choke', $wellTest->downstream_choke],
        ['Readings in log', number_format(count($rows ?? []))], ['Log file', $wellTest->filename],
    ];
    $results = [
        ['Oil rate', $oil, 'STB/d', 2, 'oil_rate'], ['Gas rate', $gas, 'MMScf/d', 2, 'gas_rate'], ['Water rate', $wrate, 'STB/d', 2, 'water_rate'],
        ['GOR', $gor, 'Scf/STB', 0, 'gor'], ['GVF', $gvf, '%', 2, 'gvf'], ['BSW', $bsw, '%', 2, 'bsw'], ['WLR', $wlr, '%', 2, 'wlr'],
    ];
    $pt = [
        ['Pressure', 'psi', $g('min_pressure'), $g('avg_pressure'), $g('max_pressure')],
        ['Temperature', $tUnit, $g('min_temperature'), $g('avg_temperature'), $g('max_temperature')],
    ];
    $pvt = [['Oil formation volume factor (Bo)', 'bo', 4, 'Sm³/m³'], ['Water formation volume factor (Bw)', 'bw', 4, 'Sm³/m³'], ['Gas formation volume factor (Bg)', 'bg', 4, 'Sm³/m³'], ['Gas compressibility factor (Z)', 'z_factor', 4, '—'], ['Solution GOR (Rs)', 'rst', 2, 'Scf/STB'], ['Oil density', 'po', 2, 'kg/m³'], ['Water density', 'pw', 2, 'kg/m³'], ['Gas density', 'pg', 4, 'kg/m³'],
        ['Cumulative oil', 'cumulative_oil_volume', 0, 'bbl'], ['Cumulative water', 'cumulative_water_volume', 0, 'bbl'], ['Cumulative gas', 'cumulative_gas_volume', 0, 'Scf'], ['H₂S average', 'h2s_avg', 2, 'ppm'], ['H₂S maximum', 'h2s_max', 2, 'ppm']];
    $cross = [['GOR', 'gor', 'gor_formula', 0, ''], ['BSW', 'bsw', 'bsw_formula', 2, '%'], ['WLR', 'wlr', 'wlr_formula', 2, '%'], ['GVF', 'gvf', 'gvf_formula', 2, '%']];
    $flagged = [];
    foreach (($M['safety_issues'] ?? []) as $grp => $items) { foreach ((array) $items as $it) { $col = $it['column'] ?? '?'; $flagged[$col]['n'] = ($flagged[$col]['n'] ?? 0) + 1; $flagged[$col]['v'][] = $it['value'] ?? null; } }
    $metaKeys = ['Log File Version', 'Vx DAFC version', 'File created:', 'DAFC Serial Number', 'GD Serial Number', 'Configuration name', 'Mode'];

    // ---- static SVG line chart (prints reliably, no JavaScript) ----
    $svg = function (array $labels, array $series, int $h = 210) {
        $n = count($labels); if (! $n) return '<div class="nodata">No data</div>';
        $W = 720; $pl = 54; $pr = 54; $pt = 26; $pb = 28; $step = max(1, (int) ceil($n / 160));
        $idx = []; for ($i = 0; $i < $n; $i += $step) $idx[] = $i; if (end($idx) !== $n - 1) $idx[] = $n - 1;
        $ext = function ($ax) use ($series) { $lo = INF; $hi = -INF; foreach ($series as $s) { if ($s['axis'] !== $ax) continue; foreach ($s['data'] as $v) { if (is_numeric($v)) { $lo = min($lo, $v); $hi = max($hi, $v); } } } if ($lo === INF) return null; if ($hi == $lo) { $hi += 1; $lo -= 1; } $p = ($hi - $lo) * .1; return [$lo - $p, $hi + $p]; };
        $E = ['l' => $ext('l'), 'r' => $ext('r')];
        $hasR = $E['r'] !== null; $iw = $W - $pl - ($hasR ? $pr : 16); $ih = $h - $pt - $pb;
        $X = fn ($k) => $pl + ($k / max(count($idx) - 1, 1)) * $iw;
        $out = '<svg viewBox="0 0 '.$W.' '.$h.'" xmlns="http://www.w3.org/2000/svg" class="chart">';
        for ($g = 0; $g <= 4; $g++) {
            $y = $pt + $g * $ih / 4;
            $out .= '<line x1="'.$pl.'" x2="'.($pl + $iw).'" y1="'.$y.'" y2="'.$y.'" stroke="#e2e8f0"/>';
            if ($E['l']) $out .= '<text x="'.($pl - 6).'" y="'.($y + 3.5).'" text-anchor="end" font-size="9" fill="#64748b">'.number_format($E['l'][1] - ($E['l'][1] - $E['l'][0]) * $g / 4, 0).'</text>';
            if ($E['r']) $out .= '<text x="'.($pl + $iw + 6).'" y="'.($y + 3.5).'" font-size="9" fill="#64748b">'.number_format($E['r'][1] - ($E['r'][1] - $E['r'][0]) * $g / 4, 1).'</text>';
        }
        $tk = min(6, count($idx));
        for ($t = 0; $t < $tk; $t++) { $k = (int) round($t * (count($idx) - 1) / max($tk - 1, 1)); $lab = (string) $labels[$idx[$k]]; $lab = str_contains($lab, ' ') ? substr(strrchr($lab, ' '), 1, 5) : substr($lab, 0, 8); $out .= '<text x="'.$X($k).'" y="'.($h - 9).'" text-anchor="middle" font-size="9" fill="#64748b">'.e($lab).'</text>'; }
        $lx = $pl;
        foreach ($series as $s) {
            $e2 = $E[$s['axis']]; if (! $e2) continue; $d = ''; $first = true;
            foreach ($idx as $k => $i) { $v = $s['data'][$i] ?? null; if (! is_numeric($v)) { $first = true; continue; } $y = $pt + (1 - ($v - $e2[0]) / ($e2[1] - $e2[0])) * $ih; $d .= ($first ? 'M' : 'L').round($X($k), 1).','.round($y, 1).' '; $first = false; }
            $out .= '<path d="'.trim($d).'" fill="none" stroke="'.$s['color'].'" stroke-width="1.4" stroke-linejoin="round"/>';
            $out .= '<rect x="'.$lx.'" y="6" width="10" height="3" fill="'.$s['color'].'"/><text x="'.($lx + 14).'" y="11" font-size="9" fill="#334155">'.e($s['name']).'</text>'; $lx += 24 + strlen($s['name']) * 5.2;
        }
        return $out.'</svg>';
    };
    $cfg = function ($c, array $colors, $rightFor = null) { $d = $c['data'] ?? []; $series = []; foreach (($d['datasets'] ?? []) as $i => $s) { $name = $s['label'] ?? ('Series '.($i + 1)); $series[] = ['name' => $name, 'color' => $colors[$i % count($colors)], 'axis' => (($s['yAxisID'] ?? '') === 'y1' || ($rightFor && preg_match($rightFor, $name))) ? 'r' : 'l', 'data' => array_map(fn ($v) => is_numeric($v) ? (float) $v : null, $s['data'] ?? [])]; } return [$d['labels'] ?? [], $series]; };
    [$fl, $fs] = $cfg($rates_chart_data ?? [], ['#d97706', '#0284c7', '#0f766e'], '/^qg|gas/i');
    [$gl, $gs] = $cfg($gor_chart_data ?? [], ['#059669']);
    [$pl2, $ps] = $cfg($pressure_temp_chart_data ?? [], ['#7c3aed', '#dc2626']);

    // ---- parameter summary ----
    $keyCols = ['qo_sc', 'qw_sc', 'qg_sc', 'qo_lc', 'qw_lc', 'qg_lc', 'Fo_lc', 'Fw_lc', 'Fg_lc', 'GOR', 'GLR', 'BSW', 'WLR', 'GVF', 'PIN', 'TL', 'DPV', 'Dyn_DP', 'Do_lc', 'Dw_lc', 'Dg_lc', 'Dm_lc'];
    $stat = function ($c) use ($rows) { $vals = []; foreach ($rows as $r) { $v = $r[$c] ?? null; if (is_numeric($v)) $vals[] = (float) $v; } if (! count($vals)) return null; return [min($vals), array_sum($vals) / count($vals), max($vals), end($vals)]; };
    $paramRows = []; $allRows = [];
    foreach (($columns ?? []) as $c) {
        $base = preg_replace('/\[.*\]$/u', '', $c);
        $st = $stat($c); if (! $st) continue;
        $gl2 = G::column($c);
        $row = ['name' => $gl2[0], 'col' => $c, 'unit' => $gl2[2], 'st' => $st, 'why' => $gl2[1]];
        $allRows[] = $row;
        if (in_array($base, $keyCols, true)) $paramRows[] = $row;
    }
    $defs = ['oil_rate', 'gas_rate', 'water_rate', 'gor', 'bsw', 'wlr', 'gvf', 'bo', 'bg', 'z_factor', 'rst'];
@endphp
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $ref }} · Well Test Report · {{ $wellTest->well }}</title>
<style>
@page { size:A4; margin:14mm 12mm 16mm; @bottom-left { content:"{{ $ref }} · {{ $wellTest->well }}"; font:8pt Arial,sans-serif; color:#64748b; } @bottom-right { content:"Page " counter(page) " of " counter(pages); font:8pt Arial,sans-serif; color:#64748b; } }
* { box-sizing:border-box; -webkit-print-color-adjust:exact; print-color-adjust:exact; }
html, body { margin:0; padding:0; }
body { font:10pt/1.45 'Segoe UI',Arial,sans-serif; color:#0f172a; background:#e5e9f0; }
.bar { position:sticky; top:0; z-index:10; background:#0b1426; color:#e8eefc; padding:12px 24px; display:flex; gap:14px; align-items:center; flex-wrap:wrap; box-shadow:0 6px 24px rgba(0,0,0,.25); }
.bar b { font-size:14px; } .bar .sp { flex:1; } .bar small { color:#9fb0cf; }
.bar button, .bar a.b { padding:9px 20px; border-radius:50px; border:0; font:700 13px 'Segoe UI',Arial,sans-serif; cursor:pointer; text-decoration:none; background:linear-gradient(135deg,#3b82f6,#22d3ee); color:#04111f; }
.bar a.b.g { background:rgba(255,255,255,.1); color:#e8eefc; border:1px solid rgba(255,255,255,.2); }
.bar label { font-size:12.5px; display:flex; gap:7px; align-items:center; cursor:pointer; color:#cbd5e1; }
.sheet { width:210mm; margin:18px auto; background:#fff; padding:14mm 12mm; box-shadow:0 10px 40px rgba(15,23,42,.18); }
.head { display:flex; align-items:center; gap:14px; padding-bottom:10px; border-bottom:2.5px solid #1e3a8a; }
.badge { height:56px; aspect-ratio:600/940; border-radius:8px; background:#fff url('{{ asset('assets/img/brand/logo.png') }}') 50% 50.8%/333.33% auto no-repeat; border:1.5px solid #cbd5e1; flex:none; }
.head h1 { margin:0; font-size:17pt; color:#1e3a8a; letter-spacing:-.01em; } .head p { margin:2px 0 0; color:#475569; font-size:9.5pt; }
.head .ref { margin-left:auto; text-align:right; font-size:8.5pt; color:#475569; line-height:1.5; } .head .ref b { display:block; font-size:13pt; color:#0f172a; letter-spacing:.04em; }
h2 { font-size:11pt; color:#1e3a8a; margin:16px 0 7px; padding-bottom:3px; border-bottom:1px solid #cbd5e1; text-transform:uppercase; letter-spacing:.06em; break-after:avoid; }
.grid2 { display:grid; grid-template-columns:1fr 1fr; gap:0 18px; }
table { width:100%; border-collapse:collapse; font-size:9pt; }
th { text-align:left; background:#f1f5f9; color:#334155; font-weight:700; padding:5px 8px; border:1px solid #cbd5e1; font-size:8.5pt; }
td { padding:4.5px 8px; border:1px solid #e2e8f0; vertical-align:top; }
td.l { width:38%; color:#475569; background:#f8fafc; } td.n { text-align:right; font-variant-numeric:tabular-nums; }
tr { break-inside:avoid; }
.kpis { display:grid; grid-template-columns:repeat(4,1fr); gap:8px; }
.kpi { border:1px solid #cbd5e1; border-left:4px solid var(--c); border-radius:6px; padding:7px 10px; break-inside:avoid; }
.kpi small { display:block; font-size:7.5pt; text-transform:uppercase; letter-spacing:.08em; color:#64748b; font-weight:700; } .kpi b { font-size:15pt; color:#0f172a; } .kpi span { font-size:8pt; color:#64748b; margin-left:4px; }
.stack { display:flex; height:14px; border-radius:7px; overflow:hidden; border:1px solid #cbd5e1; margin:6px 0 4px; } .stack i { display:block; height:100%; }
.legend { font-size:8.5pt; color:#475569; display:flex; gap:16px; } .legend b { display:inline-block; width:9px; height:9px; border-radius:2px; margin-right:5px; vertical-align:-1px; }
.chart { width:100%; height:auto; display:block; border:1px solid #e2e8f0; border-radius:6px; margin-bottom:8px; } .nodata { padding:18px; text-align:center; color:#94a3b8; border:1px dashed #cbd5e1; border-radius:6px; }
.cap { font-size:8.5pt; color:#475569; margin:6px 0 3px; font-weight:700; }
.defs { columns:2; column-gap:18px; font-size:8.3pt; color:#334155; } .defs p { margin:0 0 6px; break-inside:avoid; } .defs b { color:#0f172a; }
.sign { display:grid; grid-template-columns:repeat(3,1fr); gap:20px; margin-top:26px; break-inside:avoid; } .sign div { border-top:1px solid #0f172a; padding-top:4px; font-size:8.5pt; color:#475569; min-height:46px; } .sign b { color:#0f172a; display:block; }
.foot { margin-top:18px; padding-top:7px; border-top:1px solid #cbd5e1; font-size:8pt; color:#64748b; display:flex; justify-content:space-between; gap:12px; }
.pill { display:inline-block; padding:1px 8px; border-radius:20px; font-size:8pt; font-weight:700; } .pill.ok { background:#dcfce7; color:#166534; } .pill.w { background:#fef3c7; color:#92400e; } .pill.na { background:#e2e8f0; color:#475569; }
.appx { display:none; } body.full .appx { display:block; }
.pb { break-before:page; }
@media print {
    body { background:#fff; font-size:9.5pt; }
    .bar { display:none; }
    .sheet { width:auto; margin:0; padding:0; box-shadow:none; }
}
</style>
</head>
<body>
<div class="bar">
    <b>Well test report · {{ $ref }}</b><small>Choose “Save as PDF” as the destination in the print dialog to get a file you can attach to an email.</small>
    <span class="sp"></span>
    <label><input type="checkbox" id="full"> Include full parameter table</label>
    <a class="b g" href="{{ route('well_tests.show', $wellTest->id) }}">← Back to test</a>
    <button type="button" id="printBtn">🖨 Print / Save as PDF</button>
</div>

<div class="sheet">
    <div class="head">
        <span class="badge"></span>
        <div><h1>MPFM Well Test Report</h1><p>Oil Field Management System</p></div>
        <div class="ref"><b>{{ $ref }}</b>Issued {{ now()->format('d M Y, H:i') }}<br>Prepared by {{ auth()->user()->name ?? '—' }}</div>
    </div>

    <h2>1. Test identification</h2>
    <div class="grid2">
        @foreach(array_chunk(array_values(array_filter($details, fn ($d) => filled($d[1]) && $d[1] !== 'N/A')), (int) ceil(count(array_filter($details, fn ($d) => filled($d[1]) && $d[1] !== 'N/A')) / 2)) as $chunk)
            <table>@foreach($chunk as [$l, $v])<tr><td class="l">{{ $l }}</td><td>{{ $v }}</td></tr>@endforeach</table>
        @endforeach
    </div>

    <h2>2. Key results (test average)</h2>
    <div class="kpis">
        @foreach($results as [$l, $v, $u, $d, $k])
            <div class="kpi" style="--c:{{ ['oil_rate' => '#d97706', 'gas_rate' => '#0891b2', 'water_rate' => '#0284c7', 'gor' => '#059669', 'gvf' => '#7c3aed', 'bsw' => '#dc2626', 'wlr' => '#0ea5e9'][$k] }}"><small>{{ $l }}</small><b>{{ $f($v, $d) }}</b><span>{{ $u }}</span></div>
        @endforeach
    </div>

    <div class="grid2" style="margin-top:12px">
        <div>
            <h2 style="margin-top:6px">3. Pressure &amp; temperature</h2>
            <table><tr><th>Quantity</th><th>Unit</th><th class="n">Min</th><th class="n">Average</th><th class="n">Max</th></tr>
                @foreach($pt as [$l, $u, $mn, $av, $mx])<tr><td>{{ $l }}</td><td>{{ $u }}</td><td class="n">{{ $f($mn, 1) }}</td><td class="n">{{ $f($av, 1) }}</td><td class="n">{{ $f($mx, 1) }}</td></tr>@endforeach
            </table>
        </div>
        <div>
            <h2 style="margin-top:6px">Phase fractions in the pipe</h2>
            <div class="stack"><i style="width:{{ $gasF }}%;background:#06b6d4"></i><i style="width:{{ $oilF }}%;background:#f59e0b"></i><i style="width:{{ $waterF }}%;background:#0284c7"></i></div>
            <div class="legend"><span><b style="background:#06b6d4"></b>Gas {{ $f($gasF, 1) }}%</span><span><b style="background:#f59e0b"></b>Oil {{ $f($oilF, 1) }}%</span><span><b style="background:#0284c7"></b>Water {{ $f($waterF, 1) }}%</span></div>
        </div>
    </div>

    <h2>4. Fluid properties, volumes &amp; H₂S</h2>
    @php $pv = array_values(array_filter($pvt, fn ($p) => $g($p[1]) !== null)); $half = (int) ceil(max(count($pv), 1) / 2); @endphp
    <div class="grid2">
        @foreach([array_slice($pv, 0, $half), array_slice($pv, $half)] as $chunk)
            <table><tr><th>Property</th><th class="n">Value</th><th>Unit</th></tr>@foreach($chunk as [$l, $k, $d, $u])<tr><td>{{ $l }}</td><td class="n">{{ $f($g($k), $d) }}</td><td>{{ $u }}</td></tr>@endforeach</table>
        @endforeach
    </div>

    <h2>5. Test curves</h2>
    <div class="cap">Flow rates during the test (oil &amp; water: left axis · gas: right axis)</div>{!! $svg($fl, $fs, 230) !!}
    <div class="grid2">
        <div><div class="cap">Gas-oil ratio (GOR)</div>{!! $svg($gl, $gs, 190) !!}</div>
        <div><div class="cap">Pressure &amp; temperature</div>{!! $svg($pl2, $ps, 190) !!}</div>
    </div>

    <h2>6. Data quality</h2>
    <div class="grid2">
        <div>
            <div class="cap">Meter value vs recalculated</div>
            <table><tr><th>Quantity</th><th class="n">Meter</th><th class="n">Formula</th><th>Check</th></tr>
            @foreach($cross as [$l, $k, $kf, $d, $u])
                @php $a = $g($k); $b = $g($kf); $big = ($a && $b) ? max(abs($a), abs($b)) / max(min(abs($a), abs($b)), 1e-9) : null; $ok = $a !== null && $b !== null && ($big === null || $big <= 20) && abs($a - $b) <= max(abs($a) * .05, 1); @endphp
                <tr><td>{{ $l }}</td><td class="n">{{ $f($a, $d) }} {{ $u }}</td><td class="n">{{ $f($b, ($b && abs($b) < 1) ? 4 : $d) }} {{ $u }}</td><td>@if($big !== null && $big > 20)<span class="pill na">not comparable</span>@elseif($ok)<span class="pill ok">consistent</span>@elseif($a === null || $b === null)<span class="pill na">n/a</span>@else<span class="pill w">review</span>@endif</td></tr>
            @endforeach</table>
        </div>
        <div>
            <div class="cap">Readings flagged by the checker</div>
            @if(count($flagged))
                <table><tr><th>Parameter</th><th class="n">Readings</th><th class="n">Range</th></tr>
                @foreach($flagged as $col => $d)@php $vals = array_filter($d['v'], 'is_numeric'); @endphp<tr><td>{{ $col }}</td><td class="n">{{ $d['n'] }}</td><td class="n">{{ count($vals) ? $f(min($vals), 1).' – '.$f(max($vals), 1) : '—' }}</td></tr>@endforeach</table>
            @else
                <table><tr><td>No readings were flagged.</td></tr></table>
            @endif
        </div>
    </div>

    <h2>7. Meter &amp; log information</h2>
    <div class="grid2">
        @php $mk = array_values(array_filter($metaKeys, fn ($k) => filled($meta[$k] ?? null))); $mh = (int) ceil(max(count($mk), 1) / 2); @endphp
        @foreach([array_slice($mk, 0, $mh), array_slice($mk, $mh)] as $chunk)
            <table>@foreach($chunk as $k)<tr><td class="l">{{ G::field($k)[0] }}</td><td>{{ $meta[$k] }}</td></tr>@endforeach</table>
        @endforeach
    </div>

    <h2>8. Key logged parameters</h2>
    <table><tr><th>Parameter</th><th>Log column</th><th>Unit</th><th class="n">Min</th><th class="n">Average</th><th class="n">Max</th><th class="n">Last</th></tr>
        @foreach($paramRows as $r)<tr><td>{{ $r['name'] }}</td><td>{{ str_replace('Â', '', $r['col']) }}</td><td>{{ $r['unit'] }}</td><td class="n">{{ $f($r['st'][0], 2) }}</td><td class="n">{{ $f($r['st'][1], 2) }}</td><td class="n">{{ $f($r['st'][2], 2) }}</td><td class="n">{{ $f($r['st'][3], 2) }}</td></tr>@endforeach
    </table>

    <h2>9. Definitions</h2>
    <div class="defs">
        @foreach($defs as $k)@php $d = G::metric($k); @endphp<p><b>{{ $d[0] }}{{ $d[2] ? ' ('.$d[2].')' : '' }}.</b> {{ $d[1] }}</p>@endforeach
    </div>

    <div class="sign"><div><b>Prepared by</b>{{ auth()->user()->name ?? '' }}<br>Date: {{ now()->format('d M Y') }}</div><div><b>Reviewed by</b>Name / date</div><div><b>Approved by</b>Name / date</div></div>
    <div class="foot"><span>{{ $ref }} · generated from log “{{ $wellTest->filename }}”</span><span>Oil Field Management System</span></div>

    <div class="appx pb">
        <h2 style="margin-top:0">Appendix — all logged parameters ({{ count($allRows) }})</h2>
        <table><tr><th>Parameter</th><th>Log column</th><th>Unit</th><th class="n">Min</th><th class="n">Average</th><th class="n">Max</th><th class="n">Last</th></tr>
            @foreach($allRows as $r)<tr><td>{{ $r['name'] }}</td><td>{{ str_replace('Â', '', $r['col']) }}</td><td>{{ $r['unit'] }}</td><td class="n">{{ $f($r['st'][0], 2) }}</td><td class="n">{{ $f($r['st'][1], 2) }}</td><td class="n">{{ $f($r['st'][2], 2) }}</td><td class="n">{{ $f($r['st'][3], 2) }}</td></tr>@endforeach
        </table>
    </div>
</div>

<script>
(function () {
    var p = new URLSearchParams(location.search);
    var full = document.getElementById('full');
    if (p.get('full') === '1') { full.checked = true; document.body.classList.add('full'); }
    full.addEventListener('change', function () { document.body.classList.toggle('full', full.checked); });
    document.getElementById('printBtn').addEventListener('click', function () { window.print(); });
    if (p.get('auto') === '1') setTimeout(function () { window.print(); }, 700);
})();
</script>
</body>
</html>
