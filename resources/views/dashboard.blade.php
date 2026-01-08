@extends('layouts.master')

@section('title', 'Dashboard')

@section('css')
<style>
    .dashboard-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 20px;
        padding: 2.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
    }

    .metric-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
        background: white;
        position: relative;
    }

    .metric-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(180deg, var(--card-accent) 0%, transparent 100%);
    }

    .metric-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.15);
    }

    .metric-card .card-body {
        padding: 1.75rem;
    }

    .metric-icon {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        margin-bottom: 1rem;
        position: relative;
        overflow: hidden;
    }

    .metric-icon::after {
        content: '';
        position: absolute;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, transparent 100%);
    }

    .metric-value {
        font-size: 2.25rem;
        font-weight: 700;
        line-height: 1;
        margin-bottom: 0.5rem;
        color: #2d3748;
    }

    .metric-label {
        font-size: 0.875rem;
        font-weight: 500;
        color: #718096;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .metric-trend {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        font-size: 0.875rem;
        font-weight: 600;
        margin-top: 0.75rem;
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
    }

    .trend-up {
        background: #d1fae5;
        color: #065f46;
    }

    .trend-down {
        background: #fee2e2;
        color: #991b1b;
    }

    .chart-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        background: white;
    }

    .chart-card .card-header {
        background: transparent;
        border-bottom: 1px solid #e2e8f0;
        padding: 1.5rem 1.75rem;
    }

    .chart-card .card-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #2d3748;
        margin: 0;
    }

    .quick-action-btn {
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 1.25rem;
        text-align: center;
        transition: all 0.3s ease;
        display: block;
        color: #4a5568;
        font-weight: 500;
        background: white;
    }

    .quick-action-btn:hover {
        border-color: #667eea;
        background: #f7fafc;
        color: #667eea;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.15);
    }

    .quick-action-btn i {
        font-size: 2rem;
        margin-bottom: 0.5rem;
        display: block;
    }

    .activity-item {
        padding: 1rem 0;
        border-bottom: 1px solid #e2e8f0;
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
    }

    @media (max-width: 768px) {
        .dashboard-gradient {
            padding: 1.5rem;
        }

        .metric-value {
            font-size: 1.75rem;
        }
    }
</style>
@endsection

