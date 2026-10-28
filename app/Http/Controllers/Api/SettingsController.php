<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        // Fetch settings from database (adjust based on your settings table structure)
        // If using a key-value table:
        $settings = \App\Models\Setting::pluck('value', 'key')->toArray();
        
        // OR if using a single-row settings table:
        // $settings = \App\Models\Setting::first()?->toArray() ?? [];

        return response()->json([
            'success' => true,
            'data' => [
                'gold_price' => $settings['gold_price'] ?? 26000000,      // Hardcoded for testing
                'silver_price' => $settings['silver_price'] ?? 4900000,   // Hardcoded for testing
                'seller_profit' => $settings['seller_profit'] ?? 0.07,    // From admin panel (0.090 in your screenshot)
                'tax_rate' => $settings['tax_rate'] ?? 0.10,              // From admin panel (0.001 in your screenshot)
            ]
        ]);
    }
}