@extends('layouts.master')

@section('title', 'MPFM Test Preview - ' . ($filename ?? 'Unknown File'))

@push('styles')
<style>
    .summary-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    .quality-indicator {
        position: absolute;
        top: 10px;
        right: 10px;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .quality-excellent { background: #28a745; color: white; }
    .quality-good { background: #17a2b8; color: white; }
    .quality-fair { background: #ffc107; color: #212529; }
    .quality-poor { background: #dc3545; color: white; }

    .kpi-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }

    .kpi-card:hover {
        transform: translateY(-5px);
    }

    .kpi-value {
        font-size: 2rem;
        font-weight: 700;
        color: #4e73df;
        margin-bottom: 0.5rem;
    }

    .kpi-label {
        color: #6c757d;
        font-size: 0.9rem;
        font-weight: 500;
    }

    .chart-container {
        position: relative;
        height: 400px;
        margin-bottom: 2rem;
        background: rgba(255, 255, 255, 0.95);
        border-radius: 12px;
        padding: 1rem;
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }

    .data-table {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }

    .data-table thead th {
        background: linear-gradient(135deg, #4e73df 0%, #2e59d9 100%);
        color: white;
        border: none;
        padding: 1rem;
        font-weight: 600;
    }

    .data-table tbody td {
        border: none;
        padding: 0.75rem 1rem;
        vertical-align: middle;
    }

    .data-table tbody tr:nth-child(even) {
        background: rgba(78, 115, 223, 0.05);
    }

    .data-table tbody tr:hover {
        background: rgba(78, 115, 223, 0.1);
    }

    .log-preview {
        background: #2d3748;
        color: #e2e8f0;
        border-radius: 8px;
        padding: 1rem;
        font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
        font-size: 0.875rem;
        max-height: 300px;
        overflow-y: auto;
        margin-top: 1rem;
    }

    .status-badge {
        padding: 0.375rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-good {
        background: #d4edda;
        color: #155724;
    }

    .status-warning {
        background: #fff3cd;
        color: #856404;
    }

    .status-error {
        background: #f8d7da;
        color: #721c24;
    }

    .action-buttons {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 12px;
        padding: 2rem;
        margin-top: 2rem;
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }

    .btn-save {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border: none;
        border-radius: 25px;
        padding: 12px 30px;
        font-size: 1.1rem;
        font-weight: 600;
        color: white;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
    }

    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
        color: white;
    }

    .tab-content {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 12px;
        padding: 1.5rem;
        margin-top: 1rem;
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }

    .nav-tabs .nav-link {
        border: none;
        border-radius: 8px 8px 0 0;
        color: #6c757d;
        font-weight: 500;
        padding: 0.75rem 1.5rem;
        margin-right: 0.25rem;
        transition: all 0.3s ease;
    }

    .nav-tabs .nav-link.active {
        background: linear-gradient(135deg, #4e73df 0%, #2e59d9 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(78, 115, 223, 0.3);
    }

    .nav-tabs .nav-link:hover:not(.active) {
        background: rgba(78, 115, 223, 0.1);
        color: #4e73df;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-leftheader">
            <h4 class="page-title">MPFM Test Preview</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('mpfm_tests.index') }}">MPFM Tests</a></li>
                <li class="breadcrumb-item"><a href="{{ route('mpfm_tests.upload') }}">Upload</a></li>
                <li class="breadcrumb-item active">Preview</li>
            </ol>
        </div>
        <div class="page-rightheader">
            <a href="{{ route('mpfm_tests.upload') }}" class="btn btn-outline-secondary">
                <i class="fe fe-arrow-left"></i> Back to Upload
            </a>
        </div>
    </div>

    <!-- Summary Card -->
    <div class="summary-card">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="mb-2">
                    <i class="fe fe-file-text mr-2"></i>Log File: {{ $filename ?? 'Unknown File' }}
                </h2>
                <p class="mb-2 opacity-75">
                    <i class="fe fe-calendar mr-1"></i>Test Date: {{ \Carbon\Carbon::parse($summary['test_date'] ?? now())->format('F j, Y') }} |
                    <i class="fe fe-clock mr-1"></i>Shift: {{ ucfirst($summary['shift'] ?? 'morning') }} |
                    <i class="fe fe-trending-up mr-1"></i>Duration: {{ $summary['duration_minutes'] ? $summary['duration_minutes'] . ' minutes' : 'N/A' }}
                </p>
                <div class="d-flex gap-3">
                    <small class="opacity-75">
                        <i class="fe fe-database mr-1"></i>{{ $summary['total_rows'] ?? 0 }} data points
                    </small>
                    <small class="opacity-75">
                        <i class="fe fe-hard-drive mr-1"></i>{{ isset($file_size) ? number_format($file_size / 1024, 1) . ' KB' : 'N/A' }}
                    </small>
                    @if(isset($data_quality['score']))
                        <small class="opacity-75">
                            <i class="fe fe-bar-chart-2 mr-1"></i>Data Quality: {{ number_format($data_quality['score'], 0) }}%
                        </small>
                    @endif
                </div>
            </div>
            <div class="col-md-4 text-end">
                <!-- Data Quality Indicator -->
                @if(isset($data_quality['score']))
                    <div class="quality-indicator
                        @if($data_quality['score'] >= 90) quality-excellent
                        @elseif($data_quality['score'] >= 75) quality-good
                        @elseif($data_quality['score'] >= 50) quality-fair
                        @else quality-poor @endif">
                        @if($data_quality['score'] >= 90)
                            <i class="fe fe-check-circle mr-1"></i>Excellent
                        @elseif($data_quality['score'] >= 75)
                            <i class="fe fe-thumbs-up mr-1"></i>Good
                        @elseif($data_quality['score'] >= 50)
                            <i class="fe fe-alert-triangle mr-1"></i>Fair
                        @else
                            <i class="fe fe-alert-circle mr-1"></i>Poor
                        @endif
                        {{ number_format($data_quality['score'], 0) }}%
                    </div>
                @endif

                <!-- Key Metrics -->
                <div class="d-flex justify-content-end gap-4 mt-3">
                    <div class="text-center">
                        <div class="h4 mb-0 text-white">
                            {{ isset($mpfm_data['oil_rate']) && $mpfm_data['oil_rate'] ? number_format($mpfm_data['oil_rate'], 1) : 'N/A' }}
                        </div>
                        <small class="opacity-75">Oil Rate<br>(STB/d)</small>
                    </div>
                    <div class="text-center">
                        <div class="h4 mb-0 text-white">
                            {{ isset($mpfm_data['gas_rate']) && $mpfm_data['gas_rate'] ? number_format($mpfm_data['gas_rate'], 1) : 'N/A' }}
                        </div>
                        <small class="opacity-75">Gas Rate<br>(Mscf/d)</small>
                    </div>
                    <div class="text-center">
                        <div class="h4 mb-0 text-white">
                            {{ isset($mpfm_data['gor']) && $mpfm_data['gor'] ? number_format($mpfm_data['gor'], 1) : 'N/A' }}
                        </div>
                        <small class="opacity-75">GOR<br>(scf/STB)</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Validation Messages -->
        @if(isset($validation) && (!$validation['is_valid'] || !empty($validation['warnings'])))
            <div class="mt-3">
                @if(!$validation['is_valid'])
                    <div class="alert alert-danger mb-2">
                        <strong><i class="fe fe-alert-triangle mr-1"></i>Validation Errors:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($validation['errors'] as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(!empty($validation['warnings']))
                    <div class="alert alert-warning">
                        <strong><i class="fe fe-info mr-1"></i>Warnings:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($validation['warnings'] as $warning)
                                <li>{{ $warning }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        @endif

        <!-- Data Quality Issues -->
        @if(isset($data_quality['issues']) && !empty($data_quality['issues']))
            <div class="mt-3">
                <div class="alert alert-info">
                    <strong><i class="fe fe-info mr-1"></i>Data Quality Notes:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($data_quality['issues'] as $issue)
                            <li>{{ $issue }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
    </div>

    <!-- Navigation Tabs -->
    <ul class="nav nav-tabs" id="previewTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="kpis-tab" data-bs-toggle="tab" data-bs-target="#kpis" type="button" role="tab">
                <i class="fe fe-trending-up mr-2"></i>KPI Analysis
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="charts-tab" data-bs-toggle="tab" data-bs-target="#charts" type="button" role="tab">
                <i class="fe fe-bar-chart-2 mr-2"></i>Visual Charts
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="data-tab" data-bs-toggle="tab" data-bs-target="#data" type="button" role="tab">
                <i class="fe fe-table mr-2"></i>Raw Data
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="log-tab" data-bs-toggle="tab" data-bs-target="#log" type="button" role="tab">
                <i class="fe fe-file-text mr-2"></i>Log Preview
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="previewTabsContent">
        <!-- KPIs Tab -->
        <div class="tab-pane fade show active" id="kpis" role="tabpanel">
            <div class="row">
                <!-- Production Rates -->
                <div class="col-md-12">
                    <h5 class="mb-4 text-primary">
                        <i class="fe fe-trending-up mr-2"></i>Production Rates & Flow Parameters
                    </h5>
                </div>

                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="kpi-card">
                        <div class="kpi-value">
                            {{ isset($mpfm_data['oil_rate']) && $mpfm_data['oil_rate'] ? number_format($mpfm_data['oil_rate'], 1) : 'N/A' }}
                        </div>
                        <div class="kpi-label">Oil Rate</div>
                        <div class="mt-1">
                            <small class="text-muted d-block">STB/d (Stock Tank Barrels)</small>
                            <small class="text-info">Average production rate</small>
                        </div>
                        @if(isset($averages['oil_rate_min']) && isset($averages['oil_rate_max']))
                            <div class="mt-2">
                                <small class="text-muted">
                                    Range: {{ number_format($averages['oil_rate_min'], 1) }} - {{ number_format($averages['oil_rate_max'], 1) }}
                                </small>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="kpi-card">
                        <div class="kpi-value">
                            {{ isset($mpfm_data['gas_rate']) && $mpfm_data['gas_rate'] ? number_format($mpfm_data['gas_rate'], 1) : 'N/A' }}
                        </div>
                        <div class="kpi-label">Gas Rate</div>
                        <div class="mt-1">
                            <small class="text-muted d-block">Mscf/d (Thousand Std Cu Ft)</small>
                            <small class="text-info">Associated gas production</small>
                        </div>
                        @if(isset($averages['gas_rate_min']) && isset($averages['gas_rate_max']))
                            <div class="mt-2">
                                <small class="text-muted">
                                    Range: {{ number_format($averages['gas_rate_min'], 1) }} - {{ number_format($averages['gas_rate_max'], 1) }}
                                </small>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="kpi-card">
                        <div class="kpi-value">
                            {{ isset($mpfm_data['water_rate']) && $mpfm_data['water_rate'] ? number_format($mpfm_data['water_rate'], 1) : 'N/A' }}
                        </div>
                        <div class="kpi-label">Water Rate</div>
                        <div class="mt-1">
                            <small class="text-muted d-block">STB/d (Stock Tank Barrels)</small>
                            <small class="text-info">Produced water volume</small>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="kpi-card">
                        <div class="kpi-value">
                            {{ isset($mpfm_data['pressure']) && $mpfm_data['pressure'] ? number_format($mpfm_data['pressure'], 1) : 'N/A' }}
                        </div>
                        <div class="kpi-label">Pressure</div>
                        <div class="mt-1">
                            <small class="text-muted d-block">psi (Pounds per Sq Inch)</small>
                            <small class="text-info">Operating pressure</small>
                        </div>
                    </div>
                </div>

                <!-- Quality Parameters -->
                <div class="col-md-12 mt-4">
                    <h5 class="mb-4 text-success">
                        <i class="fe fe-target mr-2"></i>Quality Parameters & Ratios
                    </h5>
                </div>

                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="kpi-card">
                        <div class="kpi-value">
                            {{ isset($mpfm_data['gor']) && $mpfm_data['gor'] ? number_format($mpfm_data['gor'], 1) : 'N/A' }}
                        </div>
                        <div class="kpi-label">Gas-Oil Ratio</div>
                        <div class="mt-1">
                            <small class="text-muted d-block">scf/STB (Std Cu Ft/Stock Tank Barrel)</small>
                            <small class="text-info">Gas dissolved in oil</small>
                        </div>
                        @if(isset($mpfm_data['gor']))
                            <div class="mt-2">
                                @php
                                    $gorStatus = $mpfm_data['gor'] > 2000 ? 'high' : ($mpfm_data['gor'] < 500 ? 'low' : 'normal');
                                @endphp
                                <span class="badge bg-{{ $gorStatus === 'high' ? 'warning' : ($gorStatus === 'low' ? 'info' : 'success') }}">
                                    {{ ucfirst($gorStatus) }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="kpi-card">
                        <div class="kpi-value">
                            {{ isset($mpfm_data['bsw']) && $mpfm_data['bsw'] ? number_format($mpfm_data['bsw'], 1) : 'N/A' }}%
                        </div>
                        <div class="kpi-label">Basic Sediment & Water</div>
                        <div class="mt-1">
                            <small class="text-muted d-block">BSW (%)</small>
                            <small class="text-info">Water cut percentage</small>
                        </div>
                        @if(isset($mpfm_data['bsw']))
                            <div class="mt-2">
                                @php
                                    $bswStatus = $mpfm_data['bsw'] > 80 ? 'high' : ($mpfm_data['bsw'] < 20 ? 'low' : 'normal');
                                @endphp
                                <span class="badge bg-{{ $bswStatus === 'high' ? 'danger' : ($bswStatus === 'low' ? 'success' : 'primary') }}">
                                    {{ ucfirst($bswStatus) }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="kpi-card">
                        <div class="kpi-value">
                            {{ isset($mpfm_data['wlr']) && $mpfm_data['wlr'] ? number_format($mpfm_data['wlr'], 2) : 'N/A' }}
                        </div>
                        <div class="kpi-label">Water-Liquid Ratio</div>
                        <div class="mt-1">
                            <small class="text-muted d-block">WLR (dimensionless)</small>
                            <small class="text-info">Water to total liquid ratio</small>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="kpi-card">
                        <div class="kpi-value">
                            {{ isset($mpfm_data['temperature']) && $mpfm_data['temperature'] ? number_format($mpfm_data['temperature'], 1) : 'N/A' }}
                        </div>
                        <div class="kpi-label">Temperature</div>
                        <div class="mt-1">
                            <small class="text-muted d-block">°F (Fahrenheit)</small>
                            <small class="text-info">Fluid temperature</small>
                        </div>
                    </div>
                </div>

                <!-- Additional Parameters (if available) -->
                @if(isset($mpfm_data['h2s_ppm']) || isset($mpfm_data['co2_percent']))
                <div class="col-md-12 mt-4">
                    <h5 class="mb-4 text-warning">
                        <i class="fe fe-alert-triangle mr-2"></i>Additional Parameters
                    </h5>
                </div>

                @if(isset($mpfm_data['h2s_ppm']) && $mpfm_data['h2s_ppm'])
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="kpi-card">
                        <div class="kpi-value">
                            {{ number_format($mpfm_data['h2s_ppm'], 1) }}
                        </div>
                        <div class="kpi-label">H2S Concentration</div>
                        <div class="mt-1">
                            <small class="text-muted d-block">ppm (parts per million)</small>
                            <small class="text-warning">Hydrogen sulfide level</small>
                        </div>
                    </div>
                </div>
                @endif

                @if(isset($mpfm_data['co2_percent']) && $mpfm_data['co2_percent'])
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="kpi-card">
                        <div class="kpi-value">
                            {{ number_format($mpfm_data['co2_percent'], 2) }}%
                        </div>
                        <div class="kpi-label">CO2 Concentration</div>
                        <div class="mt-1">
                            <small class="text-muted d-block">% (percentage)</small>
                            <small class="text-warning">Carbon dioxide level</small>
                        </div>
                    </div>
                </div>
                @endif
                @endif
            </div>

            <!-- Additional Metrics Table -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="data-table">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Metric</th>
                                    <th>Value</th>
                                    <th>Unit</th>
                                    <th>Status</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Water Cut</strong></td>
                                    <td>{{ isset($mpfm_data['water_cut']) ? number_format($mpfm_data['water_cut'], 2) : 'N/A' }}</td>
                                    <td>%</td>
                                    <td>
                                        @if(isset($mpfm_data['water_cut']))
                                            @if($mpfm_data['water_cut'] < 10)
                                                <span class="status-badge status-good">Good</span>
                                            @elseif($mpfm_data['water_cut'] < 30)
                                                <span class="status-badge status-warning">Moderate</span>
                                            @else
                                                <span class="status-badge status-error">High</span>
                                            @endif
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>Percentage of water in total fluid</td>
                                </tr>
                                <tr>
                                    <td><strong>WLR</strong></td>
                                    <td>{{ isset($mpfm_data['wlr']) ? number_format($mpfm_data['wlr'], 2) : 'N/A' }}</td>
                                    <td>%</td>
                                    <td>
                                        @if(isset($mpfm_data['wlr']))
                                            <span class="status-badge status-good">Calculated</span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>Water-liquid ratio</td>
                                </tr>
                                <tr>
                                    <td><strong>Pressure</strong></td>
                                    <td>{{ isset($mpfm_data['pressure']) ? number_format($mpfm_data['pressure'], 1) : 'N/A' }}</td>
                                    <td>psi</td>
                                    <td>
                                        @if(isset($mpfm_data['pressure']))
                                            <span class="status-badge status-good">Available</span>
                                        @else
                                            <span class="text-muted">Not Available</span>
                                        @endif
                                    </td>
                                    <td>Operating pressure</td>
                                </tr>
                                <tr>
                                    <td><strong>Temperature</strong></td>
                                    <td>{{ isset($mpfm_data['temperature']) ? number_format($mpfm_data['temperature'], 1) : 'N/A' }}</td>
                                    <td>°F</td>
                                    <td>
                                        @if(isset($mpfm_data['temperature']))
                                            <span class="status-badge status-good">Available</span>
                                        @else
                                            <span class="text-muted">Not Available</span>
                                        @endif
                                    </td>
                                    <td>Operating temperature</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Tab -->
        <div class="tab-pane fade" id="charts" role="tabpanel">
            <div class="row">
                <!-- Production Rates Chart -->
                <div class="col-xl-6 col-lg-12">
                    <div class="chart-container">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">
                                <i class="fe fe-trending-up mr-2 text-primary"></i>Production Rates Over Time
                            </h5>
                            @if(isset($summary['sampling_interval']) && $summary['sampling_interval'])
                                <small class="text-muted">
                                    <i class="fe fe-clock mr-1"></i>
                                    {{ number_format($summary['sampling_interval'], 1) }}s intervals
                                </small>
                            @endif
                        </div>
                        @if(isset($rates_chart_data) && $rates_chart_data && isset($rates_chart_data['datasets']))
                            <canvas id="ratesChart"></canvas>
                            <div class="mt-2 text-center">
                                <small class="text-muted">
                                    <i class="fe fe-info mr-1"></i>
                                    X-axis: {{ $chart_time_column ?? 'Time' }} |
                                    Y-axis: Production Rates
                                </small>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fe fe-bar-chart-2" style="font-size: 3rem; color: #6c757d;"></i>
                                <h6 class="mt-3 text-muted">Production rates chart not available</h6>
                                <small class="text-muted">Insufficient data or parsing error</small>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Gas-Oil Ratio Chart -->
                <div class="col-xl-6 col-lg-12">
                    <div class="chart-container">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">
                                <i class="fe fe-activity mr-2 text-success"></i>Gas-Oil Ratio Over Time
                            </h5>
                            @if(isset($mpfm_data['gor']))
                                <small class="text-muted">
                                    <i class="fe fe-target mr-1"></i>
                                    Avg: {{ number_format($mpfm_data['gor'], 1) }} scf/STB
                                </small>
                            @endif
                        </div>
                        @if(isset($gor_chart_data) && $gor_chart_data && isset($gor_chart_data['datasets']))
                            <canvas id="gorChart"></canvas>
                            <div class="mt-2 text-center">
                                <small class="text-muted">
                                    <i class="fe fe-info mr-1"></i>
                                    X-axis: {{ $chart_time_column ?? 'Time' }} |
                                    Y-axis: GOR (scf/STB)
                                </small>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fe fe-activity" style="font-size: 3rem; color: #6c757d;"></i>
                                <h6 class="mt-3 text-muted">Gas-oil ratio chart not available</h6>
                                <small class="text-muted">Insufficient gas/oil data or parsing error</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Additional Chart Analysis -->
            @if(isset($data_quality['score']))
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="chart-container">
                        <h5 class="mb-3">
                            <i class="fe fe-bar-chart-2 mr-2 text-info"></i>Data Quality Analysis
                        </h5>
                        <div class="row text-center">
                            <div class="col-md-3">
                                <div class="p-3 bg-light rounded">
                                    <h4 class="text-info">{{ number_format($data_quality['score'], 0) }}%</h4>
                                    <small class="text-muted">Overall Quality</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3 bg-light rounded">
                                    <h4 class="text-success">{{ number_format($data_quality['completeness'] ?? 0, 1) }}%</h4>
                                    <small class="text-muted">Data Completeness</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3 bg-light rounded">
                                    <h4 class="text-primary">{{ $summary['total_rows'] ?? 0 }}</h4>
                                    <small class="text-muted">Total Readings</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3 bg-light rounded">
                                    <h4 class="text-warning">{{ $summary['duration_minutes'] ? number_format($summary['duration_minutes'], 0) : 'N/A' }}</h4>
                                    <small class="text-muted">Test Duration (min)</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Raw Data Tab -->
        <div class="tab-pane fade" id="data" role="tabpanel">
            <div class="row">
                <div class="col-md-12">
                    <div class="data-table">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    @if(isset($parsed_data['test_data']['columns']))
                                        @foreach($parsed_data['test_data']['columns'] as $column)
                                            <th>{{ $column }}</th>
                                        @endforeach
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($parsed_data['test_data']['rows']))
                                    @foreach(array_slice($parsed_data['test_data']['rows'], 0, 50) as $row)
                                        <tr>
                                            @foreach($row as $value)
                                                <td>{{ $value }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                        @if(isset($parsed_data['test_data']['rows']) && count($parsed_data['test_data']['rows']) > 50)
                            <div class="text-center mt-3">
                                <small class="text-muted">
                                    Showing first 50 rows of {{ count($parsed_data['test_data']['rows']) }} total rows
                                </small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Log Preview Tab -->
        <div class="tab-pane fade" id="log" role="tabpanel">
            <div class="row">
                <div class="col-md-12">
                    <div class="log-preview">
                        {{ $raw_log_content }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="action-buttons">
        <div class="row">
            <div class="col-md-12 text-center">
                <form id="saveForm" action="{{ route('mpfm_tests.store_from_log') }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="temp_file_path" value="{{ $temp_file_path }}">
                    <input type="hidden" name="mpfm_data" value="{{ json_encode($mpfm_data) }}">
                    <input type="hidden" name="parsed_data" value="{{ json_encode($parsed_data) }}">
                    <input type="hidden" name="summary" value="{{ json_encode($summary) }}">

                    <!-- Asset Selection -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label class="form-label">Oil Field *</label>
                            <select name="oil_field_id" class="form-control" required onchange="updateTowers()">
                                <option value="">Select Oil Field</option>
                                @foreach(\App\Models\OilField::active()->get() as $field)
                                <option value="{{ $field->id }}">{{ $field->name }} ({{ $field->code }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tower *</label>
                            <select name="tower_id" class="form-control" required id="towerSelect" onchange="updateOilStrings()">
                                <option value="">Select Tower</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Oil String *</label>
                            <select name="oil_string_id" class="form-control" required id="oilStringSelect">
                                <option value="">Select Oil String</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Operational Plan</label>
                            <select name="mpfm_operational_plan_id" class="form-control">
                                <option value="">None</option>
                                @foreach(\App\Models\MPFMOperationalPlan::active()->with(['oilField', 'tower'])->get() as $plan)
                                <option value="{{ $plan->id }}">{{ $plan->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Optional notes about this test"></textarea>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-save btn-lg mr-3">
                        <i class="fe fe-save mr-2"></i> Save MPFM Test
                    </button>
                </form>

                <a href="{{ route('mpfm_tests.upload') }}" class="btn btn-secondary btn-lg ml-3">
                    <i class="fe fe-x mr-2"></i> Cancel
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
    initializeAssetSelection();
});

function initializeCharts() {
    // Rates Chart
    @if(isset($rates_chart_data) && $rates_chart_data && isset($rates_chart_data['datasets']))
    const ratesCtx = document.getElementById('ratesChart');
    if (ratesCtx) {
        new Chart(ratesCtx.getContext('2d'), {
            type: 'line',
            data: @json($rates_chart_data),
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true, position: 'top' },
                    title: { display: false }
                },
                scales: {
                    x: {
                        title: { display: true, text: '{{ $chart_time_column ?? "Time" }}' },
                        ticks: { maxTicksLimit: 10 }
                    },
                    y: {
                        title: { display: true, text: 'Production Rates' },
                        beginAtZero: true
                    }
                },
                interaction: {
                    mode: 'index',
                    intersect: false
                }
            }
        });
    }
    @endif

    // GOR Chart
    @if(isset($gor_chart_data) && $gor_chart_data && isset($gor_chart_data['datasets']))
    const gorCtx = document.getElementById('gorChart');
    if (gorCtx) {
        new Chart(gorCtx.getContext('2d'), {
            type: 'line',
            data: @json($gor_chart_data),
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true, position: 'top' },
                    title: { display: false }
                },
                scales: {
                    x: {
                        title: { display: true, text: '{{ $chart_time_column ?? "Time" }}' },
                        ticks: { maxTicksLimit: 10 }
                    },
                    y: {
                        title: { display: true, text: 'GOR (scf/STB)' },
                        beginAtZero: true
                    }
                },
                interaction: {
                    mode: 'index',
                    intersect: false
                }
            }
        });
    }
    @endif
}

function initializeAssetSelection() {
    // Asset selection logic (similar to create form)
}

function updateTowers() {
    const oilFieldId = document.querySelector('[name="oil_field_id"]').value;
    const towerSelect = document.getElementById('towerSelect');

    if (oilFieldId) {
        fetch(`/ajax/towers/by-oil-field/${oilFieldId}`)
            .then(response => response.json())
            .then(data => {
                towerSelect.innerHTML = '<option value="">Select Tower</option>';
                data.forEach(tower => {
                    towerSelect.innerHTML += `<option value="${tower.id}">${tower.name} (${tower.code})</option>`;
                });
            });
    } else {
        towerSelect.innerHTML = '<option value="">Select Oil Field First</option>';
    }
}

function updateOilStrings() {
    const towerId = document.getElementById('towerSelect').value;
    const oilStringSelect = document.getElementById('oilStringSelect');

    if (towerId) {
        fetch(`/ajax/oil-strings/by-tower/${towerId}`)
            .then(response => response.json())
            .then(data => {
                oilStringSelect.innerHTML = '<option value="">Select Oil String</option>';
                data.forEach(oilString => {
                    oilStringSelect.innerHTML += `<option value="${oilString.id}">${oilString.name} (${oilString.code})</option>`;
                });
            });
    } else {
        oilStringSelect.innerHTML = '<option value="">Select Tower First</option>';
    }
}

// Enhanced form validation with better UX
document.getElementById('saveForm').addEventListener('submit', function(e) {
    const requiredFields = ['oil_field_id', 'tower_id', 'oil_string_id'];
    let isValid = true;
    let firstInvalidField = null;

    // Clear previous validation states
    document.querySelectorAll('.is-invalid').forEach(el => {
        el.classList.remove('is-invalid');
    });
    document.querySelectorAll('.invalid-feedback').forEach(el => {
        el.style.display = 'none';
    });

    // Validate required fields
    requiredFields.forEach(field => {
        const element = document.querySelector(`[name="${field}"]`);
        if (element && !element.value.trim()) {
            element.classList.add('is-invalid');
            const feedback = element.parentNode.querySelector('.invalid-feedback');
            if (feedback) {
                feedback.style.display = 'block';
            }
            if (!firstInvalidField) firstInvalidField = element;
            isValid = false;
        }
    });

    // Check data validation status
    @if(isset($validation) && !$validation['is_valid'])
        isValid = false;
        showNotification('Please resolve data validation errors before saving.', 'danger');
    @endif

    if (!isValid) {
        e.preventDefault();
        if (firstInvalidField) {
            firstInvalidField.focus();
            firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        return false;
    }

    // Show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fe fe-loader mr-2"></i>Saving Test...';
    }

    return true;
});

// Utility function for notifications
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px; max-width: 500px;';
    notification.innerHTML = `
        <i class="fe fe-${type === 'success' ? 'check-circle' : type === 'danger' ? 'alert-triangle' : 'info'} mr-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.body.appendChild(notification);

    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

// Enhanced asset selection with loading states
function updateTowers() {
    const oilFieldId = document.querySelector('[name="oil_field_id"]').value;
    const towerSelect = document.getElementById('towerSelect');

    if (!towerSelect) return;

    // Clear dependent selects
    const oilStringSelect = document.getElementById('oilStringSelect');
    if (oilStringSelect) {
        oilStringSelect.innerHTML = '<option value="">Select Tower First</option>';
    }

    if (oilFieldId) {
        // Show loading state
        towerSelect.innerHTML = '<option value="">Loading...</option>';
        towerSelect.disabled = true;

        fetch(`/ajax/towers/by-oil-field/${oilFieldId}`)
            .then(response => {
                if (!response.ok) throw new Error('Network response failed');
                return response.json();
            })
            .then(data => {
                towerSelect.innerHTML = '<option value="">Select Tower</option>';
                if (data && data.length > 0) {
                    data.forEach(tower => {
                        const option = document.createElement('option');
                        option.value = tower.id;
                        option.textContent = `${tower.name} (${tower.code})`;
                        towerSelect.appendChild(option);
                    });
                    towerSelect.disabled = false;
                } else {
                    towerSelect.innerHTML = '<option value="">No towers available</option>';
                }
            })
            .catch(error => {
                console.error('Error loading towers:', error);
                towerSelect.innerHTML = '<option value="">Error loading towers</option>';
            });
    } else {
        towerSelect.innerHTML = '<option value="">Select Oil Field First</option>';
        towerSelect.disabled = true;
    }
}

function updateOilStrings() {
    const towerId = document.getElementById('towerSelect').value;
    const oilStringSelect = document.getElementById('oilStringSelect');

    if (!oilStringSelect) return;

    if (towerId) {
        // Show loading state
        oilStringSelect.innerHTML = '<option value="">Loading...</option>';
        oilStringSelect.disabled = true;

        fetch(`/ajax/oil-strings/by-tower/${towerId}`)
            .then(response => {
                if (!response.ok) throw new Error('Network response failed');
                return response.json();
            })
            .then(data => {
                oilStringSelect.innerHTML = '<option value="">Select Oil String</option>';
                if (data && data.length > 0) {
                    data.forEach(oilString => {
                        const option = document.createElement('option');
                        option.value = oilString.id;
                        option.textContent = `${oilString.name} (${oilString.code})`;
                        oilStringSelect.appendChild(option);
                    });
                    oilStringSelect.disabled = false;
                } else {
                    oilStringSelect.innerHTML = '<option value="">No oil strings available</option>';
                }
            })
            .catch(error => {
                console.error('Error loading oil strings:', error);
                oilStringSelect.innerHTML = '<option value="">Error loading oil strings</option>';
            });
    } else {
        oilStringSelect.innerHTML = '<option value="">Select Tower First</option>';
        oilStringSelect.disabled = true;
    }
}

// Initialize on page load with better error handling
document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
    initializeAssetSelection();

    // Pre-load towers if oil field is selected
    const oilFieldSelect = document.querySelector('[name="oil_field_id"]');
    if (oilFieldSelect && oilFieldSelect.value) {
        setTimeout(updateTowers, 100); // Small delay to ensure DOM is ready
    }
});
</script>
@endpush

