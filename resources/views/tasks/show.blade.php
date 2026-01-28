@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>{{ $task->title }}</h2>
            <p class="text-muted">
                <small>Created {{ $task->created_at->diffForHumans() }} by {{ $task->createdBy->name }}</small>
            </p>
        </div>
        <div class="col-md-4 text-end">
            @can('update', $task)
                <a href="{{ route('tasks.edit', $task) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Edit
                </a>
            @endcan
            @can('delete', $task)
                <form action="{{ route('tasks.destroy', $task) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger"
                            onclick="return confirm('Are you sure you want to delete this task?')">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </form>
            @endcan
            <a href="{{ route('tasks.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <!-- Task Details Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Task Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Description</label>
                            <p>{{ $task->description ?? 'No description provided' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Notes</label>
                            <p>{{ $task->notes ?? 'No notes' }}</p>
                        </div>
                    </div>
                    <hr />
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Task Type</label>
                            <p>
                                <span class="badge bg-primary">{{ $task->task_type }}</span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Current State</label>
                            <p>
                                <span class="badge" style="background-color: {{ $task->state->color() }}; padding: 8px 12px;">
                                    {{ $task->state->label() }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assignment Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Assignment Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Assigned To</label>
                            @if ($task->assignedTo)
                                <p>{{ $task->assignedTo->name }}</p>
                                <small class="text-muted">({{ $task->assignedTo->email }})</small>
                            @else
                                <p class="text-muted">Unassigned</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Department</label>
                            <p>{{ $task->department->name }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Related Asset</label>
                            @if ($task->oilString)
                                <p>Oil String: {{ $task->oilString->name }}</p>
                            @elseif ($task->tower)
                                <p>Tower: {{ $task->tower->name }}</p>
                            @else
                                <p class="text-muted">No asset assigned</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Monthly Plan</label>
                            <p>
                                <a href="{{ route('monthly-plans.show', $task->monthlyPlan) }}">
                                    {{ $task->monthlyPlan->name }}
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timelines Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Timeline</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Expected Completion</label>
                            @if ($task->expected_completion_at)
                                <p>
                                    {{ $task->expected_completion_at->format('M d, Y @ H:i') }}
                                    <br />
                                    <small class="text-muted">({{ $task->getDaysUntilDue() }} days from now)</small>
                                </p>
                            @else
                                <p class="text-muted">Not set</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Actual Completion</label>
                            @if ($task->actual_completion_at)
                                <p>{{ $task->actual_completion_at->format('M d, Y @ H:i') }}</p>
                            @else
                                <p class="text-muted">Not completed yet</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- State History Card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">State History</h5>
                </div>
                <div class="card-body">
                    @if ($task->stateHistory->isNotEmpty())
                        <div class="timeline">
                            @foreach ($task->stateHistory->reverse() as $history)
                                <div class="timeline-item mb-3">
                                    <div class="timeline-marker">
                                        <span class="badge bg-success">✓</span>
                                    </div>
                                    <div class="timeline-content">
                                        <p class="mb-1">
                                            <strong>{{ $history->from_state->label() }}</strong>
                                            <i class="fas fa-arrow-right"></i>
                                            <strong>{{ $history->to_state->label() }}</strong>
                                        </p>
                                        <small class="text-muted">
                                            By {{ $history->changedBy->name }} ({{ $history->changedBy->roles()->first()?->name }})
                                            on {{ $history->changed_at->format('M d, Y @ H:i') }}
                                        </small>
                                        @if ($history->notes)
                                            <p class="mt-2 mb-0">
                                                <em>{{ $history->notes }}</em>
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">No state history recorded yet.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Status Card -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Status</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted">Progress</label>
                        <div class="progress" style="height: 30px;">
                            <div class="progress-bar" role="progressbar"
                                 style="width: {{ $task->getProgressPercentage() }}%"
                                 aria-valuenow="{{ $task->getProgressPercentage() }}"
                                 aria-valuemin="0" aria-valuemax="100">
                                <span>{{ $task->getProgressPercentage() }}%</span>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Priority</label>
                        <p>
                            <span class="badge bg-{{ $task->getPriorityColor() }}">
                                {{ ucfirst($task->priority) }}
                            </span>
                        </p>
                    </div>
                    @if ($task->isOverdue())
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Overdue!</strong> This task is {{ $task->getDaysOverdue() }} days overdue.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions Card -->
            @if ($task->state !== TaskState::COMPLETED && Auth::check())
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Actions</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('tasks.transition', $task) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="newState" class="form-label">Transition To</label>
                                <select name="new_state" id="newState" class="form-select">
                                    <option value="">Select new state...</option>
                                    @foreach ($task->getAvailableTransitions() as $availableState)
                                        <option value="{{ $availableState->value }}">
                                            {{ $availableState->label() }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="notes" class="form-label">Notes (Optional)</label>
                                <textarea name="notes" id="notes" class="form-control" rows="3"
                                          placeholder="Add notes about this transition..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-check"></i> Update State
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding: 20px 0;
}

.timeline-item {
    display: flex;
    margin-bottom: 30px;
    position: relative;
}

.timeline-marker {
    width: 40px;
    height: 40px;
    background: #fff;
    border: 2px solid #0d6efd;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 20px;
    flex-shrink: 0;
}

.timeline-content {
    flex: 1;
    padding: 10px 0;
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form[action*="transition"]');
    if (form) {
        form.addEventListener('submit', function (e) {
            const newState = document.getElementById('newState').value;
            if (!newState) {
                e.preventDefault();
                alert('Please select a new state');
            }
        });
    }
});
</script>
@endpush
@endsection
