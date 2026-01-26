@extends('layouts.master')

@section('title', 'System Dashboard')

@section('css')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
    }

    .dashboard-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 40px 0;
        margin-bottom: 40px;
        box-shadow: 0 10px 40px rgba(102, 126, 234, 0.2);
    }

    .dashboard-header h1 {
        font-size: 2.5rem;
        font-weight: 700;
        margin: 0;
    }

    .dashboard-header p {
        opacity: 0.9;
        margin: 10px 0 0 0;
        font-size: 1.1rem;
    }

    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        border-left: 5px solid;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
    }

    .stat-card.primary {
        border-left-color: #667eea;
    }

    .stat-card.success {
        border-left-color: #28a745;
    }

    .stat-card.warning {
        border-left-color: #ffc107;
    }

    .stat-card.danger {
        border-left-color: #dc3545;
    }

    .stat-card.info {
        border-left-color: #17a2b8;
    }

    .stat-content {
        flex: 1;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #2d3748;
        margin: 0;
    }

    .stat-label {
        font-size: 0.9rem;
        color: #718096;
        margin-top: 5px;
        text-transform: uppercase;
        font-weight: 600;
    }

    .stat-icon {
        font-size: 3rem;
        margin-left: 20px;
        opacity: 0.2;
    }

    .stat-card.primary .stat-icon {
        color: #667eea;
    }

    .stat-card.success .stat-icon {
        color: #28a745;
    }

    .stat-card.warning .stat-icon {
        color: #ffc107;
    }

    .stat-card.danger .stat-icon {
        color: #dc3545;
    }

    .stat-card.info .stat-icon {
        color: #17a2b8;
    }

    .chart-container {
        background: white;
        border-radius: 16px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }

    .chart-title {
        font-size: 1.3rem;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2d3748;
        margin-top: 40px;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 3px solid #667eea;
    }

    .department-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .department-card:hover {
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        transform: translateY(-3px);
    }

    .dept-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .dept-name {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2d3748;
    }

    .dept-code {
        background: #667eea;
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .progress-bar {
        height: 8px;
        background: #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 10px;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #667eea, #764ba2);
        border-radius: 10px;
        transition: width 0.5s ease;
    }

    .dept-stats {
        display: flex;
        justify-content: space-around;
        text-align: center;
        font-size: 0.85rem;
    }

    .dept-stat {
        flex: 1;
    }

    .dept-stat-value {
        font-size: 1.3rem;
        font-weight: 700;
        color: #667eea;
    }

    .dept-stat-label {
        color: #718096;
        margin-top: 5px;
    }

    .recent-activity {
        background: white;
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }

    .activity-item {
        display: flex;
        align-items: flex-start;
        padding: 15px 0;
        border-bottom: 1px solid #e2e8f0;
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        flex-shrink: 0;
        font-size: 18px;
    }

    .activity-icon.task {
        background: #e0e7ff;
        color: #667eea;
    }

    .activity-icon.plan {
        background: #dbeafe;
        color: #0284c7;
    }

    .activity-icon.user {
        background: #fce7f3;
        color: #ec4899;
    }

    .activity-content h4 {
        margin: 0 0 5px 0;
        color: #2d3748;
        font-weight: 600;
    }

    .activity-content p {
        margin: 0;
        color: #718096;
        font-size: 0.9rem;
    }

    .activity-time {
        margin-left: auto;
        color: #a0aec0;
        font-size: 0.85rem;
        white-space: nowrap;
    }

    .plan-progress-item {
        margin-bottom: 20px;
        padding-bottom: 20px;
        border-bottom: 1px solid #e2e8f0;
    }

    .plan-progress-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .plan-name {
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 5px;
    }

    .plan-month {
        font-size: 0.85rem;
        color: #718096;
        margin-bottom: 10px;
    }

    .plan-stats-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.9rem;
        margin-bottom: 8px;
    }

    .plan-progress-percent {
        font-weight: 700;
        color: #667eea;
    }

    .badge-small {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-right: 5px;
    }

    .badge-primary {
        background: #e0e7ff;
        color: #667eea;
    }

    .badge-success {
        background: #dcfce7;
        color: #16a34a;
    }

    .badge-warning {
        background: #fef3c7;
        color: #d97706;
    }

    .badge-danger {
        background: #fee2e2;
        color: #dc2626;
    }

    .row-equal {
        display: flex;
        flex-wrap: wrap;
        gap: 0;
    }

    .row-equal > [class*='col-'] {
        display: flex;
        flex-direction: column;
    }

    @media (max-width: 768px) {
        .dashboard-header h1 {
            font-size: 1.8rem;
        }

        .stat-card {
            flex-direction: column;
            text-align: center;
        }

        .stat-icon {
            margin-left: 0;
            margin-top: 15px;
        }

        .dept-stats {
            flex-direction: column;
            gap: 10px;
        }

        .activity-time {
            margin-left: 0;
            margin-top: 10px;
        }
    }

    .canvas-container {
        position: relative;
        height: 300px;
        margin-bottom: 20px;
    }
