@extends('layouts.master')

@section('title', 'Daily Plans')

@section('css')
<link href="{{URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet" />
<link href="{{URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css')}}" rel="stylesheet">
<link href="{{URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css')}}" rel="stylesheet" />
<link href="{{URL::asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet">
@endsection

@section('content')
<div class="main-container container-fluid">
    <div class="page-header">
        <div class="page-leftheader">
            <h4 class="page-title mb-0">Daily Plans</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}"><i class="fe fe-home mr-2 fs-14"></i>Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Daily Plans</li>
            </ol>
        </div>
        <div class="page-rightheader">
            <a href="{{ route('daily-plans.create') }}" class="btn btn-primary">
                <i class="fe fe-plus mr-2"></i>Create New Plan
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Daily Plans</h3>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Date From</label>
                                <input type="date" id="date_from" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Date To</label>
                                <input type="date" id="date_to" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Status</label>
                                <select id="status_filter" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="draft">Draft</option>
                                    <option value="active">Active</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Oil Field</label>
                                <select id="oil_field_filter" class="form-control select2">
                                    <option value="">All Fields</option>
                                    @foreach($oilFields as $field)
                                        <option value="{{ $field->id }}">{{ $field->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- DataTable -->
                    <div class="table-responsive">
                        <table id="plansTable" class="table table-bordered table-hover text-nowrap w-100">
                            <thead>
                                <tr>
                                    <th>Plan Date</th>
                                    <th>Plan Name</th>
                                    <th>Oil Field</th>
                                    <th>Tower</th>
                                    <th>Status</th>
                                    <th>Progress</th>
                                    <th>Total Tasks</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/select2/js/select2.min.js')}}"></script>

<script>
$(document).ready(function() {
    $('.select2').select2();

    var table = $('#plansTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('daily-plans.index') }}",
            data: function(d) {
                d.date_from = $('#date_from').val();
                d.date_to = $('#date_to').val();
                d.status = $('#status_filter').val();
                d.oil_field_id = $('#oil_field_filter').val();
            }
        },
        columns: [
            { data: 'plan_date', name: 'plan_date' },
            { data: 'plan_name', name: 'plan_name' },
            { data: 'oil_field.name', name: 'oilField.name' },
            { data: 'tower.name', name: 'tower.name', defaultContent: '-' },
            { data: 'status_badge', name: 'status', orderable: false, searchable: false },
            { data: 'progress_bar', name: 'progress', orderable: false, searchable: false },
            { data: 'total_tasks', name: 'total_tasks' },
            { data: 'created_at', name: 'created_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        responsive: true
    });

    // Filter triggers
    $('#date_from, #date_to, #status_filter, #oil_field_filter').on('change', function() {
        table.draw();
    });
});
</script>
@endsection
