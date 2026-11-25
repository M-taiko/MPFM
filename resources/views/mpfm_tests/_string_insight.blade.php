{{-- Integrated well analysis: status, production, reservoir, recommendations, charts, timeline. Needs $analysis, $wireline, $oilString. --}}
@include('wireline._style')
@php
    $st = $analysis['status'];
    $pr = $analysis['production'];
    $rs = $analysis['reservoir'];
    $mt = $analysis['meters'];
    $bk = $analysis['breakdown'];
    $nf = fn ($v, $d = 0) => is_numeric($v) ? number_format((float) $v, $d) : '—';
    $sg = fn ($v, $d = 1) => is_numeric($v) ? sprintf('%+.' . $d . 'f', $v) : '—';
    $col = ['ok' => '#34d399', 'warn' => '#fbbf24', 'bad' => '#f87171'][$st['class']];
    $circ = 2 * M_PI * 52;
    $sevChip = ['high' => 'bad', 'medium' => 'warn', 'low' => 'info', 'info' => 'pending'];
    $sevIcon = ['high' => 'fe-alert-octagon', 'medium' => 'fe-alert-triangle', 'low' => 'fe-info', 'info' => 'fe-check-circle'];
    $trendWl = $wireline['trend'];
    $dec = $pr['decline'] ?? null;
