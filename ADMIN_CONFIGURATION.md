# Admin Configuration & Full System Access

## Overview

The MPFM (Multi-Phase Flow Meter) Oil Field Management System is configured with comprehensive admin access. Admin users have unrestricted permissions across the entire system.

## Admin Capabilities

### System-Wide Access
Admins can access and manage:
- ✅ **All User Management**: Create, edit, delete users; assign/remove roles
- ✅ **All Role & Permission Management**: Create roles, assign permissions, modify access levels
- ✅ **All Department Management**: Create/edit departments, manage department assignments
- ✅ **All Task & Plan Management**: Create, edit, delete, modify any task or plan
- ✅ **Workflow State Override**: Change task states regardless of normal workflow constraints
- ✅ **All Operator Management**: Assign operators to any task, any department
- ✅ **All Reports & Analytics**: View all system reports, statistics, and department breakdowns
- ✅ **Global Visibility**: View data across all departments without restrictions

### Authorization Layers

Admin access is enforced at multiple levels:

#### 1. **Spatie Permission System**
```php
// In RolePermissionSeeder.php
$adminRole->givePermissionTo(Permission::all());
```
Admin users are automatically granted **all permissions** in the system.

#### 2. **Gate-Based Authorization**
```php
// In AuthServiceProvider.php
Gate::define('admin-access', function ($user) {
    return $user && $user->isAdmin();
});

// Before gate for admin bypass on all abilities
Gate::before(function ($user, $ability) {
    if ($user->isAdmin()) {
        return true;
    }
});
```
All gate checks bypass for admins automatically.

#### 3. **Model Policies**
```php
// In PlanTaskPolicy.php (and others)
public function before(User $user, string $ability): ?bool
{
    if ($user->hasRole('admin')) {
        return true;
    }
    return null;
}
```
All model policy checks include admin bypasses.

#### 4. **Controller Authorization**
```php
// In AdminDashboardController.php
public function dashboard()
{
    $this->authorize('admin-access');
    // Admin can access any resource
}
```

#### 5. **User Model Methods**
```php
// In User.php
public function isAdmin(): bool
{
    return $this->hasRole('admin');
}

public function hasAdminAccess(): bool
{
    return $this->isAdmin();
}
```

## Admin User Setup

### Creating an Admin User

#### Via Database Seed (Recommended)
The system includes a seeder that creates admin users:

```php
// In UserSeeder.php
$admin = User::firstOrCreate(
    ['email' => 'admin@adnoc.ae'],
    [
        'name' => 'System Administrator',
        'password' => bcrypt('password'),
        'department_id' => null, // Admin has global access
    ]
);
$admin->assignRole('admin');
```

#### Via Artisan Command
```bash
php artisan tinker
> $user = User::create(['name' => 'Admin User', 'email' => 'admin@example.com', 'password' => bcrypt('password')]);
> $user->assignRole('admin');
```

#### Via Administrator API
Use the admin dashboard to create users and assign the 'admin' role.

## Admin Dashboard

The system includes a comprehensive admin dashboard at `/admin/dashboard` with:

- **System Statistics**: Total users, departments, plans, tasks, roles, permissions
- **User Management**: View, create, edit, delete users; assign roles
- **Department Management**: Manage all departments and user assignments
- **Plan Management**: View all plans across all departments
- **Task Management**: View, edit, override states for all tasks
- **Role & Permission Management**: Create/manage roles and permissions
- **System Analytics**: Task completion rates, user distribution, departmental breakdown

### Admin Dashboard Routes

```
GET  /admin/dashboard           - Main admin dashboard
GET  /admin/users               - User management
GET  /admin/departments         - Department management
GET  /admin/plans               - Plan management
GET  /admin/tasks               - Task management
GET  /admin/roles               - Role & permission management
GET  /admin/statistics          - System statistics
POST /admin/users/{user}/role   - Assign role to user
```

## Admin Permissions List

### All Available Permissions
Admin users have all permissions including:

**User Management**
- `manage_users` - Full user management
- `view_users` - View all users
- `create_users` - Create new users
- `edit_users` - Edit user details
- `delete_users` - Delete users

**Role Management**
- `manage_roles` - Full role management
- `view_roles` - View all roles
- `create_roles` - Create new roles
- `edit_roles` - Edit role definitions
- `delete_roles` - Delete roles

**Permission Management**
- `manage_permissions` - Full permission management
- `view_permissions` - View all permissions
- `create_permissions` - Create new permissions
- `edit_permissions` - Edit permissions
- `delete_permissions` - Delete permissions

**Well Test Management**
- `view_well_tests` - View all well tests
- `create_well_tests` - Create new tests
- `edit_well_tests` - Edit test details
- `delete_well_tests` - Delete tests

**Reports**
- `view_job_officer_reports` - Access Job Officer reports
- `view_reservoir_engineer_reports` - Access Engineer reports
- `view_production_supervisor_reports` - Access Supervisor reports
- `view_mpfm_supervisor_reports` - Access MPFM reports
- `view_mpfm_operator_reports` - Access Operator reports

