<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class SettingsController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => [
                // STATIC PRICES (Hardcoded as requested)
                'gold_price' => 26000000,    // 26 Million Toman per gram
                'silver_price' => 4900000,   // 4.9 Million Toman per gram
                
                // STATIC PERCENTAGES (Converted to decimals)
                'seller_profit' => 0.07,     // 7%
                'tax_rate' => 0.10,          // 10%
            ]
        ]);
    }
}