@extends('layouts.master')

@section('title', 'Test Vessel Tests')

@push('styles')
<link href="{{URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css')}}" rel="stylesheet">
<link href="{{URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
@endpush

@section('content')
<div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Test Vessel Tests</h3>
                <div class="card-options">
                    @can('create', App\Models\TestVesselTest::class)
                    <a href="{{ route('tv_tests.create') }}" class="btn btn-primary btn-sm">
                        <i class="fe fe-plus"></i> Create New Test
                    </a>
                    @endcan
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-2">
                        <label>Oil Field:</label>
                        <select id="oilFieldFilter" class="form-control">
                            <option value="">All Fields</option>
                            @foreach($oilFields as $field)
                            <option value="{{ $field->id }}">{{ $field->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>Test Vessel:</label>
                        <select id="testVesselFilter" class="form-control">
                            <option value="">All Vessels</option>
                            @foreach($testVessels as $vessel)
                            <option value="{{ $vessel->id }}">{{ $vessel->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>Status:</label>
                        <select id="statusFilter" class="form-control">
                            <option value="">All Status</option>
                            <option value="draft">Draft</option>
                            <option value="submitted">Submitted</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>From Date:</label>
                        <input type="date" id="dateFromFilter" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>To Date:</label>
                        <input type="date" id="dateToFilter" class="form-control">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-secondary btn-sm w-100" id="resetFilters">
                            <i class="fe fe-refresh-cw"></i> Reset
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="tvTestsTable">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Shift</th>
                                <th>Vessel</th>
                                <th>Operator</th>
                                <th>Oil Field</th>
                                <th>Tower</th>
                                <th>String</th>
                                <th>Oil Rate</th>
                                <th>MPFM Test</th>
                                <th>Status</th>
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
@endsection

@push('scripts')
<script src="{{URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.min.js')}}"></script>
<script>
$(document).ready(function() {
    var table = $('#tvTestsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('tv_tests.index') }}',
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            data: function(d) {
                d.oil_field_id = $('#oilFieldFilter').val();
                d.test_vessel_id = $('#testVesselFilter').val();
                d.status = $('#statusFilter').val();
                d.date_from = $('#dateFromFilter').val();
                d.date_to = $('#dateToFilter').val();
            }
        },
        columns: [
            { data: 'test_date', name: 'test_date' },
            { data: 'shift', name: 'shift' },
            { data: 'vessel_name', name: 'vessel_name', orderable: false, searchable: false },
            { data: 'operator', name: 'operator', orderable: false, searchable: false },
            { data: 'oil_field_name', name: 'oil_field_name', orderable: false, searchable: false },
            { data: 'tower_name', name: 'tower_name', orderable: false, searchable: false },
            { data: 'oil_string_name', name: 'oil_string_name', orderable: false, searchable: false },
            { data: 'oil_rate', name: 'oil_rate' },
            { data: 'mpfm_test_link', name: 'mpfm_test_link', orderable: false, searchable: false },
            { data: 'status_badge', name: 'status', orderable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        language: {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
        }
    });

    // Filter event listeners
    $('#oilFieldFilter, #testVesselFilter, #statusFilter, #dateFromFilter, #dateToFilter').on('change', function() {
        table.draw();
    });

    $('#resetFilters').on('click', function() {
        $('#oilFieldFilter').val('');
        $('#testVesselFilter').val('');
        $('#statusFilter').val('');
        $('#dateFromFilter').val('');
        $('#dateToFilter').val('');
        table.draw();
    });
});
</script>
@endpush
