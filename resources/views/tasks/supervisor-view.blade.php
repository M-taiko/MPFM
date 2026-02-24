@extends('layouts.master')

@section('title', 'Task Workflow - ' . $task->title)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title mb-2">Task Workflow & Details</h1>
                <p class="text-muted mb-0">
                    Task ID: <strong>#{{ $task->id }}</strong> |
                    Title: <strong>{{ $task->title ?? $task->well_full_name }}</strong>
                </p>
            </div>
            <div class="btn-group">
                <a href="{{ route('departments.tasks', $task->department_id) }}" class="btn btn-secondary">
                    <i class="fe fe-arrow-left"></i> Back to Tasks
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <!-- Left Column: Task Details -->
        <div class="col-lg-8">
            <!-- Basic Information Card -->
            <div class="card mb-3" style="border-left: 5px solid #667eea;">
                <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h5 class="card-title mb-0">
                        <i class="fe fe-info"></i> Basic Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-uppercase font-weight-bold text-muted mb-2" style="font-size: 12px;">Task Title</h6>
                            <p class="mb-3">{{ $task->title ?? $task->well_full_name }}</p>

                            <h6 class="text-uppercase font-weight-bold text-muted mb-2" style="font-size: 12px;">Department</h6>
                            <p class="mb-3">
                                <span class="badge badge-primary">{{ $task->department?->name ?? 'N/A' }}</span>
                            </p>

                            <h6 class="text-uppercase font-weight-bold text-muted mb-2" style="font-size: 12px;">Priority</h6>
                            <p class="mb-3">
                                @if($task->priority === 'critical')
                                    <span class="badge badge-danger">Critical</span>
                                @elseif($task->priority === 'high')
                                    <span class="badge badge-warning">High</span>
                                @elseif($task->priority === 'normal')
                                    <span class="badge badge-info">Normal</span>
                                @else
                                    <span class="badge badge-secondary">Low</span>
                                @endif
                            </p>
                        </div>

                        <div class="col-md-6">
                            <h6 class="text-uppercase font-weight-bold text-muted mb-2" style="font-size: 12px;">Current State</h6>
                            <p class="mb-3">
                                <span class="badge badge-success">
                                    {{ is_string($task->state) ? ucwords(str_replace('_', ' ', $task->state)) : $task->state->label() }}
                                </span>
                            </p>

                            <h6 class="text-uppercase font-weight-bold text-muted mb-2" style="font-size: 12px;">Expected Completion</h6>
                            <p class="mb-3">
                                @if($task->expected_completion_at)
                                    {{ $task->expected_completion_at->format('M d, Y H:i') }}
                                    @if($task->expected_completion_at->isPast())
                                        <span class="badge badge-danger">Overdue</span>
                                    @endif
                                @else
                                    <span class="text-muted">Not set</span>
                                @endif
                            </p>

                            <h6 class="text-uppercase font-weight-bold text-muted mb-2" style="font-size: 12px;">Created By</h6>
                            <p class="mb-0">
                                {{ $task->createdBy?->name ?? 'System' }}
                                @if($task->created_at)
                                    <small class="text-muted">({{ $task->created_at->format('M d, Y') }})</small>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Well Details Card -->
            @if($task->well_number || $task->tower)
            <div class="card mb-3" style="border-left: 5px solid #47a3ba;">
                <div class="card-header" style="background: linear-gradient(135deg, #47a3ba 0%, #347a87 100%); color: white;">
                    <h5 class="card-title mb-0">
                        <i class="fe fe-map-pin"></i> Well & Location Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-uppercase font-weight-bold text-muted mb-2" style="font-size: 12px;">Well Number</h6>
                            <p class="mb-3">{{ $task->well_number ?? 'N/A' }}</p>

                            <h6 class="text-uppercase font-weight-bold text-muted mb-2" style="font-size: 12px;">Well Full Name</h6>
                            <p class="mb-3">{{ $task->well_full_name ?? 'N/A' }}</p>

                            <h6 class="text-uppercase font-weight-bold text-muted mb-2" style="font-size: 12px;">String Number</h6>
                            <p class="mb-0">{{ $task->string_number ?? 'N/A' }}</p>
                        </div>

                        <div class="col-md-6">
                            <h6 class="text-uppercase font-weight-bold text-muted mb-2" style="font-size: 12px;">Tower</h6>
                            <p class="mb-3">{{ $task->tower ?? 'N/A' }}</p>

                            <h6 class="text-uppercase font-weight-bold text-muted mb-2" style="font-size: 12px;">Reservoir Layer</h6>
                            <p class="mb-3">{{ $task->reservoir_layer ?? 'N/A' }}</p>

                            <h6 class="text-uppercase font-weight-bold text-muted mb-2" style="font-size: 12px;">Flow Route</h6>
                            <p class="mb-0">{{ $task->flow_route ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Assigned Operators Card -->
            <div class="card mb-3" style="border-left: 5px solid #f39c12;">
                <div class="card-header" style="background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%); color: white;">
                    <h5 class="card-title mb-0">
                        <i class="fe fe-users"></i> Assigned Operators
                    </h5>
                </div>
                <div class="card-body">
                    @if($task->operators->count() > 0)
                        <div class="row">
                            @foreach($task->operators as $operator)
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0">{{ $operator->name }}</h6>
                                        <small class="text-muted">{{ $operator->email }}</small>
                                    </div>
                                    @if($operator->pivot->is_primary)
                                    <span class="badge badge-primary">Primary</span>
                                    @endif
                                </div>
                                <small class="text-muted d-block mt-2">
                                    Assigned: {{ is_string($operator->pivot->assigned_at) ? $operator->pivot->assigned_at : ($operator->pivot->assigned_at ? $operator->pivot->assigned_at->format('M d, Y') : 'N/A') }}
                                </small>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">No operators assigned yet</p>
                    @endif
                </div>
            </div>

            <!-- Task Description Card -->
            @if($task->description)
            <div class="card mb-3" style="border-left: 5px solid #27ae60;">
                <div class="card-header" style="background: linear-gradient(135deg, #27ae60 0%, #229954 100%); color: white;">
                    <h5 class="card-title mb-0">
                        <i class="fe fe-file-text"></i> Description
                    </h5>
                </div>
                <div class="card-body">
                    <p>{{ $task->description }}</p>
                </div>
            </div>
            @endif

        </div>

        <!-- Right Column: Workflow & Status -->
        <div class="col-lg-4">
            <!-- Task State Timeline -->
            <div class="card mb-3" style="border-left: 5px solid #9b59b6;">
                <div class="card-header" style="background: linear-gradient(135deg, #9b59b6 0%, #7a3e7d 100%); color: white;">
                    <h5 class="card-title mb-0">
                        <i class="fe fe-activity"></i> Workflow Timeline
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Timeline States -->
                    <div class="timeline">
                        <!-- Draft State -->
                        <div class="timeline-item">
                            <div class="timeline-marker" style="background-color: #95a5a6;"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Draft</h6>
                                <p class="text-muted small mb-0">Task created</p>
                            </div>
                        </div>

                        <!-- Submitted State -->
                        <div class="timeline-item">
                            <div class="timeline-marker" style="background-color: {{ $task->state && in_array($task->state, ['submitted', 'assigned', 'in_progress', 'reviewed', 'approved', 'completed']) ? '#3498db' : '#ecf0f1' }};"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Submitted</h6>
                                <p class="text-muted small mb-0">Waiting for assignment</p>
                            </div>
                        </div>

                        <!-- Assigned State -->
                        <div class="timeline-item">
                            <div class="timeline-marker" style="background-color: {{ $task->state && in_array($task->state, ['assigned', 'in_progress', 'reviewed', 'approved', 'completed']) ? '#3498db' : '#ecf0f1' }};"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Assigned</h6>
                                <p class="text-muted small mb-0">Operator(s) assigned</p>
                            </div>
                        </div>

                        <!-- In Progress State -->
                        <div class="timeline-item">
                            <div class="timeline-marker" style="background-color: {{ $task->state && in_array($task->state, ['in_progress', 'reviewed', 'approved', 'completed']) ? '#f39c12' : '#ecf0f1' }};"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">In Progress</h6>
                                <p class="text-muted small mb-0">Work in progress</p>
                            </div>
                        </div>

                        <!-- Reviewed State -->
                        <div class="timeline-item">
                            <div class="timeline-marker" style="background-color: {{ $task->state && in_array($task->state, ['reviewed', 'approved', 'completed']) ? '#9b59b6' : '#ecf0f1' }};"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Reviewed</h6>
                                <p class="text-muted small mb-0">Pending review</p>
                            </div>
                        </div>

                        <!-- Approved State -->
                        <div class="timeline-item">
                            <div class="timeline-marker" style="background-color: {{ $task->state && in_array($task->state, ['approved', 'completed']) ? '#27ae60' : '#ecf0f1' }};"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Approved</h6>
                                <p class="text-muted small mb-0">Awaiting completion</p>
                            </div>
                        </div>

                        <!-- Completed State -->
                        <div class="timeline-item">
                            <div class="timeline-marker" style="background-color: {{ $task->state === 'completed' ? '#27ae60' : '#ecf0f1' }};"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Completed</h6>
                                <p class="text-muted small mb-0">Task finished</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Task Statistics Card -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fe fe-bar-chart-2"></i> Task Statistics
                    </h5>
                </div>
                <div class="card-body">
                    <div class="stat-item mb-3">
                        <p class="text-muted small mb-1">Total Assigned Operators</p>
                        <h4>{{ $task->operators->count() }}</h4>
                    </div>

                    @if($task->expected_completion_at)
                    <div class="stat-item mb-3">
                        <p class="text-muted small mb-1">Days Until Due</p>
                        <h4>
                            {{ max(0, $task->expected_completion_at->diffInDays(now())) }}
                            @if($task->expected_completion_at->isPast())
                                <span class="badge badge-danger">Overdue</span>
                            @endif
                        </h4>
                    </div>
                    @endif

                    <div class="stat-item">
                        <p class="text-muted small mb-1">Created</p>
                        <small>{{ $task->created_at?->format('M d, Y H:i') ?? 'N/A' }}</small>
                    </div>
                </div>
            </div>

            <!-- Related MPFM Test Results -->
            @if($task->mpfmTest)
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Test Results</h3>
                    <div class="card-options" style="display: flex; gap: 5px;">
                        <a href="{{ route('mpfm_tests.show', $task->mpfmTest->id) }}" class="btn btn-sm btn-primary" title="View Full Test Report">
                            <i class="fe fe-eye mr-2"></i>Details
                        </a>
                        <a href="{{ route('mpfm_tests.show_history', $task->mpfmTest->id) }}" class="btn btn-sm btn-warning" title="View Validation & History">
                            <i class="fe fe-check-circle mr-2"></i>Validation
                        </a>
                        @if($task->mpfmTest->oilString)
                        <a href="{{ route('mpfm_tests.string_analysis', $task->mpfmTest->oil_string_id) }}" class="btn btn-sm btn-info" title="View String Analysis">
                            <i class="fe fe-trending-up mr-2"></i>Analysis
                        </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if($task->mpfmTest->calculated_kpis)
                        <div class="row">
                            <div class="col-md-4">
                                <h6>Oil Rate</h6>
                                <h4>{{ number_format($task->mpfmTest->calculated_kpis['oil_rate'] ?? $task->mpfmTest->oil_rate ?? 0, 2) }} <small>STB/d</small></h4>
                            </div>
                            <div class="col-md-4">
                                <h6>Gas Rate</h6>
                                <h4>{{ number_format($task->mpfmTest->calculated_kpis['gas_rate'] ?? $task->mpfmTest->gas_rate ?? 0, 2) }} <small>MMScf/d</small></h4>
                            </div>
                            <div class="col-md-4">
                                <h6>Water Cut</h6>
                                <h4>{{ number_format($task->mpfmTest->water_cut ?? 0, 2) }} <small>%</small></h4>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-4">
                                <h6>Pressure</h6>
                                <h4>{{ number_format($task->mpfmTest->pressure_avg ?? $task->mpfmTest->pressure ?? 0, 2) }} <small>psi</small></h4>
                            </div>
                            <div class="col-md-4">
                                <h6>Temperature</h6>
                                <h4>{{ number_format($task->mpfmTest->temperature_avg ?? $task->mpfmTest->temperature ?? 0, 2) }} <small>°F</small></h4>
                            </div>
                            <div class="col-md-4">
                                <h6>GOR</h6>
                                <h4>{{ number_format($task->mpfmTest->calculated_kpis['gor'] ?? 0, 2) }}</h4>
                            </div>
                        </div>
                    @else
                        <p class="text-muted mb-0">Test data not yet processed</p>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
    .timeline {
        position: relative;
        padding: 10px 0;
    }

    .timeline-item {
        display: flex;
        margin-bottom: 20px;
        position: relative;
    }

    .timeline-item:not(:last-child)::before {
        content: '';
        position: absolute;
        left: 7px;
        top: 30px;
        bottom: -20px;
        width: 2px;
        background: #ecf0f1;
    }

    .timeline-marker {
        flex-shrink: 0;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 2px solid white;
        box-shadow: 0 0 0 3px #ecf0f1;
        margin-top: 2px;
        margin-right: 15px;
    }

    .timeline-content h6 {
        margin-bottom: 3px;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .timeline-content p {
        font-size: 0.8rem;
        line-height: 1.3;
    }

    .stat-item {
        padding-bottom: 15px;
        border-bottom: 1px solid #ecf0f1;
    }

    .stat-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .page-header {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .card {
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.12);
    }
</style>

@endsection
