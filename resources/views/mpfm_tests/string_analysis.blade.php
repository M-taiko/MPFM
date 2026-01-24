@extends('layouts.master')

@section('title', 'String Analysis - ' . $oilString->name)

@section('css')
<style>
    .analysis-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 8px;
        margin-bottom: 30px;
    }

    .page-header h1 {
        margin: 0;
        font-size: 2rem;
    }

    .stat-box {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }

    .stat-item {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 6px;
        text-align: center;
    }

    .stat-number {
        font-size: 1.8rem;
        font-weight: bold;
        color: #667eea;
    }

    .stat-label {
        font-size: 0.85rem;
        color: #666;
        margin-top: 5px;
    }

    .test-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px;
        border-bottom: 1px solid #dee2e6;
        transition: background 0.2s ease;
    }

    .test-row:hover {
        background: #f8f9fa;
    }

    .test-date {
        font-weight: bold;
        color: #333;
    }

    .test-values {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
        gap: 10px;
        margin-top: 8px;
    }

    .value-item {
        font-size: 0.85rem;
        color: #666;
    }

    .value-label {
        font-size: 0.75rem;
        color: #999;
    }

    .validation-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 0.8rem;
        font-weight: bold;
    }

    .badge-pass { background: #d4edda; color: #155724; }
    .badge-warning { background: #fff3cd; color: #856404; }
    .badge-fail { background: #f8d7da; color: #721c24; }

    .alert-section {
        margin-top: 20px;
    }

    .alert-item {
        padding: 12px;
        border-left: 4px solid;
        border-radius: 4px;
        margin-bottom: 10px;
    }

    .alert-critical { background: #fff5f5; border-color: #dc3545; }
    .alert-warning { background: #fffbf0; border-color: #ffc107; }

    .chart-container {
        position: relative;
        height: 300px;
        margin-bottom: 30px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header">
        <h1>
            <i class="fe fe-activity" style="opacity: 0.8;"></i>
            {{ $oilString->name }} Analysis
        </h1>
        <p style="margin: 10px 0 0 0; opacity: 0.9;">Tower: {{ $oilString->tower->name }} | Well: {{ $oilString->well_name ?? 'N/A' }}</p>
    </div>

    <!-- Statistics -->
    <div class="stat-box">
        <div class="stat-item">
            <div class="stat-number">{{ $tests->total() }}</div>
            <div class="stat-label">Total Tests</div>
        </div>
        @if($trendingData && isset($trendingData['tests_analyzed']))
            <div class="stat-item">
                <div class="stat-number">{{ $trendingData['tests_analyzed'] }}</div>
                <div class="stat-label">Analyzed (180 days)</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" style="color: {{ ($trendingData['alert_count'] ?? 0) > 0 ? '#dc3545' : '#28a745' }};">
                    {{ $trendingData['alert_count'] ?? 0 }}
                </div>
                <div class="stat-label">Active Alerts</div>
            </div>
        @elseif($trendingData && isset($trendingData['message']))
            <div class="stat-item" style="grid-column: 2 / -1;">
                <p class="text-muted mb-0">{{ $trendingData['message'] }}</p>
            </div>
        @endif
    </div>

    <!-- Alerts Section -->
    @if($trendingData && isset($trendingData['alert_count']) && $trendingData['alert_count'] > 0)
    <div class="analysis-card alert-section">
        <h5>
            <i class="fa fa-exclamation-triangle"></i> Active Alerts
        </h5>
        @foreach($trendingData['alerts'] as $alert)
        <div class="alert-item alert-{{ $alert['severity'] }}">
            <strong>{{ ucfirst(str_replace('_', ' ', $alert['parameter'])) }}</strong>
            <span class="badge badge-{{ $alert['severity'] }}">{{ $alert['type'] }}</span>
            <p class="mb-0">{{ $alert['message'] }}</p>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Test History -->
    <div class="analysis-card">
        <h5>Test History</h5>

        @forelse($tests as $test)
        <div class="test-row">
            <div>
                <div class="test-date">{{ $test->test_date }}</div>
                <div class="test-values">
                    <div class="value-item">
                        <div class="value-label">Oil Rate</div>
                        <strong>{{ $test->oil_rate ?? 'N/A' }}</strong> bbl/day
                    </div>
                    <div class="value-item">
                        <div class="value-label">Water Cut</div>
                        <strong>{{ $test->water_cut ?? 'N/A' }}</strong>%
                    </div>
                    <div class="value-item">
                        <div class="value-label">GOR</div>
                        <strong>{{ $test->gor ?? 'N/A' }}</strong> scf/bbl
                    </div>
                </div>
            </div>
            <div class="text-right">
                @if($test->latestValidation)
                    <span class="validation-badge badge-{{ $test->latestValidation->overall_status }}">
                        {{ ucfirst($test->latestValidation->overall_status) }}
                    </span>
                @endif
                <div style="margin-top: 8px;">
                    <a href="{{ route('mpfm_tests.show_history', $test->id) }}" class="btn btn-sm btn-info">
                        <i class="fa fa-eye"></i> Details
                    </a>
                </div>
            </div>
        </div>
        @empty
        <p class="text-muted">No tests found for this string</p>
        @endforelse

        <!-- Pagination -->
        <div class="mt-3">
            {{ $tests->links() }}
        </div>
    </div>
</div>
@endsection
