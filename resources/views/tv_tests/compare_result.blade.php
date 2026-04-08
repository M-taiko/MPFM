@extends('layouts.master')

@section('title', 'Test Comparison: TV vs MPFM')

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

    .comparison-header {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .test-info-card {
        border-radius: 0.75rem;
        padding: 1.5rem;
        color: white;
    }

    .test-info-card.tv {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .test-info-card.mpfm {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
    }

    .test-info-label {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
        opacity: 0.9;
    }

    .test-info-value {
        font-size: 1.1rem;
        font-weight: 600;
        margin-top: 0.25rem;
    }

    .test-info-row {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .test-info-row:last-child {
        border-bottom: none;
    }

    .comparison-table {
        width: 100%;
        border-collapse: collapse;
    }

    .comparison-table thead {
        background: #f7fafc;
        border-bottom: 2px solid #e2e8f0;
    }

    .comparison-table th {
        padding: 1rem;
        text-align: center;
        font-weight: 600;
        font-size: 0.9rem;
        color: #2d3748;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .comparison-table tbody tr {
        border-bottom: 1px solid #e2e8f0;
        transition: all 0.2s ease;
    }

    .comparison-table tbody tr:hover {
        background: #f7fafc;
    }

    .comparison-table td {
        padding: 1rem;
        text-align: center;
        font-size: 0.95rem;
    }

    .metric-name {
        text-align: left;
        font-weight: 600;
        color: #2d3748;
    }

    .value-badge {
        display: inline-block;
        padding: 0.5rem 0.75rem;
        border-radius: 0.3rem;
        font-weight: 600;
        min-width: 100px;
    }

    .value-badge.tv {
        background: #bee3f8;
        color: #2c5282;
    }

    .value-badge.mpfm {
        background: #c6f6d5;
        color: #22543d;
    }

    .difference-small {
        background: #c6f6d5;
        color: #22543d;
    }

    .difference-medium {
        background: #feebc8;
        color: #7c2d12;
    }

    .difference-large {
        background: #fed7d7;
        color: #742a2a;
    }

    .direction-badge {
        display: inline-block;
        padding: 0.35rem 0.7rem;
        border-radius: 0.25rem;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: capitalize;
    }

    .direction-equal {
        background: #c6f6d5;
        color: #22543d;
    }

    .direction-higher {
        background: #bee3f8;
        color: #2c5282;
    }

    .direction-lower {
        background: #feebc8;
        color: #7c2d12;
    }

    .alert-modern {
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
        border-left: 4px solid;
        display: flex;
        align-items: flex-start;
        gap: 1rem;
    }

    .alert-danger-modern {
        background: #fff5f5;
        border-left-color: #e53e3e;
        color: #742a2a;
    }

    .alert-warning-modern {
        background: #fffaf0;
        border-left-color: #c05621;
        color: #7c2d12;
    }

    .alert-success-modern {
        background: #f0fff4;
        border-left-color: #22543d;
        color: #22543d;
    }

    .charts-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .chart-container {
        background: white;
        border-radius: 0.75rem;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 1px solid #e2e8f0;
    }

    .chart-title {
        font-size: 1rem;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .summary-card {
        background: #f7fafc;
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        padding: 1.25rem;
        text-align: center;
    }

    .summary-label {
        font-size: 0.85rem;
        color: #718096;
        text-transform: uppercase;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .summary-value {
        font-size: 2rem;
        font-weight: 700;
        color: #667eea;
    }

    .btn-modern {
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 600;
        font-size: 0.95rem;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-primary-modern {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .btn-primary-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(102, 126, 234, 0.3);
        color: white;
        text-decoration: none;
    }

    .btn-secondary-modern {
        background: #e2e8f0;
        color: #2d3748;
    }

    .btn-secondary-modern:hover {
        background: #cbd5e0;
        color: #1a202c;
        text-decoration: none;
    }

    .action-buttons {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid #e2e8f0;
        flex-wrap: wrap;
    }

    @media (max-width: 768px) {
        .comparison-header {
            grid-template-columns: 1fr;
        }

        .comparison-table {
            font-size: 0.85rem;
        }

        .comparison-table th,
        .comparison-table td {
            padding: 0.75rem;
        }

        .charts-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<!-- Modern Header -->
<div class="modern-header">
    <h1>
        <i class="fe fe-shuffle"></i>
        Test Comparison Analysis
    </h1>
    <p>Test Vessel vs MPFM Test on the same Oil String</p>
</div>

<!-- Test Information Cards -->
<div class="comparison-header">
    <div class="test-info-card tv">
        <div class="test-info-label">
            <i class="fe fe-anchor"></i> Test Vessel Test
        </div>
        <div class="test-info-value">#{{ $tvTest->id }}</div>

        <div style="margin-top: 1.5rem;">
            <div class="test-info-row">
                <span class="test-info-label">Vessel</span>
                <span class="test-info-value">{{ $tvTest->testVessel->name }}</span>
            </div>
            <div class="test-info-row">
                <span class="test-info-label">Date</span>
                <span class="test-info-value">{{ $tvTest->test_date->format('Y-m-d') }}</span>
            </div>
            <div class="test-info-row">
                <span class="test-info-label">Oil Field</span>
                <span class="test-info-value">{{ $tvTest->oilField->name }}</span>
            </div>
            <div class="test-info-row">
                <span class="test-info-label">Tower</span>
                <span class="test-info-value">{{ $tvTest->tower->name }}</span>
            </div>
            <div class="test-info-row">
                <span class="test-info-label">Status</span>
                <span class="test-info-value">{{ ucfirst($tvTest->status) }}</span>
            </div>
        </div>
    </div>

    <div class="test-info-card mpfm">
        <div class="test-info-label">
            <i class="fe fe-bar-chart-2"></i> MPFM Test
        </div>
        <div class="test-info-value">#{{ $mpfmTest->id }}</div>

        <div style="margin-top: 1.5rem;">
            <div class="test-info-row">
                <span class="test-info-label">Oil Field</span>
                <span class="test-info-value">{{ $mpfmTest->oilField->name }}</span>
            </div>
            <div class="test-info-row">
                <span class="test-info-label">Date</span>
                <span class="test-info-value">{{ $mpfmTest->test_date->format('Y-m-d') }}</span>
            </div>
            <div class="test-info-row">
                <span class="test-info-label">Tower</span>
                <span class="test-info-value">{{ $mpfmTest->tower->name }}</span>
            </div>
            <div class="test-info-row">
                <span class="test-info-label">Oil String</span>
                <span class="test-info-value">{{ $mpfmTest->oilString->name }}</span>
            </div>
            <div class="test-info-row">
                <span class="test-info-label">Status</span>
                <span class="test-info-value">{{ ucfirst($mpfmTest->status) }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Alert Banner -->
@php
    $hasDanger = false;
    $hasWarning = false;
    foreach ($metrics as $metric) {
        if ($metric['pct_diff'] > 10) $hasDanger = true;
        if ($metric['pct_diff'] > 5) $hasWarning = true;
    }
@endphp

@if($hasDanger)
<div class="alert-modern alert-danger-modern">
    <i class="fe fe-alert-circle" style="font-size: 1.5rem;"></i>
    <div>
        <strong>⚠ Significant Differences Detected</strong>
        <p style="margin-bottom: 0; margin-top: 0.25rem;">
            Some measurements show differences greater than 10%. Review the details below.
        </p>
    </div>
</div>
@elseif($hasWarning)
<div class="alert-modern alert-warning-modern">
    <i class="fe fe-alert-triangle" style="font-size: 1.5rem;"></i>
    <div>
        <strong>Note: Some Differences Detected</strong>
        <p style="margin-bottom: 0; margin-top: 0.25rem;">
            Measurements show differences between 5-10%. This is within normal variation range.
        </p>
    </div>
</div>
@else
<div class="alert-modern alert-success-modern">
    <i class="fe fe-check-circle" style="font-size: 1.5rem;"></i>
    <div>
        <strong>✓ Tests Match Well</strong>
        <p style="margin-bottom: 0; margin-top: 0.25rem;">
            All measurements are very similar with differences less than 5%.
        </p>
    </div>
</div>
@endif

<!-- Summary Statistics -->
<div class="card-modern">
    <div class="section-title">
        <i class="fe fe-bar-chart-2"></i>
        Comparison Summary
    </div>

    <div class="summary-grid">
        <div class="summary-card">
            <div class="summary-label">Average Difference</div>
            <div class="summary-value">
                @php
                    $avgDiff = array_reduce($metrics, fn($carry, $m) => $carry + $m['pct_diff'], 0) / count($metrics);
                @endphp
                {{ number_format($avgDiff, 2) }}%
            </div>
        </div>
        <div class="summary-card">
            <div class="summary-label">Max Difference</div>
            <div class="summary-value">
                @php
                    $maxDiff = max(array_map(fn($m) => $m['pct_diff'], $metrics));
                @endphp
                {{ number_format($maxDiff, 2) }}%
            </div>
        </div>
        <div class="summary-card">
            <div class="summary-label">Metrics Analyzed</div>
            <div class="summary-value">{{ count($metrics) }}</div>
        </div>
        <div class="summary-card">
            <div class="summary-label">Time Difference</div>
            <div class="summary-value">
                @php
                    $days = abs($tvTest->test_date->diffInDays($mpfmTest->test_date));
                @endphp
                {{ $days }} days
            </div>
        </div>
    </div>
</div>

<!-- Detailed Comparison Table -->
<div class="card-modern">
    <div class="section-title">
        <i class="fe fe-list"></i>
        Detailed Measurement Comparison
    </div>

    <div style="overflow-x: auto;">
        <table class="comparison-table">
            <thead>
                <tr>
                    <th>Metric</th>
                    <th>Test Vessel</th>
                    <th>MPFM</th>
                    <th>Absolute Difference</th>
                    <th>% Difference</th>
                    <th>Direction</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $metricLabels = [
                        'oil_rate' => 'Oil Rate (B/D)',
                        'gas_rate' => 'Gas Rate (MMscf/D)',
                        'water_cut' => 'Water Cut (%)',
                        'pressure' => 'Pressure (psia)',
                        'temperature' => 'Temperature (°F)',
                        'gor' => 'GOR (scf/stb)',
                    ];
                @endphp

                @foreach($metrics as $metricKey => $metric)
                @php
                    $diffClass = 'difference-small';
                    if ($metric['pct_diff'] > 10) {
                        $diffClass = 'difference-large';
                    } elseif ($metric['pct_diff'] > 5) {
                        $diffClass = 'difference-medium';
                    }

                    $directionClass = 'direction-equal';
                    if ($metric['direction'] == 'higher') {
                        $directionClass = 'direction-higher';
                    } elseif ($metric['direction'] == 'lower') {
                        $directionClass = 'direction-lower';
                    }
                @endphp
                <tr>
                    <td class="metric-name">{{ $metricLabels[$metricKey] ?? ucfirst(str_replace('_', ' ', $metricKey)) }}</td>
                    <td>
                        <span class="value-badge tv">{{ number_format($metric['tv_value'], 2) }}</span>
                    </td>
                    <td>
                        <span class="value-badge mpfm">{{ number_format($metric['mpfm_value'], 2) }}</span>
                    </td>
                    <td>
                        <span class="value-badge {{ $diffClass }}">{{ number_format($metric['abs_diff'], 2) }}</span>
                    </td>
                    <td>
                        <span class="value-badge {{ $diffClass }}">{{ number_format($metric['pct_diff'], 2) }}%</span>
                    </td>
                    <td>
                        <span class="direction-badge {{ $directionClass }}">
                            @if($metric['direction'] == 'equal')
                                <i class="fe fe-check"></i> Equal
                            @elseif($metric['direction'] == 'higher')
                                <i class="fe fe-arrow-up"></i> Higher
                            @else
                                <i class="fe fe-arrow-down"></i> Lower
                            @endif
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Visual Comparison Charts -->
<div class="card-modern">
    <div class="section-title">
        <i class="fe fe-bar-chart-2"></i>
        Visual Comparison
    </div>

    <div class="charts-grid">
        <div class="chart-container">
            <div class="chart-title">
                <i class="fe fe-bar-chart"></i>
                Measurement Values
            </div>
            <canvas id="comparisonChart"></canvas>
        </div>

        <div class="chart-container">
            <div class="chart-title">
                <i class="fe fe-percent"></i>
                Percentage Differences
            </div>
            <canvas id="diffChart"></canvas>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="card-modern">
    <div class="action-buttons">
        <a href="{{ route('tv_tests.show', $tvTest->id) }}" class="btn btn-modern btn-primary-modern">
            <i class="fe fe-eye"></i> View TV Test
        </a>
        <a href="{{ route('mpfm_tests.show', $mpfmTest->id) }}" class="btn btn-modern btn-primary-modern">
            <i class="fe fe-eye"></i> View MPFM Test
        </a>
        <a href="{{ route('tv_tests.compare') }}" class="btn btn-modern btn-secondary-modern">
            <i class="fe fe-arrow-left"></i> Back to Compare
        </a>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const metrics = @json($metrics);
    const metricLabels = {
        'oil_rate': 'Oil Rate',
        'gas_rate': 'Gas Rate',
        'water_cut': 'Water Cut',
        'pressure': 'Pressure',
        'temperature': 'Temperature',
        'gor': 'GOR'
    };

    // Comparison Chart
    const labels = Object.keys(metrics).map(k => metricLabels[k] || k);
    const tvValues = Object.values(metrics).map(m => m.tv_value);
    const mpfmValues = Object.values(metrics).map(m => m.mpfm_value);

    const comparisonCtx = document.getElementById('comparisonChart').getContext('2d');
    new Chart(comparisonCtx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Test Vessel',
                    data: tvValues,
                    backgroundColor: '#667eea',
                    borderColor: '#667eea',
                    borderWidth: 2
                },
                {
                    label: 'MPFM',
                    data: mpfmValues,
                    backgroundColor: '#48bb78',
                    borderColor: '#48bb78',
                    borderWidth: 2
                }
            ]
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

    // Difference Chart
    const diffs = Object.values(metrics).map(m => m.pct_diff);
    const diffCtx = document.getElementById('diffChart').getContext('2d');
    new Chart(diffCtx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Percentage Difference (%)',
                data: diffs,
                backgroundColor: diffs.map(d => {
                    if (d > 10) return '#fc8181';
                    if (d > 5) return '#fbd38d';
                    return '#9ae6b4';
                }),
                borderColor: diffs.map(d => {
                    if (d > 10) return '#e53e3e';
                    if (d > 5) return '#c05621';
                    return '#22543d';
                }),
                borderWidth: 2
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
});
</script>
@endpush
