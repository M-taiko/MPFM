@extends('layouts.master')

@section('title', auth()->user()->department->name . ' Department Dashboard')

@section('css')
<link href="{{URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css')}}" rel="stylesheet">
<link href="{{URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
<style>
    .dept-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 8px;
        margin-bottom: 30px;
    }

    .dept-header h1 {
        margin: 0;
        font-size: 2rem;
        font-weight: 600;
    }

    .stat-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        border-left: 4px solid #667eea;
    }

    .stat-card.success {
        border-left-color: #28a745;
    }

    .stat-card.warning {
        border-left-color: #ffc107;
    }

    .stat-card.danger {
        border-left-color: #dc3545;
    }

    .stat-card.info {
        border-left-color: #17a2b8;
    }

    .stat-value {
        font-size: 2.5rem;
        font-weight: 700;
        color: #667eea;
    }

    .stat-card.success .stat-value {
        color: #28a745;
    }

    .stat-card.warning .stat-value {
        color: #ffc107;
    }

    .stat-card.danger .stat-value {
        color: #dc3545;
    }

    .stat-label {
        color: #666;
        font-size: 0.95rem;
        margin-top: 10px;
    }

    .task-row-unassigned {
        background-color: #fff3cd !important;
    }

    .task-row-assigned {
        background-color: #d4edda !important;
    }

    .task-row-completed {
        background-color: #d1ecf1 !important;
    }

    .operator-badge {
        display: inline-block;
        background: #667eea;
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        margin: 2px;
    }

    .operator-badge.empty {
        background: #dc3545;
    }

    .action-buttons {
        white-space: nowrap;
    }

    .action-buttons .btn {
        padding: 4px 8px;
        font-size: 0.85rem;
        margin: 2px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Department Header -->
    <div class="dept-header">
        <h1>
            <i class="fe fe-briefcase mr-3"></i>
            {{ auth()->user()->department->name }} Department Dashboard
        </h1>
        <p style="margin: 10px 0 0 0; opacity: 0.9;">{{ auth()->user()->department->description }}</p>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stat-card">
                <div class="stat-value">{{ $stats['total_tasks'] }}</div>
                <div class="stat-label"><i class="fe fe-layers mr-2"></i>Total Tasks</div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stat-card warning">
                <div class="stat-value">{{ $stats['unassigned_count'] }}</div>
                <div class="stat-label"><i class="fe fe-alert-circle mr-2"></i>Unassigned</div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stat-card info">
                <div class="stat-value">{{ $stats['in_progress_count'] }}</div>
                <div class="stat-label"><i class="fe fe-zap mr-2"></i>In Progress</div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stat-card success">
                <div class="stat-value">{{ $stats['completed_count'] }}</div>
                <div class="stat-label"><i class="fe fe-check-circle mr-2"></i>Completed</div>
            </div>
        </div>
    </div>

    <!-- Tasks Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Department Tasks</h3>
                    <div class="card-options">
                        <a href="{{ route('department-manager.reports') }}" class="btn btn-sm btn-secondary">
                            <i class="fe fe-bar-chart-2"></i> View Reports
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($tasks->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="tasksTable">
                            <thead>
                                <tr>
                                    <th width="10%">Task ID</th>
                                    <th width="15%">Plan (Month)</th>
                                    <th width="15%">Well</th>
                                    <th width="12%">Status</th>
                                    <th width="25%">Assigned Operators</th>
                                    <th width="15%">Last Update</th>
                                    <th width="8%" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tasks as $task)
                                <tr class="task-row-{{ $task->state }}">
                                    <td>
                                        <strong>{{ $task->id }}</strong>
                                    </td>
                                    <td>
                                        <a href="{{ route('monthly-plans.show', $task->plan) }}">
                                            {{ $task->plan->plan_name }} ({{ $task->plan->plan_month }})
                                        </a>
                                    </td>
                                    <td>
                                        {{ $task->well_number ?? 'N/A' }}
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $task->state === 'completed' ? 'success' : ($task->state === 'in_progress' ? 'info' : 'warning') }}">
                                            {{ str_replace('_', ' ', ucfirst($task->state)) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($task->assignedOperators->count() > 0)
                                            @foreach($task->assignedOperators as $operator)
                                                <span class="operator-badge">
                                                    {{ $operator->name }}
                                                </span>
                                            @endforeach
                                        @else
                                            <span class="operator-badge empty">
                                                <i class="fe fe-alert-circle"></i> No Operator
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $task->updated_at->format('M d, Y H:i') }}
                                        </small>
                                    </td>
                                    <td class="text-center action-buttons">
                                        <a href="{{ route('department-manager.show-task', $task) }}"
                                           class="btn btn-sm btn-primary"
                                           title="View & Manage">
                                            <i class="fe fe-eye"></i>
                                        </a>
                                        @if($task->assignedOperators->count() === 0 && $task->state === 'assigned_to_department')
                                        <button class="btn btn-sm btn-success assign-operator-btn"
                                                data-task-id="{{ $task->id }}"
                                                title="Assign Operator">
                                            <i class="fe fe-plus"></i>
                                        </button>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-info">
                        <i class="fe fe-info"></i> No tasks assigned to your department yet.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assign Operator Modal -->
