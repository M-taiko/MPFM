<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Oil Field Management System</title>
    <link rel="icon" href="/assets/img/brand/favicon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
/* ══════════════════ RESET & VARIABLES ══════════════════ */
*, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }

:root {
    --bg:       #050a15;
    --bg2:      #080f20;
    --bg3:      #0d1730;
    --b900:     #0a1628;
    --b800:     #0f2044;
    --b700:     #1a3461;
    --b600:     #1e4080;
    --b500:     #2563EB;
    --b400:     #3b82f6;
    --b300:     #60a5fa;
    --b200:     #93c5fd;
    --b100:     #dbeafe;
    --cyan:     #06b6d4;
    --cyan-lt:  #67e8f9;
    --amber:    #f59e0b;
    --red:      #ef4444;
    --green:    #22c55e;
    --border:   rgba(37,99,235,.18);
    --card:     rgba(255,255,255,.03);
    --glass:    rgba(255,255,255,.04);
    --muted:    #64748b;
    --sub:      #94a3b8;
    --glow:     0 0 30px rgba(37,99,235,.35);
    --glow-c:   0 0 24px rgba(6,182,212,.4);
}

/* Light mode overrides */
[data-theme="light"] {
    --bg:    #f0f4ff;
    --bg2:   #e8eeff;
    --bg3:   #dde5ff;
    --glass: rgba(255,255,255,.7);
    --card:  rgba(255,255,255,.6);
    --border:rgba(37,99,235,.15);
    --sub:   #475569;
    --muted: #64748b;
}
[data-theme="light"] body { color:#0f172a; }
[data-theme="light"] .grid-bg {
    background-image:
        linear-gradient(rgba(37,99,235,.06) 1px, transparent 1px),
        linear-gradient(90deg, rgba(37,99,235,.06) 1px, transparent 1px);
}

html { scroll-behavior:smooth; }
body {
    font-family:'Inter',sans-serif;
    background:var(--bg);
    color:#e2e8f0;
    line-height:1.65;
    overflow-x:hidden;
    transition:background .3s, color .3s;
}

::-webkit-scrollbar { width:4px; }
::-webkit-scrollbar-track { background:var(--bg); }
::-webkit-scrollbar-thumb { background:var(--b500); border-radius:4px; }

img { display:block; max-width:100%; }
a  { text-decoration:none; }

/* ══ ANIMATED GRID BACKGROUND ══ */
.grid-bg {
    position:fixed; inset:0; z-index:0; pointer-events:none;
    background-image:
        linear-gradient(rgba(37,99,235,.04) 1px, transparent 1px),
        linear-gradient(90deg, rgba(37,99,235,.04) 1px, transparent 1px);
    background-size:60px 60px;
    animation:gridDrift 40s linear infinite;
}
@keyframes gridDrift {
    0%   { background-position:0 0, 0 0; }
    100% { background-position:60px 60px, 60px 60px; }
}
.blob {
    position:fixed; border-radius:50%; filter:blur(80px); pointer-events:none; z-index:0;
    animation:blobFloat 12s ease-in-out infinite alternate;
}
.blob-1 { width:500px; height:500px; background:rgba(37,99,235,.09); top:-100px; left:-100px; }
.blob-2 { width:400px; height:400px; background:rgba(6,182,212,.07); bottom:-80px; right:-80px; animation-delay:-6s; }
@keyframes blobFloat {
    0%   { transform:translate(0,0) scale(1); }
    100% { transform:translate(30px,30px) scale(1.08); }
}

/* ══ NAV ══ */
nav {
    position:fixed; top:0; left:0; right:0; z-index:300;
    height:68px;
    display:flex; align-items:center; justify-content:space-between;
    padding:0 5%;
    background:rgba(5,10,21,.92);
    backdrop-filter:blur(22px);
    border-bottom:1px solid var(--border);
}
[data-theme="light"] nav { background:rgba(240,244,255,.92); }
.nav-logo img { height:52px; width:auto; object-fit:contain; }
.nav-links { display:flex; gap:28px; list-style:none; }
.nav-links a { color:#94a3b8; font-size:.85rem; font-weight:500; transition:color .2s; }
.nav-links a:hover { color:#fff; }
[data-theme="light"] .nav-links a { color:#475569; }
[data-theme="light"] .nav-links a:hover { color:#0f172a; }
.nav-right { display:flex; align-items:center; gap:14px; }
.nav-cta {
    display:inline-flex; align-items:center; gap:6px;
    background:var(--b500); color:#fff;
    padding:9px 22px; border-radius:8px;
    font-size:.85rem; font-weight:700;
    transition:all .2s;
    box-shadow:0 4px 20px rgba(37,99,235,.4);
}
.nav-cta:hover { background:var(--b400); transform:translateY(-1px); box-shadow:var(--glow); }
.dm-toggle {
    width:42px; height:24px; border-radius:12px;
    background:var(--bg3); border:1px solid var(--border);
    cursor:pointer; position:relative; transition:background .3s;
    flex-shrink:0;
}
.dm-toggle::after {
    content:''; position:absolute;
    width:16px; height:16px; border-radius:50%;
    background:var(--b400); top:3px; left:3px;
    transition:transform .3s, background .3s;
    box-shadow:0 0 8px rgba(59,130,246,.6);
}
[data-theme="light"] .dm-toggle::after { transform:translateX(18px); background:var(--amber); }
.nav-burger {
    display:none; flex-direction:column; gap:5px;
    background:none; border:none; cursor:pointer; padding:4px;
}
.nav-burger span { display:block; width:24px; height:2px; background:#e2e8f0; border-radius:2px; transition:all .3s; }
.nav-drawer {
    display:none; position:fixed; top:68px; left:0; right:0; z-index:299;
    background:rgba(5,10,21,.97);
    border-bottom:1px solid var(--border);
    padding:20px 5%;
}
.nav-drawer.open { display:block; }
.nav-drawer ul { list-style:none; display:flex; flex-direction:column; gap:16px; }
.nav-drawer a { color:#94a3b8; font-size:.9rem; font-weight:500; }

/* ══ HERO ══ */
.hero {
    position:relative; z-index:1;
    min-height:100vh;
    display:flex; align-items:center; justify-content:center;
    text-align:center;
    padding:100px 5% 80px;
    overflow:hidden;
}
.hero-bg {
    position:absolute; inset:0;
    background:url('/assets/img/brand/oilfield oprations.webp') center/cover no-repeat;
    opacity:.12;
}
.hero-overlay {
    position:absolute; inset:0;
    background:linear-gradient(180deg, rgba(5,10,21,.7) 0%, rgba(5,10,21,.4) 50%, rgba(5,10,21,.9) 100%);
}
.hero-content { position:relative; z-index:2; max-width:860px; margin:0 auto; }
.hero-logo {
    height:120px; width:auto; margin:0 auto 32px;
    filter:drop-shadow(0 0 20px rgba(37,99,235,.5));
}
.hero-badge {
    display:inline-flex; align-items:center; gap:8px;
    background:rgba(37,99,235,.15); border:1px solid rgba(37,99,235,.3);
    color:var(--b300); padding:6px 18px; border-radius:20px;
    font-size:.75rem; font-weight:600; letter-spacing:.08em; text-transform:uppercase;
    margin-bottom:24px;
}
.hero-badge::before {
    content:''; width:7px; height:7px; border-radius:50%;
    background:var(--cyan); box-shadow:0 0 8px var(--cyan);
    animation:pulse 1.5s ease-in-out infinite;
}
@keyframes pulse {
    0%,100% { opacity:1; transform:scale(1); }
    50%      { opacity:.5; transform:scale(.8); }
}
.hero h1 {
    font-size:clamp(2rem,5.5vw,4rem);
    font-weight:900; line-height:1.12;
    letter-spacing:-.02em; margin-bottom:20px;
    background:linear-gradient(135deg,#fff 30%,var(--b300) 70%,var(--cyan-lt) 100%);
    -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text;
}
.hero-sub {
    font-size:clamp(1rem,2vw,1.2rem);
    color:var(--sub); max-width:640px; margin:0 auto 40px;
}
.hero-btns { display:flex; gap:16px; justify-content:center; flex-wrap:wrap; }
.btn-primary {
    display:inline-flex; align-items:center; gap:8px;
    background:var(--b500); color:#fff;
    padding:14px 34px; border-radius:10px;
    font-size:1rem; font-weight:700;
    box-shadow:0 6px 30px rgba(37,99,235,.5);
    transition:all .2s;
}
.btn-primary:hover { background:var(--b400); transform:translateY(-2px); box-shadow:var(--glow); }
.btn-secondary {
    display:inline-flex; align-items:center; gap:8px;
    background:transparent; color:#fff;
    padding:13px 34px; border-radius:10px;
    border:1.5px solid rgba(255,255,255,.2);
    font-size:1rem; font-weight:600;
    backdrop-filter:blur(8px); transition:all .2s;
}
.btn-secondary:hover { border-color:var(--b400); color:var(--b300); transform:translateY(-2px); }
.hero-stats {
    display:flex; gap:32px; justify-content:center; flex-wrap:wrap;
    margin-top:56px; padding-top:40px;
    border-top:1px solid rgba(255,255,255,.07);
}
.hstat { text-align:center; }
.hstat-val { font-size:1.8rem; font-weight:800; color:#fff; line-height:1; }
.hstat-val span { color:var(--cyan); }
.hstat-lbl { font-size:.73rem; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin-top:4px; }

/* ══ PHOTO STRIP ══ */
.photo-strip {
    position:relative; z-index:1;
    display:grid; grid-template-columns:repeat(4,1fr); height:220px; overflow:hidden;
}
.photo-strip img {
    width:100%; height:100%; object-fit:cover;
    filter:grayscale(60%); transition:filter .5s, transform .5s;
}
.photo-strip img:hover { filter:grayscale(0%); transform:scale(1.04); }

/* ══ KPI CARDS ══ */
.kpi-section {
    position:relative; z-index:1;
    padding:80px 5%;
}
.section-label {
    display:inline-flex; align-items:center; gap:8px;
    color:var(--cyan); font-size:.72rem; font-weight:700;
    letter-spacing:.12em; text-transform:uppercase; margin-bottom:14px;
}
.section-label::before { content:''; width:24px; height:2px; background:var(--cyan); }
.section-title { font-size:clamp(1.6rem,3.5vw,2.4rem); font-weight:800; margin-bottom:10px; line-height:1.2; }
.section-sub { color:var(--sub); font-size:.95rem; max-width:520px; margin-bottom:48px; }
.kpi-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:20px; }
.kpi-card {
    background:var(--glass);
    border:1px solid var(--border);
    border-radius:16px; padding:28px 24px;
    backdrop-filter:blur(12px);
    position:relative; overflow:hidden;
    transition:transform .3s, box-shadow .3s, border-color .3s;
}
.kpi-card::before {
    content:''; position:absolute; top:0; left:0; right:0; height:3px;
    background:linear-gradient(90deg,var(--b500),var(--cyan)); opacity:.7;
}
.kpi-card:hover {
    transform:translateY(-6px);
    box-shadow:0 20px 50px rgba(37,99,235,.2), var(--glow);
    border-color:rgba(37,99,235,.35);
}
.kpi-icon {
    width:48px; height:48px; border-radius:12px;
    display:flex; align-items:center; justify-content:center;
    font-size:1.4rem; margin-bottom:16px;
    background:rgba(37,99,235,.12); border:1px solid rgba(37,99,235,.2);
}
.kpi-icon.cyan  { background:rgba(6,182,212,.12); border-color:rgba(6,182,212,.2); }
.kpi-icon.amber { background:rgba(245,158,11,.12); border-color:rgba(245,158,11,.2); }
.kpi-icon.green { background:rgba(34,197,94,.12);  border-color:rgba(34,197,94,.2); }
.kpi-icon.red   { background:rgba(239,68,68,.12);  border-color:rgba(239,68,68,.2); }
.kpi-val { font-size:2.2rem; font-weight:800; line-height:1; margin-bottom:6px; }
.kpi-val.blue  { color:var(--b400); }
.kpi-val.cyan  { color:var(--cyan); }
.kpi-val.amber { color:var(--amber); }
.kpi-val.green { color:var(--green); }
.kpi-val.red   { color:var(--red); }
.kpi-lbl { font-size:.82rem; color:var(--sub); font-weight:500; }
.kpi-trend {
    position:absolute; top:20px; right:20px;
    font-size:.72rem; font-weight:700; padding:3px 8px; border-radius:6px;
}
.trend-up   { background:rgba(34,197,94,.12); color:var(--green); }
.trend-down { background:rgba(239,68,68,.12);  color:var(--red); }
.trend-ok   { background:rgba(6,182,212,.12);  color:var(--cyan); }

/* ══ INTERACTIVE PRODUCTION DIAGRAM ══ */
.diagram-section {
    position:relative; z-index:1;
    padding:90px 5%;
    background:linear-gradient(180deg, var(--bg2) 0%, var(--bg3) 100%);
}
.diagram-wrap {
    max-width:100%;
    margin:0 auto;
    background:var(--glass);
    border:1px solid var(--border);
    border-radius:24px;
    padding:10px;
    backdrop-filter:blur(16px);
    box-shadow:0 30px 80px rgba(0,0,0,.3), inset 0 1px 0 rgba(255,255,255,.05);
    position:relative; overflow:hidden;
}
.diagram-wrap::before {
    content:''; position:absolute; top:-1px; left:10%; right:10%; height:2px;
    background:linear-gradient(90deg,transparent,var(--b500),var(--cyan),transparent);
}
.diagram-title {
    text-align:center; font-size:1rem; font-weight:700;
    color:var(--b300); letter-spacing:.06em; text-transform:uppercase;
    margin-bottom:32px; display:flex; align-items:center; justify-content:center; gap:10px;
}
.diagram-title::before,.diagram-title::after {
    content:''; flex:1; height:1px; background:var(--border);
}

/* SVG diagram */
.prod-svg { width:100%; height:auto; display:block; }

/* Tooltip */
.diag-tooltip {
    position:absolute; z-index:50;
    background:rgba(5,10,21,.95);
    border:1px solid var(--b500);
    border-radius:10px; padding:12px 16px;
    font-size:.78rem; line-height:1.6;
    pointer-events:none; opacity:0; transform:translateY(6px);
    transition:opacity .2s, transform .2s;
    min-width:160px;
    box-shadow:var(--glow);
}
.diag-tooltip.show { opacity:1; transform:translateY(0); }
.tt-title { font-weight:700; color:var(--b300); margin-bottom:6px; font-size:.85rem; }
.tt-row { display:flex; justify-content:space-between; gap:16px; color:var(--sub); }
.tt-row span:last-child { color:#e2e8f0; font-weight:600; }

/* Node groups */
.diag-node {
    cursor:pointer;
    transition:filter .2s;
}
.diag-node:hover rect, .diag-node:hover circle {
    filter:drop-shadow(0 0 8px rgba(37,99,235,.8));
}
.diag-node:hover { filter:brightness(1.2); }

/* Flow animation along paths */
.flow-dot {
    animation:flowAlong 3s linear infinite;
}
@keyframes flowAlong {
    0%   { offset-distance:0%; }
    100% { offset-distance:100%; }
}

/* ══ DEPARTMENTS ══ */
.dept-section {
    position:relative; z-index:1;
    padding:90px 5%;
}
.dept-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(300px,1fr)); gap:24px; }
.dept-card {
    border-radius:20px; overflow:hidden;
    border:1px solid var(--border);
    background:var(--glass);
    backdrop-filter:blur(12px);
    transition:transform .3s, box-shadow .3s;
    position:relative;
}
.dept-card:hover {
    transform:translateY(-8px);
    box-shadow:0 24px 60px rgba(37,99,235,.18), var(--glow);
}
.dept-img {
    height:160px; overflow:hidden; position:relative;
}
.dept-img img { width:100%; height:100%; object-fit:cover; transition:transform .5s; }
.dept-card:hover .dept-img img { transform:scale(1.08); }
.dept-img::after {
    content:''; position:absolute; inset:0;
    background:linear-gradient(180deg,transparent 40%, rgba(5,10,21,.85) 100%);
}
.dept-tag {
    position:absolute; bottom:12px; left:16px; z-index:2;
    background:var(--b500); color:#fff;
    font-size:.65rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase;
    padding:3px 10px; border-radius:6px;
    box-shadow:0 2px 12px rgba(37,99,235,.5);
}
.dept-body { padding:22px; }
.dept-name { font-size:1.05rem; font-weight:700; margin-bottom:8px; }
.dept-desc { font-size:.83rem; color:var(--sub); line-height:1.65; margin-bottom:16px; }
.dept-kpis { display:flex; gap:12px; flex-wrap:wrap; }
.dept-kpi {
    font-size:.72rem; font-weight:600;
    padding:4px 10px; border-radius:6px;
    background:rgba(37,99,235,.1); border:1px solid rgba(37,99,235,.2);
    color:var(--b300);
}

/* ══ WELL ARCHITECTURE DIAGRAM ══ */
.arch-section {
    position:relative; z-index:1;
    padding:90px 5%;
    background:linear-gradient(180deg,var(--bg2) 0%,var(--bg) 100%);
}
.arch-wrap { max-width:1100px; margin:0 auto; }

/* Layer styling */
.arch-layer { margin-bottom:0; }
.arch-layer-label {
    font-size:.68rem; font-weight:700; letter-spacing:.12em;
    text-transform:uppercase; color:var(--muted);
    margin-bottom:14px; padding-left:4px;
}
.arch-depts { display:grid; grid-template-columns:repeat(6,1fr); gap:10px; margin-bottom:0; }
.arch-dept-box {
    background:var(--glass); border:1px solid var(--border);
    border-radius:12px; padding:14px 10px; text-align:center;
    backdrop-filter:blur(8px);
    border-top:3px solid var(--b500);
    font-size:.75rem; font-weight:600;
    transition:transform .2s;
}
.arch-dept-box:hover { transform:translateY(-3px); }
.arch-dept-box .di { font-size:1.3rem; margin-bottom:6px; }

/* SVG connector */
.arch-connector { width:100%; overflow:visible; display:block; }

/* Platform Core */
.platform-core {
    background:linear-gradient(135deg,rgba(37,99,235,.15),rgba(6,182,212,.08));
    border:1px solid rgba(37,99,235,.35);
    border-radius:16px; padding:24px 32px;
    text-align:center; margin:0 auto;
    max-width:500px;
    position:relative;
    box-shadow:var(--glow);
}
.platform-core::before {
    content:''; position:absolute; inset:-1px;
    border-radius:17px;
    background:linear-gradient(135deg,rgba(37,99,235,.3),rgba(6,182,212,.2));
    z-index:-1;
}
.platform-title { font-size:1rem; font-weight:800; color:var(--b300); margin-bottom:10px; }
.platform-sub { font-size:.78rem; color:var(--sub); margin-bottom:16px; }
.platform-chips { display:flex; gap:8px; flex-wrap:wrap; justify-content:center; }
.pchip {
    font-size:.68rem; font-weight:700; padding:4px 10px; border-radius:8px;
    background:rgba(37,99,235,.15); border:1px solid rgba(37,99,235,.25); color:var(--b300);
}

/* Well cards */
.arch-wells { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; }
.well-card {
    background:var(--glass); border:1px solid var(--border);
    border-radius:14px; padding:18px;
    backdrop-filter:blur(8px);
    border-top:3px solid var(--cyan);
    transition:transform .2s, box-shadow .2s;
}
.well-card:hover { transform:translateY(-4px); box-shadow:var(--glow-c); }
.well-name { font-size:.9rem; font-weight:700; color:var(--cyan-lt); margin-bottom:12px; display:flex; align-items:center; gap:8px; }
.well-dot { width:8px; height:8px; border-radius:50%; background:var(--green); box-shadow:0 0 8px var(--green); animation:pulse 2s infinite; }
.well-kpis { display:grid; grid-template-columns:1fr 1fr; gap:8px; }
.wkpi { background:rgba(255,255,255,.03); border-radius:8px; padding:8px; text-align:center; }
.wkpi-val { font-size:.95rem; font-weight:700; color:#fff; }
.wkpi-lbl { font-size:.62rem; color:var(--muted); }
.well-badges { display:flex; gap:6px; flex-wrap:wrap; margin-top:12px; }
.wbadge { font-size:.6rem; padding:2px 8px; border-radius:4px; font-weight:600; }
.wbadge.mpfm     { background:rgba(6,182,212,.15); color:var(--cyan); }
.wbadge.wl       { background:rgba(37,99,235,.15); color:var(--b300); }
.wbadge.prod     { background:rgba(34,197,94,.15);  color:var(--green); }
.wbadge.drillng  { background:rgba(245,158,11,.15); color:var(--amber); }

/* Field output */
.field-output {
    background:linear-gradient(135deg,rgba(37,99,235,.12),rgba(6,182,212,.06));
    border:1px solid rgba(37,99,235,.3);
    border-radius:16px; padding:28px 32px;
    margin-top:0;
    display:grid; grid-template-columns:repeat(6,1fr); gap:16px;
    box-shadow:var(--glow);
}
.fo-stat { text-align:center; }
.fo-val { font-size:1.5rem; font-weight:800; color:var(--cyan); line-height:1; }
.fo-badge { font-size:.65rem; font-weight:700; color:var(--green); margin:2px 0; }
.fo-lbl { font-size:.65rem; color:var(--muted); }

/* Legend */
.arch-legend {
    display:flex; gap:20px; flex-wrap:wrap; margin-top:24px;
    padding-top:20px; border-top:1px solid var(--border);
}
.leg-item { display:flex; align-items:center; gap:8px; font-size:.72rem; color:var(--sub); }
.leg-dot { width:10px; height:10px; border-radius:50%; }

/* ══ IMAGE BANNER ══ */
.img-banner {
    position:relative; z-index:1;
    height:320px; overflow:hidden;
}
.img-banner img { width:100%; height:100%; object-fit:cover; }
.img-banner::after {
    content:''; position:absolute; inset:0;
    background:linear-gradient(90deg,rgba(5,10,21,.8) 0%,rgba(5,10,21,.3) 50%,rgba(5,10,21,.7) 100%);
}
.banner-text {
    position:absolute; z-index:2; top:50%; left:5%; transform:translateY(-50%);
}
.banner-text h3 { font-size:clamp(1.4rem,3vw,2.2rem); font-weight:800; margin-bottom:10px; }
.banner-text p { color:var(--sub); max-width:420px; font-size:.92rem; }

/* ══ WORKFLOW ══ */
.workflow-section {
    position:relative; z-index:1;
    padding:90px 5%;
    background:var(--bg2);
}
.workflow-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(280px,1fr)); gap:24px; }
.step-card {
    background:var(--glass); border:1px solid var(--border);
    border-radius:16px; padding:28px;
    backdrop-filter:blur(12px);
    position:relative; transition:transform .3s, box-shadow .3s;
}
.step-card:hover { transform:translateY(-5px); box-shadow:var(--glow); }
.step-num {
    width:40px; height:40px; border-radius:10px;
    background:linear-gradient(135deg,var(--b500),var(--cyan));
    color:#fff; font-size:.9rem; font-weight:800;
    display:flex; align-items:center; justify-content:center;
    margin-bottom:16px;
    box-shadow:0 4px 16px rgba(37,99,235,.4);
}
.step-icon { font-size:1.5rem; margin-bottom:10px; }
.step-title { font-size:1rem; font-weight:700; margin-bottom:8px; }
.step-desc { font-size:.83rem; color:var(--sub); line-height:1.65; }

/* ══ ROLES ══ */
.roles-section { position:relative; z-index:1; padding:90px 5%; }
.roles-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(260px,1fr)); gap:20px; }
.role-card {
    background:var(--glass); border:1px solid var(--border);
    border-radius:14px; padding:24px;
    backdrop-filter:blur(10px);
    transition:transform .2s, box-shadow .2s;
}
.role-card:hover { transform:translateY(-4px); box-shadow:var(--glow); }
.role-icon { font-size:2rem; margin-bottom:12px; }
.role-name { font-size:1rem; font-weight:700; margin-bottom:6px; color:var(--b300); }
.role-desc { font-size:.82rem; color:var(--sub); line-height:1.6; margin-bottom:14px; }
.role-perms { display:flex; gap:6px; flex-wrap:wrap; }
.rperm {
    font-size:.65rem; padding:3px 8px; border-radius:5px; font-weight:600;
    background:rgba(37,99,235,.1); border:1px solid rgba(37,99,235,.2); color:var(--b300);
}

/* ══ BENEFITS ══ */
.benefits-section { position:relative; z-index:1; padding:90px 5%; background:var(--bg2); }
.cost-strip {
    background:linear-gradient(135deg,var(--b500),var(--b700));
    border-radius:16px; padding:32px; margin-bottom:48px;
    display:flex; gap:32px; align-items:center; flex-wrap:wrap;
    box-shadow:var(--glow);
}
.cost-strip-main { flex:1; min-width:220px; }
.cost-pct { font-size:3rem; font-weight:900; color:#fff; line-height:1; }
.cost-lbl { font-size:1rem; color:rgba(255,255,255,.8); margin-top:4px; }
.cost-metrics { display:flex; gap:24px; flex-wrap:wrap; }
.cost-m { text-align:center; }
.cost-m-val { font-size:1.4rem; font-weight:800; color:#fff; }
.cost-m-lbl { font-size:.72rem; color:rgba(255,255,255,.7); }

.benefits-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(280px,1fr)); gap:20px; margin-bottom:48px; }
.benefit-card {
    background:var(--glass); border:1px solid var(--border);
    border-radius:14px; padding:24px;
    backdrop-filter:blur(10px);
    transition:transform .2s, box-shadow .2s;
}
.benefit-card:hover { transform:translateY(-4px); box-shadow:var(--glow); }
.benefit-icon { font-size:1.8rem; margin-bottom:12px; }
.benefit-title { font-size:.95rem; font-weight:700; margin-bottom:8px; color:var(--b300); }
.benefit-desc { font-size:.82rem; color:var(--sub); line-height:1.6; }

/* Before/after table */
.compare-table {
    width:100%; border-collapse:collapse;
    background:var(--glass); border-radius:14px; overflow:hidden;
    border:1px solid var(--border);
    backdrop-filter:blur(10px);
}
.compare-table th {
    padding:14px 18px; text-align:left;
    font-size:.78rem; font-weight:700; letter-spacing:.07em; text-transform:uppercase;
    background:rgba(37,99,235,.12); color:var(--b300);
    border-bottom:1px solid var(--border);
}
.compare-table td {
    padding:13px 18px; font-size:.83rem;
    border-bottom:1px solid rgba(255,255,255,.04); color:var(--sub);
}
.compare-table tr:last-child td { border-bottom:none; }
.compare-table td:first-child { color:#e2e8f0; font-weight:500; }
.td-before { color:var(--red) !important; }
.td-after  { color:var(--green) !important; font-weight:600 !important; }

/* ══ SECURITY ══ */
.security-section { position:relative; z-index:1; padding:90px 5%; }
.sec-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(260px,1fr)); gap:20px; }
.sec-card {
    background:var(--glass); border:1px solid var(--border);
    border-radius:14px; padding:24px;
    backdrop-filter:blur(10px);
    border-left:3px solid var(--b500);
    transition:transform .2s, box-shadow .2s;
}
.sec-card:hover { transform:translateY(-4px); box-shadow:var(--glow); }
.sec-icon { font-size:1.6rem; margin-bottom:12px; }
.sec-title { font-size:.95rem; font-weight:700; margin-bottom:8px; color:var(--b300); }
.sec-desc { font-size:.82rem; color:var(--sub); line-height:1.6; }

/* ══ CAPABILITIES ══ */
.caps-section { position:relative; z-index:1; padding:90px 5%; background:var(--bg2); }
.caps-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(240px,1fr)); gap:18px; }
.cap-card {
    background:var(--glass); border:1px solid var(--border);
    border-radius:12px; padding:20px;
    backdrop-filter:blur(10px);
    transition:transform .2s;
}
.cap-card:hover { transform:translateY(-3px); }
.cap-icon { font-size:1.5rem; margin-bottom:10px; }
.cap-title { font-size:.9rem; font-weight:700; color:var(--b300); margin-bottom:6px; }
.cap-desc { font-size:.78rem; color:var(--sub); line-height:1.55; }

/* ══ CTA ══ */
.cta-section {
    position:relative; z-index:1; padding:90px 5%; text-align:center;
    background:linear-gradient(135deg, rgba(37,99,235,.1) 0%, rgba(6,182,212,.06) 100%);
    border-top:1px solid var(--border); border-bottom:1px solid var(--border);
}
.cta-title { font-size:clamp(1.6rem,3.5vw,2.8rem); font-weight:800; margin-bottom:14px; }
.cta-sub { color:var(--sub); max-width:520px; margin:0 auto 36px; font-size:.95rem; }
.cta-btns { display:flex; gap:16px; justify-content:center; flex-wrap:wrap; }

/* ══ FOOTER ══ */
footer {
    position:relative; z-index:1;
    padding:48px 5% 32px;
    background:rgba(5,10,21,.95);
    border-top:1px solid var(--border);
}
.footer-top { display:flex; gap:40px; flex-wrap:wrap; justify-content:space-between; margin-bottom:32px; }
.footer-brand img { height:44px; margin-bottom:14px; }
.footer-brand p { font-size:.8rem; color:var(--muted); max-width:260px; line-height:1.6; }
.footer-col h5 { font-size:.78rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--b300); margin-bottom:14px; }
.footer-col ul { list-style:none; display:flex; flex-direction:column; gap:10px; }
.footer-col a { font-size:.8rem; color:var(--muted); transition:color .2s; }
.footer-col a:hover { color:var(--b300); }
.footer-bottom {
    border-top:1px solid var(--border); padding-top:24px;
    display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;
    font-size:.75rem; color:var(--muted);
}
.footer-badge {
    display:flex; align-items:center; gap:6px;
    background:rgba(34,197,94,.1); border:1px solid rgba(34,197,94,.2);
    color:var(--green); padding:4px 12px; border-radius:8px; font-size:.7rem; font-weight:600;
}

