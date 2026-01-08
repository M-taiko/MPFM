<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MPFMTest extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mpfm_tests';

    protected $fillable = [
        'user_id',
        'oil_field_id',
        'tower_id',
        'oil_string_id',
        'mpfm_operational_plan_id',
        'plan_task_id',
        'well_test_id',
        'test_date',
        'shift',
        'oil_rate',
        'gas_rate',
        'water_cut',
        'pressure',
        'temperature',
        'gor',
        'calculated_kpis',
        'notes',
        'status',
        'log_file_path',
        'raw_log_data',
        'test_duration_minutes',
    ];

    protected $casts = [
        'test_date' => 'date',
        'shift' => 'string',
        'oil_rate' => 'decimal:2',
        'gas_rate' => 'decimal:2',
        'water_cut' => 'decimal:2',
        'pressure' => 'decimal:2',
        'temperature' => 'decimal:2',
        'calculated_kpis' => 'array',
        'status' => 'string',
        'raw_log_data' => 'array',
    ];

    /**
     * Get the user who created this test (operator)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the oil field
     */
    public function oilField(): BelongsTo
    {
        return $this->belongsTo(OilField::class);
    }

    /**
     * Get the tower
     */
    public function tower(): BelongsTo
    {
        return $this->belongsTo(Tower::class);
    }

    /**
     * Get the oil string
     */
    public function oilString(): BelongsTo
    {
        return $this->belongsTo(OilString::class);
    }

    /**
     * Get the operational plan (if linked)
     */
    public function mpfmOperationalPlan(): BelongsTo
    {
        return $this->belongsTo(MPFMOperationalPlan::class);
    }

    /**
     * Get the plan task (if linked)
     */
    public function planTask(): BelongsTo
    {
        return $this->belongsTo(PlanTask::class);
    }

    /**
     * Get the well test (if linked)
     */
    public function wellTest(): BelongsTo
    {
        return $this->belongsTo(WellTest::class);
    }

    /**
     * Scope to filter by user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter by oil field
     */
    public function scopeForOilField($query, $oilFieldId)
    {
        return $query->where('oil_field_id', $oilFieldId);
    }

    /**
     * Scope to filter by tower
     */
    public function scopeForTower($query, $towerId)
    {
        return $query->where('tower_id', $towerId);
    }

    /**
     * Scope to filter by oil string
     */
    public function scopeForOilString($query, $oilStringId)
    {
        return $query->where('oil_string_id', $oilStringId);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('test_date', [$startDate, $endDate]);
    }

    /**
     * Scope to filter by shift
     */
    public function scopeForShift($query, $shift)
    {
        return $query->where('shift', $shift);
    }

    /**
     * Scope to filter accessible tests for operator
     * Operators can only see tests for their assigned assets
     */
    public function scopeAccessibleByOperator($query, User $user)
    {
        // Get all operational plans assigned to this user
        $planIds = MPFMOperationalPlan::where('assigned_user_id', $user->id)
            ->pluck('id')
            ->toArray();

        return $query->where(function ($q) use ($user, $planIds) {
            // Tests created by the user
            $q->where('user_id', $user->id)
                // Or tests linked to plans assigned to the user
                ->orWhereIn('mpfm_operational_plan_id', $planIds);
        });
    }
}

