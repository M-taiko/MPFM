<?php

namespace App\Domains\MPFM\Models;

use App\Models\Department;
use App\Models\User;
use App\Models\WellTest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MPFMTest extends Model
{
    use SoftDeletes;

    protected $table = 'mpfm_tests';

    protected $fillable = [
        'well_test_id',
        'department_id',
        'test_date',
        'test_duration_minutes',
        'workflow_config',
        'is_active',
        'processing_status',
        'processing_errors',
    ];

    protected $casts = [
        'test_date' => 'datetime',
        'workflow_config' => 'json',
        'processing_errors' => 'json',
    ];

    /**
     * Relationships
     */
    public function wellTest(): BelongsTo
    {
        return $this->belongsTo(WellTest::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function testResults(): HasMany
    {
        return $this->hasMany(TestResult::class);
    }

    public function testLogs(): HasMany
    {
        return $this->hasMany(TestLog::class, 'mpfm_test_id');
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

    public function scopeByProcessingStatus($query, $status)
    {
        return $query->where('processing_status', $status);
    }

    public function scopeRecentFirst($query)
    {
        return $query->orderByDesc('test_date');
    }

    /**
     * Methods
     */
    public function hasResults(): bool
    {
        return $this->testResults()->exists();
    }

    public function getResultByParameter(string $parameterName): ?TestResult
    {
        return $this->testResults()
            ->where('parameter_name', $parameterName)
            ->first();
    }

    public function getAllResults()
    {
        return $this->testResults()->get();
    }

    public function isProcessed(): bool
    {
        return in_array($this->processing_status, ['completed', 'failed']);
    }

    public function markAsProcessing()
    {
        $this->update(['processing_status' => 'processing']);
    }

    public function markAsCompleted()
    {
        $this->update(['processing_status' => 'completed']);
    }

    public function markAsFailed(array $errors = [])
    {
        $this->update([
            'processing_status' => 'failed',
            'processing_errors' => $errors,
        ]);
    }

    public function getDepartmentWorkflow(): ?array
    {
        return $this->workflow_config;
    }
}
