<?php

namespace App\Services\DataTable;

use App\Models\TestVesselTest;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;

class TestVesselTestDataTableService
{
    /**
     * Get DataTable query for Test Vessel Tests
     */
    public function getDataTable(Request $request)
    {
        $query = TestVesselTest::query();

        // Filter by oil field
        if ($request->filled('oil_field_id')) {
            $query->where('oil_field_id', $request->get('oil_field_id'));
        }

        // Filter by test vessel
        if ($request->filled('test_vessel_id')) {
            $query->where('test_vessel_id', $request->get('test_vessel_id'));
        }

        // Filter by oil string
        if ($request->filled('oil_string_id')) {
            $query->where('oil_string_id', $request->get('oil_string_id'));
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        // Filter by date range
        $dateFrom = $request->get('date_from');
        if ($dateFrom && trim($dateFrom) !== '') {
            $query->where('test_date', '>=', $dateFrom);
        }

        $dateTo = $request->get('date_to');
        if ($dateTo && trim($dateTo) !== '') {
            $query->where('test_date', '<=', $dateTo);
        }

        return DataTables::of($query)
            ->addColumn('vessel_name', function ($test) {
                return $test->testVessel?->name ?? '-';
            })
            ->addColumn('operator', function ($test) {
                return $test->user?->name ?? '-';
            })
            ->addColumn('oil_field_name', function ($test) {
                return $test->oilField?->name ?? '-';
            })
            ->addColumn('tower_name', function ($test) {
                return $test->tower?->name ?? '-';
            })
            ->addColumn('oil_string_name', function ($test) {
                return $test->oilString?->name ?? '-';
            })
            ->addColumn('mpfm_test_link', function ($test) {
                if ($test->mpfm_test_id) {
                    return '<a href="' . route('mpfm_tests.show', $test->mpfmTest?->id) . '" class="btn btn-sm btn-outline-primary">View MPFM</a>';
                }
                return '-';
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
                $actions = '<a href="' . route('tv_tests.show', $test->id) . '" class="btn btn-sm btn-info">View</a> ';
                $actions .= '<a href="' . route('tv_tests.edit', $test->id) . '" class="btn btn-sm btn-primary">Edit</a> ';
                $actions .= '<form action="' . route('tv_tests.destroy', $test->id) . '" method="POST" style="display:inline;">
                    ' . csrf_field() . '
                    ' . method_field('DELETE') . '
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure?\')">Delete</button>
                </form>';
                return $actions;
            })
            ->editColumn('test_date', function ($test) {
                return $test->test_date->format('Y-m-d');
            })
            ->editColumn('created_at', function ($test) {
                return $test->created_at->format('Y-m-d H:i');
            })
            ->rawColumns(['mpfm_test_link', 'status_badge', 'action'])
            ->make(true);
    }
}
