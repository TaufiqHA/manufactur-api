<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\MaterialController;

// Public routes
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // User CRUD routes
    Route::apiResource('users', UserController::class);

    // Project CRUD routes
    Route::apiResource('projects', ProjectController::class);

    // Material CRUD routes
    Route::apiResource('materials', MaterialController::class);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
