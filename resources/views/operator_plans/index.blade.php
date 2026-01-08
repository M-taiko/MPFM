@extends('layouts.master2')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            Operator Plan - {{ $wellTest->well }}
            <small class="text-muted">Test Date: {{ $wellTest->test_date }}</small>
        </h1>
        <div class="page-subtitle">
            <span class="badge badge-{{ $planStats['is_completed'] ? 'success' : 'warning' }}">
                {{ $planStats['completion_percentage'] }}% Complete
            </span>
            <span class="ml-2">
                {{ $planStats['completed_steps'] }}/{{ $planStats['total_steps'] }} Steps
            </span>
        </div>
    </div>

    <div class="row">
        <!-- Progress Summary -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Progress Summary</h3>
                </div>
                <div class="card-body">
                    <div class="progress mb-3">
                        <div class="progress-bar progress-bar-striped progress-bar-animated"
                             role="progressbar"
                             style="width: {{ $planStats['completion_percentage'] }}%">
                            {{ $planStats['completion_percentage'] }}%
                        </div>
                    </div>
                    <div class="row text-center">
                        <div class="col-6">
                            <h4 class="mb-0">{{ $planStats['completed_steps'] }}</h4>
                            <small class="text-muted">Completed</small>
                        </div>
                        <div class="col-6">
                            <h4 class="mb-0">{{ $planStats['pending_steps'] }}</h4>
                            <small class="text-muted">Pending</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Actions</h3>
                </div>
                <div class="card-body">
                    <a href="{{ route('operator_plans.create', ['wellTestId' => $wellTest->id]) }}"
                       class="btn btn-primary btn-block mb-2">
                        Add New Step
                    </a>
                    <a href="{{ route('well_tests.show', $wellTest->id) }}"
                       class="btn btn-secondary btn-block">
                        Back to Well Test
                    </a>
                </div>
            </div>
        </div>

        <!-- Operator Plan Steps -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Operator Plan Steps</h3>
                    <div class="card-options">
                        <button class="btn btn-sm btn-outline-primary" id="reorderBtn">
                            <i class="fe fe-move"></i> Reorder
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="steps-container">
                        @forelse($operatorPlans as $index => $step)
                            <div class="step-item mb-3 p-3 border rounded" data-step-id="{{ $step->id }}">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="step-number badge badge-primary mr-2">
                                                {{ $step->step_number }}
                                            </span>
                                            <h5 class="mb-0">
                                                {{ $step->step_description }}
                                                <span class="badge badge-{{ $step->step_type == 'instruction' ? 'info' :
                                                       $step->step_type == 'measurement' ? 'warning' :
                                                       $step->step_type == 'action' ? 'primary' :
                                                       $step->step_type == 'preparation' ? 'success' : 'secondary' }}">
                                                    {{ ucfirst($step->step_type) }}
                                                </span>
                                            </h5>
                                        </div>

                                        @if($step->step_parameters)
                                            <div class="step-parameters small text-muted mb-2">
                                                <strong>Parameters:</strong>
                                                <pre class="mb-0">{{ json_encode($step->step_parameters, JSON_PRETTY_PRINT) }}</pre>
                                            </div>
                                        @endif

                                        <div class="step-status">
                                            @if($step->is_completed)
                                                <span class="badge badge-success">
                                                    <i class="fe fe-check-circle"></i> Completed
                                                    @if($step->completed_at)
                                                        on {{ $step->completed_at->format('M d, Y H:i') }}
                                                    @endif
                                                    @if($step->completed_by)
                                                        by {{ $step->completed_by }}
                                                    @endif
                                                </span>
                                            @else
                                                <span class="badge badge-warning">
                                                    <i class="fe fe-clock"></i> Pending
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="step-actions">
                                        @if(!$step->is_completed)
                                            <button class="btn btn-sm btn-success complete-step-btn"
                                                    data-step-id="{{ $step->id }}">
                                                <i class="fe fe-check"></i> Complete
                                            </button>
                                        @else
                                            <button class="btn btn-sm btn-warning pending-step-btn"
                                                    data-step-id="{{ $step->id }}">
                                                <i class="fe fe-rotate-ccw"></i> Mark Pending
                                            </button>
                                        @endif

                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-secondary dropdown-toggle"
                                                    type="button"
                                                    data-toggle="dropdown">
                                                <i class="fe fe-more-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item"
                                                   href="{{ route('operator_plans.edit', $step->id) }}">
                                                    <i class="fe fe-edit"></i> Edit
                                                </a>
                                                <form method="POST"
                                                      action="{{ route('operator_plans.destroy', $step->id) }}"
                                                      class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="dropdown-item text-danger"
                                                            onclick="return confirm('Are you sure?')">
                                                        <i class="fe fe-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if($step->step_results)
                                    <div class="step-results mt-2 p-2 bg-light rounded">
                                        <strong>Results:</strong>
                                        <pre class="mb-0">{{ json_encode($step->step_results, JSON_PRETTY_PRINT) }}</pre>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <i class="fe fe-clipboard h1 text-muted"></i>
                                <p class="lead">No operator plan steps created yet</p>
                                <a href="{{ route('operator_plans.create', ['wellTestId' => $wellTest->id]) }}"
                                   class="btn btn-primary">
                                    Create First Step
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for completing steps -->
<div class="modal fade" id="completeStepModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Complete Step</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="completeStepForm">
                    @csrf
                    <input type="hidden" id="stepId" name="step_id">
                    <div class="form-group">
                        <label>Completed By</label>
                        <input type="text" class="form-control" name="completed_by" required>
                    </div>
                    <div class="form-group">
                        <label>Results (JSON)</label>
                        <textarea class="form-control" name="results" rows="3"
                                  placeholder='{"parameter": "value"}'></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmCompleteBtn">Complete Step</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Complete step functionality
    document.querySelectorAll('.complete-step-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const stepId = this.dataset.stepId;
            document.getElementById('stepId').value = stepId;
            $('#completeStepModal').modal('show');
        });
    });

    // Confirm complete step
    document.getElementById('confirmCompleteBtn').addEventListener('click', function() {
        const stepId = document.getElementById('stepId').value;
        const formData = new FormData(document.getElementById('completeStepForm'));

        fetch(`{{ url('operator-plans') }}/${stepId}/complete`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                $('#completeStepModal').modal('hide');
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to complete step'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while completing the step');
        });
    });

    // Mark pending functionality
    document.querySelectorAll('.pending-step-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const stepId = this.dataset.stepId;
            if (confirm('Are you sure you want to mark this step as pending?')) {
                fetch(`{{ url('operator-plans') }}/${stepId}/pending`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + (data.message || 'Failed to mark step as pending'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while marking the step as pending');
                });
            }
        });
    });

    // Reorder functionality
    document.getElementById('reorderBtn').addEventListener('click', function() {
        alert('Drag and drop functionality would be implemented here');
        // In a real implementation, you would use a drag-and-drop library like SortableJS
    });
});
</script>
@endpush