@section('content')
<div class="main-container container-fluid">
    <!-- Page Header -->
    <div class="dashboard-gradient">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="mb-2" style="font-size: 2rem; font-weight: 700;">
                    Welcome back, {{ Auth::user()->name }}
                </h1>
                <p class="mb-0" style="opacity: 0.9; font-size: 1rem;">
                    Monitor and manage your MPFM operations in real-time
                </p>
            </div>
            <div class="col-md-4 text-right">
                <div class="text-white-50 small">{{ now()->format('l, F j, Y') }}</div>
                <div class="text-white font-weight-bold">{{ now()->format('g:i A') }}</div>
            </div>
        </div>
    </div>

    <!-- Production KPIs -->
    <div class="row">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="metric-card" style="--card-accent: #10b981;">
                <div class="card-body">
                    <div class="metric-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">
                        <i class="fe fe-droplet"></i>
                    </div>
                    <div class="metric-value">{{ number_format($summaryMetrics['total_oil_rate'] ?? 0, 0) }}</div>
                    <div class="metric-label">Oil Rate (STB/d)</div>
                    <div class="metric-trend trend-up">
                        <i class="fe fe-trending-up"></i> +8.2% vs last week
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="metric-card" style="--card-accent: #f59e0b;">
                <div class="card-body">
                    <div class="metric-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white;">
                        <i class="fe fe-wind"></i>
                    </div>
                    <div class="metric-value">{{ number_format($summaryMetrics['total_gas_rate'] ?? 0, 2) }}</div>
                    <div class="metric-label">Gas Rate (MMScf/d)</div>
                    <div class="metric-trend trend-up">
                        <i class="fe fe-trending-up"></i> +5.1% vs last week
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="metric-card" style="--card-accent: #3b82f6;">
                <div class="card-body">
                    <div class="metric-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white;">
                        <i class="fe fe-activity"></i>
                    </div>
                    <div class="metric-value">{{ number_format($summaryMetrics['avg_gor'] ?? 0, 0) }}</div>
                    <div class="metric-label">Average GOR (Scf/STB)</div>
                    <div class="metric-trend trend-down">
                        <i class="fe fe-trending-down"></i> -2.3% vs last week
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="metric-card" style="--card-accent: #8b5cf6;">
                <div class="card-body">
                    <div class="metric-icon" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); color: white;">
                        <i class="fe fe-percent"></i>
                    </div>
                    <div class="metric-value">{{ number_format($summaryMetrics['avg_bsw'] ?? 0, 1) }}%</div>
                    <div class="metric-label">Average BSW</div>
                    <div class="metric-trend trend-up">
                        <i class="fe fe-trending-up"></i> +1.2% vs last week
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Statistics -->
    <div class="row">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="metric-card" style="--card-accent: #667eea;">
                <div class="card-body">
                    <div class="metric-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                        <i class="fe fe-file-text"></i>
                    </div>
                    <div class="metric-value">{{ $totalTests }}</div>
                    <div class="metric-label">Total Tests</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="metric-card" style="--card-accent: #06b6d4;">
                <div class="card-body">
                    <div class="metric-icon" style="background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); color: white;">
                        <i class="fe fe-trending-up"></i>
                    </div>
                    <div class="metric-value">{{ $recentTests }}</div>
                    <div class="metric-label">Tests (Last 7 Days)</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="metric-card" style="--card-accent: #ec4899;">
                <div class="card-body">
                    <div class="metric-icon" style="background: linear-gradient(135deg, #ec4899 0%, #db2777 100%); color: white;">
                        <i class="fe fe-users"></i>
                    </div>
                    <div class="metric-value">{{ $totalUsers }}</div>
                    <div class="metric-label">Active Users</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="metric-card" style="--card-accent: #14b8a6;">
                <div class="card-body">
                    <div class="metric-icon" style="background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%); color: white;">
                        <i class="fe fe-droplet"></i>
                    </div>
                    <div class="metric-value">{{ number_format($summaryMetrics['total_water_rate'] ?? 0, 0) }}</div>
                    <div class="metric-label">Water Rate (STB/d)</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="chart-card">
                <div class="card-header">
                    <h3 class="card-title">Quick Actions</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ url('/upload') }}" class="quick-action-btn">
                                <i class="fe fe-upload"></i>
                                <div>Upload New Test</div>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('well_tests.index') }}" class="quick-action-btn">
                                <i class="fe fe-database"></i>
                                <div>Test Archive</div>
                            </a>
                        </div>
                        @role('supervisor')
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('daily-plans.create') }}" class="quick-action-btn">
                                <i class="fe fe-calendar"></i>
                                <div>Create Daily Plan</div>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('supervisor.dashboard') }}" class="quick-action-btn">
                                <i class="fe fe-pie-chart"></i>
                                <div>Supervisor Dashboard</div>
                            </a>
                        </div>
                        @endrole
                        @role('operator')
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('operator.tasks') }}" class="quick-action-btn">
                                <i class="fe fe-check-square"></i>
                                <div>My Tasks</div>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('operator.dashboard') }}" class="quick-action-btn">
                                <i class="fe fe-activity"></i>
                                <div>Operator Dashboard</div>
                            </a>
                        </div>
                        @endrole
                        @can('manage_users')
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('users.index') }}" class="quick-action-btn">
                                <i class="fe fe-users"></i>
                                <div>Manage Users</div>
                            </a>
                        </div>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        @if($testsByClient->count() > 0)
        <div class="col-lg-6 mb-4">
            <div class="chart-card">
                <div class="card-header">
                    <h3 class="card-title">Tests Distribution by Client</h3>
                </div>
                <div class="card-body">
                    <canvas id="testsByClientChart" height="280"></canvas>
                </div>
            </div>
        </div>
        @endif

        @if($testsOverTime->count() > 0)
        <div class="col-lg-6 mb-4">
            <div class="chart-card">
                <div class="card-header">
                    <h3 class="card-title">Test Activity (Last 30 Days)</h3>
                </div>
                <div class="card-body">
                    <canvas id="testsOverTimeChart" height="280"></canvas>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-12">
            <div class="chart-card">
                <div class="card-header">
                    <h3 class="card-title">Recent Tests</h3>
                </div>
                <div class="card-body">
                    @if($wellTests->count() > 0)
                        @foreach($wellTests as $test)
                        <div class="activity-item">
                            <div class="d-flex align-items-start">
                                <div class="activity-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                                    <i class="fe fe-file-text"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1 font-weight-600">{{ $test->well ?? 'N/A' }}</h6>
                                            <p class="text-muted mb-1 small">
                                                <strong>{{ $test->client ?? 'N/A' }}</strong> •
                                                {{ $test->field ?? 'N/A' }}
                                                @if($test->wellhead_tower) • {{ $test->wellhead_tower }} @endif
                                            </p>
                                            <p class="text-muted mb-0 small">
                                                <i class="fe fe-user mr-1"></i>{{ $test->operator ?? 'N/A' }}
                                                <i class="fe fe-clock ml-3 mr-1"></i>{{ $test->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            @php
                                                $metrics = $test->calculated_metrics ?? [];
                                            @endphp
                                            <div class="small text-muted">Oil: <strong>{{ number_format($metrics['oil_rate'] ?? 0, 0) }}</strong> STB/d</div>
                                            <div class="small text-muted">Gas: <strong>{{ number_format($metrics['gas_rate'] ?? 0, 2) }}</strong> MMScf/d</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        <div class="text-center mt-3">
                            <a href="{{ route('well_tests.index') }}" class="btn btn-outline-primary btn-sm">
                                View All Tests <i class="fe fe-arrow-right ml-1"></i>
                            </a>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fe fe-inbox" style="font-size: 3rem; opacity: 0.3;"></i>
                            <p class="mt-3">No tests found</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
@if($testsByClient->count() > 0)
// Tests by Client Chart
const testsByClientCtx = document.getElementById('testsByClientChart').getContext('2d');
new Chart(testsByClientCtx, {
    type: 'doughnut',
    data: {
        labels: @json($testsByClient->pluck('client')),
        datasets: [{
            data: @json($testsByClient->pluck('count')),
            backgroundColor: [
                '#667eea', '#764ba2', '#10b981', '#f59e0b',
                '#3b82f6', '#ec4899', '#14b8a6', '#8b5cf6',
                '#ef4444', '#06b6d4'
            ],
            borderWidth: 0,
            hoverOffset: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 15,
                    font: {
                        size: 12,
                        weight: '500'
                    },
                    usePointStyle: true,
                    pointStyle: 'circle'
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                cornerRadius: 8,
                titleFont: {
                    size: 14,
                    weight: 'bold'
                },
                bodyFont: {
                    size: 13
                },
                callbacks: {
                    label: function(context) {
                        return context.label + ': ' + context.parsed + ' tests';
                    }
                }
            }
        }
    }
});
@endif

@if($testsOverTime->count() > 0)
// Tests Over Time Chart
const testsOverTimeCtx = document.getElementById('testsOverTimeChart').getContext('2d');
new Chart(testsOverTimeCtx, {
    type: 'line',
    data: {
        labels: @json($testsOverTime->pluck('date')),
        datasets: [{
            label: 'Tests Count',
            data: @json($testsOverTime->pluck('count')),
            borderColor: '#667eea',
            backgroundColor: function(context) {
                const ctx = context.chart.ctx;
                const gradient = ctx.createLinearGradient(0, 0, 0, 250);
                gradient.addColorStop(0, 'rgba(102, 126, 234, 0.2)');
                gradient.addColorStop(1, 'rgba(102, 126, 234, 0.0)');
                return gradient;
            },
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointRadius: 4,
            pointHoverRadius: 6,
            pointBackgroundColor: '#667eea',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointHoverBackgroundColor: '#667eea',
            pointHoverBorderColor: '#fff',
            pointHoverBorderWidth: 3
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
            intersect: false,
            mode: 'index'
        },
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                cornerRadius: 8,
                titleFont: {
                    size: 14,
                    weight: 'bold'
                },
                bodyFont: {
                    size: 13
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
                    padding: 10,
                    font: {
                        size: 12
                    }
                }
            },
            x: {
                grid: {
                    display: false,
                    drawBorder: false
                },
                ticks: {
                    padding: 10,
                    font: {
                        size: 12
                    },
                    maxRotation: 0,
                    autoSkip: true,
                    maxTicksLimit: 10
                }
            }
        }
    }
});
@endif
</script>
@endsection
