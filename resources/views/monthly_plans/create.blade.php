@extends('layouts.master')

@section('title', 'Create Monthly Plan')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div>
        <h1 class="page-title">Create Monthly Plan</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('monthly-plans.index') }}">Monthly Plans</a></li>
            <li class="breadcrumb-item active">Create Plan</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 col-xl-6 mx-auto">
        <!-- Instructions Card -->
        <div class="card mb-4" style="border-left: 4px solid #3b82f6;">
            <div class="card-header" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white;">
                <h3 class="card-title mb-0">
                    <i class="fe fe-info"></i> Getting Started
                </h3>
            </div>
            <div class="card-body">
                <h5 class="mb-3">Creating a Monthly Plan:</h5>
                <ol class="mb-0">
                    <li class="mb-2">Fill in the plan details below (plan name, month, KPI target)</li>
                    <li class="mb-2">After creation, you'll add individual test tasks based on plan.txt</li>
                    <li class="mb-2">Once all tasks are added, submit the plan for MPFM Supervisor review</li>
                    <li class="mb-0">MPFM Supervisor will review, assign operators, and approve the plan</li>
                </ol>
            </div>
        </div>

        <!-- Create Form -->
        <div class="card" style="border-left: 4px solid #10b981;">
            <div class="card-header" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">
                <h3 class="card-title mb-0">
                    <i class="fe fe-edit"></i> Plan Details
                </h3>
            </div>
            <div class="card-body">
                @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h5><i class="fe fe-x-circle"></i> Validation Errors</h5>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
                @endif

                <form action="{{ route('monthly-plans.store') }}" method="POST">
                    @csrf

                    <!-- Plan Name -->
                    <div class="form-group">
                        <label for="plan_name" class="form-label">
                            <strong>Plan Name</strong> <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               class="form-control @error('plan_name') is-invalid @enderror"
                               id="plan_name"
                               name="plan_name"
                               value="{{ old('plan_name', 'MPFM Flow Testing ' . now()->format('M. Y') . ' Plan') }}"
                               placeholder="e.g., MPFM Flow Testing Dec. 2025 Plan"
                               required>
                        @error('plan_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            <i class="fe fe-info"></i> Give your plan a descriptive name
                        </small>
                    </div>

                    <!-- Plan Month -->
                    <div class="form-group">
                        <label for="plan_month" class="form-label">
                            <strong>Plan Month</strong> <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               class="form-control @error('plan_month') is-invalid @enderror"
                               id="plan_month"
                               name="plan_month"
                               value="{{ old('plan_month', now()->format('F Y')) }}"
                               placeholder="e.g., December 2025"
                               required>
                        @error('plan_month')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            <i class="fe fe-info"></i> Month and year for this plan
                        </small>
                    </div>

                    <!-- Monthly KPI Target -->
                    <div class="form-group">
                        <label for="monthly_target_kpi" class="form-label">
                            <strong>Monthly KPI Target</strong> <span class="text-danger">*</span>
                        </label>
                        <input type="number"
                               class="form-control @error('monthly_target_kpi') is-invalid @enderror"
                               id="monthly_target_kpi"
                               name="monthly_target_kpi"
                               value="{{ old('monthly_target_kpi', 255) }}"
                               min="0"
                               placeholder="e.g., 255"
                               required>
                        @error('monthly_target_kpi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            <i class="fe fe-info"></i> Target number of tests to complete this month
                        </small>
                    </div>

                    <!-- Notes -->
                    <div class="form-group">
                        <label for="notes" class="form-label">
                            <strong>Notes</strong> <small class="text-muted">(Optional)</small>
                        </label>
                        <textarea class="form-control @error('notes') is-invalid @enderror"
                                  id="notes"
                                  name="notes"
                                  rows="4"
                                  placeholder="Add any additional notes or instructions for this plan...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3" style="border-top: 1px solid #e5e7eb;">
                        <a href="{{ route('monthly-plans.index') }}" class="btn btn-light">
                            <i class="fe fe-arrow-left"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fe fe-check"></i> Create Plan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- What's Next Card -->
        <div class="card" style="border-left: 4px solid #fbbf24;">
            <div class="card-header bg-warning-transparent">
                <h3 class="card-title mb-0">
                    <i class="fe fe-alert-triangle"></i> What's Next?
                </h3>
            </div>
            <div class="card-body">
                <p class="mb-2">After creating the plan, you will:</p>
                <ol class="mb-0">
                    <li>Add individual test tasks based on plan.txt file</li>
                    <li>Review all tasks for completeness</li>
                    <li>Submit the plan for MPFM Supervisor review</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection
