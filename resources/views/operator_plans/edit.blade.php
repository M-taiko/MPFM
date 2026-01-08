@extends('layouts.master2')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            Edit Operator Plan Step
            <small class="text-muted">#{{ $operatorPlan->step_number }}</small>
        </h1>
        <div class="page-subtitle">
            Well: {{ $operatorPlan->wellTest->well }}
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Step</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('operator_plans.update', $operatorPlan->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="step_number">Step Number *</label>
                            <input type="number"
                                   class="form-control @error('step_number') is-invalid @enderror"
                                   id="step_number"
                                   name="step_number"
                                   value="{{ old('step_number', $operatorPlan->step_number) }}"
                                   required>
                            @error('step_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="step_type">Step Type *</label>
                            <select class="form-control @error('step_type') is-invalid @enderror"
                                    id="step_type"
                                    name="step_type"
                                    required>
                                <option value="">Select Step Type</option>
                                <option value="instruction" {{ old('step_type', $operatorPlan->step_type) == 'instruction' ? 'selected' : '' }}>
                                    Instruction
                                </option>
                                <option value="measurement" {{ old('step_type', $operatorPlan->step_type) == 'measurement' ? 'selected' : '' }}>
                                    Measurement
                                </option>
                                <option value="action" {{ old('step_type', $operatorPlan->step_type) == 'action' ? 'selected' : '' }}>
                                    Action
                                </option>
                                <option value="preparation" {{ old('step_type', $operatorPlan->step_type) == 'preparation' ? 'selected' : '' }}>
                                    Preparation
                                </option>
                                <option value="cleanup" {{ old('step_type', $operatorPlan->step_type) == 'cleanup' ? 'selected' : '' }}>
                                    Cleanup
                                </option>
                            </select>
                            @error('step_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="step_description">Step Description *</label>
                            <textarea class="form-control @error('step_description') is-invalid @enderror"
                                      id="step_description"
                                      name="step_description"
                                      rows="4"
                                      placeholder="Describe what the operator should do..."
                                      required>{{ old('step_description', $operatorPlan->step_description) }}</textarea>
                            @error('step_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="step_parameters">Step Parameters (JSON)</label>
                            <textarea class="form-control @error('step_parameters') is-invalid @enderror"
                                      id="step_parameters"
                                      name="step_parameters"
                                      rows="3"
                                      placeholder='{"threshold": 100, "unit": "psi"}'>{{ old('step_parameters') ? json_encode(old('step_parameters')) : ($operatorPlan->step_parameters ? json_encode($operatorPlan->step_parameters) : '') }}</textarea>
                            <small class="form-text text-muted">
                                Optional parameters for the step in JSON format
                            </small>
                            @error('step_parameters')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="order">Order</label>
                            <input type="number"
                                   class="form-control @error('order') is-invalid @enderror"
                                   id="order"
                                   name="order"
                                   value="{{ old('order', $operatorPlan->order) }}">
                            <small class="form-text text-muted">
                                Order number for arranging steps (lower numbers appear first)
                            </small>
                            @error('order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fe fe-save"></i> Update Step
                            </button>
                            <a href="{{ route('operator_plans.index', ['wellTestId' => $operatorPlan->well_test_id]) }}"
                               class="btn btn-secondary">
                                <i class="fe fe-x"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
