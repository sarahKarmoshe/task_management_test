<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;


Route::prefix('auth')->controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::get('logout', 'logout')->middleware('auth:sanctum');
});

Route::get('tasks', [TaskController::class, 'index']);
Route::get('tasks/{task}', [TaskController::class, 'show']);


Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('tasks', TaskController::class)->except(['index','show']);
});
