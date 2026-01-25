{{-- Detailed Metrics Display Component --}}
<div class="detailed-metrics-container">
    @if(isset($metrics) && !empty($metrics))

    {{-- Timing Information --}}
    @if(isset($metrics['timing']) && !empty($metrics['timing']))
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary">
                    <h5 class="card-title mb-0" style="color: white;">
                        <i class="fe fe-clock mr-2"></i>Test Timing Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="metric-item">
                                <h6 class="text-muted mb-2">Started At</h6>
                                <p class="metric-value">{{ $metrics['timing']['test_started_at'] ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric-item">
                                <h6 class="text-muted mb-2">Completed At</h6>
                                <p class="metric-value">{{ $metrics['timing']['test_completed_at'] ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric-item">
                                <h6 class="text-muted mb-2">Duration</h6>
                                <p class="metric-value">{{ round($metrics['timing']['test_duration_hours'] ?? 0, 2) }} hrs</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric-item">
                                <h6 class="text-muted mb-2">Stable Duration</h6>
                                <p class="metric-value">{{ round($metrics['timing']['test_stable_duration_hours'] ?? 0, 2) }} hrs</p>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <p class="text-muted small">
                                <i class="fe fe-info mr-2"></i>
                                Data Points: {{ $metrics['timing']['test_total_data_points'] ?? 0 }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Flow Rates --}}
    @if(isset($metrics['flow_rates']) && !empty($metrics['flow_rates']))
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-info">
                    <h5 class="card-title mb-0" style="color: white;">
                        <i class="fe fe-arrow-right mr-2"></i>Flow Rates
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        {{-- Line Conditions --}}
                        @if(isset($metrics['flow_rates']['line_conditions']))
                        <div class="col-md-6">
                            <h6 class="mb-3"><strong>Line Conditions</strong></h6>
                            @foreach($metrics['flow_rates']['line_conditions'] as $key => $metric)
                            <div class="metric-row">
                                <span class="metric-label">{{ $metric['label'] ?? ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                <span class="metric-value">{{ number_format($metric['value'] ?? 0, 2) }} {{ $metric['unit'] ?? '' }}</span>
                            </div>
                            @endforeach
                        </div>
                        @endif

                        {{-- Standard Conditions --}}
                        @if(isset($metrics['flow_rates']['standard_conditions']))
                        <div class="col-md-6">
                            <h6 class="mb-3"><strong>Standard Conditions</strong></h6>
                            @foreach($metrics['flow_rates']['standard_conditions'] as $key => $metric)
                            <div class="metric-row">
                                <span class="metric-label">{{ $metric['label'] ?? ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                <span class="metric-value">{{ number_format($metric['value'] ?? 0, 2) }} {{ $metric['unit'] ?? '' }}</span>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Water Cut / BSW / WLR --}}
    @if(isset($metrics['water_cut_metrics']) && !empty($metrics['water_cut_metrics']))
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-warning">
                    <h5 class="card-title mb-0" style="color: white;">
                        <i class="fe fe-droplet mr-2"></i>Water Cut Metrics
                    </h5>
                </div>
                <div class="card-body">
                    @foreach($metrics['water_cut_metrics'] as $key => $metric)
                    <div class="metric-row">
                        <span class="metric-label">{{ $metric['label'] ?? ucfirst($key) }}:</span>
                        <span class="metric-value">{{ number_format($metric['value'] ?? 0, 2) }} {{ $metric['unit'] ?? '' }}</span>
                        @if(isset($metric['max']) || isset($metric['min']))
                        <small class="text-muted">(Min: {{ number_format($metric['min'] ?? 0, 2) }}, Max: {{ number_format($metric['max'] ?? 0, 2) }})</small>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Gas-Oil Relations --}}
        @if(isset($metrics['gas_oil_relations']) && !empty($metrics['gas_oil_relations']))
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success">
                    <h5 class="card-title mb-0" style="color: white;">
                        <i class="fe fe-wind mr-2"></i>Gas-Oil Relations
                    </h5>
                </div>
                <div class="card-body">
                    @foreach($metrics['gas_oil_relations'] as $key => $metric)
                    <div class="metric-row">
                        <span class="metric-label">{{ $metric['label'] ?? ucfirst(str_replace('_', ' ', $key)) }}:</span>
                        <span class="metric-value">{{ number_format($metric['value'] ?? 0, 2) }} {{ $metric['unit'] ?? '' }}</span>
                        @if(isset($metric['max']) || isset($metric['min']))
                        <small class="text-muted">(Min: {{ number_format($metric['min'] ?? 0, 2) }}, Max: {{ number_format($metric['max'] ?? 0, 2) }})</small>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- Density & Mass Flow --}}
    @if(isset($metrics['density_mass_flow']) && !empty($metrics['density_mass_flow']))
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-secondary">
                    <h5 class="card-title mb-0" style="color: white;">
                        <i class="fe fe-layers mr-2"></i>Density & Mass Flow
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($metrics['density_mass_flow'] as $condition => $items)
                        <div class="col-md-6">
                            <h6 class="mb-3"><strong>{{ ucfirst($condition === 'line_conditions' ? 'Line Conditions' : 'Standard Conditions') }}</strong></h6>
                            @foreach($items as $key => $metric)
                            <div class="metric-row">
                                <span class="metric-label">{{ $metric['label'] ?? ucfirst($key) }}:</span>
                                <span class="metric-value">{{ number_format($metric['value'] ?? 0, 3) }} {{ $metric['unit'] ?? '' }}</span>
                            </div>
                            @endforeach
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Cumulative Volumes --}}
    @if(isset($metrics['cumulative_volumes']) && !empty($metrics['cumulative_volumes']))
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h5 class="card-title mb-0" style="color: white;">
                        <i class="fe fe-volume-2 mr-2"></i>Cumulative Volumes
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($metrics['cumulative_volumes'] as $condition => $items)
                        <div class="col-md-6">
                            <h6 class="mb-3"><strong>{{ ucfirst(str_replace('_', ' ', $condition)) }}</strong></h6>
                            @foreach($items as $key => $metric)
                            <div class="metric-row">
                                <span class="metric-label">{{ $metric['label'] ?? ucfirst($key) }}:</span>
                                <span class="metric-value">{{ number_format($metric['value'] ?? 0, 2) }} {{ $metric['unit'] ?? '' }}</span>
                            </div>
                            @endforeach
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- PVT Relationships --}}
    @if(isset($metrics['pvt_relationships']) && !empty($metrics['pvt_relationships']))
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header" style="background: #e74c3c;">
                    <h5 class="card-title mb-0" style="color: white;">
                        <i class="fe fe-thermometer mr-2"></i>PVT Relationships
                    </h5>
                </div>
                <div class="card-body">
                    @foreach($metrics['pvt_relationships'] as $key => $metric)
                    <div class="metric-row">
                        <span class="metric-label">{{ $metric['label'] ?? ucfirst(str_replace('_', ' ', $key)) }}:</span>
                        <span class="metric-value">{{ number_format($metric['value'] ?? 0, 4) }} {{ $metric['unit'] ?? '' }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Pressure & Temperature --}}
        @if(isset($metrics['pressure_diagnostics']) && !empty($metrics['pressure_diagnostics']))
        <div class="col-md-6">
            <div class="card">
                <div class="card-header" style="background: #3498db;">
                    <h5 class="card-title mb-0" style="color: white;">
                        <i class="fe fe-activity mr-2"></i>Pressure & Temperature
                    </h5>
                </div>
                <div class="card-body">
                    @foreach($metrics['pressure_diagnostics'] as $key => $metric)
                    <div class="metric-row">
                        <span class="metric-label">{{ $metric['label'] ?? ucfirst(str_replace('_', ' ', $key)) }}:</span>
                        <span class="metric-value">{{ number_format($metric['value'] ?? 0, 2) }} {{ $metric['unit'] ?? '' }}</span>
                        @if(isset($metric['max']) || isset($metric['min']))
                        <small class="text-muted">(Avg: {{ number_format($metric['value'] ?? 0, 2) }}, Min: {{ number_format($metric['min'] ?? 0, 2) }}, Max: {{ number_format($metric['max'] ?? 0, 2) }})</small>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- Data Quality --}}
    @if(isset($metrics['data_quality']) && !empty($metrics['data_quality']))
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header" style="background: #27ae60;">
                    <h5 class="card-title mb-0" style="color: white;">
                        <i class="fe fe-check-circle mr-2"></i>Data Quality Assessment
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="metric-item">
                                <h6 class="text-muted mb-2">Quality Score</h6>
                                <p class="metric-value">
                                    {{ round(($metrics['data_quality']['quality_score'] ?? 0) * 100, 1) }}%
                                </p>
                                <small class="text-muted">{{ $metrics['data_quality']['status'] ?? 'Unknown' }}</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric-item">
                                <h6 class="text-muted mb-2">Missing Data</h6>
                                <p class="metric-value">{{ round($metrics['data_quality']['missing_data_percentage'] ?? 0, 2) }}%</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric-item">
                                <h6 class="text-muted mb-2">Total Rows</h6>
                                <p class="metric-value">{{ $metrics['data_quality']['total_rows'] ?? 0 }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="progress" style="height: 25px;">
                                @php
                                    $qualityScore = ($metrics['data_quality']['quality_score'] ?? 0) * 100;
                                    $color = $qualityScore >= 80 ? 'bg-success' : ($qualityScore >= 60 ? 'bg-warning' : 'bg-danger');
                                @endphp
                                <div class="progress-bar {{ $color }}" role="progressbar"
                                     style="width: {{ $qualityScore }}%;"
                                     aria-valuenow="{{ $qualityScore }}"
                                     aria-valuemin="0"
                                     aria-valuemax="100">
                                    {{ round($qualityScore, 1) }}%
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @else
    <div class="alert alert-info">
        <i class="fe fe-info mr-2"></i>
        No detailed metrics available.
    </div>
    @endif
</div>

<style>
    .detailed-metrics-container {
        padding: 1rem 0;
    }

    .metric-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .metric-row:last-child {
        border-bottom: none;
    }

    .metric-label {
        font-weight: 500;
        color: #6c757d;
        flex: 1;
    }

    .metric-value {
        font-weight: 600;
        color: #495057;
        font-size: 1.1rem;
    }

    .metric-item {
        padding: 1rem;
        background: rgba(78, 115, 223, 0.05);
        border-radius: 8px;
        margin-bottom: 1rem;
    }

    .card {
        border: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 1.5rem;
    }

    .card-header {
        border: none;
        padding: 1rem 1.5rem;
        font-weight: 600;
    }
</style>
