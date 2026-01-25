@extends('layouts.master')

@section('title', 'Task Archive - ' . ($task->well_full_name ?? 'Task #' . $task->id))

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-leftheader">
            <h4 class="page-title mb-0">
                <i class="fe fe-archive mr-2"></i>{{ $task->well_full_name ?? 'Task #' . $task->id }}
            </h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}"><i class="fe fe-home mr-2 fs-14"></i>Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('task-archive.index') }}">Tasks Archive</a></li>
                <li class="breadcrumb-item active">{{ $task->well_full_name ?? 'Task #' . $task->id }}</li>
            </ol>
        </div>
        <div class="page-rightheader">
            <a href="{{ route('task-archive.index') }}" class="btn btn-outline-primary">
                <i class="fe fe-arrow-left mr-2"></i>Back to Archive
            </a>
        </div>
    </div>
{{-- @dd($task) --}}
    <div class="row">
        <!-- Task Information -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Task Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">Well Name:</th>
                            <td>{{ $task->well_full_name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Tower:</th>
                            <td>{{ $task->towerModel?->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Oil Field:</th>
                            <td>{{ $task->towerModel?->oilField?->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Oil String:</th>
                            <td>{{ $task->oilString?->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Operator:</th>
                            <td>{{ $task->operator?->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                <span class="badge badge-{{ $task->completion_status === 'completed' ? 'success' : 'danger' }}">
                                    {{ ucfirst($task->completion_status) }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Dates & Timeline -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Timeline</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">Scheduled Date:</th>
                            <td>{{ $task->scheduled_date?->format('M d, Y') ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Started At:</th>
                            <td>{{ $task->started_at?->format('M d, Y H:i') ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Completed At:</th>
                            <td>{{ $task->completed_at?->format('M d, Y H:i') ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Duration:</th>
                            <td>
                                @if($task->started_at && $task->completed_at)
                                    {{ $task->completed_at->diff($task->started_at)->format('%h hours %i minutes') }}
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Plan:</th>
                            <td>
                                @if($task->monthlyPlan)
                                <a href="{{ route('monthly-plans.show', $task->monthlyPlan) }}">
                                    {{ $task->monthlyPlan->plan_name }}
                                </a>
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Test Timing Information -->
    @if($task->mpfmTest)
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary">
                    <h5 class="card-title mb-0" style="color: white;">
                        <i class="fe fe-clock mr-2"></i>Test Timing & Duration
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div style="padding: 1rem; background: rgba(78, 115, 223, 0.05); border-radius: 8px;">
                                <h6 class="text-muted mb-2">Test Started At</h6>
                                <p style="font-weight: 600; font-size: 1rem;">
                                    {{ $task->mpfmTest->test_started_at?->format('M d, Y H:i:s') ?? 'N/A' }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div style="padding: 1rem; background: rgba(78, 115, 223, 0.05); border-radius: 8px;">
                                <h6 class="text-muted mb-2">Test Completed At</h6>
                                <p style="font-weight: 600; font-size: 1rem;">
                                    {{ $task->mpfmTest->test_completed_at?->format('M d, Y H:i:s') ?? 'N/A' }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div style="padding: 1rem; background: rgba(52, 211, 153, 0.05); border-radius: 8px;">
                                <h6 class="text-muted mb-2">Total Duration</h6>
                                <p style="font-weight: 600; font-size: 1rem;">
                                    {{ round($task->mpfmTest->test_duration_hours ?? 0, 2) }} hours
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div style="padding: 1rem; background: rgba(59, 130, 246, 0.05); border-radius: 8px;">
                                <h6 class="text-muted mb-2">Stable Duration</h6>
                                <p style="font-weight: 600; font-size: 1rem;">
                                    {{ round($task->mpfmTest->test_stable_duration_hours ?? 0, 2) }} hours
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- MPFM Test Results Summary -->
    @if($task->mpfmTest)
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">MPFM Test Results Summary</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-3">Production Data</h6>
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <th width="50%">Oil Rate (SC):</th>
                                    <td>{{ number_format($task->mpfmTest->qo_sc ?? 0, 2) }} STB/d</td>
                                </tr>
                                <tr>
                                    <th>Gas Rate (SC):</th>
                                    <td>{{ number_format($task->mpfmTest->qg_sc ?? 0, 2) }} MMScf/d</td>
                                </tr>
                                <tr>
                                    <th>Water Rate (SC):</th>
                                    <td>{{ number_format($task->mpfmTest->qw_sc ?? 0, 2) }} STB/d</td>
                                </tr>
                                <tr>
                                    <th>Temperature:</th>
                                    <td>{{ number_format($task->mpfmTest->temperature ?? 0, 2) }} °C</td>
                                </tr>
                                <tr>
                                    <th>Pressure:</th>
                                    <td>{{ number_format($task->mpfmTest->pressure ?? 0, 2) }} bara</td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h6 class="mb-3">Key Ratios & Metrics</h6>
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <th width="50%">GOR:</th>
                                    <td>{{ number_format($task->mpfmTest->gor ?? 0, 2) }} Scf/STB</td>
                                </tr>
                                <tr>
                                    <th>WLR:</th>
                                    <td>{{ number_format($task->mpfmTest->wlr ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>BSW:</th>
                                    <td>{{ number_format($task->mpfmTest->bsw ?? 0, 2) }}%</td>
                                </tr>
                                <tr>
                                    <th>GVF:</th>
                                    <td>{{ number_format($task->mpfmTest->gvf ?? 0, 2) }}%</td>
                                </tr>
                                <tr>
                                    <th>Z-Factor:</th>
                                    <td>{{ number_format($task->mpfmTest->z_factor ?? 0, 4) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="mt-3" style="display: flex; gap: 10px;">
                        @if($task->mpfmTest->log_file_path)
                        <a href="{{ route('mpfm_tests.download_log', $task->mpfmTest) }}" class="btn btn-sm btn-outline-info">
                            <i class="fe fe-download mr-2"></i>Download Log File
                        </a>
                        @endif
                        @if($task->mpfmTest->test_data)
                        <a href="{{ route('mpfm-tests.view', $task->mpfmTest) }}" class="btn btn-sm btn-success" target="_blank">
                            <i class="fe fe-bar-chart-2 mr-2"></i>View Charts
                        </a>
                        @endif
                        <a href="{{ route('mpfm_tests.show', $task->mpfmTest) }}" class="btn btn-sm btn-primary">
                            <i class="fe fe-external-link mr-2"></i>View Full Test Report
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Metrics Display -->
    @if($task->mpfmTest->detailed_metrics)
    <div class="row mt-4">
        <div class="col-md-12">
            <x-detailed-metrics-display :metrics="$task->mpfmTest->detailed_metrics" />
        </div>
    </div>
    @endif
    @endif
</div>
@endsection
