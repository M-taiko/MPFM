<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MPFMPlan extends Model
{
    protected $table = 'mpfm_plans';
    protected $fillable = [
        'well_test_id',
        'name',
        'description',
        'planned_oil_rate',
        'planned_gas_rate',
        'planned_water_rate',
        'expected_gor',
        'expected_bsw',
        'expected_choke',
        'expected_duration',
        'test_objective',
        'settings',
    ];

    protected $casts = [
        'planned_oil_rate' => 'decimal:2',
        'planned_gas_rate' => 'decimal:2',
        'planned_water_rate' => 'decimal:2',
        'expected_gor' => 'decimal:2',
        'expected_bsw' => 'decimal:2',
        'expected_duration' => 'integer',
        'settings' => 'array',
    ];

    public function wellTest(): BelongsTo
    {
        return $this->belongsTo(WellTest::class);
    }

    public function steps(): HasMany
    {
        return $this->hasMany(MPFMStep::class);
    }
}