<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserAuthController;
use App\Http\Middleware\EnsureEmailIsVerified;

Route::get('/', function () {
    dd("hello");
});

Route::post('register',[UserAuthController::class,'register']);
Route::post('login',[UserAuthController::class,'login']);

Route::middleware([EnsureEmailIsVerified::class])->group(function () {
    Route::get('/profile', [UserAuthController::class,'profile']);
});



Route::post('logout',[UserAuthController::class,'logout'])
    ->middleware('auth:sanctum');
