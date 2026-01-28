@extends("layouts.app")

@section("content")
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-sm-12">
            <div class="page-title-box">
                <h4 class="page-title">{{ $department->name }} - Tasks Management</h4>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route("dashboard") }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Department Tasks</li>
                </ol>
            </div>
        </div>
    </div>

    @if ($message = Session::get("success"))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Success!</strong> {{ $message }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <h5 class="card-title mb-0">Tasks for {{ $department->name }}</h5>
                </div>
                <div class="card-body">
                    @if ($tasks->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Task ID</th>
                                    <th>Title</th>
                                    <th>Priority</th>
                                    <th>State</th>
                                    <th>Assigned Operators</th>
                                    <th>Due Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tasks as $task)
                                <tr>
                                    <td>
                                        <a href="{{ route("tasks.show", $task) }}" class="text-primary">
                                            #{{ $task->id }}
                                        </a>
                                    </td>
                                    <td>{{ $task->title }}</td>
                                    <td>
                                        <span class="badge badge-{{ $task->getPriorityColor() }}">
                                            {{ ucfirst($task->priority) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-secondary">
                                            {{ $task->state->name }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="operator-list" id="operators-{{ $task->id }}">
                                            @if ($task->operators->count() > 0)
                                                @foreach ($task->operators as $operator)
                                                    <span class="badge bg-info me-1 mb-1">
                                                        {{ $operator->name }}
                                                    </span>
                                                @endforeach
                                            @else
                                                <span class="badge bg-warning">Not Assigned</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if ($task->expected_completion_at)
                                            <small>{{ $task->expected_completion_at->format("Y-m-d") }}</small>
                                        @else
                                            <small class="text-muted">-</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-primary btn-assign"
                                                    data-task-id="{{ $task->id }}"
                                                    data-task-title="{{ $task->title }}"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#assignmentModal">
                                                <i class="fe fe-users"></i> Assign
                                            </button>
                                            @if ($department->code === 'MPFM')
                                                <a href="{{ route('upload') }}?task_id={{ $task->id }}" class="btn btn-success" title="Upload MPFM test results">
                                                    <i class="fe fe-upload"></i> Upload
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $tasks->links() }}
                    </div>
                    @else
                    <div class="alert alert-info" role="alert">
                        No tasks assigned to this department yet.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="assignmentModal" tabindex="-1" role="dialog" aria-labelledby="assignmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignmentModalLabel">Assign Operators to Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modalContent" class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push("scripts")
<script src="{{ asset("js/department-tasks.js") }}"></script>
@endpush
