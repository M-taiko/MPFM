@extends('layouts.master')

@section('title', 'Well Testing - Test Vessel Tests')

@section('css')
<link href="{{ URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
<style>
.tvl { direction:ltr; text-align:left; --bg:#050a15; --ink:#e8eefc; --sub:#9fb0cf; --mute:#64748b; --cyan:#22d3ee; --amber:#fbbf24; --green:#34d399; --red:#f87171; --water:#38bdf8;
    --line:rgba(96,165,250,.2); --glass:rgba(255,255,255,.05); --grad:linear-gradient(135deg,#3b82f6,#22d3ee); --ease:cubic-bezier(.22,1,.36,1);
    position:relative; overflow:hidden; isolation:isolate; padding:26px; border-radius:26px; background:var(--bg); color:var(--ink); font-family:'Inter',sans-serif; -webkit-font-smoothing:antialiased; }
.tvl *, .tvl *::before, .tvl *::after { box-sizing:border-box; }
.tvl a { text-decoration:none; }
.tvl h1, .tvl h3, .tvl p { margin:0; color:inherit; }
.tvl .bgl { position:absolute; inset:0; z-index:-1; pointer-events:none; overflow:hidden; }
.tvl .bgl::before { content:""; position:absolute; inset:-50%; background-image:linear-gradient(rgba(59,130,246,.07) 1px,transparent 1px),linear-gradient(90deg,rgba(59,130,246,.07) 1px,transparent 1px); background-size:56px 56px; transform:perspective(700px) rotateX(58deg) translateY(-6%); animation:tlGrid 14s linear infinite; mask-image:radial-gradient(ellipse at 50% 30%,#000 10%,transparent 70%); -webkit-mask-image:radial-gradient(ellipse at 50% 30%,#000 10%,transparent 70%); }
@keyframes tlGrid { to { background-position:0 56px; } }
.tvl .orb { position:absolute; border-radius:50%; filter:blur(90px); opacity:.3; animation:tlFloat 16s ease-in-out infinite; }
.tvl .o1 { width:440px; height:440px; background:#2563eb; top:-140px; left:-120px; }
.tvl .o2 { width:400px; height:400px; background:#0891b2; bottom:-160px; right:-100px; animation-delay:-6s; }
@keyframes tlFloat { 50% { transform:translate(40px,-40px) scale(1.12); } }
.tvl .lg { --h:84px; display:inline-block; flex:none; height:var(--h); aspect-ratio:1/1; border-radius:calc(var(--h) * .14); background:#071326 url('/assets/img/brand/logo.svg') center/cover no-repeat; border:2px solid rgba(34,211,238,.5); box-shadow:0 0 0 1px rgba(59,130,246,.5),0 8px 30px rgba(0,0,0,.45),0 0 26px rgba(34,211,238,.35); position:relative; overflow:hidden; animation:tlGlow 3.2s ease-in-out infinite; }
.tvl .lg::after { content:""; position:absolute; top:0; left:-130%; width:60%; height:100%; background:linear-gradient(90deg,transparent,rgba(34,211,238,.45),transparent); transform:skewX(-20deg); animation:tlShine 4.5s infinite; }
@keyframes tlShine { 60%,100% { left:160%; } }
@keyframes tlGlow { 50% { box-shadow:0 0 0 1px rgba(59,130,246,.6),0 10px 40px rgba(0,0,0,.5),0 0 54px rgba(34,211,238,.65); } }
.tvl .rv { opacity:0; transform:translateY(28px); transition:opacity .9s var(--ease), transform .9s var(--ease); transition-delay:calc(var(--d,0) * 90ms); }
.tvl .rv.in { opacity:1; transform:none; }

.tvl .hero { position:relative; display:grid; grid-template-columns:1fr 360px; gap:20px; align-items:center; padding:26px 30px; border-radius:22px; border:1px solid var(--line); background:linear-gradient(100deg,rgba(5,10,21,.94) 40%,rgba(5,10,21,.45)),url('/assets/img/brand/Offshore.jpg') center/cover; overflow:hidden; }
.tvl .who { display:flex; align-items:center; gap:22px; }
.tvl .eyebrow { font-size:11px; font-weight:700; letter-spacing:.16em; text-transform:uppercase; color:var(--cyan); display:flex; align-items:center; gap:8px; }
.tvl .eyebrow i { width:8px; height:8px; border-radius:50%; background:var(--green); animation:tlPing 1.8s infinite; }
@keyframes tlPing { to { box-shadow:0 0 0 12px rgba(52,211,153,0); } }
.tvl .hero h1 { font-size:clamp(24px,3vw,38px); font-weight:900; letter-spacing:-.03em; line-height:1.1; margin:6px 0 8px; }
.tvl .hero h1 em { font-style:normal; background:linear-gradient(120deg,#60a5fa,#22d3ee,#fbbf24,#60a5fa); background-size:250% 100%; -webkit-background-clip:text; background-clip:text; color:transparent; animation:tlFlow 6s linear infinite; }
@keyframes tlFlow { to { background-position:250% 0; } }
.tvl .hero p { color:var(--sub); font-size:13.5px; max-width:520px; line-height:1.6; }
.tvl .actions { display:flex; gap:10px; flex-wrap:wrap; margin-top:16px; }
.tvl .btn2 { display:inline-flex; align-items:center; gap:8px; padding:10px 18px; border-radius:50px; font-size:13px; font-weight:700; border:1px solid var(--line); background:var(--glass); color:var(--ink); transition:transform .3s var(--ease), border-color .3s; }
.tvl .btn2:hover { transform:translateY(-3px); border-color:var(--cyan); color:#fff; }
.tvl .btn2.p { background:var(--grad); color:#04111f; border:0; box-shadow:0 8px 24px rgba(34,211,238,.28); }
.tvl .btn2.p:hover { color:#04111f; }
.tvl .mini svg { width:100%; height:auto; display:block; }

.tvl .kpis { display:grid; grid-template-columns:repeat(5,1fr); gap:14px; margin-top:18px; }
.tvl .kpi { position:relative; padding:16px; border-radius:18px; border:1px solid var(--line); background:var(--glass); overflow:hidden; transition:transform .4s var(--ease), border-color .3s; }
.tvl .kpi:hover { transform:translateY(-5px); border-color:var(--c,var(--cyan)); }
.tvl .kpi::before { content:""; position:absolute; left:0; top:0; height:3px; width:100%; background:var(--c,var(--cyan)); }
.tvl .kpi .ic { width:34px; height:34px; border-radius:11px; display:grid; place-items:center; background:rgba(255,255,255,.07); color:var(--c,var(--cyan)); font-size:16px; margin-bottom:10px; }
.tvl .kpi .l { font-size:10.5px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--mute); }
.tvl .kpi .v { font:700 clamp(20px,1.9vw,27px)/1.1 'Space Grotesk',sans-serif; margin-top:4px; color:#fff; }
.tvl .kpi .u { font-size:11px; color:var(--sub); margin-top:2px; }

.tvl .sec { margin-top:20px; }
.tvl .panel { position:relative; border-radius:22px; border:1px solid var(--line); background:linear-gradient(180deg,rgba(255,255,255,.045),rgba(255,255,255,.015)); padding:22px; overflow:hidden; }
.tvl .ph { display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:16px; }
.tvl .ph h3 { font-size:16px; font-weight:800; display:flex; align-items:center; gap:10px; }
.tvl .ph h3 i { width:30px; height:30px; border-radius:10px; display:grid; place-items:center; background:var(--grad); color:#04111f; font-size:14px; }
.tvl .ph .sub { font-size:12px; color:var(--sub); }
.tvl .g3 { display:grid; grid-template-columns:1fr 1.5fr 1fr; gap:18px; }

/* process strip */
.tvl .flow { display:grid; grid-template-columns:repeat(5,1fr); gap:0; align-items:start; }
.tvl .fs { text-align:center; position:relative; padding:0 6px; }
.tvl .fs svg { width:96px; height:96px; }
.tvl .fs b { display:block; font-size:13px; margin-top:4px; }
.tvl .fs small { display:block; font-size:11.5px; color:var(--mute); margin-top:3px; line-height:1.4; }
.tvl .fs .n { position:absolute; top:0; left:calc(50% - 56px); width:22px; height:22px; border-radius:50%; background:var(--grad); color:#04111f; font:700 11px 'Space Grotesk'; display:grid; place-items:center; z-index:2; }
.tvl .fs:not(:last-child)::after { content:""; position:absolute; top:46px; left:calc(50% + 52px); width:calc(100% - 104px); border-top:2px dashed rgba(34,211,238,.5); animation:tlMarch 1s linear infinite; }
@keyframes tlMarch { to { border-top-color:rgba(34,211,238,.15); } }
.tvl .fs .ico { animation:tlBob 3.4s ease-in-out infinite; }
.tvl .fs:nth-child(2) .ico { animation-delay:-.6s; } .tvl .fs:nth-child(3) .ico { animation-delay:-1.2s; } .tvl .fs:nth-child(4) .ico { animation-delay:-1.8s; } .tvl .fs:nth-child(5) .ico { animation-delay:-2.4s; }
@keyframes tlBob { 50% { transform:translateY(-5px); } }

/* charts */
.tvl .donutw { display:flex; flex-direction:column; align-items:center; gap:14px; }
.tvl .donut { width:170px; height:170px; }
.tvl .arc { fill:none; stroke-width:14; transform:rotate(-90deg); transform-origin:50% 50%; animation:tlArc 1.6s var(--ease) both; }
.tvl .arc.t { stroke:rgba(255,255,255,.08); animation:none; }
@keyframes tlArc { from { stroke-dasharray:0 100; } }
.tvl .lgd { display:grid; grid-template-columns:1fr 1fr; gap:8px 18px; width:100%; }
.tvl .lgd div { display:flex; align-items:center; gap:8px; font-size:12.5px; color:var(--sub); }
.tvl .lgd b { width:11px; height:11px; border-radius:4px; }
.tvl .lgd strong { color:#fff; margin-left:auto; font:700 14px 'Space Grotesk'; }
.tvl .chart svg { width:100%; height:auto; display:block; overflow:visible; }
.tvl .chart { position:relative; }
.tvl .tip { position:absolute; pointer-events:none; opacity:0; transition:opacity .15s; background:#0b1426; border:1px solid var(--line); border-radius:10px; padding:6px 10px; font-size:12px; white-space:nowrap; transform:translate(-50%,-125%); z-index:3; }
.tvl .tip b { display:block; font:700 14px 'Space Grotesk'; }
.tvl .vb { display:grid; gap:12px; }
.tvl .vr { display:grid; grid-template-columns:90px 1fr 34px; gap:10px; align-items:center; font-size:12.5px; color:var(--sub); }
.tvl .vr span:first-child { white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.tvl .vr .tr { height:12px; border-radius:6px; background:rgba(255,255,255,.06); overflow:hidden; }
.tvl .vr .tr i { display:block; height:100%; width:0; border-radius:6px; background:var(--grad); transition:width 1.3s var(--ease); }
.tvl .in .vr .tr i { width:var(--w); }
.tvl .vr b { color:#fff; font:700 13px 'Space Grotesk'; text-align:right; }
.tvl .empty { color:var(--mute); text-align:center; padding:24px; font-size:13px; }

/* filters + table */
.tvl .filters { display:grid; grid-template-columns:repeat(5,1fr) auto; gap:12px; align-items:end; margin-bottom:16px; }
.tvl .filters label { display:block; font-size:10.5px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--mute); margin-bottom:6px; }
.tvl .filters .form-control { background:rgba(255,255,255,.06); border:1px solid var(--line); color:#fff; border-radius:12px; height:40px; font-size:13px; }
.tvl .filters .form-control:focus { border-color:var(--cyan); box-shadow:0 0 0 3px rgba(34,211,238,.15); background:rgba(255,255,255,.08); color:#fff; }
.tvl .filters option { background:#0b1426; color:#fff; }
.tvl .filters input[type=date] { color-scheme:dark; }
.tvl .btn-r { height:40px; padding:0 16px; border-radius:12px; border:1px solid var(--line); background:var(--glass); color:var(--ink); font-weight:700; font-size:13px; cursor:pointer; transition:border-color .3s; }
.tvl .btn-r:hover { border-color:var(--cyan); }
.tvl .dataTables_wrapper { color:var(--sub); font-size:13px; }
.tvl .dataTables_wrapper label, .tvl .dataTables_info { color:var(--sub); }
.tvl .dataTables_wrapper select, .tvl .dataTables_wrapper input[type=search] { background:rgba(255,255,255,.06); border:1px solid var(--line); color:#fff; border-radius:10px; padding:4px 10px; }
.tvl table.dataTable { width:100% !important; border-collapse:separate; border-spacing:0 8px; border:0; background:transparent; color:var(--ink); }
.tvl table.dataTable thead th { background:transparent; color:var(--mute); font-size:10.5px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; border:0 !important; padding:6px 12px; }
.tvl table.dataTable tbody td { background:#0c1528 !important; border:0 !important; border-top:1px solid var(--line) !important; border-bottom:1px solid var(--line) !important; padding:12px; color:var(--ink); vertical-align:middle; font-size:13px; }
.tvl table.dataTable tbody td:first-child { border-left:1px solid var(--line) !important; border-radius:14px 0 0 14px; font-weight:600; }
.tvl table.dataTable tbody td:last-child { border-right:1px solid var(--line) !important; border-radius:0 14px 14px 0; white-space:nowrap; }
.tvl table.dataTable tbody tr, .tvl table.dataTable tbody tr.odd, .tvl table.dataTable tbody tr.even, .tvl table.dataTable.table-striped tbody tr:nth-of-type(odd) { background:transparent !important; }
.tvl table.dataTable tbody tr { transition:transform .3s var(--ease); }
.tvl table.dataTable tbody tr:hover td { background:#0f2236 !important; }
.tvl table.dataTable tbody tr:hover { transform:translateX(4px); }
.tvl table.dataTable thead .sorting:before, .tvl table.dataTable thead .sorting:after, .tvl table.dataTable thead .sorting_asc:before, .tvl table.dataTable thead .sorting_desc:before, .tvl table.dataTable thead .sorting_asc:after, .tvl table.dataTable thead .sorting_desc:after { color:var(--cyan); }
.tvl .badge { padding:5px 12px; border-radius:50px; font-size:10.5px; font-weight:700; letter-spacing:.07em; text-transform:uppercase; border:1px solid var(--line); }
.tvl .badge-secondary { background:rgba(148,163,184,.12); color:#cbd5e1; }
.tvl .badge-info { background:rgba(59,130,246,.14); color:#93c5fd; border-color:rgba(59,130,246,.5); }
.tvl .badge-success { background:rgba(52,211,153,.12); color:var(--green); border-color:rgba(52,211,153,.5); }
.tvl .badge-danger { background:rgba(248,113,113,.12); color:var(--red); border-color:rgba(248,113,113,.5); }
.tvl td .btn { border-radius:50px; font-size:11.5px; font-weight:700; padding:4px 12px; border:1px solid var(--line); background:var(--glass); color:var(--ink); }
.tvl td .btn:hover { border-color:var(--cyan); color:#fff; }
.tvl td .btn-info { color:var(--cyan); } .tvl td .btn-primary { color:#93c5fd; } .tvl td .btn-danger { color:var(--red); } .tvl td .btn-outline-primary { color:var(--cyan); }
.tvl .page-item .page-link, .tvl .paginate_button { background:var(--glass) !important; border:1px solid var(--line) !important; color:var(--sub) !important; border-radius:10px !important; margin:0 2px; }
.tvl .page-item.active .page-link, .tvl .paginate_button.current { background:var(--grad) !important; color:#04111f !important; border:0 !important; }
.tvl div.dataTables_processing { background:rgba(11,20,38,.9); color:var(--cyan); border-radius:14px; border:1px solid var(--line); }

@media (max-width:1200px) { .tvl .kpis { grid-template-columns:repeat(3,1fr); } .tvl .filters { grid-template-columns:repeat(3,1fr); } .tvl .g3 { grid-template-columns:1fr 1fr; } }
@media (max-width:992px) { .tvl .hero { grid-template-columns:1fr; } .tvl .mini { max-width:380px; } .tvl .flow { grid-template-columns:1fr 1fr; row-gap:18px; } .tvl .fs::after { display:none; } .tvl .g3 { grid-template-columns:1fr; } }
@media (max-width:640px) { .tvl { padding:14px; border-radius:18px; } .tvl .who { flex-direction:column; align-items:flex-start; } .tvl .kpis, .tvl .filters { grid-template-columns:1fr 1fr; } }
@media (prefers-reduced-motion:reduce) { .tvl *, .tvl *::before, .tvl *::after { animation:none !important; transition:none !important; } .tvl .rv { opacity:1; transform:none; } .tvl .vr .tr i { width:var(--w); } }
</style>
@endsection

@section('content')
@php
    $st = $stats['byStatus'];
    $total = max($stats['total'], 0);
    $seg = [['draft', 'Draft', '#94a3b8'], ['submitted', 'Submitted', '#60a5fa'], ['approved', 'Approved', '#34d399'], ['rejected', 'Rejected', '#f87171']];
    $maxV = max(1, $stats['vessels']->max('count') ?? 1);
    $off = 0;
@endphp
<div class="tvl" id="tvl">
    <div class="bgl"><div class="orb o1"></div><div class="orb o2"></div></div>

    {{-- HERO --}}
    <div class="hero rv">
        <div>
            <div class="who">
                <span class="lg"></span>
                <div>
                    <div class="eyebrow"><i></i> Well Testing · Test vessel tests</div>
                    <h1>Reference tests <em>on the vessel</em></h1>
                </div>
            </div>
            <p style="margin-top:14px">Every test run on a test vessel is the reference reading the MPFM is checked against. Record, review and compare them here.</p>
            <div class="actions">
                @can('create', App\Models\TestVesselTest::class)
                    <a href="{{ route('tv_tests.create') }}" class="btn2 p"><i class="fe fe-plus"></i> New test</a>
                @endcan
                <a href="{{ route('tv_tests.compare') }}" class="btn2"><i class="fe fe-shuffle"></i> Compare vs MPFM</a>
            </div>
        </div>
        <div class="mini">
            <svg viewBox="0 0 360 190" aria-hidden="true">
                <defs>
                    <linearGradient id="mShell" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#a8b6cb"/><stop offset=".15" stop-color="#f1f5f9"/><stop offset=".5" stop-color="#7c8aa1"/><stop offset="1" stop-color="#1b2433"/></linearGradient>
                    <linearGradient id="mOil" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#f59e0b"/><stop offset="1" stop-color="#78350f"/></linearGradient>
                    <linearGradient id="mWat" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#0ea5e9"/><stop offset="1" stop-color="#0c4a6e"/></linearGradient>
                    <clipPath id="mClip"><path d="M70,52 H250 A22,38 0 0 1 250,128 H70 A22,38 0 0 1 70,52 Z"/></clipPath>
                    <path id="mIn" d="M6,40 H92 V50"/><path id="mGas" d="M230,50 V22 H350"/><path id="mOilP" d="M240,130 V168 H350"/>
                </defs>
                <g fill="none" stroke-linejoin="round">
                    <use href="#mIn" stroke="#0b1220" stroke-width="10"/><use href="#mIn" stroke="#64748b" stroke-width="7"/>
                    <use href="#mGas" stroke="#0b1220" stroke-width="10"/><use href="#mGas" stroke="#64748b" stroke-width="7"/>
                    <use href="#mOilP" stroke="#0b1220" stroke-width="10"/><use href="#mOilP" stroke="#64748b" stroke-width="7"/>
                </g>
                <path d="M48,46 H272 A24,44 0 0 1 272,134 H48 A24,44 0 0 1 48,46 Z" fill="url(#mShell)" stroke="#0b1220" stroke-width="1.5"/>
                <g clip-path="url(#mClip)">
                    <rect x="40" y="46" width="250" height="90" fill="#04101f"/>
                    <rect x="40" y="46" width="250" height="90" fill="#22d3ee" opacity=".1"/>
                    <rect x="40" y="82" width="190" height="54" fill="url(#mOil)"/>
                    <rect x="40" y="112" width="190" height="24" fill="url(#mWat)"/>
                    <rect x="232" y="102" width="60" height="34" fill="url(#mOil)"/>
                    <rect x="228" y="82" width="4" height="54" fill="#cbd5e1"/>
                    <g class="tvx"><path d="M30,82 q8,-4 16,0 t16,0 t16,0 t16,0 t16,0 t16,0 t16,0 t16,0 t16,0 t16,0 t16,0 t16,0 t16,0 t16,0" fill="none" stroke="#fde68a" stroke-opacity=".8" stroke-width="1.3"><animateTransform attributeName="transform" type="translate" from="0 0" to="-32 0" dur="2.6s" repeatCount="indefinite"/></path></g>
                    @for($i = 0; $i < 7; $i++)
                        <circle cx="{{ 90 + $i * 24 }}" cy="{{ 108 - ($i % 3) * 6 }}" r="2" fill="rgba(255,236,179,.7)"><animate attributeName="cy" values="{{ 110 - ($i % 3) * 6 }};86" dur="{{ 2.4 + ($i % 3) * .5 }}s" begin="-{{ $i * .4 }}s" repeatCount="indefinite"/><animate attributeName="opacity" values="0;1;0" dur="{{ 2.4 + ($i % 3) * .5 }}s" begin="-{{ $i * .4 }}s" repeatCount="indefinite"/></circle>
                    @endfor
                    <rect x="40" y="46" width="250" height="90" fill="#fff" opacity=".06"/>
                </g>
                <rect x="94" y="136" width="30" height="34" fill="#334155"/><rect x="224" y="136" width="30" height="34" fill="#334155"/><rect x="80" y="168" width="190" height="6" rx="2" fill="#475569"/>
                @foreach([0, .7, 1.4, 2.1] as $b)
                    <circle r="2.6" fill="#fde68a"><animateMotion dur="2.6s" begin="{{ $b }}s" repeatCount="indefinite"><mpath href="#mIn"/></animateMotion></circle>
                    <circle r="2.4" fill="#a5f3fc"><animateMotion dur="2.6s" begin="{{ $b }}s" repeatCount="indefinite"><mpath href="#mGas"/></animateMotion></circle>
                    <circle r="2.6" fill="#f59e0b"><animateMotion dur="2.8s" begin="{{ $b }}s" repeatCount="indefinite"><mpath href="#mOilP"/></animateMotion></circle>
                @endforeach
                <text x="8" y="30" font-size="9" font-weight="700" fill="#fcd34d" letter-spacing="1.2">WELL STREAM</text>
                <text x="300" y="14" font-size="9" font-weight="700" fill="#67e8f9" letter-spacing="1.2">GAS</text>
                <text x="306" y="160" font-size="9" font-weight="700" fill="#fcd34d" letter-spacing="1.2">OIL</text>
                <text x="160" y="96" font-size="9" font-weight="800" fill="#fff" opacity=".8" letter-spacing="2">V-101</text>
            </svg>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="kpis">
        <div class="kpi rv" style="--d:1;--c:#22d3ee"><div class="ic"><i class="fe fe-layers"></i></div><div class="l">Total tests</div><div class="v cu" data-to="{{ $stats['total'] }}" data-dec="0">{{ $stats['total'] }}</div><div class="u">all time</div></div>
        <div class="kpi rv" style="--d:2;--c:#a78bfa"><div class="ic"><i class="fe fe-calendar"></i></div><div class="l">Last 7 days</div><div class="v cu" data-to="{{ $stats['week'] }}" data-dec="0">{{ $stats['week'] }}</div><div class="u">tests</div></div>
        <div class="kpi rv" style="--d:3;--c:#34d399"><div class="ic"><i class="fe fe-check-circle"></i></div><div class="l">Approved</div><div class="v cu" data-to="{{ $st['approved'] ?? 0 }}" data-dec="0">{{ $st['approved'] ?? 0 }}</div><div class="u">{{ $total ? round((($st['approved'] ?? 0) / $total) * 100) : 0 }}% of total</div></div>
        <div class="kpi rv" style="--d:4;--c:#fbbf24"><div class="ic"><i class="fe fe-droplet"></i></div><div class="l">Avg oil rate</div><div class="v cu" data-to="{{ round($stats['avgOil'], 0) }}" data-dec="0">{{ number_format($stats['avgOil'], 0) }}</div><div class="u">B/D · water cut {{ number_format($stats['avgWc'], 1) }}%</div></div>
        <div class="kpi rv" style="--d:5;--c:#38bdf8"><div class="ic"><i class="fe fe-shuffle"></i></div><div class="l">Linked to MPFM</div><div class="v cu" data-to="{{ $stats['linked'] }}" data-dec="0">{{ $stats['linked'] }}</div><div class="u">ready for comparison</div></div>
    </div>

    {{-- PROCESS STRIP --}}
    <div class="sec">
        <div class="panel rv">
            <div class="ph"><h3><i class="fe fe-git-merge"></i> Life of a test vessel test</h3><span class="sub">From request to MPFM comparison</span></div>
            <div class="flow">
                <div class="fs"><span class="n">1</span>
                    <svg class="ico" viewBox="0 0 96 96"><rect x="24" y="14" width="48" height="66" rx="7" fill="#0b1426" stroke="#60a5fa" stroke-width="2.5"/><path d="M34,34 H62 M34,46 H62 M34,58 H50" stroke="#60a5fa" stroke-width="3" stroke-linecap="round"/><circle cx="66" cy="68" r="12" fill="#3b82f6"/><path d="M66,62 V74 M60,68 H72" stroke="#fff" stroke-width="2.6" stroke-linecap="round"/></svg>
                    <b>Test request</b><small>Wells and vessel are scheduled</small></div>
                <div class="fs"><span class="n">2</span>
                    <svg class="ico" viewBox="0 0 96 96"><path d="M16,34 H72 A12,14 0 0 1 72,62 H16 A12,14 0 0 1 16,34 Z" fill="#1e293b" stroke="#94a3b8" stroke-width="2.5"/><path d="M20,52 H70 A8,8 0 0 1 70,62 H20 Z" fill="#b45309"/><path d="M20,58 H70 V62 H20 Z" fill="#0ea5e9"/><path d="M10,48 H4 M84,38 H92 M84,60 H92" stroke="#94a3b8" stroke-width="4"/><circle cx="48" cy="22" r="9" fill="#0a1222" stroke="#22d3ee" stroke-width="2"/><path d="M48,22 L52,17" stroke="#fbbf24" stroke-width="2" stroke-linecap="round"/><path d="M48,31 V34" stroke="#94a3b8" stroke-width="3"/></svg>
                    <b>Run on vessel</b><small>Oil · gas · water cut, P &amp; T</small></div>
                <div class="fs"><span class="n">3</span>
                    <svg class="ico" viewBox="0 0 96 96"><path d="M14,48 L82,18 L62,80 L46,56 Z" fill="#0b1426" stroke="#22d3ee" stroke-width="2.5" stroke-linejoin="round"/><path d="M46,56 L82,18" stroke="#22d3ee" stroke-width="2.5"/><path d="M46,56 V74 L56,64" fill="none" stroke="#22d3ee" stroke-width="2.5" stroke-linejoin="round"/></svg>
                    <b>Submit</b><small>Operator sends it for review</small></div>
                <div class="fs"><span class="n">4</span>
                    <svg class="ico" viewBox="0 0 96 96"><circle cx="48" cy="48" r="32" fill="#0b1426" stroke="#34d399" stroke-width="2.5"/><path d="M32,48 L44,60 L66,36" fill="none" stroke="#34d399" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <b>Review</b><small>Approved or sent back</small></div>
                <div class="fs"><span class="n">5</span>
                    <svg class="ico" viewBox="0 0 96 96"><rect x="12" y="52" width="14" height="28" rx="3" fill="#fbbf24"/><rect x="30" y="38" width="14" height="42" rx="3" fill="#fbbf24"/><rect x="52" y="48" width="14" height="32" rx="3" fill="#22d3ee"/><rect x="70" y="30" width="14" height="50" rx="3" fill="#22d3ee"/><path d="M12,24 Q40,6 84,18" fill="none" stroke="#fff" stroke-width="2" stroke-dasharray="4 4"/></svg>
                    <b>Compare with MPFM</b><small>Deviation against ±5 %</small></div>
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
            <div class="ph"><h3><i class="fe fe-anchor"></i> Busiest vessels</h3></div>
            <div class="vb">
                @forelse($stats['vessels'] as $v)
                    <div class="vr"><span title="{{ $v['name'] }}">{{ $v['name'] }}</span><div class="tr"><i style="--w:{{ round($v['count'] / $maxV * 100) }}%"></i></div><b>{{ $v['count'] }}</b></div>
                @empty
                    <div class="empty">No vessels yet</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="sec">
        <div class="panel rv">
            <div class="ph"><h3><i class="fe fe-list"></i> All tests</h3><span class="sub">Filter, sort and open any test</span></div>
            <div class="filters">
                <div><label>Oil field</label>
                    <select id="oilFieldFilter" class="form-control"><option value="">All fields</option>@foreach($oilFields as $field)<option value="{{ $field->id }}">{{ $field->name }}</option>@endforeach</select></div>
                <div><label>Test vessel</label>
                    <select id="testVesselFilter" class="form-control"><option value="">All vessels</option>@foreach($testVessels as $vessel)<option value="{{ $vessel->id }}">{{ $vessel->name }}</option>@endforeach</select></div>
                <div><label>Status</label>
                    <select id="statusFilter" class="form-control"><option value="">All status</option><option value="draft">Draft</option><option value="submitted">Submitted</option><option value="approved">Approved</option><option value="rejected">Rejected</option></select></div>
                <div><label>From</label><input type="date" id="dateFromFilter" class="form-control"></div>
                <div><label>To</label><input type="date" id="dateToFilter" class="form-control"></div>
                <button type="button" class="btn-r" id="resetFilters"><i class="fe fe-refresh-cw"></i> Reset</button>
            </div>
            <div class="table-responsive">
                <table class="table table-striped" id="tvTestsTable">
                    <thead><tr><th>Date</th><th>Shift</th><th>Vessel</th><th>Operator</th><th>Oil field</th><th>Tower</th><th>String</th><th>Oil rate</th><th>MPFM test</th><th>Status</th><th>Actions</th></tr></thead>
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
    var table = $('#tvTestsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('tv_tests.index') }}',
            type: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            data: function (d) {
                d.oil_field_id = $('#oilFieldFilter').val();
                d.test_vessel_id = $('#testVesselFilter').val();
                d.status = $('#statusFilter').val();
                d.date_from = $('#dateFromFilter').val();
                d.date_to = $('#dateToFilter').val();
            }
        },
        columns: [
            { data: 'test_date', name: 'test_date' },
            { data: 'shift', name: 'shift' },
            { data: 'vessel_name', name: 'vessel_name', orderable: false, searchable: false },
            { data: 'operator', name: 'operator', orderable: false, searchable: false },
            { data: 'oil_field_name', name: 'oil_field_name', orderable: false, searchable: false },
            { data: 'tower_name', name: 'tower_name', orderable: false, searchable: false },
            { data: 'oil_string_name', name: 'oil_string_name', orderable: false, searchable: false },
            { data: 'oil_rate', name: 'oil_rate' },
            { data: 'mpfm_test_link', name: 'mpfm_test_link', orderable: false, searchable: false },
            { data: 'status_badge', name: 'status', orderable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        language: { processing: '<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>' }
    });
    $('#oilFieldFilter, #testVesselFilter, #statusFilter, #dateFromFilter, #dateToFilter').on('change', function () { table.draw(); });
    $('#resetFilters').on('click', function () {
        $('#oilFieldFilter, #testVesselFilter, #statusFilter, #dateFromFilter, #dateToFilter').val('');
        table.draw();
    });
});

(function () {
    var root = document.getElementById('tvl');
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

    // activity bars
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
        var tip = el.querySelector('.tip'), svg = el.querySelector('svg');
        el.querySelectorAll('.ab').forEach(function (b) {
            b.addEventListener('mouseenter', function () {
                var i = +b.dataset.i, r = svg.getBoundingClientRect(), bb = b.getBoundingClientRect(), er = el.getBoundingClientRect();
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
            if (e.target.classList.contains('kpi')) e.target.querySelectorAll('.cu').forEach(function () {});
            io.unobserve(e.target);
        });
    }, { threshold: .12 });
    root.querySelectorAll('.rv').forEach(function (el) { io.observe(el); });

    if (reduce) root.querySelectorAll('svg').forEach(function (s) { if (s.pauseAnimations) s.pauseAnimations(); });
})();
</script>
@endpush
