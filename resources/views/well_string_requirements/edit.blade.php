@extends('layouts.master')

@section('title', 'Update Well String Requirement')

@section('content')
<div class="container">
    <h2>Update Well String Requirement</h2>

    <div class="card">
        <div class="card-body">
            <h4>Well: {{ $requirement->wellTest->well_name ?? 'N/A' }} | String: {{ $requirement->well_string }}</h4>

            @if(auth()->user()->hasRole('job_officer'))
            <form action="{{ route('well_string_requirements.update', $requirement->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="job_officer_notes">Job Officer Notes</label>
                    <textarea name="job_officer_notes" id="job_officer_notes" class="form-control" rows="5" placeholder="Add your notes here...">{{ $requirement->job_officer_notes }}</textarea>
                </div>

                <div class="form-group mt-3">
                    <button type="submit" class="btn btn-primary">Update Requirement</button>
                    <a href="{{ route('well_string_requirements.show', $requirement->id) }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>

            @elseif(auth()->user()->hasRole('production_supervisor'))
            <form action="{{ route('well_string_requirements.update', $requirement->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="production_supervisor_comments">Production Supervisor Comments</label>
                    <textarea name="production_supervisor_comments" id="production_supervisor_comments" class="form-control" rows="5" placeholder="Add your comments here...">{{ $requirement->production_supervisor_comments }}</textarea>
                </div>

                <div class="form-group mt-3">
                    <button type="submit" class="btn btn-primary">Update Requirement</button>
                    <a href="{{ route('well_string_requirements.show', $requirement->id) }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>

            @elseif(auth()->user()->hasRole('mpfm_supervisor'))
            <form action="{{ route('well_string_requirements.update', $requirement->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="mpfm_supervisor_allocation">MPFM Supervisor Allocation</label>
                    <textarea name="mpfm_supervisor_allocation" id="mpfm_supervisor_allocation" class="form-control" rows="3" placeholder="Describe operator allocation...">{{ $requirement->mpfm_supervisor_allocation }}</textarea>
                </div>

                <div class="form-group">
                    <label>Operator Assignments</label>
                    <div id="operator-assignments">
                        @if($requirement->operator_assignments && is_array($requirement->operator_assignments))
                            @foreach($requirement->operator_assignments as $assignment)
                                <div class="row mb-2 assignment-row">
                                    @php
                                        $parts = explode(' - ', $assignment, 2);
                                        $operator = $parts[0] ?? '';
                                        $wellString = $parts[1] ?? '';
                                    @endphp
                                    <div class="col-md-5">
                                        <input type="text" name="operator_assignments[]" class="form-control" value="{{ $operator }}" placeholder="Operator name or ID">
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" name="well_string_assignments[]" class="form-control" value="{{ $wellString }}" placeholder="Well string assignment">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-danger remove-assignment">Remove</button>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="row mb-2 assignment-row">
                                <div class="col-md-5">
                                    <input type="text" name="operator_assignments[]" class="form-control" placeholder="Operator name or ID">
                                </div>
                                <div class="col-md-5">
                                    <input type="text" name="well_string_assignments[]" class="form-control" placeholder="Well string assignment">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-danger remove-assignment">Remove</button>
                                </div>
                            </div>
                        @endif
                    </div>
                    <button type="button" id="add-assignment" class="btn btn-secondary mt-2">Add Assignment</button>
                </div>

                <div class="form-group mt-3">
                    <button type="submit" class="btn btn-primary">Complete Requirement</button>
                    <a href="{{ route('well_string_requirements.show', $requirement->id) }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>

            @else
            <p>You do not have permission to update this requirement.</p>
            <a href="{{ route('well_string_requirements.show', $requirement->id) }}" class="btn btn-secondary">Back to Requirement</a>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add assignment row
    document.getElementById('add-assignment').addEventListener('click', function() {
        const container = document.getElementById('operator-assignments');
        const newRow = document.createElement('div');
        newRow.className = 'row mb-2 assignment-row';
        newRow.innerHTML = `
            <div class="col-md-5">
                <input type="text" name="operator_assignments[]" class="form-control" placeholder="Operator name or ID">
            </div>
            <div class="col-md-5">
                <input type="text" name="well_string_assignments[]" class="form-control" placeholder="Well string assignment">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger remove-assignment">Remove</button>
            </div>
        `;
        container.appendChild(newRow);

        // Add event to new remove button
        newRow.querySelector('.remove-assignment').addEventListener('click', function() {
            this.closest('.assignment-row').remove();
        });
    });

    // Add event to existing remove buttons
    document.querySelectorAll('.remove-assignment').forEach(button => {
        button.addEventListener('click', function() {
            this.closest('.assignment-row').remove();
        });
    });
});
</script>
@endsection
