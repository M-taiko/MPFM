<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class OilField extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'location',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Get all towers for this oil field
     */
    public function towers(): HasMany
    {
        return $this->hasMany(Tower::class);
    }

    /**
     * Get all MPFM operational plans for this oil field
     */
    public function mpfmOperationalPlans(): HasMany
    {
        return $this->hasMany(MPFMOperationalPlan::class);
    }

    /**
     * Get all MPFM tests for this oil field
     */
    public function mpfmTests(): HasMany
    {
        return $this->hasMany(MPFMTest::class);
    }

    /**
     * Get all test vessels for this oil field
     */
    public function testVessels(): HasMany
    {
        return $this->hasMany(TestVessel::class);
    }

    /**
     * Scope to filter active oil fields
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}