/* ══ REVEAL ANIMATION ══ */
.reveal { opacity:0; transform:translateY(24px); transition:opacity .6s, transform .6s; }
.reveal.visible { opacity:1; transform:translateY(0); }

/* ══ RESPONSIVE ══ */
@media (max-width:1024px) {
    .arch-depts { grid-template-columns:repeat(3,1fr); }
    .field-output { grid-template-columns:repeat(3,1fr); }
}
@media (max-width:860px) {
    .nav-links { display:none; }
    .nav-burger { display:flex; }
    .photo-strip { grid-template-columns:repeat(2,1fr); height:160px; }
    .arch-wells { grid-template-columns:1fr 1fr; }
    .arch-depts { grid-template-columns:repeat(2,1fr); }
}
@media (max-width:600px) {
    .hero h1 { font-size:1.9rem; }
    .photo-strip { grid-template-columns:1fr 1fr; height:130px; }
    .arch-wells { grid-template-columns:1fr; }
    .arch-depts { grid-template-columns:1fr 1fr; }
    .field-output { grid-template-columns:repeat(2,1fr); }
    .cost-strip { flex-direction:column; }
    .footer-top { flex-direction:column; }
}
</style>
</head>
<body>

<!-- Animated background -->
<div class="grid-bg"></div>
<div class="blob blob-1"></div>
<div class="blob blob-2"></div>

