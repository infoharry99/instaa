<?php

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\LeadApiController;
use Illuminate\Support\Facades\Route;

// Public API routes
Route::post('/auth/login', [AuthApiController::class, 'login']);

// Authenticated API routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/user', [AuthApiController::class, 'user']);
    Route::post('/auth/logout', [AuthApiController::class, 'logout']);

    Route::get('/leads', [LeadApiController::class, 'index']);
    Route::get('/leads/{lead}', [LeadApiController::class, 'show']);
    Route::get('/discovery', [LeadApiController::class, 'discover']);
});
