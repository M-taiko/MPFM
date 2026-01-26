@extends('layouts.master')

@section('title', 'Edit User - ' . $user->name)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title mb-2">Edit User</h1>
                <p class="text-muted mb-0">
                    Update user information: {{ $user->name }}
                </p>
            </div>
            <div class="btn-group">
                <a href="{{ route('departments.users.index', $department->id) }}" class="btn btn-secondary">
                    <i class="fe fe-arrow-left"></i> Back to Users
                </a>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card" style="border-left: 5px solid #667eea;">
                <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h5 class="card-title mb-0">
                        <i class="fe fe-edit"></i> User Information
                    </h5>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('departments.users.update', [$department->id, $user->id]) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Name -->
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name', $user->name) }}" required
                                   placeholder="Enter user full name">
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="form-group mb-3">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email', $user->email) }}" required
                                   placeholder="Enter email address">
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="form-group mb-3">
                            <label for="password" class="form-label">New Password <span class="text-muted">(Leave blank to keep current)</span></label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                   id="password" name="password"
                                   placeholder="Enter new password (minimum 8 characters)">
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="form-group mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control"
                                   id="password_confirmation" name="password_confirmation"
                                   placeholder="Confirm new password">
                        </div>

                        <!-- Role -->
                        <div class="form-group mb-4">
                            <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-control @error('role') is-invalid @enderror"
                                    id="role" name="role" required>
                                <option value="">-- Select Role --</option>
                                @foreach($operatorRoles as $role)
                                    <option value="{{ $role->name }}"
                                            {{ (old('role') ?? $userRole?->name) == $role->name ? 'selected' : '' }}>
                                        {{ str_replace('_', ' ', ucfirst($role->name)) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Buttons -->
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fe fe-save"></i> Update User
                            </button>
                            <a href="{{ route('departments.users.index', $department->id) }}" class="btn btn-secondary">
                                <i class="fe fe-x"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Info Card -->
        <div class="col-lg-4">
            <div class="card" style="border-left: 5px solid #27ae60;">
                <div class="card-header" style="background: linear-gradient(135deg, #27ae60 0%, #1e8449 100%); color: white;">
                    <h5 class="card-title mb-0">
                        <i class="fe fe-info"></i> User Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">User ID</small>
                        <p class="mb-0"><strong>{{ $user->id }}</strong></p>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <small class="text-muted">Department</small>
                        <p class="mb-0"><strong>{{ $department->name }}</strong></p>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <small class="text-muted">Current Role</small>
                        <p class="mb-0">
                            <strong>
                                @if($userRole)
                                    {{ str_replace('_', ' ', ucfirst($userRole->name)) }}
                                @else
                                    <span class="text-muted">No role assigned</span>
                                @endif
                            </strong>
                        </p>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <small class="text-muted">Created Date</small>
                        <p class="mb-0"><strong>{{ $user->created_at->format('M d, Y H:i') }}</strong></p>
                    </div>

                    <hr>

                    <div>
                        <small class="text-muted">Last Updated</small>
                        <p class="mb-0"><strong>{{ $user->updated_at->format('M d, Y H:i') }}</strong></p>
                    </div>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="card mt-4" style="border-left: 5px solid #e74c3c;">
                <div class="card-header" style="background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%); color: white;">
                    <h5 class="card-title mb-0">
                        <i class="fe fe-alert-triangle"></i> Danger Zone
                    </h5>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-3">
                        Deleting a user cannot be undone. Please be certain before proceeding.
                    </p>
                    <form action="{{ route('departments.users.destroy', [$department->id, $user->id]) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm w-100"
                                onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                            <i class="fe fe-trash"></i> Delete User
                        </button>
                    </form>
                </div>
            </div>
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

    .form-control:focus,
    .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    .btn-primary {
        background-color: #667eea;
        border-color: #667eea;
    }

    .btn-primary:hover {
        background-color: #5568d3;
        border-color: #5568d3;
    }
</style>
@endsection
