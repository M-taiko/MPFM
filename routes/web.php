<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\VxLogController;
use App\Http\Controllers\WellTestController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MPFMPlanController;
use App\Http\Controllers\OilFieldController;
use App\Http\Controllers\TowerController;
use App\Http\Controllers\OilStringController;
use App\Http\Controllers\MPFMOperationalPlanController;
use App\Http\Controllers\MPFMTestController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TaskStateController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DepartmentUserController;
use App\Http\Controllers\DepartmentTaskController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskImportController;
use App\Http\Controllers\NotificationController;


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
| Notifications
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('index');
    Route::post('/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('mark-as-read');
    Route::post('/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-as-read');
    Route::delete('/{id}', [NotificationController::class, 'delete'])->name('delete');
    Route::delete('/', [NotificationController::class, 'deleteAll'])->name('delete-all');
});

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
    | Users CRUD Operations
    |--------------------------------------------------------------------------
    */
    Route::resource('users', UserController::class)->names([
        'index' => 'users.index',
        'create' => 'users.create',
        'store' => 'users.store',
        'show' => 'users.show',
        'edit' => 'users.edit',
        'update' => 'users.update',
        'destroy' => 'users.destroy',
    ]);

    /*
    |--------------------------------------------------------------------------
    | Users Role/Permission Management
    |--------------------------------------------------------------------------
    */
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
    Route::get('/mpfm-tests/{mpfmTest}/view', [VxLogController::class, 'viewTest'])->name('mpfm-tests.view');
    Route::post('/mpfm-tests/{mpfmTest}/approve', [VxLogController::class, 'approveTest'])->name('mpfm-tests.approve');
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
Route::get('well-tests/{id}/print', [WellTestController::class, 'print'])
    ->whereNumber('id')
    ->name('well_tests.print');
/** Compare */
Route::get('/well-tests/compare', [WellTestController::class, 'compareForm'])
    ->name('well_tests.compare');

Route::post('/well-tests/compare', [WellTestController::class, 'compare'])
    ->name('well_tests.compare.submit');

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

// MPFM Test History and Analysis - More specific routes BEFORE resource
Route::middleware('auth')->group(function () {
    Route::get('/mpfm-tests/trending-dashboard', [MPFMTestController::class, 'trendingDashboard'])
        ->name('mpfm_tests.trending_dashboard');
    Route::get('/mpfm-tests/string-analysis/{oilString}', [MPFMTestController::class, 'stringAnalysis'])
        ->name('mpfm_tests.string_analysis');
    Route::get('/mpfm-tests/{mpfmTest}/history', [MPFMTestController::class, 'showWithHistory'])
        ->name('mpfm_tests.show_history');
    Route::post('/mpfm-tests/{mpfmTest}/revalidate', [MPFMTestController::class, 'revalidate'])
        ->name('mpfm_tests.revalidate');
    Route::get('/mpfm-tests/{mpfmTest}/download-log', [MPFMTestController::class, 'downloadLog'])
        ->name('mpfm_tests.download_log');
});

Route::resource('mpfm-tests', MPFMTestController::class)->names('mpfm_tests');

// MPFM Test Supervisor Review Actions - Role-based approval workflow
Route::middleware('auth')->group(function () {
    Route::post('/mpfm-tests/{mpfmTest}/approve-by-department', [MPFMTestController::class, 'approveByDepartment'])
        ->name('mpfm_tests.approve_by_department')
        ->middleware('can:approve-test-as-department');

    Route::post('/mpfm-tests/{mpfmTest}/approve-by-surveillance', [MPFMTestController::class, 'approveBySurveillance'])
        ->name('mpfm_tests.approve_by_surveillance')
        ->middleware('can:approve-test-as-surveillance');

    Route::post('/mpfm-tests/{mpfmTest}/reject', [MPFMTestController::class, 'rejectTest'])
        ->name('mpfm_tests.reject')
        ->middleware('can:reject-test');

    Route::post('/mpfm-tests/{mpfmTest}/request-retest', [MPFMTestController::class, 'requestRetest'])
        ->name('mpfm_tests.request_retest')
        ->middleware('can:request-retest');
});

