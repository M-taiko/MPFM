@extends('layouts.master')

@section('title', 'Test Details - ' . $mpfmTest->id)

@section('css')
<style>
    .detail-card {
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

    .detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }

    .detail-item {
        padding: 15px;
        background: #f8f9fa;
        border-radius: 6px;
    }

    .detail-label {
        font-size: 0.85rem;
        color: #666;
        margin-bottom: 5px;
    }

    .detail-value {
        font-size: 1.3rem;
        font-weight: bold;
        color: #333;
    }

    .comparison-table {
        width: 100%;
        border-collapse: collapse;
    }

    .comparison-table th {
        background: #f8f9fa;
        padding: 12px;
        text-align: left;
        border-bottom: 2px solid #dee2e6;
        font-weight: bold;
    }

    .comparison-table td {
        padding: 12px;
        border-bottom: 1px solid #dee2e6;
    }

    .comparison-table tr:hover {
        background: #f8f9fa;
    }

    .badge-normal { background: #d1ecf1; color: #0c5460; }
    .badge-notable { background: #fff3cd; color: #856404; }
    .badge-significant { background: #ffc75f; color: #6d4c00; }
    .badge-anomaly { background: #f8d7da; color: #721c24; }
    .badge-critical { background: #dc3545; color: white; }

    .nav-tabs .nav-link {
        color: #666;
        border: none;
        border-bottom: 3px solid transparent;
        padding: 12px 20px;
    }

    .nav-tabs .nav-link.active {
        background: transparent;
        border-bottom-color: #667eea;
        color: #667eea;
    }

    .trend-chart {
        margin-top: 30px;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 6px;
    }

    .info-section {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 2px solid #dee2e6;
    }

    .action-buttons {
        margin-top: 20px;
        display: flex;
        gap: 10px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header">
        <h1>
            <i class="fe fe-clipboard"></i>
            Test Details #{{ $mpfmTest->id }}
        </h1>
        <p style="margin: 10px 0 0 0; opacity: 0.9;">
            {{ $mpfmTest->oilString->name }} | Operator: {{ $mpfmTest->user->name }} | Date: {{ $mpfmTest->test_date }}
        </p>
    </div>

    <!-- Test Timing -->
    @if($mpfmTest->test_started_at)
    <div class="detail-card">
        <h5>Test Timing Information</h5>
        <div class="detail-grid">
            <div class="detail-item">
                <div class="detail-label">Test Started At</div>
                <div class="detail-value" style="font-size: 1rem;">{{ $mpfmTest->test_started_at?->format('M d, Y H:i:s') ?? 'N/A' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Test Completed At</div>
                <div class="detail-value" style="font-size: 1rem;">{{ $mpfmTest->test_completed_at?->format('M d, Y H:i:s') ?? 'N/A' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Total Duration</div>
                <div class="detail-value">{{ round($mpfmTest->test_duration_hours ?? 0, 2) }} hrs</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Stable Duration</div>
                <div class="detail-value">{{ round($mpfmTest->test_stable_duration_hours ?? 0, 2) }} hrs</div>
            </div>
        </div>
    </div>
    @endif

    <!-- Test Data -->
    <div class="detail-card">
        <h5>Test Results (Extracted from Log File)</h5>
        <div class="detail-grid">
            <div class="detail-item">
                <div class="detail-label">Oil Rate SC (STB/d)</div>
                <div class="detail-value">{{ number_format($mpfmTest->qo_sc ?? 0, 2) }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Water Cut / BSW (%)</div>
                <div class="detail-value">{{ number_format($mpfmTest->bsw ?? 0, 2) }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">GOR (Scf/STB)</div>
                <div class="detail-value">{{ number_format($mpfmTest->gor ?? 0, 2) }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Gas Rate SC (MMScf/d)</div>
                <div class="detail-value">{{ number_format($mpfmTest->qg_sc ?? 0, 2) }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Pressure Avg (psia)</div>
                <div class="detail-value">{{ number_format($mpfmTest->pressure_avg ?? $mpfmTest->pressure ?? 0, 2) }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Temperature Avg (°C)</div>
                <div class="detail-value">{{ number_format($mpfmTest->temperature_avg ?? $mpfmTest->temperature ?? 0, 2) }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">GVF (%)</div>
                <div class="detail-value">{{ number_format($mpfmTest->gvf ?? 0, 2) }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">WLR</div>
                <div class="detail-value">{{ number_format($mpfmTest->wlr ?? 0, 2) }}</div>
            </div>
        </div>

        @if($mpfmTest->notes)
        <div class="info-section">
            <h6>Operator Notes</h6>
            <p>{{ $mpfmTest->notes }}</p>
        </div>
        @endif
    </div>

    <!-- Tabs -->
    <div class="detail-card">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#validation">Validation</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#comparison">Historical Comparison</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#trends">Trends</a>
            </li>
        </ul>

        <div class="tab-content" style="padding: 20px; background: white; border: 1px solid #dee2e6; border-top: none;">
            <!-- Validation Tab -->
            <div id="validation" class="tab-pane fade show active">
                @if($mpfmTest->latestValidation)
                    <h6>Validation Results</h6>
                    <p><strong>Status:</strong> <span class="badge badge-{{ $mpfmTest->latestValidation->overall_status === 'pass' ? 'success' : ($mpfmTest->latestValidation->overall_status === 'warning' ? 'warning' : 'danger') }}">{{ ucfirst($mpfmTest->latestValidation->overall_status) }}</span></p>
                    <p><strong>Confidence Score:</strong> {{ $mpfmTest->latestValidation->confidence_score }}%</p>
                    <p><strong>Suggestion:</strong> <span class="badge badge-{{ $mpfmTest->latestValidation->auto_suggestion === 'approve' ? 'success' : ($mpfmTest->latestValidation->auto_suggestion === 'review' ? 'warning' : 'danger') }}">{{ ucfirst($mpfmTest->latestValidation->auto_suggestion) }}</span></p>

                    <h6 style="margin-top: 20px;">Rule Results</h6>
                    @foreach($mpfmTest->latestValidation->rule_results as $result)
                    <div style="padding: 10px; margin-bottom: 8px; background: {{ $result['result'] === 'pass' ? '#f8fff9' : ($result['result'] === 'warning' ? '#fffbf0' : '#fff5f5') }}; border-left: 4px solid {{ $result['result'] === 'pass' ? '#28a745' : ($result['result'] === 'warning' ? '#ffc107' : '#dc3545') }}; border-radius: 4px;">
                        <strong>{{ $result['rule_name'] }}</strong>
                        <span class="badge badge-{{ $result['result'] === 'pass' ? 'success' : ($result['result'] === 'warning' ? 'warning' : 'danger') }}">{{ ucfirst($result['result']) }}</span>
                        <p style="margin: 5px 0 0 0; font-size: 0.9rem;">{{ $result['message'] }}</p>
                    </div>
                    @endforeach
                @else
                    <p class="text-muted">No validation data available</p>
                @endif
            </div>

            <!-- Comparison Tab -->
            <div id="comparison" class="tab-pane fade">
                @if($mpfmTest->latestValidation && $mpfmTest->latestValidation->historical_comparison)
                    <h6>Historical Statistics</h6>
                    <p><strong>Tests Compared:</strong> {{ $mpfmTest->latestValidation->historical_comparison['tests_compared'] }}</p>
                    <p><strong>Date Range:</strong> {{ $mpfmTest->latestValidation->historical_comparison['date_range']['from'] }} to {{ $mpfmTest->latestValidation->historical_comparison['date_range']['to'] }}</p>

                    <h6 style="margin-top: 20px;">Current vs. Historical Average</h6>
                    <table class="comparison-table">
                        <thead>
                            <tr>
                                <th>Parameter</th>
                                <th>Current Value</th>
                                <th>Historical Average</th>
                                <th>Deviation %</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($mpfmTest->latestValidation->historical_comparison['current_deviations'] as $param => $dev)
                            <tr>
                                <td><strong>{{ ucfirst(str_replace('_', ' ', $param)) }}</strong></td>
                                <td>{{ $dev['current_value'] }}</td>
                                <td>{{ $dev['average'] }}</td>
                                <td>
                                    <strong>{{ $dev['percent_diff'] > 0 ? '+' : '' }}{{ $dev['percent_diff'] }}%</strong>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $dev['deviation_type'] }}">
                                        {{ ucfirst($dev['deviation_type']) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-muted">Insufficient historical data for comparison</p>
                @endif
            </div>

            <!-- Trends Tab -->
            <div id="trends" class="tab-pane fade">
                <h6 style="margin-bottom: 20px;">Well String Performance Trends</h6>

                <!-- Production Rates Chart -->
                @if(isset($trendingCharts['production_rates']))
                <div style="margin-bottom: 30px;">
                    <h6>Production Rates Over Time</h6>
                    <canvas id="productionRatesChart" height="80"></canvas>
                </div>
                @endif

                <!-- GOR Trend Chart -->
                @if(isset($trendingCharts['gor_trend']))
                <div style="margin-bottom: 30px;">
                    <h6>Gas-Oil Ratio (GOR) Trend</h6>
                    <canvas id="gorTrendChart" height="80"></canvas>
                </div>
                @endif

                <!-- BSW Trend Chart -->
                @if(isset($trendingCharts['bsw_trend']))
                <div style="margin-bottom: 30px;">
                    <h6>Basic Sediment & Water (BSW) Trend</h6>
                    <canvas id="bswTrendChart" height="80"></canvas>
                </div>
                @endif

                <!-- WLR Trend Chart -->
                @if(isset($trendingCharts['wlr_trend']))
                <div style="margin-bottom: 30px;">
                    <h6>Water-Liquid Ratio (WLR) Trend</h6>
                    <canvas id="wlrTrendChart" height="80"></canvas>
                </div>
                @endif

                <!-- Trend Analysis Summary -->
                @if($mpfmTest->latestValidation && $mpfmTest->latestValidation->historical_comparison && isset($mpfmTest->latestValidation->historical_comparison['trends']))
                <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #dee2e6;">
                    <h6>Trend Analysis Summary</h6>
                    @foreach($mpfmTest->latestValidation->historical_comparison['trends'] as $param => $trend)
                    <div style="padding: 15px; margin-bottom: 15px; background: #f8f9fa; border-radius: 6px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <strong>{{ ucfirst(str_replace('_', ' ', $param)) }}</strong>
                                <span class="badge badge-{{ $trend['direction'] === 'increasing' ? 'danger' : ($trend['direction'] === 'decreasing' ? 'info' : 'success') }}" style="margin-left: 10px;">
                                    {{ ucfirst($trend['direction']) }}
                                </span>
                            </div>
                            <div style="text-align: right;">
                                <div><small>R²: {{ $trend['r_squared'] }}</small></div>
                                <div><small>Significance: <strong>{{ ucfirst($trend['significance']) }}</strong></small></div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                    <p class="text-muted">No detailed trend analysis available</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Detailed Metrics Display -->
    @if($mpfmTest->detailed_metrics)
    <div class="detail-card">
        <h5>Detailed Metrics Analysis</h5>
        <x-detailed-metrics-display :metrics="$mpfmTest->detailed_metrics" />
    </div>
    @endif

    <!-- Action Buttons -->
    <div class="action-buttons">
        <a href="{{ route('mpfm_tests.string_analysis', $mpfmTest->oil_string_id) }}" class="btn btn-primary">
            <i class="fa fa-chart-line"></i> View String Analysis
        </a>
        @if(auth()->user()->hasAnyRole(['mpfm_supervisor', 'surveillance_supervisor']))
            <a href="{{ route('supervisor.test_approval', $mpfmTest->id) }}" class="btn btn-info">
                <i class="fa fa-check-circle"></i> Approve/Review
            </a>
        @endif
        <a href="{{ route('mpfm_tests.trending_dashboard') }}" class="btn btn-secondary">
            <i class="fa fa-trending-up"></i> Trending Dashboard
        </a>
    </div>
</div>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
// Production Rates Chart
@if(isset($trendingCharts['production_rates']))
const productionCtx = document.getElementById('productionRatesChart')?.getContext('2d');
if (productionCtx) {
    new Chart(productionCtx, {
        type: 'line',
        data: {!! json_encode($trendingCharts['production_rates']) !!},
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Rate (STB/d)'
                    }
                }
            }
        }
    });
}
@endif

// GOR Trend Chart
@if(isset($trendingCharts['gor_trend']))
const gorCtx = document.getElementById('gorTrendChart')?.getContext('2d');
if (gorCtx) {
    new Chart(gorCtx, {
        type: 'line',
        data: {!! json_encode($trendingCharts['gor_trend']) !!},
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'GOR (Scf/STB)'
                    }
                }
            }
        }
    });
}
@endif

// BSW Trend Chart
@if(isset($trendingCharts['bsw_trend']))
const bswCtx = document.getElementById('bswTrendChart')?.getContext('2d');
if (bswCtx) {
    new Chart(bswCtx, {
        type: 'line',
        data: {!! json_encode($trendingCharts['bsw_trend']) !!},
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    title: {
                        display: true,
                        text: 'BSW (%)'
                    }
                }
            }
        }
    });
}
@endif

// WLR Trend Chart
@if(isset($trendingCharts['wlr_trend']))
const wlrCtx = document.getElementById('wlrTrendChart')?.getContext('2d');
if (wlrCtx) {
    new Chart(wlrCtx, {
        type: 'line',
        data: {!! json_encode($trendingCharts['wlr_trend']) !!},
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'WLR'
                    }
                }
            }
        }
    });
}
@endif
</script>
@endsection

@endsection
