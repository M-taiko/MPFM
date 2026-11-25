@extends('layouts.master')

@section('title', 'Edit Test Vessel Test #'.$tvTest->id)

@section('css')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
<style>
.tvf { direction:ltr; text-align:left; --bg:#050a15; --ink:#e8eefc; --sub:#9fb0cf; --mute:#64748b; --cyan:#22d3ee; --amber:#fbbf24; --green:#34d399; --red:#f87171; --water:#38bdf8;
    --line:rgba(96,165,250,.2); --glass:rgba(255,255,255,.05); --grad:linear-gradient(135deg,#3b82f6,#22d3ee); --ease:cubic-bezier(.22,1,.36,1);
    position:relative; overflow:hidden; isolation:isolate; padding:26px; border-radius:26px; background:var(--bg); color:var(--ink); font-family:'Inter',sans-serif; -webkit-font-smoothing:antialiased; }
.tvf *, .tvf *::before, .tvf *::after { box-sizing:border-box; }
.tvf a { text-decoration:none; }
.tvf h1, .tvf h3, .tvf p { margin:0; color:inherit; }
.tvf .bgl { position:absolute; inset:0; z-index:-1; pointer-events:none; overflow:hidden; }
.tvf .bgl::before { content:""; position:absolute; inset:-50%; background-image:linear-gradient(rgba(59,130,246,.07) 1px,transparent 1px),linear-gradient(90deg,rgba(59,130,246,.07) 1px,transparent 1px); background-size:56px 56px; transform:perspective(700px) rotateX(58deg) translateY(-6%); animation:tfGrid 14s linear infinite; mask-image:radial-gradient(ellipse at 50% 30%,#000 10%,transparent 70%); -webkit-mask-image:radial-gradient(ellipse at 50% 30%,#000 10%,transparent 70%); }
@keyframes tfGrid { to { background-position:0 56px; } }
.tvf .orb { position:absolute; border-radius:50%; filter:blur(90px); opacity:.3; animation:tfFloat 16s ease-in-out infinite; }
.tvf .o1 { width:440px; height:440px; background:#2563eb; top:-140px; left:-120px; }
.tvf .o2 { width:400px; height:400px; background:#0891b2; bottom:-160px; right:-100px; animation-delay:-6s; }
@keyframes tfFloat { 50% { transform:translate(40px,-40px) scale(1.12); } }
.tvf .lg { --h:76px; display:inline-block; flex:none; height:var(--h); aspect-ratio:1/1; border-radius:calc(var(--h) * .14); background:#071326 url('/assets/img/brand/logo.svg') center/cover no-repeat; border:2px solid rgba(34,211,238,.5); box-shadow:0 0 0 1px rgba(59,130,246,.5),0 8px 30px rgba(0,0,0,.45),0 0 26px rgba(34,211,238,.35); position:relative; overflow:hidden; animation:tfGlow 3.2s ease-in-out infinite; }
.tvf .lg::after { content:""; position:absolute; top:0; left:-130%; width:60%; height:100%; background:linear-gradient(90deg,transparent,rgba(34,211,238,.45),transparent); transform:skewX(-20deg); animation:tfShine 4.5s infinite; }
@keyframes tfShine { 60%,100% { left:160%; } }
@keyframes tfGlow { 50% { box-shadow:0 0 0 1px rgba(59,130,246,.6),0 10px 40px rgba(0,0,0,.5),0 0 54px rgba(34,211,238,.65); } }
.tvf .rv { opacity:0; transform:translateY(26px); transition:opacity .8s var(--ease), transform .8s var(--ease); transition-delay:calc(var(--d,0) * 80ms); }
.tvf .rv.in { opacity:1; transform:none; }

.tvf .hero { display:flex; align-items:center; justify-content:space-between; gap:20px; flex-wrap:wrap; padding:24px 28px; border-radius:22px; border:1px solid var(--line); background:linear-gradient(100deg,rgba(5,10,21,.94) 40%,rgba(5,10,21,.5)),url('/assets/img/brand/Offshore.jpg') center/cover; }
.tvf .who { display:flex; align-items:center; gap:20px; }
.tvf .eyebrow { font-size:11px; font-weight:700; letter-spacing:.16em; text-transform:uppercase; color:var(--cyan); }
.tvf .hero h1 { font-size:clamp(22px,2.6vw,34px); font-weight:900; letter-spacing:-.03em; margin:5px 0 6px; }
.tvf .hero h1 em { font-style:normal; background:linear-gradient(120deg,#60a5fa,#22d3ee,#fbbf24,#60a5fa); background-size:250% 100%; -webkit-background-clip:text; background-clip:text; color:transparent; animation:tfFlow 6s linear infinite; }
@keyframes tfFlow { to { background-position:250% 0; } }
.tvf .hero p { color:var(--sub); font-size:13px; }
.tvf .btn2 { display:inline-flex; align-items:center; gap:8px; padding:10px 18px; border-radius:50px; font-size:13px; font-weight:700; border:1px solid var(--line); background:var(--glass); color:var(--ink); cursor:pointer; transition:transform .3s var(--ease), border-color .3s, box-shadow .3s; font-family:inherit; }
.tvf .btn2:hover { transform:translateY(-3px); border-color:var(--cyan); color:#fff; }
.tvf .btn2.p { background:var(--grad); color:#04111f; border:0; box-shadow:0 8px 24px rgba(34,211,238,.28); }
.tvf .btn2.p:hover { color:#04111f; box-shadow:0 12px 32px rgba(34,211,238,.45); }
.tvf .btn2.g { background:linear-gradient(135deg,#10b981,#34d399); color:#04130c; border:0; }
.tvf .btn2.g:hover { color:#04130c; }

.tvf .err { margin-top:18px; padding:14px 18px; border-radius:16px; border:1px solid rgba(248,113,113,.45); background:rgba(248,113,113,.08); color:#fecaca; font-size:13px; }
.tvf .err ul { margin:8px 0 0; padding-left:18px; }
.tvf .grid { display:grid; grid-template-columns:1fr 400px; gap:20px; margin-top:20px; align-items:start; }
.tvf .side { position:sticky; top:84px; display:grid; gap:16px; }
.tvf .panel { position:relative; border-radius:22px; border:1px solid var(--line); background:linear-gradient(180deg,rgba(255,255,255,.045),rgba(255,255,255,.015)); padding:22px; margin-bottom:16px; }
.tvf .side .panel { margin:0; }
.tvf .ph { display:flex; align-items:center; gap:12px; margin-bottom:18px; }
.tvf .ph .n { width:32px; height:32px; border-radius:50%; background:var(--grad); color:#04111f; font:700 14px 'Space Grotesk'; display:grid; place-items:center; flex:none; }
.tvf .ph h3 { font-size:16px; font-weight:800; }
.tvf .ph small { display:block; color:var(--mute); font-size:12px; font-weight:500; margin-top:1px; }
.tvf .row { display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:16px; }
.tvf .f label { display:flex; align-items:center; gap:7px; font-size:10.5px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--mute); margin-bottom:7px; }
.tvf .f label b { color:var(--red); }
.tvf .f label i { color:var(--cyan); font-size:13px; }
.tvf .ctl { position:relative; }
.tvf .fc { width:100%; height:46px; padding:0 14px; border-radius:14px; border:1px solid var(--line); background:rgba(255,255,255,.06); color:#fff; font:500 14px 'Inter',sans-serif; transition:border-color .25s, box-shadow .25s, background .25s; outline:none; appearance:none; -webkit-appearance:none; }
.tvf textarea.fc { height:auto; padding:12px 14px; resize:vertical; line-height:1.5; }
.tvf .fc:focus { border-color:var(--cyan); box-shadow:0 0 0 4px rgba(34,211,238,.14); background:rgba(255,255,255,.09); }
.tvf .fc::placeholder { color:#52627d; }
.tvf select.fc { padding-right:36px; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' fill='none' stroke='%2322d3ee' stroke-width='2'%3E%3Cpath d='M1 1l5 5 5-5'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 14px center; }
.tvf select.fc option { background:#0b1426; color:#fff; }
.tvf select.fc:disabled { opacity:.55; cursor:not-allowed; }
.tvf input[type=date].fc { color-scheme:dark; }
.tvf .unit { position:absolute; right:12px; top:50%; transform:translateY(-50%); font-size:11px; font-weight:700; color:var(--cyan); background:rgba(34,211,238,.1); border:1px solid rgba(34,211,238,.3); padding:3px 9px; border-radius:50px; pointer-events:none; }
.tvf .ctl.u .fc { padding-right:86px; }
.tvf .fc.bad, .tvf .f.has-err .fc { border-color:var(--red); }
.tvf .em { color:#fca5a5; font-size:12px; margin-top:5px; display:block; }
.tvf .lock .fc { pointer-events:none; opacity:.75; border-style:dashed; }
.tvf .locknote { margin-bottom:14px; padding:10px 14px; border-radius:12px; border:1px solid rgba(251,191,36,.3); background:rgba(251,191,36,.07); color:#fde68a; font-size:12.5px; }

/* shift tiles */
.tvf .shifts { display:grid; grid-template-columns:repeat(3,1fr); gap:10px; }
.tvf .shifts input { position:absolute; opacity:0; pointer-events:none; }
.tvf .shifts label { margin:0; cursor:pointer; text-transform:none; letter-spacing:0; flex-direction:column; justify-content:center; gap:4px; height:76px; border-radius:16px; border:1px solid var(--line); background:rgba(255,255,255,.05); color:var(--sub); font-size:13px; font-weight:700; transition:all .3s var(--ease); }
.tvf .shifts label svg { width:30px; height:30px; }
.tvf .shifts label:hover { border-color:var(--cyan); transform:translateY(-2px); }
.tvf .shifts input:checked + label { border-color:var(--cyan); background:rgba(34,211,238,.13); color:#fff; box-shadow:0 0 22px rgba(34,211,238,.25); }
.tvf .shifts input:focus-visible + label { outline:2px solid var(--cyan); }

/* water cut slider */
.tvf input[type=range] { -webkit-appearance:none; appearance:none; width:100%; height:8px; border-radius:50px; background:linear-gradient(90deg,#f59e0b,#0ea5e9); outline:none; margin-top:12px; }
.tvf input[type=range]::-webkit-slider-thumb { -webkit-appearance:none; width:20px; height:20px; border-radius:50%; background:#fff; border:3px solid var(--cyan); box-shadow:0 0 12px rgba(34,211,238,.6); cursor:pointer; }
.tvf input[type=range]::-moz-range-thumb { width:16px; height:16px; border-radius:50%; background:#fff; border:3px solid var(--cyan); cursor:pointer; }
.tvf .rl { display:flex; justify-content:space-between; font-size:10.5px; color:var(--mute); margin-top:4px; }

.tvf .bar { display:flex; gap:12px; flex-wrap:wrap; align-items:center; padding:18px 22px; border-radius:22px; border:1px solid var(--line); background:rgba(255,255,255,.04); }
.tvf .bar .sp { flex:1; }
.tvf .hint { font-size:12px; color:var(--mute); }

/* live vessel */
.tvf .live svg { width:100%; height:auto; display:block; }
.tvf .live text { font-family:'Inter',sans-serif; }
.tvf .nd { transition:transform 1s var(--ease); transform:rotate(-120deg); }
.tvf .lvw { transform-box:fill-box; transform-origin:bottom; transform:scaleY(0); transition:transform 1s var(--ease); }
.tvf .lvwave { transition:transform 1s var(--ease); }
.tvf .wv { animation:tfWave 3.2s linear infinite; }
.tvf .wv.r { animation-duration:4.4s; animation-direction:reverse; }
@keyframes tfWave { to { transform:translateX(-40px); } }
.tvf .flow { fill:none; stroke-width:2; stroke-linecap:round; stroke-dasharray:3 8; animation:tfDash 1.2s linear infinite; }
@keyframes tfDash { to { stroke-dashoffset:-22; } }
.tvf .bub { fill:rgba(255,236,179,.6); animation:tfRise 3.4s linear infinite; }
@keyframes tfRise { 0% { transform:translateY(0); opacity:0; } 15% { opacity:1; } 100% { transform:translateY(-34px); opacity:0; } }
.tvf .stats { display:grid; grid-template-columns:repeat(3,1fr); gap:8px; margin-top:12px; }
.tvf .st { padding:10px 8px; border-radius:14px; background:var(--glass); border:1px solid var(--line); text-align:center; }
.tvf .st small { display:block; font-size:9.5px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--mute); }
.tvf .st b { display:block; font:700 17px 'Space Grotesk'; color:#fff; margin-top:2px; }
.tvf .st span { font-size:10px; color:var(--sub); }
.tvf .prog { display:flex; align-items:center; gap:16px; }
.tvf .prog svg { width:84px; height:84px; flex:none; }
.tvf .prog .arc { fill:none; stroke-width:9; stroke-linecap:round; transform:rotate(-90deg); transform-origin:50% 50%; transition:stroke-dasharray .6s var(--ease); }
.tvf .prog b { display:block; font-size:14px; }
.tvf .prog small { color:var(--mute); font-size:12px; line-height:1.5; display:block; margin-top:2px; }
.tvf .tips { margin:0; padding:0; list-style:none; display:grid; gap:8px; }
.tvf .tips li { display:flex; gap:10px; font-size:12.5px; color:var(--sub); line-height:1.5; }
.tvf .tips i { color:var(--cyan); margin-top:2px; }

@media (max-width:1200px) { .tvf .grid { grid-template-columns:1fr; } .tvf .side { position:static; grid-template-columns:1fr 1fr; } }
@media (max-width:760px) { .tvf { padding:14px; border-radius:18px; } .tvf .side { grid-template-columns:1fr; } .tvf .who { flex-direction:column; align-items:flex-start; } }
@media (prefers-reduced-motion:reduce) { .tvf *, .tvf *::before, .tvf *::after { animation:none !important; transition:none !important; } .tvf .rv { opacity:1; transform:none; } }
</style>
@endsection

@section('content')
@php
    $v = fn ($k, $d = '') => old($k, $tvTest->{$k} ?? $d);
    $fromRequest = (bool) $tvTest->tv_test_request_well_id;
    $shiftSel = $v('shift');
@endphp
<div class="tvf" id="tvf">
    <div class="bgl"><div class="orb o1"></div><div class="orb o2"></div></div>

    <div class="hero rv">
        <div class="who">
            <span class="lg"></span>
            <div>
                <div class="eyebrow">Well Testing · Edit reading</div>
                <h1>Edit <em>test #{{ $tvTest->id }}</em></h1>
                <p>Correct the readings or change the status — the drawing on the right follows your numbers.</p>
            </div>
        </div>
        <a href="{{ route('tv_tests.show', $tvTest->id) }}" class="btn2"><i class="fe fe-arrow-left"></i> Back to test</a>
    </div>

    @if($errors->any())
        <div class="err"><b><i class="fe fe-alert-circle"></i> Please fix the following:</b>
            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <form action="{{ route('tv_tests.update', $tvTest) }}" method="POST" id="testForm" novalidate>
        @csrf
        @method('PUT')
        <input type="hidden" name="tv_test_request_well_id" value="{{ $v('tv_test_request_well_id') }}">

        <div class="grid">
            <div class="main">
                {{-- 1 LOCATION --}}
                <div class="panel rv">
                    <div class="ph"><span class="n">1</span><div><h3>Vessel &amp; location</h3><small>Which vessel, and which well is being tested</small></div></div>
                    @if($fromRequest)<div class="locknote"><i class="fe fe-lock"></i> Filled from the test request — vessel and well can't be changed here.</div>@endif
                    <div class="row {{ $fromRequest ? 'lock' : '' }}">
                        <div class="f {{ $errors->has('test_vessel_id') ? 'has-err' : '' }}"><label for="test_vessel_id"><i class="fe fe-anchor"></i> Test vessel <b>*</b></label>
                            <select name="test_vessel_id" id="test_vessel_id" class="fc" required>
                                <option value="">Select vessel…</option>
                                @foreach($testVessels as $tv)<option value="{{ $tv->id }}" @selected($v('test_vessel_id') == $tv->id)>{{ $tv->name }}{{ $tv->code ? ' ('.$tv->code.')' : '' }}</option>@endforeach
                            </select>@error('test_vessel_id')<span class="em">{{ $message }}</span>@enderror</div>
                        <div class="f {{ $errors->has('oil_field_id') ? 'has-err' : '' }}"><label for="oil_field_id"><i class="fe fe-layers"></i> Oil field <b>*</b></label>
                            <select name="oil_field_id" id="oil_field_id" class="fc" required>
                                <option value="">Select field…</option>
                                @foreach($oilFields as $of)<option value="{{ $of->id }}" @selected($v('oil_field_id') == $of->id)>{{ $of->name }}</option>@endforeach
                            </select>@error('oil_field_id')<span class="em">{{ $message }}</span>@enderror</div>
                        <div class="f {{ $errors->has('tower_id') ? 'has-err' : '' }}"><label for="tower_id"><i class="fe fe-corner-down-right"></i> Tower <b>*</b></label>
                            <select name="tower_id" id="tower_id" class="fc" required data-init="{{ $v('tower_id') }}">
                                <option value="">Select tower…</option>
                                @foreach($towers as $t)<option value="{{ $t->id }}" @selected($v('tower_id') == $t->id)>{{ $t->name }}</option>@endforeach
                            </select>@error('tower_id')<span class="em">{{ $message }}</span>@enderror</div>
                        <div class="f {{ $errors->has('oil_string_id') ? 'has-err' : '' }}"><label for="oil_string_id"><i class="fe fe-trending-up"></i> Oil string <b>*</b></label>
                            <select name="oil_string_id" id="oil_string_id" class="fc" required data-init="{{ $v('oil_string_id') }}">
                                <option value="">Select string…</option>
                                @foreach($oilStrings as $s)<option value="{{ $s->id }}" @selected($v('oil_string_id') == $s->id)>{{ $s->name }}</option>@endforeach
                            </select>@error('oil_string_id')<span class="em">{{ $message }}</span>@enderror</div>
                    </div>
                </div>

                {{-- 2 DETAILS --}}
                <div class="panel rv" style="--d:1">
                    <div class="ph"><span class="n">2</span><div><h3>When</h3><small>Test date and shift</small></div></div>
                    <div class="row">
                        <div class="f {{ $errors->has('test_date') ? 'has-err' : '' }}"><label for="test_date"><i class="fe fe-calendar"></i> Test date <b>*</b></label>
                            <input type="date" name="test_date" id="test_date" class="fc" value="{{ old('test_date', $tvTest->test_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required>
                            @error('test_date')<span class="em">{{ $message }}</span>@enderror</div>
                        <div class="f {{ $errors->has('shift') ? 'has-err' : '' }}"><label><i class="fe fe-clock"></i> Shift <b>*</b></label>
                            <div class="shifts">
                                <input type="radio" name="shift" id="sh1" value="morning" @checked($shiftSel === 'morning')><label for="sh1"><svg viewBox="0 0 32 32" fill="none" stroke="#fbbf24" stroke-width="2" stroke-linecap="round"><path d="M4 22h24M9 22a7 7 0 0 1 14 0M16 6v4M6 12l3 3M26 12l-3 3"/></svg>Morning</label>
                                <input type="radio" name="shift" id="sh2" value="afternoon" @checked($shiftSel === 'afternoon')><label for="sh2"><svg viewBox="0 0 32 32" fill="none" stroke="#fb923c" stroke-width="2" stroke-linecap="round"><circle cx="16" cy="16" r="5"/><path d="M16 4v3M16 25v3M4 16h3M25 16h3M7.5 7.5l2 2M22.5 22.5l2 2M7.5 24.5l2-2M22.5 9.5l2-2"/></svg>Afternoon</label>
                                <input type="radio" name="shift" id="sh3" value="night" @checked($shiftSel === 'night')><label for="sh3"><svg viewBox="0 0 32 32" fill="none" stroke="#a5b4fc" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M24 20A10 10 0 0 1 12 8a10 10 0 1 0 12 12z"/></svg>Night</label>
                            </div>@error('shift')<span class="em">{{ $message }}</span>@enderror</div>
                    </div>
                </div>

                {{-- 3 PRODUCTION --}}
                <div class="panel rv" style="--d:2">
                    <div class="ph"><span class="n">3</span><div><h3>Production</h3><small>Rates measured at the vessel outlets</small></div></div>
                    <div class="row">
                        <div class="f {{ $errors->has('oil_rate') ? 'has-err' : '' }}"><label for="oil_rate"><i class="fe fe-droplet"></i> Oil rate <b>*</b></label>
                            <div class="ctl u"><input type="number" name="oil_rate" id="oil_rate" class="fc" step="0.01" min="0" value="{{ $v('oil_rate') }}" placeholder="0.00" required><span class="unit">B/D</span></div>
                            @error('oil_rate')<span class="em">{{ $message }}</span>@enderror</div>
                        <div class="f {{ $errors->has('gas_rate') ? 'has-err' : '' }}"><label for="gas_rate"><i class="fe fe-wind"></i> Gas rate <b>*</b></label>
                            <div class="ctl u"><input type="number" name="gas_rate" id="gas_rate" class="fc" step="0.01" min="0" value="{{ $v('gas_rate') }}" placeholder="0.00" required><span class="unit">MMscf/D</span></div>
                            @error('gas_rate')<span class="em">{{ $message }}</span>@enderror</div>
                        <div class="f {{ $errors->has('water_cut') ? 'has-err' : '' }}"><label for="water_cut"><i class="fe fe-percent"></i> Water cut <b>*</b></label>
                            <div class="ctl u"><input type="number" name="water_cut" id="water_cut" class="fc" step="0.01" min="0" max="100" value="{{ $v('water_cut') }}" placeholder="0.00" required><span class="unit">%</span></div>
                            <input type="range" id="wcRange" min="0" max="100" step="0.5" value="{{ (float) $v('water_cut', 0) }}" aria-label="Water cut slider">
                            <div class="rl"><span>0% dry oil</span><span>100% water</span></div>
                            @error('water_cut')<span class="em">{{ $message }}</span>@enderror</div>
                    </div>
                </div>

                {{-- 4 CONDITIONS --}}
                <div class="panel rv" style="--d:3">
                    <div class="ph"><span class="n">4</span><div><h3>Operating conditions</h3><small>Vessel pressure and temperature</small></div></div>
                    <div class="row">
                        <div class="f {{ $errors->has('pressure') ? 'has-err' : '' }}"><label for="pressure"><i class="fe fe-activity"></i> Pressure <b>*</b></label>
                            <div class="ctl u"><input type="number" name="pressure" id="pressure" class="fc" step="0.01" min="0" value="{{ $v('pressure') }}" placeholder="0.00" required><span class="unit">psia</span></div>
                            @error('pressure')<span class="em">{{ $message }}</span>@enderror</div>
                        <div class="f {{ $errors->has('temperature') ? 'has-err' : '' }}"><label for="temperature"><i class="fe fe-thermometer"></i> Temperature <b>*</b></label>
                            <div class="ctl u"><input type="number" name="temperature" id="temperature" class="fc" step="0.01" min="-50" max="150" value="{{ $v('temperature') }}" placeholder="0.00" required><span class="unit">°F</span></div>
                            @error('temperature')<span class="em">{{ $message }}</span>@enderror</div>
                        <div class="f {{ $errors->has('gor') ? 'has-err' : '' }}"><label for="gor"><i class="fe fe-layers"></i> GOR</label>
                            <div class="ctl u"><input type="number" name="gor" id="gor" class="fc" step="0.01" min="0" value="{{ $v('gor') }}" placeholder="optional"><span class="unit">scf/stb</span></div>
                            @error('gor')<span class="em">{{ $message }}</span>@enderror</div>
                    </div>
                </div>

                {{-- 5 LINK + NOTES --}}
                <div class="panel rv" style="--d:4">
                    <div class="ph"><span class="n">5</span><div><h3>Status, MPFM link &amp; notes</h3><small>Review status, optional MPFM link and observations</small></div></div>
                    <div class="f {{ $errors->has('status') ? 'has-err' : '' }}" style="margin-bottom:16px"><label for="status"><i class="fe fe-flag"></i> Status <b>*</b></label>
                        <select name="status" id="status" class="fc" required>
                            @foreach(['draft' => 'Draft', 'submitted' => 'Submitted', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $sv => $sl)<option value="{{ $sv }}" @selected($v('status', 'draft') === $sv)>{{ $sl }}</option>@endforeach
                        </select>
                        @error('status')<span class="em">{{ $message }}</span>@enderror</div>
                    <div class="f {{ $errors->has('mpfm_test_id') ? 'has-err' : '' }}" style="margin-bottom:16px"><label for="mpfm_test_id"><i class="fe fe-link-2"></i> Linked MPFM test</label>
                        <select name="mpfm_test_id" id="mpfm_test_id" class="fc">
                            <option value="">No link</option>
                            @foreach($mpfmTests as $m)
                                <option value="{{ $m->id }}" data-string="{{ $m->oil_string_id }}" @selected($v('mpfm_test_id') == $m->id)>MPFM #{{ $m->id }} · {{ $m->test_date->format('Y-m-d') }} · {{ $m->oilString->name ?? '—' }}</option>
                            @endforeach
                        </select>
                        <span class="hint" id="mpfmHint" style="display:block;margin-top:6px">Tests on the selected oil string are listed first.</span>
                        @error('mpfm_test_id')<span class="em">{{ $message }}</span>@enderror</div>
                    <div class="f"><label for="notes"><i class="fe fe-edit-3"></i> Notes &amp; observations</label>
                        <textarea name="notes" id="notes" rows="4" class="fc" placeholder="Anything unusual during the test — slugging, level swings, valve changes…">{{ $v('notes') }}</textarea>
                        @error('notes')<span class="em">{{ $message }}</span>@enderror</div>
                </div>

                <div class="bar rv">
                    <button type="submit" class="btn2 p"><i class="fe fe-save"></i> Update test</button>
                    <a href="{{ route('tv_tests.show', $tvTest->id) }}" class="btn2"><i class="fe fe-x"></i> Cancel</a>
                    <span class="sp"></span><span class="hint">Fields marked * are required</span>
                </div>
            </div>

            {{-- LIVE SIDE --}}
            <aside class="side">
                <div class="panel live rv">
                    <div class="ph" style="margin-bottom:6px"><div><h3>Live vessel preview</h3><small>Levels and gauges follow your inputs</small></div></div>
                    <svg viewBox="0 0 420 290" aria-label="Live test vessel preview">
                        <defs>
                            <linearGradient id="lvShell" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#a8b6cb"/><stop offset=".14" stop-color="#f1f5f9"/><stop offset=".5" stop-color="#7c8aa1"/><stop offset="1" stop-color="#1b2433"/></linearGradient>
                            <linearGradient id="lvOil" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#f59e0b"/><stop offset="1" stop-color="#78350f"/></linearGradient>
                            <linearGradient id="lvWat" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#0ea5e9"/><stop offset="1" stop-color="#0c4a6e"/></linearGradient>
                            <linearGradient id="lvCyl" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#fff" stop-opacity=".22"/><stop offset=".35" stop-color="#fff" stop-opacity=".02"/><stop offset="1" stop-color="#000" stop-opacity=".45"/></linearGradient>
                            <clipPath id="lvIn"><path d="M74,95 H296 A26,55 0 0 1 296,205 H74 A26,55 0 0 1 74,95 Z"/></clipPath>
                            <path id="lpIn" d="M8,62 H100 V96"/><path id="lpGas" d="M262,96 V40 H414"/><path id="lpOil" d="M282,202 V262 H414"/>
                        </defs>
                        <g fill="none" stroke-linejoin="round">
                            <use href="#lpIn" stroke="#0b1220" stroke-width="11"/><use href="#lpIn" stroke="#64748b" stroke-width="8"/>
                            <use href="#lpGas" stroke="#0b1220" stroke-width="11"/><use href="#lpGas" stroke="#64748b" stroke-width="8"/>
                            <use href="#lpOil" stroke="#0b1220" stroke-width="11"/><use href="#lpOil" stroke="#64748b" stroke-width="8"/>
                        </g>
                        <rect x="116" y="212" width="36" height="34" fill="#334155"/><rect x="238" y="212" width="36" height="34" fill="#334155"/><rect x="100" y="244" width="190" height="7" rx="2" fill="#475569"/>
                        <path d="M70,90 H300 A30,60 0 0 1 300,210 H70 A30,60 0 0 1 70,90 Z" fill="url(#lvShell)" stroke="#0b1220" stroke-width="1.6"/>
                        <g clip-path="url(#lvIn)">
                            <rect x="40" y="90" width="300" height="120" fill="#04101f"/>
                            <rect x="40" y="90" width="300" height="120" fill="#22d3ee" opacity=".09"/>
                            <rect x="40" y="130" width="202" height="80" fill="url(#lvOil)"/>
                            <rect class="lvw" id="lvWater" x="40" y="135" width="202" height="70" fill="url(#lvWat)"/>
                            <rect x="244" y="165" width="100" height="45" fill="url(#lvOil)"/>
                            <g class="lvwave" id="lvWave" style="transform:translateY(0)"><g class="wv r"><path id="lvWaveP" d="M30,205 q10,-4 20,0 t20,0 t20,0 t20,0 t20,0 t20,0 t20,0 t20,0 t20,0 t20,0 t20,0 t20,0 t20,0 t20,0" fill="none" stroke="#bae6fd" stroke-opacity=".7" stroke-width="1.3"/></g></g>
                            <g class="wv"><path d="M30,130 q10,-4 20,0 t20,0 t20,0 t20,0 t20,0 t20,0 t20,0 t20,0 t20,0 t20,0 t20,0 t20,0 t20,0 t20,0" fill="none" stroke="#fde68a" stroke-opacity=".8" stroke-width="1.3"/></g>
                            @for($i = 0; $i < 8; $i++)<circle class="bub" cx="{{ 92 + $i * 19 }}" cy="{{ 128 - ($i % 3) * 3 }}" r="{{ 1.8 + ($i % 3) * .6 }}" style="animation-delay:-{{ $i * .45 }}s;transform-origin:center"/>@endfor
                            <rect x="242" y="130" width="4" height="80" fill="#cbd5e1"/>
                            <rect x="212" y="98" width="24" height="30" fill="none" stroke="#cbd5e1" stroke-opacity=".6"/>
                            <path class="flow" d="M104,118 H238 Q262,118 262,100" stroke="#67e8f9"/>
                            <rect x="40" y="90" width="300" height="120" fill="url(#lvCyl)"/>
                            <text x="140" y="116" font-size="9" font-weight="800" letter-spacing="2" fill="#67e8f9" opacity=".85">GAS</text>
                            <text x="120" y="152" font-size="9" font-weight="800" letter-spacing="2" fill="#fff" opacity=".75">OIL</text>
                            <text id="lvWT" x="120" y="196" font-size="9" font-weight="800" letter-spacing="2" fill="#fff" opacity="0">WATER</text>
                        </g>
                        <path d="M70,90 H300 A30,60 0 0 1 300,210 H70 A30,60 0 0 1 70,90 Z" fill="none" stroke="#fff" stroke-opacity=".25"/>
                        {{-- dials --}}
                        @foreach([[160, 'P', 'PSIA'], [215, 'T', '°F']] as [$dx, $id, $du])
                            <g transform="translate({{ $dx }},46)">
                                <rect x="-3" y="22" width="6" height="26" fill="#64748b"/>
                                <circle r="25" fill="url(#lvShell)" stroke="#0b1220"/><circle r="21" fill="#0a1222" stroke="#475569"/>
                                <path d="M-18,10 A21,21 0 0 1 -10,-18" fill="none" stroke="#34d399" stroke-width="2.5" opacity=".7"/>
                                <path d="M10,-18 A21,21 0 0 1 18,10" fill="none" stroke="#ef4444" stroke-width="2.5" opacity=".8"/>
                                <text y="14" text-anchor="middle" font-size="6.5" font-weight="700" fill="#7dd3fc">{{ $du }}</text>
                                <g class="nd" id="nd{{ $id }}"><path d="M-1.6,4 L0,-17 L1.6,4 Z" fill="#fbbf24"/></g>
                                <circle r="3" fill="#e2e8f0"/>
                            </g>
                        @endforeach
                        @foreach([0, .8, 1.6, 2.4] as $b)
                            <circle r="2.8" fill="#fde68a"><animateMotion dur="3s" begin="{{ $b }}s" repeatCount="indefinite"><mpath href="#lpIn"/></animateMotion></circle>
                            <circle r="2.5" fill="#a5f3fc"><animateMotion dur="3s" begin="{{ $b }}s" repeatCount="indefinite"><mpath href="#lpGas"/></animateMotion></circle>
                            <circle r="2.8" fill="#f59e0b"><animateMotion dur="3.2s" begin="{{ $b }}s" repeatCount="indefinite"><mpath href="#lpOil"/></animateMotion></circle>
                        @endforeach
                        <text x="8" y="50" font-size="8.5" font-weight="700" letter-spacing="1.3" fill="#fcd34d">WELL STREAM</text>
                        <text x="330" y="30" font-size="8.5" font-weight="700" letter-spacing="1.3" fill="#67e8f9">GAS OUT</text>
                        <text x="330" y="252" font-size="8.5" font-weight="700" letter-spacing="1.3" fill="#fcd34d">OIL OUT</text>
                        <text x="160" y="86" text-anchor="middle" font-size="8" font-weight="700" fill="#94a3b8" id="lvPT">PI</text>
                        <text x="215" y="86" text-anchor="middle" font-size="8" font-weight="700" fill="#94a3b8">TI</text>
                    </svg>
                    <div class="stats">
                        <div class="st"><small>Oil</small><b id="stO">—</b><span>B/D</span></div>
                        <div class="st"><small>Gas</small><b id="stG">—</b><span>MMscf/D</span></div>
                        <div class="st"><small>Water cut</small><b id="stW">—</b><span>%</span></div>
                    </div>
                </div>

                <div class="panel rv" style="--d:1">
                    <div class="prog">
                        <svg viewBox="0 0 100 100"><circle class="arc" cx="50" cy="50" r="40" pathLength="100" stroke="rgba(255,255,255,.08)" style="stroke-dasharray:100 100"/><circle class="arc" id="pArc" cx="50" cy="50" r="40" pathLength="100" stroke="#22d3ee" style="stroke-dasharray:0 100"/><text id="pTxt" x="50" y="56" text-anchor="middle" font-family="Space Grotesk" font-weight="700" font-size="20" fill="#fff">0%</text></svg>
                        <div><b id="pLbl">Required fields</b><small id="pSub">Fill in the vessel, well, date and readings to enable a clean save.</small></div>
                    </div>
                </div>

                <div class="panel rv" style="--d:2">
                    <div class="ph" style="margin-bottom:12px"><div><h3>Reading tips</h3></div></div>
                    <ul class="tips">
                        <li><i class="fe fe-check-circle"></i> Record rates after the vessel level has stabilised, not during slugging.</li>
                        <li><i class="fe fe-check-circle"></i> Water cut is a percentage of liquid, from 0 to 100.</li>
                        <li><i class="fe fe-check-circle"></i> Link an MPFM test on the same string so the comparison is ready straight away.</li>
                    </ul>
                </div>
            </aside>
        </div>
    </form>
</div>
@endsection

@section('js')
<script>
(function () {
    var $ = function (id) { return document.getElementById(id); };
    var root = $('tvf');

    // ---- cascading field -> tower -> string -------------------------------
    function fill(sel, rows, placeholder, keep) {
        sel.innerHTML = '<option value="">' + placeholder + '</option>';
        rows.forEach(function (r) {
            var o = document.createElement('option'); o.value = r.id; o.textContent = r.name;
            if (String(r.id) === String(keep)) o.selected = true;
            sel.appendChild(o);
        });
    }
    function getJSON(url, cb) { fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }, credentials: 'same-origin' }).then(function (r) { return r.json(); }).then(cb).catch(function () {}); }
    function loadTowers(keep, then) {
        var f = $('oil_field_id').value;
        if (!f) { fill($('tower_id'), [], 'Select tower…'); fill($('oil_string_id'), [], 'Select string…'); return then && then(); }
        getJSON('/ajax/operational-plans/towers/' + f, function (d) { fill($('tower_id'), d, 'Select tower…', keep); then && then(); update(); });
    }
    function loadStrings(keep) {
        var t = $('tower_id').value;
        if (!t) { fill($('oil_string_id'), [], 'Select string…'); return; }
        getJSON('/ajax/operational-plans/oil-strings/' + t, function (d) { fill($('oil_string_id'), d, 'Select string…', keep); filterMpfm(); update(); });
    }
    $('oil_field_id').addEventListener('change', function () { loadTowers('', function () { loadStrings(''); }); });
    $('tower_id').addEventListener('change', function () { loadStrings(''); });
    $('oil_string_id').addEventListener('change', filterMpfm);

    // MPFM options of the selected string first
    function filterMpfm() {
        var s = $('oil_string_id').value, sel = $('mpfm_test_id'), opts = [].slice.call(sel.options).slice(1), n = 0;
        opts.forEach(function (o) { var m = s && o.dataset.string === s; o.hidden = !!s && !m && !o.selected; if (m) n++; });
        $('mpfmHint').textContent = s ? (n ? n + ' MPFM test(s) found on this oil string.' : 'No MPFM test on this oil string yet.') : 'Tests on the selected oil string are listed first.';
    }

    // ---- live vessel -------------------------------------------------------
    function num(id) { var v = parseFloat($(id).value); return isNaN(v) ? null : v; }
    function fmt(v, d) { return v === null ? '—' : v.toLocaleString('en-US', { minimumFractionDigits: d, maximumFractionDigits: d }); }
    function ang(frac) { return -120 + 240 * Math.max(0, Math.min(1, frac)); }
    function update() {
        var wc = num('water_cut'), p = num('pressure'), t = num('temperature'), o = num('oil_rate'), g = num('gas_rate');
        var w = Math.max(0, Math.min(100, wc || 0));
        $('lvWater').style.transform = 'scaleY(' + (w / 100) + ')';
        $('lvWave').style.transform = 'translateY(' + (-w / 100 * 70) + 'px)';
        $('lvWT').setAttribute('opacity', w > 22 ? .8 : 0);
        $('ndP').style.transform = 'rotate(' + ang((p || 0) / 3000) + 'deg)';
        $('ndT').style.transform = 'rotate(' + ang(((t === null ? -50 : t) + 50) / 200) + 'deg)';
        $('stO').textContent = fmt(o, 0); $('stG').textContent = fmt(g, 1); $('stW').textContent = wc === null ? '—' : fmt(wc, 1);
        if (document.activeElement !== $('wcRange')) $('wcRange').value = w;

        var req = ['test_vessel_id', 'oil_field_id', 'tower_id', 'oil_string_id', 'test_date', 'oil_rate', 'gas_rate', 'water_cut', 'pressure', 'temperature'];
        var done = req.filter(function (id) { return $(id).value !== ''; }).length + (root.querySelector('input[name=shift]:checked') ? 1 : 0), total = req.length + 1;
        var pc = Math.round(done / total * 100);
        $('pArc').style.strokeDasharray = pc + ' 100'; $('pArc').setAttribute('stroke', pc === 100 ? '#34d399' : '#22d3ee');
        $('pTxt').textContent = pc + '%';
        $('pLbl').textContent = done + ' of ' + total + ' required fields';
        $('pSub').textContent = pc === 100 ? 'All set — ready to save.' : 'Fill in the vessel, well, date and readings to enable a clean save.';
    }
    root.addEventListener('input', update); root.addEventListener('change', update);
    $('wcRange').addEventListener('input', function () { $('water_cut').value = this.value; update(); });

    // ---- submit ------------------------------------------------------------
    $('testForm').addEventListener('submit', function (e) {
        var bad = null;
        root.querySelectorAll('[required]').forEach(function (el) {
            var empty = el.value === '';
            el.classList.toggle('bad', empty);
            if (empty && !bad) bad = el;
        });
        if (!root.querySelector('input[name=shift]:checked')) { root.querySelector('.shifts').scrollIntoView({ block: 'center', behavior: 'smooth' }); e.preventDefault(); return; }
        if (bad) { e.preventDefault(); bad.scrollIntoView({ block: 'center', behavior: 'smooth' }); bad.focus(); }
    });

    // ---- reveal ------------------------------------------------------------
    var io = new IntersectionObserver(function (es) { es.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } }); }, { threshold: .08 });
    root.querySelectorAll('.rv').forEach(function (el) { io.observe(el); });

    // initial state (restores cascades after a failed validation too)
    var initT = $('tower_id').dataset.init, initS = $('oil_string_id').dataset.init;
    if ($('oil_field_id').value && $('tower_id').options.length <= 1) loadTowers(initT, function () { loadStrings(initS); });
    else if ($('tower_id').value && $('oil_string_id').options.length <= 1) loadStrings(initS);
    filterMpfm(); update();
    if (window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches) root.querySelectorAll('svg').forEach(function (s) { s.pauseAnimations && s.pauseAnimations(); });
})();
</script>
@endsection
