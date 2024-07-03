<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserAuthController;
use App\Http\Middleware\EnsureEmailIsVerified;

Route::post('register',[UserAuthController::class,'register']);
Route::post('login',[UserAuthController::class,'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('logout',[UserAuthController::class,'logout']);
    Route::get('profile',[UserAuthController::class,'profile']);
});
