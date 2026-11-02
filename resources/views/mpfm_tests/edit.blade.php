@extends('layouts.master')

@section('title', 'Edit MPFM Test #'.$mpfmTest->id)

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/ofpage.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/offorms.css') }}">
<style>
.ofp .ctl { position:relative; }
.ofp .unit { position:absolute; right:12px; top:50%; transform:translateY(-50%); font-size:11px; font-weight:700; color:var(--cyan); background:rgba(34,211,238,.1); border:1px solid rgba(34,211,238,.3); padding:3px 9px; border-radius:50px; pointer-events:none; }
.ofp .ctl.u .fc { padding-right:88px; }
.ofp textarea.fc { height:auto; padding:12px 14px; resize:vertical; line-height:1.5; }
.ofp input[type=date].fc { color-scheme:dark; }
.ofp .f label.lb i { color:var(--cyan); font-size:13px; }
.ofp .shifts { display:grid; grid-template-columns:repeat(3,1fr); gap:10px; }
.ofp .shifts input { position:absolute; opacity:0; pointer-events:none; }
.ofp .shifts label { margin:0; cursor:pointer; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:4px; height:76px; border-radius:16px; border:1px solid var(--line); background:rgba(255,255,255,.05); color:var(--sub); font-size:13px; font-weight:700; transition:all .3s var(--ease); }
.ofp .shifts label svg { width:30px; height:30px; }
.ofp .shifts label:hover { border-color:var(--cyan); transform:translateY(-2px); }
.ofp .shifts input:checked + label { border-color:var(--cyan); background:rgba(34,211,238,.13); color:#fff; box-shadow:0 0 22px rgba(34,211,238,.25); }
.ofp .shifts input:focus-visible + label { outline:2px solid var(--cyan); }
.ofp .stp { display:grid; grid-template-columns:repeat(4,1fr); gap:10px; }
.ofp .stp input { position:absolute; opacity:0; pointer-events:none; }
.ofp .stp label { margin:0; cursor:pointer; text-align:center; padding:12px 6px; border-radius:14px; border:1px solid var(--line); background:rgba(255,255,255,.05); color:var(--sub); font-size:12.5px; font-weight:700; transition:all .25s var(--ease); }
.ofp .stp label::before { content:""; display:block; width:10px; height:10px; border-radius:50%; background:var(--c); margin:0 auto 7px; box-shadow:0 0 10px var(--c); }
.ofp .stp label:hover { border-color:var(--c); }
.ofp .stp input:checked + label { border-color:var(--c); background:color-mix(in srgb, var(--c) 14%, transparent); color:#fff; }
.ofp .stp input:focus-visible + label { outline:2px solid var(--cyan); }
.ofp input[type=range] { -webkit-appearance:none; appearance:none; width:100%; height:8px; border-radius:50px; background:linear-gradient(90deg,#f59e0b,#0ea5e9); outline:none; margin-top:12px; }
.ofp input[type=range]::-webkit-slider-thumb { -webkit-appearance:none; width:20px; height:20px; border-radius:50%; background:#fff; border:3px solid var(--cyan); box-shadow:0 0 12px rgba(34,211,238,.6); cursor:pointer; }
.ofp input[type=range]::-moz-range-thumb { width:16px; height:16px; border-radius:50%; background:#fff; border:3px solid var(--cyan); cursor:pointer; }
.ofp .rl { display:flex; justify-content:space-between; font-size:10.5px; color:var(--mute); margin-top:4px; }
.ofp .locknote { margin-bottom:14px; padding:10px 14px; border-radius:12px; border:1px solid rgba(251,191,36,.3); background:rgba(251,191,36,.07); color:#fde68a; font-size:12.5px; }
.ofp .live svg { width:100%; height:auto; display:block; } .ofp .live text { font-family:'Inter',sans-serif; }
.ofp .nd { transition:transform 1s var(--ease); transform:rotate(-120deg); }
.ofp .lvw { transform-box:fill-box; transform-origin:bottom; transform:scaleY(0); transition:transform 1s var(--ease); }
.ofp .wv { animation:mfWave 3.2s linear infinite; } @keyframes mfWave { to { transform:translateX(-40px); } }
.ofp .stats { display:grid; grid-template-columns:repeat(3,1fr); gap:8px; margin-top:12px; }
.ofp .stt { padding:10px 8px; border-radius:14px; background:var(--glass); border:1px solid var(--line); text-align:center; }
.ofp .stt small { display:block; font-size:9.5px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--mute); }
.ofp .stt b { display:block; font:700 17px 'Space Grotesk'; color:#fff; margin-top:2px; } .ofp .stt span { font-size:10px; color:var(--sub); }
.ofp .prog { display:flex; align-items:center; gap:16px; } .ofp .prog svg { width:84px; height:84px; flex:none; }
.ofp .prog .arc { fill:none; stroke-width:9; stroke-linecap:round; transform:rotate(-90deg); transform-origin:50% 50%; transition:stroke-dasharray .6s var(--ease); animation:none; }
.ofp .prog b { display:block; font-size:14px; } .ofp .prog small { color:var(--mute); font-size:12px; line-height:1.5; display:block; margin-top:2px; }
.ofp .tips { margin:0; padding:0; list-style:none; display:grid; gap:8px; } .ofp .tips li { display:flex; gap:10px; font-size:12.5px; color:var(--sub); line-height:1.5; } .ofp .tips i { color:var(--cyan); margin-top:2px; }
@media (max-width:640px) { .ofp .stp { grid-template-columns:1fr 1fr; } }
@media (prefers-reduced-motion:reduce) { .ofp .nd, .ofp .lvw { transition:none; } }
</style>
@endsection

@section('content')
@php
    $v = fn ($k, $d = '') => old($k, $mpfmTest->{$k} ?? $d);
    $shiftSel = $v('shift');
    $statusSel = $v('status', 'draft');
    $stColors = ['draft' => '#94a3b8', 'submitted' => '#fbbf24', 'approved' => '#34d399', 'rejected' => '#f87171'];
@endphp
<div class="ofp" id="ofp">
    <div class="bgl"><div class="orb o1"></div><div class="orb o2"></div></div>

    <div class="hero rv">
        <div class="who">
            <span class="lg"></span>
            <div>
                <div class="eyebrow"><i></i> MPFM · Edit test</div>
                <h1>Edit <em>test #{{ $mpfmTest->id }}</em></h1>
                <div class="meta">
                    <span class="chip {{ $mpfmTest->status }}">{{ $mpfmTest->status }}</span>
                    <span>{{ $mpfmTest->oilString->name ?? 'No oil string' }} · {{ optional($mpfmTest->test_date)->format('d M Y') }}</span>
                </div>
            </div>
        </div>
        <div class="actions">
            <a class="btn2" href="{{ route('mpfm_tests.show', $mpfmTest->id) }}"><i class="fe fe-eye"></i> View test</a>
            <a class="btn2" href="{{ route('mpfm_tests.index') }}"><i class="fe fe-arrow-left"></i> All tests</a>
        </div>
    </div>

    @if($errors->any())
        <div class="err"><b><i class="fe fe-alert-circle"></i> Please fix the following:</b>
            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <form action="{{ route('mpfm_tests.update', $mpfmTest->id) }}" method="POST" id="mForm" novalidate>
        @csrf
        @method('PUT')
        <div class="grid">
            <div class="main">
                {{-- 1 LOCATION --}}
                <div class="panel rv">
                    <div class="ph num"><span class="n">1</span><div><h3>Field, tower &amp; oil string</h3><small>Where the multiphase meter took this reading</small></div></div>
                    <div class="row">
                        <div class="f {{ $errors->has('oil_field_id') ? 'has-err' : '' }}"><label class="lb" for="oil_field_id"><i class="fe fe-layers"></i> Oil field <b>*</b></label>
                            <select name="oil_field_id" id="oil_field_id" class="fc" required>
                                <option value="">Select field…</option>
                                @foreach($oilFields as $of)<option value="{{ $of->id }}" @selected($v('oil_field_id') == $of->id)>{{ $of->name }}{{ $of->code ? ' ('.$of->code.')' : '' }}</option>@endforeach
                            </select>@error('oil_field_id')<span class="em">{{ $message }}</span>@enderror</div>
                        <div class="f {{ $errors->has('tower_id') ? 'has-err' : '' }}"><label class="lb" for="tower_id"><i class="fe fe-corner-down-right"></i> Tower <b>*</b></label>
                            <select name="tower_id" id="tower_id" class="fc" required data-init="{{ $v('tower_id') }}">
                                <option value="">Select tower…</option>
                                @foreach($towers as $t)<option value="{{ $t->id }}" @selected($v('tower_id') == $t->id)>{{ $t->name }}{{ $t->code ? ' ('.$t->code.')' : '' }}</option>@endforeach
                            </select>@error('tower_id')<span class="em">{{ $message }}</span>@enderror</div>
                        <div class="f {{ $errors->has('oil_string_id') ? 'has-err' : '' }}"><label class="lb" for="oil_string_id"><i class="fe fe-trending-up"></i> Oil string <b>*</b></label>
                            <select name="oil_string_id" id="oil_string_id" class="fc" required data-init="{{ $v('oil_string_id') }}">
                                <option value="">Select string…</option>
                                @foreach($oilStrings as $s)<option value="{{ $s->id }}" @selected($v('oil_string_id') == $s->id)>{{ $s->name }}{{ $s->code ? ' ('.$s->code.')' : '' }}</option>@endforeach
                            </select>@error('oil_string_id')<span class="em">{{ $message }}</span>@enderror</div>
                        <div class="f {{ $errors->has('mpfm_operational_plan_id') ? 'has-err' : '' }}"><label class="lb" for="mpfm_operational_plan_id"><i class="fe fe-calendar"></i> Operational plan</label>
                            <select name="mpfm_operational_plan_id" id="mpfm_operational_plan_id" class="fc">
                                <option value="">None</option>
                                @foreach($operationalPlans as $plan)<option value="{{ $plan->id }}" @selected($v('mpfm_operational_plan_id') == $plan->id)>{{ $plan->name }} — {{ $plan->oilField->name ?? '' }} / {{ $plan->tower->name ?? '' }}</option>@endforeach
                            </select>@error('mpfm_operational_plan_id')<span class="em">{{ $message }}</span>@enderror</div>
                    </div>
                </div>

                {{-- 2 WHEN --}}
                <div class="panel rv" style="--d:1">
                    <div class="ph num"><span class="n">2</span><div><h3>When</h3><small>Test date and shift</small></div></div>
                    <div class="row">
                        <div class="f {{ $errors->has('test_date') ? 'has-err' : '' }}"><label class="lb" for="test_date"><i class="fe fe-calendar"></i> Test date <b>*</b></label>
                            <input type="date" name="test_date" id="test_date" class="fc" value="{{ old('test_date', optional($mpfmTest->test_date)->format('Y-m-d')) }}" required>
                            @error('test_date')<span class="em">{{ $message }}</span>@enderror</div>
                        <div class="f {{ $errors->has('shift') ? 'has-err' : '' }}"><label class="lb"><i class="fe fe-clock"></i> Shift <b>*</b></label>
                            <div class="shifts">
                                <input type="radio" name="shift" id="sh1" value="morning" @checked($shiftSel === 'morning')><label for="sh1"><svg viewBox="0 0 32 32" fill="none" stroke="#fbbf24" stroke-width="2" stroke-linecap="round"><path d="M4 22h24M9 22a7 7 0 0 1 14 0M16 6v4M6 12l3 3M26 12l-3 3"/></svg>Morning</label>
                                <input type="radio" name="shift" id="sh2" value="afternoon" @checked($shiftSel === 'afternoon')><label for="sh2"><svg viewBox="0 0 32 32" fill="none" stroke="#fb923c" stroke-width="2" stroke-linecap="round"><circle cx="16" cy="16" r="5"/><path d="M16 4v3M16 25v3M4 16h3M25 16h3M7.5 7.5l2 2M22.5 22.5l2 2M7.5 24.5l2-2M22.5 9.5l2-2"/></svg>Afternoon</label>
                                <input type="radio" name="shift" id="sh3" value="night" @checked($shiftSel === 'night')><label for="sh3"><svg viewBox="0 0 32 32" fill="none" stroke="#a5b4fc" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M24 20A10 10 0 0 1 12 8a10 10 0 1 0 12 12z"/></svg>Night</label>
                            </div>@error('shift')<span class="em">{{ $message }}</span>@enderror</div>
                    </div>
                </div>

                {{-- 3 PRODUCTION --}}
                <div class="panel rv" style="--d:2">
                    <div class="ph num"><span class="n">3</span><div><h3>Production</h3><small>Rates reported by the meter</small></div></div>
                    <div class="row">
                        <div class="f {{ $errors->has('oil_rate') ? 'has-err' : '' }}"><label class="lb" for="oil_rate"><i class="fe fe-droplet"></i> Oil rate</label>
                            <div class="ctl u"><input type="number" name="oil_rate" id="oil_rate" class="fc" step="0.01" min="0" value="{{ $v('oil_rate') }}" placeholder="0.00"><span class="unit">STB/D</span></div>
                            @error('oil_rate')<span class="em">{{ $message }}</span>@enderror</div>
                        <div class="f {{ $errors->has('gas_rate') ? 'has-err' : '' }}"><label class="lb" for="gas_rate"><i class="fe fe-wind"></i> Gas rate</label>
                            <div class="ctl u"><input type="number" name="gas_rate" id="gas_rate" class="fc" step="0.01" min="0" value="{{ $v('gas_rate') }}" placeholder="0.00"><span class="unit">MMscf/D</span></div>
                            @error('gas_rate')<span class="em">{{ $message }}</span>@enderror</div>
                        <div class="f {{ $errors->has('water_cut') ? 'has-err' : '' }}"><label class="lb" for="water_cut"><i class="fe fe-percent"></i> Water cut</label>
                            <div class="ctl u"><input type="number" name="water_cut" id="water_cut" class="fc" step="0.01" min="0" max="100" value="{{ $v('water_cut') }}" placeholder="0.00"><span class="unit">%</span></div>
                            <input type="range" id="wcRange" min="0" max="100" step="0.5" value="{{ (float) $v('water_cut', 0) }}" aria-label="Water cut slider">
                            <div class="rl"><span>0% dry oil</span><span>100% water</span></div>
                            @error('water_cut')<span class="em">{{ $message }}</span>@enderror</div>
                    </div>
                </div>

                {{-- 4 CONDITIONS --}}
                <div class="panel rv" style="--d:3">
                    <div class="ph num"><span class="n">4</span><div><h3>Operating conditions</h3><small>Line pressure and temperature</small></div></div>
                    <div class="row">
                        <div class="f {{ $errors->has('pressure') ? 'has-err' : '' }}"><label class="lb" for="pressure"><i class="fe fe-activity"></i> Pressure</label>
                            <div class="ctl u"><input type="number" name="pressure" id="pressure" class="fc" step="0.01" min="0" value="{{ $v('pressure') }}" placeholder="0.00"><span class="unit">psi</span></div>
                            @error('pressure')<span class="em">{{ $message }}</span>@enderror</div>
                        <div class="f {{ $errors->has('temperature') ? 'has-err' : '' }}"><label class="lb" for="temperature"><i class="fe fe-thermometer"></i> Temperature</label>
                            <div class="ctl u"><input type="number" name="temperature" id="temperature" class="fc" step="0.01" value="{{ $v('temperature') }}" placeholder="0.00"><span class="unit">°F</span></div>
                            @error('temperature')<span class="em">{{ $message }}</span>@enderror</div>
                    </div>
                </div>

                {{-- 5 STATUS + NOTES --}}
                <div class="panel rv" style="--d:4">
                    <div class="ph num"><span class="n">5</span><div><h3>Status &amp; notes</h3><small>Where this test is in the review flow</small></div></div>
                    <div class="f {{ $errors->has('status') ? 'has-err' : '' }}" style="margin-bottom:16px"><label class="lb"><i class="fe fe-flag"></i> Status</label>
                        <div class="stp">
                            @foreach(['draft' => 'Draft', 'submitted' => 'Submitted', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $sv => $sl)
                                <input type="radio" name="status" id="st_{{ $sv }}" value="{{ $sv }}" @checked($statusSel === $sv)><label for="st_{{ $sv }}" style="--c:{{ $stColors[$sv] }}">{{ $sl }}</label>
                            @endforeach
                        </div>
                        @error('status')<span class="em">{{ $message }}</span>@enderror</div>
                    <div class="f"><label class="lb" for="notes"><i class="fe fe-edit-3"></i> Notes &amp; observations</label>
                        <textarea name="notes" id="notes" rows="4" class="fc" placeholder="Anything unusual — unstable flow, meter alarms, choke changes…">{{ $v('notes') }}</textarea>
                        @error('notes')<span class="em">{{ $message }}</span>@enderror</div>
                </div>

                <div class="bar rv">
                    <button type="submit" class="btn2 p"><i class="fe fe-save"></i> Update test</button>
                    <a href="{{ route('mpfm_tests.show', $mpfmTest->id) }}" class="btn2"><i class="fe fe-x"></i> Cancel</a>
                    <span class="sp"></span><span class="hint">Fields marked * are required</span>
                </div>
            </div>

            {{-- LIVE SIDE --}}
            <aside class="side">
                <div class="panel live rv">
                    <div class="ph" style="margin-bottom:6px"><h3 style="font-size:16px">Live meter preview</h3></div>
                    <svg viewBox="0 0 420 250" aria-label="Live multiphase meter preview">
                        <defs>
                            <linearGradient id="mfSteel" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#a8b6cb"/><stop offset=".16" stop-color="#f1f5f9"/><stop offset=".5" stop-color="#7c8aa1"/><stop offset="1" stop-color="#1b2433"/></linearGradient>
                            <linearGradient id="mfBody" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#ef6a5e"/><stop offset=".5" stop-color="#b3322a"/><stop offset="1" stop-color="#5d1510"/></linearGradient>
                            <linearGradient id="mfOil" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#f59e0b"/><stop offset="1" stop-color="#78350f"/></linearGradient>
                            <linearGradient id="mfWat" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#0ea5e9"/><stop offset="1" stop-color="#0c4a6e"/></linearGradient>
                            <clipPath id="mfIn"><rect x="120" y="116" width="180" height="40" rx="6"/></clipPath>
                            <path id="mfPipe" d="M6 136 H414"/>
                        </defs>
                        <ellipse cx="210" cy="136" rx="200" ry="70" fill="#22d3ee" opacity=".06"/>
                        <use href="#mfPipe" fill="none" stroke="#0b1220" stroke-width="30"/><use href="#mfPipe" fill="none" stroke="url(#mfSteel)" stroke-width="26"/>
                        <rect x="104" y="108" width="12" height="56" rx="3" fill="#64748b" stroke="#0b1220"/><rect x="304" y="108" width="12" height="56" rx="3" fill="#64748b" stroke="#0b1220"/>
                        <path d="M118 112 L160 122 H260 L302 112 V160 L260 150 H160 L118 160 Z" fill="url(#mfBody)" stroke="#0b1220" stroke-width="1.4"/>
                        <g clip-path="url(#mfIn)" opacity=".92">
                            <rect x="120" y="116" width="180" height="40" fill="#04101f" opacity=".7"/>
                            <rect x="120" y="116" width="180" height="40" fill="url(#mfOil)"/>
                            <rect class="lvw" id="mfWater" x="120" y="116" width="180" height="40" fill="url(#mfWat)"/>
                            <g class="wv"><path d="M100 140 q10 -4 20 0 t20 0 t20 0 t20 0 t20 0 t20 0 t20 0 t20 0 t20 0 t20 0 t20 0" fill="none" stroke="#fde68a" stroke-opacity=".7" stroke-width="1.2"/></g>
                        </g>
                        <rect x="176" y="62" width="68" height="46" rx="8" fill="url(#mfSteel)" stroke="#0b1220"/>
                        <rect x="183" y="69" width="54" height="22" rx="3" fill="#06230f"/><text id="mfRead" x="210" y="85" text-anchor="middle" font-size="13" font-weight="800" fill="#4ade80">—</text>
                        <circle cx="191" cy="99" r="2.4" fill="#4ade80"><animate attributeName="opacity" values="1;.2;1" dur="1.2s" repeatCount="indefinite"/></circle><circle cx="200" cy="99" r="2.4" fill="#fbbf24"/>
                        <text x="210" y="56" text-anchor="middle" font-size="9" font-weight="800" letter-spacing="2" fill="#22d3ee">MPFM</text>
                        @foreach([[70, 'ndP', 'PSI'], [350, 'ndT', '°F']] as [$dx, $dn, $du])
                            <g transform="translate({{ $dx }},70)"><rect x="-3" y="22" width="6" height="30" fill="#64748b"/>
                                <circle r="26" fill="url(#mfSteel)" stroke="#0b1220"/><circle r="22" fill="#0a1222" stroke="#475569"/>
                                <path d="M-19 10 A22 22 0 0 1 -10 -19" fill="none" stroke="#34d399" stroke-width="2.5" opacity=".7"/><path d="M10 -19 A22 22 0 0 1 19 10" fill="none" stroke="#ef4444" stroke-width="2.5" opacity=".8"/>
                                <text y="15" text-anchor="middle" font-size="6.5" font-weight="700" fill="#7dd3fc">{{ $du }}</text>
                                <g class="nd" id="{{ $dn }}"><path d="M-1.6 4 L0 -18 L1.6 4 Z" fill="#fbbf24"/></g><circle r="3" fill="#e2e8f0"/></g>
                        @endforeach
                        @foreach([0, .9, 1.8, 2.7] as $b)
                            <circle r="3" fill="#fde68a"><animateMotion dur="3.2s" begin="{{ $b }}s" repeatCount="indefinite"><mpath href="#mfPipe"/></animateMotion></circle>
                        @endforeach
                        <text x="8" y="204" font-size="8.5" font-weight="700" letter-spacing="1.3" fill="#fcd34d">WELL STREAM →</text>
                        <text x="412" y="204" text-anchor="end" font-size="8.5" font-weight="700" letter-spacing="1.3" fill="#9fb0cf">TO SEPARATOR</text>
                        <text x="210" y="190" text-anchor="middle" font-size="8.5" fill="#94a3b8" letter-spacing="1.3">γ-RAY VENTURI · MULTIPHASE</text>
                    </svg>
                    <div class="stats">
                        <div class="stt"><small>Oil</small><b id="stO">—</b><span>STB/D</span></div>
                        <div class="stt"><small>Gas</small><b id="stG">—</b><span>MMscf/D</span></div>
                        <div class="stt"><small>Water cut</small><b id="stW">—</b><span>%</span></div>
                    </div>
                </div>

                <div class="panel rv" style="--d:1">
                    <div class="prog">
                        <svg viewBox="0 0 100 100"><circle class="arc" cx="50" cy="50" r="40" pathLength="100" stroke="rgba(255,255,255,.08)" style="stroke-dasharray:100 100"/><circle class="arc" id="pArc" cx="50" cy="50" r="40" pathLength="100" stroke="#22d3ee" style="stroke-dasharray:0 100"/><text id="pTxt" x="50" y="56" text-anchor="middle" font-family="Space Grotesk" font-weight="700" font-size="20" fill="#fff">0%</text></svg>
                        <div><b id="pLbl">Required fields</b><small id="pSub">Field, tower, oil string, date and shift are required.</small></div>
                    </div>
                </div>

                <div class="panel rv" style="--d:2">
                    <div class="ph" style="margin-bottom:12px"><h3 style="font-size:15px">Good to know</h3></div>
                    <ul class="tips">
                        <li><i class="fe fe-check-circle"></i> Changing the field or tower reloads the towers and strings below it.</li>
                        <li><i class="fe fe-check-circle"></i> Water cut is a percentage of liquid, from 0 to 100.</li>
                        <li><i class="fe fe-check-circle"></i> <span>Setting the status to <b>Approved</b> here skips the review steps — use it only to correct a record.</span></li>
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
    var $ = function (id) { return document.getElementById(id); }, root = $('ofp');

    function fill(sel, rows, placeholder, keep) {
        sel.innerHTML = '<option value="">' + placeholder + '</option>';
        rows.forEach(function (r) { var o = document.createElement('option'); o.value = r.id; o.textContent = r.name + (r.code ? ' (' + r.code + ')' : ''); if (String(r.id) === String(keep)) o.selected = true; sel.appendChild(o); });
    }
    function getJSON(url, cb) { fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }, credentials: 'same-origin' }).then(function (r) { return r.json(); }).then(cb).catch(function () {}); }
    function loadTowers(keep, then) {
        var f = $('oil_field_id').value;
        if (!f) { fill($('tower_id'), [], 'Select tower…'); fill($('oil_string_id'), [], 'Select string…'); return then && then(); }
        getJSON('/ajax/operational-plans/towers/' + f, function (d) { fill($('tower_id'), d, 'Select tower…', keep); then && then(); update(); });
    }
    function loadStrings(keep) {
        var t = $('tower_id').value;
        if (!t) { fill($('oil_string_id'), [], 'Select string…'); update(); return; }
        getJSON('/ajax/operational-plans/oil-strings/' + t, function (d) { fill($('oil_string_id'), d, 'Select string…', keep); update(); });
    }
    $('oil_field_id').addEventListener('change', function () { loadTowers('', function () { loadStrings(''); }); });
    $('tower_id').addEventListener('change', function () { loadStrings(''); });

    function num(id) { var v = parseFloat($(id).value); return isNaN(v) ? null : v; }
    function fmt(v, d) { return v === null ? '—' : v.toLocaleString('en-US', { minimumFractionDigits: d, maximumFractionDigits: d }); }
    function ang(f) { return -120 + 240 * Math.max(0, Math.min(1, f)); }
    function update() {
        var wc = num('water_cut'), p = num('pressure'), t = num('temperature'), o = num('oil_rate'), g = num('gas_rate'), w = Math.max(0, Math.min(100, wc || 0));
        $('mfWater').style.transform = 'scaleY(' + (w / 100) + ')';
        $('ndP').style.transform = 'rotate(' + ang((p || 0) / 3000) + 'deg)';
        $('ndT').style.transform = 'rotate(' + ang(((t === null ? -50 : t) + 50) / 300) + 'deg)';
        $('stO').textContent = fmt(o, 0); $('stG').textContent = fmt(g, 1); $('stW').textContent = wc === null ? '—' : fmt(wc, 1);
        $('mfRead').textContent = o === null ? '—' : fmt(o, 0);
        if (document.activeElement !== $('wcRange')) $('wcRange').value = w;
        var req = ['oil_field_id', 'tower_id', 'oil_string_id', 'test_date'];
        var done = req.filter(function (id) { return $(id).value !== ''; }).length + (root.querySelector('input[name=shift]:checked') ? 1 : 0), total = req.length + 1, pc = Math.round(done / total * 100);
        $('pArc').style.strokeDasharray = pc + ' 100'; $('pArc').setAttribute('stroke', pc === 100 ? '#34d399' : '#22d3ee'); $('pTxt').textContent = pc + '%';
        $('pLbl').textContent = done + ' of ' + total + ' required fields';
        $('pSub').textContent = pc === 100 ? 'All set — ready to save.' : 'Field, tower, oil string, date and shift are required.';
    }
    root.addEventListener('input', update); root.addEventListener('change', update);
    $('wcRange').addEventListener('input', function () { $('water_cut').value = this.value; update(); });

    $('mForm').addEventListener('submit', function (e) {
        var bad = null;
        root.querySelectorAll('[required]').forEach(function (el) { var empty = el.value === ''; el.classList.toggle('bad', empty); if (empty && !bad) bad = el; });
        if (!root.querySelector('input[name=shift]:checked')) { root.querySelector('.shifts').scrollIntoView({ block: 'center', behavior: 'smooth' }); e.preventDefault(); return; }
        if (bad) { e.preventDefault(); bad.scrollIntoView({ block: 'center', behavior: 'smooth' }); bad.focus(); }
    });

    var io = new IntersectionObserver(function (es) { es.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } }); }, { threshold: .08 });
    root.querySelectorAll('.rv').forEach(function (el) { io.observe(el); });

    var initT = $('tower_id').dataset.init, initS = $('oil_string_id').dataset.init;
    if ($('oil_field_id').value && $('tower_id').options.length <= 1) loadTowers(initT, function () { loadStrings(initS); });
    else if ($('tower_id').value && $('oil_string_id').options.length <= 1) loadStrings(initS);
    update();
    if (window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches) root.querySelectorAll('svg').forEach(function (s) { s.pauseAnimations && s.pauseAnimations(); });
})();
</script>
@endsection
