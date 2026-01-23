@extends('layouts.master')

@section('title', 'My Tasks')

@section('css')
<link href="{{URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet" />
<link href="{{URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css')}}" rel="stylesheet">
<link href="{{URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css')}}" rel="stylesheet" />
@endsection

@section('content')
<div class="main-container container-fluid">
    <div class="page-header">
        <div class="page-leftheader">
            <h4 class="page-title mb-0">My Tasks</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}"><i class="fe fe-home mr-2 fs-14"></i>Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('operator.dashboard') }}">Operator</a></li>
                <li class="breadcrumb-item active" aria-current="page">My Tasks</li>
            </ol>
        </div>
        <div class="page-rightheader">
            <a href="{{ route('operator.dashboard') }}" class="btn btn-outline-primary">
                <i class="fe fe-arrow-left mr-2"></i>Back to Dashboard
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All My Tasks</h3>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <form method="GET" action="{{ route('operator.tasks') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        <option value="">All Status</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Date From</label>
                                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Date To</label>
                                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fe fe-filter mr-1"></i>Filter
                                        </button>
                                        <a href="{{ route('operator.tasks') }}" class="btn btn-secondary">
                                            <i class="fe fe-x mr-1"></i>Clear
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Tasks Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Task #</th>
                                    <th>Scheduled Date</th>
                                    <th>Plan Name</th>
                                    <th>Tower / Field</th>
                                    <th>Device</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>SLA Status</th>
                                    <th>Duration</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tasks as $task)
                                <tr>
                                    <td><strong>{{ $task->sno ?? '-' }}</strong></td>
                                    <td>
                                        @if($task->scheduled_date)
                                            {{ $task->scheduled_date->format('d M Y') }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $task->monthlyPlan->plan_name ?? '-' }}</td>
                                    <td>
                                        @if($task->towerModel && $task->towerModel->oilField)
                                            {{ $task->towerModel->oilField->name }} / {{ $task->towerModel->name }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $task->mpfm_device ?? '-' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $task->priority_badge_class }}">
                                            {{ ucfirst($task->priority) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $task->status_badge_class }}">
                                            {{ ucfirst($task->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <x-sla-badge :task="$task" size="sm" />
                                    </td>
                                    <td>
                                        @if($task->duration_minutes)
                                            {{ $task->duration_minutes }} min
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('operator.tasks.show', $task) }}"
                                               class="btn btn-sm btn-info"
                                               title="View Details">
                                                <i class="fe fe-eye"></i>
                                            </a>
                                            @if($task->status === 'pending')
                                            <form action="{{ route('operator.tasks.start', $task) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit"
                                                        class="btn btn-sm btn-primary"
                                                        title="Start Task">
                                                    <i class="fe fe-play"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <i class="fe fe-inbox" style="font-size: 3rem; opacity: 0.2;"></i>
                                        <p class="text-muted mt-2">No tasks found</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $tasks->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.js')}}"></script>
@endsection
