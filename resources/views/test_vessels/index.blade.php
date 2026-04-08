@extends('layouts.master')

@section('title', 'Test Vessels')

@push('styles')
<link href="{{URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css')}}" rel="stylesheet">
<link href="{{URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
<style>
    .dt-loading { display: none; }

    .modern-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2.5rem 2rem;
        border-radius: 0.75rem;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.2);
    }

    .modern-header h1 {
        margin: 0;
        font-size: 2rem;
        font-weight: 700;
        letter-spacing: -0.5px;
    }

    .modern-header p {
        margin: 0.5rem 0 0 0;
        opacity: 0.9;
        font-size: 0.95rem;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        border-radius: 0.75rem;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border-left: 4px solid #667eea;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
        transform: translateY(-2px);
    }

    .stat-card-icon {
        font-size: 1.5rem;
        color: #667eea;
        margin-bottom: 0.5rem;
    }

    .stat-card-value {
        font-size: 2rem;
        font-weight: 700;
        color: #1a202c;
        margin: 0.5rem 0;
    }

    .stat-card-label {
        font-size: 0.85rem;
        color: #718096;
        font-weight: 500;
    }

    .modern-filters {
        background: white;
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 1px solid #e2e8f0;
    }

    .filter-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        align-items: flex-end;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
    }

    .filter-group label {
        font-weight: 600;
        font-size: 0.9rem;
        color: #2d3748;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .filter-group select,
    .filter-group input {
        padding: 0.75rem;
        border: 1px solid #cbd5e0;
        border-radius: 0.5rem;
        font-size: 0.9rem;
        transition: all 0.2s ease;
        background-color: white;
    }

    .filter-group select:focus,
    .filter-group input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .btn-modern {
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 600;
        font-size: 0.9rem;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-primary-modern {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .btn-primary-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(102, 126, 234, 0.3);
        color: white;
        text-decoration: none;
    }

    .btn-secondary-modern {
        background: #e2e8f0;
        color: #2d3748;
    }

    .btn-secondary-modern:hover {
        background: #cbd5e0;
        color: #1a202c;
        text-decoration: none;
    }

    .modern-table-wrapper {
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        border: 1px solid #e2e8f0;
    }

    .modern-table {
        width: 100%;
        margin: 0;
    }

    .modern-table thead {
        background: #f7fafc;
        border-bottom: 2px solid #e2e8f0;
    }

    .modern-table th {
        padding: 1.25rem;
        text-align: left;
        font-weight: 600;
        font-size: 0.9rem;
        color: #2d3748;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .modern-table tbody tr {
        border-bottom: 1px solid #e2e8f0;
        transition: all 0.2s ease;
    }

    .modern-table tbody tr:hover {
        background: #f7fafc;
    }

    .modern-table td {
        padding: 1.25rem;
        font-size: 0.95rem;
        color: #4a5568;
    }

    .badge-modern {
        display: inline-block;
        padding: 0.4rem 0.8rem;
        border-radius: 0.3rem;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: capitalize;
    }

    .badge-success {
        background: #c6f6d5;
        color: #22543d;
    }

    .badge-warning {
        background: #fed7d7;
        color: #742a2a;
    }

    .badge-secondary {
        background: #e2e8f0;
        color: #2d3748;
    }

    .action-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .btn-sm-modern {
        padding: 0.5rem 0.75rem;
        border-radius: 0.4rem;
        font-size: 0.8rem;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-weight: 500;
    }

    .btn-info-modern {
        background: #bee3f8;
        color: #2c5282;
    }

    .btn-info-modern:hover {
        background: #90cdf4;
        color: #1a365d;
    }

    .btn-primary-sm-modern {
        background: #667eea;
        color: white;
    }

    .btn-primary-sm-modern:hover {
        background: #5568d3;
        color: white;
    }

    .btn-danger-modern {
        background: #fed7d7;
        color: #742a2a;
    }

    .btn-danger-modern:hover {
        background: #fc8181;
        color: #5a202c;
    }

    .dataTables_wrapper .dataTables_paginate {
        padding: 1rem;
        text-align: right;
    }

    .dataTables_wrapper .paginate_button {
        padding: 0.5rem 0.75rem;
        margin: 0 0.25rem;
        border: 1px solid #cbd5e0;
        border-radius: 0.4rem;
        background: white;
        color: #2d3748;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .dataTables_wrapper .paginate_button:hover {
        background: #667eea;
        color: white;
        border-color: #667eea;
    }

    .dataTables_wrapper .paginate_button.current {
        background: #667eea;
        color: white;
        border-color: #667eea;
    }
</style>
@endpush

@section('content')
<!-- Modern Header -->
<div class="modern-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fe fe-anchor" style="margin-right: 0.5rem;"></i>Test Vessel Management</h1>
        <p>Manage and monitor all test vessels in your organization</p>
    </div>
    @can('manage', App\Models\TestVessel::class)
    <a href="{{ route('test_vessels.create') }}" class="btn btn-modern btn-primary-modern">
        <i class="fe fe-plus"></i> Add New Vessel
    </a>
    @endcan
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-card-icon"><i class="fe fe-anchor"></i></div>
        <div class="stat-card-value" id="totalVessels">-</div>
        <div class="stat-card-label">Total Vessels</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon"><i class="fe fe-activity" style="color: #48bb78;"></i></div>
        <div class="stat-card-value" id="activeVessels">-</div>
        <div class="stat-card-label">Active Vessels</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon"><i class="fe fe-activity" style="color: #ed8936;"></i></div>
        <div class="stat-card-value" id="maintenanceVessels">-</div>
        <div class="stat-card-label">In Maintenance</div>
    </div>
</div>

<!-- Modern Filters -->
<div class="modern-filters">
    <div class="filter-row">
        <div class="filter-group flex-grow-1">
            <label><i class="fe fe-filter"></i> Filter by Status</label>
            <select id="statusFilter" class="form-control">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="maintenance">Maintenance</option>
            </select>
        </div>
        <div class="filter-group flex-grow-1">
            <label><i class="fe fe-map-pin"></i> Filter by Oil Field</label>
            <select id="oilFieldFilter" class="form-control">
                <option value="">All Oil Fields</option>
                @foreach($oilFields as $field)
                <option value="{{ $field->id }}">{{ $field->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <button type="button" class="btn btn-modern btn-secondary-modern" id="resetFilters" style="margin-bottom: 0;">
                <i class="fe fe-refresh-cw"></i> Reset
            </button>
        </div>
    </div>
</div>

<!-- Modern Table -->
<div class="modern-table-wrapper">
    <div class="table-responsive">
        <table class="modern-table" id="testVesselsTable">
            <thead>
                <tr>
                    <th>Vessel Name</th>
                    <th>Code</th>
                    <th>Oil Field</th>
                    <th>Tests</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.min.js')}}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof $ === 'undefined' || typeof $.fn.dataTable === 'undefined') {
        console.error('jQuery or DataTables not loaded');
        return;
    }

    var table = $('#testVesselsTable').DataTable({
        processing: true,
        serverSide: true,
        stateSave: false,
        ajax: {
            url: '{{ route('test_vessels.index') }}',
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            data: function(d) {
                d.status = $('#statusFilter').val();
                d.oil_field_id = $('#oilFieldFilter').val();
                return d;
            }
        },
        columns: [
            { data: 'name', name: 'name' },
            { data: 'code', name: 'code' },
            { data: 'oil_field_name', name: 'oil_field_name', orderable: false, searchable: false },
            { data: 'tests_count', name: 'tests_count', orderable: false, searchable: false },
            { data: 'status_badge', name: 'status', orderable: false },
            { data: 'created_at', name: 'created_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[5, 'desc']],
        pageLength: 25,
        language: {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>'
        },
        drawCallback: function() {
            updateStats();
        }
    });

    $('#statusFilter, #oilFieldFilter').on('change', function() {
        table.draw();
    });

    $('#resetFilters').on('click', function() {
        $('#statusFilter').val('');
        $('#oilFieldFilter').val('');
        table.draw();
    });

    function updateStats() {
        var data = table.data();
        var total = table.page.info().recordsTotal;
        var active = 0;
        var maintenance = 0;

        table.rows().data().each(function(row) {
            if (row.status === 'active') active++;
            if (row.status === 'maintenance') maintenance++;
        });

        $('#totalVessels').text(total);
        $('#activeVessels').text(active);
        $('#maintenanceVessels').text(maintenance);
    }
});
</script>
@endpush
