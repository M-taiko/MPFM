<?php

namespace App\Http\Controllers;

use App\Models\PlanTask;
use App\Models\Tower;
use Illuminate\Http\Request;

class TaskArchiveController extends Controller
{
    /**
     * Display completed tasks archive with filters
     */
    public function index(Request $request)
    {
        // Check authorization - supervisors and admin can access
        if (!auth()->check() || !auth()->user()->hasAnyRole(['admin', 'surveillance_supervisor', 'mpfm_supervisor', 'production_supervisor'])) {
            abort(403, 'Unauthorized to access task archive');
        }

        $query = PlanTask::with([
            'monthlyPlan',
            'towerModel',
            'oilString',
            'operator',
            'mpfmTest'
        ])->whereIn('completion_status', ['completed', 'failed'])
          ->orWhere('state', 'completed')
          ->latest('completed_at');

        // Filter by tower if provided
        if ($request->has('tower_id') && $request->tower_id) {
            $query->where('tower_id', $request->tower_id);
        }

        // Filter by status if provided
        if ($request->has('status') && $request->status) {
            if ($request->status === 'completed') {
                $query->where('completion_status', 'completed');
            } elseif ($request->status === 'failed') {
                $query->where('completion_status', 'failed');
            }
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->where('completed_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('completed_at', '<=', $request->date_to . ' 23:59:59');
        }

        $tasks = $query->paginate(20);
        $towers = Tower::orderBy('name')->get();

        return view('task-archive.index', compact('tasks', 'towers'));
    }

    /**
     * View completed task details
     */
    public function show(PlanTask $task)
    {
        // Check authorization - supervisors and admin can access
        if (!auth()->check() || !auth()->user()->hasAnyRole(['admin', 'surveillance_supervisor', 'mpfm_supervisor', 'production_supervisor'])) {
            abort(403, 'Unauthorized');
        }

        $task->load([
            'monthlyPlan',
            'towerModel',
            'oilString',
            'operator',
            'mpfmTest',
            'mpfmTest.wellTest'
        ]);

        return view('task-archive.show', compact('task'));
    }
}
