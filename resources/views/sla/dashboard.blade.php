@extends('layouts.master')

@section('title', 'SLA Monitoring Dashboard')

@section('css')
<style>
    .sla-stat-card {
        border-left: 4px solid;
        transition: all 0.3s ease;
    }
    .sla-stat-card.on-track {
        border-left-color: #10b981;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }
    .sla-stat-card.warning {
        border-left-color: #f59e0b;
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }
    .sla-stat-card.danger {
        border-left-color: #ef4444;
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    }
    .sla-stat-card h3 {
        color: white;
        margin-bottom: 0;
        font-weight: bold;
    }
    .sla-stat-card p {
        color: rgba(255, 255, 255, 0.8);
        margin-bottom: 0;
        font-size: 13px;
    }

    .compliance-chart {
        height: 250px;
    }
</style>
@endsection

@section('content')
<div class="main-container container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-leftheader">
            <h4 class="page-title mb-0">SLA Monitoring Dashboard</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fe fe-home mr-2"></i>Dashboard</a></li>
                <li class="breadcrumb-item active">SLA Monitoring</li>
            </ol>
        </div>
        <div class="page-rightheader">
            <button type="button" class="btn btn-primary" onclick="location.reload()">
                <i class="fe fe-refresh-cw mr-2"></i> Refresh
            </button>
        </div>
    </div>

    <!-- Overall SLA Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3">
            <div class="card sla-stat-card on-track">
                <div class="card-body p-4">
                    <h3>{{ $slaStats['on_time'] ?? 0 }}</h3>
                    <p>Tasks On Track</p>
                    <small class="text-white-50">Total: {{ $slaStats['total_tasks'] ?? 0 }}</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3">
            <div class="card sla-stat-card warning">
                <div class="card-body p-4">
                    <h3>{{ $slaStats['overdue'] ?? 0 }}</h3>
                    <p>Overdue Tasks</p>
                    <small class="text-white-50">Requires Action</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3">
            <div class="card sla-stat-card on-track">
                <div class="card-body p-4">
                    <h3>{{ number_format($slaStats['compliance_percentage'] ?? 100, 1) }}%</h3>
                    <p>Overall Compliance</p>
                    <small class="text-white-50">SLA Target: > 85%</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3">
            <div class="card sla-stat-card danger">
                <div class="card-body p-4">
                    <h3 id="level3-escalations">{{ $escalationStats['level_3'] ?? 0 }}</h3>
                    <p>Level 3 Escalations</p>
                    <small class="text-white-50">24+ Hours Overdue</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Overdue by State -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fe fe-alert-circle mr-2"></i> Overdue Tasks by Workflow State
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $overdueByState = $slaStats['overdue_by_state'] ?? [];
                    @endphp
                    @if(!empty($overdueByState))
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead style="background: #f9fafb;">
                                    <tr>
                                        <th>Workflow State</th>
                                        <th class="text-right">Overdue Count</th>
                                        <th class="text-right">Percentage</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalOverdue = array_sum($overdueByState);
                                    @endphp
                                    @foreach($overdueByState as $state => $count)
                                        <tr>
                                            <td>
                                                <strong>
                                                    @php
                                                        $stateEnum = \App\Enums\TaskState::tryFrom($state);
                                                        echo $stateEnum ? $stateEnum->label() : ucfirst(str_replace('_', ' ', $state));
                                                    @endphp
                                                </strong>
                                            </td>
                                            <td class="text-right"><strong>{{ $count }}</strong></td>
                                            <td class="text-right">
                                                {{ $totalOverdue > 0 ? number_format(($count / $totalOverdue) * 100, 1) : 0 }}%
                                            </td>
                                            <td>
                                                @if($count > 5)
                                                    <span class="badge badge-danger">High</span>
                                                @elseif($count > 2)
                                                    <span class="badge badge-warning">Medium</span>
                                                @else
                                                    <span class="badge badge-success">Low</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fe fe-check-circle" style="font-size: 3rem; opacity: 0.2; color: #10b981;"></i>
                            <p class="text-muted mt-2">No overdue tasks - great job!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Escalation Summary -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fe fe-trending-up mr-2"></i> Escalation Breakdown
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Level 1 (Initial Notification)</span>
                            <strong>{{ $escalationStats['level_1'] ?? 0 }}</strong>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-info" style="width: {{ ($escalationStats['level_1'] ?? 0) > 0 ? min(100, (($escalationStats['level_1'] ?? 0) / max(($escalationStats['level_1'] ?? 0) + ($escalationStats['level_2'] ?? 0) + ($escalationStats['level_3'] ?? 0), 1)) * 100) : 0 }}%"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Level 2 (Supervisor)</span>
                            <strong>{{ $escalationStats['level_2'] ?? 0 }}</strong>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-warning" style="width: {{ ($escalationStats['level_2'] ?? 0) > 0 ? min(100, (($escalationStats['level_2'] ?? 0) / max(($escalationStats['level_1'] ?? 0) + ($escalationStats['level_2'] ?? 0) + ($escalationStats['level_3'] ?? 0), 1)) * 100) : 0 }}%"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Level 3 (Management)</span>
                            <strong class="text-danger">{{ $escalationStats['level_3'] ?? 0 }}</strong>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-danger" style="width: {{ ($escalationStats['level_3'] ?? 0) > 0 ? min(100, (($escalationStats['level_3'] ?? 0) / max(($escalationStats['level_1'] ?? 0) + ($escalationStats['level_2'] ?? 0) + ($escalationStats['level_3'] ?? 0), 1)) * 100) : 0 }}%"></div>
                        </div>
                    </div>

                    @if(($escalationStats['level_3'] ?? 0) > 0)
                        <div class="alert alert-danger mt-3 mb-0">
                            <i class="fe fe-alert-triangle mr-2"></i>
                            <strong>{{ $escalationStats['level_3'] }} critical escalations</strong> require immediate attention
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Most Critical Overdue Tasks -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fe fe-zap mr-2"></i> Most Critical Overdue Tasks
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if(!empty($criticalTasks))
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead style="background: #f9fafb;">
                                    <tr>
                                        <th>Task ID</th>
                                        <th>Well / Device</th>
                                        <th>Current State</th>
                                        <th>Expected Due</th>
                                        <th>Overdue Duration</th>
                                        <th>Escalation Level</th>
                                        <th>Responsible Person</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($criticalTasks as $task)
                                        <tr class="border-left border-{{ $task->overdue_flag ? 'danger' : 'warning' }}">
                                            <td><strong>{{ $task->sno }}</strong></td>
                                            <td>{{ $task->well_full_name ?? $task->mpfm_type ?? '-' }}</td>
                                            <td>
                                                <span class="badge badge-{{ $task->state?->value === 'completed' ? 'success' : 'primary' }}">
                                                    {{ $task->state?->label() ?? 'Unknown' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($task->expected_completion_at)
                                                    {{ $task->expected_completion_at->format('d M Y H:i') }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($task->overdue_flag && $task->expected_completion_at)
                                                    <strong class="text-danger">{{ $task->getOverdueDurationFormatted() }}</strong>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $slaHistory = $task->slaHistory;
                                                    $escalationLevel = $slaHistory?->escalation_level ?? 0;
                                                @endphp
                                                <span class="badge badge-{{ $escalationLevel >= 3 ? 'danger' : ($escalationLevel >= 2 ? 'warning' : 'info') }}">
                                                    Level {{ $escalationLevel }}
                                                </span>
                                            </td>
                                            <td>
                                                @php
                                                    $responsible = null;
                                                    if ($task->state === \App\Enums\TaskState::IN_PROGRESS) {
                                                        $responsible = $task->getPrimaryOperator()?->name ?? 'Unassigned';
                                                    } elseif ($task->state === \App\Enums\TaskState::SUBMITTED_TO_SURVEILLANCE) {
                                                        $responsible = \App\Models\User::role('surveillance_supervisor')->first()?->name ?? 'Unassigned';
                                                    }
                                                @endphp
                                                <small>{{ $responsible ?? 'TBD' }}</small>
                                            </td>
                                            <td>
                                                <a href="{{  }}" class="btn btn-sm btn-info" title="View Task">
                                                    <i class="fe fe-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fe fe-check-circle" style="font-size: 4rem; opacity: 0.2; color: #10b981;"></i>
                            <p class="text-muted mt-3">No critical tasks - excellent compliance!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('js')
<script>
    // Auto-refresh every 5 minutes
    setTimeout(function() {
        location.reload();
    }, 5 * 60 * 1000);
</script>
@endsection
