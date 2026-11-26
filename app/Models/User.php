<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    use HasRoles { hasRole as protected spatieHasRole; }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'department_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get operational plans assigned to this user (for operators)
     */
    public function assignedPlans(): HasMany
    {
        return $this->hasMany(MPFMOperationalPlan::class, 'assigned_user_id');
    }

    /**
     * Get MPFM tests created by this user
     */
    public function mpfmTests(): HasMany
    {
        return $this->hasMany(MPFMTest::class, 'user_id');
    }

    /**
     * Get the department this user belongs to
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Scope to filter users in a specific department
     */
    public function scopeInDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Scope to filter users with a specific role
     */
    public function scopeWithRole($query, $roleName)
    {
        return $query->whereHas('roles', fn($q) => $q->where('name', $roleName));
    }

    /**
     * Check if user is a department manager
     */
    public function isDepartmentManager(): bool
    {
        return $this->hasRole('department_manager');
    }

    /**
     * Check if user can manage a specific department
     */
    public function canManageDepartment($departmentId): bool
    {
        return $this->department_id === $departmentId && $this->isDepartmentManager();
    }

    /**
     * Check if user is surveillance supervisor
     */
    public function isSurveillanceSupervisor(): bool
    {
        return $this->hasRole('surveillance_supervisor');
    }

    /**
     * Check if user is job officer
     */
    public function isJobOfficer(): bool
    {
        return $this->hasRole('job_officer');
    }

    /**
     * Check if user is operator
     */
    public function isOperator(): bool
    {
        return $this->hasAnyRole(['mpfm_operator', 'operator']);
    }

    /**
     * Wire Line access: management roles, supervisors, and Wire Line department users
     */
    public function canAccessWireline(): bool
    {
        return $this->hasAnyRole(['admin', 'department_manager', 'job_officer', 'surveillance_supervisor', 'production_supervisor'])
            || $this->department?->code === 'WIRELINE';
    }

    /**
     * Well Integrity access (view): same audience as Wire Line plus reservoir engineers
     */
    public function canAccessIntegrity(): bool
    {
        return $this->canAccessWireline() || $this->hasRole('reservoir_engineer');
    }

    /**
     * Well Integrity management (create / edit records)
     */
    public function canManageIntegrity(): bool
    {
        return $this->hasAnyRole(['admin', 'department_manager', 'job_officer', 'surveillance_supervisor']);
    }

    /**
     * Locations (onshore / offshore sites): same managers as Well Integrity
     */
    public function canManageLocations(): bool
    {
        return $this->canManageIntegrity();
    }

    /**
     * "full_access" users pass every check that an admin passes, except user / role / permission management,
     * which stays with real admins (see isStrictAdmin()).
     */
    public function hasRole($roles, ?string $guard = null): bool
    {
        if ($this->spatieHasRole($roles, $guard)) {
            return true;
        }
        if ($this->isFullAccess()) {
            $list = is_string($roles) ? explode('|', $roles) : (is_iterable($roles) ? collect($roles)->all() : []);
            foreach ($list as $r) {
                if ($r === 'admin') {
                    return true;
                }
            }
        }

        return false;
    }

    public function isFullAccess(): bool
    {
        $this->loadMissing('roles');

        return $this->roles->contains('name', 'full_access');
    }

    /** A real admin: the only kind of user who may manage users, roles and permissions */
    public function isStrictAdmin(): bool
    {
        $this->loadMissing('roles');

        return $this->roles->contains('name', 'admin');
    }
    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user has admin access (full system control)
     * Admin users can:
     * - Access all modules
     * - Override any authorization
     * - Manage users, roles, permissions
     * - Access all departments and tasks
     * - View all reports and analytics
     */
    public function hasAdminAccess(): bool
    {
        return $this->isAdmin();
    }
}