// AJAX routes for dynamic dropdowns
Route::get('/ajax/operational-plans/towers/{oilFieldId}', [MPFMOperationalPlanController::class, 'getTowers'])->name('ajax.operational_plans.towers');
Route::get('/ajax/operational-plans/oil-strings/{towerId}', [MPFMOperationalPlanController::class, 'getOilStrings'])->name('ajax.operational_plans.oil_strings');

/*
|--------------------------------------------------------------------------
| Test Vessel Operations
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('test-vessels/{testVessel}/tests', [\App\Http\Controllers\TestVesselController::class, 'getTests'])
        ->name('test_vessels.get_tests');
    Route::get('/ajax/test-vessels/{testVessel}/info', [\App\Http\Controllers\TestVesselController::class, 'getInfo'])
        ->name('ajax.test_vessels.info');
    Route::resource('test-vessels', \App\Http\Controllers\TestVesselController::class)->names('test_vessels');

    // TV Test Requests
    Route::get('tv-test-requests/{tvTestRequest}/start/{well}', [\App\Http\Controllers\TVTestRequestController::class, 'startTest'])
        ->name('tv_test_requests.start_test');
    Route::resource('tv-test-requests', \App\Http\Controllers\TVTestRequestController::class)
        ->only(['index', 'create', 'store', 'show'])
        ->names('tv_test_requests')
        ->parameters(['tv-test-requests' => 'tvTestRequest']);

    // TV Tests - approval/rejection
    Route::post('tv-tests/{tvTest}/approve', [\App\Http\Controllers\TVTestRequestController::class, 'approveTest'])
        ->name('tv_tests.approve');
    Route::post('tv-tests/{tvTest}/reject', [\App\Http\Controllers\TVTestRequestController::class, 'rejectTest'])
        ->name('tv_tests.reject');
    Route::post('tv-tests/{tvTest}/submit', [\App\Http\Controllers\TestVesselTestController::class, 'submit'])
        ->name('tv_tests.submit');

    Route::get('tv-tests/compare', [\App\Http\Controllers\TestVesselTestController::class, 'compare'])
        ->name('tv_tests.compare');
    Route::get('tv-tests/compare-result', [\App\Http\Controllers\TestVesselTestController::class, 'compareResult'])
        ->name('tv_tests.compare_result');
    Route::resource('tv-tests', \App\Http\Controllers\TestVesselTestController::class)
        ->names('tv_tests')
        ->parameters(['tv-tests' => 'tvTest']);
});

/*
|--------------------------------------------------------------------------
| Wire Line (memory gauge surveys & interventions)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('wireline')->name('wireline.')->group(function () {
    $c = \App\Http\Controllers\WirelineController::class;
    Route::get('/', [$c, 'dashboard'])->name('dashboard');
    Route::get('operations', [$c, 'index'])->name('operations');
    Route::get('operations/create', [$c, 'create'])->name('create');
    Route::post('operations', [$c, 'store'])->name('store');
    Route::get('operations/{operation}', [$c, 'show'])->name('show');
    Route::get('operations/{operation}/edit', [$c, 'edit'])->name('edit');
    Route::put('operations/{operation}', [$c, 'update'])->name('update');
    Route::delete('operations/{operation}', [$c, 'destroy'])->name('destroy');
    Route::post('operations/{operation}/runs', [$c, 'uploadRun'])->name('runs.upload');
    Route::delete('runs/{run}', [$c, 'destroyRun'])->name('runs.destroy');
    Route::post('runs/{run}/reanalyse', [$c, 'reanalyseRun'])->name('runs.reanalyse');
    Route::post('runs/{run}/stops', [$c, 'saveStops'])->name('runs.stops');
});

/*
|--------------------------------------------------------------------------
| Well Integrity (barriers, annulus pressures, inspections, anomalies)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'integrity.access'])->prefix('well-integrity')->name('integrity.')->group(function () {
    $c = \App\Http\Controllers\WellIntegrityController::class;
    Route::get('/', [$c, 'dashboard'])->name('dashboard');
    Route::get('wells', [$c, 'wells'])->name('wells');
    Route::get('anomalies', [$c, 'anomalies'])->name('anomalies');
    Route::get('wells/{well}', [$c, 'show'])->name('show');
    Route::get('wells/{well}/checks/create', [$c, 'createCheck'])->name('checks.create');
    Route::post('wells/{well}/checks', [$c, 'storeCheck'])->name('checks.store');
    Route::get('checks/{check}', [$c, 'showCheck'])->name('checks.show');
    Route::delete('checks/{check}', [$c, 'destroyCheck'])->name('checks.destroy');
    Route::post('wells/{well}/profile', [$c, 'saveProfile'])->name('profile');
    Route::post('wells/{well}/annuli', [$c, 'saveAnnulus'])->name('annulus.save');
    Route::delete('annuli/{annulus}', [$c, 'destroyAnnulus'])->name('annulus.destroy');
    Route::post('wells/{well}/readings', [$c, 'storeReading'])->name('readings.store');
    Route::delete('readings/{reading}', [$c, 'destroyReading'])->name('readings.destroy');
    Route::post('wells/{well}/barriers/template', [$c, 'seedBarriers'])->name('barriers.template');
    Route::post('wells/{well}/barriers', [$c, 'storeElement'])->name('barriers.store');
    Route::delete('barriers/{element}', [$c, 'destroyElement'])->name('barriers.destroy');
    Route::post('barriers/{element}/tests', [$c, 'storeTest'])->name('tests.store');
    Route::delete('tests/{test}', [$c, 'destroyTest'])->name('tests.destroy');
    Route::post('wells/{well}/inspections', [$c, 'storeInspection'])->name('inspections.store');
    Route::delete('inspections/{inspection}', [$c, 'destroyInspection'])->name('inspections.destroy');
    Route::post('wells/{well}/anomalies', [$c, 'storeAnomaly'])->name('anomalies.store');
    Route::put('anomalies/{anomaly}', [$c, 'updateAnomaly'])->name('anomalies.update');
    Route::delete('anomalies/{anomaly}', [$c, 'destroyAnomaly'])->name('anomalies.destroy');
});

/*
|--------------------------------------------------------------------------
| Reports
|--------------------------------------------------------------------------
*/
Route::get('/reports',[ReportController::class, 'index'])->name('reports.index');
Route::get('/reports/operator-performance', [ReportController::class, 'operatorPerformance'])->name('reports.operator_performance');
Route::get('/reports/mpfm-tests-count', [ReportController::class, 'mpfmTestsCount'])->name('reports.mpfm_tests_count');
Route::get('/reports/tower-efficiency', [ReportController::class, 'towerEfficiency'])->name('reports.tower_efficiency');
Route::get('/reports/field-production-summary', [ReportController::class, 'fieldProductionSummary'])->name('reports.field_production_summary');
Route::get('/reports/date-shift', [ReportController::class, 'dateShift'])->name('reports.date_shift');

