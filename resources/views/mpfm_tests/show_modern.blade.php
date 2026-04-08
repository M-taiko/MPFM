@extends('layouts.master')

@section('title', 'MPFM Test Details - ' . ($wellTest->well ?? $mpfmTest->oilString->name ?? 'N/A'))

@section('css')
<link href="{{URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css')}}" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<style>
    .test-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
    }

    .metric-card-modern {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        margin-bottom: 1.5rem;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .metric-card-modern:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.12);
    }

    .metric-card-header {
        background: linear-gradient(135deg, var(--header-color-1) 0%, var(--header-color-2) 100%);
        color: white;
        padding: 1rem 1.5rem;
        font-weight: 600;
        font-size: 0.95rem;
    }

    .metric-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 1.5rem;
        padding: 1.5rem;
    }

    .metric-item-box {
        text-align: center;
    }

    .metric-value-large {
        font-size: 1.75rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 0.25rem;
    }

    .metric-label-small {
        font-size: 0.75rem;
        color: #718096;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .metric-unit {
        font-size: 0.875rem;
        color: #a0aec0;
        font-weight: 500;
    }

    .info-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        background: white;
    }

    .info-card .card-header {
        background: transparent;
        border-bottom: 2px solid #e2e8f0;
        padding: 1.25rem 1.5rem;
    }

    .info-card .card-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #2d3748;
        margin: 0;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        padding: 1.5rem;
    }

    .info-item {
        display: flex;
        flex-direction: column;
    }

    .info-label {
        font-size: 0.75rem;
        color: #718096;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
    }

    .info-value {
        font-size: 1rem;
        color: #2d3748;
        font-weight: 500;
    }

    .chart-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        background: white;
        margin-bottom: 1.5rem;
    }

    .chart-card .card-header {
        background: transparent;
        border-bottom: 1px solid #e2e8f0;
        padding: 1.25rem 1.5rem;
    }

    .chart-card .card-title {
        margin: 0;
        font-size: 1rem;
        font-weight: 600;
        color: #2d3748;
    }

    .detailed-table-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        overflow: hidden;
    }

    #vxDetailTable thead th {
        background: #f7fafc;
        color: #4a5568;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: none;
        padding: 0.75rem;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    #vxDetailTable tbody td {
        padding: 0.75rem;
        font-size: 0.875rem;
        border-bottom: 1px solid #e2e8f0;
    }

    #vxDetailTable tbody tr:hover {
        background: #f7fafc;
    }

    .action-buttons {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .btn-action {
        padding: 0.5rem 1.25rem;
        border-radius: 10px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .chart-container {
        position: relative;
        height: 300px;
        margin-bottom: 1rem;
    }
</style>
@endsection

@section('content')
<div class="main-container container-fluid">
    <!-- Test Header -->
    <div class="test-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center mb-2">
                    <i class="fe fe-activity" style="font-size: 2rem; margin-right: 1rem;"></i>
                    <div>
                        <h1 class="mb-1" style="font-size: 1.75rem; font-weight: 700;">
                            {{ $wellTest->well ?? $mpfmTest->oilString->name ?? 'MPFM Test #' . $mpfmTest->id }}
                        </h1>
                        <div style="opacity: 0.9; font-size: 1rem;">
                            @if($wellTest)
                                {{ $wellTest->client }} • {{ $wellTest->field }}
                                @if($wellTest->wellhead_tower)
                                • {{ $wellTest->wellhead_tower }}
                                @endif
                            @else
                                {{ $mpfmTest->oilField->name }} • {{ $mpfmTest->tower->name }}
                            @endif
                        </div>
                    </div>
                </div>
                <div class="mt-3" style="opacity: 0.85; font-size: 0.9rem;">
                    <i class="fe fe-user mr-2"></i>{{ $wellTest->operator ?? $mpfmTest->user->name ?? 'N/A' }}
                    <span class="ml-3"><i class="fe fe-calendar mr-2"></i>{{ ($wellTest->test_date ?? $mpfmTest->test_date)->format('M d, Y') }}</span>
                    @if($wellTest)
                    <span class="ml-3"><i class="fe fe-clock mr-2"></i>{{ $wellTest->created_at->diffForHumans() }}</span>
                    @endif
                </div>
            </div>
            <div class="col-md-4 text-right">
                <div class="action-buttons justify-content-end">
                    <a href="{{ route('mpfm_tests.index') }}" class="btn btn-light btn-action">
                        <i class="fe fe-arrow-left mr-2"></i>Back to Tests
                    </a>
                    <a href="{{ route('mpfm_tests.string_analysis', $mpfmTest->oil_string_id) }}" class="btn btn-light btn-action">
                        <i class="fe fe-trending-up mr-2"></i>String Analysis
                    </a>
                    <button class="btn btn-light btn-action" onclick="window.print()">
                        <i class="fe fe-printer mr-2"></i>Print
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Debug: Display status -->
    <div style="background: #f0f0f0; border: 1px solid #ddd; padding: 1rem; margin: 1rem 0; border-radius: 0.5rem; font-size: 0.9rem; font-family: monospace;">
        <strong>Debug Info:</strong><br>
        wellTest: {{ $wellTest ? 'Yes (ID: ' . $wellTest->id . ')' : 'No (null)' }}<br>
        metrics count: {{ count($metrics ?? []) }}<br>
        metrics empty: {{ empty($metrics) ? 'true' : 'false' }}<br>
        wellTest && !empty(metrics): {{ ($wellTest && !empty($metrics)) ? 'TRUE (show metrics)' : 'FALSE (hide metrics)' }}<br>
        @if($wellTest)
            wellTest->calculated_metrics exists: {{ isset($wellTest->calculated_metrics) ? 'Yes' : 'No' }}<br>
            wellTest->calculated_metrics count: {{ count($wellTest->calculated_metrics ?? []) }}
        @endif
    </div>

    @if($wellTest && !empty($metrics))
        <!-- Key Production Metrics -->
        <div class="row">
            <div class="col-lg-4 col-md-6">
                <div class="metric-card-modern" style="--header-color-1: #10b981; --header-color-2: #059669;">
                    <div class="metric-card-header">
                        <i class="fe fe-droplet mr-2"></i>Oil Production
                    </div>
                    <div class="metric-grid" style="grid-template-columns: 1fr;">
                        <div class="metric-item-box">
                            <div class="metric-value-large" data-metric="oil_rate">
                                {{ is_numeric($metrics['oil_rate'] ?? null) ? number_format($metrics['oil_rate'], 2) : 'N/A' }}
                            </div>
                            <div class="metric-label-small">Oil Rate</div>
                            <div class="metric-unit">STB/d</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="metric-card-modern" style="--header-color-1: #f59e0b; --header-color-2: #d97706;">
                    <div class="metric-card-header">
                        <i class="fe fe-wind mr-2"></i>Gas Production
                    </div>
                    <div class="metric-grid" style="grid-template-columns: 1fr;">
                        <div class="metric-item-box">
                            <div class="metric-value-large" data-metric="gas_rate">
                                {{ is_numeric($metrics['gas_rate'] ?? null) ? number_format($metrics['gas_rate'], 2) : 'N/A' }}
                            </div>
                            <div class="metric-label-small">Gas Rate</div>
                            <div class="metric-unit">MMScf/d</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="metric-card-modern" style="--header-color-1: #3b82f6; --header-color-2: #2563eb;">
                    <div class="metric-card-header">
                        <i class="fe fe-percent mr-2"></i>Water Production
                    </div>
                    <div class="metric-grid" style="grid-template-columns: 1fr;">
                        <div class="metric-item-box">
                            <div class="metric-value-large" data-metric="water_rate">
                                {{ is_numeric($metrics['water_rate'] ?? null) ? number_format($metrics['water_rate'], 2) : 'N/A' }}
                            </div>
                            <div class="metric-label-small">Water Rate</div>
                            <div class="metric-unit">STB/d</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Production Ratios -->
        <div class="row">
            <div class="col-12">
                <div class="metric-card-modern" style="--header-color-1: #8b5cf6; --header-color-2: #7c3aed;">
                    <div class="metric-card-header">
                        <i class="fe fe-bar-chart-2 mr-2"></i>Production Ratios & Quality
                    </div>
                    <div class="metric-grid">
                        <div class="metric-item-box">
                            <div class="metric-value-large">
                                {{ is_numeric($metrics['gor'] ?? null) ? number_format($metrics['gor'], 0) : 'N/A' }}
                            </div>
                            <div class="metric-label-small">GOR</div>
                            <div class="metric-unit">Scf/STB</div>
                        </div>
                        <div class="metric-item-box">
                            <div class="metric-value-large">
                                {{ is_numeric($metrics['bsw'] ?? null) ? number_format($metrics['bsw'], 1) : 'N/A' }}%
                            </div>
                            <div class="metric-label-small">BSW</div>
                            <div class="metric-unit">Basic Sediment & Water</div>
                        </div>
                        <div class="metric-item-box">
                            <div class="metric-value-large">
                                {{ is_numeric($metrics['wlr'] ?? null) ? number_format($metrics['wlr'], 1) : 'N/A' }}%
                            </div>
                            <div class="metric-label-small">WLR</div>
                            <div class="metric-unit">Water-Liquid Ratio</div>
                        </div>
                        <div class="metric-item-box">
                            <div class="metric-value-large">
                                {{ is_numeric($metrics['gvf'] ?? null) ? number_format($metrics['gvf'], 1) : 'N/A' }}%
                            </div>
                            <div class="metric-label-small">GVF</div>
                            <div class="metric-unit">Gas Void Fraction</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pressure & Temperature -->
        <div class="row">
            <div class="col-lg-6">
                <div class="metric-card-modern" style="--header-color-1: #ec4899; --header-color-2: #db2777;">
                    <div class="metric-card-header">
                        <i class="fe fe-thermometer mr-2"></i>Pressure Metrics
                    </div>
                    <div class="metric-grid">
                        <div class="metric-item-box">
                            <div class="metric-value-large">
                                {{ is_numeric($metrics['max_pressure'] ?? null) ? number_format($metrics['max_pressure'], 1) : 'N/A' }}
                            </div>
                            <div class="metric-label-small">Max Pressure</div>
                            <div class="metric-unit">psi</div>
                        </div>
                        <div class="metric-item-box">
                            <div class="metric-value-large">
                                {{ is_numeric($metrics['avg_pressure'] ?? null) ? number_format($metrics['avg_pressure'], 1) : 'N/A' }}
                            </div>
                            <div class="metric-label-small">Avg Pressure</div>
                            <div class="metric-unit">psi</div>
                        </div>
                        <div class="metric-item-box">
                            <div class="metric-value-large">
                                {{ is_numeric($metrics['min_pressure'] ?? null) ? number_format($metrics['min_pressure'], 1) : 'N/A' }}
                            </div>
                            <div class="metric-label-small">Min Pressure</div>
                            <div class="metric-unit">psi</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="metric-card-modern" style="--header-color-1: #14b8a6; --header-color-2: #0d9488;">
                    <div class="metric-card-header">
                        <i class="fe fe-thermometer mr-2"></i>Temperature Metrics
                    </div>
                    <div class="metric-grid">
                        <div class="metric-item-box">
                            <div class="metric-value-large">
                                {{ is_numeric($metrics['max_temperature'] ?? null) ? number_format($metrics['max_temperature'], 1) : 'N/A' }}
                            </div>
                            <div class="metric-label-small">Max Temp</div>
                            <div class="metric-unit">°F</div>
                        </div>
                        <div class="metric-item-box">
                            <div class="metric-value-large">
                                {{ is_numeric($metrics['avg_temperature'] ?? null) ? number_format($metrics['avg_temperature'], 1) : 'N/A' }}
                            </div>
                            <div class="metric-label-small">Avg Temp</div>
                            <div class="metric-unit">°F</div>
                        </div>
                        <div class="metric-item-box">
                            <div class="metric-value-large">
                                {{ is_numeric($metrics['min_temperature'] ?? null) ? number_format($metrics['min_temperature'], 1) : 'N/A' }}
                            </div>
                            <div class="metric-label-small">Min Temp</div>
                            <div class="metric-unit">°F</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        @if($flowRatesChartData || $gorChartData)
        <div class="row">
            @if($flowRatesChartData)
            <div class="col-lg-6">
                <div class="chart-card">
                    <div class="card-header">
                        <h5 class="card-title"><i class="fe fe-bar-chart-2 mr-2"></i>Flow Rates</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="flowRatesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if($gorChartData)
            <div class="col-lg-6">
                <div class="chart-card">
                    <div class="card-header">
                        <h5 class="card-title"><i class="fe fe-trending-up mr-2"></i>GOR Trend</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="gorChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endif

        @if($pressureTempChartData)
        <div class="row">
            <div class="col-12">
                <div class="chart-card">
                    <div class="card-header">
                        <h5 class="card-title"><i class="fe fe-bar-chart-2 mr-2"></i>Pressure & Temperature</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="pressureTempChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    @endif

    <!-- Test Information -->
    <div class="row">
        <div class="col-12">
            <div class="info-card">
                <div class="card-header">
                    <h5 class="card-title">Test Information</h5>
                </div>
                <div class="info-grid">
                    @if($wellTest)
                        <div class="info-item">
                            <div class="info-label">Client</div>
                            <div class="info-value">{{ $wellTest->client ?? 'N/A' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Field</div>
                            <div class="info-value">{{ $wellTest->field ?? 'N/A' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Well</div>
                            <div class="info-value">{{ $wellTest->well ?? 'N/A' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Tower</div>
                            <div class="info-value">{{ $wellTest->wellhead_tower ?? 'N/A' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Operator</div>
                            <div class="info-value">{{ $wellTest->operator ?? 'N/A' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Test Type</div>
                            <div class="info-value">{{ $wellTest->test_type ?? 'N/A' }}</div>
                        </div>
                    @else
                        <div class="info-item">
                            <div class="info-label">Oil Field</div>
                            <div class="info-value">{{ $mpfmTest->oilField->name ?? 'N/A' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Tower</div>
                            <div class="info-value">{{ $mpfmTest->tower->name ?? 'N/A' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Oil String</div>
                            <div class="info-value">{{ $mpfmTest->oilString->name ?? 'N/A' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Operator</div>
                            <div class="info-value">{{ $mpfmTest->user->name ?? 'N/A' }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Data Table -->
    @if($wellTest && $rows && count($rows) > 0)
    <div class="row">
        <div class="col-12">
            <div class="detailed-table-card">
                <div class="card-header" style="background: transparent; border-bottom: 2px solid #e2e8f0; padding: 1.25rem 1.5rem;">
                    <h5 class="card-title" style="margin: 0;"><i class="fe fe-list mr-2"></i>Detailed Test Data</h5>
                </div>
                <div class="card-body" style="padding: 0; overflow-x: auto;">
                    <table id="vxDetailTable" class="table table-striped mb-0">
                        <thead>
                            <tr>
                                @foreach($columns ?? [] as $column)
                                <th>{{ $column }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(array_slice($rows ?? [], 0, 100) as $row)
                            <tr>
                                @foreach($row as $value)
                                <td>{{ is_numeric($value) ? number_format($value, 2) : $value }}</td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if($flowRatesChartData)
        const flowRatesCtx = document.getElementById('flowRatesChart');
        if (flowRatesCtx) {
            new Chart(flowRatesCtx.getContext('2d'), @json($flowRatesChartData));
        }
        @endif

        @if($gorChartData)
        const gorCtx = document.getElementById('gorChart');
        if (gorCtx) {
            new Chart(gorCtx.getContext('2d'), @json($gorChartData));
        }
        @endif

        @if($pressureTempChartData)
        const pressureTempCtx = document.getElementById('pressureTempChart');
        if (pressureTempCtx) {
            new Chart(pressureTempCtx.getContext('2d'), @json($pressureTempChartData));
        }
        @endif

        // Initialize DataTables if needed
        if (document.getElementById('vxDetailTable')) {
            $('#vxDetailTable').DataTable({
                paging: true,
                pageLength: 25,
                searching: true,
                ordering: true,
                info: true,
                responsive: true
            });
        }
    });
</script>
@endpush
