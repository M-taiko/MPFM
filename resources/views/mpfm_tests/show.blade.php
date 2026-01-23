@extends('layouts.master')

@section('title', 'MPFM Test Details')

@section('content')
<div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">MPFM Test #{{ $mpfmTest->id }}</h3>
                <div class="card-options">
                    @can('update', $mpfmTest)
                    <a href="{{ route('mpfm_tests.edit', $mpfmTest->id) }}" class="btn btn-primary btn-sm">
                        <i class="fe fe-edit"></i> Edit
                    </a>
                    @endcan
                    <a href="{{ route('mpfm_tests.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fe fe-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Test Information</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th width="40%">Test Date:</th>
                                <td>{{ $mpfmTest->test_date->format('Y-m-d') }}</td>
                            </tr>
                            <tr>
                                <th>Shift:</th>
                                <td>{{ ucfirst($mpfmTest->shift) }}</td>
                            </tr>
                            <tr>
                                <th>Operator:</th>
                                <td>{{ $mpfmTest->user->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Oil Field:</th>
                                <td>
                                    <a href="{{ route('oil_fields.show', $mpfmTest->oilField->id) }}">
                                        {{ $mpfmTest->oilField->name }} ({{ $mpfmTest->oilField->code }})
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <th>Tower:</th>
                                <td>
                                    <a href="{{ route('towers.show', $mpfmTest->tower->id) }}">
                                        {{ $mpfmTest->tower->name }} ({{ $mpfmTest->tower->code }})
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <th>Oil String:</th>
                                <td>
                                    <a href="{{ route('oil_strings.show', $mpfmTest->oilString->id) }}">
                                        {{ $mpfmTest->oilString->name }} ({{ $mpfmTest->oilString->code }})
                                    </a>
                                </td>
                            </tr>
                            @if($mpfmTest->mpfmOperationalPlan)
                            <tr>
                                <th>Operational Plan:</th>
                                <td>
                                    <a href="{{ route('mpfm_operational_plans.show', $mpfmTest->mpfmOperationalPlan->id) }}">
                                        {{ $mpfmTest->mpfmOperationalPlan->name }}
                                    </a>
                                </td>
                            </tr>
                            @endif
                            <tr>
                                <th>Status:</th>
                                <td>
                                    @if($mpfmTest->status == 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @elseif($mpfmTest->status == 'submitted')
                                        <span class="badge bg-info">Submitted</span>
                                    @elseif($mpfmTest->status == 'draft')
                                        <span class="badge bg-secondary">Draft</span>
                                    @else
                                        <span class="badge bg-danger">Rejected</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="col-md-6">
                        <h5>Production Data</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th width="40%">Oil Rate:</th>
                                <td>{{ number_format($mpfmTest->oil_rate ?? 0, 2) }} STB/d</td>
                            </tr>
                            <tr>
                                <th>Gas Rate:</th>
                                <td>{{ number_format($mpfmTest->gas_rate ?? 0, 2) }} Mscf/d</td>
                            </tr>
                            <tr>
                                <th>Water Cut:</th>
                                <td>{{ number_format($mpfmTest->water_cut ?? 0, 2) }}%</td>
                            </tr>
                            <tr>
                                <th>Pressure:</th>
                                <td>{{ number_format($mpfmTest->pressure ?? 0, 2) }} psi</td>
                            </tr>
                            <tr>
                                <th>Temperature:</th>
                                <td>{{ number_format($mpfmTest->temperature ?? 0, 2) }} °F</td>
                            </tr>
                        </table>

                        @if($mpfmTest->calculated_kpis)
                        <h5 class="mt-4">Calculated KPIs</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th width="40%">GOR (Gas-Oil Ratio):</th>
                                <td>{{ number_format($mpfmTest->calculated_kpis['gor'] ?? 0, 2) }} Scf/STB</td>
                            </tr>
                            <tr>
                                <th>WLR (Water-Liquid Ratio):</th>
                                <td>{{ number_format($mpfmTest->calculated_kpis['wlr'] ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <th>BSW (Basic Sediment & Water):</th>
                                <td>{{ number_format($mpfmTest->calculated_kpis['bsw'] ?? 0, 2) }}%</td>
                            </tr>
                            <tr>
                                <th>Total Production Rate:</th>
                                <td>{{ number_format($mpfmTest->calculated_kpis['total_production_rate'] ?? 0, 2) }}</td>
                            </tr>
                            @if(isset($mpfmTest->calculated_kpis['productivity_index']))
                            <tr>
                                <th>Productivity Index:</th>
                                <td>{{ number_format($mpfmTest->calculated_kpis['productivity_index'], 2) }}</td>
                            </tr>
                            @endif
                        </table>
                        @endif
                    </div>
                </div>

                @if($mpfmTest->notes)
                <div class="row mt-4">
                    <div class="col-md-12">
                        <h5>Notes</h5>
                        <div class="card">
                            <div class="card-body">
                                <p>{{ $mpfmTest->notes }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Supervisor Review Section -->
                @if($mpfmTest->status === 'draft' || $mpfmTest->status === 'rejected')
                <div class="row mt-4">
                    <div class="col-md-12">
                        <h5>Supervisor Review Decision</h5>
                        <div class="card border-primary">
                            <div class="card-body">
                                <form id="testReviewForm" method="POST" action="{{ route('mpfm-tests.approve', $mpfmTest->id) }}">
                                    @csrf

                                    <div class="form-group">
                                        <label><strong>Make a Decision:</strong></label>
                                        <div class="custom-control custom-radio mb-3">
                                            <input type="radio" class="custom-control-input" id="approveRadio" name="action" value="approve">
                                            <label class="custom-control-label" for="approveRadio">
                                                <i class="fe fe-check-circle text-success mr-2"></i>
                                                <strong>Approve Test</strong>
                                                <br><small class="text-muted ml-4">Test data is acceptable and valid</small>
                                            </label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" id="rejectRadio" name="action" value="reject">
                                            <label class="custom-control-label" for="rejectRadio">
                                                <i class="fe fe-x-circle text-danger mr-2"></i>
                                                <strong>Reject Test</strong>
                                                <br><small class="text-muted ml-4">Request operator to re-upload with corrections</small>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="form-group" id="remarksGroup" style="display: none;">
                                        <label for="remarks"><strong>Reason for Rejection (Required)</strong></label>
                                        <textarea class="form-control @error('remarks') is-invalid @enderror" id="remarks" name="remarks" rows="4" placeholder="Explain what needs to be corrected..."></textarea>
                                        @error('remarks')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Be specific about issues and required corrections</small>
                                    </div>

                                    <div class="form-group mt-3">
                                        <button type="submit" class="btn btn-success" id="submitBtn">
                                            <i class="fe fe-check mr-2"></i> Submit Decision
                                        </button>
                                        <a href="{{ route('monthly-plans.show', $task->monthly_plan_id) }}" class="btn btn-secondary">
                                            <i class="fe fe-arrow-left mr-2"></i> Back to Plan
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @elseif($mpfmTest->status === 'approved')
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="alert alert-success" role="alert">
                            <i class="fe fe-check-circle mr-2"></i>
                            <strong>Test Approved!</strong> This test has been approved by the supervisor.
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('input[name="action"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const remarksGroup = document.getElementById('remarksGroup');
        const remarksField = document.getElementById('remarks');

        if (this.value === 'reject') {
            remarksGroup.style.display = 'block';
            remarksField.required = true;
        } else {
            remarksGroup.style.display = 'none';
            remarksField.required = false;
            remarksField.value = '';
        }
    });
});

document.getElementById('testReviewForm')?.addEventListener('submit', function(e) {
    const action = document.querySelector('input[name="action"]:checked');
    if (!action) {
        e.preventDefault();
        alert('Please select a decision');
        return false;
    }

    if (action.value === 'reject') {
        const remarks = document.getElementById('remarks').value.trim();
        if (!remarks) {
            e.preventDefault();
            alert('Please provide a reason for rejection');
            return false;
        }
    }
});
</script>
@endpush

