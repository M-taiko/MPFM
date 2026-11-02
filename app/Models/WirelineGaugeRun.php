<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WirelineGaugeRun extends Model
{
    public const SURVEY_TYPES = [
        'bhcip'            => 'BHCIP - Bottom Hole Closed-In Pressure',
        'bhfp'             => 'BHFP - Bottom Hole Flowing Pressure',
        'static_gradient'  => 'Static Gradient Survey',
        'flowing_gradient' => 'Flowing Gradient Survey',
        'pbu'              => 'Pressure Build-Up (PBU)',
        'drawdown'         => 'Drawdown',
    ];

    protected $fillable = [
        'wireline_operation_id', 'survey_type', 'gauge_serial', 'gauge_model', 'gauge_manufacturer',
        'calibration_date', 'max_range', 'pressure_unit', 'temperature_unit', 'sample_interval_sec',
        'started_at', 'ended_at', 'sample_count', 'source_file', 'file_path', 'analysis',
    ];

    protected $casts = [
        'calibration_date' => 'date',
        'started_at'       => 'datetime',
        'ended_at'         => 'datetime',
        'analysis'         => 'array',
        'max_range'        => 'float',
    ];

    public function operation(): BelongsTo
    {
        return $this->belongsTo(WirelineOperation::class, 'wireline_operation_id');
    }

    public function readings(): HasMany
    {
        return $this->hasMany(WirelineGaugeReading::class)->orderBy('t_sec');
    }

    public function surveyLabel(): string
    {
        return self::SURVEY_TYPES[$this->survey_type] ?? strtoupper($this->survey_type);
    }

    /** Final bottom-hole pressure (psi) from the stored analysis. */
    public function getBhpAttribute(): ?float
    {
        return $this->analysis['stats']['bhp'] ?? null;
    }

    public function getBhtAttribute(): ?float
    {
        return $this->analysis['stats']['bht'] ?? null;
    }
}
