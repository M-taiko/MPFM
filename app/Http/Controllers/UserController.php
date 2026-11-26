<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isStrictAdmin()) {
                abort(403, 'Unauthorized access to user management.');
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of users (filtered and sorted client-side)
     */
    public function index(Request $request)
    {
        $users = User::with(['roles.permissions', 'department', 'permissions'])->orderBy('name')->get();
        $departments = Department::where('is_active', true)->orderBy('display_order')->get();
        $roles = Role::withCount('users')->orderBy('name')->get();

        return view('users.index', compact('users', 'departments', 'roles'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        $departments = Department::where('is_active', true)->orderBy('display_order')->get();
        $roles = Role::with('permissions')->orderBy('name')->get();

        return view('users.create', compact('departments', 'roles'));
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'department_id' => 'nullable|exists:departments,id',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,name',
        ]);

        // Create the user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'department_id' => $validated['department_id'] ?? null,
        ]);

        // Assign roles
        if (isset($validated['roles']) && is_array($validated['roles'])) {
            $user->syncRoles($validated['roles']);
        }

        return redirect()->route('users.index')
            ->with('success', 'User created successfully!');
    }

    /**
     * Show the form for editing a user
     */
    public function edit(User $user)
    {
        $departments = Department::where('is_active', true)->orderBy('display_order')->get();
        $roles = Role::with('permissions')->orderBy('name')->get();
        $userRoles = $user->roles->pluck('name')->toArray();

        return view('users.edit', compact('user', 'departments', 'roles', 'userRoles'));
    }

    /**
     * Update a user
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'department_id' => 'nullable|exists:departments,id',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,name',
        ]);

        // Update user details
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'department_id' => $validated['department_id'] ?? null,
        ]);

        // Update password if provided
        if (!empty($validated['password'])) {
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);
        }

        // Sync roles (an admin can't remove their own admin role and lock themselves out)
        $roles = $validated['roles'] ?? [];
        if ($user->id === auth()->id() && $user->hasRole('admin') && ! in_array('admin', $roles, true)) {
            $roles[] = 'admin';
        }
        $user->syncRoles($roles);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully!');
    }

    /**
     * Delete a user
     */
    public function destroy(User $user)
    {
        $userName = $user->name;

        // Prevent deletion of the currently authenticated user
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'You cannot delete your own account!');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', "User '{$userName}' deleted successfully!");
    }

    /**
     * Show user details view
     */
    public function show(User $user)
    {
        $user->load(['roles.permissions', 'permissions', 'department']);
        $allRoles = Role::orderBy('name')->get();
        $allPermissions = Permission::orderBy('name')->get();

        $activity = [
            'mpfm' => \App\Models\MPFMTest::where('user_id', $user->id)->count(),
            'tv' => \App\Models\TestVesselTest::where('user_id', $user->id)->count(),
            'last_mpfm' => \App\Models\MPFMTest::where('user_id', $user->id)->latest('test_date')->value('test_date'),
        ];

        return view('users.show', compact('user', 'allRoles', 'allPermissions', 'activity'));
    }
}
