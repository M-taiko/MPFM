<?php

namespace App\Services;

use App\Models\MPFMTest;
use App\Models\MPFMOperationalPlan;
use App\Models\OilField;
use App\Models\Tower;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardService
{
    /**
     * Get dashboard KPIs for the authenticated user
     */
    public function getKPIs(User $user): array
    {
        $today = Carbon::today();

        // Base query for MPFM tests - apply access control
        $testsQuery = $this->getAccessibleTestsQuery($user);

        // Total MPFM tests today
        $testsToday = (clone $testsQuery)
            ->whereDate('test_date', $today)
            ->count();

        // Active oil fields
        $activeOilFields = OilField::active()->count();

        // Active towers
        $activeTowers = Tower::active()->count();

        // Operators on duty (operators with active plans today)
        $operatorsOnDuty = User::role('mpfm_operator')
            ->whereHas('assignedPlans', function ($query) use ($today) {
                $query->whereDate('plan_date', $today)
                    ->where('status', 'active');
            })
            ->count();

        // Average GOR from recent tests (last 7 days)
        $avgGOR = (clone $testsQuery)
            ->where('test_date', '>=', $today->copy()->subDays(7))
            ->whereNotNull('calculated_kpis')
            ->get()
            ->map(function ($test) {
                $kpis = $test->calculated_kpis ?? [];
                return $kpis['gor'] ?? null;
            })
            ->filter()
            ->avg();

        return [
            'tests_today' => $testsToday,
            'active_oil_fields' => $activeOilFields,
            'active_towers' => $activeTowers,
            'operators_on_duty' => $operatorsOnDuty,
            'avg_gor' => round($avgGOR ?? 0, 2),
        ];
    }

    /**
     * Get MPFM tests per day for the last 7 days
     */
    public function getTestsPerDayChartData(User $user): array
    {
        $startDate = Carbon::today()->subDays(6);
        $endDate = Carbon::today();

        $testsQuery = $this->getAccessibleTestsQuery($user);

        $data = (clone $testsQuery)
            ->selectRaw('DATE(test_date) as date, COUNT(*) as count')
            ->whereBetween('test_date', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Fill in missing dates with 0
        $chartData = [];
        for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
            $dateStr = $date->format('Y-m-d');
            $found = $data->firstWhere('date', $dateStr);
            $chartData[] = [
                'date' => $date->format('M d'),
                'count' => $found ? $found->count : 0,
            ];
        }

        return $chartData;
    }

    /**
     * Get production per oil field (bar chart)
     */
    public function getProductionPerFieldChartData(User $user): array
    {
        $testsQuery = $this->getAccessibleTestsQuery($user);

        $data = (clone $testsQuery)
            ->select(
                'oil_fields.name as field_name',
                DB::raw('SUM(mpfm_tests.oil_rate) as total_oil'),
                DB::raw('SUM(mpfm_tests.gas_rate) as total_gas'),
                DB::raw('COUNT(*) as test_count')
            )
            ->join('oil_fields', 'mpfm_tests.oil_field_id', '=', 'oil_fields.id')
            ->where('mpfm_tests.test_date', '>=', Carbon::today()->subDays(30))
            ->groupBy('oil_fields.id', 'oil_fields.name')
            ->orderBy('total_oil', 'desc')
            ->limit(10)
            ->get();

        return $data->map(function ($item) {
            return [
                'field' => $item->field_name,
                'oil' => round($item->total_oil ?? 0, 2),
                'gas' => round($item->total_gas ?? 0, 2),
                'tests' => $item->test_count,
            ];
        })->toArray();
    }

    /**
     * Get operator performance (tests count per operator)
     */
    public function getOperatorPerformanceChartData(User $user): array
    {
        $testsQuery = $this->getAccessibleTestsQuery($user);

        $data = (clone $testsQuery)
            ->select(
                'users.name as operator_name',
                DB::raw('COUNT(*) as test_count'),
                DB::raw('AVG(mpfm_tests.oil_rate) as avg_oil_rate')
            )
            ->join('users', 'mpfm_tests.user_id', '=', 'users.id')
            ->where('mpfm_tests.test_date', '>=', Carbon::today()->subDays(30))
            ->groupBy('users.id', 'users.name')
            ->orderBy('test_count', 'desc')
            ->limit(10)
            ->get();

        return $data->map(function ($item) {
            return [
                'operator' => $item->operator_name,
                'tests' => $item->test_count,
                'avg_oil_rate' => round($item->avg_oil_rate ?? 0, 2),
            ];
        })->toArray();
    }

    /**
     * Get recent MPFM tests (last 10)
     */
    public function getRecentTests(User $user, int $limit = 10)
    {
        $testsQuery = $this->getAccessibleTestsQuery($user);

        return (clone $testsQuery)
            ->with(['user', 'oilField', 'tower', 'oilString'])
            ->orderBy('test_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get accessible tests query based on user role
     */
    protected function getAccessibleTestsQuery(User $user)
    {
        $query = MPFMTest::query();

        // Operators can only see their own tests or tests from assigned plans
        if ($user->hasRole('mpfm_operator')) {
            $query->accessibleByOperator($user);
        }

        // Supervisors and admins see all tests
        return $query;
    }

    /**
     * Get dashboard statistics summary
     */
    public function getStatisticsSummary(User $user): array
    {
        $testsQuery = $this->getAccessibleTestsQuery($user);
        $last30Days = Carbon::today()->subDays(30);

        $totalTests = (clone $testsQuery)->count();
        $testsLast30Days = (clone $testsQuery)
            ->where('test_date', '>=', $last30Days)
            ->count();

        $totalOilProduction = (clone $testsQuery)
            ->where('test_date', '>=', $last30Days)
            ->sum('oil_rate');

        $totalGasProduction = (clone $testsQuery)
            ->where('test_date', '>=', $last30Days)
            ->sum('gas_rate');

        $avgWaterCut = (clone $testsQuery)
            ->where('test_date', '>=', $last30Days)
            ->avg('water_cut');

        return [
            'total_tests' => $totalTests,
            'tests_last_30_days' => $testsLast30Days,
            'total_oil_production' => round($totalOilProduction ?? 0, 2),
            'total_gas_production' => round($totalGasProduction ?? 0, 2),
            'avg_water_cut' => round($avgWaterCut ?? 0, 2),
        ];
    }
}

