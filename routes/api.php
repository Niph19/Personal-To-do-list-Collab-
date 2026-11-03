<?php
use App\Http\Controllers\Api\AuthenticatedSessionApiController;
use App\Http\Controllers\Api\RegisteredUserApiController;
use App\Http\Controllers\Api\TaskApiController;
use Illuminate\Support\Facades\Route;

// Auth routes
Route::post('/register', [RegisteredUserApiController::class, 'store']);
Route::post('/login', [AuthenticatedSessionApiController::class, 'store']);

// Task routes (all require auth)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthenticatedSessionApiController::class, 'destroy']);
    Route::get('/profile', [App\Http\Controllers\Api\ProfileApiController::class, 'show']);
    Route::get('/tugas', [TaskApiController::class, 'index']);
    Route::get('/tugas/{id}', [TaskApiController::class, 'show']);
    Route::post('/tugas', [TaskApiController::class, 'store']);
    Route::put('/tugas/{id}', [TaskApiController::class, 'update']);
    Route::delete('/tugas/{id}', [TaskApiController::class, 'destroy']);
});
