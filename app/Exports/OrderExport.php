<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrderExport implements FromArray, WithHeadings
{
    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order->load('products');
    }

    public function array(): array
    {
        $rows = [];

        foreach ($this->order->products as $product) {
            $rows[] = [
                'Produto'     => $product->name,
                'Preço Unit.' => $product->pivot->price,
                'Quantidade'  => $product->pivot->quantity,
                'Subtotal'    => $product->pivot->price * $product->pivot->quantity,
            ];
        }

        // Linhas de resumo (subtotal, IVA, total)
        $rows[] = ['', '', 'Subtotal', $this->order->total];
        $rows[] = ['', '', 'IVA (14%)', round($this->order->total * 0.14, 2)];
        $rows[] = ['', '', 'Total', round($this->order->total * 1.14, 2)];

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Produto',
            'Preço Unit.',
            'Quantidade',
            'Subtotal',
        ];
    }
}
