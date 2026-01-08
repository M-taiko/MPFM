@extends('layouts.master')

@section('title', 'Create Well String Requirement')

@section('content')
<div class="container">
    <h2>Create Well String Requirement for: {{ $wellTest->well_name ?? 'N/A' }}</h2>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('well_string_requirements.store', $wellTest->id) }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="well_string">Well String</label>
                    <input type="text" name="well_string" id="well_string" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="reservoir_engineer_requirements">Reservoir Engineer Requirements</label>
                    <textarea name="reservoir_engineer_requirements" id="reservoir_engineer_requirements" class="form-control" rows="5" placeholder="Enter requirements for this well string..."></textarea>
                </div>

                <div class="form-group mt-3">
                    <button type="submit" class="btn btn-primary">Create Requirement</button>
                    <a href="{{ route('well_tests.show', $wellTest->id) }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
