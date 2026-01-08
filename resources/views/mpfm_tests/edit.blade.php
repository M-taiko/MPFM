@extends('layouts.master')

@section('title', 'Edit MPFM Test')

@section('content')
<div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Edit MPFM Test #{{ $mpfmTest->id }}</h3>
                <div class="card-options">
                    <a href="{{ route('mpfm_tests.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fe fe-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('mpfm_tests.update', $mpfmTest->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Operational Plan (Optional)</label>
                                <select class="form-control @error('mpfm_operational_plan_id') is-invalid @enderror" 
                                        name="mpfm_operational_plan_id">
                                    <option value="">None</option>
                                    @foreach($operationalPlans as $plan)
                                    <option value="{{ $plan->id }}" 
                                            {{ old('mpfm_operational_plan_id', $mpfmTest->mpfm_operational_plan_id) == $plan->id ? 'selected' : '' }}>
                                        {{ $plan->name }} - {{ $plan->oilField->name }} / {{ $plan->tower->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('mpfm_operational_plan_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Test Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('test_date') is-invalid @enderror" 
                                       name="test_date" value="{{ old('test_date', $mpfmTest->test_date->format('Y-m-d')) }}" required>
                                @error('test_date')
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
                                            {{ old('oil_field_id', $mpfmTest->oil_field_id) == $field->id ? 'selected' : '' }}>
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
                                            {{ old('tower_id', $mpfmTest->tower_id) == $tower->id ? 'selected' : '' }}>
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
                                <label class="form-label">Oil String <span class="text-danger">*</span></label>
                                <select class="form-control @error('oil_string_id') is-invalid @enderror" 
                                        name="oil_string_id" id="oil_string_id" required>
                                    <option value="">Select Oil String</option>
                                    @foreach($oilStrings as $oilString)
                                    <option value="{{ $oilString->id }}" 
                                            {{ old('oil_string_id', $mpfmTest->oil_string_id) == $oilString->id ? 'selected' : '' }}>
                                        {{ $oilString->name }} ({{ $oilString->code }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('oil_string_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Shift <span class="text-danger">*</span></label>
                                <select class="form-control @error('shift') is-invalid @enderror" name="shift" required>
                                    <option value="">Select Shift</option>
                                    <option value="morning" {{ old('shift', $mpfmTest->shift) == 'morning' ? 'selected' : '' }}>Morning</option>
                                    <option value="afternoon" {{ old('shift', $mpfmTest->shift) == 'afternoon' ? 'selected' : '' }}>Afternoon</option>
                                    <option value="night" {{ old('shift', $mpfmTest->shift) == 'night' ? 'selected' : '' }}>Night</option>
                                </select>
                                @error('shift')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <h5 class="mt-4 mb-3">Production Data</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Oil Rate (STB/d)</label>
                                <input type="number" step="0.01" class="form-control @error('oil_rate') is-invalid @enderror" 
                                       name="oil_rate" value="{{ old('oil_rate', $mpfmTest->oil_rate) }}">
                                @error('oil_rate')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Gas Rate (Mscf/d)</label>
                                <input type="number" step="0.01" class="form-control @error('gas_rate') is-invalid @enderror" 
                                       name="gas_rate" value="{{ old('gas_rate', $mpfmTest->gas_rate) }}">
                                @error('gas_rate')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Water Cut (%)</label>
                                <input type="number" step="0.01" min="0" max="100" class="form-control @error('water_cut') is-invalid @enderror" 
                                       name="water_cut" value="{{ old('water_cut', $mpfmTest->water_cut) }}">
                                @error('water_cut')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Pressure (psi)</label>
                                <input type="number" step="0.01" class="form-control @error('pressure') is-invalid @enderror" 
                                       name="pressure" value="{{ old('pressure', $mpfmTest->pressure) }}">
                                @error('pressure')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Temperature (°F)</label>
                                <input type="number" step="0.01" class="form-control @error('temperature') is-invalid @enderror" 
                                       name="temperature" value="{{ old('temperature', $mpfmTest->temperature) }}">
                                @error('temperature')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Status</label>
                                <select class="form-control @error('status') is-invalid @enderror" name="status">
                                    <option value="draft" {{ old('status', $mpfmTest->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="submitted" {{ old('status', $mpfmTest->status) == 'submitted' ? 'selected' : '' }}>Submitted</option>
                                    <option value="approved" {{ old('status', $mpfmTest->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ old('status', $mpfmTest->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
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
                        <label class="form-label">Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  name="notes" rows="3">{{ old('notes', $mpfmTest->notes) }}</textarea>
                        @error('notes')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fe fe-save"></i> Update Test
                        </button>
                        <a href="{{ route('mpfm_tests.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

