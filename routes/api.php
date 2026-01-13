<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HabitsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Auth Routes
Route::post('/auth/register',[AuthController::class,'register']);
Route::post('/auth/login',[AuthController::class,'login']);
Route::post('/auth/mail-verify',[AuthController::class,'mailVerify']);
Route::post('/auth/forgot-password',[AuthController::class,'forgotPassword']);
Route::post('/auth/reset-password',[AuthController::class,'resetPassword']);
Route::post('/auth/continuoue-with-google',[AuthController::class,'googleAuth']);

// Habits route

// Check if user is logged in.
Route::middleware(['auth:sanctum'])->group(function(){

    //Check if logged in user has user role.
    Route::middleware(['user'])->group(function(){
        Route::apiResource('/habits',HabitsController::class);
    });
    
});