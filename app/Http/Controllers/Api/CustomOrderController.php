<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CustomOrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'jewelry_type' => 'required|string',
            'metal_type' => 'required|string',
            'reference_image' => 'nullable|image|max:5120', // 5MB max
            'details' => 'required|string',
            'budget' => 'nullable|numeric',
        ]);

        $imagePath = null;
        if ($request->hasFile('reference_image')) {
            $imagePath = $request->file('reference_image')->store('custom_orders', 'public');
        }

        // Get user ID if authenticated (assuming you use Sanctum or similar)
        // If using phone-based auth, you might need to adjust this to find user by phone
        $userId = Auth::id(); 

        $customOrder = CustomOrder::create([
            'user_id' => $userId,
            'jewelry_type' => $request->jewelry_type,
            'metal_type' => $request->metal_type,
            'reference_image' => $imagePath,
            'details' => $request->details,
            'budget' => $request->budget,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Custom order submitted successfully!',
            'data' => $customOrder
        ], 201);
    }
}