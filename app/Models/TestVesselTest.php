<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TestVesselTest extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'test_vessel_tests';

    protected $fillable = [
        'test_vessel_id',
        'user_id',
        'oil_field_id',
        'tower_id',
        'oil_string_id',
        'mpfm_test_id',
        'test_date',
        'shift',
        'oil_rate',
        'gas_rate',
        'water_cut',
        'pressure',
        'temperature',
        'gor',
        'notes',
        'status',
        'tv_test_request_well_id',
        'reviewed_by_user_id',
        'review_notes',
        'reviewed_at',
    ];

    protected $casts = [
        'test_date' => 'date',
        'shift' => 'string',
        'oil_rate' => 'decimal:2',
        'gas_rate' => 'decimal:2',
        'water_cut' => 'decimal:2',
        'pressure' => 'decimal:2',
        'temperature' => 'decimal:2',
        'gor' => 'decimal:2',
        'status' => 'string',
    ];

    /**
     * Get the test vessel that owns this test
     */
    public function testVessel(): BelongsTo
    {
        return $this->belongsTo(TestVessel::class, 'test_vessel_id');
    }

    /**
     * Get the user who created this test
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the oil field
     */
    public function oilField(): BelongsTo
    {
        return $this->belongsTo(OilField::class);
    }

    /**
     * Get the tower
     */
    public function tower(): BelongsTo
    {
        return $this->belongsTo(Tower::class);
    }

    /**
     * Get the oil string
     */
    public function oilString(): BelongsTo
    {
        return $this->belongsTo(OilString::class, 'oil_string_id');
    }

    /**
     * Get the related MPFM test (if linked)
     */
    public function mpfmTest(): BelongsTo
    {
        return $this->belongsTo(MPFMTest::class, 'mpfm_test_id');
    }

    /**
     * Get the test request well this test is linked to
     */
    public function requestWell()
    {
        return $this->belongsTo(TVTestRequestWell::class, 'tv_test_request_well_id');
    }

    /**
     * Get the user who reviewed this test
     */
    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }

    /**
     * Scope to filter active test vessel tests
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope to filter by test vessel
     */
    public function scopeForVessel($query, $vesselId)
    {
        return $query->where('test_vessel_id', $vesselId);
    }

    /**
     * Scope to filter by oil field
     */
    public function scopeForOilField($query, $oilFieldId)
    {
        return $query->where('oil_field_id', $oilFieldId);
    }
}
