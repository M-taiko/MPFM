<?php

namespace App\Http\Controllers;

use App\Models\PlanTask;
use Illuminate\Http\Request;

class PlanTaskController extends Controller
{
    /**
     * Store a newly created task in storage
     */
    public function store(Request $request, $dailyPlan)
    {
        // This is a placeholder for now
        // Tasks are primarily created through MonthlyPlan
        return back()->with('info', 'Task management through monthly plans.');
    }

    /**
     * Show the form for editing the specified task
     */
    public function edit(PlanTask $task)
    {
        return view('plan_tasks.edit', compact('task'));
    }

    /**
     * Update the specified task in storage
     */
    public function update(Request $request, PlanTask $task)
    {
        $validated = $request->validate([
            'description' => 'string|nullable',
            'priority' => 'in:low,normal,high,urgent',
        ]);

        $task->update($validated);

        return back()->with('success', 'Task updated successfully.');
    }

    /**
     * Remove the specified task from storage
     */
    public function destroy(PlanTask $task)
    {
        $task->delete();

        return back()->with('success', 'Task deleted successfully.');
    }
}
