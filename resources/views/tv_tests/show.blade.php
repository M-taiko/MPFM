@extends('layouts.master')

@section('title', 'Test Vessel Test #'.$tvTest->id)

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/oilfield-anim.css') }}">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
<style>
.tvp { direction:ltr; text-align:left; --bg:#050a15; --ink:#e8eefc; --sub:#9fb0cf; --mute:#64748b; --blue:#3b82f6; --cyan:#22d3ee; --amber:#fbbf24; --green:#34d399; --red:#f87171; --water:#38bdf8;
    --line:rgba(96,165,250,.2); --glass:rgba(255,255,255,.05); --grad:linear-gradient(135deg,#3b82f6,#22d3ee); --ease:cubic-bezier(.22,1,.36,1);
    position:relative; overflow:hidden; isolation:isolate; padding:26px; border-radius:26px; background:var(--bg); color:var(--ink); font-family:'Inter',sans-serif; -webkit-font-smoothing:antialiased; }
.tvp *, .tvp *::before, .tvp *::after { box-sizing:border-box; }
.tvp a { text-decoration:none; color:inherit; }
.tvp h1, .tvp h2, .tvp h3, .tvp h4, .tvp p { margin:0; color:inherit; }

/* background */
.tvp .bgl { position:absolute; inset:0; z-index:-1; pointer-events:none; overflow:hidden; }
.tvp .bgl::before { content:""; position:absolute; inset:-50%; background-image:linear-gradient(rgba(59,130,246,.07) 1px,transparent 1px),linear-gradient(90deg,rgba(59,130,246,.07) 1px,transparent 1px); background-size:56px 56px; transform:perspective(700px) rotateX(58deg) translateY(-6%); animation:tvGrid 14s linear infinite; mask-image:radial-gradient(ellipse at 50% 30%,#000 10%,transparent 70%); -webkit-mask-image:radial-gradient(ellipse at 50% 30%,#000 10%,transparent 70%); }
@keyframes tvGrid { to { background-position:0 56px; } }
.tvp .orb { position:absolute; border-radius:50%; filter:blur(90px); opacity:.3; animation:tvFloat 16s ease-in-out infinite; }
.tvp .o1 { width:440px; height:440px; background:#2563eb; top:-140px; left:-120px; }
.tvp .o2 { width:400px; height:400px; background:#0891b2; bottom:-160px; right:-100px; animation-delay:-6s; }
@keyframes tvFloat { 50% { transform:translate(40px,-40px) scale(1.12); } }

/* logo badge */
.tvp .lg { --h:84px; display:inline-block; flex:none; height:var(--h); aspect-ratio:1/1; border-radius:calc(var(--h) * .14); background:#071326 url('/assets/img/brand/logo.svg') center/cover no-repeat; border:2px solid rgba(34,211,238,.5); box-shadow:0 0 0 1px rgba(59,130,246,.5),0 8px 30px rgba(0,0,0,.45),0 0 26px rgba(34,211,238,.35); position:relative; overflow:hidden; animation:tvGlow 3.2s ease-in-out infinite; }
.tvp .lg::after { content:""; position:absolute; top:0; left:-130%; width:60%; height:100%; background:linear-gradient(90deg,transparent,rgba(34,211,238,.45),transparent); transform:skewX(-20deg); animation:tvShine 4.5s infinite; }
@keyframes tvShine { 60%,100% { left:160%; } }
@keyframes tvGlow { 50% { box-shadow:0 0 0 1px rgba(59,130,246,.6),0 10px 40px rgba(0,0,0,.5),0 0 54px rgba(34,211,238,.65); } }

/* reveal */
.tvp .rv { opacity:0; transform:translateY(30px); transition:opacity .9s var(--ease), transform .9s var(--ease); transition-delay:calc(var(--d,0) * 90ms); }
.tvp .rv.in { opacity:1; transform:none; }

/* hero */
.tvp .hero { position:relative; display:grid; grid-template-columns:1fr auto; gap:20px; align-items:center; padding:28px 30px; border-radius:22px; border:1px solid var(--line); background:linear-gradient(100deg,rgba(5,10,21,.93) 35%,rgba(5,10,21,.4)),url('/assets/img/brand/Offshore.jpg') center/cover; overflow:hidden; }
.tvp .who { display:flex; align-items:center; gap:22px; position:relative; z-index:2; }
.tvp .eyebrow { font-size:11px; font-weight:700; letter-spacing:.16em; text-transform:uppercase; color:var(--cyan); display:flex; align-items:center; gap:8px; }
.tvp .eyebrow i { width:8px; height:8px; border-radius:50%; background:var(--green); box-shadow:0 0 0 0 rgba(52,211,153,.7); animation:tvPing 1.8s infinite; }
@keyframes tvPing { to { box-shadow:0 0 0 12px rgba(52,211,153,0); } }
.tvp .hero h1 { font-size:clamp(24px,3vw,38px); font-weight:900; letter-spacing:-.03em; line-height:1.1; margin:6px 0 8px; }
.tvp .hero h1 em { font-style:normal; background:linear-gradient(120deg,#60a5fa,#22d3ee,#fbbf24,#60a5fa); background-size:250% 100%; -webkit-background-clip:text; background-clip:text; color:transparent; animation:tvFlow 6s linear infinite; }
@keyframes tvFlow { to { background-position:250% 0; } }
.tvp .meta { color:var(--sub); font-size:13px; display:flex; gap:12px; flex-wrap:wrap; align-items:center; }
.tvp .chip { display:inline-flex; align-items:center; gap:6px; padding:3px 12px; border-radius:50px; font-size:11px; font-weight:700; letter-spacing:.07em; text-transform:uppercase; border:1px solid var(--line); background:var(--glass); }
.tvp .chip.draft { color:#cbd5e1; } .tvp .chip.submitted { color:#93c5fd; border-color:rgba(59,130,246,.5); background:rgba(59,130,246,.12); }
.tvp .chip.approved { color:var(--green); border-color:rgba(52,211,153,.5); background:rgba(52,211,153,.12); }
.tvp .chip.rejected { color:var(--red); border-color:rgba(248,113,113,.5); background:rgba(248,113,113,.12); }
.tvp .actions { display:flex; gap:10px; flex-wrap:wrap; justify-content:flex-end; position:relative; z-index:2; }
.tvp .btn2 { display:inline-flex; align-items:center; gap:8px; padding:10px 18px; border-radius:50px; font-size:13px; font-weight:700; border:1px solid var(--line); background:var(--glass); color:var(--ink); transition:transform .3s var(--ease), border-color .3s; position:relative; overflow:hidden; }
.tvp .btn2:hover { transform:translateY(-3px); border-color:var(--cyan); color:#fff; }
.tvp .btn2.p { background:var(--grad); color:#04111f; border:0; box-shadow:0 8px 24px rgba(34,211,238,.28); }
.tvp .btn2.p:hover { color:#04111f; box-shadow:0 12px 32px rgba(34,211,238,.45); }
.tvp .btn2.g { background:linear-gradient(135deg,#10b981,#34d399); color:#04130c; border:0; }
.tvp .btn2.g:hover { color:#04130c; }

/* KPI */
.tvp .kpis { display:grid; grid-template-columns:repeat(6,1fr); gap:14px; margin-top:18px; }
.tvp .kpi { position:relative; padding:16px 16px 14px; border-radius:18px; border:1px solid var(--line); background:var(--glass); backdrop-filter:blur(8px); overflow:hidden; transition:transform .4s var(--ease), border-color .3s; }
.tvp .kpi:hover { transform:translateY(-5px); border-color:var(--c,var(--cyan)); }
.tvp .kpi::before { content:""; position:absolute; left:0; top:0; height:3px; width:100%; background:var(--c,var(--cyan)); opacity:.8; }
.tvp .kpi .ic { width:34px; height:34px; border-radius:11px; display:grid; place-items:center; background:rgba(255,255,255,.07); color:var(--c,var(--cyan)); font-size:16px; margin-bottom:10px; }
.tvp .kpi .l { font-size:10.5px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--mute); }
.tvp .kpi .v { font:700 clamp(20px,1.9vw,27px)/1.1 'Space Grotesk',sans-serif; margin-top:4px; color:#fff; }
.tvp .kpi .u { font-size:11px; color:var(--sub); margin-top:2px; }

/* panels */
.tvp .sec { margin-top:22px; }
.tvp .panel { position:relative; border-radius:22px; border:1px solid var(--line); background:linear-gradient(180deg,rgba(255,255,255,.045),rgba(255,255,255,.015)); padding:22px; overflow:hidden; }
.tvp .ph { display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:16px; }
.tvp .ph h3 { font-size:16px; font-weight:800; letter-spacing:-.01em; display:flex; align-items:center; gap:10px; }
.tvp .ph h3 i { width:30px; height:30px; border-radius:10px; display:grid; place-items:center; background:var(--grad); color:#04111f; font-size:14px; }
.tvp .ph .sub { font-size:12px; color:var(--sub); }
.tvp .g2 { display:grid; grid-template-columns:1fr 1fr; gap:18px; }
.tvp .g3 { display:grid; grid-template-columns:1.1fr 1.6fr; gap:18px; }

/* vessel drawing */
.tvp .scroll { overflow-x:auto; }
.tvp .vessel { min-width:760px; }
.tvp .vessel svg { display:block; width:100%; height:auto; }
.tvp .vessel text { font-family:'Inter',sans-serif; }
.tvp .legend { display:flex; gap:16px; flex-wrap:wrap; font-size:11.5px; color:var(--sub); margin-top:12px; }
.tvp .legend span { display:inline-flex; align-items:center; gap:7px; }
.tvp .legend b { width:12px; height:12px; border-radius:4px; display:inline-block; }
.tvx-pp use { fill:none; stroke-linejoin:round; stroke-linecap:butt; }
.tvx-tag { font-size:9.5px; font-weight:700; letter-spacing:.08em; fill:#94a3b8; text-anchor:middle; }
.tvx-vl { font-size:8.5px; font-weight:700; letter-spacing:.14em; fill:#7dd3fc; }
.tvx-vv { font:700 17px 'Space Grotesk',sans-serif; fill:#fff; }
.tvx-vu { font-size:9.5px; fill:#9fb0cf; }
.tvx-ph { font-size:10px; font-weight:800; letter-spacing:.18em; }
.tvx-dv { font-family:'Space Grotesk',sans-serif; font-weight:700; fill:#fff; }
.tvx-du { fill:#7dd3fc; font-weight:600; letter-spacing:.06em; }
.tvx-needle { animation:tvNeedle 2s var(--ease) both; }
.tvx-jit { animation:tvJit 2.6s ease-in-out 2s infinite; }
@keyframes tvNeedle { from { transform:rotate(-120deg); } to { transform:rotate(var(--a)); } }
@keyframes tvJit { 25% { transform:rotate(.9deg); } 50% { transform:rotate(-.7deg); } 75% { transform:rotate(.5deg); } }
.tvx-flow { fill:none; stroke-width:2.2; stroke-linecap:round; stroke-dasharray:3 9; animation:tvDash 1.2s linear infinite; }
@keyframes tvDash { to { stroke-dashoffset:-24; } }
.tvx-wave { animation:tvWave 3.4s linear infinite; }
.tvx-wave.r { animation-duration:4.6s; animation-direction:reverse; }
@keyframes tvWave { to { transform:translateX(-60px); } }
.tvx-bub { fill:rgba(255,236,179,.55); stroke:rgba(255,255,255,.55); stroke-width:.6; animation:tvRise 4s linear infinite; }
@keyframes tvRise { 0% { transform:translateY(0); opacity:0; } 12% { opacity:1; } 85% { opacity:1; } 100% { transform:translateY(var(--rise)); opacity:0; } }
.tvx-mist { fill:rgba(186,230,253,.6); animation:tvFall 2.6s ease-in infinite; }
@keyframes tvFall { 0% { transform:translateY(0); opacity:0; } 20% { opacity:.9; } 100% { transform:translateY(var(--fall,24px)); opacity:0; } }
.tvx-foam { fill:rgba(255,255,255,.7); animation:tvFoam 2.2s ease-in-out infinite; transform-box:fill-box; transform-origin:center; }
@keyframes tvFoam { 50% { transform:scale(1.5); opacity:.45; } }
.tvx-pulse { animation:tvPulse 2.2s ease-in-out infinite; }
@keyframes tvPulse { 50% { opacity:.45; } }
.tvx-spin { transform-box:fill-box; transform-origin:center; animation:tvSpin 6s linear infinite; }
@keyframes tvSpin { to { transform:rotate(360deg); } }
.tvx-blink { animation:tvBlink 1.4s steps(2,end) infinite; }
@keyframes tvBlink { 50% { opacity:.2; } }

/* donut / rings */
.tvp .donutw { display:flex; align-items:center; gap:22px; flex-wrap:wrap; justify-content:center; }
.tvp .donut { width:190px; height:190px; flex:none; }
.tvp .arc { fill:none; stroke-width:13; transform:rotate(-90deg); transform-origin:50% 50%; stroke-linecap:butt; animation:tvArc 1.6s var(--ease) both; }
.tvp .arc.t { stroke:rgba(255,255,255,.08); animation:none; }
.tvp .rg { stroke-width:9; }
@keyframes tvArc { from { stroke-dasharray:0 100; } }
.tvp .lgd { display:grid; gap:12px; }
.tvp .lgd div { display:flex; align-items:center; gap:10px; font-size:13px; color:var(--sub); }
.tvp .lgd b { width:12px; height:12px; border-radius:4px; }
.tvp .lgd strong { color:#fff; font:700 18px 'Space Grotesk',sans-serif; margin-left:auto; padding-left:14px; }
.tvp .rings { display:grid; grid-template-columns:repeat(3,1fr); gap:10px; }
.tvp .ring { text-align:center; }
.tvp .ring svg { width:100%; max-width:140px; }
.tvp .ring .rv2 { font:700 17px 'Space Grotesk',sans-serif; fill:#fff; }
.tvp .ring .ru { font-size:9px; fill:#9fb0cf; }
.tvp .ring .rl { font-size:11px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--sub); margin-top:2px; }
.tvp .ring .rs { font-size:10.5px; color:var(--mute); }

/* trend */
.tvp .trend { position:relative; }
.tvp .trend svg { width:100%; height:auto; display:block; overflow:visible; }
.tvp .trend .tip { position:absolute; pointer-events:none; opacity:0; transition:opacity .15s; background:#0b1426; border:1px solid var(--line); border-radius:10px; padding:7px 10px; font-size:12px; white-space:nowrap; transform:translate(-50%,-120%); z-index:3; }
.tvp .trend .tip b { display:block; font:700 14px 'Space Grotesk',sans-serif; }
.tvp .empty { color:var(--mute); font-size:13px; padding:26px; text-align:center; }
.tvp .ln { fill:none; stroke-width:2.6; stroke-linecap:round; stroke-dasharray:var(--len); stroke-dashoffset:var(--len); }
.tvp .trend.in .ln { animation:tvDraw 1.6s var(--ease) forwards; }
@keyframes tvDraw { to { stroke-dashoffset:0; } }

/* info + flow */
.tvp .infog { display:grid; grid-template-columns:repeat(4,1fr); gap:12px; }
.tvp .info { padding:13px 15px; border-radius:14px; background:var(--glass); border:1px solid var(--line); min-width:0; }
.tvp .info .l { font-size:10px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--mute); display:flex; align-items:center; gap:7px; }
.tvp .info .v { margin-top:5px; font-size:14px; font-weight:600; color:#fff; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.tvp .info .v a { color:var(--cyan); }
.tvp .info .v a:hover { text-decoration:underline; }
.tvp .steps { display:grid; grid-template-columns:repeat(3,1fr); position:relative; margin-top:6px; }
.tvp .step { text-align:center; position:relative; padding:0 8px; }
.tvp .step::before { content:""; position:absolute; top:19px; left:-50%; width:100%; height:3px; background:rgba(255,255,255,.1); }
.tvp .step:first-child::before { display:none; }
.tvp .step.done::before { background:var(--grad); }
.tvp .step .dot { width:40px; height:40px; border-radius:50%; margin:0 auto; display:grid; place-items:center; background:#0b1426; border:2px solid rgba(255,255,255,.14); color:var(--mute); position:relative; z-index:1; font-size:16px; }
.tvp .step.done .dot { border-color:var(--cyan); color:var(--cyan); box-shadow:0 0 18px rgba(34,211,238,.35); }
.tvp .step.cur .dot { animation:tvPing2 1.8s infinite; }
.tvp .step.ok .dot { border-color:var(--green); color:var(--green); box-shadow:0 0 18px rgba(52,211,153,.4); }
.tvp .step.bad .dot { border-color:var(--red); color:var(--red); box-shadow:0 0 18px rgba(248,113,113,.4); }
@keyframes tvPing2 { 50% { box-shadow:0 0 0 10px rgba(34,211,238,0); } }
.tvp .step b { display:block; margin-top:9px; font-size:13px; }
.tvp .step small { display:block; color:var(--mute); font-size:11px; margin-top:2px; }
.tvp .note { margin-top:16px; padding:14px 16px; border-radius:14px; background:rgba(251,191,36,.07); border:1px solid rgba(251,191,36,.25); border-left:4px solid var(--amber); font-size:13.5px; color:#e8eefc; line-height:1.6; white-space:pre-line; }
.tvp .note small { display:block; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--amber); margin-bottom:5px; font-size:10px; }

/* comparison */
.tvp .cmp { display:grid; gap:14px; }
.tvp .crow { display:grid; grid-template-columns:110px 1fr 86px; gap:14px; align-items:center; }
.tvp .crow .n { font-size:12px; font-weight:700; letter-spacing:.06em; text-transform:uppercase; color:var(--sub); }
.tvp .bars { display:grid; gap:6px; }
.tvp .bar { height:20px; border-radius:6px; background:rgba(255,255,255,.05); position:relative; overflow:hidden; }
.tvp .bar i { position:absolute; inset:0 auto 0 0; width:0; border-radius:6px; transition:width 1.3s var(--ease); display:flex; align-items:center; padding-left:8px; font:600 11px 'Inter',sans-serif; font-style:normal; color:#04111f; white-space:nowrap; }
.tvp .bar.tv i { background:linear-gradient(90deg,#f59e0b,#fbbf24); }
.tvp .bar.mp i { background:linear-gradient(90deg,#0891b2,#22d3ee); }
.tvp .in .bar i, .tvp .bar.in i { width:var(--w); }
.tvp .dev { text-align:center; padding:5px 0; border-radius:50px; font:700 12px 'Space Grotesk',sans-serif; }
.tvp .dev.ok { color:var(--green); background:rgba(52,211,153,.12); border:1px solid rgba(52,211,153,.4); }
.tvp .dev.warn { color:var(--amber); background:rgba(251,191,36,.1); border:1px solid rgba(251,191,36,.4); }
.tvp .dev.bad { color:var(--red); background:rgba(248,113,113,.1); border:1px solid rgba(248,113,113,.4); }
.tvp .pills { display:flex; flex-wrap:wrap; gap:10px; margin-top:14px; }
.tvp .pill { display:inline-flex; align-items:center; gap:8px; padding:9px 15px; border-radius:50px; font-size:12.5px; font-weight:600; border:1px solid rgba(34,211,238,.35); background:rgba(34,211,238,.08); color:#cffafe; transition:transform .3s var(--ease), background .3s; }
.tvp .pill:hover { transform:translateY(-3px); background:rgba(34,211,238,.18); color:#fff; }
.tvp .pill small { color:var(--sub); font-weight:500; }
.tvp .msg { padding:14px 16px; border-radius:14px; font-size:13px; line-height:1.6; border:1px solid var(--line); background:var(--glass); color:var(--sub); }
.tvp .msg.ok { border-color:rgba(52,211,153,.4); background:rgba(52,211,153,.08); color:#bbf7d0; }
.tvp .msg.in2 { border-color:rgba(59,130,246,.4); background:rgba(59,130,246,.08); color:#bfdbfe; }
.tvp .none { text-align:center; padding:26px; border:2px dashed var(--line); border-radius:16px; color:var(--mute); }
.tvp .none i { font-size:34px; display:block; margin-bottom:8px; opacity:.6; }

@media (max-width:1200px) { .tvp .kpis { grid-template-columns:repeat(3,1fr); } .tvp .infog { grid-template-columns:repeat(2,1fr); } }
@media (max-width:992px) { .tvp .g2, .tvp .g3 { grid-template-columns:1fr; } .tvp .hero { grid-template-columns:1fr; } .tvp .actions { justify-content:flex-start; } }
@media (max-width:640px) { .tvp { padding:14px; border-radius:18px; } .tvp .kpis { grid-template-columns:repeat(2,1fr); } .tvp .who { flex-direction:column; align-items:flex-start; } .tvp .crow { grid-template-columns:1fr; gap:6px; } .tvp .rings { grid-template-columns:1fr; } .tvp .infog { grid-template-columns:1fr; } }
@media (prefers-reduced-motion:reduce) { .tvp *, .tvp *::before, .tvp *::after { animation:none !important; transition:none !important; } .tvp .rv { opacity:1; transform:none; } .tvp .ln { stroke-dashoffset:0; } .tvp .bar i { width:var(--w); } }
</style>
@endsection

@section('content')
@php
    $f = fn ($v, $d = 2) => number_format((float) $v, $d);
    $oil = (float) $tvTest->oil_rate;
    $gas = (float) $tvTest->gas_rate;
    $wc = min(max((float) $tvTest->water_cut, 0), 100);
    $pr = (float) $tvTest->pressure;
    $tp = (float) $tvTest->temperature;
    $gor = $tvTest->gor !== null ? (float) $tvTest->gor : null;

    // "nice" scale ceiling for dials and rings: 1154 -> 2000, 35 -> 50
    $nice = function ($v) {
        $v = max($v * 1.25, 1);
        $m = 10 ** floor(log10($v));
        foreach ([1, 2, 2.5, 5, 10] as $s) {
            if ($v <= $s * $m) return $s * $m;
        }
        return 10 * $m;
    };
    $prMax = $nice($pr);
    $tpMax = $nice($tp);
    $gorMax = $gor ? $nice($gor) : 100;

    // vessel liquid levels (inner shell spans y=156..324, oil weir top = 220)
    $waterH = min($wc / 100 * 110, 92);
    $yi = 324 - $waterH;
    $oilTop = 220;

    $vesselName = $tvTest->testVessel->name ?? 'Test Vessel';
    $stringName = $tvTest->oilString->name ?? '—';
    $status = $tvTest->status;
    $mpfm = $tvTest->mpfmTest;

    $dev = function ($m, $t) { return (float) $t > 0 ? (($m - $t) / $t) * 100 : null; };
    $devCls = fn ($d) => $d === null ? 'warn' : (abs($d) <= 5 ? 'ok' : (abs($d) <= 10 ? 'warn' : 'bad'));
@endphp

<div class="tvp" id="tvp">
    <div class="bgl"><div class="orb o1"></div><div class="orb o2"></div></div>

    {{-- HERO --}}
    <div class="hero rv">
        <div class="who">
            <span class="lg"></span>
            <div>
                <div class="eyebrow"><i></i> Well Testing · Test Vessel Report</div>
                <h1>Test #{{ $tvTest->id }} <em>· {{ $vesselName }}</em></h1>
                <div class="meta">
                    <span class="chip {{ $status }}">{{ ucfirst($status) }}</span>
                    <span><i class="fe fe-calendar"></i> {{ $tvTest->test_date->format('d M Y') }}</span>
                    <span><i class="fe fe-clock"></i> {{ ucfirst($tvTest->shift) }} shift</span>
                    <span><i class="fe fe-trending-up"></i> {{ $stringName }}</span>
                </div>
            </div>
        </div>
        <div class="actions">
            @if($mpfm)
                <a class="btn2 g" href="{{ route('tv_tests.compare_result', ['tv_test_id' => $tvTest->id, 'mpfm_test_id' => $mpfm->id]) }}"><i class="fe fe-bar-chart-2"></i> Compare vs MPFM</a>
            @endif
            @can('update', $tvTest)
                <a class="btn2 p" href="{{ route('tv_tests.edit', $tvTest->id) }}"><i class="fe fe-edit"></i> Edit</a>
            @endcan
            <a class="btn2" href="{{ route('tv_tests.index') }}"><i class="fe fe-arrow-left"></i> Back</a>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="kpis">
        <div class="kpi rv" style="--d:1;--c:#fbbf24"><div class="ic"><i class="fe fe-droplet"></i></div><div class="l">Oil rate</div><div class="v cu" data-to="{{ $oil }}" data-dec="2">{{ $f($oil) }}</div><div class="u">B/D</div></div>
        <div class="kpi rv" style="--d:2;--c:#22d3ee"><div class="ic"><i class="fe fe-wind"></i></div><div class="l">Gas rate</div><div class="v cu" data-to="{{ $gas }}" data-dec="2">{{ $f($gas) }}</div><div class="u">MMscf/D</div></div>
        <div class="kpi rv" style="--d:3;--c:#38bdf8"><div class="ic"><i class="fe fe-percent"></i></div><div class="l">Water cut</div><div class="v cu" data-to="{{ $wc }}" data-dec="2">{{ $f($wc) }}</div><div class="u">%</div></div>
        <div class="kpi rv" style="--d:4;--c:#a78bfa"><div class="ic"><i class="fe fe-activity"></i></div><div class="l">Pressure</div><div class="v cu" data-to="{{ $pr }}" data-dec="2">{{ $f($pr) }}</div><div class="u">psia</div></div>
        <div class="kpi rv" style="--d:5;--c:#f87171"><div class="ic"><i class="fe fe-thermometer"></i></div><div class="l">Temperature</div><div class="v cu" data-to="{{ $tp }}" data-dec="2">{{ $f($tp) }}</div><div class="u">°F</div></div>
        <div class="kpi rv" style="--d:6;--c:#34d399"><div class="ic"><i class="fe fe-layers"></i></div><div class="l">GOR</div>
            @if($gor !== null)<div class="v cu" data-to="{{ $gor }}" data-dec="2">{{ $f($gor) }}</div>@else<div class="v">N/A</div>@endif
            <div class="u">scf/stb</div></div>
    </div>

    {{-- VESSEL DRAWING --}}
    <div class="sec">
        <div class="panel rv">
            <div class="ph">
                <h3><i class="fe fe-anchor"></i> Test Vessel V-101 · 3-phase horizontal separator</h3>
                <span class="sub">{{ $vesselName }}@if($tvTest->testVessel?->code) · {{ $tvTest->testVessel->code }}@endif — levels drawn from this test's water cut</span>
            </div>
            <div class="scroll"><div class="vessel">
<svg viewBox="0 0 1000 470" role="img" aria-label="Test vessel process drawing">
<defs>
    <linearGradient id="tvSteel" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#9aa8bd"/><stop offset=".18" stop-color="#e2e8f0"/><stop offset=".5" stop-color="#64748b"/><stop offset="1" stop-color="#1e293b"/></linearGradient>
    <linearGradient id="tvShell" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#a8b6cb"/><stop offset=".12" stop-color="#f1f5f9"/><stop offset=".45" stop-color="#7c8aa1"/><stop offset="1" stop-color="#1b2433"/></linearGradient>
    <linearGradient id="tvGas" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#67e8f9" stop-opacity=".22"/><stop offset="1" stop-color="#0e7490" stop-opacity=".1"/></linearGradient>
    <linearGradient id="tvOil" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#f59e0b"/><stop offset="1" stop-color="#78350f"/></linearGradient>
    <linearGradient id="tvWater" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#0ea5e9"/><stop offset="1" stop-color="#0c4a6e"/></linearGradient>
    <linearGradient id="tvCyl" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#fff" stop-opacity=".22"/><stop offset=".3" stop-color="#fff" stop-opacity=".02"/><stop offset=".7" stop-color="#000" stop-opacity=".1"/><stop offset="1" stop-color="#000" stop-opacity=".45"/></linearGradient>
    <linearGradient id="tvRim" x1="0" y1="0" x2="1" y2="0"><stop offset="0" stop-color="#fff" stop-opacity=".1"/><stop offset=".5" stop-color="#fff" stop-opacity=".55"/><stop offset="1" stop-color="#fff" stop-opacity=".1"/></linearGradient>
    <radialGradient id="tvGlowG" cx=".5" cy=".5" r=".5"><stop offset="0" stop-color="#22d3ee" stop-opacity=".35"/><stop offset="1" stop-color="#22d3ee" stop-opacity="0"/></radialGradient>
    <pattern id="tvVane" width="6" height="8" patternUnits="userSpaceOnUse"><path d="M0,0 L3,4 L0,8 M6,0 L3,4 L6,8" stroke="#cbd5e1" stroke-width="1" fill="none"/></pattern>
    <pattern id="tvGrid" width="30" height="30" patternUnits="userSpaceOnUse"><path d="M30 0H0V30" fill="none" stroke="#3b82f6" stroke-opacity=".08"/></pattern>
    <clipPath id="tvVin"><path d="M252,156 H708 A46,84 0 0 1 708,324 H252 A46,84 0 0 1 252,156 Z"/></clipPath>
    <clipPath id="tvGlass"><rect x="153" y="178" width="14" height="137" rx="6"/></clipPath>
    <path id="pIn" d="M30,100 H250 V156"/>
    <path id="pGas" d="M690,156 V70 H955"/>
    <path id="pOil" d="M700,320 V390 H955"/>
    <path id="pWat" d="M480,320 V430 H30"/>
    <g id="tvVlv"><path d="M-13,-10 L13,10 V-10 L-13,10 Z" fill="#1e293b" stroke="#94a3b8" stroke-width="1.6"/><path d="M0,0 V-17" stroke="#94a3b8" stroke-width="2"/><rect x="-8" y="-21" width="16" height="4" rx="2" fill="#94a3b8"/></g>
    <g id="tvCv"><path d="M-13,-10 L13,10 V-10 L-13,10 Z" fill="#1e293b" stroke="#94a3b8" stroke-width="1.6"/><path d="M0,0 V-14" stroke="#94a3b8" stroke-width="2"/><path d="M-10,-14 a10,10 0 0 1 20,0 Z" fill="#1d4ed8" stroke="#93c5fd" stroke-width="1.2"/></g>
</defs>

<rect width="1000" height="470" fill="url(#tvGrid)"/>
<ellipse cx="480" cy="240" rx="330" ry="150" fill="url(#tvGlowG)"/>

{{-- skid beam + saddles --}}
<rect x="230" y="372" width="430" height="10" rx="2" fill="#334155" stroke="#0b1220"/>
<path d="M258,334 L322,334 L332,372 H248 Z" fill="url(#tvSteel)" stroke="#0b1220"/>
<path d="M568,334 L632,334 L642,372 H558 Z" fill="url(#tvSteel)" stroke="#0b1220"/>
<rect x="240" y="366" width="102" height="8" fill="#1e293b" stroke="#0b1220"/>
<rect x="550" y="366" width="102" height="8" fill="#1e293b" stroke="#0b1220"/>

{{-- level gauge stubs --}}
<path d="M225,178 H160 M225,315 H160" stroke="#0b1220" stroke-width="9" fill="none"/>
<path d="M225,178 H160 M225,315 H160" stroke="#64748b" stroke-width="5" fill="none"/>

{{-- instrument stems --}}
<rect x="466" y="120" width="8" height="38" fill="#64748b" stroke="#0b1220"/>
<rect x="561" y="120" width="8" height="38" fill="#64748b" stroke="#0b1220"/>
<rect x="332" y="124" width="16" height="34" fill="url(#tvSteel)" stroke="#0b1220"/>

{{-- pipes --}}
<g class="tvx-pp">
    <use href="#pIn" stroke="#0b1220" stroke-width="16"/><use href="#pIn" stroke="#64748b" stroke-width="12"/><use href="#pIn" stroke="#e2e8f0" stroke-opacity=".35" stroke-width="3" transform="translate(0,-2.5)"/>
    <use href="#pGas" stroke="#0b1220" stroke-width="16"/><use href="#pGas" stroke="#64748b" stroke-width="12"/><use href="#pGas" stroke="#e2e8f0" stroke-opacity=".35" stroke-width="3" transform="translate(0,-2.5)"/>
    <use href="#pOil" stroke="#0b1220" stroke-width="16"/><use href="#pOil" stroke="#64748b" stroke-width="12"/><use href="#pOil" stroke="#e2e8f0" stroke-opacity=".35" stroke-width="3" transform="translate(0,-2.5)"/>
    <use href="#pWat" stroke="#0b1220" stroke-width="16"/><use href="#pWat" stroke="#64748b" stroke-width="12"/><use href="#pWat" stroke="#e2e8f0" stroke-opacity=".35" stroke-width="3" transform="translate(0,-2.5)"/>
</g>

{{-- vessel shell --}}
<path d="M250,150 H710 A50,90 0 0 1 710,330 H250 A50,90 0 0 1 250,150 Z" fill="url(#tvShell)" stroke="#0b1220" stroke-width="2"/>
<g clip-path="url(#tvVin)">
    <rect x="200" y="150" width="570" height="180" fill="#04101f"/>
    <rect x="200" y="150" width="570" height="180" fill="url(#tvGas)"/>
    {{-- oil, water (before weir) --}}
    <rect x="200" y="{{ $oilTop }}" width="445" height="{{ round($yi - $oilTop, 1) }}" fill="url(#tvOil)"/>
    @if($waterH > 0.5)
    <rect x="200" y="{{ round($yi, 1) }}" width="445" height="{{ round(324 - $yi + 6, 1) }}" fill="url(#tvWater)"/>
    @endif
    {{-- oil spill compartment --}}
    <rect x="644" y="262" width="130" height="70" fill="url(#tvOil)"/>
    {{-- surface waves --}}
    <g clip-path="url(#tvVin)">
        <g class="tvx-wave"><path d="M-10,{{ $oilTop }} q15,-5 30,0 t30,0 t30,0 t30,0 t30,0 t30,0 t30,0 t30,0 t30,0 t30,0 t30,0 t30,0 t30,0 t30,0 t30,0 t30,0 t30,0 t30,0 t30,0 t30,0" fill="none" stroke="#fde68a" stroke-opacity=".7" stroke-width="1.6"/></g>
        <g class="tvx-wave r"><path d="M640,262 q15,-4 30,0 t30,0 t30,0 t30,0 t30,0 t30,0 t30,0" fill="none" stroke="#fde68a" stroke-opacity=".7" stroke-width="1.6"/></g>
        @if($waterH > 6)
        <g class="tvx-wave r"><path d="M-10,{{ round($yi, 1) }} q15,-4 30,0 t30,0 t30,0 t30,0 t30,0 t30,0 t30,0 t30,0 t30,0 t30,0 t30,0 t30,0 t30,0 t30,0 t30,0 t30,0 t30,0 t30,0 t30,0 t30,0" fill="none" stroke="#bae6fd" stroke-opacity=".6" stroke-width="1.4"/></g>
        @endif
    </g>
    {{-- foam at inlet --}}
    @for($i = 0; $i < 9; $i++)
        <ellipse class="tvx-foam" cx="{{ 262 + $i * 17 }}" cy="{{ $oilTop - 1 + ($i % 2) * 2 }}" rx="{{ 4 + ($i % 3) }}" ry="2.4" style="animation-delay:-{{ $i * .27 }}s"/>
    @endfor
    {{-- gas breakout bubbles inside the oil layer --}}
    @php $span = max(8, $yi - $oilTop - 12); @endphp
    @for($i = 0; $i < 16; $i++)
        @php $bx = 230 + (($i * 53) % 390); $by = $yi - 8 - (($i * 17) % $span); $rise = $by - $oilTop - 2; @endphp
        <circle class="tvx-bub" cx="{{ $bx }}" cy="{{ round($by, 1) }}" r="{{ 2 + ($i % 3) * .8 }}" style="--rise:-{{ round($rise, 1) }}px;animation-duration:{{ 3 + ($i % 4) * .7 }}s;animation-delay:-{{ round($i * .41, 2) }}s"/>
    @endfor
    {{-- water droplets settling through the oil --}}
    @if($waterH > 3)
        @for($i = 0; $i < 7; $i++)
            <circle class="tvx-mist" cx="{{ 260 + $i * 55 }}" cy="{{ $oilTop + 8 + ($i % 3) * 6 }}" r="2" style="--fall:{{ max(8, round($yi - $oilTop - 16)) }}px;fill:rgba(125,211,252,.85);animation-duration:{{ 3.2 + ($i % 3) * .5 }}s;animation-delay:-{{ $i * .5 }}s"/>
        @endfor
    @endif
    {{-- mist carry-over in the gas space --}}
    @for($i = 0; $i < 9; $i++)
        <circle class="tvx-mist" cx="{{ 290 + $i * 42 }}" cy="{{ 166 + ($i % 3) * 8 }}" r="1.6" style="--fall:{{ 26 + ($i % 3) * 6 }}px;animation-delay:-{{ $i * .33 }}s"/>
    @endfor
    {{-- weir + mist extractor + inlet diverter --}}
    <rect x="640" y="{{ $oilTop }}" width="6" height="106" fill="url(#tvSteel)" stroke="#0b1220" stroke-width=".8"/>
    <rect x="655" y="158" width="32" height="50" rx="3" fill="url(#tvVane)" stroke="#cbd5e1" stroke-opacity=".7"/>
    <path d="M236,160 H264 V184 L250,200 L236,184 Z" fill="url(#tvSteel)" stroke="#0b1220"/>
    {{-- phase flow arrows --}}
    <path class="tvx-flow" d="M268,190 H664 Q688,190 690,168" stroke="#67e8f9"/>
    <path class="tvx-flow" d="M300,{{ $oilTop + 12 }} H628 Q650,{{ $oilTop + 8 }} 658,238 V256" stroke="#fde68a"/>
    @if($waterH > 6)
    <path class="tvx-flow" d="M300,{{ round($yi + $waterH / 2, 1) }} H480 V322" stroke="#bae6fd"/>
    @endif
    <path class="tvx-flow" d="M700,284 V324" stroke="#fde68a"/>
    {{-- phase labels --}}
    <text x="420" y="178" class="tvx-ph" fill="#67e8f9" opacity=".85">GAS</text>
    <text x="420" y="{{ round(($oilTop + $yi) / 2 + 4) }}" class="tvx-ph" fill="#fff" opacity=".75">OIL · {{ $f(100 - $wc, 1) }}%</text>
    @if($waterH > 22)<text x="420" y="{{ round(($yi + 324) / 2 + 4) }}" class="tvx-ph" fill="#fff" opacity=".8">WATER · {{ $f($wc, 1) }}%</text>@endif
    <text x="672" y="294" class="tvx-ph" fill="#fff" opacity=".6" text-anchor="middle" style="font-size:8px">OIL</text>
    {{-- cylinder shading --}}
    <rect x="200" y="150" width="570" height="180" fill="url(#tvCyl)"/>
</g>
<path d="M250,150 H710 A50,90 0 0 1 710,330 H250 A50,90 0 0 1 250,150 Z" fill="none" stroke="url(#tvRim)" stroke-width="1.4"/>
<path d="M252,156 H708 A46,84 0 0 1 708,324 H252 A46,84 0 0 1 252,156 Z" fill="none" stroke="#0b1220" stroke-opacity=".6" stroke-width="1"/>
{{-- shell welds --}}
<path d="M330,150 V156 M330,324 V330 M570,150 V156 M570,324 V330" stroke="#0b1220" stroke-width="2" opacity=".5"/>
{{-- manway --}}
<rect x="192" y="224" width="12" height="32" rx="2" fill="url(#tvSteel)" stroke="#0b1220"/>
<circle cx="198" cy="230" r="1.6" fill="#0b1220"/><circle cx="198" cy="250" r="1.6" fill="#0b1220"/>
<text x="186" y="244" class="tvx-tag" text-anchor="end" style="text-anchor:end">MW</text>

{{-- nozzle flanges --}}
<g fill="url(#tvSteel)" stroke="#0b1220">
    <rect x="240" y="146" width="20" height="7" rx="1"/><rect x="680" y="146" width="20" height="7" rx="1"/>
    <rect x="690" y="327" width="20" height="7" rx="1"/><rect x="470" y="327" width="20" height="7" rx="1"/>
</g>

{{-- level gauge glass --}}
<g>
    <rect x="150" y="176" width="20" height="141" rx="8" fill="#050a15" stroke="#94a3b8" stroke-width="1.6"/>
    <g clip-path="url(#tvGlass)">
        <rect x="153" y="{{ $oilTop }}" width="14" height="{{ round($yi - $oilTop, 1) }}" fill="url(#tvOil)"/>
        @if($waterH > .5)<rect x="153" y="{{ round($yi, 1) }}" width="14" height="{{ round(324 - $yi, 1) }}" fill="url(#tvWater)"/>@endif
        <rect x="155" y="176" width="3" height="141" fill="#fff" opacity=".25"/>
    </g>
    @for($i = 0; $i < 6; $i++)<line x1="172" y1="{{ 185 + $i * 26 }}" x2="177" y2="{{ 185 + $i * 26 }}" stroke="#64748b"/>@endfor
    <text x="160" y="341" class="tvx-tag">LG-101</text>
</g>

{{-- PSV --}}
<path d="M340,124 l-7,-5 l14,-5 l-14,-5 l14,-5 l-7,-5" fill="none" stroke="#fbbf24" stroke-width="1.8"/>
<rect x="328" y="84" width="24" height="6" rx="2" fill="url(#tvSteel)" stroke="#0b1220"/>
<path d="M352,87 h22" stroke="#94a3b8" stroke-width="3"/><path d="M374,81 l8,6 l-8,6 Z" fill="#ef4444"/>
<text x="340" y="72" class="tvx-tag">PSV-101</text>

{{-- dials --}}
@include('partials.tv_dial', ['cx' => 470, 'cy' => 94, 'r' => 26, 'val' => $pr, 'max' => $prMax, 'text' => $f($pr, 0), 'unit' => 'PSIA'])
@include('partials.tv_dial', ['cx' => 565, 'cy' => 94, 'r' => 26, 'val' => $tp, 'max' => $tpMax, 'text' => $f($tp, 0), 'unit' => '°F'])
<text x="470" y="55" class="tvx-tag">PI-101</text>
<text x="565" y="55" class="tvx-tag">TI-101</text>

{{-- valves --}}
<use href="#tvVlv" x="120" y="100"/><text x="120" y="126" class="tvx-tag">XV-101</text>
<use href="#tvCv" x="790" y="70"/><text x="790" y="100" class="tvx-tag">PCV-101</text>
<use href="#tvCv" x="790" y="390"/><text x="790" y="416" class="tvx-tag">LCV-101</text>
<use href="#tvCv" x="330" y="430"/><text x="330" y="456" class="tvx-tag">LCV-102</text>

{{-- meters --}}
<g>
    <circle cx="880" cy="70" r="15" fill="#0a1222" stroke="#22d3ee" stroke-width="1.6"/><text x="880" y="74" class="tvx-tag" style="fill:#22d3ee">FT</text><text x="880" y="100" class="tvx-tag">FE-101</text>
    <circle cx="880" cy="390" r="15" fill="#0a1222" stroke="#fbbf24" stroke-width="1.6"/><text x="880" y="394" class="tvx-tag" style="fill:#fbbf24">FT</text><text x="880" y="366" class="tvx-tag">FE-102</text>
    <circle class="tvx-spin" cx="190" cy="430" r="15" fill="#0a1222" stroke="#38bdf8" stroke-width="1.6" stroke-dasharray="5 4"/><text x="190" y="434" class="tvx-tag" style="fill:#38bdf8">FT</text><text x="190" y="408" class="tvx-tag">FE-103</text>
</g>

{{-- moving fluid dots --}}
@foreach([0, .6, 1.2, 1.8, 2.4] as $b)
    <circle r="3.4" fill="#fde68a"><animateMotion dur="3s" begin="{{ $b }}s" repeatCount="indefinite"><mpath href="#pIn"/></animateMotion></circle>
    <circle r="3" fill="#a5f3fc"><animateMotion dur="3s" begin="{{ $b }}s" repeatCount="indefinite"><mpath href="#pGas"/></animateMotion></circle>
    <circle r="3.4" fill="#f59e0b"><animateMotion dur="3.4s" begin="{{ $b }}s" repeatCount="indefinite"><mpath href="#pOil"/></animateMotion></circle>
    @if($waterH > .5)<circle r="3.2" fill="#7dd3fc"><animateMotion dur="3.6s" begin="{{ $b }}s" repeatCount="indefinite"><mpath href="#pWat"/></animateMotion></circle>@endif
@endforeach

{{-- data boxes --}}
<g>
    <rect x="30" y="20" width="176" height="52" rx="10" fill="#0a1222" stroke="#fbbf24" stroke-opacity=".55"/>
    <text x="42" y="38" class="tvx-vl" style="fill:#fbbf24">INLET · WELL STREAM</text>
    <text x="42" y="58" class="tvx-vv" style="font-size:15px">{{ $stringName }}</text>
    <circle cx="192" cy="31" r="3.5" fill="#34d399" class="tvx-pulse"/>

    <rect x="815" y="8" width="168" height="42" rx="10" fill="#0a1222" stroke="#22d3ee" stroke-opacity=".6"/>
    <text x="826" y="24" class="tvx-vl">GAS OUT · GAS RATE</text>
    <text x="826" y="43" class="tvx-vv cu" data-to="{{ $gas }}" data-dec="2">{{ $f($gas) }}</text><text x="928" y="43" class="tvx-vu">MMscf/D</text>

    <rect x="815" y="412" width="168" height="44" rx="10" fill="#0a1222" stroke="#fbbf24" stroke-opacity=".6"/>
    <text x="826" y="428" class="tvx-vl" style="fill:#fcd34d">OIL OUT · OIL RATE</text>
    <text x="826" y="447" class="tvx-vv cu" data-to="{{ $oil }}" data-dec="2">{{ $f($oil) }}</text><text x="938" y="447" class="tvx-vu">B/D</text>

    <rect x="30" y="372" width="170" height="38" rx="10" fill="#0a1222" stroke="#38bdf8" stroke-opacity=".6"/>
    <text x="41" y="387" class="tvx-vl" style="fill:#7dd3fc">WATER OUT · WC</text>
    <text x="41" y="404" class="tvx-vv cu" data-to="{{ $wc }}" data-dec="2" style="font-size:15px">{{ $f($wc) }}</text><text x="100" y="404" class="tvx-vu">%</text>

    <rect x="520" y="392" width="160" height="38" rx="8" fill="#0a1222" stroke="#475569"/>
    <text x="600" y="408" class="tvx-tag" style="fill:#e2e8f0;font-size:11px">V-101 · {{ \Illuminate\Support\Str::limit($vesselName, 16) }}</text>
    <text x="600" y="422" class="tvx-tag" style="font-size:8.5px">{{ $pr > 0 ? $f($pr, 0).' psia' : '' }} · {{ $f($tp, 0) }} °F{{ $gor ? ' · GOR '.$f($gor, 0) : '' }}</text>
</g>
<text x="105" y="88" class="tvx-tag" style="fill:#fcd34d;font-size:8px" text-anchor="middle">▶ FEED</text>
<text x="380" y="62" class="tvx-tag" style="fill:#fecaca;font-size:8px;text-anchor:start">RELIEF</text>
<text x="640" y="212" class="tvx-tag" style="font-size:8px">WEIR</text>
<text x="671" y="152" class="tvx-tag" style="font-size:8px" transform="translate(0,-18)">DEMISTER</text>
<text x="250" y="218" class="tvx-tag" style="font-size:8px;fill:#e2e8f0" transform="translate(0,0)">DIVERTER</text>
</svg>
            </div></div>
            <div class="legend">
                <span><b style="background:#fbbf24"></b> Oil</span>
                <span><b style="background:#38bdf8"></b> Water</span>
                <span><b style="background:#22d3ee"></b> Gas</span>
                <span><b style="background:#94a3b8"></b> Vessel internals: inlet diverter · weir · demister</span>
                <span><b style="background:#34d399"></b> Gauges read pressure &amp; temperature of this test</span>
            </div>
        </div>
    </div>

    {{-- COMPOSITION + CONDITIONS --}}
    <div class="sec g3">
        <div class="panel rv">
            <div class="ph"><h3><i class="fe fe-pie-chart"></i> Liquid composition</h3><span class="sub">Oil vs water cut</span></div>
            <div class="donutw">
                <svg class="donut" viewBox="0 0 200 200">
                    <circle class="arc t" cx="100" cy="100" r="76" pathLength="100"/>
                    <circle class="arc" cx="100" cy="100" r="76" pathLength="100" stroke="#fbbf24" style="stroke-dasharray:{{ round(100 - $wc, 2) }} 100"/>
                    @if($wc > 0)<circle class="arc" cx="100" cy="100" r="76" pathLength="100" stroke="#38bdf8" style="stroke-dasharray:{{ round($wc, 2) }} 100;stroke-dashoffset:-{{ round(100 - $wc, 2) }}"/>@endif
                    <text x="100" y="98" text-anchor="middle" font-family="Space Grotesk" font-weight="700" font-size="30" fill="#fff">{{ $f($wc, 1) }}%</text>
                    <text x="100" y="118" text-anchor="middle" font-size="10" font-weight="700" letter-spacing="2" fill="#9fb0cf">WATER CUT</text>
                </svg>
                <div class="lgd">
                    <div><b style="background:#fbbf24"></b> Oil <strong>{{ $f(100 - $wc, 1) }}%</strong></div>
                    <div><b style="background:#38bdf8"></b> Water <strong>{{ $f($wc, 1) }}%</strong></div>
                    <div><b style="background:#22d3ee"></b> Gas rate <strong>{{ $f($gas) }}</strong></div>
                </div>
            </div>
        </div>
        <div class="panel rv" style="--d:1">
            <div class="ph"><h3><i class="fe fe-activity"></i> Operating conditions</h3><span class="sub">Ring = value ÷ scale max</span></div>
            <div class="rings">
                @foreach([
                    ['Pressure', $pr, $prMax, 'psia', '#a78bfa', 0],
                    ['Temperature', $tp, $tpMax, '°F', '#f87171', 0],
                    ['GOR', $gor ?? 0, $gorMax, 'scf/stb', '#34d399', 0],
                ] as [$lbl, $val, $mx, $un, $col, $dec])
                    <div class="ring">
                        <svg viewBox="0 0 120 120">
                            <circle class="arc rg t" cx="60" cy="60" r="46" pathLength="100"/>
                            <circle class="arc rg" cx="60" cy="60" r="46" pathLength="100" stroke="{{ $col }}" style="stroke-dasharray:{{ round(min($val / $mx, 1) * 100, 2) }} 100"/>
                            <text x="60" y="64" text-anchor="middle" class="rv2">{{ ($lbl === 'GOR' && $gor === null) ? 'N/A' : $f($val, $dec) }}</text>
                            <text x="60" y="78" text-anchor="middle" class="ru">{{ $un }}</text>
                        </svg>
                        <div class="rl">{{ $lbl }}</div>
                        <div class="rs">scale 0 – {{ number_format($mx) }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- TRENDS --}}
    <div class="sec g2">
        <div class="panel rv">
            <div class="ph"><h3><i class="fe fe-trending-up"></i> Oil rate trend</h3><span class="sub">Last tests on {{ $stringName }}</span></div>
            <div class="trend" data-key="oil" data-color="#fbbf24" data-unit="B/D" data-dec="0"></div>
        </div>
        <div class="panel rv" style="--d:1">
            <div class="ph"><h3><i class="fe fe-droplet"></i> Water cut trend</h3><span class="sub">Last tests on {{ $stringName }}</span></div>
            <div class="trend" data-key="wc" data-color="#38bdf8" data-unit="%" data-dec="1"></div>
        </div>
    </div>

    {{-- DETAILS + REVIEW --}}
    <div class="sec g3" style="grid-template-columns:1.6fr 1fr">
        <div class="panel rv">
            <div class="ph"><h3><i class="fe fe-info"></i> Test information</h3></div>
            <div class="infog">
                <div class="info"><div class="l"><i class="fe fe-anchor"></i> Test vessel</div><div class="v">@if($tvTest->testVessel)<a href="{{ route('test_vessels.show', $tvTest->testVessel->id) }}">{{ $tvTest->testVessel->name }}</a>@else — @endif</div></div>
                <div class="info"><div class="l"><i class="fe fe-layers"></i> Oil field</div><div class="v">@if($tvTest->oilField)<a href="{{ route('oil_fields.show', $tvTest->oilField->id) }}">{{ $tvTest->oilField->name }}</a>@else — @endif</div></div>
                <div class="info"><div class="l"><i class="fe fe-corner-down-right"></i> Tower</div><div class="v">@if($tvTest->tower)<a href="{{ route('towers.show', $tvTest->tower->id) }}">{{ $tvTest->tower->name }}</a>@else — @endif</div></div>
                <div class="info"><div class="l"><i class="fe fe-trending-up"></i> Oil string</div><div class="v">{{ $stringName }}</div></div>
                <div class="info"><div class="l"><i class="fe fe-user"></i> Operator</div><div class="v">{{ $tvTest->user->name ?? '—' }}</div></div>
                <div class="info"><div class="l"><i class="fe fe-calendar"></i> Test date</div><div class="v">{{ $tvTest->test_date->format('Y-m-d') }}</div></div>
                <div class="info"><div class="l"><i class="fe fe-clock"></i> Shift</div><div class="v">{{ ucfirst($tvTest->shift) }}</div></div>
                <div class="info"><div class="l"><i class="fe fe-plus-circle"></i> Created</div><div class="v">{{ $tvTest->created_at->format('Y-m-d H:i') }}</div></div>
            </div>
            @if($tvTest->notes)
                <div class="note"><small>Notes &amp; observations</small>{{ $tvTest->notes }}</div>
            @endif
        </div>
        <div class="panel rv" style="--d:1">
            <div class="ph"><h3><i class="fe fe-check-circle"></i> Review status</h3></div>
            @php
                $submitted = in_array($status, ['submitted', 'approved', 'rejected']);
                $reviewed = in_array($status, ['approved', 'rejected']);
            @endphp
            <div class="steps">
                <div class="step done {{ $status === 'draft' ? 'cur' : '' }}"><div class="dot"><i class="fe fe-edit-3"></i></div><b>Created</b><small>{{ $tvTest->created_at->format('d M') }}</small></div>
                <div class="step {{ $submitted ? 'done' : '' }} {{ $status === 'submitted' ? 'cur' : '' }}"><div class="dot"><i class="fe fe-send"></i></div><b>Submitted</b><small>{{ $submitted ? 'Sent for review' : 'Pending' }}</small></div>
                <div class="step {{ $reviewed ? 'done' : '' }} {{ $status === 'approved' ? 'ok' : '' }} {{ $status === 'rejected' ? 'bad' : '' }}"><div class="dot"><i class="fe {{ $status === 'rejected' ? 'fe-x' : 'fe-check' }}"></i></div><b>{{ $status === 'rejected' ? 'Rejected' : 'Approved' }}</b><small>{{ $reviewed && $tvTest->reviewed_at ? \Illuminate\Support\Carbon::parse($tvTest->reviewed_at)->format('d M') : 'Pending' }}</small></div>
            </div>
            @if($reviewed)
                <div class="note" style="margin-top:20px;{{ $status === 'rejected' ? 'border-color:rgba(248,113,113,.3);border-left-color:#f87171;background:rgba(248,113,113,.07)' : 'border-color:rgba(52,211,153,.3);border-left-color:#34d399;background:rgba(52,211,153,.07)' }}">
                    <small style="color:{{ $status === 'rejected' ? '#f87171' : '#34d399' }}">Reviewed by {{ $tvTest->reviewedBy->name ?? '—' }}</small>
                    {{ $tvTest->review_notes ?: 'No review notes.' }}
                </div>
            @endif
        </div>
    </div>

    {{-- MPFM COMPARISON --}}
    <div class="sec">
        <div class="panel rv">
            <div class="ph"><h3><i class="fe fe-shuffle"></i> MPFM comparison</h3><span class="sub">Tolerance ±5 % (green) · ±10 % (amber)</span></div>
            @if($mpfm)
                @php
                    $rows = [
                        ['Oil rate', $oil, (float) $mpfm->oil_rate, 'B/D'],
                        ['Gas rate', $gas, (float) $mpfm->gas_rate, 'MMscf/D'],
                        ['Water cut', $wc, (float) $mpfm->water_cut, '%'],
                    ];
                @endphp
                <div class="msg ok" style="margin-bottom:18px"><i class="fe fe-check-circle"></i> Linked with MPFM test #{{ $mpfm->id }} · {{ $mpfm->test_date->format('Y-m-d') }} · <b>{{ ucfirst($mpfm->status) }}</b></div>
                <div class="cmp">
                    @foreach($rows as [$n, $tvv, $mpv, $un])
                        @php $mx = max($tvv, $mpv, 0.0001); $d = $n === 'Water cut' ? ($mpv - $tvv) : $dev($mpv, $tvv); @endphp
                        <div class="crow">
                            <div class="n">{{ $n }}<br><span style="color:var(--mute);font-weight:500;text-transform:none;letter-spacing:0">{{ $un }}</span></div>
                            <div class="bars">
                                <div class="bar tv"><i style="--w:{{ round($tvv / $mx * 100, 1) }}%">Test vessel · {{ $f($tvv) }}</i></div>
                                <div class="bar mp"><i style="--w:{{ round($mpv / $mx * 100, 1) }}%">MPFM · {{ $f($mpv) }}</i></div>
                            </div>
                            <div class="dev {{ $n === 'Water cut' ? ($d === null || abs($d) <= 2 ? 'ok' : (abs($d) <= 5 ? 'warn' : 'bad')) : $devCls($d) }}">{{ $d === null ? '—' : (($d > 0 ? '+' : '').number_format($d, 1).($n === 'Water cut' ? ' pt' : '%')) }}</div>
                        </div>
                    @endforeach
                </div>
                <div class="pills"><a class="pill" href="{{ route('tv_tests.compare_result', ['tv_test_id' => $tvTest->id, 'mpfm_test_id' => $mpfm->id]) }}"><i class="fe fe-bar-chart-2"></i> Full comparison report</a></div>
            @elseif($mpfmNearby->count() > 0)
                <div class="msg ok"><i class="fe fe-check-circle"></i> Found <b>{{ $mpfmNearby->count() }}</b> MPFM test(s) on the same oil string within ±7 days.</div>
                <div class="pills">
                    @foreach($mpfmNearby as $m)
                        <a class="pill" href="{{ route('tv_tests.compare_result', ['tv_test_id' => $tvTest->id, 'mpfm_test_id' => $m->id]) }}"><i class="fe fe-shuffle"></i> MPFM #{{ $m->id }} <small>{{ $m->test_date->format('Y-m-d') }} · {{ ucfirst($m->status) }}</small></a>
                    @endforeach
                </div>
            @elseif($mpfmForWell->count() > 0)
                <div class="msg in2"><i class="fe fe-shuffle"></i> {{ $mpfmForWell->count() }} MPFM test(s) exist on this oil string, but outside the ±7 day window. You can still compare with them.</div>
                <div class="pills">
                    @foreach($mpfmForWell->take(10) as $m)
                        <a class="pill" href="{{ route('tv_tests.compare_result', ['tv_test_id' => $tvTest->id, 'mpfm_test_id' => $m->id]) }}"><i class="fe fe-shuffle"></i> MPFM #{{ $m->id }} <small>{{ $m->test_date->format('Y-m-d') }} · {{ ucfirst($m->status) }}</small></a>
                    @endforeach
                </div>
                @if($mpfmForWell->count() > 10)<p style="margin-top:12px;font-size:12px;color:var(--mute)">… and {{ $mpfmForWell->count() - 10 }} more</p>@endif
            @else
                <div class="none"><i class="fe fe-search"></i><b style="color:var(--sub)">No MPFM tests for this oil string yet</b><p style="margin-top:6px;font-size:12.5px">Once an MPFM test is recorded for {{ $stringName }} you can compare it here.</p></div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
window.TV_HIST = @json($history);
window.TV_ID = {{ $tvTest->id }};
(function () {
    var root = document.getElementById('tvp');
    var reduce = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;

    // count-up (HTML + SVG text)
    function fmt(v, d) { return v.toLocaleString('en-US', { minimumFractionDigits: d, maximumFractionDigits: d }); }
    function countUp(el) {
        var to = parseFloat(el.dataset.to) || 0, d = parseInt(el.dataset.dec || 0, 10), t0 = null, dur = 1400;
        if (reduce) return;
        function step(t) {
            if (t0 === null) t0 = t;
            var p = Math.min((t - t0) / dur, 1), e = 1 - Math.pow(1 - p, 4);
            el.textContent = fmt(to * e, d);
            if (p < 1) requestAnimationFrame(step); else el.textContent = fmt(to, d);
        }
        requestAnimationFrame(step);
    }

    // trend charts
    function trend(el) {
        var key = el.dataset.key, col = el.dataset.color, unit = el.dataset.unit, dec = parseInt(el.dataset.dec || 0, 10);
        var pts = (window.TV_HIST || []).map(function (h) { return { id: h.id, d: h.date, v: +h[key] }; });
        if (!pts.length) { el.innerHTML = '<div class="empty">No history yet</div>'; return; }
        var W = 600, H = 230, pl = 52, pr = 18, pt = 18, pb = 34, n = pts.length;
        var lo = Math.min.apply(null, pts.map(function (p) { return p.v; })), hi = Math.max.apply(null, pts.map(function (p) { return p.v; }));
        if (hi === lo) { hi += Math.abs(hi) * .1 || 1; lo -= Math.abs(lo) * .1 || 1; }
        var padv = (hi - lo) * .15; hi += padv; lo = Math.max(0, lo - padv);
        function X(i) { return n === 1 ? (pl + (W - pr)) / 2 : pl + i * (W - pl - pr) / (n - 1); }
        function Y(v) { return pt + (1 - (v - lo) / (hi - lo)) * (H - pt - pb); }
        var s = '<svg viewBox="0 0 ' + W + ' ' + H + '"><defs><linearGradient id="g' + key + '" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="' + col + '" stop-opacity=".35"/><stop offset="1" stop-color="' + col + '" stop-opacity="0"/></linearGradient></defs>';
        for (var g = 0; g <= 4; g++) {
            var gv = lo + (hi - lo) * g / 4, gy = Y(gv);
            s += '<line x1="' + pl + '" x2="' + (W - pr) + '" y1="' + gy + '" y2="' + gy + '" stroke="rgba(255,255,255,.07)"/><text x="' + (pl - 8) + '" y="' + (gy + 4) + '" text-anchor="end" font-size="10" fill="#64748b">' + fmt(gv, dec) + '</text>';
        }
        var every = Math.ceil(n / 6);
        pts.forEach(function (p, i) { if (i % every === 0 || i === n - 1) s += '<text x="' + X(i) + '" y="' + (H - 10) + '" text-anchor="middle" font-size="10" fill="#64748b">' + p.d.slice(5) + '</text>'; });
        var d = 'M' + X(0) + ',' + Y(pts[0].v);
        for (var i = 1; i < n; i++) { var mx = (X(i - 1) + X(i)) / 2; d += ' C' + mx + ',' + Y(pts[i - 1].v) + ' ' + mx + ',' + Y(pts[i].v) + ' ' + X(i) + ',' + Y(pts[i].v); }
        if (n > 1) s += '<path d="' + d + ' L' + X(n - 1) + ',' + (H - pb) + ' L' + X(0) + ',' + (H - pb) + ' Z" fill="url(#g' + key + ')"/>';
        s += '<path class="ln" id="ln' + key + '" d="' + d + '" stroke="' + col + '" style="--len:1400"/>';
        pts.forEach(function (p, i) {
            var cur = p.id === window.TV_ID;
            if (cur) s += '<circle cx="' + X(i) + '" cy="' + Y(p.v) + '" r="11" fill="' + col + '" opacity=".18"><animate attributeName="r" values="8;15;8" dur="2s" repeatCount="indefinite"/></circle>';
            s += '<circle cx="' + X(i) + '" cy="' + Y(p.v) + '" r="' + (cur ? 5.5 : 3.6) + '" fill="' + (cur ? '#fff' : '#0b1426') + '" stroke="' + col + '" stroke-width="2.2"/>';
        });
        s += '<line id="cr' + key + '" y1="' + pt + '" y2="' + (H - pb) + '" stroke="' + col + '" stroke-opacity=".4" stroke-dasharray="3 4" style="display:none"/></svg><div class="tip"></div>';
        el.innerHTML = s;
        var path = el.querySelector('.ln');
        try { path.style.setProperty('--len', Math.ceil(path.getTotalLength()) + 2); } catch (e) {}
        var svg = el.querySelector('svg'), tip = el.querySelector('.tip'), cr = el.querySelector('line[id^=cr]');
        svg.addEventListener('mousemove', function (e) {
            var r = svg.getBoundingClientRect(), x = (e.clientX - r.left) / r.width * W, best = 0, bd = 1e9;
            pts.forEach(function (p, i) { var dd = Math.abs(X(i) - x); if (dd < bd) { bd = dd; best = i; } });
            var p = pts[best];
            cr.style.display = ''; cr.setAttribute('x1', X(best)); cr.setAttribute('x2', X(best));
            tip.style.opacity = 1; tip.style.left = (X(best) / W * r.width) + 'px'; tip.style.top = (Y(p.v) / H * r.height) + 'px';
            tip.innerHTML = '<span style="color:#9fb0cf">' + p.d + ' · Test #' + p.id + '</span><b style="color:' + col + '">' + fmt(p.v, dec) + ' ' + unit + '</b>';
        });
        svg.addEventListener('mouseleave', function () { tip.style.opacity = 0; cr.style.display = 'none'; });
    }
    document.querySelectorAll('.trend').forEach(trend);

    // reveal on scroll
    var io = new IntersectionObserver(function (es) {
        es.forEach(function (e) {
            if (!e.isIntersecting) return;
            e.target.classList.add('in');
            e.target.querySelectorAll('.cu').forEach(countUp);
            if (e.target.classList.contains('cu')) countUp(e.target);
            e.target.querySelectorAll('.trend, .bars').forEach(function (t) { t.classList.add('in'); });
            io.unobserve(e.target);
        });
    }, { threshold: .12 });
    root.querySelectorAll('.rv').forEach(function (el) { io.observe(el); });

    if (reduce) { var sv = root.querySelector('.vessel svg'); if (sv && sv.pauseAnimations) sv.pauseAnimations(); }
})();
</script>
@endsection
