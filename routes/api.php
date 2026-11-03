<?php

use App\Http\Controllers\Auth\AuthenticatedSessionApiController;
use App\Http\Controllers\Auth\RegisteredUserApiController;
use App\Http\Controllers\Frontend\TaskApiController;
use App\Http\Controllers\ProfileApiController;
use Illuminate\Support\Facades\Route;

Route::apiResource('/register', RegisteredUserApiController::class);
Route::apiResource('/login', AuthenticatedSessionApiController::class);
Route::apiResource('/profile', ProfileApiController::class);
Route::apiResource('/tugas', TaskApiController::class);