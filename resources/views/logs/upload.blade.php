@extends('layouts.master')

@section('title', 'Upload Well Test Log')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/ofpage.css') }}">
<style>
.ofp .grid { display:grid; grid-template-columns:1fr 380px; gap:20px; margin-top:20px; align-items:start; }
.ofp .side { position:sticky; top:84px; display:grid; gap:16px; }
.ofp .main > .panel { margin-bottom:16px; }
.ofp .ph.num { justify-content:flex-start; gap:12px; }
.ofp .ph .n { width:32px; height:32px; border-radius:50%; background:var(--grad); color:#04111f; font:700 14px 'Space Grotesk'; display:grid; place-items:center; flex:none; }
.ofp .ph.num h3 { font-size:16px; font-weight:800; margin:0; display:block; } .ofp .ph.num small { display:block; color:var(--mute); font-size:12px; font-weight:500; margin-top:1px; }
.ofp .ph .auto { margin-left:auto; font-size:11.5px; color:#bbf7d0; display:inline-flex; gap:6px; align-items:center; padding:3px 11px; border-radius:50px; border:1px solid rgba(52,211,153,.4); background:rgba(52,211,153,.08); }
.ofp .row { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:16px; }
.ofp .f label.lb { display:flex; align-items:center; gap:7px; font-size:10.5px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--mute); margin-bottom:7px; }
.ofp .f label.lb b { color:var(--red); } .ofp .f label.lb i { color:var(--cyan); font-size:13px; }
.ofp .fc { width:100%; height:46px; padding:0 14px; border-radius:14px; border:1px solid var(--line); background:rgba(255,255,255,.06); color:#fff; font:500 14px 'Inter',sans-serif; transition:border-color .25s, box-shadow .25s, background .25s; outline:none; appearance:none; -webkit-appearance:none; }
.ofp .fc:focus { border-color:var(--cyan); box-shadow:0 0 0 4px rgba(34,211,238,.14); background:rgba(255,255,255,.09); }
.ofp .fc::placeholder { color:#52627d; }
.ofp .fc[readonly], .ofp .fc:disabled { opacity:.7; border-style:dashed; cursor:not-allowed; }
.ofp select.fc { padding-right:36px; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' fill='none' stroke='%2322d3ee' stroke-width='2'%3E%3Cpath d='M1 1l5 5 5-5'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 14px center; }
.ofp select.fc option { background:#0b1426; color:#fff; }
.ofp input[type=date].fc { color-scheme:dark; }
.ofp .fc.bad, .ofp .f.has-err .fc { border-color:var(--red); }
.ofp .em { color:#fca5a5; font-size:12px; margin-top:5px; display:block; }
.ofp .err { margin-top:18px; padding:14px 18px; border-radius:16px; border:1px solid rgba(248,113,113,.45); background:rgba(248,113,113,.08); color:#fecaca; font-size:13px; }
.ofp .err ul { margin:8px 0 0; padding-left:18px; }
.ofp .okb { margin-top:18px; padding:14px 18px; border-radius:16px; border:1px solid rgba(52,211,153,.4); background:rgba(52,211,153,.08); color:#bbf7d0; font-size:13px; }

/* task context */
.ofp .task { margin-top:18px; display:grid; grid-template-columns:1fr auto; gap:18px; align-items:start; }
.ofp .chips { display:flex; gap:10px; flex-wrap:wrap; margin-top:12px; }
.ofp .chips span { display:inline-flex; align-items:center; gap:8px; padding:7px 14px; border-radius:50px; font-size:12.5px; border:1px solid var(--line); background:var(--glass); color:var(--sub); }
.ofp .chips span i { color:var(--cyan); } .ofp .chips span b { color:#fff; font-weight:700; }
.ofp .exp { min-width:230px; padding:14px 16px; border-radius:16px; border:1px solid rgba(251,191,36,.35); background:rgba(251,191,36,.07); }
.ofp .exp h4 { margin:0 0 8px; font-size:11px; letter-spacing:.12em; text-transform:uppercase; color:var(--amber); display:flex; gap:7px; align-items:center; }
.ofp .exp div { display:flex; justify-content:space-between; gap:16px; font-size:13px; color:var(--sub); padding:2px 0; } .ofp .exp div b { color:#fff; font-family:'Space Grotesk'; }

/* dropzone */
.ofp .dz { position:relative; border-radius:22px; border:2px dashed rgba(96,165,250,.35); background:radial-gradient(420px 200px at 50% 0%,rgba(34,211,238,.08),transparent 70%),rgba(255,255,255,.03); padding:26px 20px; text-align:center; cursor:pointer; transition:border-color .3s, background .3s, transform .3s var(--ease); }
.ofp .dz:hover { border-color:var(--cyan); transform:translateY(-2px); }
.ofp .dz.over { border-color:var(--cyan); background:radial-gradient(420px 220px at 50% 0%,rgba(34,211,238,.22),transparent 70%),rgba(34,211,238,.06); box-shadow:0 0 40px rgba(34,211,238,.25); }
.ofp .dz.has { border-style:solid; border-color:rgba(52,211,153,.55); background:rgba(52,211,153,.06); }
.ofp .dz.bad { border-color:var(--red); }
.ofp .dz input { position:absolute; inset:0; opacity:0; cursor:pointer; width:100%; height:100%; }
.ofp .dz svg { width:100%; max-width:380px; height:auto; display:block; margin:0 auto 6px; overflow:visible; }
.ofp .dz svg text { font-family:'Inter',sans-serif; }
.ofp .dz h4 { margin:0; font-size:16px; font-weight:800; color:#fff; } .ofp .dz p { margin:5px 0 0; font-size:12.5px; color:var(--sub); }
.ofp .dz .ext { display:inline-flex; gap:8px; margin-top:10px; } .ofp .dz .ext span { padding:3px 11px; border-radius:50px; font-size:11px; font-weight:700; border:1px solid var(--line); color:var(--cyan); background:rgba(34,211,238,.08); }
.ofp .scan { animation:ulScan 2.4s ease-in-out infinite; } @keyframes ulScan { 0%,100% { transform:translateY(0); opacity:.2; } 50% { transform:translateY(42px); opacity:1; } }
.ofp .ulfly { animation:ulFly 3s ease-in-out infinite; } @keyframes ulFly { 0%,100% { transform:translateX(0); opacity:.35; } 50% { transform:translateX(12px); opacity:1; } }
.ofp .ulflow { fill:none; stroke-width:2; stroke-linecap:round; stroke-dasharray:3 7; animation:ulDash 1s linear infinite; } @keyframes ulDash { to { stroke-dashoffset:-20; } }
.ofp .dz.has .scan, .ofp .dz.has .ulfly { animation-play-state:paused; }
.ofp .picked { display:none; margin-top:14px; padding:14px 16px; border-radius:16px; border:1px solid rgba(52,211,153,.45); background:rgba(52,211,153,.08); align-items:center; gap:14px; }
.ofp .picked.on { display:flex; animation:ofFade .5s var(--ease); }
.ofp .picked .fi { width:44px; height:44px; border-radius:13px; display:grid; place-items:center; background:rgba(52,211,153,.18); color:var(--green); font-size:20px; flex:none; }
.ofp .picked b { display:block; color:#fff; font-size:14px; word-break:break-all; } .ofp .picked small { color:#a7f3d0; font-size:12px; }
.ofp .picked button { margin-left:auto; background:none; border:1px solid var(--line); color:var(--sub); border-radius:50px; padding:6px 14px; font:600 12px 'Inter'; cursor:pointer; flex:none; } .ofp .picked button:hover { border-color:var(--red); color:#fca5a5; }

.ofp .bar { display:flex; gap:12px; flex-wrap:wrap; align-items:center; padding:18px 22px; border-radius:22px; border:1px solid var(--line); background:rgba(255,255,255,.04); }
.ofp .bar .sp { flex:1; } .ofp .bar .hint { font-size:12px; color:var(--mute); }
.ofp .btn2:disabled { opacity:.6; cursor:progress; transform:none; }
.ofp .spin { width:15px; height:15px; border-radius:50%; border:2px solid rgba(4,17,31,.25); border-top-color:#04111f; animation:ulSpin .8s linear infinite; } @keyframes ulSpin { to { transform:rotate(360deg); } }
.ofp .toast { display:none; margin-top:14px; padding:12px 16px; border-radius:14px; border:1px solid rgba(248,113,113,.45); background:rgba(248,113,113,.08); color:#fecaca; font-size:13px; }
.ofp .toast.on { display:block; }

/* side */
.ofp .prog { display:flex; align-items:center; gap:16px; }
.ofp .prog svg { width:84px; height:84px; flex:none; } .ofp .prog .arc { fill:none; stroke-width:9; stroke-linecap:round; transform:rotate(-90deg); transform-origin:50% 50%; transition:stroke-dasharray .6s var(--ease); animation:none; }
.ofp .prog b { display:block; font-size:14px; } .ofp .prog small { color:var(--mute); font-size:12px; line-height:1.5; display:block; margin-top:2px; }
.ofp .next { list-style:none; margin:0; padding:0; display:grid; gap:0; counter-reset:n; }
.ofp .next li { display:flex; gap:14px; padding:0 0 18px; position:relative; }
.ofp .next li:last-child { padding-bottom:0; }
.ofp .next li::before { content:""; position:absolute; left:17px; top:36px; bottom:0; width:2px; background:repeating-linear-gradient(180deg,rgba(34,211,238,.5) 0 5px,transparent 5px 10px); }
.ofp .next li:last-child::before { display:none; }
.ofp .next .ic { width:36px; height:36px; border-radius:12px; display:grid; place-items:center; background:rgba(34,211,238,.1); border:1px solid rgba(34,211,238,.3); color:var(--cyan); flex:none; font-size:16px; }
.ofp .next b { display:block; font-size:13px; color:#fff; } .ofp .next small { font-size:12px; color:var(--sub); line-height:1.45; display:block; margin-top:2px; }
.ofp .tips { margin:0; padding:0; list-style:none; display:grid; gap:9px; } .ofp .tips li { display:flex; gap:10px; font-size:12.5px; color:var(--sub); line-height:1.5; } .ofp .tips i { color:var(--cyan); margin-top:2px; }
@media (max-width:1200px) { .ofp .grid { grid-template-columns:1fr; } .ofp .side { position:static; grid-template-columns:1fr 1fr; } .ofp .task { grid-template-columns:1fr; } }
@media (max-width:760px) { .ofp .side { grid-template-columns:1fr; } }
</style>
@endsection

@section('content')
@php
    $hasTask = isset($task) || isset($newTask);
    $af = $autoFillData ?? [];
    $field = fn ($k, $d = '') => old($k, $af[$k] ?? $d);
    $testType = old('test_type', $af['test_type'] ?? '');
@endphp
<div class="ofp" id="ofp">
    <div class="bgl"><div class="orb o1"></div><div class="orb o2"></div></div>

    {{-- HERO --}}
    <div class="hero rv">
        <div class="who">
            <span class="lg"></span>
            <div>
                <div class="eyebrow"><i></i> MPFM · VX log</div>
                <h1>Upload a <em>well test log</em></h1>
                <div class="meta">
                    <a href="{{ url('/dashboard') }}"><i class="fe fe-home"></i> Home</a><span>/</span>
                    @if(isset($newTask))<a href="{{ route('tasks.show', $newTask) }}">Task #{{ $newTask->id }}</a><span>/</span>
                    @elseif(isset($task))<a href="{{ route('operator.tasks') }}">My tasks</a><span>/</span>@endif
                    <span>Upload log</span>
                </div>
            </div>
        </div>
        <div class="actions">
            @if(isset($newTask))<a class="btn2" href="{{ route('tasks.show', $newTask) }}"><i class="fe fe-arrow-left"></i> Back to task</a>
            @elseif(isset($task))<a class="btn2" href="{{ route('operator.tasks.show', $task) }}"><i class="fe fe-arrow-left"></i> Back to task</a>
            @else<a class="btn2" href="{{ route('mpfm_tests.index') }}"><i class="fe fe-activity"></i> MPFM tests</a>@endif
        </div>
    </div>

    {{-- TASK CONTEXT --}}
    @if(isset($newTask))
    <div class="panel task rv">
        <div>
            <div class="ph" style="margin-bottom:0"><h3><i class="fe fe-info"></i> Task: {{ $newTask->title }}</h3></div>
            <div class="chips">
                @if($newTask->tower)<span><i class="fe fe-layers"></i> Tower <b>{{ $newTask->tower->name }}</b></span>@endif
                @if($newTask->oilString)<span><i class="fe fe-droplet"></i> String <b>{{ $newTask->oilString->name }}</b></span>@endif
                <span><i class="fe fe-briefcase"></i> Department <b>{{ $newTask->department?->name ?? 'N/A' }}</b></span>
                <span><i class="fe fe-flag"></i> Priority <b>{{ ucfirst($newTask->priority) }}</b></span>
            </div>
        </div>
    </div>
    @elseif(isset($task))
    <div class="panel task rv">
        <div>
            <div class="ph" style="margin-bottom:0"><h3><i class="fe fe-info"></i> Task: {{ $task->task_identifier }}</h3></div>
            <div class="chips">
                <span><i class="fe fe-map-pin"></i> Field <b>{{ $task->towerModel?->oilField?->name ?? $task->oilString?->tower?->oilField?->name ?? ($af['field'] ?? 'N/A') }}</b></span>
                <span><i class="fe fe-layers"></i> Tower <b>{{ $task->towerModel?->name ?? $task->oilString?->tower?->name ?? ($af['wellhead_tower'] ?? 'N/A') }}</b></span>
                <span><i class="fe fe-droplet"></i> String <b>{{ $task->oilString?->name ?? ($af['wellhead_string'] ?? 'N/A') }}</b></span>
                <span><i class="fe fe-settings"></i> MPFM device <b>{{ $task->mpfm_device ?? 'N/A' }}</b></span>
            </div>
            @if($task->task_instructions)<div class="note"><small>Instructions</small>{{ $task->task_instructions }}</div>@endif
        </div>
        @if($task->expected_oil_rate || $task->expected_gas_rate)
        <div class="exp">
            <h4><i class="fe fe-target"></i> Expected values</h4>
            @if($task->expected_oil_rate)<div><span>Oil rate</span><b>{{ number_format($task->expected_oil_rate, 2) }} STB/d</b></div>@endif
            @if($task->expected_gas_rate)<div><span>Gas rate</span><b>{{ number_format($task->expected_gas_rate, 2) }} MMScf/d</b></div>@endif
            @if($task->expected_water_cut)<div><span>Water cut</span><b>{{ number_format($task->expected_water_cut, 2) }}%</b></div>@endif
        </div>
        @endif
    </div>
    @endif

    @if(session('success'))<div class="okb"><i class="fe fe-check-circle"></i> <b>Success!</b> {{ session('success') }}</div>@endif
    @if(session('error'))<div class="err"><i class="fe fe-alert-circle"></i> <b>Error!</b> {{ session('error') }}</div>@endif
    @if($errors->any())
        <div class="err"><b><i class="fe fe-alert-triangle"></i> There were some problems with your input:</b><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <form action="{{ route('process.log') }}" method="POST" enctype="multipart/form-data" id="uploadForm" novalidate>
        @csrf
        @if(isset($newTask))<input type="hidden" name="new_task_id" value="{{ $newTask->id }}">
        @elseif(isset($task))<input type="hidden" name="task_id" value="{{ $task->id }}">@endif

        <div class="grid">
            <div class="main">
                {{-- 1 well --}}
                <div class="panel rv">
                    <div class="ph num"><span class="n">1</span><div><h3>Well information</h3><small>Which well was tested</small></div>@if($hasTask)<span class="auto"><i class="fe fe-check-circle"></i> Auto-filled from task</span>@endif</div>
                    <div class="row">
                        <div class="f {{ $errors->has('client') ? 'has-err' : '' }}"><label class="lb" for="client"><i class="fe fe-briefcase"></i> Client <b>*</b></label>
                            @if($hasTask)
                                <input class="fc" type="text" name="client" id="client" value="{{ $field('client') }}" readonly required>
                            @else
                                <select class="fc" id="clientSel" aria-label="Client"></select>
                                <input class="fc" type="text" id="clientOther" placeholder="Type the client name" style="display:none;margin-top:8px">
                                <input type="hidden" name="client" id="client" value="{{ $field('client') }}" data-vis="clientSel" required>
                            @endif
                            @error('client')<span class="em">{{ $message }}</span>@enderror</div>
                        <div class="f {{ $errors->has('field') ? 'has-err' : '' }}"><label class="lb" for="field"><i class="fe fe-map-pin"></i> Field <b>*</b></label>
                            @if($hasTask)
                                <input class="fc" type="text" name="field" id="field" value="{{ $field('field') }}" readonly required>
                            @else
                                <select class="fc" id="fieldSel" aria-label="Field"></select>
                                <input type="hidden" name="field" id="field" value="{{ $field('field') }}" data-vis="fieldSel" required>
                            @endif
                            @error('field')<span class="em">{{ $message }}</span>@enderror</div>
                        <div class="f {{ $errors->has('well') ? 'has-err' : '' }}"><label class="lb" for="well"><i class="fe fe-droplet"></i> Well name <b>*</b></label>
                            @if($hasTask)
                                <input class="fc" type="text" name="well" id="well" value="{{ $field('well') }}" readonly required>
                            @else
                                <select class="fc" id="wellSel" aria-label="Well"></select>
                                <input type="hidden" name="well" id="well" value="{{ $field('well') }}" data-vis="wellSel" required>
                            @endif
                            @error('well')<span class="em">{{ $message }}</span>@enderror</div>
                    </div>
                </div>

                {{-- 2 config --}}
                <div class="panel rv" style="--d:1">
                    <div class="ph num"><span class="n">2</span><div><h3>Test configuration</h3><small>How and when the test was run</small></div>@if($hasTask)<span class="auto"><i class="fe fe-check-circle"></i> Auto-filled from task</span>@endif</div>
                    <div class="row">
                        <div class="f"><label class="lb" for="wellhead_tower"><i class="fe fe-layers"></i> Wellhead tower</label>
                            @if($hasTask)
                                <input class="fc" type="text" name="wellhead_tower" id="wellhead_tower" value="{{ $field('wellhead_tower') }}" readonly>
                            @else
                                <select class="fc" id="towerSel" aria-label="Wellhead tower"></select>
                                <input type="hidden" name="wellhead_tower" id="wellhead_tower" value="{{ $field('wellhead_tower') }}">
                            @endif</div>
                        <div class="f"><label class="lb" for="wellhead_string"><i class="fe fe-trending-up"></i> Wellhead string</label>
                            @if($hasTask)
                                <input class="fc" type="text" name="wellhead_string" id="wellhead_string" value="{{ $field('wellhead_string') }}" readonly>
                            @else
                                <select class="fc" id="stringSel" aria-label="Wellhead string"></select>
                                <input type="hidden" name="wellhead_string" id="wellhead_string" value="{{ $field('wellhead_string') }}">
                            @endif</div>
                        <div class="f"><label class="lb" for="choke_size"><i class="fe fe-sliders"></i> Choke size (1/64")</label>
                            <input class="fc" type="number" name="choke_size" id="choke_size" placeholder="e.g. 32" value="{{ old('choke_size') }}"></div>
                        <div class="f {{ $errors->has('operator') ? 'has-err' : '' }}"><label class="lb" for="operator"><i class="fe fe-user"></i> Operator <b>*</b></label>
                            @if($hasTask)
                                <input class="fc" type="text" name="operator" id="operator" value="{{ $field('operator') }}" readonly required>
                            @else
                                <select class="fc" id="operatorSel" aria-label="Operator"></select>
                                <input type="hidden" name="operator" id="operator" value="{{ $field('operator', auth()->user()->name) }}" data-vis="operatorSel" required>
                            @endif
                            @error('operator')<span class="em">{{ $message }}</span>@enderror</div>
                        <div class="f {{ $errors->has('test_date') ? 'has-err' : '' }}"><label class="lb" for="test_date"><i class="fe fe-calendar"></i> Test date <b>*</b></label>
                            <input class="fc" type="date" name="test_date" id="test_date" value="{{ $field('test_date', $hasTask ? '' : now()->format('Y-m-d')) }}" {{ $hasTask ? 'readonly' : '' }} required>
                            @error('test_date')<span class="em">{{ $message }}</span>@enderror</div>
                        <div class="f"><label class="lb" for="test_type"><i class="fe fe-activity"></i> Test type <b>*</b></label>
                            @if($hasTask)
                                <select class="fc" id="test_type_ro" disabled>
                                    @foreach(['flow' => 'Flow test', 'cleanup' => 'Clean up', 'build-up' => 'Build up'] as $k => $l)<option value="{{ $k }}" @selected($testType == $k)>{{ $l }}</option>@endforeach
                                </select>
                                <input type="hidden" name="test_type" id="test_type" value="{{ $af['test_type'] ?? 'flow' }}">
                            @else
                                <select class="fc" name="test_type" id="test_type" required>
                                    <option value="">Select test type…</option>
                                    @foreach(['flow' => 'Flow test', 'cleanup' => 'Clean up', 'build-up' => 'Build up'] as $k => $l)<option value="{{ $k }}" @selected($testType == $k)>{{ $l }}</option>@endforeach
                                </select>
                            @endif</div>
                    </div>
                </div>

                {{-- 3 file --}}
                <div class="panel rv" style="--d:2">
                    <div class="ph num"><span class="n">3</span><div><h3>VX log file</h3><small>The raw log exported from the meter</small></div></div>
                    <div class="dz {{ $errors->has('logfile') ? 'bad' : '' }}" id="dz">
                        <input type="file" name="logfile" id="logfile" accept=".vxlog,.txt" required aria-label="VX log file">
                        <svg viewBox="0 0 380 130" aria-hidden="true">
                            <defs><linearGradient id="ulSteel" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#a8b6cb"/><stop offset=".18" stop-color="#f1f5f9"/><stop offset=".5" stop-color="#7c8aa1"/><stop offset="1" stop-color="#1b2433"/></linearGradient><clipPath id="ulDoc"><rect x="26" y="14" width="64" height="86" rx="8"/></clipPath></defs>
                            <rect x="26" y="14" width="64" height="86" rx="8" fill="#0b1426" stroke="#fbbf24" stroke-width="2.4"/>
                            <path d="M38,38 H78 M38,50 H78 M38,62 H66 M38,74 H72" stroke="#fbbf24" stroke-width="3" stroke-linecap="round" opacity=".75"/>
                            <g clip-path="url(#ulDoc)"><rect class="scan" x="26" y="16" width="64" height="3" fill="#22d3ee"/></g>
                            <text x="58" y="116" text-anchor="middle" font-size="9" font-weight="800" letter-spacing="2" fill="#fcd34d">VX LOG</text>
                            <g class="ulfly" fill="none" stroke="#22d3ee" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M112,57 H168 M156,45 L168,57 L156,69"/></g>
                            <path d="M196,62 H228 L246,70 H294 L312,62 H352" fill="none" stroke="#0b1220" stroke-width="16"/><path d="M196,62 H352" fill="none" stroke="#64748b" stroke-width="11"/>
                            <path d="M226,50 H252 L266,58 H288 L302,50 H326 V82 H302 L288,74 H266 L252,82 H226 Z" fill="url(#ulSteel)" stroke="#0b1220" stroke-width="1.4"/>
                            <rect x="256" y="26" width="40" height="22" rx="5" fill="#0a1222" stroke="#22d3ee" stroke-width="1.6"/><text x="276" y="41" text-anchor="middle" font-size="9" font-weight="800" fill="#67e8f9">MPFM</text>
                            <path d="M276,48 V58" stroke="#94a3b8" stroke-width="3"/>
                            <path class="ulflow" d="M196,62 H352" stroke="#fde68a"/>
                            <text x="276" y="116" text-anchor="middle" font-size="9" font-weight="800" letter-spacing="2" fill="#67e8f9">ANALYSIS</text>
                        </svg>
                        <h4 id="dzTitle">Drop your VX log here, or click to browse</h4>
                        <p>We read the rates, pressures and temperatures from the log and build the test for you.</p>
                        <div class="ext"><span>.vxlog</span><span>.txt</span></div>
                    </div>
                    @error('logfile')<span class="em">{{ $message }}</span>@enderror
                    <div class="picked" id="picked">
                        <div class="fi"><i class="fe fe-file-text"></i></div>
                        <div><b id="fileName"></b><small id="fileSize"></small></div>
                        <button type="button" id="clearFile"><i class="fe fe-x"></i> Remove</button>
                    </div>
                </div>

                <div class="bar rv" style="--d:3">
                    <button type="button" class="btn2 p" id="submitBtn"><i class="fe fe-upload"></i> Upload &amp; analyze log file</button>
                    <span class="sp"></span><span class="hint">Fields marked * are required</span>
                </div>
                <div class="toast" id="toast" role="alert"></div>
            </div>

            <aside class="side">
                <div class="panel rv">
                    <div class="prog">
                        <svg viewBox="0 0 100 100"><circle cx="50" cy="50" r="40" fill="none" stroke="rgba(255,255,255,.08)" stroke-width="9"/><circle class="arc" id="pArc" cx="50" cy="50" r="40" pathLength="100" stroke="#22d3ee" style="stroke-dasharray:0 100"/><text id="pTxt" x="50" y="56" text-anchor="middle" font-family="Space Grotesk" font-weight="700" font-size="20" fill="#fff">0%</text></svg>
                        <div><b id="pLbl">Required fields</b><small id="pSub">Fill the form and add the log file to continue.</small></div>
                    </div>
                </div>
                <div class="panel rv" style="--d:1">
                    <div class="ph" style="margin-bottom:14px"><h3 style="font-size:15px"><i class="fe fe-git-merge"></i> What happens next</h3></div>
                    <ul class="next">
                        <li><span class="ic"><i class="fe fe-upload"></i></span><div><b>Log is read</b><small>Rates, pressure, temperature and GVF are extracted.</small></div></li>
                        <li><span class="ic"><i class="fe fe-bar-chart-2"></i></span><div><b>Preview &amp; charts</b><small>You check the numbers and curves before anything is saved.</small></div></li>
                        <li><span class="ic"><i class="fe fe-check-circle"></i></span><div><b>Save the test</b><small>It joins the MPFM tests list and goes for review.</small></div></li>
                        <li><span class="ic"><i class="fe fe-shuffle"></i></span><div><b>Compare with the vessel</b><small>Check the MPFM against the well testing reading.</small></div></li>
                    </ul>
                </div>
                <div class="panel rv" style="--d:2">
                    <div class="ph" style="margin-bottom:10px"><h3 style="font-size:15px">Before you upload</h3></div>
                    <ul class="tips">
                        <li><i class="fe fe-check-circle"></i> <span>Use the log of one complete, stabilised flow period.</span></li>
                        <li><i class="fe fe-check-circle"></i> <span>The well name must match the oil string you are testing.</span></li>
                        <li><i class="fe fe-check-circle"></i> <span>Large logs can take a few seconds to analyse — keep this tab open.</span></li>
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
    var root = $('ofp'), form = $('uploadForm'), input = $('logfile'), dz = $('dz'), picked = $('picked'), btn = $('submitBtn'), toast = $('toast');

    function size(n) { return n < 1024 ? n + ' bytes' : n < 1048576 ? (n / 1024).toFixed(2) + ' KB' : (n / 1048576).toFixed(2) + ' MB'; }
    function say(msg) { toast.textContent = msg; toast.classList.add('on'); toast.scrollIntoView({ block: 'center', behavior: 'smooth' }); }

    function progress() {
        var req = [].slice.call(form.querySelectorAll('[required]')).filter(function (e) { return e.type !== 'file'; });
        var done = req.filter(function (e) { return e.value.trim() !== ''; }).length + (input.files && input.files.length ? 1 : 0), total = req.length + 1;
        var pc = Math.round(done / total * 100);
        $('pArc').style.strokeDasharray = pc + ' 100'; $('pArc').setAttribute('stroke', pc === 100 ? '#34d399' : '#22d3ee');
        $('pTxt').textContent = pc + '%'; $('pLbl').textContent = done + ' of ' + total + ' required';
        $('pSub').textContent = pc === 100 ? 'All set — ready to upload and analyze.' : 'Fill the form and add the log file to continue.';
    }

    function show() {
        var f = input.files && input.files[0];
        dz.classList.toggle('has', !!f); picked.classList.toggle('on', !!f); dz.classList.remove('bad');
        if (f) { $('fileName').textContent = f.name; $('fileSize').textContent = size(f.size); $('dzTitle').textContent = 'Log file ready'; }
        else { $('dzTitle').textContent = 'Drop your VX log here, or click to browse'; }
        progress();
    }
    input.addEventListener('change', show);
    $('clearFile').addEventListener('click', function (e) { e.stopPropagation(); input.value = ''; show(); });
    ['dragenter', 'dragover'].forEach(function (ev) { dz.addEventListener(ev, function (e) { e.preventDefault(); dz.classList.add('over'); }); });
    ['dragleave', 'drop'].forEach(function (ev) { dz.addEventListener(ev, function (e) { e.preventDefault(); dz.classList.remove('over'); }); });
    dz.addEventListener('drop', function (e) { if (e.dataTransfer.files.length) { input.files = e.dataTransfer.files; show(); } });
    form.addEventListener('input', progress); form.addEventListener('change', progress);
    root.querySelectorAll('.rv').forEach(function () {});

    btn.addEventListener('click', function (e) {
        e.preventDefault(); toast.classList.remove('on');
        var bad = null;
        form.querySelectorAll('[required]').forEach(function (el) {
            if (el.type === 'file') return;
            var t = el.dataset.vis ? $(el.dataset.vis) : el;
            var empty = el.value.trim() === ''; t.classList.toggle('bad', empty); if (empty && !bad) bad = t;
        });
        if (bad) { bad.scrollIntoView({ block: 'center', behavior: 'smooth' }); bad.focus(); say('Please fill in the highlighted fields.'); return; }
        if (!input.files || !input.files.length) { dz.classList.add('bad'); dz.scrollIntoView({ block: 'center', behavior: 'smooth' }); say('Please select a log file to upload.'); return; }

        var original = btn.innerHTML;
        btn.disabled = true; btn.innerHTML = '<span class="spin"></span> Processing…';
        fetch(form.action, { method: 'POST', body: new FormData(form) })
            .then(function (r) {
                if (r.status === 401) throw new Error('Session expired. Please log in again.');
                if (r.status === 419) throw new Error('Your session token expired. Refresh the page and try again.');
                if (!r.ok) throw new Error('HTTP ' + r.status + ': ' + r.statusText);
                return r.text();
            })
            .then(function (html) { document.open(); document.write(html); document.close(); })
            .catch(function (err) { btn.disabled = false; btn.innerHTML = original; say('Error submitting form: ' + err.message); });
    });

    // ---- dynamic pickers (field -> well/tower -> string, operator, client) built from the database ----
    var CATALOG = @json($catalog ?? []), OPERATORS = @json($operators ?? []), CLIENTS = @json($clients ?? []);
    var hv = function (id) { return $(id); };
    function fillSel(sel, names, placeholder, want) {
        if (!sel) return;
        sel.innerHTML = '<option value="">' + placeholder + '</option>';
        names.forEach(function (n) { var o = document.createElement('option'); o.value = n; o.textContent = n; sel.appendChild(o); });
        if (want && names.indexOf(want) > -1) sel.value = want; else if (names.length === 1) sel.value = names[0];
    }
    function curField() { return CATALOG.filter(function (f) { return f.name === ($('fieldSel') || {}).value; })[0]; }
    function curTower(name) { var f = curField(); return f ? f.towers.filter(function (t) { return t.name === name; })[0] : null; }
    function sync() {
        if ($('clientSel')) { var other = $('clientSel').value === '__other'; $('clientOther').style.display = other ? '' : 'none'; hv('client').value = other ? $('clientOther').value : $('clientSel').value; }
        if ($('fieldSel')) hv('field').value = $('fieldSel').value;
        if ($('wellSel')) hv('well').value = $('wellSel').value;
        if ($('towerSel')) hv('wellhead_tower').value = $('towerSel').value;
        if ($('stringSel')) hv('wellhead_string').value = $('stringSel').value;
        if ($('operatorSel')) hv('operator').value = $('operatorSel').value;
        progress();
    }
    function loadTowers(wantWell, wantTower, wantString) {
        var f = curField(), names = f ? f.towers.map(function (t) { return t.name; }) : [];
        fillSel($('wellSel'), names, f ? 'Select well…' : 'Select a field first', wantWell);
        fillSel($('towerSel'), names, f ? 'Select tower…' : 'Select a field first', wantTower || wantWell);
        loadStrings(wantString);
    }
    function loadStrings(want) {
        var t = curTower(($('towerSel') || {}).value), names = t ? t.strings.map(function (s) { return s.name; }) : [];
        fillSel($('stringSel'), names, t ? (names.length ? 'Select string…' : 'No strings on this tower') : 'Select a tower first', want);
        sync();
    }
    if ($('fieldSel')) {
        var prev = { field: hv('field').value, well: hv('well').value, tower: hv('wellhead_tower').value, string: hv('wellhead_string').value, op: hv('operator').value, client: hv('client').value };
        fillSel($('fieldSel'), CATALOG.map(function (f) { return f.name; }), 'Select field…', prev.field);
        loadTowers(prev.well, prev.tower, prev.string);
        fillSel($('operatorSel'), OPERATORS, 'Select operator…', prev.op);
        $('clientSel').innerHTML = '<option value="">Select client…</option>' + CLIENTS.map(function (c) { return '<option>' + c.replace(/</g, '&lt;') + '</option>'; }).join('') + '<option value="__other">Other…</option>';
        if (prev.client && CLIENTS.indexOf(prev.client) === -1) { $('clientSel').value = '__other'; $('clientOther').value = prev.client; } else if (prev.client) { $('clientSel').value = prev.client; } else if (CLIENTS.length) { $('clientSel').value = CLIENTS[0]; }
        $('fieldSel').addEventListener('change', function () { loadTowers(); });
        $('wellSel').addEventListener('change', function () { var w = this.value; if (w) { $('towerSel').value = w; } loadStrings(); });
        $('towerSel').addEventListener('change', function () { loadStrings(); });
        $('stringSel').addEventListener('change', sync);
        $('operatorSel').addEventListener('change', sync);
        $('clientSel').addEventListener('change', sync);
        $('clientOther').addEventListener('input', sync);
        sync();
    }

    var io = new IntersectionObserver(function (es) { es.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } }); }, { threshold: .05 });
    root.querySelectorAll('.rv').forEach(function (el) { io.observe(el); });
    if (window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches) root.querySelectorAll('svg').forEach(function (s) { s.pauseAnimations && s.pauseAnimations(); });
    progress();
})();
</script>
@endsection
