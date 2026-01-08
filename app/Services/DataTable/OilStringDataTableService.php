<?php

namespace App\Services\DataTable;

use App\Models\OilString;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;

class OilStringDataTableService
{
    /**
     * Get DataTable query for Oil Strings
     */
    public function getDataTable(Request $request)
    {
        $query = OilString::with(['tower.oilField']);

        // Apply filters
        if ($request->has('tower_id') && $request->tower_id) {
            $query->where('tower_id', $request->tower_id);
        }

        if ($request->has('oil_field_id') && $request->oil_field_id) {
            $query->forOilField($request->oil_field_id);
        }

        return DataTables::of($query)
            ->addColumn('tower_name', function ($oilString) {
                return $oilString->tower->name ?? '-';
            })
            ->addColumn('oil_field_name', function ($oilString) {
                return $oilString->tower->oilField->name ?? '-';
            })
            ->addColumn('status_badge', function ($oilString) {
                $badgeClass = match($oilString->status) {
                    'active' => 'badge-success',
                    'inactive' => 'badge-secondary',
                    'maintenance' => 'badge-warning',
                    default => 'badge-secondary',
                };
                return '<span class="badge ' . $badgeClass . '">' . ucfirst($oilString->status) . '</span>';
            })
            ->addColumn('action', function ($oilString) {
                $actions = '<a href="' . route('oil_strings.show', $oilString->id) . '" class="btn btn-sm btn-info">View</a> ';
                $actions .= '<a href="' . route('oil_strings.edit', $oilString->id) . '" class="btn btn-sm btn-primary">Edit</a> ';
                $actions .= '<form action="' . route('oil_strings.destroy', $oilString->id) . '" method="POST" style="display:inline;">
                    ' . csrf_field() . '
                    ' . method_field('DELETE') . '
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure?\')">Delete</button>
                </form>';
                return $actions;
            })
            ->editColumn('created_at', function ($oilString) {
                return $oilString->created_at->format('Y-m-d H:i');
            })
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }
}

