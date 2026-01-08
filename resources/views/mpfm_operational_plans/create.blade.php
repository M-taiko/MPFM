@extends('layouts.master')

@section('title', 'Create MPFM Operational Plan')

@section('content')
<div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Create New MPFM Operational Plan</h3>
                <div class="card-options">
                    <a href="{{ route('mpfm_operational_plans.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fe fe-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('mpfm_operational_plans.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Assigned Operator <span class="text-danger">*</span></label>
                                <select class="form-control @error('assigned_user_id') is-invalid @enderror"
                                        name="assigned_user_id" required>
                                    <option value="">Select Operator</option>
                                    @foreach($operators as $operator)
                                    <option value="{{ $operator->id }}" {{ old('assigned_user_id') == $operator->id ? 'selected' : '' }}>
                                        {{ $operator->name }} ({{ $operator->email }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('assigned_user_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Plan Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Oil Field <span class="text-danger">*</span></label>
                                <select class="form-control @error('oil_field_id') is-invalid @enderror"
                                        name="oil_field_id" id="oil_field_id" required>
                                    <option value="">Select Oil Field</option>
                                    @foreach($oilFields as $field)
                                    <option value="{{ $field->id }}" {{ old('oil_field_id') == $field->id ? 'selected' : '' }}>
                                        {{ $field->name }} ({{ $field->code }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('oil_field_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Tower <span class="text-danger">*</span></label>
                                <select class="form-control @error('tower_id') is-invalid @enderror"
                                        name="tower_id" id="tower_id" required>
                                    <option value="">Select Oil Field First</option>
                                </select>
                                @error('tower_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Plan Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('plan_date') is-invalid @enderror"
                                       name="plan_date" value="{{ old('plan_date', date('Y-m-d')) }}" required>
                                @error('plan_date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Shift</label>
                                <select class="form-control @error('shift') is-invalid @enderror" name="shift">
                                    <option value="">Select Shift</option>
                                    <option value="morning" {{ old('shift') == 'morning' ? 'selected' : '' }}>Morning</option>
                                    <option value="afternoon" {{ old('shift') == 'afternoon' ? 'selected' : '' }}>Afternoon</option>
                                    <option value="night" {{ old('shift') == 'night' ? 'selected' : '' }}>Night</option>
                                </select>
                                @error('shift')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Assigned Oil Strings <span class="text-danger">*</span></label>
                        <div id="oilStringsContainer">
                            <p class="text-muted">Please select a tower first to see available oil strings.</p>
                        </div>
                        @error('assigned_oil_string_ids')
                            <span class="text-danger" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror"
                                  name="notes" rows="3">{{ old('notes') }}</textarea>
                        @error('notes')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fe fe-save"></i> Create Plan
                        </button>
                        <a href="{{ route('mpfm_operational_plans.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#oil_field_id').on('change', function() {
        var oilFieldId = $(this).val();
        var towerSelect = $('#tower_id');

        towerSelect.html('<option value="">Loading...</option>');

        if (oilFieldId) {
            $.ajax({
                url: '{{ url('mpfm-operational-plans/towers') }}/' + oilFieldId,
                type: 'GET',
                success: function(data) {
                    towerSelect.html('<option value="">Select Tower</option>');
                    $.each(data, function(key, value) {
                        towerSelect.append('<option value="' + value.id + '">' + value.name + ' (' + value.code + ')</option>');
                    });
                }
            });
        } else {
            towerSelect.html('<option value="">Select Oil Field First</option>');
        }

        $('#oilStringsContainer').html('<p class="text-muted">Please select a tower first to see available oil strings.</p>');
    });

    $('#tower_id').on('change', function() {
        var towerId = $(this).val();
        var container = $('#oilStringsContainer');

        if (towerId) {
            $.ajax({
                url: '{{ url('mpfm-operational-plans/oil-strings') }}/' + towerId,
                type: 'GET',
                success: function(data) {
                    var html = '<div class="row">';
                    $.each(data, function(key, value) {
                        html += '<div class="col-md-4">';
                        html += '<div class="form-check">';
                        html += '<input class="form-check-input" type="checkbox" name="assigned_oil_string_ids[]" value="' + value.id + '" id="string_' + value.id + '">';
                        html += '<label class="form-check-label" for="string_' + value.id + '">' + value.name + ' (' + value.code + ')</label>';
                        html += '</div>';
                        html += '</div>';
                    });
                    html += '</div>';
                    container.html(html);
                }
            });
        } else {
            container.html('<p class="text-muted">Please select a tower first to see available oil strings.</p>');
        }
    });
});
</script>
@endpush

