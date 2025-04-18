<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Support\Facades\Route;

// Public routes with strict rate limiting
Route::middleware(['throttle:6,1'])->group(function () {
    Route::name('auth.')->group(function () {
        Route::post('/register', [AuthController::class, 'register'])->name('register');
        Route::post('/login', [AuthController::class, 'login'])->name('login');
    });
});

// Protected routes with more generous rate limiting
Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');

    // Tasks
    Route::name('tasks.')->group(function () {
        Route::apiResource('tasks', TaskController::class)->except(['update', 'destroy']);
        Route::put('/tasks/{id}/assign', [TaskController::class, 'assign'])->name('assign');
        Route::put('/tasks/{id}/complete', [TaskController::class, 'complete'])->name('complete');
    });
});
