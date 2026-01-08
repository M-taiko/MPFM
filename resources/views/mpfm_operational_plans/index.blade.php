@extends('layouts.master')

@section('title', 'MPFM Operational Plans')

@push('styles')
<link href="{{URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css')}}" rel="stylesheet">
<link href="{{URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
@endpush

@section('content')
<div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">MPFM Operational Plans</h3>
                <div class="card-options">
                    @can('create', App\Models\MPFMOperationalPlan::class)
                    <a href="{{ route('mpfm_operational_plans.create') }}" class="btn btn-primary btn-sm">
                        <i class="fe fe-plus"></i> Create New Plan
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
                    <div class="col-md-3">
                        <label>Filter by Status:</label>
                        <select class="form-control" id="statusFilter">
                            <option value="">All Statuses</option>
                            <option value="draft">Draft</option>
                            <option value="active">Active</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Filter by Date:</label>
                        <input type="date" class="form-control" id="planDateFilter">
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="operationalPlansTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Creator</th>
                                <th>Assigned Operator</th>
                                <th>Oil Field</th>
                                <th>Tower</th>
                                <th>Plan Date</th>
                                <th>Strings</th>
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
    var table = $('#operationalPlansTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('mpfm_operational_plans.index') }}',
            data: function(d) {
                d.oil_field_id = $('#oilFieldFilter').val();
                d.tower_id = $('#towerFilter').val();
                d.status = $('#statusFilter').val();
                d.plan_date = $('#planDateFilter').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { data: 'creator_name', name: 'created_by' },
            { data: 'assigned_user_name', name: 'assigned_user_id' },
            { data: 'oil_field_name', name: 'oil_field_id' },
            { data: 'tower_name', name: 'tower_id' },
            { data: 'plan_date', name: 'plan_date' },
            { data: 'assigned_strings_count', name: 'assigned_strings_count', orderable: false },
            { data: 'status_badge', name: 'status', orderable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        pageLength: 25
    });

    $('#oilFieldFilter, #towerFilter, #statusFilter, #planDateFilter').on('change', function() {
        table.ajax.reload();
    });
});
</script>
@endpush

