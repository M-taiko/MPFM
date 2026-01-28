<?php

namespace App\Domains\MPFM\Models;

use App\Models\Department;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DepartmentSLARule extends Model
{
    protected $table = 'department_sla_rules';

    public $timestamps = true;

    protected $fillable = [
        'department_id',
        'state',
        'warning_minutes',
        'critical_minutes',
        'escalation_level',
        'is_active',
    ];

    /**
     * Relationships
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    public function scopeForState($query, string $state)
    {
        return $query->where('state', $state);
    }

    /**
     * Methods
     */
    public function getWarningMinutes(): int
    {
        return $this->warning_minutes;
    }

    public function getCriticalMinutes(): int
    {
        return $this->critical_minutes;
    }

    public function getEscalationLevel(): int
    {
        return $this->escalation_level;
    }

    public function shouldWarn(\DateTime $createdAt): bool
    {
        $warningTime = $createdAt->addMinutes($this->warning_minutes);
        return now()->isAfter($warningTime);
    }

    public function isCritical(\DateTime $createdAt): bool
    {
        $criticalTime = $createdAt->addMinutes($this->critical_minutes);
        return now()->isAfter($criticalTime);
    }

    public function getEscalationLabel(): string
    {
        return match($this->escalation_level) {
            1 => 'Manager',
            2 => 'Supervisor',
            3 => 'Admin',
            default => 'Unknown',
        };
    }
}
