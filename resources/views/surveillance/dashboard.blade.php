@extends('layouts.master')

@section('title', 'Surveillance Supervisor Dashboard')

@section('css')
<link href="{{URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css')}}" rel="stylesheet">
<link href="{{URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
<style>
    .kpi-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }

    .kpi-card:hover {
        transform: translateY(-5px);
    }

    .kpi-card.success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    }

    .kpi-card.warning {
        background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
    }

    .kpi-card.danger {
        background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
    }

    .kpi-card.info {
        background: linear-gradient(135deg, #17a2b8 0%, #20c997 100%);
    }

    .kpi-value {
        font-size: 2.5rem;
        font-weight: 700;
        margin: 10px 0;
    }

    .kpi-label {
        font-size: 0.95rem;
        opacity: 0.95;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .plan-card {
        border-left: 4px solid #667eea;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 15px;
        background: white;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .plan-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .plan-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .plan-title {
        font-weight: 600;
        font-size: 1.1rem;
        color: #333;
    }

    .plan-month {
        color: #666;
        font-size: 0.9rem;
    }

    .plan-stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 15px;
        margin-top: 15px;
    }

    .stat-item {
        text-align: center;
    }

    .stat-number {
        font-size: 1.8rem;
        font-weight: 700;
        color: #667eea;
    }

    .stat-label {
        font-size: 0.85rem;
        color: #666;
        margin-top: 5px;
    }

    .progress-container {
        margin-top: 15px;
    }

    .progress-label {
        font-size: 0.9rem;
        color: #666;
        margin-bottom: 8px;
    }

    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 8px;
        margin-bottom: 30px;
    }

    .page-header h1 {
        margin: 0;
        font-size: 2rem;
        font-weight: 600;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1>
            <i class="fe fe-eye mr-3" style="opacity: 0.8;"></i>
            Surveillance Supervisor Dashboard
        </h1>
        <p style="margin: 10px 0 0 0; opacity: 0.9;">Operational Visibility & Task Management</p>
    </div>

    <!-- KPI Cards -->
    <div class="row">
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="kpi-card">
                <div class="kpi-label"><i class="fe fe-layers mr-2"></i>Total Tasks</div>
                <div class="kpi-value">{{ $totalTasks }}</div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="kpi-card success">
                <div class="kpi-label"><i class="fe fe-check-circle mr-2"></i>Completed</div>
                <div class="kpi-value">{{ $completedTasks }}</div>
                <small style="opacity: 0.9;">{{ $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0 }}%</small>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="kpi-card warning">
                <div class="kpi-label"><i class="fe fe-clock mr-2"></i>Pending</div>
                <div class="kpi-value">{{ $pendingTasks }}</div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="kpi-card danger">
                <div class="kpi-label"><i class="fe fe-alert-circle mr-2"></i>Overdue</div>
                <div class="kpi-value">{{ $overdueTasks }}</div>
            </div>
        </div>
    </div>

    <!-- Department Breakdown (Multi-Department Support) -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Task Distribution by Department</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        @php
                            // Group tasks by department
                            $deptBreakdown = collect([]);
                            if(isset($allTasks)) {
                                $deptBreakdown = $allTasks->groupBy(function($task) {
                                    return $task->department->name ?? 'Unassigned';
                                });
                            }
                        @endphp

                        @forelse($deptBreakdown as $deptName => $tasks)
                        <div class="col-xl-3 col-lg-6 col-md-6">
                            <div class="plan-card" style="margin-bottom: 15px;">
                                <div class="plan-header">
                                    <div>
                                        <div class="plan-title">{{ $deptName }}</div>
                                        <div class="plan-month">{{ $tasks->count() }} Task{{ $tasks->count() !== 1 ? 's' : '' }}</div>
                                    </div>
                                    <div class="progress-label" style="text-align: right; margin: 0;">
                                        @php
                                            $completed = $tasks->where('state', 'completed')->count();
                                            $total = $tasks->count();
                                            $progress = $total > 0 ? round(($completed / $total) * 100) : 0;
                                        @endphp
                                        <span style="font-size: 1.3rem; font-weight: 600; color: #667eea;">
                                            {{ $progress }}%
                                        </span>
                                    </div>
                                </div>

                                <div style="margin-top: 12px;">
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progress }}%"></div>
                                    </div>
                                </div>

                                <div class="plan-stats" style="grid-template-columns: repeat(3, 1fr); margin-top: 10px;">
                                    <div class="stat-item">
                                        <div class="stat-number" style="color: #28a745; font-size: 1.5rem;">{{ $completed }}</div>
                                        <div class="stat-label" style="font-size: 0.8rem;">Complete</div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="stat-number" style="color: #17a2b8; font-size: 1.5rem;">{{ $tasks->where('state', 'in_progress')->count() }}</div>
                                        <div class="stat-label" style="font-size: 0.8rem;">In Progress</div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="stat-number" style="color: #ffc107; font-size: 1.5rem;">{{ $tasks->where('state', '!=', 'completed')->where('state', '!=', 'in_progress')->count() }}</div>
                                        <div class="stat-label" style="font-size: 0.8rem;">Pending</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="fe fe-info"></i> No department breakdown available.
                            </div>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Plans Overview -->
    @if($activePlans->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Active Monthly Plans Overview</h3>
                </div>
                <div class="card-body">
                    @foreach($activePlans as $plan)
                    <div class="plan-card">
                        <div class="plan-header">
                            <div>
                                <div class="plan-title">{{ $plan['plan_name'] }}</div>
                                <div class="plan-month">{{ $plan['plan_month'] }}</div>
                            </div>
                            <div class="progress-label" style="text-align: right; margin: 0;">
                                <span style="font-size: 1.3rem; font-weight: 600; color: #667eea;">
                                    {{ $plan['progress'] }}%
                                </span>
                            </div>
                        </div>

                        <div class="plan-stats">
                            <div class="stat-item">
                                <div class="stat-number">{{ $plan['total_tasks'] }}</div>
                                <div class="stat-label">Total</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number" style="color: #28a745;">{{ $plan['completed_tasks'] }}</div>
                                <div class="stat-label">Completed</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number" style="color: #ffc107;">{{ $plan['pending_tasks'] }}</div>
                                <div class="stat-label">Pending</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number" style="color: #667eea;">
                                    {{ $plan['total_tasks'] - $plan['completed_tasks'] - $plan['pending_tasks'] }}
                                </div>
                                <div class="stat-label">Other</div>
                            </div>
                        </div>

                        <div class="progress-container">
                            <div class="progress-label">Progress</div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $plan['progress'] }}%"
                                     aria-valuenow="{{ $plan['progress'] }}" aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Tasks DataTable -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Tasks</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="tasksTable">
                            <thead>
                                <tr>
                                    <th>Task ID</th>
                                    <th>Plan (Month)</th>
                                    <th>Well Name</th>
                                    <th>Department</th>
                                    <th>Operation Type</th>
                                    <th>Assigned Operator</th>
                                    <th>Task Status</th>
                                    <th>Last Update</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Task Details Modal -->
<div class="modal fade" id="taskDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Task Details</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="taskDetailsContent">
                <!-- Content loaded via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="viewFullDetailsBtn">View Full Details</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.min.js')}}"></script>
<script>
$(document).ready(function() {
    var table = $('#tasksTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        pageLength: 25,
        ajax: {
            url: '{{ route('surveillance.dashboard') }}',
            type: 'GET'
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'plan', name: 'plan' },
            { data: 'well_name', name: 'well_name' },
            { data: 'department', name: 'department' },
            { data: 'operation_type', name: 'operation_type' },
            { data: 'operator', name: 'operator' },
            { data: 'status', name: 'status', orderable: false, searchable: false },
            { data: 'updated_at', name: 'updated_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        columnDefs: [
            { className: 'text-center', targets: [0, 3, 6] }
        ],
        order: [[7, 'desc']]
    });

    // Handle task view button click
    $(document).on('click', '.btn-view-task', function() {
        var taskId = $(this).data('task-id');
        $.get('{{ route('surveillance.dashboard') }}?task_id=' + taskId, function(data) {
            $('#taskDetailsContent').html(data);
            $('#taskDetailsModal').modal('show');
        });
    });
});
</script>
@endpush
