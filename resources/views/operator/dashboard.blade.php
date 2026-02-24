@extends('layouts.master')

@section('title', 'Operator Dashboard')

@section('css')
<style>
    .task-card {
        transition: all 0.3s ease;
        border-left: 4px solid;
        cursor: pointer;
    }
    .task-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .task-card.pending { border-left-color: #ffc107; }
    .task-card.in_progress { border-left-color: #17a2b8; }
    .task-card.completed { border-left-color: #28a745; }
    .task-card.failed { border-left-color: #dc3545; }

    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
    }
    .stat-card.success {
        background: linear-gradient(135deg, #56ab2f 0%, #a8e063 100%);
    }
    .stat-card.warning {
        background: linear-gradient(135deg, #f2994a 0%, #f2c94c 100%);
    }
    .stat-card.danger {
        background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
    }

    .priority-badge {
        font-size: 10px;
        padding: 2px 6px;
    }
</style>
@endsection

@section('content')
<div class="main-container container-fluid">
    <div class="page-header">
        <div class="page-leftheader">
            <h4 class="page-title mb-0">Operator Dashboard</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}"><i class="fe fe-home mr-2 fs-14"></i>Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Operator Dashboard</li>
            </ol>
        </div>
        <div class="page-rightheader">
            <a href="{{ route('operator.tasks') }}" class="btn btn-primary">
                <i class="fe fe-list mr-2"></i>All My Tasks
            </a>
        </div>
    </div>

    <!-- Performance Stats -->
    <div class="row">
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1 text-white-50">Total Tasks</h6>
                        <h2 class="mb-0 font-weight-bold">{{ $performance['total_tasks'] }}</h2>
                        <small class="text-white-50">Last 30 days</small>
                    </div>
                    <div>
                        <i class="fe fe-check-square" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="stat-card success">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1 text-white-50">Completed</h6>
                        <h2 class="mb-0 font-weight-bold">{{ $performance['completed_tasks'] }}</h2>
                        <small class="text-white-50">{{ $performance['completion_rate'] }}% completion rate</small>
                    </div>
                    <div>
                        <i class="fe fe-check-circle" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="stat-card warning">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1 text-white-50">Pending</h6>
                        <h2 class="mb-0 font-weight-bold">{{ $performance['pending_tasks'] }}</h2>
                        <small class="text-white-50">Awaiting action</small>
                    </div>
                    <div>
                        <i class="fe fe-clock" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="stat-card danger">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1 text-white-50">In Progress</h6>
                        <h2 class="mb-0 font-weight-bold">
                            {{ $performance['in_progress_tasks'] }}
                        </h2>
                        <small class="text-white-50">Tasks in progress</small>
                    </div>
                    <div>
                        <i class="fe fe-activity" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SLA Compliance Stats -->
    @php
        $userTasks = \App\Models\PlanTask::where('assigned_operator_id', auth()->user()->id)->get();
        $overdueTasks = $userTasks->where('overdue_flag', true)->count();
        $totalTasks = $userTasks->count();
        $slaCompliance = $totalTasks > 0 ? (($totalTasks - $overdueTasks) / $totalTasks) * 100 : 100;
    @endphp
    <div class="row">
        <div class="col-xl-6 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fe fe-trending-up mr-2"></i> SLA Compliance Overview
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); border-radius: 8px; color: white; padding: 15px;">
                                <h3 class="mb-0 font-weight-bold">{{ number_format($slaCompliance, 1) }}%</h3>
                                <small>Compliance Rate</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border-radius: 8px; color: white; padding: 15px;">
                                <h3 class="mb-0 font-weight-bold">{{ $overdueTasks }}</h3>
                                <small>Overdue Tasks</small>
                            </div>
                        </div>
                    </div>
                    @if($overdueTasks > 0)
                        <div class="alert alert-warning mt-3 mb-0">
                            <i class="fe fe-alert-triangle mr-2"></i>
                            <strong>{{ $overdueTasks }} task(s) need immediate attention</strong>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fe fe-alert-circle mr-2"></i> Overdue Tasks
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $myOverdueTasks = $userTasks->where('overdue_flag', true)->sortBy('expected_completion_at');
                    @endphp
                    @if($myOverdueTasks->isEmpty())
                        <div class="text-center py-3">
                            <i class="fe fe-check-circle" style="font-size: 2rem; opacity: 0.2; color: #28a745;"></i>
                            <p class="text-muted mt-2 mb-0">No overdue tasks</p>
                        </div>
                    @else
                        <div class="list-group list-group-sm">
                            @foreach($myOverdueTasks->take(5) as $task)
                                <a href="{{ route('operator.tasks.show', $task) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">{{ $task->well_number ?? 'Task #' . $task->sno }}</h6>
                                            <small class="text-muted">{{ $task->getOverdueDurationFormatted() ?? 'Status unknown' }}</small>
                                        </div>
                                        <x-sla-badge :task="$task" size="sm" />
                                    </div>
                                </a>
                            @endforeach
                        </div>
                        @if($myOverdueTasks->count() > 5)
                            <a href="{{ route('operator.tasks') }}" class="btn btn-sm btn-outline-primary mt-2 w-100">
                                View All {{ $myOverdueTasks->count() }} Overdue Tasks
                            </a>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Tasks -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">My Tasks for Today ({{ now()->format('d M Y') }})</h3>
                    <div class="card-options">
                        <span class="badge badge-primary">{{ $todaysTasks->count() }} tasks</span>
                    </div>
                </div>
                <div class="card-body">
                    @if($todaysTasks->isEmpty())
                        <div class="text-center py-5">
                            <i class="fe fe-check-circle" style="font-size: 4rem; opacity: 0.2; color: #28a745;"></i>
                            <p class="text-muted mt-3">No tasks assigned for today</p>
                            <p class="text-success">You're all caught up!</p>
                        </div>
                    @else
                        <div class="row">
                            @foreach($todaysTasks as $task)
                            <div class="col-md-6 col-lg-4">
                                <div class="card task-card {{ $task->status }}"
                                     onclick="window.location='{{ route('operator.tasks.show', $task) }}'">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <span class="badge badge-{{ $task->status_badge_class }}">
                                                {{ ucfirst($task->status) }}
                                            </span>
                                            <span class="badge badge-{{ $task->priority_badge_class }} priority-badge">
                                                {{ strtoupper($task->priority) }}
                                            </span>
                                        </div>

                                        <h6 class="mb-2">{{ $task->task_identifier }}</h6>

                                        <p class="text-muted mb-2" style="font-size: 13px;">
                                            <i class="fe fe-map-pin mr-1"></i>
                                            @if($task->towerModel && $task->towerModel->oilField)
                                                {{ $task->towerModel->oilField->name }} / {{ $task->towerModel->name }}
                                            @elseif($task->tower)
                                                {{ $task->tower }}
                                            @else
                                                <span class="text-muted">Location not set</span>
                                            @endif
                                        </p>

                                        @if($task->mpfm_device)
                                        <p class="mb-2" style="font-size: 12px;">
                                            <i class="fe fe-cpu mr-1"></i>{{ $task->mpfm_device }}
                                        </p>
                                        @endif

                                        @if($task->task_instructions)
                                        <p class="text-muted mb-2" style="font-size: 12px;">
                                            {{ Str::limit($task->task_instructions, 80) }}
                                        </p>
                                        @endif

                                        <hr>

                                        <div class="d-flex justify-content-between align-items-center">
                                            @if($task->completion_status === 'pending' || $task->completion_status === null)
                                                <button type="button" class="btn btn-sm btn-primary" onclick="event.stopPropagation(); window.location.href='{{ route('upload.form') }}?task_id={{ $task->id }}'">
                                                    <i class="fe fe-play mr-1"></i>Start Task
                                                </button>
                                            @elseif($task->completion_status === 'in_progress')
                                                <a href="{{ route('operator.tasks.show', $task) }}" class="btn btn-sm btn-success" onclick="event.stopPropagation()">
                                                    <i class="fe fe-check mr-1"></i>Complete Task
                                                </a>
                                            @elseif($task->completion_status === 'completed')
                                                <span class="text-success small">
                                                    <i class="fe fe-check-circle mr-1"></i>Completed
                                                </span>
                                            @else
                                                <button type="button" class="btn btn-sm btn-warning" onclick="event.stopPropagation(); window.location.href='{{ route('upload.form') }}?task_id={{ $task->id }}'">
                                                    <i class="fe fe-play mr-1"></i>Start Task
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- All Pending Tasks -->
    @if($pendingTasks->isNotEmpty())
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Pending Tasks</h3>
                    <div class="card-options">
                        <span class="badge badge-warning">{{ $pendingTasks->count() }} pending</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Task ID</th>
                                    <th>Date</th>
                                    <th>Location</th>
                                    <th>Device</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingTasks as $task)
                                <tr>
                                    <td><strong>{{ $task->sno ?? '-' }}</strong></td>
                                    <td>
                                        @if($task->scheduled_date)
                                            {{ $task->scheduled_date->format('d M Y') }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($task->towerModel && $task->towerModel->name)
                                            {{ $task->towerModel->name }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $task->mpfm_device ?? '-' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $task->priority_badge_class }}">
                                            {{ ucfirst($task->priority) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $task->status_badge_class }}">
                                            {{ ucfirst($task->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('operator.tasks.show', $task) }}" class="btn btn-sm btn-info">
                                            <i class="fe fe-eye"></i>
                                        </a>
                                        @if($task->completion_status === 'pending' || $task->completion_status === null)
                                        <button type="button" class="btn btn-sm btn-primary" onclick="window.location.href='{{ route('upload.form') }}?task_id={{ $task->id }}'">
                                            <i class="fe fe-play"></i> Start
                                        </button>
                                        @elseif($task->completion_status !== 'completed' && $task->completion_status !== 'in_progress')
                                        <button type="button" class="btn btn-sm btn-warning" onclick="window.location.href='{{ route('upload.form') }}?task_id={{ $task->id }}'">
                                            <i class="fe fe-play"></i> Start
                                        </button>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Performance Charts -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Task Status Distribution</h3>
                </div>
                <div class="card-body">
                    <canvas id="taskStatusChart" height="250"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Performance Overview</h3>
                </div>
                <div class="card-body">
                    <canvas id="performanceChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Task Status Doughnut Chart
const statusCtx = document.getElementById('taskStatusChart').getContext('2d');
new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: ['Completed', 'Pending', 'In Progress', 'Failed'],
        datasets: [{
            data: [
                {{ $performance['completed_tasks'] }},
                {{ $performance['pending_tasks'] }},
                {{ $performance['in_progress_tasks'] }},
                {{ $performance['failed_tasks'] }}
            ],
            backgroundColor: [
                '#28a745',
                '#ffc107',
                '#17a2b8',
                '#dc3545'
            ],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
            },
            title: {
                display: false
            }
        }
    }
});

// Performance Bar Chart
const perfCtx = document.getElementById('performanceChart').getContext('2d');
new Chart(perfCtx, {
    type: 'bar',
    data: {
        labels: ['Total Tasks', 'Completed', 'Pending', 'In Progress'],
        datasets: [{
            label: 'Performance Metrics',
            data: [
                {{ $performance['total_tasks'] }},
                {{ $performance['completed_tasks'] }},
                {{ $performance['pending_tasks'] }},
                {{ $performance['in_progress_tasks'] }}
            ],
            backgroundColor: [
                'rgba(102, 126, 234, 0.8)',
                'rgba(40, 167, 69, 0.8)',
                'rgba(255, 193, 7, 0.8)',
                'rgba(23, 162, 184, 0.8)'
            ],
            borderColor: [
                '#667eea',
                '#28a745',
                '#ffc107',
                '#17a2b8'
            ],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0
                }
            }
        }
    }
});
</script>
@endsection