**Department & Workflow**
- `view_department_tasks` - View tasks in any department
- `assign_operators` - Assign operators to any task
- `review_department_work` - Review department work
- `view_all_departments` - View all departments
- `view_well_string_requirements` - View string requirements

## Authorization Verification

### Check if User is Admin

```php
// In controllers or services
if (auth()->user()->isAdmin()) {
    // User has full system access
}

// Or using Gate
if (Gate::allows('admin-access')) {
    // User has admin access
}

// Or in views
@if(auth()->user()->isAdmin())
    <!-- Show admin-only content -->
@endif
```

### Verify Admin in Routes

```php
// Route-level authorization
Route::group(['middleware' => ['role:admin']], function () {
    // Routes only accessible to admins
});

// Or use the admin-access gate
Route::post('/admin/override-task-state', function () {
    // Automatically checks admin-access gate
})->middleware('gate:admin-access');
```

## Admin-Only Features

### 1. Task State Override
Admins can change task states to any valid state, even if it violates normal workflow:
```php
$task->update(['state' => 'completed']);
```

### 2. Cross-Department Operator Assignment
Admins can assign operators from any department to any task:
```php
$task->assignedOperators()->attach($operator->id);
```

### 3. Direct User Role Changes
Admins can assign/remove roles without restrictions:
```php
$user->assignRole('admin');
$user->removeRole('operator');
```

### 4. Department Reassignment
Admins can change a user's department:
```php
$user->update(['department_id' => $newDept->id]);
```

### 5. Plan Status Changes
Admins can change plan status directly:
```php
$plan->update(['status' => 'completed']);
```

## Security Considerations

### Admin Access Logging
All admin actions should be logged (implement logging middleware):
```php
// Recommended: Add audit logging for admin actions
event(new AdminActionPerformed($admin, $action, $resource));
```

### Restrict Admin Creation
Limit who can create new admin users:
- Only system owner can create initial admin
- Require verification for admin role assignment
- Log all admin role assignments

### Admin Account Protection
- Use strong passwords for admin accounts
- Enable 2FA when available
- Regularly audit admin account activities
- Disable unused admin accounts

### Admin Password Policy
- Require strong passwords (min 16 characters)
- Enforce regular password changes
- Implement password history
- Monitor failed login attempts

## Testing Admin Access

### Test Admin Bypass
```php
// In tests/Feature/AdminTest.php
public function test_admin_can_override_task_state()
{
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $task = PlanTask::factory()->create();

    $this->actingAs($admin)
        ->postJson('/api/tasks/' . $task->id . '/state', ['state' => 'completed'])
        ->assertOk();
}
```

### Test Admin Visibility
```php
public function test_admin_can_view_all_departments()
{
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $departments = Department::all();

    $this->actingAs($admin)
        ->get('/admin/departments')
        ->assertSeeText($departments->pluck('name')->toArray());
}
```

## Configuration Files Modified

### For Admin Support:
1. **app/Models/User.php** - Added `isAdmin()` and `hasAdminAccess()` methods
2. **app/Policies/AdminPolicy.php** - Created comprehensive admin policy with before hook
3. **app/Providers/AuthServiceProvider.php** - Added admin-access gate and before hook
4. **app/Http/Controllers/AdminDashboardController.php** - Created admin dashboard controller
5. **app/Http/Middleware/AdminAuthorizationBypass.php** - Created admin bypass middleware
6. **database/seeders/RolePermissionSeeder.php** - Admin gets all permissions

## Troubleshooting

### Admin Not Seeing All Data
Verify user has admin role:
```bash
php artisan tinker
> User::find($userId)->roles->pluck('name');
// Should include 'admin'
```

### Admin Can't Access Route
Check that the route doesn't have restrictive middleware:
```php
// Don't do this on admin routes:
Route::get('/route', Controller@action)->middleware('role:operator');

// Instead:
Route::get('/route', Controller@action)->middleware('role:admin|operator');
```

### Permission Cache Issues
Clear the permission cache after changes:
```bash
php artisan cache:clear
php artisan config:clear
```

## Future Enhancements

1. **Audit Trail**: Log all admin actions for compliance
2. **Admin Activity Dashboard**: Real-time monitoring of admin activities
3. **Delegation**: Allow admins to delegate specific permissions
4. **Admin Notes**: Add notes to user accounts for documentation
5. **Two-Factor Authentication**: Enforce 2FA for admin accounts
6. **Admin Approval Workflow**: Require approval for critical actions

## Support & Questions

For admin-related questions or issues:
1. Check the admin dashboard for system statistics
2. Review role and permission assignments
3. Check application logs for error messages
4. Contact system administrator

---

**Document Version**: 1.0
**Last Updated**: January 27, 2026
**System**: MPFM - Multi-Phase Flow Meter Management System
