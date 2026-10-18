<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // GET all products
    public function index()
    {
        $products = Product::with(['category', 'collection'])->get();
        return response()->json(['success' => true, 'data' => $products]);
    }

    // GET a single product by ID
    public function show($id)
    {
        $product = Product::with(['category', 'collection'])->find($id);
        
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        }
        
        return response()->json(['success' => true, 'data' => $product]);
    }

    // POST (Add) a new product
    public function store(Request $request)
    {
        // STRICT VALIDATION: Enforce project rules
        $validated = $request->validate([
            'name_en' => 'required|string',
            'name_fa' => 'required|string',
            'weight' => 'required|numeric',
            'purity' => 'required|string',
            'making_charges' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'collection_id' => 'required|exists:collections,id',
        ]);

        $product = Product::create($validated);
        
        return response()->json(['success' => true, 'data' => $product], 201);
    }
}