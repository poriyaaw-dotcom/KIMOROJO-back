<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    // GET /api/profile?phone=0912...
    public function show(Request $request)
    {
        try {
            $phone = $request->query('phone');
            if (!$phone) {
                return response()->json(['success' => false, 'message' => 'Phone is required'], 400);
            }
            
            $user = User::where('phone', $phone)->first();

            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not found'], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Profile Show Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error'], 500);
        }
    }

    // POST /api/profile
    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                // UPDATED: Strict Iranian phone validation
                'phone' => ['required', 'string', 'regex:/^(\+98|0)9\d{9}$/'],
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
            ]);

            $user = User::updateOrCreate(
                ['phone' => $validated['phone']],
                [
                    'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                    'email' => $validated['phone'] . '@kimorojo.local', 
                    'password' => Hash::make('temp_password_123'), 
                ]
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Profile Update Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
        }
    }
}