<?php

namespace App\Services\DataTable;

use App\Models\MPFMTest;
use App\Models\User;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;

class MPFMTestDataTableService
{
    /**
     * Get DataTable query for MPFM Tests
     */
    public function getDataTable(Request $request, User $user)
    {
        $query = MPFMTest::with(['user', 'oilField', 'tower', 'oilString', 'mpfmOperationalPlan']);

        // Apply access control for operators
        if ($user->hasRole('mpfm_operator')) {
            $query->accessibleByOperator($user);
        }

        // Apply filters
        if ($request->has('oil_field_id') && $request->oil_field_id) {
            $query->where('oil_field_id', $request->oil_field_id);
        }

        if ($request->has('tower_id') && $request->tower_id) {
            $query->where('tower_id', $request->tower_id);
        }

        if ($request->has('oil_string_id') && $request->oil_string_id) {
            $query->where('oil_string_id', $request->oil_string_id);
        }

        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('test_date_from') && $request->test_date_from) {
            $query->whereDate('test_date', '>=', $request->test_date_from);
        }

        if ($request->has('test_date_to') && $request->test_date_to) {
            $query->whereDate('test_date', '<=', $request->test_date_to);
        }

        if ($request->has('shift') && $request->shift) {
            $query->where('shift', $request->shift);
        }

        return DataTables::of($query)
            ->addColumn('user_name', function ($test) {
                return $test->user->name ?? '-';
            })
            ->addColumn('oil_field_name', function ($test) {
                return $test->oilField->name ?? '-';
            })
            ->addColumn('tower_name', function ($test) {
                return $test->tower->name ?? '-';
            })
            ->addColumn('oil_string_name', function ($test) {
                return $test->oilString->name ?? '-';
            })
            ->addColumn('gor', function ($test) {
                $kpis = $test->calculated_kpis ?? [];
                return isset($kpis['gor']) ? number_format($kpis['gor'], 2) : '-';
            })
            ->addColumn('status_badge', function ($test) {
                $badgeClass = match($test->status) {
                    'draft' => 'badge-secondary',
                    'submitted' => 'badge-info',
                    'approved' => 'badge-success',
                    'rejected' => 'badge-danger',
                    default => 'badge-secondary',
                };
                return '<span class="badge ' . $badgeClass . '">' . ucfirst($test->status) . '</span>';
            })
            ->addColumn('action', function ($test) {
                $actions = '<a href="' . route('mpfm_tests.show', $test->id) . '" class="btn btn-sm btn-info" title="View Details">View</a> ';
                if ($test->test_data) {
                    $actions .= '<a href="' . route('mpfm-tests.view', $test->id) . '" class="btn btn-sm btn-success" target="_blank" title="View Charts">Charts</a> ';
                }
                // Validation & History button
                $actions .= '<a href="' . route('mpfm_tests.show_history', $test->id) . '" class="btn btn-sm btn-warning" title="View Validation & History"><i class="fe fe-check-circle"></i></a> ';
                // String Analysis button
                $actions .= '<a href="' . route('mpfm_tests.string_analysis', $test->oil_string_id) . '" class="btn btn-sm btn-primary" title="View String Analysis"><i class="fe fe-trending-up"></i></a> ';

                if (auth()->user()->can('update', $test)) {
                    $actions .= '<a href="' . route('mpfm_tests.edit', $test->id) . '" class="btn btn-sm btn-primary">Edit</a> ';
                }
                if (auth()->user()->can('delete', $test)) {
                    $actions .= '<form action="' . route('mpfm_tests.destroy', $test->id) . '" method="POST" style="display:inline;">
                        ' . csrf_field() . '
                        ' . method_field('DELETE') . '
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure?\')">Delete</button>
                    </form>';
                }
                return $actions;
            })
            ->editColumn('test_date', function ($test) {
                return $test->test_date->format('Y-m-d');
            })
            ->editColumn('oil_rate', function ($test) {
                return $test->oil_rate ? number_format($test->oil_rate, 2) : '-';
            })
            ->editColumn('gas_rate', function ($test) {
                return $test->gas_rate ? number_format($test->gas_rate, 2) : '-';
            })
            ->editColumn('water_cut', function ($test) {
                return $test->water_cut ? number_format($test->water_cut, 2) . '%' : '-';
            })
            ->editColumn('created_at', function ($test) {
                return $test->created_at->format('Y-m-d H:i');
            })
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }
}

