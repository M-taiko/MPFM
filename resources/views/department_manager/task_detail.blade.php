@extends('layouts.master')

@section('title', 'Task Detail & Operator Assignment - ' . auth()->user()->department->name)

@section('css')
<style>
    .task-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 25px;
        border-radius: 8px;
        margin-bottom: 25px;
    }

    .task-header h1 {
        margin: 0 0 10px 0;
        font-size: 1.8rem;
    }

    .status-badge {
        display: inline-block;
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.9rem;
        background: rgba(255, 255, 255, 0.2);
        color: white;
        margin-top: 10px;
    }

    .task-info-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        border-left: 4px solid #667eea;
    }

    .info-group {
        margin-bottom: 20px;
    }

    .info-label {
        font-weight: 600;
        color: #667eea;
        font-size: 0.9rem;
        text-transform: uppercase;
        margin-bottom: 8px;
    }

    .info-value {
        color: #333;
        font-size: 1rem;
        padding-left: 10px;
    }

    .operator-assignment-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        border-left: 4px solid #28a745;
    }

    .operator-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .operator-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px;
        border-bottom: 1px solid #e5e7eb;
        background: white;
        margin-bottom: 8px;
        border-radius: 4px;
        border-left: 3px solid #28a745;
    }

    .operator-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }

    .operator-name {
        font-weight: 600;
        color: #333;
    }

    .operator-badge {
        display: inline-block;
        background: #28a745;
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        margin-left: 10px;
    }

    .empty-state {
        text-align: center;
        padding: 30px;
        background: #f8f9fa;
        border-radius: 8px;
        color: #666;
    }

    .empty-state i {
        font-size: 2rem;
        color: #ddd;
        display: block;
        margin-bottom: 10px;
    }

    .form-section {
        background: white;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .form-section h4 {
        color: #667eea;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e5e7eb;
    }

    .task-details-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-bottom: 20px;
    }

    @media (max-width: 768px) {
        .task-details-grid {
            grid-template-columns: 1fr;
        }
    }

    .button-group {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }

    .button-group .btn {
        flex: 1;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Task Header -->
    <div class="task-header">
        <h1>
            <i class="fe fe-layers mr-3"></i>
            Task #{{ $task->id }} - {{ $task->well_number ?? 'N/A' }}
        </h1>
        <span class="status-badge">
            {{ str_replace('_', ' ', ucfirst($task->state)) }}
        </span>
    </div>

    <div class="row">
        <!-- Task Information -->
        <div class="col-lg-8">
            <!-- Plan & Basic Info -->
            <div class="task-info-card">
                <h4 style="color: #667eea; margin-bottom: 20px;">
                    <i class="fe fe-info"></i> Task Information
                </h4>

                <div class="task-details-grid">
                    <div class="info-group">
                        <div class="info-label"><i class="fe fe-calendar"></i> Plan</div>
                        <div class="info-value">
                            <a href="{{ route('monthly-plans.show', $task->plan) }}">
                                {{ $task->plan->plan_name }} ({{ $task->plan->plan_month }})
                            </a>
                        </div>
                    </div>

                    <div class="info-group">
                        <div class="info-label"><i class="fe fe-briefcase"></i> Department</div>
                        <div class="info-value">
                            {{ $task->department->name ?? 'N/A' }}
                        </div>
                    </div>

                    <div class="info-group">
                        <div class="info-label"><i class="fe fe-map-pin"></i> Well Number</div>
                        <div class="info-value">{{ $task->well_number ?? 'N/A' }}</div>
                    </div>

                    <div class="info-group">
                        <div class="info-label"><i class="fe fe-layers"></i> String Number</div>
                        <div class="info-value">{{ $task->string_number ?? 'N/A' }}</div>
                    </div>

                    <div class="info-group">
                        <div class="info-label"><i class="fe fe-grid"></i> Tower</div>
                        <div class="info-value">{{ $task->tower ?? 'N/A' }}</div>
                    </div>

                    <div class="info-group">
                        <div class="info-label"><i class="fe fe-arrow-right"></i> Flow Route</div>
                        <div class="info-value">{{ $task->flow_route ?? 'N/A' }}</div>
                    </div>

                    <div class="info-group">
                        <div class="info-label"><i class="fe fe-layers"></i> Reservoir Layer</div>
                        <div class="info-value">{{ $task->reservoir_layer ?? 'N/A' }}</div>
                    </div>

                    <div class="info-group">
                        <div class="info-label"><i class="fe fe-target"></i> Test Location</div>
                        <div class="info-value">{{ $task->test_location ?? 'N/A' }}</div>
                    </div>

                    <div class="info-group">
                        <div class="info-label"><i class="fe fe-zap"></i> Electrification</div>
                        <div class="info-value">
                            <span class="badge badge-{{ $task->electrification_status === 'Y' ? 'success' : 'danger' }}">
                                {{ $task->electrification_status === 'Y' ? 'Yes' : 'No' }}
                            </span>
                        </div>
                    </div>

                    <div class="info-group">
                        <div class="info-label"><i class="fe fe-box"></i> MPFM Type</div>
                        <div class="info-value">{{ $task->mpfm_type ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>

            <!-- Remarks Section -->
            @if($task->supervisor_remarks || $task->hq_notes)
            <div class="task-info-card">
                <h4 style="color: #667eea; margin-bottom: 20px;">
                    <i class="fe fe-message-square"></i> Notes & Remarks
                </h4>

                @if($task->supervisor_remarks)
                <div class="info-group">
                    <div class="info-label">Supervisor Remarks</div>
                    <div class="info-value" style="background: #f8f9fa; padding: 12px; border-radius: 4px;">
                        {{ $task->supervisor_remarks }}
                    </div>
                </div>
                @endif

                @if($task->hq_notes)
                <div class="info-group">
                    <div class="info-label">HQ Notes</div>
                    <div class="info-value" style="background: #f8f9fa; padding: 12px; border-radius: 4px;">
                        {{ $task->hq_notes }}
                    </div>
                </div>
                @endif
            </div>
            @endif
        </div>

        <!-- Operator Assignment -->
        <div class="col-lg-4">
            <div class="operator-assignment-card">
                <h4 style="color: #28a745; margin-bottom: 20px;">
                    <i class="fe fe-users"></i> Assigned Operators
                </h4>

                @if($task->assignedOperators->count() > 0)
                <ul class="operator-list">
                    @foreach($task->assignedOperators as $operator)
                    <li class="operator-item">
                        <div>
                            <span class="operator-name">{{ $operator->name }}</span>
                            @if($task->assigned_operator_id == $operator->id)
                            <span class="operator-badge">Primary</span>
                            @endif
                        </div>
                        <button class="btn btn-sm btn-danger remove-operator"
                                data-task-id="{{ $task->id }}"
                                data-operator-id="{{ $operator->id }}"
                                title="Remove Operator">
                            <i class="fe fe-trash-2"></i>
                        </button>
                    </li>
                    @endforeach
                </ul>

                <button class="btn btn-success btn-block mt-3" data-toggle="modal" data-target="#addOperatorModal">
                    <i class="fe fe-plus"></i> Add Another Operator
                </button>

                <button class="btn btn-warning btn-block mt-2" data-toggle="modal" data-target="#clearOperatorsModal">
                    <i class="fe fe-trash-2"></i> Clear All Operators
                </button>
                @else
                <div class="empty-state">
                    <i class="fe fe-alert-circle"></i>
                    <p>No operators assigned yet.</p>
                    <button class="btn btn-success mt-3" data-toggle="modal" data-target="#addOperatorModal">
                        <i class="fe fe-plus"></i> Assign Operator
                    </button>
                </div>
                @endif
            </div>

            <!-- Task Status -->
            <div class="task-info-card">
                <h4 style="color: #667eea; margin-bottom: 20px;">
                    <i class="fe fe-info"></i> Task Status
                </h4>

                <div class="info-group">
                    <div class="info-label">Current State</div>
                    <div class="info-value">
                        <span class="badge badge-primary">
                            {{ str_replace('_', ' ', ucfirst($task->state)) }}
                        </span>
                    </div>
                </div>

                <div class="info-group">
                    <div class="info-label">Created</div>
                    <div class="info-value">
                        {{ $task->created_at->format('M d, Y H:i') }}
                    </div>
                </div>

                <div class="info-group">
                    <div class="info-label">Last Updated</div>
                    <div class="info-value">
                        {{ $task->updated_at->format('M d, Y H:i') }}
                    </div>
                </div>

                <div class="button-group">
                    <a href="{{ route('department-manager.dashboard') }}" class="btn btn-light">
                        <i class="fe fe-arrow-left"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Operator Modal -->
<div class="modal fade" id="addOperatorModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Operator to Task</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addOperatorForm">
                @csrf
                <input type="hidden" name="task_id" value="{{ $task->id }}">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="operatorSelect">Select Operator <span class="text-danger">*</span></label>
                        <select class="form-control" id="operatorSelect" name="operator_id" required>
                            <option value="">-- Choose an Operator --</option>
                            @foreach($availableOperators as $operator)
                                <option value="{{ $operator->id }}">{{ $operator->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="isPrimaryCheckbox">
                            <input type="checkbox" id="isPrimaryCheckbox" name="is_primary" value="1">
                            Set as Primary Operator
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Operator</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Clear Operators Modal -->
<div class="modal fade" id="clearOperatorsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Clear All Operators</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to remove all assigned operators from this task?</p>
                <p class="text-muted">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="clearOperatorsBtn">Clear All</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Add operator form submission
    $('#addOperatorForm').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();

        $.ajax({
            url: '{{ route("department-manager.assign-operator", $task->id) }}',
            type: 'POST',
            data: formData,
            success: function(data) {
                if (data.success) {
                    alert('Operator added successfully');
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Unable to add operator'));
                }
            },
            error: function(xhr) {
                var error = xhr.responseJSON?.message || 'An error occurred';
                alert('Error: ' + error);
            }
        });

        $('#addOperatorModal').modal('hide');
    });

    // Remove operator button
    $(document).on('click', '.remove-operator', function(e) {
        e.preventDefault();
        if (!confirm('Remove this operator from the task?')) {
            return;
        }

        var taskId = $(this).data('task-id');
        var operatorId = $(this).data('operator-id');

        $.ajax({
            url: '{{ route("department-manager.unassign-operator", $task->id) }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                operator_id: operatorId
            },
            success: function(data) {
                if (data.success) {
                    alert('Operator removed');
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Unable to remove operator'));
                }
            },
            error: function() {
                alert('Error removing operator. Please try again.');
            }
        });
    });

    // Clear all operators
    $('#clearOperatorsBtn').on('click', function(e) {
        e.preventDefault();

        $.ajax({
            url: '{{ route("department-manager.clear-operators", $task->id) }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(data) {
                if (data.success) {
                    alert('All operators removed');
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Unable to clear operators'));
                }
            },
            error: function() {
                alert('Error clearing operators. Please try again.');
            }
        });

        $('#clearOperatorsModal').modal('hide');
    });
});
</script>
@endpush
