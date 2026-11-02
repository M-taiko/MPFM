<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WirelineGaugeReading extends Model
{
    public $timestamps = false;

    protected $fillable = ['wireline_gauge_run_id', 't_sec', 'pressure', 'temperature', 'internal_temp'];

    public function run(): BelongsTo
    {
        return $this->belongsTo(WirelineGaugeRun::class, 'wireline_gauge_run_id');
    }
}
