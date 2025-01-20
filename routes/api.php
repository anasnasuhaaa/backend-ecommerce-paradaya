<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoleController;

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

Route::prefix('v1')->group(function () {
    // Route Anas
    route::apiResource('role', RoleController::class)->middleware(['auth:api']);
    route::prefix('auth')->group(function () {
        route::post('/login', [AuthController::class, 'login']);
        route::post('/register', [AuthController::class, 'register']);
        route::get('/me', [AuthController::class, 'me'])->middleware('auth:api');
        route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');
        route::post('/update', [AuthController::class, 'update'])->middleware('auth:api');
    })->middleware('api');
    // Route Tsania
});
