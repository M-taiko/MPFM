@extends('layouts.master')

@section('title', 'Add Task to ' . $monthlyPlan->plan_name)

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div>
        <h1 class="page-title">Add Task to Plan</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('monthly-plans.index') }}">Monthly Plans</a></li>
            <li class="breadcrumb-item"><a href="{{ route('monthly-plans.show', $monthlyPlan) }}">{{ $monthlyPlan->plan_name }}</a></li>
            <li class="breadcrumb-item active">Add Task</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <!-- Instructions Card -->
        <div class="card mb-4" style="border-left: 4px solid #3b82f6;">
            <div class="card-header" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white;">
                <h3 class="card-title mb-0">
                    <i class="fe fe-info"></i> Task Information
                </h3>
            </div>
            <div class="card-body">
                <p class="mb-0">Fill in the task details below. All fields marked with <span class="text-danger">*</span> are required. This task structure matches the plan.txt format used in monthly MPFM testing schedules.</p>
            </div>
        </div>

        <!-- Task Form -->
        <div class="card" style="border-left: 4px solid #10b981;">
            <div class="card-header" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">
                <h3 class="card-title mb-0">
                    <i class="fe fe-edit"></i> Task Details
                </h3>
            </div>
            <div class="card-body">
                @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h5><i class="fe fe-x-circle"></i> Validation Errors</h5>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
                @endif

                <form action="{{ route('monthly-plans.tasks.store', $monthlyPlan) }}" method="POST">
                    @csrf

                    <div class="row">
                        <!-- SNO -->
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="sno" class="form-label">
                                    <strong>SNO</strong> <span class="text-danger">*</span>
                                </label>
                                <input type="number"
                                       class="form-control @error('sno') is-invalid @enderror"
                                       id="sno"
                                       name="sno"
                                       value="{{ old('sno', $monthlyPlan->tasks()->count() + 1) }}"
                                       min="1"
                                       required>
                                @error('sno')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Well Number -->
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="well_number" class="form-label">
                                    <strong>Well Number</strong> <span class="text-danger">*</span>
                                </label>
                                <select class="form-control @error('well_number') is-invalid @enderror"
                                        id="well_number"
                                        name="well_number"
                                        required
                                        disabled>
                                    <option value="">-- First Select Tower --</option>
                                </select>
                                <input type="text"
                                       class="form-control mt-2 d-none"
                                       id="well_number_new"
                                       placeholder="Enter new well number (e.g., AW-01)">
                                @error('well_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    <i class="fe fe-info"></i> Select a tower first to see available wells
                                </small>
                            </div>
                        </div>

                        <!-- String Number -->
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="string_number" class="form-label">
                                    <strong>String Number</strong> <span class="text-danger">*</span>
                                </label>
                                <select class="form-control @error('string_number') is-invalid @enderror"
                                        id="string_number"
                                        name="string_number"
                                        required
                                        disabled>
                                    <option value="">-- First Select Well --</option>
                                </select>
                                <input type="text"
                                       class="form-control mt-2 d-none"
                                       id="string_number_new"
                                       placeholder="Enter new string number (e.g., 1)">
                                @error('string_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    <i class="fe fe-info"></i> Select a well first to see available strings
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Tower -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tower" class="form-label">
                                    <strong>Tower</strong>
                                </label>
                                <select class="form-control @error('tower') is-invalid @enderror"
                                        id="tower"
                                        name="tower">
                                    <option value="">-- Select Tower --</option>
                                    @foreach($towers as $tower)
                                        <option value="{{ $tower->name }}"
                                                data-tower-id="{{ $tower->id }}"
                                                {{ old('tower') == $tower->name ? 'selected' : '' }}>
                                            {{ $tower->name }}
                                        </option>
                                    @endforeach
                                    <option value="__new__">+ Add New Tower</option>
                                </select>
                                <input type="text"
                                       class="form-control mt-2 d-none"
                                       id="tower_new"
                                       placeholder="Enter new tower name (e.g., T-01)">
                                @error('tower')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Flow Route -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="flow_route" class="form-label">
                                    <strong>Flow Route</strong>
                                </label>
                                <input type="text"
                                       class="form-control @error('flow_route') is-invalid @enderror"
                                       id="flow_route"
                                       name="flow_route"
                                       value="{{ old('flow_route') }}"
                                       placeholder="e.g., Route A">
                                @error('flow_route')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Reservoir Layer -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="reservoir_layer" class="form-label">
                                    <strong>Reservoir Layer</strong>
                                </label>
                                <input type="text"
                                       class="form-control @error('reservoir_layer') is-invalid @enderror"
                                       id="reservoir_layer"
                                       name="reservoir_layer"
                                       value="{{ old('reservoir_layer') }}"
                                       placeholder="e.g., Upper Zone">
                                @error('reservoir_layer')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Test Location -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="test_location" class="form-label">
                                    <strong>Test Location</strong>
                                </label>
                                <select class="form-control @error('test_location') is-invalid @enderror"
                                        id="test_location"
                                        name="test_location">
                                    <option value="">-- Select Location --</option>
                                    <option value="Locally" {{ old('test_location') === 'Locally' ? 'selected' : '' }}>Locally</option>
                                    <option value="Scada" {{ old('test_location') === 'Scada' ? 'selected' : '' }}>Scada</option>
                                </select>
                                @error('test_location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Electrification Status -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="electrification_status" class="form-label">
                                    <strong>Electrification Status</strong>
                                </label>
                                <select class="form-control @error('electrification_status') is-invalid @enderror"
                                        id="electrification_status"
                                        name="electrification_status">
                                    <option value="">-- Select --</option>
                                    <option value="Y" {{ old('electrification_status') === 'Y' ? 'selected' : '' }}>Y (Yes)</option>
                                    <option value="N" {{ old('electrification_status') === 'N' ? 'selected' : '' }}>N (No)</option>
                                </select>
                                @error('electrification_status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- MPFM Type -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="mpfm_type" class="form-label">
                                    <strong>MPFM Type</strong>
                                </label>
                                <input type="text"
                                       class="form-control @error('mpfm_type') is-invalid @enderror"
                                       id="mpfm_type"
                                       name="mpfm_type"
                                       value="{{ old('mpfm_type') }}"
                                       placeholder="e.g., Type A">
                                @error('mpfm_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- DHSV Type -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="dhsv_type" class="form-label">
                                    <strong>DHSV Type</strong>
                                </label>
                                <input type="text"
                                       class="form-control @error('dhsv_type') is-invalid @enderror"
                                       id="dhsv_type"
                                       name="dhsv_type"
                                       value="{{ old('dhsv_type') }}"
                                       placeholder="e.g., Standard">
                                @error('dhsv_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Priority -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="priority" class="form-label">
                                    <strong>Priority</strong>
                                </label>
                                <select class="form-control @error('priority') is-invalid @enderror"
                                        id="priority"
                                        name="priority">
                                    <option value="normal" {{ old('priority', 'normal') === 'normal' ? 'selected' : '' }}>Normal</option>
                                    <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High</option>
                                    <option value="top" {{ old('priority') === 'top' ? 'selected' : '' }}>Top</option>
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Sub Category -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sub_category" class="form-label">
                                    <strong>Sub Category</strong>
                                </label>
                                <input type="text"
                                       class="form-control @error('sub_category') is-invalid @enderror"
                                       id="sub_category"
                                       name="sub_category"
                                       value="{{ old('sub_category') }}"
                                       placeholder="e.g., Production">
                                @error('sub_category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Category -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="category" class="form-label">
                                    <strong>Category</strong>
                                </label>
                                <input type="text"
                                       class="form-control @error('category') is-invalid @enderror"
                                       id="category"
                                       name="category"
                                       value="{{ old('category') }}"
                                       placeholder="e.g., Main">
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Required WHFP -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="required_whfp" class="form-label">
                                    <strong>Required WHFP</strong>
                                </label>
                                <input type="number"
                                       step="0.01"
                                       class="form-control @error('required_whfp') is-invalid @enderror"
                                       id="required_whfp"
                                       name="required_whfp"
                                       value="{{ old('required_whfp') }}"
                                       placeholder="e.g., 100.50">
                                @error('required_whfp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Production Rate -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="production_rate" class="form-label">
                                    <strong>Production Rate</strong>
                                </label>
                                <input type="text"
                                       class="form-control @error('production_rate') is-invalid @enderror"
                                       id="production_rate"
                                       name="production_rate"
                                       value="{{ old('production_rate') }}"
                                       placeholder="e.g., 500 BPD">
                                @error('production_rate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Scheduled Date -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="scheduled_date" class="form-label">
                                    <strong>Scheduled Date</strong>
                                </label>
                                <input type="date"
                                       class="form-control @error('scheduled_date') is-invalid @enderror"
                                       id="scheduled_date"
                                       name="scheduled_date"
                                       value="{{ old('scheduled_date') }}">
                                @error('scheduled_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Supervisor Remarks -->
                    <div class="form-group">
                        <label for="supervisor_remarks" class="form-label">
                            <strong>Supervisor Remarks</strong>
                        </label>
                        <textarea class="form-control @error('supervisor_remarks') is-invalid @enderror"
                                  id="supervisor_remarks"
                                  name="supervisor_remarks"
                                  rows="2"
                                  placeholder="Add any remarks or special instructions...">{{ old('supervisor_remarks') }}</textarea>
                        @error('supervisor_remarks')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- HQ Notes -->
                    <div class="form-group">
                        <label for="hq_notes" class="form-label">
                            <strong>HQ Notes</strong>
                        </label>
                        <textarea class="form-control @error('hq_notes') is-invalid @enderror"
                                  id="hq_notes"
                                  name="hq_notes"
                                  rows="2"
                                  placeholder="Add notes from headquarters...">{{ old('hq_notes') }}</textarea>
                        @error('hq_notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3" style="border-top: 1px solid #e5e7eb;">
                        <a href="{{ route('monthly-plans.show', $monthlyPlan) }}" class="btn btn-light">
                            <i class="fe fe-arrow-left"></i> Back to Plan
                        </a>
                        <div>
                            <button type="submit" name="action" value="save" class="btn btn-success btn-lg mr-2">
                                <i class="fe fe-check"></i> Add Task
                            </button>
                            <button type="submit" name="action" value="save_and_new" class="btn btn-primary btn-lg">
                                <i class="fe fe-plus"></i> Add & Create Another
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.form-label {
    margin-bottom: 0.5rem;
}
.form-group {
    margin-bottom: 1.5rem;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    let selectedTowerId = null;
    let selectedWellNumber = null;

    // Handle Tower selection - Load wells for selected tower
    $('#tower').on('change', function() {
        var towerName = $(this).val();

        if (towerName === '__new__') {
            $('#tower_new').removeClass('d-none').focus();
            // Disable well and string dropdowns
            $('#well_number').prop('disabled', true).html('<option value="">-- First Select Tower --</option>');
            $('#string_number').prop('disabled', true).html('<option value="">-- First Select Well --</option>');
            return;
        } else {
            $('#tower_new').addClass('d-none').val('');
        }

        if (!towerName) {
            // Reset well and string dropdowns
            $('#well_number').prop('disabled', true).html('<option value="">-- First Select Tower --</option>');
            $('#string_number').prop('disabled', true).html('<option value="">-- First Select Well --</option>');
            selectedTowerId = null;
            return;
        }

        // Find tower ID from the selected tower name
        selectedTowerId = $('option:selected', this).data('tower-id');

        // Load wells for this tower via AJAX
        $.ajax({
            url: '{{ route("monthly-plans.api.wells-by-tower", ":tower") }}'.replace(':tower', selectedTowerId),
            type: 'GET',
            success: function(wells) {
                if (wells.error) {
                    alert('Error: ' + wells.error);
                    return;
                }

                $('#well_number').prop('disabled', false).html('<option value="">-- Select Well Number --</option>');

                if (wells && wells.length > 0) {
                    wells.forEach(function(well) {
                        $('#well_number').append(`<option value="${well}">${well}</option>`);
                    });
                }

                $('#well_number').append('<option value="__new__">+ Add New Well Number</option>');

                // Reset string dropdown
                $('#string_number').prop('disabled', true).html('<option value="">-- First Select Well --</option>');
                selectedWellNumber = null;
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                alert('Error loading wells. Please try again.');
                $('#well_number').prop('disabled', true).html('<option value="">-- Error Loading Wells --</option>');
            }
        });
    });

    // Handle Well Number selection - Load strings for selected well
    $('#well_number').on('change', function() {
        var wellNumber = $(this).val();

        if (wellNumber === '__new__') {
            $('#well_number_new').removeClass('d-none').focus();
            $(this).prop('required', false);
            $('#string_number').prop('disabled', true).html('<option value="">-- First Select Well --</option>');
            return;
        } else {
            $('#well_number_new').addClass('d-none').val('');
            $(this).prop('required', true);
        }

        if (!wellNumber || !selectedTowerId) {
            $('#string_number').prop('disabled', true).html('<option value="">-- First Select Well --</option>');
            return;
        }

        selectedWellNumber = wellNumber;

        // Load strings for this well via AJAX
        $.ajax({
            url: '{{ route("monthly-plans.api.strings-by-well") }}',
            type: 'GET',
            data: {
                tower_id: selectedTowerId,
                well_number: wellNumber
            },
            success: function(strings) {
                if (strings.error) {
                    alert('Error: ' + strings.error);
                    return;
                }

                $('#string_number').prop('disabled', false).html('<option value="">-- Select String Number --</option>');

                if (strings && strings.length > 0) {
                    strings.forEach(function(string) {
                        $('#string_number').append(`<option value="${string}">${string}</option>`);
                    });
                }

                $('#string_number').append('<option value="__new__">+ Add New String Number</option>');
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                alert('Error loading strings. Please try again.');
                $('#string_number').prop('disabled', true).html('<option value="">-- Error Loading Strings --</option>');
            }
        });
    });

    // Handle String Number "Add New" selection
    $('#string_number').on('change', function() {
        if ($(this).val() === '__new__') {
            $('#string_number_new').removeClass('d-none').focus();
            $(this).prop('required', false);
        } else {
            $('#string_number_new').addClass('d-none').val('');
            $(this).prop('required', true);
        }
    });

    // On form submit, validate and use new values if entered
    $('form').on('submit', function(e) {
        console.log('Form submit handler triggered');

        // Remove any previously added hidden inputs to prevent duplicates
        $(this).find('input[type="hidden"][data-dynamic="true"]').remove();

        // Check if well_number is disabled (tower not selected)
        if ($('#well_number').prop('disabled')) {
            e.preventDefault();
            alert('Please select a tower first to choose a well number.');
            $('#tower').focus();
            return false;
        }

        // Check if string_number is disabled (well not selected)
        if ($('#string_number').prop('disabled')) {
            e.preventDefault();
            alert('Please select a well number first to choose a string number.');
            $('#well_number').focus();
            return false;
        }

        // Tower
        if ($('#tower').val() === '__new__') {
            var newTower = $('#tower_new').val().trim();
            if (newTower === '') {
                // Tower is optional, so just clear it and remove __new__ value
                $('#tower').val('');
            } else {
                // Add hidden input with actual value
                $('<input>').attr({
                    type: 'hidden',
                    name: 'tower',
                    value: newTower,
                    'data-dynamic': 'true'
                }).appendTo($(this));
            }
        }

        // Well Number
        if ($('#well_number').val() === '__new__') {
            var newWellNumber = $('#well_number_new').val().trim();
            if (newWellNumber === '') {
                e.preventDefault();
                alert('Please enter a well number or select an existing one.');
                $('#well_number_new').focus();
                return false;
            }
            // Add hidden input with actual value
            $('<input>').attr({
                type: 'hidden',
                name: 'well_number',
                value: newWellNumber,
                'data-dynamic': 'true'
            }).appendTo($(this));
        }

        // String Number
        if ($('#string_number').val() === '__new__') {
            var newStringNumber = $('#string_number_new').val().trim();
            if (newStringNumber === '') {
                e.preventDefault();
                alert('Please enter a string number or select an existing one.');
                $('#string_number_new').focus();
                return false;
            }
            // Add hidden input with actual value
            $('<input>').attr({
                type: 'hidden',
                name: 'string_number',
                value: newStringNumber,
                'data-dynamic': 'true'
            }).appendTo($(this));
        }

        console.log('Form validation passed, submitting...');
        // Form will submit normally after this
    });
});
</script>
@endpush
