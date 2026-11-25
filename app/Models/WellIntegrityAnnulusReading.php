<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WellIntegrityAnnulusReading extends Model
{
    public const FLUIDS = ['gas' => 'Gas', 'oil' => 'Oil', 'water' => 'Water', 'mixed' => 'Mixed', 'unknown' => 'Unknown'];

    protected $fillable = [
        'well_integrity_annulus_id', 'oil_string_id', 'reading_date', 'pressure', 'bleed_down_pressure',
        'rebuild_pressure', 'rebuild_hours', 'fluid', 'remarks', 'created_by',
    ];

    protected $casts = [
        'reading_date' => 'date', 'pressure' => 'float', 'bleed_down_pressure' => 'float',
        'rebuild_pressure' => 'float', 'rebuild_hours' => 'float',
    ];

    public function annulus(): BelongsTo
    {
        return $this->belongsTo(WellIntegrityAnnulus::class, 'well_integrity_annulus_id');
    }

    /** Pressure came back after a bleed-down = sustained casing pressure */
    public function rebuilt(): bool
    {
        return $this->rebuild_pressure !== null && $this->rebuild_pressure > ($this->bleed_down_pressure ?? 0) + 20;
    }
}
