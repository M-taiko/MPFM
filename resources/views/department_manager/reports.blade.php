@extends('layouts.master')

@section('title', auth()->user()->department->name . ' Department Reports')

@section('css')
<link href="{{URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css')}}" rel="stylesheet">
<link href="{{URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
<style>
    .report-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 8px;
        margin-bottom: 30px;
    }

    .report-header h1 {
        margin: 0;
        font-size: 2rem;
        font-weight: 600;
    }

    .metric-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        border-left: 4px solid #667eea;
    }

    .metric-card.success {
        border-left-color: #28a745;
    }

    .metric-card.warning {
        border-left-color: #ffc107;
    }

    .metric-card.danger {
        border-left-color: #dc3545;
    }

    .metric-value {
        font-size: 2rem;
        font-weight: 700;
        color: #667eea;
        margin: 10px 0;
    }

    .metric-card.success .metric-value {
        color: #28a745;
    }

    .metric-card.warning .metric-value {
        color: #ffc107;
    }

    .metric-card.danger .metric-value {
        color: #dc3545;
    }

    .metric-label {
        color: #666;
        font-size: 0.95rem;
        margin-top: 10px;
    }

    .progress-bar-container {
        margin: 15px 0;
    }

    .progress-label {
        font-size: 0.9rem;
        color: #666;
        margin-bottom: 8px;
        display: flex;
        justify-content: space-between;
    }

    .section-title {
        color: #667eea;
        font-size: 1.3rem;
        font-weight: 600;
        margin-top: 30px;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #667eea;
    }

    .state-breakdown {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 15px;
        margin-bottom: 30px;
    }

    @media (max-width: 992px) {
        .state-breakdown {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 576px) {
        .state-breakdown {
            grid-template-columns: 1fr;
        }
    }

    .state-item {
        background: white;
        padding: 15px;
        border-radius: 8px;
        text-align: center;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        border-top: 3px solid #667eea;
    }

    .state-item.completed {
        border-top-color: #28a745;
    }

    .state-item.in-progress {
        border-top-color: #17a2b8;
    }

    .state-item.pending {
        border-top-color: #ffc107;
    }

    .state-count {
        font-size: 2rem;
        font-weight: 700;
        color: #667eea;
        margin: 10px 0;
    }

    .state-item.completed .state-count {
        color: #28a745;
    }

    .state-item.in-progress .state-count {
        color: #17a2b8;
    }

    .state-item.pending .state-count {
        color: #ffc107;
    }

    .state-name {
        font-size: 0.9rem;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .bottleneck-alert {
        background: #fff3cd;
        border-left: 4px solid #ffc107;
        padding: 15px;
        border-radius: 4px;
        margin-bottom: 15px;
    }

    .bottleneck-alert.severe {
        background: #f8d7da;
        border-left-color: #dc3545;
    }

    .export-buttons {
        margin-bottom: 20px;
    }

    .export-buttons .btn {
        margin-right: 10px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Report Header -->
    <div class="report-header">
        <h1>
            <i class="fe fe-bar-chart-2 mr-3"></i>
            {{ auth()->user()->department->name }} Department Reports
        </h1>
        <p style="margin: 10px 0 0 0; opacity: 0.9;">Performance analytics and task breakdown</p>
    </div>

    <!-- Export Options -->
    <div class="row">
        <div class="col-12">
            <div class="export-buttons">
                <button class="btn btn-secondary" id="exportPdfBtn">
                    <i class="fe fe-download"></i> Export as PDF
                </button>
                <button class="btn btn-success" id="exportExcelBtn">
                    <i class="fe fe-download"></i> Export as Excel
                </button>
                <button class="btn btn-info" id="printBtn">
                    <i class="fe fe-printer"></i> Print Report
                </button>
            </div>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="row">
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="metric-card">
                <div class="metric-label"><i class="fe fe-layers mr-2"></i>Total Tasks</div>
                <div class="metric-value">{{ $metrics['total_tasks'] }}</div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="metric-card success">
                <div class="metric-label"><i class="fe fe-check-circle mr-2"></i>Completed Tasks</div>
                <div class="metric-value">{{ $metrics['completed_tasks'] }}</div>
                <small style="color: #28a745;">
                    {{ $metrics['total_tasks'] > 0 ? round(($metrics['completed_tasks'] / $metrics['total_tasks']) * 100, 1) : 0 }}%
                </small>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="metric-card warning">
                <div class="metric-label"><i class="fe fe-alert-circle mr-2"></i>In Progress</div>
                <div class="metric-value">{{ $metrics['in_progress_tasks'] }}</div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="metric-card danger">
                <div class="metric-label"><i class="fe fe-x-circle mr-2"></i>Pending/Unassigned</div>
                <div class="metric-value">{{ $metrics['pending_tasks'] }}</div>
            </div>
        </div>
    </div>

    <!-- Overall Progress -->
    <div class="row">
        <div class="col-12">
            <div class="metric-card">
                <h4 style="color: #667eea; margin-bottom: 20px;">Overall Department Progress</h4>
                <div class="progress-bar-container">
                    <div class="progress-label">
                        <span>Tasks Completed</span>
                        <span>{{ $metrics['completion_percentage'] }}%</span>
                    </div>
                    <div class="progress" style="height: 12px;">
                        <div class="progress-bar bg-success" role="progressbar"
                             style="width: {{ $metrics['completion_percentage'] }}%"
                             aria-valuenow="{{ $metrics['completion_percentage'] }}"
                             aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Task State Breakdown -->
    <h3 class="section-title">Task State Breakdown</h3>
    <div class="state-breakdown">
        @foreach($metrics['state_breakdown'] as $state => $count)
        <div class="state-item {{ strtolower(str_replace('_', '-', $state)) }}">
            <div class="state-count">{{ $count }}</div>
            <div class="state-name">{{ str_replace('_', ' ', $state) }}</div>
        </div>
        @endforeach
    </div>

    <!-- Bottleneck Analysis -->
    @if(!empty($metrics['bottlenecks']))
    <h3 class="section-title">Potential Bottlenecks</h3>
    <div class="row">
        <div class="col-12">
            @foreach($metrics['bottlenecks'] as $bottleneck)
            <div class="bottleneck-alert {{ $bottleneck['severity'] === 'high' ? 'severe' : '' }}">
                <strong>
                    <i class="fe fe-alert-triangle"></i>
                    {{ $bottleneck['state'] }}
                </strong>
                <p class="mb-0" style="margin-top: 5px;">
                    {{ $bottleneck['count'] }} tasks stuck in {{ strtolower($bottleneck['state']) }} state
                    for {{ $bottleneck['duration'] }}
                </p>
            </div>
            @endforeach
        </div>
    </div>
    @else
    <div class="alert alert-success" style="margin-top: 30px;">
        <i class="fe fe-check-circle"></i> No bottlenecks detected. All tasks are progressing smoothly.
    </div>
    @endif

    <!-- Operator Performance -->
    <h3 class="section-title">Operator Performance</h3>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if($metrics['operator_stats']->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Operator Name</th>
                                    <th class="text-center">Total Assigned</th>
                                    <th class="text-center">Completed</th>
                                    <th class="text-center">In Progress</th>
                                    <th class="text-center">Completion Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($metrics['operator_stats'] as $operator)
                                <tr>
                                    <td>
                                        <strong>{{ $operator->name }}</strong>
                                    </td>
                                    <td class="text-center">{{ $operator->assigned_count ?? 0 }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-success">{{ $operator->completed_count ?? 0 }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-info">{{ $operator->in_progress_count ?? 0 }}</span>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $total = ($operator->assigned_count ?? 0) + ($operator->completed_count ?? 0);
                                            $rate = $total > 0 ? round((($operator->completed_count ?? 0) / $total) * 100, 1) : 0;
                                        @endphp
                                        <strong>{{ $rate }}%</strong>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-info">
                        <i class="fe fe-info"></i> No operator data available yet.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Tasks -->
    <h3 class="section-title">Recent Tasks</h3>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="recentTasksTable">
                            <thead>
                                <tr>
                                    <th>Task ID</th>
                                    <th>Plan</th>
                                    <th>Well</th>
                                    <th>Status</th>
                                    <th>Operators</th>
                                    <th>Last Updated</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($metrics['recent_tasks'] as $task)
                                <tr>
                                    <td>
                                        <a href="{{ route('department-manager.show-task', $task) }}">
                                            #{{ $task->id }}
                                        </a>
                                    </td>
                                    <td>{{ $task->plan->plan_name }}</td>
                                    <td>{{ $task->well_number ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $task->state === 'completed' ? 'success' : ($task->state === 'in_progress' ? 'info' : 'warning') }}">
                                            {{ str_replace('_', ' ', ucfirst($task->state)) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($task->assignedOperators->count() > 0)
                                            {{ implode(', ', $task->assignedOperators->pluck('name')->toArray()) }}
                                        @else
                                            <span class="badge badge-danger">Unassigned</span>
                                        @endif
                                    </td>
                                    <td>{{ $task->updated_at->format('M d, Y H:i') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <div class="row mt-4">
        <div class="col-12">
            <a href="{{ route('department-manager.dashboard') }}" class="btn btn-light">
                <i class="fe fe-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.min.js')}}"></script>
<script>
$(document).ready(function() {
    $('#recentTasksTable').DataTable({
        responsive: true,
        pageLength: 10,
        order: [[5, 'desc']]
    });

    // Export and print functionality
    $('#printBtn').on('click', function() {
        window.print();
    });

    $('#exportPdfBtn').on('click', function() {
        alert('PDF export functionality to be implemented');
    });

    $('#exportExcelBtn').on('click', function() {
        alert('Excel export functionality to be implemented');
    });
});
</script>
@endpush
