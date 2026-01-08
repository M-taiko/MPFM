@extends('layouts.app')

@section('title', 'Job Officer Reports')

@section('content')
<div class="container">
    <h2>Job Officer Reports</h2>
    <p>Job Officer specific reports and analytics.</p>
    
    <div class="card">
        <div class="card-body">
            <p>Access to well string requirements and job-related activities.</p>
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
    // Tests by Field Chart
    const testsByFieldCtx = document.getElementById('testsByFieldChart').getContext('2d');
    const testsByFieldChart = new Chart(testsByFieldCtx, {
        type: 'doughnut',
        data: {
            labels: @json($testsByField->pluck('field')),
            datasets: [{
                data: @json($testsByField->pluck('count')),
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

    // Tests Over Time Chart
    const testsOverTimeCtx = document.getElementById('testsOverTimeChart').getContext('2d');
    const testsOverTimeChart = new Chart(testsOverTimeCtx, {
        type: 'line',
        data: {
            labels: @json($testsOverTime->pluck('date')),
            datasets: [{
                label: 'Tests Count',
                data: @json($testsOverTime->pluck('count')),
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
