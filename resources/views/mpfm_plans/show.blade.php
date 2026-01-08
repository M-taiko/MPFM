@extends('layouts.app')

@section('title', 'MPFM Plan: ' . $plan->name)

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>MPFM Plan: {{ $plan->name }}</h2>
        <div>
            <a href="{{ route('well_tests.show', $plan->wellTest->id) }}" class="btn btn-secondary">Back to Well Test</a>
            <a href="{{ route('mpfm_plans.edit', $plan->id) }}" class="btn btn-primary">Edit Plan</a>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <h4>Plan Details</h4>
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
            @if($plan->test_objective)
                <div class="row">
                    <div class="col-12">
                        <p><strong>Test Objective:</strong> {{ $plan->test_objective }}</p>
                    </div>
                </div>
            @endif
            @if($plan->description)
                <div class="row">
                    <div class="col-12">
                        <p><strong>Description:</strong> {{ $plan->description }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Plan Steps</h4>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addStepModal">
                Add Step
            </button>
        </div>
        <div class="card-body">
            @if($plan->steps->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Step #</th>
                                <th>Name</th>
                                <th>Choke</th>
                                <th>Time Window</th>
                                <th>Planned Oil Rate</th>
                                <th>Planned Gas Rate</th>
                                <th>Planned Water Rate</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($plan->steps as $step)
                            <tr>
                                <td>{{ $step->step_number }}</td>
                                <td>{{ $step->name }}</td>
                                <td>{{ $step->choke }}</td>
                                <td>{{ $step->time_window }}</td>
                                <td>{{ $step->planned_rates['oil_rate'] ?? 'N/A' }} STB/d</td>
                                <td>{{ $step->planned_rates['gas_rate'] ?? 'N/A' }} MMScf/d</td>
                                <td>{{ $step->planned_rates['water_rate'] ?? 'N/A' }} STB/d</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary" 
                                            data-bs-toggle="modal" data-bs-target="#addReadingModal" 
                                            data-step-id="{{ $step->id }}">Add Reading</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p>No steps defined yet.</p>
            @endif
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <h4>Report Summary</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>KPI Summary</h5>
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
                            <strong>Avg Pressure:</strong> {{ number_format($report['kpi_summary']['avg_pressure'] ?? 0, 2) }} psi
                        </li>
                        <li class="list-group-item">
                            <strong>Avg Temperature:</strong> {{ number_format($report['kpi_summary']['avg_temperature'] ?? 0, 2) }} °F
                        </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h5>Conclusions</h5>
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
    </div>
    
    <!-- Add Step Modal -->
    <div class="modal fade" id="addStepModal" tabindex="-1" aria-labelledby="addStepModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addStepModalLabel">Add Step</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('mpfm_plans.steps.add', $plan->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="step_number" class="form-label">Step Number</label>
                            <input type="number" class="form-control" id="step_number" name="step_number" required>
                        </div>
                        <div class="mb-3">
                            <label for="step_name" class="form-label">Step Name</label>
                            <input type="text" class="form-control" id="step_name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="step_choke" class="form-label">Choke Setting</label>
                            <input type="text" class="form-control" id="step_choke" name="choke">
                        </div>
                        <div class="mb-3">
                            <label for="time_window" class="form-label">Time Window</label>
                            <input type="text" class="form-control" id="time_window" name="time_window">
                        </div>
                        <div class="mb-3">
                            <label for="step_description" class="form-label">Description</label>
                            <textarea class="form-control" id="step_description" name="description"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Step</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Add Reading Modal -->
    <div class="modal fade" id="addReadingModal" tabindex="-1" aria-labelledby="addReadingModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addReadingModalLabel">Add Reading</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('mpfm_steps.readings.add', 0) }}" method="POST" id="readingForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="oil_rate" class="form-label">Oil Rate (STB/d)</label>
                            <input type="number" step="0.01" class="form-control" id="oil_rate" name="oil_rate">
                        </div>
                        <div class="mb-3">
                            <label for="gas_rate" class="form-label">Gas Rate (MMScf/d)</label>
                            <input type="number" step="0.01" class="form-control" id="gas_rate" name="gas_rate">
                        </div>
                        <div class="mb-3">
                            <label for="water_rate" class="form-label">Water Rate (STB/d)</label>
                            <input type="number" step="0.01" class="form-control" id="water_rate" name="water_rate">
                        </div>
                        <div class="mb-3">
                            <label for="wlr" class="form-label">WLR (%)</label>
                            <input type="number" step="0.01" class="form-control" id="wlr" name="wlr">
                        </div>
                        <div class="mb-3">
                            <label for="bsw" class="form-label">BSW (%)</label>
                            <input type="number" step="0.01" class="form-control" id="bsw" name="bsw">
                        </div>
                        <div class="mb-3">
                            <label for="pressure" class="form-label">Pressure (psi)</label>
                            <input type="number" step="0.01" class="form-control" id="pressure" name="pressure">
                        </div>
                        <div class="mb-3">
                            <label for="temperature" class="form-label">Temperature (°F)</label>
                            <input type="number" step="0.01" class="form-control" id="temperature" name="temperature">
                        </div>
                        <div class="mb-3">
                            <label for="reading_time" class="form-label">Reading Time</label>
                            <input type="datetime-local" class="form-control" id="reading_time" name="reading_time">
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Reading</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Update the form action when the "Add Reading" button is clicked
document.querySelectorAll('[data-bs-target="#addReadingModal"]').forEach(button => {
    button.addEventListener('click', function() {
        const stepId = this.getAttribute('data-step-id');
        document.getElementById('readingForm').action = `{{ route('mpfm_steps.readings.add', ':stepId') }}`.replace(':stepId', stepId);
    });
});
</script>
@endsection