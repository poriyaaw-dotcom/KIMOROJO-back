<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomOrder;
use Illuminate\Http\Request;

class CustomOrderController extends Controller
{
    private function withCors($response)
    {
        return $response->header('Access-Control-Allow-Origin', 'http://localhost:3000')
                        ->header('Access-Control-Allow-Credentials', 'true')
                        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept');
    }

    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return $this->withCors(response()->json(['success' => false, 'message' => 'Unauthenticated'], 401));
        }

        $orders = CustomOrder::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->withCors(response()->json(['success' => true, 'data' => $orders]));
    }

    public function store(Request $request)
    {
        // Handle preflight OPTIONS request
        if ($request->getMethod() === 'OPTIONS') {
            return $this->withCors(response('', 200));
        }

        $user = $request->user();
        
        if (!$user) {
            return $this->withCors(response()->json(['success' => false, 'message' => 'Unauthenticated'], 401));
        }

        $validated = $request->validate([
            'jewelry_type' => 'required|string',
            'metal_type' => 'required|string',
            'reference_image' => 'nullable|image|max:5120',
            'details' => 'required|string',
            'budget' => 'nullable|numeric',
        ]);

        $imagePath = null;
        if ($request->hasFile('reference_image')) {
            $imagePath = $request->file('reference_image')->store('custom_orders', 'public');
        }

        $userPhone = $user->phone ?? 'N/A';
        
        $userAddress = '';
        try {
            if (method_exists($user, 'addresses')) {
                $defaultAddress = $user->addresses()->where('is_default', true)->first() 
                                ?? $user->addresses()->first();
                if ($defaultAddress) {
                    $parts = array_filter([
                        $defaultAddress->address ?? '',
                        $defaultAddress->city ?? '',
                        $defaultAddress->province ?? '',
                        $defaultAddress->postal_code ?? ''
                    ]);
                    $userAddress = implode(', ', $parts);
                }
            }
        } catch (\Exception $e) {
            $userAddress = 'Address not available';
        }

        if (empty($userAddress)) {
            $userAddress = 'No address saved in profile';
        }

        $customOrder = CustomOrder::create([
            'user_id' => $user->id,
            'phone' => $userPhone,
            'address' => $userAddress,
            'jewelry_type' => $validated['jewelry_type'],
            'metal_type' => $validated['metal_type'],
            'reference_image' => $imagePath,
            'details' => $validated['details'],
            'budget' => $validated['budget'] ?? null,
            'status' => 'pending',
        ]);

        return $this->withCors(response()->json([
            'success' => true,
            'message' => 'Custom order submitted successfully!',
            'data' => $customOrder
        ], 201));
    }
}