@extends('layouts.master')

@section('title', 'Edit Plan - ' . $monthlyPlan->plan_name)

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div>
        <h1 class="page-title">Edit Monthly Plan</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('monthly-plans.index') }}">Monthly Plans</a></li>
            <li class="breadcrumb-item"><a href="{{ route('monthly-plans.show', $monthlyPlan) }}">{{ $monthlyPlan->plan_name }}</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 col-xl-6 mx-auto">
        <!-- Edit Form -->
        <div class="card" style="border-left: 4px solid #3b82f6;">
            <div class="card-header" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white;">
                <h3 class="card-title mb-0">
                    <i class="fe fe-edit"></i> Edit Plan Details
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

                <form action="{{ route('monthly-plans.update', $monthlyPlan) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Plan Name -->
                    <div class="form-group">
                        <label for="plan_name" class="form-label">
                            <strong>Plan Name</strong> <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               class="form-control @error('plan_name') is-invalid @enderror"
                               id="plan_name"
                               name="plan_name"
                               value="{{ old('plan_name', $monthlyPlan->plan_name) }}"
                               placeholder="e.g., MPFM Flow Testing Dec. 2025 Plan"
                               required>
                        @error('plan_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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
                               value="{{ old('plan_month', $monthlyPlan->plan_month) }}"
                               placeholder="e.g., December 2025"
                               required>
                        @error('plan_month')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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
                               value="{{ old('monthly_target_kpi', $monthlyPlan->monthly_target_kpi) }}"
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
                                  placeholder="Add any additional notes or instructions for this plan...">{{ old('notes', $monthlyPlan->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3" style="border-top: 1px solid #e5e7eb;">
                        <a href="{{ route('monthly-plans.show', $monthlyPlan) }}" class="btn btn-light">
                            <i class="fe fe-arrow-left"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fe fe-check"></i> Update Plan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Info Card -->
        <div class="card" style="border-left: 4px solid #fbbf24;">
            <div class="card-header bg-warning-transparent">
                <h3 class="card-title mb-0">
                    <i class="fe fe-info"></i> Note
                </h3>
            </div>
            <div class="card-body">
                <p class="mb-0">
                    <strong>Plan Status:</strong> {{ ucfirst(str_replace('_', ' ', $monthlyPlan->status)) }}<br>
                    @if($monthlyPlan->status === 'draft')
                    You can edit this plan because it's still in draft status.
                    @elseif($monthlyPlan->status === 'under_review')
                    You can edit this plan while it's under review. Changes will reset the review status.
                    @else
                    This plan cannot be edited as it has been {{ $monthlyPlan->status }}.
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