<!-- ══ NAV ══ -->
<nav>
    <a href="/" class="nav-logo"><img src="/assets/img/brand/logo-white.png" alt="Oil Field Management System"></a>
    <ul class="nav-links">
        <li><a href="#overview">Overview</a></li>
        <li><a href="#diagram">Production Flow</a></li>
        <li><a href="#departments">Departments</a></li>
        <li><a href="#workflow">Workflow</a></li>
        <li><a href="#benefits">ROI</a></li>
    </ul>
    <div class="nav-right">
        <div class="dm-toggle" id="dmToggle" title="Toggle dark/light mode"></div>
        @auth
        <a href="{{ url('/dashboard') }}" class="nav-cta">&#9654; Dashboard</a>
        @else
        <a href="{{ route('login') }}" class="nav-cta">&#9654; Sign In</a>
        @endauth
        <button class="nav-burger" id="burger" aria-label="Menu">
            <span></span><span></span><span></span>
        </button>
    </div>
</nav>
<div class="nav-drawer" id="drawer">
    <ul>
        <li><a href="#overview">Overview</a></li>
        <li><a href="#diagram">Production Flow</a></li>
        <li><a href="#departments">Departments</a></li>
        <li><a href="#workflow">Workflow</a></li>
        <li><a href="#benefits">ROI</a></li>
        @auth
        <li><a href="{{ url('/dashboard') }}" style="color:var(--b400);font-weight:700;">Open Dashboard</a></li>
        @else
        <li><a href="{{ route('login') }}" style="color:var(--b400);font-weight:700;">Sign In</a></li>
        @endauth
    </ul>
