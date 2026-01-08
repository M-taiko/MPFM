<?php

namespace App\Services\DataTable;

use App\Models\OilField;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;

class OilFieldDataTableService
{
    /**
     * Get DataTable query for Oil Fields
     */
    public function getDataTable(Request $request)
    {
        $query = OilField::query();

        return DataTables::of($query)
            ->addColumn('towers_count', function ($oilField) {
                return $oilField->towers()->count();
            })
            ->addColumn('status_badge', function ($oilField) {
                $badgeClass = match($oilField->status) {
                    'active' => 'badge-success',
                    'inactive' => 'badge-secondary',
                    'maintenance' => 'badge-warning',
                    default => 'badge-secondary',
                };
                return '<span class="badge ' . $badgeClass . '">' . ucfirst($oilField->status) . '</span>';
            })
            ->addColumn('action', function ($oilField) {
                $actions = '<a href="' . route('oil_fields.show', $oilField->id) . '" class="btn btn-sm btn-info">View</a> ';
                $actions .= '<a href="' . route('oil_fields.edit', $oilField->id) . '" class="btn btn-sm btn-primary">Edit</a> ';
                $actions .= '<form action="' . route('oil_fields.destroy', $oilField->id) . '" method="POST" style="display:inline;">
                    ' . csrf_field() . '
                    ' . method_field('DELETE') . '
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure?\')">Delete</button>
                </form>';
                return $actions;
            })
            ->editColumn('created_at', function ($oilField) {
                return $oilField->created_at->format('Y-m-d H:i');
            })
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }
}

