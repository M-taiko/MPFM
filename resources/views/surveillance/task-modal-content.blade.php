<style>
    /* Timeline Styles for Modal */
    .modal-timeline {
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
        width: 14px;
        height: 14px;
        border-radius: 50%;
        border: 2px solid #fff;
        box-shadow: 0 0 0 3px #ddd;
        margin-top: 2px;
        margin-right: 15px;
    }

    .timeline-item:not(:last-child)::before {
        content: '';
        position: absolute;
        left: 4px;
        top: 22px;
        bottom: -20px;
        width: 2px;
        background: #ddd;
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

    .task-info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-bottom: 20px;
    }

    .info-item {
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
    }

    .info-label {
        font-weight: 600;
        color: #666;
        font-size: 0.85rem;
        text-transform: uppercase;
    }

    .info-value {
        color: #333;
        margin-top: 3px;
    }

    .status-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .nav-tabs-modal {
        border-bottom: 2px solid #f0f0f0;
        margin-bottom: 15px;
    }

    .nav-link-modal {
        color: #666;
        border: none;
        padding: 8px 15px;
        font-size: 0.9rem;
        cursor: pointer;
        border-bottom: 3px solid transparent;
        margin-bottom: -2px;
    }

    .nav-link-modal.active {
        color: #667eea;
        border-bottom-color: #667eea;
    }

    .tab-pane-modal {
        display: none;
    }

    .tab-pane-modal.active {
        display: block;
    }
</style>

