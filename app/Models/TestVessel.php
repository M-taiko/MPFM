<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TestVessel extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'oil_field_id',
        'location_id',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Get the oil field that owns this test vessel
     */
    public function oilField(): BelongsTo
    {
        return $this->belongsTo(OilField::class);
    }

    /**
     * Get all test vessel tests for this vessel
     */
    public function tests(): HasMany
    {
        return $this->hasMany(TestVesselTest::class, 'test_vessel_id');
    }

    /**
     * Scope to filter active test vessels
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

    public function location(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

}