</div>

<!-- ══ HERO ══ -->
<section class="hero">
    <div class="hero-bg"></div>
    <div class="hero-overlay"></div>
    <div class="hero-content reveal">
        <img src="/assets/img/brand/logo-white.png" alt="Oil Field Management System" class="hero-logo">
        <div class="hero-badge">&#11044; Live Operations System</div>
        <h1>Advanced Petroleum Wells<br>Management System</h1>
        <p class="hero-sub">Enterprise-grade field operations platform — unifying all departments, wells, and production workflows on a single intelligent dashboard.</p>
        <div class="hero-btns">
            @auth
            <a href="{{ url('/dashboard') }}" class="btn-primary">&#9193; View Dashboard</a>
            @else
            <a href="{{ route('login') }}" class="btn-primary">&#9193; Access System</a>
            @endauth
            <a href="#diagram" class="btn-secondary">&#11153; System Overview</a>
        </div>
        <div class="hero-stats">
            <div class="hstat"><div class="hstat-val">6<span>+</span></div><div class="hstat-lbl">Departments</div></div>
            <div class="hstat"><div class="hstat-val">24<span>/7</span></div><div class="hstat-lbl">Monitoring</div></div>
            <div class="hstat"><div class="hstat-val">8</div><div class="hstat-lbl">Task States</div></div>
            <div class="hstat"><div class="hstat-val">100<span>%</span></div><div class="hstat-lbl">Audit Trail</div></div>
            <div class="hstat"><div class="hstat-val">60<span>%</span></div><div class="hstat-lbl">Faster Reports</div></div>
        </div>
    </div>
</section>

<!-- ══ PHOTO STRIP ══ -->
<div class="photo-strip">
    <img src="/assets/img/brand/oilfield oprations.webp" alt="Oilfield" loading="lazy">
    <img src="/assets/img/brand/Offshore.jpg" alt="Offshore Platform" loading="lazy">
    <img src="/assets/img/brand/offshore oilfield.jpg" alt="Offshore Oilfield" loading="lazy">
    <img src="/assets/img/brand/production background.jpg" alt="Production" loading="lazy">
</div>

<!-- ══ KPI CARDS ══ -->
<section class="kpi-section" id="overview">
    <div class="section-label">Live Metrics</div>
    <h2 class="section-title">Field Operations at a Glance</h2>
    <p class="section-sub">Real-time visibility across all wells, platforms, and departments from a unified control view.</p>
    <div class="kpi-grid">
        <div class="kpi-card reveal">
            <div class="kpi-trend trend-up">&#8679; Active</div>
            <div class="kpi-icon">🛢️</div>
            <div class="kpi-val blue">47</div>
            <div class="kpi-lbl">Active Wells</div>
        </div>
        <div class="kpi-card reveal">
            <div class="kpi-trend trend-up">&#8679; +12%</div>
            <div class="kpi-icon cyan">📈</div>
            <div class="kpi-val cyan">4,215</div>
            <div class="kpi-lbl">BOPD Production Today</div>
        </div>
        <div class="kpi-card reveal">
            <div class="kpi-trend trend-down">3 New</div>
            <div class="kpi-icon red">⚠️</div>
            <div class="kpi-val red">3</div>
            <div class="kpi-lbl">Active Alerts</div>
        </div>
        <div class="kpi-card reveal">
            <div class="kpi-trend trend-ok">Online</div>
            <div class="kpi-icon">📡</div>
            <div class="kpi-val blue">12</div>
            <div class="kpi-lbl">MPFM Devices Online</div>
        </div>
        <div class="kpi-card reveal">
            <div class="kpi-trend trend-up">&#8679; 2%</div>
            <div class="kpi-icon green">✅</div>
            <div class="kpi-val green">96%</div>
            <div class="kpi-lbl">Separator Efficiency</div>
        </div>
    </div>
</section>

