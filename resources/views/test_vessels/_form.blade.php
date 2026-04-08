<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="name">Vessel Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name"
                class="form-control @error('name') is-invalid @enderror"
                value="{{ old('name', $testVessel->name ?? '') }}"
                required autofocus>
            @error('name')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="code">Vessel Code <span class="text-danger">*</span></label>
            <input type="text" name="code" id="code"
                class="form-control @error('code') is-invalid @enderror"
                value="{{ old('code', $testVessel->code ?? '') }}"
                required>
            @error('code')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="oil_field_id">Oil Field</label>
            <select name="oil_field_id" id="oil_field_id"
                class="form-control @error('oil_field_id') is-invalid @enderror">
                <option value="">-- Select Oil Field --</option>
                @foreach($oilFields as $field)
                <option value="{{ $field->id }}"
                    {{ old('oil_field_id', $testVessel->oil_field_id ?? '') == $field->id ? 'selected' : '' }}>
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
            <label for="status">Status <span class="text-danger">*</span></label>
            <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                <option value="active" {{ old('status', $testVessel->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status', $testVessel->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="maintenance" {{ old('status', $testVessel->status ?? '') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
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
            <label for="description">Description</label>
            <textarea name="description" id="description" rows="3"
                class="form-control @error('description') is-invalid @enderror">{{ old('description', $testVessel->description ?? '') }}</textarea>
            @error('description')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

<div class="form-group">
    <button type="submit" class="btn btn-primary">
        <i class="fe fe-save"></i> {{ isset($testVessel) ? 'Update Vessel' : 'Create Vessel' }}
    </button>
    <a href="{{ route('test_vessels.index') }}" class="btn btn-secondary">
        <i class="fe fe-x"></i> Cancel
    </a>
</div>
