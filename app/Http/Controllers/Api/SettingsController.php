<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;

class SettingsController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'gold_price' => 4500000, // Mock for now, will be API later
                'silver_price' => 50000, // Mock for now
                'seller_profit' => Setting::get('seller_profit', '0.07'), // 7%
                'tax_rate' => Setting::get('tax_rate', '0.10'), // 10%
            ]
        ]);
    }
}