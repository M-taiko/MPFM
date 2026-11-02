{{-- ===== Tab: Wire Line (history, statistics, BHP trend) ===== --}}
@php
    $wl = $wireline['stats'];
    $ops = $wireline['operations'];
    $trend = $wireline['trend'];
    $latest = $wireline['latest_run'];
@endphp
<div class="tab-pane fade" id="tab-wireline">
    <div class="d-flex justify-content-between align-items-center mb-3" style="gap:.5rem;flex-wrap:wrap;">
        <h6 class="section-header mb-0" style="border:none;padding:0;"><i class="fe fe-git-branch mr-2" style="color:#7c3aed;"></i>Wire Line history</h6>
        <div>
            <a href="{{ route('wireline.dashboard') }}" class="btn btn-sm btn-outline-secondary"><i class="fe fe-bar-chart-2 mr-1"></i>Dashboard</a>
            <a href="{{ route('wireline.create', ['well' => $oilString->id]) }}" class="btn btn-sm btn-primary"><i class="fe fe-plus mr-1"></i>New operation</a>
        </div>
    </div>

    @if($ops->isEmpty())
        <div class="no-data-placeholder">
            <i class="fe fe-git-branch"></i>
            No Wire Line operations recorded for this well yet.
        </div>
    @else
    <div class="row mb-3">
        <div class="col-6 col-md-3 mb-3"><div class="card kpi-card p-3" style="border-top:3px solid #7c3aed;">
            <div class="kpi-label">Operations</div><div class="kpi-value">{{ $wl['total'] }}</div>
            <div class="kpi-unit">{{ $wl['completed'] }} completed · {{ $wl['surveys'] }} gauge survey(s)</div></div></div>
        <div class="col-6 col-md-3 mb-3"><div class="card kpi-card p-3" style="border-top:3px solid #06b6d4;">
            <div class="kpi-label">Latest BHP</div><div class="kpi-value">{{ $wl['latest_bhp'] !== null ? number_format($wl['latest_bhp'], 0) : '—' }}</div>
            <div class="kpi-unit">psi
                @if($wl['bhp_change'] !== null)
                    · <span style="color:{{ $wl['bhp_change'] < 0 ? '#dc2626' : '#16a34a' }}">{{ sprintf('%+.0f', $wl['bhp_change']) }} vs previous</span>
                @endif</div></div></div>
        <div class="col-6 col-md-3 mb-3"><div class="card kpi-card p-3" style="border-top:3px solid #f97316;">
            <div class="kpi-label">Latest BHT</div><div class="kpi-value">{{ $wl['latest_bht'] !== null ? number_format($wl['latest_bht'], 1) : '—' }}</div>
            <div class="kpi-unit">°C</div></div></div>
        <div class="col-6 col-md-3 mb-3"><div class="card kpi-card p-3" style="border-top:3px solid {{ ($wl['days_since_survey'] ?? 0) > 180 ? '#ef4444' : '#10b981' }};">
            <div class="kpi-label">Last survey</div>
            <div class="kpi-value">{{ $wl['days_since_survey'] !== null ? $wl['days_since_survey'] . ' d' : '—' }}</div>
            <div class="kpi-unit">{{ $wl['last_survey_at']?->format('d M Y') ?? 'never surveyed' }}</div></div></div>
    </div>

    @if(count($trend['labels']) > 0)
    <div class="mb-4">
        <h6 class="section-header"><i class="fe fe-trending-up mr-2" style="color:#06b6d4;"></i>Bottom-hole pressure &amp; temperature trend</h6>
        <div class="chart-container"><canvas id="wlTrendChart"></canvas></div>
    </div>
    @endif

    <h6 class="section-header"><i class="fe fe-list mr-2" style="color:#7c3aed;"></i>Operations performed</h6>
    <div class="table-responsive">
        <table class="table table-tasks mb-0">
            <thead><tr><th>Date</th><th>Operation</th><th>Run</th><th>Depth (m)</th><th>BHP (psi)</th><th>BHT (°C)</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @foreach($ops as $o)
                @php $r = $o->gaugeRuns->first(); @endphp
                <tr>
                    <td>{{ $o->operation_date->format('d M Y') }}</td>
                    <td><span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:{{ $o->typeColor() }};margin-right:6px;"></span>{{ $o->typeLabel() }}</td>
                    <td>#{{ $o->run_number }}</td>
                    <td>{{ $o->depth_to !== null ? number_format($o->depth_to) : '—' }}</td>
                    <td>{{ $r?->bhp !== null ? number_format($r->bhp, 1) : '—' }}</td>
                    <td>{{ $r?->bht !== null ? number_format($r->bht, 1) : '—' }}</td>
                    <td><span class="badge badge-{{ ['completed'=>'success','in_progress'=>'info','planned'=>'secondary','cancelled'=>'danger'][$o->status] }}">{{ $o->statusLabel() }}</span></td>
                    <td><a href="{{ route('wireline.show', $o) }}" class="btn btn-sm btn-outline-primary">Open</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

@if(!$ops->isEmpty() && count($trend['labels']) > 0)
@push('scripts')
<script>
(function () {
    function draw() {
        new Chart(document.getElementById('wlTrendChart'), {
            type: 'line',
            data: { labels: @json($trend['labels']), datasets: [
                { label: 'BHP (psi)', data: @json($trend['bhp']), borderColor: '#06b6d4', backgroundColor: 'rgba(6,182,212,.12)', yAxisID: 'y', tension: .25, fill: true, pointRadius: 4 },
                { label: 'BHT (°C)', data: @json($trend['bht']), borderColor: '#f97316', yAxisID: 'y1', tension: .25, pointRadius: 4 }
            ] },
            options: { responsive: true, maintainAspectRatio: false, interaction: { mode: 'index', intersect: false },
                scales: { y: { position: 'left', title: { display: true, text: 'psi' } }, y1: { position: 'right', title: { display: true, text: '°C' }, grid: { drawOnChartArea: false } } } }
        });
    }
    function init() { if (window.Chart) { draw(); return; }
        var s = document.createElement('script'); s.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js'; s.onload = draw; document.head.appendChild(s); }
    document.addEventListener('DOMContentLoaded', init);
})();
</script>
@endpush
@endif
