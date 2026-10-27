<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $phone = $request->query('phone');
        if (!$phone) {
            return response()->json(['success' => false, 'message' => 'Phone number required'], 400);
        }

        $orders = Order::where('phone', $phone)
            ->with('address')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['success' => true, 'data' => $orders]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'phone' => ['required', 'string', 'regex:/^(\+98|0)9\d{9}$/'],
            'address_id' => 'required|exists:addresses,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $calculatedSubtotal = 0;
            $orderItemsData = [];

            // Settings (will be from database/API later)
            $goldPricePerGram = 26000000;
            $silverPricePerGram = 4900000;
            $profitRate = 0.07; // 7%
            $taxRate = 0.10; // 10%

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                // --- 1. MAIN METAL CALCULATION ---
                $metalPricePerGram = ($product->metal_type === 'silver') ? $silverPricePerGram : $goldPricePerGram;
                $baseMetalValue = $product->weight * $metalPricePerGram;
                
                $makingChargesPercent = ($product->making_charges ?? 0) / 100;
                $makingChargesTotal = $baseMetalValue * $makingChargesPercent;
                
                $profitTotal = ($baseMetalValue + $makingChargesTotal) * $profitRate;
                $taxTotal = ($makingChargesTotal + $profitTotal) * $taxRate;
                
                // --- 2. ACCESSORIES CALCULATION ---
                $accessoriesBase = $product->accessories_price ?? 0;
                $accMakingChargesPercent = ($product->accessories_making_charges ?? 0) / 100;
                $accMakingCharges = $accessoriesBase * $accMakingChargesPercent;
                
                $accProfitPercent = ($product->accessories_profit ?? 0) / 100;
                $accProfit = ($accessoriesBase + $accMakingCharges) * $accProfitPercent;
                
                $accTax = 0;
                if ($product->apply_tax_on_accessories) {
                    $accTax = ($accMakingCharges + $accProfit) * $taxRate;
                }
                
                $totalAccessoriesCost = $accessoriesBase + $accMakingCharges + $accProfit + $accTax;

                // --- 3. FINAL ITEM PRICE ---
                $priceAtTime = $baseMetalValue + $makingChargesTotal + $profitTotal + $taxTotal + $totalAccessoriesCost;

                $calculatedSubtotal += ($priceAtTime * $item['quantity']);

                $orderItemsData[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price_at_time' => $priceAtTime,
                ];
            }

            $shipping = 0; 
            $finalTotal = $calculatedSubtotal + $shipping;

            $order = Order::create([
                'phone' => $validated['phone'],
                'address_id' => $validated['address_id'],
                'subtotal' => $calculatedSubtotal,
                'shipping' => $shipping,
                'tax' => 0, // Tax is already included in item prices
                'total' => $finalTotal,
                'status' => 'pending',
            ]);

            foreach ($orderItemsData as $itemData) {
                $itemData['order_id'] = $order->id;
                OrderItem::create($itemData);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $order->load('items.product', 'address'),
                'message' => 'Order placed successfully'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order: ' . $e->getMessage()
            ], 500);
        }
    }
}