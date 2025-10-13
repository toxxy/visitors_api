<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SiteController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\VisitController;

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