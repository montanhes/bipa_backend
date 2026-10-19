<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PackageCountController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/google', [AuthController::class, 'google'])->middleware('throttle:10,1');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/counts', [PackageCountController::class, 'store'])->middleware('throttle:60,1');
});
