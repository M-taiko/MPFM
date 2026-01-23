@extends('layouts.master')

@section('title', 'Plan Details - ' . $monthlyPlan->plan_name)

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div>
        <h1 class="page-title">{{ $monthlyPlan->plan_name }}</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('monthly-plans.index') }}">Monthly Plans</a></li>
            <li class="breadcrumb-item active">Plan Details</li>
        </ol>
    </div>
    <div class="ml-auto">
        @if($monthlyPlan->isEditable())
        <a href="{{ route('monthly-plans.tasks.create', $monthlyPlan) }}" class="btn btn-success mr-2">
            <i class="fe fe-plus mr-2"></i> Add Task
        </a>
        <a href="{{ route('monthly-plans.edit', $monthlyPlan) }}" class="btn btn-info mr-2">
            <i class="fe fe-edit mr-2"></i> Edit Plan
        </a>
        @endif
        @if($monthlyPlan->status === 'draft' && $monthlyPlan->tasks()->count() > 0)
        <form action="{{ route('monthly-plans.submit', $monthlyPlan) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-primary" onclick="return confirm('Submit this plan for MPFM Supervisor review?')">
                <i class="fe fe-send mr-2"></i> Submit for Review
            </button>
        </form>
        @endif
    </div>
</div>

<!-- Success/Error Messages -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fe fe-check-circle mr-2"></i> {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fe fe-x-circle mr-2"></i> {{ session('error') }}
    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
</div>
@endif