/*
|--------------------------------------------------------------------------
| Daily Plans - DEPRECATED (Use Unified Task System instead - Phase 2)
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\SupervisorDashboardController;
use App\Http\Controllers\OperatorDashboardController;
use App\Http\Controllers\MonthlyPlanController;
use App\Http\Controllers\MPFMSupervisorController;
use App\Http\Controllers\SurveillanceSupervisorController;
use App\Http\Controllers\TaskArchiveController;
use App\Http\Controllers\TaskCreationController;

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
        // More specific routes first
        Route::get('/{monthlyPlan}/tasks/create', [MonthlyPlanController::class, 'createTask'])->name('tasks.create');
        Route::get('/{monthlyPlan}/tasks/{task}', [MonthlyPlanController::class, 'taskDetails'])->name('tasks.show');
        Route::get('/{monthlyPlan}/tasks/{task}/details', [MonthlyPlanController::class, 'getTaskDetails'])->name('tasks.details');
        Route::post('/{monthlyPlan}/tasks', [MonthlyPlanController::class, 'storeTask'])->name('tasks.store');
        Route::delete('/{monthlyPlan}/tasks/{task}', [MonthlyPlanController::class, 'deleteTask'])->name('tasks.destroy');

        // Submit for review
        Route::post('/{monthlyPlan}/submit', [MonthlyPlanController::class, 'submitForReview'])->name('submit');

        // AJAX endpoints for cascading dropdowns
        Route::get('/api/wells-by-tower/{tower}', [MonthlyPlanController::class, 'getWellsByTower'])->name('api.wells-by-tower');
        Route::get('/api/strings-by-well', [MonthlyPlanController::class, 'getStringsByWell'])->name('api.strings-by-well');
        Route::get('/api/department-fields/{departmentId}', [MonthlyPlanController::class, 'getDepartmentFields'])->name('api.department-fields');
    });

    // Task Archive Routes - View completed tasks
    Route::prefix('task-archive')->name('task-archive.')->group(function () {
        Route::get('/', [TaskArchiveController::class, 'index'])->name('index');
        Route::get('/{task}', [TaskArchiveController::class, 'show'])->name('show');
    });

    // Surveillance Supervisor Routes - Review and Approve
    Route::prefix('surveillance')->name('surveillance.')->group(function () {
        Route::get('/dashboard', [SurveillanceSupervisorController::class, 'dashboard'])->name('dashboard');
        Route::get('/tasks/{task}/view', [SurveillanceSupervisorController::class, 'viewTask'])->name('view-task');
        Route::get('/', [SurveillanceSupervisorController::class, 'index'])->name('index');
        Route::get('/plans/{monthlyPlan}/review', [SurveillanceSupervisorController::class, 'reviewPlan'])->name('review');
        Route::get('/plans/{monthlyPlan}/tasks/{planTask}/details', [SurveillanceSupervisorController::class, 'getTaskDetails'])->name('task-details');
        Route::post('/plans/{monthlyPlan}/tasks/{planTask}/approve', [SurveillanceSupervisorController::class, 'approveTask'])->name('approve-task');
        Route::post('/plans/{monthlyPlan}/tasks/{planTask}/reject', [SurveillanceSupervisorController::class, 'rejectTask'])->name('reject-task');
        Route::post('/plans/{monthlyPlan}/approve', [SurveillanceSupervisorController::class, 'approvePlan'])->name('approve');
        Route::post('/plans/{monthlyPlan}/reject', [SurveillanceSupervisorController::class, 'rejectPlan'])->name('reject');

        // AJAX endpoints for task assignment modal (legacy PlanTask)
        Route::get('/tasks/{task}/assign-modal', [SurveillanceSupervisorController::class, 'getTaskAssignmentModal'])->name('task-assign-modal');
        Route::post('/tasks/{task}/assign-to-department/{department}', [SurveillanceSupervisorController::class, 'assignTaskToDepartment'])->name('assign-task-to-department');

        // New Task model — approve/reject from surveillance
        Route::post('/new-tasks/{task}/approve', [SurveillanceSupervisorController::class, 'approveNewTask'])->name('new-task.approve');
        Route::post('/new-tasks/{task}/reject',  [SurveillanceSupervisorController::class, 'rejectNewTask'])->name('new-task.reject');
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

    // MPFM Validation Rules Management (admin/supervisor only)
    Route::middleware('permission:manage-validation-rules')->group(function () {
        Route::resource('validation-rules', \App\Http\Controllers\ValidationRuleController::class);
    });

    // Test Approval with Validation
    Route::middleware('permission:review-test')->group(function () {
        Route::get('/supervisor/test-approval/{mpfmTest}', [VxLogController::class, 'showTestApproval'])
            ->name('supervisor.test_approval');
        Route::post('/supervisor/approve-test/{mpfmTest}', [VxLogController::class, 'approveTest'])
            ->name('vx_log.approve_test');
    });

    /*
    |--------------------------------------------------------------------------
    | Department Management
    |--------------------------------------------------------------------------
    */
    Route::prefix('departments')->name('departments.')->group(function () {
        Route::get('/{department}/dashboard', [DepartmentController::class, 'dashboard'])->name('dashboard');
        Route::get('/{department}/tasks', [DepartmentController::class, 'tasks'])->name('tasks');
        Route::get('/{department}/operators', [DepartmentController::class, 'operators'])->name('operators');
        Route::get('/{department}/reports', [DepartmentController::class, 'reports'])->name('reports');
        Route::post('/{department}/assign-operator', [DepartmentController::class, 'assignOperator'])->name('assign-operator');
        Route::post('/{department}/tasks/{task}/assign-operator', [DepartmentController::class, 'assignTaskToOperator'])->name('assign-task-operator');

        // Department User Management (Supervisor/Admin only)
        Route::resource('{department}/users', DepartmentUserController::class)->parameters([
            'department' => 'department',
            'users' => 'user'
        ])->names([
            'index' => 'users.index',
            'create' => 'users.create',
            'store' => 'users.store',
            'show' => 'users.show',
            'edit' => 'users.edit',
            'update' => 'users.update',
            'destroy' => 'users.destroy',
        ]);
    });

    /*
    |--------------------------------------------------------------------------
    | Unified Task Management System (Phase 1 Enterprise Refactoring)
    |--------------------------------------------------------------------------
    */
    Route::prefix('tasks')->name('tasks.')->group(function () {
        // Main CRUD operations
        Route::get('/', [TaskController::class, 'index'])->name('index');
        Route::get('/create', [TaskController::class, 'create'])->name('create');

        // Bulk import from Excel
        Route::get('/import', [TaskImportController::class, 'index'])->name('import');
        Route::post('/import', [TaskImportController::class, 'store'])->name('import.store');
        Route::get('/import/template', [TaskImportController::class, 'downloadTemplate'])->name('import.template');

        // Dynamic task creation with cascading dropdowns (MUST be before /{task} route)
        Route::get('/create-advanced', [TaskCreationController::class, 'create'])->name('create-advanced');
        Route::post('/store-advanced', [TaskCreationController::class, 'store'])->name('store-advanced');

        Route::post('/', [TaskController::class, 'store'])->name('store');

        // Supervisor task view (must be before /{task} route to avoid conflicts)
        Route::get('/{task}/supervisor-view', [TaskController::class, 'supervisorView'])->name('supervisor-view');

        Route::get('/{task}', [TaskController::class, 'show'])->name('show');
        Route::get('/{task}/edit', [TaskController::class, 'edit'])->name('edit');
        Route::put('/{task}', [TaskController::class, 'update'])->name('update');
        Route::delete('/{task}', [TaskController::class, 'destroy'])->name('destroy');

        // State transition
        Route::post('/{task}/transition', [TaskController::class, 'transition'])->name('transition');

        // Assign operator and transition to assigned_to_department
        Route::post('/{task}/assign', [TaskController::class, 'assign'])->name('assign');

        // AJAX endpoints for dynamic form population
        Route::post('/api/get-oil-strings', [TaskCreationController::class, 'getOilStrings'])->name('get-oil-strings');
        Route::post('/api/get-department-requirements', [TaskCreationController::class, 'getDepartmentRequirements'])->name('get-department-requirements');
        Route::get('/api/department-fields/{code}', [DepartmentTaskController::class, 'getFields'])->name('api.department-fields');
        Route::post('/api/get-test-type-details', [TaskCreationController::class, 'getTestTypeDetails'])->name('get-test-type-details');
    });

    /*
    |--------------------------------------------------------------------------
    | Department Task Management
    |--------------------------------------------------------------------------
    */
    Route::prefix('department-tasks')->name('department-tasks.')->group(function () {
        Route::get('/{department_id}', [DepartmentTaskController::class, 'index'])->name('index');

        // AJAX endpoints for task assignment
        Route::post('/api/get-assignment-modal', [DepartmentTaskController::class, 'getAssignmentModal'])->name('get-assignment-modal');
        Route::post('/api/assign-operator', [DepartmentTaskController::class, 'assignOperator'])->name('assign-operator');
        Route::post('/api/remove-operator', [DepartmentTaskController::class, 'removeOperator'])->name('remove-operator');
    });

    /*
    |--------------------------------------------------------------------------
    | Department-Specific Dashboards & Task Completion
    |--------------------------------------------------------------------------
    */
    Route::prefix('dept')->name('dept.')->group(function () {
        // Dashboard for each department by code (MPFM, WIRELINE, etc.)
        Route::get('/{code}/dashboard', [DepartmentTaskController::class, 'dashboard'])->name('dashboard');
        Route::get('/{code}/tasks',     [DepartmentTaskController::class, 'deptTasks'])->name('tasks');

        // Task completion flow
        Route::get('/complete/{planTask}',   [DepartmentTaskController::class, 'showCompletion'])->name('complete');
        Route::post('/complete/{planTask}',  [DepartmentTaskController::class, 'storeCompletion'])->name('complete.store');
        Route::get('/result/{planTask}',     [DepartmentTaskController::class, 'completionResult'])->name('result');
    });
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
