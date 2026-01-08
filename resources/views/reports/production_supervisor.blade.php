@extends('layouts.app')

@section('title', 'Production Supervisor Reports')

@section('content')
<div class="container">
    <h2>Production Supervisor Reports</h2>
    <p>Production Supervisor specific reports and analytics.</p>
    
    <div class="card">
        <div class="card-body">
            <p>Access to well string requirements and production oversight.</p>
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
    // Production Efficiency Chart
    const productionEfficiencyCtx = document.getElementById('productionEfficiencyChart').getContext('2d');
    const productionEfficiencyChart = new Chart(productionEfficiencyCtx, {
        type: 'line',
        data: {
            labels: @json($productionEfficiency->pluck('date')),
            datasets: [
                {
                    label: 'Avg Oil Rate (STB/d)',
                    data: @json($productionEfficiency->pluck('avg_oil_rate')),
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.1)',
                    tension: 0.4
                },
                {
                    label: 'Avg Water Rate (STB/d)',
                    data: @json($productionEfficiency->pluck('avg_water_rate')),
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

    // Water Handling Chart
    const waterHandlingCtx = document.getElementById('waterHandlingChart').getContext('2d');
    const waterHandlingChart = new Chart(waterHandlingCtx, {
        type: 'bar',
        data: {
            labels: @json($productionEfficiency->pluck('date')),
            datasets: [
                {
                    label: 'BSW %',
                    data: @json($productionEfficiency->pluck('avg_water_rate').map(function(water, index) {
                        const oil = @json($productionEfficiency->pluck('avg_oil_rate'))[index];
                        return oil > 0 ? (water / (oil + water)) * 100 : 0;
                    })),
                    backgroundColor: 'rgba(78, 115, 223, 0.7)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    borderWidth: 1
                },
                {
                    label: 'WLR %',
                    data: @json($productionEfficiency->pluck('avg_water_rate').map(function(water, index) {
                        const oil = @json($productionEfficiency->pluck('avg_oil_rate'))[index];
                        return oil > 0 ? (water / oil) * 100 : 0;
                    })),
                    backgroundColor: 'rgba(28, 200, 138, 0.7)',
                    borderColor: 'rgba(28, 200, 138, 1)',
                    borderWidth: 1
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
</script>
@endpush
