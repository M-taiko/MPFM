<?php

namespace App\Services;

use App\Models\OperatorPlan;
use App\Models\WellTest;
use Illuminate\Support\Facades\DB;

/**
 * OperatorPlanService handles CRUD operations for operator plans
 * and manages the workflow for operators during well testing
 */
class OperatorPlanService
{
    /**
     * Create a new operator plan for a well test
     */
    public function createOperatorPlan(array $data): OperatorPlan
    {
        $this->validateOperatorPlanData($data);

        return OperatorPlan::create([
            'well_test_id' => $data['well_test_id'],
            'step_number' => $data['step_number'],
            'step_type' => $data['step_type'] ?? 'instruction',
            'step_description' => $data['step_description'],
            'step_parameters' => $data['step_parameters'] ?? null,
            'order' => $data['order'] ?? 0,
        ]);
    }

    /**
     * Create multiple operator plan steps for a well test
     */
    public function createMultipleOperatorPlans(array $steps, int $wellTestId): array
    {
        $createdPlans = [];

        foreach ($steps as $index => $step) {
            $step['well_test_id'] = $wellTestId;
            $step['step_number'] = $step['step_number'] ?? ($index + 1);
            $step['order'] = $step['order'] ?? $index;

            $createdPlans[] = $this->createOperatorPlan($step);
        }

        return $createdPlans;
    }

    /**
     * Get all operator plans for a specific well test
     */
    public function getOperatorPlansForWellTest(int $wellTestId): \Illuminate\Database\Eloquent\Collection
    {
        return OperatorPlan::where('well_test_id', $wellTestId)
            ->ordered()
            ->get();
    }

    /**
     * Get a specific operator plan by ID
     */
    public function getOperatorPlan(int $id): ?OperatorPlan
    {
        return OperatorPlan::find($id);
    }

    /**
     * Update an operator plan
     */
    public function updateOperatorPlan(int $id, array $data): ?OperatorPlan
    {
        $operatorPlan = $this->getOperatorPlan($id);

        if (!$operatorPlan) {
            return null;
        }

        $this->validateOperatorPlanData($data, false);

        $operatorPlan->update([
            'step_number' => $data['step_number'] ?? $operatorPlan->step_number,
            'step_type' => $data['step_type'] ?? $operatorPlan->step_type,
            'step_description' => $data['step_description'] ?? $operatorPlan->step_description,
            'step_parameters' => $data['step_parameters'] ?? $operatorPlan->step_parameters,
            'order' => $data['order'] ?? $operatorPlan->order,
        ]);

        return $operatorPlan->refresh();
    }

    /**
     * Delete an operator plan
     */
    public function deleteOperatorPlan(int $id): bool
    {
        $operatorPlan = $this->getOperatorPlan($id);

        if (!$operatorPlan) {
            return false;
        }

        return $operatorPlan->delete();
    }

    /**
     * Mark an operator plan step as completed
     */
    public function markStepAsCompleted(int $id, string $completedBy = null, array $results = []): ?OperatorPlan
    {
        $operatorPlan = $this->getOperatorPlan($id);

        if (!$operatorPlan) {
            return null;
        }

        $operatorPlan->markAsCompleted($completedBy, $results);

        return $operatorPlan->refresh();
    }

    /**
     * Mark an operator plan step as pending
     */
    public function markStepAsPending(int $id): ?OperatorPlan
    {
        $operatorPlan = $this->getOperatorPlan($id);

        if (!$operatorPlan) {
            return null;
        }

        $operatorPlan->markAsPending();

        return $operatorPlan->refresh();
    }

    /**
     * Reorder operator plan steps
     */
    public function reorderSteps(int $wellTestId, array $orderedIds): bool
    {
        DB::transaction(function () use ($wellTestId, $orderedIds) {
            foreach ($orderedIds as $index => $id) {
                $operatorPlan = OperatorPlan::where('well_test_id', $wellTestId)->find($id);
                if ($operatorPlan) {
                    $operatorPlan->update(['order' => $index]);
                }
            }
        });

        return true;
    }

