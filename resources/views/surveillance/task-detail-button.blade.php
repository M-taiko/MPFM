<div class="btn-group" role="group">
    <a href="{{ route('surveillance.view-task', $task) }}" class="btn btn-sm btn-info" title="View Details">
        <i class="fe fe-eye"></i>
    </a>
    @if($task->mpfmTest && $task->mpfmTest->test_data)
    <a href="{{ route('mpfm-tests.view', $task->mpfmTest->id) }}" class="btn btn-sm btn-success" title="View Test Charts" target="_blank">
        <i class="fe fe-bar-chart-2"></i>
    </a>
    @endif
    @if($task->completion_status === 'completed')
    <a href="{{ route('task-archive.show', $task) }}" class="btn btn-sm btn-secondary" title="View Archive" target="_blank">
        <i class="fe fe-archive"></i>
    </a>
    @endif
</div>
