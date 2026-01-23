<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\VxLogController;
use App\Http\Controllers\WellTestController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MPFMPlanController;
use App\Http\Controllers\OperatorPlanController;
use App\Http\Controllers\OilFieldController;
use App\Http\Controllers\TowerController;
use App\Http\Controllers\OilStringController;
use App\Http\Controllers\MPFMOperationalPlanController;
use App\Http\Controllers\MPFMTestController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TaskStateController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Roles
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::get('/roles', [RolePermissionController::class, 'indexRoles'])->name('roles.index');
    Route::get('/roles/create', [RolePermissionController::class, 'createRole'])->name('roles.create');
    Route::post('/roles', [RolePermissionController::class, 'storeRole'])->name('roles.store');
    Route::get('/roles/{role}/edit', [RolePermissionController::class, 'editRole'])->name('roles.edit');
    Route::put('/roles/{role}', [RolePermissionController::class, 'updateRole'])->name('roles.update');
    Route::delete('/roles/{role}', [RolePermissionController::class, 'destroyRole'])->name('roles.destroy');

    /*
    |--------------------------------------------------------------------------
    | Permissions
    |--------------------------------------------------------------------------
    */
    Route::get('/permissions', [RolePermissionController::class, 'indexPermissions'])->name('permissions.index');
    Route::get('/permissions/create', [RolePermissionController::class, 'createPermission'])->name('permissions.create');
    Route::post('/permissions', [RolePermissionController::class, 'storePermission'])->name('permissions.store');
    Route::get('/permissions/{permission}/edit', [RolePermissionController::class, 'editPermission'])->name('permissions.edit');
    Route::put('/permissions/{permission}', [RolePermissionController::class, 'updatePermission'])->name('permissions.update');
    Route::delete('/permissions/{permission}', [RolePermissionController::class, 'destroyPermission'])->name('permissions.destroy');

    /*
    |--------------------------------------------------------------------------
    | Users
    |--------------------------------------------------------------------------
    */
    Route::get('/users', [RolePermissionController::class, 'indexUsers'])->name('users.index');
    Route::post('/users/{user}/assign-role', [RolePermissionController::class, 'assignRole'])->name('users.assign-role');
    Route::post('/users/{user}/assign-permission', [RolePermissionController::class, 'assignPermission'])->name('users.assign-permission');
    Route::delete('/users/{user}/remove-role/{roleName}', [RolePermissionController::class, 'removeRole'])->name('users.remove-role');
    Route::delete('/users/{user}/remove-permission/{permissionName}', [RolePermissionController::class, 'removePermission'])->name('users.remove-permission');

    /*
    |--------------------------------------------------------------------------
    | API Endpoints for Role/Permission Management
    |--------------------------------------------------------------------------
    */
    Route::get('/api/role-permissions/{role}', [RolePermissionController::class, 'getRolePermissions']);
    Route::put('/roles/{role}/permissions', [RolePermissionController::class, 'updateRolePermissions'])->name('roles.permissions.update');

    /*
    |--------------------------------------------------------------------------
    | Reports
    |--------------------------------------------------------------------------
    */
    Route::get('/reports/job-officer', [DashboardController::class, 'jobOfficerReport'])->name('reports.job_officer');
    Route::get('/reports/reservoir-engineer', [DashboardController::class, 'reservoirEngineerReport'])->name('reports.reservoir_engineer');
    Route::get('/reports/production-supervisor', [DashboardController::class, 'productionSupervisorReport'])->name('reports.production_supervisor');
    Route::get('/reports/mpfm-supervisor', [DashboardController::class, 'mpfmSupervisorReport'])->name('reports.mpfm_supervisor');
    Route::get('/reports/mpfm-operator', [DashboardController::class, 'mpfmOperatorReport'])->name('reports.mpfm_operator');
});

