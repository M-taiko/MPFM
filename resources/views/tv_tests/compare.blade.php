@extends('layouts.master')

@section('title', 'Compare Test Vessel with MPFM')

@section('css')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
<style>
.tvc { direction:ltr; text-align:left; --bg:#050a15; --ink:#e8eefc; --sub:#9fb0cf; --mute:#64748b; --cyan:#22d3ee; --amber:#fbbf24; --green:#34d399; --red:#f87171;
    --line:rgba(96,165,250,.2); --glass:rgba(255,255,255,.05); --grad:linear-gradient(135deg,#3b82f6,#22d3ee); --ease:cubic-bezier(.22,1,.36,1);
    position:relative; overflow:hidden; isolation:isolate; padding:26px; border-radius:26px; background:var(--bg); color:var(--ink); font-family:'Inter',sans-serif; -webkit-font-smoothing:antialiased; }
.tvc *, .tvc *::before, .tvc *::after { box-sizing:border-box; }
.tvc a { text-decoration:none; }
.tvc h1, .tvc h3, .tvc p { margin:0; color:inherit; }
.tvc .bgl { position:absolute; inset:0; z-index:-1; pointer-events:none; overflow:hidden; }
.tvc .bgl::before { content:""; position:absolute; inset:-50%; background-image:linear-gradient(rgba(59,130,246,.07) 1px,transparent 1px),linear-gradient(90deg,rgba(59,130,246,.07) 1px,transparent 1px); background-size:56px 56px; transform:perspective(700px) rotateX(58deg) translateY(-6%); animation:tcGrid 14s linear infinite; mask-image:radial-gradient(ellipse at 50% 30%,#000 10%,transparent 70%); -webkit-mask-image:radial-gradient(ellipse at 50% 30%,#000 10%,transparent 70%); }
@keyframes tcGrid { to { background-position:0 56px; } }
.tvc .orb { position:absolute; border-radius:50%; filter:blur(90px); opacity:.3; animation:tcFloat 16s ease-in-out infinite; }
.tvc .o1 { width:440px; height:440px; background:#2563eb; top:-140px; left:-120px; }
.tvc .o2 { width:400px; height:400px; background:#0891b2; bottom:-160px; right:-100px; animation-delay:-6s; }
@keyframes tcFloat { 50% { transform:translate(40px,-40px) scale(1.12); } }
.tvc .lg { --h:76px; display:inline-block; flex:none; height:var(--h); aspect-ratio:600/940; border-radius:calc(var(--h) * .14); background:#fff url('/assets/img/brand/logo.png') 50% 50.8%/333.33% auto no-repeat; border:2px solid rgba(255,255,255,.9); box-shadow:0 0 0 1px rgba(59,130,246,.5),0 8px 30px rgba(0,0,0,.45),0 0 26px rgba(34,211,238,.35); position:relative; overflow:hidden; animation:tcGlow 3.2s ease-in-out infinite; }
.tvc .lg::after { content:""; position:absolute; top:0; left:-130%; width:60%; height:100%; background:linear-gradient(90deg,transparent,rgba(34,211,238,.45),transparent); transform:skewX(-20deg); animation:tcShine 4.5s infinite; }
@keyframes tcShine { 60%,100% { left:160%; } }
@keyframes tcGlow { 50% { box-shadow:0 0 0 1px rgba(59,130,246,.6),0 10px 40px rgba(0,0,0,.5),0 0 54px rgba(34,211,238,.65); } }
.tvc .rv { opacity:0; transform:translateY(26px); transition:opacity .8s var(--ease), transform .8s var(--ease); transition-delay:calc(var(--d,0) * 80ms); }
.tvc .rv.in { opacity:1; transform:none; }

.tvc .hero { display:flex; align-items:center; justify-content:space-between; gap:20px; flex-wrap:wrap; padding:24px 28px; border-radius:22px; border:1px solid var(--line); background:linear-gradient(100deg,rgba(5,10,21,.94) 45%,rgba(5,10,21,.5)),url('/assets/img/brand/Offshore.jpg') center/cover; }
.tvc .who { display:flex; align-items:center; gap:20px; }
.tvc .eyebrow { font-size:11px; font-weight:700; letter-spacing:.16em; text-transform:uppercase; color:var(--cyan); }
.tvc .hero h1 { font-size:clamp(22px,2.6vw,34px); font-weight:900; letter-spacing:-.03em; margin:5px 0 6px; }
.tvc .hero h1 em { font-style:normal; background:linear-gradient(120deg,#60a5fa,#22d3ee,#fbbf24,#60a5fa); background-size:250% 100%; -webkit-background-clip:text; background-clip:text; color:transparent; animation:tcFlow 6s linear infinite; }
@keyframes tcFlow { to { background-position:250% 0; } }
.tvc .hero p { color:var(--sub); font-size:13px; max-width:560px; line-height:1.55; }
.tvc .btn2 { display:inline-flex; align-items:center; gap:8px; padding:11px 20px; border-radius:50px; font-size:13px; font-weight:700; border:1px solid var(--line); background:var(--glass); color:var(--ink); cursor:pointer; transition:transform .3s var(--ease), border-color .3s, box-shadow .3s; font-family:inherit; }
.tvc .btn2:hover { transform:translateY(-3px); border-color:var(--cyan); color:#fff; }
.tvc .btn2.p { background:var(--grad); color:#04111f; border:0; box-shadow:0 8px 24px rgba(34,211,238,.28); }
.tvc .btn2.p:hover { color:#04111f; box-shadow:0 12px 32px rgba(34,211,238,.45); }
.tvc .btn2:disabled { opacity:.4; cursor:not-allowed; transform:none; box-shadow:none; }

.tvc .pick { display:grid; grid-template-columns:1fr 84px 1fr; gap:14px; margin-top:20px; align-items:stretch; }
.tvc .panel { position:relative; border-radius:22px; border:1px solid var(--line); background:linear-gradient(180deg,rgba(255,255,255,.045),rgba(255,255,255,.015)); padding:20px; display:flex; flex-direction:column; min-width:0; }
.tvc .panel.tv { border-top:3px solid var(--amber); } .tvc .panel.mp { border-top:3px solid var(--cyan); }
.tvc .ph { display:flex; align-items:center; gap:14px; margin-bottom:14px; }
.tvc .ph svg { width:64px; height:44px; flex:none; }
.tvc .ph h3 { font-size:16px; font-weight:800; }
.tvc .ph small { display:block; font-size:12px; color:var(--mute); font-weight:500; margin-top:2px; }
.tvc .ph .cnt { margin-left:auto; font:700 12px 'Space Grotesk'; padding:4px 11px; border-radius:50px; border:1px solid var(--line); background:var(--glass); color:var(--sub); }
.tvc .srch { position:relative; margin-bottom:10px; }
.tvc .srch i { position:absolute; left:13px; top:50%; transform:translateY(-50%); color:var(--mute); font-size:14px; }
.tvc .srch input { width:100%; height:42px; padding:0 14px 0 38px; border-radius:14px; border:1px solid var(--line); background:rgba(255,255,255,.06); color:#fff; font:500 13.5px 'Inter'; outline:none; transition:border-color .25s, box-shadow .25s; }
.tvc .srch input:focus { border-color:var(--cyan); box-shadow:0 0 0 4px rgba(34,211,238,.14); }
.tvc .srch input::placeholder { color:#52627d; }
.tvc .chips { display:flex; gap:7px; flex-wrap:wrap; margin-bottom:10px; }
.tvc .chip { padding:5px 12px; border-radius:50px; font-size:11.5px; font-weight:700; border:1px solid var(--line); background:var(--glass); color:var(--sub); cursor:pointer; transition:all .25s var(--ease); user-select:none; }
.tvc .chip:hover { border-color:var(--cyan); color:#fff; }
.tvc .chip.on { background:rgba(34,211,238,.15); border-color:var(--cyan); color:#fff; }
.tvc .list { display:grid; gap:8px; max-height:380px; overflow:auto; padding:2px 4px 2px 2px; scrollbar-width:thin; scrollbar-color:rgba(96,165,250,.35) transparent; }
.tvc .item { display:grid; grid-template-columns:1fr auto; gap:4px 10px; padding:12px 14px; border-radius:15px; border:1px solid var(--line); background:rgba(255,255,255,.04); cursor:pointer; transition:transform .25s var(--ease), border-color .25s, background .25s, box-shadow .25s; position:relative; }
.tvc .item:hover { transform:translateX(4px); border-color:rgba(34,211,238,.5); }
.tvc .item.sel { border-color:var(--c,var(--cyan)); background:rgba(255,255,255,.09); box-shadow:0 0 0 1px var(--c,var(--cyan)), 0 0 24px rgba(34,211,238,.18); }
.tvc .item.sel::after { content:"\2713"; position:absolute; right:-8px; top:-8px; width:22px; height:22px; border-radius:50%; background:var(--c,var(--cyan)); color:#04111f; font-size:12px; font-weight:900; display:grid; place-items:center; }
.tvc .item b { font-size:13.5px; color:#fff; font-weight:700; }
.tvc .item small { color:var(--sub); font-size:12px; }
.tvc .item .r { text-align:right; font:700 14px 'Space Grotesk'; color:#fff; }
.tvc .item .r span { display:block; font:500 10.5px 'Inter'; color:var(--mute); }
.tvc .st { display:inline-block; padding:2px 9px; border-radius:50px; font-size:9.5px; font-weight:700; letter-spacing:.07em; text-transform:uppercase; border:1px solid var(--line); margin-left:6px; vertical-align:1px; }
.tvc .st.draft { color:#cbd5e1; } .tvc .st.submitted { color:#93c5fd; border-color:rgba(59,130,246,.5); } .tvc .st.approved { color:var(--green); border-color:rgba(52,211,153,.5); } .tvc .st.rejected { color:var(--red); border-color:rgba(248,113,113,.5); }
.tvc .same { display:inline-flex; align-items:center; gap:4px; margin-left:6px; padding:2px 9px; border-radius:50px; font-size:9.5px; font-weight:700; letter-spacing:.07em; text-transform:uppercase; color:var(--green); background:rgba(52,211,153,.12); border:1px solid rgba(52,211,153,.45); }
.tvc .none { text-align:center; padding:28px 12px; color:var(--mute); font-size:13px; border:2px dashed var(--line); border-radius:16px; }

/* centre link */
.tvc .mid { display:flex; flex-direction:column; align-items:center; justify-content:center; gap:10px; }
.tvc .mid svg { width:70px; height:auto; overflow:visible; }
.tvc .mid .vs { font:700 14px 'Space Grotesk'; width:44px; height:44px; border-radius:50%; display:grid; place-items:center; background:var(--grad); color:#04111f; box-shadow:0 0 28px rgba(34,211,238,.4); animation:tcPulse 2.4s ease-in-out infinite; }
@keyframes tcPulse { 50% { box-shadow:0 0 0 12px rgba(34,211,238,0); } }
.tvc .mid .ln { flex:1; width:2px; background:repeating-linear-gradient(180deg,rgba(34,211,238,.6) 0 6px,transparent 6px 12px); animation:tcMarch 1s linear infinite; min-height:30px; }
@keyframes tcMarch { to { background-position:0 12px; } }

/* preview */
.tvc .prev { margin-top:16px; }
.tvc .status { display:flex; align-items:center; gap:12px; padding:14px 18px; border-radius:16px; border:1px solid var(--line); background:var(--glass); font-size:13.5px; color:var(--sub); margin-bottom:16px; }
.tvc .status i { font-size:20px; }
.tvc .status.ok { border-color:rgba(52,211,153,.5); background:rgba(52,211,153,.08); color:#bbf7d0; } .tvc .status.ok i { color:var(--green); }
.tvc .status.warn { border-color:rgba(251,191,36,.5); background:rgba(251,191,36,.08); color:#fde68a; } .tvc .status.warn i { color:var(--amber); }
.tvc .crow { display:grid; grid-template-columns:120px 1fr 92px; gap:16px; align-items:center; padding:10px 0; border-top:1px solid rgba(255,255,255,.06); }
.tvc .crow:first-of-type { border-top:0; }
.tvc .crow .n { font-size:12px; font-weight:700; letter-spacing:.07em; text-transform:uppercase; color:var(--sub); }
.tvc .crow .n span { display:block; font-weight:500; text-transform:none; letter-spacing:0; color:var(--mute); margin-top:2px; }
.tvc .bars { display:grid; gap:6px; }
.tvc .bar { height:22px; border-radius:7px; background:rgba(255,255,255,.05); overflow:hidden; position:relative; }
.tvc .bar i { position:absolute; inset:0 auto 0 0; width:0; border-radius:7px; transition:width 1s var(--ease); display:flex; align-items:center; padding-left:9px; font:600 11px 'Inter'; font-style:normal; color:#04111f; white-space:nowrap; }
.tvc .bar .lb { position:absolute; left:9px; top:0; bottom:0; display:flex; align-items:center; font:600 11px 'Inter'; color:#fff; text-shadow:0 1px 3px rgba(0,0,0,.8); white-space:nowrap; }
.tvc .bar.a i { background:linear-gradient(90deg,#f59e0b,#fbbf24); } .tvc .bar.b i { background:linear-gradient(90deg,#0891b2,#22d3ee); }
.tvc .dev { text-align:center; padding:6px 0; border-radius:50px; font:700 13px 'Space Grotesk'; }
.tvc .dev.ok { color:var(--green); background:rgba(52,211,153,.12); border:1px solid rgba(52,211,153,.4); }
.tvc .dev.warn { color:var(--amber); background:rgba(251,191,36,.1); border:1px solid rgba(251,191,36,.4); }
.tvc .dev.bad { color:var(--red); background:rgba(248,113,113,.1); border:1px solid rgba(248,113,113,.4); }
.tvc .dev.na { color:var(--mute); border:1px solid var(--line); }
.tvc .go { display:flex; align-items:center; gap:14px; flex-wrap:wrap; margin-top:18px; padding-top:18px; border-top:1px solid var(--line); }
.tvc .go .sp { flex:1; } .tvc .go .hint { font-size:12px; color:var(--mute); }
.tvc .empty { text-align:center; padding:26px 10px; color:var(--mute); font-size:13.5px; }
.tvc .empty svg { width:120px; display:block; margin:0 auto 10px; opacity:.85; }

@media (max-width:1100px) { .tvc .pick { grid-template-columns:1fr; } .tvc .mid { flex-direction:row; } .tvc .mid .ln { width:auto; height:2px; flex:1; background:repeating-linear-gradient(90deg,rgba(34,211,238,.6) 0 6px,transparent 6px 12px); min-height:0; } .tvc .mid svg { display:none; } }
@media (max-width:640px) { .tvc { padding:14px; border-radius:18px; } .tvc .who { flex-direction:column; align-items:flex-start; } .tvc .crow { grid-template-columns:1fr; gap:6px; } }
@media (prefers-reduced-motion:reduce) { .tvc *, .tvc *::before, .tvc *::after { animation:none !important; transition:none !important; } .tvc .rv { opacity:1; transform:none; } }
</style>
@endsection

@section('content')
@php
    $tvJson = $tvTests->map(fn ($t) => [
        'id' => $t->id,
        'vessel' => $t->testVessel->name ?? '—',
        'field' => $t->oilField->name ?? '—',
        'tower' => $t->tower->name ?? '—',
        'string' => $t->oilString->name ?? '—',
        'string_id' => $t->oil_string_id,
        'date' => $t->test_date->format('Y-m-d'),
        'status' => $t->status,
        'oil' => (float) $t->oil_rate, 'gas' => (float) $t->gas_rate, 'wc' => (float) $t->water_cut,
    ])->values();
    $mpJson = $mpfmTests->map(fn ($t) => [
        'id' => $t->id,
        'field' => $t->oilField->name ?? '—',
        'field_id' => $t->oil_field_id,
        'tower' => $t->tower->name ?? '—',
        'string' => $t->oilString->name ?? '—',
        'string_id' => $t->oil_string_id,
        'date' => $t->test_date->format('Y-m-d'),
        'status' => $t->status,
        'oil' => (float) $t->oil_rate, 'gas' => (float) $t->gas_rate, 'wc' => (float) $t->water_cut,
    ])->values();
    $fields = $mpfmTests->pluck('oilField')->filter()->unique('id')->values();
@endphp
<div class="tvc" id="tvc">
    <div class="bgl"><div class="orb o1"></div><div class="orb o2"></div></div>

    <div class="hero rv">
        <div class="who">
            <span class="lg"></span>
            <div>
                <div class="eyebrow">Test Vessel vs MPFM</div>
                <h1>Compare <em>two readings</em></h1>
                <p>Pick a test vessel test as the reference and an MPFM test to check against it. Tests on the same oil string give the most meaningful comparison.</p>
            </div>
        </div>
        <a href="{{ route('tv_tests.index') }}" class="btn2"><i class="fe fe-arrow-left"></i> Back to list</a>
    </div>

    <form method="GET" action="{{ route('tv_tests.compare_result') }}" id="compareForm">
        <input type="hidden" name="tv_test_id" id="tv_test_id">
        <input type="hidden" name="mpfm_test_id" id="mpfm_test_id">

        <div class="pick">
            {{-- TV --}}
            <div class="panel tv rv">
                <div class="ph">
                    <svg viewBox="0 0 64 44" aria-hidden="true"><path d="M4,10 H44 A10,12 0 0 1 44,34 H4 A10,12 0 0 1 4,10 Z" fill="#1e293b" stroke="#94a3b8" stroke-width="2"/><path d="M8,26 H46 A6,6 0 0 1 46,34 H8 Z" fill="#b45309"/><rect x="8" y="30" width="38" height="4" fill="#0ea5e9"/><path d="M0,16 H4 M54,14 H64 M54,30 H64" stroke="#94a3b8" stroke-width="3"/><circle cx="24" cy="6" r="5" fill="#0a1222" stroke="#fbbf24" stroke-width="1.6"/></svg>
                    <div><h3>Test vessel</h3><small>Reference reading</small></div><span class="cnt" id="tvCnt">0</span>
                </div>
                <div class="srch"><i class="fe fe-search"></i><input type="text" id="tvQ" placeholder="Search vessel, string, date…" autocomplete="off"></div>
                <div class="list" id="tvList"></div>
            </div>

            <div class="mid rv" style="--d:1">
                <div class="ln"></div>
                <svg viewBox="0 0 70 70" aria-hidden="true"><g fill="none" stroke="#22d3ee" stroke-width="2" stroke-linecap="round"><path d="M12,24 H50 l-8,-8 M58,46 H20 l8,8"><animate attributeName="stroke-opacity" values="1;.35;1" dur="2s" repeatCount="indefinite"/></path></g></svg>
                <div class="vs">VS</div>
                <div class="ln"></div>
            </div>

            {{-- MPFM --}}
            <div class="panel mp rv" style="--d:2">
                <div class="ph">
                    <svg viewBox="0 0 64 44" aria-hidden="true"><path d="M0,26 H14 L22,18 H42 L50,26 H64 V34 H50 L42,40 H22 L14,34 H0 Z" fill="#1e293b" stroke="#94a3b8" stroke-width="2" stroke-linejoin="round"/><path d="M32,4 V18" stroke="#94a3b8" stroke-width="3"/><circle cx="32" cy="6" r="5" fill="#0a1222" stroke="#22d3ee" stroke-width="1.6"/><path d="M26,30 H38" stroke="#22d3ee" stroke-width="2" stroke-dasharray="3 3"><animate attributeName="stroke-dashoffset" from="0" to="-12" dur="1s" repeatCount="indefinite"/></path></svg>
                    <div><h3>MPFM test</h3><small>Multiphase meter reading</small></div><span class="cnt" id="mpCnt">0</span>
                </div>
                <div class="chips" id="mpChips"><span class="chip on" data-f="">All fields</span>@foreach($fields as $f)<span class="chip" data-f="{{ $f->id }}">{{ $f->name }}</span>@endforeach</div>
                <div class="chips"><span class="chip" id="sameOnly" style="display:none"><i class="fe fe-check-circle"></i> Same oil string only</span></div>
                <div class="srch"><i class="fe fe-search"></i><input type="text" id="mpQ" placeholder="Search string, tower, date…" autocomplete="off"></div>
                <div class="list" id="mpList"></div>
            </div>
        </div>

        {{-- PREVIEW --}}
        <div class="panel prev rv" style="--d:1">
            <div id="prevBody"></div>
            <div class="go">
                <button type="submit" class="btn2 p" id="submitBtn" disabled><i class="fe fe-arrow-right"></i> Compare tests</button>
                <a href="{{ route('tv_tests.index') }}" class="btn2"><i class="fe fe-x"></i> Cancel</a>
                <span class="sp"></span><span class="hint" id="goHint">Select one test on each side</span>
            </div>
        </div>
    </form>
</div>
@endsection

@section('js')
<script>
(function () {
    var TV = @json($tvJson), MP = @json($mpJson);
    var $ = function (id) { return document.getElementById(id); };
    var selTv = null, selMp = null, field = '', same = false;
    var fmt = function (v, d) { return Number(v).toLocaleString('en-US', { minimumFractionDigits: d, maximumFractionDigits: d }); };
    function el(tag, cls, txt) { var e = document.createElement(tag); if (cls) e.className = cls; if (txt != null) e.textContent = txt; return e; }

    function card(t, kind, opts) {
        var c = el('div', 'item' + (opts.sel ? ' sel' : '')); c.style.setProperty('--c', kind === 'tv' ? '#fbbf24' : '#22d3ee'); c.tabIndex = 0;
        var l = el('div'); var b = el('b', null, kind === 'tv' ? 'TV #' + t.id + ' · ' + t.vessel : 'MPFM #' + t.id + ' · ' + t.field);
        var s = el('span', 'st ' + t.status, t.status); b.appendChild(s);
        if (opts.same) { var sm = el('span', 'same', 'same string'); b.appendChild(sm); }
        l.appendChild(b); l.appendChild(el('br'));
        l.appendChild(el('small', null, (kind === 'tv' ? t.field + ' · ' : t.tower + ' · ') + t.string + ' · ' + t.date));
        var r = el('div', 'r', fmt(t.oil, 0)); r.appendChild(el('span', null, 'B/D · WC ' + fmt(t.wc, 1) + '%'));
        c.appendChild(l); c.appendChild(r);
        c.addEventListener('click', opts.pick); c.addEventListener('keydown', function (e) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); opts.pick(); } });
        return c;
    }

    function renderTv() {
        var q = $('tvQ').value.toLowerCase(), box = $('tvList'); box.innerHTML = '';
        var rows = TV.filter(function (t) { return !q || (t.vessel + ' ' + t.string + ' ' + t.date + ' ' + t.field + ' ' + t.id).toLowerCase().indexOf(q) > -1; });
        $('tvCnt').textContent = rows.length;
        if (!rows.length) box.appendChild(el('div', 'none', 'No test vessel tests found'));
        rows.forEach(function (t) { box.appendChild(card(t, 'tv', { sel: selTv && selTv.id === t.id, pick: function () { selTv = (selTv && selTv.id === t.id) ? null : t; sync(); } })); });
    }
    function renderMp() {
        var q = $('mpQ').value.toLowerCase(), box = $('mpList'); box.innerHTML = '';
        var rows = MP.filter(function (t) {
            if (field && String(t.field_id) !== field) return false;
            if (same && selTv && t.string_id !== selTv.string_id) return false;
            return !q || (t.string + ' ' + t.tower + ' ' + t.date + ' ' + t.field + ' ' + t.id).toLowerCase().indexOf(q) > -1;
        });
        if (selTv) rows.sort(function (a, b) { return (b.string_id === selTv.string_id) - (a.string_id === selTv.string_id); });
        $('mpCnt').textContent = rows.length;
        if (!rows.length) box.appendChild(el('div', 'none', same ? 'No MPFM test on this oil string' : 'No MPFM tests found'));
        rows.forEach(function (t) { box.appendChild(card(t, 'mp', { sel: selMp && selMp.id === t.id, same: selTv && t.string_id === selTv.string_id, pick: function () { selMp = (selMp && selMp.id === t.id) ? null : t; sync(); } })); });
    }

    // ---- preview -----------------------------------------------------------
    function dev(m, t) { return t > 0 ? (m - t) / t * 100 : null; }
    function cls(d, pt) { if (d === null) return 'na'; var a = Math.abs(d); return pt ? (a <= 2 ? 'ok' : a <= 5 ? 'warn' : 'bad') : (a <= 5 ? 'ok' : a <= 10 ? 'warn' : 'bad'); }
    function row(name, unit, tv, mp, d, pt, dec) {
        var mx = Math.max(tv, mp, 0.0001), r = el('div', 'crow');
        var n = el('div', 'n', name); n.appendChild(el('span', null, unit));
        var bars = el('div', 'bars');
        [['a', 'Test vessel · ' + fmt(tv, dec), tv], ['b', 'MPFM · ' + fmt(mp, dec), mp]].forEach(function (x) {
            var b = el('div', 'bar ' + x[0]), i = el('i'); i.style.width = '0'; i.dataset.w = (x[2] / mx * 100).toFixed(1) + '%'; b.appendChild(i); b.appendChild(el('span', 'lb', x[1])); bars.appendChild(b);
        });
        var dv = el('div', 'dev ' + cls(d, pt), d === null ? '—' : (d > 0 ? '+' : '') + d.toFixed(1) + (pt ? ' pt' : '%'));
        r.appendChild(n); r.appendChild(bars); r.appendChild(dv); return r;
    }
    function renderPrev() {
        var box = $('prevBody'); box.innerHTML = '';
        if (!selTv || !selMp) {
            var e = el('div', 'empty');
            e.innerHTML = '<svg viewBox="0 0 120 60" aria-hidden="true"><rect x="8" y="30" width="14" height="22" rx="3" fill="#fbbf24" opacity=".5"/><rect x="28" y="18" width="14" height="34" rx="3" fill="#fbbf24" opacity=".5"/><rect x="62" y="24" width="14" height="28" rx="3" fill="#22d3ee" opacity=".5"/><rect x="82" y="12" width="14" height="40" rx="3" fill="#22d3ee" opacity=".5"/><path d="M8,10 Q60,-6 110,8" fill="none" stroke="#fff" stroke-opacity=".5" stroke-dasharray="4 4"/></svg>';
            e.appendChild(document.createTextNode(!selTv && !selMp ? 'Choose a test vessel test and an MPFM test to preview the comparison.' : (!selTv ? 'Now choose a test vessel test.' : 'Now choose an MPFM test.')));
            box.appendChild(e); return;
        }
        var ok = selTv.string_id === selMp.string_id;
        var st = el('div', 'status ' + (ok ? 'ok' : 'warn')); st.innerHTML = '<i class="fe ' + (ok ? 'fe-check-circle' : 'fe-alert-triangle') + '"></i>';
        var tx = el('div'); tx.innerHTML = ok ? '<b>Same well.</b> Both tests are on oil string ' : '<b>Different wells.</b> TV is on ';
        tx.appendChild(document.createTextNode(ok ? selTv.string + ' — the comparison is ready.' : selTv.string + ' but MPFM is on ' + selMp.string + '. The comparison may not be meaningful.'));
        st.appendChild(tx); box.appendChild(st);
        box.appendChild(row('Oil rate', 'B/D', selTv.oil, selMp.oil, dev(selMp.oil, selTv.oil), false, 0));
        box.appendChild(row('Gas rate', 'MMscf/D', selTv.gas, selMp.gas, dev(selMp.gas, selTv.gas), false, 2));
        box.appendChild(row('Water cut', '%', selTv.wc, selMp.wc, selMp.wc - selTv.wc, true, 1));
        requestAnimationFrame(function () { requestAnimationFrame(function () { box.querySelectorAll('.bar i').forEach(function (i) { i.style.width = i.dataset.w; }); }); });
    }

    function sync() {
        $('tv_test_id').value = selTv ? selTv.id : ''; $('mpfm_test_id').value = selMp ? selMp.id : '';
        $('submitBtn').disabled = !(selTv && selMp);
        $('goHint').textContent = selTv && selMp ? 'Ready — press Compare tests' : 'Select one test on each side';
        var so = $('sameOnly'); so.style.display = selTv ? '' : 'none';
        if (!selTv && same) { same = false; so.classList.remove('on'); }
        renderTv(); renderMp(); renderPrev();
    }

    $('tvQ').addEventListener('input', renderTv); $('mpQ').addEventListener('input', renderMp);
    $('mpChips').addEventListener('click', function (e) {
        var c = e.target.closest('.chip'); if (!c) return;
        [].forEach.call($('mpChips').children, function (x) { x.classList.remove('on'); }); c.classList.add('on'); field = c.dataset.f; renderMp();
    });
    $('sameOnly').addEventListener('click', function () { same = !same; this.classList.toggle('on', same); renderMp(); });

    // allow ?tv_test_id=&mpfm_test_id= presets
    var qs = new URLSearchParams(location.search);
    if (qs.get('tv_test_id')) selTv = TV.filter(function (t) { return String(t.id) === qs.get('tv_test_id'); })[0] || null;
    if (qs.get('mpfm_test_id')) selMp = MP.filter(function (t) { return String(t.id) === qs.get('mpfm_test_id'); })[0] || null;
    sync();

    var io = new IntersectionObserver(function (es) { es.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } }); }, { threshold: .08 });
    document.querySelectorAll('#tvc .rv').forEach(function (n) { io.observe(n); });
    if (window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches) document.querySelectorAll('#tvc svg').forEach(function (s) { s.pauseAnimations && s.pauseAnimations(); });
})();
</script>
@endsection
