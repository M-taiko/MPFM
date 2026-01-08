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

    .test-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        margin-bottom: 2rem;
        overflow: hidden;
        height: 100%;
    }

    .test-card-header {
        padding: 1.5rem;
        font-weight: 600;
    }

    .test-card-header-1 {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }

    .test-card-header-2 {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
    }

    .metric-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 1rem;
        padding: 1.25rem;
    }

    .metric-item {
        text-align: center;
        padding: 0.75rem;
        background: #f7fafc;
        border-radius: 10px;
        position: relative;
    }

    .metric-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2d3748;
    }

    .metric-label {
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
        font-size: 0.7rem;
        font-weight: 600;
        margin-top: 0.25rem;
    }

    .diff-higher {
        background: #d1fae5;
        color: #065f46;
    }

    .diff-lower {
        background: #fee2e2;
        color: #991b1b;
    }

    .diff-neutral {
        background: #e5e7eb;
        color: #4b5563;
    }

    .chart-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        background: white;
        margin-bottom: 1rem;
    }

    .chart-card .card-header {
        background: transparent;
        border-bottom: 1px solid #e2e8f0;
        padding: 1rem 1.25rem;
    }

    .chart-card .card-title {
        font-size: 0.95rem;
        font-weight: 600;
        color: #2d3748;
        margin: 0;
    }

    .detail-item {
        padding: 0.75rem;
        background: #f8f9fa;
        border-radius: 8px;
        margin-bottom: 0.5rem;
    }

    .detail-label {
        font-size: 0.75rem;
        color: #718096;
        text-transform: uppercase;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .detail-value {
        font-size: 0.95rem;
        color: #2d3748;
        font-weight: 500;
        margin-top: 0.25rem;
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
                <a href="{{ route('well_tests.compare') }}" class="btn btn-light btn-lg">
                    <i class="fe fe-plus mr-2"></i>Select Different Tests
                </a>
            </div>
        </div>
    </div>

    @php
        // Calculate differences for indicators
        $test1 = $comparisonData[0] ?? null;
        $test2 = $comparisonData[1] ?? null;

        function calculateDiff($val1, $val2) {
            if (!is_numeric($val1) || !is_numeric($val2) || $val2 == 0) return null;
            return (($val1 - $val2) / $val2) * 100;
        }

        function formatDiff($diff) {
            if ($diff === null) return '';
            $sign = $diff > 0 ? '+' : '';
            return $sign . number_format($diff, 1) . '%';
        }
    @endphp

    <!-- Side-by-Side Comparison -->
    <div class="row">
        @foreach($comparisonData as $index => $test)
        <div class="col-lg-6">
            <div class="test-card">
                <div class="test-card-header test-card-header-{{ ($index % 2) + 1 }}">
                    <h3 class="mb-1" style="font-size: 1.25rem;">{{ $test['well'] ?? 'N/A' }}</h3>
                    <p class="mb-0" style="opacity: 0.9; font-size: 0.9rem;">
                        {{ $test['client'] ?? 'N/A' }} • {{ $test['field'] ?? 'N/A' }}
                    </p>
                    @if($test['start_time'] && $test['end_time'])
                    <p class="mb-0 mt-2" style="opacity: 0.8; font-size: 0.85rem;">
                        <i class="fe fe-clock mr-1"></i>{{ $test['start_time'] }} → {{ $test['end_time'] }}
                    </p>
                    @endif
                </div>

                <div class="card-body p-0">
                    <!-- Key Production Metrics -->
                    <div class="metric-grid">
                        @php
                            $oilRate = $test['metrics']['oil_rate_avg'] ?? null;
                            $gasRate = $test['metrics']['gas_rate_avg'] ?? null;
                            $waterRate = $test['metrics']['water_rate_avg'] ?? null;
                            $gor = $test['metrics']['total_gor_avg'] ?? null;
                            $bsw = $test['metrics']['bsw_avg'] ?? null;
                            $wlr = $test['metrics']['wlr_avg'] ?? null;
                            $gvf = $test['metrics']['gvf_avg'] ?? null;

                            // Calculate differences relative to first test
                            if ($index === 1 && $test1) {
                                $oilDiff = calculateDiff($oilRate, $test1['metrics']['oil_rate_avg'] ?? 0);
                                $gasDiff = calculateDiff($gasRate, $test1['metrics']['gas_rate_avg'] ?? 0);
                                $waterDiff = calculateDiff($waterRate, $test1['metrics']['water_rate_avg'] ?? 0);
                                $gorDiff = calculateDiff($gor, $test1['metrics']['total_gor_avg'] ?? 0);
                                $bswDiff = calculateDiff($bsw, $test1['metrics']['bsw_avg'] ?? 0);
                                $wlrDiff = calculateDiff($wlr, $test1['metrics']['wlr_avg'] ?? 0);
                                $gvfDiff = calculateDiff($gvf, $test1['metrics']['gvf_avg'] ?? 0);
                            }
                        @endphp

                        <div class="metric-item">
                            <div class="metric-value" data-test-id="{{ $test['id'] }}" data-metric="oil_rate">
                                {{ is_numeric($oilRate) ? number_format($oilRate, 2) : 'N/A' }}
                            </div>
                            <div class="metric-label">Oil Rate (STB/d)</div>
                            @if($index === 1 && isset($oilDiff))
                                <div class="diff-badge diff-{{ $oilDiff > 0 ? 'higher' : ($oilDiff < 0 ? 'lower' : 'neutral') }}">
                                    <i class="fe fe-{{ $oilDiff > 0 ? 'arrow-up' : 'arrow-down' }}"></i> {{ formatDiff($oilDiff) }}
                                </div>
                            @endif
                        </div>

                        <div class="metric-item">
                            <div class="metric-value" data-test-id="{{ $test['id'] }}" data-metric="gas_rate">
                                {{ is_numeric($gasRate) ? number_format($gasRate, 2) : 'N/A' }}
                            </div>
                            <div class="metric-label">Gas Rate (MMScf/d)</div>
                            @if($index === 1 && isset($gasDiff))
                                <div class="diff-badge diff-{{ $gasDiff > 0 ? 'higher' : ($gasDiff < 0 ? 'lower' : 'neutral') }}">
                                    <i class="fe fe-{{ $gasDiff > 0 ? 'arrow-up' : 'arrow-down' }}"></i> {{ formatDiff($gasDiff) }}
                                </div>
                            @endif
                        </div>

                        <div class="metric-item">
                            <div class="metric-value" data-test-id="{{ $test['id'] }}" data-metric="water_rate">
                                {{ is_numeric($waterRate) ? number_format($waterRate, 2) : 'N/A' }}
                            </div>
                            <div class="metric-label">Water Rate (STB/d)</div>
                            @if($index === 1 && isset($waterDiff))
                                <div class="diff-badge diff-{{ $waterDiff > 0 ? 'higher' : ($waterDiff < 0 ? 'lower' : 'neutral') }}">
                                    <i class="fe fe-{{ $waterDiff > 0 ? 'arrow-up' : 'arrow-down' }}"></i> {{ formatDiff($waterDiff) }}
                                </div>
                            @endif
                        </div>

                        <div class="metric-item">
                            <div class="metric-value">
                                {{ is_numeric($gor) ? number_format($gor, 0) : 'N/A' }}
                            </div>
                            <div class="metric-label">GOR (Scf/STB)</div>
                            @if($index === 1 && isset($gorDiff))
                                <div class="diff-badge diff-{{ $gorDiff > 0 ? 'higher' : ($gorDiff < 0 ? 'lower' : 'neutral') }}">
                                    <i class="fe fe-{{ $gorDiff > 0 ? 'arrow-up' : 'arrow-down' }}"></i> {{ formatDiff($gorDiff) }}
                                </div>
                            @endif
                        </div>

                        <div class="metric-item">
                            <div class="metric-value">
                                {{ is_numeric($bsw) ? number_format($bsw, 1) : 'N/A' }}%
                            </div>
                            <div class="metric-label">BSW</div>
                            @if($index === 1 && isset($bswDiff))
                                <div class="diff-badge diff-{{ $bswDiff > 0 ? 'higher' : ($bswDiff < 0 ? 'lower' : 'neutral') }}">
                                    <i class="fe fe-{{ $bswDiff > 0 ? 'arrow-up' : 'arrow-down' }}"></i> {{ formatDiff($bswDiff) }}
                                </div>
                            @endif
                        </div>

                        <div class="metric-item">
                            <div class="metric-value">
                                {{ is_numeric($wlr) ? number_format($wlr, 1) : 'N/A' }}%
                            </div>
                            <div class="metric-label">WLR</div>
                            @if($index === 1 && isset($wlrDiff))
                                <div class="diff-badge diff-{{ $wlrDiff > 0 ? 'higher' : ($wlrDiff < 0 ? 'lower' : 'neutral') }}">
                                    <i class="fe fe-{{ $wlrDiff > 0 ? 'arrow-up' : 'arrow-down' }}"></i> {{ formatDiff($wlrDiff) }}
                                </div>
                            @endif
                        </div>

                        <div class="metric-item">
                            <div class="metric-value">
                                {{ is_numeric($gvf) ? number_format($gvf, 1) : 'N/A' }}%
                            </div>
                            <div class="metric-label">GVF</div>
                            @if($index === 1 && isset($gvfDiff))
                                <div class="diff-badge diff-{{ $gvfDiff > 0 ? 'higher' : ($gvfDiff < 0 ? 'lower' : 'neutral') }}">
                                    <i class="fe fe-{{ $gvfDiff > 0 ? 'arrow-up' : 'arrow-down' }}"></i> {{ formatDiff($gvfDiff) }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Test Details -->
                    <div style="padding: 1.25rem; border-top: 1px solid #e2e8f0;">
                        <h6 style="font-weight: 700; color: #2d3748; margin-bottom: 1rem;">
                            <i class="fe fe-info mr-2"></i>Test Details
                        </h6>
                        <div class="row">
                            <div class="col-6">
                                <div class="detail-item">
                                    <div class="detail-label">Client</div>
                                    <div class="detail-value">{{ $test['client'] ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="detail-item">
                                    <div class="detail-label">Field</div>
                                    <div class="detail-value">{{ $test['field'] ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="detail-item">
                                    <div class="detail-label">Well</div>
                                    <div class="detail-value">{{ $test['well'] ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="detail-item">
                                    <div class="detail-label">Test ID</div>
                                    <div class="detail-value">#{{ $test['id'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Section -->
                    <div style="padding: 1.25rem; background: #f8f9fa;">
                        <h6 style="font-weight: 700; color: #2d3748; margin-bottom: 1rem;">
                            <i class="fe fe-bar-chart mr-2"></i>Production Charts
                        </h6>

                        <!-- Production Rates Chart -->
                        <div class="chart-card mb-3">
                            <div class="card-header">
                                <h6 class="card-title"><i class="fe fe-trending-up mr-2"></i>Production Rates</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="rates-{{ $test['id'] }}" height="200"></canvas>
                            </div>
                        </div>

                        <!-- GOR Chart -->
                        <div class="chart-card mb-3">
                            <div class="card-header">
                                <h6 class="card-title"><i class="fe fe-activity mr-2"></i>GOR Trend</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="gor-{{ $test['id'] }}" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
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

    // Get comparison data
    const comparisonData = @json($comparisonData);

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
        const oilRateData = extractColumnData(columns, rows, 'Oil Rate', 'OilRate', 'OILR', 'Oil_Rate', 'Oil Production', 'OILC', 'qo_lc[bbl/d]', 'qo');
        const waterRateData = extractColumnData(columns, rows, 'Water Rate', 'WaterRate', 'WATR', 'Water_Rate', 'Water Production', 'WATC', 'qw_lc[bbl/d]', 'qw');
        const gasRateData = extractColumnData(columns, rows, 'Gas Rate', 'GasRate', 'GASR', 'Gas_Rate', 'Gas Production', 'GASC', 'qg_lc[Mcf/d]', 'qg');
        const gorData = extractColumnData(columns, rows, 'GOR', 'Gas-Oil Ratio', 'Gas Oil Ratio', 'GORC');

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
    });

    console.log('Total charts created:', window.allCharts.length);
});
</script>
@endsection
