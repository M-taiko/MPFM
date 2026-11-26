<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DepartmentUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            // Allow department_supervisor or surveillance_supervisor or admin
            $user = auth()->user();
            if (!$user->hasRole(['department_supervisor', 'surveillance_supervisor', 'admin'])) {
                abort(403, 'Unauthorized access to department user management.');
            }
            return $next($request);
        });
    }

    /**
     * Check if user can access this department
     */
    private function checkDepartmentAccess(Department $department)
    {
        $user = auth()->user();

        // full-access users run operations but do not manage people
        if ($user->isFullAccess() && ! $user->isStrictAdmin()) {
            abort(403, 'User management is not available for this account.');
        }

        // Admin can access all departments
        if ($user->hasRole('admin')) {
            return true;
        }

        // Surveillance supervisor can access all departments (read-only view access)
        if ($user->hasRole('surveillance_supervisor')) {
            return true;
        }

        // Department supervisor can only access their own department
        if ($user->hasRole('department_supervisor')) {
            if ($user->department_id !== $department->id) {
                abort(403, 'You do not have access to this department.');
            }
            return true;
        }

        abort(403, 'Unauthorized access to department management.');
    }

    /**
     * Display users for a department
     */
    public function index(Department $department)
    {
        $this->checkDepartmentAccess($department);

        // Get all users in this department
        $users = $department->users()
            ->with('roles')
            ->paginate(10);

        return view('departments.users.index', compact('department', 'users'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create(Department $department)
    {
        // Only department supervisor and admin can create users
        if (!auth()->user()->hasRole(['department_supervisor', 'admin'])) {
            abort(403, 'You do not have permission to create users.');
        }

        $this->checkDepartmentAccess($department);

        // Get operator roles to assign to new users
        $operatorRoles = Role::whereIn('name', ['operator', 'mpfm_operator'])->get();

        return view('departments.users.create', compact('department', 'operatorRoles'));
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request, Department $department)
    {
        // Only department supervisor and admin can create users
        if (!auth()->user()->hasRole(['department_supervisor', 'admin'])) {
            abort(403, 'You do not have permission to create users.');
        }

        $this->checkDepartmentAccess($department);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
        ]);

        // Create the user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'department_id' => $department->id,
        ]);

        // Assign role
        $user->assignRole($validated['role']);

        return redirect()->route('departments.users.index', $department->id)
            ->with('success', 'User created successfully!');
    }

    /**
     * Show the form for editing a user
     */
    public function edit(Department $department, User $user)
    {
        // Only department supervisor and admin can edit users
        if (!auth()->user()->hasRole(['department_supervisor', 'admin'])) {
            abort(403, 'You do not have permission to edit users.');
        }

        $this->checkDepartmentAccess($department);

        // Check if user belongs to this department
        if ($user->department_id !== $department->id) {
            abort(404, 'User not found in this department.');
        }

        $operatorRoles = Role::whereIn('name', ['operator', 'mpfm_operator'])->get();
        $userRole = $user->roles()->first();

        return view('departments.users.edit', compact('department', 'user', 'operatorRoles', 'userRole'));
    }

    /**
     * Update a user
     */
    public function update(Request $request, Department $department, User $user)
    {
        // Only department supervisor and admin can edit users
        if (!auth()->user()->hasRole(['department_supervisor', 'admin'])) {
            abort(403, 'You do not have permission to edit users.');
        }

        $this->checkDepartmentAccess($department);

        // Check if user belongs to this department
        if ($user->department_id !== $department->id) {
            abort(404, 'User not found in this department.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
        ]);

        // Update user details
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        // Update password if provided
        if (!empty($validated['password'])) {
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);
        }

        // Update role
        $user->syncRoles($validated['role']);

        return redirect()->route('departments.users.index', $department->id)
            ->with('success', 'User updated successfully!');
    }

    /**
     * Delete a user
     */
    public function destroy(Department $department, User $user)
    {
        // Only department supervisor and admin can delete users
        if (!auth()->user()->hasRole(['department_supervisor', 'admin'])) {
            abort(403, 'You do not have permission to delete users.');
        }

        $this->checkDepartmentAccess($department);

        // Check if user belongs to this department
        if ($user->department_id !== $department->id) {
            abort(404, 'User not found in this department.');
        }

        $userName = $user->name;

        // Prevent deletion of the current user
        if ($user->id === auth()->id()) {
            return redirect()->route('departments.users.index', $department->id)
                ->with('error', 'You cannot delete your own account!');
        }

        $user->delete();

        return redirect()->route('departments.users.index', $department->id)
            ->with('success', "User '{$userName}' deleted successfully!");
    }
}
