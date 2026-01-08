@extends('layouts.master')

@section('title', 'Towers')

@push('styles')
<link href="{{URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css')}}" rel="stylesheet">
<link href="{{URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
@endpush

@section('content')
<div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Towers Management</h3>
                <div class="card-options">
                    @can('create', App\Models\Tower::class)
                    <a href="{{ route('towers.create') }}" class="btn btn-primary btn-sm">
                        <i class="fe fe-plus"></i> Create New Tower
                    </a>
                    @endcan
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>Filter by Oil Field:</label>
                        <select class="form-control" id="oilFieldFilter">
                            <option value="">All Oil Fields</option>
                            @foreach($oilFields as $field)
                            <option value="{{ $field->id }}">{{ $field->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="towersTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Code</th>
                                <th>Oil Field</th>
                                <th>Oil Strings</th>
                                <th>Status</th>
                                <th>Created At</th>
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
    var table = $('#towersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('towers.index') }}',
            data: function(d) {
                d.oil_field_id = $('#oilFieldFilter').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { data: 'code', name: 'code' },
            { data: 'oil_field_name', name: 'oil_field_id' },
            { data: 'oil_strings_count', name: 'oil_strings_count', orderable: false, searchable: false },
            { data: 'status_badge', name: 'status', orderable: false },
            { data: 'created_at', name: 'created_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        pageLength: 25
    });

    $('#oilFieldFilter').on('change', function() {
        table.ajax.reload();
    });
});
</script>
@endpush

