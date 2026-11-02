@extends('layouts.master')

@section('title', 'MPFM Test Trending Dashboard')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/ofpage.css') }}">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
<style>
.ofp .verdict { display:flex; align-items:center; gap:14px; padding:12px 18px; border-radius:20px; border:1px solid var(--vc); background:rgba(0,0,0,.35); backdrop-filter:blur(6px); box-shadow:0 0 30px color-mix(in srgb, var(--vc) 28%, transparent); --vc:#34d399; }
.ofp .verdict.warn { --vc:#fbbf24; } .ofp .verdict.bad { --vc:#f87171; }
.ofp .verdict svg { width:74px; height:74px; flex:none; }
.ofp .verdict .arc { stroke-width:8; stroke-linecap:round; }
.ofp .verdict b { display:block; font:800 17px 'Space Grotesk'; color:var(--vc); } .ofp .verdict small { color:var(--sub); font-size:12px; }
.ofp .towers { display:grid; grid-template-columns:repeat(auto-fill,minmax(210px,1fr)); gap:12px; }
.ofp .tw { padding:12px 14px 14px; border-radius:16px; border:1px solid var(--line); background:var(--glass); }
.ofp .tw h4 { margin:0 0 10px; font-size:12px; font-weight:700; color:var(--sub); display:flex; align-items:center; gap:7px; }
.ofp .tw h4 i { color:var(--cyan); }
.ofp .tw .wells { display:flex; flex-wrap:wrap; gap:8px; padding-top:6px; border-top:2px solid #334155; position:relative; }
.ofp .wh { width:30px; text-align:center; cursor:pointer; }
.ofp .wh svg { width:26px; height:34px; display:block; margin:0 auto; transition:transform .3s var(--ease); }
.ofp .wh:hover svg { transform:translateY(-4px) scale(1.12); }
.ofp .wh .pl { transform-origin:13px 6px; }
.ofp .wh.bad .pl, .ofp .wh.warn .pl { animation:tdPulse 1.6s ease-in-out infinite; }
@keyframes tdPulse { 50% { opacity:.25; } }
.ofp .wh small { display:block; font-size:7.5px; color:var(--mute); margin-top:1px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.ofp .ctl { display:flex; gap:10px; flex-wrap:wrap; align-items:center; margin-top:18px; }
.ofp .ctl .tab { padding:8px 16px; }
.ofp .ctl select { height:40px; padding:0 34px 0 14px; border-radius:14px; border:1px solid var(--line); background:rgba(255,255,255,.06); color:#fff; font:600 13px 'Inter'; appearance:none; -webkit-appearance:none; outline:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' fill='none' stroke='%2322d3ee' stroke-width='2'%3E%3Cpath d='M1 1l5 5 5-5'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 12px center; }
.ofp .ctl select option { background:#0b1426; }
.ofp .ctl .sp { flex:1; }
.ofp .cards { display:grid; grid-template-columns:repeat(auto-fill,minmax(380px,1fr)); gap:16px; margin-top:16px; }
.ofp .sc { position:relative; padding:18px; border-radius:20px; border:1px solid var(--line); background:linear-gradient(180deg,rgba(255,255,255,.045),rgba(255,255,255,.015)); border-top:3px solid var(--sc,#34d399); transition:transform .35s var(--ease), border-color .3s; display:flex; flex-direction:column; gap:12px; }
.ofp .sc:hover { transform:translateY(-4px); border-color:var(--sc); }
.ofp .sc.alert { --sc:#fbbf24; } .ofp .sc.nodata { --sc:#475569; opacity:.8; } .ofp .sc.ok { --sc:#34d399; }
.ofp .sc.hide { display:none; }
.ofp .sc.nodata { padding:12px 16px; gap:6px; } .ofp .sc.nodata .mid, .ofp .sc.nodata .ft { display:none; } .ofp .sc.nodata .hd svg { width:22px; height:28px; } .ofp .sc.nodata .nm { font-size:14px; } .ofp .sc.nodata .cnt { display:none; } .ofp .sc.nodata .none { font-size:11.5px; }
.ofp .sc .hd { display:flex; align-items:center; gap:12px; }
.ofp .sc .hd svg { width:34px; height:44px; flex:none; }
.ofp .sc .nm { font-size:16px; font-weight:800; color:#fff; line-height:1.2; }
.ofp .sc .tw2 { font-size:12px; color:var(--sub); margin-top:2px; }
.ofp .sc .cnt { margin-left:auto; text-align:center; } .ofp .sc .cnt b { display:block; font:800 26px 'Space Grotesk'; color:var(--sc); line-height:1; } .ofp .sc .cnt small { font-size:10px; letter-spacing:.1em; text-transform:uppercase; color:var(--mute); font-weight:700; }
.ofp .sc .mid { display:flex; align-items:center; gap:14px; padding:10px 12px; border-radius:14px; background:var(--glass); border:1px solid var(--line); }
.ofp .sc .mid svg { width:120px; height:38px; flex:none; overflow:visible; }
.ofp .sc .mid .nums { margin-left:auto; text-align:right; } .ofp .sc .mid .nums b { font:700 16px 'Space Grotesk'; color:#fff; } .ofp .sc .mid .nums small { display:block; font-size:10.5px; color:var(--sub); }
.ofp .sc .mid .nd { color:var(--mute); font-size:12px; }
.ofp .sc .al { display:grid; gap:8px; }
.ofp .sc .al div { display:flex; gap:10px; align-items:flex-start; font-size:12.5px; color:var(--sub); line-height:1.45; }
.ofp .sc .tag { flex:none; padding:2px 9px; border-radius:50px; font-size:9.5px; font-weight:700; letter-spacing:.06em; text-transform:uppercase; border:1px solid rgba(251,191,36,.5); color:var(--amber); background:rgba(251,191,36,.1); }
.ofp .sc .tag.critical, .ofp .sc .tag.danger { border-color:rgba(248,113,113,.5); color:var(--red); background:rgba(248,113,113,.1); }
.ofp .sc .tag.info { border-color:rgba(59,130,246,.5); color:#93c5fd; background:rgba(59,130,246,.1); }
.ofp .sc .good { font-size:12.5px; color:#bbf7d0; display:flex; gap:8px; align-items:center; } .ofp .sc .good i { color:var(--green); }
.ofp .sc .none { font-size:12.5px; color:var(--mute); }
.ofp .sc .ft { display:flex; gap:8px; margin-top:auto; flex-wrap:wrap; }
@media (max-width:640px) { .ofp .cards { grid-template-columns:1fr; } }
</style>
@endsection

@section('content')
@php
    $items = $stringsWithAlerts;
    $total = $items->count();
    $withTests = $items->filter(fn ($i) => $i['tests'] > 0)->count();
    $alertStrings = $items->filter(fn ($i) => $i['alert_count'] > 0)->count();
    $healthy = $items->filter(fn ($i) => $i['alert_count'] === 0 && $i['tests'] > 0)->count();
    $nodata = $total - $withTests;
    $alertTotal = $items->sum('alert_count');
    $testsTotal = $items->sum('tests');
    $healthPct = $withTests ? round($healthy / $withTests * 100) : 0;
    $vstate = $alertStrings === 0 ? '' : ($alertStrings / max($withTests, 1) > .3 ? 'bad' : 'warn');
    $state = fn ($i) => $i['alert_count'] > 0 ? 'alert' : ($i['tests'] > 0 ? 'ok' : 'nodata');
    $byTower = $items->groupBy(fn ($i) => $i['string']->tower->name ?? 'No tower')->sortKeys();
    $col = ['alert' => '#fbbf24', 'ok' => '#34d399', 'nodata' => '#475569'];
    $well = function ($c, $big = false) {
        return '<svg viewBox="0 0 26 34" aria-hidden="true"><rect x="9" y="26" width="8" height="8" fill="'.$c.'" opacity=".5"/><rect x="6" y="22" width="14" height="5" rx="1" fill="#64748b"/><rect x="10" y="9" width="6" height="14" fill="#94a3b8"/><rect x="4" y="12" width="18" height="4" rx="1" fill="#64748b"/><circle class="pl" cx="13" cy="6" r="5" fill="#0a1222" stroke="'.$c.'" stroke-width="2"/><path d="M13 6 L15.4 3.4" stroke="'.$c.'" stroke-width="1.6" stroke-linecap="round"/></svg>';
    };
@endphp

<div class="ofp" id="ofp">
    <div class="bgl"><div class="orb o1"></div><div class="orb o2"></div></div>

    {{-- HERO --}}
    <div class="hero rv">
        <div class="who">
            <span class="lg"></span>
            <div>
                <div class="eyebrow"><i></i> Trending dashboard</div>
                <h1>MPFM test <em>trends &amp; alerts</em></h1>
                <div class="meta"><span>Watching {{ $withTests }} oil strings with test data across {{ $byTower->count() }} towers</span></div>
            </div>
        </div>
        <div style="display:flex;gap:16px;align-items:center;flex-wrap:wrap;justify-content:flex-end">
            <div class="verdict {{ $vstate }}">
                <svg viewBox="0 0 100 100"><circle cx="50" cy="50" r="40" fill="none" stroke="rgba(255,255,255,.08)" stroke-width="8"/><circle class="arc" cx="50" cy="50" r="40" pathLength="100" stroke="{{ $vstate === 'bad' ? '#f87171' : ($vstate === 'warn' ? '#fbbf24' : '#34d399') }}" style="stroke-dasharray:{{ $healthPct }} 100"/><text x="50" y="57" text-anchor="middle" font-family="Space Grotesk" font-weight="700" font-size="21" fill="#fff">{{ $withTests ? $healthPct.'%' : '—' }}</text></svg>
                <div><b>{{ $alertStrings ? $alertStrings.' need attention' : 'All healthy' }}</b><small>{{ $healthy }} of {{ $withTests }} strings without alerts</small></div>
            </div>
            <div class="actions"><a class="btn2" href="{{ route('mpfm_tests.index') }}"><i class="fe fe-arrow-left"></i> MPFM tests</a></div>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="kpis">
        @foreach([
            ['Oil strings', $total, 'in the field', 'fe-layers', '#22d3ee'],
            ['Tests analysed', $testsTotal, 'MPFM tests', 'fe-activity', '#a78bfa'],
            ['Active alerts', $alertTotal, 'trend warnings', 'fe-alert-triangle', '#f87171'],
            ['With alerts', $alertStrings, 'strings', 'fe-bell', '#fbbf24'],
            ['Healthy', $healthy, 'strings', 'fe-check-circle', '#34d399'],
        ] as $i => [$l, $v, $u, $ic, $c])
            <div class="kpi rv" style="--d:{{ $i + 1 }};--c:{{ $c }}"><div class="ic"><i class="fe {{ $ic }}"></i></div><div class="l">{{ $l }}</div><div class="v cu" data-to="{{ $v }}" data-dec="0">{{ $v }}</div><div class="u">{{ $u }}</div></div>
        @endforeach
    </div>

    {{-- HEALTH MAP --}}
    <div class="sec">
        <div class="panel rv">
            <div class="ph"><h3><i class="fe fe-map"></i> Field health map</h3>
                <span class="sub"><span style="color:#34d399">● healthy</span> &nbsp; <span style="color:#fbbf24">● alert</span> &nbsp; <span style="color:#475569">● no data</span> &nbsp;· click a well to jump to its card</span></div>
            <div class="towers">
                @foreach($byTower as $tname => $group)
                    <div class="tw">
                        <h4><i class="fe fe-layers"></i> {{ $tname }} <span style="margin-left:auto;color:var(--mute)">{{ $group->count() }}</span></h4>
                        <div class="wells">
                            @foreach($group as $it)
                                @php $s = $state($it); @endphp
                                <a class="wh {{ $s === 'alert' ? 'warn' : '' }}" href="#s{{ $it['string']->id }}" title="{{ $it['string']->name }} — {{ $s === 'alert' ? $it['alert_count'].' alert(s)' : ($s === 'ok' ? 'healthy' : 'no test data') }}">{!! $well($col[$s]) !!}<small>{{ \Illuminate\Support\Str::afterLast($it['string']->name, '/') }}</small></a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- CONTROLS --}}
    <div class="ctl rv">
        <button class="tab on" data-f="data">With data ({{ $withTests }})</button>
        <button class="tab" data-f="all">All ({{ $total }})</button>
        <button class="tab" data-f="alert">With alerts ({{ $alertStrings }})</button>
        <button class="tab" data-f="ok">Healthy ({{ $healthy }})</button>
        <button class="tab" data-f="nodata">No data ({{ $nodata }})</button>
        <span class="sp"></span>
        <span class="srch"><i class="fe fe-search"></i><input type="text" id="q" placeholder="Search string or tower…" autocomplete="off"></span>
        <select id="sort" aria-label="Sort">
            <option value="alerts_desc">Most alerts</option>
            <option value="alerts_asc">Fewest alerts</option>
            <option value="tests_desc">Most tests</option>
            <option value="name_asc">Name A–Z</option>
        </select>
    </div>

    {{-- CARDS --}}
    <div class="cards" id="cards">
        @forelse($items as $it)
            @php
                $str = $it['string']; $s = $state($it); $sp = $it['spark']; $lt = $it['latest'];
                $c = $col[$s];
                $pts = '';
                if (count($sp) > 1) {
                    $mn = min($sp); $mx = max($sp); $rg = ($mx - $mn) ?: 1; $n = count($sp);
                    foreach ($sp as $k => $v) { $pts .= round($k / ($n - 1) * 120, 1).','.round(34 - ($v - $mn) / $rg * 30, 1).' '; }
                }
            @endphp
            <div class="sc {{ $s }} rv" id="s{{ $str->id }}" data-state="{{ $s }}" data-alerts="{{ $it['alert_count'] }}" data-tests="{{ $it['tests'] }}" data-name="{{ strtolower($str->name.' '.($str->tower->name ?? '').' '.($str->tower->oilField->name ?? '')) }}">
                <div class="hd">
                    {!! $well($c) !!}
                    <div><div class="nm">{{ $str->name }}</div><div class="tw2"><i class="fe fe-layers"></i> {{ $str->tower->name ?? '—' }}@if($str->tower?->oilField) · {{ $str->tower->oilField->name }}@endif</div></div>
                    <div class="cnt"><b>{{ $it['alert_count'] }}</b><small>alerts</small></div>
                </div>
                <div class="mid">
                    @if(count($sp) > 1)
                        <svg viewBox="0 0 120 38" aria-label="Oil rate trend"><polyline points="{{ trim($pts) }}" fill="none" stroke="{{ $c }}" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>@php $lp = explode(',', trim(explode(' ', trim($pts))[count($sp) - 1])); @endphp<circle cx="{{ $lp[0] }}" cy="{{ $lp[1] }}" r="3.4" fill="#fff" stroke="{{ $c }}" stroke-width="2"/></svg>
                    @else
                        <svg viewBox="0 0 120 38" aria-hidden="true"><path d="M0,19 H120" stroke="#334155" stroke-width="2" stroke-dasharray="4 5"/></svg>
                    @endif
                    @if($lt)
                        <div class="nums"><b>{{ number_format((float) $lt->oil_rate, 0) }} <span style="font-size:11px;color:var(--sub);font-weight:500">B/D</span></b><small>{{ $it['tests'] }} test{{ $it['tests'] === 1 ? '' : 's' }} · last {{ $lt->test_date->format('d M') }} · WC {{ number_format((float) $lt->water_cut, 1) }}%</small></div>
                    @else
                        <div class="nums nd">No MPFM tests yet</div>
                    @endif
                </div>
                @if($it['alert_count'] > 0)
                    <div class="al">
                        @foreach($it['alerts'] as $a)
                            <div><span class="tag {{ $a['severity'] ?? 'warning' }}">{{ str_replace('_', ' ', $a['type'] ?? 'alert') }}</span><span>{{ $a['message'] ?? '' }}</span></div>
                        @endforeach
                    </div>
                @elseif($it['tests'] > 0)
                    <div class="good"><i class="fe fe-check-circle"></i> No concerning trends detected</div>
                @else
                    <div class="none">Upload an MPFM log for this string to start trend analysis.</div>
                @endif
                <div class="ft">
                    <a class="lnk" href="{{ route('mpfm_tests.string_analysis', $str->id) }}"><i class="fe fe-trending-up"></i> View analysis</a>
                    @if($it['alert_count'] > 0)<a class="lnk w" href="{{ route('mpfm_tests.string_analysis', $str->id) }}?filter=alerts"><i class="fe fe-bell"></i> View alerts</a>@endif
                </div>
            </div>
        @empty
            <div class="panel empty" style="grid-column:1/-1">
                <svg viewBox="0 0 120 90"><path d="M8,70 L36,46 L58,58 L84,26 L112,38" fill="none" stroke="#64748b" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="7 6"/></svg>
                No strings with test data available.
            </div>
        @endforelse
    </div>
    <div class="empty" id="noMatch" style="display:none">No strings match this filter.</div>
</div>
@endsection

@section('js')
<script>
(function () {
    var root = document.getElementById('ofp');
    var reduce = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;
    var cards = [].slice.call(document.querySelectorAll('#cards .sc')), box = document.getElementById('cards');
    var f = 'data', q = '', sort = 'alerts_desc';

    function apply() {
        var shown = 0;
        cards.forEach(function (c) {
            var ok = (f === 'all' || (f === 'data' ? c.dataset.state !== 'nodata' : c.dataset.state === f)) && (!q || c.dataset.name.indexOf(q) > -1);
            c.classList.toggle('hide', !ok); if (ok) shown++;
        });
        cards.slice().sort(function (a, b) {
            var al = +b.dataset.alerts - +a.dataset.alerts, ts = +b.dataset.tests - +a.dataset.tests;
            if (sort === 'alerts_asc') return -al || ts;
            if (sort === 'tests_desc') return ts || al;
            if (sort === 'name_asc') return a.dataset.name < b.dataset.name ? -1 : 1;
            return al || ts;
        }).forEach(function (c) { box.appendChild(c); });
        document.getElementById('noMatch').style.display = shown ? 'none' : 'block';
    }
    root.querySelectorAll('.ctl .tab').forEach(function (t) {
        t.addEventListener('click', function () { root.querySelectorAll('.ctl .tab').forEach(function (x) { x.classList.remove('on'); }); t.classList.add('on'); f = t.dataset.f; apply(); });
    });
    document.getElementById('q').addEventListener('input', function () { q = this.value.toLowerCase(); apply(); });
    document.getElementById('sort').addEventListener('change', function () { sort = this.value; apply(); });
    apply();

    // jump from the map to a card
    root.querySelectorAll('.wh').forEach(function (a) {
        a.addEventListener('click', function (e) {
            var t = document.querySelector(a.getAttribute('href')); if (!t) return; e.preventDefault();
            if (t.classList.contains('hide')) { root.querySelector('.ctl .tab[data-f="all"]').click(); document.getElementById('q').value = ''; q = ''; apply(); }
            t.scrollIntoView({ behavior: 'smooth', block: 'center' });
            t.style.boxShadow = '0 0 0 2px #22d3ee, 0 0 30px rgba(34,211,238,.4)'; setTimeout(function () { t.style.boxShadow = ''; }, 1800);
        });
    });

    function countUp(el) {
        var to = parseFloat(el.dataset.to) || 0, t0 = null;
        if (reduce) return;
        function step(t) { if (t0 === null) t0 = t; var p = Math.min((t - t0) / 1300, 1), e = 1 - Math.pow(1 - p, 4); el.textContent = Math.round(to * e).toLocaleString('en-US'); if (p < 1) requestAnimationFrame(step); }
        requestAnimationFrame(step);
    }
    var io = new IntersectionObserver(function (es) { es.forEach(function (e) { if (!e.isIntersecting) return; e.target.classList.add('in'); e.target.querySelectorAll('.cu').forEach(countUp); io.unobserve(e.target); }); }, { threshold: .08 });
    root.querySelectorAll('.rv').forEach(function (el) { io.observe(el); });
    if (reduce) root.querySelectorAll('svg').forEach(function (s) { if (s.pauseAnimations) s.pauseAnimations(); });
})();
</script>
@endsection