</style>
@endsection

@section('content')
<div class="dashboard-header">
    <div class="container-fluid">
        <h1><i class="fas fa-chart-line mr-3"></i>System Dashboard</h1>
        <p>Real-time monitoring of all workflow systems and operations</p>
    </div>
</div>

<div class="container-fluid" style="padding: 0 20px;">
    <!-- Key Statistics -->
    <div class="section-title">
        <i class="fas fa-tachometer-alt mr-2" style="color: #667eea;"></i>Key Metrics
    </div>

    <div class="row">
        <!-- Total Tasks -->
        <div class="col-md-6 col-lg-3">
            <div class="stat-card primary">
                <div class="stat-content">
                    <div class="stat-value">{{ $workflowStats['total_tasks'] }}</div>
                    <div class="stat-label">Total Tasks</div>
                </div>
                <div class="stat-icon"><i class="fas fa-tasks"></i></div>
            </div>
        </div>

        <!-- Completed Tasks -->
        <div class="col-md-6 col-lg-3">
            <div class="stat-card success">
                <div class="stat-content">
                    <div class="stat-value">{{ $workflowStats['completed_tasks'] }}</div>
                    <div class="stat-label">Completed Tasks</div>
                </div>
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
            </div>
        </div>

        <!-- In Progress -->
        <div class="col-md-6 col-lg-3">
            <div class="stat-card info">
                <div class="stat-content">
                    <div class="stat-value">{{ $workflowStats['in_progress_tasks'] }}</div>
                    <div class="stat-label">In Progress</div>
                </div>
                <div class="stat-icon"><i class="fas fa-spinner"></i></div>
            </div>
        </div>

        <!-- Completion Rate -->
        <div class="col-md-6 col-lg-3">
            <div class="stat-card warning">
                <div class="stat-content">
                    <div class="stat-value">{{ $workflowStats['completion_rate'] }}%</div>
                    <div class="stat-label">Completion Rate</div>
                </div>
                <div class="stat-icon"><i class="fas fa-percent"></i></div>
            </div>
        </div>
    </div>

    <!-- Additional Stats Row -->
    <div class="row" style="margin-top: 20px;">
        <!-- Active Plans -->
        <div class="col-md-6 col-lg-3">
            <div class="stat-card primary">
                <div class="stat-content">
                    <div class="stat-value">{{ $workflowStats['active_plans'] }}</div>
                    <div class="stat-label">Active Plans</div>
                </div>
                <div class="stat-icon"><i class="fas fa-calendar"></i></div>
            </div>
        </div>

        <!-- Total Users -->
        <div class="col-md-6 col-lg-3">
            <div class="stat-card info">
                <div class="stat-content">
                    <div class="stat-value">{{ $userStats['total_users'] }}</div>
                    <div class="stat-label">Total Users</div>
                </div>
                <div class="stat-icon"><i class="fas fa-users"></i></div>
            </div>
        </div>

        <!-- Departments -->
        <div class="col-md-6 col-lg-3">
            <div class="stat-card success">
                <div class="stat-content">
                    <div class="stat-value">{{ $userStats['departments_count'] }}</div>
                    <div class="stat-label">Departments</div>
                </div>
                <div class="stat-icon"><i class="fas fa-sitemap"></i></div>
            </div>
        </div>

        <!-- Pending Tasks -->
        <div class="col-md-6 col-lg-3">
            <div class="stat-card danger">
                <div class="stat-content">
                    <div class="stat-value">{{ $workflowStats['pending_tasks'] }}</div>
                    <div class="stat-label">Pending Tasks</div>
                </div>
                <div class="stat-icon"><i class="fas fa-hourglass-half"></i></div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="section-title" style="margin-top: 40px;">
        <i class="fas fa-chart-pie mr-2" style="color: #667eea;"></i>Task Analytics
    </div>

    <div class="row">
        <!-- Task State Distribution -->
        <div class="col-lg-6">
            <div class="chart-container">
                <div class="chart-title">
                    <i class="fas fa-pie-chart" style="color: #667eea;"></i>Task State Distribution
                </div>
                <div class="canvas-container">
                    <canvas id="taskStateChart"></canvas>
                </div>
            </div>
        </div>

        <!-- User Role Distribution -->
        <div class="col-lg-6">
            <div class="chart-container">
                <div class="chart-title">
                    <i class="fas fa-users" style="color: #764ba2;"></i>User Role Distribution
                </div>
                <div class="canvas-container">
                    <canvas id="userRoleChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Department Performance -->
    <div class="section-title">
        <i class="fas fa-building mr-2" style="color: #667eea;"></i>Department Performance
    </div>

    <div class="row">
        @forelse($departmentStats as $dept)
        <div class="col-md-6 col-lg-4">
            <div class="department-card">
                <div class="dept-header">
                    <span class="dept-name">{{ $dept['name'] }}</span>
                    <span class="dept-code">{{ $dept['code'] }}</span>
                </div>

                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ $dept['progress'] }}%;"></div>
                </div>

                <div style="text-align: center; margin-bottom: 12px; font-weight: 600; color: #667eea;">
                    {{ $dept['progress'] }}% Complete
                </div>

                <div class="dept-stats">
                    <div class="dept-stat">
                        <div class="dept-stat-value">{{ $dept['completed_tasks'] }}</div>
                        <div class="dept-stat-label">Completed</div>
                    </div>
                    <div class="dept-stat">
                        <div class="dept-stat-value">{{ $dept['total_tasks'] }}</div>
                        <div class="dept-stat-label">Total</div>
                    </div>
                    <div class="dept-stat">
                        <div class="dept-stat-value">{{ $dept['users_count'] }}</div>
                        <div class="dept-stat-label">Users</div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No department data available yet.
            </div>
        </div>
        @endforelse
    </div>

    <!-- Plans Progress -->
    <div class="section-title">
        <i class="fas fa-list-check mr-2" style="color: #667eea;"></i>Monthly Plans Progress
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="chart-container">
                <div class="chart-title">
                    <i class="fas fa-chart-bar" style="color: #667eea;"></i>Plans Completion Status
                </div>
                <div class="canvas-container" style="height: 400px;">
                    <canvas id="planProgressChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Plans List -->
        <div class="col-lg-4">
            <div class="chart-container">
                <div class="chart-title">
                    <i class="fas fa-list" style="color: #667eea;"></i>Top Plans
                </div>

                @forelse($planProgress as $plan)
                <div class="plan-progress-item">
                    <div class="plan-name">{{ $plan['name'] }}</div>
                    <div class="plan-month">
                        <i class="fas fa-calendar"></i> {{ $plan['month'] }}
                    </div>

                    <div class="progress-bar" style="margin-bottom: 10px;">
                        <div class="progress-fill" style="width: {{ $plan['progress'] }}%;"></div>
                    </div>

                    <div class="plan-stats-row">
                        <span>Progress</span>
                        <span class="plan-progress-percent">{{ $plan['progress'] }}%</span>
                    </div>

                    <div style="font-size: 0.85rem; color: #718096;">
                        <span class="badge badge-small badge-success">
                            {{ $plan['completed_tasks'] }} Completed
                        </span>
                        <span class="badge badge-small badge-primary">
                            {{ $plan['total_tasks'] }} Total
                        </span>
                    </div>
                </div>
                @empty
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No plans available yet.
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="section-title">
        <i class="fas fa-clock mr-2" style="color: #667eea;"></i>Recent Activity
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="recent-activity">
                <div class="chart-title">
                    <i class="fas fa-tasks" style="color: #667eea;"></i>Recent Tasks
                </div>

                @forelse($recentActivity['recent_tasks'] as $task)
                <div class="activity-item">
                    <div class="activity-icon task">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <div class="activity-content" style="flex: 1;">
                        <h4>Task #{{ $task->id }}</h4>
                        <p>
                            <strong>{{ $task->well_number }}</strong>
                            @if($task->department)
                            in <strong>{{ $task->department->name }}</strong>
                            @endif
                            - <span class="badge badge-primary" style="padding: 2px 8px; font-size: 0.75rem;">
                                {{ str_replace('_', ' ', ucfirst($task->state->value ?? $task->state)) }}
                            </span>
                        </p>
                    </div>
                    <div class="activity-time">
                        {{ $task->updated_at->diffForHumans() }}
                    </div>
                </div>
                @empty
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No recent tasks.
                </div>
                @endforelse
            </div>
        </div>

        <div class="col-lg-6">
            <div class="recent-activity">
                <div class="chart-title">
                    <i class="fas fa-calendar" style="color: #764ba2;"></i>Recent Plans
                </div>

                @forelse($recentActivity['recent_plans'] as $plan)
                <div class="activity-item">
                    <div class="activity-icon plan">
                        <i class="fas fa-calendar"></i>
                    </div>
                    <div class="activity-content" style="flex: 1;">
                        <h4>{{ $plan->plan_name }}</h4>
                        <p>
                            <strong>{{ $plan->plan_month }}</strong>
                            - <span class="badge badge-primary" style="padding: 2px 8px; font-size: 0.75rem;">
                                {{ ucfirst($plan->status) }}
                            </span>
                            - {{ $plan->tasks()->count() }} tasks
                        </p>
                    </div>
                    <div class="activity-time">
                        {{ $plan->created_at->diffForHumans() }}
                    </div>
                </div>
                @empty
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No recent plans.
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- User Activity -->
    <div class="row" style="margin-top: 30px; margin-bottom: 40px;">
        <div class="col-12">
            <div class="recent-activity">
                <div class="chart-title">
                    <i class="fas fa-users" style="color: #667eea;"></i>Recent Users
                </div>

                @forelse($recentActivity['recent_users'] as $user)
                <div class="activity-item">
                    <div class="activity-icon user">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="activity-content" style="flex: 1;">
                        <h4>{{ $user->name }}</h4>
                        <p>
                            <strong>{{ $user->email }}</strong>
                            @if($user->department)
                            - <span style="color: #667eea; font-weight: 600;">{{ $user->department->name }}</span>
                            @endif
                            @if($user->roles->count() > 0)
                            - Roles: {{ implode(', ', $user->roles->pluck('name')->toArray()) }}
                            @endif
                        </p>
                    </div>
                    <div class="activity-time">
                        {{ $user->created_at->diffForHumans() }}
                    </div>
                </div>
                @empty
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No recent users.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Chart.js configuration for Task State Distribution
    const taskStateCtx = document.getElementById('taskStateChart');
    if (taskStateCtx) {
        const taskStateData = @json($taskStateDistribution);
        const stateLabels = Object.keys(taskStateData).map(s => s.replace(/_/g, ' ').toUpperCase());
        const stateValues = Object.values(taskStateData);

        const colors = ['#667eea', '#28a745', '#ffc107', '#dc3545', '#17a2b8', '#6f42c1', '#e83e8c', '#fd7e14'];

        new Chart(taskStateCtx, {
            type: 'doughnut',
            data: {
                labels: stateLabels,
                datasets: [{
                    data: stateValues,
                    backgroundColor: colors.slice(0, stateLabels.length),
                    borderColor: white,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            font: { size: 12, weight: '500' },
                            color: '#718096'
                        }
                    }
                }
            }
        });
    }

    // Chart.js configuration for User Role Distribution
    const userRoleCtx = document.getElementById('userRoleChart');
    if (userRoleCtx) {
        const userRoleData = @json($userStats['users_by_role']);
        const roleLabels = Object.keys(userRoleData).map(r => r.replace(/_/g, ' ').toUpperCase());
        const roleValues = Object.values(userRoleData);

        const roleColors = {
            'admin': '#dc3545',
            'job_officer': '#667eea',
            'surveillance_supervisor': '#17a2b8',
            'department_manager': '#28a745',
            'mpfm_operator': '#ffc107',
            'operator': '#6f42c1'
        };

        const colors = Object.keys(userRoleData).map(role => roleColors[role] || '#667eea');

        new Chart(userRoleCtx, {
            type: 'bar',
            data: {
                labels: roleLabels,
                datasets: [{
                    label: 'Number of Users',
                    data: roleValues,
                    backgroundColor: colors,
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                indexAxis: 'y',
                plugins: {
                    legend: {
                        display: true,
                        labels: {
                            color: '#718096',
                            font: { size: 12, weight: '500' }
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: { color: '#718096' },
                        grid: { color: '#e2e8f0' }
                    },
                    y: {
                        ticks: { color: '#718096' }
                    }
                }
            }
        });
    }

    // Chart.js configuration for Plan Progress
    const planProgressCtx = document.getElementById('planProgressChart');
    if (planProgressCtx) {
        const planData = @json($planProgress);
        const planNames = planData.map(p => p.name.substring(0, 20) + (p.name.length > 20 ? '...' : ''));
        const progressValues = planData.map(p => p.progress);
        const completedValues = planData.map(p => p.completed_tasks);
        const totalValues = planData.map(p => p.total_tasks);

        new Chart(planProgressCtx, {
            type: 'bar',
            data: {
                labels: planNames,
                datasets: [
                    {
                        label: 'Completed Tasks',
                        data: completedValues,
                        backgroundColor: '#28a745',
                        borderRadius: 6,
                        borderSkipped: false
                    },
                    {
                        label: 'Remaining Tasks',
                        data: totalValues.map((t, i) => t - completedValues[i]),
                        backgroundColor: '#e2e8f0',
                        borderRadius: 6,
                        borderSkipped: false
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                indexAxis: 'y',
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            color: '#718096',
                            font: { size: 12, weight: '500' }
                        }
                    }
                },
                scales: {
                    x: {
                        stacked: true,
                        ticks: { color: '#718096' },
                        grid: { color: '#e2e8f0' }
                    },
                    y: {
                        stacked: true,
                        ticks: { color: '#718096' }
                    }
                }
            }
        });
    }
</script>
@endpush
