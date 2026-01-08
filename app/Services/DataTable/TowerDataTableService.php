<?php

namespace App\Services\DataTable;

use App\Models\Tower;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;

class TowerDataTableService
{
    /**
     * Get DataTable query for Towers
     */
    public function getDataTable(Request $request)
    {
        $query = Tower::with('oilField');

        // Apply filters
        if ($request->has('oil_field_id') && $request->oil_field_id) {
            $query->where('oil_field_id', $request->oil_field_id);
        }

        return DataTables::of($query)
            ->addColumn('oil_field_name', function ($tower) {
                return $tower->oilField->name ?? '-';
            })
            ->addColumn('oil_strings_count', function ($tower) {
                return $tower->oilStrings()->count();
            })
            ->addColumn('status_badge', function ($tower) {
                $badgeClass = match($tower->status) {
                    'active' => 'badge-success',
                    'inactive' => 'badge-secondary',
                    'maintenance' => 'badge-warning',
                    default => 'badge-secondary',
                };
                return '<span class="badge ' . $badgeClass . '">' . ucfirst($tower->status) . '</span>';
            })
            ->addColumn('action', function ($tower) {
                $actions = '<a href="' . route('towers.show', $tower->id) . '" class="btn btn-sm btn-info">View</a> ';
                $actions .= '<a href="' . route('towers.edit', $tower->id) . '" class="btn btn-sm btn-primary">Edit</a> ';
                $actions .= '<form action="' . route('towers.destroy', $tower->id) . '" method="POST" style="display:inline;">
                    ' . csrf_field() . '
                    ' . method_field('DELETE') . '
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure?\')">Delete</button>
                </form>';
                return $actions;
            })
            ->editColumn('created_at', function ($tower) {
                return $tower->created_at->format('Y-m-d H:i');
            })
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }
}

