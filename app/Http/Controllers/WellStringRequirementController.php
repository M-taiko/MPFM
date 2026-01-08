<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WellStringRequirement;
use App\Models\WellTest;
use App\Models\User;

class WellStringRequirementController extends Controller
{
    public function index()
    {
        $requirements = WellStringRequirement::with(['wellTest', 'createdBy'])
            ->latest()
            ->paginate(10);
        
        return view('well_string_requirements.index', compact('requirements'));
    }

    public function create($wellTestId)
    {
        $wellTest = WellTest::findOrFail($wellTestId);
        return view('well_string_requirements.create', compact('wellTest'));
    }

    public function store(Request $request, $wellTestId)
    {
        $request->validate([
            'well_string' => 'required|string|max:255',
            'reservoir_engineer_requirements' => 'nullable|string',
        ]);

        $requirement = WellStringRequirement::create([
            'well_test_id' => $wellTestId,
            'well_string' => $request->well_string,
            'reservoir_engineer_requirements' => $request->reservoir_engineer_requirements,
            'status' => 'pending_job_officer', // After reservoir engineer, goes to job officer
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('well_tests.show', $wellTestId)
            ->with('success', 'Well string requirement created successfully!');
    }

    public function show($id)
    {
        $requirement = WellStringRequirement::with(['wellTest', 'createdBy', 'updatedBy'])->findOrFail($id);
        
        // Check if user has permission to view based on their role and the requirement status
        $canView = $this->userCanView($requirement);
        
        if (!$canView) {
            abort(403, 'You do not have permission to view this requirement.');
        }
        
        return view('well_string_requirements.show', compact('requirement'));
    }

    public function edit($id)
    {
        $requirement = WellStringRequirement::findOrFail($id);
        
        // Check if user has permission to edit based on their role
        $canEdit = $this->userCanEdit($requirement);
        
        if (!$canEdit) {
            abort(403, 'You do not have permission to edit this requirement.');
        }
        
        return view('well_string_requirements.edit', compact('requirement'));
    }

    public function update(Request $request, $id)
    {
        $requirement = WellStringRequirement::findOrFail($id);
        
        // Validate based on user role and current status
        $userRole = $this->getUserRole();
        $rules = [];
        
        if ($userRole === 'job_officer') {
            $rules = [
                'job_officer_notes' => 'nullable|string',
            ];
            $status = 'pending_production_supervisor';
        } elseif ($userRole === 'production_supervisor') {
            $rules = [
                'production_supervisor_comments' => 'nullable|string',
            ];
            $status = 'pending_mpfm_supervisor';
        } elseif ($userRole === 'mpfm_supervisor') {
            $rules = [
                'mpfm_supervisor_allocation' => 'nullable|string',
            ];
            $status = 'completed';
        } else {
            abort(403, 'You do not have permission to update this requirement.');
        }
        
        $request->validate($rules);
        
        $updates = $request->only(array_keys($rules));
        $updates['status'] = $status;
        $updates['updated_by'] = auth()->id();
        
        // Handle operator assignments if user is MPFM supervisor
        if ($userRole === 'mpfm_supervisor' && $request->has('operator_assignments')) {
            $operatorAssignments = [];
            if ($request->has('operator_assignments') && is_array($request->operator_assignments)) {
                foreach ($request->operator_assignments as $index => $operator) {
                    if (!empty($operator) && isset($request->well_string_assignments[$index])) {
                        $wellString = $request->well_string_assignments[$index];
                        if (!empty($wellString)) {
                            $operatorAssignments[] = "$operator - $wellString";
                        }
                    }
                }
            }
            $updates['operator_assignments'] = $operatorAssignments;
        }
        
        $requirement->update($updates);

        return redirect()->route('well_string_requirements.show', $id)
            ->with('success', 'Requirement updated successfully!');
    }

    private function getUserRole()
    {
        $user = auth()->user();
        if ($user->hasRole('job_officer')) {
            return 'job_officer';
        } elseif ($user->hasRole('production_supervisor')) {
            return 'production_supervisor';
        } elseif ($user->hasRole('mpfm_supervisor')) {
            return 'mpfm_supervisor';
        } elseif ($user->hasRole('reservoir_engineer')) {
            return 'reservoir_engineer';
        }
        return null;
    }

    private function userCanView($requirement)
    {
        $user = auth()->user();
        
        // Reservoir engineer can view requirements they created or that are pending them
        if ($user->hasRole('reservoir_engineer')) {
            return $requirement->created_by === $user->id || $requirement->status === 'pending_reservoir';
        }
        
        // Job officer can view requirements pending job officer
        if ($user->hasRole('job_officer') && $requirement->status === 'pending_job_officer') {
            return true;
        }
        
        // Production supervisor can view requirements pending production supervisor
        if ($user->hasRole('production_supervisor') && $requirement->status === 'pending_production_supervisor') {
            return true;
        }
        
        // MPFM supervisor can view requirements pending MPFM supervisor
        if ($user->hasRole('mpfm_supervisor') && $requirement->status === 'pending_mpfm_supervisor') {
            return true;
        }
        
        // Admin can view all
        if ($user->hasRole('admin')) {
            return true;
        }
        
        return false;
    }

    private function userCanEdit($requirement)
    {
        $user = auth()->user();
        
        // Job officer can edit if status is pending job officer
        if ($user->hasRole('job_officer') && $requirement->status === 'pending_job_officer') {
            return true;
        }
        
        // Production supervisor can edit if status is pending production supervisor
        if ($user->hasRole('production_supervisor') && $requirement->status === 'pending_production_supervisor') {
            return true;
        }
        
        // MPFM supervisor can edit if status is pending MPFM supervisor
        if ($user->hasRole('mpfm_supervisor') && $requirement->status === 'pending_mpfm_supervisor') {
            return true;
        }
        
        // Admin can edit all
        if ($user->hasRole('admin')) {
            return true;
        }
        
        return false;
    }
}