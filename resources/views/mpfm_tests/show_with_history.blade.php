@extends('layouts.master')

@section('title', 'Test Details - ' . $mpfmTest->id)

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/ofpage.css') }}">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
<style>
.ofp .verdict { display:flex; align-items:center; gap:14px; padding:12px 18px; border-radius:20px; border:1px solid var(--vc); background:rgba(0,0,0,.35); backdrop-filter:blur(6px); box-shadow:0 0 30px color-mix(in srgb, var(--vc) 28%, transparent); }
.ofp .verdict svg { width:74px; height:74px; flex:none; }
.ofp .verdict b { display:block; font:800 17px 'Space Grotesk'; color:var(--vc); letter-spacing:.03em; }
.ofp .verdict small { color:var(--sub); font-size:12px; }
.ofp .verdict.pass { --vc:#34d399; } .ofp .verdict.warning { --vc:#fbbf24; } .ofp .verdict.fail { --vc:#f87171; } .ofp .verdict.none { --vc:#64748b; }
.ofp .verdict .arc { stroke-width:8; stroke-linecap:round; }
.ofp .pipe { display:grid; grid-template-columns:repeat(4,1fr); gap:0; }
.ofp .ps { text-align:center; position:relative; padding:0 8px; }
.ofp .ps svg { width:84px; height:84px; transition:transform .4s var(--ease); }
.ofp .ps.on svg { animation:hBob 3.4s ease-in-out infinite; }
.ofp .ps:nth-child(2) svg { animation-delay:-.8s; } .ofp .ps:nth-child(3) svg { animation-delay:-1.6s; } .ofp .ps:nth-child(4) svg { animation-delay:-2.4s; }
@keyframes hBob { 50% { transform:translateY(-5px); } }
.ofp .ps:not(.on) svg { opacity:.35; filter:grayscale(1); }
.ofp .ps b { display:block; font-size:13px; margin-top:4px; } .ofp .ps small { display:block; font-size:11.5px; color:var(--mute); margin-top:3px; line-height:1.4; }
.ofp .ps:not(:last-child)::after { content:""; position:absolute; top:40px; left:calc(50% + 50px); width:calc(100% - 100px); border-top:2px dashed rgba(34,211,238,.5); }
.ofp .ps:not(.on):not(:last-child)::after { border-top-color:rgba(255,255,255,.12); }
.ofp .rules { display:grid; gap:10px; margin-top:16px; }
.ofp .rule { display:flex; gap:14px; align-items:flex-start; padding:14px 16px; border-radius:16px; border:1px solid var(--vc); background:color-mix(in srgb, var(--vc) 7%, transparent); }
.ofp .rule.pass { --vc:#34d399; } .ofp .rule.warning { --vc:#fbbf24; } .ofp .rule.fail { --vc:#f87171; }
.ofp .rule .ri { width:34px; height:34px; border-radius:50%; display:grid; place-items:center; flex:none; background:color-mix(in srgb, var(--vc) 18%, transparent); color:var(--vc); font-size:16px; }
.ofp .rule b { display:block; color:#fff; font-size:14px; } .ofp .rule p { color:var(--sub); font-size:12.5px; margin-top:3px; line-height:1.5; }
.ofp .rule .chip { margin-left:auto; flex:none; }
.ofp .stack { display:flex; height:14px; border-radius:7px; overflow:hidden; background:rgba(255,255,255,.07); margin:10px 0 6px; }
.ofp .stack i { display:block; height:100%; width:0; transition:width 1.2s var(--ease); }
.ofp .in .stack i { width:var(--w); }
.ofp .sum { display:grid; grid-template-columns:repeat(auto-fit,minmax(170px,1fr)); gap:12px; }
.ofp .sum div { padding:12px 14px; border-radius:14px; background:var(--glass); border:1px solid var(--line); }
.ofp .sum small { display:block; font-size:10px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--mute); }
.ofp .sum b { display:block; margin-top:5px; font:700 17px 'Space Grotesk'; color:#fff; }
.ofp .trg { display:grid; grid-template-columns:1fr 1fr; gap:18px; }
.ofp .trc { padding:14px 16px; border-radius:16px; border:1px solid var(--line); background:var(--glass); display:flex; align-items:center; gap:14px; flex-wrap:wrap; }
.ofp .trc b { color:#fff; } .ofp .trc small { color:var(--sub); font-size:12px; }
.ofp .db { transform:scaleX(0); transition:transform 1.2s var(--ease); } .ofp .in .db, .ofp .pane.in .db { transform:scaleX(1); }
.ofp .light { background:#fff; color:#2d3748; border-radius:14px; padding:14px; }
.ofp .kpis.k4 { grid-template-columns:repeat(4,1fr); }
@media (max-width:1000px) { .ofp .kpis.k4 { grid-template-columns:repeat(2,1fr); } }
@media (max-width:900px) { .ofp .pipe { grid-template-columns:1fr 1fr; row-gap:18px; } .ofp .ps::after { display:none; } .ofp .trg { grid-template-columns:1fr; } }
</style>
@endsection

@section('content')
@php
    $f = fn ($v, $d = 2) => is_numeric($v) ? number_format((float) $v, $d) : '—';
    $v = $mpfmTest->latestValidation;
    $vs = $v ? ($v->overall_status ?? 'none') : 'none';
    $vs = in_array($vs, ['pass', 'warning', 'fail']) ? $vs : 'none';
    $score = $v && is_numeric($v->confidence_score) ? (float) $v->confidence_score : null;
    $col = ['pass' => '#34d399', 'warning' => '#fbbf24', 'fail' => '#f87171', 'none' => '#64748b'][$vs];
    $sug = $v->auto_suggestion ?? null;
    $rules = $v ? (array) $v->rule_results : [];
    $rc = ['pass' => 0, 'warning' => 0, 'fail' => 0];
    foreach ($rules as $r) { $k = $r['result'] ?? 'pass'; if (isset($rc[$k])) $rc[$k]++; }
    $rt = max(array_sum($rc), 1);
    $hist = $v ? $v->historical_comparison : null;
    $devs = $hist['current_deviations'] ?? [];
    $trends = $hist['trends'] ?? [];
    $hasCharts = ! empty($trendingCharts);
    $first = fn ($a, ...$k) => collect($k)->map(fn ($x) => $a->{$x})->first(fn ($x) => is_numeric($x));
    $res = [
        ['Oil rate SC', $first($mpfmTest, 'qo_sc', 'oil_rate'), 'STB/d', 2, 'fe-droplet', '#fbbf24'],
        ['Gas rate SC', $first($mpfmTest, 'qg_sc', 'gas_rate'), 'MMScf/d', 2, 'fe-wind', '#22d3ee'],
        ['Water cut / BSW', $first($mpfmTest, 'bsw', 'water_cut'), '%', 2, 'fe-percent', '#38bdf8'],
        ['GOR', $first($mpfmTest, 'gor'), 'Scf/STB', 2, 'fe-layers', '#34d399'],
        ['Pressure avg', $first($mpfmTest, 'pressure_avg', 'pressure'), 'psia', 2, 'fe-activity', '#a78bfa'],
        ['Temperature avg', $first($mpfmTest, 'temperature_avg', 'temperature'), '°C', 2, 'fe-thermometer', '#f87171'],
        ['GVF', $first($mpfmTest, 'gvf'), '%', 2, 'fe-cloud', '#67e8f9'],
        ['WLR', $first($mpfmTest, 'wlr'), '', 2, 'fe-droplet', '#7dd3fc'],
    ];
    $R = 15; foreach ($devs as $d) { $R = max($R, ceil(abs($d['percent_diff'] ?? 0) / 5) * 5); } $R = min($R, 100);
    $cx = 330; $sc = 190 / $R; $rh = 56; $ch = 44 + count($devs) * $rh + 30;
    $dcol = ['success' => '#34d399', 'warning' => '#fbbf24', 'danger' => '#f87171'];
    $dcls = ['success' => 'ok', 'warning' => 'warn', 'danger' => 'bad'];
@endphp

<div class="ofp" id="ofp">
    <div class="bgl"><div class="orb o1"></div><div class="orb o2"></div></div>

    {{-- HERO --}}
    <div class="hero rv">
        <div class="who">
            <span class="lg"></span>
            <div>
                <div class="eyebrow"><i></i> Validation &amp; history</div>
                <h1>Test #{{ $mpfmTest->id }} <em>· {{ $mpfmTest->oilString->name ?? '' }}</em></h1>
                <div class="meta">
                    <span class="chip {{ $mpfmTest->status }}">{{ ucfirst($mpfmTest->status) }}</span>
                    <span><i class="fe fe-user"></i> {{ $mpfmTest->user->name ?? '—' }}</span>
                    <span><i class="fe fe-calendar"></i> {{ $mpfmTest->test_date->format('d M Y') }}</span>
                    @if($sug)<span class="chip {{ $sug === 'approve' ? 'ok' : ($sug === 'review' ? 'warn' : 'bad') }}">Suggestion: {{ $sug }}</span>@endif
                </div>
            </div>
        </div>
        <div class="verdict {{ $vs }}">
            <svg viewBox="0 0 100 100"><circle cx="50" cy="50" r="40" fill="none" stroke="rgba(255,255,255,.08)" stroke-width="8"/><circle class="arc" cx="50" cy="50" r="40" pathLength="100" stroke="{{ $col }}" style="stroke-dasharray:{{ $score !== null ? min(max($score, 0), 100) : 0 }} 100"/><text x="50" y="57" text-anchor="middle" font-family="Space Grotesk" font-weight="700" font-size="21" fill="#fff">{{ $score !== null ? round($score).'%' : '—' }}</text></svg>
            <div><b>{{ $vs === 'none' ? 'Not validated' : ucfirst($vs) }}</b><small>{{ $score !== null ? 'Confidence score' : 'No validation run yet' }}</small></div>
        </div>
    </div>

    <div class="actions rv" style="margin-top:14px;justify-content:flex-start">
        <a class="btn2 p" href="{{ route('mpfm_tests.show', $mpfmTest->id) }}"><i class="fe fe-arrow-left"></i> Back to test</a>
        <a class="btn2" href="{{ route('mpfm_tests.string_analysis', $mpfmTest->oil_string_id) }}"><i class="fe fe-trending-up"></i> String analysis</a>
        <form method="POST" action="{{ route('mpfm_tests.revalidate', $mpfmTest->id) }}">@csrf<button class="btn2 w" type="submit"><i class="fe fe-refresh-cw"></i> Revalidate</button></form>
        @if(auth()->user()->hasAnyRole(['mpfm_supervisor', 'surveillance_supervisor']))<a class="btn2 g" href="{{ route('supervisor.test_approval', $mpfmTest->id) }}"><i class="fe fe-check-circle"></i> Approve / review</a>@endif
        <a class="btn2" href="{{ route('mpfm_tests.trending_dashboard') }}"><i class="fe fe-bar-chart-2"></i> Trending dashboard</a>
    </div>

    {{-- PIPELINE --}}
    <div class="sec">
        <div class="panel rv">
            <div class="ph"><h3><i class="fe fe-shield"></i> How this test is checked</h3><span class="sub">{{ $v ? 'Last validated '.($mpfmTest->last_validated_at ? $mpfmTest->last_validated_at->diffForHumans() : 'recently') : 'Waiting for a validation run' }}</span></div>
            <div class="pipe">
                <div class="ps on">
                    <svg viewBox="0 0 96 96"><path d="M30,12 H58 L72,26 V84 H30 Z" fill="#0b1426" stroke="#fbbf24" stroke-width="2.5" stroke-linejoin="round"/><path d="M58,12 V26 H72" fill="none" stroke="#fbbf24" stroke-width="2.5"/><path d="M38,44 H64 M38,54 H64 M38,64 H54" stroke="#fbbf24" stroke-width="3" stroke-linecap="round"/></svg>
                    <b>Log parsed</b><small>Rates, pressure and temperature extracted</small></div>
                <div class="ps {{ $v ? 'on' : '' }}">
                    <svg viewBox="0 0 96 96"><path d="M48,10 L80,22 V48 C80,66 66,80 48,86 C30,80 16,66 16,48 V22 Z" fill="#0b1426" stroke="#34d399" stroke-width="2.5" stroke-linejoin="round"/><path d="M34,48 L44,58 L64,38" fill="none" stroke="#34d399" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <b>Rule checks</b><small>{{ $v ? count($rules).' rules evaluated' : 'Not run yet' }}</small></div>
                <div class="ps {{ $hist ? 'on' : '' }}">
                    <svg viewBox="0 0 96 96"><rect x="14" y="22" width="68" height="52" rx="7" fill="#0b1426" stroke="#22d3ee" stroke-width="2.5"/><path d="M22,60 L34,46 L44,54 L58,36 L74,48" fill="none" stroke="#22d3ee" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/><circle cx="58" cy="36" r="4" fill="#22d3ee"/></svg>
                    <b>History comparison</b><small>{{ $hist ? ($hist['tests_compared'] ?? 0).' earlier tests compared' : 'Needs earlier approved tests' }}</small></div>
                <div class="ps {{ $sug ? 'on' : '' }}">
                    <svg viewBox="0 0 96 96"><circle cx="48" cy="48" r="32" fill="#0b1426" stroke="#a78bfa" stroke-width="2.5"/><path d="M48,26 V50 L62,58" fill="none" stroke="#a78bfa" stroke-width="4" stroke-linecap="round"/></svg>
                    <b>Suggestion</b><small>{{ $sug ? ucfirst($sug) : 'Pending' }}</small></div>
            </div>
        </div>
    </div>

    {{-- RESULTS --}}
    <div class="kpis k4">
        @foreach($res as $i => [$l, $val, $u, $d, $ic, $c])
            <div class="kpi rv" style="--d:{{ $i % 4 + 1 }};--c:{{ $c }}"><div class="ic"><i class="fe {{ $ic }}"></i></div><div class="l">{{ $l }}</div>
                @if($val !== null)<div class="v cu" data-to="{{ $val }}" data-dec="{{ $d }}">{{ $f($val, $d) }}</div>@else<div class="v">—</div>@endif<div class="u">{{ $u }}</div></div>
        @endforeach
    </div>

    @if($mpfmTest->test_started_at)
    <div class="sec"><div class="panel rv">
        <div class="ph"><h3><i class="fe fe-clock"></i> Test timing</h3></div>
        <div class="sum">
            <div><small>Started</small><b style="font-size:14px">{{ $mpfmTest->test_started_at->format('d M Y H:i:s') }}</b></div>
            <div><small>Completed</small><b style="font-size:14px">{{ $mpfmTest->test_completed_at ? $mpfmTest->test_completed_at->format('d M Y H:i:s') : 'N/A' }}</b></div>
            <div><small>Total duration</small><b>{{ round($mpfmTest->test_duration_hours ?? 0, 2) }} hrs</b></div>
            <div><small>Stable duration</small><b>{{ round($mpfmTest->test_stable_duration_hours ?? 0, 2) }} hrs</b></div>
        </div>
    </div></div>
    @endif
    @if($mpfmTest->notes)<div class="sec"><div class="note" style="margin-top:0"><small>Operator notes</small>{{ $mpfmTest->notes }}</div></div>@endif

    {{-- TABS --}}
    <div class="sec">
        <div class="panel rv">
            <div class="tabs" role="tablist">
                <button class="tab on" data-p="pVal"><i class="fe fe-shield"></i> Validation</button>
                <button class="tab" data-p="pCmp"><i class="fe fe-sliders"></i> Historical comparison</button>
                <button class="tab" data-p="pTrd"><i class="fe fe-trending-up"></i> Trends</button>
            </div>

            {{-- validation --}}
            <div class="pane on" id="pVal">
                @if($v)
                    <div class="sum">
                        <div><small>Status</small><b style="color:{{ $col }}">{{ ucfirst($vs) }}</b></div>
                        <div><small>Confidence</small><b>{{ $score !== null ? $f($score, 0).'%' : '—' }}</b></div>
                        <div><small>Suggestion</small><b>{{ $sug ? ucfirst($sug) : '—' }}</b></div>
                        <div><small>Rules</small><b>{{ array_sum($rc) }}</b></div>
                    </div>
                    @if(array_sum($rc))
                        <div class="stack" aria-label="Rule results"><i style="--w:{{ $rc['pass'] / $rt * 100 }}%;background:#34d399"></i><i style="--w:{{ $rc['warning'] / $rt * 100 }}%;background:#fbbf24"></i><i style="--w:{{ $rc['fail'] / $rt * 100 }}%;background:#f87171"></i></div>
                        <div class="meta" style="font-size:12px"><span style="color:#34d399">{{ $rc['pass'] }} pass</span><span style="color:#fbbf24">{{ $rc['warning'] }} warning</span><span style="color:#f87171">{{ $rc['fail'] }} fail</span></div>
                    @endif
                    <div class="rules">
                        @foreach($rules as $r)
                            @php $k = $r['result'] ?? 'pass'; @endphp
                            <div class="rule {{ $k }}"><div class="ri"><i class="fe {{ $k === 'pass' ? 'fe-check' : ($k === 'warning' ? 'fe-alert-triangle' : 'fe-x') }}"></i></div>
                                <div><b>{{ $r['rule_name'] ?? 'Rule' }}</b><p>{{ $r['message'] ?? '' }}</p></div><span class="chip {{ $k }}">{{ ucfirst($k) }}</span></div>
                        @endforeach
                    </div>
                @else
                    <div class="empty">
                        <svg viewBox="0 0 120 90"><path d="M60,8 L100,22 V48 C100,68 82,82 60,88 C38,82 20,68 20,48 V22 Z" fill="none" stroke="#64748b" stroke-width="3" stroke-dasharray="6 5"/><path d="M44,48 L56,60 L78,36" fill="none" stroke="#64748b" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        No validation data for this test yet.<br><span style="font-size:12px">Validation needs the parsed log data of the test — run it with “Revalidate” once the log has been uploaded.</span>
                    </div>
                @endif
            </div>

            {{-- comparison --}}
            <div class="pane" id="pCmp">
                @if($hist && count($devs))
                    <div class="sum" style="margin-bottom:18px">
                        <div><small>Tests compared</small><b>{{ $hist['tests_compared'] ?? '—' }}</b></div>
                        <div><small>From</small><b style="font-size:14px">{{ $hist['date_range']['from'] ?? '—' }}</b></div>
                        <div><small>To</small><b style="font-size:14px">{{ $hist['date_range']['to'] ?? '—' }}</b></div>
                    </div>
                    <div class="g21">
                        <div>
                            <svg viewBox="0 0 600 {{ $ch }}" style="width:100%;height:auto" role="img" aria-label="Deviation from the historical average">
                                @php $bx0 = fn ($p) => $cx + $p * $sc; @endphp
                                <rect x="{{ $bx0(-10) }}" y="34" width="{{ 20 * $sc }}" height="{{ $ch - 64 }}" fill="#fbbf24" opacity=".07"/>
                                <rect x="{{ $bx0(-5) }}" y="34" width="{{ 10 * $sc }}" height="{{ $ch - 64 }}" fill="#34d399" opacity=".1"/>
                                @foreach([-$R, -$R / 2, 0, $R / 2, $R] as $tk)
                                    <line x1="{{ $bx0($tk) }}" x2="{{ $bx0($tk) }}" y1="34" y2="{{ $ch - 28 }}" stroke="{{ $tk == 0 ? 'rgba(255,255,255,.45)' : 'rgba(255,255,255,.08)' }}"/>
                                    <text x="{{ $bx0($tk) }}" y="{{ $ch - 10 }}" text-anchor="middle" font-size="12" fill="#64748b">{{ $tk > 0 ? '+' : '' }}{{ $f($tk, $R % 2 ? 1 : 0) }}%</text>
                                @endforeach
                                <text x="{{ $bx0(0) }}" y="20" text-anchor="middle" font-size="12" font-weight="700" fill="#34d399">historical average</text>
                                @php $i = 0; @endphp
                                @foreach($devs as $param => $d)
                                    @php
                                        $pd = (float) ($d['percent_diff'] ?? 0); $cl = max(-$R, min($R, $pd)); $bw = abs($cl) * $sc; $bx = $cl >= 0 ? $cx : $cx - $bw; $y = 44 + $i * $rh; $i++;
                                        $c = $dcol[$d['deviation_type'] ?? ''] ?? '#64748b';
                                    @endphp
                                    <text x="4" y="{{ $y + 26 }}" font-size="13.5" font-weight="700" fill="#e8eefc">{{ \Illuminate\Support\Str::limit(ucfirst(str_replace('_', ' ', $param)), 14) }}</text>
                                    <rect class="db" x="{{ $bx }}" y="{{ $y + 8 }}" width="{{ max($bw, 2) }}" height="26" rx="6" fill="{{ $c }}" style="transform-origin:{{ $cx }}px 0;transition-delay:{{ $i * .12 }}s"/>
                                    <text x="{{ $cl >= 0 ? $bx + $bw + 8 : $bx - 8 }}" y="{{ $y + 27 }}" text-anchor="{{ $cl >= 0 ? 'start' : 'end' }}" font-family="Space Grotesk" font-weight="700" font-size="14" fill="{{ $c }}">{{ $pd > 0 ? '+' : '' }}{{ $f($pd, 1) }}%</text>
                                @endforeach
                            </svg>
                        </div>
                        <div class="scroll"><table class="t" style="min-width:0">
                            <thead><tr><th>Parameter</th><th>Current</th><th>Average</th><th>Δ</th></tr></thead>
                            <tbody>@foreach($devs as $param => $d)<tr><td>{{ ucfirst(str_replace('_', ' ', $param)) }}</td><td>{{ $d['current_value'] ?? '—' }}</td><td>{{ $d['average'] ?? '—' }}</td><td><span class="bd {{ $dcls[$d['deviation_type'] ?? ''] ?? 'na' }}">{{ ($d['percent_diff'] ?? 0) > 0 ? '+' : '' }}{{ $d['percent_diff'] ?? 0 }}%</span></td></tr>@endforeach</tbody>
                        </table></div>
                    </div>
                @else
                    <div class="empty">
                        <svg viewBox="0 0 120 90"><rect x="14" y="30" width="16" height="44" rx="3" fill="#64748b" opacity=".5"/><rect x="38" y="18" width="16" height="56" rx="3" fill="#64748b" opacity=".5"/><rect x="70" y="40" width="16" height="34" rx="3" fill="#64748b" opacity=".5"/><rect x="94" y="26" width="16" height="48" rx="3" fill="#64748b" opacity=".5"/><path d="M10,14 Q60,0 112,12" fill="none" stroke="#fff" stroke-opacity=".4" stroke-dasharray="4 4"/></svg>
                        Not enough history to compare against yet.<br><span style="font-size:12px">The current test is compared with earlier approved tests on the same oil string.</span>
                    </div>
                @endif
            </div>

            {{-- trends --}}
            <div class="pane" id="pTrd">
                @if($hasCharts)
                    <div class="trg">
                        @foreach(['production_rates' => ['Production rates', 'Rate (STB/d)'], 'gor_trend' => ['GOR', 'Scf/STB'], 'bsw_trend' => ['BSW', '%'], 'wlr_trend' => ['WLR', '']] as $k => [$tt, $un])
                            @if(isset($trendingCharts[$k]))<div><div class="ph" style="margin-bottom:8px"><h3 style="font-size:14px">{{ $tt }}</h3><span class="sub">{{ $un }}</span></div><div id="ch_{{ $k }}"></div></div>@endif
                        @endforeach
                    </div>
                @else
                    <div class="empty">
                        <svg viewBox="0 0 120 90"><path d="M8,70 L36,46 L58,58 L84,26 L112,38" fill="none" stroke="#64748b" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="7 6"/><circle cx="84" cy="26" r="5" fill="#64748b"/></svg>
                        No trend data for this oil string yet.<br><span style="font-size:12px">Trends are drawn from the approved tests on the same string.</span>
                    </div>
                @endif
                @if(count($trends))
                    <div class="ph" style="margin:24px 0 12px"><h3 style="font-size:14px">Trend analysis</h3></div>
                    <div style="display:grid;gap:10px">
                    @foreach($trends as $param => $tr)
                        @php $dir = $tr['direction'] ?? ''; @endphp
                        <div class="trc"><b>{{ ucfirst(str_replace('_', ' ', $param)) }}</b>
                            <span class="chip {{ $dir === 'increasing' ? 'bad' : ($dir === 'decreasing' ? 'info' : 'ok') }}">{{ ucfirst($dir) }}</span>
                            <span style="margin-left:auto;text-align:right"><small>R² {{ $tr['r_squared'] ?? '—' }}</small><br><small>Significance: <b>{{ ucfirst($tr['significance'] ?? '—') }}</b></small></span></div>
                    @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if($mpfmTest->detailed_metrics)
    <div class="sec"><div class="panel rv">
        <div class="ph"><h3><i class="fe fe-list"></i> Detailed metrics analysis</h3></div>
        <div class="light"><x-detailed-metrics-display :metrics="$mpfmTest->detailed_metrics" /></div>
    </div></div>
    @endif
</div>
@endsection

@section('js')
<script src="{{ asset('assets/js/ofcharts.js') }}"></script>
<script>
(function () {
    var root = document.getElementById('ofp');
    var reduce = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;
    var trending = @json($trendingCharts ?? []);
    var meta = { production_rates: { dec: 0, zero: true }, gor_trend: { dec: 0, area: true }, bsw_trend: { dec: 1, zero: true }, wlr_trend: { dec: 2, zero: true } };
    var rendered = false;
    function drawTrends() {
        if (rendered) return; rendered = true;
        Object.keys(trending).forEach(function (k) {
            var el = document.getElementById('ch_' + k); if (!el) return;
            var d = trending[k];
            OFCharts.line(el, Object.assign({
                labels: d.labels || [], height: 240,
                series: (d.datasets || []).map(function (s, i) {
                    return { name: s.label, data: (s.data || []).map(function (x) { var n = parseFloat(x); return isNaN(n) ? null : n; }), color: OFCharts.colors[i % OFCharts.colors.length] };
                })
            }, meta[k] || {}));
        });
    }

    root.querySelectorAll('.tab').forEach(function (t) {
        t.addEventListener('click', function () {
            root.querySelectorAll('.tab').forEach(function (x) { x.classList.remove('on'); }); t.classList.add('on');
            root.querySelectorAll('.pane').forEach(function (p) { p.classList.remove('on', 'in'); });
            var p = document.getElementById(t.dataset.p); p.classList.add('on'); setTimeout(function () { p.classList.add('in'); }, 30);
            if (t.dataset.p === 'pTrd') drawTrends();
        });
    });

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