/*
|--------------------------------------------------------------------------
| Vx Log
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/upload', [VxLogController::class, 'showUploadForm'])->name('upload.form');
    Route::post('/process-log', [VxLogController::class, 'processLogFile'])->name('process.log');
    Route::get('/chart-data', [VxLogController::class, 'getChartDataForUnit'])->name('chart.data');
    Route::post('/tests/store', [VxLogController::class, 'storeTest'])->name('tests.store');
});

/*
|--------------------------------------------------------------------------
| Well Tests  ✅ (الجزء المهم)
|--------------------------------------------------------------------------
*/

// Well Tests
Route::get('well-tests/datatables', [WellTestController::class, 'getDatatables'])
    ->name('well_tests.datatables');

Route::resource('well-tests', WellTestController::class)
    ->names('well_tests');
/** Compare */
Route::get('/well-tests/compare', [WellTestController::class, 'compareForm'])
    ->name('well_tests.compare');

Route::post('/well-tests/compare', [WellTestController::class, 'compare'])
    ->name('well_tests.compare.submit');

/*
|--------------------------------------------------------------------------
| Operator Plans
|--------------------------------------------------------------------------
*/
Route::prefix('operator-plans')->group(function () {
    Route::get('well-test/{wellTestId}', [OperatorPlanController::class, 'index'])->name('operator_plans.index');
    Route::get('well-test/{wellTestId}/create', [OperatorPlanController::class, 'create'])->name('operator_plans.create');
    Route::post('well-test/{wellTestId}', [OperatorPlanController::class, 'store'])->name('operator_plans.store');
    Route::get('{id}', [OperatorPlanController::class, 'show'])->name('operator_plans.show');
    Route::get('{id}/edit', [OperatorPlanController::class, 'edit'])->name('operator_plans.edit');
    Route::put('{id}', [OperatorPlanController::class, 'update'])->name('operator_plans.update');
    Route::delete('{id}', [OperatorPlanController::class, 'destroy'])->name('operator_plans.destroy');

    // AJAX routes for operator plan operations
    Route::post('{id}/complete', [OperatorPlanController::class, 'markCompleted'])->name('operator_plans.complete');
    Route::post('{id}/pending', [OperatorPlanController::class, 'markPending'])->name('operator_plans.pending');
    Route::post('well-test/{wellTestId}/reorder', [OperatorPlanController::class, 'reorder'])->name('operator_plans.reorder');
    Route::get('well-test/{wellTestId}/next-step', [OperatorPlanController::class, 'getNextStep'])->name('operator_plans.next_step');
    Route::get('well-test/{wellTestId}/statistics', [OperatorPlanController::class, 'getStatistics'])->name('operator_plans.statistics');
    Route::get('well-test/{wellTestId}/completed-steps', [OperatorPlanController::class, 'getCompletedSteps'])->name('operator_plans.completed_steps');
    Route::get('well-test/{wellTestId}/pending-steps', [OperatorPlanController::class, 'getPendingSteps'])->name('operator_plans.pending_steps');
});

/*
|--------------------------------------------------------------------------
| MPFM Plans
|--------------------------------------------------------------------------
*/
Route::resource('mpfm-plans', MPFMPlanController::class)->names('mpfm_plans');
Route::post('/mpfm-plans/{planId}/steps', [MPFMPlanController::class, 'addStep'])->name('mpfm_plans.steps.add');
Route::post('/mpfm-steps/{stepId}/readings', [MPFMPlanController::class, 'addReading'])->name('mpfm_steps.readings.add');
Route::get('/mpfm-plans/{id}/report', [MPFMPlanController::class, 'generateReport'])->name('mpfm_plans.report');

/*
|--------------------------------------------------------------------------
| Core Asset Management
|--------------------------------------------------------------------------
*/
Route::resource('oil-fields', OilFieldController::class)->names('oil_fields');
Route::resource('towers', TowerController::class)->names('towers');
Route::resource('oil-strings', OilStringController::class)->names('oil_strings');

/*
|--------------------------------------------------------------------------
| MPFM Operations
|--------------------------------------------------------------------------
*/
Route::resource('mpfm-operational-plans', MPFMOperationalPlanController::class)->names('mpfm_operational_plans');
Route::resource('mpfm-tests', MPFMTestController::class)->names('mpfm_tests');

