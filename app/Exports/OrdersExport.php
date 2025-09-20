<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrdersExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        $rows = [];

        $orders = Order::with('products')->get();

        foreach ($orders as $order) {
            foreach ($order->products as $product) {
                $rows[] = [
                    'ID Pedido'   => $order->id,
                    'Cliente'     => $order->customer_name,
                    'Status'      => ucfirst($order->status),
                    'Data'        => $order->created_at->format('d/m/Y H:i'),
                    'Produto'     => $product->name,
                    'Preço Unit.' => number_format($product->pivot->price, 2, ',', '.'),
                    'Quantidade'  => $product->pivot->quantity,
                    'Subtotal'    => number_format($product->pivot->price * $product->pivot->quantity, 2, ',', '.'),
                    'Total Pedido'=> number_format($order->total, 2, ',', '.'),
                ];
            }
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'ID Pedido',
            'Cliente',
            'Status',
            'Data',
            'Produto',
            'Preço Unit.',
            'Quantidade',
            'Subtotal',
            'Total Pedido',
        ];
    }
}
