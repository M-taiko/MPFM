@extends('layouts.master')

@section('title', 'MPFM Operational Plan Details')

@section('content')
<div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">MPFM Operational Plan: {{ $mpfmOperationalPlan->name }}</h3>
                <div class="card-options">
                    @can('update', $mpfmOperationalPlan)
                    <a href="{{ route('mpfm_operational_plans.edit', $mpfmOperationalPlan->id) }}" class="btn btn-primary btn-sm">
                        <i class="fe fe-edit"></i> Edit
                    </a>
                    @endcan
                    <a href="{{ route('mpfm_operational_plans.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fe fe-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr>
                                <th width="40%">Plan Name:</th>
                                <td>{{ $mpfmOperationalPlan->name }}</td>
                            </tr>
                            <tr>
                                <th>Created By:</th>
                                <td>{{ $mpfmOperationalPlan->creator->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Assigned Operator:</th>
                                <td>{{ $mpfmOperationalPlan->assignedUser->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Oil Field:</th>
                                <td>
                                    <a href="{{ route('oil_fields.show', $mpfmOperationalPlan->oilField->id) }}">
                                        {{ $mpfmOperationalPlan->oilField->name }} ({{ $mpfmOperationalPlan->oilField->code }})
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <th>Tower:</th>
                                <td>
                                    <a href="{{ route('towers.show', $mpfmOperationalPlan->tower->id) }}">
                                        {{ $mpfmOperationalPlan->tower->name }} ({{ $mpfmOperationalPlan->tower->code }})
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <th>Plan Date:</th>
                                <td>{{ $mpfmOperationalPlan->plan_date->format('Y-m-d') }}</td>
                            </tr>
                            <tr>
                                <th>Shift:</th>
                                <td>{{ ucfirst($mpfmOperationalPlan->shift ?? 'N/A') }}</td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td>
                                    @if($mpfmOperationalPlan->status == 'active')
                                        <span class="badge bg-success">Active</span>
                                    @elseif($mpfmOperationalPlan->status == 'draft')
                                        <span class="badge bg-secondary">Draft</span>
                                    @elseif($mpfmOperationalPlan->status == 'completed')
                                        <span class="badge bg-info">Completed</span>
                                    @else
                                        <span class="badge bg-danger">Cancelled</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Description:</th>
                                <td>{{ $mpfmOperationalPlan->description ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Notes:</th>
                                <td>{{ $mpfmOperationalPlan->notes ?? 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-12">
                        <h4>Assigned Oil Strings ({{ count($mpfmOperationalPlan->assigned_oil_string_ids ?? []) }})</h4>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Code</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($assignedStrings as $oilString)
                                    <tr>
                                        <td>{{ $oilString->name }}</td>
                                        <td>{{ $oilString->code }}</td>
                                        <td>
                                            @if($oilString->status == 'active')
                                                <span class="badge bg-success">Active</span>
                                            @elseif($oilString->status == 'inactive')
                                                <span class="badge bg-secondary">Inactive</span>
                                            @else
                                                <span class="badge bg-warning">Maintenance</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('oil_strings.show', $oilString->id) }}" class="btn btn-sm btn-info">View</a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No oil strings assigned.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-12">
                        <h4>MPFM Tests ({{ $mpfmOperationalPlan->mpfmTests->count() }})</h4>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Test Date</th>
                                        <th>Shift</th>
                                        <th>Operator</th>
                                        <th>Oil String</th>
                                        <th>Oil Rate</th>
                                        <th>Gas Rate</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($mpfmOperationalPlan->mpfmTests as $test)
                                    <tr>
                                        <td>{{ $test->test_date->format('Y-m-d') }}</td>
                                        <td>{{ ucfirst($test->shift) }}</td>
                                        <td>{{ $test->user->name ?? 'N/A' }}</td>
                                        <td>{{ $test->oilString->name ?? 'N/A' }}</td>
                                        <td>{{ number_format($test->oil_rate ?? 0, 2) }}</td>
                                        <td>{{ number_format($test->gas_rate ?? 0, 2) }}</td>
                                        <td>
                                            @if($test->status == 'approved')
                                                <span class="badge bg-success">Approved</span>
                                            @elseif($test->status == 'submitted')
                                                <span class="badge bg-info">Submitted</span>
                                            @elseif($test->status == 'draft')
                                                <span class="badge bg-secondary">Draft</span>
                                            @else
                                                <span class="badge bg-danger">Rejected</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('mpfm_tests.show', $test->id) }}" class="btn btn-sm btn-info">View</a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No MPFM tests found for this plan.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

