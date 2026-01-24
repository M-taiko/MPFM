<div class="btn-group" role="group">
    <a href="{{ route('surveillance.view-task', $task) }}" class="btn btn-sm btn-info" title="View Details">
        <i class="fe fe-eye"></i>
    </a>
    @if($task->mpfmTest)
        @if($task->mpfmTest->test_data)
        <a href="{{ route('mpfm-tests.view', $task->mpfmTest->id) }}" class="btn btn-sm btn-success" title="View Test Charts" target="_blank">
            <i class="fe fe-bar-chart-2"></i>
        </a>
        @endif
        <!-- Validation & History -->
        <a href="{{ route('mpfm_tests.show_history', $task->mpfmTest->id) }}" class="btn btn-sm btn-warning" title="View Validation & History">
            <i class="fe fe-check-circle"></i>
        </a>
        <!-- String Analysis -->
        <a href="{{ route('mpfm_tests.string_analysis', $task->mpfmTest->oil_string_id) }}" class="btn btn-sm btn-primary" title="View String Analysis">
            <i class="fe fe-trending-up"></i>
        </a>
    @endif
    @if($task->completion_status === 'completed')
    <a href="{{ route('task-archive.show', $task) }}" class="btn btn-sm btn-secondary" title="View Archive" target="_blank">
        <i class="fe fe-archive"></i>
    </a>
    @endif
</div>
