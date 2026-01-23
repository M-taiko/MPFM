@extends('layouts.master')

@section('title', 'Review Plan - Surveillance Supervisor')

@section('css')
<style>
    .task-card {
        border-left: 4px solid #667eea;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
        margin-bottom: 15px;
    }

    .task-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .task-card.completed {
        border-left-color: #28a745;
        background: #f8fff8;
    }

    .task-card.pending {
        border-left-color: #ffc107;
        background: #fffbf0;
    }

    .task-status-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .task-status-badge.pending {
        background: #ffc107;
        color: white;
    }

    .task-status-badge.completed {
        background: #28a745;
        color: white;
    }

    .task-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .task-identifier {
        font-weight: 600;
        font-size: 1.1rem;
        color: #333;
    }

    .task-meta {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 10px;
        margin-bottom: 10px;
        font-size: 0.9rem;
        color: #666;
    }

    .task-meta-item {
        display: flex;
        justify-content: space-between;
    }

    .task-meta-label {
        font-weight: 600;
        color: #555;
    }

    .task-actions {
        display: flex;
        gap: 10px;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #eee;
    }

    .plan-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 8px;
        margin-bottom: 30px;
    }

    .plan-header h2 {
        margin: 0 0 10px 0;
        font-size: 2rem;
    }

    .plan-header-meta {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-top: 15px;
    }

    .plan-header-item {
        background: rgba(255, 255, 255, 0.1);
        padding: 15px;
        border-radius: 6px;
    }

    .plan-header-item strong {
        display: block;
        font-size: 0.9rem;
        opacity: 0.9;
        margin-bottom: 5px;
    }

    .plan-header-item .value {
        font-size: 1.3rem;
        font-weight: 600;
    }

    .task-list-container {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .modal-dialog {
        max-width: 700px;
    }

    @media (max-width: 768px) {
        .plan-header {
            padding: 20px;
        }

        .plan-header h2 {
            font-size: 1.5rem;
        }

        .plan-header-meta {
            grid-template-columns: 1fr;
        }

        .task-meta {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>
@endsection

@section('content')
<div class="main-container container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-leftheader">
            <h4 class="page-title mb-0">
                <i class="fe fe-clipboard mr-2"></i>Review Monthly Plan
            </h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}"><i class="fe fe-home mr-2 fs-14"></i>Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('surveillance.index') }}">Surveillance Review</a></li>
                <li class="breadcrumb-item active">{{ $plan->plan_name }}</li>
            </ol>
        </div>
        <div class="page-rightheader">
            <a href="{{ route('surveillance.index') }}" class="btn btn-outline-primary">
                <i class="fe fe-arrow-left mr-2"></i>Back to Plans
            </a>
        </div>
    </div>

    <!-- Plan Details Header -->
    <div class="plan-header">
        <h2>{{ $plan->plan_name }}</h2>
        <p class="mb-0">{{ $plan->plan_month }}</p>

        <div class="plan-header-meta">
            <div class="plan-header-item">
                <strong>Created By</strong>
                <div class="value">{{ $plan->creator?->name ?? 'N/A' }}</div>
            </div>
            <div class="plan-header-item">
                <strong>Created At</strong>
                <div class="value">{{ $plan->created_at->format('d M Y') }}</div>
            </div>
            <div class="plan-header-item">
                <strong>Total Tasks</strong>
                <div class="value">{{ $plan->tasks->count() }}</div>
            </div>
            <div class="plan-header-item">
                <strong>Pending Review</strong>
                <div class="value">{{ $pendingTasks }}</div>
            </div>
        </div>
    </div>

    <!-- Status Alert -->
    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fe fe-check-circle mr-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fe fe-alert-circle mr-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Tasks List -->
    <div class="task-list-container">
        @if ($plan->tasks->count() > 0)
            <h5 class="mb-4">Tasks for Review</h5>

            @forelse ($plan->tasks as $task)
            <div class="card task-card {{ $task->state && $task->state->value === 'submitted_to_surveillance' ? 'pending' : 'completed' }}">
                <div class="card-body">
                    <div class="task-status-badge {{ $task->state && $task->state->value === 'submitted_to_surveillance' ? 'pending' : 'completed' }}">
                        {{ $task->state && $task->state->value === 'submitted_to_surveillance' ? 'Pending Review' : 'Reviewed' }}
                    </div>

                    <div class="task-header">
                        <div class="task-identifier">
                            {{ $task->well_full_name ?? $task->sno ?? "Task #{$task->id}" }}
                        </div>
                    </div>

                    <div class="task-meta">
                        <div class="task-meta-item">
                            <span class="task-meta-label">Tower:</span>
                            <span>{{ $task->towerModel?->name ?? 'N/A' }}</span>
                        </div>
                        <div class="task-meta-item">
                            <span class="task-meta-label">String:</span>
                            <span>{{ $task->oilString?->name ?? 'N/A' }}</span>
                        </div>
                        <div class="task-meta-item">
                            <span class="task-meta-label">Priority:</span>
                            <span>
                                <span class="badge badge-{{ $task->priority === 'high' || $task->priority === 'top' ? 'danger' : ($task->priority === 'normal' ? 'info' : 'secondary') }}">
                                    {{ ucfirst($task->priority) }}
                                </span>
                            </span>
                        </div>
                        <div class="task-meta-item">
                            <span class="task-meta-label">Status:</span>
                            <span>
                                <span class="badge badge-{{ $task->completion_status === 'completed' ? 'success' : ($task->completion_status === 'in_progress' ? 'info' : 'warning') }}">
                                    {{ ucfirst($task->completion_status) }}
                                </span>
                            </span>
                        </div>
                        @if ($task->scheduled_date)
                        <div class="task-meta-item">
                            <span class="task-meta-label">Scheduled:</span>
                            <span>{{ $task->scheduled_date->format('d M Y') }}</span>
                        </div>
                        @endif
                        <div class="task-meta-item">
                            <span class="task-meta-label">Assigned To:</span>
                            <span>{{ $task->operator?->name ?? 'Unassigned' }}</span>
                        </div>
                    </div>

                    <!-- Task Actions -->
                    <div class="task-actions">
                        <button class="btn btn-sm btn-info" onclick="viewTaskDetails({{ $task->id }})">
                            <i class="fe fe-eye mr-2"></i>View Details
                        </button>

                        @if ($task->state && $task->state->value === 'submitted_to_surveillance')
                        <form action="{{ route('surveillance.approve-task', ['monthlyPlan' => $plan->id, 'planTask' => $task->id]) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success">
                                <i class="fe fe-check mr-2"></i>Approve
                            </button>
                        </form>
                        <button class="btn btn-sm btn-danger" onclick="showRejectModal({{ $task->id }})">
                            <i class="fe fe-x mr-2"></i>Reject
                        </button>
                        @else
                        <span class="badge badge-success">Approved</span>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="alert alert-info">
                <i class="fe fe-info mr-2"></i>
                All tasks in this plan have been reviewed.
            </div>
            @endforelse
        @else
        <div class="alert alert-info">
            <i class="fe fe-inbox mr-2"></i>
            This plan has no tasks.
        </div>
        @endif
    </div>
</div>

<!-- Task Details Modal -->
<div class="modal fade" id="taskDetailsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Task Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="taskDetailsContent">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Reject Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="remarks">Rejection Remarks <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="remarks" name="remarks" rows="4" placeholder="Please provide the reason for rejection..." required></textarea>
                        <small class="form-text text-muted">This will be sent to the Job Officer</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fe fe-x mr-2"></i>Reject Task
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    var rejectTaskId = null;

    function viewTaskDetails(taskId) {
        const planId = '{{ $plan->id }}';
        const url = `{{ route('surveillance.task-details', ['monthlyPlan' => $plan->id, 'planTask' => ':taskId']) }}`.replace(':taskId', taskId);

        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to load task details');
                }
                return response.text();
            })
            .then(html => {
                document.getElementById('taskDetailsContent').innerHTML = html;
                new bootstrap.Modal(document.getElementById('taskDetailsModal')).show();
            })
            .catch(error => {
                alert('Error loading task details: ' + error.message);
            });
    }

    function showRejectModal(taskId) {
        rejectTaskId = taskId;
        const form = document.getElementById('rejectForm');
        const planId = '{{ $plan->id }}';
        form.action = `{{ route('surveillance.reject-task', ['monthlyPlan' => $plan->id, 'planTask' => ':taskId']) }}`.replace(':taskId', taskId);

        // Clear form
        document.getElementById('remarks').value = '';

        new bootstrap.Modal(document.getElementById('rejectModal')).show();
    }
</script>
@endsection
