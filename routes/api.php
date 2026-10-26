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

// Products API
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']); // <-- ADDED THIS LINE

// Collections API
Route::get('/collections', [CollectionController::class, 'index']);

// Categories API
Route::get('/categories', [CategoryController::class, 'index']);

// Addresses API
Route::get('/addresses', [AddressController::class, 'index']);
Route::post('/addresses', [AddressController::class, 'store']);
Route::put('/addresses/{id}', [AddressController::class, 'update']);
Route::delete('/addresses/{id}', [AddressController::class, 'destroy']);

// Orders API
Route::post('/orders', [OrderController::class, 'store']);
Route::get('/orders', [OrderController::class, 'index']);

// Profile API
Route::get('/profile', [ProfileController::class, 'show']);
Route::post('/profile', [ProfileController::class, 'update']);

// Settings API
Route::get('/settings', [SettingsController::class, 'index']);

// Blogs API
Route::get('/blogs', [\App\Http\Controllers\Api\BlogController::class, 'index']);
Route::get('/blogs/{slug}', [\App\Http\Controllers\Api\BlogController::class, 'show']);