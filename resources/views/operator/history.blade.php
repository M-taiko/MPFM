@extends('layouts.master')

@section('title', 'Task History')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div>
        <h1 class="page-title">Task History</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('operator.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">History</li>
        </ol>
    </div>
</div>

<!-- Filters -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('operator.history') }}" class="row align-items-center">
                    <div class="col-md-3">
                        <label class="form-label">Month</label>
                        <select name="month" class="form-control">
                            <option value="">All Months</option>
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Year</label>
                        <select name="year" class="form-control">
                            <option value="">All Years</option>
                            @foreach(range(date('Y') - 2, date('Y')) as $y)
                                <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary form-control">
                            <i class="fe fe-filter"></i> Filter
                        </button>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <a href="{{ route('operator.history') }}" class="btn btn-secondary form-control">
                            <i class="fe fe-x"></i> Clear Filters
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- History Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">
                <h3 class="card-title mb-0">
                    <i class="fe fe-check-circle mr-2"></i>
                    Completed Tasks
                </h3>
                <div class="ml-auto">
                    <span class="badge badge-light">{{ $completedTasks->total() }} total</span>
                </div>
            </div>
            <div class="card-body p-0">
                @if($completedTasks->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead style="background: #f9fafb;">
                                <tr>
                                    <th>Completed Date</th>
                                    <th>Well</th>
                                    <th>Tower</th>
                                    <th>Plan</th>
                                    <th>Duration</th>
                                    <th>Result</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($completedTasks as $task)
                                <tr>
                                    <td>
                                        <strong>{{ $task->completed_at ? $task->completed_at->format('d M Y') : '-' }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $task->completed_at ? $task->completed_at->format('h:i A') : '' }}</small>
                                    </td>
                                    <td>
                                        {{ $task->well_number }}/{{ $task->string_number }}
                                    </td>
                                    <td>
                                        @if($task->tower)
                                            <span class="badge badge-secondary">{{ $task->tower }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $task->monthlyPlan->plan_name }}</small>
                                    </td>
                                    <td>
                                        @if($task->duration_minutes)
                                            <span class="badge badge-info">{{ $task->duration_minutes }} min</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($task->completion_result)
                                            {{ $task->completion_result }}
                                        @else
                                            <span class="badge badge-success">Completed</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('operator.tasks.show', $task) }}" class="btn btn-sm btn-info">
                                            <i class="fe fe-eye"></i> View
                                        </a>
                                        @if($task->mpfmTest)
                                            <a href="{{ route('well-tests.show', $task->mpfmTest) }}" class="btn btn-sm btn-success" title="View Test Results">
                                                <i class="fe fe-file-text"></i> Results
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="card-footer">
                        {{ $completedTasks->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fe fe-clock" style="font-size: 64px; color: #cbd5e1;"></i>
                        <h3 class="mt-3 mb-2">No Completed Tasks</h3>
                        <p class="text-muted mb-0">You haven't completed any tasks yet with the current filters</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
