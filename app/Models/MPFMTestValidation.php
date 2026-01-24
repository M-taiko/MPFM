<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MPFMTestValidation extends Model
{
    protected $table = 'mpfm_test_validations';

    protected $fillable = [
        'mpfm_test_id', 'validated_at', 'overall_status',
        'confidence_score', 'auto_suggestion',
        'rule_results', 'historical_comparison', 'anomalies'
    ];

    protected $casts = [
        'validated_at' => 'datetime',
        'rule_results' => 'array',
        'historical_comparison' => 'array',
        'anomalies' => 'array',
    ];

    public function mpfmTest()
    {
        return $this->belongsTo(MPFMTest::class);
    }

    public function hasFailures()
    {
        return $this->overall_status === 'fail';
    }

    public function hasWarnings()
    {
        return $this->overall_status === 'warning';
    }

    public function isPassing()
    {
        return $this->overall_status === 'pass';
    }
}
