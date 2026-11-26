<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tower extends Model
{
    use HasFactory, SoftDeletes;

    /** key => [label, icon] */
    public const TYPES = [
        'wellhead_tower'  => ['Wellhead tower', 'fe-radio'],
        'platform'        => ['Production platform', 'fe-layers'],
        'desert_wellsite' => ['Desert wellsite', 'fe-sun'],
        'subsea'          => ['Subsea tie-back', 'fe-arrow-down-circle'],
        'flow_station'    => ['Flow / gathering station', 'fe-git-merge'],
        'other'           => ['Other', 'fe-map-pin'],
    ];

    protected $fillable = [
        'oil_field_id',
        'tower_type',
        'name',
        'code',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Get the oil field that owns this tower
     */
    public function oilField(): BelongsTo
    {
        return $this->belongsTo(OilField::class);
    }

    /**
     * Get all oil strings for this tower
     */
    public function oilStrings(): HasMany
    {
        return $this->hasMany(OilString::class);
    }

    /**
     * Get all MPFM operational plans for this tower
     */
    public function mpfmOperationalPlans(): HasMany
    {
        return $this->hasMany(MPFMOperationalPlan::class);
    }

    /**
     * Get all MPFM tests for this tower
     */
    public function mpfmTests(): HasMany
    {
        return $this->hasMany(MPFMTest::class);
    }

    /**
     * Scope to filter active towers
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to filter by oil field
     */
    public function scopeForOilField($query, $oilFieldId)
    {
        return $query->where('oil_field_id', $oilFieldId);
    }



    public function typeLabel(): string
    {
        return self::TYPES[$this->tower_type][0] ?? ucfirst(str_replace('_', ' ', (string) $this->tower_type));
    }

    public function typeIcon(): string
    {
        return self::TYPES[$this->tower_type][1] ?? 'fe-map-pin';
    }
}