<!-- ══ INTERACTIVE PRODUCTION DIAGRAM ══ -->
<section class="diagram-section" id="diagram">
    <div class="section-label">Production Flow</div>
    <h2 class="section-title">Interactive Oil Production Diagram</h2>
    <p class="section-sub">Hover over any component to view live operational data. Click to explore details.</p>

    <div class="diagram-wrap reveal">
        <div class="diagram-title">Full Production Pipeline — Wellhead to Storage</div>
        <div style="position:relative;">
        <svg class="prod-svg" viewBox="0 0 1020 420" xmlns="http://www.w3.org/2000/svg" id="prodSvg">
            <defs>
                <marker id="arr" markerWidth="8" markerHeight="8" refX="6" refY="3" orient="auto">
                    <path d="M0,0 L0,6 L8,3 z" fill="#3b82f6"/>
                </marker>
                <marker id="arr-c" markerWidth="8" markerHeight="8" refX="6" refY="3" orient="auto">
                    <path d="M0,0 L0,6 L8,3 z" fill="#06b6d4"/>
                </marker>
                <!-- Animated gradient flow -->
                <linearGradient id="flowGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" stop-color="#2563EB" stop-opacity="0"/>
                    <stop offset="50%" stop-color="#06b6d4" stop-opacity="1"/>
                    <stop offset="100%" stop-color="#2563EB" stop-opacity="0"/>
                    <animateTransform attributeName="gradientTransform" type="translate" from="-1 0" to="1 0" dur="2s" repeatCount="indefinite"/>
                </linearGradient>
                <filter id="glow">
                    <feGaussianBlur stdDeviation="2" result="blur"/>
                    <feMerge><feMergeNode in="blur"/><feMergeNode in="SourceGraphic"/></feMerge>
                </filter>
            </defs>

            <!-- Background grid -->
            <rect width="1020" height="420" fill="rgba(5,10,21,0)" rx="12"/>

            <!-- WELLHEAD -->
            <g class="diag-node" data-node="wellhead" transform="translate(30,170)">
                <rect width="110" height="80" rx="10" fill="rgba(37,99,235,.15)" stroke="#3b82f6" stroke-width="1.5"/>
                <text x="55" y="22" text-anchor="middle" fill="#93c5fd" font-size="18">🏗️</text>
                <text x="55" y="42" text-anchor="middle" fill="#e2e8f0" font-size="10" font-weight="700">WELLHEAD</text>
                <text x="55" y="56" text-anchor="middle" fill="#64748b" font-size="8">Surface Control</text>
                <!-- Live indicator -->
                <circle cx="95" cy="10" r="5" fill="#22c55e">
                    <animate attributeName="opacity" values="1;0.3;1" dur="1.5s" repeatCount="indefinite"/>
                </circle>
            </g>

            <!-- Arrow Wellhead → Tubing -->
            <line x1="140" y1="210" x2="175" y2="210" stroke="#3b82f6" stroke-width="2" marker-end="url(#arr)"/>
            <!-- Flow animation -->
            <line x1="140" y1="210" x2="175" y2="210" stroke="url(#flowGrad)" stroke-width="4" opacity="0.6"/>

            <!-- TUBING / DOWNHOLE -->
            <g class="diag-node" data-node="tubing" transform="translate(175,170)">
                <rect width="110" height="80" rx="10" fill="rgba(37,99,235,.12)" stroke="#3b82f6" stroke-width="1.5"/>
                <text x="55" y="22" text-anchor="middle" fill="#93c5fd" font-size="18">🔩</text>
                <text x="55" y="42" text-anchor="middle" fill="#e2e8f0" font-size="10" font-weight="700">TUBING</text>
                <text x="55" y="56" text-anchor="middle" fill="#64748b" font-size="8">Downhole String</text>
                <circle cx="95" cy="10" r="5" fill="#22c55e">
                    <animate attributeName="opacity" values="1;0.3;1" dur="1.8s" repeatCount="indefinite"/>
                </circle>
            </g>

            <!-- Arrow → MPFM -->
            <line x1="285" y1="210" x2="320" y2="210" stroke="#3b82f6" stroke-width="2" marker-end="url(#arr)"/>
            <line x1="285" y1="210" x2="320" y2="210" stroke="url(#flowGrad)" stroke-width="4" opacity="0.6"/>

            <!-- MPFM METER -->
            <g class="diag-node" data-node="mpfm" transform="translate(320,155)">
                <rect width="120" height="110" rx="10" fill="rgba(6,182,212,.15)" stroke="#06b6d4" stroke-width="2"/>
                <text x="60" y="24" text-anchor="middle" fill="#67e8f9" font-size="18">📊</text>
                <text x="60" y="46" text-anchor="middle" fill="#e2e8f0" font-size="10" font-weight="700">MPFM METER</text>
                <text x="60" y="60" text-anchor="middle" fill="#64748b" font-size="8">Multi-Phase Flow</text>
                <text x="60" y="76" text-anchor="middle" fill="#06b6d4" font-size="9" font-weight="600">Oil: 87 BOPD</text>
                <text x="60" y="90" text-anchor="middle" fill="#06b6d4" font-size="9">Gas: 12 MMSCFD</text>
                <circle cx="108" cy="12" r="5" fill="#f59e0b">
                    <animate attributeName="opacity" values="1;0.2;1" dur="1s" repeatCount="indefinite"/>
                </circle>
            </g>

            <!-- Pressure sensor on MPFM -->
            <g class="diag-node" data-node="pressure" transform="translate(340,80)">
                <rect width="80" height="55" rx="8" fill="rgba(245,158,11,.12)" stroke="#f59e0b" stroke-width="1.5"/>
                <text x="40" y="20" text-anchor="middle" fill="#fcd34d" font-size="14">⏱️</text>
                <text x="40" y="34" text-anchor="middle" fill="#e2e8f0" font-size="8" font-weight="700">PRESSURE</text>
                <text x="40" y="46" text-anchor="middle" fill="#f59e0b" font-size="9">1,840 PSI</text>
            </g>
            <line x1="380" y1="135" x2="380" y2="155" stroke="#f59e0b" stroke-width="1.5" stroke-dasharray="4,3"/>

            <!-- Temperature sensor -->
            <g class="diag-node" data-node="temp" transform="translate(430,80)">
                <rect width="80" height="55" rx="8" fill="rgba(239,68,68,.12)" stroke="#ef4444" stroke-width="1.5"/>
                <text x="40" y="20" text-anchor="middle" fill="#fca5a5" font-size="14">🌡️</text>
                <text x="40" y="34" text-anchor="middle" fill="#e2e8f0" font-size="8" font-weight="700">TEMPERATURE</text>
                <text x="40" y="46" text-anchor="middle" fill="#ef4444" font-size="9">78°C</text>
            </g>
            <line x1="470" y1="135" x2="420" y2="155" stroke="#ef4444" stroke-width="1.5" stroke-dasharray="4,3"/>

            <!-- Arrow MPFM → Separator -->
            <line x1="440" y1="210" x2="480" y2="210" stroke="#06b6d4" stroke-width="2" marker-end="url(#arr-c)"/>
            <line x1="440" y1="210" x2="480" y2="210" stroke="url(#flowGrad)" stroke-width="4" opacity="0.6"/>

            <!-- SEPARATOR -->
            <g class="diag-node" data-node="separator" transform="translate(480,165)">
                <rect width="120" height="90" rx="10" fill="rgba(37,99,235,.15)" stroke="#3b82f6" stroke-width="1.5"/>
                <text x="60" y="24" text-anchor="middle" fill="#93c5fd" font-size="18">⚗️</text>
                <text x="60" y="46" text-anchor="middle" fill="#e2e8f0" font-size="10" font-weight="700">SEPARATOR</text>
                <text x="60" y="60" text-anchor="middle" fill="#64748b" font-size="8">3-Phase Sep.</text>
                <text x="60" y="75" text-anchor="middle" fill="#22c55e" font-size="9">Efficiency: 96%</text>
                <circle cx="108" cy="12" r="5" fill="#22c55e">
                    <animate attributeName="opacity" values="1;0.3;1" dur="2s" repeatCount="indefinite"/>
                </circle>
            </g>

            <!-- Gas outlet from Separator (up) -->
            <line x1="540" y1="165" x2="540" y2="120" stroke="#64748b" stroke-width="1.5" stroke-dasharray="5,3" marker-end="url(#arr)"/>
            <text x="548" y="145" fill="#64748b" font-size="8">Gas</text>

            <!-- Arrow Separator → Flowline -->
            <line x1="600" y1="210" x2="640" y2="210" stroke="#3b82f6" stroke-width="2" marker-end="url(#arr)"/>
            <line x1="600" y1="210" x2="640" y2="210" stroke="url(#flowGrad)" stroke-width="4" opacity="0.6"/>

            <!-- FLOWLINE -->
            <g class="diag-node" data-node="flowline" transform="translate(640,170)">
                <rect width="110" height="80" rx="10" fill="rgba(37,99,235,.12)" stroke="#3b82f6" stroke-width="1.5"/>
                <text x="55" y="22" text-anchor="middle" fill="#93c5fd" font-size="18">🌊</text>
                <text x="55" y="42" text-anchor="middle" fill="#e2e8f0" font-size="10" font-weight="700">FLOWLINE</text>
                <text x="55" y="56" text-anchor="middle" fill="#64748b" font-size="8">Transfer Line</text>
                <circle cx="95" cy="10" r="5" fill="#06b6d4">
                    <animate attributeName="opacity" values="1;0.3;1" dur="1.3s" repeatCount="indefinite"/>
                </circle>
            </g>

            <!-- Arrow → Storage Tank -->
            <line x1="750" y1="210" x2="790" y2="210" stroke="#3b82f6" stroke-width="2" marker-end="url(#arr)"/>
            <line x1="750" y1="210" x2="790" y2="210" stroke="url(#flowGrad)" stroke-width="4" opacity="0.6"/>

            <!-- STORAGE TANK -->
            <g class="diag-node" data-node="storage" transform="translate(790,155)">
                <rect width="110" height="110" rx="10" fill="rgba(37,99,235,.15)" stroke="#3b82f6" stroke-width="1.5"/>
                <!-- Tank visual -->
                <rect x="20" y="30" width="70" height="50" rx="4" fill="rgba(37,99,235,.2)" stroke="#3b82f6" stroke-width="1"/>
                <!-- Fill level -->
                <rect x="20" y="58" width="70" height="22" rx="0" fill="rgba(37,99,235,.5)"/>
                <text x="55" y="100" text-anchor="middle" fill="#e2e8f0" font-size="10" font-weight="700">STORAGE TANK</text>
                <text x="55" y="20" text-anchor="middle" fill="#93c5fd" font-size="8">Cap: 5,000 bbl</text>
                <circle cx="98" cy="12" r="5" fill="#22c55e">
                    <animate attributeName="opacity" values="1;0.3;1" dur="2.5s" repeatCount="indefinite"/>
                </circle>
            </g>

            <!-- CONTROL PANEL (top center) -->
            <g class="diag-node" data-node="control" transform="translate(430,300)">
                <rect width="160" height="70" rx="10" fill="rgba(37,99,235,.18)" stroke="#60a5fa" stroke-width="2"/>
                <text x="80" y="22" text-anchor="middle" fill="#bfdbfe" font-size="18">🖥️</text>
                <text x="80" y="42" text-anchor="middle" fill="#e2e8f0" font-size="10" font-weight="700">CONTROL PANEL</text>
                <text x="80" y="57" text-anchor="middle" fill="#64748b" font-size="8">SCADA Interface — 24/7</text>
                <circle cx="148" cy="12" r="5" fill="#22c55e">
                    <animate attributeName="opacity" values="1;0.2;1" dur="0.8s" repeatCount="indefinite"/>
                </circle>
            </g>
            <!-- Control lines -->
            <line x1="510" y1="300" x2="510" y2="265" stroke="#3b82f6" stroke-width="1.5" stroke-dasharray="5,4"/>
            <line x1="380" y1="300" x2="380" y2="265" stroke="#3b82f6" stroke-width="1.5" stroke-dasharray="5,4"/>

            <!-- Flow status labels -->
            <text x="155" y="204" fill="#3b82f6" font-size="8" opacity="0.7">Flow</text>
            <text x="300" y="204" fill="#3b82f6" font-size="8" opacity="0.7">Flow</text>
            <text x="455" y="204" fill="#06b6d4" font-size="8" opacity="0.7">Flow</text>
            <text x="614" y="204" fill="#3b82f6" font-size="8" opacity="0.7">Flow</text>
            <text x="758" y="204" fill="#3b82f6" font-size="8" opacity="0.7">Flow</text>

            <!-- Legend -->
            <g transform="translate(30,370)">
                <circle cx="6" cy="6" r="5" fill="#22c55e"/>
                <text x="16" y="10" fill="#64748b" font-size="8">Online</text>
                <circle cx="70" cy="6" r="5" fill="#f59e0b"/>
                <text x="80" y="10" fill="#64748b" font-size="8">Monitoring</text>
                <line x1="140" y1="6" x2="165" y2="6" stroke="#64748b" stroke-dasharray="4,3" stroke-width="1.5"/>
                <text x="170" y="10" fill="#64748b" font-size="8">Signal Line</text>
                <line x1="240" y1="6" x2="265" y2="6" stroke="#3b82f6" stroke-width="2"/>
                <text x="270" y="10" fill="#64748b" font-size="8">Flow Path</text>
            </g>
        </svg>

        <!-- Tooltip -->
        <div class="diag-tooltip" id="diagTooltip">
            <div class="tt-title" id="ttTitle">Component</div>
            <div id="ttBody"></div>
        </div>
        </div>
    </div>
</section>

<!-- ══ DEPARTMENTS ══ -->
<section class="dept-section" id="departments">
    <div class="section-label">Our Departments</div>
    <h2 class="section-title">Six Departments. One Unified Platform.</h2>
    <p class="section-sub">Each department contributes specialized expertise that feeds directly into well performance and production output.</p>
    <div class="dept-grid">
        <div class="dept-card reveal">
            <div class="dept-img">
                <img src="/assets/img/brand/mpfm monitoing.jpg" alt="MPFM" loading="lazy">
                <div class="dept-tag">MPFM</div>
            </div>
            <div class="dept-body">
                <div class="dept-name">MPFM Monitoring</div>
                <p class="dept-desc">Multi-Phase Flow Measurement — real-time oil, gas, and water flow rates for each well, enabling precise production accounting without test separators.</p>
                <div class="dept-kpis">
                    <span class="dept-kpi">Flow Accuracy</span>
                    <span class="dept-kpi">Real-time Data</span>
                    <span class="dept-kpi">12 Devices</span>
                </div>
            </div>
        </div>
        <div class="dept-card reveal">
            <div class="dept-img">
                <img src="/assets/img/brand/wire Line lubricator .jpg" alt="Wire Line" loading="lazy">
                <div class="dept-tag">Wire Line</div>
            </div>
            <div class="dept-body">
                <div class="dept-name">Wire Line Operations</div>
                <p class="dept-desc">Downhole logging, perforation, and plug operations using wireline tools — providing critical subsurface data for reservoir management decisions.</p>
                <div class="dept-kpis">
                    <span class="dept-kpi">Downhole Logging</span>
                    <span class="dept-kpi">Perforation</span>
                    <span class="dept-kpi">Well Integrity</span>
                </div>
            </div>
        </div>
        <div class="dept-card reveal">
            <div class="dept-img">
                <img src="/assets/img/brand/production background.jpg" alt="Production" loading="lazy">
                <div class="dept-tag">Production</div>
            </div>
            <div class="dept-body">
                <div class="dept-name">Production Operations</div>
                <p class="dept-desc">Daily well monitoring, production optimization, and facility management ensuring maximum hydrocarbon recovery at minimum operating cost.</p>
                <div class="dept-kpis">
                    <span class="dept-kpi">4,215 BOPD</span>
                    <span class="dept-kpi">Optimization</span>
                    <span class="dept-kpi">SLA Tracking</span>
                </div>
            </div>
        </div>
        <div class="dept-card reveal">
            <div class="dept-img">
                <img src="/assets/img/brand/drilling background .png" alt="Drilling" loading="lazy">
                <div class="dept-tag">Drilling</div>
            </div>
            <div class="dept-body">
                <div class="dept-name">Drilling Operations</div>
                <p class="dept-desc">Well construction from surface to reservoir — planning, execution, and documentation of all drilling activities with full audit trail and safety compliance.</p>
                <div class="dept-kpis">
                    <span class="dept-kpi">Well Planning</span>
                    <span class="dept-kpi">Safety Audit</span>
                    <span class="dept-kpi">NPT Tracking</span>
                </div>
            </div>
        </div>
        <div class="dept-card reveal">
            <div class="dept-img">
                <img src="/assets/img/brand/complation.jpg" alt="Completion" loading="lazy">
                <div class="dept-tag">Completion</div>
            </div>
            <div class="dept-body">
                <div class="dept-name">Well Completion</div>
                <p class="dept-desc">Installation of downhole equipment (tubing, packers, valves) that prepares drilled wells for production — bridging drilling and production phases.</p>
                <div class="dept-kpis">
                    <span class="dept-kpi">Tubing Design</span>
                    <span class="dept-kpi">Packer Setting</span>
                    <span class="dept-kpi">Flow Assurance</span>
                </div>
            </div>
        </div>
        <div class="dept-card reveal">
            <div class="dept-img">
                <img src="/assets/img/brand/work over .jpg" alt="Workover" loading="lazy">
                <div class="dept-tag">Workover</div>
            </div>
            <div class="dept-body">
                <div class="dept-name">Workover Operations</div>
                <p class="dept-desc">Well intervention and remediation — restoring underperforming wells to full production potential through mechanical repairs and stimulation treatments.</p>
                <div class="dept-kpis">
                    <span class="dept-kpi">Well Intervention</span>
                    <span class="dept-kpi">Stimulation</span>
                    <span class="dept-kpi">Cost Reduction</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ══ WELL ARCHITECTURE DIAGRAM ══ -->
