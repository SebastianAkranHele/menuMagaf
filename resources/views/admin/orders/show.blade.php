@extends('admin.layout')

@section('content')
<h2>Pedido #{{ $order->id }}</h2>
<p>Status: {{ ucfirst($order->status) }}</p>
<p>Total: KZ {{ number_format($order->total, 2, ',', '.') }}</p>

<h3>Produtos</h3>
<table class="table">
    <thead>
        <tr>
            <th>Produto</th>
            <th>Preço</th>
            <th>Quantidade</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($order->products as $product)
        <tr>
            <td>{{ $product->name }}</td>
            <td>KZ {{ number_format($product->pivot->price, 2, ',', '.') }}</td>
            <td>{{ $product->pivot->quantity }}</td>
            <td>KZ {{ number_format($product->pivot->price * $product->pivot->quantity, 2, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<form action="{{ route('admin.orders.complete', $order) }}" method="POST">
    @csrf
    <button class="btn btn-success">Marcar como concluído</button>
</form>

@endsection
