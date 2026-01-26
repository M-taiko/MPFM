@extends('layouts.master')

@section('title', $department->name . ' - Team Users')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title mb-2">{{ $department->name }} - Team Users</h1>
                <p class="text-muted mb-0">
                    Manage users assigned to this department
                </p>
            </div>
            <div class="btn-group">
                @if(auth()->user()->hasRole(['department_supervisor', 'admin']))
                <a href="{{ route('departments.users.create', $department->id) }}" class="btn btn-primary">
                    <i class="fe fe-plus"></i> Create New User
                </a>
                @endif
                <a href="{{ route('departments.operators', $department->id) }}" class="btn btn-secondary">
                    <i class="fe fe-arrow-left"></i> Back to Team Members
                </a>
            </div>
        </div>
    </div>

    <!-- Messages -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <!-- Users Table -->
    <div class="card" style="border-left: 5px solid #2980b9;">
        <div class="card-header" style="background: linear-gradient(135deg, #2980b9 0%, #1a5276 100%); color: white;">
            <h5 class="card-title mb-0">
                <i class="fe fe-users"></i> Department Users
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Created Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>
                                <strong>{{ $user->name }}</strong>
                            </td>
                            <td>
                                <small class="text-muted">{{ $user->email }}</small>
                            </td>
                            <td>
                                @forelse($user->roles as $role)
                                    <span class="badge badge-info">
                                        {{ str_replace('_', ' ', ucfirst($role->name)) }}
                                    </span>
                                @empty
                                    <span class="text-muted small">No role</span>
                                @endforelse
                            </td>
                            <td>
                                @if($user->is_active ?? true)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ $user->created_at->format('M d, Y') }}</small>
                            </td>
                            <td>
                                @if(auth()->user()->hasRole(['department_supervisor', 'admin']))
                                <a href="{{ route('departments.users.edit', [$department->id, $user->id]) }}"
                                   class="btn btn-sm btn-outline-primary" title="Edit user">
                                    <i class="fe fe-edit"></i> Edit
                                </a>
                                <form action="{{ route('departments.users.destroy', [$department->id, $user->id]) }}"
                                      method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Are you sure you want to delete this user?')"
                                            title="Delete user">
                                        <i class="fe fe-trash"></i> Delete
                                    </button>
                                </form>
                                @else
                                <small class="text-muted">No actions available</small>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fe fe-users" style="font-size: 32px; opacity: 0.3;"></i><br>
                                No users assigned to this department yet
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    @if($users->hasPages())
    <div class="mt-4">
        {{ $users->links() }}
    </div>
    @endif
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
</style>
@endsection
