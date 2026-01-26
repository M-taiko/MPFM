@extends('layouts.master')

@section('title', 'Users Management')

@section('css')
<style>
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 8px;
        margin-bottom: 30px;
    }

    .page-header h1 {
        font-size: 2.2rem;
        font-weight: 600;
        margin: 0;
    }

    .page-header p {
        margin: 8px 0 0 0;
        opacity: 0.9;
        font-size: 0.95rem;
    }

    .user-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        transition: all 0.3s ease;
        margin-bottom: 20px;
    }

    .user-card:hover {
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        transform: translateY(-2px);
    }

    .user-card-header {
        background: linear-gradient(135deg, #f5f7fa 0%, #f9fafb 100%);
        padding: 20px;
        border-bottom: 1px solid #e8ecf1;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .user-info {
        flex: 1;
    }

    .user-name {
        font-weight: 600;
        font-size: 1.1rem;
        color: #333;
        margin: 0;
    }

    .user-email {
        color: #666;
        font-size: 0.9rem;
        margin: 4px 0 0 0;
    }

    .user-date {
        color: #999;
        font-size: 0.85rem;
        margin-top: 4px;
    }

    .user-card-body {
        padding: 15px 20px;
    }

    .roles-section {
        margin-bottom: 15px;
    }

    .roles-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #667eea;
        text-transform: uppercase;
        margin-bottom: 8px;
        display: block;
    }

    .role-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .role-badge {
        display: inline-block;
        padding: 5px 12px;
        background: #e3f2fd;
        color: #1976d2;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
        border: 1px solid #90caf9;
    }

    .role-badge.admin {
        background: #ffebee;
        color: #c62828;
        border-color: #ef5350;
    }

    .role-badge.supervisor {
        background: #f3e5f5;
        color: #6a1b9a;
        border-color: #ce93d8;
    }

    .role-badge.operator {
        background: #e8f5e9;
        color: #2e7d32;
        border-color: #81c784;
    }

    .actions-section {
        display: flex;
        gap: 8px;
        padding-top: 15px;
        border-top: 1px solid #e8ecf1;
    }

    .action-btn {
        flex: 1;
        font-size: 0.85rem;
        padding: 8px 12px;
        border-radius: 6px;
        border: 1px solid #ddd;
        background: white;
        color: #333;
        cursor: pointer;
        transition: all 0.2s;
    }

    .action-btn:hover {
        background: #f5f7fa;
        border-color: #667eea;
        color: #667eea;
    }

    .action-btn.danger {
        color: #dc3545;
        border-color: #f5c6cb;
    }

    .action-btn.danger:hover {
        background: #fff5f7;
        border-color: #dc3545;
    }

    .users-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
        gap: 20px;
    }

    @media (max-width: 768px) {
        .users-grid {
            grid-template-columns: 1fr;
        }

        .page-header {
            padding: 20px;
        }

        .page-header h1 {
            font-size: 1.5rem;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1><i class="fe fe-users mr-2"></i>Users Management</h1>
                <p>Manage user accounts, roles, and permissions</p>
            </div>
            <a href="{{ route('users.create') }}" class="btn btn-primary">
                <i class="fe fe-plus"></i> Create New User
            </a>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="card mb-4" style="border-left: 5px solid #667eea;">
        <div class="card-body">
            <form method="GET" action="{{ route('users.index') }}" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control"
                           placeholder="Search by name or email" value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="department" class="form-control">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="role" class="form-control">
                        <option value="">All Roles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                                {{ str_replace('_', ' ', ucfirst($role->name)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fe fe-search"></i> Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Grid -->
    <div class="users-grid">
        @forelse($users as $user)
        <div class="card user-card">
            <div class="user-card-header">
                <div class="user-info">
                    <p class="user-name">{{ $user->name }}</p>
                    <p class="user-email">{{ $user->email }}</p>
                    <p class="user-date">
                        <i class="fe fe-calendar"></i> {{ $user->created_at->format('M d, Y') }}
                    </p>
                </div>
            </div>

            <div class="user-card-body">
                <!-- Roles -->
                <div class="roles-section">
                    <span class="roles-label">Roles ({{ $user->roles->count() }})</span>
                    <div class="role-badges">
                        @forelse($user->roles as $role)
                            <span class="role-badge {{ str_contains($role->name, 'admin') ? 'admin' : (str_contains($role->name, 'supervisor') ? 'supervisor' : 'operator') }}">
                                {{ str_replace('_', ' ', ucfirst($role->name)) }}
                            </span>
                        @empty
                            <span style="color: #999; font-size: 0.9rem;">No roles assigned</span>
                        @endforelse
                    </div>
                </div>

                <!-- Actions -->
                <div class="actions-section">
                    <a href="{{ route('users.edit', $user->id) }}" class="action-btn" title="Edit user information">
                        <i class="fe fe-edit"></i> Edit
                    </a>
                    <button class="action-btn" onclick="showRoleAssignModal({{ $user->id }})" title="Assign or change user role">
                        <i class="fe fe-plus"></i> Role
                    </button>
                    <button class="action-btn" onclick="showPermissionAssignModal({{ $user->id }})" title="Give specific permissions">
                        <i class="fe fe-lock"></i> Permission
                    </button>
                    <button class="action-btn danger" onclick="confirmDelete({{ $user->id }})" title="Permanently delete user">
                        <i class="fe fe-trash"></i> Delete
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card text-center py-5">
                <i class="fe fe-users" style="font-size: 48px; color: #ccc;"></i>
                <h3 class="mt-3">No Users Found</h3>
                <p class="text-muted">There are no users in the system yet.</p>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($users->hasPages())
    <div class="mt-4">
        {{ $users->links() }}
    </div>
    @endif
</div>

<!-- Role Assignment Modal -->
<div class="modal fade" id="roleAssignModal" tabindex="-1" role="dialog" aria-labelledby="roleAssignModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="roleAssignModalLabel">Assign Role to User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="roleAssignForm" method="POST">
                    @csrf
                    @method('POST')
                    <div class="mb-3">
                        <label for="roleSelect" class="form-label">Select Role</label>
                        <select class="form-select" id="roleSelect" name="role">
                            <option value="">Select Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Assign Role</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Permission Assignment Modal -->
<div class="modal fade" id="permissionAssignModal" tabindex="-1" role="dialog" aria-labelledby="permissionAssignModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="permissionAssignModalLabel">Assign Permission to User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="permissionAssignForm" method="POST">
                    @csrf
                    @method('POST')
                    <div class="mb-3">
                        <label for="permissionSelect" class="form-label">Select Permission</label>
                        <select class="form-select" id="permissionSelect" name="permission">
                            <option value="">Select Permission</option>
                            @foreach($permissions as $permission)
                                <option value="{{ $permission->name }}">{{ $permission->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Assign Permission</button>
                </form>
            </div>
        </div>
    </div>
</div>

<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
    let currentUserId = null;

    function showRoleAssignModal(userId) {
        currentUserId = userId;
        document.getElementById('roleAssignForm').action = `/users/${userId}/assign-role`;
        $('#roleAssignModal').modal('show');
    }

    function showPermissionAssignModal(userId) {
        currentUserId = userId;
        document.getElementById('permissionAssignForm').action = `/users/${userId}/assign-permission`;
        $('#permissionAssignModal').modal('show');
    }

    function confirmDelete(userId) {
        if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
            document.getElementById('deleteForm').action = `/users/${userId}`;
            document.getElementById('deleteForm').submit();
        }
    }

    // Handle form submissions
    document.getElementById('roleAssignForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Role assigned successfully!');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while assigning the role.');
        });
    });

    document.getElementById('permissionAssignForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Permission assigned successfully!');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while assigning the permission.');
        });
    });
</script>
@endpush
