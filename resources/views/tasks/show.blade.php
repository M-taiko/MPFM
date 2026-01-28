@extends('layouts.master')

@section('content')
<style>
    .modern-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        border-radius: 12px;
        margin-bottom: 2rem;
        box-shadow: 0 8px 32px rgba(102, 126, 234, 0.2);
    }

    .modern-header h1 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .task-badge {
        display: inline-block;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .status-badge {
        padding: 0.75rem 1.5rem !important;
        border-radius: 20px !important;
        font-weight: 600 !important;
        font-size: 0.9rem !important;
    }

    .info-card {
        background: white;
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .info-card:hover {
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        transform: translateY(-2px);
    }

    .info-card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.5rem;
        border: none;
    }

    .info-card-header h5 {
        color: white;
        margin: 0;
        font-weight: 600;
    }

    .info-section {
        padding: 1.5rem;
        border-bottom: 1px solid #f0f0f0;
    }

    .info-section:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 600;
        color: #667eea;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    .info-value {
        font-size: 1rem;
        color: #333;
        font-weight: 500;
    }

    .info-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
    }

    .action-buttons {
        display: flex;
        gap: 0.75rem;
        margin-top: 1rem;
    }

    .btn-modern {
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        border: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-primary-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .btn-primary-gradient:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        color: white;
    }

    .transitions-container {
        margin-top: 1.5rem;
        padding: 1.5rem;
        background: #f8f9fa;
        border-radius: 8px;
    }

    .timeline {
        margin-top: 1.5rem;
    }

    .timeline-item {
        display: flex;
        margin-bottom: 1.5rem;
    }

    .timeline-marker {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        flex-shrink: 0;
        margin-right: 1rem;
    }

    .timeline-content {
        flex: 1;
    }

    .timeline-title {
        font-weight: 600;
        color: #333;
        margin-bottom: 0.25rem;
    }

    .timeline-date {
        font-size: 0.85rem;
        color: #999;
    }

    .priority-low { background-color: #d4edda; color: #155724; }
    .priority-medium { background-color: #fff3cd; color: #856404; }
    .priority-high { background-color: #f8d7da; color: #721c24; }
    .priority-critical { background-color: #721c24; color: white; }
</style>

<div class="container-fluid">
    <!-- Modern Header -->
    <div class="modern-header">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h1><i class="fe fe-check-square"></i> {{ $task->title }}</h1>
                <p class="mb-2" style="opacity: 0.9;">
                    <i class="fe fe-clock"></i> Created {{ $task->created_at->diffForHumans() }} by {{ $task->createdBy->name }}
                </p>
                <div>
                    <span class="task-badge priority-{{ $task->priority }}" style="background-color: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); color: white;">
                        <i class="fe fe-flag"></i> {{ ucfirst($task->priority) }}
                    </span>
                    <span class="task-badge status-badge" style="background-color: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); color: white;">
                        {{ $task->state->label() }}
                    </span>
                </div>
            </div>
            <div class="action-buttons">
                @can('update', $task)
                    <a href="{{ route('tasks.edit', $task) }}" class="btn btn-modern btn-warning">
                        <i class="fe fe-edit-3"></i> Edit
                    </a>
                @endcan
                @if ($task->department && $task->department->code === 'MPFM' && $task->isAssignedTo(Auth::id()))
                    <a href="{{ route('upload.form') }}?task_id={{ $task->id }}" class="btn btn-modern" style="background: rgba(76,209,55,0.9); color: white; border: 1px solid rgba(76,209,55,0.3);">
                        <i class="fe fe-upload"></i> Upload Test Results
                    </a>
                @endif
                <a href="{{ route('tasks.index') }}" class="btn btn-modern" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3);">
                    <i class="fe fe-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Task Details Card -->
            <div class="info-card mb-4">
                <div class="info-card-header">
                    <h5><i class="fe fe-file-text"></i> Task Details</h5>
                </div>
                <div class="info-section">
                    <div class="info-label">Description</div>
                    <div class="info-value">
                        {{ $task->description ?? 'No description provided' }}
                    </div>
                </div>
                @if ($task->notes)
                <div class="info-section">
                    <div class="info-label">Notes</div>
                    <div class="info-value">
                        {{ $task->notes }}
                    </div>
                </div>
                @endif
            </div>

            <!-- Assignment Information -->
            <div class="info-card mb-4">
                <div class="info-card-header">
                    <h5><i class="fe fe-users"></i> Assignment & Location</h5>
                </div>
                <div class="info-section">
                    <div class="info-row">
                        <div>
                            <div class="info-label">Department</div>
                            <div class="info-value">
                                <i class="fe fe-briefcase"></i> {{ $task->department->name }}
                            </div>
                        </div>
                        <div>
                            <div class="info-label">Assigned To</div>
                            <div class="info-value">
                                @if ($task->assignedTo)
                                    <i class="fe fe-user"></i> {{ $task->assignedTo->name }}
                                @else
                                    <span class="text-muted">Unassigned</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="info-section">
                    <div class="info-row">
                        <div>
                            <div class="info-label">Tower</div>
                            <div class="info-value">
                                @if ($task->tower)
                                    <i class="fe fe-map-pin"></i> {{ $task->tower->name }}
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <div class="info-label">Oil String</div>
                            <div class="info-value">
                                @if ($task->oilString)
                                    <i class="fe fe-layers"></i> {{ $task->oilString->name }}
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timeline/History Card -->
            @if ($task->stateHistories && $task->stateHistories->count() > 0)
            <div class="info-card mb-4">
                <div class="info-card-header">
                    <h5><i class="fe fe-activity"></i> State History</h5>
                </div>
                <div class="info-section">
                    <div class="timeline">
                        @foreach ($task->stateHistories->reverse() as $history)
                        <div class="timeline-item">
                            <div class="timeline-marker">
                                <i class="fe fe-arrow-right"></i>
                            </div>
                            <div class="timeline-content">
                                <div class="timeline-title">
                                    {{ $history->from_state }} → {{ $history->to_state }}
                                </div>
                                <div class="timeline-date">
                                    {{ $history->created_at->format('M d, Y H:i') }}
                                    @if ($history->changed_by)
                                        by {{ $history->changed_by }}
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- State Transition Card -->
            @if ($task->state->value !== \App\Enums\TaskState::COMPLETED->value && Auth::check())
            <div class="info-card mb-4">
                <div class="info-card-header">
                    <h5><i class="fe fe-shuffle"></i> State Transition</h5>
                </div>
                <div class="info-section">
                    @if ($availableTransitions && count($availableTransitions) > 0)
                        <form action="{{ route('tasks.transition', $task) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="newState" class="info-label">Select New State</label>
                                <select name="new_state" id="newState" class="form-select" style="border-color: #667eea;">
                                    <option value="">-- Choose a state --</option>
                                    @foreach ($availableTransitions as $state)
                                        <option value="{{ $state->value }}">
                                            {{ $state->label() }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-modern btn-primary-gradient">
                                <i class="fe fe-check"></i> Transition
                            </button>
                        </form>
                    @else
                        <p class="text-muted">
                            <i class="fe fe-info"></i> No transitions available for this task state.
                        </p>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Summary Card -->
            <div class="info-card mb-4">
                <div class="info-card-header">
                    <h5><i class="fe fe-info"></i> Summary</h5>
                </div>
                <div class="info-section">
                    <div class="info-label">Status</div>
                    <div class="info-value mb-3">
                        <span class="status-badge" style="background-color: {{ $task->state->color() }}; color: white;">
                            {{ $task->state->label() }}
                        </span>
                    </div>

                    <div class="info-label">Priority</div>
                    <div class="info-value mb-3">
                        <span class="badge priority-{{ $task->priority }}">
                            {{ ucfirst($task->priority) }}
                        </span>
                    </div>

                    <div class="info-label">Task Type</div>
                    <div class="info-value mb-3">
                        <span class="badge bg-secondary">{{ $task->task_type }}</span>
                    </div>

                    <div class="info-label">Monthly Plan</div>
                    <div class="info-value">
                        @if ($task->monthlyPlan)
                            {{ $task->monthlyPlan->plan_name ?? 'Plan' }}
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Dates Card -->
            <div class="info-card mb-4">
                <div class="info-card-header">
                    <h5><i class="fe fe-calendar"></i> Dates</h5>
                </div>
                <div class="info-section">
                    <div class="info-label">Created</div>
                    <div class="info-value mb-3">
                        {{ $task->created_at->format('M d, Y H:i') }}
                    </div>

                    <div class="info-label">Expected Completion</div>
                    <div class="info-value mb-3">
                        @if ($task->expected_completion_at)
                            {{ $task->expected_completion_at->format('M d, Y H:i') }}
                        @else
                            <span class="text-muted">Not set</span>
                        @endif
                    </div>

                    @if ($task->actual_completion_at)
                    <div class="info-label">Completed</div>
                    <div class="info-value">
                        {{ $task->actual_completion_at->format('M d, Y H:i') }}
                    </div>
                    @endif
                </div>
            </div>

            <!-- Assigned Operators Card -->
            @if ($task->operators && $task->operators->count() > 0)
            <div class="info-card">
                <div class="info-card-header">
                    <h5><i class="fe fe-users"></i> Assigned Operators</h5>
                </div>
                <div class="info-section">
                    @foreach ($task->operators as $operator)
                    <div class="mb-2">
                        <span class="badge bg-info">
                            <i class="fe fe-user"></i> {{ $operator->name }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Add any JavaScript functionality here
    document.querySelectorAll('.info-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
</script>
@endpush