@endphp
<style>
.ofp .ins-top { display:grid; grid-template-columns:260px 1fr; gap:18px; align-items:stretch; }
.ofp .ring { position:relative; width:150px; height:150px; margin:0 auto; }
.ofp .ring svg { width:150px; height:150px; transform:rotate(-90deg); }
.ofp .ring b { position:absolute; inset:0; display:grid; place-items:center; font:800 34px 'Space Grotesk',sans-serif; color:#fff; }
.ofp .ring b small { display:block; font:600 10px 'Inter'; letter-spacing:.12em; color:var(--mute); text-transform:uppercase; text-align:center; margin-top:-6px; }
.ofp .ins-sub { font:700 11px 'Inter'; letter-spacing:.14em; text-transform:uppercase; color:var(--cyan); margin:20px 0 10px; display:flex; align-items:center; gap:8px; }
.ofp .ins-sub::after { content:""; flex:1; height:1px; background:var(--line); }
.ofp .fac { display:flex; gap:8px; font-size:12px; color:var(--sub); line-height:1.5; margin-top:5px; }
.ofp .fac b { font:700 12px 'Space Grotesk'; min-width:30px; color:var(--red); }
.ofp .rec { display:flex; gap:14px; padding:14px 16px; border-radius:16px; border:1px solid var(--line); background:var(--glass); border-left:4px solid var(--rc); }
.ofp .rec + .rec { margin-top:10px; }
.ofp .rec > i { font-size:20px; color:var(--rc); margin-top:2px; }
.ofp .rec h4 { margin:0 0 3px; font-size:14.5px; font-weight:700; color:#fff; display:flex; gap:8px; align-items:center; flex-wrap:wrap; }
.ofp .rec p { margin:0; font-size:12.8px; line-height:1.6; color:var(--sub); }
.ofp .hb { display:grid; gap:9px; }
.ofp .hb div { display:grid; grid-template-columns:130px 1fr 34px; gap:10px; align-items:center; font-size:12px; color:var(--sub); }
.ofp .hb i { display:block; height:8px; border-radius:6px; background:var(--bc,#22d3ee); width:var(--w); }
.ofp .hb span.tr { background:rgba(255,255,255,.06); border-radius:6px; }
.ofp .tl { list-style:none; margin:0; padding:0 0 0 18px; border-left:2px solid var(--line); display:grid; gap:12px; max-height:420px; overflow:auto; }
.ofp .tl li { position:relative; font-size:12.8px; color:var(--sub); }
.ofp .tl li::before { content:""; position:absolute; left:-25px; top:4px; width:12px; height:12px; border-radius:50%; background:var(--tc); box-shadow:0 0 10px var(--tc); }
.ofp .tl b { color:#fff; font-weight:600; } .ofp .tl small { color:var(--mute); }
.ofp .tl a { color:var(--cyan); }
.ofp .disc { margin-top:14px; font-size:11.5px; color:var(--mute); line-height:1.5; }
@media (max-width:900px) { .ofp .ins-top { grid-template-columns:1fr; } .ofp .hb div { grid-template-columns:100px 1fr 30px; } }
</style>

<div class="sec" id="wellInsight">
    {{-- STATUS + HEALTH --}}
    <div class="panel">
        <div class="ph"><h3><i class="fe fe-activity"></i> Well analysis</h3>
            <span class="sub">{{ $pr['n'] ?? 0 }} test date(s) · {{ $wireline['stats']['surveys'] }} pressure survey(s) · {{ $wireline['stats']['total'] }} Wire Line operation(s)</span></div>
        <div class="ins-top">
            <div style="text-align:center">
                <div class="ring">
                    <svg viewBox="0 0 120 120"><circle cx="60" cy="60" r="52" fill="none" stroke="rgba(255,255,255,.08)" stroke-width="10"/>
                        <circle cx="60" cy="60" r="52" fill="none" stroke="{{ $col }}" stroke-width="10" stroke-linecap="round" stroke-dasharray="{{ $circ }}" stroke-dashoffset="{{ $circ * (1 - $st['score'] / 100) }}"/></svg>
                    <b><span>{{ $st['score'] }}<small>health</small></span></b>
                </div>
                <div style="margin-top:10px"><span class="chip {{ $st['class'] }}">{{ $st['label'] }}</span></div>
            </div>
            <div>
                <div class="kv">
                    <div><small>Well status</small><b>{{ ucfirst($st['operational']) }}</b></div>
                    <div><small>Production state</small><b style="color:{{ ['ok'=>'#34d399','warn'=>'#fbbf24','bad'=>'#f87171'][$st['production_class']] }}">{{ $st['production_state'] }}</b></div>
                    <div><small>Last test</small><b>{{ $st['last_test_date']?->format('d M Y') ?? '—' }}@if($st['days_since_test'] !== null) <span style="color:var(--mute);font-weight:500">({{ $st['days_since_test'] }} d ago)</span>@endif</b></div>
                    <div><small>Reservoir pressure</small><b>{{ $rs['has_data'] ? $nf($rs['current_p']) . ' psi' : 'No survey' }}</b></div>
                    <div><small>Data basis</small><b>{{ (($pr['source'] ?? 'mpfm') === 'mpfm' ? 'MPFM' : 'Test vessel') . ' · ' . (($pr['basis'] ?? 'all') === 'approved' ? 'approved tests' : 'all non-rejected') }}</b></div>
                    <div><small>Meter agreement</small><b>{{ $mt ? $mt['mean_abs_pct'] . '% (' . $mt['pairs'] . ' pairs)' : 'No paired tests' }}</b></div>
                </div>
                @if($st['factors'])
                    <div class="ins-sub">What affects the score</div>
                    @foreach($st['factors'] as $fc)<div class="fac"><b>{{ $fc['delta'] }}</b><span>{{ $fc['text'] }}</span></div>@endforeach
                @else
                    <div class="msg ok" style="margin-top:14px"><i class="fe fe-check-circle"></i><span>No adverse indicators were found for this well.</span></div>
                @endif
            </div>
        </div>

        {{-- PRODUCTION KPIs --}}
        <div class="ins-sub"><i class="fe fe-droplet"></i> Production performance</div>
        @if(!$pr['has_data'])
            <div class="empty">No usable test data yet.</div>
        @else
        <div class="kpis" style="margin-top:0">
            <div class="kpi" style="--c:#22d3ee"><div class="l">Latest oil rate</div><div class="v">{{ $nf($pr['latest']['oil'], 1) }}</div><div class="u">B/D · avg {{ $nf($pr['avg_oil'], 1) }}</div></div>
            <div class="kpi" style="--c:#a78bfa"><div class="l">Peak oil</div><div class="v">{{ $nf($pr['peak_oil'], 1) }}</div><div class="u">B/D · {{ $pr['peak_date']?->format('d M Y') }}</div></div>
            <div class="kpi" style="--c:#34d399"><div class="l">Est. cumulative oil</div><div class="v">{{ $nf($pr['cum_oil_bbl']) }}</div><div class="u">bbl across {{ $pr['span_days'] }} days of tests</div></div>
            <div class="kpi" style="--c:{{ ($dec['pct_per_year'] ?? 0) > 20 ? '#f87171' : '#fbbf24' }}"><div class="l">Oil decline</div><div class="v">{{ $dec ? $dec['pct_per_year'] . '%' : '—' }}</div><div class="u">{{ $dec ? 'per year · R² ' . $dec['r2'] : 'needs 3+ tests over 2+ weeks' }}</div></div>
            <div class="kpi" style="--c:#38bdf8"><div class="l">Water cut</div><div class="v">{{ $nf($pr['wc']['latest'] ?? null, 1) }}<span style="font-size:14px">%</span></div><div class="u">{{ ($pr['wc']['slope_per_month'] ?? null) !== null ? $sg($pr['wc']['slope_per_month']) . ' pts / month' : 'trend n/a' }}</div></div>
            <div class="kpi" style="--c:#fbbf24"><div class="l">Gas rate</div><div class="v">{{ $nf($pr['latest']['gas'], 2) }}</div><div class="u">MMscf/D · GOR {{ $nf($pr['gor']['latest'] ?? null, 1) }}</div></div>
            <div class="kpi" style="--c:#f87171"><div class="l">Wellhead pressure</div><div class="v">{{ $nf($pr['whfp']['latest'] ?? null) }}</div><div class="u">psi · {{ ($pr['whfp']['change_pct'] ?? null) !== null ? $sg($pr['whfp']['change_pct']) . '% since first' : 'single reading' }}</div></div>
            <div class="kpi" style="--c:#fb923c"><div class="l">Temperature</div><div class="v">{{ $nf($pr['temp']['latest'] ?? null, 1) }}</div><div class="u">°C at wellhead</div></div>
        </div>
        @if($dec)
            <div class="msg" style="margin-top:14px"><i class="fe fe-trending-down"></i><span>Exponential-decline forecast: <b>{{ $nf($dec['fc']['d90'], 1) }} B/D</b> in 90 days and <b>{{ $nf($dec['fc']['d180'], 1) }} B/D</b> in 180 days if current behaviour continues (fit on the last {{ $dec['n'] }} tests).</span></div>
        @endif
        @endif

        {{-- RESERVOIR KPIs --}}
        <div class="ins-sub"><i class="fe fe-layers"></i> Reservoir condition (memory-gauge surveys)</div>
        @if(!$rs['has_data'])
            <div class="msg warn"><i class="fe fe-alert-triangle"></i><span>No analysed pressure survey for this well. <a href="{{ route('wireline.create', ['well' => $oilString->id]) }}" style="color:var(--cyan)">Record a memory-gauge survey</a> to unlock reservoir pressure, depletion and fluid-level analysis.</span></div>
        @else
        <div class="kpis" style="margin-top:0">
            <div class="kpi" style="--c:#22d3ee"><div class="l">Current BHP</div><div class="v">{{ $nf($rs['current_p']) }}</div><div class="u">psi · {{ $rs['current_date']->format('d M Y') }} · {{ strtoupper($rs['survey_type']) }}</div></div>
            <div class="kpi" style="--c:#a78bfa"><div class="l">Initial BHP</div><div class="v">{{ $nf($rs['initial_p']) }}</div><div class="u">psi · {{ $rs['initial_date']->format('d M Y') }}</div></div>
            <div class="kpi" style="--c:{{ ($rs['depletion_pct'] ?? 0) > 10 ? '#f87171' : '#34d399' }}"><div class="l">Depletion</div><div class="v">{{ $rs['n_surveys'] > 1 ? $nf($rs['depletion_psi']) : '—' }}</div><div class="u">psi{{ $rs['n_surveys'] > 1 && $rs['depletion_pct'] !== null ? ' · ' . $rs['depletion_pct'] . '%' : ' · needs 2+ surveys' }}</div></div>
            <div class="kpi" style="--c:#fbbf24"><div class="l">Pressure rate</div><div class="v">{{ $rs['psi_per_month'] !== null ? $sg($rs['psi_per_month']) : '—' }}</div><div class="u">psi / month{{ $rs['proj_6m'] ? ' · ≈ ' . $nf($rs['proj_6m']) . ' psi in 6 mo' : '' }}</div></div>
            <div class="kpi" style="--c:#fb923c"><div class="l">Bottom-hole temp</div><div class="v">{{ $nf($rs['bht'], 1) }}</div><div class="u">°C</div></div>
            <div class="kpi" style="--c:{{ $rs['stabilised'] ? '#34d399' : '#fbbf24' }}"><div class="l">Last survey quality</div><div class="v" style="font-size:18px">{{ $rs['stabilised'] ? 'Stabilised' : 'Still trending' }}</div><div class="u">{{ $rs['days_since'] }} days ago · <a href="{{ route('wireline.show', [$rs['operation_id'], 'run' => $rs['run_id']]) }}" style="color:var(--cyan)">open</a></div></div>
            @isset($rs['pi'])
            <div class="kpi" style="--c:#38bdf8"><div class="l">Productivity index</div><div class="v">{{ $rs['pi']['value'] }}</div><div class="u">B/D/psi · drawdown {{ $nf($rs['pi']['drawdown']) }} psi</div></div>
            @endisset
            @if($rs['water_level_m'] !== null || $rs['gas_level_m'] !== null)
            <div class="kpi" style="--c:#f87171"><div class="l">Fluid contacts</div><div class="v" style="font-size:16px">@if($rs['water_level_m'] !== null)Water ≥ {{ $nf($rs['water_level_m']) }} m @endif @if($rs['gas_level_m'] !== null)<br>Gas ≤ {{ $nf($rs['gas_level_m']) }} m @endif</div><div class="u">from gradient survey</div></div>
            @endif
        </div>
        @endif
    </div>

    {{-- WELL INTEGRITY (feeds the health score, production state and recommendations above) --}}
    @if($analysis['integrity'])
    @php $ig = $analysis['integrity']; @endphp
    <div class="panel" style="margin-top:18px">
        <div class="ph"><h3><i class="fe fe-shield"></i> Well integrity</h3>
            <span class="sub">{{ $ig['guidance'] }}</span>
            <a class="lnk" href="{{ route('integrity.show', $oilString) }}">Open integrity file</a></div>
        @if(!$ig['has_data'])
            <div class="msg warn"><i class="fe fe-alert-triangle"></i><span>No integrity data recorded for this well, so it is shown as <b>not assessed</b> (not assumed healthy).</span></div>
        @else
        <div class="kpis" style="margin-top:0">
            <div class="kpi" style="--c:{{ $ig['color'] }}"><div class="l">Integrity status</div><div class="v" style="font-size:24px">{{ strtoupper($ig['label']) }}</div><div class="u">score {{ $ig['score'] }}</div></div>
            <div class="kpi" style="--c:#fbbf24"><div class="l">Annuli in alert</div><div class="v">{{ $ig['counts']['annuli_alert'] }}</div><div class="u">of {{ count($ig['annuli']) }} monitored</div></div>
            <div class="kpi" style="--c:#f87171"><div class="l">Failed barriers</div><div class="v">{{ $ig['counts']['failed'] }}</div><div class="u">{{ $ig['counts']['degraded'] }} degraded · {{ $ig['counts']['overdue_tests'] }} tests overdue</div></div>
            <div class="kpi" style="--c:#a78bfa"><div class="l">Open anomalies</div><div class="v">{{ $ig['counts']['open_anomalies'] }}</div><div class="u">{{ $ig['counts']['critical'] }} critical</div></div>
            <div class="kpi" style="--c:#2dd4bf"><div class="l">Last wellhead check</div><div class="v" style="font-size:18px">{{ $ig['latest_check']?->check_date->format('d M Y') ?? '—' }}</div><div class="u">{{ $ig['counts']['check_issues'] ? $ig['counts']['check_issues'] . ' issue(s)' : 'no issues' }}</div></div>
            @if($ig['profile'] && ($ig['profile']->max_oil_rate || $ig['profile']->mawhp))
            <div class="kpi" style="--c:#fb923c"><div class="l">Operating limits</div><div class="v" style="font-size:16px">{{ $ig['profile']->max_oil_rate ? number_format($ig['profile']->max_oil_rate) . ' B/D' : '' }}<br>{{ $ig['profile']->mawhp ? 'MAWHP ' . number_format($ig['profile']->mawhp) : '' }}</div><div class="u">applied to this analysis</div></div>
            @endif
        </div>
        @if($ig['reasons'])
            <div class="ins-sub">Integrity factors</div>
            @foreach(array_slice($ig['reasons'], 0, 8) as $r)<div class="fac"><b>{{ $r['delta'] }}</b><span><span style="color:var(--mute)">{{ $r['area'] }} ·</span> {{ $r['text'] }}</span></div>@endforeach
        @endif
        @isset($rs['pi'])
            @if(!empty($rs['pi']['restricted']))<div class="msg warn" style="margin-top:14px"><i class="fe fe-alert-triangle"></i><span><b>Productivity index:</b> {{ $rs['pi']['restricted'] }}</span></div>
            @elseif(!empty($rs['pi']['caution']))<div class="msg" style="margin-top:14px"><i class="fe fe-info"></i><span><b>Productivity index:</b> {{ $rs['pi']['caution'] }}</span></div>@endif
        @endisset
        @endif
    </div>
    @endif

    {{-- RECOMMENDATIONS --}}
    <div class="panel" style="margin-top:18px">
        <div class="ph"><h3><i class="fe fe-check-square"></i> Recommendations</h3><span class="sub">{{ count($analysis['recommendations']) }} item(s) · ranked by priority</span></div>
        @foreach($analysis['recommendations'] as $rec)
            @php $rc = ['high'=>'#f87171','medium'=>'#fbbf24','low'=>'#60a5fa','info'=>'#34d399'][$rec['severity']]; @endphp
            <div class="rec" style="--rc:{{ $rc }}">
                <i class="fe {{ $sevIcon[$rec['severity']] }}"></i>
                <div><h4>{{ $rec['title'] }} <span class="chip {{ $sevChip[$rec['severity']] }}">{{ $rec['severity'] }}</span> <span class="chip">{{ $rec['area'] }}</span></h4>
                    <p>{{ $rec['detail'] }}</p></div>
            </div>
        @endforeach
        <div class="disc"><i class="fe fe-info"></i> Automated, rule-based decision support computed from the recorded tests and surveys. Review with the reservoir/production engineer before acting.</div>
    </div>

    {{-- CHARTS --}}
    <div class="g2" style="margin-top:18px">
        <div class="panel"><div class="ph"><h3><i class="fe fe-trending-down"></i> Oil rate, decline fit &amp; forecast</h3><span class="sub">B/D</span></div><div id="chInsDecline"></div></div>
        <div class="panel"><div class="ph"><h3><i class="fe fe-percent"></i> Water cut &amp; GOR</h3><span class="sub">% · GOR</span></div><div id="chInsWcGor"></div></div>
        <div class="panel"><div class="ph"><h3><i class="fe fe-gauge"></i> Wellhead pressure &amp; temperature</h3><span class="sub">psi · °C</span></div><div id="chInsWh"></div></div>
        <div class="panel"><div class="ph"><h3><i class="fe fe-layers"></i> Reservoir pressure &amp; temperature</h3><span class="sub">BHP / BHT per survey</span></div>
            @if(count($trendWl['labels']) > 0)<div id="chInsBhp"></div>@else<div class="empty">No pressure surveys yet.</div>@endif</div>
        <div class="panel"><div class="ph"><h3><i class="fe fe-bar-chart-2"></i> Estimated cumulative oil</h3><span class="sub">bbl (integrated from test rates)</span></div><div id="chInsCum"></div></div>
        <div class="panel"><div class="ph"><h3><i class="fe fe-pie-chart"></i> Activity on this well</h3></div>
            @php
                $maxW = max(1, collect($bk['wireline'])->max('count') ?? 1);
                $maxD = max(1, collect($bk['departments'])->max('count') ?? 1);
            @endphp
            <div class="hb">
                <div><span>MPFM tests</span><span class="tr"><i style="--w:{{ min(100, $bk['tests']['mpfm'] * 100 / max(1, $bk['tests']['mpfm'] + $bk['tests']['tv'])) }}%;--bc:#22d3ee"></i></span><b style="color:#fff">{{ $bk['tests']['mpfm'] }}</b></div>
                <div><span>Test vessel</span><span class="tr"><i style="--w:{{ min(100, $bk['tests']['tv'] * 100 / max(1, $bk['tests']['mpfm'] + $bk['tests']['tv'])) }}%;--bc:#fbbf24"></i></span><b style="color:#fff">{{ $bk['tests']['tv'] }}</b></div>
                @foreach($bk['wireline'] as $w)
                    <div><span>{{ $w['label'] }}</span><span class="tr"><i style="--w:{{ $w['count'] * 100 / $maxW }}%;--bc:{{ $w['color'] }}"></i></span><b style="color:#fff">{{ $w['count'] }}</b></div>
                @endforeach
                @foreach($bk['departments'] as $d)
                    <div><span>{{ $d['label'] }} tasks</span><span class="tr"><i style="--w:{{ $d['count'] * 100 / $maxD }}%;--bc:#a78bfa"></i></span><b style="color:#fff">{{ $d['count'] }}</b></div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- TIMELINE --}}
    <div class="panel" style="margin-top:18px">
        <div class="ph"><h3><i class="fe fe-clock"></i> Everything that happened on this well</h3><span class="sub">tests · Wire Line · department tasks (latest {{ count($analysis['timeline']) }})</span></div>
        @if(empty($analysis['timeline']))
            <div class="empty">No recorded activity yet.</div>
        @else
        <ul class="tl">
            @foreach($analysis['timeline'] as $e)
                <li style="--tc:{{ $e['color'] }}"><b>{{ $e['date']->format('d M Y') }}</b> · <span style="color:{{ $e['color'] }}">{{ $e['kind'] }}</span>
                    — @if($e['url'])<a href="{{ $e['url'] }}">{{ $e['text'] }}</a>@else{{ $e['text'] }}@endif
                    @if($e['status'])<small>· {{ str_replace('_', ' ', $e['status']) }}</small>@endif</li>
            @endforeach
        </ul>
        @endif
    </div>
</div>

<script>
window.addEventListener('load', function () {
    if (!window.OFCharts) return;
    var C = @json($analysis['charts']);
    var BHP = @json($trendWl);
    function put(id, o) { var el = document.getElementById(id); if (el) OFCharts.line(el, o); }
    if (C.labels.length) {
        put('chInsDecline', C.decline.enabled
            ? { labels: C.decline.labels, height: 260, series: [
                { name: 'Oil rate', data: C.decline.actual, color: '#22d3ee', unit: 'B/D' },
                { name: 'Decline fit / forecast', data: C.decline.fit, color: '#fbbf24', unit: 'B/D' } ] }
            : { labels: C.labels, height: 260, series: [ { name: 'Oil rate', data: C.oil, color: '#22d3ee', unit: 'B/D' } ] });
        put('chInsWcGor', { labels: C.labels, height: 260, series: [
            { name: 'Water cut', data: C.wc, color: '#38bdf8', unit: '%' },
            { name: 'GOR', data: C.gor, color: '#a78bfa', axis: 'r' } ] });
        put('chInsWh', { labels: C.labels, height: 260, series: [
            { name: 'WHFP', data: C.whfp, color: '#f87171', unit: 'psi' },
            { name: 'Temperature', data: C.temp, color: '#fb923c', unit: '°C', axis: 'r' } ] });
        put('chInsCum', { labels: C.labels, height: 260, area: true, series: [ { name: 'Cumulative oil', data: C.cum, color: '#34d399', unit: 'bbl' } ] });
    }
    if (BHP.labels.length) {
        put('chInsBhp', { labels: BHP.labels, height: 260, series: [
            { name: 'BHP', data: BHP.bhp, color: '#22d3ee', unit: 'psi' },
            { name: 'BHT', data: BHP.bht, color: '#fb923c', unit: '°C', axis: 'r' } ] });
    }
});
</script>
