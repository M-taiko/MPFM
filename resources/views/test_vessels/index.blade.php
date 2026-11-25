@extends('layouts.master')

@section('title', 'Test Vessels')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/ofpage.css') }}">
<style>
.ofp .ctl { display:flex; gap:10px; flex-wrap:wrap; align-items:center; margin-top:18px; }
.ofp .ctl .sp { flex:1; }
.ofp .ctl select { height:40px; padding:0 34px 0 14px; border-radius:14px; border:1px solid var(--line); background:rgba(255,255,255,.06); color:#fff; font:600 13px 'Inter'; appearance:none; -webkit-appearance:none; outline:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' fill='none' stroke='%2322d3ee' stroke-width='2'%3E%3Cpath d='M1 1l5 5 5-5'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 12px center; }
.ofp .ctl select option { background:#0b1426; }
.ofp .fleet { display:grid; grid-template-columns:repeat(auto-fill,minmax(310px,1fr)); gap:16px; margin-top:16px; }
.ofp .vc { position:relative; border-radius:22px; border:1px solid var(--line); background:linear-gradient(180deg,rgba(255,255,255,.05),rgba(255,255,255,.015)); border-top:3px solid var(--sc); overflow:hidden; display:flex; flex-direction:column; transition:transform .35s var(--ease), border-color .3s, box-shadow .3s; }
.ofp .vc:hover { transform:translateY(-5px); border-color:var(--sc); box-shadow:0 18px 40px rgba(0,0,0,.35); }
.ofp .vc.active { --sc:#34d399; } .ofp .vc.maintenance { --sc:#fbbf24; } .ofp .vc.inactive { --sc:#64748b; }
.ofp .vc.hide { display:none; }
.ofp .vc .art { padding:14px 14px 0; background:radial-gradient(260px 120px at 50% 80%,color-mix(in srgb, var(--sc) 14%, transparent),transparent 70%); }
.ofp .vc .art svg { width:100%; height:auto; display:block; }
.ofp .vc .art text { font-family:'Inter',sans-serif; }
.ofp .vc .bd2 { padding:14px 18px 18px; display:flex; flex-direction:column; gap:12px; flex:1; }
.ofp .vc .tt { display:flex; align-items:flex-start; gap:10px; }
.ofp .vc .nm { font-size:17px; font-weight:800; color:#fff; line-height:1.2; }
.ofp .vc .cd { font:600 11.5px 'Space Grotesk',sans-serif; color:var(--sub); margin-top:3px; letter-spacing:.05em; }
.ofp .vc .tt .chip { margin-left:auto; flex:none; }
.ofp .vc .fd { font-size:12.5px; color:var(--sub); display:flex; align-items:center; gap:7px; }
.ofp .vc .st { display:grid; grid-template-columns:repeat(3,1fr); gap:8px; }
.ofp .vc .st div { padding:9px 10px; border-radius:12px; background:var(--glass); border:1px solid var(--line); text-align:center; }
.ofp .vc .st b { display:block; font:700 16px 'Space Grotesk'; color:#fff; } .ofp .vc .st small { font-size:9.5px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:var(--mute); }
.ofp .vc .ds { font-size:12px; color:var(--mute); line-height:1.5; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; min-height:36px; }
.ofp .vc .ft { display:flex; gap:8px; flex-wrap:wrap; margin-top:auto; }
.ofp .lnk.d { color:var(--red); cursor:pointer; font-family:inherit; }
.ofp .lnk.d:hover { border-color:var(--red); }
.ofp .vflow { fill:none; stroke-width:2; stroke-linecap:round; stroke-dasharray:3 8; animation:vDash 1.1s linear infinite; }
@keyframes vDash { to { stroke-dashoffset:-22; } }
.ofp .vc:not(.active) .vflow, .ofp .vc:not(.active) .vdot, .ofp .vc:not(.active) .vwave { display:none; }
.ofp .vwave { animation:vWave 3s linear infinite; } @keyframes vWave { to { transform:translateX(-30px); } }
.ofp .vc.inactive .art svg { filter:grayscale(.85) brightness(.8); }
.ofp .vwrench { animation:vWrench 2.4s ease-in-out infinite; transform-origin:150px 40px; } @keyframes vWrench { 50% { transform:rotate(-18deg); } }
.ofp .vc:not(.maintenance) .vwrench { display:none; }
.ofp .donutw { display:flex; align-items:center; gap:18px; }
@media (max-width:640px) { .ofp .fleet { grid-template-columns:1fr; } }
</style>
@endsection

@section('content')
@php
    $total = $vessels->count();
    $cnt = $vessels->groupBy('status')->map->count();
    $tests = $vessels->sum('tests_count');
    $stCol = ['active' => '#34d399', 'maintenance' => '#fbbf24', 'inactive' => '#64748b'];
    $fieldsUsed = $vessels->pluck('oilField')->filter()->unique('id');
@endphp
<div class="ofp" id="ofp">
    <div class="bgl"><div class="orb o1"></div><div class="orb o2"></div></div>

    {{-- shared drawing defs --}}
    <svg width="0" height="0" style="position:absolute" aria-hidden="true">
        <defs>
            <linearGradient id="vShell" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#a8b6cb"/><stop offset=".16" stop-color="#f1f5f9"/><stop offset=".5" stop-color="#7c8aa1"/><stop offset="1" stop-color="#1b2433"/></linearGradient>
            <linearGradient id="vOil" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#f59e0b"/><stop offset="1" stop-color="#78350f"/></linearGradient>
            <linearGradient id="vWat" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#0ea5e9"/><stop offset="1" stop-color="#0c4a6e"/></linearGradient>
            <linearGradient id="vCyl" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#fff" stop-opacity=".22"/><stop offset=".35" stop-color="#fff" stop-opacity=".02"/><stop offset="1" stop-color="#000" stop-opacity=".45"/></linearGradient>
            <clipPath id="vClip"><path d="M52,36 H152 A16,26 0 0 1 152,88 H52 A16,26 0 0 1 52,36 Z"/></clipPath>
            <path id="vIn" d="M4,50 H44 V40"/><path id="vGas" d="M138,36 V16 H216"/><path id="vOutP" d="M140,88 V108 H216"/>
        </defs>
    </svg>

    {{-- HERO --}}
    <div class="hero rv">
        <div class="who">
            <span class="lg"></span>
            <div>
                <div class="eyebrow"><i></i> Well Testing · Vessel fleet</div>
                <h1>Reference <em>vessels</em></h1>
                <div class="meta"><span>Three-phase separators that give the reference reading the MPFM is checked against</span></div>
            </div>
        </div>
        <div class="actions">
            @can('manage', App\Models\TestVessel::class)<a class="btn2 p" href="{{ route('test_vessels.create') }}"><i class="fe fe-plus"></i> Add vessel</a>@endcan
            <a class="btn2" href="{{ route('tv_test_requests.index') }}"><i class="fe fe-clipboard"></i> Test requests</a>
            <a class="btn2" href="{{ route('tv_tests.index') }}"><i class="fe fe-list"></i> TV tests</a>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="kpis">
        @foreach([
            ['Total vessels', $total, 'in the fleet', 'fe-anchor', '#22d3ee'],
            ['Active', $cnt['active'] ?? 0, 'ready for tests', 'fe-play-circle', '#34d399'],
            ['In maintenance', $cnt['maintenance'] ?? 0, 'temporarily out', 'fe-settings', '#fbbf24'],
            ['Inactive', $cnt['inactive'] ?? 0, 'not in use', 'fe-pause-circle', '#64748b'],
            ['Tests run', $tests, 'across all vessels', 'fe-activity', '#a78bfa'],
        ] as $i => [$l, $v, $u, $ic, $c])
            <div class="kpi rv" style="--d:{{ $i + 1 }};--c:{{ $c }}"><div class="ic"><i class="fe {{ $ic }}"></i></div><div class="l">{{ $l }}</div><div class="v cu" data-to="{{ $v }}" data-dec="0">{{ $v }}</div><div class="u">{{ $u }}</div></div>
        @endforeach
    </div>

    {{-- CONTROLS --}}
    <div class="ctl rv">
        <button class="tab on" data-s="all">All ({{ $total }})</button>
        <button class="tab" data-s="active">Active ({{ $cnt['active'] ?? 0 }})</button>
        <button class="tab" data-s="maintenance">Maintenance ({{ $cnt['maintenance'] ?? 0 }})</button>
        <button class="tab" data-s="inactive">Inactive ({{ $cnt['inactive'] ?? 0 }})</button>
        <span class="sp"></span>
        @if($oilFields->count())
            <select id="fieldF" aria-label="Oil field"><option value="">All oil fields</option>@foreach($oilFields as $f)<option value="{{ $f->id }}">{{ $f->name }}</option>@endforeach</select>
        @endif
        <span class="srch"><i class="fe fe-search"></i><input type="text" id="q" placeholder="Search vessel or code…" autocomplete="off"></span>
    </div>

    {{-- FLEET --}}
    <div class="fleet" id="fleet">
        @forelse($vessels as $v)
            @php $s = in_array($v->status, ['active', 'maintenance', 'inactive']) ? $v->status : 'inactive'; @endphp
            <div class="vc {{ $s }} rv" data-s="{{ $s }}" data-f="{{ $v->oil_field_id }}" data-n="{{ strtolower($v->name.' '.$v->code) }}">
                <div class="art">
                    <svg viewBox="0 0 220 124" aria-hidden="true">
                        <g fill="none"><use href="#vIn" stroke="#0b1220" stroke-width="9"/><use href="#vIn" stroke="#64748b" stroke-width="6"/><use href="#vGas" stroke="#0b1220" stroke-width="9"/><use href="#vGas" stroke="#64748b" stroke-width="6"/><use href="#vOutP" stroke="#0b1220" stroke-width="9"/><use href="#vOutP" stroke="#64748b" stroke-width="6"/></g>
                        <rect x="68" y="92" width="22" height="22" fill="#334155"/><rect x="118" y="92" width="22" height="22" fill="#334155"/><rect x="56" y="112" width="96" height="5" rx="2" fill="#475569"/>
                        <path d="M48,32 H156 A20,30 0 0 1 156,92 H48 A20,30 0 0 1 48,32 Z" fill="url(#vShell)" stroke="#0b1220" stroke-width="1.4"/>
                        <g clip-path="url(#vClip)">
                            <rect x="30" y="32" width="150" height="62" fill="#04101f"/>
                            <rect x="30" y="32" width="150" height="62" fill="#22d3ee" opacity=".08"/>
                            <rect x="30" y="52" width="110" height="44" fill="url(#vOil)"/>
                            <rect x="30" y="{{ 78 - ($loop->index % 3) * 4 }}" width="110" height="{{ 18 + ($loop->index % 3) * 4 }}" fill="url(#vWat)"/>
                            <rect x="140" y="52" width="3" height="44" fill="#cbd5e1"/><rect x="143" y="66" width="40" height="30" fill="url(#vOil)"/>
                            <g class="vwave"><path d="M26,52 q7,-3 14,0 t14,0 t14,0 t14,0 t14,0 t14,0 t14,0 t14,0 t14,0 t14,0 t14,0 t14,0" fill="none" stroke="#fde68a" stroke-opacity=".8" stroke-width="1.2"/></g>
                            @for($i = 0; $i < 5; $i++)<circle class="vdot" cx="{{ 70 + $i * 18 }}" cy="{{ 62 + ($i % 2) * 6 }}" r="1.8" fill="rgba(255,236,179,.75)"><animate attributeName="cy" values="{{ 78 - ($i % 2) * 6 }};54" dur="{{ 2.2 + $i * .3 }}s" begin="-{{ $i * .5 }}s" repeatCount="indefinite"/><animate attributeName="opacity" values="0;1;0" dur="{{ 2.2 + $i * .3 }}s" begin="-{{ $i * .5 }}s" repeatCount="indefinite"/></circle>@endfor
                            <rect x="30" y="32" width="150" height="62" fill="url(#vCyl)"/>
                        </g>
                        <path d="M124,30 H132 V34 H124 Z" fill="#94a3b8"/>
                        <g class="vwrench" fill="none" stroke="#fbbf24" stroke-width="3" stroke-linecap="round"><path d="M140,48 l16,-16"/><circle cx="160" cy="28" r="7"/></g>
                        @foreach([0, .9, 1.8, 2.7] as $b)
                            <circle class="vdot" r="2.6" fill="#fde68a"><animateMotion dur="2.6s" begin="{{ $b }}s" repeatCount="indefinite"><mpath href="#vIn"/></animateMotion></circle>
                            <circle class="vdot" r="2.3" fill="#a5f3fc"><animateMotion dur="2.6s" begin="{{ $b }}s" repeatCount="indefinite"><mpath href="#vGas"/></animateMotion></circle>
                            <circle class="vdot" r="2.6" fill="#f59e0b"><animateMotion dur="2.8s" begin="{{ $b }}s" repeatCount="indefinite"><mpath href="#vOutP"/></animateMotion></circle>
                        @endforeach
                        <text x="102" y="68" text-anchor="middle" font-size="9" font-weight="800" letter-spacing="2" fill="#fff" opacity=".8">{{ \Illuminate\Support\Str::limit(strtoupper($v->code), 10, '') }}</text>
                    </svg>
                </div>
                <div class="bd2">
                    <div class="tt"><div><div class="nm">{{ $v->name }}</div><div class="cd">{{ $v->code }}</div></div><span class="chip {{ $s === 'active' ? 'ok' : ($s === 'maintenance' ? 'warn' : '') }}">{{ ucfirst($s) }}</span></div>
                    <div class="fd"><i class="fe fe-map-pin"></i> {{ $v->oilField->name ?? 'No oil field' }}</div>
                    <div class="st">
                        <div><b>{{ $v->tests_count }}</b><small>Tests</small></div>
                        <div><b>{{ $v->avg_oil ? number_format($v->avg_oil, 0) : '—' }}</b><small>Avg oil</small></div>
                        <div><b style="font-size:13px">{{ $v->last_test_at ? \Illuminate\Support\Carbon::parse($v->last_test_at)->format('d M') : '—' }}</b><small>Last test</small></div>
                    </div>
                    <div class="ds">{{ $v->description ?: 'No description.' }}</div>
                    <div class="ft">
                        <a class="lnk" href="{{ route('test_vessels.show', $v->id) }}"><i class="fe fe-eye"></i> View</a>
                        @can('update', $v)<a class="lnk" href="{{ route('test_vessels.edit', $v->id) }}"><i class="fe fe-edit"></i> Edit</a>@endcan
                        @can('delete', $v)
                            <form method="POST" action="{{ route('test_vessels.destroy', $v->id) }}" onsubmit="return confirm('Delete {{ addslashes($v->name) }}?')">@csrf @method('DELETE')<button class="lnk d" type="submit"><i class="fe fe-trash-2"></i> Delete</button></form>
                        @endcan
                    </div>
                </div>
            </div>
        @empty
            <div class="panel empty" style="grid-column:1/-1">
                <svg viewBox="0 0 120 90"><path d="M20,30 H92 A14,18 0 0 1 92,66 H20 A14,18 0 0 1 20,30 Z" fill="none" stroke="#64748b" stroke-width="3" stroke-dasharray="6 5"/></svg>
                No test vessels yet.
                @can('manage', App\Models\TestVessel::class)<br><a class="btn2 p" style="margin-top:14px" href="{{ route('test_vessels.create') }}"><i class="fe fe-plus"></i> Add the first vessel</a>@endcan
            </div>
        @endforelse
    </div>
    <div class="empty" id="noMatch" style="display:none">No vessels match this filter.</div>
</div>
@endsection

@section('js')
<script>
(function () {
    var root = document.getElementById('ofp');
    var reduce = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;
    var cards = [].slice.call(document.querySelectorAll('#fleet .vc')), s = 'all', f = '', q = '';
    function apply() {
        var n = 0;
        cards.forEach(function (c) {
            var ok = (s === 'all' || c.dataset.s === s) && (!f || c.dataset.f === f) && (!q || c.dataset.n.indexOf(q) > -1);
            c.classList.toggle('hide', !ok); if (ok) n++;
        });
        document.getElementById('noMatch').style.display = n || !cards.length ? 'none' : 'block';
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
