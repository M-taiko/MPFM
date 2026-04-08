@extends('layouts.master')

@section('title', 'Test Request Details')

@section('content')
<div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12">
        <!-- Request Info Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Request Details</h3>
                <div class="card-options">
                    <a href="{{ route('tv_test_requests.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fe fe-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Vessel:</strong>
                        <p>{{ $tvTestRequest->testVessel?->name ?? '-' }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Tower:</strong>
                        <p>{{ $tvTestRequest->tower?->name ?? '-' }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Oil Field:</strong>
                        <p>{{ $tvTestRequest->oilField?->name ?? '-' }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Test Date:</strong>
                        <p>{{ $tvTestRequest->test_date->format('Y-m-d') }}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <strong>Shift:</strong>
                        <p>{{ ucfirst($tvTestRequest->shift) }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Requested By:</strong>
                        <p>{{ $tvTestRequest->requestedBy?->name ?? '-' }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Status:</strong>
                        <p>
                            @if($tvTestRequest->status === 'open')
                                <span class="badge badge-success">Open</span>
                            @elseif($tvTestRequest->status === 'in_progress')
                                <span class="badge badge-warning">In Progress</span>
                            @elseif($tvTestRequest->status === 'completed')
                                <span class="badge badge-info">Completed</span>
                            @else
                                <span class="badge badge-secondary">{{ ucfirst($tvTestRequest->status) }}</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-3">
                        <strong>Created:</strong>
                        <p>{{ $tvTestRequest->created_at->format('Y-m-d H:i') }}</p>
                    </div>
                </div>
                @if($tvTestRequest->notes)
                <div class="row">
                    <div class="col-md-12">
                        <strong>Notes:</strong>
                        <p>{{ $tvTestRequest->notes }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Wells Table -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Wells</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Oil String</th>
                                <th>Status</th>
                                <th>Operator</th>
                                <th>Started At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tvTestRequest->wells as $well)
                            <tr>
                                <td>{{ $well->oilString?->name ?? '-' }}</td>
                                <td>
                                    @if($well->status === 'pending')
                                        <span class="badge badge-secondary">Pending</span>
                                    @elseif($well->status === 'in_progress')
                                        <span class="badge badge-warning">In Progress</span>
                                    @elseif($well->status === 'submitted')
                                        <span class="badge badge-info">Submitted</span>
                                    @elseif($well->status === 'approved')
                                        <span class="badge badge-success">Approved</span>
                                    @elseif($well->status === 'rejected')
                                        <span class="badge badge-danger">Rejected</span>
                                    @endif
                                </td>
                                <td>{{ $well->startedBy?->name ?? '-' }}</td>
                                <td>{{ $well->started_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                <td>
                                    @if($well->status === 'pending')
                                        @if(auth()->user()->hasRole(['mpfm_operator', 'operator', 'admin']))
                                        <a href="{{ route('tv_test_requests.start_test', [$tvTestRequest->id, $well->id]) }}"
                                           class="btn btn-sm btn-primary">
                                            <i class="fe fe-play"></i> Start Test
                                        </a>
                                        @endif
                                    @elseif($well->status === 'in_progress')
                                        @if($well->testVesselTest)
                                        <a href="{{ route('tv_tests.show', $well->testVesselTest->id) }}"
                                           class="btn btn-sm btn-warning">
                                            <i class="fe fe-edit"></i> Continue Test
                                        </a>
                                        @endif
                                    @elseif($well->status === 'submitted')
                                        @if(auth()->user()->hasRole(['job_officer', 'surveillance_supervisor', 'admin']))
                                        @if($well->testVesselTest)
                                        <div class="btn-group" role="group">
                                            <form action="{{ route('tv_tests.approve', $well->testVesselTest->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Approve this test?')">
                                                    <i class="fe fe-check"></i> Approve
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="showRejectForm({{ $well->testVesselTest->id }})">
                                                <i class="fe fe-x"></i> Reject
                                            </button>
                                        </div>
                                        <div id="rejectForm-{{ $well->testVesselTest->id }}" style="display:none; margin-top: 10px;">
                                            <form action="{{ route('tv_tests.reject', $well->testVesselTest->id) }}" method="POST">
                                                @csrf
                                                <textarea name="review_notes" class="form-control mb-2" placeholder="Rejection reason..." required></textarea>
                                                <button type="submit" class="btn btn-sm btn-danger">Submit</button>
                                                <button type="button" class="btn btn-sm btn-secondary" onclick="hideRejectForm({{ $well->testVesselTest->id }})">Cancel</button>
                                            </form>
                                        </div>
                                        @endif
                                        @endif
                                    @elseif($well->status === 'approved')
                                        @if($well->testVesselTest)
                                        <a href="{{ route('tv_tests.compare') }}?tv_test_id={{ $well->testVesselTest->id }}"
                                           class="btn btn-sm btn-info">
                                            <i class="fe fe-eye"></i> View Test
                                        </a>
                                        <a href="{{ route('tv_tests.compare') }}?tv_test_id={{ $well->testVesselTest->id }}"
                                           class="btn btn-sm btn-primary">
                                            <i class="fe fe-shuffle"></i> Compare MPFM
                                        </a>
                                        @endif
                                    @elseif($well->status === 'rejected')
                                        @if($well->testVesselTest)
                                        <a href="{{ route('tv_tests.show', $well->testVesselTest->id) }}"
                                           class="btn btn-sm btn-warning">
                                            <i class="fe fe-eye"></i> View Test
                                        </a>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No wells in this request</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showRejectForm(testId) {
    document.getElementById('rejectForm-' + testId).style.display = 'block';
}

function hideRejectForm(testId) {
    document.getElementById('rejectForm-' + testId).style.display = 'none';
}
</script>
@endpush
