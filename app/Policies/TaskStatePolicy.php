<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PlanTask;
use App\Enums\TaskState;

class TaskStatePolicy
{
    /**
     * Job Officer can submit tasks to surveillance.
     *
     * @param User $user
     * @param PlanTask $task
     * @return bool
     */
    public function submitToSurveillance(User $user, PlanTask $task): bool
    {
        return $user->hasRole('job_officer')
            && $task->isInState(TaskState::DRAFT)
            && $task->monthlyPlan
            && $task->monthlyPlan->created_by === $user->id;
    }

    /**
     * Surveillance Supervisor can classify and assign to Department.
     *
     * Supports both new ASSIGNED_TO_DEPARTMENT and legacy ASSIGNED_TO_MPFM states
     *
     * @param User $user
     * @param PlanTask $task
     * @return bool
     */
    public function assignToDepartment(User $user, PlanTask $task): bool
    {
        return $user->hasRole('surveillance_supervisor')
            && $task->isInState(TaskState::SUBMITTED_TO_SURVEILLANCE);
    }

    /**
     * Legacy method name for backward compatibility
     */
    public function assignToMPFM(User $user, PlanTask $task): bool
    {
        return $this->assignToDepartment($user, $task);
    }

    /**
     * Surveillance Supervisor can send task back to draft.
     *
     * @param User $user
     * @param PlanTask $task
     * @return bool
     */
    public function sendBackToDraft(User $user, PlanTask $task): bool
    {
        return $user->hasRole('surveillance_supervisor')
            && $task->isInState(TaskState::SUBMITTED_TO_SURVEILLANCE);
    }

    /**
     * Operator can start assigned tasks.
     *
     * Supports both new ASSIGNED_TO_DEPARTMENT and legacy ASSIGNED_TO_MPFM states
     * Also checks department matching if applicable
     *
     * @param User $user
     * @param PlanTask $task
     * @return bool
     */
    public function startTask(User $user, PlanTask $task): bool
    {
        if (!($user->hasRole('mpfm_operator') || $user->hasRole('operator'))) {
            return false;
        }

        if (!$task->isAssignedTo($user->id)) {
            return false;
        }

        // Check if task is in a state that allows starting (both new and legacy names)
        $canStart = $task->isInState(TaskState::ASSIGNED_TO_DEPARTMENT)
            || $task->isInState(TaskState::ASSIGNED_TO_MPFM);

        if (!$canStart) {
            return false;
        }

        // Verify department match if department is assigned
        if ($task->department_id && $user->department_id) {
            return $task->department_id === $user->department_id;
        }

        return true;
    }

    /**
     * Department Manager or MPFM Supervisor can review completed work.
     *
     * Supports both department_manager (new) and mpfm_supervisor (legacy) roles
     *
     * @param User $user
     * @param PlanTask $task
     * @return bool
     */
    public function reviewAsDepartment(User $user, PlanTask $task): bool
    {
        // Check if user is department manager or legacy MPFM supervisor
        $isDeptMgr = $user->hasRole('department_manager')
            || $user->hasRole('mpfm_supervisor');

        if (!$isDeptMgr) {
            return false;
        }

        if (!$task->isInState(TaskState::IN_PROGRESS)) {
            return false;
        }

        if ($task->completion_status !== 'completed') {
            return false;
        }

        // Department Manager must be in same department
        if ($user->hasRole('department_manager')) {
            if ($task->department_id && $task->department_id !== $user->department_id) {
                return false;
            }
        }

        return true;
    }

    /**
     * Legacy method name for backward compatibility
     */
    public function reviewAsMPFM(User $user, PlanTask $task): bool
    {
        return $this->reviewAsDepartment($user, $task);
    }

    /**
     * Department Manager or MPFM Supervisor can reject task back to operator.
     *
     * @param User $user
     * @param PlanTask $task
     * @return bool
     */
    public function rejectToOperator(User $user, PlanTask $task): bool
    {
        $isDeptMgr = $user->hasRole('department_manager')
            || $user->hasRole('mpfm_supervisor');

        if (!$isDeptMgr) {
            return false;
        }

        $canReject = $task->isInState(TaskState::DEPARTMENT_REVIEWED)
            || $task->isInState(TaskState::MPFM_REVIEWED);

        if (!$canReject) {
            return false;
        }

        // Department Manager must be in same department
        if ($user->hasRole('department_manager')) {
            if ($task->department_id && $task->department_id !== $user->department_id) {
                return false;
            }
        }

        return true;
    }

