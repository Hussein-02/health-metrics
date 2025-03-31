<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HealthDataController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('jwt.auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/upload', [HealthDataController::class, 'upload']);
    Route::get('/health-data', [HealthDataController::class, 'getData']);
});
