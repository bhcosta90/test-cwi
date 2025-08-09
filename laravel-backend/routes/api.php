<?php

declare(strict_types = 1);

use App\Http\Controllers\ExternalController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/swagger', fn () => view('swagger'));

Route::apiResource('user', UserController::class);
Route::get('external', ExternalController::class);
