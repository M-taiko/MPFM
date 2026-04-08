@extends('layouts.master')

@section('title', 'Oil String Analysis - ' . $oilString->name)

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<style>
    .modern-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2.5rem 2rem;
        border-radius: 0.75rem;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.2);
    }

    .modern-header h1 {
        margin: 0;
        font-size: 2rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .modern-header p {
        margin: 0.5rem 0 0 0;
        opacity: 0.9;
    }

    .breadcrumb-modern {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        opacity: 0.9;
        font-size: 0.95rem;
        margin-top: 1rem;
    }

    .breadcrumb-modern a {
        color: white;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .breadcrumb-modern a:hover {
        text-decoration: underline;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        border-radius: 0.75rem;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 1px solid #e2e8f0;
        text-align: center;
    }

    .stat-icon {
        font-size: 2rem;
        color: #667eea;
        margin-bottom: 0.5rem;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #1a202c;
    }

    .stat-label {
        font-size: 0.85rem;
        color: #718096;
        text-transform: uppercase;
        font-weight: 600;
        margin-top: 0.5rem;
    }

    .card-modern {
        background: white;
        border-radius: 0.75rem;
        padding: 2rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 1px solid #e2e8f0;
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #667eea;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .section-title i {
        color: #667eea;
    }

    .tests-table {
        width: 100%;
        border-collapse: collapse;
    }

    .tests-table thead {
        background: #f7fafc;
        border-bottom: 2px solid #e2e8f0;
    }

    .tests-table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        font-size: 0.9rem;
        color: #2d3748;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .tests-table tbody tr {
        border-bottom: 1px solid #e2e8f0;
        transition: all 0.3s ease;
        position: relative;
    }

    .tests-table tbody tr:hover {
        background: linear-gradient(90deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
        box-shadow: inset 0 0 10px rgba(102, 126, 234, 0.08);
    }

    .tests-table td {
        padding: 1.15rem 1rem;
        font-size: 0.95rem;
        color: #4a5568;
        font-weight: 500;
    }

    .tests-table td strong {
        color: #2d3748;
        font-weight: 700;
    }

    .badge-modern {
        display: inline-block;
        padding: 0.4rem 0.8rem;
        border-radius: 0.3rem;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: capitalize;
    }

    .badge-mpfm {
        background: #c6f6d5;
        color: #22543d;
    }

    .badge-tv {
        background: #bee3f8;
        color: #2c5282;
    }

    .status-draft { background: #e2e8f0; color: #2d3748; }
    .status-submitted { background: #bee3f8; color: #2c5282; }
    .status-approved { background: #c6f6d5; color: #22543d; }
    .status-rejected { background: #fed7d7; color: #742a2a; }

    .test-row-mpfm {
        border-left: 4px solid #48bb78;
    }

    .test-row-tv {
        border-left: 4px solid #667eea;
    }

    .action-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .btn-sm-modern {
        padding: 0.4rem 0.8rem;
        border-radius: 0.3rem;
        font-size: 0.8rem;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        font-weight: 500;
    }

    .btn-info-sm {
        background: #bee3f8;
        color: #2c5282;
    }

    .btn-info-sm:hover {
        background: #90cdf4;
    }

    .btn-warning-sm {
        background: #feebc8;
        color: #7c2d12;
    }

    .btn-warning-sm:hover {
        background: #fbd38d;
    }

    .chart-container {
        background: white;
        border-radius: 1rem;
        padding: 2rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        border: 1px solid #e2e8f0;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }

    .chart-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    }

    .chart-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .chart-title i {
        color: #667eea;
        font-size: 1.25rem;
    }

    .filter-tabs {
        display: flex;
        gap: 0.75rem;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        padding-bottom: 1.5rem;
        border-bottom: 2px solid #e2e8f0;
    }

    .filter-tab {
        padding: 0.65rem 1.5rem;
        border-radius: 0.5rem;
        border: 2px solid #e2e8f0;
        background: white;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 600;
        font-size: 0.9rem;
        color: #4a5568;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .filter-tab:hover {
        border-color: #667eea;
        background: #f7fafc;
        color: #667eea;
        transform: translateY(-2px);
    }

    .filter-tab.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-color: transparent;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    .empty-state {
        text-align: center;
        padding: 3rem 2rem;
        background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
        border-radius: 1rem;
        border: 2px dashed #cbd5e0;
        margin: 2rem 0;
    }

    .empty-state i {
        font-size: 4rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .empty-state h5 {
        color: #2d3748 !important;
        font-size: 1.25rem;
        font-weight: 600;
    }

    .empty-state p {
        color: #718096 !important;
        font-size: 0.95rem;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1.25rem;
        margin-bottom: 2rem;
    }

    .info-card {
        background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
        border: 2px solid #e2e8f0;
        border-radius: 0.75rem;
        padding: 1.25rem;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .info-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    }

    .info-card:hover {
        border-color: #667eea;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
        transform: translateY(-2px);
    }

    .info-label {
        font-size: 0.75rem;
        color: #718096;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 0.5px;
    }

    .info-value {
        font-size: 1.5rem;
        color: #1a202c;
        font-weight: 700;
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .measurement-metrics {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 1rem;
        margin-top: 1.5rem;
    }

    .metric-item {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        padding: 1rem;
        text-align: center;
    }

    .metric-label {
        font-size: 0.75rem;
        color: #718096;
        font-weight: 600;
        text-transform: uppercase;
    }

    .metric-value {
        font-size: 1.25rem;
        font-weight: 700;
        color: #667eea;
        margin-top: 0.5rem;
    }
</style>
@endpush

@section('content')
<!-- Modern Header -->
<div class="modern-header">
    <h1>
        <i class="fe fe-trending-up"></i>
        Oil String Analysis
    </h1>
    <p>{{ $oilString->name }} - Complete Test History</p>
    <div class="breadcrumb-modern">
        <a href="{{ route('oil_fields.show', $oilString->tower->oilField->id) }}">
            <i class="fe fe-home"></i> {{ $oilString->tower->oilField->name }}
        </a>
        <span>/</span>
        <a href="{{ route('towers.show', $oilString->tower->id) }}">
            <i class="fe fe-layers"></i> {{ $oilString->tower->name }}
        </a>
        <span>/</span>
        <span>{{ $oilString->name }}</span>
    </div>
</div>

<!-- Statistics -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon"><i class="fe fe-activity"></i></div>
        <div class="stat-value">{{ count($allTests) }}</div>
        <div class="stat-label">Total Tests</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon"><i class="fe fe-bar-chart-2"></i></div>
        <div class="stat-value">{{ count($mpfmTests) }}</div>
        <div class="stat-label">MPFM Tests</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon"><i class="fe fe-anchor"></i></div>
        <div class="stat-value">{{ count($tvTests) }}</div>
        <div class="stat-label">TV Tests</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon"><i class="fe fe-calendar"></i></div>
        <div class="stat-value">
            @if($allTests->count() > 0)
                {{ $allTests->first()['test']->test_date->format('Y-m-d') }}
            @else
                N/A
            @endif
        </div>
        <div class="stat-label">Latest Test</div>
    </div>
</div>

<!-- Oil String Info -->
<div class="card-modern">
    <div class="section-title">
        <i class="fe fe-info"></i>
        Oil String Information
    </div>

    <div class="info-grid">
        <div class="info-card">
            <div class="info-label">
                <i class="fe fe-anchor" style="color: #667eea; margin-right: 0.3rem;"></i>
                Oil String
            </div>
            <div class="info-value">{{ $oilString->name }}</div>
        </div>
        <div class="info-card">
            <div class="info-label">
                <i class="fe fe-layers" style="color: #667eea; margin-right: 0.3rem;"></i>
                Tower
            </div>
            <div class="info-value">{{ $oilString->tower->name }}</div>
        </div>
        <div class="info-card">
            <div class="info-label">
                <i class="fe fe-map-pin" style="color: #667eea; margin-right: 0.3rem;"></i>
                Oil Field
            </div>
            <div class="info-value">{{ $oilString->tower->oilField->name }}</div>
        </div>
        <div class="info-card">
            <div class="info-label">
                <i class="fe fe-activity" style="color: #667eea; margin-right: 0.3rem;"></i>
                Total Tests
            </div>
            <div class="info-value">{{ count($allTests) }}</div>
        </div>
    </div>

    @if(count($allTests) > 0)
        <div class="section-title" style="margin-top: 2rem; padding-top: 1.5rem; border-top: 2px solid #e2e8f0;">
            <i class="fe fe-bar-chart-2"></i>
            Latest Measurements
        </div>
        @php
            $latestTest = $allTests->first()['test'] ?? null;
        @endphp
        @if($latestTest)
            <div class="measurement-metrics">
                <div class="metric-item">
                    <div class="metric-label">Oil Rate</div>
                    <div class="metric-value">{{ number_format($latestTest->oil_rate, 2) }}</div>
                    <div style="font-size: 0.75rem; color: #a0aec0;">B/D</div>
                </div>
                <div class="metric-item">
                    <div class="metric-label">Gas Rate</div>
                    <div class="metric-value">{{ number_format($latestTest->gas_rate, 2) }}</div>
                    <div style="font-size: 0.75rem; color: #a0aec0;">MMscf/D</div>
                </div>
                <div class="metric-item">
                    <div class="metric-label">Water Cut</div>
                    <div class="metric-value">{{ number_format($latestTest->water_cut, 2) }}</div>
                    <div style="font-size: 0.75rem; color: #a0aec0;">%</div>
                </div>
                @if($latestTest->pressure)
                <div class="metric-item">
                    <div class="metric-label">Pressure</div>
                    <div class="metric-value">{{ number_format($latestTest->pressure, 0) }}</div>
                    <div style="font-size: 0.75rem; color: #a0aec0;">psi</div>
                </div>
                @endif
                @if($latestTest->temperature)
                <div class="metric-item">
                    <div class="metric-label">Temperature</div>
                    <div class="metric-value">{{ number_format($latestTest->temperature, 1) }}</div>
                    <div style="font-size: 0.75rem; color: #a0aec0;">°C</div>
                </div>
                @endif
            </div>
        @endif
    @endif
</div>

<!-- All Tests Combined -->
<div class="card-modern">
    <div class="section-title">
        <i class="fe fe-list"></i>
        Complete Test History
    </div>

    @if($allTests->count() > 0)
        <div class="filter-tabs">
            <button class="filter-tab active" onclick="filterTests('all')">
                <i class="fe fe-eye"></i> All Tests ({{ count($allTests) }})
            </button>
            <button class="filter-tab" onclick="filterTests('mpfm')">
                <i class="fe fe-bar-chart-2"></i> MPFM ({{ count($mpfmTests) }})
            </button>
            <button class="filter-tab" onclick="filterTests('tv')">
                <i class="fe fe-anchor"></i> Test Vessel ({{ count($tvTests) }})
            </button>
        </div>

        <div class="table-responsive">
            <table class="tests-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Test ID</th>
                        <th>Status</th>
                        <th>Shift</th>
                        <th>Oil Rate (B/D)</th>
                        <th>Gas Rate (MMscf/D)</th>
                        <th>Water Cut (%)</th>
                        <th>Operator</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allTests as $item)
                        @php
                            $test = $item['test'];
                            $type = $item['type'];
                            $rowClass = $type === 'mpfm' ? 'test-row-mpfm' : 'test-row-tv';
                            $badgeClass = $type === 'mpfm' ? 'badge-mpfm' : 'badge-tv';
                            $typeLabel = $type === 'mpfm' ? 'MPFM' : 'TV';
                        @endphp
                        <tr class="{{ $rowClass }} filter-row filter-{{ $type }}">
                            <td><strong>{{ $test->test_date->format('Y-m-d') }}</strong></td>
                            <td>
                                <span class="badge-modern {{ $badgeClass }}">
                                    @if($type === 'mpfm')
                                        <i class="fe fe-bar-chart-2"></i> MPFM
                                    @else
                                        <i class="fe fe-anchor"></i> TV
                                    @endif
                                </span>
                            </td>
                            <td>
                                @if($type === 'mpfm')
                                    <a href="{{ route('mpfm_tests.show', $test->id) }}" class="font-weight-bold text-primary">
                                        MPFM #{{ $test->id }}
                                    </a>
                                @else
                                    <a href="{{ route('tv_tests.show', $test->id) }}" class="font-weight-bold text-primary">
                                        TV #{{ $test->id }}
                                    </a>
                                @endif
                            </td>
                            <td>
                                <span class="badge-modern status-{{ $test->status }}">{{ ucfirst($test->status) }}</span>
                            </td>
                            <td>{{ ucfirst($test->shift) }}</td>
                            <td>{{ number_format($test->oil_rate, 2) }}</td>
                            <td>{{ number_format($test->gas_rate, 2) }}</td>
                            <td>{{ number_format($test->water_cut, 2) }}</td>
                            <td>{{ $test->user->name ?? '-' }}</td>
                            <td>
                                <div class="action-buttons">
                                    @if($type === 'mpfm')
                                        <a href="{{ route('mpfm_tests.show', $test->id) }}" class="btn btn-sm-modern btn-info-sm">
                                            <i class="fe fe-eye"></i> View
                                        </a>
                                    @else
                                        <a href="{{ route('tv_tests.show', $test->id) }}" class="btn btn-sm-modern btn-info-sm">
                                            <i class="fe fe-eye"></i> View
                                        </a>
                                        @if($mpfmTests->count() > 0)
                                        <a href="{{ route('tv_tests.compare_result', ['tv_test_id' => $test->id, 'mpfm_test_id' => $mpfmTests->first()->id]) }}" class="btn btn-sm-modern btn-warning-sm">
                                            <i class="fe fe-shuffle"></i> Compare
                                        </a>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="empty-state">
            <i class="fe fe-search"></i>
            <h5 style="color: #718096; margin: 0;">No Tests Found</h5>
            <p style="color: #a0aec0; margin-top: 0.5rem;">
                There are no tests recorded for this oil string yet.
            </p>
        </div>
    @endif
</div>

<!-- Trending Data -->
@if(isset($trendingData) && count($trendingData) > 0)
<div class="card-modern">
    <div class="section-title">
        <i class="fe fe-trending-up"></i>
        Trending Analysis
    </div>

    <div class="chart-container">
        <div class="chart-title">
            <i class="fe fe-bar-chart-2"></i>
            Oil Rate Trend
        </div>
        <canvas id="trendChart" height="80"></canvas>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
function filterTests(type) {
    // Update active tab
    document.querySelectorAll('.filter-tab').forEach(tab => tab.classList.remove('active'));
    event.target.closest('.filter-tab').classList.add('active');

    // Filter rows
    const rows = document.querySelectorAll('.filter-row');
    rows.forEach(row => {
        if (type === 'all') {
            row.style.display = '';
        } else if (row.classList.contains(`filter-${type}`)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

@if(isset($trendingData) && count($trendingData) > 0)
document.addEventListener('DOMContentLoaded', function() {
    const trendingData = @json($trendingData);

    if (trendingData.dates && trendingData.oil_rates) {
        const ctx = document.getElementById('trendChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: trendingData.dates,
                datasets: [{
                    label: 'Oil Rate (B/D)',
                    data: trendingData.oil_rates,
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#667eea',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }
});
@endif
</script>
@endpush
