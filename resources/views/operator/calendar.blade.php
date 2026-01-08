@extends('layouts.master')

@section('title', 'Task Calendar')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div>
        <h1 class="page-title">Task Calendar</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('operator.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Calendar</li>
        </ol>
    </div>
</div>

<!-- Month Selector -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('operator.calendar') }}" class="row align-items-center">
                    <div class="col-md-3">
                        <label class="form-label">Month</label>
                        <select name="month" class="form-control">
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Year</label>
                        <select name="year" class="form-control">
                            @foreach(range(date('Y') - 1, date('Y') + 1) as $y)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary form-control">
                            <i class="fe fe-calendar"></i> View Calendar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Calendar View -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white;">
                <h3 class="card-title mb-0">
                    <i class="fe fe-calendar mr-2"></i>
                    {{ date('F Y', mktime(0, 0, 0, $month, 1, $year)) }}
                </h3>
                <div class="ml-auto">
                    <span class="badge badge-light">{{ $tasks->count() }} tasks this month</span>
                </div>
            </div>
            <div class="card-body">
                @if($tasks->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead style="background: #f9fafb;">
                                <tr>
                                    <th>Date</th>
                                    <th>Well</th>
                                    <th>Tower</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Plan</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tasks->sortBy('scheduled_date') as $task)
                                <tr>
                                    <td>
                                        <strong>{{ $task->scheduled_date->format('d M') }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $task->scheduled_date->format('l') }}</small>
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
                                        @if($task->priority === 'top')
                                            <span class="badge badge-danger">Top</span>
                                        @elseif($task->priority === 'high')
                                            <span class="badge badge-warning">High</span>
                                        @else
                                            <span class="badge badge-secondary">Normal</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($task->completion_status === 'completed')
                                            <span class="badge badge-success">Completed</span>
                                        @elseif($task->completion_status === 'in_progress')
                                            <span class="badge badge-primary">In Progress</span>
                                        @elseif($task->completion_status === 'failed')
                                            <span class="badge badge-danger">Failed</span>
                                        @else
                                            <span class="badge badge-warning">Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $task->monthlyPlan->plan_name }}</small>
                                    </td>
                                    <td>
                                        <a href="{{ route('operator.tasks.show', $task) }}" class="btn btn-sm btn-info">
                                            <i class="fe fe-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fe fe-calendar" style="font-size: 64px; color: #cbd5e1;"></i>
                        <h3 class="mt-3 mb-2">No Tasks This Month</h3>
                        <p class="text-muted mb-0">You don't have any tasks scheduled for {{ date('F Y', mktime(0, 0, 0, $month, 1, $year)) }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
