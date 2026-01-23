@extends('layouts.master')

@section('title', 'Roles Management')

@section('css')
<style>
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 8px;
        margin-bottom: 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .page-header h1 {
        font-size: 2.2rem;
        font-weight: 600;
        margin: 0;
    }

    .page-header .btn {
        padding: 10px 25px;
        font-weight: 500;
        border-radius: 6px;
    }

    .role-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        transition: all 0.3s ease;
        margin-bottom: 20px;
        overflow: hidden;
    }

    .role-card:hover {
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        transform: translateY(-2px);
    }

    .role-card-header {
        background: linear-gradient(135deg, #f5f7fa 0%, #f9fafb 100%);
        padding: 20px;
        border-bottom: 1px solid #e8ecf1;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .role-name {
        font-weight: 600;
        font-size: 1.2rem;
        color: #333;
        margin: 0;
    }

    .user-badge {
        background: #667eea;
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .role-card-body {
        padding: 20px;
    }

    .permissions-section {
        margin-bottom: 20px;
    }

    .permissions-label {
        font-size: 0.9rem;
        font-weight: 600;
        color: #667eea;
        text-transform: uppercase;
        margin-bottom: 10px;
        display: block;
    }

    .permissions-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 8px;
    }

    .permission-item {
        display: inline-block;
        padding: 6px 12px;
        background: #f0f4ff;
        color: #667eea;
        border: 1px solid #c5d3f0;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .permission-item.no-permissions {
        color: #999;
        background: #f5f5f5;
        border-color: #ddd;
    }

    .role-actions {
        display: flex;
        gap: 10px;
        padding-top: 15px;
        border-top: 1px solid #e8ecf1;
    }

    .action-btn {
        flex: 1;
        font-size: 0.85rem;
        padding: 10px 15px;
        border-radius: 6px;
        border: 1px solid #ddd;
        background: white;
        color: #333;
        cursor: pointer;
        transition: all 0.2s;
        text-align: center;
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

    .roles-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
        gap: 20px;
    }

    @media (max-width: 768px) {
        .roles-grid {
            grid-template-columns: 1fr;
        }

        .page-header {
            flex-direction: column;
            gap: 15px;
            text-align: center;
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
        <div>
            <h1><i class="fe fe-shield mr-2"></i>Roles Management</h1>
        </div>
        <a href="{{ route('roles.create') }}" class="btn btn-light">
            <i class="fe fe-plus mr-2"></i>Create New Role
        </a>
    </div>

    <!-- Roles Grid -->
    <div class="roles-grid">
        @forelse($roles as $role)
        <div class="card role-card">
            <div class="role-card-header">
                <h3 class="role-name">
                    {{ str_replace('_', ' ', ucfirst($role->name)) }}
                </h3>
                <span class="user-badge">{{ $role->users->count() }} user{{ $role->users->count() !== 1 ? 's' : '' }}</span>
            </div>

            <div class="role-card-body">
                <!-- Permissions -->
                <div class="permissions-section">
                    <span class="permissions-label">Permissions ({{ $role->permissions->count() }})</span>
                    @if($role->permissions->count() > 0)
                        <div class="permissions-list">
                            @foreach($role->permissions as $permission)
                                <span class="permission-item">
                                    {{ str_replace('_', ' ', $permission->name) }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        <span class="permission-item no-permissions">No permissions assigned</span>
                    @endif
                </div>

                <!-- Actions -->
                <div class="role-actions">
                    <a href="{{ route('roles.edit', $role) }}" class="action-btn" title="Edit this role">
                        <i class="fe fe-edit"></i> Edit
                    </a>
                    <button class="action-btn" onclick="showPermissionModal({{ $role->id }})" title="Manage permissions">
                        <i class="fe fe-lock"></i> Permissions
                    </button>
                    <button class="action-btn danger" onclick="confirmDelete({{ $role->id }})" title="Delete this role">
                        <i class="fe fe-trash"></i> Delete
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card text-center py-5">
                <i class="fe fe-shield" style="font-size: 48px; color: #ccc;"></i>
                <h3 class="mt-3">No Roles Found</h3>
                <p class="text-muted">Create a new role to get started.</p>
                <a href="{{ route('roles.create') }}" class="btn btn-primary mt-3">
                    <i class="fe fe-plus mr-2"></i>Create Role
                </a>
            </div>
        </div>
        @endforelse
    </div>
</div>

<!-- Permission Management Modal -->
<div class="modal fade" id="permissionModal" tabindex="-1" role="dialog" aria-labelledby="permissionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="permissionModalLabel">Manage Role Permissions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="permissionForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="permissionsList" class="form-label">Select Permissions</label>
                        <div id="permissionsList" class="row">
                            @foreach($allPermissions as $permission)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input permission-checkbox" type="checkbox" value="{{ $permission->name }}" id="perm_{{ $permission->id }}" name="permissions[]">
                                        <label class="form-check-label" for="perm_{{ $permission->id }}">
                                            {{ str_replace('_', ' ', $permission->name) }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Update Permissions</button>
                </form>
            </div>
        </div>
    </div>
</div>

<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
    let currentRoleId = null;

    function showPermissionModal(roleId) {
        currentRoleId = roleId;
        document.getElementById('permissionForm').action = `/roles/${roleId}/permissions`;

        // Uncheck all checkboxes first
        document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = false);

        // Fetch and check current permissions
        fetch(`/api/role-permissions/${roleId}`)
            .then(response => response.json())
            .then(data => {
                const currentPermissions = data.permissions.map(p => p.name);
                document.querySelectorAll('.permission-checkbox').forEach(cb => {
                    if (currentPermissions.includes(cb.value)) {
                        cb.checked = true;
                    }
                });
                new bootstrap.Modal(document.getElementById('permissionModal')).show();
            });
    }

    function confirmDelete(roleId) {
        if (confirm('Are you sure you want to delete this role? Users with this role will be affected.')) {
            document.getElementById('deleteForm').action = `/roles/${roleId}`;
            document.getElementById('deleteForm').submit();
        }
    }

    document.getElementById('permissionForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const permissions = Array.from(document.querySelectorAll('.permission-checkbox:checked')).map(cb => cb.value);

        fetch(this.action, {
            method: 'PUT',
            body: JSON.stringify({ permissions }),
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Permissions updated successfully!');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating permissions.');
        });
    });
</script>
@endpush
@endsection
