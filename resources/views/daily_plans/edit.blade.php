@extends('layouts.master')

@section('title', 'Edit Daily Plan')

@section('css')
<link href="{{URL::asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet">
@endsection

@section('content')
<div class="main-container container-fluid">
    <div class="page-header">
        <div class="page-leftheader">
            <h4 class="page-title mb-0">Edit Daily Plan</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}"><i class="fe fe-home mr-2 fs-14"></i>Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('daily-plans.index') }}">Daily Plans</a></li>
                <li class="breadcrumb-item"><a href="{{ route('daily-plans.show', $dailyPlan) }}">{{ $dailyPlan->plan_name }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit</li>
            </ol>
        </div>
        <div class="page-rightheader">
            <a href="{{ route('daily-plans.show', $dailyPlan) }}" class="btn btn-outline-primary">
                <i class="fe fe-arrow-left mr-2"></i>Back to Plan
            </a>
        </div>
    </div>

    <form action="{{ route('daily-plans.update', $dailyPlan) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Plan Details</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Plan Name <span class="text-danger">*</span></label>
                                    <input type="text" name="plan_name" class="form-control @error('plan_name') is-invalid @enderror"
                                           value="{{ old('plan_name', $dailyPlan->plan_name) }}" required>
                                    @error('plan_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Plan Date <span class="text-danger">*</span></label>
                                    <input type="date" name="plan_date" class="form-control @error('plan_date') is-invalid @enderror"
                                           value="{{ old('plan_date', $dailyPlan->plan_date->format('Y-m-d')) }}" required>
                                    @error('plan_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Oil Field <span class="text-danger">*</span></label>
                                    <select name="oil_field_id" id="oil_field_id" class="form-control select2 @error('oil_field_id') is-invalid @enderror" required>
                                        <option value="">Select Oil Field</option>
                                        @foreach($oilFields as $field)
                                            <option value="{{ $field->id }}" {{ old('oil_field_id', $dailyPlan->oil_field_id) == $field->id ? 'selected' : '' }}>
                                                {{ $field->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('oil_field_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Tower (Optional)</label>
                                    <select name="tower_id" id="tower_id" class="form-control select2">
                                        <option value="">All Towers</option>
                                        @if($dailyPlan->oilField)
                                            @foreach($dailyPlan->oilField->towers as $tower)
                                                <option value="{{ $tower->id }}" {{ old('tower_id', $dailyPlan->tower_id) == $tower->id ? 'selected' : '' }}>
                                                    {{ $tower->name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Plan Notes</label>
                            <textarea name="plan_notes" class="form-control" rows="4">{{ old('plan_notes', $dailyPlan->plan_notes) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Actions</h3>
                    </div>
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fe fe-save mr-2"></i>Update Plan
                        </button>
                        <a href="{{ route('daily-plans.show', $dailyPlan) }}" class="btn btn-outline-secondary btn-block">
                            <i class="fe fe-x mr-2"></i>Cancel
                        </a>
                        <hr>
                        <form action="{{ route('daily-plans.destroy', $dailyPlan) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this plan?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-block">
                                <i class="fe fe-trash-2 mr-2"></i>Delete Plan
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Plan Status</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Current Status:</strong>
                            <span class="badge badge-{{ $dailyPlan->status_badge_class }} float-right">
                                {{ ucfirst($dailyPlan->status) }}
                            </span>
                        </div>
                        <div class="mb-3">
                            <strong>Total Tasks:</strong>
                            <span class="float-right">{{ $dailyPlan->total_tasks }}</span>
                        </div>
                        <div class="mb-3">
                            <strong>Completed:</strong>
                            <span class="float-right">{{ $dailyPlan->completed_tasks }}</span>
                        </div>
                        @if($dailyPlan->status === 'draft')
                        <div class="alert alert-info">
                            <i class="fe fe-info mr-2"></i>
                            You can only edit plans that are in Draft status.
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('js')
<script src="{{URL::asset('assets/plugins/select2/js/select2.min.js')}}"></script>

<script>
$(document).ready(function() {
    $('.select2').select2();

    // Load towers when field is selected
    $('#oil_field_id').on('change', function() {
        const fieldId = $(this).val();
        $('#tower_id').html('<option value="">Loading...</option>');

        if (fieldId) {
            $.ajax({
                url: `/api/oil-fields/${fieldId}/towers`,
                success: function(towers) {
                    let options = '<option value="">All Towers</option>';
                    towers.forEach(tower => {
                        options += `<option value="${tower.id}">${tower.name}</option>`;
                    });
                    $('#tower_id').html(options);
                }
            });
        } else {
            $('#tower_id').html('<option value="">Select field first</option>');
        }
    });
});
</script>
@endsection
