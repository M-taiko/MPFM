<?php

namespace App\Enums;

enum TaskState: string
{
    case DRAFT = 'draft';
    case SUBMITTED_TO_SURVEILLANCE = 'submitted_to_surveillance';
    case ASSIGNED_TO_MPFM = 'assigned_to_mpfm';
    case IN_PROGRESS = 'in_progress';
    case MPFM_REVIEWED = 'mpfm_reviewed';
    case SURVEILLANCE_REVIEWED = 'surveillance_reviewed';
    case JOB_OFFICER_APPROVED = 'job_officer_approved';
    case COMPLETED = 'completed';

    /**
     * Get human-readable label for the state
     */
    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Draft',
            self::SUBMITTED_TO_SURVEILLANCE => 'Submitted to Surveillance',
            self::ASSIGNED_TO_MPFM => 'Assigned to MPFM',
            self::IN_PROGRESS => 'In Progress',
            self::MPFM_REVIEWED => 'MPFM Reviewed',
            self::SURVEILLANCE_REVIEWED => 'Surveillance Reviewed',
            self::JOB_OFFICER_APPROVED => 'Job Officer Approved',
            self::COMPLETED => 'Completed',
        };
    }

    /**
     * Get color for UI badges
     */
    public function color(): string
    {
        return match($this) {
            self::DRAFT => 'gray',
            self::SUBMITTED_TO_SURVEILLANCE => 'blue',
            self::ASSIGNED_TO_MPFM => 'purple',
            self::IN_PROGRESS => 'yellow',
            self::MPFM_REVIEWED => 'indigo',
            self::SURVEILLANCE_REVIEWED => 'teal',
            self::JOB_OFFICER_APPROVED => 'green',
            self::COMPLETED => 'dark-green',
        };
    }

    /**
     * Get allowed state transitions from current state
     *
     * @return array<TaskState>
     */
    public function allowedTransitions(): array
    {
        return match($this) {
            self::DRAFT => [
                self::SUBMITTED_TO_SURVEILLANCE
            ],
            self::SUBMITTED_TO_SURVEILLANCE => [
                self::ASSIGNED_TO_MPFM,
                self::DRAFT  // Can be sent back
            ],
            self::ASSIGNED_TO_MPFM => [
                self::IN_PROGRESS
            ],
            self::IN_PROGRESS => [
                self::MPFM_REVIEWED
            ],
            self::MPFM_REVIEWED => [
                self::SURVEILLANCE_REVIEWED,
                self::IN_PROGRESS  // Can be rejected back to operator
            ],
            self::SURVEILLANCE_REVIEWED => [
                self::JOB_OFFICER_APPROVED,
                self::MPFM_REVIEWED  // Can be sent back
            ],
            self::JOB_OFFICER_APPROVED => [
                self::COMPLETED,
                self::SURVEILLANCE_REVIEWED  // Can be rejected
            ],
            self::COMPLETED => []  // Terminal state
        };
    }

    /**
     * Check if transition to target state is allowed
     */
    public function canTransitionTo(TaskState $targetState): bool
    {
        return in_array($targetState, $this->allowedTransitions(), true);
    }

    /**
     * Get all states as array for validation rules
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Check if state is a completed state
     */
    public function isCompleted(): bool
    {
        return $this === self::COMPLETED;
    }

    /**
     * Check if state is editable by Job Officer
     */
    public function isEditableByJobOfficer(): bool
    {
        return in_array($this, [
            self::DRAFT,
            self::SUBMITTED_TO_SURVEILLANCE
        ], true);
    }

    /**
     * Check if state allows operator to start work
     */
    public function allowsOperatorStart(): bool
    {
        return $this === self::ASSIGNED_TO_MPFM;
    }

    /**
     * Get icon for UI representation
     */
    public function icon(): string
    {
        return match($this) {
            self::DRAFT => 'document',
            self::SUBMITTED_TO_SURVEILLANCE => 'paper-airplane',
            self::ASSIGNED_TO_MPFM => 'user-group',
            self::IN_PROGRESS => 'clock',
            self::MPFM_REVIEWED => 'check-circle',
            self::SURVEILLANCE_REVIEWED => 'shield-check',
            self::JOB_OFFICER_APPROVED => 'badge-check',
            self::COMPLETED => 'check-badge',
        };
    }
}
