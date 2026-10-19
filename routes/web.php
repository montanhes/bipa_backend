<?php

use App\Http\Controllers\DownloadController;
use App\Http\Controllers\LandingController;
use Illuminate\Support\Facades\Route;

Route::get('/', LandingController::class);
Route::get('/download', DownloadController::class);
