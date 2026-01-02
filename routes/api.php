<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\RfqController;
use App\Http\Controllers\RfqItemController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\PoItemController;
use App\Http\Controllers\ReceivingGoodController;
use App\Http\Controllers\ProjectItemController;
use App\Http\Controllers\SubAssemblyController;
use App\Http\Controllers\MachineController;
use App\Http\Controllers\BomItemController;
use App\Http\Controllers\ItemStepConfigsController;
use App\Http\Controllers\TaskController;

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

    // Project Item CRUD routes
    Route::apiResource('project-items', ProjectItemController::class);

    // Material CRUD routes
    Route::apiResource('materials', MaterialController::class);

    // BOM Item CRUD routes
    Route::apiResource('bom-items', BomItemController::class);

    // RFQ CRUD routes
    Route::apiResource('rfqs', RfqController::class);

    // RFQ Item CRUD routes
    Route::apiResource('rfq-items', RfqItemController::class);

    // Supplier CRUD routes
    Route::apiResource('suppliers', SupplierController::class);

    // Purchase Order CRUD routes
    Route::apiResource('purchase-orders', PurchaseOrderController::class);

    // Po Item CRUD routes
    Route::apiResource('po-items', PoItemController::class);

    // Receiving Good CRUD routes
    Route::apiResource('receiving-goods', ReceivingGoodController::class);

    // Receiving Item CRUD routes
    Route::apiResource('receiving-items', \App\Http\Controllers\ReceivingItemController::class);

    // Sub Assembly CRUD routes
    Route::apiResource('sub-assemblies', SubAssemblyController::class);

    // Machine CRUD routes
    Route::apiResource('machines', MachineController::class);

    // Item Step Configs CRUD routes
    Route::apiResource('item-step-configs', ItemStepConfigsController::class);

    // Additional route to get step configs by item ID
    Route::get('project-items/{itemId}/step-configs', [ItemStepConfigsController::class, 'getByItemId']);

    // Task CRUD routes
    Route::apiResource('tasks', TaskController::class);

    // Additional Task-specific routes
    Route::patch('tasks/{task}/status', [TaskController::class, 'updateStatus']);
    Route::patch('tasks/{task}/completion', [TaskController::class, 'updateCompletion']);
    Route::patch('tasks/{task}/downtime', [TaskController::class, 'updateDowntime']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
