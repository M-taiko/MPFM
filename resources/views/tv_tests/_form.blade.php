<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="test_vessel_id">Test Vessel <span class="text-danger">*</span></label>
            <select name="test_vessel_id" id="test_vessel_id"
                class="form-control @error('test_vessel_id') is-invalid @enderror" required>
                <option value="">-- Select Test Vessel --</option>
                @foreach($testVessels as $vessel)
                <option value="{{ $vessel->id }}"
                    {{ old('test_vessel_id', $tvTest->test_vessel_id ?? '') == $vessel->id ? 'selected' : '' }}>
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
            <label for="test_date">Test Date <span class="text-danger">*</span></label>
            <input type="date" name="test_date" id="test_date"
                class="form-control @error('test_date') is-invalid @enderror"
                value="{{ old('test_date', $tvTest->test_date?->format('Y-m-d') ?? '') }}"
                required>
            @error('test_date')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="oil_field_id">Oil Field <span class="text-danger">*</span></label>
            <select name="oil_field_id" id="oil_field_id"
                class="form-control @error('oil_field_id') is-invalid @enderror" required>
                <option value="">-- Select Oil Field --</option>
                @foreach($oilFields as $field)
                <option value="{{ $field->id }}"
                    {{ old('oil_field_id', $tvTest->oil_field_id ?? '') == $field->id ? 'selected' : '' }}>
                    {{ $field->name }}
                </option>
                @endforeach
            </select>
            @error('oil_field_id')
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
                @if(isset($towers))
                    @foreach($towers as $tower)
                    <option value="{{ $tower->id }}"
                        {{ old('tower_id', $tvTest->tower_id ?? '') == $tower->id ? 'selected' : '' }}>
                        {{ $tower->name }}
                    </option>
                    @endforeach
                @endif
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
            <label for="oil_string_id">Oil String <span class="text-danger">*</span></label>
            <select name="oil_string_id" id="oil_string_id"
                class="form-control @error('oil_string_id') is-invalid @enderror" required>
                <option value="">-- Select Oil String --</option>
                @if(isset($oilStrings))
                    @foreach($oilStrings as $string)
                    <option value="{{ $string->id }}"
                        {{ old('oil_string_id', $tvTest->oil_string_id ?? '') == $string->id ? 'selected' : '' }}>
                        {{ $string->name }}
                    </option>
                    @endforeach
                @endif
            </select>
            @error('oil_string_id')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="shift">Shift <span class="text-danger">*</span></label>
            <select name="shift" id="shift" class="form-control @error('shift') is-invalid @enderror" required>
                <option value="morning" {{ old('shift', $tvTest->shift ?? '') == 'morning' ? 'selected' : '' }}>Morning</option>
                <option value="afternoon" {{ old('shift', $tvTest->shift ?? '') == 'afternoon' ? 'selected' : '' }}>Afternoon</option>
                <option value="night" {{ old('shift', $tvTest->shift ?? '') == 'night' ? 'selected' : '' }}>Night</option>
            </select>
            @error('shift')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label for="oil_rate">Oil Rate (B/D) <span class="text-danger">*</span></label>
            <input type="number" name="oil_rate" id="oil_rate" step="0.01"
                class="form-control @error('oil_rate') is-invalid @enderror"
                value="{{ old('oil_rate', $tvTest->oil_rate ?? '') }}"
                required>
            @error('oil_rate')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            <label for="gas_rate">Gas Rate (MMscf/D) <span class="text-danger">*</span></label>
            <input type="number" name="gas_rate" id="gas_rate" step="0.01"
                class="form-control @error('gas_rate') is-invalid @enderror"
                value="{{ old('gas_rate', $tvTest->gas_rate ?? '') }}"
                required>
            @error('gas_rate')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            <label for="water_cut">Water Cut (%) <span class="text-danger">*</span></label>
            <input type="number" name="water_cut" id="water_cut" step="0.01" min="0" max="100"
                class="form-control @error('water_cut') is-invalid @enderror"
                value="{{ old('water_cut', $tvTest->water_cut ?? '') }}"
                required>
            @error('water_cut')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            <label for="pressure">Pressure (psia) <span class="text-danger">*</span></label>
            <input type="number" name="pressure" id="pressure" step="0.01"
                class="form-control @error('pressure') is-invalid @enderror"
                value="{{ old('pressure', $tvTest->pressure ?? '') }}"
                required>
            @error('pressure')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label for="temperature">Temperature (°F) <span class="text-danger">*</span></label>
            <input type="number" name="temperature" id="temperature" step="0.01" min="-50" max="150"
                class="form-control @error('temperature') is-invalid @enderror"
                value="{{ old('temperature', $tvTest->temperature ?? '') }}"
                required>
            @error('temperature')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            <label for="gor">GOR (scf/stb)</label>
            <input type="number" name="gor" id="gor" step="0.01"
                class="form-control @error('gor') is-invalid @enderror"
                value="{{ old('gor', $tvTest->gor ?? '') }}">
            @error('gor')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            <label for="mpfm_test_id">Link to MPFM Test</label>
            <select name="mpfm_test_id" id="mpfm_test_id" class="form-control @error('mpfm_test_id') is-invalid @enderror">
                <option value="">-- No Link --</option>
                @if(isset($mpfmTests))
                    @foreach($mpfmTests as $test)
                    <option value="{{ $test->id }}"
                        {{ old('mpfm_test_id', $tvTest->mpfm_test_id ?? '') == $test->id ? 'selected' : '' }}>
                        Test #{{ $test->id }} - {{ $test->test_date->format('Y-m-d') }}
                    </option>
                    @endforeach
                @endif
            </select>
            @error('mpfm_test_id')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            <label for="status">Status <span class="text-danger">*</span></label>
            <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                <option value="draft" {{ old('status', $tvTest->status ?? 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="submitted" {{ old('status', $tvTest->status ?? '') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                <option value="approved" {{ old('status', $tvTest->status ?? '') == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ old('status', $tvTest->status ?? '') == 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
            @error('status')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label for="notes">Notes</label>
            <textarea name="notes" id="notes" rows="3"
                class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $tvTest->notes ?? '') }}</textarea>
            @error('notes')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

<!-- Hidden field for linking to test request -->
<input type="hidden" name="tv_test_request_well_id" value="{{ old('tv_test_request_well_id', $tvTest->tv_test_request_well_id ?? '') }}">

<div class="form-group">
    <button type="submit" class="btn btn-primary">
        <i class="fe fe-save"></i> {{ isset($tvTest->id) ? 'Update Test' : 'Create Test' }}
    </button>
    <a href="{{ route('tv_tests.index') }}" class="btn btn-secondary">
        <i class="fe fe-x"></i> Cancel
    </a>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Handle cascading dropdowns for oil field -> tower -> oil string
    $('#oil_field_id').on('change', function() {
        var oilFieldId = $(this).val();
        if (oilFieldId) {
            // Load towers for the selected oil field
            $.get('/ajax/operational-plans/towers/' + oilFieldId, function(data) {
                var towerSelect = $('#tower_id');
                towerSelect.html('<option value="">-- Select Tower --</option>');
                $.each(data, function(index, tower) {
                    towerSelect.append('<option value="' + tower.id + '">' + tower.name + '</option>');
                });
            });
        } else {
            $('#tower_id').html('<option value="">-- Select Tower --</option>');
            $('#oil_string_id').html('<option value="">-- Select Oil String --</option>');
        }
    });

    $('#tower_id').on('change', function() {
        var towerId = $(this).val();
        if (towerId) {
            // Load oil strings for the selected tower
            $.get('/ajax/operational-plans/oil-strings/' + towerId, function(data) {
                var stringSelect = $('#oil_string_id');
                stringSelect.html('<option value="">-- Select Oil String --</option>');
                $.each(data, function(index, string) {
                    stringSelect.append('<option value="' + string.id + '">' + string.name + '</option>');
                });
            });
        } else {
            $('#oil_string_id').html('<option value="">-- Select Oil String --</option>');
        }
    });
});
</script>
@endpush
