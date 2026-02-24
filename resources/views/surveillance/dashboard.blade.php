@extends('layouts.master')

@section('title', 'Surveillance Supervisor Dashboard')

@section('content')
<style>
    /* ── Dashboard Header ── */
    .dashboard-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        border-radius: 12px;
        margin-bottom: 2rem;
        box-shadow: 0 8px 32px rgba(102, 126, 234, 0.2);
    }
    .dashboard-header h1 { font-size: 2rem; font-weight: 700; margin: 0; }

    /* ── KPI Cards ── */
    .kpi-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,.08);
        transition: all .3s ease;
        border-left: 4px solid #667eea;
        margin-bottom: 1.5rem;
        display: block;
        text-decoration: none !important;
        color: inherit;
        cursor: pointer;
    }
    .kpi-card:hover {
        box-shadow: 0 8px 24px rgba(0,0,0,.15);
        transform: translateY(-3px);
        text-decoration: none;
        color: inherit;
    }
    .kpi-card.success { border-left-color: #28a745; }
    .kpi-card.warning { border-left-color: #ffc107; }
    .kpi-card.danger  { border-left-color: #dc3545; }
    .kpi-card.info    { border-left-color: #17a2b8; }
    .kpi-value { font-size: 2.2rem; font-weight: 700; color: #667eea; margin: .5rem 0; }
    .kpi-label { font-size: .85rem; color: #666; text-transform: uppercase; letter-spacing: .5px; font-weight: 600; }
    .kpi-card.success .kpi-value { color: #28a745; }
    .kpi-card.warning .kpi-value { color: #e67e22; }
    .kpi-card.danger  .kpi-value { color: #dc3545; }
    .kpi-card.info    .kpi-value { color: #17a2b8; }

    /* ── Department Cards ── */
    .department-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,.08);
        transition: all .3s ease;
        margin-bottom: 1.5rem;
        border-top: 3px solid #667eea;
        cursor: pointer;
    }
    .department-card:hover {
        box-shadow: 0 8px 24px rgba(0,0,0,.15);
        transform: translateY(-3px);
        text-decoration: none;
    }
    .department-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
    .department-name { font-size: 1.3rem; font-weight: 700; color: #333; }

    .completion-badge { display: inline-block; padding: .5rem 1rem; border-radius: 20px; font-weight: 600; font-size: .9rem; }
    .completion-badge.high   { background: #d4edda; color: #155724; }
    .completion-badge.medium { background: #fff3cd; color: #856404; }
    .completion-badge.low    { background: #f8d7da; color: #721c24; }

    .progress { height: 25px; border-radius: 8px; background: #f0f0f0; margin: 1rem 0; }
    .progress-bar {
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        display: flex; align-items: center; justify-content: center;
        color: white; font-weight: 600; font-size: .85rem; border-radius: 8px;
    }

    /* ── Metric boxes (clickable) ── */
    .dept-metrics { display: grid; grid-template-columns: repeat(auto-fit, minmax(100px,1fr)); gap: .75rem; margin-top: 1rem; }
    .metric {
        text-align: center; padding: .75rem; background: #f8f9fa; border-radius: 8px;
        text-decoration: none; color: inherit; display: block; transition: all .2s;
    }
    .metric:hover { background: #e9ecef; transform: scale(1.04); text-decoration: none; color: inherit; }
    .metric-value { font-size: 1.7rem; font-weight: 700; color: #667eea; }
    .metric-label { font-size: .75rem; color: #666; text-transform: uppercase; margin-top: .3rem; }

    /* ── Table cards ── */
    .table-card { background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,.08); margin-bottom: 2rem; }
    .table-card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white; padding: 1rem;
        margin: -1.5rem -1.5rem 1.5rem -1.5rem;
        border-radius: 12px 12px 0 0;
    }
    .table-card-header h5 { margin: 0; font-weight: 600; }

    .data-table { width: 100%; border-collapse: collapse; }
    .data-table thead th { background: #f8f9fa; padding: .85rem 1rem; text-align: left; font-weight: 600; color: #333; border-bottom: 2px solid #e0e0e0; }
    .data-table tbody td { padding: .85rem 1rem; border-bottom: 1px solid #f0f0f0; vertical-align: middle; }
    .data-table tbody tr:hover { background: #f8fafc; cursor: pointer; }
    .data-table tbody td a { color: inherit; text-decoration: none; }
    .data-table tbody td a:hover { color: #667eea; text-decoration: underline; }

    /* ── Tabs ── */
    .nav-tabs { border-bottom: 2px solid #e9ecef; }
    .nav-tabs .nav-link { color: #666; border: none; border-bottom: 3px solid transparent; font-weight: 600; transition: all .3s; padding: .75rem 1.25rem; cursor: pointer; }
    .nav-tabs .nav-link:hover { color: #667eea; border-bottom-color: #667eea; background: transparent; }
    .nav-tabs .nav-link.active { color: #667eea; background: transparent; border-bottom-color: #667eea; border-top: none; border-left: none; border-right: none; }
    .tab-content { display: block; }
    .tab-pane { display: none; }
    .tab-pane.active { display: block; }

    /* ── Tower/OilString cards ── */
    .infra-card {
        background: white; border-radius: 10px; padding: 1.25rem;
        box-shadow: 0 2px 8px rgba(0,0,0,.07); margin-bottom: 1rem;
        border-left: 4px solid #667eea; transition: all .25s;
        text-decoration: none; color: inherit; display: block;
    }
    .infra-card:hover { box-shadow: 0 6px 20px rgba(102,126,234,.2); transform: translateY(-2px); text-decoration: none; color: inherit; }
    .infra-card-title { font-size: 1rem; font-weight: 700; color: #1e293b; margin-bottom: .25rem; }
    .infra-card-sub { font-size: .8rem; color: #64748b; }
    .infra-stats { display: flex; gap: .75rem; margin-top: .75rem; }
    .infra-stat { text-align: center; flex: 1; background: #f8f9fa; border-radius: 6px; padding: .4rem; }
    .infra-stat-val { font-size: 1.2rem; font-weight: 700; color: #667eea; }
    .infra-stat-lbl { font-size: .65rem; color: #94a3b8; text-transform: uppercase; }

    .status-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-right: 4px; }
    .status-dot.active      { background: #22c55e; }
    .status-dot.inactive    { background: #94a3b8; }
    .status-dot.maintenance { background: #f59e0b; }

    /* ── Modals ── */
    .assign-modal .modal-content { border-radius: 12px; border: none; box-shadow: 0 8px 32px rgba(0,0,0,.15); }
    .assign-modal .modal-header  { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 12px 12px 0 0; }
    .btn-assign { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; padding: .5rem 1rem; border-radius: 6px; font-weight: 600; transition: all .3s; }
    .btn-assign:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(102,126,234,.4); color: white; }
</style>

<div class="container-fluid">

    <!-- ════════════════════════════════════ HEADER ════════════════════════════════════ -->
    <div class="dashboard-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1><i class="fe fe-monitor"></i> Surveillance Supervisor Dashboard</h1>
                <p class="mb-0 mt-2" style="opacity:.9;">Monitor all departments, tasks, wells and infrastructure</p>
            </div>
            <div>
                <a href="{{ route('tasks.create') }}" class="btn btn-light mr-2">
                    <i class="fe fe-plus"></i> New Task
                </a>
                <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#assignTaskModal">
                    <i class="fe fe-send"></i> Assign Legacy Task
                </button>
            </div>
        </div>
    </div>

    <!-- ════════════════════════════════════ KPI CARDS ════════════════════════════════════ -->
    <div class="row mb-4">
        <div class="col-6 col-md-3">
            <a href="{{ route('tasks.index') }}" class="kpi-card info">
                <div class="kpi-label"><i class="fe fe-list"></i> Total Tasks</div>
                <div class="kpi-value">{{ $totalTasks }}</div>
                <small style="color:#94a3b8;font-size:.75rem;">Click to view all →</small>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('tasks.index', ['state' => 'completed']) }}" class="kpi-card success">
                <div class="kpi-label"><i class="fe fe-check-circle"></i> Completed</div>
                <div class="kpi-value">{{ $completedTasks }}</div>
                <small style="color:#94a3b8;font-size:.75rem;">Click to view →</small>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('tasks.index', ['state' => 'submitted_to_surveillance']) }}" class="kpi-card warning">
                <div class="kpi-label"><i class="fe fe-clock"></i> Pending Review</div>
                <div class="kpi-value">{{ $pendingTasks }}</div>
                <small style="color:#94a3b8;font-size:.75rem;">Click to view →</small>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('tasks.index') }}" class="kpi-card danger">
                <div class="kpi-label"><i class="fe fe-alert-triangle"></i> Overdue</div>
                <div class="kpi-value">{{ $overdueTasks }}</div>
                <small style="color:#94a3b8;font-size:.75rem;">Click to view all →</small>
            </a>
        </div>
    </div>

    <!-- ════════════════════════════════════ TABS ════════════════════════════════════ -->
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#pending-review" type="button">
                <i class="fe fe-bell"></i> Pending Review
                @if($newTasksForReview->count() > 0)
                    <span class="badge" style="background:#ef4444;color:#fff;border-radius:10px;padding:.1rem .45rem;font-size:.7rem;margin-left:.3rem;">{{ $newTasksForReview->count() }}</span>
                @endif
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#departments" type="button">
                <i class="fe fe-layers"></i> Department Performance
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#infrastructure" type="button">
                <i class="fe fe-map"></i> Wells & Infrastructure
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#unassigned" type="button">
                <i class="fe fe-inbox"></i> Unassigned Tasks
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#plans" type="button">
                <i class="fe fe-calendar"></i> Active Plans
            </button>
        </li>
    </ul>

    <div class="tab-content">

        <!-- ══════════════════════════════════════════════════════
             TAB 1: Pending Review
        ══════════════════════════════════════════════════════ -->
        <div class="tab-pane fade show active" id="pending-review" role="tabpanel">

            @if($newTasksForReview->isEmpty())
            <div style="background:#fff;border-radius:16px;padding:3rem;text-align:center;box-shadow:0 2px 8px rgba(0,0,0,.06);">
                <i class="fe fe-check-circle" style="font-size:3rem;color:#22c55e;display:block;margin-bottom:1rem;"></i>
                <h5 style="color:#374151;font-weight:700;">All caught up!</h5>
                <p style="color:#9ca3af;">No tasks pending your review.</p>
            </div>
            @else
            <div class="table-card">
                <div class="table-card-header">
                    <h5><i class="fe fe-inbox me-2"></i> Tasks Awaiting Review ({{ $newTasksForReview->count() }})</h5>
                </div>
                <div style="overflow-x:auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Task Title</th>
                            <th>Department</th>
                            <th>Priority</th>
                            <th>Monthly Plan</th>
                            <th>Created By</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($newTasksForReview as $task)
                    @php
                        $priColors = ['low'=>'#22c55e','medium'=>'#f59e0b','high'=>'#f97316','critical'=>'#ef4444'];
                        $priColor  = $priColors[$task->priority ?? 'medium'] ?? '#94a3b8';
                    @endphp
                    <tr onclick="window.location='{{ route('tasks.show', $task) }}'" style="cursor:pointer;">
                        <td><span style="font-weight:600;color:#64748b;">#{{ $task->id }}</span></td>
                        <td>
                            <a href="{{ route('tasks.show', $task) }}" onclick="event.stopPropagation();">
                                <div style="font-weight:600;color:#1e293b;">{{ $task->title }}</div>
                                @if($task->description)
                                <div style="font-size:.75rem;color:#9ca3af;margin-top:.2rem;">{{ Str::limit($task->description, 60) }}</div>
                                @endif
                            </a>
                        </td>
                        <td>
                            @if($task->department_id)
                            <a href="{{ route('dept.dashboard', $task->department->code ?? '') }}" onclick="event.stopPropagation();"
                               style="background:#f1f5f9;border-radius:8px;padding:.25rem .65rem;font-size:.8rem;font-weight:600;color:#475569;text-decoration:none;display:inline-block;">
                                {{ $task->department->name ?? '—' }}
                            </a>
                            @else
                            <span style="color:#94a3b8;">—</span>
                            @endif
                        </td>
                        <td>
                            <span style="background:{{ $priColor }}20;color:{{ $priColor }};border-radius:8px;padding:.25rem .65rem;font-size:.78rem;font-weight:700;text-transform:capitalize;">
                                {{ $task->priority ?? '—' }}
                            </span>
                        </td>
                        <td>
                            @if($task->monthly_plan_id)
                            <a href="{{ route('surveillance.review', $task->monthly_plan_id) }}" onclick="event.stopPropagation();"
                               style="font-size:.82rem;color:#667eea;">
                                {{ $task->monthlyPlan->plan_name ?? '—' }}
                            </a>
                            @else
                            <span style="font-size:.82rem;color:#64748b;">—</span>
                            @endif
                        </td>
                        <td style="font-size:.82rem;color:#64748b;">{{ $task->createdBy->name ?? '—' }}</td>
                        <td style="font-size:.78rem;color:#94a3b8;">{{ $task->created_at->format('d M Y H:i') }}</td>
                        <td onclick="event.stopPropagation();">
                            <div style="display:flex;gap:.4rem;flex-wrap:wrap;">
                                <form action="{{ route('surveillance.new-task.approve', $task) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm"
                                            style="background:#16a34a;color:#fff;border:none;border-radius:8px;padding:.3rem .7rem;font-size:.75rem;font-weight:600;"
                                            onclick="return confirm('Approve and assign to {{ addslashes($task->department->name ?? 'the department') }}?')">
                                        <i class="fe fe-check"></i> Approve
                                    </button>
                                </form>
                                <button type="button" class="btn btn-sm"
                                        style="background:#ef4444;color:#fff;border:none;border-radius:8px;padding:.3rem .7rem;font-size:.75rem;font-weight:600;"
                                        data-bs-toggle="modal" data-bs-target="#rejectModal{{ $task->id }}">
                                    <i class="fe fe-x"></i> Reject
                                </button>
                                <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm"
                                   style="background:#f1f5f9;color:#475569;border:none;border-radius:8px;padding:.3rem .7rem;font-size:.75rem;font-weight:600;">
                                    <i class="fe fe-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>

                    {{-- Reject Modal --}}
                    <div class="modal fade" id="rejectModal{{ $task->id }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content" style="border-radius:16px;border:none;">
                                <div class="modal-header" style="background:linear-gradient(135deg,#ef4444,#dc2626);color:#fff;border-radius:16px 16px 0 0;border:none;">
                                    <h5 class="modal-title"><i class="fe fe-x-circle me-2"></i>Reject Task</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('surveillance.new-task.reject', $task) }}" method="POST">
                                    @csrf
                                    <div class="modal-body" style="padding:1.5rem;">
                                        <p style="color:#374151;font-size:.9rem;">Rejecting: <strong>{{ $task->title }}</strong></p>
                                        <label style="font-size:.8rem;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.4px;margin-bottom:.4rem;display:block;">
                                            Reason <span style="color:#ef4444;">*</span>
                                        </label>
                                        <textarea name="remarks" rows="3" required
                                                  style="width:100%;border:1.5px solid #e2e8f0;border-radius:10px;padding:.65rem .9rem;font-size:.88rem;resize:vertical;"
                                                  placeholder="Explain why this task is being rejected..."></textarea>
                                    </div>
                                    <div class="modal-footer" style="border:none;padding:1rem 1.5rem;">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-danger"><i class="fe fe-x me-1"></i>Reject Task</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    </tbody>
                </table>
                </div>
            </div>
            @endif

            {{-- Already Assigned --}}
            @if($assignedNewTasks->isNotEmpty())
            <div class="table-card" style="margin-top:1.5rem;">
                <div class="table-card-header" style="background:linear-gradient(135deg,#6366f1,#4f46e5);">
                    <h5><i class="fe fe-layers me-2"></i> Assigned to Departments ({{ $assignedNewTasks->count() }})</h5>
                </div>
                <div style="overflow-x:auto;">
                <table class="data-table">
                    <thead>
                        <tr><th>#</th><th>Task Title</th><th>Department</th><th>Priority</th><th>Approved At</th><th></th></tr>
                    </thead>
                    <tbody>
                    @foreach($assignedNewTasks as $task)
                    <tr onclick="window.location='{{ route('tasks.show', $task) }}'" style="cursor:pointer;">
                        <td><span style="font-weight:600;color:#64748b;">#{{ $task->id }}</span></td>
                        <td style="font-weight:600;color:#1e293b;">
                            <a href="{{ route('tasks.show', $task) }}" onclick="event.stopPropagation();">{{ $task->title }}</a>
                        </td>
                        <td>
                            @if($task->department_id)
                            <a href="{{ route('dept.dashboard', $task->department->code ?? '') }}" onclick="event.stopPropagation();"
                               style="background:#eef2ff;border-radius:8px;padding:.25rem .65rem;font-size:.8rem;font-weight:600;color:#4f46e5;text-decoration:none;display:inline-block;">
                                {{ $task->department->name ?? '—' }}
                            </a>
                            @else <span>—</span> @endif
                        </td>
                        <td style="text-transform:capitalize;font-size:.82rem;">{{ $task->priority ?? '—' }}</td>
                        <td style="font-size:.78rem;color:#94a3b8;">{{ $task->updated_at->format('d M Y') }}</td>
                        <td onclick="event.stopPropagation();">
                            <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm"
                               style="background:#f1f5f9;color:#475569;border:none;border-radius:8px;padding:.3rem .7rem;font-size:.75rem;">
                                <i class="fe fe-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
                </div>
            </div>
            @endif
        </div>

        <!-- ══════════════════════════════════════════════════════
             TAB 2: Department Performance
        ══════════════════════════════════════════════════════ -->
        <div class="tab-pane fade" id="departments" role="tabpanel">
            <div class="row">
                @forelse($departments as $dept)
                <div class="col-md-6 col-lg-4">
                    <div class="department-card" onclick="window.location='{{ route('dept.dashboard', $dept['code']) }}'">
                        <div class="department-header">
                            <div>
                                <div class="department-name">{{ $dept['name'] }}</div>
                                <small class="text-muted" style="font-size:.75rem;font-weight:600;text-transform:uppercase;letter-spacing:.5px;">{{ $dept['code'] }}</small>
                            </div>
                            <div>
                                @if($dept['completion_rate'] >= 80)
                                    <span class="completion-badge high">{{ $dept['completion_rate'] }}%</span>
                                @elseif($dept['completion_rate'] >= 50)
                                    <span class="completion-badge medium">{{ $dept['completion_rate'] }}%</span>
                                @else
                                    <span class="completion-badge low">{{ $dept['completion_rate'] }}%</span>
                                @endif
                            </div>
                        </div>

                        <div class="progress">
                            <div class="progress-bar" style="width:{{ $dept['completion_rate'] }}%;">
                                {{ $dept['completion_rate'] }}%
                            </div>
                        </div>

                        <!-- Clickable Metrics -->
                        <div class="dept-metrics">
                            <a href="{{ route('dept.tasks', $dept['code']) }}" class="metric" onclick="event.stopPropagation();">
                                <div class="metric-value">{{ $dept['total_tasks'] }}</div>
                                <div class="metric-label">Total</div>
                            </a>
                            <a href="{{ route('dept.tasks', ['code' => $dept['code'], 'state' => 'completed']) }}" class="metric" onclick="event.stopPropagation();">
                                <div class="metric-value text-success">{{ $dept['completed_tasks'] }}</div>
                                <div class="metric-label">Done</div>
                            </a>
                            <a href="{{ route('dept.tasks', ['code' => $dept['code'], 'state' => 'pending']) }}" class="metric" onclick="event.stopPropagation();">
                                <div class="metric-value text-warning">{{ $dept['pending_tasks'] }}</div>
                                <div class="metric-label">Pending</div>
                            </a>
                            <a href="{{ route('dept.tasks', ['code' => $dept['code'], 'state' => 'failed']) }}" class="metric" onclick="event.stopPropagation();">
                                <div class="metric-value text-danger">{{ $dept['failed_tasks'] }}</div>
                                <div class="metric-label">Failed</div>
                            </a>
                        </div>

                        <div class="mt-3 d-flex gap-2">
                            <a href="{{ route('dept.dashboard', $dept['code']) }}" class="btn btn-sm btn-outline-primary flex-fill" onclick="event.stopPropagation();">
                                <i class="fe fe-bar-chart-2"></i> Dashboard
                            </a>
                            <a href="{{ route('dept.tasks', $dept['code']) }}" class="btn btn-sm btn-outline-secondary flex-fill" onclick="event.stopPropagation();">
                                <i class="fe fe-list"></i> Tasks
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="alert alert-info text-center"><i class="fe fe-info"></i> No departments available</div>
                </div>
                @endforelse
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════
             TAB 3: Wells & Infrastructure
        ══════════════════════════════════════════════════════ -->
        <div class="tab-pane fade" id="infrastructure" role="tabpanel">

            <!-- Towers Section -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 style="font-weight:700;color:#1e293b;margin:0;"><i class="fe fe-grid" style="color:#667eea;"></i> Towers ({{ count($towers) }})</h5>
                <a href="{{ route('towers.index') }}" class="btn btn-sm btn-outline-primary">
                    <i class="fe fe-list"></i> View All Towers
                </a>
            </div>
            <div class="row mb-4">
                @forelse($towers as $tower)
                <div class="col-md-4 col-lg-3">
                    <a href="{{ route('towers.show', $tower['id']) }}" class="infra-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="infra-card-title">{{ $tower['name'] }}</div>
                                <div class="infra-card-sub">{{ $tower['code'] }}</div>
                            </div>
                            <span class="status-dot {{ $tower['status'] ?? 'active' }}" style="margin-top:5px;" title="{{ ucfirst($tower['status'] ?? 'active') }}"></span>
                        </div>
                        <div class="infra-stats">
                            <div class="infra-stat">
                                <div class="infra-stat-val">{{ $tower['strings_count'] }}</div>
                                <div class="infra-stat-lbl">Strings</div>
                            </div>
                            <div class="infra-stat">
                                <div class="infra-stat-val">{{ $tower['tasks_count'] }}</div>
                                <div class="infra-stat-lbl">Tasks</div>
                            </div>
                        </div>
                    </a>
                </div>
                @empty
                <div class="col-12">
                    <div class="alert alert-info text-center"><i class="fe fe-info"></i> No towers found.</div>
                </div>
                @endforelse
            </div>

            <!-- Oil Strings Section -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 style="font-weight:700;color:#1e293b;margin:0;"><i class="fe fe-git-branch" style="color:#667eea;"></i> Oil Strings ({{ count($oilStrings) }})</h5>
                <a href="{{ route('oil_strings.index') }}" class="btn btn-sm btn-outline-primary">
                    <i class="fe fe-list"></i> View All Strings
                </a>
            </div>
            <div class="table-card">
                <div style="overflow-x:auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>String Name</th>
                            <th>Code</th>
                            <th>Tower</th>
                            <th>Status</th>
                            <th>Tasks</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($oilStrings as $string)
                    <tr onclick="window.location='{{ route('oil_strings.show', $string['id']) }}'" style="cursor:pointer;">
                        <td style="font-weight:600;color:#1e293b;">{{ $string['name'] }}</td>
                        <td style="font-size:.82rem;color:#64748b;">{{ $string['code'] }}</td>
                        <td>
                            @if($string['tower_id'])
                            <a href="{{ route('towers.show', $string['tower_id']) }}" onclick="event.stopPropagation();"
                               style="color:#667eea;font-size:.85rem;">
                                {{ $string['tower_name'] }}
                            </a>
                            @else
                            <span style="color:#94a3b8;">—</span>
                            @endif
                        </td>
                        <td>
                            @php $statusColors = ['active'=>'success','inactive'=>'secondary','maintenance'=>'warning']; @endphp
                            <span class="badge badge-{{ $statusColors[$string['status'] ?? 'inactive'] ?? 'secondary' }}">
                                {{ ucfirst($string['status'] ?? 'unknown') }}
                            </span>
                        </td>
                        <td>
                            <span style="font-weight:700;color:#667eea;">{{ $string['tasks_count'] }}</span>
                            <span style="font-size:.75rem;color:#94a3b8;"> tasks</span>
                        </td>
                        <td onclick="event.stopPropagation();">
                            <a href="{{ route('oil_strings.show', $string['id']) }}" class="btn btn-sm btn-outline-primary" style="font-size:.75rem;">
                                <i class="fe fe-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-3"><i class="fe fe-inbox"></i> No oil strings found.</td></tr>
                    @endforelse
                    </tbody>
                </table>
                </div>
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════
             TAB 4: Unassigned Tasks (Legacy PlanTasks)
        ══════════════════════════════════════════════════════ -->
        <div class="tab-pane fade" id="unassigned" role="tabpanel">
            <div class="table-card">
                <div class="table-card-header">
                    <h5><i class="fe fe-inbox"></i> Unassigned Tasks</h5>
                </div>

                @if($unassignedTasks->count() > 0)
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Task ID</th>
                                <th>Well Name</th>
                                <th>Monthly Plan</th>
                                <th>Status</th>
                                <th>Department</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($unassignedTasks as $task)
                            <tr onclick="window.location='{{ route('surveillance.viewTask', $task->id) }}'" style="cursor:pointer;">
                                <td><strong>#{{ $task->id }}</strong></td>
                                <td>
                                    <a href="{{ route('surveillance.viewTask', $task->id) }}" onclick="event.stopPropagation();" style="font-weight:600;color:#1e293b;">
                                        {{ $task->well_full_name ?? $task->well_number }}
                                    </a>
                                </td>
                                <td>
                                    @if($task->monthly_plan_id)
                                    <a href="{{ route('surveillance.review', $task->monthly_plan_id) }}" onclick="event.stopPropagation();" style="color:#667eea;font-size:.85rem;">
                                        {{ $task->monthlyPlan->plan_name ?? 'N/A' }}
                                    </a>
                                    @else <span style="color:#94a3b8;">N/A</span> @endif
                                </td>
                                <td>
                                    @if($task->completion_status === 'pending')
                                        <span class="badge badge-warning">Pending</span>
                                    @elseif($task->completion_status === 'in_progress')
                                        <span class="badge badge-info">In Progress</span>
                                    @else
                                        <span class="badge badge-secondary">{{ ucfirst($task->completion_status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($task->department)
                                        <a href="{{ route('dept.dashboard', $task->department->code) }}" onclick="event.stopPropagation();" class="badge badge-primary" style="text-decoration:none;">
                                            {{ $task->department->name }}
                                        </a>
                                    @else
                                        <span class="badge badge-danger">Not Assigned</span>
                                    @endif
                                </td>
                                <td onclick="event.stopPropagation();">
                                    <button type="button" class="btn btn-sm btn-assign btn-assign-task"
                                            data-task-id="{{ $task->id }}"
                                            data-task-name="{{ $task->well_full_name ?? $task->well_number }}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#assignTaskModal">
                                        <i class="fe fe-arrow-right"></i> {{ $task->department ? 'Reassign' : 'Assign' }}
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="alert alert-success text-center">
                    <i class="fe fe-check-circle"></i> All tasks are assigned to departments!
                </div>
                @endif
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════
             TAB 5: Active Plans
        ══════════════════════════════════════════════════════ -->
        <div class="tab-pane fade" id="plans" role="tabpanel">
            <div class="row">
                @forelse($activePlans as $plan)
                <div class="col-md-6">
                    <div class="table-card mb-3" style="cursor:pointer;transition:all .25s;" onclick="window.location='{{ route('surveillance.review', $plan['id']) }}'"
                         onmouseenter="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 24px rgba(0,0,0,.12)'"
                         onmouseleave="this.style.transform='';this.style.boxShadow=''">
                        <div class="table-card-header">
                            <h5>
                                <i class="fe fe-calendar me-2"></i>
                                {{ $plan['plan_name'] }}
                                <small style="opacity:.8;font-size:.8rem;font-weight:400;"> — {{ $plan['plan_month'] }}</small>
                            </h5>
                        </div>
                        <div style="padding:1.5rem;">
                            <div class="row mb-3">
                                <div class="col-6">
                                    <a href="{{ route('surveillance.review', $plan['id']) }}"
                                       onclick="event.stopPropagation();" style="text-decoration:none;">
                                        <div class="metric">
                                            <div class="metric-value">{{ $plan['total_tasks'] }}</div>
                                            <div class="metric-label">Total Tasks</div>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('surveillance.review', $plan['id']) }}"
                                       onclick="event.stopPropagation();" style="text-decoration:none;">
                                        <div class="metric">
                                            <div class="metric-value text-success">{{ $plan['completed_tasks'] }}</div>
                                            <div class="metric-label">Completed</div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="progress">
                                <div class="progress-bar" style="width:{{ $plan['progress'] }}%;">{{ $plan['progress'] }}%</div>
                            </div>
                            <a href="{{ route('surveillance.review', $plan['id']) }}" onclick="event.stopPropagation();"
                               class="btn btn-sm btn-outline-primary mt-3 w-100">
                                <i class="fe fe-eye"></i> Review Plan
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="alert alert-info text-center"><i class="fe fe-info"></i> No active plans</div>
                </div>
                @endforelse
            </div>
        </div>

    </div><!-- /tab-content -->
</div>

<!-- Assign Task Modal (Legacy) -->
<div class="modal fade assign-modal" id="assignTaskModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fe fe-send"></i> Assign Task to Department</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="modalContent">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab switching
        document.querySelectorAll('.nav-link[data-bs-toggle="tab"]').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelectorAll('.nav-link').forEach(b => b.classList.remove('active'));
                document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active','show'));
                this.classList.add('active');
                const pane = document.querySelector(this.getAttribute('data-bs-target'));
                if (pane) pane.classList.add('active','show');
            });
        });

        // Assign modal
        document.querySelectorAll('.btn-assign-task').forEach(button => {
            button.addEventListener('click', function() {
                const taskId = this.dataset.taskId;
                fetch(`/surveillance/tasks/${taskId}/assign-modal`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(r => r.text())
                .then(html => { document.getElementById('modalContent').innerHTML = html; })
                .catch(() => { document.getElementById('modalContent').innerHTML = '<p class="text-danger p-3">Error loading form.</p>'; });
            });
        });
    });

    function assignTaskToDepartment(taskId, departmentId) {
        if (!departmentId) { alert('Please select a department'); return; }
        fetch(`/surveillance/tasks/${taskId}/assign-to-department/${departmentId}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Content-Type': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) { alert('Task assigned!'); location.reload(); }
            else alert('Error: ' + data.message);
        })
        .catch(() => alert('An error occurred.'));
    }
</script>
@endpush
