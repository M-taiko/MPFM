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
     * Surveillance Supervisor can classify and assign to MPFM.
     *
     * @param User $user
     * @param PlanTask $task
     * @return bool
     */
    public function assignToMPFM(User $user, PlanTask $task): bool
    {
        return $user->hasRole('surveillance_supervisor')
            && $task->isInState(TaskState::SUBMITTED_TO_SURVEILLANCE);
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
     * MPFM Operator can start assigned tasks.
     *
     * @param User $user
     * @param PlanTask $task
     * @return bool
     */
    public function startTask(User $user, PlanTask $task): bool
    {
        return ($user->hasRole('mpfm_operator') || $user->hasRole('operator'))
            && $task->isInState(TaskState::ASSIGNED_TO_MPFM)
            && $task->isAssignedTo($user->id);
    }

    /**
     * MPFM Supervisor can review completed work.
     *
     * @param User $user
     * @param PlanTask $task
     * @return bool
     */
    public function reviewAsMPFM(User $user, PlanTask $task): bool
    {
        return $user->hasRole('mpfm_supervisor')
            && $task->isInState(TaskState::IN_PROGRESS)
            && $task->completion_status === 'completed'; // Technical completion check
    }

    /**
     * MPFM Supervisor can reject task back to operator.
     *
     * @param User $user
     * @param PlanTask $task
     * @return bool
     */
    public function rejectToOperator(User $user, PlanTask $task): bool
    {
        return $user->hasRole('mpfm_supervisor')
            && $task->isInState(TaskState::MPFM_REVIEWED);
    }

    /**
     * Surveillance Supervisor can review after MPFM.
     *
     * @param User $user
     * @param PlanTask $task
     * @return bool
     */
    public function reviewAsSurveillance(User $user, PlanTask $task): bool
    {
        return $user->hasRole('surveillance_supervisor')
            && $task->isInState(TaskState::MPFM_REVIEWED);
    }

    /**
     * Surveillance Supervisor can reject back to MPFM.
     *
     * @param User $user
     * @param PlanTask $task
     * @return bool
     */
    public function rejectToMPFM(User $user, PlanTask $task): bool
    {
        return $user->hasRole('surveillance_supervisor')
            && $task->isInState(TaskState::SURVEILLANCE_REVIEWED);
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
        // Admins, supervisors, and job officers can view history
        // Operators can view history for their own tasks
        if ($user->hasAnyRole(['admin', 'job_officer', 'surveillance_supervisor', 'mpfm_supervisor'])) {
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
