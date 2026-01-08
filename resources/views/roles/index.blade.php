@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Roles Management</h4>
                    <a href="{{ route('roles.create') }}" class="btn btn-primary">Create Role</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped text-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Permissions</th>
                                    <th>Users Count</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($roles as $role)
                                <tr>
                                    <td>{{ $role->name }}</td>
                                    <td>
                                        <div class="d-flex flex-wrap">
                                            @foreach($role->permissions as $permission)
                                                <span class="badge bg-success me-1 mb-1">{{ $permission->name }}</span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td>{{ $role->users->count() }}</td>
                                    <td>{{ $role->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Actions
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="{{ route('roles.edit', $role) }}">Edit</a>
                                                <a class="dropdown-item" href="javascript:void(0);" onclick="showPermissionModal({{ $role->id }})">Manage Permissions</a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="confirmDelete({{ $role->id }})">Delete</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
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
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input permission-checkbox" type="checkbox" value="{{ $permission->name }}" id="perm_{{ $permission->id }}" name="permissions[]">
                                        <label class="form-check-label" for="perm_{{ $permission->id }}">
                                            {{ $permission->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Permissions</button>
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
    let currentRoleId = null;

    function showPermissionModal(roleId) {
        currentRoleId = roleId;
        // Clear all checkboxes first
        document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
            checkbox.checked = false;
        });

        // Set the form action
        document.getElementById('permissionForm').action = `/roles/${roleId}/permissions`;

        // Get current permissions for this role via AJAX and check the appropriate boxes
        fetch(`/api/role-permissions/${roleId}`)
            .then(response => response.json())
            .then(data => {
                data.permissions.forEach(permission => {
                    const checkbox = document.getElementById(`perm_${permission.id}`);
                    if (checkbox) {
                        checkbox.checked = true;
                    }
                });
                $('#permissionModal').modal('show');
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading permissions');
            });
    }

    function confirmDelete(roleId) {
        if (confirm('Are you sure you want to delete this role? This will also remove this role from all users.')) {
            document.getElementById('deleteForm').action = `/roles/${roleId}`;
            document.getElementById('deleteForm').submit();
        }
    }

    // Handle permission form submission
    document.getElementById('permissionForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch(this.action, {
            method: 'PUT',
            body: formData,
            headers: {
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
