@extends('layouts.master')

@section('title', 'Well Integrity')

@section('css')
@include('integrity._style')
@endsection

@section('content')
@php
    use App\Models\WellIntegrityAnomaly as An;
    $nf = fn ($v, $d = 0) => $v === null ? '—' : number_format($v, $d);
    $total = max(1, $stats['wells']);
@endphp
<div class="ofp" id="ofp">
    <div class="bgl"><div class="orb o1"></div><div class="orb o2"></div></div>

    <div class="hero">
        <div class="who">
            <span class="lg"></span>
            <div>
                <div class="eyebrow"><i></i> Well Integrity Management</div>
                <h1>Well <em>integrity</em></h1>
                <div class="meta"><span>Barrier envelopes, annulus pressures vs MAASP, inspections and the anomaly register - feeding every well analysis.</span></div>
            </div>
        </div>
        <div class="actions">
            <a class="btn2" href="{{ route('integrity.wells') }}"><i class="fe fe-list"></i> Wells register</a>
            <a class="btn2" href="{{ route('integrity.anomalies') }}"><i class="fe fe-alert-octagon"></i> Anomalies</a>
        </div>
    </div>

    @if(session('success'))<div class="msg ok flash"><i class="fe fe-check-circle"></i><span>{{ session('success') }}</span></div>@endif

    <div class="kpis">
        <div class="kpi" style="--c:#34d399"><div class="ic"><i class="fe fe-shield"></i></div><div class="l">Green</div><div class="v">{{ $stats['green'] }}</div><div class="u">integrity maintained</div></div>
        <div class="kpi" style="--c:#fbbf24"><div class="ic"><i class="fe fe-alert-triangle"></i></div><div class="l">Amber</div><div class="v">{{ $stats['amber'] }}</div><div class="u">operate with mitigation</div></div>
        <div class="kpi" style="--c:#f87171"><div class="ic"><i class="fe fe-alert-octagon"></i></div><div class="l">Red</div><div class="v">{{ $stats['red'] }}</div><div class="u">unacceptable risk</div></div>
        <div class="kpi" style="--c:#94a3b8"><div class="ic"><i class="fe fe-help-circle"></i></div><div class="l">Not assessed</div><div class="v">{{ $stats['none'] }}</div><div class="u">no integrity data</div></div>
        <div class="kpi" style="--c:#fb923c"><div class="ic"><i class="fe fe-x-octagon"></i></div><div class="l">Failed barriers</div><div class="v">{{ $stats['failed'] }}</div><div class="u">{{ $stats['overdue'] }} tests overdue</div></div>
        <div class="kpi" style="--c:#a78bfa"><div class="ic"><i class="fe fe-flag"></i></div><div class="l">Open anomalies</div><div class="v">{{ $stats['anomalies'] }}</div><div class="u">{{ $stats['critical'] }} critical</div></div>
    </div>

    <div class="wl-g3">
        <div class="panel">
            <div class="ph"><h3><i class="fe fe-droplet"></i> Wells by integrity status</h3><a class="lnk" href="{{ route('integrity.wells') }}">All wells</a></div>
            @if($rows->isEmpty())
                <div class="empty">No wells found.</div>
            @else
            <div class="scroll"><table class="t"><thead><tr><th>Well</th><th>Status</th><th>Score</th><th>Main driver</th><th></th></tr></thead><tbody>
                @foreach($rows->take(12) as $r)
                @php $a = $r['a']; @endphp
                <tr>
                    <td>{{ $r['well']->name }}<div class="mini">{{ $r['well']->tower?->oilField?->name }}</div></td>
                    <td><span class="chip {{ $a['class'] }}">{{ $a['label'] }}</span></td>
                    <td>{{ $a['score'] ?? '—' }}</td>
                    <td class="mini">{{ $a['reasons'][0]['text'] ?? ($a['has_data'] ? 'No adverse indicator' : 'Set up integrity file') }}</td>
                    <td><a class="lnk" href="{{ route('integrity.show', $r['well']) }}">Open</a></td>
                </tr>
                @endforeach
            </tbody></table></div>
            @endif
        </div>
        <div class="panel">
            <div class="ph"><h3><i class="fe fe-pie-chart"></i> Field status</h3></div>
            <div class="cw sm"><canvas id="cStatus"></canvas></div>
        </div>
    </div>

    <div class="wl-g3">
        <div class="panel">
            <div class="ph"><h3><i class="fe fe-alert-octagon"></i> Highest-risk open anomalies</h3><a class="lnk" href="{{ route('integrity.anomalies') }}">Register</a></div>
            @if($topAnomalies->isEmpty())
                <div class="msg ok"><i class="fe fe-check-circle"></i><span>No open anomalies.</span></div>
            @else
            <div class="scroll"><table class="t"><thead><tr><th>Risk</th><th>Well</th><th>Anomaly</th><th>Category</th><th>Due</th></tr></thead><tbody>
                @foreach($topAnomalies as $an)
                @php $lv = $an->level(); @endphp
                <tr>
                    <td><span class="rk" style="--rk:{{ $lv[2] }}">{{ $an->risk_score }}</span></td>
                    <td><a class="lnk" href="{{ route('integrity.show', $an->oil_string_id) }}">{{ $an->oilString->name ?? '—' }}</a></td>
                    <td>{{ $an->title }}</td>
                    <td class="mini">{{ An::CATEGORIES[$an->category] ?? $an->category }}</td>
                    <td class="{{ $an->isOverdue() ? 'mini' : '' }}" @if($an->isOverdue()) style="color:#fca5a5" @endif>{{ $an->due_date?->format('d M Y') ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody></table></div>
            @endif
        </div>
        <div class="panel">
            <div class="ph"><h3><i class="fe fe-gauge"></i> Annulus pressure alerts</h3></div>
            @if($annulusAlerts->isEmpty())
                <div class="msg ok"><i class="fe fe-check-circle"></i><span>All monitored annuli are within limits.</span></div>
            @else
            <ul class="list q">
                @foreach($annulusAlerts as $x)
                <li class="{{ $x['an']['state'] === 'bad' ? 'bad' : 'warn' }}"><i class="fe fe-alert-triangle"></i><span>
                    <a href="{{ route('integrity.show', $x['well']) }}" style="color:var(--cyan)">{{ $x['well']->name }}</a> - annulus {{ $x['an']['name'] }}:
                    {{ $nf($x['an']['last']->pressure) }} psi @if($x['an']['pct'] !== null)({{ $x['an']['pct'] }}% of MAASP)@endif
                    @if($x['an']['scp']) · rebuilds after bleed-down @endif</span></li>
                @endforeach
            </ul>
            @endif
        </div>
    </div>

    <div class="panel" style="margin-top:18px">
        <div class="ph"><h3><i class="fe fe-clock"></i> Barrier tests overdue</h3><span class="sub">{{ $stats['overdue'] }} element(s)</span></div>
        @if($overdueTests->isEmpty())
            <div class="msg ok"><i class="fe fe-check-circle"></i><span>No barrier tests are overdue.</span></div>
        @else
        <div class="scroll"><table class="t"><thead><tr><th>Well</th><th>Element</th><th>Envelope</th><th>Status</th><th>Due since</th><th></th></tr></thead><tbody>
            @foreach($overdueTests as $e)
            <tr>
                <td>{{ $e->oilString->name ?? '—' }}</td><td>{{ $e->name }}</td><td>{{ ucfirst($e->envelope) }}</td>
                <td><span class="chip {{ ['healthy'=>'ok','degraded'=>'warn','failed'=>'bad','untested'=>'none'][$e->status] }}">{{ \App\Models\WellBarrierElement::STATUSES[$e->status] }}</span></td>
                <td>{{ $e->next_due_date->format('d M Y') }} <span class="mini">({{ $e->next_due_date->diffInDays(now()) }} d)</span></td>
                <td><a class="lnk" href="{{ route('integrity.show', $e->oil_string_id) }}">Open</a></td>
            </tr>
            @endforeach
        </tbody></table></div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    if (!window.Chart) return;
    Chart.defaults.color = '#9fb0cf';
    new Chart(document.getElementById('cStatus'), {
        type: 'doughnut',
        data: { labels: ['Green', 'Amber', 'Red', 'Not assessed'],
            datasets: [{ data: [{{ $stats['green'] }}, {{ $stats['amber'] }}, {{ $stats['red'] }}, {{ $stats['none'] }}], backgroundColor: ['#34d399', '#fbbf24', '#f87171', '#64748b'], borderWidth: 0 }] },
        options: { responsive: true, maintainAspectRatio: false, cutout: '62%', plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 11 } } } } }
    });
})();
</script>
@endsection
