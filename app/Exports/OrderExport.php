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

        // Cabeçalho com info do pedido
        $rows[] = ["Pedido #{$this->order->id}", "Cliente: " . ($this->order->customer_name ?? '-'), "Status: " . ucfirst($this->order->status), "Data: " . $this->order->created_at->format('d/m/Y H:i')];
        $rows[] = []; // linha em branco

        // Cabeçalho da tabela de produtos (igual ao headings())
        $rows[] = $this->headings();

        // Linhas de produtos
        foreach ($this->order->products as $product) {
            $rows[] = [
                $product->name,
                number_format($product->pivot->price, 2, ',', '.'),
                $product->pivot->quantity,
                number_format($product->pivot->price * $product->pivot->quantity, 2, ',', '.'),
            ];
        }

        $rows[] = []; // linha em branco
        $rows[] = ['', '', 'Subtotal', number_format($this->order->total, 2, ',', '.')];
        $rows[] = ['', '', 'IVA (14%)', number_format($this->order->total * 0.14, 2, ',', '.')];
        $rows[] = ['', '', 'Total', number_format($this->order->total * 1.14, 2, ',', '.')];

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
