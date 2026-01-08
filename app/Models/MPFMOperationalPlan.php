<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MPFMOperationalPlan extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mpfm_operational_plans';

    protected $fillable = [
        'created_by',
        'assigned_user_id',
        'oil_field_id',
        'tower_id',
        'name',
        'description',
        'plan_date',
        'shift',
        'status',
        'assigned_oil_string_ids',
        'notes',
    ];

    protected $casts = [
        'plan_date' => 'date',
        'shift' => 'string',
        'status' => 'string',
        'assigned_oil_string_ids' => 'array',
    ];

    /**
     * Get the user who created this plan (supervisor)
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the assigned user (operator)
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
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
     * Get assigned oil strings
     */
    public function assignedOilStrings()
    {
        if (!$this->assigned_oil_string_ids) {
            return collect();
        }
        
        return OilString::whereIn('id', $this->assigned_oil_string_ids)->get();
    }

    /**
     * Get all MPFM tests for this operational plan
     */
    public function mpfmTests(): HasMany
    {
        return $this->hasMany(MPFMTest::class);
    }

    /**
     * Scope to filter by assigned user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('assigned_user_id', $userId);
    }

    /**
     * Scope to filter by creator
     */
    public function scopeCreatedBy($query, $userId)
    {
        return $query->where('created_by', $userId);
    }

    /**
     * Scope to filter active plans
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Check if user has access to this plan
     */
    public function canBeAccessedBy(User $user): bool
    {
        // Supervisors can access plans they created
        if ($this->created_by === $user->id) {
            return true;
        }

        // Operators can only access plans assigned to them
        if ($this->assigned_user_id === $user->id) {
            return true;
        }

        // Admins can access all
        return $user->hasRole('admin');
    }
}

