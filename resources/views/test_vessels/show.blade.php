@extends('layouts.master')

@section('title', 'Test Vessel Details')

@push('styles')
<link href="{{URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css')}}" rel="stylesheet">
<link href="{{URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
@endpush

@section('content')
<div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Test Vessel Details: {{ $testVessel->name }}</h3>
                <div class="card-options">
                    @can('update', $testVessel)
                    <a href="{{ route('test_vessels.edit', $testVessel->id) }}" class="btn btn-primary btn-sm">
                        <i class="fe fe-edit"></i> Edit
                    </a>
                    @endcan
                    <a href="{{ route('test_vessels.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fe fe-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr>
                                <th width="40%">Name:</th>
                                <td>{{ $testVessel->name }}</td>
                            </tr>
                            <tr>
                                <th>Code:</th>
                                <td>{{ $testVessel->code }}</td>
                            </tr>
                            <tr>
                                <th>Oil Field:</th>
                                <td>
                                    @if($testVessel->oilField)
                                    <a href="{{ route('oil_fields.show', $testVessel->oilField->id) }}">
                                        {{ $testVessel->oilField->name }}
                                    </a>
                                    @else
                                    N/A
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td>
                                    @if($testVessel->status == 'active')
                                        <span class="badge bg-success">Active</span>
                                    @elseif($testVessel->status == 'inactive')
                                        <span class="badge bg-secondary">Inactive</span>
                                    @else
                                        <span class="badge bg-warning">Maintenance</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Description:</th>
                                <td>{{ $testVessel->description ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Created At:</th>
                                <td>{{ $testVessel->created_at->format('Y-m-d H:i:s') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-12">
                        <h4>Tests ({{ $testVessel->tests->count() }})</h4>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Test Date</th>
                                        <th>Shift</th>
                                        <th>Operator</th>
                                        <th>Oil Field</th>
                                        <th>Tower</th>
                                        <th>Oil String</th>
                                        <th>Oil Rate</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($testVessel->tests()->orderBy('test_date', 'desc')->paginate(10) as $test)
                                    <tr>
                                        <td>{{ $test->test_date->format('Y-m-d') }}</td>
                                        <td>{{ ucfirst($test->shift) }}</td>
                                        <td>{{ $test->user?->name ?? 'N/A' }}</td>
                                        <td>{{ $test->oilField?->name ?? 'N/A' }}</td>
                                        <td>{{ $test->tower?->name ?? 'N/A' }}</td>
                                        <td>{{ $test->oilString?->name ?? 'N/A' }}</td>
                                        <td>{{ $test->oil_rate }}</td>
                                        <td>
                                            @if($test->status == 'draft')
                                                <span class="badge bg-secondary">Draft</span>
                                            @elseif($test->status == 'submitted')
                                                <span class="badge bg-info">Submitted</span>
                                            @elseif($test->status == 'approved')
                                                <span class="badge bg-success">Approved</span>
                                            @else
                                                <span class="badge bg-danger">Rejected</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('tv_tests.show', $test->id) }}" class="btn btn-sm btn-info">View</a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No tests found.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center">
                            {{ $testVessel->tests()->paginate(10)->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
