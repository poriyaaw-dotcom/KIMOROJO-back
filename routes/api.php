<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CollectionController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\SettingsController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomOrderController;

// ==========================================
// CORS PREFLIGHT HANDLER
// ==========================================
Route::options('/{any}', function () {
    return response('', 200)
        ->header('Access-Control-Allow-Origin', 'http://localhost:3000')
        ->header('Access-Control-Allow-Credentials', 'true')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept');
})->where('any', '.*');

// ==========================================
// AUTH API (Public)
// ==========================================
Route::post('/auth/send-otp', [AuthController::class, 'sendOtp']);
Route::post('/auth/verify-otp', [AuthController::class, 'verifyOtp'])
    ->middleware('throttle:10,15'); // 10 attempts per 15 minutes

// ==========================================
// PUBLIC APIs
// ==========================================
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/collections', [CollectionController::class, 'index']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/blogs', [\App\Http\Controllers\Api\BlogController::class, 'index']);
Route::get('/blogs/{slug}', [\App\Http\Controllers\Api\BlogController::class, 'show']);
Route::get('/settings', [SettingsController::class, 'index']);

// ==========================================
// PROTECTED APIs (Require Authentication)
// ==========================================
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/profile', [ProfileController::class, 'update']);
    
    Route::get('/addresses', [AddressController::class, 'index']);
    Route::post('/addresses', [AddressController::class, 'store']);
    Route::put('/addresses/{id}', [AddressController::class, 'update']);
    Route::delete('/addresses/{id}', [AddressController::class, 'destroy']);
    
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders', [OrderController::class, 'index']);
    
    // SECURE: Custom orders must be authenticated so we can safely attach user data
    Route::post('/custom-orders', [CustomOrderController::class, 'store'])->middleware('throttle:5,60');
    Route::get('/custom-orders', [CustomOrderController::class, 'index']);
});