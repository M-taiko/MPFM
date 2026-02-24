@extends('layouts.master')
@section('content')
<style>
.results-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 16px;
    padding: 2rem;
    color: white;
    margin-bottom: 2rem;
    box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
}

.metric-card-modern {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border-left: 4px solid transparent;
}

.metric-card-modern:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}

.metric-card-header {
    background: linear-gradient(135deg, var(--header-color-1, #667eea) 0%, var(--header-color-2, #764ba2) 100%);
    color: white;
    padding: 1rem 1.25rem;
    border-radius: 12px;
    margin: -1.5rem -1.5rem 1.5rem -1.5rem;
    font-weight: 600;
    font-size: 1.1rem;
}

.metric-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1.5rem;
}

.metric-item-box {
    text-align: center;
    padding: 1rem;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border-radius: 12px;
    border: 1px solid #e9ecef;
}

.metric-value-large {
    font-size: 2rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 0.5rem;
}

.metric-label-small {
    font-size: 0.875rem;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.metric-unit {
    font-size: 0.75rem;
    color: #94a3b8;
    margin-top: 0.25rem;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1.5rem;
}

.info-item {
    padding: 1rem;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border-radius: 10px;
    border-left: 3px solid #667eea;
}

.info-label {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #64748b;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.info-value {
    font-size: 1.1rem;
    font-weight: 600;
    color: #2d3748;
}

.chart-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    height: 100%;
}

.chart-header {
    font-size: 1.1rem;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid #f1f5f9;
}

.safety-alert {
    background: linear-gradient(135deg, #fef3c7 0%, #fef9e7 100%);
    border-left: 4px solid #f59e0b;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}

.safety-alert-header {
    font-size: 1.1rem;
    font-weight: 700;
    color: #92400e;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
}

.data-table-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

.section-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 1.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 3px solid #667eea;
    display: inline-block;
}

.save-section {
    background: linear-gradient(135deg, #d1fae5 0%, #ecfdf5 100%);
    border-left: 4px solid #10b981;
    border-radius: 12px;
    padding: 2rem;
    margin-bottom: 1.5rem;
}

.raw-log-section {
    background: #1e293b;
    color: #e2e8f0;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    font-family: 'Courier New', monospace;
    font-size: 0.85rem;
    max-height: 400px;
    overflow-y: auto;
}

.table-responsive {
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

table.modern-table {
    margin-bottom: 0;
}

table.modern-table thead {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

table.modern-table thead th {
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
    padding: 1rem;
    border: none;
}

table.modern-table tbody td {
    padding: 0.875rem 1rem;
    vertical-align: middle;
}

table.modern-table tbody tr {
    transition: background-color 0.2s ease;
}

table.modern-table tbody tr:hover {
    background-color: #f8f9fa;
}

.btn-modern-success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border: none;
    color: white;
    padding: 1rem 2rem;
    border-radius: 10px;
    font-weight: 600;
    font-size: 1.1rem;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.btn-modern-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
    color: white;
}
</style>

<div class="container-fluid">
    <!-- Modern Header -->
    <div class="results-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center mb-2">
                    <i class="fe fe-activity" style="font-size: 2rem; margin-right: 1rem;"></i>
                    <div>
                        <h1 class="mb-1" style="font-size: 1.75rem; font-weight: 700;">
                            Well Test Results: {{ $summary['well'] }}
                        </h1>
                        <div style="opacity: 0.9; font-size: 1rem;">
                            {{ $summary['client'] }} • {{ $summary['field'] }}
                        </div>
                    </div>
                </div>
                <div class="mt-3" style="opacity: 0.85; font-size: 0.9rem;">
                    <i class="fe fe-calendar mr-2"></i>{{ $summary['start_time'] ? $summary['start_time']->format('M d, Y H:i') : 'N/A' }}
                    @if($summary['end_time'])
                        <span class="ml-2">to {{ $summary['end_time']->format('H:i') }}</span>
                    @endif
                </div>
            </div>
            <div class="col-md-4 text-right">
                <div style="font-size: 2.5rem; opacity: 0.9;">
                    <i class="fe fe-check-circle"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Production Metrics -->
    <h3 class="section-title"><i class="fe fe-trending-up mr-2"></i>Key Production Metrics</h3>

    <div class="row">
        <!-- Oil Production -->
        <div class="col-md-4">
            <div class="metric-card-modern" style="--header-color-1: #10b981; --header-color-2: #059669; border-left-color: #10b981;">
                <div class="metric-card-header">
                    <i class="fe fe-droplet mr-2"></i>Oil Production
                </div>
                <div class="metric-grid" style="grid-template-columns: 1fr;">
                    <div class="metric-item-box">
                        <div class="metric-value-large">
                            {{ is_numeric($averages['oil_rate']) ? number_format($averages['oil_rate'], 2) : 'N/A' }}
                        </div>
                        <div class="metric-label-small">Oil Rate</div>
                        <div class="metric-unit">STB/d</div>
                    </div>
                    @if(isset($averages['cumulative_oil_volume']) && is_numeric($averages['cumulative_oil_volume']))
                    <div class="metric-item-box">
                        <div class="metric-value-large" style="font-size: 1.5rem;">
                            {{ number_format($averages['cumulative_oil_volume'], 2) }}
                        </div>
                        <div class="metric-label-small">Cumulative Oil</div>
                        <div class="metric-unit">bbl</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Gas Production -->
        <div class="col-md-4">
            <div class="metric-card-modern" style="--header-color-1: #f59e0b; --header-color-2: #d97706; border-left-color: #f59e0b;">
                <div class="metric-card-header">
                    <i class="fe fe-wind mr-2"></i>Gas Production
                </div>
                <div class="metric-grid" style="grid-template-columns: 1fr;">
                    <div class="metric-item-box">
                        <div class="metric-value-large">
                            {{ is_numeric($averages['gas_rate']) ? number_format($averages['gas_rate'], 2) : 'N/A' }}
                        </div>
                        <div class="metric-label-small">Gas Rate</div>
                        <div class="metric-unit">MMScf/d</div>
                    </div>
                    @if(isset($averages['cumulative_gas_volume']) && is_numeric($averages['cumulative_gas_volume']))
                    <div class="metric-item-box">
                        <div class="metric-value-large" style="font-size: 1.5rem;">
                            {{ number_format($averages['cumulative_gas_volume'], 2) }}
                        </div>
                        <div class="metric-label-small">Cumulative Gas</div>
                        <div class="metric-unit">Mcf</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Water Production -->
        <div class="col-md-4">
            <div class="metric-card-modern" style="--header-color-1: #3b82f6; --header-color-2: #2563eb; border-left-color: #3b82f6;">
                <div class="metric-card-header">
                    <i class="fe fe-droplet mr-2"></i>Water Production
                </div>
                <div class="metric-grid" style="grid-template-columns: 1fr;">
                    <div class="metric-item-box">
                        <div class="metric-value-large">
                            {{ is_numeric($averages['water_rate']) ? number_format($averages['water_rate'], 2) : 'N/A' }}
                        </div>
                        <div class="metric-label-small">Water Rate</div>
                        <div class="metric-unit">STB/d</div>
                    </div>
                    @if(isset($averages['cumulative_water_volume']) && is_numeric($averages['cumulative_water_volume']))
                    <div class="metric-item-box">
                        <div class="metric-value-large" style="font-size: 1.5rem;">
                            {{ number_format($averages['cumulative_water_volume'], 2) }}
                        </div>
                        <div class="metric-label-small">Cumulative Water</div>
                        <div class="metric-unit">bbl</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Production Ratios -->
    <div class="metric-card-modern" style="--header-color-1: #8b5cf6; --header-color-2: #7c3aed; border-left-color: #8b5cf6;">
        <div class="metric-card-header">
            <i class="fe fe-bar-chart-2 mr-2"></i>Production Ratios & Quality Indicators
        </div>
        <div class="metric-grid">
            <div class="metric-item-box">
                <div class="metric-value-large">
                    {{ is_numeric($averages['gor']) ? number_format($averages['gor'], 0) : 'N/A' }}
                </div>
                <div class="metric-label-small">GOR</div>
                <div class="metric-unit">Scf/STB</div>
                @if(isset($averages['gor_formula']) && is_numeric($averages['gor_formula']))
                <div class="metric-unit" style="color: #64748b; margin-top: 0.5rem;">
                    Calculated: {{ number_format($averages['gor_formula'], 0) }}
                </div>
                @endif
            </div>
            <div class="metric-item-box">
                <div class="metric-value-large">
                    {{ is_numeric($averages['bsw']) ? number_format($averages['bsw'], 2) : 'N/A' }}%
                </div>
                <div class="metric-label-small">BSW</div>
                <div class="metric-unit">Basic Sediment & Water</div>
                @if(isset($averages['bsw_formula']) && is_numeric($averages['bsw_formula']))
                <div class="metric-unit" style="color: #64748b; margin-top: 0.5rem;">
                    Calculated: {{ number_format($averages['bsw_formula'], 2) }}%
                </div>
                @endif
            </div>
            <div class="metric-item-box">
                <div class="metric-value-large">
                    {{ is_numeric($averages['wlr']) ? number_format($averages['wlr'], 2) : 'N/A' }}%
                </div>
                <div class="metric-label-small">WLR</div>
                <div class="metric-unit">Water-Liquid Ratio</div>
                @if(isset($averages['wlr_formula']) && is_numeric($averages['wlr_formula']))
                <div class="metric-unit" style="color: #64748b; margin-top: 0.5rem;">
                    Calculated: {{ number_format($averages['wlr_formula'], 2) }}%
                </div>
                @endif
            </div>
            <div class="metric-item-box">
                <div class="metric-value-large">
                    {{ is_numeric($averages['gvf']) ? number_format($averages['gvf'], 2) : 'N/A' }}%
                </div>
                <div class="metric-label-small">GVF</div>
                <div class="metric-unit">Gas Void Fraction</div>
                @if(isset($averages['gvf_formula']) && is_numeric($averages['gvf_formula']))
                <div class="metric-unit" style="color: #64748b; margin-top: 0.5rem;">
                    Calculated: {{ number_format($averages['gvf_formula'], 2) }}%
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Pressure & Temperature Metrics -->
    <div class="row">
        <div class="col-md-6">
            <div class="metric-card-modern" style="--header-color-1: #ec4899; --header-color-2: #db2777; border-left-color: #ec4899;">
                <div class="metric-card-header">
                    <i class="fe fe-gauge mr-2"></i>Pressure Metrics
                </div>
                <div class="metric-grid">
                    <div class="metric-item-box">
                        <div class="metric-value-large" style="font-size: 1.5rem;">
                            {{ isset($averages['max_pressure']) && is_numeric($averages['max_pressure']) ? number_format($averages['max_pressure'], 2) : 'N/A' }}
                        </div>
                        <div class="metric-label-small">Maximum</div>
                        <div class="metric-unit">psi</div>
                    </div>
                    <div class="metric-item-box">
                        <div class="metric-value-large" style="font-size: 1.5rem;">
                            {{ isset($averages['avg_pressure']) && is_numeric($averages['avg_pressure']) ? number_format($averages['avg_pressure'], 2) : 'N/A' }}
                        </div>
                        <div class="metric-label-small">Average</div>
                        <div class="metric-unit">psi</div>
                    </div>
                    <div class="metric-item-box">
                        <div class="metric-value-large" style="font-size: 1.5rem;">
                            {{ isset($averages['min_pressure']) && is_numeric($averages['min_pressure']) ? number_format($averages['min_pressure'], 2) : 'N/A' }}
                        </div>
                        <div class="metric-label-small">Minimum</div>
                        <div class="metric-unit">psi</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="metric-card-modern" style="--header-color-1: #ef4444; --header-color-2: #dc2626; border-left-color: #ef4444;">
                <div class="metric-card-header">
                    <i class="fe fe-thermometer mr-2"></i>Temperature Metrics
                </div>
                <div class="metric-grid">
                    <div class="metric-item-box">
                        <div class="metric-value-large" style="font-size: 1.5rem;">
                            {{ isset($averages['max_temperature']) && is_numeric($averages['max_temperature']) ? number_format($averages['max_temperature'], 2) : 'N/A' }}
                        </div>
                        <div class="metric-label-small">Maximum</div>
                        <div class="metric-unit">°F</div>
                    </div>
                    <div class="metric-item-box">
                        <div class="metric-value-large" style="font-size: 1.5rem;">
                            {{ isset($averages['avg_temperature']) && is_numeric($averages['avg_temperature']) ? number_format($averages['avg_temperature'], 2) : 'N/A' }}
                        </div>
                        <div class="metric-label-small">Average</div>
                        <div class="metric-unit">°F</div>
                    </div>
                    <div class="metric-item-box">
                        <div class="metric-value-large" style="font-size: 1.5rem;">
                            {{ isset($averages['min_temperature']) && is_numeric($averages['min_temperature']) ? number_format($averages['min_temperature'], 2) : 'N/A' }}
                        </div>
                        <div class="metric-label-small">Minimum</div>
                        <div class="metric-unit">°F</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fluid Properties & PVT Data -->
    <h3 class="section-title"><i class="fe fe-zap mr-2"></i>Fluid Properties & PVT Data</h3>
    <div class="row">
        <!-- Formation Volume Factors -->
        <div class="col-md-6">
            <div class="metric-card-modern" style="--header-color-1: #06b6d4; --header-color-2: #0891b2; border-left-color: #06b6d4;">
                <div class="metric-card-header">
                    <i class="fe fe-droplet mr-2"></i>Formation Volume Factors
                </div>
                <div class="metric-grid">
                    <div class="metric-item-box">
                        <div class="metric-value-large" style="font-size: 1.5rem;">
                            {{ isset($averages['bo']) && is_numeric($averages['bo']) ? number_format($averages['bo'], 4) : 'N/A' }}
                        </div>
                        <div class="metric-label-small">Oil (bo)</div>
                        <div class="metric-unit">Sm³/m³</div>
                    </div>
                    <div class="metric-item-box">
                        <div class="metric-value-large" style="font-size: 1.5rem;">
                            {{ isset($averages['bw']) && is_numeric($averages['bw']) ? number_format($averages['bw'], 4) : 'N/A' }}
                        </div>
                        <div class="metric-label-small">Water (bw)</div>
                        <div class="metric-unit">Sm³/m³</div>
                    </div>
                    <div class="metric-item-box">
                        <div class="metric-value-large" style="font-size: 1.5rem;">
                            {{ isset($averages['bg']) && is_numeric($averages['bg']) ? number_format($averages['bg'], 4) : 'N/A' }}
                        </div>
                        <div class="metric-label-small">Gas (bg)</div>
                        <div class="metric-unit">Sm³/m³</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fluid Densities -->
        <div class="col-md-6">
            <div class="metric-card-modern" style="--header-color-1: #8b5cf6; --header-color-2: #7c3aed; border-left-color: #8b5cf6;">
                <div class="metric-card-header">
                    <i class="fe fe-activity mr-2"></i>Fluid Densities
                </div>
                <div class="metric-grid">
                    <div class="metric-item-box">
                        <div class="metric-value-large" style="font-size: 1.5rem;">
                            {{ isset($averages['po']) && is_numeric($averages['po']) ? number_format($averages['po'], 2) : 'N/A' }}
                        </div>
                        <div class="metric-label-small">Oil (ρo)</div>
                        <div class="metric-unit">kg/m³</div>
                    </div>
                    <div class="metric-item-box">
                        <div class="metric-value-large" style="font-size: 1.5rem;">
                            {{ isset($averages['pw']) && is_numeric($averages['pw']) ? number_format($averages['pw'], 2) : 'N/A' }}
                        </div>
                        <div class="metric-label-small">Water (ρw)</div>
                        <div class="metric-unit">kg/m³</div>
                    </div>
                    <div class="metric-item-box">
                        <div class="metric-value-large" style="font-size: 1.5rem;">
                            {{ isset($averages['pg']) && is_numeric($averages['pg']) ? number_format($averages['pg'], 4) : 'N/A' }}
                        </div>
                        <div class="metric-label-small">Gas (ρg)</div>
                        <div class="metric-unit">kg/m³</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gas Properties -->
    <div class="row">
        <div class="col-md-12">
            <div class="metric-card-modern" style="--header-color-1: #10b981; --header-color-2: #059669; border-left-color: #10b981;">
                <div class="metric-card-header">
                    <i class="fe fe-wind mr-2"></i>Gas Properties & Solution GOR
                </div>
                <div class="metric-grid">
                    <div class="metric-item-box">
                        <div class="metric-value-large" style="font-size: 1.5rem;">
                            {{ isset($averages['rst']) && is_numeric($averages['rst']) ? number_format($averages['rst'], 2) : 'N/A' }}
                        </div>
                        <div class="metric-label-small">Solution GOR (Rst)</div>
                        <div class="metric-unit">Scf/STB</div>
                    </div>
                    <div class="metric-item-box">
                        <div class="metric-value-large" style="font-size: 1.5rem;">
                            {{ isset($averages['z_factor']) && is_numeric($averages['z_factor']) ? number_format($averages['z_factor'], 4) : 'N/A' }}
                        </div>
                        <div class="metric-label-small">Compressibility (Z)</div>
                        <div class="metric-unit">Dimensionless</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- H2S Metrics (if available) -->
    @if(isset($averages['h2s_avg']) || isset($averages['h2s_max']) || isset($averages['h2s_min']))
    <div class="metric-card-modern" style="--header-color-1: #14b8a6; --header-color-2: #0f766e; border-left-color: #14b8a6;">
        <div class="metric-card-header">
            <i class="fe fe-alert-triangle mr-2"></i>H2S Metrics
        </div>
        <div class="metric-grid">
            @if(isset($averages['h2s_avg']) && is_numeric($averages['h2s_avg']))
            <div class="metric-item-box">
                <div class="metric-value-large" style="font-size: 1.5rem;">
                    {{ number_format($averages['h2s_avg'], 2) }}
                </div>
                <div class="metric-label-small">Average H2S</div>
                <div class="metric-unit">ppm</div>
            </div>
            @endif
            @if(isset($averages['h2s_max']) && is_numeric($averages['h2s_max']))
            <div class="metric-item-box">
                <div class="metric-value-large" style="font-size: 1.5rem;">
                    {{ number_format($averages['h2s_max'], 2) }}
                </div>
                <div class="metric-label-small">Maximum H2S</div>
                <div class="metric-unit">ppm</div>
            </div>
            @endif
            @if(isset($averages['h2s_min']) && is_numeric($averages['h2s_min']))
            <div class="metric-item-box">
                <div class="metric-value-large" style="font-size: 1.5rem;">
                    {{ number_format($averages['h2s_min'], 2) }}
                </div>
                <div class="metric-label-small">Minimum H2S</div>
                <div class="metric-unit">ppm</div>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Safety Issues Alert -->
    @if(isset($averages['safety_issues']) && !empty($averages['safety_issues']))
    <div class="safety-alert">
        <div class="safety-alert-header">
            <i class="fe fe-alert-circle mr-2" style="font-size: 1.5rem;"></i>
            Safety Issues Detected
        </div>
        <div class="row">
            @if(isset($averages['safety_issues']['voltage_issues']) && count($averages['safety_issues']['voltage_issues']) > 0)
            <div class="col-md-4">
                <div style="background: white; padding: 1rem; border-radius: 8px; margin-bottom: 0.5rem;">
                    <div style="font-weight: 700; color: #92400e; margin-bottom: 0.5rem;">
                        <i class="fe fe-zap mr-2"></i>Voltage Issues
                    </div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: #f59e0b;">
                        {{ count($averages['safety_issues']['voltage_issues']) }}
                    </div>
                    <div style="font-size: 0.875rem; color: #78350f; margin-top: 0.5rem;">
                        @php
                            $voltages = array_column($averages['safety_issues']['voltage_issues'], 'value');
                            $avgVoltage = count($voltages) > 0 ? array_sum($voltages) / count($voltages) : 0;
                        @endphp
                        Avg: {{ number_format($avgVoltage, 2) }}V
                    </div>
                </div>
            </div>
            @endif

            @if(isset($averages['safety_issues']['pressure_issues']) && count($averages['safety_issues']['pressure_issues']) > 0)
            <div class="col-md-4">
                <div style="background: white; padding: 1rem; border-radius: 8px; margin-bottom: 0.5rem;">
                    <div style="font-weight: 700; color: #92400e; margin-bottom: 0.5rem;">
                        <i class="fe fe-gauge mr-2"></i>Pressure Issues
                    </div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: #f59e0b;">
                        {{ count($averages['safety_issues']['pressure_issues']) }}
                    </div>
                    <div style="font-size: 0.875rem; color: #78350f; margin-top: 0.5rem;">
                        @php
                            $pressures = array_column($averages['safety_issues']['pressure_issues'], 'value');
                            $avgPressure = count($pressures) > 0 ? array_sum($pressures) / count($pressures) : 0;
                        @endphp
                        Avg: {{ number_format($avgPressure, 2) }} psi
                    </div>
                </div>
            </div>
            @endif

            @if(isset($averages['safety_issues']['temperature_issues']) && count($averages['safety_issues']['temperature_issues']) > 0)
            <div class="col-md-4">
                <div style="background: white; padding: 1rem; border-radius: 8px; margin-bottom: 0.5rem;">
                    <div style="font-weight: 700; color: #92400e; margin-bottom: 0.5rem;">
                        <i class="fe fe-thermometer mr-2"></i>Temperature Issues
                    </div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: #f59e0b;">
                        {{ count($averages['safety_issues']['temperature_issues']) }}
                    </div>
                    <div style="font-size: 0.875rem; color: #78350f; margin-top: 0.5rem;">
                        @php
                            $temperatures = array_column($averages['safety_issues']['temperature_issues'], 'value');
                            $avgTemperature = count($temperatures) > 0 ? array_sum($temperatures) / count($temperatures) : 0;
                        @endphp
                        Avg: {{ number_format($avgTemperature, 2) }}°F
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Test Information -->
    <div class="metric-card-modern" style="--header-color-1: #06b6d4; --header-color-2: #0891b2; border-left-color: #06b6d4;">
        <div class="metric-card-header">
            <i class="fe fe-info mr-2"></i>Test Information
        </div>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Client</div>
                <div class="info-value">{{ $summary['client'] ?? 'N/A' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Field</div>
                <div class="info-value">{{ $summary['field'] ?? 'N/A' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Well Name</div>
                <div class="info-value">{{ $summary['well'] ?? 'N/A' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Wellhead Tower</div>
                <div class="info-value">{{ $summary['wellhead_tower'] ?? 'N/A' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Wellhead String</div>
                <div class="info-value">{{ $summary['wellhead_string'] ?? 'N/A' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Choke Size</div>
                <div class="info-value">{{ $summary['choke_size'] ?? 'N/A' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Upstream Choke</div>
                <div class="info-value">{{ $summary['upstream_choke'] ?? 'N/A' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Downstream Choke</div>
                <div class="info-value">{{ $summary['downstream_choke'] ?? 'N/A' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Start Time</div>
                <div class="info-value">{{ $summary['start_time'] ? $summary['start_time']->format('Y-m-d H:i') : 'N/A' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">End Time</div>
                <div class="info-value">{{ $summary['end_time'] ? $summary['end_time']->format('Y-m-d H:i') : 'N/A' }}</div>
            </div>
            @if(isset($summary['operator']))
            <div class="info-item">
                <div class="info-label">Operator</div>
                <div class="info-value">{{ $summary['operator'] }}</div>
            </div>
            @endif
            @if(isset($summary['test_date']))
            <div class="info-item">
                <div class="info-label">Test Date</div>
                <div class="info-value">{{ $summary['test_date'] }}</div>
            </div>
            @endif
        </div>
    </div>

    <!-- Charts Section -->
    <h3 class="section-title"><i class="fe fe-bar-chart mr-2"></i>Production Analysis Charts</h3>

    <!-- Chart Info -->
    <div class="row mb-3">
        <div class="col-md-12">
            <div style="background: #f8f9fa; padding: 1rem; border-radius: 10px;">
                <p style="margin: 0; color: #64748b; font-size: 0.875rem;">
                    <i class="fe fe-info mr-2"></i>
                    <strong>Interactive Charts:</strong> Use mouse wheel or buttons to zoom. Click and drag to pan. Each chart has individual controls.
                </p>
            </div>
        </div>
    </div>

    <!-- Production Rates Charts -->
    <div class="row">
        <div class="col-12 col-lg-6">
            <div class="chart-card">
                <div class="chart-header d-flex justify-content-between align-items-center">
                    <span><i class="fe fe-trending-up mr-2"></i>Production Rates Over Time</span>
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
                <div style="height: 350px; position: relative;">
                    <canvas id="ratesChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="chart-card">
                <div class="chart-header d-flex justify-content-between align-items-center">
                    <span><i class="fe fe-bar-chart-2 mr-2"></i>Gas-Oil Ratio (GOR) Trend</span>
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
                <div style="height: 350px; position: relative;">
                    <canvas id="gorChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Quality Metrics Charts -->
    <div class="row">
        <div class="col-12 col-lg-6">
            <div class="chart-card">
                <div class="chart-header d-flex justify-content-between align-items-center">
                    <span><i class="fe fe-droplet mr-2"></i>Basic Sediment & Water (BSW) Trend</span>
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
                <div style="height: 350px; position: relative;">
                    <canvas id="bswChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="chart-card">
                <div class="chart-header d-flex justify-content-between align-items-center">
                    <span><i class="fe fe-activity mr-2"></i>Water-Liquid Ratio (WLR) Trend</span>
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
                <div style="height: 350px; position: relative;">
                    <canvas id="wlrChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Pressure & Temperature Charts -->
    <div class="row">
        <div class="col-12 col-lg-6">
            <div class="chart-card">
                <div class="chart-header d-flex justify-content-between align-items-center">
                    <span><i class="fe fe-gauge mr-2"></i>Pressure Trend Over Time</span>
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
                <div style="height: 350px; position: relative;">
                    <canvas id="pressureChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="chart-card">
                <div class="chart-header d-flex justify-content-between align-items-center">
                    <span><i class="fe fe-thermometer mr-2"></i>Temperature Trend Over Time</span>
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
                <div style="height: 350px; position: relative;">
                    <canvas id="temperatureChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Gas Void Fraction Chart -->
    <div class="row">
        <div class="col-12">
            <div class="chart-card">
                <div class="chart-header d-flex justify-content-between align-items-center">
                    <span><i class="fe fe-wind mr-2"></i>Gas Void Fraction (GVF) Trend</span>
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
                <div style="height: 350px; position: relative;">
                    <canvas id="gvfChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Full Average Row Table -->
    <h3 class="section-title"><i class="fe fe-database mr-2"></i>Complete Average Metrics</h3>
    <div class="data-table-card">
        <div class="table-responsive">
            <table class="table modern-table table-striped table-sm mb-0">
                <thead>
                    <tr>
                        @foreach(array_keys($full_average_row) as $col)
                            <th style="min-width: 150px;">{{ $col }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        @foreach($full_average_row as $col => $value)
                            <td style="min-width: 150px;">
                                @if(is_numeric($value))
                                    {{ number_format($value, 2) }}
                                @else
                                    {{ $value }}
                                @endif
                            </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Save Test Section -->
    @if(isset($task_id))
    <div class="alert alert-info" style="background: linear-gradient(135deg, #dbeafe 0%, #eff6ff 100%); border-left: 4px solid #3b82f6; border-radius: 12px;">
        <div style="display: flex; align-items: center;">
            <i class="fe fe-info" style="font-size: 2rem; margin-right: 1rem; color: #1e40af;"></i>
            <div>
                <div style="font-weight: 700; font-size: 1.1rem; color: #1e3a8a; margin-bottom: 0.25rem;">
                    Task Completion
                </div>
                <div style="color: #1e40af;">
                    Saving this test will mark your assigned task as completed and notify the supervisor.
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="save-section">
        <div style="margin-bottom: 1.5rem;">
            <h3 style="color: #065f46; font-weight: 700; font-size: 1.5rem; margin-bottom: 0.5rem;">
                <i class="fe fe-save mr-2"></i>
                @if(isset($task_id))
                    Complete Task & Save Test
                @else
                    Save Test to Archive
                @endif
            </h3>
            <p style="color: #047857; margin-bottom: 0;">
                Review all metrics above and save this test to the archive for future reference and analysis.
            </p>
        </div>

        <form action="{{ route('tests.store') }}" method="POST">
            @csrf
            <input type="hidden" name="summary" value="{{ json_encode($summary) }}">
            <input type="hidden" name="averages" value="{{ json_encode($averages) }}">
            <input type="hidden" name="parsed_data" value="{{ json_encode($parsed_data) }}">
            <input type="hidden" name="temp_file_path" value="{{ $temp_file_path }}">
            <input type="hidden" name="filename" value="{{ $filename }}">
            <input type="hidden" name="operator" value="{{ $summary['operator'] ?? 'N/A' }}">
            <input type="hidden" name="test_date" value="{{ $summary['test_date'] ?? 'N/A' }}">
            <input type="hidden" name="test_type" value="{{ $summary['test_type'] ?? 'N/A' }}">
            <input type="hidden" name="full_average_row" value="{{ json_encode($full_average_row) }}">
            @if(isset($task_id) && $task_id)
            <input type="hidden" name="task_id" value="{{ $task_id }}">
            @endif
            @if(isset($new_task_id) && $new_task_id)
            <input type="hidden" name="new_task_id" value="{{ $new_task_id }}">
            @endif

            <button type="submit" class="btn btn-modern-success">
                <i class="fe fe-save mr-2"></i>
                @if((isset($task_id) && $task_id) || (isset($new_task_id) && $new_task_id))
                    Complete Task & Save Test
                @else
                    Save Test to Archive
                @endif
            </button>
        </form>
    </div>

    <!-- Detailed Log Table -->
    <h3 class="section-title"><i class="fe fe-table mr-2"></i>Detailed VX Log Data</h3>
    <div class="data-table-card">
        <div class="table-responsive">
            <table class="table modern-table table-striped table-sm mb-0" id="vxDetailTable" style="width:100%;">
                <thead>
                    <tr>
                        @foreach($parsed_data['test_data']['columns'] as $col)
                            <th style="min-width: 150px;">{{ $col }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($parsed_data['test_data']['rows'] as $row)
                    <tr>
                        @foreach($parsed_data['test_data']['columns'] as $col)
                            @php
                                $value = $row[$col] ?? '';
                                $class = '';
                                if (is_numeric($value)) {
                                    if (stripos($col, 'WHP') !== false && $value < 400) $class = 'table-danger';
                                    if (stripos($col, 'Temp') !== false && $value > 200) $class = 'table-warning';
                                    if (stripos($col, 'Press') !== false && $value < 200) $class = 'table-danger';
                                    if (stripos($col, 'Voltage') !== false && ($value < 100 || $value > 250)) $class = 'table-warning';
                                }
                            @endphp
                            <td class="{{ $class }}" style="min-width: 150px;">{{ $value }}</td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Raw Log File Content -->
    <h3 class="section-title"><i class="fe fe-file-text mr-2"></i>Raw Log File</h3>
    <div class="data-table-card">
        <div style="background: #f8f9fa; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
            <strong style="color: #2d3748;">Filename:</strong>
            <span style="color: #64748b;">{{ $filename }}</span>
        </div>
        <div class="raw-log-section">
            <pre style="margin: 0; white-space: pre-wrap; word-wrap: break-word;"><code>{{ $raw_log_content }}</code></pre>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@2.0.1/dist/chartjs-plugin-zoom.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/hammerjs@2.0.8"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ratesData = @json($rates_chart_data);
    const gorData = @json($gor_chart_data);
    const parsed_data = @json($parsed_data);

    // Store all chart instances globally
    window.allCharts = [];

    // Helper to add chart to global array
    function registerChart(chart) {
        if (chart) window.allCharts.push(chart);
        return chart;
    }

    // Common zoom plugin configuration
    const zoomOptions = {
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
        },
        limits: {
            x: {min: 'original', max: 'original'},
            y: {min: 'original', max: 'original'}
        }
    };

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
                        weight: '600'
                    }
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                titleFont: {
                    size: 13,
                    weight: 'bold'
                },
                bodyFont: {
                    size: 12
                },
                cornerRadius: 8
            },
            zoom: zoomOptions
        },
        scales: {
            x: {
                grid: {
                    display: false
                },
                ticks: {
                    font: {
                        size: 10
                    }
                }
            },
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                },
                ticks: {
                    font: {
                        size: 10
                    }
                }
            }
        }
    };

    // Extract data from parsed_data
    const testData = parsed_data.test_data || {};
    const rows = testData.rows || [];
    const columns = testData.columns || [];

    console.log('Available columns in log file:', columns);

    // Helper function to extract column data - searches for column by multiple possible names
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

    // Helper function to create time labels
    function createTimeLabels(dataLength) {
        const timeCol = columns.find(c => c.toLowerCase().includes('time'));
        if (timeCol) {
            return rows.slice(0, dataLength).map(row => row[timeCol] || '');
        }
        return Array.from({length: dataLength}, (_, i) => `Point ${i + 1}`);
    }

    // --- Production Rates Chart ---
    const ratesCtx = document.getElementById('ratesChart');
    if (ratesCtx && ratesData && ratesData.labels) {
        // Store original data for unit conversion
        ratesData.datasets.forEach(dataset => {
            dataset.originalData = [...dataset.data];
        });

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
    }

    // --- GOR Chart ---
    const gorCtx = document.getElementById('gorChart');
    if (gorCtx && gorData && gorData.labels) {
        const gorChart = new Chart(gorCtx.getContext('2d'), {
            type: 'line',
            data: gorData,
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
    }

    // --- BSW Chart ---
    const bswData = extractColumnData('BSW', 'BS&W', 'Basic Sediment & Water', 'BS_W', 'BSWC');
    const bswCtx = document.getElementById('bswChart');
    if (bswCtx) {
        if (bswData.length === 0) {
            // Show message if no data available
            bswCtx.parentElement.innerHTML = '<div style="height: 350px; display: flex; align-items: center; justify-content: center; color: #64748b; font-size: 0.9rem;"><i class="fe fe-alert-circle mr-2"></i>No BSW data available in log file</div>';
        } else {
        const bswChart = new Chart(bswCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: createTimeLabels(bswData.length),
                datasets: [{
                    label: 'BSW (%)',
                    data: bswData,
                    borderColor: '#8b5cf6',
                    backgroundColor: 'rgba(139, 92, 246, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
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
        }
    }

    // --- WLR Chart ---
    const wlrData = extractColumnData('WLR', 'Water-Liquid Ratio', 'Water Liquid Ratio', 'WLR%', 'WLRC');
    const wlrCtx = document.getElementById('wlrChart');
    if (wlrCtx) {
        if (wlrData.length === 0) {
            // Show message if no data available
            wlrCtx.parentElement.innerHTML = '<div style="height: 350px; display: flex; align-items: center; justify-content: center; color: #64748b; font-size: 0.9rem;"><i class="fe fe-alert-circle mr-2"></i>No WLR data available in log file</div>';
        } else{
        const wlrChart = new Chart(wlrCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: createTimeLabels(wlrData.length),
                datasets: [{
                    label: 'WLR (%)',
                    data: wlrData,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
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
        }
    }

    // --- Pressure Chart ---
    const pressureData = extractColumnData('Pressure', 'WHP', 'Press', 'Wellhead Pressure', 'P', 'WHPC');
    const pressureCtx = document.getElementById('pressureChart');
    if (pressureCtx) {
        if (pressureData.length === 0) {
            // Show message if no data available
            pressureCtx.parentElement.innerHTML = '<div style="height: 350px; display: flex; align-items: center; justify-content: center; color: #64748b; font-size: 0.9rem;"><i class="fe fe-alert-circle mr-2"></i>No Pressure data available in log file</div>';
        } else {
        const pressureChart = new Chart(pressureCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: createTimeLabels(pressureData.length),
                datasets: [{
                    label: 'Pressure (psi)',
                    data: pressureData,
                    borderColor: '#ec4899',
                    backgroundColor: 'rgba(236, 72, 153, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
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
        }
    }

    // --- Temperature Chart ---
    const tempData = extractColumnData('Temperature', 'Temp', 'T', 'WHT', 'Wellhead Temperature', 'WHTC');
    const tempCtx = document.getElementById('temperatureChart');
    if (tempCtx) {
        if (tempData.length === 0) {
            // Show message if no data available
            tempCtx.parentElement.innerHTML = '<div style="height: 350px; display: flex; align-items: center; justify-content: center; color: #64748b; font-size: 0.9rem;"><i class="fe fe-alert-circle mr-2"></i>No Temperature data available in log file</div>';
        } else {
        const tempChart = new Chart(tempCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: createTimeLabels(tempData.length),
                datasets: [{
                    label: 'Temperature (°F)',
                    data: tempData,
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
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
        }
    }

    // --- GVF Chart ---
    const gvfData = extractColumnData('GVF', 'Gas Void Fraction', 'GVF%', 'GVFC');
    const gvfCtx = document.getElementById('gvfChart');
    if (gvfCtx) {
        if (gvfData.length === 0) {
            // Show message if no data available
            gvfCtx.parentElement.innerHTML = '<div style="height: 350px; display: flex; align-items: center; justify-content: center; color: #64748b; font-size: 0.9rem;"><i class="fe fe-alert-circle mr-2"></i>No GVF data available in log file</div>';
        } else {
        const gvfChart = new Chart(gvfCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: createTimeLabels(gvfData.length),
                datasets: [{
                    label: 'GVF (%)',
                    data: gvfData,
                    borderColor: '#14b8a6',
                    backgroundColor: 'rgba(20, 184, 166, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
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
        }
    }

    // Unit conversion factors (base: STB)
    const unitConversions = {
        stb: { factor: 1, label: 'STB/d', gasLabel: 'MMScf/d' },
        m3: { factor: 0.158987, label: 'm³/d', gasLabel: 'Mm³/d' },
        bbl: { factor: 1, label: 'BBL/d', gasLabel: 'MMScf/d' } // BBL = STB
    };

    // Store charts by name for individual control - AFTER all charts are created
    window.chartInstances = {
        rates: window.allCharts.find((_, i) => i === 0),
        gor: window.allCharts.find((_, i) => i === 1),
        bsw: window.allCharts.find((_, i) => i === 2),
        wlr: window.allCharts.find((_, i) => i === 3),
        pressure: window.allCharts.find((_, i) => i === 4),
        temp: window.allCharts.find((_, i) => i === 5),
        gvf: window.allCharts.find((_, i) => i === 6)
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

    // Handle unit selector changes for individual Production Rates chart
    $('.unit-selector').on('change', function() {
        const chartName = $(this).data('chart');
        const selectedUnit = this.value;
        const conversion = unitConversions[selectedUnit];
        const chart = window.chartInstances[chartName];

        if (!chart) return;

        // Update chart datasets
        chart.data.datasets.forEach(dataset => {
            const isProductionRate = dataset.label && (
                dataset.label.includes('Oil') ||
                dataset.label.includes('Water') ||
                dataset.label.includes('Gas Rate')
            );

            if (isProductionRate && dataset.originalData) {
                dataset.data = dataset.originalData.map(val => {
                    if (val === null || val === undefined) return val;
                    if (dataset.label.includes('Gas')) {
                        return selectedUnit === 'm3' ? val * 28.317 : val;
                    }
                    return val * conversion.factor;
                });

                // Update dataset label
                if (dataset.label.includes('Gas')) {
                    dataset.label = `Gas Rate (${conversion.gasLabel})`;
                } else if (dataset.label.includes('Oil')) {
                    dataset.label = `Oil Rate (${conversion.label})`;
                } else if (dataset.label.includes('Water')) {
                    dataset.label = `Water Rate (${conversion.label})`;
                }
            }
        });

        // Update Y-axis label
        if (chart.options.scales.y && chart.options.scales.y.title) {
            chart.options.scales.y.title.text = conversion.label;
        }

        chart.update();
    });

    // --- Initialize DataTable ---
    const detailTable = document.getElementById('vxDetailTable');
    if (detailTable) {
        $('#vxDetailTable').DataTable({
            scrollX: true,
            pageLength: 25,
            lengthMenu: [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
            language: {
                search: "Search log data:",
                lengthMenu: "Show _MENU_ rows",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            }
        });
    }
});
</script>
@endsection
