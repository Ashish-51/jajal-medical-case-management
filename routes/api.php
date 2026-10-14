<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CaseController;
use Illuminate\Support\Facades\Route;

// Public API Authentication Routes
Route::post('/login', [AuthController::class, 'login']);

// Authenticated Sanctum API Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Case Management REST API
    Route::get('/cases', [CaseController::class, 'index']);
    Route::post('/cases', [CaseController::class, 'store']);
    Route::get('/cases/{id}', [CaseController::class, 'show']);
    Route::put('/cases/{id}', [CaseController::class, 'update']);
    Route::delete('/cases/{id}', [CaseController::class, 'destroy']);
});
