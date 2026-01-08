<?php

namespace App\Services;

use App\Models\MPFMOperationalPlan;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class MPFMOperationalPlanService
{
    /**
     * Create a new operational plan
     */
    public function create(array $data, User $creator): MPFMOperationalPlan
    {
        DB::beginTransaction();
        
        try {
            $data['created_by'] = $creator->id;
            $plan = MPFMOperationalPlan::create($data);

            DB::commit();
            return $plan->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update an operational plan
     */
    public function update(MPFMOperationalPlan $plan, array $data): MPFMOperationalPlan
    {
        DB::beginTransaction();
        
        try {
            $plan->update($data);

            DB::commit();
            return $plan->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get plans accessible by user
     */
    public function getAccessiblePlans(User $user)
    {
        $query = MPFMOperationalPlan::with(['creator', 'assignedUser', 'oilField', 'tower']);

        if ($user->hasRole('admin')) {
            // Admins see all
            return $query;
        }

        if ($user->hasRole('mpfm_supervisor')) {
            // Supervisors see plans they created
            return $query->createdBy($user->id);
        }

        if ($user->hasRole('mpfm_operator')) {
            // Operators see plans assigned to them
            return $query->forUser($user->id);
        }

        return $query->whereRaw('1 = 0'); // No access
    }
}

