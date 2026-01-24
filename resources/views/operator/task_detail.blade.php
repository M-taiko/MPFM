@extends('layouts.master')

@section('title', 'Task Details')

@section('css')
<style>
    .info-card {
        background: #f8f9fa;
        border-left: 4px solid #667eea;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 15px;
    }
    .expected-value {
        background: #e3f2fd;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 10px;
    }

    /* Timeline Styles */
    .timeline {
        position: relative;
        padding: 10px 0;
    }

    .timeline-item {
        display: flex;
        margin-bottom: 20px;
        position: relative;
    }

    .timeline-marker {
        flex-shrink: 0;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 3px solid #fff;
        box-shadow: 0 0 0 3px #ddd;
        margin-top: 2px;
        margin-right: 15px;
    }

    .timeline-item:not(:last-child)::before {
        content: '';
        position: absolute;
        left: 5px;
        top: 26px;
        bottom: -20px;
        width: 2px;
        background: #ddd;
    }

    .timeline-content h6 {
        margin-bottom: 5px;
        font-weight: 600;
        font-size: 0.95rem;
    }

    .timeline-content p {
        font-size: 0.85rem;
        line-height: 1.4;
    }
</style>
@endsection

@section('content')
<div class="main-container container-fluid">
    <div class="page-header">
        <div class="page-leftheader">
            <h4 class="page-title mb-0">Task Details</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}"><i class="fe fe-home mr-2 fs-14"></i>Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('operator.dashboard') }}">Operator</a></li>
                <li class="breadcrumb-item"><a href="{{ route('operator.tasks') }}">My Tasks</a></li>
                <li class="breadcrumb-item active" aria-current="page">Task {{ $task->task_identifier }}</li>
            </ol>
        </div>
        <div class="page-rightheader">
            <a href="{{ route('operator.tasks') }}" class="btn btn-outline-primary">
                <i class="fe fe-arrow-left mr-2"></i>Back to Tasks
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Task Information -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Task Information</h3>
                    <div class="card-options">
                        <span class="badge badge-{{ $task->status_badge_class }} badge-lg">
                            {{ ucfirst($task->status) }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="info-card">
                        <h5>{{ $task->task_identifier }}</h5>
                        <p class="mb-0">
                            <strong>Plan:</strong> {{ $task->monthlyPlan->plan_name }}<br>
                            <strong>Period:</strong> {{ $task->monthlyPlan->plan_month }}
                        </p>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6>Location</h6>
                            <p>
                                @if($task->oilString && $task->oilString->tower && $task->oilString->tower->oilField)
                                    <i class="fe fe-map-pin text-primary mr-2"></i>
                                    <strong>Field:</strong> {{ $task->oilString->tower->oilField->name }}<br>
                                    <i class="fe fe-map-pin text-primary mr-2"></i>
                                    <strong>Tower:</strong> {{ $task->oilString->tower->name }}<br>
                                    <i class="fe fe-map-pin text-primary mr-2"></i>
                                    <strong>String:</strong> {{ $task->oilString->name }}
                                @elseif($task->towerModel && $task->towerModel->oilField)
                                    <i class="fe fe-map-pin text-primary mr-2"></i>
                                    <strong>Field:</strong> {{ $task->towerModel->oilField->name }}<br>
                                    <i class="fe fe-map-pin text-primary mr-2"></i>
                                    <strong>Tower:</strong> {{ $task->towerModel->name }}<br>
                                    @if($task->oilString)
                                    <i class="fe fe-map-pin text-primary mr-2"></i>
                                    <strong>String:</strong> {{ $task->oilString->name }}
                                    @endif
                                @else
                                    <span class="text-muted">Location information not available</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6>Device Information</h6>
                            <p>
                                @if($task->mpfm_device)
                                    <i class="fe fe-cpu text-info mr-2"></i>
                                    <strong>Device:</strong> {{ $task->mpfm_device }}<br>
                                @endif
                                @if($task->device_type)
                                    <i class="fe fe-cpu text-info mr-2"></i>
                                    <strong>Type:</strong> {{ $task->device_type }}
                                @endif
                            </p>
                        </div>
                    </div>

                    @if($task->task_instructions)
                    <div class="mb-3">
                        <h6>Task Instructions</h6>
                        <div class="alert alert-info">
                            {{ $task->task_instructions }}
                        </div>
                    </div>
                    @endif

                    <h6>Expected Values</h6>
                    <div class="row">
                        @if($task->expected_oil_rate)
                        <div class="col-md-4">
                            <div class="expected-value">
                                <small class="text-muted">Oil Rate</small>
                                <h5 class="mb-0">{{ number_format($task->expected_oil_rate, 2) }} STB/d</h5>
                            </div>
                        </div>
                        @endif
                        @if($task->expected_gas_rate)
                        <div class="col-md-4">
                            <div class="expected-value">
                                <small class="text-muted">Gas Rate</small>
                                <h5 class="mb-0">{{ number_format($task->expected_gas_rate, 2) }} MMScf/d</h5>
                            </div>
                        </div>
                        @endif
                        @if($task->expected_water_cut)
                        <div class="col-md-4">
                            <div class="expected-value">
                                <small class="text-muted">Water Cut</small>
                                <h5 class="mb-0">{{ number_format($task->expected_water_cut, 2) }}%</h5>
                            </div>
                        </div>
                        @endif
                        @if($task->expected_pressure)
                        <div class="col-md-6">
                            <div class="expected-value">
                                <small class="text-muted">Pressure</small>
                                <h5 class="mb-0">{{ number_format($task->expected_pressure, 2) }} psi</h5>
                            </div>
                        </div>
                        @endif
                        @if($task->expected_temperature)
                        <div class="col-md-6">
                            <div class="expected-value">
                                <small class="text-muted">Temperature</small>
                                <h5 class="mb-0">{{ number_format($task->expected_temperature, 2) }} °F</h5>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Task Actions -->
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        @if($task->status === 'pending')
                            <form action="{{ route('operator.tasks.start', $task) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fe fe-play mr-2"></i>Start This Task
                                </button>
                            </form>
                        @elseif($task->status === 'in_progress')
                            <div>
                                <p class="mb-2"><strong>Scheduled for:</strong> {{ $task->scheduled_date->format('d M Y') }}</p>
                                <a href="{{ route('upload.form') }}?task_id={{ $task->id }}" class="btn btn-success btn-lg">
                                    <i class="fe fe-upload mr-2"></i>Upload Log & Complete Task
                                </a>
                            </div>
                        @elseif($task->status === 'completed')
                            <div class="alert alert-success w-100">
                                <i class="fe fe-check-circle mr-2"></i>
                                <strong>Task Completed</strong><br>
                                @if($task->completed_at)
                                Completed at: {{ $task->completed_at->format('d M Y H:i') }}<br>
                                @endif
                            </div>
                        @endif
                    </div>

                    @if($task->operator_notes || $task->completion_notes)
                    <hr>
                    <div>
                        @if($task->operator_notes)
                        <h6>Operator Notes</h6>
                        <p>{{ $task->operator_notes }}</p>
                        @endif
                        @if($task->completion_notes)
                        <h6>Completion Notes</h6>
                        <p>{{ $task->completion_notes }}</p>
                        @endif
                    </div>
                    @endif

                    @if($task->has_issues)
                    <div class="alert alert-danger">
                        <strong>Issues Reported:</strong><br>
                        {{ $task->issues_description }}
                    </div>
                    @endif
                </div>
            </div>

            <!-- Test Results (if completed) -->
            @if($task->mpfmTest)
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Test Results</h3>
                    <div class="card-options" style="display: flex; gap: 5px;">
                        <a href="{{ route('mpfm_tests.show', $task->mpfmTest) }}" class="btn btn-sm btn-primary" title="View Full Test Report">
                            <i class="fe fe-eye mr-2"></i>Details
                        </a>
                        <a href="{{ route('mpfm_tests.show_history', $task->mpfmTest) }}" class="btn btn-sm btn-warning" title="View Validation & History">
                            <i class="fe fe-check-circle mr-2"></i>Validation
                        </a>
                        <a href="{{ route('mpfm_tests.string_analysis', $task->mpfmTest->oil_string_id) }}" class="btn btn-sm btn-info" title="View String Analysis">
                            <i class="fe fe-trending-up mr-2"></i>Analysis
                        </a>
                        @if($task->mpfmTest->test_data)
                        <a href="{{ route('mpfm-tests.view', $task->mpfmTest->id) }}" class="btn btn-sm btn-success" target="_blank" title="View Charts">
                            <i class="fe fe-bar-chart-2 mr-2"></i>Charts
                        </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <h6>Oil Rate</h6>
                            <h4>{{ number_format($task->mpfmTest->oil_rate, 2) }} STB/d</h4>
                            @if($task->expected_oil_rate)
                                @php
                                    $deviation = (($task->mpfmTest->oil_rate - $task->expected_oil_rate) / $task->expected_oil_rate) * 100;
                                @endphp
                                <span class="badge badge-{{ abs($deviation) > 10 ? 'danger' : 'success' }}">
                                    {{ $deviation > 0 ? '+' : '' }}{{ number_format($deviation, 2) }}% vs expected
                                </span>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <h6>Gas Rate</h6>
                            <h4>{{ number_format($task->mpfmTest->gas_rate, 2) }} MMScf/d</h4>
                        </div>
                        <div class="col-md-4">
                            <h6>Water Cut</h6>
                            <h4>{{ number_format($task->mpfmTest->water_cut, 2) }}%</h4>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Task Status</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Priority:</strong>
                        <span class="badge badge-{{ $task->priority_badge_class }} float-right">
                            {{ ucfirst($task->priority) }}
                        </span>
                    </div>
                    <div class="mb-3">
                        <strong>Status:</strong>
                        <span class="badge badge-{{ $task->status_badge_class }} float-right">
                            {{ ucfirst($task->status) }}
                        </span>
                    </div>
                    <div class="mb-3">
                        <strong>Scheduled Date:</strong>
                        <span class="float-right">{{ $task->scheduled_date->format('d M Y') }}</span>
                    </div>
                    @if($task->completed_at)
                    <div class="mb-3">
                        <strong>Completed:</strong>
                        <span class="float-right">{{ $task->completed_at->format('H:i') }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Timeline -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fe fe-clock mr-2"></i>Timeline
                    </h3>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <!-- Assignment -->
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Assigned</h6>
                                <p class="text-muted mb-0 small">
                                    @if($task->assigned_at)
                                        {{ $task->assigned_at->format('d M Y H:i') }}
                                    @else
                                        Pending
                                    @endif
                                </p>
                            </div>
                        </div>

                        <!-- State History -->
                        @forelse($task->stateHistories()->latest()->get() as $history)
                        <div class="timeline-item">
                            <div class="timeline-marker" style="background-color: {{ $history->fromState?->color() ?? '#6c757d' }}"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">
                                    {{ str_replace('_', ' ', ucfirst($history->to_state)) }}
                                </h6>
                                <p class="text-muted mb-0 small">
                                    {{ $history->created_at->format('d M Y H:i') }}
                                    @if($history->changedBy)
                                        <br>by {{ $history->changedBy->name }}
                                    @endif
                                </p>
                                @if($history->notes)
                                    <p class="mt-1 mb-0 small">{{ $history->notes }}</p>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="timeline-item">
                            <div class="timeline-marker bg-secondary"></div>
                            <div class="timeline-content">
                                <p class="text-muted mb-0 small">No state history available</p>
                            </div>
                        </div>
                        @endforelse

                        <!-- Completion -->
                        @if($task->completed_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Completed</h6>
                                <p class="text-muted mb-0 small">
                                    {{ $task->completed_at->format('d M Y H:i') }}
                                </p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Plan Information</h3>
                </div>
                <div class="card-body">
                    <h6>{{ $task->monthlyPlan->plan_name }}</h6>
                    <p class="text-muted mb-2">{{ $task->monthlyPlan->plan_month }}</p>

                    <small class="text-muted">
                        Total Tests: {{ $task->monthlyPlan->total_tests }}<br>
                        Completed: {{ $task->monthlyPlan->total_completed }}<br>
                        Pending: {{ $task->monthlyPlan->total_pending }}
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
