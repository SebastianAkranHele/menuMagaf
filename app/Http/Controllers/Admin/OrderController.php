<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('products')->orderBy('created_at', 'desc')->get();
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('products');
        return view('admin.orders.show', compact('order'));
    }

    public function complete(Order $order)
    {
        $order->status = 'completed';
        $order->save();

        return redirect()->back()->with('success', 'Pedido marcado como concluído.');
    }

    public function store(Request $request)
    {
        $order = Order::create([
            'user_id' => auth()->id() ?? null,
            'total'   => $request->total,
            'status'  => 'pending',
        ]);

        foreach ($request->items as $item) {
            $order->products()->attach($item['product_id'], [
                'quantity' => $item['quantity'],
                'price'    => $item['price']
            ]);
        }

        return response()->json(['success' => true, 'order_id' => $order->id]);
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->back()->with('success', 'Pedido deletado.');
    }
}
