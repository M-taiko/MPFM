<?php

namespace App\Services\DataTable;

use App\Models\TestVessel;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;

class TestVesselDataTableService
{
    /**
     * Get DataTable query for Test Vessels
     */
    public function getDataTable(Request $request)
    {
        $query = TestVessel::query();

        // Filter by status - ONLY if it has a real value
        $status = $request->get('status');
        if ($status && trim($status) !== '') {
            $query->where('status', trim($status));
        }

        // Filter by oil field - ONLY if it has a real value
        $oilFieldId = $request->get('oil_field_id');
        if ($oilFieldId && trim($oilFieldId) !== '') {
            $query->where('oil_field_id', trim($oilFieldId));
        }

        return DataTables::of($query)
            ->addColumn('oil_field_name', function ($vessel) {
                return $vessel->oilField?->name ?? '-';
            })
            ->addColumn('tests_count', function ($vessel) {
                return $vessel->tests()->count();
            })
            ->addColumn('status_badge', function ($vessel) {
                $badgeClass = match($vessel->status) {
                    'active' => 'badge-success',
                    'inactive' => 'badge-secondary',
                    'maintenance' => 'badge-warning',
                    default => 'badge-secondary',
                };
                return '<span class="badge ' . $badgeClass . '">' . ucfirst($vessel->status) . '</span>';
            })
            ->addColumn('action', function ($vessel) {
                $actions = '<a href="' . route('test_vessels.show', $vessel->id) . '" class="btn btn-sm btn-info">View</a> ';
                $actions .= '<a href="' . route('test_vessels.edit', $vessel->id) . '" class="btn btn-sm btn-primary">Edit</a> ';
                $actions .= '<form action="' . route('test_vessels.destroy', $vessel->id) . '" method="POST" style="display:inline;">
                    ' . csrf_field() . '
                    ' . method_field('DELETE') . '
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure?\')">Delete</button>
                </form>';
                return $actions;
            })
            ->editColumn('created_at', function ($vessel) {
                return $vessel->created_at->format('Y-m-d H:i');
            })
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }
}
