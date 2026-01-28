<?php

namespace App\Domains\MPFM\Models;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TestTemplate extends Model
{
    use SoftDeletes;

    protected $table = 'test_templates';

    protected $fillable = [
        'department_id',
        'name',
        'description',
        'template_type',
        'required_fields',
        'optional_fields',
        'validation_rules',
        'calculations',
        'version',
        'is_active',
        'is_default',
        'created_by_user_id',
        'updated_by_user_id',
    ];

    protected $casts = [
        'required_fields' => 'json',
        'optional_fields' => 'json',
        'validation_rules' => 'json',
        'calculations' => 'json',
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

    public function versions(): HasMany
    {
        return $this->hasMany(TestTemplateVersion::class);
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

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('template_type', $type);
    }

    /**
     * Methods
     */
    public function getRequiredFields(): array
    {
        return $this->required_fields ?? [];
    }

    public function getOptionalFields(): array
    {
        return $this->optional_fields ?? [];
    }

    public function getAllFields(): array
    {
        return array_merge($this->getRequiredFields(), $this->getOptionalFields());
    }

    public function hasField(string $fieldName): bool
    {
        return in_array($fieldName, $this->getAllFields());
    }

    public function getValidationRules(): array
    {
        return $this->validation_rules ?? [];
    }

    public function getCalculations(): array
    {
        return $this->calculations ?? [];
    }

    public function createNewVersion(array $data, string $notes = null): TestTemplateVersion
    {
        $newVersion = $this->version + 1;

        return $this->versions()->create([
            'version_number' => $newVersion,
            'template_data' => $data,
            'change_notes' => $notes,
            'changed_by_user_id' => auth()->id(),
        ]);
    }

    public function setAsDefault()
    {
        // Set all other templates for this department as non-default
        self::where('department_id', $this->department_id)
            ->where('template_type', $this->template_type)
            ->update(['is_default' => false]);

        // Set this template as default
        $this->update(['is_default' => true]);
    }

    public function duplicate(string $newName = null): TestTemplate
    {
        return self::create([
            'department_id' => $this->department_id,
            'name' => $newName ?? $this->name . ' (Copy)',
            'description' => $this->description,
            'template_type' => $this->template_type,
            'required_fields' => $this->required_fields,
            'optional_fields' => $this->optional_fields,
            'validation_rules' => $this->validation_rules,
            'calculations' => $this->calculations,
            'version' => 1,
            'is_active' => true,
            'is_default' => false,
            'created_by_user_id' => auth()->id(),
        ]);
    }
}
