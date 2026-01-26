@extends('layouts.master')

@section('title', 'Task Details - ' . $task->well_full_name)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title mb-2">Task Details</h1>
                <p class="text-muted mb-0">
                    Well: <strong>{{ $task->well_full_name }}</strong> |
                    Plan: <strong>{{ $monthlyPlan->plan_name }}</strong>
                </p>
            </div>
            <div class="btn-group">
                <a href="{{ route('monthly-plans.show', $monthlyPlan) }}" class="btn btn-secondary">
                    <i class="fe fe-arrow-left"></i> Back to Plan
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
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
                            <h6 class="text-uppercase font-weight-bold text-muted mb-2" style="font-size: 12px;">Sequence Number</h6>
                            <p class="mb-3">{{ $task->sno }}</p>

                            <h6 class="text-uppercase font-weight-bold text-muted mb-2" style="font-size: 12px;">Department</h6>
                            <p class="mb-3">
                                <span class="badge badge-primary">{{ $task->department?->name ?? 'N/A' }}</span>
                            </p>

                            <h6 class="text-uppercase font-weight-bold text-muted mb-2" style="font-size: 12px;">Priority</h6>
                            <p class="mb-3">
                                @if($task->priority === 'top')
                                    <span class="badge badge-danger">Top Priority</span>
                                @elseif($task->priority === 'high')
                                    <span class="badge badge-warning">High Priority</span>
                                @else
                                    <span class="badge badge-info">Normal Priority</span>
                                @endif
                            </p>

                            <h6 class="text-uppercase font-weight-bold text-muted mb-2" style="font-size: 12px;">Current State</h6>
                            <p class="mb-0">
                                <span class="badge badge-success">{{ $task->state?->label() ?? 'Draft' }}</span>
                            </p>
                        </div>

                        <div class="col-md-6">
                            <h6 class="text-uppercase font-weight-bold text-muted mb-2" style="font-size: 12px;">Tower</h6>
                            <p class="mb-3">{{ $task->tower ?? 'N/A' }}</p>

                            <h6 class="text-uppercase font-weight-bold text-muted mb-2" style="font-size: 12px;">Well Number</h6>
                            <p class="mb-3">{{ $task->well_number }}</p>

                            <h6 class="text-uppercase font-weight-bold text-muted mb-2" style="font-size: 12px;">String Number</h6>
                            <p class="mb-3">{{ $task->string_number }}</p>

                            <h6 class="text-uppercase font-weight-bold text-muted mb-2" style="font-size: 12px;">Scheduled Date</h6>
                            <p class="mb-0">
                                @if($task->scheduled_date)
                                    {{ $task->scheduled_date->format('M d, Y') }}
                                @else
                                    <span class="text-muted">Not scheduled</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Well & Layer Details Card -->
            <div class="card mb-3" style="border-left: 5px solid #47a3ba;">
                <div class="card-header" style="background: linear-gradient(135deg, #47a3ba 0%, #347a87 100%); color: white;">
                    <h5 class="card-title mb-0">
                        <i class="fe fe-map-pin"></i> Well & Layer Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-uppercase font-weight-bold text-muted mb-2" style="font-size: 12px;">Reservoir Layer</h6>
                            <p class="mb-3">{{ $task->reservoir_layer ?? 'N/A' }}</p>

                            <h6 class="text-uppercase font-weight-bold text-muted mb-2" style="font-size: 12px;">Flow Route</h6>
                            <p class="mb-0">{{ $task->flow_route ?? 'N/A' }}</p>
                        </div>

                        <div class="col-md-6">
                            <h6 class="text-uppercase font-weight-bold text-muted mb-2" style="font-size: 12px;">Test Location</h6>
                            <p class="mb-3">
                                @if($task->test_location === 'Locally')
                                    <span class="badge badge-info">Locally</span>
                                @elseif($task->test_location === 'Scada')
                                    <span class="badge badge-secondary">Scada</span>
                                @else
                                    N/A
                                @endif
                            </p>

                            <h6 class="text-uppercase font-weight-bold text-muted mb-2" style="font-size: 12px;">Production Rate</h6>
                            <p class="mb-0">{{ $task->production_rate ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MPFM Configuration Card -->
            <div class="card mb-3" style="border-left: 5px solid #9b59b6;">
                <div class="card-header" style="background: linear-gradient(135deg, #9b59b6 0%, #7a3e7d 100%); color: white;">
                    <h5 class="card-title mb-0">
                        <i class="fe fe-settings"></i> Configuration Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-uppercase font-weight-bold text-muted mb-2" style="font-size: 12px;">MPFM Type</h6>
                            <p class="mb-3">{{ $task->mpfm_type ?? 'N/A' }}</p>

                            <h6 class="text-uppercase font-weight-bold text-muted mb-2" style="font-size: 12px;">DHSV Type</h6>
                            <p class="mb-0">{{ $task->dhsv_type ?? 'N/A' }}</p>
                        </div>

                        <div class="col-md-6">
                            <h6 class="text-uppercase font-weight-bold text-muted mb-2" style="font-size: 12px;">Electrification Status</h6>
                            <p class="mb-3">
                                @if($task->electrification_status === 'Y')
                                    <span class="badge badge-success">Yes</span>
                                @elseif($task->electrification_status === 'N')
                                    <span class="badge badge-danger">No</span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </p>

                            <h6 class="text-uppercase font-weight-bold text-muted mb-2" style="font-size: 12px;">Required WHFP</h6>
                            <p class="mb-0">{{ $task->required_whfp ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Remarks & Notes Card -->
            <div class="card mb-3" style="border-left: 5px solid #f39c12;">
                <div class="card-header" style="background: linear-gradient(135deg, #f39c12 0%, #d68910 100%); color: white;">
                    <h5 class="card-title mb-0">
                        <i class="fe fe-message-square"></i> Remarks & Notes
                    </h5>
                </div>
                <div class="card-body">
                    <h6 class="text-uppercase font-weight-bold text-muted mb-2" style="font-size: 12px;">Supervisor Remarks</h6>
                    <div class="alert alert-info mb-3">
                        {{ $task->supervisor_remarks ?? 'No remarks' }}
                    </div>

                    <h6 class="text-uppercase font-weight-bold text-muted mb-2" style="font-size: 12px;">HQ Notes</h6>
                    <div class="alert alert-light mb-0">
                        {{ $task->hq_notes ?? 'No notes' }}
                    </div>

                    @if($task->operator_notes)
                    <h6 class="text-uppercase font-weight-bold text-muted mb-2" style="font-size: 12px; margin-top: 15px;">Operator Notes</h6>
                    <div class="alert alert-warning mb-0">
                        {{ $task->operator_notes }}
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Sidebar -->
        <div class="col-lg-4">
            <!-- Status & Completion Card -->
            <div class="card mb-3" style="border-left: 5px solid #27ae60;">
                <div class="card-header" style="background: linear-gradient(135deg, #27ae60 0%, #1e8449 100%); color: white;">
                    <h5 class="card-title mb-0">
                        <i class="fe fe-check-circle"></i> Status & Completion
                    </h5>
                </div>
                <div class="card-body">
                    <h6 class="text-uppercase font-weight-bold text-muted mb-2" style="font-size: 12px;">Completion Status</h6>
                    <p class="mb-3">
                        @if($task->completion_status === 'completed')
                            <span class="badge badge-success">Completed</span>
                        @elseif($task->completion_status === 'in_progress')
                            <span class="badge badge-info">In Progress</span>
                        @elseif($task->completion_status === 'failed')
                            <span class="badge badge-danger">Failed</span>
                        @else
                            <span class="badge badge-secondary">Pending</span>
                        @endif
                    </p>

                    <h6 class="text-uppercase font-weight-bold text-muted mb-2" style="font-size: 12px;">Final Status</h6>
                    <p class="mb-3">{{ $task->final_status ?? 'Not set' }}</p>

                    @if($task->completed_at)
                    <h6 class="text-uppercase font-weight-bold text-muted mb-2" style="font-size: 12px;">Completed On</h6>
                    <p class="mb-0">{{ $task->completed_at->format('M d, Y H:i') }}</p>
                    @endif

                    @if($task->completion_result)
                    <h6 class="text-uppercase font-weight-bold text-muted mb-2" style="font-size: 12px; margin-top: 10px;">Completion Result</h6>
                    <p class="mb-0">{{ $task->completion_result }}</p>
                    @endif
                </div>
            </div>

            <!-- Operators Card -->
            <div class="card mb-3" style="border-left: 5px solid #2980b9;">
                <div class="card-header" style="background: linear-gradient(135deg, #2980b9 0%, #1a5276 100%); color: white;">
                    <h5 class="card-title mb-0">
                        <i class="fe fe-users"></i> Assigned Operators
                    </h5>
                </div>
                <div class="card-body">
                    @if($task->operators->count() > 0)
                        <div class="list-group">
                            @foreach($task->operators as $operator)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0">{{ $operator->name }}</h6>
                                        <small class="text-muted">{{ $operator->email }}</small>
                                    </div>
                                    @if($operator->pivot->is_primary)
                                    <span class="badge badge-primary">Primary</span>
                                    @endif
                                </div>
                                <small class="text-muted d-block mt-1">
                                    Assigned: {{ $operator->pivot->assigned_at->format('M d, Y') }}
                                </small>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">No operators assigned yet</p>
                    @endif
                </div>
            </div>

            <!-- SLA Information Card -->
            @if($task->slaHistory || $task->expected_completion_at)
            <div class="card mb-3" style="border-left: 5px solid #e74c3c;">
                <div class="card-header" style="background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%); color: white;">
                    <h5 class="card-title mb-0">
                        <i class="fe fe-alert-circle"></i> SLA Information
                    </h5>
                </div>
                <div class="card-body">
                    @if($task->expected_completion_at)
                    <h6 class="text-uppercase font-weight-bold text-muted mb-2" style="font-size: 12px;">Expected Completion</h6>
                    <p class="mb-3">
                        {{ $task->expected_completion_at->format('M d, Y H:i') }}
                        @if($task->overdue_flag)
                        <span class="badge badge-danger">Overdue</span>
                        @endif
                    </p>
                    @endif

                    @if($task->slaHistory)
                    <h6 class="text-uppercase font-weight-bold text-muted mb-2" style="font-size: 12px;">SLA Duration</h6>
                    <p class="mb-3">{{ $task->slaHistory->sla_duration_hours }} hours</p>

                    <h6 class="text-uppercase font-weight-bold text-muted mb-2" style="font-size: 12px;">Escalation Level</h6>
                    <p class="mb-0">{{ $task->slaHistory->getEscalationLabel() }}</p>
                    @endif
                </div>
            </div>
            @endif

            <!-- Timeline Card -->
            @if($task->stateHistories->count() > 0)
            <div class="card" style="border-left: 5px solid #3498db;">
                <div class="card-header" style="background: linear-gradient(135deg, #3498db 0%, #2471a3 100%); color: white;">
                    <h5 class="card-title mb-0">
                        <i class="fe fe-clock"></i> State History
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @foreach($task->stateHistories->reverse() as $history)
                        <div class="timeline-item" style="margin-bottom: 15px;">
                            <div class="d-flex">
                                <div class="timeline-marker" style="width: 12px; height: 12px; background: #667eea; border-radius: 50%; margin-right: 10px; margin-top: 4px;"></div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        {{ $history->to_state?->label() ?? 'Unknown State' }}
                                    </h6>
                                    <small class="text-muted">
                                        {{ $history->created_at->format('M d, Y H:i') }}
                                    </small>
                                    @if($history->notes)
                                    <p class="mb-0 mt-1" style="font-size: 13px;">{{ $history->notes }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
    .timeline-item {
        position: relative;
    }

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
        margin-bottom: 8px;
    }
</style>
@endsection
