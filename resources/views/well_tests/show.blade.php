@extends('layouts.master')

@section('title', 'Test Details - ' . ($wellTest->well ?? 'N/A'))

@section('css')
<link href="{{URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css')}}" rel="stylesheet">
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

    .safety-alert {
        border-left: 4px solid #f59e0b;
        background: #fffbeb;
        border-radius: 12px;
        padding: 1rem 1.5rem;
        margin-bottom: 1.5rem;
    }

    .safety-alert-title {
        color: #d97706;
        font-weight: 600;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
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
                            {{ $wellTest->well ?? 'N/A' }}
                        </h1>
                        <div style="opacity: 0.9; font-size: 1rem;">
                            {{ $wellTest->client }} • {{ $wellTest->field }}
                            @if($wellTest->wellhead_tower)
                            • {{ $wellTest->wellhead_tower }}
                            @endif
                        </div>
                    </div>
                </div>
                <div class="mt-3" style="opacity: 0.85; font-size: 0.9rem;">
                    <i class="fe fe-user mr-2"></i>{{ $wellTest->operator ?? 'N/A' }}
                    <span class="ml-3"><i class="fe fe-calendar mr-2"></i>{{ $wellTest->test_date ? $wellTest->test_date->format('M d, Y') : 'N/A' }}</span>
                    <span class="ml-3"><i class="fe fe-clock mr-2"></i>{{ $wellTest->created_at->diffForHumans() }}</span>
                </div>
            </div>
            <div class="col-md-4 text-right">
                <div class="action-buttons justify-content-end">
                    <a href="{{ route('well_tests.index') }}" class="btn btn-light btn-action">
                        <i class="fe fe-arrow-left mr-2"></i>Back to Archive
                    </a>
                    <button class="btn btn-light btn-action" onclick="window.print()">
                        <i class="fe fe-printer mr-2"></i>Print
                    </button>
                </div>
            </div>
        </div>
    </div>

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
                            {{ is_numeric($wellTest->calculated_metrics['oil_rate'] ?? null) ? number_format($wellTest->calculated_metrics['oil_rate'], 2) : 'N/A' }}
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
                            {{ is_numeric($wellTest->calculated_metrics['gas_rate'] ?? null) ? number_format($wellTest->calculated_metrics['gas_rate'], 2) : 'N/A' }}
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
                            {{ is_numeric($wellTest->calculated_metrics['water_rate'] ?? null) ? number_format($wellTest->calculated_metrics['water_rate'], 2) : 'N/A' }}
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
                            {{ is_numeric($wellTest->calculated_metrics['gor'] ?? null) ? number_format($wellTest->calculated_metrics['gor'], 0) : 'N/A' }}
                        </div>
                        <div class="metric-label-small">GOR</div>
                        <div class="metric-unit">Scf/STB</div>
                    </div>
                    <div class="metric-item-box">
                        <div class="metric-value-large">
                            {{ is_numeric($wellTest->calculated_metrics['bsw'] ?? null) ? number_format($wellTest->calculated_metrics['bsw'], 1) : 'N/A' }}%
                        </div>
                        <div class="metric-label-small">BSW</div>
                        <div class="metric-unit">Basic Sediment & Water</div>
                    </div>
                    <div class="metric-item-box">
                        <div class="metric-value-large">
                            {{ is_numeric($wellTest->calculated_metrics['wlr'] ?? null) ? number_format($wellTest->calculated_metrics['wlr'], 1) : 'N/A' }}%
                        </div>
                        <div class="metric-label-small">WLR</div>
                        <div class="metric-unit">Water-Liquid Ratio</div>
                    </div>
                    <div class="metric-item-box">
                        <div class="metric-value-large">
                            {{ is_numeric($wellTest->calculated_metrics['gvf'] ?? null) ? number_format($wellTest->calculated_metrics['gvf'], 1) : 'N/A' }}%
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
                            {{ is_numeric($wellTest->calculated_metrics['max_pressure'] ?? null) ? number_format($wellTest->calculated_metrics['max_pressure'], 1) : 'N/A' }}
                        </div>
                        <div class="metric-label-small">Max Pressure</div>
                        <div class="metric-unit">psi</div>
                    </div>
                    <div class="metric-item-box">
                        <div class="metric-value-large">
                            {{ is_numeric($wellTest->calculated_metrics['avg_pressure'] ?? null) ? number_format($wellTest->calculated_metrics['avg_pressure'], 1) : 'N/A' }}
                        </div>
                        <div class="metric-label-small">Avg Pressure</div>
                        <div class="metric-unit">psi</div>
                    </div>
                    <div class="metric-item-box">
                        <div class="metric-value-large">
                            {{ is_numeric($wellTest->calculated_metrics['min_pressure'] ?? null) ? number_format($wellTest->calculated_metrics['min_pressure'], 1) : 'N/A' }}
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
                            {{ is_numeric($wellTest->calculated_metrics['max_temperature'] ?? null) ? number_format($wellTest->calculated_metrics['max_temperature'], 1) : 'N/A' }}
                        </div>
                        <div class="metric-label-small">Max Temp</div>
                        <div class="metric-unit">°F</div>
                    </div>
                    <div class="metric-item-box">
                        <div class="metric-value-large">
                            {{ is_numeric($wellTest->calculated_metrics['avg_temperature'] ?? null) ? number_format($wellTest->calculated_metrics['avg_temperature'], 1) : 'N/A' }}
                        </div>
                        <div class="metric-label-small">Avg Temp</div>
                        <div class="metric-unit">°F</div>
                    </div>
                    <div class="metric-item-box">
                        <div class="metric-value-large">
                            {{ is_numeric($wellTest->calculated_metrics['min_temperature'] ?? null) ? number_format($wellTest->calculated_metrics['min_temperature'], 1) : 'N/A' }}
                        </div>
                        <div class="metric-label-small">Min Temp</div>
                        <div class="metric-unit">°F</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Test Information -->
    <div class="row">
        <div class="col-12">
            <div class="info-card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fe fe-info mr-2"></i>Test Information</h3>
                </div>
                <div class="info-grid">
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
                        <div class="info-label">Operator</div>
                        <div class="info-value">{{ $wellTest->operator ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Test Date</div>
                        <div class="info-value">{{ $wellTest->test_date ? $wellTest->test_date->format('Y-m-d') : 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Test Type</div>
                        <div class="info-value">{{ ucfirst($wellTest->test_type ?? 'N/A') }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Wellhead Tower</div>
                        <div class="info-value">{{ $wellTest->wellhead_tower ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Wellhead String</div>
                        <div class="info-value">{{ $wellTest->wellhead_string ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Choke Size</div>
                        <div class="info-value">{{ $wellTest->choke_size ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Upstream Choke</div>
                        <div class="info-value">{{ $wellTest->upstream_choke ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Downstream Choke</div>
                        <div class="info-value">{{ $wellTest->downstream_choke ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Test Duration</div>
                        <div class="info-value">
                            @if($wellTest->start_time && $wellTest->end_time)
                                {{ $wellTest->start_time->format('H:i') }} - {{ $wellTest->end_time->format('H:i') }}
                            @else
                                N/A
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Safety Issues -->
    @if(isset($wellTest->calculated_metrics['safety_issues']) && !empty($wellTest->calculated_metrics['safety_issues']))
    <div class="safety-alert">
        <div class="safety-alert-title">
            <i class="fe fe-alert-triangle"></i>
            <span>Safety Issues Detected</span>
        </div>
        <div class="row">
            @if(isset($wellTest->calculated_metrics['safety_issues']['voltage_issues']))
            <div class="col-md-4">
                <strong>Voltage Issues:</strong> {{ count($wellTest->calculated_metrics['safety_issues']['voltage_issues']) }} occurrences
            </div>
            @endif
            @if(isset($wellTest->calculated_metrics['safety_issues']['pressure_issues']))
            <div class="col-md-4">
                <strong>Pressure Issues:</strong> {{ count($wellTest->calculated_metrics['safety_issues']['pressure_issues']) }} occurrences
            </div>
            @endif
            @if(isset($wellTest->calculated_metrics['safety_issues']['temperature_issues']))
            <div class="col-md-4">
                <strong>Temperature Issues:</strong> {{ count($wellTest->calculated_metrics['safety_issues']['temperature_issues']) }} occurrences
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Charts -->
    <div class="row">
        <!-- Production Rates Chart -->
        <div class="col-lg-6">
            <div class="chart-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0"><i class="fe fe-trending-up mr-2"></i>Production Rates Over Time</h3>
                    <div class="d-flex align-items-center" style="gap: 8px;">
                        <select class="form-control form-control-sm unit-selector" data-chart="rates" style="width: 110px; font-size: 0.75rem;">
                            <option value="stb">STB/d</option>
                            <option value="m3">m³/d</option>
                            <option value="bbl">BBL/d</option>
                        </select>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="zoomChart('rates', 'in')" title="Zoom In">
                                <i class="fe fe-zoom-in"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="zoomChart('rates', 'out')" title="Zoom Out">
                                <i class="fe fe-zoom-out"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="resetChart('rates')" title="Reset">
                                <i class="fe fe-rotate-ccw"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="ratesChart" height="280"></canvas>
                </div>
            </div>
        </div>

        <!-- GOR Chart -->
        <div class="col-lg-6">
            <div class="chart-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0"><i class="fe fe-activity mr-2"></i>Gas-Oil Ratio (GOR)</h3>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="zoomChart('gor', 'in')" title="Zoom In">
                            <i class="fe fe-zoom-in"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="zoomChart('gor', 'out')" title="Zoom Out">
                            <i class="fe fe-zoom-out"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="resetChart('gor')" title="Reset">
                            <i class="fe fe-rotate-ccw"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="gorChart" height="280"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- BSW Chart -->
        <div class="col-lg-6">
            <div class="chart-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0"><i class="fe fe-droplet mr-2"></i>Basic Sediment & Water (BSW) Trend</h3>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="zoomChart('bsw', 'in')" title="Zoom In">
                            <i class="fe fe-zoom-in"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="zoomChart('bsw', 'out')" title="Zoom Out">
                            <i class="fe fe-zoom-out"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="resetChart('bsw')" title="Reset">
                            <i class="fe fe-rotate-ccw"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="bswChart" height="280"></canvas>
                </div>
            </div>
        </div>

        <!-- WLR Chart -->
        <div class="col-lg-6">
            <div class="chart-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0"><i class="fe fe-percent mr-2"></i>Water-Liquid Ratio (WLR) Trend</h3>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="zoomChart('wlr', 'in')" title="Zoom In">
                            <i class="fe fe-zoom-in"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="zoomChart('wlr', 'out')" title="Zoom Out">
                            <i class="fe fe-zoom-out"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="resetChart('wlr')" title="Reset">
                            <i class="fe fe-rotate-ccw"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="wlrChart" height="280"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Pressure Chart -->
        <div class="col-lg-6">
            <div class="chart-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0"><i class="fe fe-thermometer mr-2"></i>Pressure Trend Over Time</h3>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="zoomChart('pressure', 'in')" title="Zoom In">
                            <i class="fe fe-zoom-in"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="zoomChart('pressure', 'out')" title="Zoom Out">
                            <i class="fe fe-zoom-out"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="resetChart('pressure')" title="Reset">
                            <i class="fe fe-rotate-ccw"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="pressureChart" height="280"></canvas>
                </div>
            </div>
        </div>

        <!-- Temperature Chart -->
        <div class="col-lg-6">
            <div class="chart-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0"><i class="fe fe-thermometer mr-2"></i>Temperature Trend Over Time</h3>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="zoomChart('temp', 'in')" title="Zoom In">
                            <i class="fe fe-zoom-in"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="zoomChart('temp', 'out')" title="Zoom Out">
                            <i class="fe fe-zoom-out"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="resetChart('temp')" title="Reset">
                            <i class="fe fe-rotate-ccw"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="tempChart" height="280"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- GVF Chart -->
        <div class="col-lg-6">
            <div class="chart-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0"><i class="fe fe-wind mr-2"></i>Gas Void Fraction (GVF) Trend</h3>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="zoomChart('gvf', 'in')" title="Zoom In">
                            <i class="fe fe-zoom-in"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="zoomChart('gvf', 'out')" title="Zoom Out">
                            <i class="fe fe-zoom-out"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="resetChart('gvf')" title="Reset">
                            <i class="fe fe-rotate-ccw"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="gvfChart" height="280"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Cumulative Volumes -->
    @if(isset($wellTest->calculated_metrics['cumulative_oil_volume']) || isset($wellTest->calculated_metrics['cumulative_gas_volume']))
    <div class="row">
        <div class="col-12">
            <div class="metric-card-modern" style="--header-color-1: #06b6d4; --header-color-2: #0891b2;">
                <div class="metric-card-header">
                    <i class="fe fe-database mr-2"></i>Cumulative Volumes
                </div>
                <div class="metric-grid">
                    @if(isset($wellTest->calculated_metrics['cumulative_oil_volume']))
                    <div class="metric-item-box">
                        <div class="metric-value-large">
                            {{ number_format($wellTest->calculated_metrics['cumulative_oil_volume'], 0) }}
                        </div>
                        <div class="metric-label-small">Cumulative Oil</div>
                        <div class="metric-unit">bbl</div>
                    </div>
                    @endif
                    @if(isset($wellTest->calculated_metrics['cumulative_water_volume']))
                    <div class="metric-item-box">
                        <div class="metric-value-large">
                            {{ number_format($wellTest->calculated_metrics['cumulative_water_volume'], 0) }}
                        </div>
                        <div class="metric-label-small">Cumulative Water</div>
                        <div class="metric-unit">bbl</div>
                    </div>
                    @endif
                    @if(isset($wellTest->calculated_metrics['cumulative_gas_volume']))
                    <div class="metric-item-box">
                        <div class="metric-value-large">
                            {{ number_format($wellTest->calculated_metrics['cumulative_gas_volume'], 0) }}
                        </div>
                        <div class="metric-label-small">Cumulative Gas</div>
                        <div class="metric-unit">Mcf</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Detailed Log Table -->
    <div class="detailed-table-card">
        <div class="card-header bg-light">
            <h3 class="card-title mb-0"><i class="fe fe-list mr-2"></i>Complete VX Log Data</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                <table class="table table-sm mb-0" id="vxDetailTable">
                    <thead>
                        <tr>
                            @foreach($wellTest->test_data['columns'] ?? [] as $col)
                                <th style="min-width: 120px;">{{ $col }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($wellTest->test_data['rows'] ?? [] as $row)
                            <tr>
                                @foreach($wellTest->test_data['columns'] ?? [] as $col)
                                    @php
                                    $value = $row[$col] ?? '';
                                    $class = '';
                                    if(is_numeric($value)){
                                        if(stripos($col, 'WHP') !== false && $value < 400) $class='table-danger';
                                        if(stripos($col, 'Temp') !== false && $value > 200) $class='table-warning';
                                        if(stripos($col, 'Press') !== false && $value < 200) $class='table-danger';
                                        if(stripos($col, 'Voltage') !== false && ($value < 100 || $value > 250)) $class='table-warning';
                                    }
                                    @endphp
                                    <td class="{{ $class }}">{{ $value }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/hammerjs@2.0.8"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@2.0.1/dist/chartjs-plugin-zoom.min.js"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js')}}"></script>
<script>
$(document).ready(function() {
    // Initialize DataTable for detailed log
    $('#vxDetailTable').DataTable({
        scrollX: true,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        dom: 'Bfrtip',
        buttons: ['copy', 'excel', 'csv']
    });

    // Store all chart instances globally
    window.allCharts = [];

    // Helper to add chart to global array
    function registerChart(chart) {
        if (chart) window.allCharts.push(chart);
        return chart;
    }

    // Unit conversion factors (base: STB)
    const unitConversions = {
        stb: { factor: 1, label: 'STB/d', gasLabel: 'MMScf/d' },
        m3: { factor: 0.158987, label: 'm³/d', gasLabel: 'Mm³/d' },
        bbl: { factor: 1, label: 'BBL/d', gasLabel: 'MMScf/d' }
    };
    let currentUnit = 'stb';

    // Get test data
    const rows = @json($wellTest->test_data['rows'] ?? []);
    const columns = @json($wellTest->test_data['columns'] ?? []);

    console.log('Available columns in log file:', columns);

    // Helper function to extract column data with flexible name matching
    function extractColumnData(...possibleNames) {
        let foundColumn = null;

        // Try exact match first
        for (let name of possibleNames) {
            if (columns.includes(name)) {
                foundColumn = name;
                break;
            }
        }

        // If no exact match, try case-insensitive and partial match
        if (!foundColumn) {
            for (let name of possibleNames) {
                foundColumn = columns.find(col =>
                    col.toLowerCase().includes(name.toLowerCase()) ||
                    name.toLowerCase().includes(col.toLowerCase())
                );
                if (foundColumn) break;
            }
        }

        if (!foundColumn) return [];

        return rows.map(row => {
            const value = row[foundColumn];
            return value !== undefined && value !== null && value !== '' ? parseFloat(value) : null;
        }).filter(v => v !== null);
    }

    // Extract all data columns
    const oilRateData = extractColumnData('Oil Rate', 'OilRate', 'OILR', 'Oil_Rate', 'Oil Production', 'OILC', 'qo_lc[bbl/d]', 'qo');
    const waterRateData = extractColumnData('Water Rate', 'WaterRate', 'WATR', 'Water_Rate', 'Water Production', 'WATC', 'qw_lc[bbl/d]', 'qw');
    const gasRateData = extractColumnData('Gas Rate', 'GasRate', 'GASR', 'Gas_Rate', 'Gas Production', 'GASC', 'qg_lc[Mcf/d]', 'qg');
    const gorData = extractColumnData('GOR', 'Gas-Oil Ratio', 'Gas Oil Ratio', 'GORC');
    const bswData = extractColumnData('BSW', 'BS&W', 'Basic Sediment & Water', 'BS_W', 'BSWC');
    const wlrData = extractColumnData('WLR', 'Water-Liquid Ratio', 'Water Liquid Ratio', 'WLR%', 'WLRC');
    const pressureData = extractColumnData('Pressure', 'WHP', 'Press', 'Wellhead Pressure', 'P', 'WHPC');
    const tempData = extractColumnData('Temperature', 'Temp', 'WHT', 'Wellhead Temperature', 'T', 'WHTC');
    const gvfData = extractColumnData('GVF', 'Gas Void Fraction', 'Gas_Void_Fraction', 'GVFC');

    // Debug: Log extracted data
    console.log('Oil Rate Data:', oilRateData.length, 'points');
    console.log('Water Rate Data:', waterRateData.length, 'points');
    console.log('Gas Rate Data:', gasRateData.length, 'points');

    // Create time labels
    const labels = rows.map((_, i) => `Point ${i + 1}`);

    // Common chart options
    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
            intersect: false,
            mode: 'index'
        },
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    usePointStyle: true,
                    padding: 15,
                    font: {
                        size: 11,
                        weight: '500'
                    }
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                cornerRadius: 8,
                titleFont: {
                    size: 13,
                    weight: '600'
                },
                bodyFont: {
                    size: 12
                }
            },
            zoom: {
                zoom: {
                    wheel: {
                        enabled: true,
                        speed: 0.1
                    },
                    pinch: {
                        enabled: true
                    },
                    mode: 'xy'
                },
                pan: {
                    enabled: true,
                    mode: 'xy'
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)',
                    drawBorder: false
                },
                ticks: {
                    font: {
                        size: 11
                    },
                    padding: 8
                }
            },
            x: {
                grid: {
                    display: false
                },
                ticks: {
                    font: {
                        size: 11
                    },
                    maxRotation: 45,
                    minRotation: 45
                }
            }
        }
    };

    // --- Production Rates Chart ---
    const ratesCtx = document.getElementById('ratesChart');
    if (ratesCtx && (oilRateData.length > 0 || waterRateData.length > 0 || gasRateData.length > 0)) {
        const ratesData = {
            labels: labels,
            datasets: []
        };

        if (oilRateData.length > 0) {
            ratesData.datasets.push({
                label: 'Oil Rate (STB/d)',
                data: [...oilRateData],
                originalData: [...oilRateData],
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                borderWidth: 2,
                tension: 0.3,
                fill: true
            });
        }

        if (waterRateData.length > 0) {
            ratesData.datasets.push({
                label: 'Water Rate (STB/d)',
                data: [...waterRateData],
                originalData: [...waterRateData],
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 2,
                tension: 0.3,
                fill: true
            });
        }

        if (gasRateData.length > 0) {
            ratesData.datasets.push({
                label: 'Gas Rate (MMScf/d)',
                data: [...gasRateData],
                originalData: [...gasRateData],
                borderColor: '#f59e0b',
                backgroundColor: 'rgba(245, 158, 11, 0.1)',
                borderWidth: 2,
                tension: 0.3,
                fill: true
            });
        }

        const ratesChart = new Chart(ratesCtx.getContext('2d'), {
            type: 'line',
            data: ratesData,
            options: {
                ...commonOptions,
                scales: {
                    ...commonOptions.scales,
                    x: {
                        ...commonOptions.scales.x,
                        title: {
                            display: true,
                            text: 'Time',
                            font: {
                                size: 12,
                                weight: '600'
                            }
                        }
                    },
                    y: {
                        ...commonOptions.scales.y,
                        title: {
                            display: true,
                            text: 'Rate',
                            font: {
                                size: 12,
                                weight: '600'
                            }
                        }
                    }
                }
            }
        });
        registerChart(ratesChart);
    } else {
        console.warn('No production rates data found. Available columns:', columns);
        document.getElementById('ratesChart').parentElement.innerHTML = '<p class="text-muted text-center py-5">No production rates data available in this log file</p>';
    }

    // --- GOR Chart ---
    const gorCtx = document.getElementById('gorChart');
    if (gorCtx && gorData.length > 0) {
        const gorChart = new Chart(gorCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'GOR (Scf/STB)',
                    data: gorData,
                    borderColor: '#8b5cf6',
                    backgroundColor: 'rgba(139, 92, 246, 0.1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                ...commonOptions,
                scales: {
                    ...commonOptions.scales,
                    x: {
                        ...commonOptions.scales.x,
                        title: {
                            display: true,
                            text: 'Time',
                            font: {
                                size: 12,
                                weight: '600'
                            }
                        }
                    },
                    y: {
                        ...commonOptions.scales.y,
                        title: {
                            display: true,
                            text: 'GOR (Scf/STB)',
                            font: {
                                size: 12,
                                weight: '600'
                            }
                        }
                    }
                }
            }
        });
        registerChart(gorChart);
    } else {
        document.getElementById('gorChart').parentElement.innerHTML = '<p class="text-muted text-center py-5">No GOR data available in this log file</p>';
    }

    // --- BSW Chart ---
    const bswCtx = document.getElementById('bswChart');
    if (bswCtx && bswData.length > 0) {
        const bswChart = new Chart(bswCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'BSW (%)',
                    data: bswData,
                    borderColor: '#ec4899',
                    backgroundColor: 'rgba(236, 72, 153, 0.1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                ...commonOptions,
                scales: {
                    ...commonOptions.scales,
                    x: {
                        ...commonOptions.scales.x,
                        title: {
                            display: true,
                            text: 'Time',
                            font: {
                                size: 12,
                                weight: '600'
                            }
                        }
                    },
                    y: {
                        ...commonOptions.scales.y,
                        title: {
                            display: true,
                            text: 'BSW (%)',
                            font: {
                                size: 12,
                                weight: '600'
                            }
                        }
                    }
                }
            }
        });
        registerChart(bswChart);
    } else {
        document.getElementById('bswChart').parentElement.innerHTML = '<p class="text-muted text-center py-5">No BSW data available in this log file</p>';
    }

    // --- WLR Chart ---
    const wlrCtx = document.getElementById('wlrChart');
    if (wlrCtx && wlrData.length > 0) {
        const wlrChart = new Chart(wlrCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'WLR (%)',
                    data: wlrData,
                    borderColor: '#06b6d4',
                    backgroundColor: 'rgba(6, 182, 212, 0.1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                ...commonOptions,
                scales: {
                    ...commonOptions.scales,
                    x: {
                        ...commonOptions.scales.x,
                        title: {
                            display: true,
                            text: 'Time',
                            font: {
                                size: 12,
                                weight: '600'
                            }
                        }
                    },
                    y: {
                        ...commonOptions.scales.y,
                        title: {
                            display: true,
                            text: 'WLR (%)',
                            font: {
                                size: 12,
                                weight: '600'
                            }
                        }
                    }
                }
            }
        });
        registerChart(wlrChart);
    } else {
        document.getElementById('wlrChart').parentElement.innerHTML = '<p class="text-muted text-center py-5">No WLR data available in this log file</p>';
    }

    // --- Pressure Chart ---
    const pressureCtx = document.getElementById('pressureChart');
    if (pressureCtx && pressureData.length > 0) {
        const pressureChart = new Chart(pressureCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Pressure (psi)',
                    data: pressureData,
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                ...commonOptions,
                scales: {
                    ...commonOptions.scales,
                    x: {
                        ...commonOptions.scales.x,
                        title: {
                            display: true,
                            text: 'Time',
                            font: {
                                size: 12,
                                weight: '600'
                            }
                        }
                    },
                    y: {
                        ...commonOptions.scales.y,
                        title: {
                            display: true,
                            text: 'Pressure (psi)',
                            font: {
                                size: 12,
                                weight: '600'
                            }
                        }
                    }
                }
            }
        });
        registerChart(pressureChart);
    } else {
        document.getElementById('pressureChart').parentElement.innerHTML = '<p class="text-muted text-center py-5">No pressure data available in this log file</p>';
    }

    // --- Temperature Chart ---
    const tempCtx = document.getElementById('tempChart');
    if (tempCtx && tempData.length > 0) {
        const tempChart = new Chart(tempCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Temperature (°F)',
                    data: tempData,
                    borderColor: '#14b8a6',
                    backgroundColor: 'rgba(20, 184, 166, 0.1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                ...commonOptions,
                scales: {
                    ...commonOptions.scales,
                    x: {
                        ...commonOptions.scales.x,
                        title: {
                            display: true,
                            text: 'Time',
                            font: {
                                size: 12,
                                weight: '600'
                            }
                        }
                    },
                    y: {
                        ...commonOptions.scales.y,
                        title: {
                            display: true,
                            text: 'Temperature (°F)',
                            font: {
                                size: 12,
                                weight: '600'
                            }
                        }
                    }
                }
            }
        });
        registerChart(tempChart);
    } else {
        document.getElementById('tempChart').parentElement.innerHTML = '<p class="text-muted text-center py-5">No temperature data available in this log file</p>';
    }

    // --- GVF Chart ---
    const gvfCtx = document.getElementById('gvfChart');
    if (gvfCtx && gvfData.length > 0) {
        const gvfChart = new Chart(gvfCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'GVF (%)',
                    data: gvfData,
                    borderColor: '#a855f7',
                    backgroundColor: 'rgba(168, 85, 247, 0.1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                ...commonOptions,
                scales: {
                    ...commonOptions.scales,
                    x: {
                        ...commonOptions.scales.x,
                        title: {
                            display: true,
                            text: 'Time',
                            font: {
                                size: 12,
                                weight: '600'
                            }
                        }
                    },
                    y: {
                        ...commonOptions.scales.y,
                        title: {
                            display: true,
                            text: 'GVF (%)',
                            font: {
                                size: 12,
                                weight: '600'
                            }
                        }
                    }
                }
            }
        });
        registerChart(gvfChart);
    } else {
        document.getElementById('gvfChart').parentElement.innerHTML = '<p class="text-muted text-center py-5">No GVF data available in this log file</p>';
    }

    // Store charts by name for individual control - AFTER all charts are created
    window.chartInstances = {
        rates: window.allCharts[0] || null,
        gor: window.allCharts[1] || null,
        bsw: window.allCharts[2] || null,
        wlr: window.allCharts[3] || null,
        pressure: window.allCharts[4] || null,
        temp: window.allCharts[5] || null,
        gvf: window.allCharts[6] || null
    };

    console.log('Charts registered:', window.allCharts.length, 'Chart instances:', Object.keys(window.chartInstances).filter(k => window.chartInstances[k]).length);

    // Individual zoom control functions
    window.zoomChart = function(chartName, direction) {
        const chart = window.chartInstances[chartName];
        if (!chart || !chart.zoom) return;

        if (direction === 'in') {
            chart.zoom(1.2);
        } else if (direction === 'out') {
            chart.zoom(0.8);
        }
    };

    window.resetChart = function(chartName) {
        const chart = window.chartInstances[chartName];
        if (!chart || !chart.resetZoom) return;
        chart.resetZoom();
    };

    // Unit conversion for metric cards and charts
    const originalOilRate = {{ $wellTest->calculated_metrics['oil_rate'] ?? 0 }};
    const originalWaterRate = {{ $wellTest->calculated_metrics['water_rate'] ?? 0 }};
    const originalGasRate = {{ $wellTest->calculated_metrics['gas_rate'] ?? 0 }};

    // Handle unit selector changes for Production Rates chart
    $('.unit-selector').on('change', function() {
        const chartName = $(this).data('chart');
        const selectedUnit = this.value;
        const conversion = unitConversions[selectedUnit];
        const chart = window.chartInstances[chartName];

        if (!chart) return;

        // Update metric cards
        let oilValue = originalOilRate * conversion.factor;
        let waterValue = originalWaterRate * conversion.factor;
        let gasValue = selectedUnit === 'm3' ? originalGasRate * 28.317 : originalGasRate;

        $('[data-metric="oil_rate"]').text(oilValue.toFixed(2));
        $('.metric-unit').eq(0).text(conversion.label);

        $('[data-metric="gas_rate"]').text(gasValue.toFixed(2));
        $('.metric-unit').eq(1).text(conversion.gasLabel);

        $('[data-metric="water_rate"]').text(waterValue.toFixed(2));
        $('.metric-unit').eq(2).text(conversion.label);

        // Update chart
        chart.data.datasets.forEach(dataset => {
            const isProductionRate = dataset.label && (
                dataset.label.includes('Oil') ||
                dataset.label.includes('Water') ||
                dataset.label.includes('Gas Rate')
            );

            if (isProductionRate && dataset.originalData) {
                dataset.data = dataset.originalData.map(val => {
                    if (dataset.label.includes('Gas')) {
                        return selectedUnit === 'm3' ? val * 28.317 : val;
                    }
                    return val * conversion.factor;
                });

                // Update label
                if (dataset.label.includes('Gas')) {
                    dataset.label = `Gas Rate (${conversion.gasLabel})`;
                } else if (dataset.label.includes('Oil')) {
                    dataset.label = `Oil Rate (${conversion.label})`;
                } else if (dataset.label.includes('Water')) {
                    dataset.label = `Water Rate (${conversion.label})`;
                }
            }
        });
        chart.update();
    });
});
</script>
@endsection
