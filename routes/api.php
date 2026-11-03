<?php

use App\Http\Controllers\Auth\AuthenticatedSessionApiController;
use App\Http\Controllers\Auth\RegisteredUserApiController;
use App\Http\Controllers\ProfileApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::apiResource('/register', RegisteredUserApiController::class);
Route::apiResource('/login', AuthenticatedSessionApiController::class);
Route::apiResource('/profile', ProfileApiController::class);