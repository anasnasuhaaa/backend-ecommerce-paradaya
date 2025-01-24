<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductsController;
use App\Http\Controllers\Api\OrdersController;

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
    route::apiResource('role', RoleController::class)->middleware(['auth:api', 'isAdmin']);
    route::prefix('auth')->group(function () {
        route::post('/login', [AuthController::class, 'login']);
        route::post('/register', [AuthController::class, 'register']);
        route::get('/me', [AuthController::class, 'me'])->middleware('auth:api');
        route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');
        route::post('/update', [AuthController::class, 'update'])->middleware('auth:api');
        route::post('/generate-otp-code', [App\Http\Controllers\Api\AuthController::class, 'generateOtpCode'])->middleware('auth:api');
        route::post('/verifikasi-akun', [App\Http\Controllers\Api\AuthController::class, 'verify'])->middleware('auth:api');
    })->middleware('api');
    route::post('/profile', [App\Http\Controllers\Api\ProfileController::class, 'storeupdate'])->middleware(['auth:api', 'isEmailVerified']);
    // Route Tsania
    Route::apiResource('category', CategoryController::class);
    Route::apiResource('products', ProductsController::class);
    Route::apiResource('orders', OrdersController::class);
});
