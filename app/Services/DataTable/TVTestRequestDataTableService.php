<?php

namespace App\Services\DataTable;

use App\Models\TVTestRequest;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;

class TVTestRequestDataTableService
{
    /**
     * Get DataTable query for TV Test Requests
     */
    public function getDataTable(Request $request)
    {
        $query = TVTestRequest::query()->with('testVessel', 'tower', 'oilField', 'requestedBy', 'wells');

        // Filter by oil field
        $oilFieldId = $request->get('oil_field_id');
        if ($oilFieldId && trim($oilFieldId) !== '') {
            $query->where('oil_field_id', trim($oilFieldId));
        }

        // Filter by status
        $status = $request->get('status');
        if ($status && trim($status) !== '') {
            $query->where('status', trim($status));
        }

        return DataTables::of($query)
            ->addColumn('test_date', function ($request) {
                return $request->test_date->format('Y-m-d');
            })
            ->addColumn('vessel_name', function ($request) {
                return $request->testVessel?->name ?? '-';
            })
            ->addColumn('tower_name', function ($request) {
                return $request->tower?->name ?? '-';
            })
            ->addColumn('oil_field_name', function ($request) {
                return $request->oilField?->name ?? '-';
            })
            ->addColumn('requested_by', function ($request) {
                return $request->requestedBy?->name ?? '-';
            })
            ->addColumn('wells_count', function ($request) {
                return $request->wells->count();
            })
            ->addColumn('pending_count', function ($request) {
                return $request->wells->where('status', 'pending')->count();
            })
            ->addColumn('status_badge', function ($request) {
                $badgeClass = match($request->status) {
                    'open' => 'badge-success',
                    'in_progress' => 'badge-warning',
                    'completed' => 'badge-info',
                    'cancelled' => 'badge-secondary',
                    default => 'badge-secondary',
                };
                return '<span class="badge ' . $badgeClass . '">' . ucfirst($request->status) . '</span>';
            })
            ->addColumn('action', function ($request) {
                $actions = '<a href="' . route('tv_test_requests.show', $request->id) . '" class="btn btn-sm btn-info">View</a>';
                return $actions;
            })
            ->editColumn('created_at', function ($request) {
                return $request->created_at->format('Y-m-d H:i');
            })
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }
}
