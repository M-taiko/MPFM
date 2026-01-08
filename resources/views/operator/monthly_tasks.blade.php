@extends('layouts.master')

@section('content')
<div class="container-fluid mt-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">
                    <i class="fe fe-calendar me-2"></i>Monthly Tasks
                </h2>
                <a href="{{ route('operator.dashboard') }}" class="btn btn-outline-primary btn-sm">
                    <i class="fe fe-arrow-left me-2"></i>Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Status Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">TOTAL TASKS</p>
                            <h4 class="mb-0">{{ $statusCounts->sum() ?? 0 }}</h4>
                        </div>
                        <i class="fe fe-list text-primary" style="font-size: 2em;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">PENDING</p>
                            <h4 class="mb-0 text-warning">{{ $statusCounts->get('pending', 0) ?? 0 }}</h4>
                        </div>
                        <i class="fe fe-clock text-warning" style="font-size: 2em;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">IN PROGRESS</p>
                            <h4 class="mb-0 text-info">{{ $statusCounts->get('in_progress', 0) ?? 0 }}</h4>
                        </div>
                        <i class="fe fe-play-circle text-info" style="font-size: 2em;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">COMPLETED</p>
                            <h4 class="mb-0 text-success">{{ $statusCounts->get('completed', 0) ?? 0 }}</h4>
                        </div>
                        <i class="fe fe-check-circle text-success" style="font-size: 2em;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h6 class="mb-3 fw-bold">
                <i class="fe fe-filter me-2"></i>Filters
            </h6>
            <form method="GET" action="{{ route('operator.monthly-tasks') }}" id="filterForm">
                <div class="row align-items-end g-3">
                    <!-- Month Filter -->
                    <div class="col-md-3">
                        <label class="form-label fw-bold small">Month</label>
                        <select name="month" class="form-select" id="monthSelect">
                            <option value="">-- Select Month --</option>
                            @foreach($availableMonths as $m)
                                <option value="{{ $m['value'] }}" {{ $month == $m['value'] ? 'selected' : '' }}>
                                    {{ $m['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Year Filter -->
                    <div class="col-md-3">
                        <label class="form-label fw-bold small">Year</label>
                        <select name="year" class="form-select" id="yearSelect">
                            <option value="">-- Select Year --</option>
                            @foreach($availableYears as $y)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div class="col-md-3">
                        <label class="form-label fw-bold small">Status</label>
                        <select name="status" class="form-select" id="statusSelect">
                            <option value="">-- All Status --</option>
                            <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in_progress" {{ $status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ $status == 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>

                    <!-- Action Buttons -->
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="fe fe-search me-2"></i>Apply Filters
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tasks Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light border-bottom">
            <h6 class="mb-0 fw-bold">
                <i class="fe fe-list me-2"></i>Tasks
                <span class="badge badge-primary ms-2">{{ $tasks->total() }}</span>
            </h6>
        </div>
        <div class="card-body p-0">
            @if($tasks->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-muted small fw-bold">Well</th>
                                <th class="text-muted small fw-bold">Tower</th>
                                <th class="text-muted small fw-bold">String</th>
                                <th class="text-muted small fw-bold">Priority</th>
                                <th class="text-muted small fw-bold">Status</th>
                                <th class="text-muted small fw-bold">Date</th>
                                <th class="text-muted small fw-bold">Team</th>
                                <th class="text-muted small fw-bold">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tasks as $task)
                                <tr class="align-middle">
                                    <td>
                                        <strong>{{ $task->towerModel?->well_name ?? 'N/A' }}</strong>
                                    </td>
                                    <td>
                                        <span class="text-muted small">{{ $task->towerModel?->name ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-light text-dark">
                                            {{ $task->oilString?->string_identifier ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $task->priority === 'urgent' ? 'danger' : ($task->priority === 'high' ? 'warning' : ($task->priority === 'normal' ? 'info' : 'secondary')) }}">
                                            {{ ucfirst($task->priority ?? 'normal') }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($task->completion_status === 'pending')
                                            <span class="badge badge-warning">
                                                <i class="fe fe-clock me-1"></i>Pending
                                            </span>
                                        @elseif($task->completion_status === 'in_progress')
                                            <span class="badge badge-info">
                                                <i class="fe fe-play-circle me-1"></i>In Progress
                                            </span>
                                        @else
                                            <span class="badge badge-success">
                                                <i class="fe fe-check-circle me-1"></i>Completed
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $task->scheduled_date ? \Carbon\Carbon::parse($task->scheduled_date)->format('M d, Y') : 'N/A' }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="team-avatars" style="display: flex; gap: 2px;">
                                            @foreach($task->operators->take(3) as $operator)
                                                <span class="badge badge-sm badge-{{ $operator->pivot->is_primary ? 'primary' : 'secondary' }}" title="{{ $operator->name }}">
                                                    {{ substr($operator->name, 0, 1) }}
                                                </span>
                                            @endforeach
                                            @if($task->operators->count() > 3)
                                                <span class="badge badge-sm badge-light text-dark">+{{ $task->operators->count() - 3 }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($task->completion_status === 'pending')
                                            <a href="{{ route('operator.tasks.show', $task) }}" class="btn btn-sm btn-primary" title="Start Task">
                                                <i class="fe fe-play-circle"></i>
                                            </a>
                                        @elseif($task->completion_status === 'in_progress')
                                            <a href="{{ route('operator.tasks.upload', $task) }}" class="btn btn-sm btn-info" title="Upload Log">
                                                <i class="fe fe-upload-cloud"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('operator.tasks.show', $task) }}" class="btn btn-sm btn-outline-secondary" title="View Task">
                                                <i class="fe fe-eye"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="card-footer bg-light border-top">
                    {{ $tasks->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fe fe-inbox text-muted" style="font-size: 3em;"></i>
                    <p class="text-muted mt-3">
                        @if($month && $year)
                            No tasks found for {{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}
                        @else
                            No tasks found. Please select a month and year.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .team-avatars {
        display: flex;
        align-items: center;
    }

    .team-avatars .badge {
        font-size: 0.7rem;
        padding: 0.3rem 0.4rem;
        min-width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endsection
