<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TVTestRequestWell extends Model
{
    use HasFactory;

    protected $table = 'tv_test_request_wells';

    protected $fillable = [
        'tv_test_request_id',
        'oil_string_id',
        'test_vessel_test_id',
        'started_by_user_id',
        'status',
        'started_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function request()
    {
        return $this->belongsTo(TVTestRequest::class, 'tv_test_request_id');
    }

    public function oilString()
    {
        return $this->belongsTo(OilString::class);
    }

    public function testVesselTest()
    {
        return $this->belongsTo(TestVesselTest::class, 'test_vessel_test_id');
    }

    public function startedBy()
    {
        return $this->belongsTo(User::class, 'started_by_user_id');
    }
}
