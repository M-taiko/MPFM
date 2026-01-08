@extends('layouts.master')

@section('title', 'MPFM Tests')

@push('styles')
<link href="{{URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css')}}" rel="stylesheet">
<link href="{{URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
@endpush

@section('content')
<div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">MPFM Tests</h3>
                <div class="card-options">
                    @can('create', App\Models\MPFMTest::class)
                    <a href="{{ route('mpfm_tests.create') }}" class="btn btn-primary btn-sm">
                        <i class="fe fe-plus"></i> Create New Test
                    </a>
                    @endcan
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label>Filter by Oil Field:</label>
                        <select class="form-control" id="oilFieldFilter">
                            <option value="">All Oil Fields</option>
                            @foreach($oilFields as $field)
                            <option value="{{ $field->id }}">{{ $field->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Filter by Tower:</label>
                        <select class="form-control" id="towerFilter">
                            <option value="">All Towers</option>
                            @foreach($towers as $tower)
                            <option value="{{ $tower->id }}">{{ $tower->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>Filter by Operator:</label>
                        <select class="form-control" id="userFilter">
                            <option value="">All Operators</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>From Date:</label>
                        <input type="date" class="form-control" id="dateFromFilter">
                    </div>
                    <div class="col-md-2">
                        <label>To Date:</label>
                        <input type="date" class="form-control" id="dateToFilter">
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="mpfmTestsTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Test Date</th>
                                <th>Shift</th>
                                <th>Operator</th>
                                <th>Oil Field</th>
                                <th>Tower</th>
                                <th>Oil String</th>
                                <th>Oil Rate</th>
                                <th>Gas Rate</th>
                                <th>Water Cut</th>
                                <th>GOR</th>
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
    var table = $('#mpfmTestsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('mpfm_tests.index') }}',
            data: function(d) {
                d.oil_field_id = $('#oilFieldFilter').val();
                d.tower_id = $('#towerFilter').val();
                d.user_id = $('#userFilter').val();
                d.test_date_from = $('#dateFromFilter').val();
                d.test_date_to = $('#dateToFilter').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'test_date', name: 'test_date' },
            { data: 'shift', name: 'shift' },
            { data: 'user_name', name: 'user_id' },
            { data: 'oil_field_name', name: 'oil_field_id' },
            { data: 'tower_name', name: 'tower_id' },
            { data: 'oil_string_name', name: 'oil_string_id' },
            { data: 'oil_rate', name: 'oil_rate' },
            { data: 'gas_rate', name: 'gas_rate' },
            { data: 'water_cut', name: 'water_cut' },
            { data: 'gor', name: 'gor', orderable: false, searchable: false },
            { data: 'status_badge', name: 'status', orderable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        pageLength: 25
    });

    $('#oilFieldFilter, #towerFilter, #userFilter, #dateFromFilter, #dateToFilter').on('change', function() {
        table.ajax.reload();
    });
});
</script>
@endpush

