@extends('admin.layout')

@section('content')
<h2>Pedido #{{ $order->id }}</h2>
<p>Status: <strong>{{ ucfirst($order->status) }}</strong></p>
<p>Total: KZ {{ number_format($order->total, 2, ',', '.') }}</p>

<h3>Produtos</h3>
<table class="table">
    <thead>
        <tr>
            <th>Produto</th>
            <th>Preço</th>
            <th>Quantidade</th>
            <th>Subtotal</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        @foreach($order->products as $product)
        <tr>
            <td>{{ $product->name }}</td>
            <td>KZ {{ number_format($product->pivot->price, 2, ',', '.') }}</td>
            <td>{{ $product->pivot->quantity }}</td>
            <td>KZ {{ number_format($product->pivot->price * $product->pivot->quantity, 2, ',', '.') }}</td>
            <td>
                {{-- Exportar este produto em PDF/Excel/CSV --}}
                <a href="{{ route('admin.orders.export.product.pdf', ['order' => $order->id, 'product' => $product->id]) }}"
                   class="btn btn-sm btn-danger" title="Exportar PDF">
                    <i class="fas fa-file-pdf"></i>
                </a>
                <a href="{{ route('admin.orders.export.product.excel', ['order' => $order->id, 'product' => $product->id]) }}"
                   class="btn btn-sm btn-success" title="Exportar Excel">
                    <i class="fas fa-file-excel"></i>
                </a>
                <a href="{{ route('admin.orders.export.product.csv', ['order' => $order->id, 'product' => $product->id]) }}"
                   class="btn btn-sm btn-secondary" title="Exportar CSV">
                    <i class="fas fa-file-csv"></i>
                </a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="mt-3">
    {{-- Exportações do pedido inteiro --}}
    <a href="{{ route('admin.orders.export.single', $order) }}" class="btn btn-danger">
        <i class="fas fa-file-pdf me-1"></i> Exportar Pedido PDF
    </a>
    <a href="{{ route('admin.orders.export.single.excel', $order) }}" class="btn btn-success">
        <i class="fas fa-file-excel me-1"></i> Exportar Pedido Excel
    </a>
    <a href="{{ route('admin.orders.export.single.csv', $order) }}" class="btn btn-secondary">
        <i class="fas fa-file-csv me-1"></i> Exportar Pedido CSV
    </a>
</div>
@endsection
