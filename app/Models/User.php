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
    use HasFactory, Notifiable, HasRoles;

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