<div class="modal fade" id="assignOperatorModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Operator to Task</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="assignOperatorForm">
                @csrf
                <input type="hidden" id="taskIdInput" name="task_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="operatorSelect">Select Operator <span class="text-danger">*</span></label>
                        <select class="form-control" id="operatorSelect" name="operator_id" required>
                            <option value="">-- Choose an Operator --</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="isPrimaryCheckbox">
                            <input type="checkbox" id="isPrimaryCheckbox" name="is_primary" value="1" checked>
                            Set as Primary Operator
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Operator</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.min.js')}}"></script>
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#tasksTable').DataTable({
        responsive: true,
        pageLength: 15,
        order: [[5, 'desc']],
        columnDefs: [
            { orderable: false, targets: [4, 6] }
        ]
    });

    // Handle assign operator button click
    $(document).on('click', '.assign-operator-btn', function(e) {
        e.preventDefault();
        var taskId = $(this).data('task-id');
        $('#taskIdInput').val(taskId);

        // Load available operators
        $.ajax({
            url: '{{ route("department-manager.get-operators") }}',
            type: 'GET',
            data: { task_id: taskId },
            success: function(data) {
                var select = $('#operatorSelect');
                select.html('<option value="">-- Choose an Operator --</option>');

                if (data.operators && data.operators.length > 0) {
                    data.operators.forEach(function(operator) {
                        select.append('<option value="' + operator.id + '">' + operator.name + '</option>');
                    });
                }

                $('#assignOperatorModal').modal('show');
            },
            error: function() {
                alert('Error loading operators. Please try again.');
            }
        });
    });

    // Handle assign operator form submission
    $('#assignOperatorForm').on('submit', function(e) {
        e.preventDefault();
        var taskId = $('#taskIdInput').val();
        var operatorId = $('#operatorSelect').val();
        var isPrimary = $('#isPrimaryCheckbox').is(':checked') ? 1 : 0;

        if (!operatorId) {
            alert('Please select an operator');
            return;
        }

        $.ajax({
            url: '{{ route("department-manager.assign-operator", ":task") }}'.replace(':task', taskId),
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                operator_id: operatorId,
                is_primary: isPrimary
            },
            success: function(data) {
                if (data.success) {
                    alert('Operator assigned successfully');
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Unable to assign operator'));
                }
            },
            error: function(xhr) {
                var error = xhr.responseJSON?.message || 'An error occurred';
                alert('Error: ' + error);
            }
        });

        $('#assignOperatorModal').modal('hide');
    });
});
</script>
@endpush
