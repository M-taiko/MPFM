@extends('layouts.master')

@section('title', 'MPFM Test Trending Dashboard')

@section('css')
<style>
    .string-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 15px;
        border-left: 4px solid #667eea;
        transition: all 0.3s ease;
    }

    .string-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transform: translateX(5px);
    }

    .alert-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: bold;
        margin-right: 8px;
    }

    .alert-critical { background: #f8d7da; color: #721c24; }
    .alert-warning { background: #fff3cd; color: #856404; }
    .alert-info { background: #d1ecf1; color: #0c5460; }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
        margin-top: 15px;
    }

    .stat-item {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 6px;
        text-align: center;
    }

    .stat-number {
        font-size: 1.8rem;
        font-weight: bold;
        color: #667eea;
    }

    .stat-label {
        font-size: 0.85rem;
        color: #666;
        margin-top: 5px;
    }

    .header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 8px;
        margin-bottom: 30px;
    }

    .header h1 {
        margin: 0;
        font-size: 2rem;
    }

    .header p {
        margin: 10px 0 0 0;
        opacity: 0.9;
    }

    .no-alerts {
        background: #d4edda;
        border-left: 4px solid #28a745;
        padding: 15px;
        border-radius: 4px;
        color: #155724;
    }

    .filter-section {
        background: white;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .string-name {
        font-size: 1.3rem;
        font-weight: bold;
        color: #333;
        margin-bottom: 10px;
    }

    .tower-badge {
        display: inline-block;
        background: #e9ecef;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        color: #666;
    }

    .trend-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 0.8rem;
        margin-top: 5px;
    }

    .trend-increasing { background: #fff3cd; color: #856404; }
    .trend-decreasing { background: #d1ecf1; color: #0c5460; }
    .trend-stable { background: #d4edda; color: #155724; }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="header">
        <h1>
            <i class="fe fe-trending-up" style="opacity: 0.8;"></i>
            MPFM Test Trending Analysis
        </h1>
        <p>Monitor test trends and anomalies across all oil strings</p>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <form class="form-inline" method="GET">
            <div class="form-group mr-3">
                <label for="sortBy" class="mr-2">Sort By:</label>
                <select class="form-control form-control-sm" id="sortBy" name="sort">
                    <option value="alerts_desc" {{ request('sort') === 'alerts_desc' ? 'selected' : '' }}>Most Alerts (High to Low)</option>
                    <option value="alerts_asc" {{ request('sort') === 'alerts_asc' ? 'selected' : '' }}>Least Alerts (Low to High)</option>
                    <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>String Name (A-Z)</option>
                </select>
            </div>
            <button type="submit" class="btn btn-sm btn-primary">Filter</button>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="stat-number">{{ count($stringsWithAlerts) }}</div>
                    <div class="stat-label">Total Strings</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="stat-number text-danger">{{ $stringsWithAlerts->sum('alert_count') }}</div>
                    <div class="stat-label">Active Alerts</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="stat-number text-warning">{{ $stringsWithAlerts->filter(fn($s) => $s['alert_count'] > 0)->count() }}</div>
                    <div class="stat-label">Strings with Alerts</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="stat-number text-success">{{ $stringsWithAlerts->filter(fn($s) => $s['alert_count'] === 0)->count() }}</div>
                    <div class="stat-label">Healthy Strings</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Strings List -->
    <div class="row">
        <div class="col-12">
            @forelse($stringsWithAlerts as $item)
            <div class="string-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <div class="string-name">{{ $item['string']->name }}</div>
                        <div>
                            <span class="tower-badge">
                                <i class="fa fa-building"></i> {{ $item['string']->tower->name }}
                            </span>
                        </div>

                        <!-- Alerts Display -->
                        @if($item['alert_count'] > 0)
                        <div style="margin-top: 15px;">
                            <strong style="color: #666;">Active Alerts:</strong>
                            <div style="margin-top: 10px;">
                                @foreach($item['alerts'] as $alert)
                                <div style="margin-bottom: 8px;">
                                    <span class="alert-badge alert-{{ $alert['severity'] }}">
                                        {{ $alert['type'] }}
                                    </span>
                                    <span>{{ $alert['message'] }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @else
                        <div class="no-alerts" style="margin-top: 15px;">
                            <i class="fa fa-check-circle"></i> No concerning trends detected
                        </div>
                        @endif

                        <!-- Action Links -->
                        <div style="margin-top: 15px;">
                            <a href="{{ route('mpfm_tests.string_analysis', $item['string']->id) }}" class="btn btn-sm btn-info">
                                <i class="fa fa-chart-line"></i> View Analysis
                            </a>
                            <a href="{{ route('mpfm_tests.string_analysis', $item['string']->id) }}?filter=alerts" class="btn btn-sm btn-warning">
                                <i class="fa fa-bell"></i> View Alerts
                            </a>
                        </div>
                    </div>

                    <!-- Alert Count Badge -->
                    <div style="text-align: center; min-width: 80px;">
                        <div style="font-size: 2rem; font-weight: bold; color: {{ $item['alert_count'] > 0 ? '#dc3545' : '#28a745' }};">
                            {{ $item['alert_count'] }}
                        </div>
                        <div style="font-size: 0.85rem; color: #666;">Alerts</div>
                    </div>
                </div>
            </div>
            @empty
            <div class="alert alert-info">
                <i class="fa fa-info-circle"></i> No strings with test data available
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
