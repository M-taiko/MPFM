@extends('layouts.app')

@section('title', 'Reservoir Engineer Reports')

@section('content')
<div class="container">
    <h2>Reservoir Engineer Reports</h2>
    <p>Reservoir Engineer specific reports and analytics.</p>
    
    <div class="card">
        <div class="card-body">
            <p>Access to well string requirements and reservoir analysis.</p>
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
    // Production by Well Chart
    const productionByWellCtx = document.getElementById('productionByWellChart').getContext('2d');
    const productionByWellChart = new Chart(productionByWellCtx, {
        type: 'bar',
        data: {
            labels: @json($productionByWell->pluck('well')),
            datasets: [
                {
                    label: 'Avg Oil Rate (STB/d)',
                    data: @json($productionByWell->pluck('avg_oil_rate')),
                    backgroundColor: 'rgba(78, 115, 223, 0.7)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Avg Gas Rate (Mcf/d)',
                    data: @json($productionByWell->pluck('avg_gas_rate')),
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

    // Water Rate Chart
    const waterRateCtx = document.getElementById('waterRateChart').getContext('2d');
    const waterRateChart = new Chart(waterRateCtx, {
        type: 'line',
        data: {
            labels: @json($productionByWell->pluck('well')),
            datasets: [{
                label: 'Avg Water Rate (STB/d)',
                data: @json($productionByWell->pluck('avg_oil_rate').map(function(oil) {
                    // This is a placeholder - in a real system, we would have actual water rate data
                    return Math.max(0, oil * 0.3); // Simulated water rate as 30% of oil rate
                })),
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                tension: 0.4
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
