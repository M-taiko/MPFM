@extends('layouts.master')

@section('title', 'Create Test Request')

@section('content')
<div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Create New Test Request</h3>
                <div class="card-options">
                    <a href="{{ route('tv_test_requests.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fe fe-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>Please fix the following errors:</strong>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('tv_test_requests.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="test_vessel_id">Testing Unit <span class="text-danger">*</span></label>
                                <select name="test_vessel_id" id="test_vessel_id"
                                    class="form-control @error('test_vessel_id') is-invalid @enderror" required>
                                    <option value="">-- Select Testing Unit --</option>
                                    @foreach($testVessels as $vessel)
                                    <option value="{{ $vessel->id }}" data-oil-field="{{ $vessel->oil_field_id }}"
                                        {{ old('test_vessel_id') == $vessel->id ? 'selected' : '' }}>
                                        {{ $vessel->name }} ({{ $vessel->code }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('test_vessel_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tower_id">Tower <span class="text-danger">*</span></label>
                                <select name="tower_id" id="tower_id"
                                    class="form-control @error('tower_id') is-invalid @enderror" required>
                                    <option value="">-- Select Tower --</option>
                                </select>
                                @error('tower_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="test_date">Test Date <span class="text-danger">*</span></label>
                                <input type="date" name="test_date" id="test_date"
                                    class="form-control @error('test_date') is-invalid @enderror"
                                    value="{{ old('test_date') }}" required>
                                @error('test_date')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="shift">Shift <span class="text-danger">*</span></label>
                                <select name="shift" id="shift" class="form-control @error('shift') is-invalid @enderror" required>
                                    <option value="">-- Select Shift --</option>
                                    <option value="morning" {{ old('shift') == 'morning' ? 'selected' : '' }}>Morning</option>
                                    <option value="afternoon" {{ old('afternoon') == 'afternoon' ? 'selected' : '' }}>Afternoon</option>
                                    <option value="night" {{ old('shift') == 'night' ? 'selected' : '' }}>Night</option>
                                </select>
                                @error('shift')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Oil Strings <span class="text-danger">*</span></label>
                        <div id="oilStringsContainer" class="border rounded p-3" style="min-height: 150px;">
                            <p class="text-muted">Select a tower first to load oil strings</p>
                        </div>
                        @error('oil_string_ids')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea name="notes" id="notes" rows="3"
                            class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                        @error('notes')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Hidden field for oil_field_id -->
                    <input type="hidden" name="oil_field_id" id="oil_field_id" value="{{ old('oil_field_id') }}">

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fe fe-save"></i> Create Request
                        </button>
                        <a href="{{ route('tv_test_requests.index') }}" class="btn btn-secondary">
                            <i class="fe fe-x"></i> Cancel
                        </a>
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
    // When vessel is selected, load towers
    $('#test_vessel_id').on('change', function() {
        var vesselId = $(this).val();
        var oilFieldId = $(this).find(':selected').data('oil-field');

        if (!vesselId) {
            $('#tower_id').html('<option value="">-- Select Tower --</option>');
            $('#oilStringsContainer').html('<p class="text-muted">Select a tower first to load oil strings</p>');
            return;
        }

        // Set the oil_field_id
        $('#oil_field_id').val(oilFieldId);

        // Load towers via AJAX
        $.get('/ajax/operational-plans/towers/' + oilFieldId, function(data) {
            var towerSelect = $('#tower_id');
            towerSelect.html('<option value="">-- Select Tower --</option>');
            $.each(data, function(index, tower) {
                towerSelect.append('<option value="' + tower.id + '">' + tower.name + '</option>');
            });
        });
    });

    // When tower is selected, load oil strings
    $('#tower_id').on('change', function() {
        var towerId = $(this).val();

        if (!towerId) {
            $('#oilStringsContainer').html('<p class="text-muted">Select a tower first to load oil strings</p>');
            return;
        }

        // Load oil strings via AJAX
        $.get('/ajax/operational-plans/oil-strings/' + towerId, function(data) {
            var html = '';
            $.each(data, function(index, string) {
                html += '<div class="form-check">';
                html += '<input class="form-check-input" type="checkbox" name="oil_string_ids[]" value="' + string.id + '" id="oilString_' + string.id + '">';
                html += '<label class="form-check-label" for="oilString_' + string.id + '">';
                html += string.name;
                html += '</label>';
                html += '</div>';
            });
            $('#oilStringsContainer').html(html);
        });
    });

    // Trigger vessel change if already selected
    if ($('#test_vessel_id').val()) {
        $('#test_vessel_id').trigger('change');
    }
});
</script>
@endpush
