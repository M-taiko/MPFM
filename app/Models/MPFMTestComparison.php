<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MPFMTestComparison extends Model
{
    protected $table = 'mpfm_test_comparisons';

    protected $fillable = [
        'current_test_id', 'oil_string_id',
        'compared_test_ids', 'comparison_data', 'alerts'
    ];

    protected $casts = [
        'compared_test_ids' => 'array',
        'comparison_data' => 'array',
        'alerts' => 'array',
    ];

    public function currentTest()
    {
        return $this->belongsTo(MPFMTest::class, 'current_test_id');
    }

    public function oilString()
    {
        return $this->belongsTo(OilString::class);
    }
}
