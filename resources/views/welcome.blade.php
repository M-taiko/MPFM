<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ADNOC — Field Operations Management System</title>
    <link rel="icon" href="/assets/img/brand/favicon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
    /* ══════════════════════════════════
       RESET & VARIABLES  — Blue Theme
    ══════════════════════════════════ */
    *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

    :root {
        /* Primary blues */
        --b900: #0a1628;   /* deepest navy */
        --b800: #0f2044;
        --b700: #1a3461;
        --b600: #1e4080;
        --b500: #2563EB;   /* core brand blue */
        --b400: #3b82f6;
        --b300: #60a5fa;
        --b200: #93c5fd;
        --b100: #dbeafe;

        /* Accent — cyan / sky */
        --cyan:    #06b6d4;
        --cyan-lt: #67e8f9;
        --sky:     #0ea5e9;

        /* Neutral */
        --slate:   #080f1e;
        --slate2:  #0d1b35;
        --slate3:  #162040;
        --border:  rgba(255,255,255,.07);
        --card:    rgba(255,255,255,.035);
        --muted:   #64748b;
        --sub:     #94a3b8;
    }

    html { scroll-behavior: smooth; }

    body {
        font-family: 'Inter', sans-serif;
        background: var(--slate);
        color: #f1f5f9;
        line-height: 1.65;
        overflow-x: hidden;
    }

    ::-webkit-scrollbar { width: 4px; }
    ::-webkit-scrollbar-track { background: var(--slate); }
    ::-webkit-scrollbar-thumb { background: var(--b500); border-radius: 4px; }

    img { display: block; max-width: 100%; }

    /* ══ NAV ══ */
    nav {
        position: fixed; top: 0; left: 0; right: 0; z-index: 300;
        height: 68px;
        display: flex; align-items: center; justify-content: space-between;
        padding: 0 5%;
        background: rgba(8,15,30,.93);
        backdrop-filter: blur(22px);
        border-bottom: 1px solid rgba(37,99,235,.2);
    }
    .nav-logo img { height: 52px; width: auto; object-fit: contain; }
    .nav-links { display: flex; gap: 28px; list-style: none; }
    .nav-links a {
        color: #94a3b8; text-decoration: none;
        font-size: .85rem; font-weight: 500;
        transition: color .2s;
    }
    .nav-links a:hover { color: #fff; }
    .nav-cta {
        display: inline-flex; align-items: center; gap: 6px;
        background: var(--b500); color: #fff;
        padding: 9px 22px; border-radius: 8px;
        text-decoration: none; font-size: .85rem; font-weight: 700;
        transition: background .2s, transform .15s;
        flex-shrink: 0;
        box-shadow: 0 4px 20px rgba(37,99,235,.4);
    }
    .nav-cta:hover { background: var(--b600); transform: translateY(-1px); }
    .nav-burger {
        display: none; flex-direction: column; gap: 5px;
        background: none; border: none; cursor: pointer; padding: 4px;
    }
    .nav-burger span {
        display: block; width: 24px; height: 2px;
        background: #f1f5f9; border-radius: 2px; transition: all .3s;
    }
    .nav-drawer {
        display: none; position: fixed; top: 68px; left: 0; right: 0; z-index: 299;
        background: rgba(8,15,30,.97);
        border-bottom: 1px solid rgba(37,99,235,.2);
        padding: 20px 5%; flex-direction: column; gap: 4px;
    }
    .nav-drawer.open { display: flex; }
    .nav-drawer a {
        color: #94a3b8; text-decoration: none; font-size: .95rem; font-weight: 500;
        padding: 10px 0; border-bottom: 1px solid var(--border); transition: color .2s;
    }
    .nav-drawer a:last-child { border-bottom: none; }
    .nav-drawer a:hover { color: #fff; }
    .nav-drawer .drawer-cta {
        margin-top: 12px; text-align: center;
        background: var(--b500); color: #fff;
        padding: 12px; border-radius: 8px; font-weight: 700;
    }

    /* ══ HERO ══ */
    .hero {
        min-height: 100vh;
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        text-align: center; padding: 100px 5% 72px;
        position: relative; overflow: hidden;
    }
    .hero-bg-img {
        position: absolute; inset: 0; z-index: 0;
        background-image: url('/assets/img/brand/oilfield oprations.webp');
        background-size: cover; background-position: center 50%;
        opacity: .14; filter: saturate(.5) hue-rotate(10deg);
    }
    .hero-overlay {
        position: absolute; inset: 0; z-index: 1;
        background:
            linear-gradient(to bottom,
                rgba(8,15,30,.82) 0%,
                rgba(8,15,30,.38) 45%,
                rgba(8,15,30,.65) 75%,
                rgba(8,15,30,.97) 100%),
            radial-gradient(ellipse 70% 55% at 50% 0%,
                rgba(37,99,235,.28) 0%, transparent 65%),
            radial-gradient(ellipse 40% 40% at 80% 80%,
                rgba(6,182,212,.12) 0%, transparent 60%);
    }
    .hero-inner { position: relative; z-index: 2; width: 100%; }

    .hero-logo { margin-bottom: 28px; }
    .hero-logo img {
        height: 120px; width: auto; object-fit: contain; margin: 0 auto;
        filter: drop-shadow(0 8px 36px rgba(37,99,235,.6)) brightness(1.08);
    }
    .hero-eyebrow {
        display: inline-flex; align-items: center; gap: 8px;
        background: rgba(37,99,235,.12); border: 1px solid rgba(37,99,235,.35);
        color: var(--b300); padding: 6px 18px; border-radius: 50px;
        font-size: .76rem; font-weight: 700; letter-spacing: 1px;
        text-transform: uppercase; margin-bottom: 24px;
    }
    .hero h1 {
        font-size: clamp(2.2rem, 5.5vw, 4.8rem);
        font-weight: 900; line-height: 1.08; letter-spacing: -1.5px; margin-bottom: 18px;
    }
    .grad-blue {
        background: linear-gradient(135deg, var(--b300) 0%, var(--b200) 50%, var(--cyan-lt) 100%);
        -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
    }
    .grad-cyan {
        background: linear-gradient(135deg, var(--cyan) 0%, var(--cyan-lt) 100%);
        -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
    }
    .hero-sub {
        font-size: clamp(.95rem, 2vw, 1.15rem);
        color: var(--sub); max-width: 640px; margin: 0 auto 40px; font-weight: 400;
    }
    .hero-btns { display: flex; gap: 14px; justify-content: center; flex-wrap: wrap; }
    .btn-primary {
        display: inline-flex; align-items: center; gap: 8px;
        background: var(--b500); color: #fff;
        padding: 13px 30px; border-radius: 10px;
        text-decoration: none; font-weight: 700; font-size: .93rem;
        box-shadow: 0 6px 28px rgba(37,99,235,.45);
        transition: background .2s, transform .2s, box-shadow .2s;
    }
    .btn-primary:hover { background: var(--b600); transform: translateY(-2px); box-shadow: 0 10px 40px rgba(37,99,235,.6); }
    .btn-outline {
        display: inline-flex; align-items: center; gap: 8px;
        background: rgba(255,255,255,.05); border: 1px solid rgba(255,255,255,.15);
        color: #fff; padding: 13px 30px; border-radius: 10px;
        text-decoration: none; font-weight: 600; font-size: .93rem;
        transition: background .2s, transform .2s;
    }
    .btn-outline:hover { background: rgba(255,255,255,.10); transform: translateY(-2px); }

    /* hero stats */
    .stats-bar {
        display: flex; flex-wrap: wrap; justify-content: center;
        gap: 0; margin-top: 60px; padding-top: 44px;
        border-top: 1px solid rgba(37,99,235,.2); width: 100%;
    }
    .stat-item {
        flex: 1; min-width: 120px; text-align: center;
        padding: 12px 20px; border-right: 1px solid rgba(37,99,235,.15);
    }
    .stat-item:last-child { border-right: none; }
    .stat-num {
        font-size: clamp(1.8rem,3vw,2.6rem); font-weight: 900; letter-spacing: -1px;
        background: linear-gradient(135deg, var(--b300), var(--cyan));
        -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
    }
    .stat-lbl { color: var(--muted); font-size: .75rem; font-weight: 500; margin-top: 2px; }

    /* ══ PHOTO STRIP ══ */
    .photo-strip {
        display: grid; grid-template-columns: 1.8fr 1fr 1fr 1fr;
        gap: 3px; height: 260px; overflow: hidden;
    }
    .ps-item { position: relative; overflow: hidden; }
    .ps-item img {
        width: 100%; height: 100%; object-fit: cover;
        filter: brightness(.55) saturate(.65);
        transition: filter .4s, transform .5s;
    }
    .ps-item:hover img { filter: brightness(.78) saturate(.95); transform: scale(1.04); }
    .ps-label {
        position: absolute; bottom: 12px; left: 14px;
        background: rgba(8,15,30,.78); border: 1px solid rgba(37,99,235,.3);
        color: #f1f5f9; font-size: .72rem; font-weight: 700;
        padding: 4px 11px; border-radius: 6px; backdrop-filter: blur(8px);
    }

    /* ══ SECTION BASE ══ */
    section { padding: 88px 5%; }
    .sec-alt { background: var(--slate2); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); }
    .sec-head { margin-bottom: 56px; }
    .sec-head.center { text-align: center; }
    .sec-head.center .sec-sub { margin: 12px auto 0; }
    .sec-label {
        display: inline-flex; align-items: center; gap: 6px;
        color: var(--cyan); font-size: .72rem; font-weight: 700;
        letter-spacing: 2px; text-transform: uppercase; margin-bottom: 12px;
    }
    .sec-title {
        font-size: clamp(1.8rem,3.5vw,2.9rem);
        font-weight: 800; letter-spacing: -.6px; line-height: 1.15; margin-bottom: 12px;
    }
    .sec-sub { color: var(--muted); font-size: .97rem; max-width: 560px; line-height: 1.75; }

    /* ══ DEPARTMENTS ══ */
    .dept-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px,1fr));
        gap: 20px;
    }
    .dept-card {
        background: var(--card); border: 1px solid var(--border);
        border-radius: 18px; overflow: hidden;
        transition: transform .28s, box-shadow .28s, border-color .28s;
    }
    .dept-card:hover { transform: translateY(-5px); box-shadow: 0 20px 56px rgba(0,0,0,.4); }
    .dept-img { width: 100%; height: 170px; object-fit: cover; filter: brightness(.55) saturate(.65); transition: filter .4s, transform .4s; }
    .dept-card:hover .dept-img { filter: brightness(.78) saturate(.9); transform: scale(1.03); }
    .dept-body { padding: 22px 22px 26px; }
    .dept-accent-bar { height: 3px; width: 40px; border-radius: 2px; margin-bottom: 14px; }
    .dept-body .d-icon { font-size: 2rem; margin-bottom: 10px; display: block; }
    .dept-body h3 { font-size: 1.05rem; font-weight: 800; margin-bottom: 8px; }
    .dept-body .desc { color: var(--muted); font-size: .85rem; line-height: 1.7; margin-bottom: 14px; }
    .dept-impact {
        background: rgba(37,99,235,.06); border: 1px solid rgba(37,99,235,.15);
        border-radius: 10px; padding: 11px 14px; margin-bottom: 13px;
    }
    .dept-impact-title {
        font-size: .68rem; font-weight: 700; letter-spacing: .8px; text-transform: uppercase;
        color: var(--b300); margin-bottom: 7px;
    }
    .dept-impact-items { display: flex; flex-wrap: wrap; gap: 5px; }
    .dept-impact-item {
        background: rgba(37,99,235,.10); border: 1px solid rgba(37,99,235,.18);
        color: var(--b200); font-size: .7rem; font-weight: 600;
        padding: 2px 9px; border-radius: 50px;
    }
    .dept-tags { display: flex; flex-wrap: wrap; gap: 6px; }
    .dept-tag {
        font-size: .7rem; font-weight: 600; padding: 3px 10px; border-radius: 50px;
        background: rgba(6,182,212,.08); color: var(--cyan); border: 1px solid rgba(6,182,212,.2);
    }

    /* dept border accent colors */
    .dc-1:hover { border-color: rgba(37,99,235,.5); }
    .dc-2:hover { border-color: rgba(6,182,212,.5); }
    .dc-3:hover { border-color: rgba(14,165,233,.5); }
    .dc-4:hover { border-color: rgba(56,189,248,.5); }
    .dc-5:hover { border-color: rgba(99,102,241,.5); }
    .dc-6:hover { border-color: rgba(34,211,238,.5); }

    .da-1 { background: var(--b500); }
    .da-2 { background: var(--cyan); }
    .da-3 { background: var(--sky); }
    .da-4 { background: #38bdf8; }
    .da-5 { background: #6366f1; }
    .da-6 { background: #22d3ee; }

    /* ══════════════════════════════════════════════
       WELL DIAGRAM  (fully detailed)
    ══════════════════════════════════════════════ */
    .diag-section {
        background:
            radial-gradient(ellipse 80% 50% at 50% 0%, rgba(37,99,235,.12) 0%, transparent 60%),
            var(--slate);
    }
    .diag-wrap {
        background: rgba(37,99,235,.04);
        border: 1px solid rgba(37,99,235,.18);
        border-radius: 22px; overflow: hidden; margin-top: 48px;
    }
    /* title bar */
    .diag-bar {
        background: rgba(37,99,235,.08); border-bottom: 1px solid rgba(37,99,235,.18);
        padding: 14px 22px;
        display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;
    }
    .diag-bar-left { display: flex; align-items: center; gap: 10px; }
    .diag-dot { width: 10px; height: 10px; border-radius: 50%; }
    .diag-bar-title { font-size: .8rem; font-weight: 700; color: var(--b300); }
    .diag-bar-badge {
        font-size: .68rem; font-weight: 700; padding: 2px 10px; border-radius: 50px;
        background: rgba(6,182,212,.12); border: 1px solid rgba(6,182,212,.25); color: var(--cyan);
    }

    .diag-body { padding: 32px 24px 28px; }

    /* ─ LAYER 1 — input dept boxes ─ */
    .diag-layer { width: 100%; }
    .diag-layer-label {
        display: flex; align-items: center; gap: 8px;
        font-size: .68rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase;
        color: var(--muted); margin-bottom: 14px;
    }
    .diag-layer-label::after {
        content: ''; flex: 1; height: 1px; background: var(--border);
    }

    .dept-boxes { display: grid; grid-template-columns: repeat(6,1fr); gap: 10px; }

    .dept-box {
        border: 1px solid var(--border);
        border-radius: 12px; padding: 14px 10px 12px;
        text-align: center; background: rgba(255,255,255,.025);
        position: relative; transition: all .25s;
        cursor: default;
    }
    .dept-box:hover { background: rgba(37,99,235,.08); border-color: rgba(37,99,235,.35); transform: translateY(-2px); }
    .dept-box .db-icon { font-size: 1.5rem; margin-bottom: 7px; }
    .dept-box h4 { font-size: .72rem; font-weight: 800; margin-bottom: 4px; white-space: nowrap; }
    .dept-box .db-role { font-size: .62rem; color: var(--muted); line-height: 1.4; margin-bottom: 6px; }
    .dept-box .db-tag {
        display: inline-block; font-size: .6rem; font-weight: 700;
        padding: 2px 7px; border-radius: 50px;
        background: rgba(37,99,235,.12); border: 1px solid rgba(37,99,235,.22); color: var(--b300);
    }
    /* border-top accents per dept */
    .db-blue   { border-top: 3px solid var(--b500); }
    .db-cyan   { border-top: 3px solid var(--cyan); }
    .db-sky    { border-top: 3px solid var(--sky); }
    .db-lblue  { border-top: 3px solid #38bdf8; }
    .db-indigo { border-top: 3px solid #6366f1; }
    .db-teal   { border-top: 3px solid #22d3ee; }

    /* ─ SVG connector layer ─ */
    .diag-svg-wrap { width: 100%; padding: 2px 0; }
    .diag-svg-wrap svg { width: 100%; overflow: visible; display: block; }

    /* ─ LAYER 2 — platform core ─ */
    .platform-core {
        background: linear-gradient(135deg, rgba(37,99,235,.14), rgba(6,182,212,.08));
        border: 2px solid rgba(37,99,235,.4);
        border-radius: 16px; padding: 22px 28px;
        display: flex; align-items: center; justify-content: space-between;
        flex-wrap: wrap; gap: 18px;
        position: relative;
    }
    .platform-core::before {
        content: 'LIVE PLATFORM';
        position: absolute; top: -10px; left: 22px;
        background: var(--slate); padding: 0 8px;
        font-size: .6rem; font-weight: 800; letter-spacing: 1.5px;
        color: var(--b400);
    }
    .platform-title { }
    .platform-title h3 { font-size: .95rem; font-weight: 800; margin-bottom: 4px; }
    .platform-title p { font-size: .75rem; color: var(--muted); }
    .platform-kpis { display: flex; gap: 20px; flex-wrap: wrap; }
    .pk {
        text-align: center; padding: 10px 16px;
        background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.08);
        border-radius: 10px; min-width: 80px;
    }
    .pk-val { font-size: 1rem; font-weight: 900; color: var(--b300); }
    .pk-lbl { font-size: .6rem; color: var(--muted); font-weight: 600; margin-top: 2px; }

    /* ─ mid connector (platform → well) ─ */
    .mid-connector {
        display: flex; justify-content: center; align-items: center;
        padding: 4px 0; gap: 40px;
    }
    .mid-line { display: flex; flex-direction: column; align-items: center; gap: 0; }
    .mid-line-bar { width: 2px; height: 28px; background: linear-gradient(to bottom, rgba(37,99,235,.7), rgba(6,182,212,.4)); }
    .mid-arrow { font-size: .75rem; color: var(--b400); }

    /* ─ LAYER 3 — well cluster ─ */
    .well-cluster { display: grid; grid-template-columns: repeat(3,1fr); gap: 12px; }
    .well-card {
        border: 1px solid rgba(37,99,235,.25);
        border-radius: 14px; overflow: hidden;
        background: rgba(37,99,235,.05);
        transition: box-shadow .2s, border-color .2s;
    }
    .well-card:hover { border-color: rgba(37,99,235,.5); box-shadow: 0 8px 28px rgba(37,99,235,.15); }
    .well-card-head {
        background: rgba(37,99,235,.1); padding: 10px 14px;
        border-bottom: 1px solid rgba(37,99,235,.15);
        display: flex; align-items: center; gap: 8px;
    }
    .well-card-head .wh-icon { font-size: 1.1rem; }
    .well-card-head h4 { font-size: .78rem; font-weight: 800; }
    .well-card-head .wh-status {
        margin-left: auto; font-size: .62rem; font-weight: 700;
        padding: 2px 8px; border-radius: 50px;
        background: rgba(34,197,94,.12); border: 1px solid rgba(34,197,94,.25); color: #86efac;
    }
    .well-card-body { padding: 12px 14px; }
    .well-kpi-row { display: flex; gap: 8px; flex-wrap: wrap; }
    .wkpi {
        flex: 1; min-width: 56px; text-align: center;
        background: rgba(255,255,255,.03); border: 1px solid rgba(255,255,255,.07);
        border-radius: 8px; padding: 8px 6px;
    }
    .wkpi-val { font-size: .85rem; font-weight: 800; color: var(--b300); }
    .wkpi-lbl { font-size: .55rem; color: var(--muted); font-weight: 600; margin-top: 2px; }
    .well-dept-badges { display: flex; flex-wrap: wrap; gap: 4px; margin-top: 9px; }
    .wdb {
        font-size: .6rem; font-weight: 700; padding: 2px 7px; border-radius: 50px;
        background: rgba(6,182,212,.08); border: 1px solid rgba(6,182,212,.18); color: var(--cyan);
    }

    /* ─ OUTPUT ARROW ─ */
    .output-connector { display: flex; justify-content: center; padding: 4px 0; }
    .out-line { display: flex; flex-direction: column; align-items: center; gap: 0; }
    .out-bar { width: 3px; height: 36px; background: linear-gradient(to bottom, rgba(6,182,212,.8), rgba(6,182,212,.1)); }
    .out-arrow { color: var(--cyan); font-size: .8rem; }

    /* ─ LAYER 4 — field output ─ */
    .field-output {
        background: linear-gradient(135deg, rgba(6,182,212,.12), rgba(37,99,235,.07));
        border: 1px solid rgba(6,182,212,.3);
        border-radius: 16px; padding: 20px 24px;
    }
    .field-output-top {
        display: flex; align-items: center; justify-content: space-between;
        flex-wrap: wrap; gap: 12px; margin-bottom: 16px;
    }
    .field-output-top h3 { font-size: .92rem; font-weight: 800; color: var(--cyan-lt); }
    .fo-live {
        display: flex; align-items: center; gap: 6px;
        font-size: .68rem; font-weight: 700; color: #86efac;
    }
    .fo-live-dot { width: 7px; height: 7px; border-radius: 50%; background: #22c55e; animation: pulse 2s infinite; }
    @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.3} }
    .field-kpis { display: grid; grid-template-columns: repeat(auto-fit, minmax(110px,1fr)); gap: 10px; }
    .fkpi {
        background: rgba(255,255,255,.04); border: 1px solid rgba(6,182,212,.15);
        border-radius: 10px; padding: 12px 14px; text-align: center;
    }
    .fkpi-val { font-size: 1.15rem; font-weight: 900; color: var(--cyan-lt); letter-spacing: -.5px; }
    .fkpi-delta {
        font-size: .62rem; font-weight: 700; padding: 1px 6px; border-radius: 50px;
        background: rgba(34,197,94,.12); color: #86efac;
        display: inline-block; margin: 3px 0;
    }
    .fkpi-lbl { font-size: .62rem; color: var(--muted); font-weight: 600; }

    /* ─ LEGEND ─ */
    .diag-legend {
        display: flex; flex-wrap: wrap; gap: 16px; margin-top: 22px;
        padding-top: 18px; border-top: 1px solid var(--border);
        justify-content: center;
    }
    .legend-item { display: flex; align-items: center; gap: 6px; font-size: .7rem; color: var(--muted); }
    .legend-line { width: 20px; height: 2px; border-radius: 1px; }
    .legend-dash { width: 20px; height: 0; border-top: 2px dashed rgba(255,255,255,.2); }
    .legend-dot { width: 8px; height: 8px; border-radius: 50%; }

    /* ══ IMAGE BANNER ══ */
    .img-banner {
        position: relative; height: 300px; overflow: hidden;
        display: flex; align-items: center; justify-content: center;
    }
    .img-banner-bg {
        position: absolute; inset: 0;
        background-size: cover; background-position: center;
        filter: brightness(.38) saturate(.55);
    }
    .img-banner-overlay {
        position: absolute; inset: 0;
        background: linear-gradient(to right,
            rgba(8,15,30,.88) 0%, rgba(8,15,30,.35) 50%, rgba(8,15,30,.75) 100%);
    }
    .img-banner-content { position: relative; z-index: 2; text-align: center; padding: 0 5%; }
    .img-banner-content h2 {
        font-size: clamp(1.5rem,3.5vw,2.8rem);
        font-weight: 800; letter-spacing: -.4px; margin-bottom: 12px;
    }
    .img-banner-content p { color: var(--sub); font-size: .97rem; max-width: 540px; margin: 0 auto; }

    /* ══ WORKFLOW ══ */
    .flow-wrap {
        display: grid; grid-template-columns: repeat(6,1fr);
        border: 1px solid var(--border); border-radius: 18px; overflow: hidden;
    }
    .flow-step {
        padding: 30px 20px; border-right: 1px solid var(--border);
        background: var(--card); transition: background .2s;
    }
    .flow-step:last-child { border-right: none; }
    .flow-step:hover { background: rgba(37,99,235,.06); }
    .flow-num {
        width: 34px; height: 34px; border-radius: 9px;
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: .82rem; margin-bottom: 14px;
        background: rgba(37,99,235,.15); color: var(--b300);
    }
    .flow-step .f-icon { font-size: 1.5rem; display: block; margin-bottom: 10px; }
    .flow-step h3 { font-size: .85rem; font-weight: 700; margin-bottom: 7px; }
    .flow-step p { color: var(--muted); font-size: .77rem; line-height: 1.6; }

    /* ══ ROLES ══ */
    .roles-grid { display: grid; grid-template-columns: repeat(auto-fit,minmax(260px,1fr)); gap: 16px; }
    .role-card {
        background: var(--card); border: 1px solid var(--border);
        border-radius: 14px; padding: 22px 20px;
        display: flex; gap: 16px; transition: transform .2s, border-color .2s;
    }
    .role-card:hover { transform: translateY(-3px); border-color: rgba(37,99,235,.4); }
    .role-icon-box {
        width: 46px; height: 46px; border-radius: 11px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.4rem; background: rgba(37,99,235,.12);
    }
    .role-card h3 { font-size: .88rem; font-weight: 700; margin-bottom: 5px; }
    .role-card p { color: var(--muted); font-size: .8rem; line-height: 1.6; }

    /* ══ BENEFITS ══ */
    .cost-strip { display: grid; grid-template-columns: repeat(auto-fit,minmax(150px,1fr)); gap: 14px; }
    .cost-card {
        background: linear-gradient(135deg, rgba(37,99,235,.10), rgba(37,99,235,.04));
        border: 1px solid rgba(37,99,235,.22); border-radius: 14px;
        padding: 22px 18px; text-align: center;
    }
    .cost-val {
        font-size: 2rem; font-weight: 900; letter-spacing: -1px;
        background: linear-gradient(135deg, var(--b300), var(--cyan));
        -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
    }
    .cost-lbl { color: var(--muted); font-size: .75rem; font-weight: 600; margin-top: 4px; }
    .benefits-grid { display: grid; grid-template-columns: repeat(auto-fit,minmax(280px,1fr)); gap: 18px; }
    .benefit-card {
        background: var(--card); border: 1px solid var(--border);
        border-radius: 16px; padding: 26px 22px;
        transition: transform .25s, box-shadow .25s;
    }
    .benefit-card:hover { transform: translateY(-4px); box-shadow: 0 16px 48px rgba(0,0,0,.35); }
    .benefit-top { display: flex; align-items: center; gap: 13px; margin-bottom: 12px; }
    .benefit-icon {
        width: 48px; height: 48px; border-radius: 12px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center; font-size: 1.4rem;
        background: rgba(37,99,235,.12);
    }
    .benefit-card h3 { font-size: .92rem; font-weight: 700; }
    .benefit-card p { color: var(--muted); font-size: .83rem; line-height: 1.7; }
    .saving-tag {
        display: inline-block; margin-top: 13px;
        background: rgba(6,182,212,.10); border: 1px solid rgba(6,182,212,.22);
        color: var(--cyan-lt); padding: 3px 12px; border-radius: 50px;
        font-size: .72rem; font-weight: 700;
    }
    /* compare table */
    .compare-wrap { background: var(--card); border: 1px solid var(--border); border-radius: 18px; overflow: hidden; margin-top: 48px; overflow-x: auto; }
    .compare-table { width: 100%; border-collapse: collapse; min-width: 540px; }
    .compare-table th { padding: 15px 18px; text-align: left; font-size: .76rem; font-weight: 700; letter-spacing: .5px; text-transform: uppercase; border-bottom: 1px solid var(--border); }
    .compare-table th.old-h { color: #f87171; background: rgba(239,68,68,.06); }
    .compare-table th.new-h { color: #34d399; background: rgba(16,185,129,.06); }
    .compare-table td { padding: 12px 18px; font-size: .83rem; border-bottom: 1px solid var(--border); vertical-align: middle; }
    .compare-table tr:last-child td { border-bottom: none; }
    .compare-table td.aspect { font-weight: 600; color: #94a3b8; white-space: nowrap; }
    .compare-table td.old { color: #f87171; background: rgba(239,68,68,.04); }
    .compare-table td.new { color: #34d399; background: rgba(16,185,129,.04); }

    /* ══ SECURITY SECTION ══ */
    .sec-security {
        background:
            radial-gradient(ellipse 60% 45% at 50% 50%, rgba(37,99,235,.08) 0%, transparent 65%),
            var(--slate2);
        border-top: 1px solid var(--border); border-bottom: 1px solid var(--border);
    }
    .security-grid { display: grid; grid-template-columns: repeat(auto-fit,minmax(250px,1fr)); gap: 18px; }
    .sec-card {
        background: rgba(37,99,235,.05); border: 1px solid rgba(37,99,235,.18);
        border-radius: 16px; padding: 26px 22px;
        transition: transform .25s, border-color .25s, box-shadow .25s;
    }
    .sec-card:hover { transform: translateY(-3px); border-color: rgba(37,99,235,.4); box-shadow: 0 12px 40px rgba(37,99,235,.12); }
    .sec-card .sc-icon { font-size: 2rem; margin-bottom: 14px; display: block; }
    .sec-card h3 { font-size: .92rem; font-weight: 700; color: var(--b200); margin-bottom: 8px; }
    .sec-card p { color: var(--muted); font-size: .83rem; line-height: 1.7; }
    .sec-badge {
        display: inline-block; margin-top: 12px;
        background: rgba(37,99,235,.10); border: 1px solid rgba(37,99,235,.22);
        color: var(--b300); padding: 2px 11px; border-radius: 50px;
        font-size: .68rem; font-weight: 700;
    }

    /* ══ AI ══ */
    .ai-section {
        background:
            radial-gradient(ellipse 60% 50% at 50% 50%, rgba(99,102,241,.10) 0%, transparent 65%),
            var(--slate);
    }
    .ai-badge {
        display: inline-block;
        background: rgba(99,102,241,.14); border: 1px solid rgba(99,102,241,.32);
        color: #c7d2fe; padding: 4px 14px; border-radius: 50px;
        font-size: .72rem; font-weight: 700; letter-spacing: .8px;
        text-transform: uppercase; margin-bottom: 18px;
    }
    .ai-grid { display: grid; grid-template-columns: repeat(auto-fit,minmax(240px,1fr)); gap: 16px; margin-top: 48px; }
    .ai-card {
        background: rgba(99,102,241,.06); border: 1px solid rgba(99,102,241,.18);
        border-radius: 15px; padding: 26px 22px;
        transition: transform .25s, border-color .25s, box-shadow .25s;
    }
    .ai-card:hover { transform: translateY(-4px); border-color: rgba(99,102,241,.45); box-shadow: 0 14px 44px rgba(99,102,241,.15); }
    .ai-card .ai-icon { font-size: 1.9rem; margin-bottom: 14px; display: block; }
    .ai-card h3 { font-size: .9rem; font-weight: 700; color: #c7d2fe; margin-bottom: 8px; }
    .ai-card p { color: var(--muted); font-size: .82rem; line-height: 1.7; }

    /* ══ CAPABILITIES (replaces tech stack) ══ */
    .cap-grid { display: grid; grid-template-columns: repeat(auto-fit,minmax(230px,1fr)); gap: 14px; margin-top: 44px; }
    .cap-card {
        background: var(--card); border: 1px solid var(--border);
        border-radius: 12px; padding: 20px 18px;
        display: flex; gap: 14px; align-items: flex-start;
        transition: border-color .2s;
    }
    .cap-card:hover { border-color: rgba(37,99,235,.35); }
    .cap-icon { font-size: 1.4rem; flex-shrink: 0; margin-top: 2px; }
    .cap-card h4 { font-size: .85rem; font-weight: 700; margin-bottom: 4px; color: var(--b200); }
    .cap-card p { color: var(--muted); font-size: .78rem; line-height: 1.55; }

    /* ══ CTA ══ */
    .cta-box {
        background: linear-gradient(135deg, rgba(37,99,235,.13), rgba(6,182,212,.06));
        border: 1px solid rgba(37,99,235,.28);
        border-radius: 22px; padding: 72px 32px;
        text-align: center; max-width: 820px; margin: 0 auto;
    }
    .cta-box h2 { font-size: clamp(1.8rem,3.5vw,2.8rem); font-weight: 800; margin-bottom: 14px; }
    .cta-box p { color: var(--muted); font-size: .97rem; margin-bottom: 36px; max-width: 520px; margin-left: auto; margin-right: auto; }

    /* ══ FOOTER ══ */
    footer { background: var(--slate2); border-top: 1px solid var(--border); padding: 44px 5%; }
    .footer-inner { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 24px; }
    .footer-logo img { height: 38px; width: auto; object-fit: contain; opacity: .82; }
    .footer-copy { color: #374151; font-size: .78rem; margin-top: 6px; }
    .footer-dev { text-align: right; }
    .footer-dev-label { color: #374151; font-size: .76rem; margin-bottom: 3px; }
    .footer-dev a { color: var(--cyan-lt); text-decoration: none; font-weight: 700; font-size: .97rem; transition: color .2s; }
    .footer-dev a:hover { color: #fff; }
    .footer-dev p { color: #374151; font-size: .72rem; margin-top: 2px; }

    /* ══ REVEAL ══ */
    .reveal { opacity: 0; transform: translateY(26px); transition: opacity .65s ease, transform .65s ease; }
    .reveal.visible { opacity: 1; transform: translateY(0); }

    /* ══ RESPONSIVE ══ */
    @media (max-width: 1200px) {
        .dept-boxes { grid-template-columns: repeat(3,1fr); }
        .well-cluster { grid-template-columns: repeat(2,1fr); }
    }
    @media (max-width: 1024px) {
        .flow-wrap { grid-template-columns: repeat(3,1fr); }
        .flow-step { border-bottom: 1px solid var(--border); }
        .flow-step:nth-child(3) { border-right: none; }
        .flow-step:nth-child(5),
        .flow-step:nth-child(6) { border-bottom: none; }
    }
    @media (max-width: 860px) {
        .nav-links { display: none; }
        .nav-burger { display: flex; }
        .hero-logo img { height: 90px; }
        .photo-strip { grid-template-columns: 1fr 1fr; }
        .ps-item:nth-child(3),
        .ps-item:nth-child(4) { display: none; }
        .dept-boxes { grid-template-columns: repeat(2,1fr); }
        .well-cluster { grid-template-columns: 1fr; }
        .platform-kpis { gap: 10px; }
        .flow-wrap { grid-template-columns: repeat(2,1fr); }
        .flow-step:nth-child(2),
        .flow-step:nth-child(4) { border-right: none; }
        .flow-step:nth-child(3) { border-right: 1px solid var(--border); }
    }
    @media (max-width: 600px) {
        nav { padding: 0 4%; }
        section { padding: 64px 4%; }
        .hero { padding: 90px 4% 60px; }
        .hero-logo img { height: 74px; }
        .hero h1 { letter-spacing: -.5px; }
        .stats-bar { flex-direction: column; }
        .stat-item { border-right: none; border-bottom: 1px solid rgba(37,99,235,.15); }
        .stat-item:last-child { border-bottom: none; }
        .photo-strip { grid-template-columns: 1fr; height: 200px; }
        .ps-item:not(:first-child) { display: none; }
        .dept-boxes { grid-template-columns: repeat(2,1fr); }
        .dept-grid { grid-template-columns: 1fr; }
        .flow-wrap { grid-template-columns: 1fr; border-radius: 14px; }
        .flow-step { border-right: none; border-bottom: 1px solid var(--border); }
        .flow-step:last-child { border-bottom: none; }
        .img-banner { height: 220px; }
        .cost-strip { grid-template-columns: repeat(2,1fr); }
        footer .footer-inner { flex-direction: column; text-align: center; }
        .footer-dev { text-align: center; }
        .diag-body { padding: 18px 12px; }
        .platform-core { flex-direction: column; }
        .field-kpis { grid-template-columns: repeat(2,1fr); }
        .compare-table th, .compare-table td { font-size: .75rem; padding: 9px 10px; }
    }
    </style>
</head>
<body>

{{-- ══════ NAV ══════ --}}
<nav>
    <a href="/" class="nav-logo">
        <img src="/assets/img/brand/logo-white.png" alt="ADNOC">
    </a>
    <ul class="nav-links">
        <li><a href="#departments">Departments</a></li>
        <li><a href="#diagram">Well Architecture</a></li>
        <li><a href="#workflow">Workflow</a></li>
        <li><a href="#benefits">Cost Savings</a></li>
        <li><a href="#ai">AI Features</a></li>
    </ul>
    @auth
        <a href="{{ url('/dashboard') }}" class="nav-cta">🚀 Dashboard</a>
    @else
        <a href="{{ route('login') }}" class="nav-cta">🔐 Sign In</a>
    @endauth
    <button class="nav-burger" id="burger" aria-label="Menu">
        <span></span><span></span><span></span>
    </button>
</nav>

<div class="nav-drawer" id="drawer">
    <a href="#departments"  onclick="closeDrawer()">🏗️ Departments</a>
    <a href="#diagram"      onclick="closeDrawer()">🔩 Well Architecture</a>
    <a href="#workflow"     onclick="closeDrawer()">🔄 Workflow</a>
    <a href="#benefits"     onclick="closeDrawer()">💰 Cost Savings</a>
    <a href="#ai"           onclick="closeDrawer()">🤖 AI Features</a>
    @auth
        <a href="{{ url('/dashboard') }}" class="drawer-cta">🚀 Go to Dashboard</a>
    @else
        <a href="{{ route('login') }}"    class="drawer-cta">🔐 Sign In</a>
    @endauth
</div>

{{-- ══════ HERO ══════ --}}
<section class="hero">
    <div class="hero-bg-img"></div>
    <div class="hero-overlay"></div>
    <div class="hero-inner">
        <div class="hero-logo reveal">
            <img src="/assets/img/brand/logo-white.png" alt="ADNOC">
        </div>
        <div class="hero-eyebrow reveal">
            🏭 Abu Dhabi National Oil Company &nbsp;·&nbsp; Petroleum Field Operations
        </div>
        <h1 class="reveal">
            <span class="grad-blue">Field Operations</span><br>
            Management <span class="grad-cyan">System</span>
        </h1>
        <p class="hero-sub reveal">
            A unified digital platform covering all operational departments —
            MPFM, Wire Line, Production, Drilling, Completion, and Workover —
            delivering real-time visibility, automated workflows, and measurable cost reduction.
        </p>
        <div class="hero-btns reveal">
            @auth
                <a href="{{ url('/dashboard') }}" class="btn-primary">🚀 Open Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn-primary">🔐 Sign In to Your Account</a>
            @endauth
            <a href="#departments" class="btn-outline">📋 Explore the System ↓</a>
        </div>
        <div class="stats-bar reveal">
            <div class="stat-item"><div class="stat-num">6</div><div class="stat-lbl">Operational Departments</div></div>
            <div class="stat-item"><div class="stat-num">8</div><div class="stat-lbl">Workflow States</div></div>
            <div class="stat-item"><div class="stat-num">6</div><div class="stat-lbl">User Roles</div></div>
            <div class="stat-item"><div class="stat-num">100%</div><div class="stat-lbl">Zero Paperwork</div></div>
            <div class="stat-item"><div class="stat-num">60%</div><div class="stat-lbl">Faster Approvals</div></div>
        </div>
    </div>
</section>

{{-- ══════ PHOTO STRIP — all 4 field images ══════ --}}
<div class="photo-strip">
    <div class="ps-item">
        <img src="/assets/img/brand/oilfield oprations.webp" alt="Field Operations">
        <span class="ps-label">🏭 Field Operations</span>
    </div>
    <div class="ps-item">
        <img src="/assets/img/brand/Offshore.jpg" alt="Offshore Platform">
        <span class="ps-label">🛢️ Offshore Platform</span>
    </div>
    <div class="ps-item">
        <img src="/assets/img/brand/offshore oilfield.jpg" alt="Oil Field">
        <span class="ps-label">⛽ Oil Field</span>
    </div>
    <div class="ps-item">
        <img src="/assets/img/brand/production background.jpg" alt="Production">
        <span class="ps-label">📊 Production</span>
    </div>
</div>

{{-- ══════ DEPARTMENTS ══════ --}}
<section id="departments" class="sec-alt">
    <div class="sec-head reveal">
        <div class="sec-label">🏗️ All Departments</div>
        <h2 class="sec-title">One System. Six Departments.<br>Total Field Control.</h2>
        <p class="sec-sub">
            Every department has its own dedicated dashboard, task queue, and supervisor workflow —
            all connected in one unified platform with full cross-department visibility.
        </p>
    </div>
    <div class="dept-grid">

        <div class="dept-card dc-1 reveal">
            <img class="dept-img" src="/assets/img/brand/mpfm monitoing.jpg" alt="MPFM">
            <div class="dept-body">
                <div class="dept-accent-bar da-1"></div>
                <span class="d-icon">📡</span>
                <h3>MPFM — Multi-Phase Flow Meter</h3>
                <p class="desc">Manages all MPFM test operations across oil strings and towers. Operators upload log files from the field — the system auto-parses flow data, calculates KPIs, and routes results through the review chain automatically.</p>
                <div class="dept-impact">
                    <div class="dept-impact-title">📈 Key Impact on Well Production</div>
                    <div class="dept-impact-items">
                        <span class="dept-impact-item">Accurate oil rate per well</span>
                        <span class="dept-impact-item">Water cut monitoring</span>
                        <span class="dept-impact-item">GOR tracking</span>
                        <span class="dept-impact-item">Retest management</span>
                    </div>
                </div>
                <div class="dept-tags">
                    <span class="dept-tag">Log File Upload</span>
                    <span class="dept-tag">Auto-Parse Data</span>
                    <span class="dept-tag">KPI Reports</span>
                    <span class="dept-tag">Digital Review</span>
                </div>
            </div>
        </div>

        <div class="dept-card dc-2 reveal">
            <img class="dept-img" src="/assets/img/brand/wire Line lubricator .jpg" alt="Wire Line">
            <div class="dept-body">
                <div class="dept-accent-bar da-2"></div>
                <span class="d-icon">🔌</span>
                <h3>Wire Line</h3>
                <p class="desc">Coordinates well intervention and downhole logging operations. Tracks job requests from planning to completion, assigns specialized crews, and archives completion reports for every well.</p>
                <div class="dept-impact">
                    <div class="dept-impact-title">📈 Key Impact on Well Production</div>
                    <div class="dept-impact-items">
                        <span class="dept-impact-item">Well integrity validation</span>
                        <span class="dept-impact-item">Perforation accuracy</span>
                        <span class="dept-impact-item">Formation evaluation</span>
                        <span class="dept-impact-item">Plug & abandon ops</span>
                    </div>
                </div>
                <div class="dept-tags">
                    <span class="dept-tag">Well Intervention</span>
                    <span class="dept-tag">Logging Jobs</span>
                    <span class="dept-tag">Crew Scheduling</span>
                    <span class="dept-tag">Compliance Archive</span>
                </div>
            </div>
        </div>

        <div class="dept-card dc-3 reveal">
            <img class="dept-img" src="/assets/img/brand/production background.jpg" alt="Production">
            <div class="dept-body">
                <div class="dept-accent-bar da-3"></div>
                <span class="d-icon">🛢️</span>
                <h3>Production</h3>
                <p class="desc">Monitors and manages daily production operations across all oil strings and towers. Tracks production rates, records exceptions, coordinates field inspections, and maintains full production history per well.</p>
                <div class="dept-impact">
                    <div class="dept-impact-title">📈 Key Impact on Well Production</div>
                    <div class="dept-impact-items">
                        <span class="dept-impact-item">Daily production rates</span>
                        <span class="dept-impact-item">Downtime reduction</span>
                        <span class="dept-impact-item">Anomaly detection</span>
                        <span class="dept-impact-item">Well optimization</span>
                    </div>
                </div>
                <div class="dept-tags">
                    <span class="dept-tag">Real-Time Monitoring</span>
                    <span class="dept-tag">Field Inspection</span>
                    <span class="dept-tag">Exceptions Log</span>
                    <span class="dept-tag">Well History</span>
                </div>
            </div>
        </div>

        <div class="dept-card dc-4 reveal">
            <img class="dept-img" src="/assets/img/brand/drilling background .png" alt="Drilling">
            <div class="dept-body">
                <div class="dept-accent-bar da-4"></div>
                <span class="d-icon">🔩</span>
                <h3>Drilling</h3>
                <p class="desc">Manages drilling operations from planning through execution. Tracks drilling programs, assigns crews, monitors daily reports (DDR), and handles equipment scheduling, logistics, and HSE compliance.</p>
                <div class="dept-impact">
                    <div class="dept-impact-title">📈 Key Impact on Well Production</div>
                    <div class="dept-impact-items">
                        <span class="dept-impact-item">Faster well delivery</span>
                        <span class="dept-impact-item">NPT reduction</span>
                        <span class="dept-impact-item">Cost per metre</span>
                        <span class="dept-impact-item">HSE compliance</span>
                    </div>
                </div>
                <div class="dept-tags">
                    <span class="dept-tag">Drilling Programs</span>
                    <span class="dept-tag">DDR Tracking</span>
                    <span class="dept-tag">Equipment Planning</span>
                    <span class="dept-tag">NPT Analysis</span>
                </div>
            </div>
        </div>

        <div class="dept-card dc-5 reveal">
            <img class="dept-img" src="/assets/img/brand/complation.jpg" alt="Completion">
            <div class="dept-body">
                <div class="dept-accent-bar da-5"></div>
                <span class="d-icon">✅</span>
                <h3>Completion</h3>
                <p class="desc">Handles well completion workflows from perforation to handover. Manages completion programs, tracks stage-by-stage progress with digital checklists, validates quality, and coordinates final handover documentation.</p>
                <div class="dept-impact">
                    <div class="dept-impact-title">📈 Key Impact on Well Production</div>
                    <div class="dept-impact-items">
                        <span class="dept-impact-item">Faster first oil</span>
                        <span class="dept-impact-item">Completion quality</span>
                        <span class="dept-impact-item">Reservoir contact</span>
                        <span class="dept-impact-item">Skin factor control</span>
                    </div>
                </div>
                <div class="dept-tags">
                    <span class="dept-tag">Completion Programs</span>
                    <span class="dept-tag">Stage Tracking</span>
                    <span class="dept-tag">Quality Validation</span>
                    <span class="dept-tag">Handover Docs</span>
                </div>
            </div>
        </div>

        <div class="dept-card dc-6 reveal">
            <img class="dept-img" src="/assets/img/brand/work over .jpg" alt="Workover">
            <div class="dept-body">
                <div class="dept-accent-bar da-6"></div>
                <span class="d-icon">🔧</span>
                <h3>Workover</h3>
                <p class="desc">Manages well maintenance, remediation, and workover jobs. Schedules rigs, tracks job scope and progress, manages HSE checklists, and records post-workover production improvement per well.</p>
                <div class="dept-impact">
                    <div class="dept-impact-title">📈 Key Impact on Well Production</div>
                    <div class="dept-impact-items">
                        <span class="dept-impact-item">Production restoration</span>
                        <span class="dept-impact-item">Rig efficiency</span>
                        <span class="dept-impact-item">Well uplift tracking</span>
                        <span class="dept-impact-item">Cost per job</span>
                    </div>
                </div>
                <div class="dept-tags">
                    <span class="dept-tag">Rig Scheduling</span>
                    <span class="dept-tag">Job Scope</span>
                    <span class="dept-tag">HSE Checklists</span>
                    <span class="dept-tag">Post-Workover KPIs</span>
                </div>
            </div>
        </div>

    </div>
</section>

{{-- ══════ DETAILED WELL ARCHITECTURE DIAGRAM ══════ --}}
<section class="diag-section" id="diagram">
    <div class="sec-head reveal">
        <div class="sec-label">🔩 Field Architecture</div>
        <h2 class="sec-title">How All Departments Work<br>Together on Every Well</h2>
        <p class="sec-sub">
            Each well is simultaneously supported by all six departments.
            The diagram below shows the full data flow — from department-level activities,
            through the unified digital platform, down to well-level KPIs and field production output.
        </p>
    </div>

    <div class="diag-wrap reveal">
        {{-- title bar --}}
        <div class="diag-bar">
            <div class="diag-bar-left">
                <div class="diag-dot" style="background:#ef4444;"></div>
                <div class="diag-dot" style="background:#f59e0b;"></div>
                <div class="diag-dot" style="background:#22c55e;"></div>
                <span class="diag-bar-title">Field Operations Architecture — Department → Platform → Well → Output</span>
            </div>
            <span class="diag-bar-badge">🔴 Live System</span>
        </div>

        <div class="diag-body">

            {{-- ── LAYER 1: DEPARTMENTS ── --}}
            <div class="diag-layer">
                <div class="diag-layer-label">
                    INPUT LAYER — Operational Departments
                </div>
                <div class="dept-boxes">
                    <div class="dept-box db-blue">
                        <div class="db-icon">📡</div>
                        <h4>MPFM</h4>
                        <p class="db-role">Measures multiphase flow rates per well. Uploads log files, auto-parses oil/water/gas data.</p>
                        <span class="db-tag">Oil · Water · Gas</span>
                    </div>
                    <div class="dept-box db-cyan">
                        <div class="db-icon">🔌</div>
                        <h4>Wire Line</h4>
                        <p class="db-role">Validates well integrity via downhole logging. Evaluates perforations and formation data.</p>
                        <span class="db-tag">Logging · Testing</span>
                    </div>
                    <div class="dept-box db-sky">
                        <div class="db-icon">🛢️</div>
                        <h4>Production</h4>
                        <p class="db-role">Monitors daily output across all wells. Detects anomalies and coordinates field inspections.</p>
                        <span class="db-tag">Daily Rates · Anomaly</span>
                    </div>
                    <div class="dept-box db-lblue">
                        <div class="db-icon">🔩</div>
                        <h4>Drilling</h4>
                        <p class="db-role">Delivers new wells on schedule. Tracks NPT, cost per metre, and drilling efficiency.</p>
                        <span class="db-tag">NPT · Cost/m</span>
                    </div>
                    <div class="dept-box db-indigo">
                        <div class="db-icon">✅</div>
                        <h4>Completion</h4>
                        <p class="db-role">Maximises reservoir contact and production index. Controls skin factor and stimulation quality.</p>
                        <span class="db-tag">Skin · PI · Contact</span>
                    </div>
                    <div class="dept-box db-teal">
                        <div class="db-icon">🔧</div>
                        <h4>Workover</h4>
                        <p class="db-role">Restores declining well production. Tracks rig efficiency and post-job uplift in BOPD.</p>
                        <span class="db-tag">Uplift BOPD · Rig</span>
                    </div>
                </div>
            </div>

            {{-- ── CONNECTOR 1: depts → platform ── --}}
            <div class="diag-svg-wrap" style="height:52px;">
                <svg viewBox="0 0 1000 52" preserveAspectRatio="none">
                    <defs>
                        <marker id="arr1" markerWidth="6" markerHeight="6" refX="3" refY="3" orient="auto">
                            <path d="M0,0 L6,3 L0,6 Z" fill="rgba(37,99,235,.6)"/>
                        </marker>
                    </defs>
                    <!-- vertical drops from each dept -->
                    <line x1="83"  y1="0" x2="83"  y2="26" stroke="rgba(37,99,235,.35)" stroke-width="1.5" stroke-dasharray="4,3"/>
                    <line x1="250" y1="0" x2="250" y2="26" stroke="rgba(6,182,212,.35)"  stroke-width="1.5" stroke-dasharray="4,3"/>
                    <line x1="417" y1="0" x2="417" y2="26" stroke="rgba(14,165,233,.35)" stroke-width="1.5" stroke-dasharray="4,3"/>
                    <line x1="583" y1="0" x2="583" y2="26" stroke="rgba(56,189,248,.35)" stroke-width="1.5" stroke-dasharray="4,3"/>
                    <line x1="750" y1="0" x2="750" y2="26" stroke="rgba(99,102,241,.35)" stroke-width="1.5" stroke-dasharray="4,3"/>
                    <line x1="917" y1="0" x2="917" y2="26" stroke="rgba(34,211,238,.35)" stroke-width="1.5" stroke-dasharray="4,3"/>
                    <!-- horizontal bus -->
                    <line x1="83" y1="26" x2="917" y2="26" stroke="rgba(255,255,255,.12)" stroke-width="1.5" stroke-dasharray="6,4"/>
                    <!-- drop to platform -->
                    <line x1="500" y1="26" x2="500" y2="50" stroke="rgba(37,99,235,.6)" stroke-width="2" marker-end="url(#arr1)"/>
                </svg>
            </div>

            {{-- ── LAYER 2: PLATFORM CORE ── --}}
            <div class="diag-layer">
                <div class="diag-layer-label">PROCESSING LAYER — Unified Digital Platform</div>
                <div class="platform-core">
                    <div class="platform-title">
                        <h3>⚙️ ADNOC Field Operations Management Platform</h3>
                        <p>All department data flows into one secure, unified system — tasks tracked, approved, and audited digitally</p>
                    </div>
                    <div class="platform-kpis">
                        <div class="pk"><div class="pk-val">8 States</div><div class="pk-lbl">Task Workflow</div></div>
                        <div class="pk"><div class="pk-val">6 Roles</div><div class="pk-lbl">Access Control</div></div>
                        <div class="pk"><div class="pk-val">24/7</div><div class="pk-lbl">Monitoring</div></div>
                        <div class="pk"><div class="pk-val">100%</div><div class="pk-lbl">Audit Trail</div></div>
                        <div class="pk"><div class="pk-val">SLA</div><div class="pk-lbl">Enforcement</div></div>
                    </div>
                </div>
            </div>

            {{-- ── CONNECTOR 2: platform → wells ── --}}
            <div class="diag-svg-wrap" style="height:52px;">
                <svg viewBox="0 0 1000 52" preserveAspectRatio="none">
                    <defs>
                        <marker id="arr2" markerWidth="6" markerHeight="6" refX="3" refY="3" orient="auto">
                            <path d="M0,0 L6,3 L0,6 Z" fill="rgba(6,182,212,.7)"/>
                        </marker>
                    </defs>
                    <!-- drop from platform -->
                    <line x1="500" y1="0" x2="500" y2="26" stroke="rgba(37,99,235,.6)" stroke-width="2"/>
                    <!-- horizontal to 3 wells -->
                    <line x1="167" y1="26" x2="833" y2="26" stroke="rgba(255,255,255,.12)" stroke-width="1.5"/>
                    <!-- drops to each well -->
                    <line x1="167" y1="26" x2="167" y2="50" stroke="rgba(6,182,212,.5)" stroke-width="1.5" marker-end="url(#arr2)"/>
                    <line x1="500" y1="26" x2="500" y2="50" stroke="rgba(6,182,212,.5)" stroke-width="1.5" marker-end="url(#arr2)"/>
                    <line x1="833" y1="26" x2="833" y2="50" stroke="rgba(6,182,212,.5)" stroke-width="1.5" marker-end="url(#arr2)"/>
                </svg>
            </div>

            {{-- ── LAYER 3: WELLS ── --}}
            <div class="diag-layer">
                <div class="diag-layer-label">WELL LAYER — Real-Time Per-Well Data</div>
                <div class="well-cluster">

                    <div class="well-card">
                        <div class="well-card-head">
                            <span class="wh-icon">🛢️</span>
                            <h4>Well A-14 — Oil String 3</h4>
                            <span class="wh-status">● Active</span>
                        </div>
                        <div class="well-card-body">
                            <div class="well-kpi-row">
                                <div class="wkpi"><div class="wkpi-val">1,240</div><div class="wkpi-lbl">BOPD</div></div>
                                <div class="wkpi"><div class="wkpi-val">18%</div><div class="wkpi-lbl">BSW</div></div>
                                <div class="wkpi"><div class="wkpi-val">820</div><div class="wkpi-lbl">GOR scf/bbl</div></div>
                                <div class="wkpi"><div class="wkpi-val">97%</div><div class="wkpi-lbl">Uptime</div></div>
                            </div>
                            <div class="well-dept-badges">
                                <span class="wdb">📡 MPFM tested</span>
                                <span class="wdb">✅ Completion done</span>
                                <span class="wdb">🛢️ Prod. monitoring</span>
                            </div>
                        </div>
                    </div>

                    <div class="well-card">
                        <div class="well-card-head">
                            <span class="wh-icon">🛢️</span>
                            <h4>Well B-07 — Tower 2</h4>
                            <span class="wh-status">● Active</span>
                        </div>
                        <div class="well-card-body">
                            <div class="well-kpi-row">
                                <div class="wkpi"><div class="wkpi-val">870</div><div class="wkpi-lbl">BOPD</div></div>
                                <div class="wkpi"><div class="wkpi-val">31%</div><div class="wkpi-lbl">BSW</div></div>
                                <div class="wkpi"><div class="wkpi-val">650</div><div class="wkpi-lbl">GOR scf/bbl</div></div>
                                <div class="wkpi"><div class="wkpi-val">93%</div><div class="wkpi-lbl">Uptime</div></div>
                            </div>
                            <div class="well-dept-badges">
                                <span class="wdb">🔧 Workover done</span>
                                <span class="wdb">🔌 Wire Line logged</span>
                                <span class="wdb">📡 MPFM tested</span>
                            </div>
                        </div>
                    </div>

                    <div class="well-card">
                        <div class="well-card-head">
                            <span class="wh-icon">🛢️</span>
                            <h4>Well C-22 — Oil String 1</h4>
                            <span class="wh-status">● Active</span>
                        </div>
                        <div class="well-card-body">
                            <div class="well-kpi-row">
                                <div class="wkpi"><div class="wkpi-val">2,105</div><div class="wkpi-lbl">BOPD</div></div>
                                <div class="wkpi"><div class="wkpi-val">9%</div><div class="wkpi-lbl">BSW</div></div>
                                <div class="wkpi"><div class="wkpi-val">1,100</div><div class="wkpi-lbl">GOR scf/bbl</div></div>
                                <div class="wkpi"><div class="wkpi-val">99%</div><div class="wkpi-lbl">Uptime</div></div>
                            </div>
                            <div class="well-dept-badges">
                                <span class="wdb">🔩 New drill</span>
                                <span class="wdb">✅ Completion done</span>
                                <span class="wdb">📡 MPFM tested</span>
                            </div>
                        </div>
                    </div>

                </div>{{-- end well-cluster --}}
            </div>

            {{-- ── CONNECTOR 3: wells → output ── --}}
            <div class="output-connector">
                <div class="out-line">
                    <div class="out-bar"></div>
                    <div class="out-arrow">▼</div>
                </div>
            </div>

            {{-- ── LAYER 4: FIELD OUTPUT ── --}}
            <div class="diag-layer">
                <div class="diag-layer-label">OUTPUT LAYER — Field Production Results &amp; Cost Reduction</div>
                <div class="field-output">
                    <div class="field-output-top">
                        <h3>💰 Aggregated Field Production Output</h3>
                        <div class="fo-live"><div class="fo-live-dot"></div> Live Data</div>
                    </div>
                    <div class="field-kpis">
                        <div class="fkpi">
                            <div class="fkpi-val">4,215</div>
                            <div class="fkpi-delta">↑ 12% MoM</div>
                            <div class="fkpi-lbl">Total BOPD</div>
                        </div>
                        <div class="fkpi">
                            <div class="fkpi-val">↓ 18%</div>
                            <div class="fkpi-delta">↑ Efficiency</div>
                            <div class="fkpi-lbl">Opex per Barrel</div>
                        </div>
                        <div class="fkpi">
                            <div class="fkpi-val">60%</div>
                            <div class="fkpi-delta">↑ Speed</div>
                            <div class="fkpi-lbl">Faster Approvals</div>
                        </div>
                        <div class="fkpi">
                            <div class="fkpi-val">↓ 70%</div>
                            <div class="fkpi-delta">↑ SLA Met</div>
                            <div class="fkpi-lbl">Overdue Tasks</div>
                        </div>
                        <div class="fkpi">
                            <div class="fkpi-val">100%</div>
                            <div class="fkpi-delta">↑ Compliance</div>
                            <div class="fkpi-lbl">Audit Trail</div>
                        </div>
                        <div class="fkpi">
                            <div class="fkpi-val">3 min</div>
                            <div class="fkpi-delta">vs. 3 days</div>
                            <div class="fkpi-lbl">Audit Prep</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- legend --}}
            <div class="diag-legend">
                <div class="legend-item">
                    <div class="legend-dash"></div> Department data feed
                </div>
                <div class="legend-item">
                    <div class="legend-line" style="background:rgba(37,99,235,.7);"></div> Platform data flow
                </div>
                <div class="legend-item">
                    <div class="legend-line" style="background:rgba(6,182,212,.7);"></div> Well output stream
                </div>
                <div class="legend-item">
                    <div class="legend-dot" style="background:#22c55e;"></div> Active well
                </div>
                <div class="legend-item">
                    <div class="legend-dot" style="background:rgba(37,99,235,.8);"></div> Platform node
                </div>
            </div>

        </div>{{-- end diag-body --}}
    </div>{{-- end diag-wrap --}}
</section>

{{-- ══════ IMAGE BANNER 1 ══════ --}}
<div class="img-banner">
    <div class="img-banner-bg" style="background-image:url('/assets/img/brand/Offshore.jpg'); background-position:center 50%;"></div>
    <div class="img-banner-overlay"></div>
    <div class="img-banner-content reveal">
        <h2>Every Action Tracked.<br>Every Approval Digital. Every Well Optimised.</h2>
        <p>From monthly planning to final sign-off — complete traceability across all departments.</p>
    </div>
</div>

{{-- ══════ WORKFLOW ══════ --}}
<section id="workflow" class="sec-alt">
    <div class="sec-head reveal">
        <div class="sec-label">🔄 Task Lifecycle</div>
        <h2 class="sec-title">Structured 6-Step<br>Operational Workflow</h2>
        <p class="sec-sub">Every task follows a standardized, role-gated digital workflow with SLA enforcement, automatic notifications, and a full audit trail at every step.</p>
    </div>
    <div class="flow-wrap">
        <div class="flow-step reveal"><div class="flow-num">1</div><span class="f-icon">📅</span><h3>Monthly Planning</h3><p>Job Officer builds the monthly plan, defining tasks per department, well, and priority level.</p></div>
        <div class="flow-step reveal"><div class="flow-num">2</div><span class="f-icon">📤</span><h3>Task Submission</h3><p>Submitted to Surveillance Supervisor who reviews, classifies, and routes to correct departments.</p></div>
        <div class="flow-step reveal"><div class="flow-num">3</div><span class="f-icon">🎯</span><h3>Assignment</h3><p>Supervisor assigns to operators with SLA deadlines. Operators notified instantly via the system.</p></div>
        <div class="flow-step reveal"><div class="flow-num">4</div><span class="f-icon">⚙️</span><h3>Field Execution</h3><p>Operator performs field work and uploads data files and results directly through the app.</p></div>
        <div class="flow-step reveal"><div class="flow-num">5</div><span class="f-icon">🔍</span><h3>Supervisor Review</h3><p>Department and Surveillance Supervisors review results, approve or request retest with notes.</p></div>
        <div class="flow-step reveal"><div class="flow-num">6</div><span class="f-icon">✅</span><h3>Final Approval</h3><p>Job Officer gives final sign-off. Completed with full history, timestamps, and audit trail.</p></div>
    </div>
</section>

{{-- ══════ ROLES ══════ --}}
<section id="roles">
    <div class="sec-head reveal">
        <div class="sec-label">👥 User Roles</div>
        <h2 class="sec-title">Every Team Member<br>Gets a Tailored Experience</h2>
        <p class="sec-sub">Role-based access ensures each user sees only what is relevant — maximising efficiency while maintaining strict operational security boundaries.</p>
    </div>
    <div class="roles-grid">
        <div class="role-card reveal"><div class="role-icon-box">🛡️</div><div><h3>System Administrator</h3><p>Full access. Manages users, departments, configurations, and has override authority on all workflow states.</p></div></div>
        <div class="role-card reveal"><div class="role-icon-box">💼</div><div><h3>Job Officer</h3><p>Creates monthly operational plans, submits tasks for review, and provides final approval on completed work.</p></div></div>
        <div class="role-card reveal"><div class="role-icon-box">👁️</div><div><h3>Surveillance Supervisor</h3><p>Reviews incoming tasks, classifies by department and priority, assigns operators, monitors cross-department progress.</p></div></div>
        <div class="role-card reveal"><div class="role-icon-box">🏢</div><div><h3>Department Supervisor</h3><p>Manages tasks within their department, reviews operator submissions, approves or escalates for further review.</p></div></div>
        <div class="role-card reveal"><div class="role-icon-box">⚙️</div><div><h3>Field Operator</h3><p>Receives assignments, performs field work, uploads measurement data and test results, submits for review.</p></div></div>
        <div class="role-card reveal"><div class="role-icon-box">📊</div><div><h3>MPFM Supervisor</h3><p>Oversees MPFM operations, validates test quality and measurement accuracy, manages operator assignments.</p></div></div>
    </div>
</section>

{{-- ══════ BENEFITS & COST REDUCTION ══════ --}}
<section id="benefits" class="sec-alt">
    <div class="sec-head reveal">
        <div class="sec-label">💰 ROI & Cost Reduction</div>
        <h2 class="sec-title">How the System Saves<br>Time, Effort & Money</h2>
        <p class="sec-sub">Every manual, paper-based process has been replaced with a fast, automated digital alternative — delivering measurable cost reduction from day one.</p>
    </div>
    <div class="cost-strip reveal">
        <div class="cost-card"><div class="cost-val">60%</div><div class="cost-lbl">Faster Task Approval</div></div>
        <div class="cost-card"><div class="cost-val">70%</div><div class="cost-lbl">Fewer Overdue Tasks</div></div>
        <div class="cost-card"><div class="cost-val">100%</div><div class="cost-lbl">Paperwork Eliminated</div></div>
        <div class="cost-card"><div class="cost-val">4 hr</div><div class="cost-lbl">Saved per Task</div></div>
        <div class="cost-card"><div class="cost-val">3 min</div><div class="cost-lbl">Audit vs. Days</div></div>
        <div class="cost-card"><div class="cost-val">↓ Opex</div><div class="cost-lbl">Cost per Barrel</div></div>
    </div>
    <div class="benefits-grid" style="margin-top:38px;">
        <div class="benefit-card reveal"><div class="benefit-top"><div class="benefit-icon">⏱️</div><h3>Instant Task Routing</h3></div><p>Tasks routed to the right department and operator automatically — no phone calls, no WhatsApp, no manual follow-up.</p><span class="saving-tag">⚡ Saves 2–4 hours per task</span></div>
        <div class="benefit-card reveal"><div class="benefit-top"><div class="benefit-icon">📄</div><h3>Zero Paperwork</h3></div><p>All forms, approvals, and results are digital. Field operators submit directly from their device in the field.</p><span class="saving-tag">📋 100% paper-free operations</span></div>
        <div class="benefit-card reveal"><div class="benefit-top"><div class="benefit-icon">🔔</div><h3>Automatic Notifications</h3></div><p>Every workflow transition triggers instant notifications — no reminder emails, no phone calls, no status chasing.</p><span class="saving-tag">📱 30–60 min/day per supervisor</span></div>
        <div class="benefit-card reveal"><div class="benefit-top"><div class="benefit-icon">📊</div><h3>Real-Time Visibility</h3></div><p>Live dashboards show task status, completion rates, and overdue items — no weekly status meetings needed.</p><span class="saving-tag">📈 Replaces weekly status reports</span></div>
        <div class="benefit-card reveal"><div class="benefit-top"><div class="benefit-icon">⏲️</div><h3>SLA Enforcement</h3></div><p>Every task has a configurable SLA deadline. Overdue tasks are automatically flagged and escalated.</p><span class="saving-tag">🎯 70% fewer overdue tasks</span></div>
        <div class="benefit-card reveal"><div class="benefit-top"><div class="benefit-icon">📁</div><h3>Instant Audit & Compliance</h3></div><p>Every action logged with timestamps and user identity. Audits that took days now take minutes.</p><span class="saving-tag">🗂️ Audit prep: days → minutes</span></div>
    </div>

    <div class="sec-head reveal" style="margin-top:72px;margin-bottom:0;">
        <div class="sec-label">⚖️ Before vs. After</div>
        <h2 class="sec-title" style="font-size:clamp(1.5rem,2.5vw,2rem);">Manual Process vs. Digital System</h2>
    </div>
    <div class="compare-wrap reveal">
        <table class="compare-table">
            <thead>
                <tr>
                    <th>Process</th>
                    <th class="old-h">❌ Old Manual Way</th>
                    <th class="new-h">✅ With This System</th>
                </tr>
            </thead>
            <tbody>
                <tr><td class="aspect">Task Assignment</td><td class="old">Phone + WhatsApp + verbal confirmation, no record</td><td class="new">One click — operator notified instantly, logged</td></tr>
                <tr><td class="aspect">Test Result Submission</td><td class="old">Paper form → scan → email → manual spreadsheet entry</td><td class="new">Upload file → auto-parsed → stored & reviewed digitally</td></tr>
                <tr><td class="aspect">Approval Chain</td><td class="old">Physical signatures → courier between offices → days</td><td class="new">Digital approval in one click → completed same day</td></tr>
                <tr><td class="aspect">Status Tracking</td><td class="old">Call supervisor → call operator → compile manual update</td><td class="new">Live dashboard — real-time status for all roles</td></tr>
                <tr><td class="aspect">Monthly Planning</td><td class="old">Excel files → emails → version confusion → data loss</td><td class="new">Structured plan builder → auto-distributed to departments</td></tr>
                <tr><td class="aspect">Audit & Compliance</td><td class="old">Search physical files + email archives → days of work</td><td class="new">Full audit trail in system → exportable in seconds</td></tr>
                <tr><td class="aspect">Retest Management</td><td class="old">No tracking — retests lost in emails, missed deadlines</td><td class="new">Automatic retest workflow → tracked with SLA deadline</td></tr>
            </tbody>
        </table>
    </div>
</section>

{{-- ══════ IMAGE BANNER 2 ══════ --}}
<div class="img-banner">
    <div class="img-banner-bg" style="background-image:url('/assets/img/brand/offshore oilfield.jpg'); background-position:center 40%;"></div>
    <div class="img-banner-overlay"></div>
    <div class="img-banner-content reveal">
        <h2>Built for the Field.<br>Designed for Decision-Makers.</h2>
        <p>From operator on the rig to Job Officer in the office — everyone works from the same live data.</p>
    </div>
</div>

{{-- ══════ SECURITY ══════ --}}
<section class="sec-security" id="security">
    <div class="sec-head center reveal">
        <div class="sec-label">🔒 Security & Compliance</div>
        <h2 class="sec-title">Enterprise-Grade Security<br>at Every Layer</h2>
        <p class="sec-sub">Protecting sensitive operational data with multi-layered security controls, strict access management, and full compliance traceability.</p>
    </div>
    <div class="security-grid">
        <div class="sec-card reveal">
            <span class="sc-icon">🔐</span>
            <h3>Role-Based Access Control</h3>
            <p>Every user is granted only the minimum permissions required for their function. No cross-role data leakage — operators cannot see management data, and vice versa.</p>
            <span class="sec-badge">Zero Trust Principle</span>
        </div>
        <div class="sec-card reveal">
            <span class="sc-icon">🛡️</span>
            <h3>End-to-End Audit Trail</h3>
            <p>Every action — task creation, state change, approval, file upload, or login — is immutably logged with timestamp, user identity, IP, and context. Tamper-proof records.</p>
            <span class="sec-badge">Full Traceability</span>
        </div>
        <div class="sec-card reveal">
            <span class="sc-icon">🔑</span>
            <h3>Secure Authentication</h3>
            <p>Multi-factor authentication support, session management with automatic timeout, and brute-force protection on all login endpoints.</p>
            <span class="sec-badge">MFA Ready</span>
        </div>
        <div class="sec-card reveal">
            <span class="sc-icon">🗄️</span>
            <h3>Encrypted Data Storage</h3>
            <p>All sensitive operational data encrypted at rest and in transit. Uploaded field files stored securely with access restricted to authorised roles only.</p>
            <span class="sec-badge">AES-256 Encryption</span>
        </div>
        <div class="sec-card reveal">
            <span class="sc-icon">🌐</span>
            <h3>Network Security</h3>
            <p>HTTPS enforced across all connections. CSRF protection, XSS prevention, and SQL injection defence built into every request. Regular security header validation.</p>
            <span class="sec-badge">OWASP Compliant</span>
        </div>
        <div class="sec-card reveal">
            <span class="sc-icon">📋</span>
            <h3>Compliance & Governance</h3>
            <p>Full support for internal audit requirements. Export-ready compliance reports, SLA breach records, and approval chain documentation for regulatory review.</p>
            <span class="sec-badge">Audit Ready</span>
        </div>
    </div>
</section>

{{-- ══════ AI ══════ --}}
<section class="ai-section" id="ai">
    <div class="sec-head center reveal">
        <div class="ai-badge">🤖 ROADMAP — Coming Soon</div>
        <div class="sec-label" style="justify-content:center;">✨ Next Generation</div>
        <h2 class="sec-title">AI-Powered Intelligence<br><span style="background:linear-gradient(135deg,#818cf8,#60a5fa,#34d399);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">for Smarter Field Operations</span></h2>
        <p class="sec-sub">The next evolution embeds AI across every department — predicting failures, optimising schedules, and cutting operational costs further.</p>
    </div>
    <div class="ai-grid">
        <div class="ai-card reveal"><span class="ai-icon">🔮</span><h3>Predictive Maintenance</h3><p>Analyses historical test data to predict equipment failures before they happen — eliminating unplanned downtime.</p></div>
        <div class="ai-card reveal"><span class="ai-icon">📉</span><h3>Anomaly Detection</h3><p>Machine learning detects abnormal flow readings instantly — flagging potential well issues before they affect production.</p></div>
        <div class="ai-card reveal"><span class="ai-icon">🗓️</span><h3>Smart Scheduling</h3><p>Optimises monthly task plans by analysing operator workload, well history, and field logistics automatically.</p></div>
        <div class="ai-card reveal"><span class="ai-icon">💬</span><h3>AI Operations Assistant</h3><p>Natural language chat lets managers query operational data, generate reports, and get insights without technical knowledge.</p></div>
        <div class="ai-card reveal"><span class="ai-icon">📈</span><h3>Production Forecasting</h3><p>Deep learning forecasts oil, water, and gas rates using measurement trends and reservoir history per well.</p></div>
        <div class="ai-card reveal"><span class="ai-icon">✍️</span><h3>Auto Report Generation</h3><p>AI drafts completion reports, monthly KPI summaries, and review notes — cutting report writing from hours to seconds.</p></div>
    </div>
</section>

{{-- ══════ CAPABILITIES (no tech stack details) ══════ --}}
<section class="sec-alt">
    <div class="sec-head center reveal">
        <div class="sec-label">⚙️ System Capabilities</div>
        <h2 class="sec-title">What the Platform<br>Delivers</h2>
        <p class="sec-sub">Built for enterprise-scale petroleum operations — reliable, secure, and ready to grow with ADNOC field expansion.</p>
    </div>
    <div class="cap-grid">
        <div class="cap-card reveal"><span class="cap-icon">🔐</span><div><h4>Multi-Layer Security</h4><p>Role-based access control, encrypted data, session management, and full audit logging.</p></div></div>
        <div class="cap-card reveal"><span class="cap-icon">📊</span><div><h4>Real-Time Dashboards</h4><p>Live KPI dashboards per role — operators, supervisors, and management all see their relevant data.</p></div></div>
        <div class="cap-card reveal"><span class="cap-icon">🔔</span><div><h4>Smart Notifications</h4><p>Automatic alerts at every workflow transition — no missed deadlines, no manual chasing.</p></div></div>
        <div class="cap-card reveal"><span class="cap-icon">📁</span><div><h4>Digital File Management</h4><p>Secure upload, storage, and retrieval of field data files with version control and access logging.</p></div></div>
        <div class="cap-card reveal"><span class="cap-icon">⏱️</span><div><h4>SLA Management Engine</h4><p>Configurable deadlines per state and department with automatic escalation for overdue tasks.</p></div></div>
        <div class="cap-card reveal"><span class="cap-icon">📋</span><div><h4>Compliance & Audit</h4><p>Every action timestamped and attributed. Export-ready compliance reports for regulatory review.</p></div></div>
        <div class="cap-card reveal"><span class="cap-icon">📱</span><div><h4>Mobile-Responsive</h4><p>Fully optimised for smartphones and tablets — field operators work from any device, anywhere.</p></div></div>
        <div class="cap-card reveal"><span class="cap-icon">🔗</span><div><h4>Cross-Department Integration</h4><p>All six departments connected — tasks, data, and approvals flow seamlessly between teams.</p></div></div>
    </div>
</section>

{{-- ══════ CTA ══════ --}}
<section>
    <div class="cta-box reveal">
        <div class="sec-label" style="justify-content:center;margin-bottom:16px;">🚀 Get Started</div>
        <h2>Ready to Digitise Your<br>Field Operations?</h2>
        <p>Stop managing operations through spreadsheets, phone calls, and paper forms. Bring all departments into one transparent, efficient, and cost-reducing platform.</p>
        <div style="display:flex;gap:14px;justify-content:center;flex-wrap:wrap;margin-top:32px;">
            @auth
                <a href="{{ url('/dashboard') }}" class="btn-primary">🚀 Open Your Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn-primary">🔐 Sign In to Your Account</a>
            @endauth
            <a href="#departments" class="btn-outline">📖 Explore Features ↑</a>
        </div>
    </div>
</section>

{{-- ══════ FOOTER ══════ --}}
<footer>
    <div class="footer-inner">
        <div>
            <div class="footer-logo"><img src="/assets/img/brand/logo-white.png" alt="ADNOC"></div>
            <div class="footer-copy">© {{ date('Y') }} ADNOC — Field Operations Management System. All rights reserved.</div>
        </div>
        <div class="footer-dev">
            <div class="footer-dev-label">Developed &amp; Engineered by</div>
            <a href="https://masarsoft.io" target="_blank" rel="noopener noreferrer">🚀 Masarsoft.io</a>
            <p>Software &amp; Digital Solutions</p>
        </div>
    </div>
</footer>

<script>
// Mobile nav
const burger = document.getElementById('burger');
const drawer = document.getElementById('drawer');
burger.addEventListener('click', () => drawer.classList.toggle('open'));
function closeDrawer() { drawer.classList.remove('open'); }
document.addEventListener('click', e => {
    if (!burger.contains(e.target) && !drawer.contains(e.target))
        drawer.classList.remove('open');
});

// Scroll-reveal with stagger
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('visible');
            observer.unobserve(entry.target);
        }
    });
}, { threshold: 0.08, rootMargin: '0px 0px -20px 0px' });

document.querySelectorAll('.reveal').forEach(el => {
    const siblings = Array.from(el.parentElement.querySelectorAll(':scope > .reveal'));
    const idx = siblings.indexOf(el);
    if (idx > 0 && siblings.length > 1)
        el.style.transitionDelay = `${Math.min(idx * 65, 450)}ms`;
    observer.observe(el);
});
</script>

</body>
</html>
