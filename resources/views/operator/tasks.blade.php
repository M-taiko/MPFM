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
                                    <th>Well</th>
                                    <th>String</th>
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
                                            <span class="badge badge-light">{{ $task->scheduled_date->format('d M Y') }}</span>
                                        @else
                                            <span class="badge badge-secondary">Not scheduled</span>
                                        @endif
                                    </td>
                                    <td>{{ $task->monthlyPlan->plan_name ?? '-' }}</td>
                                    <td>
                                        @if($task->towerModel && $task->towerModel->oilField)
                                            <span class="badge badge-info">{{ $task->towerModel->oilField->name }} / {{ $task->towerModel->name }}</span>
                                        @elseif($task->tower)
                                            <span class="badge badge-info">{{ $task->tower }}</span>
                                        @else
                                            <span class="badge badge-secondary">Not assigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($task->well_full_name)
                                            {{ $task->well_full_name }}
                                        @elseif($task->oilString && $task->oilString->name)
                                            {{ $task->oilString->name }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($task->string_number)
                                            <strong>{{ $task->string_number }}</strong>
                                        @elseif($task->oilString && $task->oilString->string_number)
                                            <strong>{{ $task->oilString->string_number }}</strong>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($task->mpfm_device)
                                            <span class="badge badge-primary">{{ $task->mpfm_device }}</span>
                                        @else
                                            <span class="badge badge-secondary">Not specified</span>
                                        @endif
                                    </td>
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
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('operator.tasks.show', $task) }}"
                                               class="btn btn-sm btn-info"
                                               title="View Details">
                                                <i class="fe fe-eye"></i>
                                            </a>
                                            @if($task->mpfmTest)
                                                @if($task->mpfmTest->test_data)
                                                <a href="{{ route('mpfm-tests.view', $task->mpfmTest->id) }}"
                                                   class="btn btn-sm btn-success"
                                                   title="View Test with Charts"
                                                   target="_blank">
                                                    <i class="fe fe-bar-chart-2"></i>
                                                </a>
                                                @endif
                                                <!-- Validation & History -->
                                                <a href="{{ route('mpfm_tests.show_history', $task->mpfmTest->id) }}"
                                                   class="btn btn-sm btn-warning"
                                                   title="View Validation & History">
                                                    <i class="fe fe-check-circle"></i>
                                                </a>
                                                <!-- String Analysis -->
                                                <a href="{{ route('mpfm_tests.string_analysis', $task->mpfmTest->oil_string_id) }}"
                                                   class="btn btn-sm btn-primary"
                                                   title="View String Analysis">
                                                    <i class="fe fe-trending-up"></i>
                                                </a>
                                            @endif
                                            @if($task->completion_status === 'pending' || $task->completion_status === null)
                                            <a href="{{ route('upload.form') }}?task_id={{ $task->id }}"
                                               class="btn btn-sm btn-primary"
                                               title="Upload Log File">
                                                <i class="fe fe-upload"></i> Upload
                                            </a>
                                            @elseif($task->completion_status === 'in_progress')
                                            <a href="{{ route('upload.form') }}?task_id={{ $task->id }}"
                                               class="btn btn-sm btn-success"
                                               title="Upload Log File">
                                                <i class="fe fe-upload"></i> Upload
                                            </a>
                                            @elseif($task->completion_status !== 'completed')
                                            <a href="{{ route('upload.form') }}?task_id={{ $task->id }}"
                                               class="btn btn-sm btn-warning"
                                               title="Upload Log File">
                                                <i class="fe fe-upload"></i> Start
                                            </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="12" class="text-center py-4">
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
