@extends('layouts.master')

@section('title', 'Create Oil String')

@section('content')
<div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Create New Oil String</h3>
                <div class="card-options">
                    <a href="{{ route('oil_strings.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fe fe-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('oil_strings.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Tower <span class="text-danger">*</span></label>
                                <select class="form-control @error('tower_id') is-invalid @enderror" 
                                        name="tower_id" id="tower_id" required>
                                    <option value="">Select Tower</option>
                                    @foreach($towers as $tower)
                                    <option value="{{ $tower->id }}" {{ old('tower_id') == $tower->id ? 'selected' : '' }}>
                                        {{ $tower->name }} ({{ $tower->oilField->name ?? '' }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('tower_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Name <span class="text-danger">*</span></label>
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
                                <label class="form-label">String number <small class="text-muted">(1, 2, 3...)</small></label>
                                <input type="number" min="1" max="99" class="form-control @error('string_number') is-invalid @enderror" name="string_number" value="{{ old('string_number') }}">
                                @error('string_number')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                            </div>
                        </div>
                    </div>
<div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                       name="code" value="{{ old('code') }}" required>
                                @error('code')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-control @error('status') is-invalid @enderror" name="status" required>
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                </select>
                                @error('status')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
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

                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fe fe-save"></i> Create Oil String
                        </button>
                        <a href="{{ route('oil_strings.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

