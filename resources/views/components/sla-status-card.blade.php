{{-- SLA Status Card Component for Task Details --}}
@props(['task'])

@php
    $isOverdue = $task->overdue_flag ?? false;
    $hoursRemaining = $task->getHoursUntilSLAExpiry();
    $expectedAt = $task->expected_completion_at;
    $slaHistory = $task->slaHistory;

    $statusColor = match(true) {
        $isOverdue => 'danger',
        $hoursRemaining === null => 'secondary',
        $hoursRemaining < 4 => 'warning',
        $hoursRemaining < 24 => 'info',
        default => 'success'
    };

    $statusIcon = match(true) {
        $isOverdue => 'fe-alert-circle',
        $hoursRemaining === null => 'fe-clock',
        $hoursRemaining < 4 => 'fe-alert-triangle',
        $hoursRemaining < 24 => 'fe-clock',
        default => 'fe-check-circle'
    };

    $statusText = match(true) {
        $isOverdue => 'OVERDUE',
        $hoursRemaining === null => 'No SLA Set',
        $hoursRemaining < 1 => 'Critical - Less than 1 hour',
        $hoursRemaining < 4 => 'Warning - ' . intval($hoursRemaining) . ' hours remaining',
        $hoursRemaining < 24 => intval($hoursRemaining / 24) . ' days ' . intval($hoursRemaining % 24) . ' hours remaining',
        default => 'On Track'
    };
@endphp

<div class="card border-{{ $statusColor }}">
    <div class="card-header bg-{{ $statusColor }} text-white">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0">
                <i class="fe {{ $statusIcon }} mr-2"></i> SLA Status
            </h5>
            <span class="badge badge-light">{{ $statusText }}</span>
        </div>
    </div>
    <div class="card-body">
        @if($expectedAt)
            <div class="row mb-3">
                <div class="col-md-6">
                    <small class="text-muted">Expected Completion</small>
                    <p class="font-weight-bold">{{ $expectedAt->format('d M Y, h:i A') }}</p>
                </div>
                <div class="col-md-6">
                    @if($hoursRemaining !== null)
                        <small class="text-muted">Time {{ $isOverdue ? 'Overdue' : 'Remaining' }}</small>
                        <p class="font-weight-bold text-{{ $statusColor }}">
                            @if($isOverdue)
                                {{ $task->getOverdueDurationFormatted() }}
                            @else
                                {{ intval($hoursRemaining / 24) }}d {{ intval($hoursRemaining % 24) }}h
                            @endif
                        </p>
                    @endif
                </div>
            </div>

            @if($slaHistory)
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <small class="text-muted">SLA Duration</small>
                        <p class="font-weight-bold">{{ $slaHistory->sla_duration_hours }} hours</p>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">Escalation Level</small>
                        <p class="font-weight-bold">
                            <span class="badge badge-{{ $slaHistory->escalation_level >= 2 ? 'warning' : ($slaHistory->escalation_level >= 1 ? 'info' : 'success') }}">
                                {{ $slaHistory->getEscalationLabel() }}
                            </span>
                        </p>
                    </div>
                </div>

                @if($slaHistory->overdue_detected_at)
                    <hr>
                    <small class="text-muted d-block">Overdue Detected</small>
                    <small class="text-muted">{{ $slaHistory->overdue_detected_at->format('d M Y, h:i A') }}</small>
                @endif

                @if($slaHistory->notification_sent_at)
                    <div class="mt-2">
                        <small class="text-muted d-block">Last Notification</small>
                        <small class="text-muted">
                            {{ $slaHistory->notification_sent_at->format('d M Y, h:i A') }}
                            @if($slaHistory->notified_to_role)
                                (to {{ $slaHistory->notified_to_role }})
                            @endif
                        </small>
                    </div>
                @endif
            @endif
        @else
            <div class="alert alert-secondary mb-0">
                <i class="fe fe-info mr-2"></i> No SLA deadline set for this task
            </div>
        @endif
    </div>
</div>
