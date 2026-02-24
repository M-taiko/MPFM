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
        // Timing fields
        'test_started_at',
        'test_completed_at',
        // Detailed metrics fields
        'qo_lc', 'qw_lc', 'qg_lc', 'ql_lc',
        'qo_sc', 'qw_sc', 'qg_sc',
        'fo_lc', 'fw_lc', 'fg_lc',
        'bsw', 'wlr', 'glr', 'gvf',
        'po_lc', 'pw_lc', 'pg_lc', 'mo_lc', 'mw_lc', 'mg_lc',
        'cvo_lc', 'cvw_lc', 'cvg_lc', 'cvo_sc', 'cvw_sc', 'cvg_sc',
        'bo', 'bw', 'bg', 'z_factor', 'rst',
        'dp', 'pl', 'tl',
        'pressure_avg', 'pressure_max', 'pressure_min',
        'temperature_avg', 'temperature_max', 'temperature_min',
        'qo_flow_weighted', 'qw_flow_weighted', 'qg_flow_weighted',
        'bsw_sc', 'wlr_sc', 'gor_sc',
        'h2s_mfr', 'co2_mfr', 'n2_mfr',
        'choke_size', 'choke_dp',
        'test_duration_hours', 'test_stable_duration_hours', 'test_total_data_points',
        'gamma_reading', 'detector_temp',
        'data_quality_score', 'missing_data_percentage',
        // JSON fields
        'detailed_metrics', 'flow_rate_metrics', 'pvt_metrics',
        'pressure_temperature_metrics', 'ratio_metrics', 'cumulative_metrics',
        'data_quality_metrics',
        // Supervisor approval fields
        'department_supervisor_id',
        'surveillance_supervisor_id',
        'approved_by_department_at',
        'approved_by_surveillance_at',
        // Rejection fields
        'rejected_by_user_id',
        'rejected_by_role',
        'rejected_reason',
        'rejected_details',
        'rejected_at',
        // Re-test request fields
        'retest_requested_by_user_id',
        'retest_reason',
        'retest_details',
        'retest_requested_at',
        'supervisor_remarks',
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
        // Timing fields
        'test_started_at' => 'datetime',
        'test_completed_at' => 'datetime',
        // Detailed metrics JSON fields
        'detailed_metrics' => 'array',
        'flow_rate_metrics' => 'array',
        'pvt_metrics' => 'array',
        'pressure_temperature_metrics' => 'array',
        'ratio_metrics' => 'array',
        'cumulative_metrics' => 'array',
        'data_quality_metrics' => 'array',
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

    /**
     * Get all validation results for this test
     */
    public function validations()
    {
        return $this->hasMany(MPFMTestValidation::class, 'mpfm_test_id', 'id')->latest('validated_at');
    }

    /**
     * Get the latest validation result
     */
    public function latestValidation()
    {
        return $this->hasOne(MPFMTestValidation::class, 'mpfm_test_id', 'id')->latestOfMany('validated_at');
    }

    /**
     * Get all comparisons for this test
     */
    public function comparisons()
    {
        return $this->hasMany(MPFMTestComparison::class, 'current_test_id')->latest();
    }

    /**
     * Get the latest comparison
     */
    public function latestComparison()
    {
        return $this->hasOne(MPFMTestComparison::class, 'current_test_id')->latestOfMany();
    }

    /**
     * Scope to get tests that need validation
     */
    public function scopeNeedsValidation($query)
    {
        return $query->whereNull('last_validated_at')
                     ->orWhere('last_validated_at', '<', \DB::raw('updated_at'));
    }

    /**
     * Scope to filter approved tests
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Check if test needs revalidation
     */
    public function needsRevalidation()
    {
        return $this->last_validated_at === null ||
               $this->last_validated_at < $this->updated_at;
    }
}

