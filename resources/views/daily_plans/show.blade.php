@extends('layouts.master')

@section('title', 'Daily Plan Details')

@section('css')
<link href="{{URL::asset('assets/plugins/sweet-alert/sweetalert.css')}}" rel="stylesheet">
<style>
    .plan-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 10px;
        margin-bottom: 20px;
    }
    .stat-box {
        background: white;
        padding: 15px;
        border-radius: 8px;
        text-align: center;
    }
    .stat-box h3 {
        margin: 0;
        font-size: 2rem;
    }
    .task-table tr {
        cursor: pointer;
        transition: all 0.2s;
    }
    .task-table tr:hover {
        background: #f8f9fa;
    }
</style>
@endsection

@section('content')
<div class="main-container container-fluid">
    <div class="page-header">
        <div class="page-leftheader">
            <h4 class="page-title mb-0">Daily Plan Details</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}"><i class="fe fe-home mr-2 fs-14"></i>Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('daily-plans.index') }}">Daily Plans</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $dailyPlan->plan_name }}</li>
            </ol>
        </div>
        <div class="page-rightheader">
            @if($dailyPlan->status === 'draft')
                <a href="{{ route('daily-plans.edit', $dailyPlan) }}" class="btn btn-primary mr-2">
                    <i class="fe fe-edit mr-2"></i>Edit Plan
                </a>
                <form action="{{ route('daily-plans.activate', $dailyPlan) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-success" onclick="return confirm('Activate this plan? Operators will be able to see their tasks.')">
                        <i class="fe fe-check-circle mr-2"></i>Activate Plan
                    </button>
                </form>
            @elseif(in_array($dailyPlan->status, ['active', 'in_progress']))
                <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#cancelModal">
                    <i class="fe fe-x-circle mr-2"></i>Cancel Plan
                </button>
            @endif

            @if($dailyPlan->status === 'completed')
                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#cloneModal">
                    <i class="fe fe-copy mr-2"></i>Clone Plan
                </button>
            @endif

            <a href="{{ route('daily-plans.index') }}" class="btn btn-outline-primary">
                <i class="fe fe-arrow-left mr-2"></i>Back
            </a>
        </div>
    </div>

    <!-- Plan Header -->
    <div class="plan-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h2 class="mb-2">{{ $dailyPlan->plan_name }}</h2>
                <p class="mb-2">
                    <i class="fe fe-calendar mr-2"></i>{{ $dailyPlan->plan_date->format('l, d F Y') }}
                </p>
                <p class="mb-0">
                    <i class="fe fe-map-pin mr-2"></i>{{ $dailyPlan->oilField->name }}
                    @if($dailyPlan->tower)
                        / {{ $dailyPlan->tower->name }}
                    @endif
                </p>
            </div>
            <div class="col-md-6 text-right">
                <span class="badge badge-{{ $dailyPlan->status_badge_class }} badge-lg p-3" style="font-size: 1.2rem;">
                    {{ ucfirst($dailyPlan->status) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-primary">{{ $stats['total_tasks'] }}</h3>
                    <p class="text-muted mb-0">Total Tasks</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-success">{{ $stats['completed_tasks'] }}</h3>
                    <p class="text-muted mb-0">Completed</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-warning">{{ $stats['pending_tasks'] }}</h3>
                    <p class="text-muted mb-0">Pending</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-info">{{ $stats['in_progress_tasks'] }}</h3>
                    <p class="text-muted mb-0">In Progress</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Tasks List -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tasks</h3>
                    <div class="card-options">
                        <div class="progress" style="width: 200px; height: 25px;">
                            <div class="progress-bar bg-{{ $dailyPlan->completion_percentage == 100 ? 'success' : 'info' }}"
                                 style="width: {{ $dailyPlan->completion_percentage }}%">
                                {{ $dailyPlan->completion_percentage }}%
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover task-table">
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Operator</th>
                                    <th>Location</th>
                                    <th>Device</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Duration</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dailyPlan->tasks->sortBy('task_order') as $task)
                                <tr>
                                    <td><strong>#{{ $task->task_order }}</strong></td>
                                    <td>{{ $task->operator->name }}</td>
                                    <td>
                                        <small>
                                            {{ $task->oilString->tower->name }} / {{ $task->oilString->name }}
                                        </small>
                                    </td>
                                    <td>{{ $task->mpfm_device ?? '-' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $task->priority_badge_class }}">
                                            {{ ucfirst($task->priority) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $task->status_badge_class }}">
                                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($task->duration_minutes)
                                            {{ $task->duration_minutes }} min
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('operator.tasks.show', $task) }}" class="btn btn-sm btn-info" title="View Task">
                                            <i class="fe fe-eye"></i>
                                        </a>
                                        @if($task->mpfmTest)
                                        <a href="{{ route('mpfm_tests.show', $task->mpfmTest) }}" class="btn btn-sm btn-success" title="View Test">
                                            <i class="fe fe-file-text"></i>
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="fe fe-inbox" style="font-size: 3rem; opacity: 0.2;"></i>
                                        <p class="text-muted mt-2">No tasks in this plan</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Plan Info -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Plan Information</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Created By:</strong>
                        <span class="float-right">{{ $dailyPlan->creator->name }}</span>
                    </div>
                    <div class="mb-3">
                        <strong>Created:</strong>
                        <span class="float-right">{{ $dailyPlan->created_at->format('d M Y H:i') }}</span>
                    </div>
                    @if($dailyPlan->started_at)
                    <div class="mb-3">
                        <strong>Started:</strong>
                        <span class="float-right">{{ $dailyPlan->started_at->format('d M Y H:i') }}</span>
                    </div>
                    @endif
                    @if($dailyPlan->completed_at)
                    <div class="mb-3">
                        <strong>Completed:</strong>
                        <span class="float-right">{{ $dailyPlan->completed_at->format('d M Y H:i') }}</span>
                    </div>
                    @endif
                    <div class="mb-3">
                        <strong>Operators:</strong>
                        <span class="float-right">{{ $stats['operators_count'] }}</span>
                    </div>
                    @if($stats['average_task_duration'])
                    <div class="mb-3">
                        <strong>Avg. Duration:</strong>
                        <span class="float-right">{{ number_format($stats['average_task_duration'], 0) }} min</span>
                    </div>
                    @endif
                    @if($stats['tasks_with_issues'])
                    <div class="mb-3">
                        <strong>Issues:</strong>
                        <span class="float-right text-danger">{{ $stats['tasks_with_issues'] }} tasks</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Plan Notes -->
            @if($dailyPlan->plan_notes)
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Plan Notes</h3>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $dailyPlan->plan_notes }}</p>
                </div>
            </div>
            @endif

            <!-- Assigned Operators -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Assigned Operators</h3>
                </div>
                <div class="card-body">
                    @php
                        $operatorTasks = $dailyPlan->tasks->groupBy('assigned_to');
                    @endphp
                    @foreach($operatorTasks as $operatorId => $tasks)
                        @php
                            $operator = $tasks->first()->operator;
                            $completed = $tasks->where('status', 'completed')->count();
                            $total = $tasks->count();
                        @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <strong>{{ $operator->name }}</strong>
                                <small class="text-muted">{{ $completed }}/{{ $total }}</small>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-{{ $completed == $total ? 'success' : 'info' }}"
                                     style="width: {{ $total > 0 ? ($completed / $total * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('daily-plans.cancel', $dailyPlan) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Cancel Plan</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Reason for Cancellation</label>
                        <textarea name="reason" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="alert alert-warning">
                        <i class="fe fe-alert-triangle mr-2"></i>
                        All pending and in-progress tasks will be cancelled.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Cancel Plan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Clone Modal -->
<div class="modal fade" id="cloneModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('daily-plans.clone', $dailyPlan) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Clone Plan</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>New Plan Date <span class="text-danger">*</span></label>
                        <input type="date" name="new_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>New Plan Name (Optional)</label>
                        <input type="text" name="new_name" class="form-control" placeholder="Leave empty to auto-generate">
                    </div>
                    <div class="alert alert-info">
                        <i class="fe fe-info mr-2"></i>
                        All tasks and their settings will be copied to the new plan.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Clone Plan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{URL::asset('assets/plugins/sweet-alert/sweetalert.min.js')}}"></script>
@endsection
