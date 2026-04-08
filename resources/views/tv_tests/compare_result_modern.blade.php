@extends('layouts.master')

@section('title', 'Test Comparison: TV vs MPFM')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<style>
    * {
        margin: 0;
        padding: 0;
    }

    .hero-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 3rem 2rem;
        border-radius: 1rem;
        margin-bottom: 2rem;
        box-shadow: 0 10px 40px rgba(102, 126, 234, 0.2);
    }

    .hero-header h1 {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .hero-header p {
        font-size: 1.1rem;
        opacity: 0.95;
    }

    .breadcrumb-nav {
        display: flex;
        gap: 0.75rem;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid rgba(255,255,255,0.2);
        opacity: 0.9;
    }

    .breadcrumb-nav a {
        color: white;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .breadcrumb-nav a:hover {
        text-decoration: underline;
    }

    .tests-comparison-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        margin-bottom: 2rem;
    }

    .test-card {
        border-radius: 1rem;
        padding: 2rem;
        color: white;
        position: relative;
        overflow: hidden;
    }

    .test-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, currentColor, rgba(255,255,255,0.5));
    }

    .test-card.tv {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .test-card.mpfm {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
    }

    .test-card-header {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .test-info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .test-info-item {
        background: rgba(255,255,255,0.1);
        padding: 1rem;
        border-radius: 0.5rem;
        backdrop-filter: blur(10px);
    }

    .test-info-label {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
        opacity: 0.8;
        margin-bottom: 0.5rem;
    }

    .test-info-value {
        font-size: 1.1rem;
        font-weight: 600;
    }

    .card-modern {
        background: white;
        border-radius: 1rem;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid #e2e8f0;
    }

    .card-title {
        font-size: 1.4rem;
        font-weight: 800;
        color: #1a202c;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding-bottom: 1rem;
        border-bottom: 3px solid #667eea;
    }

    .card-title i {
        color: #667eea;
        font-size: 1.5rem;
    }

    .values-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
    }

    .value-column {
        border-radius: 0.75rem;
        padding: 1.5rem;
    }

    .value-column.tv {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        border: 2px solid #667eea;
    }

    .value-column.mpfm {
        background: linear-gradient(135deg, rgba(72, 187, 120, 0.1) 0%, rgba(56, 161, 105, 0.1) 100%);
        border: 2px solid #48bb78;
    }

    .value-table {
        width: 100%;
        border-collapse: collapse;
    }

    .value-table tr {
        border-bottom: 1px solid #e2e8f0;
    }

    .value-table tr:last-child {
        border-bottom: none;
    }

    .value-table td {
        padding: 0.75rem 0;
    }

    .value-table td:first-child {
        font-weight: 600;
        color: #2d3748;
    }

    .value-table td:last-child {
        text-align: right;
        font-weight: 700;
        color: #667eea;
    }

    .value-column.mpfm .value-table td:last-child {
        color: #48bb78;
    }

    .alert-box {
        border-radius: 1rem;
        padding: 1.5rem;
        margin-bottom: 2rem;
        border-left: 5px solid;
        display: flex;
        align-items: flex-start;
        gap: 1.5rem;
    }

    .alert-box i {
        font-size: 2rem;
        flex-shrink: 0;
        margin-top: 0.25rem;
    }

    .alert-danger {
        background: linear-gradient(135deg, #fff5f5 0%, #ffe0e0 100%);
        border-left-color: #e53e3e;
        color: #742a2a;
    }

    .alert-warning {
        background: linear-gradient(135deg, #fffaf0 0%, #ffe8c2 100%);
        border-left-color: #c05621;
        color: #7c2d12;
    }

    .alert-success {
        background: linear-gradient(135deg, #f0fff4 0%, #c6f6d5 100%);
        border-left-color: #22543d;
        color: #22543d;
    }

    .alert-title {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .issues-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .issue-card {
        border-radius: 0.75rem;
        padding: 1.5rem;
        border-left: 4px solid;
    }

    .issue-card.critical {
        background: #fff5f5;
        border-left-color: #e53e3e;
    }

    .issue-card.warning {
        background: #fffaf0;
        border-left-color: #c05621;
    }

    .issue-title {
        font-weight: 700;
        margin-bottom: 0.75rem;
        font-size: 1.05rem;
    }

    .issue-card.critical .issue-title {
        color: #742a2a;
    }

    .issue-card.warning .issue-title {
        color: #7c2d12;
    }

    .issue-diff {
        font-size: 1.8rem;
        font-weight: 800;
        margin: 0.5rem 0;
    }

    .issue-card.critical .issue-diff {
        color: #e53e3e;
    }

    .issue-card.warning .issue-diff {
        color: #c05621;
    }

    .issue-direction {
        font-size: 0.9rem;
        font-weight: 600;
        margin-top: 0.5rem;
    }

    .recommendations-box {
        background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
        border-radius: 1rem;
        padding: 2rem;
        border: 2px solid #667eea;
    }

    .recommendations-title {
        font-size: 1.3rem;
        font-weight: 800;
        color: #667eea;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .recommendations-list {
        list-style: none;
    }

    .recommendations-list li {
        padding: 0.75rem 0;
        color: #2d3748;
        font-weight: 500;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
    }

    .recommendations-list li:last-child {
        border-bottom: none;
    }

    .recommendations-list li::before {
        content: '✓';
        color: #48bb78;
        font-weight: 800;
        font-size: 1.2rem;
        flex-shrink: 0;
        margin-top: -0.1rem;
    }

    .comparison-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1.5rem;
    }

    .comparison-table thead {
        background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
        border-bottom: 3px solid #667eea;
    }

    .comparison-table th {
        padding: 1.25rem;
        text-align: center;
        font-weight: 700;
        font-size: 0.95rem;
        color: #2d3748;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .comparison-table tbody tr {
        border-bottom: 1px solid #e2e8f0;
        transition: all 0.3s ease;
    }

    .comparison-table tbody tr:hover {
        background: linear-gradient(90deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
    }

    .comparison-table td {
        padding: 1.25rem;
        text-align: center;
        font-size: 0.95rem;
    }

    .metric-name {
        text-align: left;
        font-weight: 700;
        color: #1a202c;
    }

    .diff-badge {
        display: inline-block;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-weight: 700;
        font-size: 1rem;
    }

    .diff-green {
        background: #c6f6d5;
        color: #22543d;
    }

    .diff-yellow {
        background: #feebc8;
        color: #7c2d12;
    }

    .diff-red {
        background: #fed7d7;
        color: #742a2a;
    }

    .direction-badge {
        display: inline-block;
        padding: 0.35rem 0.75rem;
        border-radius: 0.35rem;
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: capitalize;
    }

    .dir-equal {
        background: #c6f6d5;
        color: #22543d;
    }

    .dir-higher {
        background: #bee3f8;
        color: #2c5282;
    }

    .dir-lower {
        background: #feebc8;
        color: #7c2d12;
    }

    .summary-metrics {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-top: 1.5rem;
    }

    .metric-box {
        background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
        border-radius: 0.75rem;
        padding: 1.5rem;
        text-align: center;
        border: 2px solid #e2e8f0;
        transition: all 0.3s ease;
    }

    .metric-box:hover {
        border-color: #667eea;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
    }

    .metric-box-value {
        font-size: 2rem;
        font-weight: 800;
        color: #667eea;
        margin-bottom: 0.5rem;
    }

    .metric-box-label {
        font-size: 0.85rem;
        color: #718096;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
    }

    .charts-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 2rem;
        margin-top: 2rem;
    }

    .chart-box {
        background: white;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid #e2e8f0;
    }

    .chart-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .chart-container {
        position: relative;
        height: 300px;
    }

    @media (max-width: 768px) {
        .tests-comparison-grid,
        .values-grid {
            grid-template-columns: 1fr;
        }

        .hero-header h1 {
            font-size: 1.8rem;
        }

        .comparison-table {
            font-size: 0.85rem;
        }

        .comparison-table th,
        .comparison-table td {
            padding: 0.75rem;
        }
    }
</style>
@endpush

@section('content')
<!-- Hero Header -->
<div class="hero-header">
    <h1>
        <i class="fe fe-shuffle"></i>
        Test Comparison Analysis
    </h1>
    <p>Detailed comparison between Test Vessel and MPFM measurements</p>
    <div class="breadcrumb-nav">
        <a href="{{ route('tv_tests.index') }}">
            <i class="fe fe-home"></i> TV Tests
        </a>
        <span>/</span>
        <a href="{{ route('tv_tests.show', $tvTest->id) }}">
            Test #{{ $tvTest->id }}
        </a>
        <span>/</span>
        <span>Compare with MPFM #{{ $mpfmTest->id }}</span>
    </div>
</div>

<!-- Tests Comparison Cards -->
<div class="tests-comparison-grid">
    <!-- TV Test Card -->
    <div class="test-card tv">
        <div class="test-card-header">
            <i class="fe fe-anchor"></i>
            Test Vessel Test
        </div>
        <div class="test-info-grid">
            <div class="test-info-item">
                <div class="test-info-label">Test ID</div>
                <div class="test-info-value">#{{ $tvTest->id }}</div>
            </div>
            <div class="test-info-item">
                <div class="test-info-label">Date</div>
                <div class="test-info-value">{{ $tvTest->test_date->format('Y-m-d') }}</div>
            </div>
            <div class="test-info-item">
                <div class="test-info-label">Vessel</div>
                <div class="test-info-value">{{ $tvTest->testVessel->name ?? 'N/A' }}</div>
            </div>
            <div class="test-info-item">
                <div class="test-info-label">Status</div>
                <div class="test-info-value">{{ ucfirst($tvTest->status) }}</div>
            </div>
            <div class="test-info-item">
                <div class="test-info-label">Oil Field</div>
                <div class="test-info-value">{{ $tvTest->oilField->name ?? 'N/A' }}</div>
            </div>
            <div class="test-info-item">
                <div class="test-info-label">Operator</div>
                <div class="test-info-value">{{ $tvTest->user->name ?? 'N/A' }}</div>
            </div>
        </div>
    </div>

    <!-- MPFM Test Card -->
    <div class="test-card mpfm">
        <div class="test-card-header">
            <i class="fe fe-bar-chart-2"></i>
            MPFM Test
        </div>
        <div class="test-info-grid">
            <div class="test-info-item">
                <div class="test-info-label">Test ID</div>
                <div class="test-info-value">#{{ $mpfmTest->id }}</div>
            </div>
            <div class="test-info-item">
                <div class="test-info-label">Date</div>
                <div class="test-info-value">{{ $mpfmTest->test_date->format('Y-m-d') }}</div>
            </div>
            <div class="test-info-item">
                <div class="test-info-label">Tower</div>
                <div class="test-info-value">{{ $mpfmTest->tower->name ?? 'N/A' }}</div>
            </div>
            <div class="test-info-item">
                <div class="test-info-label">Status</div>
                <div class="test-info-value">{{ ucfirst($mpfmTest->status) }}</div>
            </div>
            <div class="test-info-item">
                <div class="test-info-label">Oil Field</div>
                <div class="test-info-value">{{ $mpfmTest->oilField->name ?? 'N/A' }}</div>
            </div>
            <div class="test-info-item">
                <div class="test-info-label">Data Source</div>
                <div class="test-info-value">
                    @if($mpfmTest->wellTest)
                        ✓ WellTest #{{ $mpfmTest->wellTest->id }}
                    @else
                        Direct
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Alert -->
@php
    $hasDanger = false;
    $hasWarning = false;
    foreach ($metrics as $metric) {
        if ($metric['pct_diff'] > 10) $hasDanger = true;
        if ($metric['pct_diff'] > 5) $hasWarning = true;
    }
    $timeDiff = abs($tvTest->test_date->diffInDays($mpfmTest->test_date));
@endphp

@if($hasDanger)
<div class="alert-box alert-danger">
    <i class="fe fe-alert-circle"></i>
    <div>
        <div class="alert-title">⚠️ Significant Differences Detected</div>
        <p style="margin: 0;">Some measurements show differences greater than 10%. Immediate review and investigation required.</p>
    </div>
</div>
@elseif($hasWarning)
<div class="alert-box alert-warning">
    <i class="fe fe-alert-triangle"></i>
    <div>
        <div class="alert-title">⚡ Warnings Detected</div>
        <p style="margin: 0;">Some measurements show differences between 5-10%. Monitor closely in the next test cycle.</p>
    </div>
</div>
@else
<div class="alert-box alert-success">
    <i class="fe fe-check-circle"></i>
    <div>
        <div class="alert-title">✓ Tests Match Well</div>
        <p style="margin: 0;">All measurements are within acceptable limits (< 5% difference).</p>
    </div>
</div>
@endif

<!-- Measured Values Section -->
<div class="card-modern">
    <div class="card-title">
        <i class="fe fe-activity"></i>
        Measured Values Comparison
    </div>

    <div class="values-grid">
        <!-- TV Values -->
        <div class="value-column tv">
            <h4 style="color: #667eea; margin-bottom: 1rem; font-size: 1.1rem;">
                <i class="fe fe-anchor"></i> Test Vessel Test
            </h4>
            <table class="value-table">
                <tr>
                    <td>Oil Rate</td>
                    <td>{{ number_format($tvMetrics['oil_rate'] ?? 0, 2) }} B/D</td>
                </tr>
                <tr>
                    <td>Gas Rate</td>
                    <td>{{ number_format($tvMetrics['gas_rate'] ?? 0, 2) }} MMscf/D</td>
                </tr>
                <tr>
                    <td>Water Cut</td>
                    <td>{{ number_format($tvMetrics['water_cut'] ?? 0, 2) }} %</td>
                </tr>
                <tr>
                    <td>Pressure</td>
                    <td>{{ number_format($tvMetrics['pressure'] ?? 0, 2) }} psia</td>
                </tr>
                <tr>
                    <td>Temperature</td>
                    <td>{{ number_format($tvMetrics['temperature'] ?? 0, 2) }} °F</td>
                </tr>
                <tr>
                    <td>GOR</td>
                    <td>{{ number_format($tvMetrics['gor'] ?? 0, 2) }} scf/stb</td>
                </tr>
            </table>
        </div>

        <!-- MPFM Values -->
        <div class="value-column mpfm">
            <h4 style="color: #48bb78; margin-bottom: 1rem; font-size: 1.1rem;">
                <i class="fe fe-bar-chart-2"></i> MPFM Test
                @if($mpfmTest->wellTest)
                    <span style="font-size: 0.75rem; background: #48bb78; color: white; padding: 0.35rem 0.65rem; border-radius: 0.35rem; margin-left: 0.75rem;">✓ WellTest #{{ $mpfmTest->wellTest->id }}</span>
                @endif
            </h4>
            <table class="value-table">
                <tr>
                    <td>Oil Rate</td>
                    <td>{{ number_format($mpfmMetrics['oil_rate'] ?? 0, 2) }} B/D</td>
                </tr>
                <tr>
                    <td>Gas Rate</td>
                    <td>{{ number_format($mpfmMetrics['gas_rate'] ?? 0, 2) }} MMscf/D</td>
                </tr>
                <tr>
                    <td>Water Cut</td>
                    <td>{{ number_format($mpfmMetrics['water_cut'] ?? 0, 2) }} %</td>
                </tr>
                <tr>
                    <td>Pressure</td>
                    <td>{{ number_format($mpfmMetrics['pressure'] ?? 0, 2) }} psia</td>
                </tr>
                <tr>
                    <td>Temperature</td>
                    <td>{{ number_format($mpfmMetrics['temperature'] ?? 0, 2) }} °F</td>
                </tr>
                <tr>
                    <td>GOR</td>
                    <td>{{ number_format($mpfmMetrics['gor'] ?? 0, 2) }} scf/stb</td>
                </tr>
            </table>
        </div>
    </div>
</div>

<!-- Issues & Recommendations -->
@php
    $issues = [];
    $warnings = [];

    foreach ($metrics as $key => $metric) {
        if ($metric['pct_diff'] > 10) {
            $issues[] = ['metric' => $key, 'diff' => $metric['pct_diff'], 'direction' => $metric['direction']];
        } elseif ($metric['pct_diff'] > 5) {
            $warnings[] = ['metric' => $key, 'diff' => $metric['pct_diff']];
        }
    }
@endphp

@if(!empty($issues) || !empty($warnings))
<div class="card-modern">
    <div class="card-title">
        <i class="fe fe-alert-circle"></i>
        Issues & Analysis
    </div>

    <div class="issues-grid">
        @foreach($issues as $issue)
        <div class="issue-card critical">
            <div class="issue-title">
                {{ ucfirst(str_replace('_', ' ', $issue['metric'])) }}
            </div>
            <div class="issue-diff">{{ number_format($issue['diff'], 2) }}%</div>
            <div class="issue-direction">
                @if($issue['direction'] == 'higher')
                    ⬆️ TV reading is HIGHER
                @elseif($issue['direction'] == 'lower')
                    ⬇️ TV reading is LOWER
                @endif
            </div>
        </div>
        @endforeach

        @foreach($warnings as $warning)
        <div class="issue-card warning">
            <div class="issue-title">
                {{ ucfirst(str_replace('_', ' ', $warning['metric'])) }}
            </div>
            <div class="issue-diff">{{ number_format($warning['diff'], 2) }}%</div>
            <div style="color: #7c2d12; font-size: 0.9rem; margin-top: 0.5rem;">⚠️ Monitor closely</div>
        </div>
        @endforeach
    </div>
</div>
@endif

<!-- Recommendations -->
<div class="card-modern">
    <div class="recommendations-box">
        <div class="recommendations-title">
            <i class="fe fe-lightbulb"></i>
            Recommendations & Next Steps
        </div>

        <ul class="recommendations-list">
            @if(!empty($issues))
                <li><strong>High Priority:</strong> Review Test Vessel equipment calibration immediately</li>
                <li>Check all sensor connections and battery levels on Test Vessel</li>
                <li>Verify data entry for accuracy (no manual input errors)</li>
                <li>Run a new test after equipment service and re-verification</li>
            @endif

            @if(!empty($warnings))
                <li><strong>Medium Priority:</strong> Document well conditions during both tests</li>
                <li>Monitor next test cycle for consistency in measurements</li>
            @endif

            @if($timeDiff > 7)
                <li><strong>Important:</strong> Tests are {{ $timeDiff }} days apart - re-run both tests under similar conditions</li>
            @else
                <li><strong>Good:</strong> Tests only {{ $timeDiff }} days apart - comparison is more reliable</li>
            @endif

            <li>Schedule regular calibration for Test Vessel equipment (monthly recommended)</li>
            <li>Compare with baseline MPFM readings to identify trends</li>
            <li>Maintain detailed logs of test conditions and environmental factors</li>
            <li>Train operators on proper Test Vessel operation and data recording</li>
        </ul>
    </div>
</div>

<!-- Summary Metrics -->
<div class="card-modern">
    <div class="card-title">
        <i class="fe fe-bar-chart-2"></i>
        Comparison Summary
    </div>

    <div class="summary-metrics">
        <div class="metric-box">
            <div class="metric-box-value">
                @php
                    $avgDiff = array_reduce($metrics, fn($carry, $m) => $carry + $m['pct_diff'], 0) / count($metrics);
                @endphp
                {{ number_format($avgDiff, 2) }}%
            </div>
            <div class="metric-box-label">Average Difference</div>
        </div>
        <div class="metric-box">
            <div class="metric-box-value">
                @php
                    $maxDiff = max(array_map(fn($m) => $m['pct_diff'], $metrics));
                @endphp
                {{ number_format($maxDiff, 2) }}%
            </div>
            <div class="metric-box-label">Maximum Difference</div>
        </div>
        <div class="metric-box">
            <div class="metric-box-value">{{ count($metrics) }}</div>
            <div class="metric-box-label">Metrics Analyzed</div>
        </div>
        <div class="metric-box">
            <div class="metric-box-value">{{ abs($tvTest->test_date->diffInDays($mpfmTest->test_date)) }}</div>
            <div class="metric-box-label">Days Apart</div>
        </div>
    </div>
</div>

<!-- Detailed Comparison Table -->
<div class="card-modern">
    <div class="card-title">
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
                    <th>Absolute Diff</th>
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

                @foreach($metrics as $key => $metric)
                @php
                    $diffClass = 'diff-green';
                    if ($metric['pct_diff'] > 10) {
                        $diffClass = 'diff-red';
                    } elseif ($metric['pct_diff'] > 5) {
                        $diffClass = 'diff-yellow';
                    }

                    $dirClass = 'dir-equal';
                    $dirText = 'Equal';
                    if ($metric['direction'] == 'higher') {
                        $dirClass = 'dir-higher';
                        $dirText = 'TV Higher';
                    } elseif ($metric['direction'] == 'lower') {
                        $dirClass = 'dir-lower';
                        $dirText = 'TV Lower';
                    }
                @endphp
                <tr>
                    <td class="metric-name">{{ $metricLabels[$key] ?? $key }}</td>
                    <td>{{ number_format($metric['tv_value'], 2) }}</td>
                    <td>{{ number_format($metric['mpfm_value'], 2) }}</td>
                    <td>{{ number_format($metric['abs_diff'], 2) }}</td>
                    <td>
                        <span class="diff-badge {{ $diffClass }}">
                            {{ number_format($metric['pct_diff'], 2) }}%
                        </span>
                    </td>
                    <td>
                        <span class="direction-badge {{ $dirClass }}">{{ $dirText }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Action Buttons -->
<div style="display: flex; gap: 1rem; margin-top: 2rem; margin-bottom: 2rem;">
    <a href="{{ route('tv_tests.show', $tvTest->id) }}" class="btn btn-primary" style="padding: 0.75rem 1.5rem; border-radius: 0.5rem; text-decoration: none;">
        <i class="fe fe-arrow-left"></i> Back to TV Test
    </a>
    <a href="{{ route('tv_tests.compare') }}" class="btn btn-secondary" style="padding: 0.75rem 1.5rem; border-radius: 0.5rem; text-decoration: none;">
        <i class="fe fe-shuffle"></i> Compare Another
    </a>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Smooth animations on load
    const cards = document.querySelectorAll('.test-card, .card-modern, .metric-box');
    cards.forEach((card, index) => {
        card.style.animation = `fadeInUp 0.5s ease-out ${index * 0.1}s both`;
    });
});

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</script>
@endpush
