@extends('layouts.master')

@section('title', 'Compare Well Tests')

@section('css')
<style>
    .compare-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
    }

    .test-comparison-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        margin-bottom: 2rem;
        overflow: hidden;
    }

    .test-card-header {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        padding: 1.5rem;
        font-weight: 600;
    }

    .chart-card-compare {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        background: white;
        margin-bottom: 1rem;
    }

    .chart-card-compare .card-header {
        background: transparent;
        border-bottom: 1px solid #e2e8f0;
        padding: 1rem 1.25rem;
    }

    .chart-card-compare .card-title {
        font-size: 0.95rem;
        font-weight: 600;
        color: #2d3748;
        margin: 0;
    }

    .metric-grid-compare {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 1rem;
        padding: 1.25rem;
    }

    .metric-item-compare {
        text-align: center;
        padding: 0.75rem;
        background: #f7fafc;
        border-radius: 10px;
    }

    .metric-value-compare {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2d3748;
    }

    .metric-label-compare {
        font-size: 0.7rem;
        color: #718096;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 0.25rem;
    }

    .diff-badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-top: 0.25rem;
    }

    .diff-positive {
        background: #d1fae5;
        color: #065f46;
    }

    .diff-negative {
        background: #fee2e2;
        color: #991b1b;
    }

    .diff-neutral {
        background: #e5e7eb;
        color: #4b5563;
    }
</style>
@endsection

@section('content')
<div class="main-container container-fluid">
    <!-- Page Header -->
    <div class="compare-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="mb-2" style="font-size: 2rem; font-weight: 700;">
                    <i class="fe fe-git-compare mr-2"></i>Well Tests Comparison
                </h1>
                <p class="mb-0" style="opacity: 0.9;">
                    Side-by-side analysis of {{ count($comparisonData) }} well tests
                </p>
            </div>
            <div class="col-md-4 text-right">
                <a href="{{ route('well_tests.index') }}" class="btn btn-light btn-lg">
                    <i class="fe fe-arrow-left mr-2"></i>Back to Archive
                </a>
            </div>
        </div>
    </div>

    <!-- Chart Controls -->
    <div class="row mb-3">
        <div class="col-md-6">
            <div style="background: #f8f9fa; padding: 1rem; border-radius: 10px;">
                <p style="margin: 0; color: #64748b; font-size: 0.875rem;">
                    <i class="fe fe-info mr-2"></i>
                    <strong>Interactive Charts:</strong> Use mouse wheel or buttons to zoom. Click and drag to pan. Double-click to reset.
                </p>
            </div>
        </div>
        <div class="col-md-6">
            <div style="background: white; padding: 1rem; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <label style="font-size: 0.875rem; font-weight: 600; color: #64748b; margin-bottom: 0.5rem;">
                            <i class="fe fe-sliders mr-2"></i>Production Unit
                        </label>
                        <select id="unitSelector" class="form-control form-control-sm">
                            <option value="stb">STB/d (Stock Tank Barrel per Day)</option>
                            <option value="m3">m³/d (Cubic Meter per Day)</option>
                            <option value="bbl">BBL/d (Barrel per Day)</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label style="font-size: 0.875rem; font-weight: 600; color: #64748b; margin-bottom: 0.5rem;">
                            <i class="fe fe-zoom-in mr-2"></i>Chart Zoom
                        </label>
                        <div class="btn-group btn-group-sm" role="group" style="width: 100%;">
                            <button type="button" class="btn btn-outline-primary" onclick="zoomAllCharts('in')">
                                <i class="fe fe-zoom-in"></i> Zoom In
                            </button>
                            <button type="button" class="btn btn-outline-primary" onclick="zoomAllCharts('out')">
                                <i class="fe fe-zoom-out"></i> Zoom Out
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="resetAllCharts()">
                                <i class="fe fe-rotate-ccw"></i> Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Test Comparison Cards -->
    @foreach($comparisonData as $index => $test)
    <div class="test-comparison-card">
        <div class="test-card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-1" style="font-size: 1.25rem;">{{ $test['well'] ?? 'N/A' }}</h3>
                    <p class="mb-0" style="opacity: 0.9; font-size: 0.9rem;">
                        {{ $test['client'] ?? 'N/A' }} • {{ $test['field'] ?? 'N/A' }}
                        @if($test['start_time'] && $test['end_time'])
                        • {{ $test['start_time'] }} → {{ $test['end_time'] }}
                        @endif
                    </p>
                </div>
                <span class="badge badge-light" style="font-size: 0.9rem; padding: 0.5rem 1rem;">
                    Test #{{ $index + 1 }}
                </span>
            </div>
        </div>

        <div class="card-body p-3">
            <!-- Key Metrics -->
            <div class="metric-grid-compare">
                <div class="metric-item-compare">
                    <div class="metric-value-compare" data-test-id="{{ $test['id'] }}" data-metric="oil_rate">
                        {{ is_numeric($test['metrics']['oil_rate_avg'] ?? null) ? number_format($test['metrics']['oil_rate_avg'], 2) : 'N/A' }}
                    </div>
                    <div class="metric-label-compare">Oil Rate (STB/d)</div>
                </div>
                <div class="metric-item-compare">
                    <div class="metric-value-compare" data-test-id="{{ $test['id'] }}" data-metric="gas_rate">
                        {{ is_numeric($test['metrics']['gas_rate_avg'] ?? null) ? number_format($test['metrics']['gas_rate_avg'], 2) : 'N/A' }}
                    </div>
                    <div class="metric-label-compare">Gas Rate (MMScf/d)</div>
                </div>
                <div class="metric-item-compare">
                    <div class="metric-value-compare" data-test-id="{{ $test['id'] }}" data-metric="water_rate">
                        {{ is_numeric($test['metrics']['water_rate_avg'] ?? null) ? number_format($test['metrics']['water_rate_avg'], 2) : 'N/A' }}
                    </div>
                    <div class="metric-label-compare">Water Rate (STB/d)</div>
                </div>
                <div class="metric-item-compare">
                    <div class="metric-value-compare">
                        {{ is_numeric($test['metrics']['total_gor_avg'] ?? null) ? number_format($test['metrics']['total_gor_avg'], 0) : 'N/A' }}
                    </div>
                    <div class="metric-label-compare">GOR (Scf/STB)</div>
                </div>
                <div class="metric-item-compare">
                    <div class="metric-value-compare">
                        {{ is_numeric($test['metrics']['bsw_avg'] ?? null) ? number_format($test['metrics']['bsw_avg'], 1) : 'N/A' }}%
                    </div>
                    <div class="metric-label-compare">BSW</div>
                </div>
                <div class="metric-item-compare">
                    <div class="metric-value-compare">
                        {{ is_numeric($test['metrics']['wlr_avg'] ?? null) ? number_format($test['metrics']['wlr_avg'], 1) : 'N/A' }}%
                    </div>
                    <div class="metric-label-compare">WLR</div>
                </div>
                <div class="metric-item-compare">
                    <div class="metric-value-compare">
                        {{ is_numeric($test['metrics']['gvf_avg'] ?? null) ? number_format($test['metrics']['gvf_avg'], 1) : 'N/A' }}%
                    </div>
                    <div class="metric-label-compare">GVF</div>
                </div>
            </div>

            <!-- Charts Grid -->
            <div class="row mt-3">
                <div class="col-lg-6">
                    <div class="chart-card-compare">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fe fe-trending-up mr-2"></i>Production Rates</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="rates-{{ $test['id'] }}" height="200"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="chart-card-compare">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fe fe-activity mr-2"></i>GOR Trend</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="gor-{{ $test['id'] }}" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <div class="chart-card-compare">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fe fe-droplet mr-2"></i>BSW Trend</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="bsw-{{ $test['id'] }}" height="200"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="chart-card-compare">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fe fe-percent mr-2"></i>WLR Trend</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="wlr-{{ $test['id'] }}" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <div class="chart-card-compare">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fe fe-thermometer mr-2"></i>Pressure Trend</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="pressure-{{ $test['id'] }}" height="200"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="chart-card-compare">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fe fe-thermometer mr-2"></i>Temperature Trend</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="temp-{{ $test['id'] }}" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <div class="chart-card-compare">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fe fe-wind mr-2"></i>GVF Trend</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="gvf-{{ $test['id'] }}" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/hammerjs@2.0.8"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@2.0.1/dist/chartjs-plugin-zoom.min.js"></script>
