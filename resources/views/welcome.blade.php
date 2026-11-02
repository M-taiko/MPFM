<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Oil Field Management System - Well surveillance, testing and intervention in one platform</title>
<meta name="description" content="Enterprise platform for well testing (MPFM and test vessel), Wire Line memory-gauge surveys, reservoir pressure and well performance analysis.">
<link rel="icon" href="{{ asset('assets/img/brand/favicon.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
<style>
:root { --bg:#060b18; --bg2:#0a1226; --card:#0d1730; --card2:#111d3a; --ink:#e8eefc; --sub:#9aaccb; --mute:#64748b; --line:rgba(120,150,210,.16);
    --blue:#3b82f6; --cyan:#22d3ee; --green:#34d399; --amber:#fbbf24; --red:#f87171; --violet:#a78bfa; --orange:#fb923c; --ease:cubic-bezier(.22,1,.36,1); }
* { box-sizing:border-box; margin:0; padding:0; }
html { scroll-behavior:smooth; scroll-padding-top:76px; }
body { background:var(--bg); color:var(--ink); font-family:'Inter',sans-serif; line-height:1.6; -webkit-font-smoothing:antialiased; overflow-x:hidden; }
a { color:inherit; text-decoration:none; }
.wrap { max-width:1200px; margin:0 auto; padding:0 24px; }
h1,h2,h3,h4 { letter-spacing:-.02em; line-height:1.15; }

/* nav */
.nav { position:fixed; top:0; left:0; right:0; z-index:50; background:rgba(6,11,24,.82); backdrop-filter:blur(14px); border-bottom:1px solid var(--line); }
.nav .wrap { display:flex; align-items:center; justify-content:space-between; height:64px; gap:20px; }
.brand { display:flex; align-items:center; gap:12px; font-weight:800; font-size:15px; }
.brand .lg { --h:38px; height:38px; aspect-ratio:600/940; border-radius:6px; background:#fff url('/assets/img/brand/logo.png') 50% 50.8%/333.33% auto no-repeat; border:1px solid rgba(255,255,255,.9); box-shadow:0 0 18px rgba(34,211,238,.35); }
.brand small { display:block; font-size:10px; font-weight:600; color:var(--sub); letter-spacing:.12em; text-transform:uppercase; }
.links { display:flex; gap:26px; font-size:13.5px; font-weight:500; color:var(--sub); }
.links a:hover { color:#fff; }
.btn { display:inline-flex; align-items:center; gap:8px; padding:11px 22px; border-radius:10px; font-size:14px; font-weight:700; border:1px solid var(--line); background:var(--card); color:#fff; transition:all .25s var(--ease); }
.btn:hover { border-color:var(--cyan); transform:translateY(-2px); }
.btn.p { background:linear-gradient(135deg,#2563eb,#0ea5e9); border:0; box-shadow:0 8px 26px rgba(14,165,233,.32); }
.btn.sm { padding:8px 16px; font-size:13px; }

/* hero */
.hero { position:relative; padding:150px 0 90px; background:radial-gradient(900px 500px at 78% 10%, rgba(37,99,235,.28), transparent 60%), radial-gradient(700px 400px at 5% 90%, rgba(14,165,233,.14), transparent 60%); overflow:hidden; }
.hero::before { content:""; position:absolute; inset:0; background-image:linear-gradient(rgba(59,130,246,.06) 1px,transparent 1px),linear-gradient(90deg,rgba(59,130,246,.06) 1px,transparent 1px); background-size:56px 56px; mask-image:radial-gradient(ellipse at 60% 20%,#000 10%,transparent 70%); -webkit-mask-image:radial-gradient(ellipse at 60% 20%,#000 10%,transparent 70%); }
.hero .wrap { position:relative; display:grid; grid-template-columns:1.05fr 1fr; gap:50px; align-items:center; }
.eyebrow { display:inline-flex; align-items:center; gap:9px; font-size:11.5px; font-weight:700; letter-spacing:.16em; text-transform:uppercase; color:var(--cyan); padding:6px 14px; border:1px solid rgba(34,211,238,.3); border-radius:30px; background:rgba(34,211,238,.06); }
.eyebrow i { width:7px; height:7px; border-radius:50%; background:var(--green); box-shadow:0 0 10px var(--green); }
.hero h1 { font-size:clamp(34px,4.6vw,58px); font-weight:800; margin:20px 0 18px; }
.hero h1 em { font-style:normal; background:linear-gradient(120deg,#60a5fa,#22d3ee,#a78bfa); -webkit-background-clip:text; background-clip:text; color:transparent; }
.hero p.lead { font-size:18px; color:var(--sub); max-width:560px; }
.hero .cta { display:flex; gap:14px; flex-wrap:wrap; margin-top:30px; }
.stats { display:grid; grid-template-columns:repeat(4,1fr); gap:1px; margin-top:44px; background:var(--line); border:1px solid var(--line); border-radius:14px; overflow:hidden; max-width:620px; }
.stats div { background:var(--bg2); padding:14px 16px; }
.stats b { display:block; font:700 24px 'Space Grotesk',sans-serif; color:#fff; } .stats span { font-size:11px; color:var(--mute); text-transform:uppercase; letter-spacing:.08em; font-weight:600; }

/* console mock */
.console { border-radius:18px; border:1px solid var(--line); background:linear-gradient(180deg,var(--card2),var(--card)); box-shadow:0 40px 90px rgba(0,0,0,.55), 0 0 0 1px rgba(255,255,255,.03); overflow:hidden; transform:perspective(1200px) rotateY(-6deg) rotateX(2deg); }
.console .bar { display:flex; gap:7px; padding:12px 16px; border-bottom:1px solid var(--line); align-items:center; }
.console .bar i { width:10px; height:10px; border-radius:50%; background:#2a3858; } .console .bar span { margin-left:10px; font-size:11px; color:var(--mute); }
.console svg { display:block; width:100%; height:auto; }

/* sections */
section { padding:92px 0; position:relative; }
section.alt { background:var(--bg2); border-top:1px solid var(--line); border-bottom:1px solid var(--line); }
.kicker { font-size:12px; font-weight:800; letter-spacing:.18em; text-transform:uppercase; color:var(--cyan); }
.sh { font-size:clamp(26px,3.2vw,40px); font-weight:800; margin:12px 0 14px; }
.sp { color:var(--sub); font-size:16.5px; max-width:700px; }
.center { text-align:center; } .center .sp { margin:0 auto; }

.mod { display:grid; grid-template-columns:1fr 1.1fr; gap:56px; align-items:center; }
.mod.rev .txt { order:2; }
.num { font:700 13px 'Space Grotesk',sans-serif; color:var(--cyan); letter-spacing:.14em; }
.mod h3 { font-size:clamp(24px,2.6vw,34px); font-weight:800; margin:10px 0 14px; }
.mod p { color:var(--sub); }
.ticks { list-style:none; margin-top:20px; display:grid; gap:11px; }
.ticks li { display:flex; gap:12px; font-size:14.5px; color:var(--ink); }
.ticks li::before { content:""; flex:none; width:20px; height:20px; border-radius:50%; margin-top:2px; background:rgba(52,211,153,.14) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3E%3Cpath d='M5.5 10.5l3 3 6-6.5' fill='none' stroke='%2334d399' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E") center/contain no-repeat; }
.chips { display:flex; gap:8px; flex-wrap:wrap; margin-top:20px; }
.chip { font-size:11px; font-weight:700; letter-spacing:.06em; text-transform:uppercase; padding:5px 12px; border-radius:20px; border:1px solid var(--line); color:var(--sub); background:rgba(255,255,255,.03); }
.fig { border-radius:18px; border:1px solid var(--line); background:linear-gradient(180deg,var(--card2),var(--card)); padding:18px; box-shadow:0 24px 60px rgba(0,0,0,.4); }
.fig svg { display:block; width:100%; height:auto; }
.fig figcaption { font-size:12px; color:var(--mute); margin-top:10px; text-align:center; }
.fig text { font-family:'Inter',sans-serif; }

/* grids */
.cards { display:grid; grid-template-columns:repeat(3,1fr); gap:18px; margin-top:44px; }
.c { padding:24px; border-radius:16px; border:1px solid var(--line); background:var(--card); transition:all .3s var(--ease); }
.c:hover { transform:translateY(-5px); border-color:var(--cc,var(--cyan)); background:var(--card2); }
.c .ic { width:44px; height:44px; border-radius:12px; display:grid; place-items:center; background:color-mix(in srgb, var(--cc,var(--cyan)) 16%, transparent); color:var(--cc,var(--cyan)); margin-bottom:16px; }
.c .ic svg { width:22px; height:22px; stroke:currentColor; fill:none; stroke-width:1.8; stroke-linecap:round; stroke-linejoin:round; }
.c h4 { font-size:17px; font-weight:700; margin-bottom:6px; } .c p { font-size:14px; color:var(--sub); }
.c .soon { display:inline-block; margin-top:12px; font-size:10px; font-weight:800; letter-spacing:.12em; text-transform:uppercase; padding:3px 10px; border-radius:20px; background:rgba(251,191,36,.12); color:var(--amber); }
.c .live { display:inline-block; margin-top:12px; font-size:10px; font-weight:800; letter-spacing:.12em; text-transform:uppercase; padding:3px 10px; border-radius:20px; background:rgba(52,211,153,.12); color:var(--green); }

.cta-s { text-align:center; background:radial-gradient(ellipse at 50% 100%, rgba(37,99,235,.35), transparent 65%), var(--bg); }
.cta-s .cta { justify-content:center; display:flex; gap:14px; margin-top:30px; flex-wrap:wrap; }
footer { border-top:1px solid var(--line); padding:28px 0; color:var(--mute); font-size:12.5px; }
footer .wrap { display:flex; justify-content:space-between; gap:20px; flex-wrap:wrap; }

/* company logo badge */
.lg { --h:64px; display:inline-block; flex:none; height:var(--h); aspect-ratio:600/940; border-radius:calc(var(--h) * .14); background:#fff url('/assets/img/brand/logo.png') 50% 50.8%/333.33% auto no-repeat; border:2px solid rgba(255,255,255,.9); box-shadow:0 0 0 1px rgba(59,130,246,.5),0 8px 30px rgba(0,0,0,.45),0 0 26px rgba(34,211,238,.35); position:relative; overflow:hidden; }
.lg::after { content:""; position:absolute; top:0; left:-130%; width:60%; height:100%; background:linear-gradient(90deg,transparent,rgba(34,211,238,.45),transparent); transform:skewX(-20deg); animation:shine 4.5s infinite; }
@keyframes shine { 60%,100% { left:160%; } }
@keyframes spin { to { transform:rotate(360deg); } }
@keyframes glowlg { 50% { box-shadow:0 0 0 1px rgba(59,130,246,.6),0 10px 40px rgba(0,0,0,.5),0 0 60px rgba(34,211,238,.7); transform:translateY(-4px); } }
.hero-logo { display:flex; align-items:center; gap:20px; margin-bottom:26px; }
.hero-logo .lg { --h:112px; animation:glowlg 3.2s ease-in-out infinite; }
.hero-logo .nm { font:800 clamp(16px,1.8vw,22px) 'Space Grotesk',sans-serif; letter-spacing:.1em; text-transform:uppercase; line-height:1.25; }
.hero-logo .nm small { display:block; color:var(--cyan); font-size:11px; letter-spacing:.24em; margin-top:4px; }
.cta-s .lg { --h:130px; margin-bottom:22px; animation:glowlg 3.2s ease-in-out infinite; }
/* production-flow drawings (MPFM + test separator) and readings */
.flow-box { margin-top:22px; padding:16px 14px 12px; border-radius:22px; border:1px solid var(--line); background:linear-gradient(180deg,rgba(15,32,68,.55),rgba(5,10,21,.65)); }
.flow-box svg { width:100%; height:auto; max-height:56vh; display:block; }
.blink { animation:blink 1.4s infinite; } @keyframes blink { 50% { opacity:.25; } }
.legend { display:flex; gap:22px; justify-content:center; margin-top:14px; font-size:12px; color:var(--sub); flex-wrap:wrap; }
.legend i { display:inline-block; width:18px; height:4px; border-radius:2px; margin-right:7px; vertical-align:middle; }
.fx .po { fill:none; stroke:#3d6fc4; stroke-width:16; }
.fx .pi { fill:none; stroke:#081226; stroke-width:11; }
.fx .fl { fill:none; stroke-linecap:round; animation:fxf var(--t,1s) linear infinite; }
@keyframes fxf { to { stroke-dashoffset:calc(var(--p) * -1); } }
.fx .fo { stroke:#d97706; stroke-width:3; stroke-dasharray:14 9; --p:23px; --t:1.2s; }
.fx .fw { stroke:#38bdf8; stroke-width:2.2; stroke-dasharray:6 16; --p:22px; --t:1.5s; }
.fx .fg { stroke:#e2e8f0; stroke-width:3.2; stroke-dasharray:.1 12; --p:12.1px; --t:.8s; }
.fx .fs { stroke:#22d3ee; stroke-width:1.6; stroke-dasharray:2 5; --p:7px; --t:.7s; opacity:.8; }
.fx .tx { font:600 11px Inter,sans-serif; fill:#cfe0ff; }
.fx .tx.s { font-size:9.5px; fill:#9fb0cf; font-weight:500; }
.fx .tx.m { font:600 9px 'Space Grotesk',monospace; fill:#34d399; }
.fx .cap { fill:#0b1a3a; stroke-width:1.2; }
.fx .eqp { fill:#0d1b3a; stroke:#3b82f6; stroke-width:1.6; }
.fx .spin { transform-box:fill-box; transform-origin:center; animation:spin 2.4s linear infinite; }
.fx .beam { animation:beam 1.4s ease-in-out infinite; }
@keyframes beam { 0%,100% { opacity:.25; } 50% { opacity:1; } }
.fx .wave { animation:wvx 3s linear infinite; }
@keyframes wvx { to { transform:translateX(-50px); } }
.fx .wave.r { animation-direction:reverse; animation-duration:4.2s; }
.fx .bub { transform-box:fill-box; transform-origin:center; animation:rise 3.2s ease-in infinite; }
@keyframes rise { 0% { transform:translateY(0) scale(.6); opacity:0; } 15% { opacity:.9; } 100% { transform:translateY(-34px) scale(1.2); opacity:0; } }
.fx .drp { animation:fall 2.2s ease-in infinite; }
@keyframes fall { 0% { transform:translateY(0); opacity:0; } 15% { opacity:.9; } 100% { transform:translateY(32px); opacity:0; } }
.fx .needle { transform-origin:350px 226px; animation:needle 2.6s ease-in-out infinite; }
@keyframes needle { 0%,100% { transform:rotate(-16deg); } 40% { transform:rotate(10deg); } 70% { transform:rotate(-4deg); } }
.fx .vent { animation:vent 2.4s ease-out infinite; transform-box:fill-box; transform-origin:center bottom; }
@keyframes vent { 0% { transform:translateY(0) scale(.5); opacity:.7; } 100% { transform:translateY(-16px) scale(1.4); opacity:0; } }
.fx .chk { stroke-dasharray:30; stroke-dashoffset:30; animation:chk 4s ease-out infinite; }
@keyframes chk { 0%,10% { stroke-dashoffset:30; } 35%,90% { stroke-dashoffset:0; } 100% { stroke-dashoffset:30; } }
.fx .ln-d { animation:blink 1s infinite; }

/* ── 4b READINGS ── */
.rd { margin-top:28px; display:grid; grid-template-columns:repeat(3,1fr); gap:18px; }
.rcard { padding:20px 20px 16px; border-radius:20px; background:linear-gradient(180deg,rgba(255,255,255,.06),rgba(255,255,255,.02)); border:1px solid var(--line); border-top:3px solid var(--ac); position:relative; overflow:hidden; }
.rcard.m { --ac:var(--cyan); } .rcard.s { --ac:#f59e0b; } .rcard.r { --ac:var(--green); }
.rcard::after { content:""; position:absolute; left:-30%; top:0; width:30%; height:100%; background:linear-gradient(90deg,transparent,rgba(255,255,255,.06),transparent); transform:skewX(-20deg); animation:shine 6s infinite; }
.rcard header { display:flex; align-items:flex-start; justify-content:space-between; gap:10px; margin-bottom:10px; }
.rcard h4 { font-size:17px; } .rcard header p { font-size:12px; color:var(--sub); margin-top:2px; max-width:240px; }
.live-b { flex:none; font:700 10px 'Space Grotesk'; letter-spacing:.16em; color:var(--green); display:flex; align-items:center; gap:6px; padding:4px 10px; border-radius:50px; background:rgba(52,211,153,.1); border:1px solid rgba(52,211,153,.3); }
.live-b i { width:7px; height:7px; border-radius:50%; background:var(--green); animation:blink 1.1s infinite; }
.rrow { display:flex; align-items:baseline; justify-content:space-between; gap:10px; padding:7px 0; border-bottom:1px dashed rgba(96,165,250,.14); font-size:13px; color:var(--sub); }
.rrow:last-of-type { border:0; }
.rrow b { font:700 19px 'Space Grotesk',sans-serif; color:#fff; font-variant-numeric:tabular-nums; margin-left:auto; }
.rrow u { text-decoration:none; font-size:11px; color:var(--mute); width:60px; }
.rcard.m .rrow b.k { color:var(--cyan); } .rcard.s .rrow b.k { color:#fbbf24; }
.rcard footer { margin-top:10px; font-size:11px; color:var(--mute); line-height:1.5; }
.dvr { padding:9px 0; }
.dvr > div:first-child { display:flex; justify-content:space-between; font-size:13px; color:var(--sub); }
.dvr b { font:700 15px 'Space Grotesk'; color:#fff; font-variant-numeric:tabular-nums; }
.dvt { position:relative; height:8px; margin-top:7px; border-radius:6px; background:linear-gradient(90deg,rgba(248,113,113,.22) 0 10%,rgba(251,191,36,.14) 10% 30%,rgba(52,211,153,.16) 30% 70%,rgba(251,191,36,.14) 70% 90%,rgba(248,113,113,.22) 90%); }
.dvt::before { content:""; position:absolute; left:50%; top:-3px; bottom:-3px; width:1px; background:var(--sub); }
.dvt i { position:absolute; top:0; height:100%; border-radius:6px; background:var(--green); transition:left .8s var(--ease), width .8s var(--ease), background .4s; }
.verdict { margin:12px 0 10px; padding:10px 14px; border-radius:12px; font-weight:700; font-size:13px; text-align:center; color:var(--green); background:rgba(52,211,153,.1); border:1px solid rgba(52,211,153,.35); }
.verdict.bad { color:var(--red); background:rgba(248,113,113,.1); border-color:rgba(248,113,113,.35); }
.meta { display:grid; grid-template-columns:1fr 1fr; gap:6px 14px; font-size:12px; color:var(--sub); }
.meta b { color:#fff; font-weight:600; }
@media (max-width:1100px) { .rd { grid-template-columns:1fr; } }

.fx .v { fill:#fff; font-weight:800; }
/* reveal + svg draw */
.rv { opacity:0; transform:translateY(28px); transition:opacity .9s var(--ease), transform .9s var(--ease); }
.rv.in { opacity:1; transform:none; }
.draw { stroke-dasharray:var(--len,1400); stroke-dashoffset:var(--len,1400); transition:stroke-dashoffset 2.4s var(--ease) .2s; }
.in .draw, .draw.in { stroke-dashoffset:0; }
.pulse { animation:pulse 2.4s ease-in-out infinite; } @keyframes pulse { 50% { opacity:.35; } }
.flowd { stroke-dasharray:4 8; animation:flowd 1.2s linear infinite; } @keyframes flowd { to { stroke-dashoffset:-24; } }
@media (prefers-reduced-motion: reduce) { .rv { opacity:1; transform:none; transition:none; } .draw { stroke-dashoffset:0; transition:none; } .flowd,.pulse { animation:none; } html { scroll-behavior:auto; } }

@media (max-width:980px) {
  .hero .wrap, .mod { grid-template-columns:1fr; } .mod.rev .txt { order:0; } .console { transform:none; }
  .links { display:none; } .cards { grid-template-columns:1fr 1fr; } .stats { grid-template-columns:repeat(2,1fr); }
}
@media (max-width:620px) { .cards { grid-template-columns:1fr; } section { padding:64px 0; } .hero { padding-top:120px; } .brand small { display:none; } }
</style>
</head>
<body>

<header class="nav">
    <div class="wrap">
        <a class="brand" href="#top"><span class="lg" role="img" aria-label="Logo"></span><span>Oil Field<small>Management System</small></span></a>
        <nav class="links" aria-label="Sections">
            <a href="#platform">Platform</a><a href="#flow">Production flow</a><a href="#mpfm">MPFM</a><a href="#vessel">Test Vessel</a><a href="#wireline">Wire Line</a><a href="#analysis">Well Analysis</a><a href="#governance">Governance</a><a href="#roadmap">Roadmap</a>
        </nav>
        @auth<a href="{{ url('/dashboard') }}" class="btn p sm">Open dashboard</a>@else<a href="{{ route('login') }}" class="btn p sm">Sign in</a>@endauth
    </div>
</header>

<main id="top">
{{-- ═════════ HERO ═════════ --}}
<div class="hero">
    <div class="wrap">
        <div>
            <div class="hero-logo"><span class="lg" role="img" aria-label="Company logo"></span><span class="nm">Oil Field<br>Management System<small>Well operations platform</small></span></div>
            <span class="eyebrow"><i></i> Integrated well operations platform</span>
            <h1>One system for <em>well testing, intervention</em> and reservoir insight.</h1>
            <p class="lead">Bring MPFM and test-vessel results, Wire Line memory-gauge surveys and well performance analysis into a single, governed source of truth - from the wellhead to the reservoir.</p>
            <div class="cta">
                @auth<a href="{{ url('/dashboard') }}" class="btn p">Open dashboard</a>@else<a href="{{ route('login') }}" class="btn p">Access the system</a>@endauth
                <a href="#platform" class="btn">Explore the platform</a>
            </div>
            <div class="stats">
                <div><b>4</b><span>Live modules</span></div><div><b>8</b><span>Role profiles</span></div><div><b>2</b><span>Gauge formats</span></div><div><b>180 d</b><span>Survey policy</span></div>
            </div>
        </div>

        <div class="console rv" aria-hidden="true">
            <div class="bar"><i></i><i></i><i></i><span>Operations command center</span></div>
            <svg viewBox="0 0 560 360">
                <defs>
                    <linearGradient id="hArea" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#22d3ee" stop-opacity=".35"/><stop offset="1" stop-color="#22d3ee" stop-opacity="0"/></linearGradient>
                </defs>
                <g font-size="9" fill="#64748b" font-weight="700" letter-spacing="1">
                    <rect x="20" y="18" width="122" height="62" rx="10" fill="#0a1226" stroke="#1d2b4d"/><text x="32" y="38">OIL B/D</text><text x="32" y="64" font-size="22" fill="#fff" font-family="Space Grotesk" letter-spacing="0">4,820</text>
                    <rect x="150" y="18" width="122" height="62" rx="10" fill="#0a1226" stroke="#1d2b4d"/><text x="162" y="38">WATER CUT</text><text x="162" y="64" font-size="22" fill="#38bdf8" font-family="Space Grotesk" letter-spacing="0">18.4%</text>
                    <rect x="280" y="18" width="122" height="62" rx="10" fill="#0a1226" stroke="#1d2b4d"/><text x="292" y="38">BHP PSI</text><text x="292" y="64" font-size="22" fill="#a78bfa" font-family="Space Grotesk" letter-spacing="0">2,497</text>
                    <rect x="410" y="18" width="130" height="62" rx="10" fill="#0a1226" stroke="#1d2b4d"/><text x="422" y="38">WELL HEALTH</text><text x="422" y="64" font-size="22" fill="#34d399" font-family="Space Grotesk" letter-spacing="0">82 / 100</text>
                </g>
                <rect x="20" y="94" width="350" height="190" rx="10" fill="#0a1226" stroke="#1d2b4d"/>
                <g stroke="#1b2847"><path d="M36 130H354M36 168H354M36 206H354M36 244H354"/></g>
                <path d="M36 236 C80 220 110 190 150 196 S220 150 262 160 S320 120 354 112 V262 H36Z" fill="url(#hArea)"/>
                <path class="draw" style="--len:520" d="M36 236 C80 220 110 190 150 196 S220 150 262 160 S320 120 354 112" fill="none" stroke="#22d3ee" stroke-width="2.4" stroke-linecap="round"/>
                <path class="draw" style="--len:520" d="M36 252 C90 250 130 244 180 236 S280 226 354 208" fill="none" stroke="#38bdf8" stroke-width="1.6" stroke-dasharray="4 4" opacity=".8"/>
                <text x="36" y="112" font-size="10" fill="#9aaccb" font-weight="600">Production trend</text>
                <rect x="382" y="94" width="158" height="190" rx="10" fill="#0a1226" stroke="#1d2b4d"/>
                <text x="394" y="112" font-size="10" fill="#9aaccb" font-weight="600">Needs attention</text>
                <g font-size="9.5" fill="#c9d6ef">
                    <circle cx="396" cy="136" r="4" fill="#f87171"/><text x="408" y="139">2 surveys overdue</text>
                    <circle cx="396" cy="162" r="4" fill="#fbbf24"/><text x="408" y="165">3 tests in review</text>
                    <circle cx="396" cy="188" r="4" fill="#a78bfa"/><text x="408" y="191">1 open Wire Line job</text>
                    <circle cx="396" cy="214" r="4" fill="#34d399"/><text x="408" y="217">Meters within ±10%</text>
                </g>
                <g>
                    <rect x="20" y="298" width="520" height="44" rx="10" fill="#0a1226" stroke="#1d2b4d"/>
                    <g fill="#22d3ee" opacity=".9"><rect x="40" y="322" width="10" height="8" rx="2"/><rect x="58" y="316" width="10" height="14" rx="2"/><rect x="76" y="319" width="10" height="11" rx="2"/><rect x="94" y="310" width="10" height="20" rx="2"/><rect x="112" y="314" width="10" height="16" rx="2"/></g>
                    <text x="140" y="318" font-size="10" fill="#9aaccb" font-weight="600">MPFM · Test Vessel · Wire Line</text><text x="140" y="332" font-size="9" fill="#64748b">Unified activity across every well</text>
                </g>
            </svg>
        </div>
    </div>
</div>

{{-- ═════════ PLATFORM OVERVIEW ═════════ --}}
<section id="platform" class="alt">
    <div class="wrap">
        <div class="center rv"><div class="kicker">Platform</div><h2 class="sh">Everything starts at the well</h2>
            <p class="sp">Every test, survey and intervention is tied to a well in the field hierarchy, so a single page can tell its full story.</p></div>
        <figure class="fig rv" style="margin-top:44px">
            <svg viewBox="0 0 1000 420" role="img" aria-label="Platform diagram: modules connected around the well record">
                <defs><radialGradient id="hubg" cx=".5" cy=".4"><stop offset="0" stop-color="#1d4ed8"/><stop offset="1" stop-color="#0b1a3a"/></radialGradient></defs>
                <g fill="none" stroke-width="1.6" stroke-linecap="round" class="flowd">
                    <path d="M500 210 L170 90" stroke="#22d3ee"/><path d="M500 210 L170 330" stroke="#fbbf24"/><path d="M500 210 L500 62" stroke="#a78bfa"/>
                    <path d="M500 210 L830 90" stroke="#34d399"/><path d="M500 210 L830 330" stroke="#38bdf8"/><path d="M500 210 L500 358" stroke="#f87171"/>
                </g>
                <circle cx="500" cy="210" r="62" fill="url(#hubg)" stroke="#3b82f6" stroke-width="2"/><circle cx="500" cy="210" r="76" fill="none" stroke="#3b82f6" stroke-opacity=".3" class="pulse"/>
                <text x="500" y="204" text-anchor="middle" font-size="15" font-weight="800" fill="#fff">WELL</text><text x="500" y="224" text-anchor="middle" font-size="10" fill="#9aaccb">single record</text>
                <g font-size="12" font-weight="700" fill="#fff" text-anchor="middle">
                    <g transform="translate(90,70)"><rect width="160" height="64" rx="12" fill="#0d1730" stroke="#22d3ee"/><text x="80" y="28">MPFM</text><text x="80" y="46" font-size="10" fill="#9aaccb" font-weight="500">Log upload &amp; validation</text></g>
                    <g transform="translate(90,298)"><rect width="160" height="64" rx="12" fill="#0d1730" stroke="#fbbf24"/><text x="80" y="28">Test Vessel</text><text x="80" y="46" font-size="10" fill="#9aaccb" font-weight="500">Separator tests &amp; requests</text></g>
                    <g transform="translate(420,-2)"><rect width="160" height="64" rx="12" fill="#0d1730" stroke="#a78bfa"/><text x="80" y="28">Wire Line</text><text x="80" y="46" font-size="10" fill="#9aaccb" font-weight="500">Memory-gauge surveys</text></g>
                    <g transform="translate(750,70)"><rect width="160" height="64" rx="12" fill="#0d1730" stroke="#34d399"/><text x="80" y="28">Well Analysis</text><text x="80" y="46" font-size="10" fill="#9aaccb" font-weight="500">Health &amp; recommendations</text></g>
                    <g transform="translate(750,298)"><rect width="160" height="64" rx="12" fill="#0d1730" stroke="#38bdf8"/><text x="80" y="28">Field Setup</text><text x="80" y="46" font-size="10" fill="#9aaccb" font-weight="500">Field → Tower → Well</text></g>
                    <g transform="translate(420,356)"><rect width="160" height="64" rx="12" fill="#0d1730" stroke="#f87171"/><text x="80" y="28">Governance</text><text x="80" y="46" font-size="10" fill="#9aaccb" font-weight="500">Roles, review, audit trail</text></g>
                </g>
            </svg>
            <figcaption>Modules share one well record - no re-keying, no spreadsheets passed between teams.</figcaption>
        </figure>
        <div class="cards">
            <div class="c rv" style="--cc:#22d3ee"><div class="ic"><svg viewBox="0 0 24 24"><path d="M3 12h4l3-8 4 16 3-8h4"/></svg></div><h4>Measure</h4><p>Ingest MPFM logs and vessel results with automatic validation against configurable rules.</p><span class="live">Live</span></div>
            <div class="c rv" style="--cc:#a78bfa"><div class="ic"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></div><h4>Intervene</h4><p>Record Wire Line operations and analyse memory-gauge pressure and temperature recordings.</p><span class="live">Live</span></div>
            <div class="c rv" style="--cc:#34d399"><div class="ic"><svg viewBox="0 0 24 24"><path d="M4 20V9M10 20V4M16 20v-8M22 20H2"/></svg></div><h4>Decide</h4><p>Health scores, decline forecasts and prioritised recommendations built from every data source.</p><span class="live">Live</span></div>
        </div>
    </div>
</section>

{{-- ═════════ PRODUCTION FLOW (MPFM + TEST SEPARATOR drawings) ═════════ --}}
<section id="flow">
    <div class="wrap">
        <div class="center rv"><div class="kicker">Production flow</div><h2 class="sh">One well. Two independent measurements.</h2>
            <p class="sp">The MPFM and the test separator sit on <b>separate parallel lines</b> - each measures the same well stream its own way, then both flows rejoin the production header.</p></div>
        <div class="flow-box rv" style="margin-top:40px">
        <svg class="fx" viewBox="0 0 1000 420" role="img" aria-label="Wells feed a manifold that splits into a parallel MPFM line and a test separator line, then rejoin to the production header and storage">
            <defs>
                <path id="W1" d="M70 110 H190" fill="none"/><path id="W2" d="M70 200 H190" fill="none"/><path id="W3" d="M70 290 H190" fill="none"/>
                <path id="MAN" d="M190 90 V300" fill="none"/>
                <path id="P1" d="M190 90 H700" fill="none"/>
                <path id="P1v" d="M340 90 H460" fill="none"/>
                <path id="P2" d="M190 300 H318" fill="none"/>
                <path id="P3" d="M520 250 V226 H700" fill="none"/>
                <path id="P4" d="M543 330 H700" fill="none"/>
                <path id="P5" d="M430 350 V374 H700" fill="none"/>
                <path id="P6" d="M700 90 V374" fill="none"/>
                <path id="P6a" d="M700 90 V233" fill="none"/>
                <path id="P6b" d="M700 374 V233" fill="none"/>
                <path id="P7" d="M700 233 H810" fill="none"/>
                <g id="vlv"><path d="M-8 -6 L8 6 V-6 L-8 6Z" fill="#0d1b3a" stroke="#22d3ee" stroke-width="1.4" stroke-linejoin="round"/><path d="M0 0 V-11 M-5 -11 H5" stroke="#22d3ee" stroke-width="1.6"/></g>
                <linearGradient id="fxOil" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#b45f0a"/><stop offset="1" stop-color="#5c3205"/></linearGradient>
                <linearGradient id="fxWat" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#2f8fd0"/><stop offset="1" stop-color="#12466f"/></linearGradient>
                <linearGradient id="fxSteel" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#4a7bd0"/><stop offset=".5" stop-color="#1b3a78"/><stop offset="1" stop-color="#3a66b8"/></linearGradient>
                <clipPath id="fxVes"><rect x="303" y="253" width="244" height="94" rx="39"/></clipPath>
                <clipPath id="fxTank"><rect x="810" y="190" width="130" height="206"/></clipPath>
            </defs>

            <path d="M0 398 H1000" stroke="#16305f" stroke-width="2"/>

            <!-- pipes -->
            <g>
                <use href="#W1" class="po"/><use href="#W2" class="po"/><use href="#W3" class="po"/><use href="#MAN" class="po"/><use href="#P1" class="po"/><use href="#P2" class="po"/>
                <use href="#P3" class="po"/><use href="#P4" class="po"/><use href="#P5" class="po"/><use href="#P6" class="po"/><use href="#P7" class="po"/>
                <use href="#W1" class="pi"/><use href="#W2" class="pi"/><use href="#W3" class="pi"/><use href="#MAN" class="pi"/><use href="#P1" class="pi"/><use href="#P2" class="pi"/>
                <use href="#P3" class="pi"/><use href="#P4" class="pi"/><use href="#P5" class="pi"/><use href="#P6" class="pi"/><use href="#P7" class="pi"/>
            </g>
            <!-- multiphase flow (oil + water + gas bubbles) -->
            <g>
                <use href="#W1" class="fl fo" transform="translate(0,-2.6)"/><use href="#W1" class="fl fw" transform="translate(0,2.6)"/><use href="#W1" class="fl fg"/>
                <use href="#W2" class="fl fo" transform="translate(0,-2.6)"/><use href="#W2" class="fl fw" transform="translate(0,2.6)"/><use href="#W2" class="fl fg"/>
                <use href="#W3" class="fl fo" transform="translate(0,-2.6)"/><use href="#W3" class="fl fw" transform="translate(0,2.6)"/><use href="#W3" class="fl fg"/>
                <use href="#MAN" class="fl fo" transform="translate(-2.6,0)"/><use href="#MAN" class="fl fw" transform="translate(2.6,0)"/><use href="#MAN" class="fl fg"/>
                <use href="#P1" class="fl fo" transform="translate(0,-2.6)"/><use href="#P1" class="fl fw" transform="translate(0,2.6)"/><use href="#P1" class="fl fg"/>
                <use href="#P2" class="fl fo" transform="translate(0,-2.6)"/><use href="#P2" class="fl fw" transform="translate(0,2.6)"/><use href="#P2" class="fl fg"/>
                <use href="#P6a" class="fl fo" transform="translate(-2.6,0)"/><use href="#P6a" class="fl fw" transform="translate(2.6,0)"/><use href="#P6a" class="fl fg"/>
                <use href="#P6b" class="fl fo" transform="translate(-2.6,0)"/><use href="#P6b" class="fl fw" transform="translate(2.6,0)"/>
                <use href="#P7" class="fl fo" transform="translate(0,-2.6)"/><use href="#P7" class="fl fw" transform="translate(0,2.6)"/><use href="#P7" class="fl fg" style="--t:.6s"/>
            </g>
            <!-- single-phase outlets of the separator -->
            <use href="#P3" class="fl fg" style="--t:.7s"/>
            <use href="#P4" class="fl fo" style="--t:1.5s"/>
            <use href="#P5" class="fl fw" style="--t:1.8s"/>

            <!-- WELLS -->
            <g>
                <rect x="61" y="141" width="6" height="257" fill="#10234a" stroke="#1e3a6e"/>
                <rect x="61" y="231" width="6" height="167" fill="#10234a" stroke="#1e3a6e"/>
                <rect x="61" y="321" width="6" height="77" fill="#10234a" stroke="#1e3a6e"/>
                <g id="tr1"><rect x="58" y="88" width="12" height="48" fill="#12306a" stroke="#60a5fa"/><rect x="55" y="118" width="18" height="7" fill="#0d1b3a" stroke="#22d3ee"/><rect x="50" y="136" width="28" height="5" rx="1" fill="#1e3a6e" stroke="#60a5fa"/><circle cx="64" cy="82" r="5" fill="#0d1b3a" stroke="#22d3ee"/></g>
                <use href="#tr1" y="90"/><use href="#tr1" y="180"/>
                <circle class="blink" cx="64" cy="82" r="2" fill="#34d399"/><circle class="blink" style="animation-delay:.4s" cx="64" cy="172" r="2" fill="#34d399"/><circle class="blink" style="animation-delay:.8s" cx="64" cy="262" r="2" fill="#34d399"/>
                <text class="tx" x="64" y="72" text-anchor="middle">W-01</text><text class="tx" x="64" y="162" text-anchor="middle">W-02</text><text class="tx" x="64" y="252" text-anchor="middle">W-03</text>
                <use href="#vlv" x="140" y="110"/><use href="#vlv" x="140" y="200"/><use href="#vlv" x="140" y="290"/>
            </g>
            <text class="tx s" x="176" y="72">TEST MANIFOLD</text>
            <text class="tx s" x="214" y="176">SAME WELL STREAM</text>
            <text class="tx s" x="214" y="189">split into two parallel</text>
            <text class="tx s" x="214" y="202">measurement paths</text>
            <path d="M206 168 L206 210" stroke="#22d3ee" stroke-width="1.5" opacity=".5"/>
            <use href="#vlv" x="250" y="90"/><use href="#vlv" x="250" y="300"/>

            <!-- ============ MPFM (top branch, inline) ============ -->
            <path d="M340 82 H365 L392 86 H408 L435 82 H460 V98 H435 L408 94 H392 L365 98 H340Z" fill="#2a55a4" stroke="#7fb4ff" stroke-width="1.4"/>
            <path d="M340 84.5 H365 L392 87.5 H408 L435 84.5 H460 V95.5 H435 L408 92.5 H392 L365 95.5 H340Z" fill="#081226"/>
            <use href="#P1v" class="fl fo" transform="translate(0,-2)" style="--t:.8s"/><use href="#P1v" class="fl fw" transform="translate(0,2)" style="--t:1s"/>
            <circle r="2.4" fill="#e2e8f0"><animateMotion dur="2.2s" repeatCount="indefinite" keyPoints="0;.38;.62;1" keyTimes="0;.5;.6;1" path="M340 88 H460"/></circle>
            <circle r="2" fill="#e2e8f0"><animateMotion dur="2.2s" begin="-.7s" repeatCount="indefinite" keyPoints="0;.38;.62;1" keyTimes="0;.5;.6;1" path="M340 91.5 H460"/></circle>
            <circle r="2.6" fill="#e2e8f0"><animateMotion dur="2.2s" begin="-1.4s" repeatCount="indefinite" keyPoints="0;.38;.62;1" keyTimes="0;.5;.6;1" path="M340 90 H460"/></circle>
            <rect x="336" y="128" width="128" height="6" rx="2" fill="#12306a" stroke="#60a5fa"/>
            <path d="M350 98 V128 M450 98 V128" stroke="#3d6fc4" stroke-width="3"/>
            <!-- gamma source / detector -->
            <polygon class="beam" points="394,62 406,62 412,104 388,104" fill="#fbbf24" opacity=".16"/>
            <path class="beam" d="M400 62 V104" stroke="#fbbf24" stroke-width="2" stroke-dasharray="3 3"/>
            <rect class="eqp" x="388" y="40" width="24" height="22" rx="3" style="stroke:#fbbf24"/><circle class="spin" cx="400" cy="51" r="5" fill="none" stroke="#fbbf24" stroke-width="1.6" stroke-dasharray="3 2.2"/>
            <rect class="eqp" x="388" y="104" width="24" height="18" rx="3" style="stroke:#fbbf24"/><path d="M394 113 H406" stroke="#fbbf24" stroke-width="2" class="beam"/>
            <!-- DP + P/T transmitters -->
            <rect class="eqp" x="345" y="44" width="30" height="20" rx="3" style="stroke:#22d3ee"/><circle class="blink" cx="352" cy="54" r="2" fill="#34d399"/><text class="tx s" x="368" y="57" text-anchor="middle" style="font-size:8px">DP</text>
            <path d="M352 64 V82 M368 64 V72 H392 V86" stroke="#94a3b8" stroke-width="1.4" fill="none"/>
            <rect class="eqp" x="422" y="56" width="22" height="14" rx="3" style="stroke:#22d3ee"/><text class="tx s" x="433" y="66" text-anchor="middle" style="font-size:8px">P/T</text>
            <path d="M433 70 V82" stroke="#94a3b8" stroke-width="1.4"/>
            <!-- flow computer -->
            <path d="M360 44 V32 H470 M412 51 H470 M444 63 H470" stroke="#22d3ee" stroke-width="1" stroke-dasharray="2 3" opacity=".5" fill="none"/>
            <rect x="470" y="24" width="74" height="54" rx="5" fill="#0b1a3a" stroke="#22d3ee" stroke-width="1.4"/>
            <rect x="476" y="30" width="62" height="30" rx="2" fill="#021016" stroke="#134e4a"/>
            <text class="tx m" x="480" y="40">OIL <tspan data-v="mo" data-nc="1">1248</tspan></text>
            <text class="tx m" x="480" y="49">GAS <tspan data-v="mg">2.31</tspan></text>
            <text class="tx m" x="480" y="58">WC&#160; <tspan data-v="mwc">24.8</tspan>%</text>
            <circle class="blink" cx="482" cy="69" r="2" fill="#34d399"/><circle class="blink" style="animation-delay:.3s" cx="492" cy="69" r="2" fill="#fbbf24"/><circle class="blink" style="animation-delay:.6s" cx="502" cy="69" r="2" fill="#22d3ee"/>
            <!-- MPFM readouts -->
            <rect class="cap" x="330" y="146" width="132" height="22" rx="11" style="stroke:#22d3ee"/>
            <circle class="blink" cx="345" cy="157" r="3.5" fill="#34d399"/><text class="tx" x="354" y="161">MPFM · M-01</text>
            <text class="tx s" x="396" y="182" text-anchor="middle">inline · no separation</text>
            <g>
                <text class="tx s" x="500" y="116">MPFM OUTPUT</text>
                <circle cx="504" cy="127" r="3" fill="#d97706"/><text class="tx" x="512" y="130">OIL <tspan class="v" data-v="mo">1,248</tspan> <tspan class="tx s">bbl/d</tspan></text>
                <circle cx="504" cy="141" r="3" fill="#e2e8f0"/><text class="tx" x="512" y="144">GAS <tspan class="v" data-v="mg">2.31</tspan> <tspan class="tx s">MMscf/d</tspan></text>
                <circle cx="504" cy="155" r="3" fill="#38bdf8"/><text class="tx" x="512" y="158">WATER <tspan class="v" data-v="mw">412</tspan> <tspan class="tx s">bbl/d</tspan></text>
            </g>

            <!-- ============ TEST SEPARATOR (bottom branch) ============ -->
            <path d="M335 350 L329 362 H371 L365 350Z M485 350 L479 362 H521 L515 350Z" fill="#12306a" stroke="#60a5fa" stroke-width="1.4"/>
            <rect x="300" y="250" width="250" height="100" rx="42" fill="url(#fxSteel)" stroke="#7fb4ff" stroke-width="2.5"/>
            <g clip-path="url(#fxVes)">
                <rect x="300" y="250" width="250" height="38" fill="#0c1a38"/>
                <rect x="300" y="288" width="208" height="40" fill="url(#fxOil)"/>
                <rect x="300" y="326" width="208" height="24" fill="url(#fxWat)"/>
                <rect x="508" y="312" width="42" height="38" fill="url(#fxOil)"/>
                <rect x="505" y="292" width="3.5" height="58" fill="#cbd5e1"/>
                <g class="wave"><path d="M290 288 q12.5 -4 25 0 t25 0 t25 0 t25 0 t25 0 t25 0 t25 0 t25 0 t25 0 t25 0 t25 0 t25 0" fill="none" stroke="#fcd34d" stroke-width="1.8"/></g>
                <g class="wave r"><path d="M290 326 q12.5 -2.5 25 0 t25 0 t25 0 t25 0 t25 0 t25 0 t25 0 t25 0 t25 0 t25 0 t25 0 t25 0" fill="none" stroke="#7dd3fc" stroke-width="1.6"/></g>
                <path d="M508 312 q10 -2 20 0 t20 0" fill="none" stroke="#fcd34d" stroke-width="1.5"/>
                <path d="M316 286 L330 300 L316 314" fill="none" stroke="#cbd5e1" stroke-width="2.5"/>
                <use href="#P2" class="fl fo" transform="translate(0,-2.6)" style="--t:.5s"/><use href="#P2" class="fl fw" transform="translate(0,2.6)" style="--t:.6s"/>
                <path d="M334 270 H520" fill="none" class="fl fg" style="--t:.9s"/>
                <rect x="478" y="253" width="34" height="14" fill="none" stroke="#94a3b8" stroke-dasharray="2 2"/>
                <circle class="bub" cx="345" cy="318" r="2.2" fill="#fde68a"/><circle class="bub" style="animation-delay:.8s" cx="380" cy="320" r="1.8" fill="#fde68a"/><circle class="bub" style="animation-delay:1.6s" cx="415" cy="319" r="2.4" fill="#fde68a"/>
                <circle class="bub" style="animation-delay:.4s" cx="450" cy="321" r="2" fill="#fde68a"/><circle class="bub" style="animation-delay:1.2s" cx="485" cy="320" r="2.2" fill="#fde68a"/>
                <circle class="drp" cx="350" cy="292" r="1.8" fill="#7dd3fc"/><circle class="drp" style="animation-delay:.7s" cx="395" cy="293" r="1.6" fill="#7dd3fc"/><circle class="drp" style="animation-delay:1.4s" cx="445" cy="292" r="1.8" fill="#7dd3fc"/>
                <circle r="1.8" fill="#e2e8f0"><animateMotion dur="1.4s" repeatCount="indefinite" path="M326 296 Q346 274 374 288"/></circle>
                <circle r="1.6" fill="#d97706"><animateMotion dur="1.4s" begin="-.5s" repeatCount="indefinite" path="M326 304 Q346 322 372 322"/></circle>
                <circle r="1.6" fill="#38bdf8"><animateMotion dur="1.4s" begin="-.9s" repeatCount="indefinite" path="M326 300 Q352 300 380 310"/></circle>
            </g>
            <path d="M340 258 H500" stroke="#fff" stroke-width="1.5" opacity=".12"/>
            <!-- gauge, RTU, PSV -->
            <path d="M350 239 V250" stroke="#94a3b8" stroke-width="3"/>
            <circle cx="350" cy="226" r="13" fill="#0a1630" stroke="#94a3b8" stroke-width="2"/>
            <path d="M341 232 A11 11 0 1 1 359 232" fill="none" stroke="#475a85" stroke-width="2" stroke-dasharray="2 2.4"/>
            <path class="needle" d="M350 226 V216" stroke="#f87171" stroke-width="2" stroke-linecap="round"/><circle cx="350" cy="226" r="2" fill="#cbd5e1"/>
            <path d="M422 238 V250" stroke="#94a3b8" stroke-width="2" stroke-dasharray="2 2"/>
            <rect x="392" y="196" width="60" height="42" rx="4" fill="#0b1a3a" stroke="#f59e0b" stroke-width="1.4"/>
            <rect x="397" y="201" width="50" height="24" rx="2" fill="#150e02" stroke="#78450a"/>
            <text class="tx m" x="400" y="211" style="fill:#fbbf24">OIL <tspan data-v="so" data-nc="1">1271</tspan></text>
            <text class="tx m" x="400" y="221" style="fill:#fbbf24">GAS <tspan data-v="sg">2.26</tspan></text>
            <circle class="blink" cx="402" cy="232" r="2" fill="#34d399"/><circle class="blink" style="animation-delay:.5s" cx="412" cy="232" r="2" fill="#f59e0b"/>
            <path d="M475 250 V236" stroke="#94a3b8" stroke-width="3"/><path d="M467 236 H483 L475 222Z" fill="#7f1d1d" stroke="#f87171" stroke-width="1.2"/>
            <circle class="vent" cx="475" cy="216" r="3" fill="#cbd5e1"/><circle class="vent" style="animation-delay:1.2s" cx="475" cy="216" r="3" fill="#cbd5e1"/>
            <!-- separator label -->
            <rect class="cap" x="160" y="326" width="136" height="22" rx="11" style="stroke:#f59e0b"/>
            <circle class="blink" cx="175" cy="337" r="3.5" fill="#34d399"/><text class="tx" x="184" y="341">Test Separator V-101</text>
            <text class="tx s" x="228" y="362" text-anchor="middle">3-phase · each phase metered</text>
            <!-- outlet meters + readouts -->
            <rect x="556" y="214" width="3" height="24" fill="#94a3b8"/><rect x="564" y="214" width="3" height="24" fill="#94a3b8"/>
            <path d="M558 214 V208 M565 214 V208" stroke="#94a3b8" stroke-width="1.4"/><circle cx="562" cy="202" r="6" fill="#0d1b3a" stroke="#22d3ee"/><circle class="blink" cx="562" cy="202" r="1.8" fill="#34d399"/>
            <text class="tx s" x="580" y="195">GAS · orifice meter</text>
            <text class="tx" x="580" y="208"><tspan class="v" data-v="sg">2.26</tspan> <tspan class="tx s">MMscf/d</tspan></text>
            <circle cx="590" cy="330" r="11" fill="#0d1b3a" stroke="#f59e0b" stroke-width="1.6"/><path d="M584 325 q6 11 12 0" fill="none" stroke="#f59e0b" stroke-width="1.6"/>
            <text class="tx s" x="610" y="299">OIL · Coriolis meter</text>
            <text class="tx" x="610" y="311"><tspan class="v" data-v="so">1,271</tspan> <tspan class="tx s">bbl/d</tspan></text>
            <circle cx="560" cy="374" r="10" fill="#0d1b3a" stroke="#38bdf8" stroke-width="1.6"/><path class="spin" d="M555 374 H565 M560 369 V379" stroke="#38bdf8" stroke-width="1.6" style="transform-origin:560px 374px;transform-box:view-box"/>
            <text class="tx s" x="580" y="346">WATER · meter</text>
            <text class="tx" x="580" y="359"><tspan class="v" data-v="sw">405</tspan> <tspan class="tx s">bbl/d</tspan></text>
            <text class="tx s" x="500" y="388">SEPARATOR OUTPUT</text>
            <use href="#vlv" x="640" y="374"/>

            <!-- header, export, storage -->
            <text class="tx s" x="716" y="216">PRODUCTION HEADER</text>
            <text class="tx s" x="716" y="258">to export</text>
            <rect x="810" y="190" width="130" height="206" fill="#0a1630" stroke="#3b82f6" stroke-width="1.6"/>
            <g clip-path="url(#fxTank)">
                <rect x="810" y="300" width="130" height="96" fill="url(#fxOil)"><animate attributeName="y" values="300;286;300" dur="14s" repeatCount="indefinite"/><animate attributeName="height" values="96;110;96" dur="14s" repeatCount="indefinite"/></rect>
                <g class="wave r"><path d="M800 300 q12.5 -3 25 0 t25 0 t25 0 t25 0 t25 0 t25 0 t25 0" fill="none" stroke="#fcd34d" stroke-width="1.6"><animateTransform attributeName="transform" type="translate" values="0 0;0 -14;0 0" dur="14s" repeatCount="indefinite"/></path></g>
            </g>
            <ellipse cx="875" cy="190" rx="65" ry="9" fill="#12306a" stroke="#60a5fa" stroke-width="1.6"/>
            <path d="M810 396 a65 9 0 0 0 130 0" fill="#0a1630" stroke="#3b82f6" stroke-width="1.4"/>
            <path d="M946 394 V196 M946 394 H952" stroke="#64748b" stroke-width="1.5" stroke-dasharray="4 3"/>
            <text class="tx" x="875" y="218" text-anchor="middle">STORAGE</text><text class="tx s" x="875" y="231" text-anchor="middle">export tank</text>

            <!-- report + data links -->
            <path d="M544 50 H800" stroke="#22d3ee" stroke-width="1.2" stroke-dasharray="2 4" fill="none" opacity=".7"/>
            <path d="M452 217 H462 V176 H770 V98 H800" stroke="#f59e0b" stroke-width="1.2" stroke-dasharray="2 4" fill="none" opacity=".7"/>
            <circle r="3.2" fill="#22d3ee"><animateMotion dur="2.4s" repeatCount="indefinite" path="M544 50 H800"/></circle>
            <circle r="3.2" fill="#22d3ee"><animateMotion dur="2.4s" begin="-1.2s" repeatCount="indefinite" path="M544 50 H800"/></circle>
            <circle r="3.2" fill="#f59e0b"><animateMotion dur="4.4s" repeatCount="indefinite" path="M452 217 H462 V176 H770 V98 H800"/></circle>
            <circle r="3.2" fill="#f59e0b"><animateMotion dur="4.4s" begin="-2.2s" repeatCount="indefinite" path="M452 217 H462 V176 H770 V98 H800"/></circle>
            <rect x="800" y="26" width="140" height="92" rx="8" fill="#0d1b3a" stroke="#22d3ee" stroke-width="1.6"/>
            <rect x="800" y="26" width="140" height="20" rx="8" fill="#12306a"/><text class="tx" x="870" y="40" text-anchor="middle" style="fill:#67e8f9">TEST REPORT · W-02</text>
            <rect x="810" y="55" width="70" height="5" rx="2" fill="#22d3ee" opacity=".7"/><rect x="810" y="66" width="104" height="5" rx="2" fill="#1e3a6e"/><rect x="810" y="77" width="88" height="5" rx="2" fill="#f59e0b" opacity=".7"/><rect x="810" y="88" width="60" height="5" rx="2" fill="#1e3a6e"/>
            <text class="tx s" x="810" y="110">Δ oil <tspan data-v="do" style="fill:#34d399">-1.8</tspan>% · gas <tspan data-v="dg" style="fill:#34d399">+2.2</tspan>%</text>
            <circle cx="916" cy="84" r="13" fill="#34d399"/><path class="chk" d="M909 84 l5 5 l9 -10" fill="none" stroke="#04111f" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        </div>
        <div class="center rv" style="margin-top:56px"><div class="kicker">Readings &amp; reports</div><h2 class="sh" style="font-size:clamp(22px,2.6vw,32px)">What each system measures - and what the report says</h2></div>
    <div class="rd">
        <article class="rcard m" style="--d:2">
            <header><div><h4>MPFM · M-01</h4><p>Multiphase flow meter — reads the full stream inline, no separation</p></div><span class="live-b"><i></i>LIVE</span></header>
            <div class="rrow">Oil rate<b class="k" data-v="mo">1,248</b><u>bbl/d</u></div>
            <div class="rrow">Gas rate<b class="k" data-v="mg">2.31</b><u>MMscf/d</u></div>
            <div class="rrow">Water rate<b class="k" data-v="mw">412</b><u>bbl/d</u></div>
            <div class="rrow">Water cut (WC)<b data-v="mwc">24.8</b><u>%</u></div>
            <div class="rrow">Gas-oil ratio (GOR)<b data-v="mgor">1,851</b><u>scf/bbl</u></div>
            <div class="rrow">Gas volume fraction<b data-v="mgvf">71.4</b><u>%</u></div>
            <div class="rrow">Line pressure<b data-v="mp">310</b><u>psi</u></div>
            <div class="rrow">Temperature<b data-v="mt">78</b><u>°C</u></div>
            <footer>How: Venturi differential pressure + gamma-ray fraction meter, calculated by the flow computer.</footer>
        </article>
        <article class="rcard s" style="--d:3">
            <header><div><h4>Test Separator · V-101</h4><p>Separates oil, gas and water — each phase measured on its own line</p></div><span class="live-b"><i></i>LIVE</span></header>
            <div class="rrow">Oil rate<b class="k" data-v="so">1,271</b><u>bbl/d</u></div>
            <div class="rrow">Gas rate<b class="k" data-v="sg">2.26</b><u>MMscf/d</u></div>
            <div class="rrow">Water rate<b class="k" data-v="sw">405</b><u>bbl/d</u></div>
            <div class="rrow">Water cut (BS&amp;W)<b data-v="swc">24.2</b><u>%</u></div>
            <div class="rrow">Gas-oil ratio (GOR)<b data-v="sgor">1,778</b><u>scf/bbl</u></div>
            <div class="rrow">Separator pressure<b data-v="sp">185</b><u>psi</u></div>
            <div class="rrow">Temperature<b data-v="st">74</b><u>°C</u></div>
            <div class="rrow">Oil / water interface<b>54</b><u>% level</u></div>
            <footer>How: Coriolis meter (oil), orifice plate (gas), water meter — after physical separation.</footer>
        </article>
        <article class="rcard r" style="--d:4">
            <header><div><h4>Test report · W-02</h4><p>MPFM compared with the separator for the same well</p></div><span class="live-b"><i></i>LIVE</span></header>
            <div class="dvr"><div><span>Oil deviation</span><b><span data-v="do">-1.8</span>%</b></div><div class="dvt"><i style="left:42.8%;width:7.2%"></i></div></div>
            <div class="dvr"><div><span>Gas deviation</span><b><span data-v="dg">+2.2</span>%</b></div><div class="dvt"><i style="left:50%;width:8.8%"></i></div></div>
            <div class="dvr"><div><span>Water deviation</span><b><span data-v="dw">+1.7</span>%</b></div><div class="dvt"><i style="left:50%;width:6.8%"></i></div></div>
            <div class="verdict" id="verdict">✓ Within ±5% tolerance — test validated</div>
            <div class="meta">
                <span>Test no. <b>T-0142</b></span><span>Duration <b>6 h</b></span>
                <span>Samples <b>360</b></span><span>Date <b id="rdate">02 Nov 2026</b></span>
                <span>Field oil today <b>4,215 BOPD</b></span><span>Wells tested <b>12 / 14</b></span>
            </div>
        </article>
    </div>
        <p class="sp center" style="font-size:12px;margin:14px auto 0;color:var(--mute)">Sample values shown for illustration.</p>
    </div>
</section>

{{-- ═════════ WIRE LINE OPERATION drawing ═════════ --}}
<section id="wl-op" class="alt">
    <div class="wrap">
        <div class="center rv"><div class="kicker">Wire Line operation</div><h2 class="sh">From the lubricator to the reservoir - and back as data</h2>
            <p class="sp">A slickline unit runs the memory gauge through the lubricator and wellhead, parks it opposite the reservoir, then pulls it out. The recording is uploaded and analysed automatically.</p></div>
        <div class="flow-box rv" style="margin-top:40px">
            @include('partials.wireline_rig')
        </div>
    </div>
</section>
{{-- ═════════ MPFM ═════════ --}}
<section id="mpfm">
    <div class="wrap mod">
        <div class="txt rv">
            <div class="num">01 · MPFM MONITORING</div>
            <h3>From raw flow-computer log to an approved well test</h3>
            <p>Operators upload the Vx log; the platform parses it, calculates flow, water cut and GOR, scores data quality and routes the test through review - all traceable to the person who touched it.</p>
            <ul class="ticks">
                <li>Automatic extraction of oil, gas, water, pressure and temperature</li>
                <li>Rule-based validation with data-quality scoring</li>
                <li>Draft → Submitted → Approved / Rejected review workflow</li>
                <li>Per-well history, trending dashboard and alerts</li>
            </ul>
            <div class="chips"><span class="chip">Vx logs</span><span class="chip">Validation rules</span><span class="chip">Trending</span></div>
        </div>
        <figure class="fig rv">
            <svg viewBox="0 0 560 300">
                <g font-size="11" font-weight="700" text-anchor="middle" fill="#fff">
                    <g transform="translate(14,30)"><rect width="92" height="62" rx="10" fill="#0a1226" stroke="#22d3ee"/><path d="M30 18h22l10 10v22H30z" fill="none" stroke="#22d3ee" stroke-width="1.8"/><text x="46" y="58" font-size="9.5" fill="#9aaccb">Vx log</text></g>
                    <g transform="translate(130,30)"><rect width="92" height="62" rx="10" fill="#0a1226" stroke="#3b82f6"/><text x="46" y="28" font-size="18" fill="#60a5fa">{ }</text><text x="46" y="52" font-size="9.5" fill="#9aaccb">Parse</text></g>
                    <g transform="translate(246,30)"><rect width="92" height="62" rx="10" fill="#0a1226" stroke="#fbbf24"/><path d="M34 32l8 8 16-18" fill="none" stroke="#fbbf24" stroke-width="2.4" stroke-linecap="round"/><text x="46" y="58" font-size="9.5" fill="#9aaccb">Validate</text></g>
                    <g transform="translate(362,30)"><rect width="92" height="62" rx="10" fill="#0a1226" stroke="#a78bfa"/><circle cx="46" cy="28" r="10" fill="none" stroke="#a78bfa" stroke-width="1.8"/><text x="46" y="58" font-size="9.5" fill="#9aaccb">Review</text></g>
                    <g transform="translate(470,30)"><rect width="78" height="62" rx="10" fill="#0d2a22" stroke="#34d399"/><text x="39" y="36" font-size="11" fill="#34d399">Approved</text></g>
                </g>
                <g stroke="#2e4170" stroke-width="1.6" fill="none" class="flowd"><path d="M106 61H130M222 61H246M338 61H362M454 61H470"/></g>
                <rect x="14" y="116" width="534" height="170" rx="12" fill="#0a1226" stroke="#1d2b4d"/>
                <text x="30" y="140" font-size="11" fill="#9aaccb" font-weight="700">Oil · gas · water per test</text>
                <g stroke="#1b2847"><path d="M30 170H530M30 210H530M30 250H530"/></g>
                <path class="draw" style="--len:560" d="M30 232 C90 224 130 200 180 204 S270 176 330 184 S430 150 530 144" fill="none" stroke="#22d3ee" stroke-width="2.4"/>
                <path class="draw" style="--len:560" d="M30 252 C100 250 160 246 220 240 S360 232 530 214" fill="none" stroke="#38bdf8" stroke-width="1.8" stroke-dasharray="5 4"/>
                <path class="draw" style="--len:560" d="M30 266 C120 262 200 258 280 256 S420 250 530 244" fill="none" stroke="#fbbf24" stroke-width="1.8"/>
                <g font-size="10" fill="#9aaccb"><circle cx="360" cy="136" r="4" fill="#22d3ee"/><text x="370" y="140">Oil</text><circle cx="404" cy="136" r="4" fill="#38bdf8"/><text x="414" y="140">Water cut</text><circle cx="478" cy="136" r="4" fill="#fbbf24"/><text x="488" y="140">Gas</text></g>
            </svg>
            <figcaption>Pipeline: upload → parse → validate → review → approved, feeding well trends.</figcaption>
        </figure>
    </div>
</section>

{{-- ═════════ TEST VESSEL ═════════ --}}
<section id="vessel" class="alt">
    <div class="wrap mod rev">
        <div class="txt rv">
            <div class="num">02 · TEST VESSEL</div>
            <h3>Cross-check every meter against the separator</h3>
            <p>Plan vessel tests with requests, record results and compare them with MPFM readings for the same well. Deviations beyond tolerance are surfaced automatically so meter issues are caught early.</p>
            <ul class="ticks">
                <li>Fleet register with status (active, maintenance, inactive)</li>
                <li>Test requests with multi-well scheduling and tracking</li>
                <li>Side-by-side MPFM vs vessel comparison with ±10% band</li>
                <li>Approval workflow and shift / operator reporting</li>
            </ul>
            <div class="chips"><span class="chip">Requests</span><span class="chip">Comparison</span><span class="chip">Fleet status</span></div>
        </div>
        <figure class="fig rv">
            <svg viewBox="0 0 560 320">
                <g transform="translate(0,6)">
                    <rect x="22" y="40" width="150" height="70" rx="34" fill="#16233f" stroke="#7fb4ff" stroke-width="2"/>
                    <clipPath id="vc"><rect x="22" y="40" width="150" height="70" rx="34"/></clipPath>
                    <g clip-path="url(#vc)"><rect x="22" y="72" width="150" height="20" fill="#b45309"/><rect x="22" y="90" width="150" height="22" fill="#0369a1"/><rect x="22" y="40" width="150" height="32" fill="#1e3a5f" opacity=".5"/></g>
                    <path d="M172 75H215" stroke="#fbbf24" stroke-width="3" class="flowd"/><path d="M50 40V18M150 40V18" stroke="#7fb4ff" stroke-width="3"/>
                    <text x="97" y="136" text-anchor="middle" font-size="10" fill="#9aaccb" font-weight="700">TEST SEPARATOR</text>
                </g>
                <rect x="240" y="20" width="300" height="270" rx="12" fill="#0a1226" stroke="#1d2b4d"/>
                <text x="256" y="44" font-size="11" fill="#9aaccb" font-weight="700">MPFM vs vessel · oil rate</text>
                <path d="M280 262L520 52" stroke="#34d399" stroke-width="1.6" stroke-dasharray="6 4"/>
                <path d="M280 262L520 28" stroke="#f87171" stroke-opacity=".5"/><path d="M280 262L520 78" stroke="#f87171" stroke-opacity=".5"/>
                <path d="M280 262H524M280 262V50" stroke="#2e4170"/>
                <g fill="#22d3ee"><circle cx="318" cy="228" r="5"/><circle cx="352" cy="205" r="5"/><circle cx="388" cy="170" r="5"/><circle cx="420" cy="150" r="5"/><circle cx="452" cy="116" r="5"/><circle cx="486" cy="88" r="5"/></g>
                <circle cx="404" cy="172" r="6" fill="#f87171" class="pulse"/>
                <g font-size="9.5" fill="#9aaccb"><text x="450" y="274">Vessel B/D</text><text x="250" y="150" transform="rotate(-90 250 150)">MPFM B/D</text><text x="470" y="46" fill="#f87171">±10%</text></g>
            </svg>
            <figcaption>Points on the dashed line mean the meters agree; outliers outside the red band are flagged.</figcaption>
        </figure>
    </div>
</section>

{{-- ═════════ WIRE LINE ═════════ --}}
<section id="wireline">
    <div class="wrap mod">
        <div class="txt rv">
            <div class="num">03 · WIRE LINE</div>
            <h3>Memory-gauge surveys rendered into reservoir answers</h3>
            <p>Upload the gauge export and the platform detects the run phases, extracts final bottom-hole pressure and temperature, finds pressure events and gauge stops, computes fluid gradients and checks data quality.</p>
            <ul class="ticks">
                <li>Kuster / Quantum <b>.log</b> and <b>.csv</b> recordings supported</li>
                <li>Run-in, on-bottom, pull-out phases and stabilisation check</li>
                <li>Build-up detection, gradient survey and fluid typing</li>
                <li>Operation history per well: surveys, fishing, plugs, perforating</li>
            </ul>
            <div class="chips"><span class="chip">BHCIP</span><span class="chip">Gradient survey</span><span class="chip">Build-up</span><span class="chip">Interventions</span></div>
        </div>
        <figure class="fig rv">
            <svg viewBox="0 0 560 330">
                <rect x="14" y="14" width="532" height="262" rx="10" fill="#0a1226" stroke="#1d2b4d"/>
                <g opacity=".9">
                    <rect x="90" y="24" width="110" height="238" fill="#3b82f6" opacity=".10"/><rect x="200" y="24" width="190" height="238" fill="#34d399" opacity=".10"/><rect x="390" y="24" width="100" height="238" fill="#fbbf24" opacity=".10"/>
                </g>
                <g font-size="9.5" font-weight="700" text-anchor="middle"><text x="145" y="40" fill="#60a5fa">RUN IN HOLE</text><text x="295" y="40" fill="#34d399">ON BOTTOM</text><text x="440" y="40" fill="#fbbf24">PULL OUT</text></g>
                <g stroke="#1b2847"><path d="M30 80H530M30 130H530M30 180H530M30 230H530"/></g>
                <path class="draw" style="--len:900" d="M30 238 L90 238 C110 238 120 110 200 96 L232 92 L244 70 L252 134 L266 90 L300 104 C340 98 372 92 392 98 C430 108 442 200 490 238 L530 238" fill="none" stroke="#22d3ee" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"/>
                <path class="draw" style="--len:900" d="M30 240 L90 240 C120 240 130 150 200 130 C260 118 360 118 392 124 C440 130 450 200 490 240 L530 240" fill="none" stroke="#f87171" stroke-width="1.8" stroke-dasharray="5 4"/>
                <g fill="#fff" stroke="#0a1226" stroke-width="2"><circle cx="418" cy="144" r="4.5"/><circle cx="430" cy="166" r="4.5"/><circle cx="448" cy="196" r="4.5"/></g>
                <g><line x1="330" y1="104" x2="330" y2="62" stroke="#a78bfa" stroke-dasharray="3 3"/><rect x="288" y="48" width="84" height="22" rx="6" fill="#1e1b3a" stroke="#a78bfa"/><text x="330" y="63" text-anchor="middle" font-size="10" fill="#c4b5fd" font-weight="700">BHP 2,497 psi</text></g>
                <g font-size="9.5" fill="#9aaccb"><circle cx="40" cy="298" r="4" fill="#22d3ee"/><text x="50" y="302">Pressure</text><circle cx="116" cy="298" r="4" fill="#f87171"/><text x="126" y="302">Temperature</text><circle cx="212" cy="298" r="4" fill="#fff"/><text x="222" y="302">Gauge stops (gradient points)</text></g>
                <g font-size="9.5" fill="#64748b"><text x="30" y="294" opacity="0">.</text><text x="478" y="302">time →</text></g>
            </svg>
            <figcaption>A real BHCIP recording, interpreted: phases shaded, BHP / BHT extracted, stops marked for gradients.</figcaption>
        </figure>
    </div>
</section>

{{-- ═════════ WELL ANALYSIS ═════════ --}}
<section id="analysis" class="alt">
    <div class="wrap mod rev">
        <div class="txt rv">
            <div class="num">04 · WELL ANALYSIS</div>
            <h3>Production and reservoir condition, with clear next steps</h3>
            <p>For every well the platform combines MPFM, vessel and Wire Line data into a health score, decline forecast, reservoir depletion view and a ranked list of recommendations.</p>
            <ul class="ticks">
                <li>Health score with transparent contributing factors</li>
                <li>Oil decline fit and 90 / 180-day forecast</li>
                <li>Reservoir pressure depletion and projected pressure</li>
                <li>Prioritised actions: re-test, survey, water control, calibration</li>
            </ul>
            <div class="chips"><span class="chip">Health score</span><span class="chip">Decline</span><span class="chip">Recommendations</span></div>
        </div>
        <figure class="fig rv">
            <svg viewBox="0 0 560 330">
                <g transform="translate(18,18)">
                    <circle cx="70" cy="70" r="52" fill="none" stroke="rgba(255,255,255,.08)" stroke-width="12"/>
                    <circle cx="70" cy="70" r="52" fill="none" stroke="#34d399" stroke-width="12" stroke-linecap="round" stroke-dasharray="327" stroke-dashoffset="59" transform="rotate(-90 70 70)"/>
                    <text x="70" y="78" text-anchor="middle" font-size="30" font-weight="800" fill="#fff" font-family="Space Grotesk">82</text><text x="70" y="96" text-anchor="middle" font-size="9" fill="#64748b" font-weight="700">HEALTH</text>
                </g>
                <rect x="170" y="18" width="372" height="148" rx="10" fill="#0a1226" stroke="#1d2b4d"/>
                <text x="184" y="38" font-size="10.5" fill="#9aaccb" font-weight="700">Oil rate · decline &amp; forecast</text>
                <path class="draw" style="--len:300" d="M186 62 C220 70 250 84 290 96 S350 118 372 124" fill="none" stroke="#22d3ee" stroke-width="2.4"/>
                <path d="M372 124 C410 134 450 146 524 156" fill="none" stroke="#fbbf24" stroke-width="2.2" stroke-dasharray="6 5"/>
                <line x1="372" y1="50" x2="372" y2="156" stroke="#2e4170" stroke-dasharray="3 3"/><text x="380" y="62" font-size="9" fill="#64748b">forecast →</text>
                <g transform="translate(18,186)" font-size="12">
                    <g><rect width="524" height="40" rx="10" fill="#1b1015" stroke="#f87171" stroke-opacity=".5"/><rect width="4" height="40" rx="2" fill="#f87171"/><text x="18" y="25" fill="#fff" font-weight="700">Investigate oil-rate decline</text><text x="506" y="25" text-anchor="end" fill="#f87171" font-size="10" font-weight="800">HIGH</text></g>
                    <g transform="translate(0,48)"><rect width="524" height="40" rx="10" fill="#1b1a10" stroke="#fbbf24" stroke-opacity=".5"/><rect width="4" height="40" rx="2" fill="#fbbf24"/><text x="18" y="25" fill="#fff" font-weight="700">Pressure survey overdue</text><text x="506" y="25" text-anchor="end" fill="#fbbf24" font-size="10" font-weight="800">MEDIUM</text></g>
                    <g transform="translate(0,96)"><rect width="524" height="40" rx="10" fill="#0e1b2a" stroke="#60a5fa" stroke-opacity=".5"/><rect width="4" height="40" rx="2" fill="#60a5fa"/><text x="18" y="25" fill="#fff" font-weight="700">Gauge calibration older than 12 months</text><text x="506" y="25" text-anchor="end" fill="#60a5fa" font-size="10" font-weight="800">LOW</text></g>
                </g>
            </svg>
            <figcaption>Illustrative well analysis: score, forecast and ranked recommendations.</figcaption>
        </figure>
    </div>
</section>

{{-- ═════════ FIELD SETUP ═════════ --}}
<section id="field">
    <div class="wrap mod">
        <div class="txt rv">
            <div class="num">05 · FIELD SETUP</div>
            <h3>A clean asset hierarchy behind every record</h3>
            <p>Fields, towers and wells (oil strings) are defined once. Every test, survey and task rolls up through the same structure, so field, tower and well views always agree.</p>
            <ul class="ticks">
                <li>Field → Tower → Well hierarchy with status control</li>
                <li>Well profile pages with production statistics and charts</li>
                <li>Cross-module well history: tests, Wire Line, department work</li>
            </ul>
        </div>
        <figure class="fig rv">
            <svg viewBox="0 0 560 300">
                <g fill="none" stroke="#2e4170" stroke-width="1.6"><path d="M280 62V84M280 84H110V108M280 84H280V108M280 84H450V108M110 160V180M110 180H60V200M110 180H160V200M450 160V180M450 180H400V200M450 180H500V200"/></g>
                <g font-size="11" font-weight="700" text-anchor="middle" fill="#fff">
                    <rect x="200" y="14" width="160" height="48" rx="10" fill="#0d1730" stroke="#38bdf8"/><text x="280" y="43">Oil Field</text>
                    <g><rect x="40" y="108" width="140" height="52" rx="10" fill="#0d1730" stroke="#22d3ee"/><text x="110" y="139">Tower A</text></g>
                    <g><rect x="210" y="108" width="140" height="52" rx="10" fill="#0d1730" stroke="#22d3ee"/><text x="280" y="139">Tower B</text></g>
                    <g><rect x="380" y="108" width="140" height="52" rx="10" fill="#0d1730" stroke="#22d3ee"/><text x="450" y="139">Tower C</text></g>
                    <g font-size="10"><rect x="14" y="200" width="92" height="40" rx="9" fill="#0a1226" stroke="#34d399"/><text x="60" y="225">Well 1</text>
                    <rect x="114" y="200" width="92" height="40" rx="9" fill="#0a1226" stroke="#34d399"/><text x="160" y="225">Well 2</text>
                    <rect x="354" y="200" width="92" height="40" rx="9" fill="#0a1226" stroke="#fbbf24"/><text x="400" y="225">Well 3</text>
                    <rect x="454" y="200" width="92" height="40" rx="9" fill="#0a1226" stroke="#34d399"/><text x="500" y="225">Well 4</text></g>
                </g>
                <g font-size="9.5" fill="#9aaccb"><circle cx="190" cy="278" r="4" fill="#34d399"/><text x="200" y="282">Active</text><circle cx="260" cy="278" r="4" fill="#fbbf24"/><text x="270" y="282">Maintenance</text><circle cx="360" cy="278" r="4" fill="#64748b"/><text x="370" y="282">Inactive</text></g>
            </svg>
            <figcaption>Every module attaches its data to a well in this hierarchy.</figcaption>
        </figure>
    </div>
</section>

{{-- ═════════ GOVERNANCE ═════════ --}}
<section id="governance" class="alt">
    <div class="wrap">
        <div class="center rv"><div class="kicker">06 · Governance &amp; security</div><h2 class="sh">Controlled, reviewable, accountable</h2>
            <p class="sp">Role-based access, structured approval workflows and a recorded history keep data trustworthy and auditable.</p></div>
        <figure class="fig rv" style="margin-top:40px">
            <svg viewBox="0 0 1000 200">
                <g font-size="13" font-weight="700" text-anchor="middle" fill="#fff">
                    <rect x="20" y="62" width="150" height="62" rx="12" fill="#0d1730" stroke="#64748b"/><text x="95" y="98">Draft</text>
                    <rect x="260" y="62" width="150" height="62" rx="12" fill="#0d1730" stroke="#fbbf24"/><text x="335" y="98">Submitted</text>
                    <rect x="520" y="14" width="150" height="62" rx="12" fill="#0d2a22" stroke="#34d399"/><text x="595" y="50">Approved</text>
                    <rect x="520" y="112" width="150" height="62" rx="12" fill="#2a1216" stroke="#f87171"/><text x="595" y="148">Rejected</text>
                    <rect x="780" y="112" width="190" height="62" rx="12" fill="#0d1730" stroke="#a78bfa"/><text x="875" y="148">Re-test requested</text>
                </g>
                <g fill="none" stroke="#2e4170" stroke-width="2" class="flowd"><path d="M170 93H260M410 93L520 45M410 93L520 143M670 143H780"/><path d="M875 112V93H95" stroke-opacity=".5" stroke-dasharray="2 6"/></g>
                <g font-size="10" fill="#9aaccb" text-anchor="middle"><text x="215" y="84">submit</text><text x="465" y="56">review ✓</text><text x="465" y="140">review ✗</text></g>
            </svg>
            <figcaption>Review workflow applied to MPFM and test-vessel results.</figcaption>
        </figure>
        <div class="cards">
            <div class="c rv" style="--cc:#f87171"><div class="ic"><svg viewBox="0 0 24 24"><rect x="5" y="11" width="14" height="9" rx="2"/><path d="M8 11V8a4 4 0 0 1 8 0v3"/></svg></div><h4>Role-based access</h4><p>Eight role profiles - administrators, managers, job officers, supervisors, engineers and operators - each see only what they should.</p></div>
            <div class="c rv" style="--cc:#34d399"><div class="ic"><svg viewBox="0 0 24 24"><path d="M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6z"/><path d="M9 12l2 2 4-4"/></svg></div><h4>Validated data</h4><p>Configurable validation rules and quality scores stop questionable results before they reach reports.</p></div>
            <div class="c rv" style="--cc:#38bdf8"><div class="ic"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v5h4"/></svg></div><h4>Full history</h4><p>Who recorded, reviewed and changed each test, survey and operation - with timestamps - is always visible.</p></div>
        </div>
    </div>
</section>

{{-- ═════════ ROADMAP ═════════ --}}
<section id="roadmap">
    <div class="wrap">
        <div class="center rv"><div class="kicker">Modules</div><h2 class="sh">Built for the whole operation</h2>
            <p class="sp">Surveillance and test modules are live today; the department workflow suite is next.</p></div>
        <div class="cards">
            <div class="c rv" style="--cc:#22d3ee"><div class="ic"><svg viewBox="0 0 24 24"><path d="M3 12h4l3-8 4 16 3-8h4"/></svg></div><h4>MPFM monitoring</h4><p>Log ingestion, validation, review and trends.</p><span class="live">Live</span></div>
            <div class="c rv" style="--cc:#fbbf24"><div class="ic"><svg viewBox="0 0 24 24"><rect x="3" y="8" width="18" height="8" rx="4"/><path d="M7 8V5M17 8V5"/></svg></div><h4>Test vessel</h4><p>Requests, vessel tests and meter comparison.</p><span class="live">Live</span></div>
            <div class="c rv" style="--cc:#a78bfa"><div class="ic"><svg viewBox="0 0 24 24"><path d="M12 3v18M8 7l4-4 4 4"/><circle cx="12" cy="15" r="3"/></svg></div><h4>Wire Line</h4><p>Memory-gauge surveys and intervention history.</p><span class="live">Live</span></div>
            <div class="c rv" style="--cc:#34d399"><div class="ic"><svg viewBox="0 0 24 24"><path d="M4 20V9M10 20V4M16 20v-8M22 20H2"/></svg></div><h4>Well analysis</h4><p>Health, forecast, reservoir and recommendations.</p><span class="live">Live</span></div>
            <div class="c rv" style="--cc:#60a5fa"><div class="ic"><svg viewBox="0 0 24 24"><rect x="4" y="4" width="16" height="16" rx="2"/><path d="M8 9h8M8 13h8M8 17h5"/></svg></div><h4>Tasks &amp; monthly plans</h4><p>Department task management with SLA tracking.</p><span class="soon">Coming soon</span></div>
            <div class="c rv" style="--cc:#fb923c"><div class="ic"><svg viewBox="0 0 24 24"><path d="M14.7 6.3a4 4 0 0 0-5.4 5.4L3 18l3 3 6.3-6.3a4 4 0 0 0 5.4-5.4l-2.5 2.5-2.5-.5-.5-2.5z"/></svg></div><h4>Drilling · Completion · Workover · Maintenance</h4><p>Department workflows on the same well record.</p><span class="soon">Coming soon</span></div>
        </div>
    </div>
</section>

<section class="cta-s">
    <div class="wrap rv">
        <span class="lg" role="img" aria-label="Company logo"></span>
        <div class="kicker">Get started</div>
        <h2 class="sh">See every well, every test, every survey - in one place</h2>
        <p class="sp" style="margin:0 auto">Sign in to open your operations dashboard.</p>
        <div class="cta">@auth<a href="{{ url('/dashboard') }}" class="btn p">Open dashboard</a>@else<a href="{{ route('login') }}" class="btn p">Sign in to the system</a>@endauth</div>
    </div>
</section>
</main>

<footer><div class="wrap"><span>© {{ date('Y') }} Oil Field Management System</span><span>Well testing · Wire Line · Reservoir insight</span></div></footer>

<script>
(function () {
    var els = document.querySelectorAll('.rv');
    if (!('IntersectionObserver' in window)) { els.forEach(function (e) { e.classList.add('in'); }); return; }
    var io = new IntersectionObserver(function (es) { es.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } }); }, { threshold: .12 });
    els.forEach(function (e) { io.observe(e); });
})();
</script>
</body>
</html>
