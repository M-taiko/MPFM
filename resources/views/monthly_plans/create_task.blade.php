@extends('layouts.master')

@section('title', 'Create Task - ' . $monthlyPlan->plan_name)

@section('content')
<!-- Page Header -->
<div class="page-header mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title mb-2">Create New Task</h1>
            <p class="text-muted mb-0">Plan: <strong>{{ $monthlyPlan->plan_name }}</strong></p>
        </div>
        <a href="{{ route('monthly-plans.show', $monthlyPlan) }}" class="btn btn-secondary">
            <i class="fe fe-arrow-left"></i> Back to Plan
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-10 mx-auto">
        <!-- Task Form -->
        <div class="card" style="border-left: 5px solid #667eea; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
            <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-bottom: none;">
                <div class="d-flex align-items-center">
                    <i class="fe fe-plus-circle" style="font-size: 24px; margin-right: 10px;"></i>
                    <div>
                        <h5 class="card-title mb-0">New Task Details</h5>
                        <small style="opacity: 0.9;">Select Department and fill in the required fields</small>
                    </div>
                </div>
            </div>
            <div class="card-body" style="padding: 30px;">
                @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h5><i class="fe fe-alert-triangle"></i> Error - Invalid Data</h5>
                    <ul class="mb-0" style="margin-top: 10px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
                @endif

                <form action="{{ route('monthly-plans.tasks.store', $monthlyPlan) }}" method="POST">
                    @csrf

                    <!-- Step 1: Department Selection -->
                    <div class="mb-4 pb-4" style="border-bottom: 2px solid #f0f0f0;">
                        <h6 class="text-uppercase mb-3" style="font-weight: 700; color: #667eea; font-size: 13px;">
                            <i class="fe fe-filter"></i> 1. Select Department
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label for="department_id" class="form-label" style="font-weight: 600;">
                                        Department <span class="badge badge-danger">Required</span>
                                    </label>
                                    <select class="form-control form-control-lg @error('department_id') is-invalid @enderror"
                                            id="department_id"
                                            name="department_id"
                                            style="border-radius: 8px; border: 2px solid #e5e7eb;"
                                            required>
                                        <option value="">-- Select Department --</option>
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept->id }}"
                                                    {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                                {{ $dept->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('department_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group mb-0">
                                    <label for="sno" class="form-label" style="font-weight: 600;">
                                        Sequence <span class="badge badge-danger">Required</span>
                                    </label>
                                    <input type="number"
                                           class="form-control form-control-lg @error('sno') is-invalid @enderror"
                                           id="sno"
                                           name="sno"
                                           value="{{ old('sno', $monthlyPlan->tasks()->count() + 1) }}"
                                           style="border-radius: 8px; border: 2px solid #e5e7eb;"
                                           min="1"
                                           required>
                                    @error('sno')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Basic Information -->
                    <div class="mb-4 pb-4" style="border-bottom: 2px solid #f0f0f0;">
                        <h6 class="text-uppercase mb-3" style="font-weight: 700; color: #667eea; font-size: 13px;">
                            <i class="fe fe-map-pin"></i> 2. Tower, Well & Layer Information
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tower" class="form-label" style="font-weight: 600;">Tower</label>
                                    <select class="form-control @error('tower') is-invalid @enderror"
                                            id="tower"
                                            name="tower"
                                            style="border-radius: 8px;">
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
                                           style="border-radius: 8px;"
                                           placeholder="Enter new tower name">
                                    @error('tower')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="well_number" class="form-label" style="font-weight: 600;">
                                        Well <span class="badge badge-danger">Required</span>
                                    </label>
                                    <select class="form-control @error('well_number') is-invalid @enderror"
                                            id="well_number"
                                            name="well_number"
                                            style="border-radius: 8px;"
                                            required
                                            disabled>
                                        <option value="">-- Select Tower First --</option>
                                    </select>
                                    <input type="text"
                                           class="form-control mt-2 d-none"
                                           id="well_number_new"
                                           style="border-radius: 8px;"
                                           placeholder="Enter new well number">
                                    @error('well_number')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="string_number" class="form-label" style="font-weight: 600;">
                                        String <span class="badge badge-danger">Required</span>
                                    </label>
                                    <select class="form-control @error('string_number') is-invalid @enderror"
                                            id="string_number"
                                            name="string_number"
                                            style="border-radius: 8px;"
                                            required
                                            disabled>
                                        <option value="">-- Select Well First --</option>
                                    </select>
                                    <input type="text"
                                           class="form-control mt-2 d-none"
                                           id="string_number_new"
                                           style="border-radius: 8px;"
                                           placeholder="Enter new string number">
                                    @error('string_number')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="reservoir_layer" class="form-label" style="font-weight: 600;">Reservoir Layer</label>
                                    <input type="text"
                                           class="form-control @error('reservoir_layer') is-invalid @enderror"
                                           id="reservoir_layer"
                                           name="reservoir_layer"
                                           value="{{ old('reservoir_layer') }}"
                                           style="border-radius: 8px;"
                                           placeholder="E.g., Upper Layer">
                                    @error('reservoir_layer')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Configuration Details -->
                    <div class="mb-4 pb-4" style="border-bottom: 2px solid #f0f0f0;">
                        <h6 class="text-uppercase mb-3" style="font-weight: 700; color: #667eea; font-size: 13px;">
                            <i class="fe fe-settings"></i> 3. Configuration Details
                        </h6>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="mpfm_type" class="form-label" style="font-weight: 600;">MPFM Type</label>
                                    <input type="text"
                                           class="form-control @error('mpfm_type') is-invalid @enderror"
                                           id="mpfm_type"
                                           name="mpfm_type"
                                           value="{{ old('mpfm_type') }}"
                                           style="border-radius: 8px;"
                                           placeholder="E.g., Type A">
                                    @error('mpfm_type')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="electrification_status" class="form-label" style="font-weight: 600;">Electrification Status</label>
                                    <select class="form-control @error('electrification_status') is-invalid @enderror"
                                            id="electrification_status"
                                            name="electrification_status"
                                            style="border-radius: 8px;">
                                        <option value="">-- Select --</option>
                                        <option value="Y" {{ old('electrification_status') === 'Y' ? 'selected' : '' }}>Yes</option>
                                        <option value="N" {{ old('electrification_status') === 'N' ? 'selected' : '' }}>No</option>
                                    </select>
                                    @error('electrification_status')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="test_location" class="form-label" style="font-weight: 600;">Test Location</label>
                                    <select class="form-control @error('test_location') is-invalid @enderror"
                                            id="test_location"
                                            name="test_location"
                                            style="border-radius: 8px;">
                                        <option value="">-- Select --</option>
                                        <option value="Locally" {{ old('test_location') === 'Locally' ? 'selected' : '' }}>Locally</option>
                                        <option value="Scada" {{ old('test_location') === 'Scada' ? 'selected' : '' }}>Scada</option>
                                    </select>
                                    @error('test_location')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: Additional Information -->
                    <div class="mb-4 pb-4" style="border-bottom: 2px solid #f0f0f0;">
                        <h6 class="text-uppercase mb-3" style="font-weight: 700; color: #667eea; font-size: 13px;">
                            <i class="fe fe-info"></i> 4. Additional Information
                        </h6>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="priority" class="form-label" style="font-weight: 600;">Priority</label>
                                    <select class="form-control @error('priority') is-invalid @enderror"
                                            id="priority"
                                            name="priority"
                                            style="border-radius: 8px;">
                                        <option value="normal" {{ old('priority', 'normal') === 'normal' ? 'selected' : '' }}>Normal</option>
                                        <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High</option>
                                        <option value="top" {{ old('priority') === 'top' ? 'selected' : '' }}>Top</option>
                                    </select>
                                    @error('priority')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="production_rate" class="form-label" style="font-weight: 600;">Production Rate</label>
                                    <input type="text"
                                           class="form-control @error('production_rate') is-invalid @enderror"
                                           id="production_rate"
                                           name="production_rate"
                                           value="{{ old('production_rate') }}"
                                           style="border-radius: 8px;"
                                           placeholder="E.g., 500 BPD">
                                    @error('production_rate')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="scheduled_date" class="form-label" style="font-weight: 600;">Scheduled Date</label>
                                    <input type="date"
                                           class="form-control @error('scheduled_date') is-invalid @enderror"
                                           id="scheduled_date"
                                           name="scheduled_date"
                                           value="{{ old('scheduled_date') }}"
                                           style="border-radius: 8px;">
                                    @error('scheduled_date')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="supervisor_remarks" class="form-label" style="font-weight: 600;">Supervisor Remarks</label>
                                    <textarea class="form-control @error('supervisor_remarks') is-invalid @enderror"
                                              id="supervisor_remarks"
                                              name="supervisor_remarks"
                                              rows="2"
                                              style="border-radius: 8px;"
                                              placeholder="Add any remarks or special instructions...">{{ old('supervisor_remarks') }}</textarea>
                                    @error('supervisor_remarks')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="hq_notes" class="form-label" style="font-weight: 600;">HQ Notes</label>
                                    <textarea class="form-control @error('hq_notes') is-invalid @enderror"
                                              id="hq_notes"
                                              name="hq_notes"
                                              rows="2"
                                              style="border-radius: 8px;"
                                              placeholder="Add notes from headquarters...">{{ old('hq_notes') }}</textarea>
                                    @error('hq_notes')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" name="action" value="save" class="btn btn-success btn-lg" style="border-radius: 8px; padding: 10px 30px;">
                            <i class="fe fe-save"></i> Save Task
                        </button>
                        <button type="submit" name="action" value="save_and_new" class="btn btn-primary btn-lg" style="border-radius: 8px; padding: 10px 30px;">
                            <i class="fe fe-plus"></i> Save and Add Another
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        margin-bottom: 0.5rem;
        font-size: 14px;
    }

    .form-control, .form-control-lg {
        border: 2px solid #e5e7eb;
        border-radius: 8px !important;
        padding: 10px 15px;
        font-size: 14px;
    }

    .form-control:focus, .form-control-lg:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .badge {
        font-size: 11px;
        padding: 3px 8px;
        margin-left: 5px;
    }

    .btn-lg {
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-lg:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .card {
        border: none;
        border-radius: 12px;
    }

    .gap-2 {
        gap: 10px !important;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    let selectedTowerId = null;
    let selectedWellNumber = null;
    let currentDepartmentConfig = null;

    // Department-Specific Fields Handling
    function updateFormFieldsForDepartment(config) {
        if (!config) return;
        currentDepartmentConfig = config;

        const requiredFields = config.required_fields || [];
        const optionalFields = config.optional_fields || [];
        const hiddenFields = config.hidden_fields || [];
        const allFieldIds = [
            'tower', 'flow_route', 'reservoir_layer', 'test_location',
            'electrification_status', 'mpfm_type', 'dhsv_type', 'priority',
            'required_whfp', 'production_rate', 'scheduled_date',
            'sub_category', 'category', 'supervisor_remarks', 'hq_notes'
        ];

        allFieldIds.forEach(function(fieldId) {
            const field = $('#' + fieldId).closest('.form-group');
            const label = field.find('label');

            if (!field.length) return;

            if (hiddenFields.includes(fieldId)) {
                field.hide();
                field.find('input, select, textarea').prop('disabled', true);
            } else {
                field.show();
                field.find('input, select, textarea').prop('disabled', false);

                if (requiredFields.includes(fieldId)) {
                    if (!label.find('.badge').length) {
                        label.append('<span class="badge badge-danger">Required</span>');
                    }
                    field.find('input, select, textarea').prop('required', true);
                } else {
                    label.find('.badge').remove();
                    field.find('input, select, textarea').prop('required', false);
                }
            }
        });
    }

    $('#department_id').on('change', function() {
        const departmentId = $(this).val();
        if (!departmentId) {
            currentDepartmentConfig = null;
            return;
        }

        $.ajax({
            url: '/monthly-plans/api/department-fields/' + departmentId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success && response.config) {
                    updateFormFieldsForDepartment(response.config);
                }
            }
        });
    });

    const selectedDept = $('#department_id').val();
    if (selectedDept) {
        $('#department_id').trigger('change');
    }

    // Tower Selection
    $('#tower').on('change', function() {
        var towerName = $(this).val();

        if (towerName === '__new__') {
            $('#tower_new').removeClass('d-none').focus();
            $('#well_number').prop('disabled', true).html('<option value="">-- Select Tower First --</option>');
            $('#string_number').prop('disabled', true).html('<option value="">-- Select Well First --</option>');
            return;
        } else {
            $('#tower_new').addClass('d-none').val('');
        }

        if (!towerName) {
            $('#well_number').prop('disabled', true).html('<option value="">-- Select Tower First --</option>');
            $('#string_number').prop('disabled', true).html('<option value="">-- Select Well First --</option>');
            selectedTowerId = null;
            return;
        }

        selectedTowerId = $('option:selected', this).data('tower-id');

        $.ajax({
            url: '{{ route("monthly-plans.api.wells-by-tower", ":tower") }}'.replace(':tower', selectedTowerId),
            type: 'GET',
            success: function(wells) {
                if (wells.error) {
                    alert('Error: ' + wells.error);
                    return;
                }

                $('#well_number').prop('disabled', false).html('<option value="">-- Select Well --</option>');

                if (wells && wells.length > 0) {
                    wells.forEach(function(well) {
                        $('#well_number').append(`<option value="${well}">${well}</option>`);
                    });
                }

                $('#well_number').append('<option value="__new__">+ Add New Well</option>');
                $('#string_number').prop('disabled', true).html('<option value="">-- Select Well First --</option>');
                selectedWellNumber = null;
            }
        });
    });

    // Well Number Selection
    $('#well_number').on('change', function() {
        var wellNumber = $(this).val();

        if (wellNumber === '__new__') {
            $('#well_number_new').removeClass('d-none').focus();
            $(this).prop('required', false);
            $('#string_number').prop('disabled', true).html('<option value="">-- Select Well First --</option>');
            return;
        } else {
            $('#well_number_new').addClass('d-none').val('');
            $(this).prop('required', true);
        }

        if (!wellNumber || !selectedTowerId) {
            $('#string_number').prop('disabled', true).html('<option value="">-- Select Well First --</option>');
            return;
        }

        selectedWellNumber = wellNumber;

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

                $('#string_number').prop('disabled', false).html('<option value="">-- Select String --</option>');

                if (strings && strings.length > 0) {
                    strings.forEach(function(string) {
                        $('#string_number').append(`<option value="${string}">${string}</option>`);
                    });
                }

                $('#string_number').append('<option value="__new__">+ Add New String</option>');
            }
        });
    });

    // String Number Selection
    $('#string_number').on('change', function() {
        if ($(this).val() === '__new__') {
            $('#string_number_new').removeClass('d-none').focus();
            $(this).prop('required', false);
        } else {
            $('#string_number_new').addClass('d-none').val('');
            $(this).prop('required', true);
        }
    });

    // Form Submission
    $('form').on('submit', function(e) {
        $(this).find('input[type="hidden"][data-dynamic="true"]').remove();

        if ($('#well_number').prop('disabled')) {
            e.preventDefault();
            alert('Please select a Tower first.');
            $('#tower').focus();
            return false;
        }

        if ($('#string_number').prop('disabled')) {
            e.preventDefault();
            alert('Please select a Well first.');
            $('#well_number').focus();
            return false;
        }

        // Handle new tower
        if ($('#tower').val() === '__new__') {
            var newTower = $('#tower_new').val().trim();
            if (newTower === '') {
                $('#tower').val('');
            } else {
                $('<input>').attr({
                    type: 'hidden',
                    name: 'tower',
                    value: newTower,
                    'data-dynamic': 'true'
                }).appendTo($(this));
            }
        }

        // Handle new well number
        if ($('#well_number').val() === '__new__') {
            var newWellNumber = $('#well_number_new').val().trim();
            if (newWellNumber === '') {
                e.preventDefault();
                alert('Please enter a new Well Number.');
                $('#well_number_new').focus();
                return false;
            }
            $('<input>').attr({
                type: 'hidden',
                name: 'well_number',
                value: newWellNumber,
                'data-dynamic': 'true'
            }).appendTo($(this));
        }

        // Handle new string number
        if ($('#string_number').val() === '__new__') {
            var newStringNumber = $('#string_number_new').val().trim();
            if (newStringNumber === '') {
                e.preventDefault();
                alert('Please enter a new String Number.');
                $('#string_number_new').focus();
                return false;
            }
            $('<input>').attr({
                type: 'hidden',
                name: 'string_number',
                value: newStringNumber,
                'data-dynamic': 'true'
            }).appendTo($(this));
        }
    });
});
</script>
@endpush
