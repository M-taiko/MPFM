<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TaskSLAService;
use App\Models\PlanTask;
use App\Models\TaskSLAHistory;
use App\Enums\TaskState;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CheckTaskSLAsCommand extends Command
{
    /**
     * The name and signature of the console command
     *
     * @var string
     */
    protected $signature = 'sla:check {--force : Force processing even if recently run}';

    /**
     * The console command description
     *
     * @var string
     */
    protected $description = 'Check for overdue tasks and send notifications based on SLA configuration';

    /**
     * The SLA service
     *
     * @var TaskSLAService
     */
    protected TaskSLAService $slaService;

    /**
     * Create a new command instance
     */
    public function __construct(TaskSLAService $slaService)
    {
        parent::__construct();
        $this->slaService = $slaService;
    }

    /**
     * Execute the console command
     *
     * @return int
     */
    public function handle(): int
    {
        // Check if SLA monitoring is enabled
        if (!config('sla.enabled')) {
            $this->info('SLA monitoring is disabled in configuration');
            return Command::SUCCESS;
        }

        try {
            $this->line('<info>Starting SLA check...</info>');

            // Get all overdue tasks
            $overdueTasks = $this->slaService->checkAndProcessOverdueTasks();

            if ($overdueTasks->isEmpty()) {
                $this->line('<info>✓ No overdue tasks detected</info>');
                return Command::SUCCESS;
            }

            $this->line("<info>Found {$overdueTasks->count()} overdue task(s)</info>");

            // Process each overdue task
            $processed = 0;
            $notified = 0;

            foreach ($overdueTasks as $task) {
                if ($this->processOverdueTask($task)) {
                    $notified++;
                }
                $processed++;
            }

            // Summary
            $this->line("");
            $this->line("<info>SLA Check Complete</info>");
            $this->line("Processed: {$processed}");
            $this->line("Notified: {$notified}");

            // Show SLA summary
            $this->showSLASummary();

            Log::info("SLA check completed", [
                'overdue_tasks' => $overdueTasks->count(),
                'notified' => $notified,
            ]);

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("SLA check failed: {$e->getMessage()}");
            Log::error("SLA check command failed: {$e->getMessage()}", [
                'exception' => $e,
            ]);

            return Command::FAILURE;
        }
    }

    /**
     * Process a single overdue task
     *
     * @param PlanTask $task
     * @return bool True if notification was sent
     */
    protected function processOverdueTask(PlanTask $task): bool
    {
        try {
            $this->line("Processing task {$task->id}: {$task->well_full_name}");

            // Get or create SLA history
            $slaHistory = $task->slaHistory;
            $hoursOverdue = $task->expected_completion_at->diffInHours(now());

            $this->output->writeln(
                "  State: {$task->state?->label()}",
                null
            );
            $this->output->writeln(
                "  Expected: {$task->expected_completion_at->format('Y-m-d H:i')}",
                null
            );
            $this->output->writeln(
                "  Overdue: {$hoursOverdue} hours",
                null
            );

            // Check escalation levels
            $escalationLevel = $this->determineEscalationLevel($hoursOverdue);

            if ($slaHistory) {
                $slaHistory->escalateTo($escalationLevel);
            }

            // Send notification
            $sent = $this->slaService->sendSLANotification(
                $task,
                "Task overdue by {$hoursOverdue} hours - automatic SLA check"
            );

            if ($sent) {
                $this->line("  <fg=green>✓ Notification sent</>");
            } else {
                $this->line("  <fg=yellow>⚠ Notification not sent (throttled or failed)</>");
            }

            return $sent;

        } catch (\Exception $e) {
            $this->error("  Error processing task: {$e->getMessage()}");
            Log::error("Failed to process overdue task {$task->id}: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Determine escalation level based on hours overdue
     *
     * @param int $hoursOverdue
     * @return int
     */
    protected function determineEscalationLevel(int $hoursOverdue): int
    {
        $config = config('sla.escalation', []);

        if ($hoursOverdue >= 24) {
            return 3; // Level 3: Management escalation
        }

        if ($hoursOverdue >= 4) {
            return 2; // Level 2: Supervisor escalation
        }

        if ($hoursOverdue >= 1) {
            return 1; // Level 1: Initial notification
        }

        return 0; // No escalation
    }

    /**
     * Show SLA summary statistics
     *
     * @return void
     */
    protected function showSLASummary(): void
    {
        try {
            $summary = $this->slaService->getSLASummary();

            $this->line("");
            $this->line("<info>═══════════════ SLA Summary ═══════════════</info>");
            $this->line("Total Tasks:           {$summary['total_tasks']}");
            $this->line("On Time:               {$summary['on_time']}");
            $this->line("Overdue:               {$summary['overdue']}");

            $complianceColor = $summary['compliance_percentage'] >= 80 ? 'green' : 'yellow';
            $this->line(
                "Compliance:            <fg={$complianceColor}>" .
                number_format($summary['compliance_percentage'], 2) . "%</>"
            );

            if (!empty($summary['overdue_by_state'])) {
                $this->line("");
                $this->line("<info>Overdue by State:</info>");
                foreach ($summary['overdue_by_state'] as $state => $count) {
                    $stateLabel = TaskState::tryFrom($state)?->label() ?? $state;
                    $this->line("  {$stateLabel}: {$count}");
                }
            }

            $this->line("<info>═════════════════════════════════════════</info>");

        } catch (\Exception $e) {
            Log::warning("Could not display SLA summary: {$e->getMessage()}");
        }
    }
}
