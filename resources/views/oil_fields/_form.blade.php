<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="name">Field Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name"
                class="form-control @error('name') is-invalid @enderror"
                value="{{ old('name', $oilField->name ?? '') }}"
                required autofocus>
            @error('name')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="code">Field Code <span class="text-danger">*</span></label>
            <input type="text" name="code" id="code"
                class="form-control @error('code') is-invalid @enderror"
                value="{{ old('code', $oilField->code ?? '') }}"
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
            <label for="location">Location</label>
            <input type="text" name="location" id="location"
                class="form-control @error('location') is-invalid @enderror"
                value="{{ old('location', $oilField->location ?? '') }}">
            @error('location')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="status">Status <span class="text-danger">*</span></label>
            <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                <option value="active" {{ old('status', $oilField->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status', $oilField->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                class="form-control @error('description') is-invalid @enderror">{{ old('description', $oilField->description ?? '') }}</textarea>
            @error('description')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

<div class="form-group">
    <button type="submit" class="btn btn-primary">
        <i class="fe fe-save"></i> {{ isset($oilField) ? 'Update Oil Field' : 'Create Oil Field' }}
    </button>
    <a href="{{ route('oil_fields.index') }}" class="btn btn-secondary">
        <i class="fe fe-x"></i> Cancel
    </a>
</div>
