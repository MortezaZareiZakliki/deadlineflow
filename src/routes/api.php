<?php

use App\Http\Controllers\Api\AuditLogController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\VersionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Protected Routes (Requires Authentication)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);

    // Profile
    Route::prefix('profile')->group(function () {
        Route::put('/info', [AuthController::class, 'updateInfo']);
        Route::put('/password', [AuthController::class, 'updatePassword']);
    });

    // Sessions
    Route::prefix('sessions')->group(function () {
        Route::get('/', [AuthController::class, 'getSessions']);
        Route::delete('/{id}', [AuthController::class, 'deleteSession']);
    });

    // Projects
    Route::apiResource('projects', ProjectController::class);

    // Versions (nested under projects)
    Route::apiResource('projects.versions', VersionController::class)->shallow();
    Route::delete('/versions/{version}', [VersionController::class, 'destroy']);

    // Tasks
    Route::prefix('tasks')->group(function () {
        Route::get('/stats', [TaskController::class, 'stats']);
        Route::get('/upcoming', [TaskController::class, 'upcoming']);
    });
    Route::apiResource('tasks', TaskController::class);

    // Notifications
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/{id}', [NotificationController::class, 'destroy']);
    });

    // Audit Logs
    Route::get('/audit-logs', [AuditLogController::class, 'index']);
});