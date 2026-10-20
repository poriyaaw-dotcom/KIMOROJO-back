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
    // GET /api/orders?phone=0912...
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

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    // POST /api/orders
    public function store(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string',
            'address_id' => 'required|exists:addresses,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'subtotal' => 'required|numeric',
            'shipping' => 'required|numeric',
            'tax' => 'required|numeric',
            'total' => 'required|numeric',
        ]);

        try {
            DB::beginTransaction();

            $order = Order::create([
                'phone' => $validated['phone'],
                'address_id' => $validated['address_id'],
                'subtotal' => $validated['subtotal'],
                'shipping' => $validated['shipping'],
                'tax' => $validated['tax'],
                'total' => $validated['total'],
                'status' => 'pending',
            ]);

            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);
                
                $goldValue = ($product->weight * 4500000); 
                $makingCharges = $product->making_charges ?? 0;
                $accessoriesPrice = $product->accessories_price ?? 0;
                $subtotalItem = $goldValue + $makingCharges + $accessoriesPrice;
                $priceAtTime = $subtotalItem + ($subtotalItem * 0.10); 

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price_at_time' => $priceAtTime,
                ]);
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