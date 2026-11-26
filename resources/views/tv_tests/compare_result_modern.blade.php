@extends('layouts.master')

@section('title', 'Test Comparison: TV vs MPFM')

@section('css')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
<style>
.tvr { direction:ltr; text-align:left; --bg:#050a15; --ink:#e8eefc; --sub:#9fb0cf; --mute:#64748b; --cyan:#22d3ee; --amber:#fbbf24; --green:#34d399; --red:#f87171;
    --line:rgba(96,165,250,.2); --glass:rgba(255,255,255,.05); --grad:linear-gradient(135deg,#3b82f6,#22d3ee); --ease:cubic-bezier(.22,1,.36,1);
    position:relative; overflow:hidden; isolation:isolate; padding:26px; border-radius:26px; background:var(--bg); color:var(--ink); font-family:'Inter',sans-serif; -webkit-font-smoothing:antialiased; }
.tvr *, .tvr *::before, .tvr *::after { box-sizing:border-box; }
.tvr a { text-decoration:none; }
.tvr h1, .tvr h3, .tvr p { margin:0; color:inherit; }
.tvr .bgl { position:absolute; inset:0; z-index:-1; pointer-events:none; overflow:hidden; }
.tvr .bgl::before { content:""; position:absolute; inset:-50%; background-image:linear-gradient(rgba(59,130,246,.07) 1px,transparent 1px),linear-gradient(90deg,rgba(59,130,246,.07) 1px,transparent 1px); background-size:56px 56px; transform:perspective(700px) rotateX(58deg) translateY(-6%); animation:trGrid 14s linear infinite; mask-image:radial-gradient(ellipse at 50% 30%,#000 10%,transparent 70%); -webkit-mask-image:radial-gradient(ellipse at 50% 30%,#000 10%,transparent 70%); }
@keyframes trGrid { to { background-position:0 56px; } }
.tvr .orb { position:absolute; border-radius:50%; filter:blur(90px); opacity:.3; animation:trFloat 16s ease-in-out infinite; }
.tvr .o1 { width:440px; height:440px; background:#2563eb; top:-140px; left:-120px; }
.tvr .o2 { width:400px; height:400px; background:#0891b2; bottom:-160px; right:-100px; animation-delay:-6s; }
@keyframes trFloat { 50% { transform:translate(40px,-40px) scale(1.12); } }
.tvr .lg { --h:76px; display:inline-block; flex:none; height:var(--h); aspect-ratio:1/1; border-radius:calc(var(--h) * .14); background:#071326 url('/assets/img/brand/logo.svg') center/cover no-repeat; border:2px solid rgba(34,211,238,.5); box-shadow:0 0 0 1px rgba(59,130,246,.5),0 8px 30px rgba(0,0,0,.45),0 0 26px rgba(34,211,238,.35); position:relative; overflow:hidden; animation:trGlow 3.2s ease-in-out infinite; }
.tvr .lg::after { content:""; position:absolute; top:0; left:-130%; width:60%; height:100%; background:linear-gradient(90deg,transparent,rgba(34,211,238,.45),transparent); transform:skewX(-20deg); animation:trShine 4.5s infinite; }
@keyframes trShine { 60%,100% { left:160%; } }
@keyframes trGlow { 50% { box-shadow:0 0 0 1px rgba(59,130,246,.6),0 10px 40px rgba(0,0,0,.5),0 0 54px rgba(34,211,238,.65); } }
.tvr .rv { opacity:0; transform:translateY(26px); transition:opacity .8s var(--ease), transform .8s var(--ease); transition-delay:calc(var(--d,0) * 80ms); }
.tvr .rv.in { opacity:1; transform:none; }

.tvr .hero { display:grid; grid-template-columns:1fr auto; gap:24px; align-items:center; padding:24px 28px; border-radius:22px; border:1px solid var(--line); background:linear-gradient(100deg,rgba(5,10,21,.94) 45%,rgba(5,10,21,.5)),url('/assets/img/brand/Offshore.jpg') center/cover; }
.tvr .who { display:flex; align-items:center; gap:20px; }
.tvr .eyebrow { font-size:11px; font-weight:700; letter-spacing:.16em; text-transform:uppercase; color:var(--cyan); }
.tvr .hero h1 { font-size:clamp(22px,2.6vw,34px); font-weight:900; letter-spacing:-.03em; margin:5px 0 8px; }
.tvr .hero h1 em { font-style:normal; background:linear-gradient(120deg,#60a5fa,#22d3ee,#fbbf24,#60a5fa); background-size:250% 100%; -webkit-background-clip:text; background-clip:text; color:transparent; animation:trFlow 6s linear infinite; }
@keyframes trFlow { to { background-position:250% 0; } }
.tvr .crumbs { display:flex; gap:8px; flex-wrap:wrap; align-items:center; font-size:12.5px; color:var(--sub); }
.tvr .crumbs a { color:var(--cyan); } .tvr .crumbs a:hover { text-decoration:underline; }
.tvr .verdict { display:flex; align-items:center; gap:14px; padding:12px 18px; border-radius:20px; border:1px solid var(--vc); background:rgba(0,0,0,.35); backdrop-filter:blur(6px); box-shadow:0 0 30px color-mix(in srgb, var(--vc) 28%, transparent); }
.tvr .verdict svg { width:70px; height:70px; flex:none; }
.tvr .verdict b { display:block; font:800 17px 'Space Grotesk'; color:var(--vc); letter-spacing:.03em; }
.tvr .verdict small { color:var(--sub); font-size:12px; }
.tvr .verdict.ok { --vc:#34d399; } .tvr .verdict.warn { --vc:#fbbf24; } .tvr .verdict.bad { --vc:#f87171; } .tvr .verdict.na { --vc:#64748b; }
.tvr .arc { fill:none; stroke-width:8; stroke-linecap:round; transform:rotate(-90deg); transform-origin:50% 50%; animation:trArc 1.6s var(--ease) both; }
@keyframes trArc { from { stroke-dasharray:0 100; } }

.tvr .btn2 { display:inline-flex; align-items:center; gap:8px; padding:11px 20px; border-radius:50px; font-size:13px; font-weight:700; border:1px solid var(--line); background:var(--glass); color:var(--ink); cursor:pointer; transition:transform .3s var(--ease), border-color .3s, box-shadow .3s; }
.tvr .btn2:hover { transform:translateY(-3px); border-color:var(--cyan); color:#fff; }
.tvr .btn2.p { background:var(--grad); color:#04111f; border:0; box-shadow:0 8px 24px rgba(34,211,238,.28); } .tvr .btn2.p:hover { color:#04111f; }

.tvr .ids { display:grid; grid-template-columns:1fr 150px 1fr; gap:14px; margin-top:18px; align-items:stretch; }
.tvr .panel { position:relative; border-radius:22px; border:1px solid var(--line); background:linear-gradient(180deg,rgba(255,255,255,.045),rgba(255,255,255,.015)); padding:20px; min-width:0; }
.tvr .panel.tv { border-top:3px solid var(--amber); } .tvr .panel.mp { border-top:3px solid var(--cyan); }
.tvr .ph { display:flex; align-items:center; gap:14px; margin-bottom:14px; flex-wrap:wrap; }
.tvr .ph svg.ic { width:64px; height:44px; flex:none; }
.tvr .ph h3 { font-size:16px; font-weight:800; display:flex; align-items:center; gap:10px; }
.tvr .ph h3 i.hi { width:30px; height:30px; border-radius:10px; display:grid; place-items:center; background:var(--grad); color:#04111f; font-size:14px; }
.tvr .ph small { display:block; font-size:12px; color:var(--mute); font-weight:500; margin-top:2px; }
.tvr .ph .sub { margin-left:auto; font-size:12px; color:var(--sub); }
.tvr .kv { display:grid; grid-template-columns:1fr 1fr; gap:10px; }
.tvr .kv div { padding:10px 12px; border-radius:13px; background:var(--glass); border:1px solid var(--line); min-width:0; }
.tvr .kv small { display:block; font-size:9.5px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--mute); }
.tvr .kv b { display:block; margin-top:3px; font-size:13.5px; color:#fff; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.tvr .mid { display:flex; flex-direction:column; align-items:center; justify-content:center; gap:10px; text-align:center; }
.tvr .mid .ln { flex:1; width:2px; min-height:24px; background:repeating-linear-gradient(180deg,rgba(34,211,238,.6) 0 6px,transparent 6px 12px); animation:trMarch 1s linear infinite; }
@keyframes trMarch { to { background-position:0 12px; } }
.tvr .tagc { padding:6px 12px; border-radius:50px; font-size:10.5px; font-weight:700; letter-spacing:.07em; text-transform:uppercase; border:1px solid var(--line); background:var(--glass); color:var(--sub); }
.tvr .tagc.ok { color:var(--green); border-color:rgba(52,211,153,.5); background:rgba(52,211,153,.1); }
.tvr .tagc.warn { color:var(--amber); border-color:rgba(251,191,36,.5); background:rgba(251,191,36,.1); }

.tvr .sec { margin-top:18px; }
.tvr .banner { display:flex; gap:14px; align-items:flex-start; padding:16px 20px; border-radius:18px; border:1px solid var(--vc); background:color-mix(in srgb, var(--vc) 9%, transparent); }
.tvr .banner i { font-size:22px; color:var(--vc); margin-top:1px; }
.tvr .banner b { display:block; font-size:15px; color:#fff; margin-bottom:3px; }
.tvr .banner p { color:var(--sub); font-size:13px; line-height:1.55; }
.tvr .banner.ok { --vc:#34d399; } .tvr .banner.warn { --vc:#fbbf24; } .tvr .banner.bad { --vc:#f87171; }

.tvr .kpis { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; }
.tvr .kpi { position:relative; padding:16px; border-radius:18px; border:1px solid var(--line); background:var(--glass); overflow:hidden; transition:transform .4s var(--ease), border-color .3s; }
.tvr .kpi:hover { transform:translateY(-5px); border-color:var(--c,var(--cyan)); }
.tvr .kpi::before { content:""; position:absolute; left:0; top:0; height:3px; width:100%; background:var(--c,var(--cyan)); }
.tvr .kpi .l { font-size:10.5px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--mute); }
.tvr .kpi .v { font:700 clamp(22px,2.2vw,30px)/1.1 'Space Grotesk'; margin-top:6px; color:#fff; }
.tvr .kpi .u { font-size:11.5px; color:var(--sub); margin-top:3px; }

.tvr .diagram svg { width:100%; height:auto; display:block; }
.tvr .diagram text { font-family:'Inter',sans-serif; }
.tvr .scroll { overflow-x:auto; } .tvr .scroll > * { min-width:720px; }
.tvr .tg { font-size:9.5px; font-weight:700; letter-spacing:.1em; fill:#94a3b8; }
.tvr .rl { font-size:8.5px; font-weight:700; letter-spacing:.14em; }
.tvr .rv2 { font:700 15px 'Space Grotesk',sans-serif; fill:#fff; }
.tvr .rvu { font-size:9px; fill:#9fb0cf; }
.tvr .flow { fill:none; stroke-width:2; stroke-linecap:round; stroke-dasharray:3 8; animation:trDash 1.2s linear infinite; }
@keyframes trDash { to { stroke-dashoffset:-22; } }
.tvr .wv { animation:trWave 3s linear infinite; } @keyframes trWave { to { transform:translateX(-30px); } }
.tvr .pulse { animation:trPulse 2.2s ease-in-out infinite; } @keyframes trPulse { 50% { opacity:.4; } }
.tvr .spin { transform-box:fill-box; transform-origin:center; animation:trSpin 7s linear infinite; } @keyframes trSpin { to { transform:rotate(360deg); } }

.tvr .db { transform:scaleX(0); transition:transform 1.2s var(--ease); }
.tvr .in .db { transform:scaleX(1); }
.tvr .dchart svg { width:100%; height:auto; display:block; }
.tvr .dchart text { font-family:'Inter',sans-serif; }

.tvr .crow { display:grid; grid-template-columns:130px 1fr 96px; gap:16px; align-items:center; padding:11px 0; border-top:1px solid rgba(255,255,255,.06); }
.tvr .crow:first-of-type { border-top:0; }
.tvr .crow .n { font-size:12px; font-weight:700; letter-spacing:.07em; text-transform:uppercase; color:var(--sub); }
.tvr .crow .n span { display:block; font-weight:500; text-transform:none; letter-spacing:0; color:var(--mute); margin-top:2px; }
.tvr .bars { display:grid; gap:6px; }
.tvr .bar { height:22px; border-radius:7px; background:rgba(255,255,255,.05); overflow:hidden; position:relative; }
.tvr .bar i { position:absolute; inset:0 auto 0 0; width:0; border-radius:7px; transition:width 1.1s var(--ease); }
.tvr .in .bar i { width:var(--w); }
.tvr .bar.a i { background:linear-gradient(90deg,#f59e0b,#fbbf24); } .tvr .bar.b i { background:linear-gradient(90deg,#0891b2,#22d3ee); }
.tvr .bar span { position:absolute; left:9px; top:0; bottom:0; display:flex; align-items:center; font:600 11px 'Inter'; color:#fff; text-shadow:0 1px 3px rgba(0,0,0,.8); white-space:nowrap; }
.tvr .dev { text-align:center; padding:6px 0; border-radius:50px; font:700 13px 'Space Grotesk'; }
.tvr .dev.ok { color:var(--green); background:rgba(52,211,153,.12); border:1px solid rgba(52,211,153,.4); }
.tvr .dev.warn { color:var(--amber); background:rgba(251,191,36,.1); border:1px solid rgba(251,191,36,.4); }
.tvr .dev.bad { color:var(--red); background:rgba(248,113,113,.1); border:1px solid rgba(248,113,113,.4); }
.tvr .dev.na { color:var(--mute); border:1px solid var(--line); }

.tvr .g2 { display:grid; grid-template-columns:1.2fr 1fr; gap:18px; }
.tvr .issues { display:grid; gap:10px; }
.tvr .issue { display:flex; align-items:center; gap:14px; padding:13px 16px; border-radius:16px; border:1px solid var(--vc); background:color-mix(in srgb, var(--vc) 8%, transparent); }
.tvr .issue.bad { --vc:#f87171; } .tvr .issue.warn { --vc:#fbbf24; }
.tvr .issue .p { font:800 20px 'Space Grotesk'; color:var(--vc); min-width:84px; }
.tvr .issue b { display:block; font-size:13.5px; color:#fff; } .tvr .issue small { color:var(--sub); font-size:12px; }
.tvr .okbox { padding:22px; text-align:center; border:2px dashed rgba(52,211,153,.35); border-radius:16px; color:#bbf7d0; font-size:13.5px; }
.tvr .okbox i { font-size:34px; display:block; color:var(--green); margin-bottom:6px; }
.tvr .tips { margin:0; padding:0; list-style:none; display:grid; gap:10px; }
.tvr .tips li { display:flex; gap:10px; font-size:13px; color:var(--sub); line-height:1.55; }
.tvr .tips i { color:var(--cyan); margin-top:3px; flex:none; }
.tvr .tips b { color:#fff; }

.tvr table.t { width:100%; border-collapse:separate; border-spacing:0 8px; min-width:640px; }
.tvr table.t th { font-size:10.5px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--mute); padding:4px 14px; text-align:left; }
.tvr table.t td { padding:12px 14px; background:#0c1528; border-top:1px solid var(--line); border-bottom:1px solid var(--line); font-size:13.5px; color:var(--ink); }
.tvr table.t td:first-child { border-left:1px solid var(--line); border-radius:14px 0 0 14px; font-weight:700; color:#fff; }
.tvr table.t td:last-child { border-right:1px solid var(--line); border-radius:0 14px 14px 0; }
.tvr .bd { display:inline-block; padding:4px 12px; border-radius:50px; font:700 12px 'Space Grotesk'; border:1px solid var(--line); }
.tvr .bd.ok { color:var(--green); border-color:rgba(52,211,153,.5); background:rgba(52,211,153,.1); } .tvr .bd.warn { color:var(--amber); border-color:rgba(251,191,36,.5); background:rgba(251,191,36,.1); } .tvr .bd.bad { color:var(--red); border-color:rgba(248,113,113,.5); background:rgba(248,113,113,.1); } .tvr .bd.na { color:var(--mute); }
.tvr .xs { display:grid; grid-template-columns:repeat(3,1fr); gap:10px; margin-top:16px; }
.tvr .xs div { padding:10px 12px; border-radius:13px; background:var(--glass); border:1px solid var(--line); }
.tvr .xs small { display:block; font-size:9.5px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--mute); }
.tvr .xs b { font:700 14px 'Space Grotesk'; color:#fff; }
.tvr .acts { display:flex; gap:12px; flex-wrap:wrap; margin-top:18px; }

@media (max-width:1100px) { .tvr .ids, .tvr .g2 { grid-template-columns:1fr; } .tvr .mid { flex-direction:row; } .tvr .mid .ln { width:auto; height:2px; flex:1; background:repeating-linear-gradient(90deg,rgba(34,211,238,.6) 0 6px,transparent 6px 12px); min-height:0; } .tvr .kpis { grid-template-columns:repeat(2,1fr); } .tvr .hero { grid-template-columns:1fr; } }
@media (max-width:640px) { .tvr { padding:14px; border-radius:18px; } .tvr .who { flex-direction:column; align-items:flex-start; } .tvr .crow { grid-template-columns:1fr; gap:6px; } .tvr .xs { grid-template-columns:1fr; } }
@media (prefers-reduced-motion:reduce) { .tvr *, .tvr *::before, .tvr *::after { animation:none !important; transition:none !important; } .tvr .rv { opacity:1; transform:none; } .tvr .db { transform:none; } .tvr .bar i { width:var(--w); } }
</style>
@endsection

@section('content')
@php
    $f = fn ($v, $d = 2) => number_format((float) $v, $d);
    $defs = [
        'oil_rate' => ['Oil rate', 'B/D', 0, 'rel'],
        'gas_rate' => ['Gas rate', 'MMscf/D', 2, 'rel'],
        'water_cut' => ['Water cut', '%', 1, 'pt'],
        'pressure' => ['Pressure', 'psia', 1, 'rel'],
        'temperature' => ['Temperature', '°F', 1, 'rel'],
        'gor' => ['GOR', 'scf/stb', 0, 'rel'],
    ];
    // deviation of the MPFM from the Testing Unit (the reference)
    $rows = [];
    foreach ($defs as $key => [$label, $unit, $dec, $type]) {
        $m = $metrics[$key] ?? null;
        if (! $m) continue;
        $tv = (float) $m['tv_value']; $mp = (float) $m['mpfm_value'];
        if ($type === 'pt') { $d = $mp - $tv; $st = abs($d) <= 2 ? 'ok' : (abs($d) <= 5 ? 'warn' : 'bad'); }
        elseif ($tv > 0) { $d = ($mp - $tv) / $tv * 100; $a = abs($d); $st = $a <= 5 ? 'ok' : ($a <= 10 ? 'warn' : 'bad'); }
        else { $d = null; $st = 'na'; }
        $rows[$key] = compact('label', 'unit', 'dec', 'type', 'tv', 'mp', 'd', 'st');
    }
    $rel = array_filter($rows, fn ($r) => $r['d'] !== null);
    $n = count($rel);
    $okN = count(array_filter($rel, fn ($r) => $r['st'] === 'ok'));
    $hasBad = (bool) array_filter($rel, fn ($r) => $r['st'] === 'bad');
    $hasWarn = (bool) array_filter($rel, fn ($r) => $r['st'] === 'warn');
    $vstate = $n === 0 ? 'na' : ($hasBad ? 'bad' : ($hasWarn ? 'warn' : 'ok'));
    $vtext = ['ok' => 'Match', 'warn' => 'Monitor', 'bad' => 'Review needed', 'na' => 'No data'][$vstate];
    $score = $n ? round($okN / $n * 100) : 0;
    $absDevs = array_map(fn ($r) => abs($r['d']), $rel);
    $avgDev = $n ? array_sum($absDevs) / $n : null;
    $maxKey = $n ? array_search(max($absDevs), $absDevs) : null;
    $maxDev = $n ? $absDevs[$maxKey] : null;
    $days = (int) round(abs($tvTest->test_date->diffInDays($mpfmTest->test_date)));
    $sameStr = $tvTest->oil_string_id === $mpfmTest->oil_string_id;
    $o = $rows['oil_rate'] ?? null; $g = $rows['gas_rate'] ?? null; $w = $rows['water_cut'] ?? null;
    $gv = fn ($r, $k) => $r ? $f($r[$k], $r['dec']) : '—';
    $relRows = array_filter($rows, fn ($r) => $r['type'] === 'rel' && $r['d'] !== null);
    $R = 15; foreach ($relRows as $r) { $R = max($R, ceil(abs($r['d']) / 5) * 5); } $R = min($R, 100);
    $cx = 350; $sc = 200 / $R; $rh = 58; $ch = 44 + count($relRows) * $rh + 30;
    $stCol = ['ok' => '#34d399', 'warn' => '#fbbf24', 'bad' => '#f87171', 'na' => '#64748b'];
    $wt = $mpfmTest->wellTest;
@endphp

<div class="tvr" id="tvr">
    <div class="bgl"><div class="orb o1"></div><div class="orb o2"></div></div>

    {{-- HERO --}}
    <div class="hero rv">
        <div>
            <div class="who">
                <span class="lg"></span>
                <div>
                    <div class="eyebrow">Comparison report</div>
                    <h1>Testing Unit <em>vs MPFM</em></h1>
                    <div class="crumbs">
                        <a href="{{ route('tv_tests.index') }}"><i class="fe fe-home"></i> TV tests</a><span>/</span>
                        <a href="{{ route('tv_tests.show', $tvTest->id) }}">Test #{{ $tvTest->id }}</a><span>/</span>
                        <span>MPFM #{{ $mpfmTest->id }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="verdict {{ $vstate }}">
            <svg viewBox="0 0 100 100"><circle class="arc" cx="50" cy="50" r="40" pathLength="100" stroke="rgba(255,255,255,.08)" style="animation:none;stroke-dasharray:100 100"/><circle class="arc" cx="50" cy="50" r="40" pathLength="100" stroke="{{ $stCol[$vstate] }}" style="stroke-dasharray:{{ $score }} 100"/><text x="50" y="57" text-anchor="middle" font-family="Space Grotesk" font-weight="700" font-size="22" fill="#fff">{{ $n ? $score.'%' : '—' }}</text></svg>
            <div><b>{{ $vtext }}</b><small>{{ $okN }} of {{ $n }} metrics within ±5%</small></div>
        </div>
    </div>

    {{-- IDENTITY --}}
    <div class="ids">
        <div class="panel tv rv">
            <div class="ph">
                <svg class="ic" viewBox="0 0 64 44"><path d="M4,10 H44 A10,12 0 0 1 44,34 H4 A10,12 0 0 1 4,10 Z" fill="#1e293b" stroke="#94a3b8" stroke-width="2"/><path d="M8,26 H46 A6,6 0 0 1 46,34 H8 Z" fill="#b45309"/><rect x="8" y="30" width="38" height="4" fill="#0ea5e9"/><path d="M0,16 H4 M54,14 H64 M54,30 H64" stroke="#94a3b8" stroke-width="3"/><circle cx="24" cy="6" r="5" fill="#0a1222" stroke="#fbbf24" stroke-width="1.6"/></svg>
                <div><h3>Well testing</h3><small>Reference reading</small></div>
            </div>
            <div class="kv">
                <div><small>Test</small><b>#{{ $tvTest->id }}</b></div>
                <div><small>Date</small><b>{{ $tvTest->test_date->format('Y-m-d') }}</b></div>
                <div><small>Vessel</small><b>{{ $tvTest->testVessel->name ?? 'N/A' }}</b></div>
                <div><small>Status</small><b>{{ ucfirst($tvTest->status) }}</b></div>
                <div><small>Oil field</small><b>{{ $tvTest->oilField->name ?? 'N/A' }}</b></div>
                <div><small>Operator</small><b>{{ $tvTest->user->name ?? 'N/A' }}</b></div>
            </div>
        </div>
        <div class="mid rv" style="--d:1">
            <div class="ln"></div>
            <span class="tagc {{ $sameStr ? 'ok' : 'warn' }}">{{ $sameStr ? 'Same string' : 'Different string' }}</span>
            <span class="tagc {{ $days <= 7 ? 'ok' : 'warn' }}">{{ $days }} day{{ $days === 1 ? '' : 's' }} apart</span>
            <div class="ln"></div>
        </div>
        <div class="panel mp rv" style="--d:2">
            <div class="ph">
                <svg class="ic" viewBox="0 0 64 44"><path d="M0,26 H14 L22,18 H42 L50,26 H64 V34 H50 L42,40 H22 L14,34 H0 Z" fill="#1e293b" stroke="#94a3b8" stroke-width="2" stroke-linejoin="round"/><path d="M32,4 V18" stroke="#94a3b8" stroke-width="3"/><circle cx="32" cy="6" r="5" fill="#0a1222" stroke="#22d3ee" stroke-width="1.6"/><path d="M26,30 H38" stroke="#22d3ee" stroke-width="2" stroke-dasharray="3 3"><animate attributeName="stroke-dashoffset" from="0" to="-12" dur="1s" repeatCount="indefinite"/></path></svg>
                <div><h3>MPFM</h3><small>Multiphase meter</small></div>
            </div>
            <div class="kv">
                <div><small>Test</small><b>#{{ $mpfmTest->id }}</b></div>
                <div><small>Date</small><b>{{ $mpfmTest->test_date->format('Y-m-d') }}</b></div>
                <div><small>Tower</small><b>{{ $mpfmTest->tower->name ?? 'N/A' }}</b></div>
                <div><small>Status</small><b>{{ ucfirst($mpfmTest->status) }}</b></div>
                <div><small>Oil field</small><b>{{ $mpfmTest->oilField->name ?? 'N/A' }}</b></div>
                <div><small>Data source</small><b>{{ $wt ? 'WellTest #'.$wt->id : 'Direct entry' }}</b></div>
            </div>
        </div>
    </div>

    {{-- BANNER --}}
    <div class="sec rv">
        @if($vstate === 'bad')
            <div class="banner bad"><i class="fe fe-alert-circle"></i><div><b>Significant differences detected</b><p>At least one measurement differs from the well testing by more than 10%. Review both measurements before relying on the MPFM for this well.</p></div></div>
        @elseif($vstate === 'warn')
            <div class="banner warn"><i class="fe fe-alert-triangle"></i><div><b>Differences worth monitoring</b><p>Some measurements differ by 5–10%. Watch the next test cycle for consistency.</p></div></div>
        @elseif($vstate === 'ok')
            <div class="banner ok"><i class="fe fe-check-circle"></i><div><b>The two measurements agree</b><p>Every compared metric is within 5% of the well testing reference.</p></div></div>
        @else
            <div class="banner warn"><i class="fe fe-info"></i><div><b>Not enough data to compare</b><p>The MPFM test has no values that match the well testing metrics.</p></div></div>
        @endif
    </div>

    {{-- KPIs --}}
    <div class="sec kpis">
        <div class="kpi rv" style="--c:#22d3ee"><div class="l">Average deviation</div><div class="v">{{ $avgDev === null ? '—' : $f($avgDev, 1).'%' }}</div><div class="u">across {{ $n }} metrics</div></div>
        <div class="kpi rv" style="--c:#f87171;--d:1"><div class="l">Largest deviation</div><div class="v">{{ $maxDev === null ? '—' : $f($maxDev, 1).'%' }}</div><div class="u">{{ $maxKey ? $rel[$maxKey]['label'] : '' }}</div></div>
        <div class="kpi rv" style="--c:#34d399;--d:2"><div class="l">Within tolerance</div><div class="v">{{ $okN }}<span style="font-size:.55em;color:var(--mute)"> / {{ $n }}</span></div><div class="u">±5% of reference</div></div>
        <div class="kpi rv" style="--c:#a78bfa;--d:3"><div class="l">Days apart</div><div class="v">{{ $days }}</div><div class="u">{{ $days <= 7 ? 'reliable window' : 're-run under similar conditions' }}</div></div>
    </div>

    {{-- PARALLEL LINES DIAGRAM --}}
    <div class="sec">
        <div class="panel diagram rv">
            <div class="ph"><h3><i class="hi fe fe-git-branch"></i> Two independent measurements of the same stream</h3><span class="sub">The MPFM and the well testing sit on separate, parallel lines</span></div>
            <div class="scroll"><div>
<svg viewBox="0 0 900 300" role="img" aria-label="Parallel MPFM and well testing lines">
<defs>
    <linearGradient id="rSteel" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#a8b6cb"/><stop offset=".15" stop-color="#f1f5f9"/><stop offset=".5" stop-color="#7c8aa1"/><stop offset="1" stop-color="#1b2433"/></linearGradient>
    <linearGradient id="rOil" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#f59e0b"/><stop offset="1" stop-color="#78350f"/></linearGradient>
    <linearGradient id="rWat" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#0ea5e9"/><stop offset="1" stop-color="#0c4a6e"/></linearGradient>
    <clipPath id="rVin"><path d="M338,196 H442 A18,30 0 0 1 442,256 H338 A18,30 0 0 1 338,196 Z"/></clipPath>
    <path id="rTop" d="M70,150 H120 V76 H800 V150 H850"/>
    <path id="rBot" d="M70,150 H120 V226 H800 V150"/>
</defs>
<g fill="none" stroke-linejoin="round">
    <use href="#rTop" stroke="#0b1220" stroke-width="13"/><use href="#rTop" stroke="#64748b" stroke-width="9"/>
    <use href="#rBot" stroke="#0b1220" stroke-width="13"/><use href="#rBot" stroke="#64748b" stroke-width="9"/>
</g>
{{-- well --}}
<circle cx="50" cy="150" r="22" fill="#0a1222" stroke="#34d399" stroke-width="2"/><path d="M50,138 V162 M40,150 H60" stroke="#34d399" stroke-width="2.4"/>
<text x="50" y="190" text-anchor="middle" class="tg">{{ \Illuminate\Support\Str::limit($tvTest->oilString->name ?? 'WELL', 12) }}</text>
<circle cx="120" cy="150" r="5" fill="#22d3ee"/>
{{-- MPFM (top) --}}
<path d="M290,76 H322 L346,60 H414 L438,76 H470" fill="none"/>
<path d="M322,66 L346,50 H414 L438,66 V86 L414,102 H346 L322,86 Z" fill="url(#rSteel)" stroke="#0b1220" stroke-width="1.4"/>
<rect x="364" y="22" width="34" height="28" rx="4" fill="#0a1222" stroke="#22d3ee" stroke-width="1.6"/><path d="M381,28 V44" stroke="#22d3ee" stroke-width="1.6" stroke-dasharray="3 3" class="flow"/>
<path d="M381,102 V118" stroke="#94a3b8" stroke-width="3"/><rect x="371" y="118" width="20" height="10" rx="2" fill="#0a1222" stroke="#22d3ee"/>
<text x="381" y="14" text-anchor="middle" class="tg" style="fill:#67e8f9">MPFM · MULTIPHASE METER</text>
<g><rect x="490" y="30" width="210" height="84" rx="12" fill="#0a1222" stroke="#22d3ee" stroke-opacity=".6"/>
<text x="504" y="48" class="rl" fill="#67e8f9">MPFM READS</text>
<text x="504" y="70" class="rv2">{{ $gv($o, 'mp') }}</text><text x="504" y="82" class="rvu">oil · B/D</text>
<text x="574" y="70" class="rv2">{{ $gv($g, 'mp') }}</text><text x="574" y="82" class="rvu">gas · MMscf/D</text>
<text x="650" y="70" class="rv2">{{ $gv($w, 'mp') }}</text><text x="650" y="82" class="rvu">WC · %</text>
<circle cx="686" cy="44" r="3.4" fill="#34d399" class="pulse"/></g>
{{-- Well testing (bottom) --}}
<g>
<rect x="354" y="258" width="26" height="22" fill="#334155"/><rect x="408" y="258" width="26" height="22" fill="#334155"/><rect x="336" y="278" width="118" height="5" rx="2" fill="#475569"/>
<path d="M320,196 H460 A24,30 0 0 1 460,256 H320 A24,30 0 0 1 320,196 Z" fill="url(#rSteel)" stroke="#0b1220" stroke-width="1.4"/>
<g clip-path="url(#rVin)"><rect x="300" y="196" width="170" height="62" fill="#04101f"/><rect x="300" y="222" width="130" height="36" fill="url(#rOil)"/>@if(($w['tv'] ?? 0) > 0)<rect x="300" y="{{ round(256 - min($w['tv'], 60) / 100 * 34, 1) }}" width="130" height="{{ round(min($w['tv'], 60) / 100 * 34 + 2, 1) }}" fill="url(#rWat)"/>@endif<rect x="428" y="222" width="3" height="36" fill="#cbd5e1"/><rect x="431" y="240" width="40" height="18" fill="url(#rOil)"/><g class="wv"><path d="M296,222 q7,-3 14,0 t14,0 t14,0 t14,0 t14,0 t14,0 t14,0 t14,0 t14,0 t14,0 t14,0 t14,0" fill="none" stroke="#fde68a" stroke-opacity=".8" stroke-width="1.2"/></g></g>
<circle cx="378" cy="186" r="9" fill="#0a1222" stroke="#fbbf24" stroke-width="1.5"/><path d="M378,186 L381,181" stroke="#fbbf24" stroke-width="1.5"/><path d="M378,195 V198" stroke="#94a3b8" stroke-width="3"/>
<text x="390" y="176" text-anchor="middle" class="tg" style="fill:#fcd34d">TEST VESSEL · 3-PHASE SEPARATOR</text>
<rect x="490" y="186" width="210" height="84" rx="12" fill="#0a1222" stroke="#fbbf24" stroke-opacity=".6"/>
<text x="504" y="204" class="rl" fill="#fcd34d">TEST VESSEL READS</text>
<text x="504" y="226" class="rv2">{{ $gv($o, 'tv') }}</text><text x="504" y="238" class="rvu">oil · B/D</text>
<text x="574" y="226" class="rv2">{{ $gv($g, 'tv') }}</text><text x="574" y="238" class="rvu">gas · MMscf/D</text>
<text x="650" y="226" class="rv2">{{ $gv($w, 'tv') }}</text><text x="650" y="238" class="rvu">WC · %</text>
<circle cx="686" cy="200" r="3.4" fill="#34d399" class="pulse"/>
</g>
{{-- deviation link --}}
<path d="M742,114 V186" stroke="{{ $stCol[$o['st'] ?? 'na'] }}" stroke-width="2" stroke-dasharray="4 5" class="flow" style="stroke:{{ $stCol[$o['st'] ?? 'na'] }}"/>
<rect x="716" y="136" width="52" height="30" rx="15" fill="#0a1222" stroke="{{ $stCol[$o['st'] ?? 'na'] }}" stroke-width="1.6"/>
<text x="742" y="155" text-anchor="middle" font-family="Space Grotesk" font-weight="700" font-size="11.5" fill="{{ $stCol[$o['st'] ?? 'na'] }}">{{ ($o && $o['d'] !== null) ? (($o['d'] > 0 ? '+' : '').$f($o['d'], 1).'%') : '—' }}</text>
<text x="742" y="128" text-anchor="middle" class="tg" style="font-size:8px">OIL Δ</text>
{{-- header --}}
<circle cx="800" cy="150" r="5" fill="#22d3ee"/>
<text x="850" y="170" text-anchor="end" class="tg">HEADER</text>
@foreach([0, .9, 1.8, 2.7, 3.6] as $b)
    <circle r="3.2" fill="#fde68a"><animateMotion dur="4.5s" begin="{{ $b }}s" repeatCount="indefinite"><mpath href="#rTop"/></animateMotion></circle>
    <circle r="3.2" fill="#f59e0b"><animateMotion dur="4.5s" begin="{{ $b }}s" repeatCount="indefinite"><mpath href="#rBot"/></animateMotion></circle>
@endforeach
</svg>
            </div></div>
        </div>
    </div>

    {{-- DEVIATION CHART + PAIRED BARS --}}
    <div class="sec g2">
        <div class="panel dchart rv">
            <div class="ph"><h3><i class="hi fe fe-sliders"></i> Deviation from the reference</h3><span class="sub">MPFM vs well testing, %</span></div>
            @if(count($relRows))
            <svg viewBox="0 0 600 {{ $ch }}" role="img" aria-label="Deviation chart">
                @php $bandX = fn ($p) => $cx + $p * $sc; @endphp
                <rect x="{{ $bandX(-10) }}" y="34" width="{{ 20 * $sc }}" height="{{ $ch - 64 }}" fill="#fbbf24" opacity=".07"/>
                <rect x="{{ $bandX(-5) }}" y="34" width="{{ 10 * $sc }}" height="{{ $ch - 64 }}" fill="#34d399" opacity=".1"/>
                @foreach([-$R, -$R / 2, 0, $R / 2, $R] as $tk)
                    <line x1="{{ $bandX($tk) }}" x2="{{ $bandX($tk) }}" y1="34" y2="{{ $ch - 28 }}" stroke="{{ $tk == 0 ? 'rgba(255,255,255,.45)' : 'rgba(255,255,255,.08)' }}"/>
                    <text x="{{ $bandX($tk) }}" y="{{ $ch - 10 }}" text-anchor="middle" font-size="12" fill="#64748b">{{ $tk > 0 ? '+' : '' }}{{ $f($tk, $R % 2 ? 1 : 0) }}%</text>
                @endforeach
                <text x="{{ $bandX(0) }}" y="20" text-anchor="middle" font-size="12" font-weight="700" fill="#34d399">±5% tolerance</text>
                <text x="{{ $bandX(-$R) }}" y="20" font-size="11" fill="#64748b">◀ lower ◀</text>
                <text x="{{ $bandX($R) }}" y="20" text-anchor="end" font-size="11" fill="#64748b">higher ▶</text>
                @php $i = 0; @endphp
                @foreach($relRows as $key => $r)
                    @php
                        $y = 44 + $i * $rh; $clamped = max(-$R, min($R, $r['d'])); $bw = abs($clamped) * $sc; $bx = $clamped >= 0 ? $cx : $cx - $bw;
                        $i++;
                    @endphp
                    <text x="4" y="{{ $y + 26 }}" font-size="14" font-weight="700" fill="#e8eefc">{{ $r['label'] }}</text>
                    <rect class="db" x="{{ $bx }}" y="{{ $y + 8 }}" width="{{ max($bw, 2) }}" height="26" rx="6" fill="{{ $stCol[$r['st']] }}" style="transform-origin:{{ $cx }}px 0;transition-delay:{{ $i * .12 }}s"/>
                    <text x="{{ $clamped >= 0 ? $bx + $bw + 8 : $bx - 8 }}" y="{{ $y + 27 }}" text-anchor="{{ $clamped >= 0 ? 'start' : 'end' }}" font-family="Space Grotesk" font-weight="700" font-size="14" fill="{{ $stCol[$r['st']] }}">{{ $r['d'] > 0 ? '+' : '' }}{{ $f($r['d'], 1) }}%</text>
                @endforeach
            </svg>
            @else
                <div class="okbox" style="border-color:var(--line);color:var(--mute)">No relative deviations to plot</div>
            @endif
        </div>

        <div class="panel rv" style="--d:1">
            <div class="ph"><h3><i class="hi fe fe-bar-chart-2"></i> Side by side</h3><span class="sub">Absolute values</span></div>
            @foreach($rows as $r)
                @php $mx = max($r['tv'], $r['mp'], 0.0001); @endphp
                <div class="crow">
                    <div class="n">{{ $r['label'] }}<span>{{ $r['unit'] }}</span></div>
                    <div class="bars">
                        <div class="bar a"><i style="--w:{{ round($r['tv'] / $mx * 100, 1) }}%"></i><span>Well testing · {{ $f($r['tv'], $r['dec']) }}</span></div>
                        <div class="bar b"><i style="--w:{{ round($r['mp'] / $mx * 100, 1) }}%"></i><span>MPFM · {{ $f($r['mp'], $r['dec']) }}</span></div>
                    </div>
                    <div class="dev {{ $r['st'] }}">{{ $r['d'] === null ? '—' : (($r['d'] > 0 ? '+' : '').$f($r['d'], 1).($r['type'] === 'pt' ? ' pt' : '%')) }}</div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ANALYSIS --}}
    <div class="sec g2">
        <div class="panel rv">
            <div class="ph"><h3><i class="hi fe fe-alert-circle"></i> Issues &amp; analysis</h3></div>
            @php $flagged = array_filter($rows, fn ($r) => in_array($r['st'], ['warn', 'bad'])); @endphp
            @if(count($flagged))
                <div class="issues">
                    @foreach($flagged as $r)
                        <div class="issue {{ $r['st'] }}">
                            <div class="p">{{ ($r['d'] > 0 ? '+' : '').$f($r['d'], 1) }}{{ $r['type'] === 'pt' ? ' pt' : '%' }}</div>
                            <div><b>{{ $r['label'] }}</b><small>MPFM reads <b style="display:inline;color:#fff">{{ $r['d'] > 0 ? 'higher' : 'lower' }}</b> than the well testing · {{ $r['st'] === 'bad' ? 'needs review' : 'monitor closely' }}</small></div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="okbox"><i class="fe fe-check-circle"></i>No metric is outside tolerance.</div>
            @endif
            @if($wt)
                @php $wm = $mpfmMetrics; @endphp
                <div class="xs">
                    <div><small>MPFM GVF</small><b>{{ isset($wm['gvf']) ? $f($wm['gvf'], 1).' %' : '—' }}</b></div>
                    <div><small>Pressure range</small><b>{{ isset($wm['min_pressure'], $wm['max_pressure']) ? $f($wm['min_pressure'], 0).' – '.$f($wm['max_pressure'], 0) : '—' }}</b></div>
                    <div><small>Temp range</small><b>{{ isset($wm['min_temperature'], $wm['max_temperature']) ? $f($wm['min_temperature'], 1).' – '.$f($wm['max_temperature'], 1) : '—' }}</b></div>
                </div>
            @endif
        </div>
        <div class="panel rv" style="--d:1">
            <div class="ph"><h3><i class="hi fe fe-zap"></i> Recommendations</h3></div>
            <ul class="tips">
                @if($hasBad)<li><i class="fe fe-alert-triangle"></i><span><b>High priority:</b> check both sides — well testing calibration and level stability, and the MPFM inputs (PVT, GVF, water cut).</span></li>
                <li><i class="fe fe-check-circle"></i><span>Verify the data entry on both tests for manual input errors.</span></li>
                <li><i class="fe fe-check-circle"></i><span>Repeat the comparison after a new stabilised test.</span></li>@endif
                @if($hasWarn && ! $hasBad)<li><i class="fe fe-eye"></i><span><b>Monitor:</b> document well conditions during both tests and compare again next cycle.</span></li>@endif
                @if(! $sameStr)<li><i class="fe fe-alert-triangle"></i><span><b>Different oil strings:</b> the tests are not on the same well, so the deviation is not meaningful.</span></li>@endif
                @if($days > 7)<li><i class="fe fe-calendar"></i><span><b>Tests are {{ $days }} days apart</b> — re-run both under similar conditions.</span></li>
                @else<li><i class="fe fe-calendar"></i><span><b>Good window:</b> only {{ $days }} day{{ $days === 1 ? '' : 's' }} apart, so the comparison is reliable.</span></li>@endif
                <li><i class="fe fe-settings"></i><span>Keep a regular calibration schedule for the well testing instruments.</span></li>
                <li><i class="fe fe-trending-up"></i><span>Track this well's deviation over several tests to spot drift.</span></li>
            </ul>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="sec">
        <div class="panel rv">
            <div class="ph"><h3><i class="hi fe fe-list"></i> Detailed measurements</h3><span class="sub">Reference = well testing</span></div>
            <div class="scroll"><table class="t">
                <thead><tr><th>Metric</th><th>Well testing</th><th>MPFM</th><th>Absolute diff</th><th>Deviation</th><th>Direction</th></tr></thead>
                <tbody>
                    @foreach($rows as $r)
                        <tr>
                            <td>{{ $r['label'] }} <span style="color:var(--mute);font-weight:500">({{ $r['unit'] }})</span></td>
                            <td>{{ $f($r['tv']) }}</td><td>{{ $f($r['mp']) }}</td><td>{{ $f(abs($r['mp'] - $r['tv'])) }}</td>
                            <td><span class="bd {{ $r['st'] }}">{{ $r['d'] === null ? 'n/a' : (($r['d'] > 0 ? '+' : '').$f($r['d'], 2).($r['type'] === 'pt' ? ' pt' : '%')) }}</span></td>
                            <td>{{ $r['mp'] > $r['tv'] ? 'MPFM higher' : ($r['mp'] < $r['tv'] ? 'MPFM lower' : 'Equal') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table></div>
        </div>
    </div>

    <div class="acts">
        <a href="{{ route('tv_tests.show', $tvTest->id) }}" class="btn2 p"><i class="fe fe-arrow-left"></i> Back to TV test</a>
        <a href="{{ route('tv_tests.compare') }}" class="btn2"><i class="fe fe-shuffle"></i> Compare another</a>
    </div>
</div>
@endsection

@section('js')
<script>
(function () {
    var root = document.getElementById('tvr');
    var io = new IntersectionObserver(function (es) { es.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } }); }, { threshold: .1 });
    root.querySelectorAll('.rv').forEach(function (el) { io.observe(el); });
    if (window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches) root.querySelectorAll('svg').forEach(function (s) { s.pauseAnimations && s.pauseAnimations(); });
})();
</script>
@endsection
