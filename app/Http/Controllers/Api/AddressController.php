<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }
        
        $addresses = Address::where('user_id', $user->id)
            ->orderBy('is_default', 'desc')
            ->get();

        return response()->json(['success' => true, 'data' => $addresses]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_number' => ['required', 'string', 'regex:/^(\+98|0)9\d{9}$/'],
            'province' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'address' => 'required|string',
            'postal_code' => 'required|string|max:20',
            'is_default' => 'boolean',
        ]);

        if (!empty($validated['is_default'])) {
            Address::where('user_id', $user->id)->update(['is_default' => false]);
        }

        $validated['user_id'] = $user->id;
        $validated['phone'] = $user->phone;

        $address = Address::create($validated);

        return response()->json([
            'success' => true,
            'data' => $address,
            'message' => 'Address saved successfully'
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }
        
        $address = Address::where('id', $id)->where('user_id', $user->id)->firstOrFail();
        
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_number' => ['required', 'string', 'regex:/^(\+98|0)9\d{9}$/'],
            'province' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'address' => 'required|string',
            'postal_code' => 'required|string|max:20',
            'is_default' => 'boolean',
        ]);

        if (!empty($validated['is_default'])) {
            Address::where('user_id', $user->id)->update(['is_default' => false]);
        }

        $address->update($validated);

        return response()->json([
            'success' => true,
            'data' => $address,
            'message' => 'Address updated successfully'
        ]);
    }

    public function destroy($id, Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }
        
        $address = Address::where('id', $id)->where('user_id', $user->id)->firstOrFail();
        $address->delete();

        return response()->json([
            'success' => true,
            'message' => 'Address deleted successfully'
        ]);
    }
}