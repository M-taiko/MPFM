@extends('layouts.master')

@section('title', $department->name . ' - Reports')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title mb-2">{{ $department->name }} Reports</h1>
                <p class="text-muted mb-0">
                    Performance Analytics & Metrics
                </p>
            </div>
            <div class="btn-group">
                <a href="{{ route('departments.dashboard', $department->id) }}" class="btn btn-secondary">
                    <i class="fe fe-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card" style="border-left: 5px solid #667eea;">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Total Tasks</h6>
                    <h3 class="mb-0">{{ $totalTasks }}</h3>
                    <small class="text-muted">All time</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card" style="border-left: 5px solid #27ae60;">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Completion Rate</h6>
                    <h3 class="mb-0 text-success">{{ $completionRate }}%</h3>
                    <small class="text-muted">{{ $completedTasks }}/{{ $totalTasks }} completed</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card" style="border-left: 5px solid #e74c3c;">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Failed Tasks</h6>
                    <h3 class="mb-0 text-danger">{{ $failedTasks }}</h3>
                    <small class="text-muted">{{ $totalTasks > 0 ? round(($failedTasks / $totalTasks) * 100) : 0 }}% of total</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card" style="border-left: 5px solid #f39c12;">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Overdue Tasks</h6>
                    <h3 class="mb-0 text-warning">{{ $overdueTasks }}</h3>
                    <small class="text-muted">Requires attention</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Tasks by Priority -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card" style="border-left: 5px solid #3498db;">
                <div class="card-header" style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); color: white;">
                    <h5 class="card-title mb-0">
                        <i class="fe fe-bar-chart-2"></i> Tasks by Priority
                    </h5>
                </div>
                <div class="card-body">
                    @if($tasksByPriority && count($tasksByPriority) > 0)
                    <div class="list-group">
                        @foreach(['top' => 'danger', 'high' => 'warning', 'normal' => 'info', 'low' => 'secondary'] as $priority => $badgeClass)
                            @if(isset($tasksByPriority[$priority]))
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span>{{ ucfirst($priority) }} Priority</span>
                                <span class="badge badge-{{ $badgeClass }}">{{ $tasksByPriority[$priority] }}</span>
                            </div>
                            @endif
                        @endforeach
                    </div>
                    @else
                    <p class="text-muted mb-0">No task data available</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- SLA Performance -->
        <div class="col-lg-6">
            <div class="card" style="border-left: 5px solid #8e44ad;">
                <div class="card-header" style="background: linear-gradient(135deg, #8e44ad 0%, #6c3483 100%); color: white;">
                    <h5 class="card-title mb-0">
                        <i class="fe fe-clock"></i> SLA Performance
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $slaCompliance = $totalTasks > 0 ? round((($totalTasks - $overdueTasks) / $totalTasks) * 100, 2) : 0;
                    @endphp
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>SLA Compliance</span>
                            <strong>{{ $slaCompliance }}%</strong>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar" style="width: {{ $slaCompliance }}%;" role="progressbar"></div>
                        </div>
                    </div>

                    <div class="list-group mt-3">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span>On-Time Tasks</span>
                            <strong>{{ $totalTasks - $overdueTasks }}</strong>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Overdue Tasks</span>
                            <strong class="text-danger">{{ $overdueTasks }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Operator Performance -->
    <div class="card" style="border-left: 5px solid #2980b9;">
        <div class="card-header" style="background: linear-gradient(135deg, #2980b9 0%, #1a5276 100%); color: white;">
            <h5 class="card-title mb-0">
                <i class="fe fe-users"></i> Operator Performance
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Operator</th>
                            <th>Completed Tasks</th>
                            <th>Total Assigned</th>
                            <th>Completion Rate</th>
                            <th>Performance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($operatorPerformance as $perf)
                        @php
                            $rate = $perf['total_assigned'] > 0 ? round(($perf['completed_tasks'] / $perf['total_assigned']) * 100) : 0;
                        @endphp
                        <tr>
                            <td>
                                <strong>{{ $perf['name'] }}</strong><br>
                                <small class="text-muted">{{ $perf['email'] }}</small>
                            </td>
                            <td>
                                <span class="badge badge-success">{{ $perf['completed_tasks'] }}</span>
                            </td>
                            <td>
                                <strong>{{ $perf['total_assigned'] }}</strong>
                            </td>
                            <td>
                                <strong>{{ $rate }}%</strong>
                            </td>
                            <td>
                                @if($rate >= 80)
                                    <span class="badge badge-success">Excellent</span>
                                @elseif($rate >= 60)
                                    <span class="badge badge-info">Good</span>
                                @elseif($rate >= 40)
                                    <span class="badge badge-warning">Fair</span>
                                @else
                                    <span class="badge badge-danger">Needs Improvement</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                No operator performance data available
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="card mt-4" style="border-left: 5px solid #16a085;">
        <div class="card-header" style="background: linear-gradient(135deg, #16a085 0%, #0f5c3e 100%); color: white;">
            <h5 class="card-title mb-0">
                <i class="fe fe-activity"></i> Recent Activities
            </h5>
        </div>
        <div class="card-body">
            @forelse($recentActivities as $activity)
            <div class="activity-item mb-3 pb-3" style="border-bottom: 1px solid #e8eaed;">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <h6 class="mb-1">{{ $activity->well_full_name }}</h6>
                        <small class="text-muted">
                            Status: <strong>{{ $activity->completion_status ? ucfirst($activity->completion_status) : 'Pending' }}</strong> |
                            Updated: {{ $activity->updated_at->format('M d, Y H:i') }}
                        </small>
                    </div>
                </div>
            </div>
            @empty
            <p class="text-muted mb-0">No recent activities</p>
            @endforelse
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

    .list-group-item {
        border: 1px solid #e8eaed;
        border-radius: 6px;
        padding: 12px 15px;
        margin-bottom: 8px;
    }

    .activity-item:last-child {
        border-bottom: none;
    }
</style>
@endsection
