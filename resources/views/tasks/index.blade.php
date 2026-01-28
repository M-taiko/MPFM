@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Tasks</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('tasks.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create Task
            </a>
        </div>
    </div>

    @if ($tasks->isEmpty())
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> No tasks found. <a href="{{ route('tasks.create') }}">Create one now</a>.
        </div>
    @else
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <input type="text" id="taskFilter" class="form-control form-control-sm"
                               placeholder="Filter by title..." />
                    </div>
                    <div class="col-md-3">
                        <select id="stateFilter" class="form-select form-select-sm">
                            <option value="">All States</option>
                            @foreach (App\Enums\TaskState::cases() as $state)
                                <option value="{{ $state->value }}">{{ $state->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select id="departmentFilter" class="form-select form-select-sm">
                            <option value="">All Departments</option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 text-end">
                        <button class="btn btn-sm btn-secondary" onclick="clearFilters()">Clear</button>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Title</th>
                            <th>Department</th>
                            <th>State</th>
                            <th>Priority</th>
                            <th>Assigned To</th>
                            <th>Due Date</th>
                            <th>Progress</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="task-list">
                        @foreach ($tasks as $task)
                            <tr class="task-row" data-title="{{ strtolower($task->title) }}"
                                data-state="{{ $task->state->value }}"
                                data-department="{{ $task->department_id }}">
                                <td>
                                    <a href="{{ route('tasks.show', $task) }}" class="text-decoration-none">
                                        <strong>{{ $task->title }}</strong>
                                    </a>
                                    @if ($task->isOverdue())
                                        <span class="badge bg-danger ms-2">Overdue</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $task->department->name }}</span>
                                </td>
                                <td>
                                    <span class="badge" style="background-color: {{ $task->state->color() }}">
                                        {{ $task->state->label() }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $task->getPriorityColor() }}">
                                        {{ ucfirst($task->priority) }}
                                    </span>
                                </td>
                                <td>
                                    @if ($task->assignedTo)
                                        <span class="badge bg-secondary">{{ $task->assignedTo->name }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($task->expected_completion_at)
                                        <small>{{ $task->expected_completion_at->format('M d, Y') }}</small>
                                        <br />
                                        <small class="text-muted">({{ $task->getDaysUntilDue() }} days)</small>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar" role="progressbar"
                                             style="width: {{ $task->getProgressPercentage() }}%"
                                             aria-valuenow="{{ $task->getProgressPercentage() }}"
                                             aria-valuemin="0" aria-valuemax="100">
                                            {{ $task->getProgressPercentage() }}%
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('tasks.show', $task) }}" class="btn btn-outline-primary"
                                           title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @can('update', $task)
                                            <a href="{{ route('tasks.edit', $task) }}" class="btn btn-outline-warning"
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endcan
                                        @can('delete', $task)
                                            <form action="{{ route('tasks.destroy', $task) }}" method="POST"
                                                  style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger"
                                                        title="Delete"
                                                        onclick="return confirm('Are you sure?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $tasks->links() }}
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const taskFilter = document.getElementById('taskFilter');
    const stateFilter = document.getElementById('stateFilter');
    const departmentFilter = document.getElementById('departmentFilter');

    function filterTasks() {
        const titleValue = taskFilter.value.toLowerCase();
        const stateValue = stateFilter.value;
        const deptValue = departmentFilter.value;

        document.querySelectorAll('.task-row').forEach(row => {
            const title = row.dataset.title;
            const state = row.dataset.state;
            const dept = row.dataset.department;

            const titleMatch = title.includes(titleValue);
            const stateMatch = !stateValue || state === stateValue;
            const deptMatch = !deptValue || dept === deptValue;

            row.style.display = titleMatch && stateMatch && deptMatch ? '' : 'none';
        });
    }

    window.clearFilters = function () {
        taskFilter.value = '';
        stateFilter.value = '';
        departmentFilter.value = '';
        filterTasks();
    };

    taskFilter.addEventListener('input', filterTasks);
    stateFilter.addEventListener('change', filterTasks);
    departmentFilter.addEventListener('change', filterTasks);
});
</script>
@endsection
