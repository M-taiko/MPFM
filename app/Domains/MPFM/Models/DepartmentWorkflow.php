<?php

namespace App\Domains\MPFM\Models;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DepartmentWorkflow extends Model
{
    use SoftDeletes;

    protected $table = 'department_workflows';

    protected $fillable = [
        'department_id',
        'states',
        'transitions',
        'sla_rules',
        'notifications',
        'custom_fields',
        'is_active',
        'created_by_user_id',
        'updated_by_user_id',
    ];

    protected $casts = [
        'states' => 'json',
        'transitions' => 'json',
        'sla_rules' => 'json',
        'notifications' => 'json',
        'custom_fields' => 'json',
    ];

    /**
     * Relationships
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_user_id');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Methods
     */
    public function getStates(): array
    {
        return $this->states ?? [];
    }

    public function getTransitions(): array
    {
        return $this->transitions ?? [];
    }

    public function getSLARules(): array
    {
        return $this->sla_rules ?? [];
    }

    public function getNotifications(): array
    {
        return $this->notifications ?? [];
    }

    public function getCustomFields(): array
    {
        return $this->custom_fields ?? [];
    }

    public function canTransition(string $fromState, string $toState): bool
    {
        $transitions = $this->getTransitions();

        if (!isset($transitions[$fromState])) {
            return false;
        }

        return in_array($toState, (array) $transitions[$fromState]);
    }

    public function getSLAForState(string $state): ?array
    {
        $rules = $this->getSLARules();

        if (!isset($rules[$state])) {
            return null;
        }

        return $rules[$state];
    }

    public function updateStates(array $states)
    {
        $this->update(['states' => $states]);
    }

    public function updateTransitions(array $transitions)
    {
        $this->update(['transitions' => $transitions]);
    }

    public function updateSLARules(array $rules)
    {
        $this->update(['sla_rules' => $rules]);
    }
}
