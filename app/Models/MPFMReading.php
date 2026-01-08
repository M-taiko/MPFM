<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MPFMReading extends Model
{
    protected $table = 'mpfm_readings';
    protected $fillable = [
        'step_id',
        'oil_rate',
        'gas_rate',
        'water_rate',
        'wlr',
        'bsw',
        'pressure',
        'temperature',
        'reading_time',
        'notes',
        'additional_data',
    ];

    protected $casts = [
        'oil_rate' => 'decimal:2',
        'gas_rate' => 'decimal:2',
        'water_rate' => 'decimal:2',
        'wlr' => 'decimal:2',
        'bsw' => 'decimal:2',
        'pressure' => 'decimal:2',
        'temperature' => 'decimal:2',
        'reading_time' => 'datetime',
        'additional_data' => 'array',
    ];

    public function step(): BelongsTo
    {
        return $this->belongsTo(MPFMStep::class);
    }
}