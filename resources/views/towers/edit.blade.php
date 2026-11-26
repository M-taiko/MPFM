@extends('layouts.master')

@section('title', 'Edit Tower')

@section('content')
<div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Edit Tower: {{ $tower->name }}</h3>
                <div class="card-options">
                    <a href="{{ route('towers.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fe fe-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('towers.update', $tower->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Oil Field <span class="text-danger">*</span></label>
                                <select class="form-control @error('oil_field_id') is-invalid @enderror" 
                                        name="oil_field_id" required>
                                    <option value="">Select Oil Field</option>
                                    @foreach($oilFields as $field)
                                    <option value="{{ $field->id }}" 
                                            {{ old('oil_field_id', $tower->oil_field_id) == $field->id ? 'selected' : '' }}>
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
                                <label class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       name="name" value="{{ old('name', $tower->name) }}" required>
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                                                            <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">Tower type <span class="text-danger">*</span></label>
                                <select class="form-control @error('tower_type') is-invalid @enderror" name="tower_type" required>
                                    @foreach(\App\Models\Tower::TYPES as $__k => $__t)
                                        <option value="{{ $__k }}" {{ old('tower_type', $tower->tower_type) == $__k ? 'selected' : '' }}>{{ $__t[0] }}</option>
                                    @endforeach
                                </select>
                                @error('tower_type')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                            </div>
                        </div>
                    </div>

<div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                       name="code" value="{{ old('code', $tower->code) }}" required>
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
                                    <option value="active" {{ old('status', $tower->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $tower->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="maintenance" {{ old('status', $tower->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
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
                                  name="description" rows="3">{{ old('description', $tower->description) }}</textarea>
                        @error('description')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fe fe-save"></i> Update Tower
                        </button>
                        <a href="{{ route('towers.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

