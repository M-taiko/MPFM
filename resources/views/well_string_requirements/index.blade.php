@extends('layouts.master')

@section('title', 'Well String Requirements')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Well String Requirements</h2>
        <a href="{{ route('well_tests.index') }}" class="btn btn-primary">View Well Tests</a>
    </div>
    
    <div class="alert alert-info mb-3">
        <p class="mb-0">
            <strong>Note:</strong> Well String Requirements are created in the context of a specific Well Test.
            To create a new requirement, navigate to the desired Well Test and use the "Create Well String Requirements" button.
        </p>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Well Test</th>
                            <th>Well String</th>
                            <th>Reservoir Engineer</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requirements as $requirement)
                        <tr>
                            <td>{{ $requirement->id }}</td>
                            <td>{{ $requirement->wellTest->well_name ?? 'N/A' }}</td>
                            <td>{{ $requirement->well_string }}</td>
                            <td>{{ $requirement->createdBy->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge
                                    @if($requirement->status === 'pending_reservoir') badge-warning
                                    @elseif($requirement->status === 'pending_job_officer') badge-info
                                    @elseif($requirement->status === 'pending_production_supervisor') badge-primary
                                    @elseif($requirement->status === 'pending_mpfm_supervisor') badge-success
                                    @elseif($requirement->status === 'completed') badge-dark
                                    @endif">
                                    {{ ucfirst(str_replace(['pending_', '_'], [' ', ' '], $requirement->status)) }}
                                </span>
                            </td>
                            <td>{{ $requirement->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <a href="{{ route('well_string_requirements.show', $requirement->id) }}" class="btn btn-sm btn-primary">View</a>
                                @if($requirement->status !== 'completed')
                                    @can('update', $requirement)
                                        <a href="{{ route('well_string_requirements.edit', $requirement->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    @endcan
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">No well string requirements found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center">
                {{ $requirements->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
