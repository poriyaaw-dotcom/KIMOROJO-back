<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'name' => $user->name,
                'first_name' => $user->first_name ?? null,
                'last_name' => $user->last_name ?? null,
                'phone' => $user->phone,
            ]
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
        ]);

        $updateData = [];

        // Handle name logic
        if (isset($validated['name'])) {
            $updateData['name'] = $validated['name'];
        } elseif (isset($validated['first_name']) || isset($validated['last_name'])) {
            $updateData['name'] = trim(($validated['first_name'] ?? '') . ' ' . ($validated['last_name'] ?? ''));
        }

        if (isset($validated['first_name'])) $updateData['first_name'] = $validated['first_name'];
        if (isset($validated['last_name'])) $updateData['last_name'] = $validated['last_name'];

        $user->update($updateData);

        return response()->json([
            'success' => true,
            'data' => [
                'name' => $user->name,
                'first_name' => $user->first_name ?? null,
                'last_name' => $user->last_name ?? null,
                'phone' => $user->phone,
            ],
            'message' => 'Profile updated successfully'
        ]);
    }
}