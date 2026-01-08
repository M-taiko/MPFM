@extends('layouts.app')

@section('title', 'Edit MPFM Plan: ' . $plan->name)

@section('content')
<div class="container">
    <h2>Edit MPFM Plan: {{ $plan->name }}</h2>
    
    <form action="{{ route('mpfm_plans.update', $plan->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="name">Plan Name</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ $plan->name }}" required>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="expected_choke">Expected Choke Size</label>
                    <input type="text" name="expected_choke" id="expected_choke" class="form-control" value="{{ $plan->expected_choke }}">
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="planned_oil_rate">Planned Oil Rate (STB/d)</label>
                    <input type="number" step="0.01" name="planned_oil_rate" id="planned_oil_rate" class="form-control" value="{{ $plan->planned_oil_rate }}">
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="planned_gas_rate">Planned Gas Rate (MMScf/d)</label>
                    <input type="number" step="0.01" name="planned_gas_rate" id="planned_gas_rate" class="form-control" value="{{ $plan->planned_gas_rate }}">
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="planned_water_rate">Planned Water Rate (STB/d)</label>
                    <input type="number" step="0.01" name="planned_water_rate" id="planned_water_rate" class="form-control" value="{{ $plan->planned_water_rate }}">
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="expected_gor">Expected GOR (Scf/STB)</label>
                    <input type="number" step="0.01" name="expected_gor" id="expected_gor" class="form-control" value="{{ $plan->expected_gor }}">
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="expected_bsw">Expected BSW (%)</label>
                    <input type="number" step="0.01" name="expected_bsw" id="expected_bsw" class="form-control" value="{{ $plan->expected_bsw }}">
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="expected_duration">Expected Duration (minutes)</label>
                    <input type="number" name="expected_duration" id="expected_duration" class="form-control" value="{{ $plan->expected_duration }}">
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <label for="test_objective">Test Objective</label>
            <textarea name="test_objective" id="test_objective" class="form-control">{{ $plan->test_objective }}</textarea>
        </div>
        
        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" class="form-control">{{ $plan->description }}</textarea>
        </div>
        
        <div class="form-group mt-3">
            <button type="submit" class="btn btn-primary">Update Plan</button>
            <a href="{{ route('mpfm_plans.show', $plan->id) }}" class="btn btn-secondary">Cancel</a>
            <a href="{{ route('mpfm_plans.destroy', $plan->id) }}" class="btn btn-danger" 
               onclick="if(confirm('Are you sure you want to delete this plan?')) { event.preventDefault(); document.getElementById('delete-form').submit(); }">
                Delete Plan
            </a>
        </div>
    </form>
    
    <form id="delete-form" action="{{ route('mpfm_plans.destroy', $plan->id) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
</div>
@endsection