<!-- Plan Header Card -->
<div class="row">
    <div class="col-12">
        <div class="card" style="border-left: 4px solid {{ $monthlyPlan->status === 'active' ? '#10b981' : ($monthlyPlan->status === 'draft' ? '#fbbf24' : ($monthlyPlan->status === 'under_review' ? '#3b82f6' : ($monthlyPlan->status === 'rejected' ? '#ef4444' : '#10b981'))) }};">
            <div class="card-header" style="background: linear-gradient(135deg, {{ $monthlyPlan->status === 'active' ? '#10b981 0%, #059669' : ($monthlyPlan->status === 'draft' ? '#fbbf24 0%, #f59e0b' : ($monthlyPlan->status === 'under_review' ? '#3b82f6 0%, #2563eb' : ($monthlyPlan->status === 'rejected' ? '#ef4444 0%, #dc2626' : '#10b981 0%, #059669'))) }} 100%); color: white;">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fe fe-clipboard mr-2"></i> Plan Overview
                    </h3>
                    <span class="badge badge-light">
                        {{ ucfirst(str_replace('_', ' ', $monthlyPlan->status)) }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Plan Info -->
                    <div class="col-md-6">
                        <table class="table table-borderless mb-0">
                            <tr>
                                <td class="font-weight-semibold" width="40%">Plan Name:</td>
                                <td>{{ $monthlyPlan->plan_name }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-semibold">Plan Month:</td>
                                <td>{{ $monthlyPlan->plan_month }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-semibold">Created By:</td>
                                <td>{{ $monthlyPlan->creator->name }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-semibold">Created Date:</td>
                                <td>{{ $monthlyPlan->created_at->format('d M Y, h:i A') }}</td>
                            </tr>
                            @if($monthlyPlan->reviewed_by)
                            <tr>
                                <td class="font-weight-semibold">Reviewed By:</td>
                                <td>{{ $monthlyPlan->reviewer->name }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-semibold">Reviewed Date:</td>
                                <td>{{ $monthlyPlan->reviewed_at->format('d M Y, h:i A') }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>

                    <!-- Statistics -->
                    <div class="col-md-6">
                        <div class="row text-center">
                            <div class="col-6 mb-3">
                                <div class="p-3" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border-radius: 8px; color: white;">
                                    <h2 class="mb-0 font-weight-bold">{{ $monthlyPlan->total_tests }}</h2>
                                    <small>Total Tasks</small>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="p-3" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 8px; color: white;">
                                    <h2 class="mb-0 font-weight-bold">{{ $monthlyPlan->total_completed }}</h2>
                                    <small>Completed</small>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="p-3" style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); border-radius: 8px; color: white;">
                                    <h2 class="mb-0 font-weight-bold">{{ $monthlyPlan->total_pending }}</h2>
                                    <small>Pending</small>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="p-3" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border-radius: 8px; color: white;">
                                    <h2 class="mb-0 font-weight-bold">{{ $monthlyPlan->total_issues }}</h2>
                                    <small>Issues</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="font-weight-semibold">Overall Progress</span>
                            <span class="font-weight-semibold">{{ $monthlyPlan->total_tests > 0 ? round(($monthlyPlan->total_completed / $monthlyPlan->total_tests) * 100) : 0 }}%</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-success" style="width: {{ $monthlyPlan->total_tests > 0 ? ($monthlyPlan->total_completed / $monthlyPlan->total_tests) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- SLA Status Summary -->
                @php
                    $overdueTasks = $monthlyPlan->tasks()->where('overdue_flag', true)->count();
                    $totalTasks = $monthlyPlan->tasks()->count();
                    $slaCompliance = $totalTasks > 0 ? (($totalTasks - $overdueTasks) / $totalTasks) * 100 : 100;
                @endphp
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="p-3" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); border-radius: 8px; color: white;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-white-80">SLA Compliance</small>
                                    <h3 class="mb-0 font-weight-bold">{{ number_format($slaCompliance, 1) }}%</h3>
                                </div>
                                <div class="text-right">
                                    <div>
                                        <small>On Track: <strong>{{ $totalTasks - $overdueTasks }}</strong></small><br>
                                        <small>Overdue: <strong>{{ $overdueTasks }}</strong></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- KPI Section -->
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="p-3" style="background: #f3f4f6; border-radius: 8px;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted">Monthly KPI Target</small>
                                    <h3 class="mb-0 font-weight-bold">{{ $monthlyPlan->monthly_target_kpi }} tests</h3>
                                </div>
                                <div class="text-right">
                                    <small class="text-muted">KPI Progress</small>
                                    <h3 class="mb-0 font-weight-bold {{ $monthlyPlan->kpi_progress >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $monthlyPlan->kpi_progress > 0 ? '+' : '' }}{{ $monthlyPlan->kpi_progress }}
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                @if($monthlyPlan->notes)
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="alert alert-info mb-0">
                            <strong><i class="fe fe-info mr-2"></i> Notes:</strong><br>
                            {{ $monthlyPlan->notes }}
                        </div>
                    </div>
                </div>
                @endif

                <!-- Rejection Reason -->
                @if($monthlyPlan->status === 'rejected' && $monthlyPlan->rejection_reason)
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="alert alert-danger mb-0">
                            <strong><i class="fe fe-x-circle mr-2"></i> Rejection Reason:</strong><br>
                            {{ $monthlyPlan->rejection_reason }}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Tasks Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color: white;">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fe fe-list mr-2"></i> Tasks ({{ $monthlyPlan->tasks()->count() }})
                    </h3>
                    @if($monthlyPlan->isEditable())
                    <a href="{{ route('monthly-plans.tasks.create', $monthlyPlan) }}" class="btn btn-sm btn-light">
                        <i class="fe fe-plus mr-1"></i> Add Task
                    </a>
                    @endif
                </div>
            </div>
            <div class="card-body p-0">
                @if($monthlyPlan->tasks()->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead style="background: #f9fafb;">
                            <tr>
                                <th width="50">SNO</th>
                                <th>Well</th>
                                <th>Tower</th>
                                <th>String</th>
                                <th>Flow Route</th>
                                <th>Reservoir</th>
                                <th>MPFM Type</th>
                                <th>Required WHFP</th>
                                <th>Scheduled Date</th>
                                <th>Assigned To</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>SLA Status</th>
                                <th width="100">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($monthlyPlan->tasks->sortBy('sno') as $task)
                            <tr>
                                <td class="font-weight-semibold">{{ $task->sno }}</td>
                                <td>{{ $task->well_number }}</td>
                                <td>
                                    @if($task->tower)
                                    <span class="badge badge-secondary">{{ $task->tower }}</span>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $task->string_number }}</td>
                                <td>{{ $task->flow_route ?? '-' }}</td>
                                <td>{{ $task->reservoir_layer ?? '-' }}</td>
                                <td>{{ $task->mpfm_type ?? '-' }}</td>
                                <td>{{ $task->required_whfp ? number_format($task->required_whfp, 2) : '-' }}</td>
                                <td>
                                    @if($task->scheduled_date)
                                    {{ \Carbon\Carbon::parse($task->scheduled_date)->format('d M Y') }}
                                    @if($task->isOverdue())
                                    <br><span class="badge badge-danger">Overdue</span>
                                    @endif
                                    @else
                                    <span class="text-muted">Not set</span>
                                    @endif
                                </td>
                                <td>
                                    @if($task->operator)
                                    <span class="badge badge-info">{{ $task->operator->name }}</span>
                                    @else
                                    <span class="text-muted">Unassigned</span>
                                    @endif
                                </td>
                                <td>
                                    @if($task->priority === 'top')
                                    <span class="badge badge-danger">Top</span>
                                    @elseif($task->priority === 'high')
                                    <span class="badge badge-warning">High</span>
                                    @else
                                    <span class="badge badge-secondary">Normal</span>
                                    @endif
                                </td>
                                <td>
                                    @if($task->completion_status === 'completed')
                                    <span class="badge badge-success">Completed</span>
                                    @elseif($task->completion_status === 'in_progress')
                                    <span class="badge badge-primary">In Progress</span>
                                    @elseif($task->completion_status === 'failed')
                                    <span class="badge badge-danger">Failed</span>
                                    @else
                                    <span class="badge badge-warning">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    <x-sla-badge :task="$task" size="sm" />
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-sm btn-info" onclick="viewTask({{ $task->id }})" title="View Details">
                                            <i class="fe fe-eye"></i>
                                        </button>
                                        @if($task->mpfm_test_id)
                                        <a href="{{ route('mpfm-tests.view', $task->mpfm_test_id) }}" class="btn btn-sm btn-success" title="View Test Result">
                                            <i class="fe fe-check-circle"></i>
                                        </a>
                                        @endif
                                        @if($monthlyPlan->isEditable())
                                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteTask({{ $task->id }})" title="Delete">
                                            <i class="fe fe-trash"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="fe fe-inbox" style="font-size: 64px; color: #cbd5e1;"></i>
                    <h3 class="mt-3 mb-2">No Tasks Added Yet</h3>
                    <p class="text-muted mb-4">Start by adding tasks to this plan</p>
                    @if($monthlyPlan->isEditable())
                    <a href="{{ route('monthly-plans.tasks.create', $monthlyPlan) }}" class="btn btn-primary">
                        <i class="fe fe-plus mr-2"></i> Add First Task
                    </a>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Task Details Modal -->
<div class="modal fade" id="taskModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document" style="max-width: 600px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Task Details</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body" id="taskModalBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Task Form -->
<form id="deleteTaskForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@push('scripts')
<script>
function viewTask(taskId) {
    $('#taskModal').modal('show');
    $('#taskModalBody').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>');

    // Fetch task details via AJAX
    $.ajax({
        url: '/monthly-plans/{{ $monthlyPlan->id }}/tasks/' + taskId + '/details',
        method: 'GET',
        success: function(html) {
            $('#taskModalBody').html(html);
        },
        error: function() {
            $('#taskModalBody').html('<div class="alert alert-danger">Failed to load task details. Please try again.</div>');
        }
    });
}

function deleteTask(taskId) {
    if (confirm('Are you sure you want to delete this task? This action cannot be undone.')) {
        const form = document.getElementById('deleteTaskForm');
        form.action = `/monthly-plans/{{ $monthlyPlan->id }}/tasks/${taskId}`;
        form.submit();
    }
}
</script>
@endpush
