@extends('layouts.master')

@section('title', 'Completed Tasks Archive')

@section('css')
<style>
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 8px;
        margin-bottom: 30px;
    }

    .page-header h1 {
        font-size: 2.2rem;
        font-weight: 600;
        margin: 0;
    }

    .filter-card {
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        margin-bottom: 25px;
    }

    .filter-card h5 {
        margin-bottom: 15px;
        color: #333;
        font-weight: 600;
    }

    .task-item {
        background: white;
        border: 1px solid #e8ecf1;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .task-item:hover {
        border-color: #667eea;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.1);
    }

    .task-info {
        flex: 1;
    }

    .task-identifier {
        font-weight: 600;
        font-size: 1.1rem;
        color: #333;
        margin-bottom: 8px;
    }

    .task-meta {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        font-size: 0.9rem;
        color: #666;
    }

    .meta-item {
        display: flex;
        flex-direction: column;
    }

    .meta-label {
        font-weight: 600;
        color: #667eea;
        font-size: 0.85rem;
        text-transform: uppercase;
        margin-bottom: 4px;
    }

    .status-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .status-badge.completed {
        background: #d4edda;
        color: #155724;
    }

    .status-badge.failed {
        background: #f8d7da;
        color: #721c24;
    }

    .task-actions {
        margin-left: 15px;
        white-space: nowrap;
    }

    .task-actions a {
        padding: 8px 16px;
        background: #667eea;
        color: white;
        border-radius: 6px;
        text-decoration: none;
        transition: all 0.2s;
        font-size: 0.9rem;
    }

    .task-actions a:hover {
        background: #764ba2;
    }

    @media (max-width: 768px) {
        .task-item {
            flex-direction: column;
            align-items: flex-start;
        }

        .task-actions {
            margin-left: 0;
            margin-top: 15px;
        }

        .task-meta {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1><i class="fe fe-archive mr-2"></i>Completed Tasks Archive</h1>
    </div>

    <!-- Filters Section -->
    <div class="filter-card">
        <h5><i class="fe fe-filter mr-2"></i>Filter Tasks</h5>
        <form method="GET" action="{{ route('task-archive.index') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Tower</label>
                <select name="tower_id" class="form-select">
                    <option value="">All Towers</option>
                    @foreach($towers as $tower)
                    <option value="{{ $tower->id }}" {{ request('tower_id') == $tower->id ? 'selected' : '' }}>
                        {{ $tower->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">From</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>

            <div class="col-md-2">
                <label class="form-label">To</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>

            <div class="col-md-2" style="display: flex; align-items: flex-end;">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fe fe-search mr-2"></i>Search
                </button>
            </div>

            @if(request()->hasAny(['tower_id', 'status', 'date_from', 'date_to']))
            <div class="col-md-12">
                <a href="{{ route('task-archive.index') }}" class="btn btn-outline-secondary">
                    <i class="fe fe-x mr-2"></i>Clear Filters
                </a>
            </div>
            @endif
        </form>
    </div>

    <!-- Tasks List -->
    <div>
        @forelse($tasks as $task)
        <div class="task-item">
            <div class="task-info">
                <div class="task-identifier">
                    <i class="fe fe-check-circle" style="color: #28a745; margin-right: 8px;"></i>
                    {{ $task->well_full_name ?? $task->sno ?? "Task #{$task->id}" }}
                </div>
                <div class="task-meta">
                    <div class="meta-item">
                        <span class="meta-label">Tower</span>
                        <span>{{ $task->towerModel?->name ?? 'N/A' }}</span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Oil Field</span>
                        <span>{{ $task->towerModel?->oilField?->name ?? 'N/A' }}</span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">String</span>
                        <span>{{ $task->oilString?->name ?? 'N/A' }}</span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Completed Date</span>
                        <span>{{ $task->completed_at?->format('M d, Y H:i') ?? 'N/A' }}</span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Operator</span>
                        <span>{{ $task->operator?->name ?? 'N/A' }}</span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Status</span>
                        <span class="status-badge {{ $task->completion_status }}">
                            {{ ucfirst($task->completion_status) }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="task-actions" style="display: flex; gap: 10px; flex-wrap: wrap;">
                <a href="{{ route('task-archive.show', $task) }}" style="background: #667eea;" title="View Task Details">
                    <i class="fe fe-eye mr-2"></i>Details
                </a>
                @if($task->mpfmTest)
                    @if($task->mpfmTest->test_data)
                    <a href="{{ route('mpfm-tests.view', $task->mpfmTest->id) }}" target="_blank" style="background: #28a745;" title="View Charts">
                        <i class="fe fe-bar-chart-2 mr-2"></i>Charts
                    </a>
                    @endif
                    <!-- Validation & History -->
                    <a href="{{ route('mpfm_tests.show_history', $task->mpfmTest->id) }}" style="background: #ff9800;" title="View Validation & History">
                        <i class="fe fe-check-circle mr-2"></i>Validation
                    </a>
                    <!-- String Analysis -->
                    <a href="{{ route('mpfm_tests.string_analysis', $task->mpfmTest->oil_string_id) }}" style="background: #2196F3;" title="View String Analysis">
                        <i class="fe fe-trending-up mr-2"></i>Analysis
                    </a>
                @endif
            </div>
        </div>
        @empty
        <div class="alert alert-info" role="alert">
            <i class="fe fe-info mr-2"></i>
            No completed tasks found. Try adjusting your filters.
        </div>
        @endforelse

        <!-- Pagination -->
        @if($tasks->count() > 0)
        <div class="d-flex justify-content-center mt-4">
            {{ $tasks->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
