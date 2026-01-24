@extends('layouts.master')

@section('title', 'Test Approval')

@section('css')
<style>
    .validation-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .confidence-score {
        font-size: 2.5rem;
        font-weight: bold;
        text-align: center;
        padding: 20px;
        border-radius: 8px;
        color: white;
    }

    .confidence-pass { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); }
    .confidence-review { background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%); }
    .confidence-fail { background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%); }

    .suggestion-badge {
        display: inline-block;
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: bold;
        font-size: 0.9rem;
    }

    .suggestion-approve { background: #d4edda; color: #155724; }
    .suggestion-review { background: #fff3cd; color: #856404; }
    .suggestion-reject { background: #f8d7da; color: #721c24; }

    .rule-result {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px;
        border-left: 4px solid #ddd;
        margin-bottom: 10px;
    }

    .rule-pass { border-left-color: #28a745; background: #f8fff9; }
    .rule-warning { border-left-color: #ffc107; background: #fffbf0; }
    .rule-fail { border-left-color: #dc3545; background: #fff5f5; }

    .severity-critical { color: #dc3545; font-weight: bold; }
    .severity-major { color: #ff9800; }
    .severity-minor { color: #666; }

    .comparison-table {
        width: 100%;
        border-collapse: collapse;
    }

    .comparison-table th {
        background: #f8f9fa;
        padding: 10px;
        text-align: left;
        border-bottom: 2px solid #dee2e6;
    }

    .comparison-table td {
        padding: 10px;
        border-bottom: 1px solid #dee2e6;
    }

    .anomaly-alert {
        padding: 15px;
        border-radius: 4px;
        margin-bottom: 10px;
        border-left: 4px solid;
    }

    .anomaly-critical { background: #fff5f5; border-color: #dc3545; }
    .anomaly-warning { background: #fffbf0; border-color: #ffc107; }

    .nav-tabs .nav-link {
        color: #666;
        border: none;
        border-bottom: 3px solid transparent;
        padding: 12px 20px;
    }

    .nav-tabs .nav-link.active {
        background: transparent;
        border-bottom-color: #667eea;
        color: #667eea;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="mb-2">Test Approval & Validation</h1>
            <p class="text-muted">Test ID: {{ $mpfmTest->id }} | Operator: {{ $mpfmTest->user->name }} | Date: {{ $mpfmTest->test_date }}</p>
        </div>
    </div>

    <!-- Confidence Score Section -->
    @if($mpfmTest->latestValidation)
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="validation-card">
                <div class="confidence-score confidence-{{ $mpfmTest->latestValidation->auto_suggestion }}">
                    {{ $mpfmTest->latestValidation->confidence_score }}%
                </div>
                <p class="text-center mt-3 mb-0">
                    <span class="suggestion-badge suggestion-{{ $mpfmTest->latestValidation->auto_suggestion }}">
                        {{ ucfirst($mpfmTest->latestValidation->auto_suggestion) }}
                    </span>
                </p>
            </div>
        </div>
        <div class="col-md-9">
            <div class="validation-card">
                <h5>Validation Summary</h5>
                <p class="mb-2"><strong>Status:</strong> <span class="badge badge-{{ $mpfmTest->latestValidation->overall_status === 'pass' ? 'success' : ($mpfmTest->latestValidation->overall_status === 'warning' ? 'warning' : 'danger') }}">{{ ucfirst($mpfmTest->latestValidation->overall_status) }}</span></p>
                <p class="mb-0">{{ $mpfmTest->validation_summary }}</p>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="row">
        <div class="col-12">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#validation-results">Validation Results</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#historical-comparison">Historical Comparison</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#anomalies">Anomalies</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#test-data">Test Data</a>
                </li>
            </ul>

            <div class="tab-content" style="padding: 20px; background: white; border: 1px solid #dee2e6; border-top: none;">
                <!-- Validation Results Tab -->
                <div id="validation-results" class="tab-pane fade show active">
                    <h5 class="mb-3">Rule Execution Results</h5>
                    @forelse($mpfmTest->latestValidation->rule_results as $result)
                        <div class="rule-result rule-{{ $result['result'] }}">
                            <div>
                                <strong>{{ $result['rule_name'] }}</strong>
                                <p class="mb-0 text-muted small">{{ $result['message'] }}</p>
                            </div>
                            <div class="text-right">
                                <span class="badge badge-{{ $result['result'] === 'pass' ? 'success' : ($result['result'] === 'warning' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($result['result']) }}
                                </span>
                                <span class="severity-{{ $result['severity'] }}">
                                    {{ ucfirst($result['severity']) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted">No rules executed</p>
                    @endforelse
                </div>

                <!-- Historical Comparison Tab -->
                <div id="historical-comparison" class="tab-pane fade">
                    <h5 class="mb-3">Historical Comparison</h5>
                    @if($mpfmTest->latestValidation->historical_comparison)
                        <p><strong>Tests Compared:</strong> {{ $mpfmTest->latestValidation->historical_comparison['tests_compared'] }}</p>
                        <p><strong>Date Range:</strong> {{ $mpfmTest->latestValidation->historical_comparison['date_range']['from'] }} to {{ $mpfmTest->latestValidation->historical_comparison['date_range']['to'] }}</p>

                        <h6 class="mt-4 mb-3">Current Test vs. Historical Average</h6>
                        <table class="comparison-table">
                            <thead>
                                <tr>
                                    <th>Parameter</th>
                                    <th>Current Value</th>
                                    <th>Historical Average</th>
                                    <th>Deviation %</th>
                                    <th>Z-Score</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mpfmTest->latestValidation->historical_comparison['current_deviations'] as $param => $dev)
                                <tr>
                                    <td><strong>{{ ucfirst(str_replace('_', ' ', $param)) }}</strong></td>
                                    <td>{{ $dev['current_value'] }}</td>
                                    <td>{{ $dev['average'] }}</td>
                                    <td>
                                        <span class="badge badge-{{ abs($dev['percent_diff']) > 10 ? 'warning' : 'info' }}">
                                            {{ $dev['percent_diff'] > 0 ? '+' : '' }}{{ $dev['percent_diff'] }}%
                                        </span>
                                    </td>
                                    <td>{{ $dev['z_score'] }}</td>
                                    <td>{{ ucfirst($dev['deviation_type']) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted">Not enough historical data for comparison</p>
                    @endif
                </div>

                <!-- Anomalies Tab -->
                <div id="anomalies" class="tab-pane fade">
                    <h5 class="mb-3">Detected Anomalies</h5>
                    @if($mpfmTest->latestValidation->anomalies && count($mpfmTest->latestValidation->anomalies) > 0)
                        @foreach($mpfmTest->latestValidation->anomalies as $anomaly)
                        <div class="anomaly-alert anomaly-{{ $anomaly['severity'] }}">
                            <strong>{{ ucfirst(str_replace('_', ' ', $anomaly['parameter'])) }}</strong>
                            <span class="badge badge-{{ $anomaly['severity'] === 'critical' ? 'danger' : 'warning' }}">
                                {{ ucfirst($anomaly['severity']) }}
                            </span>
                            <p class="mb-0">{{ $anomaly['message'] }}</p>
                            <small class="text-muted">
                                Current: {{ $anomaly['current_value'] }} |
                                Expected Range: {{ $anomaly['expected_range'][0] }} - {{ $anomaly['expected_range'][1] }} |
                                Deviation: {{ $anomaly['deviation'] }}
                            </small>
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted">No anomalies detected</p>
                    @endif
                </div>

                <!-- Test Data Tab -->
                <div id="test-data" class="tab-pane fade">
                    <h5 class="mb-3">Raw Test Data</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Oil Rate:</strong> {{ $mpfmTest->oil_rate }} bbl/day</p>
                            <p><strong>Water Cut:</strong> {{ $mpfmTest->water_cut }}%</p>
                            <p><strong>GOR:</strong> {{ $mpfmTest->gor }} scf/bbl</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Pressure:</strong> {{ $mpfmTest->pressure }} psi</p>
                            <p><strong>Temperature:</strong> {{ $mpfmTest->temperature }}°F</p>
                            <p><strong>Gas Rate:</strong> {{ $mpfmTest->gas_rate }} MMSCF/day</p>
                        </div>
                    </div>
                    @if($mpfmTest->notes)
                        <p class="mt-3"><strong>Operator Notes:</strong></p>
                        <p>{{ $mpfmTest->notes }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Approval Form -->
    <div class="row mt-4">
        <div class="col-md-8">
            <div class="validation-card">
                <form action="{{ route('vx_log.approve_test', $mpfmTest->id) }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="action"><strong>Supervisor Decision</strong></label>
                        <div class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" id="approve" name="action" value="approve" required>
                            <label class="custom-control-label" for="approve">
                                Approve Test
                            </label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" id="reject" name="action" value="reject">
                            <label class="custom-control-label" for="reject">
                                Reject Test
                            </label>
                        </div>
                    </div>

                    @if($mpfmTest->latestValidation->auto_suggestion === 'reject')
                    <div class="alert alert-danger">
                        <strong>⚠️ Validation Warning:</strong> This test failed validation checks.
                        @if(auth()->user()->hasAnyRole(['mpfm_supervisor', 'surveillance_supervisor']))
                            To approve anyway, check the override box below.
                        @endif
                    </div>

                    @if(auth()->user()->hasAnyRole(['mpfm_supervisor', 'surveillance_supervisor']))
                        <div class="custom-control custom-checkbox mb-3">
                            <input type="checkbox" class="custom-control-input" id="override" name="override_validation">
                            <label class="custom-control-label" for="override">
                                Override validation and approve anyway
                            </label>
                        </div>
                    @endif
                    @endif

                    <div class="form-group">
                        <label for="notes">Supervisor Notes (Optional)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="4"></textarea>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-check"></i> Submit Decision
                        </button>
                        <a href="{{ route('supervisor.dashboard') }}" class="btn btn-secondary">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-md-4">
            <div class="validation-card">
                <h6>Quick Info</h6>
                <p class="mb-1"><strong>Operator:</strong> {{ $mpfmTest->user->name }}</p>
                <p class="mb-1"><strong>Tower:</strong> {{ $mpfmTest->tower->name }}</p>
                <p class="mb-1"><strong>String:</strong> {{ $mpfmTest->oilString->name }}</p>
                <p class="mb-1"><strong>Test Date:</strong> {{ $mpfmTest->test_date }}</p>
                <p class="mb-0"><strong>Last Validated:</strong> {{ $mpfmTest->last_validated_at ? $mpfmTest->last_validated_at->diffForHumans() : 'Never' }}</p>
            </div>
        </div>
    </div>
</div>
@else
<div class="alert alert-info">
    <strong>No validation data available yet. Running validation...</strong>
</div>
@endif
@endsection
