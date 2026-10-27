<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    // GET /api/addresses?phone=0912...
    public function index(Request $request)
    {
        $phone = $request->query('phone');
        
        if (!$phone) {
            return response()->json(['success' => false, 'message' => 'Phone number required'], 400);
        }

        $addresses = Address::where('phone', $phone)->orderBy('is_default', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $addresses
        ]);
    }

    // POST /api/addresses
    public function store(Request $request)
    {
        $validated = $request->validate([
            // UPDATED: Strict Iranian phone validation for both account phone and contact phone
            'phone' => ['required', 'string', 'regex:/^(\+98|0)9\d{9}$/'],
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
            Address::where('phone', $validated['phone'])->update(['is_default' => false]);
        }

        $address = Address::create($validated);

        return response()->json([
            'success' => true,
            'data' => $address,
            'message' => 'Address saved successfully'
        ], 201);
    }

    // PUT /api/addresses/{id}
    public function update(Request $request, $id)
    {
        $address = Address::findOrFail($id);
        
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
            Address::where('phone', $address->phone)->update(['is_default' => false]);
        }

        $address->update($validated);

        return response()->json([
            'success' => true,
            'data' => $address,
            'message' => 'Address updated successfully'
        ]);
    }

    // DELETE /api/addresses/{id}
    public function destroy($id)
    {
        $address = Address::findOrFail($id);
        $address->delete();

        return response()->json([
            'success' => true,
            'message' => 'Address deleted successfully'
        ]);
    }
}