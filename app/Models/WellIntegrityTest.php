<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WellIntegrityTest extends Model
{
    public const TYPES = ['function' => 'Function test', 'pressure' => 'Pressure test', 'leak_rate' => 'Leak-rate test', 'inflow' => 'Inflow test'];

    public const RESULTS = ['pass' => 'Pass', 'marginal' => 'Marginal', 'fail' => 'Fail'];

    protected $fillable = [
        'well_barrier_element_id', 'oil_string_id', 'test_date', 'test_type', 'result', 'test_pressure',
        'leak_rate', 'duration_min', 'tested_by', 'remarks', 'created_by',
    ];

    protected $casts = ['test_date' => 'date', 'test_pressure' => 'float', 'leak_rate' => 'float'];

    public function element(): BelongsTo
    {
        return $this->belongsTo(WellBarrierElement::class, 'well_barrier_element_id');
    }

    public function oilString(): BelongsTo
    {
        return $this->belongsTo(OilString::class);
    }
}
