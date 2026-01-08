<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WellStringRequirement extends Model
{
    protected $table = 'well_string_requirements';
    
    protected $fillable = [
        'well_test_id',
        'well_string',
        'reservoir_engineer_requirements',
        'job_officer_notes',
        'production_supervisor_comments',
        'mpfm_supervisor_allocation',
        'operator_assignments',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'operator_assignments' => 'array',
        'status' => 'string',
    ];

    public function wellTest(): BelongsTo
    {
        return $this->belongsTo(WellTest::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    
    public function canUpdate($user)
    {
        // Check if user has permission based on their role and the requirement status
        if ($user->hasRole('admin')) {
            return true;
        }
        
        if ($user->hasRole('job_officer') && $this->status === 'pending_job_officer') {
            return true;
        }
        
        if ($user->hasRole('production_supervisor') && $this->status === 'pending_production_supervisor') {
            return true;
        }
        
        if ($user->hasRole('mpfm_supervisor') && $this->status === 'pending_mpfm_supervisor') {
            return true;
        }
        
        return false;
    }
}