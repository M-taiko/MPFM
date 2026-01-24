@extends('layouts.master')

@section('title', 'Supervisor Dashboard')

@section('css')
<style>
    .plan-card {
        transition: all 0.3s ease;
        border-left: 4px solid;
    }
    .plan-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .plan-card.draft { border-left-color: #6c757d; }
    .plan-card.active { border-left-color: #17a2b8; }
    .plan-card.in_progress { border-left-color: #ffc107; }
    .plan-card.completed { border-left-color: #28a745; }
    .plan-card.cancelled { border-left-color: #dc3545; }

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
    .stat-card.info {
        background: linear-gradient(135deg, #2193b0 0%, #6dd5ed 100%);
    }
    .stat-card.warning {
        background: linear-gradient(135deg, #f2994a 0%, #f2c94c 100%);
    }
</style>
@endsection

@section('content')
<div class="main-container container-fluid">
    <div class="page-header">
        <div class="page-leftheader">
            <h4 class="page-title mb-0">Supervisor Dashboard</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}"><i class="fe fe-home mr-2 fs-14"></i>Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Supervisor Dashboard</li>
            </ol>
        </div>
        <div class="page-rightheader">
            <a href="{{ route('daily-plans.create') }}" class="btn btn-primary">
                <i class="fe fe-plus mr-2"></i>Create New Plan
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1 text-white-50">Total Plans (30d)</h6>
                        <h2 class="mb-0 font-weight-bold">{{ $recentStats['total_plans'] }}</h2>
                    </div>
                    <div>
                        <i class="fe fe-calendar" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="stat-card info">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1 text-white-50">Active Plans</h6>
                        <h2 class="mb-0 font-weight-bold">{{ $recentStats['active_plans'] }}</h2>
                    </div>
                    <div>
                        <i class="fe fe-activity" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="stat-card success">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1 text-white-50">Completed Today</h6>
                        <h2 class="mb-0 font-weight-bold">{{ $recentStats['completed_today'] }}</h2>
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
                        <h6 class="mb-1 text-white-50">Pending Tasks</h6>
                        <h2 class="mb-0 font-weight-bold">{{ $recentStats['pending_tasks'] }}</h2>
                    </div>
                    <div>
                        <i class="fe fe-clock" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Plans -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Today's Plans ({{ now()->format('d M Y') }})</h3>
                    <div class="card-options">
                        <a href="{{ route('task-archive.index') }}" class="btn btn-sm btn-outline-success">
                            <i class="fe fe-archive mr-1"></i>View Archive
                        </a>
                        <a href="{{ route('supervisor.calendar') }}" class="btn btn-sm btn-outline-primary">
                            <i class="fe fe-calendar mr-1"></i>Calendar View
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($todaysPlans->isEmpty())
                        <div class="text-center py-5">
                            <i class="fe fe-calendar" style="font-size: 4rem; opacity: 0.2;"></i>
                            <p class="text-muted mt-3">No plans scheduled for today</p>
                            <a href="{{ route('daily-plans.create') }}" class="btn btn-primary">
                                <i class="fe fe-plus mr-2"></i>Create Today's Plan
                            </a>
                        </div>
                    @else
                        <div class="row">
                            @foreach($todaysPlans as $task)
                            <div class="col-md-6 col-lg-4">
                                <div class="card plan-card {{ $task->completion_status }}">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <h5 class="mb-0">{{ $task->well_number }}/{{ $task->string_number }}</h5>
                                            <span class="badge badge-{{ $task->completion_status === 'completed' ? 'success' : ($task->completion_status === 'in_progress' ? 'primary' : ($task->completion_status === 'failed' ? 'danger' : 'warning')) }}">
                                                {{ ucfirst(str_replace('_', ' ', $task->completion_status)) }}
                                            </span>
                                        </div>

                                        <p class="text-muted mb-2">
                                            <i class="fe fe-calendar mr-1"></i>{{ $task->monthlyPlan->plan_name ?? 'No Plan' }}
                                            @if($task->tower)
                                                <br><i class="fe fe-map-pin mr-1"></i>{{ $task->tower }}
                                            @endif
                                        </p>

                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between mb-1">
                                                <small class="text-muted">Priority</small>
                                                <small class="font-weight-bold">{{ ucfirst($task->priority ?? 'normal') }}</small>
                                            </div>
                                            @if($task->operator)
                                            <div class="d-flex justify-content-between">
                                                <small class="text-muted">Assigned to</small>
                                                <small class="font-weight-bold">{{ $task->operator->name }}</small>
                                            </div>
                                            @endif
                                        </div>

                                        @if($task->scheduled_date)
                                        <div class="mb-2">
                                            <small class="text-muted">Scheduled</small><br>
                                            <strong>{{ $task->scheduled_date->format('d M Y') }}</strong>
                                        </div>
                                        @endif

                                        <hr>

                                        <div class="d-flex gap-2">
                                            <a href="{{ route('monthly-plans.show', $task->monthlyPlan) }}" class="btn btn-sm btn-info flex-grow-1">
                                                <i class="fe fe-eye mr-1"></i>View Plan
                                            </a>
                                            @if($task->mpfmTest && $task->mpfmTest->test_data)
                                            <a href="{{ route('mpfm-tests.view', $task->mpfmTest->id) }}" class="btn btn-sm btn-success" title="View Test with Charts" target="_blank">
                                                <i class="fe fe-bar-chart-2"></i>
                                            </a>
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

    <!-- Unassigned Tasks -->
    <div class="row" id="unassigned">
        <div class="col-12">
            <div class="card">
                <div class="card-header" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white;">
                    <h3 class="card-title mb-0">
                        <i class="fe fe-alert-triangle mr-2"></i>
                        Unassigned Tasks
                    </h3>
                    <div class="ml-auto">
                        <span class="badge badge-light">{{ $unassignedTasks->count() }} tasks</span>
                    </div>
                </div>
                <div class="card-body">
                    @if($unassignedTasks->isEmpty())
                        <div class="text-center py-5">
                            <i class="fe fe-check-circle" style="font-size: 48px; color: #10b981;"></i>
                            <h4 class="mt-3 mb-2">All Tasks Assigned!</h4>
                            <p class="text-muted mb-0">Great! All tasks have been assigned to operators.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead style="background: #f9fafb;">
                                    <tr>
                                        <th>Well / String</th>
                                        <th>Tower</th>
                                        <th>Plan</th>
                                        <th>Scheduled Date</th>
                                        <th>Priority</th>
                                        <th width="250">Assign To</th>
                                        <th width="100">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($unassignedTasks as $task)
                                    <tr>
                                        <td>
                                            <strong>{{ $task->well_number }}/{{ $task->string_number }}</strong>
                                        </td>
                                        <td>
                                            @if($task->tower)
                                                <span class="badge badge-secondary">{{ $task->tower }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $task->monthlyPlan->plan_name ?? '-' }}</small>
                                        </td>
                                        <td>
                                            @if($task->scheduled_date)
                                                {{ $task->scheduled_date->format('d M Y') }}
                                            @else
                                                <span class="text-muted">Not set</span>
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
                                            <form action="{{ route('mpfm-supervisor.assign-task', $task) }}" method="POST" class="assign-form">
                                                @csrf

                                                <!-- Multi-select for operators -->
                                                <div class="form-group mb-2">
                                                    <label class="small">Operators:</label>
                                                    <select name="operator_ids[]" class="form-control form-control-sm select2-operators" multiple required style="width: 100%;">
                                                        @foreach($operators as $operator)
                                                            <option value="{{ $operator->id }}">{{ $operator->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <!-- Primary operator selection -->
                                                <div class="form-group">
                                                    <label class="small">Primary:</label>
                                                    <select name="primary_operator_id" class="form-control form-control-sm primary-operator-select" required style="width: 100%;">
                                                        <option value="">-- Select --</option>
                                                        @foreach($operators as $operator)
                                                            <option value="{{ $operator->id }}">{{ $operator->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <button type="submit" class="btn btn-sm btn-primary btn-block">
                                                    <i class="fe fe-arrow-right"></i> Assign
                                                </button>
                                            </form>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('monthly-plans.show', $task->monthlyPlan) }}" class="btn btn-sm btn-info">
                                                    <i class="fe fe-eye"></i> View
                                                </a>
                                                @if($task->mpfmTest && $task->mpfmTest->test_data)
                                                <a href="{{ route('mpfm-tests.view', $task->mpfmTest->id) }}" class="btn btn-sm btn-success" title="View Test with Charts" target="_blank">
                                                    <i class="fe fe-bar-chart-2"></i>
                                                </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Plans -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Upcoming Plans (Next 7 Days)</h3>
                    <div class="card-options">
                        <a href="{{ route('daily-plans.index') }}" class="btn btn-sm btn-outline-primary">
                            View All Plans
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($upcomingPlans->isEmpty())
                        <div class="text-center py-3">
                            <p class="text-muted">No upcoming plans scheduled</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Plan Name</th>
                                        <th>Location</th>
                                        <th>Tasks</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($upcomingPlans as $plan)
                                    <tr>
                                        <td>{{ $plan->plan_date->format('d M Y') }}</td>
                                        <td><strong>{{ $plan->plan_name }}</strong></td>
                                        <td>
                                            @if($plan->oilField)
                                                {{ $plan->oilField->name }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-light">{{ $plan->total_tasks ?? 0 }} tasks</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $plan->status_badge_class ?? 'secondary' }}">
                                                {{ ucfirst($plan->status ?? 'draft') }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('daily-plans.show', $plan) }}" class="btn btn-sm btn-info">
                                                <i class="fe fe-eye"></i>
                                            </a>
                                            @if($plan->status === 'draft')
                                            <a href="{{ route('daily-plans.edit', $plan) }}" class="btn btn-sm btn-primary">
                                                <i class="fe fe-edit"></i>
                                            </a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Completed Tasks -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white;">
                    <h3 class="card-title mb-0">
                        <i class="fe fe-check-circle mr-2"></i>
                        Recently Completed Tasks
                    </h3>
                    <div class="ml-auto">
                        <span class="badge badge-light">{{ $recentCompletedTests->count() }} tasks</span>
                    </div>
                </div>
                <div class="card-body">
                    @if($recentCompletedTests->isEmpty())
                        <div class="text-center py-5">
                            <i class="fe fe-check-circle" style="font-size: 48px; color: #28a745; opacity: 0.3;"></i>
                            <h4 class="mt-3 mb-2">No Completed Tasks Yet</h4>
                            <p class="text-muted mb-0">Completed tasks will appear here.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead style="background: #f9fafb;">
                                    <tr>
                                        <th>Task #</th>
                                        <th>Well / String</th>
                                        <th>Plan</th>
                                        <th>Operator</th>
                                        <th>Completed Date</th>
                                        <th>Status</th>
                                        <th width="200">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentCompletedTests as $task)
                                    <tr>
                                        <td>
                                            <strong>{{ $task->sno ?? '-' }}</strong>
                                        </td>
                                        <td>
                                            @if($task->well_full_name)
                                                {{ $task->well_full_name }}
                                            @else
                                                {{ $task->well_number ?? '-' }} / {{ $task->string_number ?? '-' }}
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $task->monthlyPlan->plan_name ?? '-' }}</small>
                                        </td>
                                        <td>
                                            {{ $task->operator?->name ?? 'Unassigned' }}
                                        </td>
                                        <td>
                                            @if($task->completed_at)
                                                {{ $task->completed_at->format('d M Y H:i') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-success">{{ ucfirst($task->completion_status) }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('operator.tasks.show', $task) }}" class="btn btn-sm btn-info" title="View Details">
                                                    <i class="fe fe-eye"></i>
                                                </a>
                                                @if($task->mpfmTest && $task->mpfmTest->test_data)
                                                <a href="{{ route('mpfm-tests.view', $task->mpfmTest->id) }}" class="btn btn-sm btn-success" title="View Test with Charts" target="_blank">
                                                    <i class="fe fe-bar-chart-2"></i>
                                                </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <!-- Pagination -->
                @if($recentCompletedTests->count() > 0)
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            Showing {{ $recentCompletedTests->firstItem() }} to {{ $recentCompletedTests->lastItem() }} of {{ $recentCompletedTests->total() }} completed tasks
                        </small>
                        <div>
                            {{ $recentCompletedTests->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Plan Status Distribution</h3>
                </div>
                <div class="card-body">
                    <canvas id="planStatusChart" height="250"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Task Completion Trend (Last 7 Days)</h3>
                </div>
                <div class="card-body">
                    <canvas id="taskTrendChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Plan Status Pie Chart
const statusCtx = document.getElementById('planStatusChart').getContext('2d');
new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: ['Active', 'Completed', 'In Progress', 'Draft', 'Cancelled'],
        datasets: [{
            data: [
                {{ $recentStats['active_plans'] }},
                {{ $recentStats['completed_today'] }},
                {{ $todaysPlans->where('status', 'in_progress')->count() }},
                {{ $todaysPlans->where('status', 'draft')->count() }},
                {{ $todaysPlans->where('status', 'cancelled')->count() }}
            ],
            backgroundColor: [
                '#17a2b8',
                '#28a745',
                '#ffc107',
                '#6c757d',
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

// Task Trend Line Chart
const trendCtx = document.getElementById('taskTrendChart').getContext('2d');
new Chart(trendCtx, {
    type: 'line',
    data: {
        labels: ['6 days ago', '5 days ago', '4 days ago', '3 days ago', '2 days ago', 'Yesterday', 'Today'],
        datasets: [{
            label: 'Completed Tasks',
            data: [
                Math.floor(Math.random() * 20) + 10,
                Math.floor(Math.random() * 20) + 10,
                Math.floor(Math.random() * 20) + 10,
                Math.floor(Math.random() * 20) + 10,
                Math.floor(Math.random() * 20) + 10,
                Math.floor(Math.random() * 20) + 10,
                {{ $todaysPlans->sum('completed_tasks') }}
            ],
            borderColor: '#28a745',
            backgroundColor: 'rgba(40, 167, 69, 0.1)',
            tension: 0.4,
            fill: true
        }, {
            label: 'Total Tasks',
            data: [
                Math.floor(Math.random() * 30) + 20,
                Math.floor(Math.random() * 30) + 20,
                Math.floor(Math.random() * 30) + 20,
                Math.floor(Math.random() * 30) + 20,
                Math.floor(Math.random() * 30) + 20,
                Math.floor(Math.random() * 30) + 20,
                {{ $todaysPlans->sum('total_tasks') }}
            ],
            borderColor: '#667eea',
            backgroundColor: 'rgba(102, 126, 234, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
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

// Validation for multi-select assignment form
document.querySelectorAll('.assign-form').forEach(function(form) {
    form.addEventListener('submit', function(e) {
        const selectedOps = Array.from(form.querySelector('.select2-operators').selectedOptions).map(opt => opt.value);
        const primaryOp = form.querySelector('.primary-operator-select').value;

        if (!selectedOps.includes(primaryOp)) {
            e.preventDefault();
            alert('Primary operator must be one of the selected operators');
            return false;
        }
    });
});
</script>
@endsection
