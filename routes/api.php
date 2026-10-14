<?php

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\LeadApiController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\InstagramWebhookController;

// Public API routes
Route::post('/auth/login', [AuthApiController::class, 'login']);

// Meta / Instagram Webhook endpoints
Route::get('/instagram/webhook', [InstagramWebhookController::class, 'verify']);
Route::post('/instagram/webhook', [InstagramWebhookController::class, 'handle']);

// Authenticated API routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/user', [AuthApiController::class, 'user']);
    Route::post('/auth/logout', [AuthApiController::class, 'logout']);

    Route::get('/leads', [LeadApiController::class, 'index']);
    Route::get('/leads/{lead}', [LeadApiController::class, 'show']);
    Route::get('/discovery', [LeadApiController::class, 'discover']);
});
