@extends('layouts.master')

@section('title', 'Wire Line')

@section('css')
@include('wireline._style')
@endsection

@section('content')
@php
    use App\Models\WirelineOperation as Op;
    $fmt = fn ($v, $d = 0) => $v === null ? '—' : number_format($v, $d);
@endphp
<div class="ofp" id="ofp">
    <div class="bgl"><div class="orb o1"></div><div class="orb o2"></div></div>

    <div class="hero">
        <div class="who">
            <span class="lg"></span>
            <div>
                <div class="eyebrow"><i></i> Wire Line Services</div>
                <h1>Wire Line <em>operations</em></h1>
                <div class="meta"><span>Memory-gauge surveys, slickline interventions and well history.</span></div>
            </div>
        </div>
        <div class="actions">
            <a class="btn2" href="{{ route('wireline.operations') }}"><i class="fe fe-list"></i> All operations</a>
            @if($canManage)<a class="btn2 p" href="{{ route('wireline.create') }}"><i class="fe fe-plus"></i> New operation</a>@endif
        </div>
    </div>

    @if(session('success'))<div class="msg ok flash"><i class="fe fe-check-circle"></i><span>{{ session('success') }}</span></div>@endif

    <div class="kpis">
        <div class="kpi" style="--c:#22d3ee"><div class="ic"><i class="fe fe-git-branch"></i></div><div class="l">Total operations</div><div class="v">{{ $stats['total'] }}</div><div class="u">{{ $stats['completed'] }} completed</div></div>
        <div class="kpi" style="--c:#34d399"><div class="ic"><i class="fe fe-calendar"></i></div><div class="l">This month</div><div class="v">{{ $stats['month'] }}</div><div class="u">{{ now()->format('F Y') }}</div></div>
        <div class="kpi" style="--c:#fbbf24"><div class="ic"><i class="fe fe-clock"></i></div><div class="l">Open</div><div class="v">{{ $stats['open'] }}</div><div class="u">planned / in progress</div></div>
        <div class="kpi" style="--c:#a78bfa"><div class="ic"><i class="fe fe-activity"></i></div><div class="l">Gauge runs</div><div class="v">{{ $stats['runs'] }}</div><div class="u">analysed recordings</div></div>
        <div class="kpi" style="--c:#60a5fa"><div class="ic"><i class="fe fe-droplet"></i></div><div class="l">Wells served</div><div class="v">{{ $stats['wells'] }}</div><div class="u">with wire line history</div></div>
        <div class="kpi" style="--c:#f87171"><div class="ic"><i class="fe fe-alert-triangle"></i></div><div class="l">Survey overdue</div><div class="v">{{ $overdueCount }}</div><div class="u">active wells, no survey in 180 d</div></div>
    </div>

    <div class="wl-g3">
        <div class="panel">
            <div class="ph"><h3><i class="fe fe-bar-chart-2"></i> Operations per month</h3><span class="sub">last 12 months</span></div>
            <div class="cw sm"><canvas id="cMonth"></canvas></div>
        </div>
        <div class="panel">
            <div class="ph"><h3><i class="fe fe-pie-chart"></i> By type</h3></div>
            @if($byType->isEmpty())<div class="empty">No operations recorded yet.</div>
            @else<div class="cw sm"><canvas id="cType"></canvas></div>@endif
        </div>
    </div>

    <div class="wl-g3">
        <div class="panel">
            <div class="ph"><h3><i class="fe fe-activity"></i> Latest memory-gauge surveys</h3></div>
            @if($runs->isEmpty())
                <div class="empty">No gauge data uploaded yet.</div>
            @else
            <div class="scroll"><table class="t"><thead><tr><th>Well</th><th>Date</th><th>Survey</th><th>BHP (psi)</th><th>BHT (°C)</th><th></th></tr></thead><tbody>
                @foreach($runs as $r)
                <tr>
                    <td>{{ $r->operation->oilString->name ?? '—' }}</td>
                    <td>{{ ($r->started_at ?? $r->operation->operation_date)->format('d M Y') }}</td>
                    <td>{{ strtoupper($r->survey_type) }}</td>
                    <td>{{ $fmt($r->bhp, 1) }}</td>
                    <td>{{ $fmt($r->bht, 1) }}</td>
                    <td><a class="lnk" href="{{ route('wireline.show', [$r->operation, 'run' => $r->id]) }}">Open</a></td>
                </tr>
                @endforeach
            </tbody></table></div>
            @endif
        </div>
        <div class="panel">
            <div class="ph"><h3><i class="fe fe-alert-triangle"></i> Surveys overdue</h3><span class="sub">{{ $overdueCount }} wells</span></div>
            @if($overdue->isEmpty())
                <div class="msg ok"><i class="fe fe-check-circle"></i><span>All active wells have a recent survey.</span></div>
            @else
            <ul class="list">
                @foreach($overdue as $o)
                <li><i class="fe fe-droplet"></i><span><a href="{{ route('oil_strings.show', $o['well']) }}" style="color:var(--cyan)">{{ $o['well']->name }}</a>
                    — {{ $o['last'] ? $o['days'] . ' days since last survey' : 'never surveyed' }}</span></li>
                @endforeach
            </ul>
            @endif
        </div>
    </div>

    <div class="panel" style="margin-top:18px">
        <div class="ph"><h3><i class="fe fe-list"></i> Recent operations</h3><a class="lnk" href="{{ route('wireline.operations') }}">View all</a></div>
        @if($recent->isEmpty())
            <div class="empty">No operations yet. @if($canManage)<a class="lnk" href="{{ route('wireline.create') }}">Record the first one</a>@endif</div>
        @else
        <div class="scroll"><table class="t"><thead><tr><th>Date</th><th>Well</th><th>Operation</th><th>Run</th><th>Status</th><th></th></tr></thead><tbody>
            @foreach($recent as $o)
            <tr>
                <td>{{ $o->operation_date->format('d M Y') }}</td>
                <td>{{ $o->oilString->name ?? '—' }}</td>
                <td><span class="tdot" style="--tc:{{ $o->typeColor() }}"></span>{{ $o->typeLabel() }}</td>
                <td>#{{ $o->run_number }}</td>
                <td><span class="chip {{ ['completed'=>'ok','in_progress'=>'info','planned'=>'pending','cancelled'=>'bad'][$o->status] }}">{{ $o->statusLabel() }}</span></td>
                <td><a class="lnk" href="{{ route('wireline.show', $o) }}">Open</a></td>
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
    Chart.defaults.borderColor = 'rgba(96,165,250,.12)';
    new Chart(document.getElementById('cMonth'), {
        type: 'bar',
        data: { labels: @json($monthLabels), datasets: [{ label: 'Operations', data: @json($perMonth), backgroundColor: '#22d3ee', borderRadius: 6 }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
    });
    const typeEl = document.getElementById('cType');
    if (typeEl) {
        const meta = @json(collect(Op::TYPES)->map(fn ($t) => ['label' => $t[0], 'color' => $t[2]]));
        const raw = @json($byType);
        const keys = Object.keys(raw);
        new Chart(typeEl, {
            type: 'doughnut',
            data: { labels: keys.map(k => meta[k] ? meta[k].label : k), datasets: [{ data: keys.map(k => raw[k]), backgroundColor: keys.map(k => meta[k] ? meta[k].color : '#94a3b8'), borderWidth: 0 }] },
            options: { responsive: true, maintainAspectRatio: false, cutout: '62%', plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 11 } } } } }
        });
    }
})();
</script>
@endsection