<section class="arch-section" id="architecture">
    <div style="max-width:1100px;margin:0 auto;">
        <div class="section-label">System Architecture</div>
        <h2 class="section-title">How Departments Drive Well Output</h2>
        <p class="section-sub">Four integrated layers — from departmental inputs to measurable field output.</p>

        <div class="arch-wrap reveal">
            <!-- Layer 1: Departments -->
            <div class="arch-layer-label">&#9312; Department Inputs</div>
            <div class="arch-depts">
                <div class="arch-dept-box" style="border-top-color:#06b6d4;">
                    <div class="di">📊</div>MPFM
                </div>
                <div class="arch-dept-box" style="border-top-color:#3b82f6;">
                    <div class="di">🔩</div>Wire Line
                </div>
                <div class="arch-dept-box" style="border-top-color:#22c55e;">
                    <div class="di">⚙️</div>Production
                </div>
                <div class="arch-dept-box" style="border-top-color:#f59e0b;">
                    <div class="di">🏗️</div>Drilling
                </div>
                <div class="arch-dept-box" style="border-top-color:#a855f7;">
                    <div class="di">🔧</div>Completion
                </div>
                <div class="arch-dept-box" style="border-top-color:#ef4444;">
                    <div class="di">⛏️</div>Workover
                </div>
            </div>

            <!-- SVG connector 1 -->
            <svg class="arch-connector" height="60" viewBox="0 0 1100 60">
                <line x1="91"  y1="0" x2="91"  y2="30" stroke="#3b82f6" stroke-width="1.5" stroke-dasharray="5,3"/>
                <line x1="274" y1="0" x2="274" y2="30" stroke="#3b82f6" stroke-width="1.5" stroke-dasharray="5,3"/>
                <line x1="457" y1="0" x2="457" y2="30" stroke="#3b82f6" stroke-width="1.5" stroke-dasharray="5,3"/>
                <line x1="640" y1="0" x2="640" y2="30" stroke="#3b82f6" stroke-width="1.5" stroke-dasharray="5,3"/>
                <line x1="823" y1="0" x2="823" y2="30" stroke="#3b82f6" stroke-width="1.5" stroke-dasharray="5,3"/>
                <line x1="1006" y1="0" x2="1006" y2="30" stroke="#3b82f6" stroke-width="1.5" stroke-dasharray="5,3"/>
                <line x1="91" y1="30" x2="1006" y2="30" stroke="#3b82f6" stroke-width="1.5" stroke-dasharray="5,3"/>
                <line x1="548" y1="30" x2="548" y2="60" stroke="#3b82f6" stroke-width="2" marker-end="url(#arr)"/>
            </svg>

            <!-- Layer 2: Platform Core -->
            <div style="display:flex;justify-content:center;margin-bottom:0;">
                <div class="platform-core">
                    <div class="platform-title">🖥️ Live Platform Core</div>
                    <div class="platform-sub">Unified task management, state tracking &amp; SLA engine</div>
                    <div class="platform-chips">
                        <span class="pchip">8 Task States</span>
                        <span class="pchip">6 Roles</span>
                        <span class="pchip">24/7 Active</span>
                        <span class="pchip">100% Audit</span>
                        <span class="pchip">SLA Engine</span>
                    </div>
                </div>
            </div>

            <!-- SVG connector 2 -->
            <svg class="arch-connector" height="60" viewBox="0 0 1100 60">
                <line x1="548" y1="0" x2="548" y2="20" stroke="#06b6d4" stroke-width="2"/>
                <line x1="200" y1="20" x2="900" y2="20" stroke="#06b6d4" stroke-width="1.5" stroke-dasharray="5,3"/>
                <line x1="200" y1="20" x2="200" y2="60" stroke="#06b6d4" stroke-width="1.5" marker-end="url(#arr-c)"/>
                <line x1="548" y1="20" x2="548" y2="60" stroke="#06b6d4" stroke-width="1.5" marker-end="url(#arr-c)"/>
                <line x1="900" y1="20" x2="900" y2="60" stroke="#06b6d4" stroke-width="1.5" marker-end="url(#arr-c)"/>
            </svg>

            <!-- Layer 3: Wells -->
            <div class="arch-wells" style="margin-bottom:0;">
                <div class="well-card">
                    <div class="well-name"><div class="well-dot"></div>Well A-14</div>
                    <div class="well-kpis">
                        <div class="wkpi"><div class="wkpi-val">312</div><div class="wkpi-lbl">BOPD</div></div>
                        <div class="wkpi"><div class="wkpi-val">18%</div><div class="wkpi-lbl">BSW</div></div>
                        <div class="wkpi"><div class="wkpi-val">820</div><div class="wkpi-lbl">GOR</div></div>
                        <div class="wkpi"><div class="wkpi-val">98%</div><div class="wkpi-lbl">Uptime</div></div>
                    </div>
                    <div class="well-badges">
                        <span class="wbadge mpfm">MPFM</span>
                        <span class="wbadge prod">Production</span>
                    </div>
                </div>
                <div class="well-card">
                    <div class="well-name"><div class="well-dot"></div>Well B-07</div>
                    <div class="well-kpis">
                        <div class="wkpi"><div class="wkpi-val">198</div><div class="wkpi-lbl">BOPD</div></div>
                        <div class="wkpi"><div class="wkpi-val">24%</div><div class="wkpi-lbl">BSW</div></div>
                        <div class="wkpi"><div class="wkpi-val">1140</div><div class="wkpi-lbl">GOR</div></div>
                        <div class="wkpi"><div class="wkpi-val">94%</div><div class="wkpi-lbl">Uptime</div></div>
                    </div>
                    <div class="well-badges">
                        <span class="wbadge wl">Wire Line</span>
                        <span class="wbadge drillng">Workover</span>
                    </div>
                </div>
                <div class="well-card">
                    <div class="well-name"><div class="well-dot"></div>Well C-22</div>
                    <div class="well-kpis">
                        <div class="wkpi"><div class="wkpi-val">445</div><div class="wkpi-lbl">BOPD</div></div>
                        <div class="wkpi"><div class="wkpi-val">11%</div><div class="wkpi-lbl">BSW</div></div>
                        <div class="wkpi"><div class="wkpi-val">680</div><div class="wkpi-lbl">GOR</div></div>
                        <div class="wkpi"><div class="wkpi-val">99%</div><div class="wkpi-lbl">Uptime</div></div>
                    </div>
                    <div class="well-badges">
                        <span class="wbadge mpfm">MPFM</span>
                        <span class="wbadge prod">Production</span>
                        <span class="wbadge drillng">Drilling</span>
                    </div>
                </div>
            </div>

            <!-- SVG connector 3 -->
            <svg class="arch-connector" height="50" viewBox="0 0 1100 50">
                <line x1="200" y1="0" x2="200" y2="20" stroke="#22c55e" stroke-width="1.5"/>
                <line x1="548" y1="0" x2="548" y2="20" stroke="#22c55e" stroke-width="1.5"/>
                <line x1="900" y1="0" x2="900" y2="20" stroke="#22c55e" stroke-width="1.5"/>
                <line x1="200" y1="20" x2="900" y2="20" stroke="#22c55e" stroke-width="1.5"/>
                <line x1="548" y1="20" x2="548" y2="50" stroke="#22c55e" stroke-width="2" marker-end="url(#arr)"/>
            </svg>

            <!-- Layer 4: Field Output -->
            <div class="arch-layer-label">&#9315; Field Output &amp; Business Impact</div>
            <div class="field-output">
                <div class="fo-stat">
                    <div class="fo-val">4,215</div>
                    <div class="fo-badge">&#8679; +12%</div>
                    <div class="fo-lbl">BOPD Total</div>
                </div>
                <div class="fo-stat">
                    <div class="fo-val">↓18%</div>
                    <div class="fo-badge" style="color:var(--cyan);">Reduced</div>
                    <div class="fo-lbl">Opex Cost</div>
                </div>
                <div class="fo-stat">
                    <div class="fo-val">60%</div>
                    <div class="fo-badge">Faster</div>
                    <div class="fo-lbl">Report Time</div>
                </div>
                <div class="fo-stat">
                    <div class="fo-val">↓70%</div>
                    <div class="fo-badge">Fewer</div>
                    <div class="fo-lbl">Overdue Tasks</div>
                </div>
                <div class="fo-stat">
                    <div class="fo-val">100%</div>
                    <div class="fo-badge" style="color:var(--cyan);">Complete</div>
                    <div class="fo-lbl">Audit Trail</div>
                </div>
                <div class="fo-stat">
                    <div class="fo-val">3min</div>
                    <div class="fo-badge">Avg</div>
                    <div class="fo-lbl">Alert Response</div>
                </div>
            </div>

            <!-- Legend -->
            <div class="arch-legend">
                <div class="leg-item"><div class="leg-dot" style="background:var(--b500);"></div>Data Flow</div>
                <div class="leg-item"><div class="leg-dot" style="background:var(--cyan);"></div>Control Signal</div>
                <div class="leg-item"><div class="leg-dot" style="background:var(--green);"></div>Active Well</div>
                <div class="leg-item"><div class="leg-dot" style="background:var(--amber);"></div>Monitoring</div>
            </div>
        </div>
    </div>
