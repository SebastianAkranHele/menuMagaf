<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductExport implements FromCollection, WithHeadings
{
    protected $order;
    protected $product;

    public function __construct($order, $product)
    {
        $this->order = $order;
        $this->product = $product;
    }

    public function collection()
    {
        return collect([
            [
                'Pedido'     => $this->order->id,
                'Produto'    => $this->product->name,
                'Preço'      => $this->product->pivot->price,
                'Quantidade' => $this->product->pivot->quantity,
                'Subtotal'   => $this->product->pivot->price * $this->product->pivot->quantity,
                'Data'       => $this->order->created_at->format('d/m/Y H:i'),
            ]
        ]);
    }

    public function headings(): array
    {
        return ['Pedido', 'Produto', 'Preço', 'Quantidade', 'Subtotal', 'Data'];
    }
}
