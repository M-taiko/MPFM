@extends('layouts.master')

@section('title', 'Create Daily Plan')

@section('css')
<link href="{{URL::asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet">
<link href="{{URL::asset('assets/plugins/sweet-alert/sweetalert.css')}}" rel="stylesheet">
<style>
    .task-row {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 15px;
        border-left: 4px solid #667eea;
    }
    .task-row.removing {
        opacity: 0.5;
    }
</style>
@endsection

@section('content')
<div class="main-container container-fluid">
    <div class="page-header">
        <div class="page-leftheader">
            <h4 class="page-title mb-0">Create Daily Plan</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}"><i class="fe fe-home mr-2 fs-14"></i>Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('daily-plans.index') }}">Daily Plans</a></li>
                <li class="breadcrumb-item active" aria-current="page">Create</li>
            </ol>
        </div>
        <div class="page-rightheader">
            <a href="{{ route('daily-plans.index') }}" class="btn btn-outline-primary">
                <i class="fe fe-arrow-left mr-2"></i>Back to Plans
            </a>
        </div>
    </div>

    <form action="{{ route('daily-plans.store') }}" method="POST" id="planForm">
        @csrf

        <div class="row">
            <!-- Plan Details -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Plan Details</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Plan Name <span class="text-danger">*</span></label>
                                    <input type="text" name="plan_name" class="form-control @error('plan_name') is-invalid @enderror"
                                           value="{{ old('plan_name') }}" placeholder="e.g., Morning Shift - Field A" required>
                                    @error('plan_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Plan Date <span class="text-danger">*</span></label>
                                    <input type="date" name="plan_date" class="form-control @error('plan_date') is-invalid @enderror"
                                           value="{{ old('plan_date', request('date', now()->format('Y-m-d'))) }}" required>
                                    @error('plan_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Oil Field <span class="text-danger">*</span></label>
                                    <select name="oil_field_id" id="oil_field_id" class="form-control select2 @error('oil_field_id') is-invalid @enderror" required>
                                        <option value="">Select Oil Field</option>
                                        @foreach($oilFields as $field)
                                            <option value="{{ $field->id }}" {{ old('oil_field_id') == $field->id ? 'selected' : '' }}>
                                                {{ $field->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('oil_field_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Tower (Optional)</label>
                                    <select name="tower_id" id="tower_id" class="form-control select2">
                                        <option value="">All Towers</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Plan Notes</label>
                            <textarea name="plan_notes" class="form-control" rows="3" placeholder="Add any special instructions or notes for this plan">{{ old('plan_notes') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Tasks Section -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Tasks</h3>
                        <div class="card-options">
                            <button type="button" class="btn btn-sm btn-primary" id="addTaskBtn">
                                <i class="fe fe-plus mr-1"></i>Add Task
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="tasksContainer">
                            <p class="text-muted text-center py-4" id="noTasksMessage">
                                <i class="fe fe-info mr-2"></i>No tasks added yet. Click "Add Task" to create your first task.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Quick Actions</h3>
                    </div>
                    <div class="card-body">
                        <button type="submit" name="action" value="save_draft" class="btn btn-secondary btn-block mb-2">
                            <i class="fe fe-save mr-2"></i>Save as Draft
                        </button>
                        <button type="submit" name="action" value="save_activate" class="btn btn-success btn-block">
                            <i class="fe fe-check-circle mr-2"></i>Save & Activate
                        </button>
                        <hr>
                        <a href="{{ route('daily-plans.index') }}" class="btn btn-outline-secondary btn-block">
                            <i class="fe fe-x mr-2"></i>Cancel
                        </a>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Plan Summary</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Total Tasks:</strong>
                            <span class="float-right" id="taskCount">0</span>
                        </div>
                        <div class="mb-3">
                            <strong>Unique Operators:</strong>
                            <span class="float-right" id="operatorCount">0</span>
                        </div>
                        <div class="mb-3">
                            <strong>High Priority:</strong>
                            <span class="float-right" id="highPriorityCount">0</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Task Template -->
<template id="taskTemplate">
    <div class="task-row" data-task-index="">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <h6 class="mb-0">Task #<span class="task-number"></span></h6>
            <button type="button" class="btn btn-sm btn-danger remove-task">
                <i class="fe fe-trash-2"></i>
            </button>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Assign to Operator <span class="text-danger">*</span></label>
                    <select name="tasks[INDEX][assigned_to]" class="form-control operator-select" required>
                        <option value="">Select Operator</option>
                        @foreach($operators as $operator)
                            <option value="{{ $operator->id }}">{{ $operator->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Oil String <span class="text-danger">*</span></label>
                    <select name="tasks[INDEX][oil_string_id]" class="form-control string-select" required disabled>
                        <option value="">Select field first</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>MPFM Device</label>
                    <input type="text" name="tasks[INDEX][mpfm_device]" class="form-control" placeholder="e.g., MPFM-001">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Device Type</label>
                    <select name="tasks[INDEX][device_type]" class="form-control">
                        <option value="">Select Type</option>
                        <option value="MPFM">MPFM</option>
                        <option value="DAFC">DAFC</option>
                        <option value="Well Test">Well Test</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Expected Oil Rate (STB/d)</label>
                    <input type="number" step="0.01" name="tasks[INDEX][expected_oil_rate]" class="form-control">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Expected Gas Rate (MMScf/d)</label>
                    <input type="number" step="0.01" name="tasks[INDEX][expected_gas_rate]" class="form-control">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Expected Water Cut (%)</label>
                    <input type="number" step="0.01" name="tasks[INDEX][expected_water_cut]" class="form-control">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Priority</label>
                    <select name="tasks[INDEX][priority]" class="form-control priority-select">
                        <option value="normal">Normal</option>
                        <option value="low">Low</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Task Instructions</label>
            <textarea name="tasks[INDEX][task_instructions]" class="form-control" rows="2" placeholder="Specific instructions for this task"></textarea>
        </div>
    </div>
</template>
@endsection

@section('js')
<script src="{{URL::asset('assets/plugins/select2/js/select2.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/sweet-alert/sweetalert.min.js')}}"></script>

<script>
$(document).ready(function() {
    let taskCounter = 0;
    let oilStringsData = {};

    // Initialize Select2
    $('.select2').select2();

    // Load towers when field is selected
    $('#oil_field_id').on('change', function() {
        const fieldId = $(this).val();
        $('#tower_id').html('<option value="">Loading...</option>');

        if (fieldId) {
            $.ajax({
                url: `/api/oil-fields/${fieldId}/towers`,
                success: function(towers) {
                    let options = '<option value="">All Towers</option>';
                    towers.forEach(tower => {
                        options += `<option value="${tower.id}">${tower.name}</option>`;
                    });
                    $('#tower_id').html(options);
                    loadOilStrings(fieldId);
                }
            });
        } else {
            $('#tower_id').html('<option value="">Select field first</option>');
        }
    });

    // Load oil strings for field
    function loadOilStrings(fieldId) {
        $.ajax({
            url: `/api/oil-fields/${fieldId}/oil-strings`,
            success: function(strings) {
                oilStringsData = strings;
                updateAllStringSelects();
            }
        });
    }

    // Update all string selects
    function updateAllStringSelects() {
        $('.string-select').each(function() {
            const currentValue = $(this).val();
            let options = '<option value="">Select Oil String</option>';

            oilStringsData.forEach(string => {
                options += `<option value="${string.id}">${string.name} (${string.tower.name})</option>`;
            });

            $(this).html(options).val(currentValue).prop('disabled', false);
        });
    }

    // Add task
    $('#addTaskBtn').on('click', function() {
        const fieldId = $('#oil_field_id').val();
        if (!fieldId) {
            swal('Error', 'Please select an Oil Field first', 'error');
            return;
        }

        taskCounter++;
        const template = document.getElementById('taskTemplate').content.cloneNode(true);
        const taskRow = $(template).find('.task-row');

        // Update task number and index
        taskRow.attr('data-task-index', taskCounter);
        taskRow.find('.task-number').text(taskCounter);

        // Replace INDEX placeholder
        taskRow.find('[name*="INDEX"]').each(function() {
            const name = $(this).attr('name').replace('INDEX', taskCounter);
            $(this).attr('name', name);
        });

        $('#noTasksMessage').hide();
        $('#tasksContainer').append(taskRow);

        updateAllStringSelects();
        updateSummary();
    });

    // Remove task
    $(document).on('click', '.remove-task', function() {
        $(this).closest('.task-row').addClass('removing').fadeOut(300, function() {
            $(this).remove();
            updateTaskNumbers();
            updateSummary();

            if ($('.task-row').length === 0) {
                $('#noTasksMessage').show();
            }
        });
    });

    // Update task numbers
    function updateTaskNumbers() {
        $('.task-row').each(function(index) {
            $(this).find('.task-number').text(index + 1);
        });
    }

    // Update summary
    function updateSummary() {
        const taskCount = $('.task-row').length;
        const operators = new Set();
        let highPriority = 0;

        $('.operator-select').each(function() {
            const val = $(this).val();
            if (val) operators.add(val);
        });

        $('.priority-select').each(function() {
            if ($(this).val() === 'high' || $(this).val() === 'urgent') {
                highPriority++;
            }
        });

        $('#taskCount').text(taskCount);
        $('#operatorCount').text(operators.size);
        $('#highPriorityCount').text(highPriority);
    }

    // Update summary on changes
    $(document).on('change', '.operator-select, .priority-select', updateSummary);

    // Form validation
    $('#planForm').on('submit', function(e) {
        const taskCount = $('.task-row').length;

        if (taskCount === 0) {
            e.preventDefault();
            swal('Error', 'Please add at least one task to the plan', 'error');
            return false;
        }

        // Confirm activation
        const action = $('button[type="submit"]:focus').val();
        if (action === 'save_activate') {
            e.preventDefault();
            swal({
                title: 'Activate Plan?',
                text: 'Once activated, operators will be able to see and execute their assigned tasks.',
                icon: 'warning',
                buttons: ['Cancel', 'Activate'],
                dangerMode: false,
            }).then((confirm) => {
                if (confirm) {
                    this.submit();
                }
            });
        }
    });
});
</script>
@endsection