    /**
     * Generate a default operator plan based on well test type
     */
    public function generateDefaultPlan(WellTest $wellTest): array
    {
        $defaultSteps = [];

        // Add initial steps based on test type
        $defaultSteps[] = [
            'step_number' => 1,
            'step_type' => 'preparation',
            'step_description' => 'Verify all equipment is properly connected and calibrated',
            'order' => 0,
        ];

        $defaultSteps[] = [
            'step_number' => 2,
            'step_type' => 'measurement',
            'step_description' => 'Record initial pressure and temperature readings',
            'order' => 1,
        ];

        $defaultSteps[] = [
            'step_number' => 3,
            'step_type' => 'instruction',
            'step_description' => 'Begin data acquisition and monitor for stable readings',
            'order' => 2,
        ];

        $defaultSteps[] = [
            'step_number' => 4,
            'step_type' => 'measurement',
            'step_description' => 'Record flow rates (oil, water, gas) at regular intervals',
            'order' => 3,
        ];

        $defaultSteps[] = [
            'step_number' => 5,
            'step_type' => 'action',
            'step_description' => 'Adjust choke size as per test procedure',
            'order' => 4,
        ];

        $defaultSteps[] = [
            'step_number' => 6,
            'step_type' => 'measurement',
            'step_description' => 'Record final readings after stabilization',
            'order' => 5,
        ];

        $defaultSteps[] = [
            'step_number' => 7,
            'step_type' => 'cleanup',
            'step_description' => 'Safely shut down equipment and secure test site',
            'order' => 6,
        ];

        return $defaultSteps;
    }

    /**
     * Validate operator plan data
     */
    private function validateOperatorPlanData(array $data, bool $isCreate = true): void
    {
        $errors = [];

        if ($isCreate && !isset($data['well_test_id'])) {
            $errors[] = 'Well test ID is required';
        } elseif (isset($data['well_test_id'])) {
            $wellTest = WellTest::find($data['well_test_id']);
            if (!$wellTest) {
                $errors[] = 'Invalid well test ID';
            }
        }

        if (!isset($data['step_description']) || trim($data['step_description']) === '') {
            $errors[] = 'Step description is required';
        }

        if (isset($data['step_number']) && !is_numeric($data['step_number'])) {
            $errors[] = 'Step number must be numeric';
        }

        if (isset($data['step_type']) && !in_array($data['step_type'], ['instruction', 'measurement', 'action', 'preparation', 'cleanup'])) {
            $errors[] = 'Invalid step type. Must be one of: instruction, measurement, action, preparation, cleanup';
        }

        if (!empty($errors)) {
            throw new \InvalidArgumentException(implode(', ', $errors));
        }
    }

    /**
     * Get operator plan statistics
     */
    public function getPlanStatistics(int $wellTestId): array
    {
        $allSteps = $this->getOperatorPlansForWellTest($wellTestId);
        $totalSteps = $allSteps->count();
        $completedSteps = $allSteps->where('is_completed', true)->count();
        $pendingSteps = $totalSteps - $completedSteps;

        $completionPercentage = $totalSteps > 0 ? ($completedSteps / $totalSteps) * 100 : 0;

        return [
            'total_steps' => $totalSteps,
            'completed_steps' => $completedSteps,
            'pending_steps' => $pendingSteps,
            'completion_percentage' => round($completionPercentage, 2),
            'is_completed' => $totalSteps > 0 && $completedSteps === $totalSteps,
        ];
    }

    /**
     * Get next step in the plan
     */
    public function getNextStep(int $wellTestId): ?OperatorPlan
    {
        return OperatorPlan::where('well_test_id', $wellTestId)
            ->where('is_completed', false)
            ->ordered()
            ->first();
    }

    /**
     * Get completed steps
     */
    public function getCompletedSteps(int $wellTestId): \Illuminate\Database\Eloquent\Collection
    {
        return OperatorPlan::where('well_test_id', $wellTestId)
            ->completed()
            ->ordered()
            ->get();
    }

    /**
     * Get pending steps
     */
    public function getPendingSteps(int $wellTestId): \Illuminate\Database\Eloquent\Collection
    {
        return OperatorPlan::where('well_test_id', $wellTestId)
            ->pending()
            ->ordered()
            ->get();
    }
}
