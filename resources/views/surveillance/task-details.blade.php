@extends('layouts.master')

@section('title', 'Task Details - ' . ($task->well_full_name ?? 'Task #' . $task->id))

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-leftheader">
            <h4 class="page-title mb-0">
                <i class="fe fe-eye mr-2"></i>{{ $task->well_full_name ?? 'Task #' . $task->id }}
            </h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}"><i class="fe fe-home mr-2 fs-14"></i>Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('surveillance.dashboard') }}">Surveillance Dashboard</a></li>
                <li class="breadcrumb-item active">Task Details</li>
            </ol>
        </div>
        <div class="page-rightheader">
            <a href="{{ route('surveillance.dashboard') }}" class="btn btn-outline-primary">
                <i class="fe fe-arrow-left mr-2"></i>Back to Dashboard
            </a>
        </div>
    </div>

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
                            <th width="40%">Task Number:</th>
                            <td><strong>{{ $task->sno ?? 'N/A' }}</strong></td>
                        </tr>
                        <tr>
                            <th>Well Name:</th>
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
                            <th>Department:</th>
                            <td><span class="badge badge-primary">{{ ucfirst($task->category ?? '-') }}</span></td>
                        </tr>
                        <tr>
                            <th>Operation Type:</th>
                            <td>{{ ucfirst($task->sub_category ?? '-') }}</td>
                        </tr>
                        <tr>
                            <th>Assigned Operator:</th>
                            <td>{{ $task->operator?->name ?? 'Unassigned' }}</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                <span class="badge badge-{{ $task->completion_status === 'completed' ? 'success' : ($task->completion_status === 'pending' ? 'warning' : 'info') }}">
                                    {{ ucfirst($task->completion_status) }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Timeline & Dates -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Timeline & Dates</h5>
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
                            <th>Priority:</th>
                            <td>
                                <span class="badge badge-{{ $task->priority === 'high' || $task->priority === 'top' ? 'danger' : 'info' }}">
                                    {{ ucfirst($task->priority ?? 'normal') }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Plan:</th>
                            <td>{{ $task->monthlyPlan?->plan_name ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- MPFM Test Data (if exists) -->
    @if($task->mpfmTest)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">MPFM Test Data</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Test Date:</th>
                                    <td>{{ $task->mpfmTest->test_date?->format('M d, Y') ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Test Status:</th>
                                    <td><span class="badge badge-{{ $task->mpfmTest->status === 'approved' ? 'success' : 'warning' }}">{{ ucfirst($task->mpfmTest->status) }}</span></td>
                                </tr>
                                <tr>
                                    <th>Oil Rate:</th>
                                    <td>{{ number_format($task->mpfmTest->oil_rate ?? 0, 2) }} STB/d</td>
                                </tr>
                                <tr>
                                    <th>Gas Rate:</th>
                                    <td>{{ number_format($task->mpfmTest->gas_rate ?? 0, 2) }} Mscf/d</td>
                                </tr>
                                <tr>
                                    <th>Water Cut:</th>
                                    <td>{{ number_format($task->mpfmTest->water_cut ?? 0, 2) }}%</td>
                                </tr>
                                <tr>
                                    <th>Pressure:</th>
                                    <td>{{ number_format($task->mpfmTest->pressure ?? 0, 2) }} psia</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Temperature:</th>
                                    <td>{{ number_format($task->mpfmTest->temperature ?? 0, 2) }} °F</td>
                                </tr>
                                <tr>
                                    <th>GOR:</th>
                                    <td>{{ number_format($task->mpfmTest->calculated_kpis['gor'] ?? 0, 2) }} Scf/STB</td>
                                </tr>
                                <tr>
                                    <th>BSW:</th>
                                    <td>{{ number_format($task->mpfmTest->calculated_kpis['bsw'] ?? 0, 2) }}%</td>
                                </tr>
                                <tr>
                                    <th>WLR:</th>
                                    <td>{{ number_format($task->mpfmTest->calculated_kpis['wlr'] ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Oil Density:</th>
                                    <td>{{ number_format($task->mpfmTest->calculated_kpis['po'] ?? 0, 2) }} kg/m³</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="mt-3" style="display: flex; gap: 10px; flex-wrap: wrap;">
                        @if($task->mpfmTest->test_data)
                        <a href="{{ route('mpfm-tests.view', $task->mpfmTest) }}" class="btn btn-success" target="_blank" title="View Charts">
                            <i class="fe fe-bar-chart-2 mr-2"></i>Charts
                        </a>
                        @endif
                        <a href="{{ route('mpfm_tests.show', $task->mpfmTest) }}" class="btn btn-primary" title="View Full Test Report">
                            <i class="fe fe-eye mr-2"></i>Details
                        </a>
                        <!-- Validation & History -->
                        <a href="{{ route('mpfm_tests.show_history', $task->mpfmTest->id) }}" class="btn btn-warning" title="View Validation & History">
                            <i class="fe fe-check-circle mr-2"></i>Validation
                        </a>
                        <!-- String Analysis -->
                        <a href="{{ route('mpfm_tests.string_analysis', $task->mpfmTest->oil_string_id) }}" class="btn btn-info" title="View String Analysis">
                            <i class="fe fe-trending-up mr-2"></i>Analysis
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- State History -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Task Workflow History</h5>
                </div>
                <div class="card-body">
                    @if($task->stateHistories->count() > 0)
                    <div class="timeline">
                        @foreach($task->stateHistories->sortByDesc('created_at') as $history)
                        <div class="timeline-item" style="margin-bottom: 20px;">
                            <div style="display: flex; gap: 15px;">
                                <div style="width: 40px; height: 40px; border-radius: 50%; background: #667eea; display: flex; align-items: center; justify-content: center; color: white; flex-shrink: 0;">
                                    <i class="fe fe-check-circle"></i>
                                </div>
                                <div>
                                    <h6 style="margin: 0; font-weight: 600;">{{ str_replace('_', ' ', ucfirst($history->to_state)) }}</h6>
                                    <p style="margin: 5px 0 0 0; color: #666; font-size: 0.9rem;">
                                        {{ $history->created_at->format('M d, Y H:i') }}
                                        @if($history->changedBy)
                                            <br><small>by <strong>{{ $history->changedBy->name }}</strong></small>
                                        @endif
                                    </p>
                                    @if($history->notes)
                                    <p style="margin: 8px 0 0 0; font-size: 0.9rem; color: #555;">{{ $history->notes }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-muted">No history available</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