</section>

<!-- ══ IMAGE BANNER 1 ══ -->
<div class="img-banner">
    <img src="/assets/img/brand/Offshore.jpg" alt="Offshore Platform" loading="lazy">
    <div class="banner-text">
        <h3>Platform-wide Visibility</h3>
        <p>Monitor every well, department, and task from a single unified dashboard — available 24/7 from any device.</p>
    </div>
</div>

<!-- ══ WORKFLOW ══ -->
<section class="workflow-section" id="workflow">
    <div class="section-label">How It Works</div>
    <h2 class="section-title">Task Lifecycle in 6 Steps</h2>
    <p class="section-sub">From task creation to completion — every step tracked, time-stamped, and fully auditable.</p>
    <div class="workflow-grid">
        <div class="step-card reveal">
            <div class="step-num">01</div>
            <div class="step-icon">📋</div>
            <div class="step-title">Monthly Plan Creation</div>
            <p class="step-desc">Supervisors create monthly operational plans, assigning tasks to departments with deadlines, priorities, and resource requirements.</p>
        </div>
        <div class="step-card reveal">
            <div class="step-num">02</div>
            <div class="step-icon">🎯</div>
            <div class="step-title">Task Assignment</div>
            <p class="step-desc">Tasks are distributed to field operators with clear objectives, required tools, and SLA timelines automatically enforced by the system.</p>
        </div>
        <div class="step-card reveal">
            <div class="step-num">03</div>
            <div class="step-icon">⚙️</div>
            <div class="step-title">Field Execution</div>
            <p class="step-desc">Operators log real-time progress, upload MPFM test data, and update task states as work progresses on the platform or well site.</p>
        </div>
        <div class="step-card reveal">
            <div class="step-num">04</div>
            <div class="step-icon">🔍</div>
            <div class="step-title">Surveillance Review</div>
            <p class="step-desc">Surveillance supervisors review completed work, validate technical accuracy, and either approve or request corrections with detailed notes.</p>
        </div>
        <div class="step-card reveal">
            <div class="step-num">05</div>
            <div class="step-icon">✅</div>
            <div class="step-title">Job Officer Approval</div>
            <p class="step-desc">Senior job officers provide final approval, ensuring all safety, compliance, and production targets have been met before task closure.</p>
        </div>
        <div class="step-card reveal">
            <div class="step-num">06</div>
            <div class="step-icon">📊</div>
            <div class="step-title">Analytics &amp; Reporting</div>
            <p class="step-desc">Automated reports on KPIs, SLA compliance, department performance, and production impact — available instantly without manual compilation.</p>
        </div>
    </div>
</section>

<!-- ══ ROLES ══ -->
<section class="roles-section" id="roles">
    <div class="section-label">Access Control</div>
    <h2 class="section-title">Role-Based Access for Every Team Member</h2>
    <p class="section-sub">Six distinct roles with precise permissions — ensuring the right people see and do the right things.</p>
    <div class="roles-grid">
        <div class="role-card reveal">
            <div class="role-icon">👑</div>
            <div class="role-name">System Admin</div>
            <p class="role-desc">Full platform control — user management, system configuration, audit access, and cross-department visibility.</p>
            <div class="role-perms"><span class="rperm">All Access</span><span class="rperm">User Mgmt</span><span class="rperm">Config</span></div>
        </div>
        <div class="role-card reveal">
            <div class="role-icon">🏢</div>
            <div class="role-name">Department Head</div>
            <p class="role-desc">Oversees department tasks, monitors team performance, and manages monthly planning for their operational unit.</p>
            <div class="role-perms"><span class="rperm">Dept Tasks</span><span class="rperm">Reports</span><span class="rperm">Planning</span></div>
        </div>
        <div class="role-card reveal">
            <div class="role-icon">📡</div>
            <div class="role-name">Surveillance Supervisor</div>
            <p class="role-desc">Reviews and approves field operations, validates MPFM data, monitors SLA compliance across all departments.</p>
            <div class="role-perms"><span class="rperm">Review</span><span class="rperm">Approve</span><span class="rperm">Monitor</span></div>
        </div>
        <div class="role-card reveal">
            <div class="role-icon">✍️</div>
            <div class="role-name">Job Officer</div>
            <p class="role-desc">Final approval authority for completed tasks — ensures all technical and safety standards are met before sign-off.</p>
            <div class="role-perms"><span class="rperm">Final Approval</span><span class="rperm">Compliance</span></div>
        </div>
        <div class="role-card reveal">
            <div class="role-icon">🔧</div>
            <div class="role-name">Field Operator</div>
            <p class="role-desc">Executes assigned field tasks, logs MPFM tests, uploads evidence, and updates real-time status from the field.</p>
            <div class="role-perms"><span class="rperm">Execute</span><span class="rperm">Upload</span><span class="rperm">Log</span></div>
        </div>
        <div class="role-card reveal">
            <div class="role-icon">👁️</div>
            <div class="role-name">Read-Only Viewer</div>
            <p class="role-desc">Management and stakeholder access — view dashboards, reports, and KPIs without modification privileges.</p>
            <div class="role-perms"><span class="rperm">View Only</span><span class="rperm">Reports</span></div>
        </div>
    </div>
</section>

<!-- ══ BENEFITS ══ -->
<section class="benefits-section" id="benefits">
    <div class="section-label">Business Impact</div>
    <h2 class="section-title">Measurable ROI — Cost &amp; Time Savings</h2>
    <p class="section-sub">Real operational improvements measured across field sites after system deployment.</p>

    <div class="cost-strip">
        <div class="cost-strip-main">
            <div class="cost-pct">↓18%</div>
            <div class="cost-lbl">Total Operational Cost Reduction</div>
        </div>
        <div class="cost-metrics">
            <div class="cost-m"><div class="cost-m-val">60%</div><div class="cost-m-lbl">Faster Reporting</div></div>
            <div class="cost-m"><div class="cost-m-val">↓70%</div><div class="cost-m-lbl">Overdue Tasks</div></div>
            <div class="cost-m"><div class="cost-m-val">3min</div><div class="cost-m-lbl">Alert Response</div></div>
            <div class="cost-m"><div class="cost-m-val">100%</div><div class="cost-m-lbl">Audit Coverage</div></div>
        </div>
    </div>

    <div class="benefits-grid">
        <div class="benefit-card reveal">
            <div class="benefit-icon">⏱️</div>
            <div class="benefit-title">60% Faster Reporting</div>
            <p class="benefit-desc">Automated data aggregation eliminates manual report compilation — from 4+ hours to under 20 minutes per daily production report.</p>
        </div>
        <div class="benefit-card reveal">
            <div class="benefit-icon">📉</div>
            <div class="benefit-title">↓18% Operational Cost</div>
            <p class="benefit-desc">Fewer redundant site visits, optimized resource allocation, and proactive maintenance scheduling reduce total opex significantly.</p>
        </div>
        <div class="benefit-card reveal">
            <div class="benefit-icon">🎯</div>
            <div class="benefit-title">↓70% Overdue Tasks</div>
            <p class="benefit-desc">SLA enforcement with automated escalation ensures tasks are completed on time — dramatically reducing operational delays and penalties.</p>
        </div>
        <div class="benefit-card reveal">
            <div class="benefit-icon">🔒</div>
            <div class="benefit-title">100% Audit Trail</div>
            <p class="benefit-desc">Every action, approval, and data change is logged with timestamps and user identity — meeting all regulatory and compliance requirements.</p>
        </div>
        <div class="benefit-card reveal">
            <div class="benefit-icon">📈</div>
            <div class="benefit-title">+12% Production Uplift</div>
            <p class="benefit-desc">Better well monitoring, faster intervention decisions, and optimized test scheduling contribute to measurable production improvements.</p>
        </div>
        <div class="benefit-card reveal">
            <div class="benefit-icon">🌐</div>
            <div class="benefit-title">Zero Information Silos</div>
            <p class="benefit-desc">All six departments share a single source of truth — eliminating duplicate work, miscommunication, and data inconsistencies across the field.</p>
        </div>
    </div>

    <table class="compare-table reveal">
        <thead>
            <tr>
                <th>Area</th>
                <th>Before System</th>
                <th>After System</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Daily Production Report</td>
                <td class="td-before">4+ hours manual</td>
                <td class="td-after">Under 20 minutes</td>
            </tr>
            <tr>
                <td>Task Oversight</td>
                <td class="td-before">Spreadsheets / phone calls</td>
                <td class="td-after">Live dashboard — all depts</td>
            </tr>
            <tr>
                <td>MPFM Test Scheduling</td>
                <td class="td-before">Manual, often delayed</td>
                <td class="td-after">Automated SLA enforcement</td>
            </tr>
            <tr>
                <td>Compliance Audit</td>
                <td class="td-before">Days to prepare</td>
                <td class="td-after">Instant export, 100% complete</td>
            </tr>
            <tr>
                <td>Overdue Task Rate</td>
                <td class="td-before">~35% monthly</td>
                <td class="td-after">Under 10%</td>
            </tr>
            <tr>
                <td>Cross-dept Communication</td>
                <td class="td-before">Email / WhatsApp</td>
                <td class="td-after">In-system notifications &amp; log</td>
            </tr>
        </tbody>
    </table>
</section>

<!-- ══ IMAGE BANNER 2 ══ -->
<div class="img-banner">
    <img src="/assets/img/brand/offshore oilfield.jpg" alt="Offshore Oilfield" loading="lazy">
    <div class="banner-text">
        <h3>Enterprise-Grade Security</h3>
        <p>Military-grade encryption, role-based access control, and full audit logging protect your most critical operational data.</p>
    </div>
</div>