// AJAX routes for dynamic dropdowns
Route::get('/ajax/operational-plans/towers/{oilFieldId}', [MPFMOperationalPlanController::class, 'getTowers'])->name('ajax.operational_plans.towers');
Route::get('/ajax/operational-plans/oil-strings/{towerId}', [MPFMOperationalPlanController::class, 'getOilStrings'])->name('ajax.operational_plans.oil_strings');

/*
|--------------------------------------------------------------------------
| Reports
|--------------------------------------------------------------------------
*/
Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
Route::get('/reports/operator-performance', [ReportController::class, 'operatorPerformance'])->name('reports.operator_performance');
Route::get('/reports/mpfm-tests-count', [ReportController::class, 'mpfmTestsCount'])->name('reports.mpfm_tests_count');
Route::get('/reports/tower-efficiency', [ReportController::class, 'towerEfficiency'])->name('reports.tower_efficiency');
Route::get('/reports/field-production-summary', [ReportController::class, 'fieldProductionSummary'])->name('reports.field_production_summary');
Route::get('/reports/date-shift', [ReportController::class, 'dateShift'])->name('reports.date_shift');

/*
|--------------------------------------------------------------------------
| Daily Plans (Multi-Operator Workflow)
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\SupervisorDashboardController;
use App\Http\Controllers\OperatorDashboardController;
use App\Http\Controllers\DailyPlanController;
use App\Http\Controllers\PlanTaskController;
use App\Http\Controllers\MonthlyPlanController;
use App\Http\Controllers\MPFMSupervisorController;

Route::middleware('auth')->group(function () {
    // Supervisor Routes
    Route::prefix('supervisor')->name('supervisor.')->group(function () {
        Route::get('/dashboard', [SupervisorDashboardController::class, 'index'])->name('dashboard');
        Route::get('/calendar', [SupervisorDashboardController::class, 'calendar'])->name('calendar');
    });

    // Operator Routes
    Route::prefix('operator')->name('operator.')->group(function () {
        Route::get('/dashboard', [OperatorDashboardController::class, 'index'])->name('dashboard');
        Route::get('/tasks', [OperatorDashboardController::class, 'tasks'])->name('tasks');
        Route::get('/calendar', [OperatorDashboardController::class, 'calendar'])->name('calendar');
        Route::get('/history', [OperatorDashboardController::class, 'history'])->name('history');
    });

    // Daily Plans Resource
    Route::resource('daily-plans', DailyPlanController::class)->except(['index']);
    Route::get('daily-plans', [DailyPlanController::class, 'index'])->name('daily-plans.index');

    // Daily Plan Actions
    Route::post('daily-plans/{dailyPlan}/activate', [DailyPlanController::class, 'activate'])->name('daily-plans.activate');
    Route::post('daily-plans/{dailyPlan}/cancel', [DailyPlanController::class, 'cancel'])->name('daily-plans.cancel');
    Route::post('daily-plans/{dailyPlan}/clone', [DailyPlanController::class, 'clone'])->name('daily-plans.clone');

    // Plan Import from plan.txt
    Route::get('daily-plans/import/form', [DailyPlanController::class, 'showImportForm'])->name('daily-plans.import.form');
    Route::post('daily-plans/import', [DailyPlanController::class, 'importFromPlanTxt'])->name('daily-plans.import');

    // Plan Approval (MPFM Supervisor)
    Route::get('daily-plans/{dailyPlan}/approve/form', [DailyPlanController::class, 'showApprovalForm'])->name('daily-plans.approve.form');
    Route::post('daily-plans/{dailyPlan}/approve', [DailyPlanController::class, 'approve'])->name('daily-plans.approve');
    Route::post('daily-plans/{dailyPlan}/reject', [DailyPlanController::class, 'reject'])->name('daily-plans.reject');

    // Plan Tasks (will be created next)
    Route::post('daily-plans/{dailyPlan}/tasks', [PlanTaskController::class, 'store'])->name('plan-tasks.store');
    Route::get('plan-tasks/{task}/edit', [PlanTaskController::class, 'edit'])->name('plan-tasks.edit');
    Route::put('plan-tasks/{task}', [PlanTaskController::class, 'update'])->name('plan-tasks.update');
    Route::delete('plan-tasks/{task}', [PlanTaskController::class, 'destroy'])->name('plan-tasks.destroy');

    // API Routes for AJAX calls
    Route::get('/api/oil-fields/{oilField}/towers', [App\Http\Controllers\Api\OilFieldApiController::class, 'getTowers']);
    Route::get('/api/oil-fields/{oilField}/oil-strings', [App\Http\Controllers\Api\OilFieldApiController::class, 'getOilStrings']);
});

/*
|--------------------------------------------------------------------------
| Monthly Plans System (Job Officer → MPFM Supervisor → Operator)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    // Job Officer Routes - Monthly Plan Management
    Route::prefix('monthly-plans')->name('monthly-plans.')->group(function () {
        Route::get('/', [MonthlyPlanController::class, 'index'])->name('index');
        Route::get('/create', [MonthlyPlanController::class, 'create'])->name('create');
        Route::post('/', [MonthlyPlanController::class, 'store'])->name('store');
        Route::get('/{monthlyPlan}', [MonthlyPlanController::class, 'show'])->name('show');
        Route::get('/{monthlyPlan}/edit', [MonthlyPlanController::class, 'edit'])->name('edit');
        Route::put('/{monthlyPlan}', [MonthlyPlanController::class, 'update'])->name('update');
        Route::delete('/{monthlyPlan}', [MonthlyPlanController::class, 'destroy'])->name('destroy');

        // Add tasks to plan
        Route::get('/{monthlyPlan}/tasks/create', [MonthlyPlanController::class, 'createTask'])->name('tasks.create');
        Route::post('/{monthlyPlan}/tasks', [MonthlyPlanController::class, 'storeTask'])->name('tasks.store');
        Route::delete('/{monthlyPlan}/tasks/{task}', [MonthlyPlanController::class, 'deleteTask'])->name('tasks.destroy');

        // Submit for review
        Route::post('/{monthlyPlan}/submit', [MonthlyPlanController::class, 'submitForReview'])->name('submit');

        // AJAX endpoints for cascading dropdowns
        Route::get('/api/wells-by-tower/{tower}', [MonthlyPlanController::class, 'getWellsByTower'])->name('api.wells-by-tower');
        Route::get('/api/strings-by-well', [MonthlyPlanController::class, 'getStringsByWell'])->name('api.strings-by-well');
    });

    // MPFM Supervisor Routes - Review and Assign
    Route::prefix('mpfm-supervisor')->name('mpfm-supervisor.')->group(function () {
        Route::get('/dashboard', [MPFMSupervisorController::class, 'dashboard'])->name('dashboard');

        // Plan Review
        Route::get('/plans/{monthlyPlan}/review', [MPFMSupervisorController::class, 'reviewPlan'])->name('review');
        Route::post('/plans/{monthlyPlan}/approve', [MPFMSupervisorController::class, 'approvePlan'])->name('approve');
        Route::post('/plans/{monthlyPlan}/reject', [MPFMSupervisorController::class, 'rejectPlan'])->name('reject');

        // Task Assignment
        Route::get('/plans/{monthlyPlan}/assign', [MPFMSupervisorController::class, 'assignmentView'])->name('assign');
        Route::post('/tasks/{planTask}/assign-operator', [MPFMSupervisorController::class, 'assignOperator'])->name('assign-operator');
        Route::post('/tasks/bulk-assign', [MPFMSupervisorController::class, 'bulkAssignOperator'])->name('bulk-assign');
        Route::post('/tasks/{task}/assign', [MPFMSupervisorController::class, 'assignTask'])->name('assign-task');
        Route::post('/tasks/{task}/add-operator', [MPFMSupervisorController::class, 'addOperatorToTask'])->name('add-operator');
        Route::post('/tasks/{task}/remove-operator', [MPFMSupervisorController::class, 'removeOperatorFromTask'])->name('remove-operator');

        // Task Management
        Route::put('/tasks/{planTask}', [MPFMSupervisorController::class, 'updateTask'])->name('update-task');

        // Progress Monitoring
        Route::get('/plans/{monthlyPlan}/monitor', [MPFMSupervisorController::class, 'monitorProgress'])->name('monitor');

        // Monthly tasks view with filters
        Route::get('/monthly-tasks', [OperatorDashboardController::class, 'monthlyTasks'])->name('monthly-tasks');
    });

    // Operator Routes - View and Execute Tasks
    Route::prefix('operator')->name('operator.')->group(function () {
        // Dashboard already exists, update it
        Route::get('/dashboard', [OperatorDashboardController::class, 'index'])->name('dashboard');
        Route::get('/tasks', [OperatorDashboardController::class, 'tasks'])->name('tasks');
        Route::get('/tasks/{planTask}', [OperatorDashboardController::class, 'showTask'])->name('tasks.show');
        Route::post('/tasks/{planTask}/start', [OperatorDashboardController::class, 'startTask'])->name('tasks.start');

        // Upload log with auto-fill
        Route::get('/tasks/{planTask}/upload', [OperatorDashboardController::class, 'uploadLog'])->name('tasks.upload');
        Route::post('/tasks/{planTask}/upload', [OperatorDashboardController::class, 'storeUploadLog'])->name('tasks.upload.store');

        // Monthly tasks view with filters
        Route::get('/monthly-tasks', [OperatorDashboardController::class, 'monthlyTasks'])->name('monthly-tasks');

        // Calendar and History
        Route::get('/calendar', [OperatorDashboardController::class, 'calendar'])->name('calendar');
        Route::get('/history', [OperatorDashboardController::class, 'history'])->name('history');
    });

    // ============================================================
    // Task State Management Routes (State Machine)
    // ============================================================
    Route::prefix('tasks/{task}/state')->name('tasks.state.')->group(function () {
        // Job Officer Actions
        Route::post('submit-to-surveillance', [TaskStateController::class, 'submitToSurveillance'])
            ->name('submit-to-surveillance');

        // Surveillance Supervisor Actions
        Route::post('assign-to-mpfm', [TaskStateController::class, 'assignToMPFM'])
            ->name('assign-to-mpfm');
        Route::post('send-back-to-draft', [TaskStateController::class, 'sendBackToDraft'])
            ->name('send-back-to-draft');

        // Operator Actions
        Route::post('start', [TaskStateController::class, 'startTask'])
            ->name('start');

        // MPFM Supervisor Actions
        Route::post('review-mpfm', [TaskStateController::class, 'reviewAsMPFM'])
            ->name('review-mpfm');

        // Surveillance Supervisor Review
        Route::post('review-surveillance', [TaskStateController::class, 'reviewAsSurveillance'])
            ->name('review-surveillance');

        // Job Officer Final Approval
        Route::post('final-approve', [TaskStateController::class, 'finalApprove'])
            ->name('final-approve');

        // View State History
        Route::get('history', [TaskStateController::class, 'showHistory'])
            ->name('history');
    });

    // Bulk Operations
    Route::post('tasks/bulk-transition', [TaskStateController::class, 'bulkTransition'])
        ->name('tasks.bulk-transition');

    // Workflow Dashboard
    Route::get('workflow/dashboard', [TaskStateController::class, 'dashboard'])
        ->name('workflow.dashboard');
});

/*
|--------------------------------------------------------------------------
| Test Route
|--------------------------------------------------------------------------
*/
Route::get('/test-role', function () {
    $user = \App\Models\User::first();

    return response()->json([
        'user' => $user?->name,
        'roles' => $user?->roles->pluck('name'),
        'permissions' => $user?->permissions->pluck('name'),
        'has_admin_role' => $user?->hasRole('admin'),
        'can_view_users' => $user?->can('view-users'),
    ]);
})->name('test.role');