<div class="task-modal-container">
    <!-- Task Header -->
    <div style="margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #f0f0f0;">
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 10px;">
            <div>
                <h5 style="margin: 0;">{{ $task->well_full_name ?? $task->sno ?? "Task #{$task->id}" }}</h5>
                <p style="margin: 5px 0 0 0; color: #666; font-size: 0.9rem;">{{ $task->monthlyPlan->plan_name }}</p>
            </div>
            <span class="status-badge" style="background-color:
                @switch($task->completion_status)
                    @case('pending')
                        #ffc107
                    @break
                    @case('in_progress')
                        #17a2b8
                    @break
                    @case('completed')
                        #28a745
                    @break
                    @default
                        #6c757d
                @endswitch
            ; color: white;">
                {{ ucfirst($task->completion_status) }}
            </span>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="nav-tabs-modal">
        <button class="nav-link-modal active" data-tab="details">Details</button>
        <button class="nav-link-modal" data-tab="timeline">Timeline</button>
        @if($task->mpfmTest)
        <button class="nav-link-modal" data-tab="test">Test Results</button>
        @endif
    </div>

    <!-- Tab 1: Details -->
    <div id="tab-details" class="tab-pane-modal active">
        <div class="task-info-grid">
            <div class="info-item">
                <div class="info-label">Tower</div>
                <div class="info-value">{{ $task->towerModel?->name ?? 'N/A' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Well</div>
                <div class="info-value">{{ $task->well_number ?? 'N/A' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">String</div>
                <div class="info-value">{{ $task->oilString?->name ?? 'N/A' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Scheduled Date</div>
                <div class="info-value">{{ $task->scheduled_date?->format('d M Y') ?? 'N/A' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Priority</div>
                <div class="info-value">
                    <span class="status-badge" style="
                        @switch($task->priority)
                            @case('high')
                            @case('top')
                                background-color: #dc3545;
                            @break
                            @case('normal')
                                background-color: #17a2b8;
                            @break
                            @default
                                background-color: #6c757d;
                        @endswitch
                    ; color: white;">
                        {{ ucfirst($task->priority) }}
                    </span>
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">Assigned To</div>
                <div class="info-value">{{ $task->operator?->name ?? 'Unassigned' }}</div>
            </div>
        </div>

        @if($task->required_whfp)
        <div style="background: #e3f2fd; padding: 10px; border-radius: 4px; margin-bottom: 10px;">
            <strong>Expected WHFP:</strong> {{ number_format($task->required_whfp, 2) }} psi
        </div>
        @endif

        @if($task->production_rate)
        <div style="background: #f3e5f5; padding: 10px; border-radius: 4px;">
            <strong>Expected Production Rate:</strong> {{ number_format($task->production_rate, 2) }} STB/d
        </div>
        @endif
    </div>

    <!-- Tab 2: Timeline -->
    <div id="tab-timeline" class="tab-pane-modal">
        <div class="modal-timeline">
            <!-- Assignment -->
            <div class="timeline-item">
                <div class="timeline-marker bg-primary"></div>
                <div class="timeline-content">
                    <h6 class="mb-1">Assigned</h6>
                    <p class="text-muted mb-0">
                        @if($task->assigned_at)
                            {{ is_string($task->assigned_at) ? $task->assigned_at : $task->assigned_at->format('d M Y H:i') }}
                        @else
                            Pending
                        @endif
                    </p>
                </div>
            </div>

            <!-- State History -->
            @forelse($task->stateHistories as $history)
            <div class="timeline-item">
                <div class="timeline-marker" style="background-color: {{ $history->to_state?->color() ?? '#6c757d' }}"></div>
                <div class="timeline-content">
                    <h6 class="mb-1">
                        {{ str_replace('_', ' ', ucfirst($history->to_state)) }}
                    </h6>
                    <p class="text-muted mb-0">
                        {{ $history->created_at->format('d M Y H:i') }}
                        @if($history->changedBy)
                            <br><small>by {{ $history->changedBy->name }}</small>
                        @endif
                    </p>
                    @if($history->notes)
                        <p style="margin-top: 5px; font-size: 0.8rem;">{{ $history->notes }}</p>
                    @endif
                </div>
            </div>
            @empty
            <div class="timeline-item">
                <div class="timeline-marker bg-secondary"></div>
                <div class="timeline-content">
                    <p class="text-muted mb-0" style="font-size: 0.85rem;">No state history available</p>
                </div>
            </div>
            @endforelse

            <!-- Completion -->
            @if($task->completed_at)
            <div class="timeline-item">
                <div class="timeline-marker bg-success"></div>
                <div class="timeline-content">
                    <h6 class="mb-1">Completed</h6>
                    <p class="text-muted mb-0">
                        {{ $task->completed_at->format('d M Y H:i') }}
                    </p>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Tab 3: Test Results (if exists) -->
    @if($task->mpfmTest)
    <div id="tab-test" class="tab-pane-modal">
        <div class="task-info-grid">
            <div class="info-item">
                <div class="info-label">Test Date</div>
                <div class="info-value">{{ $task->mpfmTest->test_date?->format('d M Y') ?? 'N/A' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Test Status</div>
                <div class="info-value">
                    <span class="status-badge" style="
                        @switch($task->mpfmTest->status)
                            @case('approved')
                                background-color: #28a745;
                            @break
                            @case('rejected')
                                background-color: #dc3545;
                            @break
                            @case('submitted')
                                background-color: #17a2b8;
                            @break
                            @default
                                background-color: #6c757d;
                        @endswitch
                    ; color: white;">
                        {{ ucfirst($task->mpfmTest->status) }}
                    </span>
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">Oil Rate</div>
                <div class="info-value">{{ number_format($task->mpfmTest->oil_rate ?? 0, 2) }} STB/d</div>
            </div>
            <div class="info-item">
                <div class="info-label">Gas Rate</div>
                <div class="info-value">{{ number_format($task->mpfmTest->gas_rate ?? 0, 2) }} Mscf/d</div>
            </div>
            <div class="info-item">
                <div class="info-label">Water Cut</div>
                <div class="info-value">{{ number_format($task->mpfmTest->water_cut ?? 0, 2) }}%</div>
            </div>
            <div class="info-item">
                <div class="info-label">Pressure</div>
                <div class="info-value">{{ number_format($task->mpfmTest->pressure ?? 0, 2) }} psi</div>
            </div>
        </div>

        <div style="text-align: center; padding-top: 10px; display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
            @if($task->mpfmTest->test_data)
            <a href="{{ route('mpfm-tests.view', $task->mpfmTest->id) }}" class="btn btn-sm btn-success" target="_blank" title="View Charts">
                <i class="fe fe-bar-chart-2 mr-1"></i>Charts
            </a>
            @endif
            <a href="{{ route('mpfm_tests.show', $task->mpfmTest->id) }}" class="btn btn-sm btn-primary" title="View Full Test Report">
                <i class="fe fe-eye mr-1"></i>Details
            </a>
            <!-- Validation & History -->
            <a href="{{ route('mpfm_tests.show_history', $task->mpfmTest->id) }}" class="btn btn-sm btn-warning" title="View Validation & History">
                <i class="fe fe-check-circle mr-1"></i>Validation
            </a>
            <!-- String Analysis -->
            <a href="{{ route('mpfm_tests.string_analysis', $task->mpfmTest->oil_string_id) }}" class="btn btn-sm btn-info" title="View String Analysis">
                <i class="fe fe-trending-up mr-1"></i>Analysis
            </a>
        </div>
    </div>
    @endif
</div>

<script>
document.querySelectorAll('.nav-link-modal').forEach(button => {
    button.addEventListener('click', function() {
        const tab = this.getAttribute('data-tab');

        // Hide all tabs
        document.querySelectorAll('.tab-pane-modal').forEach(pane => {
            pane.classList.remove('active');
        });

        // Remove active class from all buttons
        document.querySelectorAll('.nav-link-modal').forEach(btn => {
            btn.classList.remove('active');
        });

        // Show selected tab
        document.getElementById('tab-' + tab).classList.add('active');
        this.classList.add('active');
    });
});
</script>
