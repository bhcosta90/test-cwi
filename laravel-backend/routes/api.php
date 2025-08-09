<?php

declare(strict_types = 1);

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/swagger', fn () => view('swagger'));

Route::prefix('api')->group(function (): void {
    Route::apiResource('user', UserController::class);
});
