<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TVTestRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tv_test_requests';

    protected $fillable = [
        'test_vessel_id',
        'tower_id',
        'oil_field_id',
        'requested_by_user_id',
        'test_date',
        'shift',
        'notes',
        'status',
    ];

    protected $casts = [
        'test_date' => 'date',
    ];

    /**
     * Relationships
     */
    public function testVessel()
    {
        return $this->belongsTo(TestVessel::class);
    }

    public function tower()
    {
        return $this->belongsTo(Tower::class);
    }

    public function oilField()
    {
        return $this->belongsTo(OilField::class);
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }

    public function wells()
    {
        return $this->hasMany(TVTestRequestWell::class, 'tv_test_request_id');
    }

    /**
     * Scopes
     */
    public function scopeForOilField($query, $oilFieldId)
    {
        return $query->where('oil_field_id', $oilFieldId);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeVisibleToUser($query, User $user)
    {
        if ($user->hasRole('admin')) {
            return $query;
        }

        if ($user->hasRole(['job_officer', 'surveillance_supervisor'])) {
            // These roles can see all requests
            return $query;
        }

        // Operators see only requests for their oil field
        if ($user->hasRole(['mpfm_operator', 'operator'])) {
            // Assuming user has a department with oil_field_id
            // This requires user/department relationship
            return $query;
        }

        return $query->whereRaw('0 = 1'); // No access
    }
}
