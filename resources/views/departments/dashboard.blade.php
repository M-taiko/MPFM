@extends('layouts.master')

@section('title', $department->name . ' - Department Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title mb-2">{{ $department->name }} Department</h1>
                <p class="text-muted mb-0">
                    Department Code: <strong>{{ $department->code }}</strong> |
                    Total Tasks: <strong>{{ $totalTasks }}</strong>
                </p>
            </div>
            @if(auth()->user()->hasRole('department_manager'))
            <div class="btn-group">
                <a href="{{ route('departments.tasks', $department->id) }}" class="btn btn-primary">
                    <i class="fe fe-list"></i> View All Tasks
                </a>
                <a href="{{ route('departments.operators', $department->id) }}" class="btn btn-secondary">
                    <i class="fe fe-users"></i> Manage Team
                </a>
            </div>
            @endif
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <!-- Total Tasks -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card" style="border-left: 5px solid #667eea;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Tasks</h6>
                            <h3 class="mb-0">{{ $totalTasks }}</h3>
                        </div>
                        <div class="text-right">
                            <i class="fe fe-layers text-primary" style="font-size: 32px; opacity: 0.5;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Completed Tasks -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card" style="border-left: 5px solid #27ae60;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Completed</h6>
                            <h3 class="mb-0 text-success">{{ $completedTasks }}</h3>
                        </div>
                        <div class="text-right">
                            <i class="fe fe-check-circle text-success" style="font-size: 32px; opacity: 0.5;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- In Progress -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card" style="border-left: 5px solid #f39c12;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">In Progress</h6>
                            <h3 class="mb-0 text-warning">{{ $inProgressTasks }}</h3>
                        </div>
                        <div class="text-right">
                            <i class="fe fe-activity text-warning" style="font-size: 32px; opacity: 0.5;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Completion Rate -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card" style="border-left: 5px solid #2980b9;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Completion Rate</h6>
                            <h3 class="mb-0 text-info">{{ $completionPercentage }}%</h3>
                        </div>
                        <div class="text-right">
                            <i class="fe fe-trending-up text-info" style="font-size: 32px; opacity: 0.5;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Statistics -->
    <div class="row mb-4">
        <!-- Pending Tasks -->
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card" style="border-left: 5px solid #95a5a6;">
                <div class="card-body">
                    <h6 class="text-muted mb-3">Pending Tasks</h6>
                    <div class="d-flex align-items-center">
                        <h3 class="mb-0 text-secondary" style="font-size: 28px;">{{ $pendingTasks }}</h3>
                        <span class="badge badge-secondary ml-2">{{ $totalTasks > 0 ? round(($pendingTasks / $totalTasks) * 100) : 0 }}%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Failed Tasks -->
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card" style="border-left: 5px solid #e74c3c;">
                <div class="card-body">
                    <h6 class="text-muted mb-3">Failed Tasks</h6>
                    <div class="d-flex align-items-center">
                        <h3 class="mb-0 text-danger" style="font-size: 28px;">{{ $failedTasks }}</h3>
                        <span class="badge badge-danger ml-2">{{ $totalTasks > 0 ? round(($failedTasks / $totalTasks) * 100) : 0 }}%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Team Members -->
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card" style="border-left: 5px solid #8e44ad;">
                <div class="card-body">
                    <h6 class="text-muted mb-3">Team Members</h6>
                    <div class="d-flex align-items-center">
                        <h3 class="mb-0 text-purple" style="font-size: 28px;">{{ $teamMembers->count() }}</h3>
                        <span class="badge badge-purple ml-2">Operators</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Bar -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card" style="border-left: 5px solid #3498db;">
                <div class="card-header" style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); color: white;">
                    <h5 class="card-title mb-0">
                        <i class="fe fe-bar-chart-2"></i> Overall Progress
                    </h5>
                </div>
                <div class="card-body">
                    <div class="progress" style="height: 30px;">
                        <div class="progress-bar bg-success" role="progressbar"
                             style="width: {{ $completionPercentage }}%; font-size: 14px; line-height: 30px;"
                             aria-valuenow="{{ $completionPercentage }}" aria-valuemin="0" aria-valuemax="100">
                            {{ $completionPercentage }}% Complete
                        </div>
                    </div>
                    <div class="row text-center mt-3" style="font-size: 12px;">
                        <div class="col-3">
                            <span class="text-muted">Completed:</span><br>
                            <strong>{{ $completedTasks }}/{{ $totalTasks }}</strong>
                        </div>
                        <div class="col-3">
                            <span class="text-muted">In Progress:</span><br>
                            <strong>{{ $inProgressTasks }}/{{ $totalTasks }}</strong>
                        </div>
                        <div class="col-3">
                            <span class="text-muted">Pending:</span><br>
                            <strong>{{ $pendingTasks }}/{{ $totalTasks }}</strong>
                        </div>
                        <div class="col-3">
                            <span class="text-muted">Failed:</span><br>
                            <strong>{{ $failedTasks }}/{{ $totalTasks }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Tasks -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card" style="border-left: 5px solid #2980b9;">
                <div class="card-header" style="background: linear-gradient(135deg, #2980b9 0%, #1a5276 100%); color: white;">
                    <h5 class="card-title mb-0">
                        <i class="fe fe-list"></i> Recent Tasks
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Well Name</th>
                                    <th>Status</th>
                                    <th>Priority</th>
                                    <th>Assigned To</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentTasks as $task)
                                <tr>
                                    <td>
                                        <strong>{{ $task->well_full_name }}</strong><br>
                                        <small class="text-muted">Plan: {{ $task->monthlyPlan->plan_name ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        @if($task->completion_status === 'completed')
                                            <span class="badge badge-success">Completed</span>
                                        @elseif($task->completion_status === 'in_progress')
                                            <span class="badge badge-warning">In Progress</span>
                                        @elseif($task->completion_status === 'failed')
                                            <span class="badge badge-danger">Failed</span>
                                        @else
                                            <span class="badge badge-secondary">Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($task->priority === 'top')
                                            <span class="badge badge-danger">Top</span>
                                        @elseif($task->priority === 'high')
                                            <span class="badge badge-warning">High</span>
                                        @else
                                            <span class="badge badge-info">Normal</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($task->getPrimaryOperator())
                                            <small>{{ $task->getPrimaryOperator()->name }}</small>
                                        @else
                                            <small class="text-muted">Unassigned</small>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('monthly-plans.tasks.show', [$task->monthlyPlan, $task]) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fe fe-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        No tasks yet for this department
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Team Members -->
        <div class="col-lg-4">
            <div class="card" style="border-left: 5px solid #8e44ad;">
                <div class="card-header" style="background: linear-gradient(135deg, #8e44ad 0%, #6c3483 100%); color: white;">
                    <h5 class="card-title mb-0">
                        <i class="fe fe-users"></i> Team Members
                    </h5>
                </div>
                <div class="card-body">
                    @forelse($teamMembers as $member)
                    <div class="list-group-item border-0 pb-3 mb-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">{{ $member->name }}</h6>
                                <small class="text-muted d-block">{{ $member->email }}</small>
                                <small class="badge badge-primary mt-1">
                                    {{ $member->task_count ?? 0 }} Tasks Assigned
                                </small>
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted mb-0">No operators assigned to this department yet</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .page-title {
        font-size: 28px;
        font-weight: 700;
        color: #2c3e50;
    }

    .card {
        border: none;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .badge {
        padding: 6px 12px;
        font-size: 12px;
        font-weight: 600;
    }

    .list-group-item {
        border: 1px solid #e8eaed;
        border-radius: 6px;
        padding: 12px 15px;
    }
</style>
@endsection
