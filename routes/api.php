<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TimesheetController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('user', [AuthController::class, 'user'])->middleware('auth:api');
});


Route::middleware('auth:api')->group(function () {
    Route::apiResource('timesheets', TimesheetController::class);
    Route::apiResource('projects', ProjectController::class);
    Route::apiResource('activities', ActivityController::class);
    Route::apiResource('users', UserController::class);

    Route::get('roles', [RoleController::class, 'index']);
});
