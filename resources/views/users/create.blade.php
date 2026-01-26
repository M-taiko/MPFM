@extends('layouts.master')

@section('title', 'Create New User')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title mb-2">Create New User</h1>
                <p class="text-muted mb-0">
                    Add a new user to the system
                </p>
            </div>
            <div class="btn-group">
                <a href="{{ route('users.index') }}" class="btn btn-secondary">
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
                        <i class="fe fe-user-plus"></i> User Information
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('users.store') }}" method="POST">
                        @csrf

                        <!-- Name -->
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name') }}" required
                                   placeholder="Enter user full name">
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="form-group mb-3">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email') }}" required
                                   placeholder="Enter email address">
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="form-group mb-3">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                   id="password" name="password" required
                                   placeholder="Enter password (minimum 8 characters)">
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="form-group mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control"
                                   id="password_confirmation" name="password_confirmation" required
                                   placeholder="Confirm password">
                        </div>

                        <!-- Department -->
                        <div class="form-group mb-3">
                            <label for="department_id" class="form-label">Department</label>
                            <select class="form-control @error('department_id') is-invalid @enderror"
                                    id="department_id" name="department_id">
                                <option value="">-- Select Department --</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}"
                                            {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Roles -->
                        <div class="form-group mb-4">
                            <label class="form-label">Assign Roles</label>
                            <div class="border rounded p-3" style="background: #f8f9fa;">
                                @foreach($roles as $role)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox"
                                               id="role_{{ $role->id }}" name="roles[]" value="{{ $role->name }}"
                                               {{ in_array($role->name, old('roles', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="role_{{ $role->id }}">
                                            <strong>{{ str_replace('_', ' ', ucfirst($role->name)) }}</strong>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('roles')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Buttons -->
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fe fe-save"></i> Create User
                            </button>
                            <a href="{{ route('users.index') }}" class="btn btn-secondary">
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
                        <i class="fe fe-info"></i> Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-muted mb-2"><i class="fe fe-lock"></i> Password Requirements</h6>
                        <ul class="small text-muted mb-0" style="padding-left: 20px;">
                            <li>Minimum 8 characters</li>
                            <li>Must match confirmation</li>
                            <li>Should be strong</li>
                        </ul>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <h6 class="text-muted mb-2"><i class="fe fe-users"></i> Roles</h6>
                        <p class="small text-muted mb-0">
                            Select one or more roles to assign to this user. Roles determine the user's permissions in the system.
                        </p>
                    </div>

                    <hr>

                    <div>
                        <h6 class="text-muted mb-2"><i class="fe fe-briefcase"></i> Department</h6>
                        <p class="small text-muted mb-0">
                            Optionally assign the user to a department. Operators must be assigned to a department.
                        </p>
                    </div>
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

    .form-check-input:checked {
        background-color: #667eea;
        border-color: #667eea;
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
