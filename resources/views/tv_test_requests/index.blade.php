@extends('layouts.master')

@section('title', 'Test Requests')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/ofpage.css') }}">
<style>
.ofp .ctl { display:flex; gap:10px; flex-wrap:wrap; align-items:center; margin-top:18px; }
.ofp .ctl .sp { flex:1; }
.ofp .ctl select { height:40px; padding:0 34px 0 14px; border-radius:14px; border:1px solid var(--line); background:rgba(255,255,255,.06); color:#fff; font:600 13px 'Inter'; appearance:none; -webkit-appearance:none; outline:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' fill='none' stroke='%2322d3ee' stroke-width='2'%3E%3Cpath d='M1 1l5 5 5-5'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 12px center; }
.ofp .ctl select option { background:#0b1426; }
.ofp .board { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:16px; margin-top:16px; align-items:start; }
.ofp .col { border-radius:22px; border:1px solid var(--line); background:rgba(255,255,255,.025); padding:14px; min-height:200px; }
.ofp .col > h4 { margin:0 0 14px; display:flex; align-items:center; gap:10px; font-size:13px; font-weight:800; letter-spacing:.04em; color:#fff; }
.ofp .col > h4 i { width:30px; height:30px; border-radius:10px; display:grid; place-items:center; background:color-mix(in srgb, var(--cc) 18%, transparent); color:var(--cc); font-size:14px; }
.ofp .col > h4 b { margin-left:auto; font:700 13px 'Space Grotesk'; padding:2px 11px; border-radius:50px; border:1px solid var(--line); color:var(--sub); }
.ofp .col.open { --cc:#34d399; } .ofp .col.in_progress { --cc:#fbbf24; } .ofp .col.completed { --cc:#60a5fa; } .ofp .col.cancelled { --cc:#94a3b8; }
.ofp .col .stack { display:grid; gap:12px; }
.ofp .rq { display:block; position:relative; padding:14px 16px 14px; border-radius:18px; border:1px solid var(--line); background:linear-gradient(180deg,rgba(255,255,255,.06),rgba(255,255,255,.02)); border-left:3px solid var(--cc); transition:transform .3s var(--ease), border-color .3s, box-shadow .3s; color:inherit; }
.ofp .rq:hover { transform:translateY(-4px); border-color:var(--cc); box-shadow:0 14px 30px rgba(0,0,0,.35); color:inherit; }
.ofp .rq.hide { display:none; }
.ofp .rq .t { display:flex; align-items:center; gap:10px; }
.ofp .rq .dt { font:700 13px 'Space Grotesk'; color:#fff; } .ofp .rq .dt small { display:block; font:500 10.5px 'Inter'; color:var(--mute); }
.ofp .rq .no { margin-left:auto; font:700 11px 'Space Grotesk'; color:var(--mute); }
.ofp .rq .vn { margin:11px 0 3px; font-size:15px; font-weight:800; color:#fff; display:flex; gap:8px; align-items:center; } .ofp .rq .vn i { color:var(--cyan); }
.ofp .rq .lc { font-size:12px; color:var(--sub); line-height:1.5; }
.ofp .rq .wells { display:flex; gap:5px; margin:11px 0 4px; flex-wrap:wrap; }
.ofp .rq .wells span { width:22px; height:22px; border-radius:7px; display:grid; place-items:center; font-size:11px; border:1px solid var(--line); background:rgba(255,255,255,.05); color:var(--mute); }
.ofp .rq .wells span.done { background:rgba(52,211,153,.16); border-color:rgba(52,211,153,.5); color:var(--green); }
.ofp .rq .wells span.run { background:rgba(251,191,36,.16); border-color:rgba(251,191,36,.5); color:var(--amber); animation:rqPulse 1.8s ease-in-out infinite; }
@keyframes rqPulse { 50% { opacity:.4; } }
.ofp .rq .pg { height:6px; border-radius:6px; background:rgba(255,255,255,.08); overflow:hidden; margin-top:8px; }
.ofp .rq .pg i { display:block; height:100%; width:0; border-radius:6px; background:var(--cc); transition:width 1.1s var(--ease); }
.ofp .in .rq .pg i { width:var(--w); }
.ofp .rq .pf { display:flex; justify-content:space-between; font-size:11px; color:var(--mute); margin-top:5px; }
.ofp .rq .by { display:flex; align-items:center; gap:8px; margin-top:10px; padding-top:10px; border-top:1px solid rgba(255,255,255,.07); font-size:11.5px; color:var(--sub); }
.ofp .rq .by .ini { width:22px; height:22px; border-radius:50%; background:var(--grad); color:#04111f; font:700 9.5px 'Space Grotesk'; display:grid; place-items:center; }
.ofp .none { text-align:center; padding:26px 8px; border:2px dashed var(--line); border-radius:16px; color:var(--mute); font-size:12.5px; }
.ofp .flow { display:grid; grid-template-columns:repeat(4,1fr); gap:0; }
.ofp .fs { text-align:center; position:relative; padding:0 6px; }
.ofp .fs svg { width:84px; height:84px; animation:rqBob 3.4s ease-in-out infinite; }
.ofp .fs:nth-child(2) svg { animation-delay:-.8s; } .ofp .fs:nth-child(3) svg { animation-delay:-1.6s; } .ofp .fs:nth-child(4) svg { animation-delay:-2.4s; }
@keyframes rqBob { 50% { transform:translateY(-5px); } }
.ofp .fs b { display:block; font-size:13px; margin-top:4px; } .ofp .fs small { display:block; font-size:11.5px; color:var(--mute); margin-top:3px; line-height:1.4; }
.ofp .fs:not(:last-child)::after { content:""; position:absolute; top:40px; left:calc(50% + 50px); width:calc(100% - 100px); border-top:2px dashed rgba(34,211,238,.5); }
@media (max-width:1300px) { .ofp .board { grid-template-columns:repeat(2,minmax(0,1fr)); } }
@media (max-width:760px) { .ofp .board { grid-template-columns:1fr; } .ofp .flow { grid-template-columns:1fr 1fr; row-gap:16px; } .ofp .fs::after { display:none; } }
</style>
@endsection

@section('content')
@php
    $cols = [
        'open' => ['Open', 'fe-inbox'],
        'in_progress' => ['In progress', 'fe-activity'],
        'completed' => ['Completed', 'fe-check-circle'],
        'cancelled' => ['Cancelled', 'fe-x-circle'],
    ];
    $by = $requests->groupBy('status');
    $total = $requests->count();
    $wellsAll = $requests->sum(fn ($r) => $r->wells->count());
    $wellsPending = $requests->sum(fn ($r) => $r->wells->whereIn('status', ['pending', 'in_progress'])->count());
@endphp
<div class="ofp" id="ofp">
    <div class="bgl"><div class="orb o1"></div><div class="orb o2"></div></div>

    <div class="hero rv">
        <div class="who">
            <span class="lg"></span>
            <div>
                <div class="eyebrow"><i></i> Well Testing · Test requests</div>
                <h1>Request <em>board</em></h1>
                <div class="meta"><span>Schedule wells on a vessel and follow each request until every well is tested</span></div>
            </div>
        </div>
        <div class="actions">
            @can('create', App\Models\TVTestRequest::class)<a class="btn2 p" href="{{ route('tv_test_requests.create') }}"><i class="fe fe-plus"></i> New request</a>@endcan
            <a class="btn2" href="{{ route('test_vessels.index') }}"><i class="fe fe-anchor"></i> Vessels</a>
            <a class="btn2" href="{{ route('tv_tests.index') }}"><i class="fe fe-list"></i> TV tests</a>
        </div>
    </div>

    <div class="kpis">
        @foreach([
            ['Total requests', $total, 'all time', 'fe-inbox', '#22d3ee'],
            ['Open', $by->get('open', collect())->count(), 'waiting to start', 'fe-play-circle', '#34d399'],
            ['In progress', $by->get('in_progress', collect())->count(), 'being tested', 'fe-activity', '#fbbf24'],
            ['Completed', $by->get('completed', collect())->count(), 'all wells done', 'fe-check-circle', '#60a5fa'],
            ['Wells to test', $wellsPending, 'of '.$wellsAll.' scheduled', 'fe-target', '#a78bfa'],
        ] as $i => [$l, $v, $u, $ic, $c])
            <div class="kpi rv" style="--d:{{ $i + 1 }};--c:{{ $c }}"><div class="ic"><i class="fe {{ $ic }}"></i></div><div class="l">{{ $l }}</div><div class="v cu" data-to="{{ $v }}" data-dec="0">{{ $v }}</div><div class="u">{{ $u }}</div></div>
        @endforeach
    </div>

    <div class="sec"><div class="panel rv">
        <div class="ph"><h3><i class="fe fe-git-merge"></i> How a request moves</h3><span class="sub">Open → in progress → completed</span></div>
        <div class="flow">
            <div class="fs"><svg viewBox="0 0 96 96"><rect x="24" y="14" width="48" height="66" rx="7" fill="#0b1426" stroke="#60a5fa" stroke-width="2.5"/><path d="M34,34 H62 M34,46 H62 M34,58 H50" stroke="#60a5fa" stroke-width="3" stroke-linecap="round"/><circle cx="66" cy="68" r="12" fill="#3b82f6"/><path d="M66,62 V74 M60,68 H72" stroke="#fff" stroke-width="2.6" stroke-linecap="round"/></svg><b>Request created</b><small>Vessel, tower, date and wells</small></div>
            <div class="fs"><svg viewBox="0 0 96 96"><path d="M16,34 H72 A12,14 0 0 1 72,62 H16 A12,14 0 0 1 16,34 Z" fill="#1e293b" stroke="#94a3b8" stroke-width="2.5"/><path d="M20,52 H70 A8,8 0 0 1 70,62 H20 Z" fill="#b45309"/><path d="M20,58 H70 V62 H20 Z" fill="#0ea5e9"/><path d="M10,48 H4 M84,38 H92 M84,60 H92" stroke="#94a3b8" stroke-width="4"/><circle cx="48" cy="22" r="9" fill="#0a1222" stroke="#fbbf24" stroke-width="2"/></svg><b>Operator starts a well</b><small>Test opens with the well pre-filled</small></div>
            <div class="fs"><svg viewBox="0 0 96 96"><path d="M14,48 L82,18 L62,80 L46,56 Z" fill="#0b1426" stroke="#22d3ee" stroke-width="2.5" stroke-linejoin="round"/><path d="M46,56 L82,18" stroke="#22d3ee" stroke-width="2.5"/></svg><b>Readings submitted</b><small>Each well is sent for review</small></div>
            <div class="fs"><svg viewBox="0 0 96 96"><circle cx="48" cy="48" r="32" fill="#0b1426" stroke="#34d399" stroke-width="2.5"/><path d="M32,48 L44,60 L66,36" fill="none" stroke="#34d399" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/></svg><b>Request completed</b><small>When every well has a test</small></div>
        </div>
    </div></div>

    <div class="ctl rv">
        <button class="tab on" data-s="all">All ({{ $total }})</button>
        @foreach($cols as $k => [$l, $ic])<button class="tab" data-s="{{ $k }}">{{ $l }} ({{ $by->get($k, collect())->count() }})</button>@endforeach
        <span class="sp"></span>
        @if($oilFields->count())<select id="fieldF" aria-label="Oil field"><option value="">All oil fields</option>@foreach($oilFields as $f)<option value="{{ $f->id }}">{{ $f->name }}</option>@endforeach</select>@endif
        <span class="srch"><i class="fe fe-search"></i><input type="text" id="q" placeholder="Search vessel, tower, user…" autocomplete="off"></span>
    </div>

    <div class="board rv" id="board">
        @foreach($cols as $k => [$l, $ic])
            @php $list = $by->get($k, collect()); @endphp
            <div class="col {{ $k }}" data-col="{{ $k }}">
                <h4><i class="fe {{ $ic }}"></i> {{ $l }} <b>{{ $list->count() }}</b></h4>
                <div class="stack">
                    @forelse($list as $r)
                        @php
                            $w = $r->wells; $n = $w->count();
                            $done = $w->filter(fn ($x) => ! in_array($x->status, ['pending', 'in_progress']))->count();
                            $pct = $n ? round($done / $n * 100) : 0;
                            $who = $r->requestedBy->name ?? '—';
                            $ini = collect(preg_split('/\s+/', $who))->filter()->take(2)->map(fn ($x) => mb_strtoupper(mb_substr($x, 0, 1)))->implode('');
                        @endphp
                        <a class="rq" href="{{ route('tv_test_requests.show', $r->id) }}" data-f="{{ $r->oil_field_id }}" data-n="{{ strtolower(($r->testVessel->name ?? '').' '.($r->tower->name ?? '').' '.$who.' '.($r->oilField->name ?? '')) }}">
                            <div class="t"><div class="dt">{{ $r->test_date->format('d M Y') }}<small>{{ ucfirst($r->shift ?? '') }} shift</small></div><span class="no">#{{ $r->id }}</span></div>
                            <div class="vn"><i class="fe fe-anchor"></i> {{ $r->testVessel->name ?? '—' }}</div>
                            <div class="lc">{{ $r->oilField->name ?? '—' }} · {{ $r->tower->name ?? '—' }}</div>
                            <div class="wells" title="{{ $done }} of {{ $n }} wells tested">
                                @foreach($w as $x)
                                    <span class="{{ in_array($x->status, ['pending']) ? '' : ($x->status === 'in_progress' ? 'run' : 'done') }}"><i class="fe {{ in_array($x->status, ['pending']) ? 'fe-circle' : ($x->status === 'in_progress' ? 'fe-activity' : 'fe-check') }}"></i></span>
                                @endforeach
                            </div>
                            <div class="pg"><i style="--w:{{ $pct }}%"></i></div>
                            <div class="pf"><span>{{ $done }} / {{ $n }} wells</span><span>{{ $pct }}%</span></div>
                            <div class="by"><span class="ini">{{ $ini ?: '?' }}</span> {{ $who }}</div>
                        </a>
                    @empty
                        <div class="none">No {{ strtolower($l) }} requests</div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

@section('js')
<script>
(function () {
    var root = document.getElementById('ofp');
    var reduce = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;
    var cards = [].slice.call(document.querySelectorAll('#board .rq')), cols = [].slice.call(document.querySelectorAll('#board .col')), s = 'all', f = '', q = '';
    function apply() {
        cards.forEach(function (c) { c.classList.toggle('hide', !((!f || c.dataset.f === f) && (!q || c.dataset.n.indexOf(q) > -1))); });
        cols.forEach(function (c) { c.style.display = (s === 'all' || c.dataset.col === s) ? '' : 'none'; });
        document.getElementById('board').style.gridTemplateColumns = s === 'all' ? '' : 'repeat(1,minmax(0,1fr))';
    }
    root.querySelectorAll('.ctl .tab').forEach(function (t) { t.addEventListener('click', function () { root.querySelectorAll('.ctl .tab').forEach(function (x) { x.classList.remove('on'); }); t.classList.add('on'); s = t.dataset.s; apply(); }); });
    var ff = document.getElementById('fieldF'); if (ff) ff.addEventListener('change', function () { f = this.value; apply(); });
    document.getElementById('q').addEventListener('input', function () { q = this.value.toLowerCase(); apply(); });
    function countUp(el) {
        var to = parseFloat(el.dataset.to) || 0, t0 = null; if (reduce) return;
        function step(t) { if (t0 === null) t0 = t; var p = Math.min((t - t0) / 1200, 1), e = 1 - Math.pow(1 - p, 4); el.textContent = Math.round(to * e).toLocaleString('en-US'); if (p < 1) requestAnimationFrame(step); }
        requestAnimationFrame(step);
    }
    var io = new IntersectionObserver(function (es) { es.forEach(function (e) { if (!e.isIntersecting) return; e.target.classList.add('in'); e.target.querySelectorAll('.cu').forEach(countUp); io.unobserve(e.target); }); }, { threshold: .08 });
    root.querySelectorAll('.rv').forEach(function (el) { io.observe(el); });
    if (reduce) root.querySelectorAll('svg').forEach(function (v) { if (v.pauseAnimations) v.pauseAnimations(); });
})();
</script>
@endsection
