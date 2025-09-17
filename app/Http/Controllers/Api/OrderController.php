<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'total' => 'required|numeric',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        $order = Order::create([
            'user_id' => null,
            'total' => $request->total,
            'status' => 'pending',
        ]);

        foreach ($request->items as $item) {
            $order->products()->attach($item['product_id'], [
                'quantity' => $item['quantity'],
                'price' => $item['price']
            ]);
        }

        return response()->json([
            'success' => true,
            'order_id' => $order->id
        ]);
    }
}
