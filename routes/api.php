<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SiteController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\VisitController;
use App\Http\Controllers\Api\VisitConfirmationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\SiteManagementController;
use App\Http\Controllers\DepartmentManagementController;
use App\Http\Controllers\SecurityController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\PasswordResetController;

// Lightweight health check (no DB requirement)
Route::get('health', function () {
    return response()->json([
        'status' => 'ok',
        'time' => now()->toISOString(),
    ]);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// API Routes for Visitors App
Route::apiResource('sites', SiteController::class);
Route::apiResource('departments', DepartmentController::class);
Route::apiResource('visits', VisitController::class);

// Additional route to get departments by site
Route::get('sites/{site}/departments', function ($siteId) {
    return response()->json(
        \App\Models\Department::where('site_id', $siteId)
            ->where('active', true)
            ->get()
    );
});

// Visit confirmation routes
Route::get('visits/{visit}/confirmation', [VisitConfirmationController::class, 'generateConfirmation']);

// Auth routes
Route::post('login', [AuthController::class, 'login']);

// Password reset routes
Route::post('forgot-password', [PasswordResetController::class, 'forgotPassword']);
Route::post('reset-password', [PasswordResetController::class, 'resetPassword']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
    
    // Dashboard routes
    Route::get('dashboard/visits', [DashboardController::class, 'getVisits']);
    Route::patch('dashboard/visits/{id}/status', [DashboardController::class, 'updateVisitStatus']);
    Route::get('dashboard/stats', [DashboardController::class, 'getStats']);
    
    // Management routes
    Route::apiResource('management/users', UserManagementController::class);
    Route::apiResource('management/sites', SiteManagementController::class);
    Route::apiResource('management/departments', DepartmentManagementController::class);
    
    // Security routes
    Route::prefix('security')->group(function () {
        Route::get('visits', [SecurityController::class, 'getVisits']);
        Route::post('visits/{visit}/checkin', [SecurityController::class, 'checkIn']);
        Route::post('visits/{visit}/checkout', [SecurityController::class, 'checkOut']);
        Route::put('visits/{visit}/reschedule', [SecurityController::class, 'reschedule']);
        Route::post('visits/{visit}/invalid', [SecurityController::class, 'markInvalid']);
        Route::get('statistics', [SecurityController::class, 'getStatistics']);
    });

    // Manager routes
    Route::prefix('manager')->group(function () {
        Route::get('visits', [ManagerController::class, 'getVisits']);
        Route::put('visits/{visit}/reschedule', [ManagerController::class, 'rescheduleVisit']);
        Route::post('visits/{visit}/confirm', [ManagerController::class, 'confirmVisit']);
        Route::post('visits/{visit}/cancel', [ManagerController::class, 'cancelVisit']);
        Route::get('statistics', [ManagerController::class, 'getStatistics']);
    });
});