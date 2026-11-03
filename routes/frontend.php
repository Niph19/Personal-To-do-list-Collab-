<?php

use App\Http\Controllers\Frontend\AuthController;
use App\Http\Controllers\Frontend\TaskController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Frontend API Routes
|--------------------------------------------------------------------------
|
| Routes untuk frontend yang bertindak sebagai proxy ke backend API
|
*/

// Auth routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/profile', [AuthController::class, 'profile']);

// Task CRUD routes
Route::get('/tugas', [TaskController::class, 'index']);
Route::get('/tugas/{id}', [TaskController::class, 'show']);
Route::post('/tugas', [TaskController::class, 'store']);
Route::put('/tugas/{id}', [TaskController::class, 'update']);
Route::delete('/tugas/{id}', [TaskController::class, 'destroy']);
