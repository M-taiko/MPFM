@extends('layouts.master')

@section('title', 'إضافة تاسك - ' . $monthlyPlan->plan_name)

@section('content')
<!-- Page Header -->
<div class="page-header mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title mb-2">إضافة تاسك جديد</h1>
            <p class="text-muted mb-0">Plan: <strong>{{ $monthlyPlan->plan_name }}</strong></p>
        </div>
        <a href="{{ route('monthly-plans.show', $monthlyPlan) }}" class="btn btn-secondary">
            <i class="fe fe-arrow-left"></i> عودة للخطة
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
                        <h5 class="card-title mb-0">تفاصيل التاسك</h5>
                        <small style="opacity: 0.9;">اختر القسم ثم املأ البيانات المطلوبة</small>
                    </div>
                </div>
            </div>
            <div class="card-body" style="padding: 30px;">
                @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h5><i class="fe fe-alert-triangle"></i> تنبيه - خطأ في البيانات</h5>
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
                        <h6 class="text-uppercase mb-3" style="font-weight: 700; color: #667eea;">
                            <i class="fe fe-filter"></i> اختر القسم
                        </h6>
                        <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="department_id" class="form-label" style="font-weight: 600;">
                                    القسم <span class="badge badge-danger">مطلوب</span>
                                </label>
                                <select class="form-control form-control-lg @error('department_id') is-invalid @enderror"
                                        id="department_id"
                                        name="department_id"
                                        style="border-radius: 8px;"
                                        required>
                                    <option value="">-- اختر القسم --</option>
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

                        <!-- SNO -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="sno" class="form-label" style="font-weight: 600;">
                                    رقم التسلسل <span class="badge badge-danger">مطلوب</span>
                                </label>
                                <input type="number"
                                       class="form-control form-control-lg @error('sno') is-invalid @enderror"
                                       id="sno"
                                       name="sno"
                                       value="{{ old('sno', $monthlyPlan->tasks()->count() + 1) }}"
                                       style="border-radius: 8px;"
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
                        <h6 class="text-uppercase mb-3" style="font-weight: 700; color: #667eea;">
                            <i class="fe fe-map-pin"></i> معلومات المئبر والبئر
                        </h6>
                        <div class="row">
                        <!-- Tower -->
                        <div class="col-md-6">
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
                                <label for="tower" class="form-label" style="font-weight: 600;">المئبر</label>
                                <select class="form-control @error('tower') is-invalid @enderror"
                                        id="tower"
                                        name="tower"
                                        style="border-radius: 8px;">
                                    <option value="">-- اختر المئبر --</option>
                                    @foreach($towers as $tower)
                                        <option value="{{ $tower->name }}"
                                                data-tower-id="{{ $tower->id }}"
                                                {{ old('tower') == $tower->name ? 'selected' : '' }}>
                                            {{ $tower->name }}
                                        </option>
                                    @endforeach
                                    <option value="__new__">+ إضافة مئبر جديد</option>
                                </select>
                                <input type="text"
                                       class="form-control mt-2 d-none"
                                       id="tower_new"
                                       style="border-radius: 8px;"
                                       placeholder="أدخل اسم المئبر الجديد">
                                @error('tower')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Well Number -->
                        <div class="col-md-6">
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
    let currentDepartmentConfig = null;

    // ============================================================
    // Department-Specific Fields Handling
    // ============================================================

    /**
     * Update form fields based on department selection
     * Shows/hides fields according to department-specific configuration
     */
    function updateFormFieldsForDepartment(config) {
        if (!config) return;

        currentDepartmentConfig = config;

        // Update section title and help text
        const formCard = $('.card').find('.card-header');
        if (config.title) {
            formCard.find('.card-title').text(config.title);
        }

        // Get all form field containers
        const requiredFields = config.required_fields || [];
        const optionalFields = config.optional_fields || [];
        const hiddenFields = config.hidden_fields || [];
        const allFieldIds = [
            'tower', 'flow_route', 'reservoir_layer', 'test_location',
            'electrification_status', 'mpfm_type', 'dhsv_type', 'priority',
            'required_whfp', 'production_rate', 'scheduled_date',
            'sub_category', 'category', 'supervisor_remarks', 'hq_notes'
        ];

        // Process each field
        allFieldIds.forEach(function(fieldId) {
            const field = $('#' + fieldId).closest('.form-group');
            const label = field.find('label');

            if (!field.length) return;

            if (hiddenFields.includes(fieldId)) {
                // Hide the field
                field.hide();
                field.find('input, select, textarea').prop('disabled', true);
            } else {
                // Show the field
                field.show();
                field.find('input, select, textarea').prop('disabled', false);

                // Update required status
                if (requiredFields.includes(fieldId)) {
                    // Mark as required
                    if (!label.find('.text-danger').length) {
                        label.append('<span class="text-danger"> *</span>');
                    }
                    field.find('input, select, textarea').prop('required', true);
                } else {
                    // Mark as optional
                    label.find('.text-danger').remove();
                    field.find('input, select, textarea').prop('required', false);
                }
            }
        });

        // Add help text if provided
        if (config.help_text) {
            const helpAlert = $('.card').find('.alert-info');
            if (helpAlert.length) {
                helpAlert.html('<i class="fe fe-info"></i> ' + config.help_text);
            } else {
                // Create help alert if it doesn't exist
                const newAlert = $('<div class="alert alert-info alert-dismissible fade show" role="alert">')
                    .html('<i class="fe fe-info"></i> ' + config.help_text)
                    .prepend('<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>');
                $('.card-body').first().prepend(newAlert);
            }
        }
    }

    /**
     * Handle department selection change
     */
    $('#department_id').on('change', function() {
        const departmentId = $(this).val();

        if (!departmentId) {
            currentDepartmentConfig = null;
            return;
        }

        // Call AJAX endpoint to get department-specific field configuration
        $.ajax({
            url: '{{ route("monthly-plans.api.department-fields", ":department") }}'.replace(':department', departmentId),
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success && response.config) {
                    updateFormFieldsForDepartment(response.config);
                    console.log('Department ' + response.department_name + ' fields updated');
                } else {
                    console.error('Invalid response format:', response);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading department fields:', status, error);
                alert('Error loading department configuration. Please refresh and try again.');
            }
        });
    });

    // Initialize with pre-selected department if any
    const selectedDept = $('#department_id').val();
    if (selectedDept) {
        $('#department_id').trigger('change');
    }

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
