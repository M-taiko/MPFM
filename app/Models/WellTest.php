<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Yajra\DataTables\EloquentDataTable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasMany as EloquentHasMany;

class WellTest extends Model
{
    protected $fillable = [
        'client', 'field', 'well', 'operator', 'test_date', 'test_type', 'wellhead_tower', 'wellhead_string',
        'choke_size', 'upstream_choke', 'downstream_choke',
        'start_time', 'end_time', 'metadata', 'test_data',
        'calculated_metrics', 'filename','log_file_path'
    ];

    protected $casts = [
        'metadata' => 'array',
        'test_data' => 'array',
        'calculated_metrics' => 'array',
        'log_file_path' => 'string',
        'test_type' => 'string',
        'operator' => 'string',
        'test_date' => 'date', // أضفت هذا أيضاً
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];


    public static function dataTable($query)
    {
        return datatables($query);
    }


    public function getShowUrlAttribute()
    {
        return route('well_tests.show', $this->id);
    }


    public function getCompareUrlAttribute()
    {
        return route('well_tests.compare-results', ['ids' => $this->id]);
    }

    /**
     * Get the MPFM plans for this well test
     */
    public function mpfmPlans(): HasMany
    {
        return $this->hasMany(\App\Models\MPFMPlan::class, 'well_test_id');
    }

    /**
     * Get the well string requirements for this well test
     */
    public function wellStringRequirements(): HasMany
    {
        return $this->hasMany(\App\Models\WellStringRequirement::class, 'well_test_id');
    }

    /**
     * Get the test data rows for this well test
     * Note: test_data is stored as JSON in the well_tests table
     */
    // Removed - test_data is a JSON column, not a relationship

    /**
     * Get the operator plans for this well test
     */
    public function operatorPlans(): EloquentHasMany
    {
        return $this->hasMany(\App\Models\OperatorPlan::class, 'well_test_id');
    }
}
