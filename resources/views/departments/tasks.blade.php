@extends('layouts.master')

@section('title', $department->name . ' - Tasks')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title mb-2">{{ $department->name }} Tasks</h1>
                <p class="text-muted mb-0">
                    Total: <strong>{{ $tasks->total() }}</strong> tasks
                </p>
            </div>
            <div class="btn-group">
                <a href="{{ route('departments.dashboard', $department->id) }}" class="btn btn-secondary">
                    <i class="fe fe-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4" style="border-left: 5px solid #3498db;">
        <div class="card-header" style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); color: white;">
            <h5 class="card-title mb-0">
                <i class="fe fe-filter"></i> Filters
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Well name or number"
                           value="{{ request('search') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="">All Status</option>
                        @foreach($statuses as $status)
                        <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                            {{ ucfirst($status) }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Priority</label>
                    <select name="priority" class="form-control">
                        <option value="">All Priorities</option>
                        @foreach($priorities as $priority)
                        <option value="{{ $priority }}" {{ request('priority') === $priority ? 'selected' : '' }}>
                            {{ ucfirst($priority) }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fe fe-search"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tasks Table -->
    <div class="card" style="border-left: 5px solid #2980b9;">
        <div class="card-header" style="background: linear-gradient(135deg, #2980b9 0%, #1a5276 100%); color: white;">
            <h5 class="card-title mb-0">
                <i class="fe fe-list"></i> All Tasks
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Sequence</th>
                            <th>Well Name</th>
                            <th>Monthly Plan</th>
                            <th>Status</th>
                            <th>State</th>
                            <th>Priority</th>
                            <th>Operator</th>
                            <th>Created</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tasks as $task)
                        <tr>
                            <td>
                                <strong>#{{ $task->sno ?? 'N/A' }}</strong>
                            </td>
                            <td>
                                <strong>{{ $task->well_full_name }}</strong><br>
                                <small class="text-muted">Well: {{ $task->well_number }}, Tower: {{ $task->tower ?? 'N/A' }}</small>
                            </td>
                            <td>
                                <small>{{ $task->monthlyPlan->plan_name ?? 'N/A' }}</small>
                            </td>
                            <td>
                                @if($task->completion_status === 'completed')
                                    <span class="badge badge-success">Completed</span>
                                @elseif($task->completion_status === 'in_progress')
                                    <span class="badge badge-warning">In Progress</span>
                                @elseif($task->completion_status === 'failed')
                                    <span class="badge badge-danger">Failed</span>
                                @else
                                    <span class="badge badge-secondary">Pending</span>
                                @endif
                            </td>
                            <td>
                                <small>{{ $task->state?->label() ?? 'Draft' }}</small>
                            </td>
                            <td>
                                @if($task->priority === 'top')
                                    <span class="badge badge-danger">Top</span>
                                @elseif($task->priority === 'high')
                                    <span class="badge badge-warning">High</span>
                                @else
                                    <span class="badge badge-info">Normal</span>
                                @endif
                            </td>
                            <td>
                                @if($task->primaryOperator)
                                    <small>{{ $task->primaryOperator->name }}</small>
                                @elseif($task->operators->count() > 0)
                                    <small>{{ $task->operators->first()->name }}</small>
                                @else
                                    <small class="text-muted">Unassigned</small>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ $task->created_at->format('M d, Y') }}</small>
                            </td>
                            <td>
                                <a href="{{ route('monthly-plans.tasks.show', [$task->monthlyPlan, $task]) }}"
                                   class="btn btn-sm btn-outline-primary" title="View Details">
                                    <i class="fe fe-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-5">
                                <i class="fe fe-inbox" style="font-size: 32px; opacity: 0.3;"></i><br>
                                No tasks found for this department
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="row mt-4">
        <div class="col-md-12">
            {{ $tasks->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<style>
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
</style>
@endsection
