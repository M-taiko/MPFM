<?php

namespace App\Domains\MPFM\Models;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DepartmentTestType extends Model
{
    use SoftDeletes;

    protected $table = 'department_test_types';

    protected $fillable = [
        'department_id',
        'name',
        'description',
        'required_fields',
        'validation_rules',
        'estimated_duration_minutes',
        'is_active',
        'created_by_user_id',
        'updated_by_user_id',
    ];

    protected $casts = [
        'required_fields' => 'json',
        'validation_rules' => 'json',
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

    public function scopeForDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Methods
     */
    public function getRequiredFields(): array
    {
        return $this->required_fields ?? [];
    }

    public function getValidationRules(): array
    {
        return $this->validation_rules ?? [];
    }

    public function validateData(array $data): array
    {
        $errors = [];
        $requiredFields = $this->getRequiredFields();
        $rules = $this->getValidationRules();

        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $errors[$field] = "Field '{$field}' is required";
            }
        }

        return $errors;
    }

    public function getEstimatedDuration(): int
    {
        return $this->estimated_duration_minutes;
    }
}
