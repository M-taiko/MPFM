@extends('layouts.master')

@section('title', $department->name . ' - Team Members')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title mb-2">{{ $department->name }} Team Members</h1>
                <p class="text-muted mb-0">
                    Total: <strong>{{ $operators->total() }}</strong> operators
                </p>
            </div>
            <div class="btn-group">
                <a href="{{ route('departments.dashboard', $department->id) }}" class="btn btn-secondary">
                    <i class="fe fe-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Add Operator Section -->
    @if(auth()->user()->hasRole('department_manager') || auth()->user()->hasRole('admin'))
    <div class="card mb-4" style="border-left: 5px solid #27ae60;">
        <div class="card-header" style="background: linear-gradient(135deg, #27ae60 0%, #1e8449 100%); color: white;">
            <h5 class="card-title mb-0">
                <i class="fe fe-plus-circle"></i> Add Operator
            </h5>
        </div>
        <div class="card-body">
            <form id="assignOperatorForm" method="POST" class="row g-3">
                @csrf
                <div class="col-md-8">
                    <label class="form-label">Select Operator</label>
                    <select name="user_id" id="user_id" class="form-control" required>
                        <option value="">-- Choose an operator --</option>
                        @foreach($availableOperators as $user)
                        <option value="{{ $user->id }}">
                            {{ $user->name }} ({{ $user->email }})
                        </option>
                        @endforeach
                    </select>
                    <small class="text-muted d-block mt-1">
                        Select from available operators
                    </small>
                </div>
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-success btn-block">
                        <i class="fe fe-plus"></i> Assign Operator
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Team Members Table -->
    <div class="card" style="border-left: 5px solid #2980b9;">
        <div class="card-header" style="background: linear-gradient(135deg, #2980b9 0%, #1a5276 100%); color: white;">
            <h5 class="card-title mb-0">
                <i class="fe fe-users"></i> Department Operators
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Total Assigned</th>
                            <th>Completed</th>
                            <th>In Progress</th>
                            <th>Joined</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($operators as $operator)
                        <tr>
                            <td>
                                <strong>{{ $operator->name }}</strong>
                            </td>
                            <td>
                                <small class="text-muted">{{ $operator->email }}</small>
                            </td>
                            <td>
                                @if($operator->is_active ?? true)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-primary">{{ $operator->total_assigned ?? 0 }}</span>
                            </td>
                            <td>
                                <span class="badge badge-success">{{ $operator->completed_tasks ?? 0 }}</span>
                            </td>
                            <td>
                                @php
                                    $assigned = $operator->total_assigned ?? 0;
                                    $completed = $operator->completed_tasks ?? 0;
                                    $inProgress = $assigned - $completed;
                                @endphp
                                <span class="badge badge-warning">{{ $inProgress }}</span>
                            </td>
                            <td>
                                <small class="text-muted">{{ $operator->created_at->format('M d, Y') }}</small>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-info" title="View Tasks">
                                    <i class="fe fe-eye"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="fe fe-users" style="font-size: 32px; opacity: 0.3;"></i><br>
                                No operators assigned to this department yet
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="row mt-4">
        <div class="col-md-12">
            {{ $operators->links() }}
        </div>
    </div>
</div>

<style>
    .page-title {
        font-size: 28px;
        font-weight: 700;
        color: #2c3e50;
    }

    .card {
        border: none;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .badge {
        padding: 6px 12px;
        font-size: 12px;
        font-weight: 600;
    }
</style>

<script>
    document.getElementById('assignOperatorForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const userId = document.getElementById('user_id').value;
        if (!userId) {
            alert('Please select an operator');
            return;
        }

        const formData = new FormData(this);

        fetch('{{ route("departments.assign-operator", $department->id) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.message) {
                alert('Operator assigned successfully!');
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error assigning operator');
        });
    });
</script>
@endsection
