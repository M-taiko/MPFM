@extends('layouts.master')

@section('title', 'Well String Requirement Details')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Well String Requirement Details</h2>
        @if($requirement->status !== 'completed')
            <a href="{{ route('well_string_requirements.edit', $requirement->id) }}" class="btn btn-primary">Update Requirement</a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Well Test:</label>
                        <p>{{ $requirement->wellTest->well_name ?? 'N/A' }}</p>
                    </div>

                    <div class="form-group">
                        <label>Well String:</label>
                        <p>{{ $requirement->well_string }}</p>
                    </div>

                    <div class="form-group">
                        <label>Status:</label>
                        <span class="badge
                            @if($requirement->status === 'pending_reservoir') badge-warning
                            @elseif($requirement->status === 'pending_job_officer') badge-info
                            @elseif($requirement->status === 'pending_production_supervisor') badge-primary
                            @elseif($requirement->status === 'pending_mpfm_supervisor') badge-success
                            @elseif($requirement->status === 'completed') badge-dark
                            @endif">
                            {{ ucfirst(str_replace(['pending_', '_'], [' ', ' '], $requirement->status)) }}
                        </span>
                    </div>

                    <div class="form-group">
                        <label>Created By:</label>
                        <p>{{ $requirement->createdBy->name ?? 'N/A' }}</p>
                    </div>

                    <div class="form-group">
                        <label>Created At:</label>
                        <p>{{ $requirement->created_at->format('Y-m-d H:i:s') }}</p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>Reservoir Engineer Requirements:</label>
                        <p>{{ $requirement->reservoir_engineer_requirements ?? 'Not specified' }}</p>
                    </div>

                    <div class="form-group">
                        <label>Job Officer Notes:</label>
                        <p>{{ $requirement->job_officer_notes ?? 'Not provided' }}</p>
                    </div>

                    <div class="form-group">
                        <label>Production Supervisor Comments:</label>
                        <p>{{ $requirement->production_supervisor_comments ?? 'Not provided' }}</p>
                    </div>

                    <div class="form-group">
                        <label>MPFM Supervisor Allocation:</label>
                        <p>{{ $requirement->mpfm_supervisor_allocation ?? 'Not provided' }}</p>
                    </div>

                    <div class="form-group">
                        <label>Operator Assignments:</label>
                        @if($requirement->operator_assignments)
                            <ul class="list-group">
                                @foreach($requirement->operator_assignments as $assignment)
                                    <li class="list-group-item">{{ $assignment }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p>Not assigned</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
