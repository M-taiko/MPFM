@extends('layouts.master')

@section('title', 'MPFM Tests')

@section('css')
<link href="{{ URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
<style>
.mpl { direction:ltr; text-align:left; --bg:#050a15; --ink:#e8eefc; --sub:#9fb0cf; --mute:#64748b; --cyan:#22d3ee; --amber:#fbbf24; --green:#34d399; --red:#f87171; --water:#38bdf8;
    --line:rgba(96,165,250,.2); --glass:rgba(255,255,255,.05); --grad:linear-gradient(135deg,#3b82f6,#22d3ee); --ease:cubic-bezier(.22,1,.36,1);
    position:relative; overflow:hidden; isolation:isolate; padding:26px; border-radius:26px; background:var(--bg); color:var(--ink); font-family:'Inter',sans-serif; -webkit-font-smoothing:antialiased; }
.mpl *, .mpl *::before, .mpl *::after { box-sizing:border-box; }
.mpl a { text-decoration:none; }
.mpl h1, .mpl h3, .mpl p { margin:0; color:inherit; }
.mpl .bgl { position:absolute; inset:0; z-index:-1; pointer-events:none; overflow:hidden; }
.mpl .bgl::before { content:""; position:absolute; inset:-50%; background-image:linear-gradient(rgba(59,130,246,.07) 1px,transparent 1px),linear-gradient(90deg,rgba(59,130,246,.07) 1px,transparent 1px); background-size:56px 56px; transform:perspective(700px) rotateX(58deg) translateY(-6%); animation:tlGrid 14s linear infinite; mask-image:radial-gradient(ellipse at 50% 30%,#000 10%,transparent 70%); -webkit-mask-image:radial-gradient(ellipse at 50% 30%,#000 10%,transparent 70%); }
@keyframes tlGrid { to { background-position:0 56px; } }
.mpl .orb { position:absolute; border-radius:50%; filter:blur(90px); opacity:.3; animation:tlFloat 16s ease-in-out infinite; }
.mpl .o1 { width:440px; height:440px; background:#2563eb; top:-140px; left:-120px; }
.mpl .o2 { width:400px; height:400px; background:#0891b2; bottom:-160px; right:-100px; animation-delay:-6s; }
@keyframes tlFloat { 50% { transform:translate(40px,-40px) scale(1.12); } }
.mpl .lg { --h:84px; display:inline-block; flex:none; height:var(--h); aspect-ratio:1/1; border-radius:calc(var(--h) * .14); background:#071326 url('/assets/img/brand/logo.svg') center/cover no-repeat; border:2px solid rgba(34,211,238,.5); box-shadow:0 0 0 1px rgba(59,130,246,.5),0 8px 30px rgba(0,0,0,.45),0 0 26px rgba(34,211,238,.35); position:relative; overflow:hidden; animation:tlGlow 3.2s ease-in-out infinite; }
.mpl .lg::after { content:""; position:absolute; top:0; left:-130%; width:60%; height:100%; background:linear-gradient(90deg,transparent,rgba(34,211,238,.45),transparent); transform:skewX(-20deg); animation:tlShine 4.5s infinite; }
@keyframes tlShine { 60%,100% { left:160%; } }
@keyframes tlGlow { 50% { box-shadow:0 0 0 1px rgba(59,130,246,.6),0 10px 40px rgba(0,0,0,.5),0 0 54px rgba(34,211,238,.65); } }
.mpl .rv { opacity:0; transform:translateY(28px); transition:opacity .9s var(--ease), transform .9s var(--ease); transition-delay:calc(var(--d,0) * 90ms); }
.mpl .rv.in { opacity:1; transform:none; }

.mpl .hero { position:relative; display:grid; grid-template-columns:1fr 380px; gap:20px; align-items:center; padding:26px 30px; border-radius:22px; border:1px solid var(--line); background:linear-gradient(100deg,rgba(5,10,21,.94) 40%,rgba(5,10,21,.45)),url('/assets/img/brand/Offshore.jpg') center/cover; overflow:hidden; }
.mpl .who { display:flex; align-items:center; gap:22px; }
.mpl .eyebrow { font-size:11px; font-weight:700; letter-spacing:.16em; text-transform:uppercase; color:var(--cyan); display:flex; align-items:center; gap:8px; }
.mpl .eyebrow i { width:8px; height:8px; border-radius:50%; background:var(--green); animation:tlPing 1.8s infinite; }
@keyframes tlPing { to { box-shadow:0 0 0 12px rgba(52,211,153,0); } }
.mpl .hero h1 { font-size:clamp(24px,3vw,38px); font-weight:900; letter-spacing:-.03em; line-height:1.1; margin:6px 0 8px; }
.mpl .hero h1 em { font-style:normal; background:linear-gradient(120deg,#60a5fa,#22d3ee,#fbbf24,#60a5fa); background-size:250% 100%; -webkit-background-clip:text; background-clip:text; color:transparent; animation:tlFlow 6s linear infinite; }
@keyframes tlFlow { to { background-position:250% 0; } }
.mpl .hero p { color:var(--sub); font-size:13.5px; max-width:520px; line-height:1.6; }
.mpl .actions { display:flex; gap:10px; flex-wrap:wrap; margin-top:16px; }
.mpl .btn2 { display:inline-flex; align-items:center; gap:8px; padding:10px 18px; border-radius:50px; font-size:13px; font-weight:700; border:1px solid var(--line); background:var(--glass); color:var(--ink); transition:transform .3s var(--ease), border-color .3s; }
.mpl .btn2:hover { transform:translateY(-3px); border-color:var(--cyan); color:#fff; }
.mpl .btn2.p { background:var(--grad); color:#04111f; border:0; box-shadow:0 8px 24px rgba(34,211,238,.28); }
.mpl .btn2.p:hover { color:#04111f; }
.mpl .mini svg { width:100%; height:auto; display:block; }

.mpl .kpis { display:grid; grid-template-columns:repeat(5,1fr); gap:14px; margin-top:18px; }
.mpl .kpi { position:relative; padding:16px; border-radius:18px; border:1px solid var(--line); background:var(--glass); overflow:hidden; transition:transform .4s var(--ease), border-color .3s; }
.mpl .kpi:hover { transform:translateY(-5px); border-color:var(--c,var(--cyan)); }
.mpl .kpi::before { content:""; position:absolute; left:0; top:0; height:3px; width:100%; background:var(--c,var(--cyan)); }
.mpl .kpi .ic { width:34px; height:34px; border-radius:11px; display:grid; place-items:center; background:rgba(255,255,255,.07); color:var(--c,var(--cyan)); font-size:16px; margin-bottom:10px; }
.mpl .kpi .l { font-size:10.5px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--mute); }
.mpl .kpi .v { font:700 clamp(20px,1.9vw,27px)/1.1 'Space Grotesk',sans-serif; margin-top:4px; color:#fff; }
.mpl .kpi .u { font-size:11px; color:var(--sub); margin-top:2px; }

.mpl .sec { margin-top:20px; }
.mpl .panel { position:relative; border-radius:22px; border:1px solid var(--line); background:linear-gradient(180deg,rgba(255,255,255,.045),rgba(255,255,255,.015)); padding:22px; overflow:hidden; }
.mpl .ph { display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:16px; }
.mpl .ph h3 { font-size:16px; font-weight:800; display:flex; align-items:center; gap:10px; }
.mpl .ph h3 i { width:30px; height:30px; border-radius:10px; display:grid; place-items:center; background:var(--grad); color:#04111f; font-size:14px; }
.mpl .ph .sub { font-size:12px; color:var(--sub); }
.mpl .g3 { display:grid; grid-template-columns:1fr 1.5fr 1fr; gap:18px; }

/* process strip */
.mpl .flow { display:grid; grid-template-columns:repeat(5,1fr); gap:0; align-items:start; }
.mpl .fs { text-align:center; position:relative; padding:0 6px; }
.mpl .fs svg { width:96px; height:96px; }
.mpl .fs b { display:block; font-size:13px; margin-top:4px; }
.mpl .fs small { display:block; font-size:11.5px; color:var(--mute); margin-top:3px; line-height:1.4; }
.mpl .fs .n { position:absolute; top:0; left:calc(50% - 56px); width:22px; height:22px; border-radius:50%; background:var(--grad); color:#04111f; font:700 11px 'Space Grotesk'; display:grid; place-items:center; z-index:2; }
.mpl .fs:not(:last-child)::after { content:""; position:absolute; top:46px; left:calc(50% + 52px); width:calc(100% - 104px); border-top:2px dashed rgba(34,211,238,.5); animation:tlMarch 1s linear infinite; }
@keyframes tlMarch { to { border-top-color:rgba(34,211,238,.15); } }
.mpl .fs .ico { animation:tlBob 3.4s ease-in-out infinite; }
.mpl .fs:nth-child(2) .ico { animation-delay:-.6s; } .mpl .fs:nth-child(3) .ico { animation-delay:-1.2s; } .mpl .fs:nth-child(4) .ico { animation-delay:-1.8s; } .mpl .fs:nth-child(5) .ico { animation-delay:-2.4s; }
@keyframes tlBob { 50% { transform:translateY(-5px); } }

/* charts */
.mpl .donutw { display:flex; flex-direction:column; align-items:center; gap:14px; }
.mpl .donut { width:170px; height:170px; }
.mpl .arc { fill:none; stroke-width:14; transform:rotate(-90deg); transform-origin:50% 50%; animation:tlArc 1.6s var(--ease) both; }
.mpl .arc.t { stroke:rgba(255,255,255,.08); animation:none; }
@keyframes tlArc { from { stroke-dasharray:0 100; } }
.mpl .lgd { display:grid; grid-template-columns:1fr 1fr; gap:8px 18px; width:100%; }
.mpl .lgd div { display:flex; align-items:center; gap:8px; font-size:12.5px; color:var(--sub); }
.mpl .lgd b { width:11px; height:11px; border-radius:4px; }
.mpl .lgd strong { color:#fff; margin-left:auto; font:700 14px 'Space Grotesk'; }
.mpl .chart svg { width:100%; height:auto; display:block; overflow:visible; }
.mpl .chart { position:relative; }
.mpl .tip { position:absolute; pointer-events:none; opacity:0; transition:opacity .15s; background:#0b1426; border:1px solid var(--line); border-radius:10px; padding:6px 10px; font-size:12px; white-space:nowrap; transform:translate(-50%,-125%); z-index:3; }
.mpl .tip b { display:block; font:700 14px 'Space Grotesk'; }
.mpl .vb { display:grid; gap:12px; }
.mpl .vr { display:grid; grid-template-columns:90px 1fr 34px; gap:10px; align-items:center; font-size:12.5px; color:var(--sub); }
.mpl .vr span:first-child { white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.mpl .vr .tr { height:12px; border-radius:6px; background:rgba(255,255,255,.06); overflow:hidden; }
.mpl .vr .tr i { display:block; height:100%; width:0; border-radius:6px; background:var(--grad); transition:width 1.3s var(--ease); }
.mpl .in .vr .tr i { width:var(--w); }
.mpl .vr b { color:#fff; font:700 13px 'Space Grotesk'; text-align:right; }
.mpl .empty { color:var(--mute); text-align:center; padding:24px; font-size:13px; }

/* filters + table */
.mpl .filters { display:grid; grid-template-columns:repeat(5,1fr) auto; gap:12px; align-items:end; margin-bottom:16px; }
.mpl .filters label { display:block; font-size:10.5px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--mute); margin-bottom:6px; }
.mpl .filters .form-control { background:rgba(255,255,255,.06); border:1px solid var(--line); color:#fff; border-radius:12px; height:40px; font-size:13px; }
.mpl .filters .form-control:focus { border-color:var(--cyan); box-shadow:0 0 0 3px rgba(34,211,238,.15); background:rgba(255,255,255,.08); color:#fff; }
.mpl .filters option { background:#0b1426; color:#fff; }
.mpl .filters input[type=date] { color-scheme:dark; }
.mpl .btn-r { height:40px; padding:0 16px; border-radius:12px; border:1px solid var(--line); background:var(--glass); color:var(--ink); font-weight:700; font-size:13px; cursor:pointer; transition:border-color .3s; }
.mpl .btn-r:hover { border-color:var(--cyan); }
.mpl .dataTables_wrapper { color:var(--sub); font-size:13px; }
.mpl .dataTables_wrapper label, .mpl .dataTables_info { color:var(--sub); }
.mpl .dataTables_wrapper select, .mpl .dataTables_wrapper input[type=search] { background:rgba(255,255,255,.06); border:1px solid var(--line); color:#fff; border-radius:10px; padding:4px 10px; }
.mpl table.dataTable { width:100% !important; border-collapse:separate; border-spacing:0 8px; border:0; background:transparent; color:var(--ink); }
.mpl table.dataTable thead th { background:transparent; color:var(--mute); font-size:10.5px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; border:0 !important; padding:6px 12px; }
.mpl table.dataTable tbody td { background:#0c1528 !important; border:0 !important; border-top:1px solid var(--line) !important; border-bottom:1px solid var(--line) !important; padding:12px; color:var(--ink); vertical-align:middle; font-size:13px; }
.mpl table.dataTable tbody td:first-child { border-left:1px solid var(--line) !important; border-radius:14px 0 0 14px; font-weight:600; }
.mpl table.dataTable tbody td:last-child { border-right:1px solid var(--line) !important; border-radius:0 14px 14px 0; white-space:nowrap; }
.mpl table.dataTable tbody tr, .mpl table.dataTable tbody tr.odd, .mpl table.dataTable tbody tr.even, .mpl table.dataTable.table-striped tbody tr:nth-of-type(odd) { background:transparent !important; }
.mpl table.dataTable tbody tr { transition:transform .3s var(--ease); }
.mpl table.dataTable tbody tr:hover td { background:#0f2236 !important; }
.mpl table.dataTable tbody tr:hover { transform:translateX(4px); }
.mpl table.dataTable thead .sorting:before, .mpl table.dataTable thead .sorting:after, .mpl table.dataTable thead .sorting_asc:before, .mpl table.dataTable thead .sorting_desc:before, .mpl table.dataTable thead .sorting_asc:after, .mpl table.dataTable thead .sorting_desc:after { color:var(--cyan); }
.mpl .badge { padding:5px 12px; border-radius:50px; font-size:10.5px; font-weight:700; letter-spacing:.07em; text-transform:uppercase; border:1px solid var(--line); }
.mpl .badge-secondary { background:rgba(148,163,184,.12); color:#cbd5e1; }
.mpl .badge-info { background:rgba(59,130,246,.14); color:#93c5fd; border-color:rgba(59,130,246,.5); }
.mpl .badge-success { background:rgba(52,211,153,.12); color:var(--green); border-color:rgba(52,211,153,.5); }
.mpl .badge-danger { background:rgba(248,113,113,.12); color:var(--red); border-color:rgba(248,113,113,.5); }
.mpl td .btn { border-radius:50px; font-size:11.5px; font-weight:700; padding:4px 12px; border:1px solid var(--line); background:var(--glass); color:var(--ink); }
.mpl td .btn:hover { border-color:var(--cyan); color:#fff; }
.mpl td .btn-info { color:var(--cyan); } .mpl td .btn-primary { color:#93c5fd; } .mpl td .btn-danger { color:var(--red); } .mpl td .btn-outline-primary { color:var(--cyan); }
.mpl .page-item .page-link, .mpl .paginate_button { background:var(--glass) !important; border:1px solid var(--line) !important; color:var(--sub) !important; border-radius:10px !important; margin:0 2px; }
.mpl .page-item.active .page-link, .mpl .paginate_button.current { background:var(--grad) !important; color:#04111f !important; border:0 !important; }
.mpl div.dataTables_processing { background:rgba(11,20,38,.9); color:var(--cyan); border-radius:14px; border:1px solid var(--line); }

@media (max-width:1200px) { .mpl .kpis { grid-template-columns:repeat(3,1fr); } .mpl .filters { grid-template-columns:repeat(3,1fr); } .mpl .g3 { grid-template-columns:1fr 1fr; } }
@media (max-width:992px) { .mpl .hero { grid-template-columns:1fr; } .mpl .mini { max-width:380px; } .mpl .flow { grid-template-columns:1fr 1fr; row-gap:18px; } .mpl .fs::after { display:none; } .mpl .g3 { grid-template-columns:1fr; } }
@media (max-width:640px) { .mpl { padding:14px; border-radius:18px; } .mpl .who { flex-direction:column; align-items:flex-start; } .mpl .kpis, .mpl .filters { grid-template-columns:1fr 1fr; } }
@media (prefers-reduced-motion:reduce) { .mpl *, .mpl *::before, .mpl *::after { animation:none !important; transition:none !important; } .mpl .rv { opacity:1; transform:none; } .mpl .vr .tr i { width:var(--w); } }
.mpl table.dataTable tbody td { padding:10px 6px; font-size:12.5px; }
.mpl table.dataTable thead th { padding:6px 8px; }
.mpl table.dataTable tbody td:nth-child(2) { white-space:nowrap; }
.mpl td .btn { padding:3px 9px; font-size:11px; }
.mpl td form { display:inline; }
.mpl .table-responsive { overflow-x:auto; }
</style>
@endsection

@section('content')
@php
    $st = $stats['byStatus'];
    $total = max($stats['total'], 0);
    $seg = [['draft', 'Draft', '#94a3b8'], ['submitted', 'Submitted', '#60a5fa'], ['approved', 'Approved', '#34d399'], ['rejected', 'Rejected', '#f87171']];
    $maxV = max(1, $stats['strings']->max('count') ?? 1);
    $off = 0;
@endphp
<div class="mpl" id="mpl">
    <div class="bgl"><div class="orb o1"></div><div class="orb o2"></div></div>

    {{-- HERO --}}
    <div class="hero rv">
        <div>
            <div class="who">
                <span class="lg"></span>
                <div>
                    <div class="eyebrow"><i></i> MPFM · Multiphase flow meter</div>
                    <h1>Well tests <em>from the meter</em></h1>
                </div>
            </div>
            <p style="margin-top:14px">Every MPFM test comes from an uploaded VX log. The meter reads oil, gas and water without separating them — review, approve and compare each test against the test vessel.</p>
            <div class="actions">
                @can('create', App\Models\MPFMTest::class)
                    <a href="{{ route('upload.form') }}" class="btn2 p"><i class="fe fe-upload"></i> Upload log</a>
                @endcan
                <a href="{{ route('mpfm_tests.trending_dashboard') }}" class="btn2"><i class="fe fe-trending-up"></i> Trends</a>
                <a href="{{ route('tv_tests.compare') }}" class="btn2"><i class="fe fe-shuffle"></i> Compare vs TV</a>
            </div>
        </div>
        <div class="mini">
            <svg viewBox="0 0 380 200" aria-hidden="true">
                <defs>
                    <linearGradient id="pSteel" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#a8b6cb"/><stop offset=".18" stop-color="#f1f5f9"/><stop offset=".5" stop-color="#7c8aa1"/><stop offset="1" stop-color="#1b2433"/></linearGradient>
                    <path id="pFlow" d="M4,128 H376"/>
                </defs>
                <g fill="none" stroke-linejoin="round">
                    <use href="#pFlow" stroke="#0b1220" stroke-width="20"/><use href="#pFlow" stroke="#64748b" stroke-width="15"/>
                </g>
                {{-- venturi body --}}
                <path d="M96,108 H132 L158,118 H222 L248,108 H284 V148 H248 L222,138 H158 L132,148 H96 Z" fill="url(#pSteel)" stroke="#0b1220" stroke-width="1.4"/>
                {{-- impulse lines + DP transmitter --}}
                <path d="M114,108 V56 H140 M190,118 V56 H180" fill="none" stroke="#94a3b8" stroke-width="2"/>
                <rect x="140" y="38" width="40" height="24" rx="5" fill="#0a1222" stroke="#22d3ee" stroke-width="1.6"/>
                <text x="160" y="54" text-anchor="middle" font-size="8.5" font-weight="700" fill="#67e8f9">DP</text>
                {{-- gamma source and detector --}}
                <rect x="170" y="160" width="40" height="26" rx="5" fill="#0a1222" stroke="#fbbf24" stroke-width="1.6"/>
                <g transform="translate(190,173)"><circle r="7" fill="none" stroke="#fbbf24" stroke-width="1.2"/><g><animateTransform attributeName="transform" type="rotate" from="0" to="360" dur="6s" repeatCount="indefinite"/><path d="M0,-5 L2,0 H-2 Z M4.3,2.5 L0,2 L2,-1.5 Z M-4.3,2.5 L-2,-1.5 L0,2 Z" fill="#fbbf24"/></g></g>
                <rect x="170" y="70" width="40" height="22" rx="5" fill="#0a1222" stroke="#34d399" stroke-width="1.6"/>
                <text x="190" y="85" text-anchor="middle" font-size="8" font-weight="700" fill="#6ee7b7">γ DET</text>
                <path d="M182,160 L178,92 M190,160 V92 M198,160 L202,92" stroke="#fbbf24" stroke-width="1.6" stroke-dasharray="3 5" fill="none"><animate attributeName="stroke-dashoffset" from="0" to="-16" dur=".9s" repeatCount="indefinite"/></path>
                {{-- PT transmitter --}}
                <path d="M266,108 V74" stroke="#94a3b8" stroke-width="2"/><circle cx="266" cy="64" r="10" fill="#0a1222" stroke="#a78bfa" stroke-width="1.6"/><text x="266" y="67" text-anchor="middle" font-size="8" font-weight="700" fill="#c4b5fd">PT</text>
                {{-- flow computer --}}
                <rect x="296" y="12" width="76" height="52" rx="8" fill="#0a1222" stroke="#22d3ee" stroke-opacity=".7"/>
                <text x="304" y="26" font-size="7.5" font-weight="700" fill="#67e8f9" letter-spacing="1">COMPUTER</text>
                <text x="304" y="40" font-size="8.5" font-weight="700" fill="#fbbf24">OIL</text><rect x="326" y="35" width="38" height="3" rx="1.5" fill="#fbbf24"><animate attributeName="width" values="14;38;26;38" dur="4s" repeatCount="indefinite"/></rect>
                <text x="304" y="50" font-size="8.5" font-weight="700" fill="#67e8f9">GAS</text><rect x="326" y="45" width="38" height="3" rx="1.5" fill="#22d3ee"><animate attributeName="width" values="30;18;36;30" dur="3.6s" repeatCount="indefinite"/></rect>
                <text x="304" y="60" font-size="8.5" font-weight="700" fill="#7dd3fc">WC</text><rect x="326" y="55" width="38" height="3" rx="1.5" fill="#38bdf8"><animate attributeName="width" values="8;14;10;8" dur="4.4s" repeatCount="indefinite"/></rect>
                <path d="M210,81 H236 V38 H296 M266,54 V38" stroke="#22d3ee" stroke-width="1.4" stroke-dasharray="3 4" fill="none"><animate attributeName="stroke-dashoffset" from="0" to="-14" dur="1s" repeatCount="indefinite"/></path>
                @foreach([0, .7, 1.4, 2.1, 2.8] as $b)
                    <circle r="3" fill="#fde68a"><animateMotion dur="3.5s" begin="{{ $b }}s" repeatCount="indefinite"><mpath href="#pFlow"/></animateMotion></circle>
                    <circle r="2.2" fill="#a5f3fc"><animateMotion dur="3.5s" begin="{{ $b + .35 }}s" repeatCount="indefinite"><mpath href="#pFlow"/></animateMotion></circle>
                @endforeach
                <text x="8" y="106" font-size="8.5" font-weight="700" fill="#fcd34d" letter-spacing="1.2">WELL STREAM</text>
                <text x="372" y="176" text-anchor="end" font-size="8.5" font-weight="700" fill="#67e8f9" letter-spacing="1.2">→ HEADER</text>
            </svg>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="kpis">
        <div class="kpi rv" style="--d:1;--c:#22d3ee"><div class="ic"><i class="fe fe-activity"></i></div><div class="l">Total tests</div><div class="v cu" data-to="{{ $stats['total'] }}" data-dec="0">{{ $stats['total'] }}</div><div class="u">all time</div></div>
        <div class="kpi rv" style="--d:2;--c:#a78bfa"><div class="ic"><i class="fe fe-calendar"></i></div><div class="l">Last 7 days</div><div class="v cu" data-to="{{ $stats['week'] }}" data-dec="0">{{ $stats['week'] }}</div><div class="u">tests</div></div>
        <div class="kpi rv" style="--d:3;--c:#34d399"><div class="ic"><i class="fe fe-check-circle"></i></div><div class="l">Approved</div><div class="v cu" data-to="{{ $st['approved'] ?? 0 }}" data-dec="0">{{ $st['approved'] ?? 0 }}</div><div class="u">{{ $total ? round((($st['approved'] ?? 0) / $total) * 100) : 0 }}% of total</div></div>
        <div class="kpi rv" style="--d:4;--c:#fbbf24"><div class="ic"><i class="fe fe-droplet"></i></div><div class="l">Avg oil rate</div><div class="v cu" data-to="{{ round($stats['avgOil']) }}" data-dec="0">{{ number_format($stats['avgOil'], 0) }}</div><div class="u">B/D · gas {{ number_format($stats['avgGas'], 1) }} MMscf/D</div></div>
        <div class="kpi rv" style="--d:5;--c:#38bdf8"><div class="ic"><i class="fe fe-percent"></i></div><div class="l">Avg water cut</div><div class="v">{{ number_format($stats['avgWc'], 1) }}<span style="font-size:.55em;color:var(--mute)"> %</span></div><div class="u">across all tests</div></div>
    </div>

    {{-- PROCESS STRIP --}}
    <div class="sec">
        <div class="panel rv">
            <div class="ph"><h3><i class="fe fe-git-merge"></i> Life of an MPFM test</h3><span class="sub">From task to comparison</span></div>
            <div class="flow">
                <div class="fs"><span class="n">1</span>
                    <svg class="ico" viewBox="0 0 96 96"><rect x="24" y="14" width="48" height="66" rx="7" fill="#0b1426" stroke="#60a5fa" stroke-width="2.5"/><path d="M34,34 H62 M34,46 H62 M34,58 H50" stroke="#60a5fa" stroke-width="3" stroke-linecap="round"/><circle cx="66" cy="68" r="12" fill="#3b82f6"/><path d="M66,62 V74 M60,68 H72" stroke="#fff" stroke-width="2.6" stroke-linecap="round"/></svg>
                    <b>Task assigned</b><small>Operator gets the well and date</small></div>
                <div class="fs"><span class="n">2</span>
                    <svg class="ico" viewBox="0 0 96 96"><path d="M30,12 H58 L72,26 V84 H30 Z" fill="#0b1426" stroke="#fbbf24" stroke-width="2.5" stroke-linejoin="round"/><path d="M58,12 V26 H72" fill="none" stroke="#fbbf24" stroke-width="2.5"/><path d="M51,70 V46 M42,54 L51,45 L60,54" fill="none" stroke="#fbbf24" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <b>Upload VX log</b><small>The meter's raw log file</small></div>
                <div class="fs"><span class="n">3</span>
                    <svg class="ico" viewBox="0 0 96 96"><rect x="14" y="20" width="68" height="50" rx="7" fill="#0b1426" stroke="#22d3ee" stroke-width="2.5"/><path d="M22,56 L34,42 L44,50 L58,32 L74,44" fill="none" stroke="#22d3ee" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/><path d="M36,80 H60" stroke="#22d3ee" stroke-width="3" stroke-linecap="round"/></svg>
                    <b>Parse &amp; validate</b><small>Rates, GOR, quality checks</small></div>
                <div class="fs"><span class="n">4</span>
                    <svg class="ico" viewBox="0 0 96 96"><circle cx="48" cy="48" r="32" fill="#0b1426" stroke="#34d399" stroke-width="2.5"/><path d="M32,48 L44,60 L66,36" fill="none" stroke="#34d399" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <b>Supervisor review</b><small>Approve, reject or retest</small></div>
                <div class="fs"><span class="n">5</span>
                    <svg class="ico" viewBox="0 0 96 96"><rect x="12" y="52" width="14" height="28" rx="3" fill="#fbbf24"/><rect x="30" y="38" width="14" height="42" rx="3" fill="#fbbf24"/><rect x="52" y="48" width="14" height="32" rx="3" fill="#22d3ee"/><rect x="70" y="30" width="14" height="50" rx="3" fill="#22d3ee"/><path d="M12,24 Q40,6 84,18" fill="none" stroke="#fff" stroke-width="2" stroke-dasharray="4 4"/></svg>
                    <b>Compare with TV</b><small>Deviation against the vessel</small></div>
            </div>
        </div>
    </div>

    {{-- CHARTS --}}
    <div class="sec g3">
        <div class="panel rv">
            <div class="ph"><h3><i class="fe fe-pie-chart"></i> By status</h3></div>
            <div class="donutw">
                <svg class="donut" viewBox="0 0 200 200">
                    <circle class="arc t" cx="100" cy="100" r="76" pathLength="100"/>
                    @foreach($seg as [$k, $lbl, $col])
                        @php $p = $total ? ($st[$k] ?? 0) / $total * 100 : 0; @endphp
                        @if($p > 0)<circle class="arc" cx="100" cy="100" r="76" pathLength="100" stroke="{{ $col }}" style="stroke-dasharray:{{ round($p, 2) }} 100;stroke-dashoffset:-{{ round($off, 2) }}"/>@endif
                        @php $off += $p; @endphp
                    @endforeach
                    <text x="100" y="104" text-anchor="middle" font-family="Space Grotesk" font-weight="700" font-size="34" fill="#fff">{{ $total }}</text>
                    <text x="100" y="124" text-anchor="middle" font-size="10" font-weight="700" letter-spacing="2" fill="#9fb0cf">TESTS</text>
                </svg>
                <div class="lgd">
                    @foreach($seg as [$k, $lbl, $col])<div><b style="background:{{ $col }}"></b>{{ $lbl }}<strong>{{ $st[$k] ?? 0 }}</strong></div>@endforeach
                </div>
            </div>
        </div>
        <div class="panel rv" style="--d:1">
            <div class="ph"><h3><i class="fe fe-bar-chart-2"></i> Tests per day</h3><span class="sub">Last 30 days</span></div>
            <div class="chart" id="actChart"></div>
        </div>
        <div class="panel rv" style="--d:2">
            <div class="ph"><h3><i class="fe fe-trending-up"></i> Busiest oil strings</h3></div>
            <div class="vb">
                @forelse($stats['strings'] as $v)
                    <div class="vr"><span title="{{ $v['name'] }}">{{ $v['name'] }}</span><div class="tr"><i style="--w:{{ round($v['count'] / $maxV * 100) }}%"></i></div><b>{{ $v['count'] }}</b></div>
                @empty
                    <div class="empty">No tests yet</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="sec">
        <div class="panel rv">
            <div class="ph"><h3><i class="fe fe-list"></i> All MPFM tests</h3><span class="sub">Filter, sort and open any test</span></div>
            <div class="filters">
                <div><label>Oil field</label>
                    <select id="oilFieldFilter" class="form-control"><option value="">All fields</option>@foreach($oilFields as $field)<option value="{{ $field->id }}">{{ $field->name }}</option>@endforeach</select></div>
                <div><label>Tower</label>
                    <select id="towerFilter" class="form-control"><option value="">All towers</option>@foreach($towers as $tower)<option value="{{ $tower->id }}">{{ $tower->name }}</option>@endforeach</select></div>
                <div><label>Operator</label>
                    <select id="userFilter" class="form-control"><option value="">All operators</option>@foreach($users as $user)<option value="{{ $user->id }}">{{ $user->name }}</option>@endforeach</select></div>
                <div><label>From</label><input type="date" id="dateFromFilter" class="form-control"></div>
                <div><label>To</label><input type="date" id="dateToFilter" class="form-control"></div>
                <button type="button" class="btn-r" id="resetFilters"><i class="fe fe-refresh-cw"></i> Reset</button>
            </div>
            <div class="table-responsive">
                <table class="table table-striped" id="mpfmTestsTable">
                    <thead><tr><th>ID</th><th>Date</th><th>Shift</th><th>Operator</th><th>Oil field</th><th>Tower</th><th>String</th><th>Oil</th><th>Gas</th><th>WC</th><th>GOR</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.min.js') }}"></script>
<script>
$(document).ready(function () {
    var table = $('#mpfmTestsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('mpfm_tests.index') }}',
            data: function (d) {
                d.oil_field_id = $('#oilFieldFilter').val();
                d.tower_id = $('#towerFilter').val();
                d.user_id = $('#userFilter').val();
                d.test_date_from = $('#dateFromFilter').val();
                d.test_date_to = $('#dateToFilter').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'test_date', name: 'test_date' },
            { data: 'shift', name: 'shift' },
            { data: 'user_name', name: 'user_id' },
            { data: 'oil_field_name', name: 'oil_field_id' },
            { data: 'tower_name', name: 'tower_id' },
            { data: 'oil_string_name', name: 'oil_string_id' },
            { data: 'oil_rate', name: 'oil_rate' },
            { data: 'gas_rate', name: 'gas_rate' },
            { data: 'water_cut', name: 'water_cut' },
            { data: 'gor', name: 'gor', orderable: false, searchable: false },
            { data: 'status_badge', name: 'status', orderable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        language: { processing: '<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>' }
    });
    $('#oilFieldFilter, #towerFilter, #userFilter, #dateFromFilter, #dateToFilter').on('change', function () { table.ajax.reload(); });
    $('#resetFilters').on('click', function () {
        $('#oilFieldFilter, #towerFilter, #userFilter, #dateFromFilter, #dateToFilter').val('');
        table.ajax.reload();
    });
});

(function () {
    var root = document.getElementById('mpl');
    var reduce = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;
    var act = @json($stats['activity']);

    function countUp(el) {
        var to = parseFloat(el.dataset.to) || 0, t0 = null;
        if (reduce) return;
        function step(t) {
            if (t0 === null) t0 = t;
            var p = Math.min((t - t0) / 1300, 1), e = 1 - Math.pow(1 - p, 4);
            el.textContent = Math.round(to * e).toLocaleString('en-US');
            if (p < 1) requestAnimationFrame(step);
        }
        requestAnimationFrame(step);
    }

    (function () {
        var el = document.getElementById('actChart'), v = act.values, l = act.labels, n = v.length;
        var W = 560, H = 210, pl = 30, pb = 26, pt = 10, max = Math.max(3, Math.max.apply(null, v));
        var bw = (W - pl) / n, s = '<svg viewBox="0 0 ' + W + ' ' + H + '"><defs><linearGradient id="actG" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#22d3ee"/><stop offset="1" stop-color="#3b82f6"/></linearGradient></defs>';
        for (var g = 0; g <= 3; g++) {
            var gy = pt + (1 - g / 3) * (H - pt - pb);
            s += '<line x1="' + pl + '" x2="' + W + '" y1="' + gy + '" y2="' + gy + '" stroke="rgba(255,255,255,.07)"/><text x="' + (pl - 6) + '" y="' + (gy + 4) + '" text-anchor="end" font-size="10" fill="#64748b">' + Math.round(max * g / 3) + '</text>';
        }
        v.forEach(function (c, i) {
            var h = c / max * (H - pt - pb), x = pl + i * bw + 2, y = H - pb - h;
            s += '<rect class="ab" data-i="' + i + '" x="' + x + '" y="' + (c ? y : H - pb - 2) + '" width="' + (bw - 4) + '" height="' + (c ? h : 2) + '" rx="3" fill="' + (c ? 'url(#actG)' : 'rgba(255,255,255,.08)') + '"><animate attributeName="height" from="0" to="' + (c ? h : 2) + '" dur="1s" begin="' + (i * 0.025) + 's" fill="freeze"/>' + (c ? '<animate attributeName="y" from="' + (H - pb) + '" to="' + y + '" dur="1s" begin="' + (i * 0.025) + 's" fill="freeze"/>' : '') + '</rect>';
            if (i % 5 === 0 || i === n - 1) s += '<text x="' + (x + (bw - 4) / 2) + '" y="' + (H - 8) + '" text-anchor="middle" font-size="10" fill="#64748b">' + l[i] + '</text>';
        });
        el.innerHTML = s + '</svg><div class="tip"></div>';
        var tip = el.querySelector('.tip');
        el.querySelectorAll('.ab').forEach(function (b) {
            b.addEventListener('mouseenter', function () {
                var i = +b.dataset.i, bb = b.getBoundingClientRect(), er = el.getBoundingClientRect();
                tip.style.opacity = 1; tip.style.left = (bb.left - er.left + bb.width / 2) + 'px'; tip.style.top = (bb.top - er.top) + 'px';
                tip.innerHTML = '<span style="color:#9fb0cf">' + l[i] + '</span><b>' + v[i] + ' test' + (v[i] === 1 ? '' : 's') + '</b>';
            });
            b.addEventListener('mouseleave', function () { tip.style.opacity = 0; });
        });
    })();

    var io = new IntersectionObserver(function (es) {
        es.forEach(function (e) {
            if (!e.isIntersecting) return;
            e.target.classList.add('in');
            e.target.querySelectorAll('.cu').forEach(countUp);
            io.unobserve(e.target);
        });
    }, { threshold: .12 });
    root.querySelectorAll('.rv').forEach(function (el) { io.observe(el); });

    if (reduce) root.querySelectorAll('svg').forEach(function (s) { if (s.pauseAnimations) s.pauseAnimations(); });
})();
</script>
@endpush
