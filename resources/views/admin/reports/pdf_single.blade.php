<!DOCTYPE html>
<html>
<head>
    <title>Pedido #{{ $order->id }}</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid #000; }
        th, td { padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <h2>Pedido #{{ $order->id }}</h2>
    <p><strong>Cliente:</strong> {{ $order->customer_name ?? ($order->user->name ?? 'Cliente Desconhecido') }}</p>
    <p><strong>Status:</strong> {{ $order->status_label }}</p>
    <p><strong>Total:</strong> KZ {{ number_format($order->total,2,',','.') }}</p>
    <p><strong>Data:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>

    <h3>Produtos</h3>
    <table>
        <thead>
            <tr>
                <th>Produto</th>
                <th>Quantidade</th>
                <th>Preço</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->products as $p)
            <tr>
                <td>{{ $p->name }}</td>
                <td>{{ $p->pivot->quantity }}</td>
                <td>KZ {{ number_format($p->pivot->price,2,',','.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
