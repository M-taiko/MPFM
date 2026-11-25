<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WellIntegrityProfile extends Model
{
    public const WELL_TYPES = [
        'producer' => 'Oil producer', 'gas_producer' => 'Gas producer', 'injector' => 'Water injector',
        'gas_injector' => 'Gas injector', 'observation' => 'Observation',
    ];

    public const LIFT_METHODS = [
        'natural_flow' => 'Natural flow', 'esp' => 'ESP', 'gas_lift' => 'Gas lift', 'rod_pump' => 'Rod pump', 'jet_pump' => 'Jet pump',
    ];

    protected $fillable = [
        'oil_string_id', 'well_type', 'lift_method', 'tubing_size', 'casing_size', 'mawhp', 'max_oil_rate',
        'restriction_note', 'review_interval_days', 'last_review_date', 'reviewed_by', 'notes',
    ];

    protected $casts = [
        'mawhp' => 'float', 'max_oil_rate' => 'float', 'last_review_date' => 'date', 'review_interval_days' => 'integer',
    ];

    public function oilString(): BelongsTo
    {
        return $this->belongsTo(OilString::class);
    }

    public function nextReviewDate()
    {
        return $this->last_review_date?->copy()->addDays($this->review_interval_days);
    }
}
