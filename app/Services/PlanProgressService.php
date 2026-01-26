<?php

namespace App\Services;

use App\Enums\TaskState;
use App\Models\MonthlyPlan;

class PlanProgressService
{
    /**
     * Calculate plan completion percentage
     *
     * Uses the state-based approach where only COMPLETED state tasks count as done
     *
     * @param MonthlyPlan $plan
     * @return float Percentage (0-100)
     */
    public function calculateProgress(MonthlyPlan $plan): float
    {
        return $plan->getWorkflowCompletionPercentage();
    }

    /**
     * Get detailed progress breakdown for a plan
     *
     * @param MonthlyPlan $plan
     * @return array
     */
    public function getDetailedProgress(MonthlyPlan $plan): array
    {
        $total = $plan->tasks()->count();
        $progress = $plan->getWorkflowProgress();

        if ($total === 0) {
            return [
                'total' => 0,
                'completed' => 0,
                'percentage' => 0.0,
                'by_state' => [],
                'status' => 'empty',
            ];
        }

        $completed = $progress['completed'] ?? 0;
        $percentage = round(($completed / $total) * 100, 2);

        return [
            'total' => $total,
            'completed' => $completed,
            'percentage' => $percentage,
            'by_state' => $progress,
            'status' => $this->getProgressStatus($percentage),
        ];
    }

    /**
     * Get department-wise progress breakdown
     *
     * Shows progress metrics for each department involved in the plan
     *
     * @param MonthlyPlan $plan
     * @return array Array of department progress data
     */
    public function getDepartmentProgress(MonthlyPlan $plan): array
    {
        $departments = $plan->getDepartments();

        return $departments->map(function ($dept) use ($plan) {
            $total = $plan->tasks()
                ->where('department_id', $dept->id)
                ->count();

            if ($total === 0) {
                return null;
            }

            $completed = $plan->tasks()
                ->where('department_id', $dept->id)
                ->where('state', TaskState::COMPLETED)
                ->count();

            $percentage = round(($completed / $total) * 100, 2);

            return [
                'department_id' => $dept->id,
                'department' => $dept->name,
                'code' => $dept->code,
                'total' => $total,
                'completed' => $completed,
                'pending' => $plan->tasks()
                    ->where('department_id', $dept->id)
                    ->where('completion_status', 'pending')
                    ->count(),
                'in_progress' => $plan->tasks()
                    ->where('department_id', $dept->id)
                    ->where('state', TaskState::IN_PROGRESS)
                    ->count(),
                'percentage' => $percentage,
                'status' => $this->getProgressStatus($percentage),
            ];
        })->filter()->values()->all();
    }

    /**
     * Get state-wise progress breakdown
     *
     * Shows how many tasks are in each state
     *
     * @param MonthlyPlan $plan
     * @return array
     */
    public function getStateProgress(MonthlyPlan $plan): array
    {
        $progress = $plan->getWorkflowProgress();
        $total = $plan->tasks()->count();

        if ($total === 0) {
            return [];
        }

        return collect($progress)
            ->map(fn($count) => round(($count / $total) * 100, 2))
            ->toArray();
    }

    /**
     * Get bottleneck analysis
     *
     * Identifies which states have the most tasks (potential bottlenecks)
     *
     * @param MonthlyPlan $plan
     * @return array
     */
    public function getBottleneckAnalysis(MonthlyPlan $plan): array
    {
        $bottlenecks = $plan->getWorkflowBottlenecks();
        $total = $plan->tasks()->count();

        if ($total === 0) {
            return [];
        }

        return collect($bottlenecks)
            ->map(fn($count) => [
                'count' => $count,
                'percentage' => round(($count / $total) * 100, 2),
            ])
            ->toArray();
    }

    /**
     * Get progress status label
     *
     * @param float $percentage
     * @return string
     */
    private function getProgressStatus(float $percentage): string
    {
        if ($percentage === 0.0) {
            return 'not_started';
        } elseif ($percentage < 25) {
            return 'early_stage';
        } elseif ($percentage < 50) {
            return 'in_progress';
        } elseif ($percentage < 75) {
            return 'advanced';
        } elseif ($percentage < 100) {
            return 'near_completion';
        } else {
            return 'completed';
        }
    }

    /**
     * Get progress badge color based on percentage
     *
     * @param float $percentage
     * @return string
     */
    public function getProgressColor(float $percentage): string
    {
        if ($percentage === 0.0) {
            return 'secondary';
        } elseif ($percentage < 25) {
            return 'danger';
        } elseif ($percentage < 50) {
            return 'warning';
        } elseif ($percentage < 75) {
            return 'info';
        } elseif ($percentage < 100) {
            return 'primary';
        } else {
            return 'success';
        }
    }

    /**
     * Check if plan is on track to meet KPI
     *
     * @param MonthlyPlan $plan
     * @return bool
     */
    public function isOnTrackForKPI(MonthlyPlan $plan): bool
    {
        if ($plan->monthly_target_kpi === null) {
            return true;
        }

        $currentCompletion = $this->calculateProgress($plan);
        return $currentCompletion >= $plan->monthly_target_kpi;
    }

    /**
     * Get KPI progress
     *
     * Returns current progress against target KPI
     *
     * @param MonthlyPlan $plan
     * @return array
     */
    public function getKPIProgress(MonthlyPlan $plan): array
    {
        $current = $this->calculateProgress($plan);
        $target = $plan->monthly_target_kpi ?? 100;

        return [
            'current' => $current,
            'target' => $target,
            'percentage_of_target' => $target > 0 ? round(($current / $target) * 100, 2) : 0,
            'on_track' => $this->isOnTrackForKPI($plan),
            'remaining' => max(0, $target - $current),
        ];
    }
}
