<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (! auth()->user()->hasRole('admin')) {
                abort(403, 'Unauthorized access to roles and permissions.');
            }

            return $next($request);
        });
    }

    /* ===================== ROLES ===================== */

    public function indexRoles()
    {
        $roles = Role::with('permissions', 'users')->orderBy('name')->get();
        $allPermissions = Permission::orderBy('name')->get();

        return view('roles.index', compact('roles', 'allPermissions'));
    }

    public function createRole()
    {
        $permissions = Permission::orderBy('name')->get();
        return view('roles.create', compact('permissions'));
    }

    public function storeRole(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role = Role::create(['name' => $request->name]);

        if ($request->permissions) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
    }

    public function editRole(Role $role)
    {
        $role->load('users');
        $permissions = Permission::orderBy('name')->get();
        return view('roles.edit', compact('role', 'permissions'));
    }

    public function updateRole(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions ?? []);

        return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroyRole(Role $role)
    {
        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Role deleted successfully.');
    }

    /* ===================== PERMISSIONS ===================== */

    public function indexPermissions()
    {
        $permissions = Permission::with('roles')->orderBy('name')->get();
        $roles = Role::orderBy('name')->get();
        return view('permissions.index', compact('permissions', 'roles'));
    }

    public function createPermission()
    {
        return view('permissions.create');
    }

    public function storePermission(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name',
        ]);

        Permission::create(['name' => $request->name]);

        return redirect()->route('permissions.index')->with('success', 'Permission created successfully.');
    }

    public function editPermission(Permission $permission)
    {
        $permission->load('roles');
        return view('permissions.edit', compact('permission'));
    }

    public function updatePermission(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name,' . $permission->id,
        ]);

        $permission->update(['name' => $request->name]);

        return redirect()->route('permissions.index')->with('success', 'Permission updated successfully.');
    }

    public function destroyPermission(Permission $permission)
    {
        $permission->delete();
        return redirect()->route('permissions.index')->with('success', 'Permission deleted successfully.');
    }

    /* ===================== USERS ===================== */

    public function indexUsers()
    {
        $users = User::with('roles')->get();
        $roles = Role::all();
        $permissions = Permission::all();

        return view('users.index', compact('users', 'roles', 'permissions'));
    }

    public function assignRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|exists:roles,name',
        ]);

        $user->assignRole($request->role);

        return response()->json(['success' => true, 'message' => 'Role assigned successfully.']);
    }

    public function assignPermission(Request $request, User $user)
    {
        $request->validate([
            'permission' => 'required|exists:permissions,name',
        ]);

        $user->givePermissionTo($request->permission);

        return response()->json(['success' => true, 'message' => 'Permission assigned successfully.']);
    }

    public function removeRole(User $user, $roleName)
    {
        $user->removeRole($roleName);
        return response()->json(['success' => true, 'message' => 'Role removed successfully.']);
    }

    public function removePermission(User $user, $permissionName)
    {
        $user->revokePermissionTo($permissionName);
        return response()->json(['success' => true, 'message' => 'Permission removed successfully.']);
    }

    /* ===================== API ENDPOINTS ===================== */

    public function getRolePermissions(Role $role)
    {
        $permissions = $role->permissions;
        return response()->json(['permissions' => $permissions]);
    }

    public function updateRolePermissions(Request $request, Role $role)
    {
        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role->syncPermissions($request->permissions ?? []);

        return response()->json(['success' => true, 'message' => 'Permissions updated successfully.']);
    }
}