    /**
     * Surveillance Supervisor can review after Department Review.
     *
     * Supports both new DEPARTMENT_REVIEWED and legacy MPFM_REVIEWED states
     *
     * @param User $user
     * @param PlanTask $task
     * @return bool
     */
    public function reviewAsSurveillance(User $user, PlanTask $task): bool
    {
        if (!$user->hasRole('surveillance_supervisor')) {
            return false;
        }

        return $task->isInState(TaskState::DEPARTMENT_REVIEWED)
            || $task->isInState(TaskState::MPFM_REVIEWED);
    }

    /**
     * Surveillance Supervisor can reject back to Department Review.
     *
     * @param User $user
     * @param PlanTask $task
     * @return bool
     */
    public function rejectToDepartment(User $user, PlanTask $task): bool
    {
        return $user->hasRole('surveillance_supervisor')
            && $task->isInState(TaskState::SURVEILLANCE_REVIEWED);
    }

    /**
     * Legacy method name for backward compatibility
     */
    public function rejectToMPFM(User $user, PlanTask $task): bool
    {
        return $this->rejectToDepartment($user, $task);
    }

    /**
     * Job Officer can give final approval.
     *
     * @param User $user
     * @param PlanTask $task
     * @return bool
     */
    public function finalApprove(User $user, PlanTask $task): bool
    {
        return $user->hasRole('job_officer')
            && $task->isInState(TaskState::SURVEILLANCE_REVIEWED);
    }

    /**
     * Job Officer can reject back to surveillance.
     *
     * @param User $user
     * @param PlanTask $task
     * @return bool
     */
    public function rejectToSurveillance(User $user, PlanTask $task): bool
    {
        return $user->hasRole('job_officer')
            && $task->isInState(TaskState::JOB_OFFICER_APPROVED);
    }

    /**
     * Check if user can view state history.
     *
     * @param User $user
     * @param PlanTask $task
     * @return bool
     */
    public function viewStateHistory(User $user, PlanTask $task): bool
    {
        // Admins, supervisors, job officers, and department managers can view history
        // Operators can view history for their own tasks
        if ($user->hasAnyRole(['admin', 'job_officer', 'surveillance_supervisor', 'mpfm_supervisor', 'department_manager'])) {
            // Department managers can only view tasks in their department
            if ($user->hasRole('department_manager')) {
                if ($task->department_id && $task->department_id !== $user->department_id) {
                    return false;
                }
            }
            return true;
        }

        if ($user->hasRole('mpfm_operator') || $user->hasRole('operator')) {
            return $task->isAssignedTo($user->id);
        }

        return false;
    }

    /**
     * Check if user can edit task details.
     *
     * @param User $user
     * @param PlanTask $task
     * @return bool
     */
    public function update(User $user, PlanTask $task): bool
    {
        // Job Officer can edit tasks in draft or submitted states
        if ($user->hasRole('job_officer') && $task->isEditableByJobOfficer()) {
            return $task->monthlyPlan && $task->monthlyPlan->created_by === $user->id;
        }

        // Surveillance can edit when submitted
        if ($user->hasRole('surveillance_supervisor') && $task->isEditableBySurveillance()) {
            return true;
        }

        // Admins can always edit
        if ($user->hasRole('admin')) {
            return true;
        }

        return false;
    }

    /**
     * Check if user can view task details.
     *
     * @param User $user
     * @param PlanTask $task
     * @return bool
     */
    public function view(User $user, PlanTask $task): bool
    {
        // Supervisors and job officers can view all tasks
        if ($user->hasAnyRole(['admin', 'job_officer', 'surveillance_supervisor', 'mpfm_supervisor'])) {
            return true;
        }

        // Department managers can view tasks in their department
        if ($user->hasRole('department_manager')) {
            if ($task->department_id && $task->department_id !== $user->department_id) {
                return false;
            }
            return true;
        }

        // Operators can view tasks assigned to them
        if ($user->hasRole('mpfm_operator') || $user->hasRole('operator')) {
            return $task->isAssignedTo($user->id);
        }

        return false;
    }

    /**
     * Admin bypass for all actions.
     *
     * @param User $user
     * @return bool|null
     */
    public function before(User $user): ?bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return null; // Continue with normal policy checks
    }
}
