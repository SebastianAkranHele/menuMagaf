<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrdersExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Order::with('products')->get()->map(function ($order) {
            return [
                'ID'         => $order->id,
                'Cliente'    => $order->customer_name,
                'Total'      => $order->total,
                'Status'     => $order->status,
                'Data'       => $order->created_at->format('d/m/Y H:i'),
                'Produtos'   => $order->products->pluck('name')->join(', '),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID Pedido',
            'Cliente',
            'Total',
            'Status',
            'Data',
            'Produtos',
        ];
    }
}
