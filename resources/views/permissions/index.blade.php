@extends('layouts.master')

@section('title', 'Permissions Management')

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

    .permission-item {
        background: white;
        border: 1px solid #e8ecf1;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.3s ease;
    }

    .permission-item:hover {
        border-color: #667eea;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.1);
    }

    .permission-info {
        flex: 1;
    }

    .permission-name {
        font-weight: 600;
        font-size: 1rem;
        color: #333;
        margin: 0 0 5px 0;
        word-break: break-word;
    }

    .permission-date {
        font-size: 0.85rem;
        color: #999;
        margin: 0;
    }

    .permission-actions {
        display: flex;
        gap: 8px;
        margin-left: 15px;
    }

    .action-btn {
        padding: 8px 12px;
        border-radius: 6px;
        border: 1px solid #ddd;
        background: white;
        color: #333;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 0.85rem;
        white-space: nowrap;
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

    .permissions-container {
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    }

    .no-permissions {
        text-align: center;
        padding: 60px 20px;
        color: #999;
    }

    .no-permissions i {
        font-size: 48px;
        color: #ccc;
        display: block;
        margin-bottom: 15px;
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            gap: 15px;
            text-align: center;
        }

        .permission-item {
            flex-direction: column;
            text-align: center;
        }

        .permission-actions {
            margin-left: 0;
            margin-top: 15px;
            justify-content: center;
        }

        .permission-name {
            font-size: 0.95rem;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1><i class="fe fe-lock mr-2"></i>Permissions Management</h1>
        </div>
        <a href="{{ route('permissions.create') }}" class="btn btn-light">
            <i class="fe fe-plus mr-2"></i>Create New Permission
        </a>
    </div>

    <!-- Permissions Container -->
    <div class="permissions-container">
        @forelse($permissions as $permission)
        <div class="permission-item">
            <div class="permission-info">
                <p class="permission-name">{{ str_replace('_', ' ', $permission->name) }}</p>
                <p class="permission-date">
                    <i class="fe fe-calendar"></i> Created: {{ $permission->created_at->format('M d, Y') }}
                </p>
            </div>
            <div class="permission-actions">
                <a href="{{ route('permissions.edit', $permission) }}" class="action-btn">
                    <i class="fe fe-edit"></i> Edit
                </a>
                <button class="action-btn danger" onclick="confirmDelete({{ $permission->id }})">
                    <i class="fe fe-trash"></i> Delete
                </button>
            </div>
        </div>
        @empty
        <div class="no-permissions">
            <i class="fe fe-lock"></i>
            <h3>No Permissions Found</h3>
            <p>Create a new permission to get started.</p>
            <a href="{{ route('permissions.create') }}" class="btn btn-primary mt-3">
                <i class="fe fe-plus mr-2"></i>Create Permission
            </a>
        </div>
        @endforelse
    </div>
</div>

<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
    function confirmDelete(permissionId) {
        if (confirm('Are you sure you want to delete this permission? This may affect users and roles that have this permission.')) {
            document.getElementById('deleteForm').action = `/permissions/${permissionId}`;
            document.getElementById('deleteForm').submit();
        }
    }
</script>
@endpush
