@extends('layouts.app')

@section('title', 'MPFM Operator Reports')

@section('content')
<div class="container">
    <h2>MPFM Operator Reports</h2>
    <p>MPFM Operator specific reports and analytics.</p>
    
    <div class="card">
        <div class="card-body">
            <p>Access to assigned well tests and operational data.</p>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .stat-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.07);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        margin-bottom: 1.5rem;
        height: 100%;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.12);
    }
    .stat-card .card-body {
        padding: 1.5rem;
        display: flex;
        align-items: center;
        height: 100%;
    }
    .stat-card .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        font-size: 1.25rem;
    }
    .stat-card .stat-info h3 {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Daily Production Chart
    const dailyProductionCtx = document.getElementById('dailyProductionChart').getContext('2d');
    const dailyProductionChart = new Chart(dailyProductionCtx, {
        type: 'line',
        data: {
            labels: @json($dailyProduction->pluck('date')),
            datasets: [
                {
                    label: 'Total Oil (STB/d)',
                    data: @json($dailyProduction->pluck('total_oil')),
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.1)',
                    tension: 0.4
                },
                {
                    label: 'Total Gas (Mcf/d)',
                    data: @json($dailyProduction->pluck('total_gas')),
                    borderColor: '#1cc88a',
                    backgroundColor: 'rgba(28, 200, 138, 0.1)',
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Recent Tests Chart
    const recentTestsCtx = document.getElementById('recentTestsChart').getContext('2d');
    const recentTestsChart = new Chart(recentTestsCtx, {
        type: 'doughnut',
        data: {
            labels: @json($recentTests->pluck('well')->unique()),
            datasets: [{
                data: @json($recentTests->groupBy('well')->map(function($group) { return $group->count(); })->values()),
                backgroundColor: [
                    '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
                    '#858796', '#5a5c69', '#6777ef', '#f57850', '#6fd96f'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
@endpush
