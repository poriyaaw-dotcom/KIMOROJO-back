<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CollectionController;
use App\Http\Controllers\Api\CategoryController;

// Products API
Route::get('/products', [ProductController::class, 'index']);

// Collections API
Route::get('/collections', [CollectionController::class, 'index']);

// Categories API (Added this so the homepage can be fully dynamic)
Route::get('/categories', [CategoryController::class, 'index']);