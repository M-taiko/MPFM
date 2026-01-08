@extends('layouts.master')

@section('title', 'Test Archive')

@section('css')
<link href="{{URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css')}}" rel="stylesheet">
<link href="{{URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
<style>
    .archive-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
    }

    .stats-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        margin-bottom: 1.5rem;
        transition: all 0.3s ease;
    }

    .stats-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.12);
    }

    .stats-card .card-body {
        padding: 1.5rem;
    }

    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 0.25rem;
    }

    .stat-label {
        font-size: 0.875rem;
        color: #718096;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 1rem;
    }

    .table-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        overflow: hidden;
    }

    .table-card .card-header {
        background: transparent;
        border-bottom: 1px solid #e2e8f0;
        padding: 1.5rem;
    }

    .table-card .card-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #2d3748;
        margin: 0;
    }

    #wellTestsTable {
        margin-bottom: 0 !important;
    }

    #wellTestsTable thead th {
        background: #f7fafc;
        color: #4a5568;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        border: none;
        padding: 1rem;
    }

    #wellTestsTable tbody td {
        padding: 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #e2e8f0;
    }

    #wellTestsTable tbody tr:hover {
        background: #f7fafc;
    }

    .badge-metric {
        padding: 0.4rem 0.8rem;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .btn-view {
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-view:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    .compare-btn {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        font-weight: 600;
        color: white;
        transition: all 0.3s ease;
    }

    .compare-btn:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
        color: white;
    }

    .compare-btn:disabled {
        background: #e2e8f0;
        color: #a0aec0;
        cursor: not-allowed;
    }

    .metric-preview {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .metric-item {
        display: flex;
        flex-direction: column;
        min-width: 80px;
    }

    .metric-item-label {
        font-size: 0.75rem;
        color: #718096;
        margin-bottom: 0.25rem;
    }

    .metric-item-value {
        font-size: 0.875rem;
        font-weight: 600;
        color: #2d3748;
    }
</style>
@endsection

@section('content')
<div class="main-container container-fluid">
    <!-- Page Header -->
    <div class="archive-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="mb-2" style="font-size: 2rem; font-weight: 700;">
                    <i class="fe fe-database mr-2"></i>Test Archive
                </h1>
                <p class="mb-0" style="opacity: 0.9;">
                    Browse, search, and analyze all MPFM well tests
                </p>
            </div>
            <div class="col-md-4 text-right">
                <a href="{{ url('/upload') }}" class="btn btn-light btn-lg">
                    <i class="fe fe-upload mr-2"></i>Upload New Test
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stats-card">
                <div class="card-body">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                        <i class="fe fe-file-text"></i>
                    </div>
                    <div class="stat-number" id="totalTests">-</div>
                    <div class="stat-label">Total Tests</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stats-card">
                <div class="card-body">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">
                        <i class="fe fe-calendar"></i>
                    </div>
                    <div class="stat-number" id="thisMonth">-</div>
                    <div class="stat-label">This Month</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stats-card">
                <div class="card-body">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white;">
                        <i class="fe fe-users"></i>
                    </div>
                    <div class="stat-number" id="totalOperators">-</div>
                    <div class="stat-label">Operators</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stats-card">
                <div class="card-body">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white;">
                        <i class="fe fe-map"></i>
                    </div>
                    <div class="stat-number" id="totalFields">-</div>
                    <div class="stat-label">Oil Fields</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Test Table -->
    <div class="row">
        <div class="col-12">
            <div class="table-card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">All Well Tests</h3>
                        <button id="compareBtn" class="compare-btn" disabled>
                            <i class="fe fe-git-compare mr-2"></i>Compare Selected
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table" id="wellTestsTable">
                            <thead>
                                <tr>
                                    <th width="50"><input type="checkbox" id="selectAll"></th>
                                    <th>Well</th>
                                    <th>Field</th>
                                    <th>Client</th>
                                    <th>Operator</th>
                                    <th>Test Date</th>
                                    <th>Key Metrics</th>
                                    <th>Created</th>
                                    <th width="120">Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
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
<script src="{{URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.min.js')}}"></script>

<script>
$(document).ready(function() {
    // DataTable
    var table = $('#wellTestsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("well_tests.datatables") }}',
            dataSrc: function(json) {
                // Update stats
                $('#totalTests').text(json.recordsTotal || 0);
                return json.data;
            }
        },
        columns: [
            {
                data: 'id',
                render: function(data){
                    return `<input type="checkbox" class="selectTest" value="${data}">`;
                },
                orderable: false,
                searchable: false
            },
            {
                data: 'display_name',
                name: 'well',
                render: function(data, type, row) {
                    return `
                        <div>
                            <strong style="color: #2d3748;">${data || 'N/A'}</strong>
                            ${row.wellhead_tower ? `<br><small class="text-muted"><i class="fe fe-map-pin"></i> ${row.wellhead_tower}</small>` : ''}
                        </div>
                    `;
                }
            },
            {
                data: 'field',
                name: 'field',
                render: function(data) {
                    return data ? `<span class="badge badge-light badge-metric">${data}</span>` : 'N/A';
                }
            },
            {
                data: 'client',
                name: 'client',
                render: function(data) {
                    return data || 'N/A';
                }
            },
            {
                data: 'operator',
                name: 'operator',
                render: function(data) {
                    return data ? `<i class="fe fe-user mr-1"></i>${data}` : 'N/A';
                }
            },
            {
                data: 'test_date',
                name: 'test_date',
                render: function(data) {
                    if (!data) return 'N/A';
                    const date = new Date(data);
                    return date.toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    });
                }
            },
            {
                data: 'calculated_metrics',
                orderable: false,
                searchable: false,
                render: function(data) {
                    if (!data) return '<small class="text-muted">No data</small>';

                    const metrics = typeof data === 'string' ? JSON.parse(data) : data;
                    return `
                        <div class="metric-preview">
                            ${metrics.oil_rate ? `
                                <div class="metric-item">
                                    <div class="metric-item-label">Oil</div>
                                    <div class="metric-item-value">${parseFloat(metrics.oil_rate).toFixed(0)}</div>
                                </div>
                            ` : ''}
                            ${metrics.gas_rate ? `
                                <div class="metric-item">
                                    <div class="metric-item-label">Gas</div>
                                    <div class="metric-item-value">${parseFloat(metrics.gas_rate).toFixed(2)}</div>
                                </div>
                            ` : ''}
                            ${metrics.gor ? `
                                <div class="metric-item">
                                    <div class="metric-item-label">GOR</div>
                                    <div class="metric-item-value">${parseFloat(metrics.gor).toFixed(0)}</div>
                                </div>
                            ` : ''}
                        </div>
                    `;
                }
            },
            {
                data: 'created_at',
                name: 'created_at',
                render: function(data) {
                    if (!data) return 'N/A';
                    const date = new Date(data);
                    const now = new Date();
                    const diff = now - date;
                    const days = Math.floor(diff / (1000 * 60 * 60 * 24));

                    if (days === 0) return '<small class="text-success">Today</small>';
                    if (days === 1) return '<small class="text-info">Yesterday</small>';
                    if (days < 7) return `<small class="text-muted">${days} days ago</small>`;
                    return `<small class="text-muted">${date.toLocaleDateString()}</small>`;
                }
            },
            {
                data: 'action',
                name: 'id',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return `
                        <a href="/well-tests/${row.id}" class="btn btn-primary btn-sm btn-view">
                            <i class="fe fe-eye"></i> View
                        </a>
                    `;
                }
            }
        ],
        order: [[7, 'desc']], // Sort by created_at descending
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        language: {
            processing: '<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>',
            emptyTable: "No tests found in archive",
            zeroRecords: "No matching tests found"
        }
    });

    // Calculate additional stats
    $.ajax({
        url: '{{ route("well_tests.datatables") }}',
        data: { length: -1 }, // Get all records
        success: function(response) {
            const tests = response.data;

            // This month
            const now = new Date();
            const thisMonth = tests.filter(t => {
                const testDate = new Date(t.created_at);
                return testDate.getMonth() === now.getMonth() &&
                       testDate.getFullYear() === now.getFullYear();
            }).length;
            $('#thisMonth').text(thisMonth);

            // Unique operators
            const operators = new Set(tests.map(t => t.operator).filter(Boolean));
            $('#totalOperators').text(operators.size);

            // Unique fields
            const fields = new Set(tests.map(t => t.field).filter(Boolean));
            $('#totalFields').text(fields.size);
        }
    });

    // Select all checkbox
    $('#selectAll').on('click', function() {
        $('.selectTest').prop('checked', this.checked);
        toggleCompareButton();
    });

    // Individual checkbox
    $(document).on('change', '.selectTest', function() {
        const allChecked = $('.selectTest').length === $('.selectTest:checked').length;
        $('#selectAll').prop('checked', allChecked);
        toggleCompareButton();
    });

    // Toggle compare button
    function toggleCompareButton() {
        const selected = $('.selectTest:checked').length;
        $('#compareBtn').prop('disabled', selected < 2);

        if (selected >= 2) {
            $('#compareBtn').html(`<i class="fe fe-git-compare mr-2"></i>Compare ${selected} Tests`);
        } else {
            $('#compareBtn').html(`<i class="fe fe-git-compare mr-2"></i>Compare Selected`);
        }
    }

    // Compare button click
    $('#compareBtn').on('click', function () {
        const selectedIds = $('.selectTest:checked').map(function () {
            return this.value;
        }).get();

        if (selectedIds.length < 2) {
            alert("Please select at least two tests to compare.");
            return;
        }

        const csrf = $('meta[name="csrf-token"]').attr('content');
        const form = $('<form>', {
            action: '{{ route("well_tests.compare") }}',
            method: 'POST'
        });

        form.append($('<input>', {
            type: 'hidden',
            name: '_token',
            value: csrf
        }));

        selectedIds.forEach(id => {
            form.append($('<input>', {
                type: 'hidden',
                name: 'test_ids[]',
                value: id
            }));
        });

        $('body').append(form);
        form.submit();
    });
});
</script>
@endsection
