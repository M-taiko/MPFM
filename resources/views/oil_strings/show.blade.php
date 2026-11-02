@extends('layouts.master')

@section('title', 'Well Profile — ' . $oilString->name)

@section('css')
<style>
    .kpi-card {
        border-radius: 12px;
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: transform 0.2s;
    }
    .kpi-card:hover { transform: translateY(-3px); }
    .kpi-label { font-size: 0.78rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
    .kpi-value { font-size: 1.8rem; font-weight: 700; color: #1e293b; line-height: 1.2; }
    .kpi-unit  { font-size: 0.72rem; color: #94a3b8; }

    .section-header {
        font-size: 1rem;
        font-weight: 700;
        color: #374151;
        border-bottom: 2px solid #e5e7eb;
        padding-bottom: 0.5rem;
        margin-bottom: 1.25rem;
    }

    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .status-active        { background: #d1fae5; color: #065f46; }
    .status-inactive      { background: #e5e7eb; color: #374151; }
    .status-maintenance   { background: #fef3c7; color: #92400e; }

    .test-approved  { border-left: 3px solid #10b981; }
    .test-submitted { border-left: 3px solid #f59e0b; }
    .test-draft     { border-left: 3px solid #9ca3af; }
    .test-rejected  { border-left: 3px solid #ef4444; }

    .chart-container { position: relative; height: 280px; }

    .breadcrumb-well { background: linear-gradient(135deg, #1e3a5f 0%, #2d5986 100%); padding: 1.5rem 2rem; border-radius: 12px; color: white; margin-bottom: 1.5rem; }
    .breadcrumb-well .well-title { font-size: 1.6rem; font-weight: 700; margin: 0; }
    .breadcrumb-well .well-code  { font-size: 0.9rem; opacity: 0.7; }
    .breadcrumb-well .well-path  { font-size: 0.85rem; opacity: 0.8; margin-top: 0.25rem; }
    .breadcrumb-well .well-path a { color: rgba(255,255,255,0.8); text-decoration: none; }
    .breadcrumb-well .well-path a:hover { color: white; }

    .info-grid dt { color: #64748b; font-size: 0.82rem; font-weight: 600; }
    .info-grid dd { color: #1e293b; font-size: 0.92rem; margin-bottom: 0.75rem; }

    .lift-note { background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px; padding: 0.75rem 1rem; font-size: 0.82rem; color: #0369a1; }

    .table-tasks th { background: #f8fafc; font-size: 0.8rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.4px; border-top: none; }
    .table-tasks td { font-size: 0.88rem; vertical-align: middle; }

    .no-data-placeholder { text-align: center; padding: 3rem; color: #9ca3af; }
    .no-data-placeholder i { font-size: 2.5rem; display: block; margin-bottom: 0.75rem; opacity: 0.3; }
</style>
@endsection

@section('content')
<div class="main-container container-fluid">

    {{-- ===== Well Header ===== --}}
    <div class="breadcrumb-well">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <div class="well-path mb-1">
                    <a href="{{ route('oil_fields.show', $oilString->tower->oilField->id) }}">
                        {{ $oilString->tower->oilField->name }}
                    </a>
                    <span style="opacity:0.5"> › </span>
                    <a href="{{ route('towers.show', $oilString->tower->id) }}">
                        {{ $oilString->tower->name }}
                    </a>
                    <span style="opacity:0.5"> › </span>
                    <strong>{{ $oilString->name }}</strong>
                </div>
                <h2 class="well-title">{{ $oilString->name }}</h2>
                <div class="well-code">Code: {{ $oilString->code }}</div>
            </div>
            <div class="d-flex align-items-center" style="gap: 0.5rem; flex-wrap: wrap;">
                <span class="status-badge status-{{ $oilString->status }}">
                    {{ ucfirst($oilString->status) }}
                </span>
                @can('update', $oilString)
                <a href="{{ route('oil_strings.edit', $oilString->id) }}" class="btn btn-sm" style="background: rgba(255,255,255,0.15); color:white; border: 1px solid rgba(255,255,255,0.3);">
                    <i class="fe fe-edit mr-1"></i>Edit
                </a>
                @endcan
                <a href="{{ route('mpfm_tests.string_analysis', $oilString->id) }}" class="btn btn-sm" style="background: rgba(255,255,255,0.15); color:white; border: 1px solid rgba(255,255,255,0.3);">
                    <i class="fe fe-trending-up mr-1"></i>Trend Analysis
                </a>
                <a href="{{ route('oil_strings.index') }}" class="btn btn-sm" style="background: rgba(255,255,255,0.15); color:white; border: 1px solid rgba(255,255,255,0.3);">
                    <i class="fe fe-arrow-left mr-1"></i>Back
                </a>
            </div>
        </div>
    </div>

    {{-- ===== KPI Cards ===== --}}
    <div class="row mb-4">
        <div class="col-6 col-md-3 mb-3">
            <div class="card kpi-card p-3" style="border-top: 3px solid #3b82f6;">
                <div class="kpi-label">Total Tests</div>
                <div class="kpi-value">{{ $stats['total_tests'] }}</div>
                <div class="kpi-unit">{{ $stats['approved_tests'] }} approved</div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card kpi-card p-3" style="border-top: 3px solid #10b981;">
                <div class="kpi-label">Latest Oil Rate</div>
                <div class="kpi-value">{{ $stats['latest_oil_rate'] !== null ? number_format($stats['latest_oil_rate'], 1) : '—' }}</div>
                <div class="kpi-unit">m³/d &nbsp;·&nbsp; avg {{ $stats['avg_oil_rate'] !== null ? number_format($stats['avg_oil_rate'], 1) : '—' }} m³/d</div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card kpi-card p-3" style="border-top: 3px solid #f59e0b;">
                <div class="kpi-label">Avg Water Cut</div>
                <div class="kpi-value">{{ $stats['avg_water_cut'] !== null ? number_format($stats['avg_water_cut'], 1) . '%' : '—' }}</div>
                <div class="kpi-unit">BSW (Basic Sediment & Water)</div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card kpi-card p-3" style="border-top: 3px solid #8b5cf6;">
                <div class="kpi-label">Avg GOR</div>
                <div class="kpi-value">{{ $stats['avg_gor'] !== null ? number_format($stats['avg_gor'], 0) : '—' }}</div>
                <div class="kpi-unit">m³/m³ &nbsp;·&nbsp; Gas-Oil Ratio</div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card kpi-card p-3" style="border-top: 3px solid #06b6d4;">
                <div class="kpi-label">Avg Wellhead Pressure</div>
                <div class="kpi-value">{{ $stats['avg_pressure'] !== null ? number_format($stats['avg_pressure'], 1) : '—' }}</div>
                <div class="kpi-unit">bar (WHFP)</div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card kpi-card p-3" style="border-top: 3px solid #f97316;">
                <div class="kpi-label">Avg Temperature</div>
                <div class="kpi-value">{{ $stats['avg_temperature'] !== null ? number_format($stats['avg_temperature'], 1) : '—' }}</div>
                <div class="kpi-unit">°C</div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card kpi-card p-3" style="border-top: 3px solid #84cc16;">
                <div class="kpi-label">Avg Gas Rate</div>
                <div class="kpi-value">{{ $stats['avg_gas_rate'] !== null ? number_format($stats['avg_gas_rate'], 1) : '—' }}</div>
                <div class="kpi-unit">10³ m³/d</div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card kpi-card p-3" style="border-top: 3px solid #10b981;">
                <div class="kpi-label">Max Oil Rate</div>
                <div class="kpi-value">{{ $stats['max_oil_rate'] !== null ? number_format($stats['max_oil_rate'], 1) : '—' }}</div>
                <div class="kpi-unit">m³/d &nbsp;·&nbsp; peak production</div>
            </div>
        </div>
    </div>

    {{-- ===== Charts Row ===== --}}
    @if(count($chartDates) > 0)
    <div class="row mb-4">
        {{-- Production Trend --}}
        <div class="col-md-8 mb-3">
            <div class="card" style="border-radius:12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <div class="card-body">
                    <h6 class="section-header"><i class="fe fe-trending-up mr-2" style="color:#3b82f6;"></i>Production Trend (Approved Tests)</h6>
                    <div class="chart-container">
                        <canvas id="productionTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        {{-- Lift Curve --}}
        <div class="col-md-4 mb-3">
            <div class="card" style="border-radius:12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <div class="card-body">
                    <h6 class="section-header"><i class="fe fe-activity mr-2" style="color:#8b5cf6;"></i>Lift Curve (Qo vs WHFP)</h6>
                    <div class="chart-container">
                        <canvas id="liftCurveChart"></canvas>
                    </div>
                    @if(count($liftCurveData) < 3)
                    <div class="lift-note mt-2">
                        <i class="fe fe-info mr-1"></i>Need more approved tests for a representative lift curve.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Water Cut & Pressure Trend --}}
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="card" style="border-radius:12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <div class="card-body">
                    <h6 class="section-header"><i class="fe fe-droplet mr-2" style="color:#f59e0b;"></i>Water Cut Trend (%)</h6>
                    <div class="chart-container">
                        <canvas id="waterCutChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card" style="border-radius:12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <div class="card-body">
                    <h6 class="section-header"><i class="fe fe-thermometer mr-2" style="color:#06b6d4;"></i>Wellhead Pressure Trend (bar)</h6>
                    <div class="chart-container">
                        <canvas id="pressureChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ===== Main Content Tabs ===== --}}
    <div class="card" style="border-radius:12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
        <div class="card-header" style="border-bottom: none; padding-bottom: 0;">
            <ul class="nav nav-tabs card-header-tabs" id="wellTabs">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#tab-tests">
                        <i class="fe fe-file-text mr-1"></i>MPFM Tests
                        <span class="badge badge-secondary ml-1">{{ $mpfmTests->count() }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tab-plan-tasks">
                        <i class="fe fe-clipboard mr-1"></i>Plan Tasks
                        <span class="badge badge-secondary ml-1">{{ $planTasks->count() }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tab-tasks">
                        <i class="fe fe-check-circle mr-1"></i>Tasks
                        <span class="badge badge-secondary ml-1">{{ $tasks->count() }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tab-wireline">
                        <i class="fe fe-git-branch mr-1"></i>Wire Line
                        <span class="badge badge-secondary ml-1">{{ $wireline['stats']['total'] }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tab-info">
                        <i class="fe fe-info mr-1"></i>Well Info
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">

                {{-- ===== Tab: MPFM Tests ===== --}}
                <div class="tab-pane fade show active" id="tab-tests">
                    @if($mpfmTests->isEmpty())
                        <div class="no-data-placeholder">
                            <i class="fe fe-inbox"></i>
                            <p>No MPFM tests recorded for this well yet.</p>
                        </div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-hover table-tasks mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Shift</th>
                                    <th>Oil Rate<br><small class="text-muted">m³/d</small></th>
                                    <th>Gas Rate<br><small class="text-muted">10³m³/d</small></th>
                                    <th>Water Cut<br><small class="text-muted">%</small></th>
                                    <th>Pressure<br><small class="text-muted">bar</small></th>
                                    <th>Temp<br><small class="text-muted">°C</small></th>
                                    <th>GOR<br><small class="text-muted">m³/m³</small></th>
                                    <th>Duration<br><small class="text-muted">min</small></th>
                                    <th>Status</th>
                                    <th>Operator</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mpfmTests as $test)
                                @php
                                    $statusColor = match($test->status) {
                                        'approved'             => '#10b981',
                                        'approved_by_department' => '#3b82f6',
                                        'submitted'            => '#f59e0b',
                                        'rejected'             => '#ef4444',
                                        default                => '#9ca3af',
                                    };
                                    $rowClass = match($test->status) {
                                        'approved'             => 'test-approved',
                                        'approved_by_department' => 'test-submitted',
                                        'submitted'            => 'test-submitted',
                                        'rejected'             => 'test-rejected',
                                        default                => 'test-draft',
                                    };
                                @endphp
                                <tr class="{{ $rowClass }}" style="cursor:pointer;" onclick="window.location='{{ route('mpfm_tests.show', $test->id) }}'">
                                    <td><small class="text-muted">#{{ $test->id }}</small></td>
                                    <td>
                                        <strong>{{ $test->test_date?->format('Y-m-d') ?? '—' }}</strong>
                                    </td>
                                    <td><small>{{ ucfirst($test->shift ?? '—') }}</small></td>
                                    <td>
                                        <strong style="color: #10b981;">
                                            {{ $test->oil_rate !== null ? number_format($test->oil_rate, 2) : '—' }}
                                        </strong>
                                    </td>
                                    <td>{{ $test->gas_rate !== null ? number_format($test->gas_rate, 2) : '—' }}</td>
                                    <td>
                                        @if($test->water_cut !== null)
                                        <span style="color: {{ $test->water_cut > 50 ? '#ef4444' : ($test->water_cut > 30 ? '#f59e0b' : '#10b981') }}">
                                            {{ number_format($test->water_cut, 1) }}%
                                        </span>
                                        @else —
                                        @endif
                                    </td>
                                    <td>{{ $test->pressure !== null ? number_format($test->pressure, 1) : '—' }}</td>
                                    <td>{{ $test->temperature !== null ? number_format($test->temperature, 1) : '—' }}</td>
                                    <td>{{ $test->gor !== null ? number_format($test->gor, 0) : '—' }}</td>
                                    <td>{{ $test->test_duration_minutes ?? '—' }}</td>
                                    <td>
                                        <span class="badge" style="background: {{ $statusColor }}; color:white; font-size:0.7rem; padding: 4px 8px; border-radius:20px;">
                                            {{ str_replace('_', ' ', ucfirst($test->status ?? 'draft')) }}
                                        </span>
                                    </td>
                                    <td><small class="text-muted">{{ $test->user?->name ?? '—' }}</small></td>
                                    <td>
                                        <a href="{{ route('mpfm_tests.show', $test->id) }}" class="btn btn-xs btn-outline-primary" onclick="event.stopPropagation();">
                                            <i class="fe fe-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>

                {{-- ===== Tab: Plan Tasks ===== --}}
                <div class="tab-pane fade" id="tab-plan-tasks">
                    @if($planTasks->isEmpty())
                        <div class="no-data-placeholder">
                            <i class="fe fe-clipboard"></i>
                            <p>No plan tasks linked to this well.</p>
                        </div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-hover table-tasks mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Well</th>
                                    <th>Department</th>
                                    <th>Scheduled Date</th>
                                    <th>Category</th>
                                    <th>WHFP Required</th>
                                    <th>WHFP Tested</th>
                                    <th>Status</th>
                                    <th>Operator</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($planTasks as $pt)
                                <tr>
                                    <td><small class="text-muted">#{{ $pt->id }}</small></td>
                                    <td>
                                        <strong>{{ $pt->well_full_name ?? $pt->task_identifier }}</strong>
                                        @if($pt->string_number)<br><small class="text-muted">String: {{ $pt->string_number }}</small>@endif
                                    </td>
                                    <td>
                                        @if($pt->department)
                                        <a href="{{ route('dept.dashboard', $pt->department->code) }}" onclick="event.stopPropagation();">
                                            {{ $pt->department->name }}
                                        </a>
                                        @else —
                                        @endif
                                    </td>
                                    <td>{{ $pt->scheduled_date?->format('Y-m-d') ?? '—' }}</td>
                                    <td><small>{{ $pt->category ?? $pt->sub_category ?? '—' }}</small></td>
                                    <td>{{ $pt->required_whfp ?? '—' }}</td>
                                    <td>{{ $pt->tested_whfp ?? '—' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $pt->status_badge_class }}">
                                            {{ ucfirst($pt->completion_status ?? 'pending') }}
                                        </span>
                                    </td>
                                    <td><small class="text-muted">{{ $pt->assignedOperator?->name ?? '—' }}</small></td>
                                    <td>
                                        <a href="{{ route('surveillance.viewTask', $pt->id) }}" class="btn btn-xs btn-outline-primary">
                                            <i class="fe fe-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>

                {{-- ===== Tab: Tasks (new system) ===== --}}
                <div class="tab-pane fade" id="tab-tasks">
                    @if($tasks->isEmpty())
                        <div class="no-data-placeholder">
                            <i class="fe fe-check-circle"></i>
                            <p>No tasks (new system) linked to this well.</p>
                        </div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-hover table-tasks mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>Department</th>
                                    <th>State</th>
                                    <th>Priority</th>
                                    <th>Created</th>
                                    <th>Operator</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tasks as $task)
                                <tr style="cursor:pointer;" onclick="window.location='{{ route('tasks.show', $task->id) }}'">
                                    <td><small class="text-muted">#{{ $task->id }}</small></td>
                                    <td><strong>{{ $task->title }}</strong></td>
                                    <td>
                                        @if($task->department)
                                        <a href="{{ route('dept.dashboard', $task->department->code) }}" onclick="event.stopPropagation();">
                                            {{ $task->department->name }}
                                        </a>
                                        @else —
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-secondary" style="font-size:0.72rem;">
                                            {{ str_replace('_', ' ', $task->state?->value ?? '—') }}
                                        </span>
                                    </td>
                                    <td><small>{{ ucfirst($task->priority ?? '—') }}</small></td>
                                    <td><small class="text-muted">{{ $task->created_at->format('Y-m-d') }}</small></td>
                                    <td><small class="text-muted">{{ $task->assignedTo?->name ?? '—' }}</small></td>
                                    <td>
                                        <a href="{{ route('tasks.show', $task->id) }}" class="btn btn-xs btn-outline-primary" onclick="event.stopPropagation();">
                                            <i class="fe fe-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>

                @include('oil_strings._wireline_tab', ['wireline' => $wireline])

                {{-- ===== Tab: Well Info ===== --}}
                <div class="tab-pane fade" id="tab-info">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="section-header">Well Identification</h6>
                            <dl class="info-grid row">
                                <dt class="col-5">Name</dt>
                                <dd class="col-7">{{ $oilString->name }}</dd>

                                <dt class="col-5">Code</dt>
                                <dd class="col-7"><code>{{ $oilString->code }}</code></dd>

                                <dt class="col-5">Status</dt>
                                <dd class="col-7">
                                    <span class="status-badge status-{{ $oilString->status }}">{{ ucfirst($oilString->status) }}</span>
                                </dd>

                                <dt class="col-5">Tower</dt>
                                <dd class="col-7">
                                    <a href="{{ route('towers.show', $oilString->tower->id) }}">
                                        {{ $oilString->tower->name }} ({{ $oilString->tower->code }})
                                    </a>
                                </dd>

                                <dt class="col-5">Oil Field</dt>
                                <dd class="col-7">
                                    <a href="{{ route('oil_fields.show', $oilString->tower->oilField->id) }}">
                                        {{ $oilString->tower->oilField->name }} ({{ $oilString->tower->oilField->code }})
                                    </a>
                                </dd>

                                <dt class="col-5">Description</dt>
                                <dd class="col-7">{{ $oilString->description ?? 'N/A' }}</dd>

                                <dt class="col-5">Created</dt>
                                <dd class="col-7">{{ $oilString->created_at->format('Y-m-d H:i') }}</dd>
                            </dl>
                        </div>

                        @if($approvedTests->isNotEmpty())
                        <div class="col-md-6">
                            <h6 class="section-header">Latest Approved Test Details</h6>
                            @php $lt = $approvedTests->first(); @endphp
                            <dl class="info-grid row">
                                <dt class="col-6">Test Date</dt>
                                <dd class="col-6">{{ $lt->test_date?->format('Y-m-d') ?? '—' }}</dd>

                                <dt class="col-6">Oil Rate (Qo)</dt>
                                <dd class="col-6">{{ $lt->oil_rate !== null ? number_format($lt->oil_rate, 2) . ' m³/d' : '—' }}</dd>

                                <dt class="col-6">Gas Rate (Qg)</dt>
                                <dd class="col-6">{{ $lt->gas_rate !== null ? number_format($lt->gas_rate, 2) . ' 10³m³/d' : '—' }}</dd>

                                <dt class="col-6">Water Cut (BSW)</dt>
                                <dd class="col-6">{{ $lt->water_cut !== null ? number_format($lt->water_cut, 2) . '%' : '—' }}</dd>

                                <dt class="col-6">GOR</dt>
                                <dd class="col-6">{{ $lt->gor !== null ? number_format($lt->gor, 1) . ' m³/m³' : '—' }}</dd>

                                <dt class="col-6">WHFP (Pressure)</dt>
                                <dd class="col-6">{{ $lt->pressure !== null ? number_format($lt->pressure, 1) . ' bar' : '—' }}</dd>

                                <dt class="col-6">Temperature</dt>
                                <dd class="col-6">{{ $lt->temperature !== null ? number_format($lt->temperature, 1) . ' °C' : '—' }}</dd>

                                @if($lt->choke_size)
                                <dt class="col-6">Choke Size</dt>
                                <dd class="col-6">{{ $lt->choke_size }}</dd>
                                @endif

                                @if($lt->bsw)
                                <dt class="col-6">BSW (SC)</dt>
                                <dd class="col-6">{{ number_format($lt->bsw, 2) }}%</dd>
                                @endif

                                @if($lt->glr)
                                <dt class="col-6">GLR</dt>
                                <dd class="col-6">{{ number_format($lt->glr, 2) }}</dd>
                                @endif

                                @if($lt->gvf)
                                <dt class="col-6">GVF</dt>
                                <dd class="col-6">{{ number_format($lt->gvf, 2) }}</dd>
                                @endif

                                @if($lt->data_quality_score)
                                <dt class="col-6">Data Quality Score</dt>
                                <dd class="col-6">{{ number_format($lt->data_quality_score, 1) }}%</dd>
                                @endif

                                <dt class="col-6">Approved By</dt>
                                <dd class="col-6">
                                    {{ $lt->approved_by_surveillance_at ? 'Surveillance (' . $lt->approved_by_surveillance_at->format('Y-m-d') . ')' : ($lt->approved_by_department_at ? 'Dept. (' . $lt->approved_by_department_at->format('Y-m-d') . ')' : '—') }}
                                </dd>
                            </dl>
                            <a href="{{ route('mpfm_tests.show', $lt->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fe fe-external-link mr-1"></i>View Full Test #{{ $lt->id }}
                            </a>
                        </div>
                        @endif
                    </div>
                </div>

            </div>{{-- end tab-content --}}
        </div>
    </div>

</div>
@endsection

@section('js')
@if(count($chartDates) > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    const dates      = @json($chartDates);
    const oilRate    = @json($chartOilRate);
    const gasRate    = @json($chartGasRate);
    const waterCut   = @json($chartWaterCut);
    const pressure   = @json($chartPressure);
    const liftData   = @json($liftCurveData);

    const baseOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'top', labels: { font: { size: 11 }, padding: 10 } },
            tooltip: { mode: 'index', intersect: false }
        },
        scales: {
            x: { ticks: { font: { size: 10 }, maxRotation: 45 }, grid: { display: false } },
            y: { ticks: { font: { size: 10 } }, beginAtZero: false, grid: { color: 'rgba(0,0,0,0.04)' } }
        }
    };

    // Production Trend Chart
    new Chart(document.getElementById('productionTrendChart'), {
        type: 'line',
        data: {
            labels: dates,
            datasets: [
                {
                    label: 'Oil Rate (m³/d)',
                    data: oilRate,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16,185,129,0.08)',
                    tension: 0.3,
                    fill: true,
                    pointRadius: 4,
                    yAxisID: 'y'
                },
                {
                    label: 'Gas Rate (10³m³/d)',
                    data: gasRate,
                    borderColor: '#84cc16',
                    backgroundColor: 'rgba(132,204,22,0.0)',
                    tension: 0.3,
                    fill: false,
                    pointRadius: 3,
                    borderDash: [4, 3],
                    yAxisID: 'y2'
                }
            ]
        },
        options: {
            ...baseOptions,
            scales: {
                x: baseOptions.scales.x,
                y: { ...baseOptions.scales.y, position: 'left', title: { display: true, text: 'Oil Rate (m³/d)', font: { size: 10 } } },
                y2: { ...baseOptions.scales.y, position: 'right', title: { display: true, text: 'Gas Rate (10³m³/d)', font: { size: 10 } }, grid: { display: false } }
            }
        }
    });

    // Lift Curve Chart (scatter)
    if (liftData.length > 0) {
        new Chart(document.getElementById('liftCurveChart'), {
            type: 'scatter',
            data: {
                datasets: [{
                    label: 'Qo vs WHFP',
                    data: liftData,
                    backgroundColor: 'rgba(139,92,246,0.7)',
                    borderColor: '#8b5cf6',
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => `WHFP: ${ctx.parsed.x} bar  |  Qo: ${ctx.parsed.y} m³/d`
                        }
                    }
                },
                scales: {
                    x: { title: { display: true, text: 'WHFP (bar)', font: { size: 10 } }, grid: { color: 'rgba(0,0,0,0.04)' } },
                    y: { title: { display: true, text: 'Oil Rate (m³/d)', font: { size: 10 } }, grid: { color: 'rgba(0,0,0,0.04)' } }
                }
            }
        });
    }

    // Water Cut Chart
    new Chart(document.getElementById('waterCutChart'), {
        type: 'line',
        data: {
            labels: dates,
            datasets: [{
                label: 'Water Cut (%)',
                data: waterCut,
                borderColor: '#f59e0b',
                backgroundColor: 'rgba(245,158,11,0.1)',
                tension: 0.3,
                fill: true,
                pointRadius: 4
            }]
        },
        options: {
            ...baseOptions,
            scales: {
                x: baseOptions.scales.x,
                y: { ...baseOptions.scales.y, min: 0, max: 100, title: { display: true, text: '%', font: { size: 10 } } }
            }
        }
    });

    // Pressure Chart
    new Chart(document.getElementById('pressureChart'), {
        type: 'line',
        data: {
            labels: dates,
            datasets: [{
                label: 'WHFP (bar)',
                data: pressure,
                borderColor: '#06b6d4',
                backgroundColor: 'rgba(6,182,212,0.1)',
                tension: 0.3,
                fill: true,
                pointRadius: 4
            }]
        },
        options: {
            ...baseOptions,
            scales: {
                x: baseOptions.scales.x,
                y: { ...baseOptions.scales.y, title: { display: true, text: 'bar', font: { size: 10 } } }
            }
        }
    });

})();
</script>
@endif
@endsection
