@extends('layouts.master')

@section('title', 'Well Test Analysis - ' . ($wellTest->well ?? 'Unknown'))

@push('styles')
<style>
/* ==========================================================================
   Well Test Analysis - Enhanced Styles
   ========================================================================== */

/* KPI Cards */
.kpi-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.kpi-card:hover {
    transform: translateY(-5px);
}

.kpi-card.success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}

.kpi-card.warning {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
    color: #212529;
}

.kpi-card.danger {
    background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);
}

/* Data Tables */
.analysis-table {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.analysis-table th {
    background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    color: white;
    padding: 12px;
    border: none;
    font-weight: 600;
}

.analysis-table td {
    padding: 12px;
    border: none;
    vertical-align: middle;
}

.analysis-table tr:nth-child(even) {
    background: rgba(78, 115, 223, 0.05);
}

.analysis-table tr.highlight {
    background: rgba(40, 167, 69, 0.1);
    border-left: 4px solid #28a745;
}

/* Charts */
.chart-container {
    position: relative;
    height: 350px;
    width: 100%;
    margin-bottom: 1rem;
}

/* Anomalies */
.anomaly-alert {
    background: rgba(220, 53, 69, 0.1);
    border: 1px solid rgba(220, 53, 69, 0.2);
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
}

.anomaly-badge {
    background: #dc3545;
    color: white;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
}

/* Tabs */
.nav-tabs .nav-link.active {
    background: #4e73df;
    color: white;
    border-color: #4e73df;
}

.tab-content {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 10px;
    padding: 2rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

/* Export Buttons */
.export-btn {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border: none;
    border-radius: 8px;
    padding: 8px 16px;
    color: white;
    font-weight: 500;
    transition: all 0.3s ease;
}

.export-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
}

/* Responsive */
@media (max-width: 768px) {
    .kpi-card {
        padding: 1.5rem;
        margin-bottom: 1rem;
    }

    .chart-container {
        height: 250px;
    }

    .tab-content {
        padding: 1rem;
    }
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-leftheader">
            <h4 class="page-title">
                <i class="fe fe-bar-chart-2 mr-2"></i>Well Test Analysis
            </h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('well_tests.index') }}">Well Tests</a></li>
                <li class="breadcrumb-item active">{{ $wellTest->well ?? 'Test' }} Analysis</li>
            </ol>
        </div>
        <div class="page-rightheader">
            <div class="btn-group mr-2">
                <button type="button" class="btn btn-success dropdown-toggle export-btn" data-bs-toggle="dropdown">
                    <i class="fe fe-download mr-1"></i>Export
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('well_tests.export', [$wellTest, 'format' => 'csv']) }}">
                        <i class="fe fe-file-text mr-2"></i>CSV Export</a></li>
                    <li><a class="dropdown-item" href="{{ route('well_tests.export', [$wellTest, 'format' => 'pdf']) }}">
                        <i class="fe fe-file mr-2"></i>PDF Report</a></li>
                    <li><a class="dropdown-item" href="{{ route('well_tests.export', [$wellTest, 'format' => 'json']) }}">
                        <i class="fe fe-code mr-2"></i>JSON Data</a></li>
                </ul>
            </div>
            @can('update', $wellTest)
            <a href="{{ route('well_tests.edit', $wellTest->id) }}" class="btn btn-primary mr-2">
                <i class="fe fe-edit mr-1"></i>Edit
            </a>
            @endcan
            <a href="{{ route('well_tests.index') }}" class="btn btn-secondary">
                <i class="fe fe-arrow-left mr-1"></i>Back to Tests
            </a>
        </div>
    </div>

    <!-- Test Overview Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="kpi-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1">Well</h6>
                        <h4 class="mb-0">{{ $wellTest->well ?? 'N/A' }}</h4>
                    </div>
                    <i class="fe fe-droplet" style="font-size: 2rem; opacity: 0.7;"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="kpi-card success">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1">Test Date</h6>
                        <h4 class="mb-0">{{ $wellTest->test_date?->format('M d, Y') ?? 'N/A' }}</h4>
                    </div>
                    <i class="fe fe-calendar" style="font-size: 2rem; opacity: 0.7;"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="kpi-card warning">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1">Data Points</h6>
                        <h4 class="mb-0">{{ count($parsedData) }}</h4>
                    </div>
                    <i class="fe fe-database" style="font-size: 2rem; opacity: 0.7;"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="kpi-card danger">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1">Anomalies</h6>
                        <h4 class="mb-0">{{ count($metadata['anomalies'] ?? []) }}</h4>
                    </div>
                    <i class="fe fe-alert-triangle" style="font-size: 2rem; opacity: 0.7;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Anomalies Alert -->
    @if(!empty($metadata['anomalies']))
    <div class="row mb-4">
        <div class="col-12">
            <div class="anomaly-alert">
                <div class="d-flex align-items-center">
                    <i class="fe fe-alert-triangle mr-2 text-danger"></i>
                    <strong>Alert:</strong>
                    <span class="ms-2">{{ count($metadata['anomalies']) }} data anomalies detected. Review the values below.</span>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Main Analysis Tabs -->
    <ul class="nav nav-tabs" id="analysisTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">
                <i class="fe fe-info mr-1"></i>Overview
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="data-tab" data-bs-toggle="tab" data-bs-target="#data" type="button" role="tab">
                <i class="fe fe-table mr-1"></i>Data Table
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="metrics-tab" data-bs-toggle="tab" data-bs-target="#metrics" type="button" role="tab">
                <i class="fe fe-bar-chart-2 mr-1"></i>Metrics
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="charts-tab" data-bs-toggle="tab" data-bs-target="#charts" type="button" role="tab">
                <i class="fe fe-trending-up mr-1"></i>Charts
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="raw-tab" data-bs-toggle="tab" data-bs-target="#raw" type="button" role="tab">
                <i class="fe fe-file-text mr-1"></i>Raw Data
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content mt-4" id="analysisContent">
        <!-- Overview Tab -->
        <div class="tab-pane fade show active" id="overview" role="tabpanel">
            <div class="row">
                <!-- Test Information -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fe fe-info mr-2"></i>Test Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="40%">Test ID:</th>
                                    <td>{{ $wellTest->id }}</td>
                                </tr>
                                <tr>
                                    <th>Client:</th>
                                    <td>{{ $wellTest->client ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Field:</th>
                                    <td>{{ $wellTest->field ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Well:</th>
                                    <td>{{ $wellTest->well ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Operator:</th>
                                    <td>{{ $wellTest->operator ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Test Date:</th>
                                    <td>{{ $wellTest->test_date?->format('Y-m-d') ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Test Type:</th>
                                    <td>{{ $wellTest->test_type ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Data Points:</th>
                                    <td>{{ count($parsedData) }}</td>
                                </tr>
                                <tr>
                                    <th>Log File:</th>
                                    <td>
                                        @if($wellTest->log_file_path)
                                            <a href="{{ Storage::url($wellTest->log_file_path) }}" target="_blank" class="text-primary">
                                                <i class="fe fe-download mr-1"></i>Download
                                            </a>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Key Metrics Summary -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fe fe-activity mr-2"></i>Key Production Metrics
                            </h5>
                        </div>
                        <div class="card-body">
                            @if(!empty($calculatedMetrics['petroleum_metrics']))
                                <table class="table table-sm table-borderless">
                                    @if(isset($calculatedMetrics['petroleum_metrics']['gor']))
                                    <tr>
                                        <th>GOR (scf/STB):</th>
                                        <td>{{ number_format($calculatedMetrics['petroleum_metrics']['gor']['average'] ?? 0, 2) }}</td>
                                    </tr>
                                    @endif
                                    @if(isset($calculatedMetrics['cumulative_totals']))
                                    <tr>
                                        <th>Total Oil:</th>
                                        <td>{{ number_format($calculatedMetrics['cumulative_totals']['oil'] ?? 0, 2) }} STB</td>
                                    </tr>
                                    <tr>
                                        <th>Total Gas:</th>
                                        <td>{{ number_format($calculatedMetrics['cumulative_totals']['gas'] ?? 0, 2) }} Mscf</td>
                                    </tr>
                                    <tr>
                                        <th>Total Water:</th>
                                        <td>{{ number_format($calculatedMetrics['cumulative_totals']['water'] ?? 0, 2) }} STB</td>
                                    </tr>
                                    @endif
                                </table>
                            @else
                                <p class="text-muted mb-0">No calculated metrics available. Analysis may need to be regenerated.</p>
                            @endif
                        </div>
                    </div>

                    @if($wellTest->notes)
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fe fe-file-text mr-2"></i>Notes
                            </h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $wellTest->notes }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Data Table Tab -->
        <div class="tab-pane fade" id="data" role="tabpanel">
            <div class="analysis-table">
                <div class="table-responsive">
                    <table id="wellTestDataTable" class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                @if(!empty($parsedData))
                                    @foreach(array_keys($parsedData[0] ?? []) as $column)
                                        <th>{{ $column }}</th>
                                    @endforeach
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @if(!empty($parsedData))
                                @foreach($parsedData as $index => $row)
                                    <tr class="{{ in_array($index + 1, array_column($metadata['anomalies'] ?? [], 'row')) ? 'table-danger' : '' }}">
                                        <td>{{ $index + 1 }}</td>
                                        @foreach($row as $value)
                                            <td>{{ is_numeric($value) ? number_format($value, 2) : $value }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Metrics Tab -->
        <div class="tab-pane fade" id="metrics" role="tabpanel">
            <div class="row">
                <!-- Column Statistics -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fe fe-bar-chart-2 mr-2"></i>Column Statistics
                            </h5>
                        </div>
                        <div class="card-body">
                            @if(!empty($calculatedMetrics['column_stats']))
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Column</th>
                                                <th>Avg</th>
                                                <th>Min</th>
                                                <th>Max</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($calculatedMetrics['column_stats'] as $column => $stats)
                                                @if(isset($stats['average']))
                                                <tr>
                                                    <td>{{ $column }}</td>
                                                    <td>{{ number_format($stats['average'], 2) }}</td>
                                                    <td>{{ number_format($stats['min'] ?? 0, 2) }}</td>
                                                    <td>{{ number_format($stats['max'] ?? 0, 2) }}</td>
                                                </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted">No column statistics available.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Petroleum Metrics -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fe fe-cpu mr-2"></i>Petroleum Engineering Metrics
                            </h5>
                        </div>
                        <div class="card-body">
                            @if(!empty($calculatedMetrics['petroleum_metrics']))
                                <table class="table table-sm table-borderless">
                                    @if(isset($calculatedMetrics['petroleum_metrics']['gor']))
                                    <tr>
                                        <th>GOR (scf/STB):</th>
                                        <td>
                                            Avg: {{ number_format($calculatedMetrics['petroleum_metrics']['gor']['average'] ?? 0, 2) }}<br>
                                            Min: {{ number_format($calculatedMetrics['petroleum_metrics']['gor']['min'] ?? 0, 2) }}<br>
                                            Max: {{ number_format($calculatedMetrics['petroleum_metrics']['gor']['max'] ?? 0, 2) }}
                                        </td>
                                    </tr>
                                    @endif
                                </table>
                            @else
                                <p class="text-muted">No petroleum metrics calculated.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Tab -->
        <div class="tab-pane fade" id="charts" role="tabpanel">
            <div class="row">
                <!-- Time Series Charts -->
                <div class="col-xl-12 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fe fe-trending-up mr-2"></i>Production Rates Over Time
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="productionChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- GOR Chart -->
                <div class="col-xl-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fe fe-activity mr-2"></i>Gas-Oil Ratio Trend
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="gorChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Distribution Chart -->
                <div class="col-xl-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fe fe-bar-chart mr-2"></i>Parameter Distributions
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="distributionChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Raw Data Tab -->
        <div class="tab-pane fade" id="raw" role="tabpanel">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fe fe-file-text mr-2"></i>Raw Log File Content
                    </h5>
                </div>
                <div class="card-body">
                    @if($wellTest->log_file_path && Storage::exists($wellTest->log_file_path))
                        <div class="alert alert-info mb-3">
                            <strong>File:</strong> {{ basename($wellTest->log_file_path) }}
                            ({{ number_format(Storage::size($wellTest->log_file_path) / 1024, 1) }} KB)
                        </div>
                        <pre class="bg-light p-3 rounded" style="max-height: 500px; overflow-y: auto; font-size: 0.85rem;">
{{ Storage::get($wellTest->log_file_path) }}
                        </pre>
                    @else
                        <div class="alert alert-warning">
                            <i class="fe fe-alert-triangle mr-2"></i>
                            Raw log file not available or has been removed.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTable
    if (document.getElementById('wellTestDataTable')) {
        $('#wellTestDataTable').DataTable({
            responsive: true,
            pageLength: 25,
            order: [[0, 'asc']],
            language: {
                search: "Filter data:",
                lengthMenu: "Show _MENU_ entries per page",
                info: "Showing _START_ to _END_ of _TOTAL_ entries"
            }
        });
    }

    // Load charts data
    loadChartsData();

    function loadChartsData() {
        // Load production chart
        fetch('{{ route("well_tests.chart_data", [$wellTest, "type" => "time_series"]) }}')
            .then(response => response.json())
            .then(data => {
                initializeCharts(data);
            })
            .catch(error => {
                console.error('Error loading chart data:', error);
            });
    }

    function initializeCharts(chartData) {
        // Production chart
        if (chartData.production && document.getElementById('productionChart')) {
            new Chart(document.getElementById('productionChart'), {
                type: 'line',
                data: chartData.production,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: true, position: 'top' }
                    },
                    scales: {
                        x: { title: { display: true, text: 'Time' } },
                        y: { title: { display: true, text: 'Production Rates' }, beginAtZero: true }
                    }
                }
            });
        }

        // GOR chart
        if (chartData.gor && document.getElementById('gorChart')) {
            new Chart(document.getElementById('gorChart'), {
                type: 'line',
                data: chartData.gor,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: true, position: 'top' }
                    },
                    scales: {
                        x: { title: { display: true, text: 'Time' } },
                        y: { title: { display: true, text: 'GOR (scf/STB)' }, beginAtZero: true }
                    }
                }
            });
        }

        // Distribution chart
        if (chartData.distribution && document.getElementById('distributionChart')) {
            new Chart(document.getElementById('distributionChart'), {
                type: 'bar',
                data: chartData.distribution,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: true, position: 'top' }
                    },
                    scales: {
                        y: { title: { display: true, text: 'Value' }, beginAtZero: true }
                    }
                }
            });
        }
    }
});
</script>
@endpush
