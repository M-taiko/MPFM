<?php

namespace App\Imports;

use App\Domains\Task\Models\Task;
use App\Enums\TaskState;
use App\Models\Department;
use App\Models\MonthlyPlan;
use App\Models\OilString;
use App\Models\Tower;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TasksImport
{
    public array $errors = [];
    public array $imported = [];
    public int $successCount = 0;
    public int $failCount = 0;

    // Lookup caches
    private array $departmentCache = [];
    private array $planCache = [];
    private array $oilStringCache = [];
    private array $towerCache = [];
    private array $userCache = [];

    // Valid enum values
    private array $validTaskTypes = ['plan_task', 'daily_plan', 'operator_plan'];
    private array $validPriorities = ['low', 'medium', 'high', 'critical'];

    // Column map: excel header → internal key
    private array $columnMap = [
        'title'                  => ['title', 'task title', 'اسم المهمة', 'عنوان'],
        'description'            => ['description', 'وصف', 'task description'],
        'notes'                  => ['notes', 'ملاحظات', 'note'],
        'monthly_plan'           => ['monthly plan', 'monthly_plan', 'الخطة الشهرية', 'plan'],
        'department'             => ['department', 'القسم', 'dept'],
        'task_type'              => ['task type', 'task_type', 'نوع المهمة', 'type'],
        'priority'               => ['priority', 'الأولوية', 'أولوية'],
        'assigned_to'            => ['assigned to', 'assigned_to', 'مسند إلى', 'operator'],
        'expected_completion_at' => ['expected completion', 'expected_completion', 'تاريخ الإنجاز', 'completion date'],
        'oil_string'             => ['oil string', 'oil_string', 'خط النفط', 'string'],
        'tower'                  => ['tower', 'البرج', 'tower name'],
    ];

    public function import(array $rows): void
    {
        foreach ($rows as $index => $row) {
            $rowNum = $index + 2; // +2 because row 1 is header
            $this->processRow($row, $rowNum);
        }
    }

    private function processRow(array $row, int $rowNum): void
    {
        // Skip completely empty rows
        if (empty(array_filter($row))) {
            return;
        }

        $data = $this->mapRow($row);
        $rowErrors = $this->validateRow($data, $rowNum);

        if (!empty($rowErrors)) {
            $this->errors[] = [
                'row'    => $rowNum,
                'data'   => $data,
                'errors' => $rowErrors,
            ];
            $this->failCount++;
            return;
        }

        try {
            $task = Task::create([
                'title'                  => trim($data['title']),
                'description'            => isset($data['description']) ? trim($data['description']) : null,
                'notes'                  => isset($data['notes']) ? trim($data['notes']) : null,
                'monthly_plan_id'        => $this->resolvePlan($data['monthly_plan']),
                'department_id'          => $this->resolveDepartment($data['department']),
                'task_type'              => strtolower(trim($data['task_type'])),
                'priority'               => strtolower(trim($data['priority'])),
                'assigned_to_user_id'    => isset($data['assigned_to']) ? $this->resolveUser($data['assigned_to']) : null,
                'expected_completion_at' => isset($data['expected_completion_at']) ? $this->parseDate($data['expected_completion_at']) : null,
                'oil_string_id'          => isset($data['oil_string']) ? $this->resolveOilString($data['oil_string']) : null,
                'tower_id'               => isset($data['tower']) ? $this->resolveTower($data['tower']) : null,
                'created_by_user_id'     => Auth::id(),
                'state'                  => TaskState::DRAFT->value,
            ]);

            $this->imported[] = $task;
            $this->successCount++;
        } catch (\Exception $e) {
            $this->errors[] = [
                'row'    => $rowNum,
                'data'   => $data,
                'errors' => ['System error: ' . $e->getMessage()],
            ];
            $this->failCount++;
        }
    }

    private function mapRow(array $row): array
    {
        // Row keys are already mapped (passed as assoc after header parsing)
        return $row;
    }

    private function validateRow(array $data, int $rowNum): array
    {
        $errors = [];

        // Title
        if (empty(trim($data['title'] ?? ''))) {
            $errors[] = 'Title is required';
        } elseif (strlen(trim($data['title'])) > 255) {
            $errors[] = 'Title must not exceed 255 characters';
        }

        // Monthly Plan
        if (empty($data['monthly_plan'])) {
            $errors[] = 'Monthly Plan is required';
        } elseif (!$this->resolvePlan($data['monthly_plan'])) {
            $errors[] = 'Monthly Plan "' . $data['monthly_plan'] . '" not found';
        }

        // Department
        if (empty($data['department'])) {
            $errors[] = 'Department is required';
        } elseif (!$this->resolveDepartment($data['department'])) {
            $errors[] = 'Department "' . $data['department'] . '" not found';
        }

        // Task Type
        if (empty($data['task_type'])) {
            $errors[] = 'Task Type is required';
        } elseif (!in_array(strtolower(trim($data['task_type'])), $this->validTaskTypes)) {
            $errors[] = 'Task Type must be one of: ' . implode(', ', $this->validTaskTypes);
        }

        // Priority
        if (empty($data['priority'])) {
            $errors[] = 'Priority is required';
        } elseif (!in_array(strtolower(trim($data['priority'])), $this->validPriorities)) {
            $errors[] = 'Priority must be one of: ' . implode(', ', $this->validPriorities);
        }

        // Assigned To (optional) - validate if provided
        if (!empty($data['assigned_to']) && !$this->resolveUser($data['assigned_to'])) {
            $errors[] = 'User "' . $data['assigned_to'] . '" not found';
        }

        // Oil String (optional) - validate if provided
        if (!empty($data['oil_string']) && !$this->resolveOilString($data['oil_string'])) {
            $errors[] = 'Oil String "' . $data['oil_string'] . '" not found';
        }

        // Tower (optional) - validate if provided
        if (!empty($data['tower']) && !$this->resolveTower($data['tower'])) {
            $errors[] = 'Tower "' . $data['tower'] . '" not found';
        }

        return $errors;
    }

    // ===== Resolvers with caching =====

    private function resolvePlan(string $name): ?int
    {
        $key = strtolower(trim($name));
        if (!isset($this->planCache[$key])) {
            $plan = MonthlyPlan::whereRaw('LOWER(plan_name) = ?', [$key])
                ->orWhereRaw('LOWER(plan_month) = ?', [$key])
                ->first();
            $this->planCache[$key] = $plan?->id;
        }
        return $this->planCache[$key];
    }

    private function resolveDepartment(string $name): ?int
    {
        $key = strtolower(trim($name));
        if (!isset($this->departmentCache[$key])) {
            $dept = Department::where('is_active', true)
                ->whereRaw('LOWER(name) = ?', [$key])
                ->orWhereRaw('LOWER(code) = ?', [$key])
                ->first();
            $this->departmentCache[$key] = $dept?->id;
        }
        return $this->departmentCache[$key];
    }

    private function resolveOilString(string $name): ?int
    {
        $key = strtolower(trim($name));
        if (!isset($this->oilStringCache[$key])) {
            $os = OilString::whereRaw('LOWER(name) = ?', [$key])->first();
            $this->oilStringCache[$key] = $os?->id;
        }
        return $this->oilStringCache[$key];
    }

    private function resolveTower(string $name): ?int
    {
        $key = strtolower(trim($name));
        if (!isset($this->towerCache[$key])) {
            $t = Tower::whereRaw('LOWER(name) = ?', [$key])->first();
            $this->towerCache[$key] = $t?->id;
        }
        return $this->towerCache[$key];
    }

    private function resolveUser(string $name): ?int
    {
        $key = strtolower(trim($name));
        if (!isset($this->userCache[$key])) {
            $u = User::whereRaw('LOWER(name) = ?', [$key])
                ->orWhereRaw('LOWER(email) = ?', [$key])
                ->first();
            $this->userCache[$key] = $u?->id;
        }
        return $this->userCache[$key];
    }

    private function parseDate(string $value): ?string
    {
        if (empty(trim($value))) return null;
        try {
            // Handle Excel numeric date serial
            if (is_numeric($value)) {
                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float)$value);
                return $date->format('Y-m-d H:i:s');
            }
            return \Carbon\Carbon::parse($value)->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return null;
        }
    }

    // ===== Column header normalizer =====
    public function normalizeHeaders(array $headers): array
    {
        $normalized = [];
        foreach ($headers as $i => $header) {
            $h = strtolower(trim((string) $header));
            $matched = null;
            foreach ($this->columnMap as $key => $aliases) {
                if (in_array($h, $aliases)) {
                    $matched = $key;
                    break;
                }
            }
            $normalized[$i] = $matched ?? $h;
        }
        return $normalized;
    }
}
