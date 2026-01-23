{{-- SLA History Timeline Component --}}
@props(['task'])

@php
    $slaHistory = $task->slaHistory;
@endphp

@if($slaHistory)
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fe fe-book mr-2"></i> SLA Audit Trail
        </h5>
    </div>
    <div class="card-body">
        <div class="timeline">
            <!-- SLA Created -->
            <div class="timeline-item">
                <div class="timeline-marker bg-info"></div>
                <div class="timeline-content">
                    <h6 class="font-weight-bold">SLA Set</h6>
                    <p class="text-muted mb-1">
                        <small>{{ $slaHistory->created_at->format('d M Y, h:i A') }}</small>
                    </p>
                    <p class="mb-0">
                        <small>
                            Duration: <strong>{{ $slaHistory->sla_duration_hours }} hours</strong><br>
                            Expected: <strong>{{ $slaHistory->expected_completion_at->format('d M Y H:i') }}</strong>
                        </small>
                    </p>
                </div>
            </div>

            <!-- Overdue Detected -->
            @if($slaHistory->overdue_detected_at)
                <div class="timeline-item">
                    <div class="timeline-marker bg-warning"></div>
                    <div class="timeline-content">
                        <h6 class="font-weight-bold text-warning">Overdue Detected</h6>
                        <p class="text-muted mb-1">
                            <small>{{ $slaHistory->overdue_detected_at->format('d M Y, h:i A') }}</small>
                        </p>
                        <p class="mb-0">
                            <small>
                                @if($slaHistory->metadata && isset($slaHistory->metadata['hours_overdue']))
                                    Overdue by: <strong>{{ $slaHistory->metadata['hours_overdue'] }} hours</strong>
                                @endif
                            </small>
                        </p>
                    </div>
                </div>
            @endif

            <!-- Notification Sent -->
            @if($slaHistory->notification_sent_at)
                <div class="timeline-item">
                    <div class="timeline-marker bg-primary"></div>
                    <div class="timeline-content">
                        <h6 class="font-weight-bold">Notification Sent</h6>
                        <p class="text-muted mb-1">
                            <small>{{ $slaHistory->notification_sent_at->format('d M Y, h:i A') }}</small>
                        </p>
                        <p class="mb-0">
                            <small>
                                Sent to: <strong>{{ $slaHistory->notified_to_role ?? 'User' }}</strong>
                            </small>
                        </p>
                    </div>
                </div>
            @endif

            <!-- Escalation -->
            @if($slaHistory->escalation_level > 0)
                <div class="timeline-item">
                    <div class="timeline-marker {{ $slaHistory->escalation_level >= 3 ? 'bg-danger' : 'bg-warning' }}"></div>
                    <div class="timeline-content">
                        <h6 class="font-weight-bold">{{ $slaHistory->getEscalationLabel() }}</h6>
                        <p class="text-muted mb-0">
                            <small>Level {{ $slaHistory->escalation_level }} - Task requires immediate attention</small>
                        </p>
                    </div>
                </div>
            @endif

            <!-- Resolved -->
            @if($slaHistory->resolution_at)
                <div class="timeline-item">
                    <div class="timeline-marker bg-success"></div>
                    <div class="timeline-content">
                        <h6 class="font-weight-bold text-success">Resolved</h6>
                        <p class="text-muted mb-1">
                            <small>{{ $slaHistory->resolution_at->format('d M Y, h:i A') }}</small>
                        </p>
                        <p class="mb-0">
                            <small>Task completed successfully</small>
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@else
<div class="alert alert-info">
    <i class="fe fe-info mr-2"></i> No SLA history available yet
</div>
@endif

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    padding-bottom: 20px;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: -20px;
    top: 30px;
    width: 2px;
    height: calc(100% - 30px);
    background: #e3e6f0;
}

.timeline-marker {
    position: absolute;
    left: -29px;
    top: 0;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    border: 3px solid white;
}

.timeline-content {
    background: #f8f9fa;
    padding: 12px;
    border-radius: 4px;
    border-left: 3px solid #e3e6f0;
}

.timeline-item:hover .timeline-content {
    background: #f0f1f3;
}
</style>
