<?php

return [

    /**
     * Enable/disable SLA monitoring globally
     * Can be overridden in environment-specific .env file
     */
    'enabled' => env('SLA_MONITORING_ENABLED', true),

    /**
     * Default SLA durations per workflow state (in hours)
     *
     * These represent the maximum time a task should spend in each state
     * before being considered overdue and triggering notifications.
     */
    'state_durations' => [
        'draft' => 24,                              // 1 day to submit
        'submitted_to_surveillance' => 48,          // 2 days to classify
        'assigned_to_mpfm' => 72,                   // 3 days to assign operator
        'in_progress' => 120,                       // 5 days to complete testing
        'mpfm_reviewed' => 48,                      // 2 days for MPFM supervisor review
        'surveillance_reviewed' => 48,              // 2 days for surveillance review
        'job_officer_approved' => 24,               // 1 day for final approval
    ],

    /**
     * Escalation rules
     *
     * Define escalation levels and actions when tasks exceed SLA
     * Level 1: First notification (immediate)
     * Level 2: Escalation to management (after 4 hours)
     * Level 3: Escalation to director (after 24 hours)
     */
    'escalation' => [
        'level_1' => [
            'hours' => 0,
            'action' => 'notify_responsible',
            'description' => 'Notify task responsible actor',
        ],
        'level_2' => [
            'hours' => 4,
            'action' => 'notify_supervisor',
            'description' => 'Escalate to supervisor',
        ],
        'level_3' => [
            'hours' => 24,
            'action' => 'notify_management',
            'description' => 'Escalate to management/director',
        ],
    ],

    /**
     * Notification settings
     */
    'notifications' => [
        // Enable/disable notifications entirely
        'enabled' => true,

        // Notification channels to use
        // Options: 'database', 'mail', 'slack', 'sms'
        'channels' => ['database', 'mail'],

        // Minimum interval between notifications for same task (in minutes)
        // Prevents notification flooding
        'on_overdue_minutes' => 15,

        // Send summary email to admin
        'send_admin_summary' => true,
        'admin_summary_time' => '09:00', // Send at 9 AM daily

        // Roles that should receive notifications
        'notify_roles' => [
            'draft' => ['job_officer'],
            'submitted_to_surveillance' => ['surveillance_supervisor'],
            'assigned_to_mpfm' => ['mpfm_supervisor'],
            'in_progress' => ['mpfm_operator', 'mpfm_supervisor'],
            'mpfm_reviewed' => ['surveillance_supervisor'],
            'surveillance_reviewed' => ['job_officer'],
        ],
    ],

    /**
     * Scheduled command settings
     */
    'check_interval' => '*/15 * * * *', // Every 15 minutes (cron format)

    // Maximum number of tasks to process in single command run
    'batch_size' => 100,

    // Timeout for scheduled command (prevents overlapping runs)
    'command_timeout_minutes' => 5,

    /**
     * Automatic actions on SLA escalation
     *
     * Define what automatic actions to take when tasks become overdue
     */
    'auto_actions' => [
        // Automatically mark task as overdue flag
        'mark_overdue_flag' => true,

        // Automatically create task state history entry
        'record_in_history' => true,

        // Optionally auto-escalate to next state after severe overdue
        // Set to null to disable
        'auto_escalate_after_hours' => null,
    ],

    /**
     * Retry settings for failed notifications
     */
    'retries' => [
        'enabled' => true,
        'max_attempts' => 3,
        'retry_after_minutes' => 30,
    ],

    /**
     * Logging
     */
    'logging' => [
        'enabled' => true,
        'channel' => 'single', // Can be any Laravel log channel
        'log_level' => 'info', // debug, info, warning, error
    ],

    /**
     * Role-based SLA customization
     *
     * Override default SLA durations for specific roles
     * This allows fine-tuning based on operational needs
     */
    'role_overrides' => [
        // Example: MPFM operators get 7 days instead of 5
        // 'mpfm_operator' => [
        //     'in_progress' => 168, // 7 days
        // ],
    ],

    /**
     * Task priority SLA multiplier
     *
     * High priority tasks get shorter SLAs
     * Low priority tasks get longer SLAs
     */
    'priority_multipliers' => [
        'critical' => 0.5,  // 50% of standard duration
        'high' => 0.75,     // 75% of standard duration
        'normal' => 1.0,    // 100% of standard duration
        'low' => 1.5,       // 150% of standard duration
    ],

    /**
     * Holidays and exclusions
     *
     * Exclude specific dates from SLA calculations
     * Useful for weekends, holidays, maintenance windows
     */
    'exclusions' => [
        // Enable/disable weekend exclusion
        'exclude_weekends' => false,

        // List of dates to exclude (Y-m-d format)
        'excluded_dates' => [
            // '2026-01-01', // New Year
            // '2026-07-04', // Independence Day
        ],

        // Hours to exclude per day (e.g., 18:00-06:00 is off-hours)
        'exclude_off_hours' => false,
        'off_hours_start' => '18:00',
        'off_hours_end' => '06:00',
    ],

];