<script>
$(document).ready(function() {
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

    // Get comparison data
    const comparisonData = @json($comparisonData);

    // Store original metric values for unit conversion
    const originalMetrics = {};
    comparisonData.forEach(test => {
        originalMetrics[test.id] = {
            oil_rate: test.metrics.oil_rate_avg || 0,
            gas_rate: test.metrics.gas_rate_avg || 0,
            water_rate: test.metrics.water_rate_avg || 0
        };
    });

    // Helper function to extract column data with flexible name matching
    function extractColumnData(columns, rows, ...possibleNames) {
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
                    padding: 12,
                    font: {
                        size: 10,
                        weight: '500'
                    }
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 10,
                cornerRadius: 6,
                titleFont: {
                    size: 12,
                    weight: '600'
                },
                bodyFont: {
                    size: 11
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
                        size: 10
                    }
                }
            },
            x: {
                grid: {
                    display: false
                },
                ticks: {
                    font: {
                        size: 9
                    },
                    maxRotation: 45,
                    minRotation: 45
                }
            }
        }
    };

    // Create charts for each test
    comparisonData.forEach(test => {
        const rows = test.test_data?.rows || [];
        const columns = test.test_data?.columns || [];
        const labels = rows.map((_, i) => `Point ${i + 1}`);

        // Extract data
        const oilRateData = extractColumnData(columns, rows, 'Oil Rate', 'OilRate', 'OILR', 'Oil_Rate', 'Oil Production', 'OILC', 'qo_lc[bbl/d]');
        const waterRateData = extractColumnData(columns, rows, 'Water Rate', 'WaterRate', 'WATR', 'Water_Rate', 'Water Production', 'WATC', 'qw_lc[bbl/d]');
        const gasRateData = extractColumnData(columns, rows, 'Gas Rate', 'GasRate', 'GASR', 'Gas_Rate', 'Gas Production', 'GASC', 'qg_lc[Mcf/d]');
        const gorData = extractColumnData(columns, rows, 'GOR', 'Gas-Oil Ratio', 'Gas Oil Ratio', 'GORC');
        const bswData = extractColumnData(columns, rows, 'BSW', 'BS&W', 'Basic Sediment & Water', 'BS_W', 'BSWC');
        const wlrData = extractColumnData(columns, rows, 'WLR', 'Water-Liquid Ratio', 'Water Liquid Ratio', 'WLR%', 'WLRC');
        const pressureData = extractColumnData(columns, rows, 'Pressure', 'WHP', 'Press', 'Wellhead Pressure', 'P', 'WHPC');
        const tempData = extractColumnData(columns, rows, 'Temperature', 'Temp', 'WHT', 'Wellhead Temperature', 'T', 'WHTC');
        const gvfData = extractColumnData(columns, rows, 'GVF', 'Gas Void Fraction', 'Gas_Void_Fraction', 'GVFC');

        // Production Rates Chart
        const ratesCtx = document.getElementById(`rates-${test.id}`);
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
                options: commonOptions
            });
            registerChart(ratesChart);
        }

        // GOR Chart
        const gorCtx = document.getElementById(`gor-${test.id}`);
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
                options: commonOptions
            });
            registerChart(gorChart);
        }

        // BSW Chart
        const bswCtx = document.getElementById(`bsw-${test.id}`);
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
                options: commonOptions
            });
            registerChart(bswChart);
        }

        // WLR Chart
        const wlrCtx = document.getElementById(`wlr-${test.id}`);
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
                options: commonOptions
            });
            registerChart(wlrChart);
        }

        // Pressure Chart
        const pressureCtx = document.getElementById(`pressure-${test.id}`);
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
                options: commonOptions
            });
            registerChart(pressureChart);
        }

        // Temperature Chart
        const tempCtx = document.getElementById(`temp-${test.id}`);
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
                options: commonOptions
            });
            registerChart(tempChart);
        }

        // GVF Chart
        const gvfCtx = document.getElementById(`gvf-${test.id}`);
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
                options: commonOptions
            });
            registerChart(gvfChart);
        }
    });

    // Global zoom control functions
    window.zoomAllCharts = function(direction) {
        window.allCharts.forEach(chart => {
            if (!chart || !chart.zoom) return;
            if (direction === 'in') {
                chart.zoom(1.2);
            } else if (direction === 'out') {
                chart.zoom(0.8);
            }
        });
    };

    window.resetAllCharts = function() {
        window.allCharts.forEach(chart => {
            if (!chart || !chart.resetZoom) return;
            chart.resetZoom();
        });
    };

    // Unit conversion
    $('#unitSelector').on('change', function() {
        currentUnit = this.value;
        const conversion = unitConversions[currentUnit];

        // Update metric cards
        comparisonData.forEach(test => {
            const original = originalMetrics[test.id];

            let oilValue = original.oil_rate * conversion.factor;
            let waterValue = original.water_rate * conversion.factor;
            let gasValue = currentUnit === 'm3' ? original.gas_rate * 28.317 : original.gas_rate;

            $(`[data-test-id="${test.id}"][data-metric="oil_rate"]`).text(oilValue.toFixed(2));
            $(`[data-test-id="${test.id}"][data-metric="water_rate"]`).text(waterValue.toFixed(2));
            $(`[data-test-id="${test.id}"][data-metric="gas_rate"]`).text(gasValue.toFixed(2));
        });

        // Update chart labels
        $('.metric-label-compare').each(function() {
            const text = $(this).text();
            if (text.includes('Oil Rate') || text.includes('Water Rate')) {
                const baseText = text.includes('Oil') ? 'Oil Rate' : 'Water Rate';
                $(this).text(`${baseText} (${conversion.label})`);
            } else if (text.includes('Gas Rate')) {
                $(this).text(`Gas Rate (${conversion.gasLabel})`);
            }
        });

        // Update charts
        window.allCharts.forEach(chart => {
            chart.data.datasets.forEach(dataset => {
                const isProductionRate = dataset.label && (
                    dataset.label.includes('Oil') ||
                    dataset.label.includes('Water') ||
                    dataset.label.includes('Gas Rate')
                );

                if (isProductionRate && dataset.originalData) {
                    dataset.data = dataset.originalData.map(val => {
                        if (dataset.label.includes('Gas')) {
                            return currentUnit === 'm3' ? val * 28.317 : val;
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
});
</script>
@endsection
