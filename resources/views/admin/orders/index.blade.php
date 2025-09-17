@extends('admin.layout')

@section('content')
<h2>Pedidos</h2>

<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Total</th>
            <th>Status</th>
            <th>Criado em</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        @foreach($orders as $order)
        <tr>
            <td>{{ $order->id }}</td>
            <td>KZ {{ number_format($order->total, 2, ',', '.') }}</td>
            <td>{{ ucfirst($order->status) }}</td>
            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
            <td>
                <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-primary">Ver</a>
                <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger" onclick="return confirm('Deseja deletar este pedido?')">Deletar</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
