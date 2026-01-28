<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'description',
        'is_active',
        'workflow_config',
        'display_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'workflow_config' => 'array',
    ];

    /**
     * Get all users in this department
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get all tasks assigned to this department
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(PlanTask::class);
    }

    /**
     * Get all active (non-completed) tasks in this department
     */
    public function activeTasks(): HasMany
    {
        return $this->tasks()
            ->whereNotIn('state', ['completed']);
    }

    /**
     * Get the workflow configuration for this department
     */
    public function workflow(): HasOne
    {
        return $this->hasOne(\App\Domains\MPFM\Models\DepartmentWorkflow::class);
    }

    /**
     * Get all test types for this department
     */
    public function testTypes(): HasMany
    {
        return $this->hasMany(\App\Domains\MPFM\Models\DepartmentTestType::class);
    }

    /**
     * Scope to get only active departments
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by display order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order');
    }

    /**
     * Get task completion rate for this department
     *
     * @return float Percentage (0-100)
     */
    public function getTaskCompletionRate(): float
    {
        $total = $this->tasks()->count();

        if ($total === 0) {
            return 0.0;
        }

        $completed = $this->tasks()
            ->where('state', 'completed')
            ->count();

        return round(($completed / $total) * 100, 2);
    }

    /**
     * Get count of tasks by state for this department
     *
     * @return array Associative array with state => count
     */
    public function getTaskStateCounts(): array
    {
        return $this->tasks()
            ->selectRaw('state, count(*) as count')
            ->groupBy('state')
            ->pluck('count', 'state')
            ->toArray();
    }

    /**
     * Get department managers (users with department_manager role in this dept)
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function managers()
    {
        return $this->users()
            ->whereHas('roles', fn($q) => $q->where('name', 'department_manager'))
            ->get();
    }

    /**
     * Get operators in this department
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function operators()
    {
        return $this->users()
            ->whereHas('roles', fn($q) => $q->whereIn('name', ['mpfm_operator', 'operator']))
            ->get();
    }
}
