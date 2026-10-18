<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        // Fetch products with their category and collection relationships
        $products = Product::with(['category', 'collection'])->get();
        
        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }
}
