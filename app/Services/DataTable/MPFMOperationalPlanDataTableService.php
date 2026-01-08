<?php

namespace App\Services\DataTable;

use App\Models\MPFMOperationalPlan;
use App\Models\User;
use App\Services\MPFMOperationalPlanService;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;

class MPFMOperationalPlanDataTableService
{
    protected $planService;

    public function __construct(MPFMOperationalPlanService $planService)
    {
        $this->planService = $planService;
    }

    /**
     * Get DataTable query for MPFM Operational Plans
     */
    public function getDataTable(Request $request, User $user)
    {
        $query = $this->planService->getAccessiblePlans($user)
            ->with(['creator', 'assignedUser', 'oilField', 'tower']);

        // Apply filters
        if ($request->has('oil_field_id') && $request->oil_field_id) {
            $query->where('oil_field_id', $request->oil_field_id);
        }

        if ($request->has('tower_id') && $request->tower_id) {
            $query->where('tower_id', $request->tower_id);
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('plan_date') && $request->plan_date) {
            $query->whereDate('plan_date', $request->plan_date);
        }

        return DataTables::of($query)
            ->addColumn('creator_name', function ($plan) {
                return $plan->creator->name ?? '-';
            })
            ->addColumn('assigned_user_name', function ($plan) {
                return $plan->assignedUser->name ?? '-';
            })
            ->addColumn('oil_field_name', function ($plan) {
                return $plan->oilField->name ?? '-';
            })
            ->addColumn('tower_name', function ($plan) {
                return $plan->tower->name ?? '-';
            })
            ->addColumn('assigned_strings_count', function ($plan) {
                return count($plan->assigned_oil_string_ids ?? []);
            })
            ->addColumn('status_badge', function ($plan) {
                $badgeClass = match($plan->status) {
                    'active' => 'badge-success',
                    'draft' => 'badge-secondary',
                    'completed' => 'badge-info',
                    'cancelled' => 'badge-danger',
                    default => 'badge-secondary',
                };
                return '<span class="badge ' . $badgeClass . '">' . ucfirst($plan->status) . '</span>';
            })
            ->addColumn('action', function ($plan) {
                $actions = '<a href="' . route('mpfm_operational_plans.show', $plan->id) . '" class="btn btn-sm btn-info">View</a> ';
                if (auth()->user()->can('update', $plan)) {
                    $actions .= '<a href="' . route('mpfm_operational_plans.edit', $plan->id) . '" class="btn btn-sm btn-primary">Edit</a> ';
                }
                if (auth()->user()->can('delete', $plan)) {
                    $actions .= '<form action="' . route('mpfm_operational_plans.destroy', $plan->id) . '" method="POST" style="display:inline;">
                        ' . csrf_field() . '
                        ' . method_field('DELETE') . '
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure?\')">Delete</button>
                    </form>';
                }
                return $actions;
            })
            ->editColumn('plan_date', function ($plan) {
                return $plan->plan_date->format('Y-m-d');
            })
            ->editColumn('created_at', function ($plan) {
                return $plan->created_at->format('Y-m-d H:i');
            })
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }
}