<!-- ══ SECURITY ══ -->
<section class="security-section" id="security">
    <div class="section-label">Security &amp; Compliance</div>
    <h2 class="section-title">Built for Enterprise Security</h2>
    <p class="section-sub">Protecting critical operational data with multiple layers of security, access control, and compliance frameworks.</p>
    <div class="sec-grid">
        <div class="sec-card reveal">
            <div class="sec-icon">🔐</div>
            <div class="sec-title">End-to-End Encryption</div>
            <p class="sec-desc">All data in transit and at rest is encrypted using AES-256 and TLS 1.3 — industry-standard protection for your most sensitive operational data.</p>
        </div>
        <div class="sec-card reveal">
            <div class="sec-icon">👤</div>
            <div class="sec-title">Role-Based Access Control</div>
            <p class="sec-desc">Granular permissions ensure each user sees only what they need — preventing unauthorized access to sensitive well data or financial information.</p>
        </div>
        <div class="sec-card reveal">
            <div class="sec-icon">📋</div>
            <div class="sec-title">Full Audit Logging</div>
            <p class="sec-desc">Every user action, data change, and approval is logged with timestamp and IP — providing complete traceability for regulatory compliance.</p>
        </div>
        <div class="sec-card reveal">
            <div class="sec-icon">🛡️</div>
            <div class="sec-title">Multi-Factor Authentication</div>
            <p class="sec-desc">Two-factor authentication for all privileged accounts, with session management and automatic timeout for inactive sessions.</p>
        </div>
        <div class="sec-card reveal">
            <div class="sec-icon">☁️</div>
            <div class="sec-title">Secure Data Infrastructure</div>
            <p class="sec-desc">Hosted on hardened infrastructure with regular backups, disaster recovery plans, and 99.9% uptime SLA for critical operations.</p>
        </div>
        <div class="sec-card reveal">
            <div class="sec-icon">✅</div>
            <div class="sec-title">Compliance Ready</div>
            <p class="sec-desc">Designed to meet ISO 27001 standards, oil &amp; gas industry data governance requirements, and regulatory compliance frameworks.</p>
        </div>
    </div>
</section>

<!-- ══ CAPABILITIES ══ -->
<section class="caps-section" id="capabilities">
    <div class="section-label">Platform Capabilities</div>
    <h2 class="section-title">Everything Your Field Operations Need</h2>
    <p class="section-sub">A complete operational platform covering every aspect of petroleum field management.</p>
    <div class="caps-grid">
        <div class="cap-card reveal">
            <div class="cap-icon">📊</div>
            <div class="cap-title">MPFM Test Management</div>
            <p class="cap-desc">Schedule, execute, upload, and validate multi-phase flow meter tests with full data history and trend analysis.</p>
        </div>
        <div class="cap-card reveal">
            <div class="cap-icon">📅</div>
            <div class="cap-title">Monthly Plan Engine</div>
            <p class="cap-desc">Create and track monthly operational plans for every department with automatic SLA monitoring and alerts.</p>
        </div>
        <div class="cap-card reveal">
            <div class="cap-icon">🔔</div>
            <div class="cap-title">Smart Notifications</div>
            <p class="cap-desc">Real-time alerts for task deadlines, SLA breaches, approval requests, and production anomalies.</p>
        </div>
        <div class="cap-card reveal">
            <div class="cap-icon">📈</div>
            <div class="cap-title">Production Analytics</div>
            <p class="cap-desc">BOPD, GOR, BSW, and efficiency KPIs tracked per well, per department, and platform-wide.</p>
        </div>
        <div class="cap-card reveal">
            <div class="cap-icon">🗺️</div>
            <div class="cap-title">Well &amp; Field Mapping</div>
            <p class="cap-desc">Visual well registry with oil string assignments, tower groupings, and production status overview.</p>
        </div>
        <div class="cap-card reveal">
            <div class="cap-icon">📁</div>
            <div class="cap-title">Document Management</div>
            <p class="cap-desc">Centralized storage for test reports, work orders, and completion certificates with version control.</p>
        </div>
        <div class="cap-card reveal">
            <div class="cap-icon">🤖</div>
            <div class="cap-title">Automated Workflows</div>
            <p class="cap-desc">Task state machines with automated routing, escalation, and approval chains — zero manual hand-offs.</p>
        </div>
        <div class="cap-card reveal">
            <div class="cap-icon">📱</div>
            <div class="cap-title">Mobile-Responsive</div>
            <p class="cap-desc">Full functionality on tablets and phones — field operators can update tasks directly from the well site.</p>
        </div>
    </div>
</section>

<!-- ══ CTA ══ -->
<section class="cta-section">
    <h2 class="cta-title">Ready to Transform Your Field Operations?</h2>
    <p class="cta-sub">Join the field teams already using the platform to optimize production, reduce costs, and achieve full operational transparency.</p>
    <div class="cta-btns">
        @auth
        <a href="{{ url('/dashboard') }}" class="btn-primary">&#9193; Open Dashboard</a>
        @else
        <a href="{{ route('login') }}" class="btn-primary">&#9193; Access Platform</a>
        @endauth
        <a href="#overview" class="btn-secondary">&#8635; Back to Top</a>
    </div>
</section>

<!-- ══ FOOTER ══ -->
<footer>
    <div class="footer-top">
        <div class="footer-brand">
            <img src="/assets/img/brand/logo-white.png" alt="Oil Field Management System">
            <p>Field Operations Management System — unifying petroleum field departments on a single enterprise platform for maximum production efficiency.</p>
        </div>
        <div class="footer-col">
            <h5>Platform</h5>
            <ul>
                <li><a href="#overview">Field Overview</a></li>
                <li><a href="#diagram">Production Flow</a></li>
                <li><a href="#departments">Departments</a></li>
                <li><a href="#workflow">Workflow</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h5>Operations</h5>
            <ul>
                <li><a href="#architecture">Well Architecture</a></li>
                <li><a href="#roles">Access Roles</a></li>
                <li><a href="#security">Security</a></li>
                <li><a href="#capabilities">Capabilities</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h5>Business</h5>
            <ul>
                <li><a href="#benefits">ROI &amp; Benefits</a></li>
                <li><a href="#benefits">Cost Reduction</a></li>
                <li><a href="#benefits">Compliance</a></li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <span>&copy; {{ date('Y') }} Oil Field Management System. All rights reserved.</span>
        <div class="footer-badge">
            <span style="width:7px;height:7px;border-radius:50%;background:var(--green);display:inline-block;box-shadow:0 0 6px var(--green);"></span>
            System Operational
        </div>
    </div>
</footer>

<!-- ══ JAVASCRIPT ══ -->
<script>
// ── Dark mode toggle ──
const html = document.documentElement;
const dmToggle = document.getElementById('dmToggle');
const stored = localStorage.getItem('theme') || 'dark';
html.setAttribute('data-theme', stored);

dmToggle.addEventListener('click', () => {
    const current = html.getAttribute('data-theme');
    const next = current === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-theme', next);
    localStorage.setItem('theme', next);
});

// ── Mobile nav ──
const burger = document.getElementById('burger');
const drawer = document.getElementById('drawer');
burger.addEventListener('click', () => drawer.classList.toggle('open'));
document.addEventListener('click', e => {
    if (!burger.contains(e.target) && !drawer.contains(e.target)) {
        drawer.classList.remove('open');
    }
});

// ── Scroll reveal ──
const revealEls = document.querySelectorAll('.reveal');
const io = new IntersectionObserver(entries => {
    entries.forEach((entry, idx) => {
        if (entry.isIntersecting) {
            const el = entry.target;
            const siblings = Array.from(el.parentElement.querySelectorAll('.reveal'));
            const i = siblings.indexOf(el);
            el.style.transitionDelay = Math.min(i * 65, 450) + 'ms';
            el.classList.add('visible');
            io.unobserve(el);
        }
    });
}, { threshold: 0.1 });
revealEls.forEach(el => io.observe(el));

// ── Interactive production diagram tooltips ──
const tooltipData = {
    wellhead: {
        title: '🏗️ Wellhead',
        rows: [
            ['Pressure', '1,840 PSI'],
            ['Temperature', '65°C'],
            ['Flow Rate', '312 BOPD'],
            ['Status', '🟢 Active']
        ]
    },
    tubing: {
        title: '🔩 Tubing / Downhole',
        rows: [
            ['Depth', '3,240 m'],
            ['Tubing Size', '3½" OD'],
            ['Integrity', '100%'],
            ['Status', '🟢 Operational']
        ]
    },
    mpfm: {
        title: '📊 MPFM Meter',
        rows: [
            ['Oil Flow', '87 BOPD'],
            ['Gas Flow', '12 MMSCFD'],
            ['Water Cut', '18%'],
            ['Status', '🟡 Monitoring']
        ]
    },
    pressure: {
        title: '⏱️ Pressure Sensor',
        rows: [
            ['Upstream', '1,840 PSI'],
            ['Downstream', '1,620 PSI'],
            ['Delta P', '220 PSI'],
            ['Status', '🟢 Normal']
        ]
    },
    temp: {
        title: '🌡️ Temperature Sensor',
        rows: [
            ['Inlet Temp', '78°C'],
            ['Outlet Temp', '52°C'],
            ['Ambient', '38°C'],
            ['Status', '🟢 Normal']
        ]
    },
    separator: {
        title: '⚗️ 3-Phase Separator',
        rows: [
            ['Efficiency', '96%'],
            ['Inlet Flow', '312 BOPD'],
            ['Oil Out', '301 BOPD'],
            ['Status', '🟢 Running']
        ]
    },
    flowline: {
        title: '🌊 Flowline',
        rows: [
            ['Diameter', '6" pipe'],
            ['Flow Velocity', '2.1 m/s'],
            ['Pressure', '850 PSI'],
            ['Status', '🟢 Clear']
        ]
    },
    storage: {
        title: '🛢️ Storage Tank',
        rows: [
            ['Capacity', '5,000 bbl'],
            ['Current Level', '68%'],
            ['Volume', '3,400 bbl'],
            ['Status', '🟢 Normal']
        ]
    },
    control: {
        title: '🖥️ Control Panel (SCADA)',
        rows: [
            ['Uptime', '99.8%'],
            ['Active Alarms', '1'],
            ['Connected', '12 devices'],
            ['Status', '🟢 Online']
        ]
    }
};

const svg = document.getElementById('prodSvg');
const tooltip = document.getElementById('diagTooltip');
const ttTitle = document.getElementById('ttTitle');
const ttBody = document.getElementById('ttBody');

svg.querySelectorAll('.diag-node').forEach(node => {
    node.addEventListener('mouseenter', e => {
        const key = node.getAttribute('data-node');
        const data = tooltipData[key];
        if (!data) return;
        ttTitle.textContent = data.title;
        ttBody.innerHTML = data.rows.map(r =>
            `<div class="tt-row"><span>${r[0]}</span><span>${r[1]}</span></div>`
        ).join('');
        tooltip.classList.add('show');
    });
    node.addEventListener('mousemove', e => {
        const wrap = svg.closest('.diagram-wrap');
        const rect = wrap.getBoundingClientRect();
        let x = e.clientX - rect.left + 12;
        let y = e.clientY - rect.top + 12;
        if (x + 180 > rect.width) x = e.clientX - rect.left - 190;
        tooltip.style.left = x + 'px';
        tooltip.style.top = y + 'px';
    });
    node.addEventListener('mouseleave', () => {
        tooltip.classList.remove('show');
    });
});
</script>
</body>
</html>
