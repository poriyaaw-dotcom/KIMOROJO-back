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
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $orders = Order::where('user_id', $user->id)
            ->with('address')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['success' => true, 'data' => $orders]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $validated = $request->validate([
            'address_id' => 'required|exists:addresses,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $address = \App\Models\Address::where('id', $validated['address_id'])
            ->where('user_id', $user->id)
            ->first();

        if (!$address) {
            return response()->json(['success' => false, 'message' => 'Invalid address'], 403);
        }

        try {
            DB::beginTransaction();

            $calculatedSubtotal = 0;
            $orderItemsData = [];

            // YOUR ORIGINAL WORKING PRICING LOGIC
            $goldPricePerGram = 26000000;
            $silverPricePerGram = 4900000;
            $profitRate = 0.07;
            $taxRate = 0.10;

            foreach ($validated['items'] as $item) {
                $product = Product::where('id', $item['product_id'])->lockForUpdate()->firstOrFail();
                
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Insufficient stock for product ID: {$item['product_id']}");
                }

                $metalPricePerGram = ($product->metal_type === 'silver') ? $silverPricePerGram : $goldPricePerGram;
                $baseMetalValue = $product->weight * $metalPricePerGram;
                
                $makingChargesPercent = ($product->making_charges ?? 0) / 100;
                $makingChargesTotal = $baseMetalValue * $makingChargesPercent;
                $profitTotal = ($baseMetalValue + $makingChargesTotal) * $profitRate;
                $taxTotal = ($makingChargesTotal + $profitTotal) * $taxRate;
                
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
                $priceAtTime = $baseMetalValue + $makingChargesTotal + $profitTotal + $taxTotal + $totalAccessoriesCost;

                $calculatedSubtotal += ($priceAtTime * $item['quantity']);

                $orderItemsData[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price_at_time' => $priceAtTime,
                ];

                $product->decrement('stock', $item['quantity']);
            }

            $shipping = 0; 
            $finalTotal = $calculatedSubtotal + $shipping;

            $order = Order::create([
                'user_id' => $user->id,
                'phone' => $user->phone,
                'address_id' => $validated['address_id'],
                'subtotal' => $calculatedSubtotal,
                'shipping' => $shipping,
                'tax' => 0,
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