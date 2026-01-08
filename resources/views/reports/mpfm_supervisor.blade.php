@extends('layouts.app')

@section('title', 'MPFM Supervisor Reports')

@section('content')
<div class="container">
    <h2>MPFM Supervisor Reports</h2>
    <p>MPFM Supervisor specific reports and analytics.</p>
    
    <div class="card">
        <div class="card-body">
            <p>Access to well string requirements and operator allocation.</p>
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
    // Pressure by Well Chart
    const pressureByWellCtx = document.getElementById('pressureByWellChart').getContext('2d');
    const pressureByWellChart = new Chart(pressureByWellCtx, {
        type: 'bar',
        data: {
            labels: @json($pressureByWell->pluck('well')),
            datasets: [{
                label: 'Avg Pressure (psi)',
                data: @json($pressureByWell->pluck('avg_pressure')),
                backgroundColor: 'rgba(78, 115, 223, 0.7)',
                borderColor: 'rgba(78, 115, 223, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Temperature by Well Chart
    const temperatureByWellCtx = document.getElementById('temperatureByWellChart').getContext('2d');
    const temperatureByWellChart = new Chart(temperatureByWellCtx, {
        type: 'bar',
        data: {
            labels: @json($pressureByWell->pluck('well')),
            datasets: [{
                label: 'Avg Temperature (°F)',
                data: @json($pressureByWell->pluck('avg_temperature')),
                backgroundColor: 'rgba(28, 200, 138, 0.7)',
                borderColor: 'rgba(28, 200, 138, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endpush
