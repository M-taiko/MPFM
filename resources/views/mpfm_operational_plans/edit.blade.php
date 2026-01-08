@extends('layouts.master')

@section('title', 'Edit MPFM Operational Plan')

@section('content')
<div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Edit MPFM Operational Plan: {{ $mpfmOperationalPlan->name }}</h3>
                <div class="card-options">
                    <a href="{{ route('mpfm_operational_plans.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fe fe-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('mpfm_operational_plans.update', $mpfmOperationalPlan->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Assigned Operator <span class="text-danger">*</span></label>
                                <select class="form-control @error('assigned_user_id') is-invalid @enderror" 
                                        name="assigned_user_id" required>
                                    <option value="">Select Operator</option>
                                    @foreach($operators as $operator)
                                    <option value="{{ $operator->id }}" 
                                            {{ old('assigned_user_id', $mpfmOperationalPlan->assigned_user_id) == $operator->id ? 'selected' : '' }}>
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
                                       name="name" value="{{ old('name', $mpfmOperationalPlan->name) }}" required>
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
                                    <option value="{{ $field->id }}" 
                                            {{ old('oil_field_id', $mpfmOperationalPlan->oil_field_id) == $field->id ? 'selected' : '' }}>
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
                                    <option value="">Select Tower</option>
                                    @foreach($towers as $tower)
                                    <option value="{{ $tower->id }}" 
                                            {{ old('tower_id', $mpfmOperationalPlan->tower_id) == $tower->id ? 'selected' : '' }}>
                                        {{ $tower->name }} ({{ $tower->code }})
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
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Plan Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('plan_date') is-invalid @enderror" 
                                       name="plan_date" value="{{ old('plan_date', $mpfmOperationalPlan->plan_date->format('Y-m-d')) }}" required>
                                @error('plan_date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Status</label>
                                <select class="form-control @error('status') is-invalid @enderror" name="status">
                                    <option value="draft" {{ old('status', $mpfmOperationalPlan->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="active" {{ old('status', $mpfmOperationalPlan->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="completed" {{ old('status', $mpfmOperationalPlan->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ old('status', $mpfmOperationalPlan->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
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
                        <label class="form-label">Assigned Oil Strings <span class="text-danger">*</span></label>
                        <div id="oilStringsContainer">
                            @foreach($oilStrings as $oilString)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="assigned_oil_string_ids[]" 
                                       value="{{ $oilString->id }}" id="string_{{ $oilString->id }}"
                                       {{ in_array($oilString->id, old('assigned_oil_string_ids', $mpfmOperationalPlan->assigned_oil_string_ids ?? [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="string_{{ $oilString->id }}">
                                    {{ $oilString->name }} ({{ $oilString->code }})
                                </label>
                            </div>
                            @endforeach
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
                                  name="description" rows="3">{{ old('description', $mpfmOperationalPlan->description) }}</textarea>
                        @error('description')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  name="notes" rows="3">{{ old('notes', $mpfmOperationalPlan->notes) }}</textarea>
                        @error('notes')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fe fe-save"></i> Update Plan
                        </button>
                        <a href="{{ route('mpfm_operational_plans.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

