<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\VxLogController;
use App\Http\Controllers\WellTestController;
use App\Http\Controllers\DashboardController;

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
});

/*
|--------------------------------------------------------------------------
| Vx Log
|--------------------------------------------------------------------------
*/
Route::get('/upload', [VxLogController::class, 'showUploadForm'])->name('upload.form');
Route::post('/process-log', [VxLogController::class, 'processLogFile'])->name('process.log');
Route::get('/chart-data', [VxLogController::class, 'getChartDataForUnit'])->name('chart.data');
Route::post('/tests/store', [VxLogController::class, 'storeTest'])->name('tests.store');

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
