@extends('layouts.app')

@section('title', 'MPFM Plan Report: ' . $plan->name)

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>MPFM Plan Report: {{ $plan->name }}</h2>
        <div>
            <a href="{{ route('mpfm_plans.show', $plan->id) }}" class="btn btn-secondary">Back to Plan</a>
            <button onclick="window.print()" class="btn btn-primary">Print Report</button>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <h4>Plan Overview</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Well Test:</strong> {{ $plan->wellTest->well ?? 'N/A' }}</p>
                    <p><strong>Client:</strong> {{ $plan->wellTest->client ?? 'N/A' }}</p>
                    <p><strong>Field:</strong> {{ $plan->wellTest->field ?? 'N/A' }}</p>
                    <p><strong>Test Date:</strong> {{ $plan->wellTest->test_date ?? 'N/A' }}</p>
                    <p><strong>Test Type:</strong> {{ $plan->wellTest->test_type ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Expected Choke:</strong> {{ $plan->expected_choke ?? 'N/A' }}</p>
                    <p><strong>Planned Oil Rate:</strong> {{ $plan->planned_oil_rate ?? 'N/A' }} STB/d</p>
                    <p><strong>Planned Gas Rate:</strong> {{ $plan->planned_gas_rate ?? 'N/A' }} MMScf/d</p>
                    <p><strong>Planned Water Rate:</strong> {{ $plan->planned_water_rate ?? 'N/A' }} STB/d</p>
                    <p><strong>Expected GOR:</strong> {{ $plan->expected_gor ?? 'N/A' }} Scf/STB</p>
                    <p><strong>Expected BSW:</strong> {{ $plan->expected_bsw ?? 'N/A' }} %</p>
                    <p><strong>Expected Duration:</strong> {{ $plan->expected_duration ?? 'N/A' }} minutes</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <h4>Step Deviations</h4>
        </div>
        <div class="card-body">
            @if(count($report['deviations']) > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Step Name</th>
                                <th>Oil Rate Deviation (%)</th>
                                <th>Gas Rate Deviation (%)</th>
                                <th>Water Rate Deviation (%)</th>
                                <th>Readings Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($report['deviations'] as $deviation)
                            <tr>
                                <td>{{ $deviation['step_name'] }}</td>
                                <td>
                                    @if($deviation['oil_rate_deviation'] !== null)
                                        {{ number_format($deviation['oil_rate_deviation'], 2) }}%
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    @if($deviation['gas_rate_deviation'] !== null)
                                        {{ number_format($deviation['gas_rate_deviation'], 2) }}%
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    @if($deviation['water_rate_deviation'] !== null)
                                        {{ number_format($deviation['water_rate_deviation'], 2) }}%
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>{{ $deviation['readings_count'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p>No deviations calculated yet.</p>
            @endif
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <h4>KPI Summary</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <strong>Total Readings:</strong> {{ $report['kpi_summary']['total_readings'] ?? 0 }}
                        </li>
                        <li class="list-group-item">
                            <strong>Avg Oil Rate:</strong> {{ number_format($report['kpi_summary']['avg_oil_rate'] ?? 0, 2) }} STB/d
                        </li>
                        <li class="list-group-item">
                            <strong>Avg Gas Rate:</strong> {{ number_format($report['kpi_summary']['avg_gas_rate'] ?? 0, 2) }} MMScf/d
                        </li>
                        <li class="list-group-item">
                            <strong>Avg Water Rate:</strong> {{ number_format($report['kpi_summary']['avg_water_rate'] ?? 0, 2) }} STB/d
                        </li>
                        <li class="list-group-item">
                            <strong>Overall GOR:</strong> {{ number_format($report['kpi_summary']['overall_gor'] ?? 0, 2) }} Scf/STB
                        </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <strong>Avg Pressure:</strong> {{ number_format($report['kpi_summary']['avg_pressure'] ?? 0, 2) }} psi
                        </li>
                        <li class="list-group-item">
                            <strong>Max Pressure:</strong> {{ number_format($report['kpi_summary']['max_pressure'] ?? 0, 2) }} psi
                        </li>
                        <li class="list-group-item">
                            <strong>Min Pressure:</strong> {{ number_format($report['kpi_summary']['min_pressure'] ?? 0, 2) }} psi
                        </li>
                        <li class="list-group-item">
                            <strong>Avg Temperature:</strong> {{ number_format($report['kpi_summary']['avg_temperature'] ?? 0, 2) }} °F
                        </li>
                        <li class="list-group-item">
                            <strong>Max Temperature:</strong> {{ number_format($report['kpi_summary']['max_temperature'] ?? 0, 2) }} °F
                        </li>
                        <li class="list-group-item">
                            <strong>Min Temperature:</strong> {{ number_format($report['kpi_summary']['min_temperature'] ?? 0, 2) }} °F
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <h4>Conclusions</h4>
        </div>
        <div class="card-body">
            <ul class="list-group">
                @forelse($report['conclusions'] as $conclusion)
                    <li class="list-group-item">{{ $conclusion }}</li>
                @empty
                    <li class="list-group-item">No conclusions available yet.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection